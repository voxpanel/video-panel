<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$url_player = (!empty($dados_revenda["dominio_padrao"])) ? "playerv.".$dados_revenda["dominio_padrao"]."" : "playerv.".$dados_config["dominio_padrao"]."";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<style>.video-js .vjs-time-control{display: none;}.video-js .vjs-progress-control{display: none;}</style>
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="/inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo-pequeno">
  <table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
                <td scope="col">
            <div id="quadro">
            <div id="quadro-topo"><strong><?php echo $lang['lang_info_players_player_ip_camera_tab_titulo_gerador']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
              <tr>
                <td>
                <div class="tab-pane" id="tabPane1">
                  <div class="tab-page" id="tabPage1">
                    <h2 class="tab"><?php echo $lang['lang_info_players_player_ip_camera_aba_configuracoes']; ?></h2>
                    <form action="/gerenciar-player-ip-camera" method="post">
                    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
                      <tr>
                        <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_ip_camera_camera']; ?></td>
                        <td width="720" align="left"><select name="camera_stream" class="input" id="camera_stream" style="width:255px;">
                <option selected="selected"><?php echo $lang['lang_info_players_player_ip_camera_selecione']; ?></option>
<?php
$query_ip_cameras = mysql_query("SELECT * FROM stmvideo.ip_cameras WHERE codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
while ($dados_ip_camera = mysql_fetch_array($query_ip_cameras)) {

if($dados_ip_camera["codigo"] == code_decode(query_string('1'),"D")) {
echo '<option value="'.$dados_ip_camera["stream"].'" selected="selected">'.$dados_ip_camera["nome"].'</option>';
} else {
echo '<option value="'.$dados_ip_camera["stream"].'">'.$dados_ip_camera["nome"].'</option>';
}

}
?>
                </select></td>
                      </tr>
                      <tr>
                        <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_ip_camera_largura']; ?></td>
                        <td width="720" align="left"><input type="number" name="largura" style="width:250px" value="320" /></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_ip_camera_altura']; ?></td>
                        <td align="left"><input type="number" name="altura" style="width:250px" value="240" /></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_ip_camera_capa']; ?></td>
                        <td align="left" class="texto_padrao_pequeno"><input type="text" name="capa" style="width:250px" value="http://" />&nbsp;<?php echo $lang['lang_info_players_player_ip_camera_capa_info']; ?></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_ip_camera_aspectratio']; ?></td>
                        <td align="left"><select class="input" name="aspectratio" style="width:255px;">
          		<option value="16:9" selected="selected">Wide Screen 16:9</option>
		  		<option value="4:3">Default 4:3</option>
	         	</select></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_ip_camera_autoplay']; ?></td>
                        <td align="left">
                        <input name="autoplay" type="checkbox" value="autoplay" style="vertical-align:middle" />&nbsp;<?php echo $lang['lang_label_sim']; ?></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
                        <td align="left"><input type="submit" class="botao" value="OK" /></td>
                      </tr>
                    </table>
                    </form>
                  </div>
                  <div class="tab-page" id="tabPage1">
                    <h2 class="tab"><?php echo $lang['lang_info_players_player_ip_camera_aba_codigo_html']; ?></h2>
                    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
                      <tr>
                        <td height="170" align="center" class="texto_padrao_vermelho_destaque" style="padding:5px">
                        <?php if($_POST["camera_stream"]) { ?>
                        <?php list($video_selecionado, $thumb_video_selecionado) = explode("|",$_POST["camera_stream"]); echo $video; ?>
                        <textarea readonly="readonly" style="width:670px; height:160px;font-size:11px" onmouseover="this.select()" onclick="this.select()"><link href="http://vjs.zencdn.net/6.0.0/video-js.css" rel="stylesheet">
<style>.video-js .vjs-time-control{display: none;}.video-js .vjs-progress-control{display: none;}</style>
<script src="http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
<video id="player_webtv" class="video-js vjs-big-play-centered" <?php echo $_POST["autoplay"]; ?> controls preload="auto" width="<?php echo $_POST["largura"]; ?>" height="<?php echo $_POST["altura"]; ?>" data-setup="{ 'fluid':true,'aspectRatio':'<?php echo $_POST["aspectratio"]; ?>' }" <?php if($_POST["capa"] && $_POST["capa"] != "http://") { echo 'poster="'.$_POST["capa"].'"'; } ?>>
   <source src="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8" type="application/x-mpegURL">
</video>
<script src="http://vjs.zencdn.net/6.0.0/video.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.8.2/videojs-contrib-hls.min.js"></script>
<script>var myPlayer=videojs('player_webtv',{},function(){var player=this;player.on("pause",function(){player.one("play",function(){player.load();player.play();});});})</script></textarea>
						<?php } else { ?>
                        <?php echo $lang['lang_info_players_player_ip_camera_info']; ?>
                        <?php } ?>
                        </td>
                        </tr>
                    </table>
                  </div>
                  <div class="tab-page" id="tabPage1">
                    <h2 class="tab"><?php echo $lang['lang_info_players_player_ip_camera_aba_links']; ?></h2>
                    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
                      <tr>
                        <td height="200" align="center" class="texto_padrao_vermelho_destaque">
                        <?php if($_POST["camera_stream"]) { ?>
                        <table width="690" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td width="230" align="center" valign="top" class="texto_padrao"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-android.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea readonly="readonly" style="width:220px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" title="Ouvir no iphone" /></a></textarea>
                <br /></td>
            <td width="230" align="center" class="texto_padrao"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-blackberry.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea readonly="readonly" style="width:220px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" title="Ouvir no iphone" /></a></textarea>
                <br /></td>
            <td width="230" align="center" valign="top" class="texto_padrao"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea readonly="readonly" style="width:220px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" title="Ouvir no iphone" /></a></textarea></td>
          </tr>
          <tr>
            <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8" /></td>
            <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8" /></td>
            <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8" /></td>
          </tr>
          <tr>
            <td align="center"><textarea readonly="readonly" style="width:220px; height:50px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8" title="Android" /></textarea></td>
            <td align="center"><textarea readonly="readonly" style="width:220px; height:50px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8" title="BlackBerry" /></textarea></td>
            <td align="center"><textarea readonly="readonly" style="width:220px; height:50px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["camera_stream"]; ?>/playlist.m3u8" title="IOS" /></textarea></td>
          </tr>
        </table>
						<?php } else { ?>
                        <?php echo $lang['lang_info_players_player_ip_camera_info']; ?>
                        <?php } ?>
                        </td>
                        </tr>
                    </table>
                  </div>
                  </div></td>
              </tr>
            </table>
            </div>
              </div>
                </td>
              </tr>
            </table>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>