<?php
// Prote��o Login
require_once("inc/protecao-admin.php");

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

if(empty($_POST["nome"]) or empty($_POST["email"]) or empty($_POST["senha"]) or empty($_POST["streamings"]) or empty($_POST["espectadores"]) or empty($_POST["bitrate"]) or empty($_POST["espaco"])) {
die ("<script> alert(\"Voc� deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

mysql_query("INSERT INTO video.revendas (id,nome,email,senha,subrevendas,streamings,espectadores,bitrate,espaco,chave_api,data_cadastro) VALUES ('".$_POST["id"]."','".$_POST["nome"]."','".$_POST["email"]."',PASSWORD('".$_POST["senha"]."'),'".$_POST["subrevendas"]."','".$_POST["streamings"]."','".$_POST["espectadores"]."','".$_POST["bitrate"]."','".$_POST["espaco"]."','".code_decode($_POST["email"],"E")."',NOW())") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());
$codigo_revenda = mysql_insert_id();

// Loga a a��o executada
mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('cadastro_revenda',NOW(),'".$_SERVER['REMOTE_ADDR']."','Cadastrada revenda ".$_POST["nome"]." email ".$_POST["email"]."')");

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Revenda ".$_POST["nome"]." cadastrada com sucesso.","ok");

header("Location: /admin/admin-revendas");
?>