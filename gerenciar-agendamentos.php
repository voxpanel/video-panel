<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ftp.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($_POST["cadastrar"]) {

$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".$_POST["codigo_playlist"]."'"));

list($dia,$mes,$ano) = explode("/",$_POST["data"]);
$data = $ano."-".$mes."-".$dia;

mysql_query("INSERT INTO video.playlists_agendamentos (codigo_stm,codigo_playlist,data,hora,minuto) VALUES ('".$dados_stm["codigo"]."','".$_POST["codigo_playlist"]."','".$data."','".$_POST["hora"]."','".$_POST["minuto"]."')");

// Gera o arquivo com as playlists e envia para o FTP do streaming
$query_agendamentos = mysql_query("SELECT * FROM video.playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
while ($dados_agendamento = mysql_fetch_array($query_agendamentos)) {

$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".$dados_agendamento["codigo_playlist"]."'"));

$playlist = $dados_agendamento["codigo"]."_".formatar_nome_playlist($dados_playlist["nome"]);

$total_videos_playlist = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'"));

if($total_videos_playlist > 0) {

$query_videos = mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."' ORDER by ordem+0,codigo ASC");
while ($dados_playlist_video = mysql_fetch_array($query_videos)) {
$lista_videos .= $dados_playlist_video["path_video"].",";
}

$data_inicio = formatar_data("Y-m-d H:i:s", "".$dados_agendamento["data"]." ".$dados_agendamento["hora"].":".$dados_agendamento["minuto"].":00", $dados_stm["timezone"]);

$config_playlist[$playlist]["playlist"] = $dados_agendamento["codigo"]."_".formatar_nome_playlist($dados_playlist["nome"]);
$config_playlist[$playlist]["data_inicio"] = $data_inicio;
$config_playlist[$playlist]["total_videos"] = $data_inicio;
$config_playlist[$playlist]["videos"] = substr($lista_videos,0,-1);

unset($lista_videos);
}

}

$array_config_playlists = array ("login" => $dados_stm["login"], "playlists" => $config_playlist);

$resultado = gerar_playlist($array_config_playlists);

// Envia via FTP
// Conexão FTP
$ftp = new FTP();
$ftp->conectar($dados_servidor["ip"]);
$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);

if($ftp->enviar_arquivo("temp/".$resultado."","playlists_agendamentos.smil")) {

@unlink("temp/".$resultado."");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_agendamentos_resultado_ok']."","ok");
// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_agendamentos_resultado_alerta']."","alerta");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_agendamentos_resultado_erro']."","ok");

}


header("Location: /gerenciar-agendamentos");
exit();
}

$total_agendamentos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."'"));
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
<?php if($total_agendamentos == 0) { ?>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:20px; background-color:#FFFF66; border:#DFDF00 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="870" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_aviso_agendamento_trasnmissao_ao_vivo']; ?></td>
  </tr>
</table>
<?php } ?>
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_agendamentos_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_agendamentos_aba_agendamentos']; ?></h2>
  <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="220" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_playlist']; ?></td>
      <td width="520" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_horario_agendado']; ?></td>
      <td width="150" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_executar_acao']; ?></td>
    </tr>
<?php
$total_agendamentos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."' ORDER by data"));

