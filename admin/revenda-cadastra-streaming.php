<?php
require_once("inc/protecao-revenda.php");
require_once('inc/classe.ssh.php');

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["bitrate"]) or empty($_POST["senha"])) {
die ("<script> alert(\"".lang_info_campos_vazios."\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

// Valida o login contra caracteres especiais
if(!preg_match("/^[a-z0-9]+$/", $_POST["login"])) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_erro_login_invalido."\");
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

// Verifica se login já esta em uso
$verificacao_login = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where login = '".strtolower($_POST["login"])."'"));

if($verificacao_login > 0) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_erro_login_existente."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

// Verifica logins restritos
$array_logins_restritos = array("web", "streaming", "home");

if(in_array($_POST["login"], $array_logins_restritos)) { 
die ("<script> alert(\"Login reservado!\\n\\nLogin reserved!\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

// Verifica os limites do cliente
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$total_streamings_subrevenda = mysql_fetch_array(mysql_query("SELECT SUM(streamings) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$total_streamings_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espectadores_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$espectadores_stm_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espaco_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$espaco_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

// Verifica se excedeu o limite de streamings do cliente
$total_streamings_revenda = $total_streamings_revenda+$total_streamings_subrevenda["total"]+1;

if($total_streamings_revenda > $dados_revenda["streamings"] && $dados_revenda["streamings"] != 999999) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_streamings."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

// Verifica se excedeu o limite de espectadores do cliente
$total_espectadores_revenda = $espectadores_revenda["total"]+$espectadores_subrevenda_revenda["total"]+$_POST["espectadores"];

if($total_espectadores_revenda > $dados_revenda["espectadores"] && $dados_revenda["espectadores"] != 999999) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_espectadores."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

// Verifica se excedeu o limite de espectadores do cliente
$total_espaco_revenda = $espaco_revenda["total"]+$espaco_subrevenda_revenda["total"]+$_POST["espaco"];

if($total_espaco_revenda > $dados_revenda["espaco"]) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_espaco_ftp."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

// Verifica se excedeu o limite de bitrate do cliente
if($_POST["bitrate"] > $dados_revenda["bitrate"]) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_bitrate."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));

mysql_query("INSERT INTO video.streamings (codigo_cliente,codigo_servidor,login,senha,senha_transmissao,autenticar_live,espectadores,bitrate,espaco,ipcameras,ftp_dir,identificacao,data_cadastro,idioma_painel,email,permitir_alterar_senha,aplicacao,exibir_app_android) VALUES ('".$dados_revenda["codigo"]."','".$dados_config["codigo_servidor_atual"]."','".strtolower($_POST["login"])."','".$_POST["senha"]."','".$_POST["senha"]."','".$_POST["autenticar_live"]."','".$_POST["espectadores"]."','".$_POST["bitrate"]."','".$_POST["espaco"]."','".$_POST["ipcameras"]."','/home/streaming/".strtolower($_POST["login"])."','".$_POST["identificacao"]."',NOW(),'".$_POST["idioma_painel"]."','".$_POST["email"]."','".$_POST["permitir_alterar_senha"]."','".$_POST["aplicacao"]."','".$_POST["exibir_app_android"]."')") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());
$codigo_streaming = mysql_insert_id();

// Cria o streaming no servidor Wowza
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_config["codigo_servidor_atual"]."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

if($_POST["aplicacao"] == 'tvstation') {

// Cria a home do streaming
$ssh->executar("/bin/mkdir -v /home/streaming/".strtolower($_POST["login"]).";/bin/chown streaming.streaming /home/streaming/".strtolower($_POST["login"])."");
// Copia a playlist demo para home do streaming
$ssh->executar("/bin/cp -vp /home/streaming/demo.mp4 /home/streaming/".strtolower($_POST["login"])."/;/bin/cp -vp /home/streaming/demo.smil /home/streaming/".strtolower($_POST["login"])."/playlists_agendamentos.smil");
// Configura a playlist demo
$ssh->executar("/usr/bin/replace LOGIN ".strtolower($_POST["login"])." -- /home/streaming/".strtolower($_POST["login"])."/playlists_agendamentos.smil;echo OK");
}

if($_POST["aplicacao"] == 'vod') {
// Cria a home do streaming
$ssh->executar("/bin/mkdir -v /home/streaming/".strtolower($_POST["login"]).";/bin/chown streaming.streaming /home/streaming/".strtolower($_POST["login"])."");
}

$aplicacao_xml = $_POST["aplicacao"];

if($_POST["autenticar_live"] == "nao") {

if($_POST["aplicacao"] == "tvstation" || $_POST["aplicacao"] == "live") {
$aplicacao_xml = $_POST["aplicacao"].'-sem-login';
}

}

// Ativa o streaming no Wowza
$ssh->executar("/usr/local/WowzaMediaServer/ativar ".strtolower($_POST["login"])." '".$_POST["senha"]."' ".$_POST["bitrate"]." ".$_POST["espectadores"]." ".$aplicacao_xml."");

// Loga a ação executada
mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('cadastro_streaming',NOW(),'".$_SERVER['REMOTE_ADDR']."','Cadastrado streaming ".strtolower($_POST["login"])." no servidor ".$dados_servidor["nome"]." pela revenda ".$dados_revenda["nome"]."')");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao(sprintf(lang_info_pagina_cadastrar_streaming_resultado_ok,strtolower($_POST["login"])),"ok");

echo '<script type="text/javascript">top.location = "/admin/revenda/'.code_decode(strtolower($_POST["login"]),"E").'"</script>';
?>