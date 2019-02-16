<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ftp.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if(isset($_POST["alterar"])) {

if(count($_POST["qualidades"]) > 0){
	$transcoder_qualidades = implode("|",$_POST["qualidades"]);
}

// Atualiza as qualidades
mysql_query("Update video.streamings set transcoder_qualidades = '".$transcoder_qualidades."' where codigo = '".$dados_stm["codigo"]."'");

// Atualiza o arquivo /home/streaming/LOGIN/transcoder.smil
$conteudo_transcoder .= "<smil>\n";
$conteudo_transcoder .= "<head></head>\n";
$conteudo_transcoder .= "<body>\n";
$conteudo_transcoder .= "<switch>\n";

foreach($_POST['qualidades'] as $qualidade) {

if($qualidade == "720p") {
$conteudo_transcoder .= "<video src=\"mp4:".$dados_stm["login"]."_720p\" system-bitrate=\"1300000\" height=\"720\"></video>\n";
}
if($qualidade == "360p") {
$conteudo_transcoder .= "<video src=\"mp4:".$dados_stm["login"]."_360p\" system-bitrate=\"850000\" height=\"360\"></video>\n";
}
if($qualidade == "240p") {
$conteudo_transcoder .= "<video src=\"mp4:".$dados_stm["login"]."_240p\" system-bitrate=\"350000\" height=\"240\"></video>\n";
}
if($qualidade == "160p") {
$conteudo_transcoder .= "<video src=\"mp4:".$dados_stm["login"]."_160p\" system-bitrate=\"200000\" height=\"160\"></video>\n";
}
if($qualidade == "h263") {
$conteudo_transcoder .= "<video src=\"mp4:".$dados_stm["login"]."_h263\" system-bitrate=\"150000\"></video>\n";
}

}

$conteudo_transcoder .= "</switch>\n";
$conteudo_transcoder .= "</body>\n";
$conteudo_transcoder .= "</smil>\n";

$handle_transcoder = fopen("temp/".$dados_stm["login"]."_transcoder.smil" ,"w");
fwrite($handle_transcoder, $conteudo_transcoder);
fclose($handle_transcoder);

// Conexão FTP
$ftp = new FTP();
$ftp->conectar($dados_servidor["ip"]);
$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);
	
$ftp->enviar_arquivo("temp/".$dados_stm["login"]."_transcoder.smil","transcoder.smil");

@unlink("temp/".$dados_stm["login"]."_transcoder.smil");

if(!mysql_error()) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Configuração alterada com sucesso.","ok");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Não foi possível alterar a configuração.","erro");

}

header("Location: /configuracoes-transcoder");
exit();

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo-pequeno">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<form method="post" action="/configuracoes-transcoder" style="padding:0px; margin:0px">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_config_painel_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_config_painel_aba_configuracoes']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="45" height="25" align="center"><input type="checkbox" name="qualidades[]" value="720p" <?php if(strpos($dados_stm["transcoder_qualidades"], '720p') !== false) {echo 'checked="checked"';} ?> /></td>
            <td width="643" align="left" class="texto_padrao">720p (1280 x 720)</td>
          </tr>
          <tr>
            <td height="25" align="center"><input type="checkbox" name="qualidades[]" value="360p" <?php if(strpos($dados_stm["transcoder_qualidades"], '360p') !== false) {echo 'checked="checked"';} ?> /></td>
            <td align="left" class="texto_padrao">360p (640 x 360)</td>
         </tr>
          <tr>
            <td height="25" align="center"><input type="checkbox" name="qualidades[]" value="240p" <?php if(strpos($dados_stm["transcoder_qualidades"], '240p') !== false) {echo 'checked="checked"';} ?> /></td>
            <td align="left" class="texto_padrao">240p (360 x 240)</td>
          </tr>
          <tr>
            <td height="25" align="center"><input type="checkbox" name="qualidades[]" value="160p" <?php if(strpos($dados_stm["transcoder_qualidades"], '160p') !== false) {echo 'checked="checked"';} ?> /></td>
            <td align="left" class="texto_padrao">160p (284 x 160)</td>
         </tr>
          <tr>
            <td height="25" align="center"><input type="checkbox" name="qualidades[]" value="h263" <?php if(strpos($dados_stm["transcoder_qualidades"], 'h263') !== false) {echo 'checked="checked"';} ?> /></td>
            <td align="left" class="texto_padrao">h263 (176 x 144)</td>
          </tr>
        </table>
   	  </div>
      </div></td>
  </tr>
  <tr>
    <td height="40" align="center"><input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_alterar_config']; ?>" />
      <input name="alterar" type="hidden" id="alterar" value="<?php echo time(); ?>" /></td>
  </tr>
</table>
    </div>
      </div>
</form>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>