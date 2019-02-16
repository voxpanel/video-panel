<?php
ini_set("max_execution_time", 1800);

require_once("".str_replace("/robots","",dirname(__FILE__))."/admin/inc/conecta.php");
require_once("".str_replace("/robots","",dirname(__FILE__))."/admin/inc/funcoes.php");
require_once("".str_replace("/robots","",dirname(__FILE__))."/admin/inc/classe.ssh.php");

$inicio_execucao = tempo_execucao();

parse_str($argv[1]);

list($inicial,$final) = explode("-",$registros);

echo "\n\n--------------------------------------------------------------------\n\n";

// Gera as estatisticas
//$sql = mysql_query("SELECT * FROM video.streamings where login = 'teste'");
$sql = mysql_query("SELECT * FROM video.streamings where status = '1' ORDER by login ASC LIMIT ".$inicial.", ".$final."");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($dados_servidor["status"] == "on") {

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin touchAppInstance ".$dados_stm["login"]."");

echo "[".$dados_stm["login"]."] Touch OK\n";

} // status servidor

} // while

$fim_execucao = tempo_execucao();

$tempo_execucao = number_format(($fim_execucao-$inicio_execucao),2);

echo "\n\n--------------------------------------------------------------------\n\n";
echo "Tempo: ".$tempo_execucao." segundo(s);\n\n";
?>