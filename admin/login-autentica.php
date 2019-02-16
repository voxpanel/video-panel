<?php
$x_email = anti_sql_injection($_POST["email"]);
$x_senha = anti_sql_injection($_POST["senha"]);

if($_POST["email"] == '' || $_POST["senha"] == '') {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Por favor informe seu e-mail/senha de acesso</strong>
  </td>
</tr>
</table>';

unset($_SESSION["type_logged_user"]);
unset($_SESSION["code_user_logged"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;
}

if (strpos($_POST["email"], '@') !== false) {

$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE email = '".$x_email."' AND senha = PASSWORD('".$x_senha."')"));

if($valida_revenda == 1) {

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE email = '".$x_email."' AND senha = PASSWORD('".$x_senha."')"));

$_SESSION["type_logged_user"] = "cliente";
$_SESSION["code_user_logged"] = $dados_revenda["codigo"];

// Loga o acesso do usuario
mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('login',NOW(),'".$_SERVER['REMOTE_ADDR']."','Revenda ".$_POST["email"]." acessou sistema.')");

// Loga ultimo acesso da revenda
@mysql_query("Update revendas set ultimo_acesso_data = NOW(), ultimo_acesso_ip = '".$_SERVER['REMOTE_ADDR']."'  WHERE codigo = '".$dados_revenda["codigo"]."'");

header("Location: http://".$_SERVER['HTTP_HOST']."/admin/revenda");
exit;

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>E-mail ou senha inválidos, tente novamente.</strong>
  </td>
</tr>
</table>';

unset($_SESSION["type_logged_user"]);
unset($_SESSION["code_user_logged"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;

}

} else { // se nao for cliente é operador

$valida_operador = mysql_num_rows(mysql_query("SELECT * FROM video.administradores WHERE usuario = '".$x_email."' AND senha = PASSWORD('".$x_senha."')"));

if($valida_operador == 1) {

$dados_operador = mysql_fetch_array(mysql_query("SELECT * FROM video.administradores WHERE usuario = '".$x_email."'"));

$_SESSION["type_logged_user"] = "operador";
$_SESSION["code_user_logged"] = $dados_operador["codigo"];

// Loga o acesso do usuario
mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('login',NOW(),'".$_SERVER['REMOTE_ADDR']."','Administrador ".$_POST["email"]." acessou sistema.')");


if($_SERVER['HTTP_REFERER']) {

$pagina_inicial = str_replace("login","admin-configuracoes",$_SERVER['HTTP_REFERER']);
$pagina_inicial = str_replace("sair","admin-configuracoes",$pagina_inicial);

header("Location: ".$pagina_inicial."");
} else {
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/admin-configuracoes");
}

exit;

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Usuário ou senha inválidos, tente novamente</strong>
  </td>
</tr>
</table>';

unset($_SESSION["type_logged_user"]);
unset($_SESSION["code_user_logged"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;

}

}
?>