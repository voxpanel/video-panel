<?php
require_once("inc/protecao-final-movel.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

$cor_status = ($dados_stm["status"] == 1) ? "#FFFFFF" : "#FFB3B3";

$login_code = code_decode($dados_stm["login"],"E");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link href="inc/estilo-movel.css" rel="stylesheet" type="text/css" />
<link href="http://vjs.zencdn.net/5.8.0/video-js.css" rel="stylesheet">
<style>.video-js .vjs-time-control{display: none;}.video-js .vjs-progress-control{display: none;}</style>
<script src="http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
   status_streaming('<?php echo $login_code; ?>');
   setInterval("status_streaming('<?php echo $login_code; ?>')",60000);
   estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','espectadores','sim');
   setInterval("estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','espectadores','sim')",30000);
   <?php if($dados_stm["aplicacao"] == 'tvstation' || $dados_stm["aplicacao"] == 'vod') { ?>
   estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','ftp','sim');
   setInterval("estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','ftp','sim')",120000);
   <?php } ?>
   };
</script>
</head>

<body style="background-color:#000000;">
<?php if($dados_stm["status"] == "1") { ?>
<?php if($dados_servidor["status"] == "on") { ?>
<div id="topo">
<div id="topo-botao-ligar">
<img src="/img/icones/img-icone-ligar-64x64.png" title="<?php echo $lang['lang_botao_titulo_ligar_stm']; ?>" width="48" height="48" style="cursor:pointer" onclick="ligar_streaming('<?php echo $login_code;?>');" />
</div>
<div id="topo-botao-desligar">
<img src="/img/icones/img-icone-desligar-48x48.png" title="<?php echo $lang['lang_botao_titulo_desligar_stm']; ?>" width="32" height="32" style="cursor:pointer" onclick="desligar_streaming('<?php echo $login_code;?>');" />
</div>
<div id="topo-botao-reiniciar">
<img src="/img/icones/img-icone-reiniciar-48x48.png" title="<?php echo $lang['lang_botao_titulo_reiniciar_stm']; ?>" width="32" height="32" style="cursor:pointer" onclick="reiniciar_streaming('<?php echo $login_code;?>');" />
</div>
<div id="topo-menu" class="texto_padrao_pequeno">
    <select class="topo-menu-select" name="menu_executar_acao" onchange="executar_acao_streaming_movel('<?php echo $login_code; ?>',this.value);">
        <option value="" selected="selected"><?php echo $lang['lang_info_escolha_acao']; ?></option>
        <optgroup label="Streaming">
        <option value="informacoes"><?php echo $lang['lang_acao_stm_info']; ?></option>
        <?php if($dados_stm["aplicacao"] == 'live' || $dados_stm["aplicacao"] == 'tvstation') { ?>
        <option value="dados-conexao"><?php echo $lang['lang_acao_stm_dados_conexao']; ?></option>
        <option value="gravador"><?php echo $lang['lang_acao_stm_gravador']; ?></option>
        <?php } ?>
        <option value="espectadores-conectados"><?php echo $lang['lang_acao_espectadores_espectadores_conectados']; ?></option>
        <?php if($dados_stm["aplicacao"] == 'tvstation') { ?>
        <option value="iniciar-playlist"><?php echo $lang['lang_botao_titulo_iniciar_playlist']; ?></option>
        <?php } ?>
        </optgroup>
    </select>
</div>
</div>
<div id="conteudo">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#000000; margin-top:5px;">
  <tr>
    <td align="center">
    <div id="quadro">
       	  <div id="quadro-topo"><strong><?php echo $lang['lang_info_pagina_informacoes_tab_streaming']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
					  <table width="100%" border="0" cellpadding="0" cellspacing="0">
   						<tr>
						  <td width="22%" height="30" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_login']; ?></td>
						  <td width="78%" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_stm["login"]; ?></td>
   						</tr>
                        <tr>
						  <td height="30" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_ip_conexao']; ?></td>
						  <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo dominio_servidor($dados_servidor["nome"]); ?></td>
   						</tr>
					  </table>
		  </div>
      </div>
      </td>
  </tr>
  <tr>
    <td align="center" height="5"></td>
  </tr>
  <tr>
    <td align="center">
    <div id="quadro">
       <div id="quadro-topo"><strong><?php echo $lang['lang_info_pagina_informacoes_tab_plano_uso']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<td width="22%" height="30" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;Status</td>
   				<td width="78%" height="25" align="left" bgcolor="#F8F8F8" scope="col" class="texto_padrao"><span id="<?php echo $login_code; ?>" style="cursor:pointer" onclick="status_streaming('<?php echo $login_code; ?>')"></span></td>
   						</tr>
    						<tr>
                              <td height="30" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_espectadores']; ?></td>
    						  <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><span id="estatistica_uso_plano_espectadores" style="cursor:pointer" onclick="estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','espectadores','sim');"></span></td>
  						  </tr>
                          <?php if($dados_stm["aplicacao"] == 'tvstation' || $dados_stm["aplicacao"] == 'vod') { ?>
    						<tr>
                              <td height="30" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_espaco_ftp']; ?></td>
                              <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><span id="estatistica_uso_plano_ftp" style="cursor:pointer" onclick="estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','ftp','sim');"></span></td>
  						  </tr>
                          <?php } ?>
                          <tr>
                              <td height="30" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_bitrate']; ?></td>
    						  <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_stm["bitrate"]; ?>&nbsp;<span class="texto_padrao_pequeno">Kbps</span></td>
  						  </tr>
					  </table>
		  </div>
      </div>
      </td>
  </tr>
<?php if($dados_stm["aplicacao"] == 'live' || $dados_stm["aplicacao"] == 'tvstation') { ?>
  <tr>
    <td align="center" height="5"></td>
  </tr>
  <tr>
    <td align="center">
    <div id="quadro">
       <div id="quadro-topo"><strong>Player</strong></div>
        <div class="texto_status_streaming_offline" id="quadro-conteudo">
		<center>
        <video id="my-video" class="video-js" controls preload="auto" width="300" height="220" data-setup="">
           <source src="rtmp://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>" type="rtmp/mp4">
           <source src="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>/<?php echo $dados_stm["login"]; ?>/<?php echo $dados_stm["login"]; ?>/playlist.m3u8" type="application/x-mpegURL">
        Seu dispositivo não suporte este player.
        </video>
        <script src="http://vjs.zencdn.net/5.8.0/video.js"></script>
        </center>
		</div>
      </div>
      </td>
  </tr>
<?php } ?>
</table>
</div>
  <?php } else { ?>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:15%; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td height="30" align="left" class="texto_status_streaming_offline" style="padding:3px;" scope="col">
		<center><img src="../img/icones/img-icone-manutencao-128x128.png" width="64" height="64" /></center>
        <br />
		<?php echo $dados_servidor["mensagem_manutencao"];?>
        </td>
    </tr>
</table>
  <?php } ?>
  <?php } else { ?>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:15%; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td height="50" align="center" class="texto_status_erro" style="padding:3px;" scope="col"><?php echo $lang['lang_alerta_bloqueio']; ?></td>
    </tr>
</table>
  <?php } ?>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="Fechar" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>