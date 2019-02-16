<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

$login_code = code_decode($dados_stm["login"],"E");

$url_source_http = "http://".dominio_servidor($dados_servidor["nome"])."/".$dados_stm["login"]."/".$dados_stm["login"]."/playlist.m3u8";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gravador</title>
<link rel="shortcut icon" href="/img/favicon-gravador.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<link href="http://vjs.zencdn.net/5.8.0/video-js.css" rel="stylesheet">
<style>.video-js .vjs-time-control{display: none;}.video-js .vjs-progress-control{display: none;}</style>
<script type="text/javascript">
   window.onload = function() {
<?php if($dados_stm["status_gravando"] == "sim") { ?>
	document.getElementById("botao_parar").style.display = "block";
	document.getElementById("status_gravacao").style.display = "block";
	document.getElementById("arquivo").innerHTML = "<?php echo $dados_stm["gravador_arquivo"]; ?>";
	contador_gravacao();
<?php } else { ?>
	document.getElementById("botao_iniciar").style.display = "block";
<?php } ?>
	}
// Timer
function contador_gravacao() {

var timerVar = setInterval(countTimer, 1000);
<?php if($dados_stm["status_gravando"] == "sim" && $dados_stm["gravador_data_inicio"] != '0000-00-00 00:00:00') { ?>
var totalSeconds = <?php echo strtotime(date("Y-m-d H:i:s"))-strtotime($dados_stm["gravador_data_inicio"]); ?>;
<?php } else { ?>
var totalSeconds = 0;
<?php } ?>
function countTimer() {
++totalSeconds;
var hour = Math.floor(totalSeconds /3600);
var minute = Math.floor((totalSeconds - hour*3600)/60);
var seconds = totalSeconds - (hour*3600 + minute*60);

document.getElementById("timer").innerHTML = "&nbsp;"+("0" + hour).slice(-2) + ":" + ("0" + minute).slice(-2) + ":" + ("0" + seconds).slice(-2);
}

}
</script>
</head>

<body>
<div style="width:400px; margin:0px auto; padding-top:5px">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_pagina_gravador_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="385" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; margin-bottom:5px; background-color: #C1E0FF; border: #006699 1px solid">
	<tr>
        <td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
        <td width="355" align="left" class="texto_padrao_pequeno" scope="col"><?php echo $lang['lang_acao_gravar_transmissao_info1']; ?></td>
    </tr>
</table>
<table width="385" border="0" cellpadding="0" cellspacing="0" bgcolor="#F8F8F8">
    <tr>
        <td height="260" colspan="2" align="center">
        <style>.video-js .vjs-time-control{display: none;}.video-js .vjs-progress-control{display: none;}</style>
<video id="player_webtv" class="video-js vjs-big-play-centered" controls preload="auto" width="320" height="240" data-setup="{ 'fluid':true,'aspectRatio':'16:9' }" >
   <source src="<?php echo $url_source_http; ?>" type="application/x-mpegURL">
</video>
<script src="//vjs.zencdn.net/6.0.0/video.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.8.2/videojs-contrib-hls.min.js"></script>
<script>var myPlayer=videojs('player_webtv',{},function(){var player=this;player.on("pause",function(){player.one("play",function(){player.load();player.play();});});})</script>
        </td>
        </tr>
    <tr>
      <td colspan="2" align="center" class="texto_padrao_vermelho"><div id="status" class="texto_padrao_vermelho" style="display:none"><img src="img/ajax-loader.gif" /></div><div id="status_gravacao" style="display:none"><img src="img/icones/img-icone-rec-animado.gif" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_pagina_gravador_status_gravando']; ?><span id="timer">00:00:00</span></div>&nbsp;<br /><div id="arquivo" class="texto_padrao_pequeno"></div></td>
    </tr>
    <tr>
      <td height="50" colspan="2" align="center">
      <input type="button" style="background-color:#009900; border:#006600 1px solid; border-radius: 10px; color:#FFFFFF; display:none; cursor:pointer; width:100px" value="<?php echo $lang['lang_info_pagina_gravador_botao_iniciar']; ?>" onclick="gravar_transmissao('iniciar');" id="botao_iniciar" />
      <input type="button" style="background-color: #FF0000; border: #990000 1px solid; border-radius: 10px; color:#FFFFFF; display:none; cursor:pointer; width:100px" value="<?php echo $lang['lang_info_pagina_gravador_botao_parar']; ?>" onclick="gravar_transmissao('parar');" id="botao_parar" />
      
      </td>
    </tr>
</table>
<table width="385" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; margin-bottom:5px; background-color:#FFFF66; border:#DFDF00 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="355" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_acao_gravar_transmissao_info2']; ?></td>
  </tr>
</table>
</div>
</div>
</div>
</body>
</html>
