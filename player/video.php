<?php
$login = query_string('1');
$player = query_string('2');
//$autoplay = query_string('3');
//$mudo = query_string('4');
$servidor = code_decode(query_string('5'),"D");
$aspectratio = query_string('6');
$capa_vodthumb = code_decode(query_string('7'),"D");
$vod = code_decode(query_string('8'),"D");

if(!empty($vod)) {
$url_source = "http://".$servidor."/".$login."/".$login."/mp4:".$vod."/playlist.m3u8";
} else {
$url_source = "http://".$servidor."/".$login."/".$login."/playlist.m3u8";
}
?>

<?php if($player == 1) { ?>
<?php $autoplay = (query_string('3') == "true") ? "autoplay" : "";  ?>
<?php $mudo = (query_string('4') == "true") ? "muted" : "";  ?>
<?php $loop = (query_string('9') == "true") ? "loop" : "";  ?>
<html>
    <!--[if IE 8]>
    <html lang="en" class="ie8 no-js"> <![endif]--><!--[if IE 9]>
    <html lang="en" class="ie9 no-js"> <![endif]--><!--[if !IE]>
    <html lang="en" class="no-js"> <!--<![endif]-->
    <head>
        <meta charset=utf-8 />
        <meta name=author content=Streaming>
<meta name=description content="Streaming">
<meta name=apple-touch-fullscreen content=yes>
<meta name=apple-mobile-web-app-capable content=yes>
<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
<link href="//vjs.zencdn.net/6.0.1/video-js.css" rel="stylesheet">
<style>*{margin:0;}html,body{height:100%;}.video-js .vjs-time-control{display: none;}.video-js .vjs-progress-control{display: none;}</style>
</head>
<body>
<video id="player_webtv" class="video-js vjs-big-play-centered vjs-fluid" <?php echo $autoplay; ?> <?php echo $mudo; ?> <?php echo $loop; ?> poster="<?php echo $capa_vodthumb; ?>" controls preload="auto" width="100%" height="100%" data-setup="{ 'fluid':true,'aspectRatio':'<?php echo $aspectratio; ?>' }" >
   <source src="<?php echo $url_source; ?>" type="application/x-mpegURL">
</video>
<script src="//vjs.zencdn.net/6.0.1/video.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.11.0/videojs-contrib-hls.min.js"></script>
<script>var myPlayer=videojs('player_webtv',{},function(){var player=this;player.on("pause",function(){player.one("play",function(){player.load();player.play();});});})</script>
</body>
</html>
<?php } else { ?>
<!DOCTYPE HTML>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <script type="text/javascript" charset="utf-8" src="//cdn.jsdelivr.net/npm/clappr@latest/dist/clappr.min.js"></script>
 <link rel="stylesheet" href="//playerv.srvstm.com/inc/clappr.min.css">
  <title>Player</title>
<style>*{margin:0;}html,body{height:100%;}</style>
</head>
<body>
<div  class="container-fluid">
    <div class="row">
    	<?php if($aspectratio == "16:9") { ?>
        <div class="embed-responsive embed-responsive-16by9">
        <?php } ?>
    	<?php if($aspectratio == "4:3") { ?>
        <div class="embed-responsive embed-responsive-4by3">
        <?php } ?>
        <div id="player_webtv" class="embed-responsive-item"></div>
        </div>
</div>
</div>
    <script type="text/javascript" charset="utf-8">
  window.onload = function() {
  var player = new Clappr.Player({
    source: '<?php echo $url_source; ?>',
    parentId: '#player_webtv',
	width: '100%',
    height: '100%',
    mute: <?php echo query_string('4'); ?>,
    hideMediaControl: true,
    poster: '<?php echo $capa_vodthumb; ?>',
	loop: '<?php echo query_string('9'); ?> ',
	autoPlay: <?php echo query_string('3'); ?>  });
}
  </script>
</body>
</html>

<?php } ?>