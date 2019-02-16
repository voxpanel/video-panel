<?php
// Inclusão de classes
require_once("../inc/geoip/geoipcity.inc");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));

$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
if($status_streaming["status"] == "loaded") {

$array_estatisticas = estatistica_espectadores_conectados($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);

$array_estatisticas = @array_filter($array_estatisticas);

$estatisticas = $array_estatisticas;

$porcentagem_uso_espectadores = ($dados_stm["espectadores"] == 0) ? "0" : count($estatisticas)*100/$dados_stm["espectadores"];
$porcentagem_uso_espectadores = ($porcentagem_uso_espectadores < 1 && count($estatisticas) > 0) ? "1" : $porcentagem_uso_espectadores;

$porcentagem_uso_ftp = ($dados_stm["espaco_usado"] == 0 || $dados_stm["espaco"] == 0) ? "0" : $dados_stm["espaco_usado"]*100/$dados_stm["espaco"];

} else {

die('<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; background-color:#FFFF66; border:#DFDF00 1px solid">
	<tr>
		<td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    	<td align="left" scope="col" style="color: #AB1C10;	font-family: Geneva, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;">'.$lang['E'].'</td>
  	</tr>
</table>');

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-movel.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script type="text/javascript">
   window.onload = function() {
   	initialize();
    setTimeout("window.location.reload(true);",30000);
   };
</script>
<style type="text/css">
<!--
body {
	overflow-x: hidden;
}
-->
</style>
</head>

<body>
<div id="topo">
<div id="topo-botao-ligar">
<img src="/img/icones/img-icone-ligar-64x64.png" title="<?php echo $lang['lang_botao_titulo_ligar_stm']; ?>" width="48" height="48" style="cursor:pointer" onclick="ligar_streaming('<?php echo $login_code;?>');" />
</div>
<div id="topo-botao-desligar">
<img src="/img/icones/img-icone-desligar-48x48.png" title="<?php echo $lang['lang_botao_titulo_desligar_stm']; ?>" width="32" height="32" style="cursor:pointer" onclick="desligar_streaming('<?php echo $login_code;?>');" />
</div>
<div id="topo-botao-reiniciar">
<img src="/img/icones/img-icone-reiniciar-48x48.png" title="<?php echo $lang['lang_botao_titulo_reiniciar_stm']; ?>" width="32" height="32" style="cursor:pointer" onclick="reiniciar_streaming('<?php echo $login_code;?>');" />
</div>
<div id="topo-menu" class="texto_padrao_pequeno">
    <select class="topo-menu-select" name="menu_executar_acao" onchange="executar_acao_streaming_movel('<?php echo $login_code; ?>',this.value);">
        <option value="" selected="selected"><?php echo $lang['lang_info_escolha_acao']; ?></option>
        <optgroup label="Streaming">
        <option value="informacoes"><?php echo $lang['lang_acao_stm_info']; ?></option>
        <?php if($dados_stm["aplicacao"] == 'live' || $dados_stm["aplicacao"] == 'tvstation') { ?>
        <option value="dados-conexao"><?php echo $lang['lang_acao_stm_dados_conexao']; ?></option>
        <option value="gravador"><?php echo $lang['lang_acao_stm_gravador']; ?></option>
        <?php } ?>
        <option value="espectadores-conectados"><?php echo $lang['lang_acao_espectadores_espectadores_conectados']; ?></option>
        <?php if($dados_stm["aplicacao"] == 'tvstation') { ?>
        <option value="iniciar-playlist"><?php echo $lang['lang_botao_titulo_iniciar_playlist']; ?></option>
        <?php } ?>
        </optgroup>
    </select>
</div>
</div>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
  <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
    <td width="40%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_espectadores_conectados_ip']; ?></td>
    <td width="30%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_espectadores_conectados_pais']; ?></td>
    <td width="28%" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_espectadores_conectados_tempo_conectado']; ?></td>
  </tr>
<?php
$i = 1;

if(count($estatisticas) > 0) {

foreach($estatisticas as $ip => $estatistica) {

list($tempo_conectado, $pais_sigla, $pais_nome, $player) = explode("|",$estatistica);

echo "
  <tr>
    <td height='23' class='texto_padrao'>&nbsp;".$ip."</td>
    <td height='23' class='texto_padrao'>&nbsp;<img src='/img/icones/paises/".strtolower($pais_sigla).".png' border='0' align='absmiddle' />&nbsp;".$pais_nome."</td>
	<td height='23' class='texto_padrao'>&nbsp;".$tempo_conectado."</td>
  </tr>
";

// Dados para o mapa
$conexao_geoip = geoip_open("../inc/geoip/GeoIPCity.dat", GEOIP_STANDARD);
$dados_geoip = geoip_record_by_addr($conexao_geoip, $ip);

if($dados_geoip->latitude && $dados_geoip->longitude) {

$dados_LatLng_array .= "myLatlng".$i.",";
$dados_LatLng_array_nome .= "\"".$lang['lang_info_espectadores_conectados_espectador'].": ".$ip."\",";
$dados_LatLng_array_info .= "\"<div class='texto_padrao' style='text-align:left'><strong>".$lang['lang_info_espectadores_conectados_ip'].":</strong> ".$ip."<br><strong>".$lang['lang_info_espectadores_conectados_pais'].":</strong> ".$pais_nome."&nbsp;<img src='/img/icones/paises/".strtolower($pais_sigla).".png' border='0' align='absmiddle' /><br><strong>".$lang['lang_info_espectadores_conectados_player'].":</strong> ".$player."<br><strong>".$lang['lang_info_espectadores_conectados_tempo_conectado'].":</strong> ".$tempo_conectado."</div>\",";
$dados_LatLng .= "var myLatlng".$i." = new google.maps.LatLng( ".$dados_geoip->latitude.", ".$dados_geoip->longitude.");\n";
$i++;
}

}

} else {

echo "
  <tr>
    <td height='30' colspan='4' align='center' class='texto_status_erro'>".$lang['lang_info_espectadores_conectados_info_sem_espectadores']."</td>
  </tr>
";

}
?>
</table>
<br />
<script type="text/javascript">
function initialize() {

<?php
  echo substr($dados_LatLng, 0, -1);
?>
  
  var locationArray = [<?php echo substr($dados_LatLng_array, 0, -1); ?>];
  var locationArrayName = [<?php echo substr($dados_LatLng_array_nome, 0, -1); ?>];
  var locationArrayInfo = [<?php echo substr($dados_LatLng_array_info, 0, -1); ?>];
  
  var myOptions = {
  zoom: 2,
  center: new google.maps.LatLng(-5,-30),
  mapTypeId: google.maps.MapTypeId.ROADMAP,
  }
  
  var map = new google.maps.Map(document.getElementById("mapa_ips"), myOptions);
  
  for(var cont = 0; cont < locationArray.length; cont++) {
  
  var infowindow = new google.maps.InfoWindow({
      content: "Carregando..."
  });
  
  var marker = new google.maps.Marker({
    position: locationArray[cont],
    title: locationArrayName[cont],
	html: locationArrayInfo[cont]
  });
  
  google.maps.event.addListener(marker, 'click', function() {
    infowindow.setContent(this.html);
	infowindow.open(map,this);
  });

  marker.setMap(map);
  }
}
</script>
<div id="mapa_ips" style="width: 98%; height: 200px; margin:0px auto" align="center"></div>
</body>
</html>