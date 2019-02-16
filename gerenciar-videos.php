<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 300);

require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ftp.php");

$login_code = code_decode($_SESSION["login_logado"],"E");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/ajax-streaming-videos.js"></script>
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
    carregar_pastas('<?php echo $login_code; ?>');
	fechar_log_sistema();
   };
   window.onkeydown = function (event) {
		if (event.keyCode == 27) {
			document.getElementById('log-sistema-fundo').style.display = 'none';
			document.getElementById('log-sistema').style.display = 'none';
		}
	}
</script>
</head>

<body>
<div id="sub-conteudo">
    <table width="890" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="310" height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_videos_pastas']; ?></td>
        <td width="580" height="25" align="left" class="texto_padrao_destaque" style="padding-left:9px;"><?php echo $lang['lang_info_gerenciador_videos_videos_pasta']; ?></td>
      </tr>
      <tr>
        <td align="left" style="padding-left:5px;">
        <div id="borda_lista_pastas" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:285px; height:350px; text-align:left; float:left; padding:5px; overflow: auto;">
        <span id="status_lista_pastas" class="texto_padrao_pequeno"></span>
		<ul id="lista-pastas">
		</ul>
		</div>		</td>
        <td align="left">
        <div id="videos" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:560px; height:350px; text-align:left; float:right; padding:5px; overflow: auto;">
        <span id="msg_pasta" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_videos_info_lista_videos']; ?></span>
        <ul id="lista-videos-pasta">
        </ul>
        </div>        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;"><img src="/img/icones/img-icone-cadastrar.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:criar_pasta('<?php echo $login_code; ?>');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_videos_botao_criar_pasta']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-atualizar.png" width="16" height="16" align="absmiddle" border="0" />&nbsp;<a href="javascript:carregar_pastas('<?php echo $login_code; ?>');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_videos_botao_recarregar_pastas']; ?></a>&nbsp;</td>
        <td rowspan="2" align="right">
        <table width="571" border="0" align="right" cellpadding="0" cellspacing="0" style="margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
          <tr>
            <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
            <td width="541" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_videos_info_ftp']; ?></td>
          </tr>
         </table>
         <br />
		<table width="571" border="0" align="right" cellpadding="0" cellspacing="0" style="background-color:#FFFF66; border:#DFDF00 1px solid">
          <tr>
            <td width="30" height="25" align="center" scope="col"><img src="/img/icones/img-icone-bloqueado.png" width="16" height="16" /></td>
            <td width="541" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_videos_info_bloqueado']; ?></td>
          </tr>
         </table>
         </td>
      </tr>
      <tr>
        <td align="center" valign="top">
        <div style="padding-top:20px;padding-left:80px;"><span id="estatistica_uso_plano_ftp"></span></div>
		<span class="texto_padrao_pequeno">(<?php echo tamanho($dados_stm["espaco_usado"]); ?> / <?php echo tamanho($dados_stm["espaco"]); ?>)</span>        </td>
      </tr>
    </table>
    <br />
    <br />
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';document.getElementById('log-sistema-conteudo').innerHTML = '';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
<script type="text/javascript">
// Checar o status dos streamings
estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','ftp','nao');
</script>
</body>
</html>