<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Busca Avançada</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/ajax-revenda.js"></script>
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="/admin/inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<?php if($dados_revenda["status"] == '1') { ?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <th scope="col"><div id="quadro">
            	<div id="quadro-topo"><strong><?php echo lang_info_pagina_busca_avancada_tab_titulo; ?></strong></div>
                <div class="texto_medio" id="quadro-conteudo">
<form method="post" action="/admin/revenda-busca-avancada-resultado" style="padding:0px; margin:0px">
  <table width="590" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
    <tr>
      <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_busca_avancada_palavra_chave; ?></td>
      <td width="390" align="left"><input name="chave" type="text" class="input" id="chave" style="width:250px;" value="" /></td>
    </tr>
    <tr>
      <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_busca_avancada_local; ?></td>
      <td align="left" class="texto_padrao_pequeno"><select name="local" class="input" id="local" style="width:255px;">
          <optgroup label="<?php echo lang_info_pagina_busca_avancada_local_grupo_geral; ?>">
          <option value="login"><?php echo lang_info_pagina_busca_avancada_local_login; ?></option>
          </optgroup>
          <optgroup label="<?php echo lang_info_pagina_busca_avancada_local_grupo_caracteristicas; ?>">
          <option value="espectadores"><?php echo lang_info_pagina_busca_avancada_local_espectadores; ?></option>
          <option value="bitrate"><?php echo lang_info_pagina_busca_avancada_local_bitrate; ?></option>
          <option value="identificacao"><?php echo lang_info_pagina_busca_avancada_local_identificacao; ?></option>
          <option value="email"><?php echo lang_info_pagina_busca_avancada_local_email; ?></option>
          <option value="data_cadastro"><?php echo lang_info_pagina_busca_avancada_local_data_cadastro; ?></option>
          </optgroup>
          <optgroup label="<?php echo lang_info_pagina_busca_avancada_local_grupo_servidor; ?>">
          <option value="servidor_ip"><?php echo lang_info_pagina_busca_avancada_local_ip; ?></option>
          <option value="servidor_nome"><?php echo lang_info_pagina_busca_avancada_local_nome; ?></option>
          </optgroup>
        </select>
      </td>
    </tr>
    <tr>
      <td height="40">&nbsp;</td>
      <td align="left"><input type="submit" class="botao" value="<?php echo lang_info_pagina_busca_avancada_botao_buscar; ?>" /></td>
    </tr>
  </table>
</form>
  </div>
  </div></th>
    </tr>
  </table>
<br />
    <table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style=" border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
      <tr style="background:url(/admin/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
        <td width="200" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_pagina_busca_avancada_resultado_login; ?></td>
        <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_pagina_busca_avancada_resultado_servidor; ?></td>
        <td width="250" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_pagina_busca_avancada_resultado_configuracao; ?></td>
        <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_pagina_busca_avancada_resultado_status; ?></td>
        <td width="250" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_pagina_busca_avancada_resultado_identificacao; ?></td>
      </tr>
<?php
if($_POST["local"] == 'login') {

$query = "SELECT * FROM video.streamings where login = '".$_POST["chave"]."' AND codigo_cliente = '".$dados_revenda["codigo"]."'";

} elseif($_POST["local"] == 'espectadores') {

$query = "SELECT * FROM video.streamings where espectadores = '".$_POST["chave"]."' AND codigo_cliente = '".$dados_revenda["codigo"]."'";

} elseif($_POST["local"] == 'bitrate') {

$query = "SELECT * FROM video.streamings where bitrate = '".str_replace("Kbps","",$_POST["chave"])."' AND codigo_cliente = '".$dados_revenda["codigo"]."'";

} elseif($_POST["local"] == 'identificacao') {

$query = "SELECT * FROM video.streamings where identificacao like '%".$_POST["chave"]."%' AND codigo_cliente = '".$dados_revenda["codigo"]."'";

} elseif($_POST["local"] == 'email') {

$query = "SELECT * FROM video.streamings where email like '%".$_POST["chave"]."%' AND codigo_cliente = '".$dados_revenda["codigo"]."'";

} elseif($_POST["local"] == 'data_cadastro') {

list($dia,$mes,$ano) = explode("/",$_POST["chave"]);
$data_cadastro = $ano."-".$mes."-".$dia;

$query = "SELECT * FROM video.streamings where data_cadastro = '".$data_cadastro."' AND codigo_cliente = '".$dados_revenda["codigo"]."'";

} elseif($_POST["local"] == 'servidor_ip') {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where ip = '".$_POST["chave"]."'"));

$query = "SELECT * FROM video.streamings where codigo_servidor = '".$dados_servidor["codigo"]."' AND codigo_cliente = '".$dados_revenda["codigo"]."'";

} elseif($_POST["local"] == 'servidor_nome') {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where nome = '".$_POST["chave"]."'"));

$query = "SELECT * FROM video.streamings where codigo_servidor = '".$dados_servidor["codigo"]."' AND codigo_cliente = '".$dados_revenda["codigo"]."'";

} else {

$query = "SELECT * FROM video.streamings where login = '".$_POST["chave"]."' AND codigo_cliente = '".$dados_revenda["codigo"]."'";

}

$sql = mysql_query("".$query." ORDER by login ASC");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

$identificacao = (strlen($dados_stm["identificacao"]) > 40) ? substr($dados_stm["identificacao"], 0, 40)."..." : $dados_stm["identificacao"];

$porcentagem_uso_espaco = ($dados_stm["espaco_usado"] == 0 || $dados_stm["espaco"] == 0) ? "0" : $dados_stm["espaco_usado"]*100/$dados_stm["espaco"];
$porcentagem_uso_espaco_barra = ($porcentagem_uso_espaco > 100) ? "100" : $porcentagem_uso_espaco;

$cor_status = ($dados_stm["status"] == 1) ? "#FFFFFF" : "#FFB3B3";

$status_inicial = ($dados_stm["status"] != 1) ? "Bloqueado" : "<img src=/admin/img/spinner.gif' />";

list($ano,$mes,$dia) = explode("-",$dados_stm["data_cadastro"]);
$data_cadastro = $dia."/".$mes."/".$ano;

$login_code = code_decode($dados_stm["login"],"E");

echo "<tr style='background-color:".$cor_status.";cursor:pointer' onmouseover='this.style.backgroundColor=\"#F3F3F3\"' onmouseout='this.style.backgroundColor=\"".$cor_status."\"' onClick='window.top.location = \"/admin/revenda/".$login_code."\";' title='Carregar/Load streaming ".$dados_stm["login"]."'>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_stm["login"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".dominio_servidor($dados_servidor["nome"])."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_stm["espectadores"]." ".lang_info_pagina_busca_avancada_resultado_espectadores." | ".$dados_stm["bitrate"]." Kbps | ".tamanho($dados_stm["espaco"])."</td>
<td height='25' align='center' scope='col' class='texto_padrao_pequeno' style='cursor:pointer' id='".$login_code."'>".$status_inicial."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$identificacao."</td>
</tr>";

// Adiciona na lista de checagem do status apenas se estiver ativo
if($dados_stm["status"] == 1) {
$array_streamings .= "".$login_code."|";
}

}

?>
    </table>
<?php } else { ?>
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo lang_alerta_bloqueio; ?></td>
    </tr>
</table>
<?php } ?>
</div>
<script type="text/javascript">
// Checar o status dos streamings
checar_status_streamings_busca_avancada('<?php echo $array_streamings; ?>');
</script>
</body>
</html>