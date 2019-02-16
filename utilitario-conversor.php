<?php
set_time_limit(0);
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/ajax-streaming.js"></script>
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="/inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
<script type="text/javascript">
function preset( preset ) {

array_configuracoes = preset.split("|");

document.getElementById("video_bitrate").value = array_configuracoes[0];
document.getElementById("audio_bitrate").value = array_configuracoes[1];

}

function validar_bitrate( bitrate ) {

if(bitrate > <?php echo $dados_stm["bitrate"]; ?>) {
alert("<?php echo $lang['lang_info_utilitario_conversor_resultado_alerta_bitrate_total']; ?>");
document.getElementById('video_bitrate').value = "";
}

}
</script>
</head>

<body>
<div id="sub-conteudo-pequeno">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<div id="quadro_requisicao" style="display:none">
  <div id="quadro">
            <div id="quadro-topo"><strong><?php echo $lang['lang_info_utilitario_conversor_tab_resultado']; ?></strong></div>
          	<div class="texto_medio" id="quadro-conteudo">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td align="center" class="texto_padrao"><img src="/img/ajax-loader.gif" width="220" height="19" id="img_loader" /><br />
                  <div id="resultado_requisicao" style="width:98%; height:200px; border:#999999 1px solid; text-align:left; overflow-y:scroll; padding:5px; background-color:#F4F4F7" class="texto_padrao"></div></td>
                </tr>
              </table>
          </div>
        </div>
<br />
</div>
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_utilitario_conversor_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; background-color: #C1E0FF; border: #006699 1px solid">
      <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/img/icones/ajuda.gif" width="16" height="16" /></td>
        <td width="660" align="left" class="texto_padrao" scope="col"><span class="texto_padrao"><?php echo $lang['lang_info_utilitario_conversor_info']; ?></span></td>
      </tr>
    </table>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_utilitario_conversor_aba_geral']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid; border-bottom:#CCCCCC 1px solid;">
      <tr>
        <td width="180" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilitario_conversor_video']; ?></td>
        <td width="510" align="left" class="texto_padrao">
          <select name="video" class="input" id="video" style="width:255px;">
                <option selected="selected"><?php echo $lang['lang_info_utilitario_conversor_selecione']; ?></option>
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
	
		echo '<optgroup label="' .$pasta_label. '">';
		
			for($ii=0;$ii<$total_videos_pasta;$ii++){

						echo '<option value="'.$pasta.'|'.$xml_videos->video[$ii]->nome.'|'.$xml_videos->video[$ii]->framerate.'">['.$xml_videos->video[$ii]->duracao.'] '.$xml_videos->video[$ii]->nome.' ('.$xml_videos->video[$ii]->width.'x'.$xml_videos->video[$ii]->height.' @ '.$xml_videos->video[$ii]->bitrate.' Kbps)</option>\n';
				
			}

			echo '</optgroup>';

		}
		
	}
	
}

