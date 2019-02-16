<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$url_player = (!empty($dados_revenda["dominio_padrao"])) ? "playerv.".$dados_revenda["dominio_padrao"]."" : "playerv.".$dados_config["dominio_padrao"]."";

if($dados_stm["aplicacao"] == "live" || $dados_stm["aplicacao"] == "tvstation") {
$url_source_http = "http://".dominio_servidor($dados_servidor["nome"])."/".$dados_stm["login"]."/".$dados_stm["login"]."/playlist.m3u8";
} elseif($dados_stm["aplicacao"] == "relayrtsp") {
$url_source_http = "http://".dominio_servidor($dados_servidor["nome"])."/".$dados_stm["login"]."/relay.stream/playlist.m3u8";
} else {
$url_source_http = "http://".dominio_servidor($dados_servidor["nome"])."/".$dados_stm["login"]."/".$dados_stm["login"]."/playlist.m3u8";
}
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
    <th scope="col"> <div id="quadro">
        <div id="quadro-topo"> <strong><?php echo $lang['lang_info_players_player_celulares']; ?></strong></div>
      <div class="texto_medio" id="quadro-conteudo">
        <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td width="290" align="center" valign="top" class="texto_padrao"><a href="<?php echo $url_source_http; ?>"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-android.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><a href="<?php echo $url_source_http; ?>"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-android.png" width="32" height="32" /></a></textarea>
                <br /></td>
            <td width="290" align="center" class="texto_padrao"><a href="<?php echo $url_source_http; ?>"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-blackberry.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><a href="<?php echo $url_source_http; ?>"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-blackberry.png" width="32" height="32" /></a></textarea>
                <br /></td>
            <td width="290" align="center" valign="top" class="texto_padrao"><a href="<?php echo $url_source_http; ?>"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><a href="<?php echo $url_source_http; ?>"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" /></a></textarea></td>
          </tr>
          <tr>
            <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=<?php echo $url_source_http; ?>" /></td>
            <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=<?php echo $url_source_http; ?>" /></td>
            <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=<?php echo $url_source_http; ?>" /></td>
          </tr>
          <tr>
            <td align="center"><textarea readonly="readonly" style="width:280px; height:50px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=<?php echo $url_source_http; ?>" title="Android" /></textarea></td>
            <td align="center"><textarea readonly="readonly" style="width:280px; height:50px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=<?php echo $url_source_http; ?>" title="BlackBerry" /></textarea></td>
            <td align="center"><textarea readonly="readonly" style="width:280px; height:50px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=<?php echo $url_source_http; ?>" title="IOS" /></textarea></td>
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