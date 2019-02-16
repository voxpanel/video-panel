<?php
ini_set("max_execution_time", 1800);

require_once("/home/painelvideo/public_html/admin/inc/conecta.php");
require_once("/home/painelvideo/public_html/admin/inc/funcoes.php");

$inicio_execucao = tempo_execucao();

parse_str($argv[1]);

list($inicial,$final) = explode("-",$registros);

echo "\n\n--------------------------------------------------------------------\n\n";

// Grava cache com o XML do wowza de todos os servidores
$sql_servidores = mysql_query("SELECT * FROM video.servidores where status = 'on' ORDER by ordem ASC");
while ($dados_servidor = mysql_fetch_array($sql_servidores)) {

$xml_wowza = @simplexml_load_string(utf8_encode(estatistica_streaming_robot($dados_servidor["ip"],$dados_servidor["senha"])));

$array_xml["stats"][$dados_servidor["codigo"]] = $xml_wowza;


echo "Servidor Wowza: ".$dados_servidor["nome"]."\n";

}

echo "\n--------------------------------------------------------------------\n\n";

$array_user_agents = array("Wirecast","Teradek","vmix","Vmix","FMLE");

// Gera as estatisticas
$sql = mysql_query("SELECT * FROM video.streamings where status = '1' ORDER by login ASC LIMIT ".$inicial.", ".$final."");
//$sql = mysql_query("SELECT * FROM video.streamings where status = '1' ORDER by login ASC");
while ($dados_stm = mysql_fetch_array($sql)) {

$array_espectadores = array();

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($dados_servidor["status"] == "on") {

$xml_stats_wowza = $array_xml["stats"][$dados_servidor["codigo"]];

$total_registros_wowza = count($xml_stats_wowza->VHost->Application);

if($total_registros_wowza > 0) {

for($i=0;$i<$total_registros_wowza;$i++){

if($xml_stats_wowza->VHost->Application[$i]->Name == $dados_stm["login"] && ($xml_stats_wowza->VHost->Application[$i]->Status == "loaded" || $xml_stats_wowza->VHost->Application[$i]->Status == "")) {

$total_espectadores_wowza = count($xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client);

for($ii=0;$ii<$total_espectadores_wowza;$ii++){

if(!@strstr($xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client[$ii]->FlashVersion,$array_user_agents)) {

$ip_wowza = $xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress;
$tempo_conectado_wowza = $xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client[$ii]->TimeRunning;
$player_wowza = ($xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client[$ii]->Type == "rtmp") ? "Flash" : "Mobile";

$array_espectadores["".$ip_wowza.""] = $tempo_conectado_wowza."|".pais_ip($ip_wowza,"nome")."|".$player_wowza."";

}

}

break;

}

}

}

// Insere os espectadores no banco de dados
$array_espectadores = array_filter($array_espectadores);

foreach($array_espectadores as $ip => $espectador) {

list($tempo_conectado, $pais, $player) = explode("|",$espectador);

if(!empty($ip) && !empty($tempo_conectado) && !empty($pais) && !empty($player)) {

$verifica_espectador = mysql_num_rows(mysql_query("SELECT * FROM video.estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND (ip = '".$ip."' AND data = '".date("Y-m-d")."')"));

if($verifica_espectador == 0) {

mysql_query("INSERT INTO video.estatisticas (codigo_stm,data,hora,ip,pais,tempo_conectado,player) VALUES ('".$dados_stm["codigo"]."',NOW(),NOW(),'".$ip."','".$pais."','".$tempo_conectado."','".$player."')") or die("Erro MySQL: ".mysql_error());

echo "[".$dados_stm["login"]."] Ouvinte: ".$ip." adicionado.\n";

} else {

mysql_query("Update video.estatisticas set tempo_conectado = '".$tempo_conectado."', player = '".$player."' where codigo_stm = '".$dados_stm["codigo"]."' AND (ip = '".$ip."' AND data = '".date("Y-m-d")."')") or die("Erro MySQL: ".mysql_error());

echo "[".$dados_stm["login"]."] Ouvinte: ".$ip." atualizado.\n";

}

}

} // foreach

} // status servidor

} // while

$fim_execucao = tempo_execucao();

$tempo_execucao = number_format(($fim_execucao-$inicio_execucao),2);

echo "\n\n--------------------------------------------------------------------\n\n";
echo "Tempo: ".$tempo_execucao." segundo(s);\n\n";
?>