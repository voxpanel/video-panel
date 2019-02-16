<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".$dados_stm["ultima_playlist"]."'"));
$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."'"));
$total_agendamentos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."'"));

$limite_espectadores = ($dados_stm["espectadores"] == 999999) ? '<span class="texto_ilimitado">'.$lang['lang_info_ilimitado'].'</span>' : $dados_stm["espectadores"];

$login_code = code_decode($dados_stm["login"],"E");

$url_source_http = "http://".dominio_servidor($dados_servidor["nome"])."/".$dados_stm["login"]."/".$dados_stm["login"]."/playlist.m3u8";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="http://vjs.zencdn.net/6.0.0/video-js.css" rel="stylesheet">
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
	// Status de exibição dos quadros
	document.getElementById('tabela_info_stm').style.display=getCookie('tabela_info_stm');
	document.getElementById('tabela_info_plano').style.display=getCookie('tabela_info_plano');
	<?php if($dados_stm["aplicacao"] == 'live' || $dados_stm["aplicacao"] == 'tvstation') { ?>
	document.getElementById('tabela_player').style.display=getCookie('tabela_player');
	<?php } ?>
	document.getElementById('tabela_gerenciamento_streaming').style.display=getCookie('tabela_gerenciamento_streaming');
	document.getElementById('tabela_gerenciamento_ondemand').style.display=getCookie('tabela_gerenciamento_ondemand');
	document.getElementById('tabela_painel').style.display=getCookie('tabela_painel');
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
<?php if($dados_servidor["status"] == "on") { ?>
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<?php 
$total_dicas_rapidas = mysql_num_rows(mysql_query("SELECT * FROM video.dicas_rapidas where exibir = 'sim'"));

if($total_dicas_rapidas > 0) {

$dados_dica_rapida = mysql_fetch_array(mysql_query("SELECT * FROM video.dicas_rapidas where exibir = 'sim' ORDER BY RAND() LIMIT 1"));

$dados_dicas_rapidas_acesso = mysql_fetch_array(mysql_query("SELECT * FROM video.dicas_rapidas_acessos where codigo_stm = '".$dados_stm["codigo"]."' AND codigo_dica = '".$dados_dica_rapida["codigo"]."'"));

if($dados_dicas_rapidas_acesso["total"] < 10) {

if($dados_dicas_rapidas_acesso["total"] == 0) {
mysql_query("INSERT INTO video.dicas_rapidas_acessos (codigo_stm,codigo_dica,total) VALUES (".$dados_stm["codigo"].",'".$dados_dica_rapida["codigo"]."','1')");
} else {
mysql_query("Update video.dicas_rapidas_acessos set total = total+1 where codigo = '".$dados_dicas_rapidas_acesso["codigo"]."'");
}

$dica_rapida = str_replace("PAINEL","http://".$_SERVER['HTTP_HOST']."",$dados_dica_rapida["mensagem"]);
$dica_rapida = str_replace("LOGIN","".$dados_stm["login"]."",$dica_rapida);
?>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
<tr>
            <td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
            <td width="870" align="left" class="texto_padrao_destaque" scope="col"><?php echo $dica_rapida; ?></td>
    </tr>
</table>
<?php
}
}
?>
<?php if($dados_stm["status"] == 1) { ?>
<?php if(carregar_avisos_streaming($dados_stm["login"],$dados_servidor["codigo"])) { ?>
<table width="900" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
      <td width="885" height="50" align="center" valign="top">
      <div id="quadro">
            	<div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_avisos');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_avisos']; ?></strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
            		  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="display:block" id="tabela_avisos">
                        <tr>
                          <td height="25" class="texto_padrao">
						  <?php
							echo carregar_avisos_streaming($dados_stm["login"],$dados_servidor["codigo"]);
						  ?>
                          </td>
                        </tr>
                      </table>
            		</div>
      </div>      </td>
    </tr>
  </table>
<?php } ?>
<?php if($dados_stm["aplicacao"] == 'live' || $dados_stm["aplicacao"] == 'tvstation') { ?>
  <table width="900" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td width="350" align="center" valign="top" style="padding-right:5px"><div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_info_stm');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_streaming']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="335" border="0" cellpadding="0" cellspacing="0" style="display:block" id="tabela_info_stm">
              <tr>
                <td width="167" height="25" align="center" bgcolor="#F8F8F8" class="texto_padrao_destaque"><?php echo $lang['lang_info_login']; ?>&nbsp;</td>
                <td width="167" align="center" bgcolor="#F8F8F8" class="texto_padrao_destaque"><?php echo $lang['lang_info_ip_conexao']; ?></td>
              </tr>
              <tr>
                <td height="40" align="center" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_stm["login"]; ?></td>
                <td height="40" align="center" bgcolor="#F8F8F8" class="texto_padrao"><?php echo ucfirst($dados_servidor["nome"]); ?></td>
              </tr>
              <tr>
                <td height="25" align="center" bgcolor="#F8F8F8" class="texto_padrao_destaque"><?php echo $lang['lang_info_espectadores']; ?></td>
                <td align="center" bgcolor="#F8F8F8" class="texto_padrao_destaque"><?php echo $lang['lang_info_bitrate']; ?></td>
              </tr>
              <tr>
                <td height="40" align="center" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $limite_espectadores; ?></td>
                <td height="40" align="center" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_stm["bitrate"]; ?> Kbps</td>
              </tr>
              <?php if($dados_stm["aplicacao"] == 'tvstation') { ?>
              <tr>
                <td height="25" align="center" bgcolor="#F8F8F8" class="texto_padrao_destaque"><?php echo $lang['lang_info_espaco_ftp']; ?></td>
                <td align="center" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;</td>
              </tr>
              <tr>
                <td height="40" align="center" bgcolor="#F8F8F8" class="texto_padrao"><?php echo tamanho($dados_stm["espaco"]); ?></td>
                <td height="40" align="center" bgcolor="#F8F8F8" class="texto_padrao">&nbsp;</td>
              </tr> 
              <?php } ?> 
              <?php if($dados_stm["aplicacao"] == 'live') { ?>
              <tr>
                <td height="25" align="center" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;</td>
                <td align="center" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;</td>
              </tr>
              <tr>
                <td height="40" align="center" bgcolor="#F8F8F8" class="texto_padrao">&nbsp;</td>
                <td height="40" align="center" bgcolor="#F8F8F8" class="texto_padrao">&nbsp;</td>
              </tr>
              <?php } ?>
              <tr>
                <td height="105" align="center" bgcolor="#F8F8F8" class="texto_padrao">&nbsp;</td>
                <td height="105" align="center" bgcolor="#F8F8F8" class="texto_padrao">&nbsp;</td>
              </tr>
            </table>
        </div>
      </div></td>
      <td width="550" rowspan="2" align="center" valign="top" style="padding-left:5px"><div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_player');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong>Player</strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="display:block;" id="tabela_player">
              <tr>
                <td align="center" style="height:300px">                
<style>.video-js .vjs-time-control{display: none;}.video-js .vjs-progress-control{display: none;}</style>
<video id="player_webtv" class="video-js vjs-big-play-centered" controls preload="auto" width="535" height="300" data-setup="{ 'fluid':true,'aspectRatio':'16:9' }" >
   <source src="<?php echo $url_source_http; ?>" type="application/x-mpegURL">
</video>
<script src="//vjs.zencdn.net/6.0.0/video.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.9.0/videojs-contrib-hls.min.js"></script>
<script>var myPlayer=videojs('player_webtv',{},function(){var player=this;player.on("pause",function(){player.one("play",function(){player.load();player.play();});});})</script>

                </td>
              </tr>
            </table>
        </div>
      </div></td>
    </tr>
  </table>
  <?php } else { ?>
  <table width="900" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td width="450" align="center" valign="top" style="padding-right:5px"><div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_info_stm');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_streaming']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="430" border="0" cellpadding="0" cellspacing="0" style="display:block" id="tabela_info_stm">
              <tr>
                <td width="100" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_login']; ?></td>
                <td width="330" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_stm["login"]; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_ip_conexao']; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo ucfirst($dados_servidor["nome"]); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;</td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno">&nbsp;</td>
              </tr>
            </table>
        </div>
      </div></td>
      <td width="450" align="center" valign="top" style="padding-left:5px"><div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_info_plano');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_plano']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="430" border="0" cellpadding="0" cellspacing="0" style="display:block" id="tabela_info_plano">
              <tr>
                <td width="100" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_espectadores']; ?></td>
                <td width="330" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $limite_espectadores; ?></td>
              </tr>
              <?php if($dados_stm["aplicacao"] == 'tvstation' || $dados_stm["aplicacao"] == 'vod') { ?>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_espaco_ftp']; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo tamanho($dados_stm["espaco"]); ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_bitrate']; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_stm["bitrate"]; ?> Kbps</td>
              </tr>
            </table>
        </div>
      </div></td>
    </tr>
  </table>
  <?php } ?>
  <?php if($dados_stm["aplicacao"] == 'tvstation') { ?>
  <table width="885" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-top:10px">
    <tr>
      <td width="885" align="center" valign="top">
      <table width="900" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="450" align="center" valign="top" style="padding-right:5px">
          <div id="quadro2">
            <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_gerenciamento_streaming');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_streaming']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
                <table width="430" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_gerenciamento_streaming">
                  <tr>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/dados-conexao','conteudo');"><img src="img/icones/img-icone-dados-conexao.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_dados_conexao']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_dados_conexao']; ?>&nbsp;</td>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-streaming','conteudo');"><img src="img/icones/img-icone-configuracoes.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>&nbsp;</td>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_players();"><img src="img/icones/img-icone-players.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>&nbsp;<span class="label label-amarelo"><?php echo $lang['lang_label_atualizado']; ?></span></td>
                  </tr>
                  <tr>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/espectadores-conectados','conteudo');"><img src="img/icones/img-icone-espectadores.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_espectadores_conectados']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_espectadores_conectados']; ?></td>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_estatisticas_streaming('<?php echo $login_code;?>');"><img src="img/icones/img-icone-estatistica.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?></td>
                    <td width="143" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_janela('/gravador',410,480);"><img src="img/icones/img-icone-rec.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gravar_transmissao']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gravar_transmissao']; ?></td>
                  </tr>
                  <tr>
                    <?php if($dados_stm["exibir_app_android"] == 'sim') { ?>
                    <td width="143" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/app-android','conteudo');"><img src="img/icones/img-icone-app-android-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_android']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_android']; ?></td>
                    <?php } ?>
                    <td width="143" height="80" align="center">&nbsp;</td>
                    <td width="143" height="80" align="center">&nbsp;</td>
                    </tr>
                </table>
            </div>
          </div></td>
          <td width="450" align="center" valign="top" style="padding-left:5px">
          <div id="quadro">
            <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_gerenciamento_ondemand');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_ondemand']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
                <table width="430" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_gerenciamento_ondemand">
                  <tr>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="menu_iniciar_playlist();"><img src="img/icones/img-icone-iniciar-playlist.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_iniciar_playlist']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_iniciar_playlist']; ?></td>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-videos','conteudo');"><img src="img/icones/img-icone-gerenciador-videos.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_videos']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_videos']; ?>&nbsp;</td>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/playlists','conteudo');"><img src="img/icones/img-icone-playlists.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_playlists']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_playlists']; ?>&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-agendamentos','conteudo');"><img src="img/icones/img-icone-agendamento.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_agendamentos']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_agendamentos']; ?></td>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-playlists-comerciais','conteudo');"><img src="img/icones/img-icone-comerciais.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_comerciais']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_comerciais']; ?></td>
                    <td width="143" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-renomear-videos','conteudo');"><img src="img/icones/img-icone-ferramenta-renomear-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_renomear_videos']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_renomear_videos']; ?></td>
                  </tr>
                  <tr>
                    <td width="143" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-conversor','conteudo');"><img src="img/icones/img-icone-conversor.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_utilitario_conversor']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_utilitario_conversor']; ?></td>
                    <td width="143" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-youtube','conteudo');"><img src="img/icones/img-icone-youtube-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_ferramenta_youtube']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_ferramenta_youtube']; ?>&nbsp;</td>
                    <td width="143" height="80" align="center" class="texto_padrao_destaque">&nbsp;</td>
                  </tr>
                </table>
            </div>
          </div></td>
        </tr>
      </table>
      </td>
    </tr>
  </table>
  <?php } ?>
  <?php if($dados_stm["aplicacao"] == 'live') { ?>
  <table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px">
<tr>
          <td width="885" align="center">
          <div id="quadro">
            <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_gerenciamento_streaming');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_streaming']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
                <table width="887" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_gerenciamento_streaming">
                  <tr>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/dados-conexao','conteudo');"><img src="img/icones/img-icone-dados-conexao.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_dados_conexao']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_dados_conexao']; ?>&nbsp;</td>
        <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-streaming','conteudo');"><img src="img/icones/img-icone-configuracoes.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>&nbsp;</td>
        <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_players();"><img src="img/icones/img-icone-players.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>&nbsp;<span class="label label-amarelo"><?php echo $lang['lang_label_atualizado']; ?></span></td>
        <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/espectadores-conectados','conteudo');"><img src="img/icones/img-icone-espectadores.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_espectadores_conectados']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_espectadores_conectados']; ?></td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_estatisticas_streaming('<?php echo $login_code;?>');"><img src="img/icones/img-icone-estatistica.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?></td>
                  <?php if($dados_stm["exibir_app_android"] == 'sim') { ?>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/app-android','conteudo');"><img src="img/icones/img-icone-app-android-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_android']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_android']; ?>&nbsp;</td>
                    <?php } ?>
                  </tr>
                </table>
            </div>
          </div></td>
          </tr>
      </table>
  <?php } ?>
  <?php if($dados_stm["aplicacao"] == 'vod') { ?>
  <table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px">
<tr>
          <td width="885" align="center">
          <div id="quadro">
            <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_gerenciamento_streaming');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_streaming']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
                <table width="887" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_gerenciamento_streaming">
                  <tr>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/dados-conexao','conteudo');"><img src="img/icones/img-icone-dados-conexao.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_dados_conexao']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_dados_conexao']; ?>&nbsp;</td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-streaming','conteudo');"><img src="img/icones/img-icone-configuracoes.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>&nbsp;</td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_players();"><img src="img/icones/img-icone-players.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>&nbsp;<span class="label label-amarelo"><?php echo $lang['lang_label_atualizado']; ?></span></td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/espectadores-conectados','conteudo');"><img src="img/icones/img-icone-espectadores.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_espectadores_conectados']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_espectadores_conectados']; ?></td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_estatisticas_streaming('<?php echo $login_code;?>');"><img src="img/icones/img-icone-estatistica.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?></td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-videos','conteudo');"><img src="img/icones/img-icone-gerenciador-videos.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_videos']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_videos']; ?>&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-conversor','conteudo');"><img src="img/icones/img-icone-conversor.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_utilitario_conversor']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_utilitario_conversor']; ?>&nbsp;</td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-youtube','conteudo');"><img src="img/icones/img-icone-youtube-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_ferramenta_youtube']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_ferramenta_youtube']; ?>&nbsp;</td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/espectadores-conectados','conteudo');">&nbsp;</td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_estatisticas_streaming('<?php echo $login_code;?>');">&nbsp;</td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-videos','conteudo');">&nbsp;</td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-renomear-videos','conteudo');">&nbsp;</td>
                  </tr>
                </table>
            </div>
          </div></td>
          </tr>
      </table>
  <?php } ?>
  <?php if($dados_stm["aplicacao"] == 'ipcamera') { ?>
  <table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px">
<tr>
          <td width="885" align="center">
          <div id="quadro">
            <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_gerenciamento_streaming');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_ip_camera']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
                <table width="887" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_gerenciamento_streaming">
                  <tr>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-ip-cameras','conteudo');"><img src="img/icones/img-icone-camera.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_ip_cameras']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_ip_cameras']; ?>&nbsp;</td>
              <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-streaming','conteudo');"><img src="img/icones/img-icone-configuracoes.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>&nbsp;</td>
              <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_players();"><img src="img/icones/img-icone-players.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>&nbsp;<span class="label label-amarelo"><?php echo $lang['lang_label_atualizado']; ?></span></td>
              <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/espectadores-conectados','conteudo');"><img src="img/icones/img-icone-espectadores.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_espectadores_conectados']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_espectadores_conectados']; ?></td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_estatisticas_streaming('<?php echo $login_code;?>');"><img src="img/icones/img-icone-estatistica.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?></td>
                    <td width="147" height="75">&nbsp;</td>
                  </tr>
                </table>
            </div>
          </div></td>
          </tr>
      </table>
  <?php } ?>
  <?php if($dados_stm["aplicacao"] == 'relayrtsp') { ?>
  <table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px">
<tr>
          <td width="885" align="center">
          <div id="quadro">
            <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_gerenciamento_streaming');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_streaming']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
                <table width="887" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_gerenciamento_streaming">
                  <tr>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-relay','conteudo');"><img src="img/icones/img-icone-relay.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config_relay']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config_relay']; ?>&nbsp;</td>
              <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-streaming','conteudo');"><img src="img/icones/img-icone-configuracoes.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>&nbsp;</td>
              <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_players();"><img src="img/icones/img-icone-players.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>&nbsp;<span class="label label-amarelo"><?php echo $lang['lang_label_atualizado']; ?></span></td>
              <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/espectadores-conectados','conteudo');"><img src="img/icones/img-icone-espectadores.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_espectadores_conectados']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_espectadores_conectados']; ?></td>
                    <td width="147" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_estatisticas_streaming('<?php echo $login_code;?>');"><img src="img/icones/img-icone-estatistica.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?></td>
                    <td width="147" height="75">&nbsp;</td>
                  </tr>
                </table>
            </div>
          </div></td>
          </tr>
      </table>
  <?php } ?>
  <table width="900" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-top:10px">
    <tr>
      <td width="885"><div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_painel');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_painel']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="887" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_painel">
              <tr>
                <td width="126" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-painel','conteudo');"><img src="img/icones/img-icone-configuracoes.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config_painel']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config_painel']; ?>&nbsp;<span class="label label-amarelo"><?php echo $lang['lang_label_atualizado']; ?></span></td>
              <td width="126" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/logs','conteudo');"><img src="img/icones/img-icone-logs-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_logs']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_logs']; ?></td>
          <td width="126" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/streaming-api','conteudo');"><img src="img/icones/img-icone-api.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_api']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_api']; ?>&nbsp;</td>
          <td width="126" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-painel','conteudo');"><img src="img/icones/img-icone-idioma.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_idioma']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_idioma']; ?>&nbsp;</td>
                <?php if($dados_revenda["stm_exibir_app_android_painel"] == 'sim') { ?>
                <td width="126" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/app-painel','conteudo');"><span class="texto_padrao_destaque" style="cursor:pointer"><img src="img/icones/img-icone-app-android-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_android']; ?>" width="48" height="48" /></span><br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_painel']; ?>&nbsp;</td>
                <?php } ?>
                <?php if($dados_revenda["stm_exibir_tutoriais"] == 'sim') { ?>
                <td width="126" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/ajuda','conteudo');"><img src="img/icones/img-icone-ajuda-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_ajuda']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_ajuda']; ?></td>
    <?php } ?>
                    <?php if($dados_revenda["stm_exibir_tutoriais"] == 'url') { ?>
                <td width="126" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="window.open('<?php echo $dados_revenda["url_tutoriais"]; ?>');"><img src="img/icones/img-icone-ajuda-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_ajuda']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_ajuda']; ?></td>
    <?php } ?>
                    <?php if($dados_revenda["stm_exibir_downloads"] == 'sim') { ?>
                <td width="126" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/downloads','conteudo');"><img src="img/icones/img-icone-download-64x64.png" title="Downloads" width="48" height="48" /> <br />
                    Downloads</td>
                <?php } ?>
              </tr>
            </table>
        </div>
      </div></td>
    </tr>
  </table>
  <?php } else { ?>
  <table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
        <td width="40" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
      <td width="860" align="left" class="texto_status_erro" scope="col"><?php echo $lang['lang_alerta_bloqueio']; ?></td>
    </tr>
    </table>
  <?php } ?>
  <?php } else { ?>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:15%; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
        <td width="180" height="150" align="center" scope="col"><img src="/img/icones/img-icone-manutencao-128x128.png" width="128" height="128" /></td>
      <td width="720" align="left" class="texto_status_erro_pequeno" scope="col" style="padding-left:5px; padding-right:5px"><?php echo $dados_servidor["mensagem_manutencao"];?></td>
    </tr>
    </table>
  <?php } ?>
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