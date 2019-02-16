<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

$login_code = code_decode($dados_stm["login"],"E");

if(query_string('1') == "FMLE") {

$dominio = dominio_servidor($dados_servidor["nome"]);
$login = $dados_stm["login"];
$stream = ($dados_stm["aplicacao"] == 'tvstation') ? "live" : $dados_stm["login"];

header('Content-disposition: attachment; filename=profile_fmle_'.$dados_stm["login"].'.xml');
header ("Content-Type:text/xml"); 
echo '<?xml version="1.0" encoding="UTF-8"?>
<flashmedialiveencoder_profile>
    <preset>
        <name>Custom</name>
        <description></description>
    </preset>
    <process>
        <video>
        <preserve_aspect></preserve_aspect>
        </video>
    </process>
	<capture>
        <video>
        <device></device>
        <crossbar_input>0</crossbar_input>
        <frame_rate>29.97</frame_rate>
        <size>
            <width></width>
            <height></height>
        </size>
        </video>
        <audio>
        <device></device>
        <crossbar_input>0</crossbar_input>
        <sample_rate>44100</sample_rate>
        <channels>2</channels>
        <input_volume>100</input_volume>
        </audio>
    </capture>
    <encode>
        <video>
        <format>H.264</format>
        <datarate>200;</datarate>
        <outputsize>320x240;</outputsize>
        <advanced>
            <profile>Baseline</profile>
            <level>3.1</level>
            <keyframe_frequency>5 Seconds</keyframe_frequency>
        </advanced>
        <autoadjust>
            <enable>false</enable>
            <maxbuffersize>1</maxbuffersize>
            <dropframes>
            <enable>false</enable>
            </dropframes>
            <degradequality>
            <enable>false</enable>
            <minvideobitrate></minvideobitrate>
            <preservepfq>false</preservepfq>
            </degradequality>
        </autoadjust>
        </video>
		<audio>
        <format>MP3</format>
        <datarate>96</datarate>
        </audio>
    </encode>
    <output>
        <rtmp>
        <url>rtmp://'.$dominio.':1935/'.$login.'</url>
        <backup_url></backup_url>
        <stream>'.$stream.'</stream>
        </rtmp>
    </output>
    <preview>
        <video>
        <input>
            <zoom>100%</zoom>
        </input>
        <output>
            <zoom>100%</zoom>
        </output>
        </video>
        <audio></audio>
    </preview>
</flashmedialiveencoder_profile>';
header("Expires: 0");
exit();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
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
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_streaming_dados_conexao_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <div class="tab-pane" id="tabPane1">
    <?php if($dados_stm["aplicacao"] == 'live' || $dados_stm["aplicacao"] == 'tvstation') { ?>
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_streaming_dados_conexao_aba_streaming']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_streaming_dados_conexao_servidor']; ?></td>
            <td align="left" class="texto_padrao_pequeno">rtmp://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:1935/<?php echo $dados_stm["login"]; ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Stream</td>
            <td align="left" class="texto_padrao_pequeno"><?php if($dados_stm["aplicacao"] == 'tvstation') { echo "live"; } else { echo $dados_stm["login"]; }?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Bitrate</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["bitrate"]; ?> Kbps (video + audio)</td>
          </tr>
          <?php if($dados_stm["autenticar_live"] == 'sim') { ?>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_streaming_dados_conexao_usuario']; ?></td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["login"]; ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_dados_conexao_senha']; ?></td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["senha_transmissao"]; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_dados_conexao_profile_fmle']; ?></td>
            <td align="left" class="texto_padrao_pequeno"><a href="/dados-conexao/FMLE">[Download]</a>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_streaming_dados_conexao_profile_fmle_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
        </table>
      </div>
      <?php } ?>
      <?php if($dados_stm["aplicacao"] == 'tvstation' || $dados_stm["aplicacao"] == 'vod') { ?>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_streaming_dados_conexao_aba_ftp']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Servidor/Server/Host</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo dominio_servidor($dados_servidor["nome"]); ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_dados_conexao_usuario']; ?></td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["login"]; ?></td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_streaming_dados_conexao_senha']; ?></td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["senha"]; ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_dados_conexao_ftp_porta']; ?></td>
            <td align="left" class="texto_padrao_pequeno">21</td>
          </tr>
        </table>
      </div>
      <?php } ?>
      </div></td>
  </tr>
</table>
    </div>
      </div>
<br />
<br />
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