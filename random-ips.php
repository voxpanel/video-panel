<?php
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));

$query = mysql_query("SELECT * FROM video.estatisticas where codigo_stm = '".$dados_stm["codigo"]."' ORDER BY RAND() LIMIT 1");
while ($dados_estatistica = mysql_fetch_array($query)) {

echo $dados_estatistica["ip"]."<br>";

}
?>