<?php
require_once("inc/protecao-revenda.php");
require_once('inc/classe.ssh.php');

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

// Prote��o contra usuario n�o logados
if(empty($_SESSION["code_user_logged"])) {
die("<span class='texto_status_erro'>0x005 - Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

if(empty($_POST["subrevenda_email"]) or empty($_POST["streamings"]) or empty($_POST["espectadores"]) or empty($_POST["bitrate"]) or empty($_POST["espaco"])) {
die ("<script> alert(\"".lang_info_campos_vazios."\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
$dados_subrevenda_atual = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".code_decode($_POST["codigo_subrevenda"],"D")."') AND tipo = '2'"));

// Verifica se e-mail ja esta em uso
if($_POST["subrevenda_email"] != $dados_subrevenda_atual["email"]) {
	$verificacao_email = mysql_num_rows(mysql_query("SELECT * FROM video.revendas where email = '".$_POST["subrevenda_email"]."'"));
	if($verificacao_email > 0) {
	die ("<script> alert(\"".lang_info_pagina_cadastrar_subrevenda_alerta_email_em_uso."\"); 
			 window.location = 'javascript:history.back(1)'; </script>");
	}
}

// Verifica os limites da revenda
$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$total_streamings_subrevenda = mysql_fetch_array(mysql_query("SELECT SUM(streamings) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$total_streamings_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espectadores_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$espectadores_stm_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espaco_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$espaco_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

// Verifica se excedeu limite de subrevendas ao liberar subrevendas para esta subrevenda
$total_subrevendas_sub = $total_subrevendas+$_POST["subrevendas"];
$total_subrevendas_sub = $total_subrevendas_sub-$dados_subrevenda_atual["subrevendas"];

if($total_subrevendas_sub > $dados_revenda["subrevendas"]) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_subrevendas,"alerta");
header("Location: ".$_SERVER['HTTP_REFERER']."");
exit;
}

// Verifica se excedeu o limite de streamings
$total_streamings_revenda = $total_streamings_revenda+$total_streamings_subrevenda["total"];
$total_streamings_revenda = $total_streamings_revenda+$_POST["streamings"];
$total_streamings_revenda = $total_streamings_revenda-$dados_subrevenda_atual["streamings"];

if($total_streamings_revenda > $dados_revenda["streamings"] && $dados_revenda["streamings"] != 999999) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_streamings,"alerta");
header("Location: ".$_SERVER['HTTP_REFERER']."");
exit;
}

// Verifica se excedeu o limite de espectadores
$total_espectadores_revenda = $espectadores_revenda["total"]+$espectadores_subrevenda_revenda["total"];
$total_espectadores_revenda = $total_espectadores_revenda+$_POST["espectadores"];
$total_espectadores_revenda = $total_espectadores_revenda-$dados_subrevenda_atual["espectadores"];

if($total_espectadores_revenda > $dados_revenda["espectadores"] && $dados_revenda["espectadores"] != 999999) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_espectadores,"alerta");
header("Location: ".$_SERVER['HTTP_REFERER']."");
exit;
}

// Verifica se excedeu o limite de espaco FTP
$total_espaco_revenda = $espaco_revenda["total"]+$espaco_subrevenda_revenda["total"];
$total_espaco_revenda = $total_espaco_revenda+$_POST["espaco"];
$total_espaco_revenda = $total_espaco_revenda-$dados_subrevenda_atual["espaco"];

if($total_espaco_revenda > $dados_revenda["espaco"]) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_espaco_ftp,"alerta");
header("Location: ".$_SERVER['HTTP_REFERER']."");
exit;
}

// Verifica se excedeu o limite de bitrate
if($_POST["bitrate"] > $dados_revenda["bitrate"]) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_bitrate,"alerta");
header("Location: ".$_SERVER['HTTP_REFERER']."");
exit;
}

if($_POST["subrevenda_senha"]) {

mysql_query("Update video.revendas set nome = '".$dados_revenda["nome"]."', email = '".$_POST["subrevenda_email"]."', senha = PASSWORD('".$_POST["subrevenda_senha"]."'), streamings = '".$_POST["streamings"]."', espectadores = '".$_POST["espectadores"]."', bitrate = '".$_POST["bitrate"]."', espaco = '".$_POST["espaco"]."', chave_api = '".code_decode($_POST["subrevenda_email"],"E")."', idioma_painel = '".$_POST["idioma_painel"]."', subrevendas = '".$_POST["subrevendas"]."' where codigo = '".$dados_subrevenda_atual["codigo"]."'") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());

} else {

mysql_query("Update video.revendas set nome = '".$dados_revenda["nome"]."', email = '".$_POST["subrevenda_email"]."', streamings = '".$_POST["streamings"]."', espectadores = '".$_POST["espectadores"]."', bitrate = '".$_POST["bitrate"]."', espaco = '".$_POST["espaco"]."', chave_api = '".code_decode($_POST["subrevenda_email"],"E")."', idioma_painel = '".$_POST["idioma_painel"]."', subrevendas = '".$_POST["subrevendas"]."' where codigo = '".$dados_subrevenda_atual["codigo"]."'") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());

}

// Insere a a��o executada no registro de logs.
logar_acao("[".$dados_subrevenda_atual["id"]."] Configura��es da Sub revenda alteradas com sucesso para revenda ".$dados_revenda["nome"]." - ".$dados_revenda["id"]."");

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] = status_acao(sprintf(lang_info_pagina_configurar_subrevenda_resultado_ok,$dados_subrevenda_atual["id"]),"ok");

echo '<script type="text/javascript">top.location = "/admin/revenda/subrevenda/'.$_POST["codigo_subrevenda"].'"</script>';
?>