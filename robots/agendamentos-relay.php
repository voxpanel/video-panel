<?php
ini_set("memory_limit", "256M");
ini_set("max_execution_time", 3600);

require_once("/home/painelvideo/public_html/admin/inc/conecta.php");
require_once("/home/painelvideo/public_html/admin/inc/funcoes.php");
require_once("/home/painelvideo/public_html/funcoes-ajax.php");

parse_str($argv[1]);

list($inicial,$final) = explode("-",$registros);

echo "[".date("d/m/Y H:i:s")."] Processo Iniciado.\n";

$hora_atual_servidor = date("H:i");

$query1 = mysql_query("SELECT * FROM stmvideo.agendamentos_relay ORDER by codigo ASC LIMIT ".$inicial.", ".$final."");
while ($dados_agendamento = mysql_fetch_array($query1)) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_agendamento["codigo_stm"]."'"));

$hora_inicio = $dados_agendamento["hora_inicio"].":".$dados_agendamento["minuto_inicio"];
$hora_termino = $dados_agendamento["hora_termino"].":".$dados_agendamento["minuto_termino"];

$hora_atual = formatar_data("H:i", $hora_atual_servidor, $dados_stm["timezone"]);
$data_atual = date("Y-m-d");

if($dados_stm["status"] == 1) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($dados_servidor["status"] == "on") {

//////////////////////////////////////////////////////////////
//// Frequкncia 1 -> Executar em data especнfica(uma vez) ////
//////////////////////////////////////////////////////////////

if($dados_agendamento["frequencia"] == 1) {

// Verifica se a data especнfica й hoje e se esta na hora de iniciar
if($dados_agendamento["data_inicio"] == $data_atual && $hora_inicio == $hora_atual) {

echo "[0x01][".$dados_stm["login"]."][".date_default_timezone_get()."][".$dados_stm["timezone"]."][".$hora_atual_servidor."][".$hora_inicio."] Iniciando relay ".$dados_agendamento["url_rtmp"]." em ".$dados_agendamento["data_inicio"]." as ".$hora_inicio."\n";

// Conexгo SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

// Inicia o relay
$ssh->executar("/home/streaming/gerenciar_relay iniciar ".$dados_stm["login"]." '".$dados_stm["senha_transmissao"]."' '".$dados_agendamento["url_rtmp"]."' 720");

// Loga a aзгo executada
mysql_query("INSERT INTO stmvideo.agendamentos_relay_logs (codigo_agendamento,codigo_stm,data,url_rtmp) VALUES ('".$dados_agendamento["codigo"]."','".$dados_stm["codigo"]."',NOW(),'[Start] ".$dados_agendamento["url_rtmp"]."')");

} // FIM -> Verifica se esta na hora de iniciar / Frequкncia 1

// Verifica se esta na hora de terminar um relay ativado
if($dados_agendamento["data_termino"] == $data_atual && $hora_termino == $hora_atual) {

echo "[0x01xb][".$dados_stm["login"]."][".date_default_timezone_get()."][".$dados_stm["timezone"]."][".$hora_atual_servidor."][".$hora_termino."] Terminando relay ".$dados_agendamento["url_rtmp"]." em ".$dados_agendamento["data_termino"]." as ".$hora_termino."\n";

// Conexгo SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

// Para o relay
$resultado = $ssh->executar("/home/streaming/gerenciar_relay parar ".$dados_stm["login"]."");

if(!preg_match('/OK/i',$resultado)) {
// Para o relay forзadamente
$resultado = $ssh->executar("/home/streaming/gerenciar_relay parar-forcado ".$dados_stm["login"]."");
}

// Loga a aзгo executada
mysql_query("INSERT INTO stmvideo.agendamentos_relay_logs (codigo_agendamento,codigo_stm,data,url_rtmp) VALUES ('".$dados_agendamento["codigo"]."','".$dados_stm["codigo"]."',NOW(),'[Stop] ".$dados_agendamento["url_rtmp"]."')");

// Remove o agendamento
//mysql_query("Delete From stmvideo.agendamentos_relay where codigo = '".$dados_agendamento["codigo"]."'");

} // FIM -> Verifica se esta na hora de PARAR / Frequкncia 1

} elseif($dados_agendamento["frequencia"] == 2) { // Else -> frequencia 2

//////////////////////////////////////////////
//// Frequкncia 2 -> Executar Diariamente ////
//////////////////////////////////////////////

// Verifica se esta na hora de iniciar
if($hora_inicio == $hora_atual) { 

echo "[0x02][".$dados_stm["login"]."][".date_default_timezone_get()."][".$dados_stm["timezone"]."][".$hora_atual_servidor."][".$hora_inicio."] Iniciando relay ".$dados_agendamento["url_rtmp"]." as ".$hora_inicio."\n";

// Conexгo SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

// Inicia o relay
$ssh->executar("/home/streaming/gerenciar_relay iniciar ".$dados_stm["login"]." '".$dados_stm["senha_transmissao"]."' '".$dados_agendamento["url_rtmp"]."' 720");

// Loga a aзгo executada
mysql_query("INSERT INTO stmvideo.agendamentos_relay_logs (codigo_agendamento,codigo_stm,data,url_rtmp) VALUES ('".$dados_agendamento["codigo"]."','".$dados_stm["codigo"]."',NOW(),'[Start] ".$dados_agendamento["url_rtmp"]."')");

} // FIM -> Verifica se esta na hora de iniciar

// Verifica se esta na hora de terminar um relay ativado
if($hora_termino == $hora_atual) { 

echo "[0x02xb][".$dados_stm["login"]."][".date_default_timezone_get()."][".$dados_stm["timezone"]."][".$hora_atual_servidor."][".$hora_termino."] Terminando relay ".$dados_agendamento["url_rtmp"]." em ".$dados_agendamento["data_termino"]." as ".$hora_termino."\n";

// Conexгo SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

// Para o relay
$resultado = $ssh->executar("/home/streaming/gerenciar_relay parar ".$dados_stm["login"]."");

if(!preg_match('/OK/i',$resultado)) {
// Para o relay forзadamente
$resultado = $ssh->executar("/home/streaming/gerenciar_relay parar-forcado ".$dados_stm["login"]."");
}

// Loga a aзгo executada
mysql_query("INSERT INTO stmvideo.agendamentos_relay_logs (codigo_agendamento,codigo_stm,data,url_rtmp) VALUES ('".$dados_agendamento["codigo"]."','".$dados_stm["codigo"]."',NOW(),'[Stop] ".$dados_agendamento["url_rtmp"]."')");

} // FIM -> Verifica se esta na hora de PARAR / Frequкncia 2

} else { // Else -> frequencia 3

///////////////////////////////////////////////
/// Frequкncia 3 -> Executar Dias da Semana ///
///////////////////////////////////////////////

$dia_semana = date("N");
$array_dias = explode(",",substr($dados_agendamento["dias"], 0, -1));

// Verifica se esta na hora de iniciar
if(in_array($dia_semana, $array_dias) === true && $hora_inicio == $hora_atual) { 

echo "[0x03][".$dados_stm["login"]."][".date_default_timezone_get()."][".$dados_stm["timezone"]."][".$hora_atual_servidor."][".$hora_inicio."] Iniciando relay ".$dados_agendamento["url_rtmp"]." as ".$hora_inicio."\n";

// Conexгo SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

// Inicia o relay
$ssh->executar("/home/streaming/gerenciar_relay iniciar ".$dados_stm["login"]." '".$dados_stm["senha_transmissao"]."' '".$dados_agendamento["url_rtmp"]."' 720");

// Loga a aзгo executada
mysql_query("INSERT INTO stmvideo.agendamentos_relay_logs (codigo_agendamento,codigo_stm,data,url_rtmp) VALUES ('".$dados_agendamento["codigo"]."','".$dados_stm["codigo"]."',NOW(),'[Start] ".$dados_agendamento["url_rtmp"]."')");

} // FIM -> Verifica se o dia da semana й o atual e se esta na hora de iniciar

// Verifica se esta na hora de PARAR
if(in_array($dia_semana, $array_dias) === true && $hora_termino == $hora_atual) { 

echo "[0x03][".$dados_stm["login"]."][".date_default_timezone_get()."][".$dados_stm["timezone"]."][".$hora_atual_servidor."][".$hora_termino."] Terminando relay ".$dados_agendamento["url_rtmp"]." as ".$hora_inicio."\n";

// Conexгo SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

// Para o relay
$resultado = $ssh->executar("/home/streaming/gerenciar_relay parar ".$dados_stm["login"]."");

if(!preg_match('/OK/i',$resultado)) {
// Para o relay forзadamente
$resultado = $ssh->executar("/home/streaming/gerenciar_relay parar-forcado ".$dados_stm["login"]."");
}

// Loga a aзгo executada
mysql_query("INSERT INTO stmvideo.agendamentos_relay_logs (codigo_agendamento,codigo_stm,data,url_rtmp) VALUES ('".$dados_agendamento["codigo"]."','".$dados_stm["codigo"]."',NOW(),'[Stop] ".$dados_agendamento["url_rtmp"]."')");

} // FIM -> Verifica se o dia da semana й o atual e se esta na hora de PARAR

} // FIM -> frequencia

} // FIM -> Verifica se o servidor esta ON/OFF

} // FIM -> Verifica se o streaming esta ON/OFF

} // FIM -> while

echo "\n[".date("d/m/Y H:i:s")."] Processo Concluнdo.\n\n";

?>