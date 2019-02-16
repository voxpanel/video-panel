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

$url_player = (!empty($dados_revenda["dominio_padrao"])) ? "playerv.".$dados_revenda["dominio_padrao"]."" : "playerv.".$dados_config["dominio_padrao"]."";

$file_source = (empty($video_vod)) ? $dados_stm["login"] : "mp4:".code_decode($video_vod,"D");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta name="twitter:card" content="player">
<meta name="twitter:domain" content="http://<?php echo "playerv.".$dados_config["dominio_padrao"].""; ?><?php echo $_SERVER['REQUEST_URI']; ?>">
<meta name="twitter:url" content="http://<?php echo "playerv.".$dados_config["dominio_padrao"].""; ?><?php echo $_SERVER['REQUEST_URI']; ?>">
<meta name="twitter:title" content="<?php echo $dados_stm["player_titulo"]; ?>">
<meta name="twitter:description" content="<?php echo $dados_stm["player_descricao"]; ?>">
<meta name="twitter:image:src" content="http://<?php echo "playerv.".$dados_config["dominio_padrao"]; ?>/img/icones/img-icone-play-facebook.jpg?<?php echo time(); ?>">
<meta name="twitter:image:width" content="150">
<meta name="twitter:image:height" content="150">
<meta name="twitter:player:width" content="320">
<meta name="twitter:player:height" content="240">
<meta name="twitter:player:stream:content_type" content="video/mp4">
<meta name="twitter:player" content="http://video.<?php echo $dados_config["dominio_padrao"].""; ?>/player-video.swf?streamer=rtmp://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>&file=<?php echo $file_source; ?>&autostart=true&repeat=always">
<meta name="twitter:player:stream" content="http://video.<?php echo $dados_config["dominio_padrao"].""; ?>/player-video.swf?streamer=rtmp://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>&file=<?php echo $file_source; ?>&autostart=true&repeat=always">
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