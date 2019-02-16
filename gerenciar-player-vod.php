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
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
    <tr>
      <th scope="col"><div id="quadro">
          <div id="quadro-topo"><strong><?php echo $lang['lang_info_players_tab_players']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="685" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
              <tr>
                <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;">
                <select name="players" class="input" id="players" style="width:98%;" onchange="window.open(this.value,'conteudo');">
                	<option value="/gerenciar-player-vod" selected="selected"><?php echo $lang['lang_info_players_player_selecione']; ?></option>
                    <option value="/gerenciar-player-vod"><?php echo $lang['lang_info_players_player_vod']; ?></option>
                    <option value="/gerenciar-player-facebook"><?php echo $lang['lang_info_players_player_facebook']; ?></option>
                  </select>
                </td>
              </tr>
            </table>
        </div>
      </div></th>
    </tr>
  </table>
  <table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
                <td scope="col">
            <div id="quadro">
            <div id="quadro-topo"><strong><?php echo $lang['lang_info_players_player_vod_tab_titulo_gerador']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
              <tr>
                <td>
                <div class="tab-pane" id="tabPane1">
                  <div class="tab-page" id="tabPage1">
                    <h2 class="tab"><?php echo $lang['lang_info_players_player_vod_aba_configuracoes']; ?></h2>
                    <form action="/gerenciar-player-vod" method="post">
                    <table width="685" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
                      <tr>
                        <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_vod_video']; ?></td>
                        <td width="715" align="left"><select name="video" class="input" id="video" style="width:255px;">
                <option selected="selected"><?php echo $lang['lang_info_players_player_vod_selecione']; ?></option>
<?php

$xml_pastas = @simplexml_load_file("http://".$dados_servidor["ip"].":55/listar-pastas.php?login=".$dados_stm["login"]."");
	
$total_pastas = count($xml_pastas->pasta);

if($total_pastas > 0) {

	for($i=0;$i<$total_pastas;$i++){
	
		$pasta = $xml_pastas->pasta[$i]->nome;
		
		$xml_videos = @simplexml_load_file("http://".$dados_servidor["ip"].":55/listar-videos.php?login=".$dados_stm["login"]."&pasta=".$pasta."&ordenar=nao");
	
		$total_videos_pasta = count($xml_videos->video);
		
		if($total_videos_pasta > 0) {
		
		$pasta_label = ($pasta == "/") ? "/ (root)" : $pasta;

		$path_separacao = ($pasta == "/" || $pasta == "") ? "" : "/";
		$pasta = ($pasta == "/") ? $path_separacao : $pasta.$path_separacao;
	
		echo '<optgroup label="' .$pasta_label. '">';
		
			for($ii=0;$ii<$total_videos_pasta;$ii++){
				
				$total_videos_compativeis = 0;

				if($xml_videos->video[$ii]->bitrate < $dados_stm["bitrate"]) { // Verifica limite bitrate
				
					if(!preg_match('/[^A-Za-z0-9\_\-\. ]/',$xml_videos->video[$ii]->nome)) { // Verifica caracteres especiais nome video
					
						echo '<option value="'.$pasta.$xml_videos->video[$ii]->nome.'|'.$xml_videos->video[$ii]->thumb.'">['.$xml_videos->video[$ii]->duracao.'] '.$xml_videos->video[$ii]->nome.' ('.$xml_videos->video[$ii]->width.'x'.$xml_videos->video[$ii]->height.' @ '.$xml_videos->video[$ii]->bitrate.' Kbps)</option>';
					$total_videos_compativeis +=1;
					}
				
				}
				
			}
			
			if($total_videos_compativeis == 0) {
			echo '<option disabled="disabled">'.$lang['lang_info_players_player_vod_sem_videos'].'</option>';
			}
			echo '</optgroup>';

		}
		
	}
	
}

