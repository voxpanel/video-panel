<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$login_code = code_decode($dados_stm["login"],"E");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<div id="quadro">
            	<div id="quadro-topo"><strong><?php echo $lang['lang_info_streaming_logs_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao"><?php echo $lang['lang_info_streaming_logs_info']; ?>
    <br />
	<br />
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style=" border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="13%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_logs_data']; ?></td>
      <td width="11%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_logs_ip']; ?></td>
      <td width="16%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_logs_navegador']; ?></td>
      <td width="60%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_logs_log']; ?></td>
    </tr>
<?php
$pagina_atual = query_string('1');

$sql = mysql_query("SELECT * FROM video.logs_streamings WHERE codigo_stm = '".$dados_stm["codigo"]."'");
$lpp = 100; // total de registros por p�gina
$total = mysql_num_rows($sql);
$paginas = ceil($total / $lpp); 
if(!isset($pagina_atual)) { $pagina_atual = 0; }
$inicio = $pagina_atual * $lpp;
$sql = mysql_query("SELECT * FROM video.logs_streamings WHERE codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo DESC LIMIT $inicio, $lpp");
while ($dados_log = mysql_fetch_array($sql)) {

$log = str_replace("<br>","",$dados_log["log"]);
$log = str_replace("<br />","",$log);

$log = (strlen($log) > 105) ? substr($log, 0, 105)."..." : $log;

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".formatar_data($dados_stm["formato_data"], $dados_log["data"], $dados_stm["timezone"])."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_log["ip"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_log["navegador"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$log."</td>
</tr>";

}
?>
  </table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style=" border:#D5D5D5 1px solid;">
  <tr>
    <td height="20" align="center"><?php
$total_registros = mysql_num_rows(mysql_query("SELECT * FROM video.logs_streamings WHERE codigo_stm = '".$dados_stm["codigo"]."'"));

if($total_registros == 0) {
echo "<span class=\"texto_padrao_destaque\">".$lang['lang_info_sem_registros']."</span>";
} else {
	
	for($i = 0; $i < $paginas; $i++) {
      $linksp = $i + 1;
      if ($pagina_atual == $i) {
              echo " <span class=\"texto_padrao_destaque\" title=\"P&aacute;gina $linksp\">$linksp</span>";
      } else {
              $url = "/".query_string('0')."/$i";
              echo " <a href=\"$url\" class=\"texto_padrao\" title=\"Ir para p&aacute;gina $linksp\">$linksp</a></span>";
      }
	}

}
?>
    </td>
  </tr>
</table></td>
    </tr>
</table>
    </div>
      </div>
<br />
<br />
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