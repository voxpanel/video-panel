<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');
session_start();

require_once("admin/inc/conecta.php");
require_once("admin/inc/funcoes.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));

//////////////////////////////////////////////////////////////////
/////////////////////////// Manuten��o ///////////////////////////
//////////////////////////////////////////////////////////////////
if($dados_config["manutencao"] == "sim" && !preg_match('/player/i',query_string('0')) ) {

require("manutencao.php");

exit();

}

//////////////////////////////////////////////////////////////////
/////////////////////// Acessos por P�ginas //////////////////////
//////////////////////////////////////////////////////////////////
/*
$total_acessos_paginas = @mysql_num_rows(@mysql_query("SELECT * FROM acessos_paginas where pagina = '".query_string('0')."'"));

if($total_acessos_paginas == 0) {
@mysql_query("INSERT INTO acessos_paginas (pagina,total) VALUES ('".query_string('0')."','1')");
} else {
@mysql_query("Update acessos_paginas set total = total+1 where pagina = '".query_string('0')."'");
}

//////////////////////////////////////////////////////////////////
/////////// Bloqueio de dominio para acesso ao painel ////////////
//////////////////////////////////////////////////////////////////

$query_dominios_bloqueados = mysql_query("SELECT * FROM video.dominios_bloqueados");
while ($dados_dominios_bloqueados = mysql_fetch_array($query_dominios_bloqueados)) {
$array_dominios_bloqueados[] = $dados_dominios_bloqueados["dominio"];
}

anti_hack_dominio($array_dominios_bloqueados);

//////////////////////////////////////////////////////////////////
///// Bloqueio de IP com hist�rico de tentativas de ataque ///////
//////////////////////////////////////////////////////////////////

//$query_ips_bloqueados = mysql_query("SELECT * FROM stmvideo.ips_bloqueados");
//while ($dados_ips_bloqueados = mysql_fetch_array($query_ips_bloqueados)) {
//$array_ips_bloqueados[] = $dados_ips_bloqueados["ip"];
//}

//anti_hack_ip($array_ips_bloqueados);
*/
//////////////////////////////////////////////////////////////////
////////////////// Idioma e TimeZone do Painel ///////////////////
//////////////////////////////////////////////////////////////////

if($_SESSION["login_logado"]) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));

if(file_exists("inc/lang-".$dados_stm["idioma_painel"].".php")) {
require_once("inc/lang-".$dados_stm["idioma_painel"].".php");
} else {
require_once("inc/lang-pt-br.php");
}

}

//////////////////////////////////////////////////////////////////
//////////////////////////// Navega��o ///////////////////////////
//////////////////////////////////////////////////////////////////

$pagina = query_string('0');

if($pagina == "sair") {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));

// Insere a a��o executada no registro de logs.
logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_info_log_logout_painel']."");

$pagina = "login";

unset($_SESSION["login_logado"]);
}

if ($pagina == "") {
require("login.php");
} elseif (!file_exists($pagina.".php")) {
require("manutencao.php");
} else {
require("".$pagina.".php");
}
?>