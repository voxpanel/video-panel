<?php
require_once("inc/protecao-admin.php");
require_once("inc/classe.ssh.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
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
<script type="text/javascript" src="/admin/inc/ajax.js"></script>
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
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
        <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configura��es</a></li>
        <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
  	</ul>
</div>
</div>
<div id="conteudo">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="770" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<form style="padding:0; margin:0" onsubmit="buscar_servidor(document.getElementById('chave').value);return false;">
  <table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px;">
    <tr>
      <td width="30" align="center" class="texto_padrao_destaque" scope="col"><img src="/admin/img/icones/img-icone-cadastrar.png" alt="Cadastrar" width="16" height="16" /></td>
      <td width="130" align="left" scope="col"><a href="/admin/admin-cadastrar-servidor" class="texto_padrao_destaque">Cadastrar Servidor</a></td>
      <td width="30" align="center" class="texto_padrao_destaque" scope="col"><img src="/admin/img/icones/img-icone-play.png" alt="Cadastrar" width="16" height="16" /></td>
      <td width="120" align="left" scope="col"><a href="javascript:ligar_streamings_geral();" class="texto_padrao_destaque">Ligar Streamings</a></td>
      <td width="160" align="center" class="texto_padrao_destaque" scope="col">&nbsp;</td>
      <td width="250" align="left" class="texto_padrao_destaque" scope="col"><input style="vertical-align:middle;" type="radio" name="filtro" onclick="window.location = '/admin/admin-servidores';" <?php if(!query_string('2')) { echo 'checked="checked"'; } ?> />
        &nbsp;Todos
        <input style="vertical-align:middle;" type="radio" name="filtro" onclick="window.location = '/admin/admin-servidores/filtro/on';" <?php if(query_string('3') == 'on') { echo 'checked="checked"'; } ?> />
        &nbsp;Ativos
        <input style="vertical-align:middle;" type="radio" name="filtro" onclick="window.location = '/admin/admin-servidores/filtro/off';" <?php if(query_string('3') == 'off') { echo 'checked="checked"'; } ?> />
        &nbsp;Inativos </td>
      <td width="300" align="right" class="texto_padrao_destaque" scope="col">
      Buscar por
        <input name="chave" type="text" id="chave" />
        <input type="button" class="botao_padrao" value="Buscar" onclick="buscar_servidor(document.getElementById('chave').value);" /></td>
    </tr>
  </table>
</form>
  <table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" style=" border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Nome</td>
      <td width="150" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;IP</td>
      <td width="130" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Dom�nio</td>
      <td width="65" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Porta</td>
      <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Streamings</td>
      <td width="65" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Load</td>
      <td width="110" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Tr�fego Rede</td>
      <td width="110" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Tr�fego do M�s</td>
      <td width="170" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;A��es</td>
    </tr>
<?php
if(query_string('2') == 'resultado') {

$cat = (preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", query_string('3'))) ? "ip" : "nome";

$query = "SELECT * FROM video.servidores where ".$cat." like '%".query_string('3')."%'";
$pagina_atual = query_string('4');

} elseif(query_string('2') == 'filtro') {

$query = "SELECT * FROM video.servidores where status = '".query_string('3')."'";
$pagina_atual = query_string('4');

} else {

$query = "SELECT * FROM video.servidores";
$pagina_atual = query_string('4');

}

$sql = mysql_query("".$query."");
$lpp = 100; // total de registros por p&aacute;gina
$total = mysql_num_rows($sql);
$paginas = ceil($total / $lpp); 
if(!isset($pagina_atual)) { $pagina_atual = 0; }
$inicio = $pagina_atual * $lpp;
$sql = mysql_query("".$query." ORDER by ordem ASC LIMIT $inicio, $lpp");

while ($dados_servidor = mysql_fetch_array($sql)) {

$total_stm = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where codigo_servidor = '".$dados_servidor["codigo"]."'"));

$servidor_code = code_decode($dados_servidor["codigo"],"E");

if($dados_servidor["status"] == "on") {

$cor_alerta_linha = ($dados_servidor["load"] > 300.0 || ($dados_servidor["trafego_out"] > 70.0 && !preg_match("/kb/i", $dados_servidor["trafego_out"]))) ? '#FFFF82' : '#FFFFFF';
$cor_alerta_load = ($dados_servidor["load"] > 300.0) ? '#FFC1C1' : $cor_alerta_linha;
$cor_alerta_trafego_out = ($dados_servidor["trafego_out"] > 70.0 && !preg_match("/kb/i", $dados_servidor["trafego_out"])) ? '#FFC1C1' : $cor_alerta_linha;

} else {
$cor_alerta_load = "#E5E5E5";
$cor_alerta_trafego_out = "#E5E5E5";
$cor_alerta_linha = "#E5E5E5";
}

echo "<tr style='background-color:".$cor_alerta_linha.";' title='C�digo: ".$dados_servidor["codigo"]." | Nome: ".$dados_servidor["nome"]." | IP: ".$dados_servidor["ip"]."'>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_servidor["nome"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_servidor["ip"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_servidor["porta_ssh"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$total_stm." / ".$dados_servidor["limite_streamings"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao' style='background-color:".$cor_alerta_load.";'>&nbsp;".$dados_servidor["load"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao' style='background-color:".$cor_alerta_trafego_out.";'>&nbsp;".$dados_servidor["trafego_out"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_servidor["trafego"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>
<select style='width:100%' id='".$servidor_code."' onchange='executar_acao_servidor(this.id,this.value);'>
  <option value='' selected='selected'>Escolha uma a��o</option>
  <optgroup label='Streamings'>
  <option value='ligar'>Ligar Streamings</option>
  <option value='listar-streamings'>Listar Streamings</option>
  <option value='sincronizar'>Sincronizar</option>
  <option value='alterar-servidor'>Mover Streamings</option>
  </optgroup>
  <optgroup label='Administra��o'>
  <option value='configurar'>Alterar Configura��es</option>
  <option value='ativar-manutencao'>Ativar Manuten��o</option>
  <option value='desativar-manutencao'>Desativar Manuten��o</option>
  <option value='".$dados_servidor["grafico_trafego"]."'>Gr�fico de Tr�fego</option>
  <option value='remover'>Remover</option>
  </optgroup>
</select>
</td>
</tr>";

unset($alerta_load);
unset($alerta_trafego_out);
}
?>
  </table>
  <table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" style=" border:#D5D5D5 1px solid;">
    <tr>
      <td height="20" align="center"><?php
$total_registros = mysql_num_rows(mysql_query("".$query.""));

if($total_registros == 0) {
echo "<span class=\"texto_padrao_destaque\">Nenhum servidor encontrado.</span>";
} else {

$pagina_atual = query_string('2');
	
	for($i = 0; $i < $paginas; $i++) {
      $linksp = $i + 1;
      if ($pagina_atual == $i) {
              echo " <span class=\"texto_padrao_destaque\" title=\"P&aacute;gina $linksp\">$linksp</span>";
      } else {
              $url = "/admin/admin-servidores/".query_string('2')."/".query_string('3')."/$i";
              echo " <a href=\"$url\" class=\"texto_padrao\" title=\"Ir para p&aacute;gina $linksp\">$linksp</a></span>";
      }
	}

}
?>      </td>
    </tr>
  </table>
</div>

<!-- In�cio div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/admin/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="Fechar" /></div>
<div id="log-sistema-conteudo"><img src="/admin/img/ajax-loader.gif" /></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
