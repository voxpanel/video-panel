<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$url_player = (!empty($dados_revenda["dominio_padrao"])) ? "playerv.".$dados_revenda["dominio_padrao"]."" : "playerv.".$dados_config["dominio_padrao"]."";

$url_source_http = "http://".dominio_servidor($dados_servidor["nome"])."/".$dados_stm["login"]."/".$dados_stm["login"]."/playlist.m3u8";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="/inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
  <table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
    <tr>
      <th scope="col"><div id="quadro">
          <div id="quadro-topo"><strong><?php echo $lang['lang_info_players_tab_players']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
              <tr>
                <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;"><select name="players" class="input" id="players" style="width:98%;" onchange="window.open(this.value,'conteudo');">
                    <option value="/gerenciar-player-flash-html5"><?php echo $lang['lang_info_players_player_selecione']; ?></option>
                    <option value="/gerenciar-player-flash-html5"><?php echo $lang['lang_info_players_player_flash_html5']; ?></option>
                    <option value="/gerenciar-player-celulares"><?php echo $lang['lang_info_players_player_celulares']; ?></option>
                    <option value="/gerenciar-player-facebook"><?php echo $lang['lang_info_players_player_facebook']; ?></option>
                    <?php if($dados_stm["exibir_app_android"] == 'sim') { ?>
                    <option value="/app-android"><?php echo $lang['lang_info_players_player_app_android']; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>
            </table>
        </div>
      </div></th>
    </tr>
  </table>
<table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="padding-bottom:10px;">
  <tr>
    <th scope="col">
    	<div id="quadro">
        <div id="quadro-topo"><strong><?php echo $lang['lang_info_players_player_flash_html5']; ?></strong></div>
      	<div class="texto_medio" id="quadro-conteudo">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
              <tr>
                <td>
                <div class="tab-pane" id="tabPane1">
                 <?php if($_POST["modelo"] == "videojs") { ?>
                 <div class="tab-page" id="tabPage1">
                    <h2 class="tab"><?php echo $lang['lang_info_players_player_flash_html5_aba_codigo_html']; ?></h2>
                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
                      <tr>
                        <td align="center" class="texto_padrao_vermelho_destaque" style="padding:5px">
<?php
$classe_responsivo = ($_POST["responsivo"] == "true") ? 'vjs-fluid' : '';
$largura = ($_POST["responsivo"] == "true") ? '100%' : $_POST["largura"];
$altura = ($_POST["responsivo"] == "true") ? '100%' : $_POST["altura"];
$mudo = ($_POST["mute"] == "true") ? 'muted' : '';
$autoplay = ($_POST["autoplay"] == "true") ? 'autoplay' : '';
$capa = ($_POST["capa"] && $_POST["capa"] != "http://") ? 'poster="'.$_POST["capa"].'"' : '';
?>
<textarea readonly="readonly" style="width:99%; height:224px;font-size:11px" onmouseover="this.select()"><link href="http://vjs.zencdn.net/6.0.0/video-js.css" rel="stylesheet">
<style>.video-js .vjs-time-control{display: none;}.video-js .vjs-progress-control{display: none;}</style>
<video id="player_webtv" class="video-js vjs-big-play-centered <?php echo $classe_responsivo; ?>" <?php echo $autoplay; ?> <?php echo $mudo; ?> controls preload="auto" width="<?php echo $largura; ?>" height="<?php echo $altura; ?>" data-setup="{ 'fluid':true,'aspectRatio':'<?php echo $_POST["aspectratio"]; ?>' }" <?php echo $capa; ?>>
   <source src="<?php echo $url_source_http; ?>" type="application/x-mpegURL">
</video>
<script src="//vjs.zencdn.net/6.0.0/video.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.9.0/videojs-contrib-hls.min.js"></script>
<script>var myPlayer=videojs('player_webtv',{},function(){var player=this;player.on("pause",function(){player.one("play",function(){player.load();player.play();});});})</script></textarea>
                        </td>
                        </tr>
                    </table>
                  </div>
                <?php } ?>
                  <div class="tab-page" id="tabPage1">
                    <h2 class="tab"><?php echo $lang['lang_info_players_player_flash_html5_aba_player1']; ?></h2>
                    <form action="/gerenciar-player-flash-html5" method="post">
                    <table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
                      <tr>
                        <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_flash_html5_largura']; ?></td>
                        <td width="720" align="left"><input type="number" name="largura" style="width:300px" value="640" /></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_flash_html5_altura']; ?></td>
                        <td align="left"><input type="number" name="altura" style="width:300px" value="480" /></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_flash_html5_capa']; ?></td>
                        <td align="left" class="texto_padrao_pequeno"><input type="text" name="capa" style="width:300px" value="http://" />&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_players_player_flash_html5_capa_info']; ?>');" style="cursor:pointer" /></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_flash_html5_aspectratio']; ?></td>
                        <td align="left"><select class="input" name="aspectratio" style="width:305px;">
          		<option value="16:9" selected="selected">Wide Screen 16:9</option>
		  		<option value="4:3">Default 4:3</option>
	         	</select></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_flash_html5_autoplay']; ?></td>
                        <td align="left">
                        <input name="autoplay" type="checkbox" value="true" style="vertical-align:middle" />
                        &nbsp;<?php echo $lang['lang_label_sim']; ?>                        </td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_flash_html5_responsivo']; ?></td>
                        <td align="left">
                        <input name="responsivo" type="checkbox" value="true" style="vertical-align:middle" />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_players_player_flash_html5_responsivo_info']; ?>');" style="cursor:pointer" /></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_flash_html5_mudo']; ?></td>
                        <td align="left">
                        <input name="mute" type="checkbox" value="true" style="vertical-align:middle" />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_players_player_flash_html5_mudo_info']; ?>');" style="cursor:pointer" /></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
                        <td align="left"><input type="submit" class="botao" value="OK" />
                            <input name="modelo" type="hidden" id="modelo3" value="videojs" /></td>
                      </tr>
                    </table>
                    </form>
                  </div>
				</td>
              </tr>
            </table>
              </div>
      	</div>
      </th>
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