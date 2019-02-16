<?php
// Proteção Login
require_once("inc/protecao-admin.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}


if(empty($_POST["codigo_servidor"]) or empty($_POST["ip"]) or empty($_POST["porta_ssh"])) {
die ("<script> alert(\"Você deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_servidor_atual = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$_POST["codigo_servidor"]."'"));

if($_POST["senha"]) {

#mysql_query("Update video.servidores set nome = '".$_POST["nome"]."', ip = '".$_POST["ip"]."', senha = '".code_decode($_POST["senha"],"E")."', porta_ssh = '".$_POST["porta_ssh"]."', limite_streamings = '".$_POST["limite_streamings"]."', grafico_trafego = '".$_POST["grafico_trafego"]." where codigo = '".$_POST["codigo_servidor"]."'") or die(mysql_error());
mysql_query("Update video.servidores set nome = '".$_POST["nome"]."', ip = '".$_POST["ip"]."', senha = '".code_decode($_POST["senha"],"E")."', porta_ssh = '".$_POST["porta_ssh"]."', limite_streamings = '".$_POST["limite_streamings"]."' where codigo = '".$_POST["codigo_servidor"]."'") or die(mysql_error());


} else {

mysql_query("Update video.servidores set nome = '".$_POST["nome"]."', ip = '".$_POST["ip"]."', porta_ssh = '".$_POST["porta_ssh"]."', limite_streamings = '".$_POST["limite_streamings"]."', grafico_trafego = '".$_POST["grafico_trafego"]."' where codigo = '".$_POST["codigo_servidor"]."'") or die(mysql_error());

}

// Loga a ação executada
mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('alterar_configuracoes_servidor',NOW(),'".$_SERVER['REMOTE_ADDR']."','Alteração nas configurações do servidor ".$dados_servidor_atual["nome"]." IP ".$dados_servidor_atual["ip"]."')");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Configurações do servidor ".$_POST["ip"]." alteradas com sucesso.","ok");

header("Location: /admin/admin-servidores/resultado/".$_POST["nome"]."");
?>
