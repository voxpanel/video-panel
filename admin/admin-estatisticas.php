<?php
require_once("inc/protecao-admin.php");

$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM video.streamings"));
$total_revendas = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE tipo = '1'"));
$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE tipo != '1'"));
$total_servidores = mysql_num_rows(mysql_query("SELECT * FROM video.servidores"));
$total_apps = mysql_num_rows(mysql_query("SELECT * FROM video.apps"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-menu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/sorttable.js"></script>
</head>

<body>
<div id="topo">
<div id="topo-conteudo" style="background:url(/admin/img/logo-advance-host.gif) no-repeat left;"></div>
</div>
<div id="menu">
<div id="menu-links">
  	<ul>
      <li style="width:150px">&nbsp;</li>
  		<li><a href="/admin/admin-streamings" class="texto_menu">Streamings</a></li>
  		<li><em></em><a href="/admin/admin-revendas" class="texto_menu">Revendas</a></li>
        <li><em></em><a href="/admin/admin-servidores" class="texto_menu">Servidores</a></li>
        <li><em></em><a href="/admin/admin-dicas" class="texto_menu">Dicas</a></li>
        <li><em></em><a href="/admin/admin-avisos" class="texto_menu">Avisos</a></li>
        <li><em></em><a href="/admin/admin-tutoriais" class="texto_menu">Tutoriais</a></li>
        <li><em></em><a href="/admin/admin-estatisticas" class="texto_menu">Estat�sticas</a></li>
        <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configura��es</a></li>
        <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
  	</ul>
</div>
</div>
<div id="conteudo">
<table width="250" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="125" height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Streamings</td>
        <td width="125" align="left" class="texto_padrao"><?php echo $total_streamings; ?></td>
      </tr>
      <tr>
        <td width="124" height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Revendas</td>
        <td width="125" align="left" class="texto_padrao"><?php echo $total_revendas; ?></td>
      </tr>
      <tr>
        <td width="124" height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Sub Revendas</td>
        <td width="125" align="left" class="texto_padrao"><?php echo $total_subrevendas; ?></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Servidores</td>
        <td align="left" class="texto_padrao"><?php echo $total_servidores; ?></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Apps</td>
        <td align="left" class="texto_padrao"><?php echo $total_apps; ?></td>
      </tr>
    </table>
</div>
</body>
</html>
