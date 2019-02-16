<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 3600);

require_once("../admin/inc/conecta.php");

$query = mysql_query("SELECT * FROM video.apps");
while ($dados_app = mysql_fetch_array($query)) {

$verifica_stm = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_app["codigo_stm"]."'"));

if($verifica_stm == 0) {
mysql_query("Delete From video.apps where codigo = '".$dados_dj["codigo"]."'");
@unlink("../app_android/apps/".$dados_app["zip"]."");
}

}

echo "[".date("d/m/Y H:i:s")."] Processo Concluído."
?>