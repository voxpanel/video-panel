<?php
//////////////////////////////////////////////////////////////////
//////// Verifica Bloqueio por Tentativas de Acesso do IP ////////
//////////////////////////////////////////////////////////////////
/*
$checar_bloqueio_ip = @mysql_num_rows(@mysql_query("SELECT * FROM bloqueios_login where ip = '".$_SERVER['REMOTE_ADDR']."' AND tentativas >= 5"));

if($checar_bloqueio_ip > 0) {

$_SESSION["status_login"] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_padrao_pequeno" style="padding-left: 5px; color: #923614;" scope="col" align="left">
<img src="/img/icones/atencao.png" align="absmiddle">&nbsp;<strong>IP '.$_SERVER['REMOTE_ADDR'].' bloqueado, contate nosso atendimento!</strong>
  </td>
</tr>
</table>';

unset($_SESSION["login_logado"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/login");
exit;
}
*/
//////////////////////////////////////////////////////////////////

if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "pt") {
$lang_erro1 = "Por favor informe a login/senha de acesso";
$lang_erro2 = "Login ou senha inválidos, tente novamente";
} elseif(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "en") {
$lang_erro1 = "Please enter the login/password";
$lang_erro2 = "Login or password invalid, try again";
} elseif(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "es") {
$lang_erro1 = "Por favor, introduzca el login/contraseña";
$lang_erro2 = "Login o contraseña no válido, inténtelo de nuevo";
} else {
$lang_erro1 = "Por favor informe a login/senha de acesso";
$lang_erro2 = "Login ou senha inválidos, tente novamente";
}

$_POST["login"] = str_replace("'='","",$_POST["login"]);
$_POST["senha"] = str_replace("'='","",$_POST["senha"]);

if($_POST["login"] == '' || $_POST["senha"] == '') {

$_SESSION["status_login"] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="/img/icones/atencao.png" align="absmiddle">&nbsp;<strong>'.$lang_erro1.'</strong>
  </td>
</tr>
</table>';

unset($_SESSION["login_logado"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/login");
exit;
}

$valida_usuario = mysql_num_rows(mysql_query("SELECT * FROM video.streamings WHERE login = '".anti_sql_injection($_POST["login"])."' AND senha = '".anti_sql_injection($_POST["senha"])."'"));

if($valida_usuario == 1) {

$_SESSION["login_logado"] = $_POST["login"];

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings WHERE login = '".$_SESSION["login_logado"]."'"));

// Limpa as tentativas de logins frustradas anteriormente
@mysql_query("Delete From video.bloqueios_login where codigo = '".$dados_stm["codigo"]."'");

if(file_exists("inc/lang-".$dados_stm["idioma_painel"].".php")) {
require_once("inc/lang-".$dados_stm["idioma_painel"].".php");
} else {
require_once("inc/lang-pt-br.php");
}

// Insere a ação executada no registro de logs.
logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_info_log_login_painel']."");

header("Location: http://".$_SERVER['HTTP_HOST']."/streaming");
exit;

} else { // Dados inválidos

//////////////////////////////////////////////////////////////////
//////////////// Grava a Tentativa de Acesso do IP ///////////////
//////////////////////////////////////////////////////////////////
/*
$checar_stm_bloqueio = @mysql_num_rows(@mysql_query("SELECT * FROM video.streamings WHERE login = '".anti_sql_injection($_POST["login"])."'"));

if($checar_stm_bloqueio > 0) {

$checar_ip = @mysql_num_rows(@mysql_query("SELECT * FROM bloqueios_login where ip = '".$_SERVER['REMOTE_ADDR']."'"));

if($checar_ip == 0) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings WHERE login = '".anti_sql_injection($_POST["login"])."'"));

@mysql_query("INSERT INTO bloqueios_login (codigo_cliente,codigo_stm,data,ip,navegador,tentativas) VALUES ('".$dados_stm["codigo_cliente"]."','".$dados_stm["codigo"]."',NOW(),'".$_SERVER['REMOTE_ADDR']."','".formatar_navegador($_SERVER['HTTP_USER_AGENT'])."','1')");

} else {
@mysql_query("Update bloqueios_login set tentativas = tentativas+1 where ip = '".$_SERVER['REMOTE_ADDR']."'");
}

}
*/
//////////////////////////////////////////////////////////////////

$_SESSION["status_login"] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="/img/icones/atencao.png" align="absmiddle">&nbsp;<strong>'.$lang_erro2.'</strong>
  </td>
</tr>
</table>';

unset($_SESSION["login_logado"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/login");
exit;
}
?>