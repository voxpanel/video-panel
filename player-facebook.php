<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$login = query_string('1');
$video_vod = query_string('2');
$titulo = query_string('3');
$descricao = query_string('4');

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

$titulo = (empty($titulo)) ? $dados_stm["player_titulo"] : code_decode($titulo,"D");
$descricao = (empty($descricao)) ? $dados_stm["player_descricao"] : code_decode($descricao,"D");

if($dados_stm["aplicacao"] == "live" || $dados_stm["aplicacao"] == "tvstation") {

$file_source = $dados_stm["login"];

} elseif($dados_stm["aplicacao"] == "vod") {

$file_source = "mp4:".code_decode($video_vod,"D");

} elseif($dados_stm["aplicacao"] == "relayrtsp") {

$file_source = "relay.stream";

} else {
$file_source = $dados_stm["login"];
}

$url_player = (!empty($dados_revenda["dominio_padrao"])) ? "playerv.".$dados_revenda["dominio_padrao"]."" : "playerv.".$dados_config["dominio_padrao"]."";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:g="http://base.google.com/ns/1.0">
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta property="fb:app_id" content="522557647825370" />
<meta property="og:locale" content="pt_BR" />
<meta property="og:site_name" content="<?php echo $titulo; ?>" />
<meta property="og:title" content="<?php echo $titulo; ?>" />
<meta property="og:description" content="<?php echo $descricao; ?>">
<meta property="og:type" content="video" />
<meta property="og:url" content="http://<?php echo "playerv.".$dados_config["dominio_padrao"].""; ?><?php echo $_SERVER['REQUEST_URI']; ?>" />
<meta property="og:image" content="http://<?php echo "playerv.".$dados_config["dominio_padrao"].""; ?>/img/icones/img-icone-play-facebook.jpg" />
<meta property="og:image:width" content="150" />
<meta property="og:image:height" content="150" />

<meta property="og:video" content="http://<?php echo "playerv.".$dados_config["dominio_padrao"].""; ?>/player-video.swf?streamer=rtmp://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>&file=<?php echo $file_source; ?>&autostart=true&repeat=always" />
<meta property="og:video:secure_url" content="https://<?php echo $dados_config["dominio_padrao"]; ?>/player-video.swf?streamer=rtmp://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>&file=<?php echo $file_source; ?>&autostart=true&repeat=always" />

<meta property="og:video:height" content="240" />
<meta property="og:video:width" content="320" />
<meta property="og:video:type" content="application/x-shockwave-flash" />
<title><?php echo $titulo; ?></title>
<?php if(query_string('2') == "fechar") { ?>
<script type="text/javascript">janela = window.open(window.location, "_self");janela.close();</script>
<?php } ?>
</head>
	  
<body oncontextmenu="return false" onkeydown="return false">
<?php echo $titulo; ?><br />
<?php echo $descricao; ?>
</body>
</html>