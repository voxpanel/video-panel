<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));

if($_POST["cadastrar"]) {


if(empty($_POST["url_rtmp"])) {
// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_agendamentos_relay_resultado_alerta_url_rtmp']."","alerta");

header("Location: /gerenciar-agendamentos-relay");
exit();
}

if($_POST["frequencia"] == "1" && (empty($_POST["data_inicio"]) || $_POST["data_inicio"] == "__/__/____")) {
// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_agendamentos_relay_resultado_alerta_data']."","alerta");

header("Location: /gerenciar-agendamentos-relay");
exit();
}

if($_POST["frequencia"] == "1" && (empty($_POST["data_termino"]) || $_POST["data_termino"] == "__/__/____")) {
// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_agendamentos_relay_resultado_alerta_data']."","alerta");

header("Location: /gerenciar-agendamentos-relay");
exit();
}

list($dia,$mes,$ano) = explode("/",$_POST["data_inicio"]);
$data_inicio = $ano."-".$mes."-".$dia;

list($dia,$mes,$ano) = explode("/",$_POST["data_termino"]);
$data_termino = $ano."-".$mes."-".$dia;

if(count($_POST["dias"]) > 0){
	$dias = implode(",",$_POST["dias"]);
}

mysql_query("INSERT INTO stmvideo.agendamentos_relay (codigo_stm,frequencia,data_inicio,hora_inicio,minuto_inicio,data_termino,hora_termino,minuto_termino,dias,url_rtmp) VALUES ('".$dados_stm["codigo"]."','".$_POST["frequencia"]."','".$data_inicio."','".$_POST["hora_inicio"]."','".$_POST["minuto_inicio"]."','".$data_termino."','".$_POST["hora_termino"]."','".$_POST["minuto_termino"]."','".$dias.",','".$_POST["url_rtmp"]."')");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_agendamentos_relay_resultado_ok']."","ok");


header("Location: /gerenciar-agendamentos-relay");
exit();
}

