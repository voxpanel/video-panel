<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

require_once("inc/protecao-admin.php");

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

// Prote��o contra usuario n�o logados
if(empty($_SESSION["code_user_logged"])) {
die("<span class='texto_status_erro'>0x005 - Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

if(empty($_POST["codigo_revenda"]) or empty($_POST["nome"]) or empty($_POST["email"]) or empty($_POST["streamings"]) or empty($_POST["espectadores"]) or empty($_POST["bitrate"]) or empty($_POST["espaco"])) {
die ("<script> alert(\"Voc� deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_revenda_atual = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_POST["codigo_revenda"]."'"));

if($_POST["senha"]) {

mysql_query("Update video.revendas set id = '".$_POST["id"]."', nome = '".$_POST["nome"]."', email = '".$_POST["email"]."', senha = PASSWORD('".$_POST["senha"]."'), subrevendas = '".$_POST["subrevendas"]."', streamings = '".$_POST["streamings"]."', espectadores = '".$_POST["espectadores"]."', bitrate = '".$_POST["bitrate"]."', espaco = '".$_POST["espaco"]."', dominio_padrao = '".$_POST["dominio_padrao"]."' where codigo = '".$_POST["codigo_revenda"]."'");

} else {

mysql_query("Update video.revendas set id = '".$_POST["id"]."', nome = '".$_POST["nome"]."', email = '".$_POST["email"]."', subrevendas = '".$_POST["subrevendas"]."', streamings = '".$_POST["streamings"]."', espectadores = '".$_POST["espectadores"]."', bitrate = '".$_POST["bitrate"]."', espaco = '".$_POST["espaco"]."', dominio_padrao = '".$_POST["dominio_padrao"]."' where codigo = '".$_POST["codigo_revenda"]."'");

}

// Altera o bitrate dos streamings de acordo com o novo bitrate caso tenha sido alterado
if($dados_revenda_atual["bitrate"] != $_POST["bitrate"]) {

$sql_stm = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_revenda_atual["codigo"]."'");
while ($dados_stm = mysql_fetch_array($sql_stm)) {

if($dados_stm["bitrate"] > $_POST["bitrate"]) {
mysql_query("Update video.streamings set bitrate = '".$_POST["bitrate"]."' where codigo = '".$dados_stm["codigo"]."'") or die(mysql_error());
}

}

}

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Configura��es da revenda ".$_POST["nome"]." alteradas com sucesso.","ok");

header("Location: /admin/admin-revendas");
?>