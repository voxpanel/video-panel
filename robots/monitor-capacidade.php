<?php
require_once("/home/painelvideo/public_html/admin/inc/conecta.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_config["codigo_servidor_atual"]."'"));
$total_stm = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where codigo_servidor = '".$dados_servidor["codigo"]."'"));

$limite_seguro = $dados_servidor["limite_streamings"]-1;

if($total_stm >= $limite_seguro) {

$mensagem = 'Data: '.date("d/m/Y H:i:s").'\n
Servidor: '.$dados_servidor["nome"].'\n
Limite: '.$dados_servidor["limite_streamings"].'\n
Total: '.$total_stm.'';

mail('atendimento@advancehost.com.br','['.$dados_servidor["nome"].'] Alerta de capacidade excedida',$mensagem);
}
?>