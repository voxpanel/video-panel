<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

require_once("inc/protecao-admin.php");
require_once("inc/classe.ssh.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

// Proteção contra usuario não logados
if(empty($_SESSION["code_user_logged"])) {
die("<span class='texto_status_erro'>0x005 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["login"]) or empty($_POST["espectadores"]) or empty($_POST["bitrate"]) or empty($_POST["senha"])) {
die ("<script> alert(\"Você deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
$dados_stm_atual = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_POST["login"]."'"));
$dados_servidor_atual = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm_atual["codigo_servidor"]."'"));

mysql_query("Update video.streamings set codigo_cliente = '".$_POST["codigo_cliente"]."', codigo_servidor = '".$_POST["servidor"]."', senha = '".$_POST["senha"]."', senha_transmissao = '".$_POST["senha_transmissao"]."', espectadores = '".$_POST["espectadores"]."', bitrate = '".$_POST["bitrate"]."', espaco = '".$_POST["espaco"]."', identificacao = '".$_POST["identificacao"]."', email = '".$_POST["email"]."', aplicacao = '".$_POST["aplicacao"]."' where codigo = '".$dados_stm_atual["codigo"]."'") or die(mysql_error());

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$_POST["servidor"]."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/sincronizar ".$dados_stm_atual["login"]." '".$_POST["senha_transmissao"]."' ".$_POST["bitrate"]." ".$_POST["espectadores"]." ".$_POST["aplicacao"]."");


if($dados_stm_atual["codigo_servidor"] != $_POST["servidor"]) {

// Conexão SSH
$ssh2 = new SSH();
$ssh2->conectar($dados_servidor_atual["ip"],$dados_servidor_atual["porta_ssh"]);
$ssh2->autenticar("root",code_decode($dados_servidor_atual["senha"],"D"));

$ssh2->executar("/usr/local/WowzaMediaServer/desativar ".$dados_stm_atual["login"]."");

}

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("Configurações do streaming ".$_POST["login"]." alteradas com sucesso.","ok");

header("Location: /admin/admin-streamings/resultado/".$_POST["login"]."");
?>