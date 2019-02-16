<?php
require_once("admin/inc/protecao-final.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<div id="quadro">
            	<div id="quadro-topo"><strong>Downloads</strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-filezilla.png" alt="Download" width="64" height="64" /></td>
    <td width="25%" align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-adobe.png" alt="Download" width="64" height="64" /></td>
    <td width="25%" align="center" class="texto_padrao">&nbsp;</td>
    <td width="25%" height="70" align="center" class="texto_padrao">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="texto_padrao">FileZilla FTP<br />
        <span class="texto_padrao_pequeno">Windows XP/7/8</span><br />
      <a href="http://ufpr.dl.sourceforge.net/project/filezilla/FileZilla_Client/3.16.0/FileZilla_3.16.0_win32-setup.exe" class="texto_padrao_verde">[download]</a></td>
    <td align="center" class="texto_padrao">Adobe Flash Media Live Encoder<br />
        <span class="texto_padrao_pequeno">Windows XP/7/8</span><br />
      <a href="http://download.macromedia.com/pub/flashmediaserver/flashmedialiveencoder/installer/flashmedialiveencoder_3.2_wwe_signed.msi" class="texto_padrao_verde">[download]</a></td>
    <td align="center" class="texto_padrao">&nbsp;</td>
    <td height="70" align="center" class="texto_padrao">&nbsp;</td>
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