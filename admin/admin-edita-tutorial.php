<?php
// Prote��o Login
require_once("inc/protecao-admin.php");

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

if(empty($_POST["titulo"]) or empty($_POST["tutorial"])) {
die ("<script> alert(\"Voc� deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$tutorial = str_replace("../../","/",$_POST["tutorial"]);

mysql_query("Update video.tutoriais set titulo = '".$_POST["titulo"]."', tutorial = '".addslashes($tutorial)."' where codigo = '".$_POST["codigo"]."'") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Tutorial ".$_POST["titulo"]." editado com sucesso.","ok");

header("Location: /admin/admin-tutoriais");
?>