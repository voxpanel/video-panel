<?php
// Prote��o Login
require_once("inc/protecao-admin.php");

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

if(empty($_POST["codigo_cliente"]) or empty($_POST["revenda_nova"])) {
die ("<script> alert(\"Voc� deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

foreach($_POST["revenda_nova"] as $login => $revenda){

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
$dados_revenda_atual = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_POST["codigo_cliente"]."'"));
$dados_revenda_nova = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$revenda."'"));

mysql_query("UPDATE streamings set codigo_cliente = '".$dados_revenda_nova["codigo"]."' where login = '".$dados_stm["login"]."'");

}

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Streamings da revenda ".$dados_revenda_atual["nome"]." alterados com sucesso para ".$dados_revenda_nova["nome"]."","ok");

header("Location: /admin/admin-revendas/resultado/".$dados_revenda_nova["nome"]."");
?>