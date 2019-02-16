<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

// Estat�sticas
$total_streamings_ativos = mysql_num_rows(mysql_query("SELECT * FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."' AND status = '1'"));
$total_streamings_bloqueados = mysql_num_rows(mysql_query("SELECT * FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."' AND status != '1'"));

$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espectadores = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espectadores_subrevendas = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$espaco_subrevendas = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$espaco_streamings = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

$total_espectadores = $espectadores["total"]+$espectadores_subrevendas["total"];

$porcentagem_uso_subrevendas = ($dados_revenda["subrevendas"] == 0) ? "0" : $total_subrevendas*100/$dados_revenda["subrevendas"];
$porcentagem_uso_streamings = ($dados_revenda["streamings"] == 0) ? "0" : $total_streamings*100/$dados_revenda["streamings"];
$porcentagem_uso_espectadores = ($dados_revenda["espectadores"] == 0) ? "0" : $total_espectadores*100/$dados_revenda["espectadores"];
$porcentagem_uso_espaco = ($dados_revenda["espaco"] == 0) ? "0" : ($espaco_subrevendas["total"]+$espaco_streamings["total"])*100/$dados_revenda["espaco"];

$stat_subrevendas_descricao = "".$total_subrevendas." / ".$dados_revenda["subrevendas"]."";
$stat_streamings_descricao = "".$total_streamings." / ".$dados_revenda["streamings"]."";
$stat_espectadores_descricao = "".$total_espectadores." / ".$dados_revenda["espectadores"]."";
$stat_espaco_descricao = "".tamanho(($espaco_subrevendas["total"]+$espaco_streamings["total"]))." / ".tamanho($dados_revenda["espaco"])."";

$limite_subrevendas = ($dados_revenda["subrevendas"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : barra_uso_plano($porcentagem_uso_subrevendas,$stat_subrevendas_descricao);

$limite_streamings = ($dados_revenda["streamings"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : barra_uso_plano($porcentagem_uso_streamings,$stat_streamings_descricao);

$limite_espectadores = ($dados_revenda["espectadores"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : barra_uso_plano($porcentagem_uso_espectadores,$stat_espectadores_descricao);

if($dados_revenda["subrevendas"] < 0 && $dados_revenda["tipo"] == 3) {

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["aviso_acesso_negado"] = status_acao(lang_info_acesso_stm_nao_permitido,"erro");

header("Location: /admin/revenda-informacoes");
exit;

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cadastrar Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
<script type="text/javascript">
function configurar_ilimitado(campo,recurso) {

if(document.getElementById(campo).checked) {
document.getElementById(recurso).value = '999999';
} else {
document.getElementById(recurso).value = '';
}

}

// Fun��o para carregar a configura��o do plano de streaming/subrevenda pr�-definido
function configuracao_plano( configuracoes ) {
  
  array_configuracoes = configuracoes.split("|");
  
  document.getElementById("streamings").value = array_configuracoes[0];
  document.getElementById("espectadores").value = array_configuracoes[1];
  document.getElementById("bitrate").value = array_configuracoes[2];
  document.getElementById("espaco").value = array_configuracoes[3];
  
}
</script>
</head>

<body>
<div id="sub-conteudo">
<?php if($dados_revenda["status"] == '1') { ?>
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="650" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<?php if($dados_revenda["subrevendas"] > 0 && ($dados_revenda["tipo"] == 1 || $dados_revenda["tipo"] == 2)) { ?>
<table width="650" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <th scope="col"><div id="quadro">
            	<div id="quadro-topo"><strong><?php echo lang_info_pagina_cadastrar_subrevenda_tab_titulo; ?></strong></div>
                <div class="texto_medio" id="quadro-conteudo">
  <form method="post" action="/admin/revenda-subrevenda-cadastra" style="padding:0px; margin:0px">
    <table width="640" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_email; ?></td>
        <td width="440" align="left" class="texto_padrao_pequeno"><input name="subrevenda_email" type="text" class="input" id="subrevenda_email" style="width:250px;" /></td>
      </tr>
	  <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_senha; ?></td>
        <td align="left"><input name="subrevenda_senha" type="text" class="input" id="subrevenda_senha" style="width:250px;" />&nbsp;<img src="/admin/img/icones/img-icone-senha-24x24.png" alt="Senha/Password" width="16" height="16" align="absmiddle" onclick="gerar_senha('subrevenda_senha');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_plano; ?></td>
        <td width="440" align="left">
        <select class="input" style="width:255px;" onchange="configuracao_plano( this.value );">
        <option value="" selected="selected"><?php echo lang_info_selecionar_opcao; ?></option>
		<?php
        $query_plano = mysql_query("SELECT * FROM video.revendas_planos where codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = 'subrevenda' ORDER by nome");
        while ($dados_plano = mysql_fetch_array($query_plano)) {
        
			echo '<option value="'.$dados_plano["streamings"].'|'.$dados_plano["espectadores"].'|'.$dados_plano["bitrate"].'|'.$dados_plano["espaco_ftp"].'">'.$dados_plano["nome"].'</option>';
        
        }
        ?>
        </select>&nbsp;<img src="/admin/img/icones/img-icone-cadastrar-64x64.png" title="Cadastrar/Add" width="16" height="16" onclick="window.open('/admin/revenda-gerenciar-planos','conteudo');" style="cursor:pointer" />
        </td>
      </tr>
      <?php if($dados_revenda["subrevendas"] > 0 && $dados_revenda["tipo"] == 1) { ?>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_limite_subrevendas; ?></td>
        <td align="left"><input name="subrevendas" type="text" class="input" id="subrevendas" style="width:250px;" value="" /> </td>
      </tr>
      <?php } ?>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_limite_stm; ?></td>
        <td align="left"><input name="streamings" type="number" class="input" id="streamings" style="width:250px;" value="" />&nbsp;
        <?php if($dados_revenda["streamings"] == '999999') { ?>
        <input type="checkbox" id="streamings_ilimitados" onclick="configurar_ilimitado('streamings_ilimitados','streamings');" style="vertical-align:middle" />&nbsp;<span class="texto_ilimitado"><?php echo lang_info_pagina_cadastrar_subrevenda_ilimitado; ?></span>
        <?php } ?>        </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_limite_espectadores; ?></td>
        <td align="left"><input name="espectadores" type="number" class="input" id="espectadores" style="width:250px;" value="" />&nbsp;
        <?php if($dados_revenda["espectadores"] == '999999') { ?>
        <input type="checkbox" id="espectadores_ilimitados" onclick="configurar_ilimitado('espectadores_ilimitados','espectadores');" style="vertical-align:middle" />&nbsp;<span class="texto_ilimitado"><?php echo lang_info_pagina_cadastrar_subrevenda_ilimitado; ?></span>
        <?php } ?>        </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_limite_bitrate; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="bitrate" type="number" class="input" id="bitrate" style="width:250px;" /></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_limite_espaco_ftp; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="espaco" type="number" class="input" id="espaco" style="width:250px;" />&nbsp;Megabytes</td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_idioma; ?></td>
        <td align="left">
        <select name="idioma_painel" id="idioma_painel" style="width:255px;">
          <option value="pt-br" selected="selected">Portugu�s(Brasil)</option>
          <option value="es">Espa�ol</option>
          <option value="en-us">English(USA)</option>
        </select>        </td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo lang_botao_titulo_cadastrar; ?>" /></td>
      </tr>
    </table>
<table width="340" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid; margin-top:10px; margin-bottom:5px;">
          <tr>
            <td height="30" align="center" class="texto_padrao_destaque"><?php echo lang_info_pagina_cadastrar_subrevenda_estatisticas_tab_titulo; ?></td>
          </tr>
    </table>
        <table width="340" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
        <tr>
          <td width="100" height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_estatisticas_subrevendas; ?></td>
          <td width="240" align="left" class="texto_padrao" style="padding-left:5px;"><?php echo $limite_subrevendas; ?></td>
        </tr>
        <tr>
          <td width="100" height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_estatisticas_stm; ?></td>
          <td width="240" align="left" class="texto_padrao" style="padding-left:5px;"><?php echo $limite_streamings; ?></td>
        </tr>
        <tr>
          <td height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_estatisticas_espectadores; ?></td>
          <td align="left" class="texto_padrao" style="padding-left:5px;"><?php echo $limite_espectadores; ?></td>
        </tr>
        <tr>
          <td height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_estatisticas_espaco_ftp; ?></td>
          <td align="left" class="texto_padrao" style="padding-left:5px;"><?php echo barra_uso_plano($porcentagem_uso_espaco,$stat_espaco_descricao); ?></td>
        </tr>
    </table>
  </form>
  </div>
  </div></th>
    </tr>
  </table>
<?php } else { ?>
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo lang_info_pagina_cadastrar_subrevenda_aviso_subrevenda_desativado; ?></td>
    </tr>
</table>
<?php } ?>
<?php } else { ?>
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo lang_alerta_bloqueio; ?></td>
    </tr>
</table>
<?php } ?>
</div>
</body>
</html>
