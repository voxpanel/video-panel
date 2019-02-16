<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("inc/protecao-revenda.php");
require_once("inc/classe.ssh.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

// Proteção contra usuario não logados
if(empty($_SESSION["code_user_logged"])) {
die("<span class='texto_status_erro'>0x005 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["login"]) or empty($_POST["bitrate"]) or empty($_POST["senha"])) {
die ("<script> alert(\"".lang_info_campos_vazios."\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
$dados_stm_atual = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_POST["login"]."'"));

// Verifica os limites da revenda
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
$dados_subrevenda_atual = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".code_decode($_POST["codigo_subrevenda"],"D")."') AND tipo = '2'"));

$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$total_streamings_subrevenda = mysql_fetch_array(mysql_query("SELECT SUM(streamings) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$total_streamings_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espectadores_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$espectadores_stm_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espaco_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$espaco_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

// Verifica se excedeu o limite de espectadores do cliente
$total_espectadores_revenda = $espectadores_revenda["total"]+$espectadores_subrevenda_revenda["total"];
$total_espectadores_revenda = $total_espectadores_revenda+$_POST["espectadores"];
$total_espectadores_revenda = $total_espectadores_revenda-$dados_stm_atual["espectadores"];

if($total_espectadores_revenda > $dados_revenda["espectadores"] && $dados_revenda["espectadores"] != 999999) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_espectadores."\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

// Verifica se excedeu o limite de espectadores do cliente
$total_espaco_revenda = $espaco_revenda["total"]+$espaco_subrevenda_revenda["total"];
$total_espaco_revenda = $total_espaco_revenda+$_POST["espaco"];
$total_espaco_revenda = $total_espaco_revenda-$dados_stm_atual["espaco"];

if($total_espaco_revenda > $dados_revenda["espaco"]) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_espaco_ftp."\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

// Verifica se excedeu o limite de bitrate do cliente
if($_POST["bitrate"] > $dados_revenda["bitrate"]) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_bitrate."\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

mysql_query("Update video.streamings set senha = '".$_POST["senha"]."', senha_transmissao = '".$_POST["senha_transmissao"]."', autenticar_live = '".$_POST["autenticar_live"]."', espectadores = '".$_POST["espectadores"]."', bitrate = '".$_POST["bitrate"]."', espaco = '".$_POST["espaco"]."', ipcameras = '".$_POST["ipcameras"]."', identificacao = '".$_POST["identificacao"]."', idioma_painel = '".$_POST["idioma_painel"]."', email = '".$_POST["email"]."', permitir_alterar_senha = '".$_POST["permitir_alterar_senha"]."', exibir_app_android = '".$_POST["exibir_app_android"]."' where codigo = '".$dados_stm_atual["codigo"]."'") or die(mysql_error());

if($dados_stm_atual["senha_transmissao"] != $_POST["senha_transmissao"] || $dados_stm_atual["espectadores"] != $_POST["espectadores"] || $dados_stm_atual["bitrate"] != $_POST["bitrate"] || $dados_stm_atual["autenticar_live"] != $_POST["autenticar_live"]) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm_atual["codigo_servidor"]."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

$aplicacao_xml = $dados_stm_atual["aplicacao"];

if($_POST["autenticar_live"] == "nao") {

if($dados_stm_atual["aplicacao"] == "tvstation" || $dados_stm_atual["aplicacao"] == "live") {
$aplicacao_xml = $dados_stm_atual["aplicacao"].'-sem-login';
}

}

$ssh->executar("/usr/local/WowzaMediaServer/sincronizar ".$dados_stm_atual["login"]." '".$_POST["senha_transmissao"]."' ".$_POST["bitrate"]." ".$_POST["espectadores"]." ".$aplicacao_xml."");

$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm_atual["login"]."");

$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm_atual["login"]."");

}

// Loga a ação executada
mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('alterar_configuracoes_streaming',NOW(),'".$_SERVER['REMOTE_ADDR']."','Alteração nas configurações do streaming ".$dados_stm["login"]." pela revenda.')");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao(sprintf(lang_info_pagina_configurar_streaming_resultado_ok,$_POST["login"]),"ok");
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_configurar_streaming_resultado_alerta,"alerta");

echo '<script type="text/javascript">top.location = "/admin/revenda/'.code_decode($_POST["login"],"E").'"</script>';
?>