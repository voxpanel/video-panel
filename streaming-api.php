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
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<div id="quadro">
            	<div id="quadro-topo"><strong><?php echo $lang['lang_info_streaming_api_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao"><?php echo $lang['lang_info_streaming_api_info']; ?>
    <br />
    <br />
    <span class="texto_padrao_destaque">API XML:</span><br />
  <input type="text" value="<?php echo "http://".$_SERVER['HTTP_HOST']."/api/".$login_code.""; ?>" style="width:90%; height:30px"  onclick="this.select()" readonly="readonly" />
<br />
<br />
<textarea readonly="readonly" style="width:90%; height:250px"  onclick="this.select()">
$xml = simplexml_load_file("<?php echo "http://".$_SERVER['HTTP_HOST']."/api/".$login_code.""; ?>");

echo $xml->ip; // Mostra o endereço do servidor
echo "<br>";
echo $xml->espectadores_conectados; // Mostra total de espectadores conectados
echo "<br>";
echo $xml->plano_espectadores; // Mostra o limite de ouvintes do plano
echo "<br>";
echo $xml->plano_ftp; // Mostra o limite de espaço do AutoDJ do plano
echo "<br>";
echo $xml->plano_bitrate; // Mostra o bitrate do plano
echo "<br>";
echo $xml->rtmp; // Mostra a URl do RTMP para uso em players próprios
echo "<br>";
echo $xml->rtsp; // Mostra a URl do RTSP para uso em players próprios
</textarea>
<br />
<br />
<span class="texto_padrao_destaque">API Json (jQuery/Javascript):</span><br />
  <input type="text" value="<?php echo "http://".$_SERVER['HTTP_HOST']."/api-json/".$login_code.""; ?>" style="width:90%; height:30px"  onclick="this.select()" readonly="readonly" />
<br />
<br />
<textarea readonly="readonly" style="width:90%; height:220px"  onclick="this.select()">
<script type="text/javascript">
$.getJSON('<?php echo "http://".$_SERVER['HTTP_HOST']."/api-json/".$login_code.""; ?>', function(data) {

var ip = data.ip; // Mostra o endereço do servidor da rádio
var espectadores_conectados = data.espectadores_conectados; // Mostra total de ouvintes conectados
var plano_espectadores = data.plano_espectadores; // Mostra o limite de ouvintes do plano
var plano_ftp = data.plano_ftp; // Mostra o limite de espaço do AutoDJ do plano
var plano_bitrate = data.plano_bitrate; // Mostra o bitrate do plano
var rtmp = data.rtmp; // Mostra a URl do RTMP para uso em players próprios
var rtsp = data.rtsp; // Mostra a URl do RTSP para uso em players próprios

});
</script>
</textarea>
<br />
<br /></td>
    </tr>
</table>
    </div>
      </div>
<br />
<br />
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
