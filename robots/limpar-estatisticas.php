<?php
ini_set("max_execution_time", 3600);

require_once("/home/painelvideo/public_html/admin/inc/conecta.php");

$query_stats = mysql_query("SELECT * FROM video.estatisticas");
while ($dados_stats = mysql_fetch_array($query_stats)) {

$verifica_stm = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_stats["codigo_stm"]."'"));

// Remove os registros de streamings removidos
if($verifica_stm == 0) {
mysql_query("Delete From video.estatisticas where codigo = '".$dados_stats["codigo"]."'");
}

}

// Remove os registros anteriores a 1 ano
$data = date("Y-m-d",mktime (0, 0, 0, date("m")  , date("d")-300, date("Y")));

mysql_query("Delete From video.estatisticas WHERE data < '".$data."'");
?>