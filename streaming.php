<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

if($_SESSION["code_user_logged"]) {
$verificacao_revenda_logada = ($dados_stm["codigo_cliente"] == $_SESSION["code_user_logged"]) ? true : false;
}

$login_code = code_decode($dados_stm["login"],"E");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>[<?php echo $dados_stm["login"]; ?>] Gerenciamento de Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript">
   window.onload = function() {
    // Carregar avisos do streaming na inicialização
	<?php
	carregar_avisos_streaming_inicializacao($dados_stm["login"],$dados_servidor["codigo"]);
	?>
	// Carregar informações do streaming na inicialização
	<?php if($dados_stm["aplicacao"] != 'vod') { ?>
	status_streaming('<?php echo $login_code; ?>');
	setInterval("status_streaming('<?php echo $login_code; ?>')",300000);
	<?php } ?>
	<?php if($dados_servidor["status"] == "on") { ?>
	<?php if($dados_stm["aparencia_exibir_stats_espectadores"] == 'sim') { ?>
	estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','espectadores','nao');
	setInterval("estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','espectadores','nao')",300000);
	<?php } ?>
	<?php if($dados_stm["aparencia_exibir_stats_ftp"] == 'sim') { ?>
	estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','ftp','nao');
	setInterval("estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','ftp','nao')",300000);
	<?php } ?>
	<?php } ?>
	calcular_altura_iframe('conteudo');
   };
   window.onkeydown = function (event) {
		if (event.keyCode == 27) {
			document.getElementById('log-sistema-fundo').style.display = 'none';
			document.getElementById('log-sistema').style.display = 'none';
			document.getElementById('log-sistema-conteudo').innerHTML = '';
			window.parent.document.getElementById('log-sistema-conteudo').innerHTML = '';
		}
	}
</script>
<style>
body {
	overflow: hidden;
}
</style>
</head>

