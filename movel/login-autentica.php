<?php
if($_POST["login"] == '' || $_POST["senha"] == '') {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_alerta" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Por favor informe o login/senha de acesso.</strong>
  </td>
</tr>
</table>';

unset($_SESSION["login_logado"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/movel/login");
exit;
}

$valida_usuario = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where login = '".anti_sql_injection($_POST["login"])."' AND senha = '".anti_sql_injection($_POST["senha"])."'"));

if($valida_usuario == 1) {

$_SESSION["login_logado"] = anti_sql_injection($_POST["login"]);

header("Location: http://".$_SERVER['HTTP_HOST']."/movel/streaming");
exit;

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Login ou senha inválidos, tente novamente.</strong>
  </td>
</tr>
</table>';

unset($_SESSION["login_logado"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/movel/login");
exit;
}
?>