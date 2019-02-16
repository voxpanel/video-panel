<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));

$login_code = code_decode($_SESSION["login_logado"],"E");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="/inc/sorttable.js"></script>
<script type="text/javascript" src="/inc/ajax-streaming-playlists.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_playlists_lista_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px;">
    <tr>
      <td width="30" height="28" align="center" class="texto_padrao_destaque" scope="col"><img src="/img/icones/img-icone-cadastrar.png" width="16" height="16" align="absmiddle" /></td>
      <td width="860" align="left" scope="col"><a href="javascript:criar_playlist('<?php echo $login_code; ?>');" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_playlists_lista_botao_criar_playlist']; ?></a></td>
      </tr>
  </table>
  <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="border:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="390" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_lista_tabela_nome']; ?></td>
      <td width="65" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_lista_tabela_videos']; ?></td>
      <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_lista_tabela_agendamentos']; ?></td>
      <td width="65" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_lista_tabela_duracao']; ?></td>      
      <td width="130" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_lista_tabela_comerciais']; ?></td>
      <td width="140" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_lista_tabela_acao']; ?></td>
    </tr>
    <?php
$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."'"));

if($total_playlists > 0) {

$sql = mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
while ($dados_playlist = mysql_fetch_array($sql)) {

$total_videos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'"));
$duracao = mysql_fetch_array(mysql_query("SELECT SUM(duracao_segundos) as total FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'"));
$total_agendamentos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_agendamentos where codigo_playlist = '".$dados_playlist["codigo"]."'"));

$comerciais = ($dados_playlist["comerciais"] == "sim") ? $lang['lang_label_sim'] : $lang['lang_label_nao'];

$playlist_code = code_decode($dados_playlist["codigo"],"E");

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_playlist["nome"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$total_videos."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$total_agendamentos."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".gmdate("H:i:s", $duracao["total"])."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$comerciais."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>";

echo "<select style='width:100%' id='".$playlist_code."' onchange='executar_acao_playlist(this.id,this.value);'>
  <option value='' selected='selected'>".$lang['lang_info_gerenciador_playlists_lista_acao']."</option>
  <optgroup label='".$lang['lang_info_gerenciador_playlists_lista_acao_playlists']."'>
  <option value='iniciar'>".$lang['lang_info_gerenciador_playlists_lista_iniciar']."</option>
  <option value='gerenciar'>".$lang['lang_info_gerenciador_playlists_lista_gerenciar']."</option>
  <option value='gerenciar-basico'>".$lang['lang_info_gerenciador_playlists_lista_gerenciar_basico']."</option>
  <option value='duplicar'>".$lang['lang_info_gerenciador_playlists_lista_duplicar']."</option>
  <option value='remover'>".$lang['lang_info_gerenciador_playlists_lista_remover']."</option>
  </optgroup>
  <optgroup label='".$lang['lang_info_gerenciador_playlists_lista_acao_comerciais']."'>
  <option value='comerciais-configurar'>".$lang['lang_info_gerenciador_playlists_lista_comerciais_configurar']."</option>
  <option value='comerciais-remover'>".$lang['lang_info_gerenciador_playlists_lista_comerciais_remover']."</option>
  </optgroup>
</select>";

echo "</td>
</tr>";

}

} else {

echo "<tr>
    <td height='23' colspan='6' align='center' class='texto_padrao'>".$lang['lang_info_sem_registros']."</td>
  </tr>";

}
?>
  </table>
</div>
</div>
</div>
<!-- In�cio div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>