if($total_agendamentos > 0) {

$sql = mysql_query("SELECT * FROM video.playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."' ORDER by data");
while ($dados_agendamento = mysql_fetch_array($sql)) {

$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".$dados_agendamento["codigo_playlist"]."'"));

$data = formatar_data($dados_stm["formato_data"], $dados_agendamento["data"], $dados_stm["timezone"])." ".$dados_agendamento["hora"].":".$dados_agendamento["minuto"].":00";
$data_original = $dados_agendamento["data"]." ".$dados_agendamento["hora"].":".$dados_agendamento["minuto"].":00";

$agendamento_code = code_decode($dados_agendamento["codigo"],"E");

$classe_texto = (strtotime($data_original) < time()) ? "texto_padrao_vermelho" : "texto_padrao";

echo "<tr>
<td height='25' align='left' scope='col' class='".$classe_texto."'>&nbsp;".$dados_playlist["nome"]."</td>
<td height='25' align='left' scope='col' class='".$classe_texto."'>&nbsp;".$data."</td>
<td height='25' align='left' scope='col' class='".$classe_texto."'>";

echo "<select style='width:100%' id='".$agendamento_code."' onchange='executar_acao_streaming(this.id,this.value);'>
  <option value='' selected='selected'>".$lang['lang_info_gerenciador_agendamentos_acao']."</option>
  <option value='ondemand-remover-agendamento'>".$lang['lang_info_gerenciador_agendamentos_acao_remover']."</option>
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
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_agendamentos_aba_cadastrar_agendamento']; ?></h2>
        <form method="post" action="/gerenciar-agendamentos" style="padding:0px; margin:0px" name="agendamentos">
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
      <tr>
        <td width="160" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_agendamentos_playlist']; ?></td>
        <td width="730" align="left">
        <select name="codigo_playlist" id="codigo_playlist" style="width:250px;">
        <?php
		$query_playlists = mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
		while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	
		$total_videos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'"));
		$duracao = mysql_fetch_array(mysql_query("SELECT *,SUM(duracao_segundos) as total FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'"));

		if($total_videos > 0) {
		echo '<option value="'.$dados_playlist["codigo"].'">'.$dados_playlist["nome"].' ('.gmdate("H:i:s", $duracao["total"]).')</option>';
		} else {
		echo '<option value="'.$dados_playlist["codigo"].'" disabled="disabled">'.$dados_playlist["nome"].' ('.$lang['lang_info_sem_videos'].')</option>';
		}
		}
        ?>
        </select>        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_data_inicio']; ?></td>
        <td align="left" class="texto_padrao_vermelho_destaque"><input name="data" type="text" id="data" onkeypress="return txtBoxFormat(this, '99/99/9999', event);" value="__/__/____" maxlength="10" onclick="this.value=''" style="width:75px;" />&nbsp;(DD/MM/YYYY)</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_horario_inicio']; ?></td>
        <td align="left" class="texto_padrao_pequeno">
        <select name="hora" id="hora" style="width:50px;">
          <option value="00">00</option>
          <option value="01">01</option>
          <option value="02">02</option>
          <option value="03">03</option>
          <option value="04">04</option>
          <option value="05">05</option>
          <option value="06">06</option>
          <option value="07">07</option>
          <option value="08">08</option>
          <option value="09">09</option>
          <option value="10">10</option>
          <option value="11">11</option>
          <option value="12">12</option>
          <option value="13">13</option>
          <option value="14">14</option>
          <option value="15">15</option>
          <option value="16">16</option>
          <option value="17">17</option>
          <option value="18">18</option>
          <option value="19">19</option>
          <option value="20">20</option>
          <option value="21">21</option>
          <option value="22">22</option>
          <option value="23">23</option>
        </select>
          <span class="texto_padrao_titulo">:</span>&nbsp;
          <select name="minuto" id="minuto" style="width:50px;">
            <?php 
			for ($minuto=0;$minuto<=59;$minuto++){

			echo '<option value="'.sprintf("%02d",$minuto).'">'.sprintf("%02d",$minuto).'</option>';
			
			}
			?>
          </select></td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_cadastrar']; ?>" />
          <input name="cadastrar" type="hidden" id="cadastrar" value="sim" />          </td>
      </tr>
    </table>
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px;">
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque"><div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_gerenciador_agendamentos_tab_info_titulo']; ?></strong></div>
          <div class="texto_medio" id="quadro-conteudo">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td height="25" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_agendamentos_instrucoes']; ?></td>
                </tr>
              </table>
          </div>
        </div></td>
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
<div id="log-sistema-botao"><img src="/admin/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
