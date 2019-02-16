<?php

$login = $_GET["login"];
$servidor = $_GET["servidor"];
$videoVOD = $_GET["video"];
$videoVODthumb = $_GET["thumb"];
$player = $_GET["player"];

if(!empty($videoVOD)) {
$url_source = "http://".$servidor."/".$login."/".$login."/mp4:".$videoVOD."/playlist.m3u8";
} else {
$url_source = "http://".$servidor."/".$login."/".$login."/playlist.m3u8";
}

$player_videojs = '<html>
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
<style>.video-js .vjs-time-control{display: none;}.video-js .vjs-progress-control{display: none;}</style>
</head>
<body>
<video id="player_webtv" class="video-js vjs-big-play-centered vjs-fluid" controls preload="auto" width="100%" height="100%" data-setup="{ \'fluid\':true,\'aspectRatio\':\'16:9\' }" >
   <source src="'.$url_source.'" type="application/x-mpegURL">
</video>
<script src="//vjs.zencdn.net/6.0.1/video.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.12.0/videojs-contrib-hls.min.js"></script>
<script>var myPlayer=videojs("player_webtv",{},function(){var player=this;player.on("pause",function(){player.one("play",function(){player.load();player.play();});});})</script>
</body>
</html>';

@file_put_contents($login.".html",$player);
?>