?>
                </select></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Player</td>
                        <td align="left"><select class="input" name="modelo" style="width:305px;">
          		<option value="1" selected="selected">VideoJS</option>
		  		<option value="2">Clappr</option>
	         	</select></td>
                      </tr>
                      <tr>
                        <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_vod_largura']; ?></td>
                        <td width="720" align="left"><input type="number" name="largura" style="width:250px" value="320" /></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_vod_altura']; ?></td>
                        <td align="left"><input type="number" name="altura" style="width:250px" value="240" /></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_vod_aspectratio']; ?></td>
                        <td align="left"><select class="input" name="aspectratio" style="width:255px;">
          		<option value="16:9" selected="selected">Wide Screen 16:9</option>
		  		<option value="4:3">Default 4:3</option>
	         	</select></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_players_player_vod_autoplay']; ?></td>
                        <td align="left">
                        <input name="autoplay" type="checkbox" value="true" style="vertical-align:middle" />
                        &nbsp;<?php echo $lang['lang_label_sim']; ?></td>
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
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Loop</td>
                        <td align="left">
                        <input name="loop" type="checkbox" value="true" style="vertical-align:middle" />
                        &nbsp;<?php echo $lang['lang_label_sim']; ?></td>
                      </tr>
                      <tr>
                        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
                        <td align="left"><input type="submit" class="botao" value="OK" /></td>
                      </tr>
                    </table>
                    </form>
                  </div>
                  <div class="tab-page" id="tabPage1">
                    <h2 class="tab"><?php echo $lang['lang_info_players_player_vod_aba_codigo_html']; ?></h2>
                    <table width="685" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
                      <tr>
                        <td height="170" align="center" class="texto_padrao_vermelho_destaque" style="padding:5px">
                        <?php if($_POST["video"]) { ?>
<?php
$largura = ($_POST["responsivo"] == "true") ? '100%' : $_POST["largura"]."px";
$altura = ($_POST["responsivo"] == "true") ? '100%' : $_POST["altura"]."px";
$autoplay = ($_POST["autoplay"] == "true") ? "true" : "false";
$mudo = ($_POST["mute"] == "true") ? "true" : "false";
$loop = ($_POST["loop"] == "true") ? "true" : "false";
list($video_selecionado, $thumb_video_selecionado) = explode("|",$_POST["video"]);
?>
<textarea readonly="readonly" style="width:99%; height:224px;font-size:11px" onmouseover="this.select()"><iframe style="width:<?php echo $largura; ?>; height:<?php echo $altura; ?>;" src="http://<?php echo $url_player; ?>/video/<?php echo $dados_stm["login"]; ?>/<?php echo $_POST["modelo"]; ?>/<?php echo $autoplay; ?>/<?php echo $mudo; ?>/<?php echo code_decode(dominio_servidor($dados_servidor["nome"]),"E"); ?>/<?php echo $_POST["aspectratio"]; ?>/<?php echo code_decode('http://'.dominio_servidor($dados_servidor["nome"]).':55/'.$thumb_video_selecionado.'',"E"); ?>/<?php echo code_decode($video_selecionado,"E"); ?>/<?php echo $loop; ?>" scrolling="no" frameborder="0" allowfullscreen></iframe></textarea>
						<?php } else { ?>
                        <?php echo $lang['lang_info_players_player_vod_info']; ?>
                        <?php } ?>
                        </td>
                        </tr>
                    </table>
                  </div>
                  <div class="tab-page" id="tabPage1">
                    <h2 class="tab"><?php echo $lang['lang_info_players_player_vod_aba_links']; ?></h2>
                    <table width="685" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
                      <tr>
                        <td height="200" align="center" class="texto_padrao_vermelho_destaque">
                        <?php if($_POST["video"]) { ?>
                        <table width="685" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td width="230" align="center" valign="top" class="texto_padrao"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-android.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea readonly="readonly" style="width:220px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" title="Ouvir no iphone" /></a></textarea>
                <br /></td>
            <td width="230" align="center" class="texto_padrao"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-blackberry.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea readonly="readonly" style="width:220px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" title="Ouvir no iphone" /></a></textarea>
                <br /></td>
            <td width="230" align="center" valign="top" class="texto_padrao"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea readonly="readonly" style="width:220px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" title="Ouvir no iphone" /></a></textarea></td>
          </tr>
          <tr>
            <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8" /></td>
            <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8" /></td>
            <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8" /></td>
          </tr>
          <tr>
            <td align="center"><textarea readonly="readonly" style="width:220px; height:50px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8" title="Android" /></textarea></td>
            <td align="center"><textarea readonly="readonly" style="width:220px; height:50px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8" title="BlackBerry" /></textarea></td>
            <td align="center"><textarea readonly="readonly" style="width:220px; height:50px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/mp4:<?php echo $video_selecionado; ?>/playlist.m3u8" title="IOS" /></textarea></td>
          </tr>
        </table>
						<?php } else { ?>
                        <?php echo $lang['lang_info_players_player_vod_info']; ?>
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