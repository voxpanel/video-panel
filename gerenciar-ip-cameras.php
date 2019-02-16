<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ssh.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($_POST["cadastrar"]) {

$arquivo_stream = formatar_nome_ip_camera($_POST["nome"]).".stream";

// Cria o arquivo .stream no servidor
// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
$resultado = $ssh->executar("echo '".$_POST["url_rtsp"]."' > /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/".$arquivo_stream.";echo OK");

if(preg_match('/OK/i',$resultado)) {

mysql_query("INSERT INTO stmvideo.ip_cameras (codigo_stm,nome,rtsp,stream,data_cadastro) VALUES ('".$dados_stm["codigo"]."','".$_POST["nome"]."','".$_POST["url_rtsp"]."','".$arquivo_stream."',NOW())");

$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
if($status_streaming["status"] == "loaded") {

$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
}
	
$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance  ".$dados_stm["login"]."");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_ip_cameras_resultado_ok']."","ok");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_ip_cameras_resultado_erro']."","erro");

}

header("Location: /gerenciar-ip-cameras");
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

echo '<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_ip_cameras_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_ip_cameras_aba_ip_cameras']; ?></h2>
  <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="200" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_ip_cameras_nome']; ?></td>
      <td width="550" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_ip_cameras_rtsp']; ?></td>
      <td width="140" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_executar_acao']; ?></td>
    </tr>
<?php
$total_cameras = mysql_num_rows(mysql_query("SELECT * FROM stmvideo.ip_cameras where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo"));

if($total_cameras > 0) {

$sql = mysql_query("SELECT * FROM stmvideo.ip_cameras where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo");
while ($dados_ip_camera = mysql_fetch_array($sql)) {

$ip_camera_code = code_decode($dados_ip_camera["codigo"],"E");

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_ip_camera["nome"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_ip_camera["rtsp"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>";

echo "<select style='width:100%' id='".$ip_camera_code."' onchange='executar_acao_streaming(this.id,this.value);'>
  <option value='' selected='selected'>".$lang['lang_info_escolha_acao']."</option>
  <option value='player-ip-camera'>".$lang['lang_info_gerenciador_ip_cameras_executar_acao_player']."</option>
  <option value='remover-ip-camera'>".$lang['lang_info_gerenciador_ip_cameras_executar_acao_remover']."</option>
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
</div>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_ip_cameras_aba_cadastrar_camera']; ?></h2>
        <form method="post" action="/gerenciar-ip-cameras" style="padding:0px; margin:0px" name="ip-cameras">
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
      <tr>
        <td width="160" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_ip_cameras_nome']; ?></td>
        <td width="730" align="left"><input name="nome" type="text" class="input" id="nome" style="width:250px;" />&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_gerenciador_ip_cameras_nome_info']; ?>');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_ip_cameras_url_rtsp']; ?></td>
        <td align="left"><input name="url_rtsp" type="text" class="input" id="url_rtsp" style="width:250px;" value="rtsp://" onclick="this.value=''" />&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_gerenciador_ip_cameras_url_rtsp_info']; ?>');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_cadastrar']; ?>" />
          <input name="cadastrar" type="hidden" id="cadastrar" value="sim" />
        </td>
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
<div id="log-sistema-botao"><img src="/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