<body>
<?php if($dados_stm["status"] == 1) { ?>
<?php if($verificacao_revenda_logada === true) { ?>
<div class="texto_padrao_vermelho" id="barra-alerta-revenda-logada">
<img src="/img/icones/atencao.png" width="16" height="16" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_acessar_painel_revenda_logado']; ?>
</div>
<?php } ?>
<div id="topo">
<div id="topo-logo"><img src="img/img-logo-topo.png" id="topo-logo-imagem" border="0" /></div>
<?php if($dados_stm["aplicacao"] != 'vod') { ?>
<div id="topo-botao-ligar">
<img src="img/icones/img-icone-ligar-64x64.png" title="<?php echo $lang['lang_botao_titulo_ligar_stm']; ?>" width="64" height="64" style="cursor:pointer" onclick="ligar_streaming('<?php echo $login_code;?>');" />
</div>
<div id="topo-botao-desligar">
<img src="img/icones/img-icone-desligar-48x48.png" title="<?php echo $lang['lang_botao_titulo_desligar_stm']; ?>" width="48" height="48" style="cursor:pointer" onclick="desligar_streaming('<?php echo $login_code;?>');" />
</div>
<div id="topo-botao-reiniciar">
<img src="img/icones/img-icone-reiniciar-48x48.png" title="<?php echo $lang['lang_botao_titulo_reiniciar_stm']; ?>" width="48" height="48" style="cursor:pointer" onclick="reiniciar_streaming('<?php echo $login_code;?>');" />
</div>
<?php } ?>
<?php if($dados_stm["aplicacao"] == 'vod') { ?>
<div id="topo-botao-reiniciar">
<img src="img/icones/img-icone-reiniciar-48x48.png" title="<?php echo $lang['lang_botao_titulo_reiniciar_stm']; ?>" width="64" height="64" style="cursor:pointer" onclick="reiniciar_streaming('<?php echo $login_code;?>');" />
</div>
<?php } ?>
<div id="topo-menu" class="texto_padrao_pequeno"><strong><?php echo $lang['lang_info_executar_acao']; ?></strong><br /><br />
<select class="topo-menu-select" id="<?php echo $login_code;?>" onchange='executar_acao_streaming(this.id,this.value);' <?php if($dados_servidor["status"] == "off") { echo 'disabled="disabled"'; }?> style="border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px;">
<option value="" selected="selected"><?php echo $lang['lang_info_escolha_acao']; ?></option>
  <?php if($dados_stm["exibir_atalhos"] == 'sim') { ?>
  <optgroup label="<?php echo $lang['lang_acao_label_atalhos']; ?>">
  <?php
  $sql_atalhos = mysql_query("SELECT * FROM video.atalhos WHERE codigo_stm = '".$dados_stm["codigo"]."' ORDER by ordem ASC");
  while ($dados_atalhos = mysql_fetch_array($sql_atalhos)) {
  
  echo "<option value='".$dados_atalhos["menu"]."'>".$lang[''.$dados_atalhos["lang"].'']."</option>\n";
  }
  ?>
  </optgroup>
  <?php } ?>
  <optgroup label="<?php echo $lang['lang_acao_label_streaming']; ?>">
  <option value='streaming-informacoes'><?php echo $lang['lang_acao_stm_info']; ?></option>
  <?php if($dados_stm["aplicacao"] == 'live' || $dados_stm["aplicacao"] == 'tvstation' || $dados_stm["aplicacao"] == 'vod') { ?>
  <option value='streaming-dados-conexao'><?php echo $lang['lang_acao_stm_dados_conexao']; ?></option>
  <?php } ?>
  <?php if($dados_stm["aplicacao"] == 'ipcamera') { ?>
  <option value='streaming-gerenciar-cameras'><?php echo $lang['lang_acao_stm_gerenciar_cameras']; ?></option>
  <?php } ?>
  <option value='streaming-configurar'><?php echo $lang['lang_acao_stm_config']; ?></option>
  <option value='streaming-players'><?php echo $lang['lang_acao_stm_players']; ?></option>
  <?php if($dados_stm["aplicacao"] == 'tvstation') { ?>
  <option value='streaming-gravador'><?php echo $lang['lang_acao_stm_gravador']; ?></option>
  <?php } ?>
  </optgroup>
  <optgroup label="<?php echo $lang['lang_acao_label_espectadores']; ?>">
  <option value='espectadores-espectadores-conectados'><?php echo $lang['lang_acao_espectadores_espectadores_conectados']; ?></option>
  <option value='espectadores-estatisticas'><?php echo $lang['lang_acao_espectadores_stats']; ?></option>
  </optgroup>
  <?php if($dados_stm["aplicacao"] == 'tvstation') { ?>
  <optgroup label="<?php echo $lang['lang_acao_label_ondemand']; ?>">
  <option value='ondemand-gerenciar-videos'><?php echo $lang['lang_acao_ondemand_gerenciar_videos']; ?></option>
  <option value='ondemand-gerenciar-playlists'><?php echo $lang['lang_acao_ondemand_gerenciar_playlists']; ?></option>
  <option value='ondemand-gerenciar-agendamentos'><?php echo $lang['lang_acao_ondemand_gerenciar_agendamentos']; ?></option>
  <option value='ondemand-gerenciar-comerciais'><?php echo $lang['lang_acao_ondemand_gerenciar_comerciais']; ?></option>
  </optgroup>
  <?php } ?>
  <?php if($dados_stm["aplicacao"] == 'vod') { ?>
  <optgroup label="<?php echo $lang['lang_acao_label_ondemand']; ?>">
  <option value='ondemand-gerenciar-videos'><?php echo $lang['lang_acao_ondemand_gerenciar_videos']; ?></option>
  </optgroup>
  <?php } ?>
  <optgroup label="<?php echo $lang['lang_acao_label_painel']; ?>">
  <option value='painel-configurar'><?php echo $lang['lang_acao_painel_config']; ?></option>
  <option value='painel-api'><?php echo $lang['lang_acao_painel_api']; ?></option>
  <option value='painel-logs'><?php echo $lang['lang_acao_painel_logs']; ?></option>
  <?php if($dados_revenda["stm_exibir_tutoriais"] == 'sim') { ?>
  <option value='painel-ajuda'><?php echo $lang['lang_acao_painel_ajuda']; ?></option>
  <?php } ?>
  <?php if($dados_revenda["stm_exibir_tutoriais"] == 'url') { ?>
  <option value='<?php echo $dados_revenda["url_tutoriais"]; ?>'><?php echo $lang['lang_acao_painel_ajuda']; ?></option>
  <?php } ?>
  <?php if($dados_revenda["stm_exibir_downloads"] == 'sim') { ?>
  <option value='painel-downloads'><?php echo $lang['lang_acao_painel_downloads']; ?></option>
  <?php } ?>
  </optgroup>
  <?php if($dados_stm["aplicacao"] == 'tvstation' || $dados_stm["aplicacao"] == 'vod') { ?>
  <optgroup label="<?php echo $lang['lang_acao_label_ferramentas']; ?>">
  <option value='utilitario-renomear-videos'><?php echo $lang['lang_acao_ferramentas_renomear_videos']; ?></option>
  <option value='utilitario-conversor'><?php echo $lang['lang_acao_ferramentas_conversor']; ?></option>
  <option value='utilitario-youtube'><?php echo $lang['lang_acao_ferramentas_youtube']; ?></option>
  </optgroup>
  <?php } ?>
  <optgroup label="<?php echo $lang['lang_acao_label_solucao_problemas']; ?>">
  <option value='solucao-problemas-sincronizar'><?php echo $lang['lang_acao_solucao_problemas_sincronizar']; ?></option>
  </optgroup>
</select>
</div>

<?php if($dados_stm["aparencia_exibir_stats_espectadores"] == 'sim' || $dados_stm["aparencia_exibir_stats_ftp"] == 'sim') { ?>
<div id="topo-estatisticas">
<?php if($dados_stm["aparencia_exibir_stats_espectadores"] == 'sim') { ?>
<div id="topo-estatisticas-espectadores" class="texto_padrao_pequeno"><strong><?php echo $lang['lang_info_espectadores_conectados']; ?></strong><br /><br />
<span id="estatistica_uso_plano_espectadores" style="cursor:pointer" onclick="estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','espectadores','nao');"></span>
</div>
<?php } ?>
<?php if(($dados_stm["aplicacao"] == 'tvstation' || $dados_stm["aplicacao"] == 'vod') && $dados_stm["aparencia_exibir_stats_ftp"] == 'sim') { ?>
<div id="topo-estatisticas-ftp" class="texto_padrao_pequeno"><strong><?php echo $lang['lang_info_uso_ftp']; ?></strong><br /><br />
<span id="estatistica_uso_plano_ftp" style="cursor:pointer" onclick="estatistica_uso_plano( '<?php echo $dados_stm["login"]; ?>','ftp','nao');"></span>
</div>
<?php } ?>
</div>
<?php } ?>
<div id="topo-botao-suporte-sair">
<?php if($dados_revenda["url_suporte"]) { ?>
<img src="img/icones/img-icone-suporte-64x64.png" title="<?php echo $lang['lang_titulo_suporte']; ?>" width="24" height="24" style="cursor:pointer" onclick="window.open('<?php echo $dados_revenda["url_suporte"]; ?>')" />&nbsp;
<?php } ?>
<img src="img/icones/img-icone-fechar.png" title="<?php echo $lang['lang_titulo_sair']; ?>" width="24" height="24" style="cursor:pointer" onclick="window.location = '/sair'" />
</div>
<?php if($dados_stm["aplicacao"] != 'vod') { ?>
<div id="topo-status" class="texto_padrao">
<span id="status_streaming" style="cursor:pointer" onclick="status_streaming('<?php echo $login_code; ?>')"></span>
</div>
<?php } ?>
</div>
<!-- Início iframe conteúdo -->
<iframe name="conteudo" id="conteudo" src="<?php echo $dados_stm["pagina_inicial"]; ?>" frameborder="0" width="100%" height="500" allowFullScreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
<!-- Fim iframe conteúdo -->
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
<?php } else { ?>
<div style="width:100%; height:700px;cursor: not-allowed; z-index:-1">
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo $lang['lang_alerta_bloqueio']; ?></td>
    </tr>
</table>
</div>
<?php } ?>
</body>
</html>