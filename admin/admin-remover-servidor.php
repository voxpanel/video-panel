<?php
// Prote��o Login
require_once("inc/protecao-admin.php");

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

$servidor_code = code_decode(query_string('2'),"D");

$total_servidor = mysql_num_rows(mysql_query("SELECT * FROM video.servidores where codigo = '".$servidor_code."'"));

if($total_servidor == 0) {
die ("<script> alert(\"Ooops! Este servidor n�o existe.\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$servidor_code."'"));

mysql_query("Delete From video.servidores where codigo='".$dados_servidor['codigo']."'") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());

// Loga a a��o executada
mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('remover_servidor',NOW(),'".$_SERVER['REMOTE_ADDR']."','Remo��o do servidor ".$dados_servidor["nome"]." IP ".$dados_servidor["ip"]."')");

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Servidor ".$dados_servidor["ip"]." removido com sucesso.","ok");

header("Location: /admin/admin-servidores");
?>