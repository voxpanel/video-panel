<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

if($_POST["cadastrar"]) {

if(empty($_POST["nome"]) or empty($_POST["espectadores"]) or empty($_POST["bitrate"])) {
die ("<script> alert(\"".lang_info_campos_vazios."\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

mysql_query("INSERT INTO video.revendas_planos (codigo_revenda,nome,espectadores,bitrate,espaco_ftp,ipcameras,subrevendas,streamings,tipo,aplicacao) VALUES ('".$dados_revenda["codigo"]."','".$_POST["nome"]."','".$_POST["espectadores"]."','".$_POST["bitrate"]."','".$_POST["espaco_ftp"]."','".$_POST["ipcameras"]."','".$_POST["subrevendas"]."','".$_POST["streamings"]."','".$_POST["tipo"]."','".$_POST["aplicacao"]."')");

if(!mysql_error()) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".lang_pagina_gerenciar_planos_cadastrar_resultado_ok."","ok");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".lang_pagina_gerenciar_planos_cadastrar_resultado_erro."","erro");

}


header("Location: /admin/revenda-gerenciar-planos");
exit();

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/ajax-revenda.js"></script>
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="/admin/inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };

function validar_tipo_plano( tipo ) {

document.getElementById('subrevendas').disabled = true;
document.getElementById('subrevendas').value = "0";
document.getElementById('subrevendas').style.cursor = "not-allowed";
document.getElementById('streamings').disabled = true;
document.getElementById('streamings').value = "0";
document.getElementById('streamings').style.cursor = "not-allowed";
document.getElementById('aplicacao').disabled = false;
document.getElementById('aplicacao').style.cursor = "default";

if(tipo == "subrevenda") {
document.getElementById('streamings').disabled = false;
document.getElementById('streamings').value = "0";
document.getElementById('streamings').style.cursor = "default";
document.getElementById('subrevendas').disabled = false;
document.getElementById('subrevendas').value = "0";
document.getElementById('subrevendas').style.cursor = "default";
document.getElementById('aplicacao').disabled = true;
document.getElementById('aplicacao').style.cursor = "not-allowed";
}

}

function validar_aplicacao( tipo ) {

document.getElementById('espaco_ftp').disabled = false;
document.getElementById('espaco_ftp').value = "0";
document.getElementById('espaco_ftp').style.cursor = "default";
document.getElementById('ipcameras').disabled = true;
document.getElementById('ipcameras').value = "0";
document.getElementById('ipcameras').style.cursor = "not-allowed";

if(tipo == "live") {
document.getElementById('espaco_ftp').disabled = true;
document.getElementById('espaco_ftp').value = "0";
document.getElementById('espaco_ftp').style.cursor = "not-allowed";
}

if(tipo == "ipcamera") {
document.getElementById('espaco_ftp').disabled = true;
document.getElementById('espaco_ftp').value = "0";
document.getElementById('espaco_ftp').style.cursor = "not-allowed";
document.getElementById('ipcameras').disabled = false;
document.getElementById('ipcameras').value = "0";
document.getElementById('ipcameras').style.cursor = "default";
}

}
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
<div id="quadro-topo"><strong><?php echo lang_pagina_gerenciar_planos_tab_titulo; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo lang_pagina_gerenciar_planos_aba_planos; ?></h2>
  <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="170" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_pagina_gerenciar_planos_nome; ?></td>
      <td width="110" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_pagina_gerenciar_planos_espectadores; ?></td>
      <td width="90" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_pagina_gerenciar_planos_bitrate; ?></td>
      <td width="90" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_pagina_gerenciar_planos_espaco_ftp; ?></td>
      <td width="90" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_pagina_gerenciar_planos_ipcameras; ?></td>
      <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_pagina_gerenciar_planos_subrevendas; ?></td>
      <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_pagina_gerenciar_planos_aplicacao; ?></td>
      <td width="140" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_executar_acao; ?></td>
    </tr>
<?php
$total_planos = mysql_num_rows(mysql_query("SELECT * FROM video.revendas_planos where codigo_revenda = '".$dados_revenda["codigo"]."'"));

if($total_planos > 0) {

$sql = mysql_query("SELECT * FROM video.revendas_planos where codigo_revenda = '".$dados_revenda["codigo"]."' ORDER by nome");
while ($dados_plano = mysql_fetch_array($sql)) {

$plano_code = code_decode($dados_plano["codigo"],"E");

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_plano["nome"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_plano["espectadores"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_plano["bitrate"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".tamanho($dados_plano["espaco_ftp"])."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_plano["ipcameras"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_plano["subrevendas"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_plano["aplicacao"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>";

echo "<select style='width:100%' id='".$plano_code."' onchange='executar_acao_diversa(this.id,this.value);'>
  <option value='' selected='selected'>".lang_info_executar_acao."</option>
  <option value='remover-plano'>".lang_info_executar_acao_remover."</option>
</select>";

echo "</td>
</tr>";

}

} else {

echo "<tr>
    <td height='23' colspan='3' align='center' class='texto_padrao'>".$lang['lang_info_sem_registros']."</td>
  </tr>";

}
?>
  </table>
  <br />
<br />
<br />
<br />
<br />
  </div>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo lang_pagina_gerenciar_planos_aba_cadastrar_plano; ?></h2>
        <form method="post" action="/admin/revenda-gerenciar-planos" style="padding:0px; margin:0px" name="planos">
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
      <tr>
        <td width="160" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_pagina_gerenciar_planos_tipo_plano; ?></td>
        <td width="730" align="left"><select name="tipo" id="tipo" style="width:255px;" onchange="validar_tipo_plano( this.value );">
		  <option value="streaming" selected="selected">Streaming</option>
          <option value="subrevenda">SubReseller</option>
        </select></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_pagina_gerenciar_planos_aplicacao; ?></td>
        <td align="left" class="texto_padrao_pequeno"><select name="aplicacao" id="aplicacao" style="width:255px;" onchange="validar_aplicacao( this.value );">
		  <option value="tvstation" selected="selected">Tv Station (live & ondemand)</option>
          <option value="live">Live</option>
          <option value="vod">OnDemand</option>
          <option value="ipcamera">IP Camera</option>
        </select></td>
      </tr>
      <tr>
        <td width="160" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_pagina_gerenciar_planos_nome; ?></td>
        <td width="730" align="left"><input name="nome" type="text" class="input" id="nome" style="width:250px;" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_pagina_gerenciar_planos_espectadores; ?></td>
        <td align="left" class="texto_padrao_vermelho_destaque"><input name="espectadores" type="number" class="input" id="espectadores" style="width:250px;" value="0" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_pagina_gerenciar_planos_bitrate; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="bitrate" type="number" class="input" id="bitrate" style="width:250px;" value="0" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_pagina_gerenciar_planos_espaco_ftp; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="espaco_ftp" type="number" class="input" id="espaco_ftp" style="width:250px;" value="0" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_pagina_gerenciar_planos_ipcameras; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="ipcameras" type="number" disabled="disabled" class="input" id="ipcameras" style="width:250px;cursor:not-allowed" value="0" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_pagina_gerenciar_planos_subrevendas; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="subrevendas" type="number" disabled="disabled" class="input" id="subrevendas" style="width:250px;cursor:not-allowed" value="0" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_pagina_gerenciar_planos_streamings; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="streamings" type="number" disabled="disabled" class="input" id="streamings" style="width:250px;cursor:not-allowed" value="0" /></td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo lang_botao_titulo_cadastrar; ?>" />
          <input name="cadastrar" type="hidden" id="cadastrar" value="sim" /></td>
      </tr>
    </table>
    </form>
      </div>
      </div>
</div>
</div>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo lang_titulo_fechar; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>