?>
         </select>
		</td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilitario_conversor_preset']; ?></td>
        <td align="left" class="texto_padrao">
          <select style="width:255px;" onChange="preset(this.value);">
          <option value="" selected="selected"><?php echo $lang['lang_info_utilitario_conversor_selecione']; ?></option>
          <option value="256|64"><?php echo $lang['lang_info_utilitario_conversor_preset_baixa']; ?></option>
          <option value="400|96"><?php echo $lang['lang_info_utilitario_conversor_preset_media']; ?></option>
          <option value="720|96"><?php echo $lang['lang_info_utilitario_conversor_preset_alta']; ?></option>
          <option value="1024|128"><?php echo $lang['lang_info_utilitario_conversor_preset_hd']; ?></option>
          </select>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_utilitario_conversor_preset_info']; ?>');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilitario_conversor_remover_original']; ?></td>
        <td align="left" class="texto_padrao"><input id="remover_source" type="checkbox" value="sim" checked />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_utilitario_conversor_remover_original_info']; ?>');" style="cursor:pointer" /></td>
      </tr>
    </table>
    </div>
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_utilitario_conversor_aba_video']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid; border-bottom:#CCCCCC 1px solid;">
      <tr>
        <td width="180" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilitario_conversor_video_resolucao']; ?></td>
        <td width="510" align="left" class="texto_padrao">
			<select name="video_resolucao" class="input" id="video_resolucao" style="width:255px;">
                <option value="" selected="selected"><?php echo $lang['lang_info_utilitario_conversor_selecione']; ?></option>
                <option value="" selected="selected"><?php echo $lang['lang_info_utilitario_conversor_nao_alterar']; ?></option>
				<option value="230x240">240p (230x240)</option>
                <option value="640x360">360p (640x360)</option>
                <option value="720x480">480p (720x480)</option>
                <option value="1280x720">720p (1280x720)</option>
                <option value="1920x1080">1080p (1920x1080)</option>
			</select>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_utilitario_conversor_info_nao_alterar']; ?>');" style="cursor:pointer" />
            </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilitario_conversor_video_framerate']; ?></td>
        <td align="left" class="texto_padrao">
        <select name="video_framerate" class="input" id="video_framerate" style="width:255px;">
                <option value="" selected="selected"><?php echo $lang['lang_info_utilitario_conversor_selecione']; ?></option>
                <option value="" selected="selected"><?php echo $lang['lang_info_utilitario_conversor_nao_alterar']; ?></option>
				<option value="24">24</option>
                <option value="30">30</option>
		</select>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_utilitario_conversor_info_nao_alterar']; ?>');" style="cursor:pointer" />
        </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilitario_conversor_video_bitrate']; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="video_bitrate" type="number" class="input" id="video_bitrate" style="width:250px;" onblur="validar_bitrate(this.value);" />&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_utilitario_conversor_info_nao_alterar']; ?>');" style="cursor:pointer" />&nbsp;(bitrate video + bitrate audio = bitrate total)</td>
      </tr>
    </table>
    </div>
    <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_utilitario_conversor_aba_audio']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid; border-bottom:#CCCCCC 1px solid;">
      <tr>
        <td width="180" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilitario_conversor_audio_bitrate']; ?></td>
        <td width="510" align="left" class="texto_padrao_pequeno">
			<select name="audio_bitrate" class="input" id="audio_bitrate" style="width:255px;">
                <option value="" selected="selected"><?php echo $lang['lang_info_utilitario_conversor_selecione']; ?></option>
                <option value="" selected="selected"><?php echo $lang['lang_info_utilitario_conversor_nao_alterar']; ?></option>
				<option value="32">32 kbps</option>
                <option value="48">48 kbps</option>
                <option value="56">56 kbps</option>
                <option value="64">64 kbps</option>
                <option value="96">96 kbps</option>
                <option value="112">112 kbps</option>
                <option value="128">128 kbps</option>
                <option value="160">160 kbps</option>
                <option value="192">192 kbps</option>
                <option value="224">224 kbps</option>
                <option value="256">256 kbps</option>
                <option value="320">320 kbps</option>
			</select>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_utilitario_conversor_info_nao_alterar']; ?>');" style="cursor:pointer" />&nbsp;(bitrate video + bitrate audio = bitrate total)</td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilitario_conversor_audio_samplerate']; ?></td>
        <td align="left" class="texto_padrao">
        <select name="audio_samplerate" class="input" id="audio_samplerate" style="width:255px;">
                <option value="" selected="selected"><?php echo $lang['lang_info_utilitario_conversor_selecione']; ?></option>
                <option value="" selected="selected"><?php echo $lang['lang_info_utilitario_conversor_nao_alterar']; ?></option>
				<option value="44100">44,100 Hz</option>
                <option value="48000">48,000 Hz</option>
                <option value="32000">32,000 Hz</option>
                <option value="22050">22,500 Hz</option>
		</select>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_utilitario_conversor_info_nao_alterar']; ?>');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
        <td align="left" class="texto_padrao">&nbsp;</td>
      </tr>
    </table>
    </div>
    </div>
    </td>
  </tr>
  <tr>
    <td height="40" align="center"><input type="button" class="botao" value="<?php echo $lang['lang_info_utilitario_conversor_botao_converter']; ?>" onclick="converter_video('<?php echo $dados_servidor["ip"]; ?>', '<?php echo $dados_stm["login"]; ?>' );" /></td>
  </tr>
</table>
    </div>
    </div>
</div>
<br />
<br />
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo">
<div class="meter">
	<span style="width: 100%"></span>
</div>
</div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