if($_POST["remover_logs"]) {
mysql_query("Delete From stmvideo.agendamentos_relay_logs Where codigo_stm = '".$dados_stm["codigo"]."'");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_agendamentos_relay_resultado_remover_logs']."","ok");

header("Location: /gerenciar-agendamentos-relay");
exit();
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
<script type="text/javascript" src="/inc/ajax-streaming.js"></script>
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="/inc/javascript-abas.js"></script>
<script type="text/javascript" src="/inc/sorttable.js"></script>
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

echo '<table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_agendamentos_relay_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
<tr>
            <td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
            <td width="860" align="left" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_agendamentos_info']; ?></td>
    </tr>
</table>
  <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_aba_agendamentos']; ?></h2>
  <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/admin/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="320" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_relay_url_rtmp']; ?></td>
      <td width="500" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_relay_horario_agendado']; ?></td>
      <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_relay_executar_acao']; ?></td>
    </tr>
<?php
$total_agendamentos = mysql_num_rows(mysql_query("SELECT * FROM stmvideo.agendamentos_relay where codigo_stm = '".$dados_stm["codigo"]."' ORDER by data_inicio"));

if($total_agendamentos > 0) {

$sql = mysql_query("SELECT * FROM stmvideo.agendamentos_relay where codigo_stm = '".$dados_stm["codigo"]."' ORDER by data_inicio");
while ($dados_agendamento = mysql_fetch_array($sql)) {

$data_inicio = formatar_data($dados_stm["formato_data"], $dados_agendamento["data_inicio"], $dados_stm["timezone"]);
$data_termino = formatar_data($dados_stm["formato_data"], $dados_agendamento["data_termino"], $dados_stm["timezone"]);

if($dados_agendamento["frequencia"] == "1") {
$descricao = "".$lang['lang_info_gerenciador_agendamentos_relay_info_frequencia1']." ".$data_inicio." ".$dados_agendamento["hora_inicio"].":".$dados_agendamento["minuto_inicio"]." ".$lang['lang_info_gerenciador_agendamentos_relay_info_ate']." ".$data_termino." ".$dados_agendamento["hora_termino"].":".$dados_agendamento["minuto_termino"]."";
} elseif($dados_agendamento["frequencia"] == "2") {
$descricao = "".$lang['lang_info_gerenciador_agendamentos_relay_info_frequencia2']." ".$dados_agendamento["hora_inicio"].":".$dados_agendamento["minuto_inicio"]." ".$lang['lang_info_gerenciador_agendamentos_relay_info_ate']." ".$dados_agendamento["hora_termino"].":".$dados_agendamento["minuto_termino"]."";
} else {

$array_dias = explode(",",$dados_agendamento["dias"]);

foreach($array_dias as $dia) {

if($dia == "1") {
$dia_nome = "<font color='#003399'>".$lang['lang_label_segunda']."</font>";
} elseif($dia == "2") {
$dia_nome = "<font color='#FF0000'>".$lang['lang_label_terca']."</font>";
} elseif($dia == "3") {
$dia_nome = "<font color='#FF9900'>".$lang['lang_label_quarta']."</font>";
} elseif($dia == "4") {
$dia_nome = "<font color='#CC0066'>".$lang['lang_label_quinta']."</font>";
} elseif($dia == "5") {
$dia_nome = "<font color='#009900'>".$lang['lang_label_sexta']."</font>";
} elseif($dia == "6") {
$dia_nome = "<font color='#663300'>".$lang['lang_label_sabado']."</font>";
} elseif($dia == "7") {
$dia_nome = "<font color='#663399'>".$lang['lang_label_domingo']."</font>";
} else {
$dia_nome = "";
}

$lista_dias .= "".$dia_nome.", ";

}

$descricao = "".$lang['lang_info_gerenciador_agendamentos_relay_info_frequencia3']." ".substr($lista_dias, 0, -2)." ".$dados_agendamento["hora_inicio"].":".$dados_agendamento["minuto_inicio"]." ".$lang['lang_info_gerenciador_agendamentos_relay_info_ate']." ".$dados_agendamento["hora_termino"].":".$dados_agendamento["minuto_termino"]."";
}

$agendamento_code = code_decode($dados_agendamento["codigo"],"E");

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_agendamento["url_rtmp"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$descricao."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>";

echo "<select style='width:100%' id='".$agendamento_code."' onchange='executar_acao_streaming(this.id,this.value);'>
  <option value='' selected='selected'>".$lang['lang_info_gerenciador_agendamentos_relay_acao']."</option>
  <option value='ondemand-remover-agendamento-relay'>".$lang['lang_info_gerenciador_agendamentos_relay_acao_remover']."</option>
</select>";

echo "</td>
</tr>";

unset($lista_dias);
unset($dia_nome);
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
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_aba_cadastrar_agendamento']; ?></h2>
        <form method="post" action="/gerenciar-agendamentos-relay" style="padding:0px; margin:0px" name="agendamentos">
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
      <tr>
        <td width="160" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_url_rtmp']; ?></td>
        <td width="730" align="left"><input name="url_rtmp" type="text" class="input" id="url_rtmp" style="width:250px;" onclick="this.value=''" onfocus="this.value=''" value="rtmp://...." /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_frequencias']; ?></td>
        <td align="left">
        <select name="frequencia" id="frequencia" style="width:250px;" onchange="valida_opcoes_frequencia(this.value);">
          <option value="1" selected="selected"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_frequencia1']; ?></option>
          <option value="2"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_frequencia2']; ?></option>
          <option value="3"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_frequencia3']; ?></option>
        </select>
        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_data_inicio']; ?></td>
        <td align="left" class="texto_padrao_vermelho_destaque"><input name="data_inicio" type="text" id="data_inicio" onkeypress="return txtBoxFormat(this, '99/99/9999', event);" value="__/__/____" maxlength="10" onclick="this.value=''" onfocus="this.value=''" style="width:75px;" />
        &nbsp;(DD/MM/YYYY)</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_horario_inicio']; ?></td>
        <td align="left" class="texto_padrao_pequeno">
        <select name="hora_inicio" id="hora_inicio" style="width:50px;">
          <?php 
			for ($hora=0;$hora<=23;$hora++){

			echo '<option value="'.sprintf("%02d",$hora).'">'.sprintf("%02d",$hora).'</option>';
			
			}
			?>
        </select>
          <span class="texto_padrao_titulo">:</span>&nbsp;
          <select name="minuto_inicio" id="minuto_inicio" style="width:50px;">
            <?php 
			for ($minuto=0;$minuto<=59;$minuto++){

			echo '<option value="'.sprintf("%02d",$minuto).'">'.sprintf("%02d",$minuto).'</option>';
			
			}
			?>
          </select></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_data_termino']; ?></td>
        <td align="left" class="texto_padrao_vermelho_destaque"><input name="data_termino" type="text" id="data_termino" onkeypress="return txtBoxFormat(this, '99/99/9999', event);" value="__/__/____" maxlength="10" onclick="this.value=''" onfocus="this.value=''" style="width:75px;" />
        &nbsp;(DD/MM/YYYY)</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_horario_termino']; ?></td>
        <td align="left" class="texto_padrao_pequeno">
        <select name="hora_termino" id="hora_termino" style="width:50px;">
          <?php 
			for ($hora=0;$hora<=23;$hora++){

			echo '<option value="'.sprintf("%02d",$hora).'">'.sprintf("%02d",$hora).'</option>';
			
			}
			?>
        </select>
          <span class="texto_padrao_titulo">:</span>&nbsp;
          <select name="minuto_termino" id="minuto_termino" style="width:50px;">
            <?php 
			for ($minuto=0;$minuto<=59;$minuto++){

			echo '<option value="'.sprintf("%02d",$minuto).'">'.sprintf("%02d",$minuto).'</option>';
			
			}
			?>
          </select></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_dias_especificos']; ?></td>
        <td align="left" valign="middle" class="texto_padrao">
        <input name="dias[]" type="checkbox" value="1" id="dias" disabled="disabled" style="vertical-align:middle;cursor:not-allowed" /><?php echo $lang['lang_label_segunda']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="2" id="dias" disabled="disabled" style="vertical-align:middle;cursor:not-allowed" /><?php echo $lang['lang_label_terca']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="3" id="dias" disabled="disabled" style="vertical-align:middle;cursor:not-allowed" /><?php echo $lang['lang_label_quarta']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="4" id="dias" disabled="disabled" style="vertical-align:middle;cursor:not-allowed" /><?php echo $lang['lang_label_quinta']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="5" id="dias" disabled="disabled" style="vertical-align:middle;cursor:not-allowed" /><?php echo $lang['lang_label_sexta']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="6" id="dias" disabled="disabled" style="vertical-align:middle;cursor:not-allowed" /><?php echo $lang['lang_label_sabado']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="7" id="dias" disabled="disabled" style="vertical-align:middle;cursor:not-allowed" /><?php echo $lang['lang_label_domingo']; ?></td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_cadastrar']; ?>" />
          <input name="cadastrar" type="hidden" id="cadastrar" value="sim" />
          </td>
      </tr>
    </table>
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px;">
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque"><div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_gerenciador_agendamentos_relay_tab_info_titulo']; ?></strong></div>
          <div class="texto_medio" id="quadro-conteudo">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td height="25" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_instrucoes']; ?></td>
                </tr>
              </table>
          </div>
        </div></td>
      </tr>
    </table>
    </form>
      </div>
      <div class="tab-page" id="tabPage3">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_aba_logs']; ?>&nbsp;<img src="/admin/img/icones/img-icone-fechar.png" onclick="document.form_remover_logs.submit();" style="cursor:pointer" title="Reset Logs" width="12" height="12" align="absmiddle" /></h2>
        <form action="/gerenciar-agendamentos-relay" method="post" name="form_remover_logs"><input name="remover_logs" type="hidden" value="sim" /></form>
        <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid; border-bottom:#D5D5D5 1px solid;" id="tab2" class="sortable">
          <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
            <td width="200" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_relay_logs_data']; ?></td>
            <td width="690" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_relay_logs_url_rtmp']; ?></td>
          </tr>
<?php
$total_logs_agendamentos = mysql_num_rows(mysql_query("SELECT * FROM stmvideo.agendamentos_relay_logs where codigo_stm = '".$dados_stm["codigo"]."' ORDER by data"));

if($total_logs_agendamentos > 0) {

$sql = mysql_query("SELECT * FROM stmvideo.agendamentos_relay_logs WHERE codigo_stm = '".$dados_stm["codigo"]."' ORDER by data DESC LIMIT 100");
while ($dados_log_agendamento = mysql_fetch_array($sql)) {

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".formatar_data($dados_stm["formato_data"], $dados_log_agendamento["data"], $dados_stm["timezone"])."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_log_agendamento["url_rtmp"]."</td>
</tr>";

}

} else {

echo "<tr>
    <td height='23' colspan='2' align='center' class='texto_padrao'>".$lang['lang_info_sem_registros']."</td>
  </tr>";

}
?>
        </table>
      </div>
    </div>
    </div>
    </div>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/admin/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
