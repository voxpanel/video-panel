<?php
ini_set("max_execution_time", 3600);

require_once("/home/painelvideo/public_html/admin/inc/conecta.php");
require_once("/home/painelvideo/public_html/admin/inc/funcoes.php");

$inicio_execucao = tempo_execucao();

$sql = mysql_query("SELECT * FROM video.streamings where status = '1' ORDER by codigo ASC");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($dados_servidor["status"] == "on") {

$resultado = @file_get_contents("http://".$dados_servidor["ip"].":55/uso-ftp.php?login=".$dados_stm["login"]."");

if($resultado === FALSE) {
$resultado = @file_get_contents("http://".$dados_servidor["ip"].":55/uso-ftp.php?login=".$dados_stm["login"]."");
}

if($resultado) {

$tamanho = ($resultado > 0) ? tamanho($resultado) : '0 MB';

mysql_query("Update video.streamings set espaco_usado = '".$resultado."' where codigo = '".$dados_stm["codigo"]."'");
echo "[".$dados_stm["login"]."] ".$tamanho."\n";
} else {
echo "[".$dados_stm["login"]."] ERRO!\n";
}

} // FIM -> Status servidor ON/OFF

} // FIM -> While

$fim_execucao = tempo_execucao();

$tempo_execucao = number_format(($fim_execucao-$inicio_execucao),2);

echo "\n\n--------------------------------------------------------------------\n\n";
echo "Tempo de Execussão: ".$tempo_execucao." segundo(s);\n\n";
?>