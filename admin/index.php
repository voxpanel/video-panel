<?php
session_set_cookie_params(0);
session_start();

require_once("inc/conecta.php");
require_once("inc/funcoes.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));

//////////////////////////////////////////////////////////////////
//////////////////////// Idioma do Painel ////////////////////////
//////////////////////////////////////////////////////////////////

if($_SESSION["code_user_logged"] && $_SESSION["type_logged_user"] == "cliente") {

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

if(file_exists("inc/lang-".$dados_revenda["idioma_painel"].".php")) {
require_once("inc/lang-".$dados_revenda["idioma_painel"].".php");
} else {
require_once("inc/lang-pt-br.php");
}

}

// Inclui funчѕes gerais do sistema
if($_SESSION["type_logged_user"] == "cliente") {
require_once("funcoes-ajax-revenda.php");
} else {
require_once("funcoes-ajax.php");
}

// Verifica se painel esta com manutenчуo ativada e entуo exibe a pсgina de manutenчуo
if($dados_config["manutencao"] == "sim" && $_SESSION["type_logged_user"] == "cliente") {

require("manutencao.php");

exit();

}

//////////////////////////////////////////////////////////////////
//////////////////////////// Navegaчуo ///////////////////////////
//////////////////////////////////////////////////////////////////

$pagina = query_string('1');

if(empty($pagina) || $pagina == "sair") {

unset($_SESSION["type_logged_user"]);
unset($_SESSION["code_user_logged"]);

header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit();
}

if ($pagina == "") {
require("login.php");
} elseif (!file_exists($pagina.".php")) {
require("manutencao.php");
} else {
require("".$pagina.".php");
}
?>