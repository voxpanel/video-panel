<?php
// Funções de uso no painel

////////////////////////////////////////
//////////// Funções Gerais ////////////
////////////////////////////////////////

// Função para gerenciar query string
function query_string($posicao='0') {

$gets = explode("/",str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));
array_shift($gets);

return utf8_decode(urldecode($gets[$posicao]));

}

// Função para codificar e decodificar strings
function code_decode($texto, $tipo = "E") {

  if($tipo == "E") {
  
  $sesencoded = $texto;
  $num = mt_rand(0,3);
  for($i=1;$i<=$num;$i++)
  {
     $sesencoded = base64_encode($sesencoded);
  }
  $alpha_array = array('1','Z','3','R','1','Y','2','N','A','T','Z','X','A','E','Y','6','9','4','F','S','X');
  $sesencoded =
  $sesencoded."+".$alpha_array[$num];
  $sesencoded = base64_encode($sesencoded);
  return $sesencoded;
  
  } else {
  
   $alpha_array = array('1','Z','3','R','1','Y','2','N','A','T','Z','X','A','E','Y','6','9','4','F','S','X');
   $decoded = base64_decode($texto);
   list($decoded,$letter) = explode("+",$decoded);
   for($i=0;$i<count($alpha_array);$i++)
   {
   if($alpha_array[$i] == $letter)
   break;
   }
   for($j=1;$j<=$i;$j++)
   {
      $decoded = base64_decode($decoded);
   }
   return $decoded;
  }
}

// Função para gerar ID da revenda
function gera_id() {

$aux = microtime();
$id = substr(md5($aux),0,6);

return $id;
}

// Função para criar células de logs do sistema
function status_acao($status,$tipo) {

if($tipo == 'ok') {
$celula_status = '<tr style="background-color:#A6EF7B;">
      <td width="790" height="35" class="texto_log_sistema" scope="col">
	  <div align="center">'.$status.'</div>
	  </td>
</tr>
<tr><td scope="col" height="2" width="770"></td></tr>
';
} elseif($tipo == 'ok2') {
$celula_status = '<tr style="background-color:#A6EF7B;">
      <td width="790" height="35" class="texto_log_sistema" scope="col">
	  <div align="center">'.$status.'</div>
	  </td>
</tr>
<tr><td scope="col" height="2" width="770"></td></tr>
';
} elseif($tipo == 'alerta') {
$celula_status = '<tr style="background-color:#FFFF66;">
      <td width="790" height="35" class="texto_log_sistema_alerta" scope="col">
	  <div align="center">'.$status.'</div>

	  </td>
</tr>
<tr><td scope="col" height="2" width="770"></td></tr>
';
} else {
$celula_status = '<tr style="background-color:#F2BBA5;">
      <td width="790" height="35" class="texto_log_sistema_erro" scope="col">
	  <div align="center">'.$status.'</div>
	  </td>
</tr>
<tr><td scope="col" height="2" width="770"></td></tr>
';
}  

return $celula_status;
}

// Função para remover acentos e espaços
function formatar_nome_playlist($playlist) {

$array_caracteres = array("/[ÂÀÁÄÃ]/"=>"a","/[âãàáä]/"=>"a","/[ÊÈÉË]/"=>"e","/[êèéë]/"=>"e","/[ÎÍÌÏ]/"=>"i","/[îíìï]/"=>"i","/[ÔÕÒÓÖ]/"=>"o", "/[ôõòóö]/"=>"o","/[ÛÙÚÜ]/"=>"u","/[ûúùü]/"=>"u","/ç/"=>"c","/Ç/"=> "c","/ /"=> "","/_/"=> "");

$formatado = preg_replace(array_keys($array_caracteres), array_values($array_caracteres), $playlist);

return strtolower($formatado);
}

// Função para remover acentos e espaços
function formatar_nome_ip_camera($ip_camera) {

$array_caracteres = array("/[ÂÀÁÄÃ]/"=>"a","/[âãàáä]/"=>"a","/[ÊÈÉË]/"=>"e","/[êèéë]/"=>"e","/[ÎÍÌÏ]/"=>"i","/[îíìï]/"=>"i","/[ÔÕÒÓÖ]/"=>"o", "/[ôõòóö]/"=>"o","/[ÛÙÚÜ]/"=>"u","/[ûúùü]/"=>"u","/ç/"=>"c","/Ç/"=> "c","/ /"=> "","/_/"=> "");

$formatado = preg_replace(array_keys($array_caracteres), array_values($array_caracteres), $ip_camera);

return strtolower($formatado);
}

// Função para formatar texto retirando acentos e caracteres especiais
function formatar_texto($texto) {

$characteres = array(
    'S'=>'S', 's'=>'s', 'Ð'=>'Dj','Z'=>'Z', 'z'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
    'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
    'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'f'=>'f', '¹'=> '', '²'=> '', '&'=> 'e',
	'³'=> '', '£'=> '', '$'=> '', '%'=> '', '¨'=> '', '§'=> '', 'º'=> '', 'ª'=> '', '©'=> '', 'Ã£'=> '',
	'('=> '', ')'=> '', "'"=> '', '@'=> '', '='=> '', ':'=> '', '!'=> '', '?'=> '', '...'=> '', '['=> '',
	']'=> '', '"'=> '', '.'=> ''
);

return strtr($texto, $characteres);

}

function pais_ip($ip,$tipo) {

require_once("/home/painelvideo/public_html/inc/geoip/geoip.inc");

$conexao_geoip = geoip_open("/home/painelvideo/public_html/inc/geoip/GeoIP.dat", GEOIP_STANDARD);

$array_prefixos_ips_brasil = array("177","179","186","187","189","191");

list($ip_parte1, $ip_parte2, $ip_parte3, $ip_parte4) = explode(".",$ip);

if($tipo == "nome") {

$pais_nome = geoip_country_name_by_addr($conexao_geoip, $ip);

if($pais_nome) {
return str_replace("Brazil","Brasil",$pais_nome);
} else {

if (in_array($ip_parte1, $array_prefixos_ips_brasil)) {
return "Brasil";
} else {
return "IP sem informações de localização";
}

}

} else {

$pais_code = geoip_country_code_by_addr($conexao_geoip, $ip);

if($pais_code) {
return $pais_code;
} else {

if (in_array($ip_parte1, $array_prefixos_ips_brasil)) {
return "BR";
} else {
return "Desconhecido";
}

}

}

}

// Função para remover acentos
function remover_acentos($msg) {
$a = array("/[ÂÀÁÄÃ]/"=>"A","/[âãàáä]/"=>"a","/[ÊÈÉË]/"=>"E","/[êèéë]/"=>"e","/[ÎÍÌÏ]/"=>"I","/[îíìï]/"=>"i","/[ÔÕÒÓÖ]/"=>"O",	"/[ôõòóö]/"=>"o","/[ÛÙÚÜ]/"=>"U","/[ûúùü]/"=>"u","/ç/"=>"c","/Ç/"=> "C");

return preg_replace(array_keys($a), array_values($a), $msg);
}

// Função para formatar os segundos em segundos, minutos e horas
function tempo_conectado($segundos) {

$days=intval($segundos/86400);
$remain=$segundos%86400;
$hours=intval($remain/3600);
$remain=$remain%3600;
$mins=intval($remain/60);
$secs=$remain%60;
if (strlen($mins)<2) {
$mins = '0'.$mins;
}
if($days > 0) $dia = $days.'d';
if($hours > 0) $hora = $hours.'hr, ';
if($mins > 0) $minuto = $mins.'min, ';

$segundo = $secs.'seg';
$segundos = $dia.$hora.$minuto.$segundo;

return $segundos;

}

function seconds2time($segundos) {

return @gmdate("H:i:s", round($segundos));

}

// Função para retornar o tipo de medida do tamanho do arquivo(Byts, Kbytes, Megabytes, Gigabytes, etc...)
function tamanho($size)
{
    $filesizename = array(" MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return $size ? round($size/pow(1000, ($i = floor(log($size, 1000)))), 2) . $filesizename[$i] : '0 Bytes';
}

// Função para criar um barra de porcentagem de uso do plano
function barra_uso_plano($porcentagem,$descricao) {

$porcentagem_progresso = ($porcentagem > 100) ? "100" : $porcentagem;

$cor = "#00CC00";
$cor = ($porcentagem_progresso > 50 && $porcentagem_progresso < 80) ? "#FFE16C" : $cor;
$cor = ($porcentagem_progresso > 80) ? "#FF0000" : $cor;

return "<div class='barra-uso-plano-corpo' title='".$descricao."'>
<div class='barra-uso-plano-progresso' style='background-color: ".$cor."; width: ".round($porcentagem_progresso)."%;'>
<div class='barra-uso-plano-texto'>".round($porcentagem)."%</div>
</div>
</div>";

}

// Função para calcular tempo de exceussão
function tempo_execucao() {
    $sec = explode(" ",microtime());
    $tempo = $sec[1] + $sec[0];
    return $tempo;
}

function anti_sql_injection($str) {
    if (!is_numeric($str)) {
        $str = get_magic_quotes_gpc() ? stripslashes($str) : $str;
        $str = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($str) : mysql_escape_string($str);
    }
    return $str;
}

function zebrar($i) {
    return func_get_arg(abs($i) % (func_num_args() - 1) + 1);
}

function anti_hack_dominio($lista_bloqueados) {

$dominio = str_replace("www.","",$_SERVER['HTTP_HOST']);

if(preg_grep('/'.$dominio.'/i',$lista_bloqueados)) {

die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Manutenção</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="http://srvstm.com/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:200px; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
<td width="30" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
<td width="870" align="left" class="texto_status_erro" scope="col">Oops! A p&aacute;gina que tentou acessar esta em manuten&ccedil;&atilde;o, volte em alguns minutos.</td>
</tr>
</table>
</body>
</html>');

}

}

function anti_hack_ip($lista_bloqueados) {

if(!array_search($_SERVER['REMOTE_ADDR'], $lista_bloqueados)) {

die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Acesso Bloqueado para '.$_SERVER['REMOTE_ADDR'].'</title>
<meta http-equiv="cache-control" content="no-cache">
<link href="http://srvstm.com/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:200px; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
<td width="30" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
<td width="870" align="left" class="texto_status_erro" scope="col">Oops! foram registrados tentativas de ataques vindo de seu endereço IP e por segurança nosso firewall efetuou bloqueio de acesso.<br><br>Por favor contate nosso atendimento.</td>
</tr>
</table>
</body>
</html>');

}

}

// Função para conectar a uma URL
function conectar_url($url,$timeout) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)');
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado === false) {
return "erro";
} else {

return $resultado;

}

}

// Função para carregar avisos para streamings na inicialização
function carregar_avisos_streaming_inicializacao($login,$servidor) {

$sql = mysql_query("SELECT * FROM video.avisos WHERE area = 'streaming'");
while ($dados_aviso = mysql_fetch_array($sql)) {

if($dados_aviso["status"] == "sim") {

$checar_status_aviso = mysql_num_rows(mysql_query("SELECT * FROM video.avisos_desativados where codigo_aviso = '".$dados_aviso["codigo"]."' AND area = 'streaming' AND login = '".$login."'"));

if($checar_status_aviso == 0 && ($dados_aviso["codigo_servidor"] == "0" || $dados_aviso["codigo_servidor"] == $servidor)) {

echo "exibir_aviso('".$dados_aviso["codigo"]."');";

} // if aviso desativado usuario
} // if exibir sim/nao
} // while avisos

}

// Função para carregar avisos para streamings
function carregar_avisos_streaming($login,$servidor) {

$avisos = "";
$total_avisos = 0;

$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y') AS data FROM video.avisos WHERE area = 'streaming'");
while ($dados_aviso = mysql_fetch_array($sql)) {

if($dados_aviso["status"] == "sim" && ($dados_aviso["codigo_servidor"] == "0" || $dados_aviso["codigo_servidor"] == $servidor)) {

$checar_status_aviso = mysql_num_rows(mysql_query("SELECT * FROM video.avisos_desativados where codigo_aviso = '".$dados_aviso["codigo"]."' AND area = 'streaming' AND login = '".$login."'"));

if($checar_status_aviso == 0) {

$avisos .= "[".$dados_aviso["data"]."] ".$dados_aviso["descricao"]."&nbsp;<a href='#' onclick='exibir_aviso(\"".$dados_aviso["codigo"]."\");'>[+]</a><br />";

$total_avisos++;

} // if exibir sim/nao DESATIVADO
} // if exibir sim/nao
} // while avisos

if($total_avisos > 0) {
return $avisos;
}

}

// Função para carregar avisos para revendas
function carregar_avisos_revenda() {

$total_avisos = 0;

$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y') AS data FROM video.avisos WHERE area = 'revenda' ORDER by data DESC");
while ($dados_aviso = mysql_fetch_array($sql)) {

if($dados_aviso["status"] == "sim") {

echo "[".$dados_aviso["data"]."] ".$dados_aviso["descricao"]."&nbsp;<a href='#' onclick='exibir_aviso(\"".$dados_aviso["codigo"]."\");'>[+]</a><br />";

$total_avisos++;
}
}

if($total_avisos == 0) {
echo "<span class='texto_padrao'>Não há registro de avisos.</span>";
}

}

// Função para carregar avisos para streamings
function carregar_avisos_streaming_revenda($login,$servidor) {

$total_avisos = 0;

$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y') AS data FROM video.avisos WHERE area = 'streaming'");
while ($dados_aviso = mysql_fetch_array($sql)) {

if($dados_aviso["status"] == "sim") {

$checar_status_aviso = mysql_num_rows(mysql_query("SELECT * FROM video.avisos_desativados where codigo_aviso = '".$dados_aviso["codigo"]."' AND area = 'streaming' AND login = '".$login."'"));

if($checar_status_aviso == 0 && ($dados_aviso["codigo_servidor"] == "0" || $dados_aviso["codigo_servidor"] == $servidor)) {

echo "[".$dados_aviso["data"]."] ".$dados_aviso["descricao"]."&nbsp;<a href='#' onclick='exibir_aviso(\"".$dados_aviso["codigo"]."\");'>[+]</a><br />";

$total_avisos++;

} // if aviso desativado usuario
} // if exibir sim/nao
} // while avisos

if($total_avisos == 0) {
echo "<span class='texto_padrao'>Não há registro de avisos.</span>";
}

}

// Função para criar formatar domínio do servidor
function dominio_servidor( $nome ) {

if($_SESSION["login_logado"]) {
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
} elseif($_SESSION["code_user_logged"] && $_SESSION["type_logged_user"] == "cliente") {
$dados = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
} else {
$dados = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
}

if($dados["dominio_padrao"]) {
return strtolower($nome).".".$dados["dominio_padrao"];
} else {
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
return strtolower($nome).".".$dados_config["dominio_padrao"];
}

}

function xml_entity_decode($_string) {
    // Set up XML translation table
    $_xml=array();
    $_xl8=get_html_translation_table(HTML_ENTITIES,ENT_COMPAT);
    while (list($_key,)=each($_xl8))
        $_xml['&#'.ord($_key).';']=$_key;
    return strtr($_string,$_xml);
}

// Função abreviar o nome do navegador
function formatar_navegador($navegador) {

if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$navegador,$matched)) {
return  'IE '.$matched[1].'';
} elseif (preg_match( '|Opera/([0-9].[0-9]{1,2})|',$navegador,$matched)) {
return  'Opera '.$matched[1].'';
} elseif(preg_match('|Firefox/([0-9\.]+)|',$navegador,$matched)) {
return  'Firefox '.$matched[1].'';
} elseif(preg_match('|Chrome/([0-9\.]+)|',$navegador,$matched)) {
return  'Chrome '.$matched[1].'';
} elseif(preg_match('|Safari/([0-9\.]+)|',$navegador,$matched)) {
return  'Safari '.$matched[1].'';
} else {
return 'Desconhecido';
}

}

// Função para inserir registro do log de ações do painel de administração/revenda no banco de dados
function logar_acao($log) {

mysql_query("INSERT INTO video.logs (data,host,ip,navegador,log) VALUES (NOW(),'http://".$_SERVER['HTTP_HOST']."','".$_SERVER['REMOTE_ADDR']."','".formatar_navegador($_SERVER['HTTP_USER_AGENT'])."','".$log."')") or die("Erro ao inserir log: ".mysql_error()."");

}

// Função para inserir registro do log de ações do painel de streaming no banco de dados
function logar_acao_streaming($codigo_stm,$log) {

mysql_query("INSERT INTO video.logs_streamings (codigo_stm,data,host,ip,navegador,log) VALUES ('".$codigo_stm."',NOW(),'http://".$_SERVER['HTTP_HOST']."','".$_SERVER['REMOTE_ADDR']."','".formatar_navegador($_SERVER['HTTP_USER_AGENT'])."','".$log."')") or die("Erro ao inserir log: ".mysql_error()."");

}

////////////////////////////////////////////////
/////////////// Funções  Wowza /////////////////
////////////////////////////////////////////////

// Verifica se esta ao vivo
function status_aovivo($agent, $ip) {

$array_user_agents = array("Wirecast","Teradek","vmix","Vmix","FMLE");

list($user_agent, $other1, $other2) = explode("/",$agent);

if($ip == "127.0.0.1") {
return "relay";
} elseif(in_array($user_agent, $array_user_agents)) {
return "aovivo";
} else {
return "ondemand";
}

}

// Função para checar status do streaming
function status_streaming($ip,$senha,$login) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":555/stats");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERPWD, "admin:".code_decode($senha,"D").""); 
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
ob_start();
$resultado = curl_exec($ch);
$data = ob_get_clean();

$status_transmissao = "";

$xml = @simplexml_load_string(utf8_encode($resultado));

$total_streamings = count($xml->VHost->Application);

if($total_streamings > 0) {

for($i=0;$i<$total_streamings;$i++){

if($xml->VHost->Application[$i]->Name == $login) {

$status = $xml->VHost->Application[$i]->Status;
	
$total_streamings_clients = $xml->VHost->Application[$i]->ApplicationInstance->ConnectionsCurrent;

if($total_streamings_clients > 0) {

for($ii=0;$ii<$total_streamings_clients;$ii++){

$status_transmissao = status_aovivo($xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->FlashVersion, $xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress);

if($status_transmissao == "aovivo") {
break;
}
}
}

break;

}

}

}

return array("status" => $status, "status_transmissao" => $status_transmissao);

curl_close($ch);
}

// Função para capturar o TOTAL de espectadores conectados
function total_espectadores_conectados($ip,$senha,$login) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":555/stats");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERPWD, "admin:".code_decode($senha,"D").""); 
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
ob_start();
$resultado = curl_exec($ch);
$data = ob_get_clean();

$xml = @simplexml_load_string(utf8_encode($resultado));

$total_streamings = count($xml->VHost->Application);

if($total_streamings > 0) {

for($i=0;$i<$total_streamings;$i++){

if($xml->VHost->Application[$i]->Name == $login) {

$total_espectadores1 = $xml->VHost->Application[$i]->ApplicationInstance->ConnectionsCurrent;

for($ii=0;$ii<$total_espectadores1;$ii++){

$status_transmissao = status_aovivo($xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->FlashVersion, $xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress);

if($status_transmissao != "aovivo") {

$ip = $xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress;

$array_espectadores[] = $ip;

}

}

$total_espectadores2 = count($xml->VHost->Application[$i]->ApplicationInstance[1]->ConnectionsCurrent);

for($iii=0;$iii<$total_espectadores2;$iii++){

$status_transmissao = status_aovivo($xml->VHost->Application[$i]->ApplicationInstance[1]->Client[$iii]->FlashVersion, $xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress);

if($status_transmissao != "aovivo") {

$ip = $xml->VHost->Application[$i]->ApplicationInstance[1]->Client[$iii]->IpAddress;

$array_espectadores[] = $ip;

}

}

$total_espectadores3 = count($xml->VHost->Application[$i]->ApplicationInstance[1]->HTTPSession);

for($iiii=0;$iiii<$total_espectadores3;$iiii++){

$ip = $xml->VHost->Application[$i]->ApplicationInstance[1]->HTTPSession[$iiii]->IpAddress;

$array_espectadores[] = $ip;

}

break;

}

}

}

return array("espectadores" => count($array_espectadores));

curl_close($ch);

}

// Função para obter as estatisticas do streaming no servidor para os robots
function estatistica_streaming_robot($ip,$senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":555/stats");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_USERPWD, "admin:".code_decode($senha,"D").""); 
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-BR; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 ( .NET CLR 3.5.30729)');
$resultado = curl_exec($ch);
curl_close($ch);

return $resultado;
}

// Função para obter as estatisticas do streaming no servidor para a pagina de espectadores conectados
function estatistica_espectadores_conectados($ip,$senha,$login) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":555/stats");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERPWD, "admin:".code_decode($senha,"D").""); 
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
ob_start();
$resultado = curl_exec($ch);
$data = ob_get_clean();

$array_espectadores = array();

$xml = @simplexml_load_string(utf8_encode($resultado));

$total_streamings = count($xml->VHost->Application);

if($total_streamings > 0) {

for($i=0;$i<$total_streamings;$i++){

if($xml->VHost->Application[$i]->Name == $login) {

$total_espectadores = count($xml->VHost->Application[$i]->ApplicationInstance->Client);

for($ii=0;$ii<$total_espectadores;$ii++){

$status_transmissao = status_aovivo($xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->FlashVersion, $xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress);

if($status_transmissao != "aovivo") {

$ip = $xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress;
$tempo_conectado = seconds2time($xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->TimeRunning);
$player = ($xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->Type == "rtmp") ? "Flash" : "HTML5";

$array_espectadores[] = $ip."|".$tempo_conectado."|".pais_ip($ip,"sigla")."|".pais_ip($ip,"nome")."|".$player."";

}

}

$total_espectadores2 = count($xml->VHost->Application[$i]->ApplicationInstance[1]->Client);

for($iii=0;$iii<$total_espectadores2;$iii++){

$status_transmissao = status_aovivo($xml->VHost->Application[$i]->ApplicationInstance[1]->Client[$iii]->FlashVersion, $xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress);

if($status_transmissao != "aovivo") {

$ip = $xml->VHost->Application[$i]->ApplicationInstance[1]->Client[$iii]->IpAddress;
$tempo_conectado = seconds2time($xml->VHost->Application[$i]->ApplicationInstance[1]->Client[$iii]->TimeRunning);
$player = ($xml->VHost->Application[$i]->ApplicationInstance[1]->Client[$iii]->Type == "rtmp") ? "Flash" : "HTML5";

$array_espectadores[] = $ip."|".$tempo_conectado."|".pais_ip($ip,"sigla")."|".pais_ip($ip,"nome")."|".$player."";

}

}

$total_espectadores3 = count($xml->VHost->Application[$i]->ApplicationInstance[1]->HTTPSession);

for($iiii=0;$iiii<$total_espectadores3;$iiii++){

$ip = $xml->VHost->Application[$i]->ApplicationInstance[1]->HTTPSession[$iiii]->IpAddress;

$array_espectadores[] = $ip;

}

break;

}

}

}

return $array_espectadores;
}

// Função para gravar transmissão ao vivo
function gravar_transmissao($ip,$senha,$login,$arquivo,$acao) {

$url = ($acao == "iniciar") ? "livestreamrecord?app=".$login."&streamname=".$login."&output=/home/streaming/".$login."/".$arquivo."&format=2&recorddata=true&action=startRecording" : "livestreamrecord?app=".$login."&streamname=".$login."&action=stopRecording";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":555/".$url."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERPWD, "admin:".code_decode($senha,"D").""); 
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
ob_start();
$resultado = curl_exec($ch);
ob_get_clean();

if($resultado === false) {
return "erro";
} else {

if(preg_match('/'.$login.'/i',$resultado)) {
return "ok";
} else {
return "erro";
}

}

curl_close($ch);
}

// Função para gerar arquivo de configuração do Ondemand
function gerar_playlist($config) {

$conteudo .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$conteudo .= "<smil title=\"".$config["login"]."\">\n";
$conteudo .= "<head></head>\n";
$conteudo .= "<body>\n";
$conteudo .= "<stream name=\"".$config["login"]."\"></stream>\n\n";

if($config["playlists"]) {

foreach($config["playlists"] as $playlist_config) {

if($playlist_config["total_videos"] > 0) {

$conteudo .= "<playlist name=\"".$playlist_config["playlist"]."\" playOnStream=\"".$config["login"]."\" repeat=\"true\" scheduled=\"".$playlist_config["data_inicio"]."\">\n";

$lista_videos = explode(",",$playlist_config["videos"]);

foreach($lista_videos as $video) {
$video = str_replace("%20"," ",$video);
$conteudo .= "<video length=\"-1\" src=\"mp4:".$video."\" start=\"0\"></video>\n";
}
$conteudo .= "</playlist>\n\n";

}

}

}

$conteudo .= "</body>\n";
$conteudo .= "</smil>\n";

$handle_playlist = fopen("/home/painelvideo/public_html/temp/".$config["login"]."_playlists_agendamentos.smil" ,"w");
fwrite($handle_playlist, $conteudo);
fclose($handle_playlist);

return $config["login"]."_playlists_agendamentos.smil";

}

// Função de monitoramento contra ataques
function monitoramento_ataques() {

$headers = "";
$headers .= 'MIME-Version: 1.0'."\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
$headers .= 'From: Painel de Streaming <cesar@advancehost.com.br>'."\r\n";
$headers .= 'To: cesar@advancehost.com.br'."\r\n";
$headers .= "X-Sender: Painel de Streaming <cesar@advancehost.com.br>\n";
$headers .= 'X-Mailer: PHP/' . phpversion();
$headers .= "X-Priority: 1\n";
$headers .= "Return-Path: cesar@advancehost.com.br\n";

$mensagem = "";
$mensagem .= "==========================================<br>";
$mensagem .= "======== Tentativa de invasão! ========<br>";
$mensagem .= "==========================================<br>";
$mensagem .= "IP: ".$_SERVER["REMOTE_ADDR"]."<br>";
$mensagem .= "Host: ".gethostbyaddr($_SERVER["REMOTE_ADDR"])."<br>";
$mensagem .= "Data: ".date("d/m/Y H:i:s")."<br>";
$mensagem .= "URI: ".$_SERVER['REQUEST_URI']."<br>";
$mensagem .= "==========================================<br>";
$mensagem .= "======== Informações Diversas ========<br>";
$mensagem .= "==========================================<br>";
$mensagem .= "".$_SERVER['HTTP_REFERER']."<br>";
$mensagem .= "".$_SERVER['HTTP_HOST']."<br>";
$mensagem .= "".$_SERVER['HTTP_USER_AGENT']."<br>";
$mensagem .= "".$_SERVER['QUERY_STRING']."<br>";
$mensagem .= "".$_SERVER['REQUEST_METHOD']."<br>";
$mensagem .= "==========================================";

mail("cesar@advancehost.com.br","[Alerta] Tentativa de invasão!",$mensagem,$headers);

}

// Função abreviar o nome do navegador
function formatar_useragent($useragent) {

if(preg_match('/VLC/i',$useragent)) {
return  'VLC';
} elseif(preg_match('/QuickTime/i',$useragent)) {
return  'QuickTime';
} elseif(preg_match('/AND/i',$useragent)) {
return  'Android RTSP';
} elseif(preg_match('/Flash/i',$useragent)) {
return  'Flash';
} elseif(preg_match('/Chrome/i',$useragent)) {
return  'HTML5';
} elseif(preg_match('/Firefox/i',$useragent) || preg_match('/Safari/i',$useragent) || preg_match('/MSIE/i',$useragent)) {
return  'Flash';
} elseif(preg_match('/Sony/i',$useragent)) {
return  'Sony Mobile';
} elseif(preg_match('/LG/i',$useragent)) {
return  'LG Mobile';
} elseif(preg_match('/Samsung/i',$useragent)) {
return  'Samsung Mobile';
} elseif(preg_match('/MPlayer/i',$useragent)) {
return  'MPlayer Linux/Win';
} else {
return 'Outro';
}

}

// Função para inserir elementos em uma array
function array_insert(&$array, $position, $insert)
{
    if (is_int($position)) {
        array_splice($array, $position, 0, $insert);
    } else {
        $pos   = array_search($position, array_keys($array));
        $array = array_merge(
            array_slice($array, 0, $pos),
            $insert,
            array_slice($array, $pos)
        );
    }
}

// Função para transformar várias arrays dentro de uma mesma array em uma só array
function flatten_array($array) {
    if (!is_array($array)) {
        // nothing to do if it's not an array
        return array($array);
    }

    $result = array();
    foreach ($array as $value) {
        // explode the sub-array, and add the parts
        $result = array_merge($result, flatten_array($value));
    }

    return $result;
}

// Calcula a diferença de horas entre 2 time zones
function get_timezone_offset($remote_tz, $origin_tz = null) {
    if($origin_tz === null) {
        if(!is_string($origin_tz = date_default_timezone_get())) {
            return false; // A UTC timestamp was returned -- bail out!
        }
    }
    $origin_dtz = new DateTimeZone($origin_tz);
    $remote_dtz = new DateTimeZone($remote_tz);
    $origin_dt = new DateTime("now", $origin_dtz);
    $remote_dt = new DateTime("now", $remote_dtz);
    $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	
	$offset = $offset/3600;
    return $offset;
}

// Função para formatar datas
function formatar_data($formato, $data, $timezone) {

$formato = (preg_match('/:/i',$data)) ? $formato : str_replace("H:i:s","",$formato);

$offset = get_timezone_offset('America/Sao_Paulo',$timezone);

$nova_data = strtotime ( ''.$offset.' hour' , strtotime ( $data ) ) ;
$nova_data = date ( $formato , $nova_data );

return $nova_data;

}

function isSSL() {

if( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' )
	return true;

if( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
	return true;

return false;
}

function date_diff_minutes( $date ) {

$first  = new DateTime( $date );
$second = new DateTime( "now" );

$diff = $first->diff( $second );

return $diff->format( '%I' );

}

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

/////////////////////////////////////////////
//////////// Funções App Android ////////////
/////////////////////////////////////////////

// Função para formatar o nome da radio retirando acentos e caracteres especiais
function formatar_nome_webtv($nome) {

$characteres = array(
    'S'=>'S', 's'=>'s', 'Ð'=>'Dj','Z'=>'Z', 'z'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
    'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
    'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'f'=>'f', '¹'=> '', '²'=> '', '&'=> 'e',
	'³'=> '', '£'=> '', '$'=> '', '%'=> '', '¨'=> '', '§'=> '', 'º'=> '', 'ª'=> '', '©'=> '', 'Ã£'=> '',
	'('=> '', ')'=> '', "'"=> '', '@'=> '', '='=> '', ':'=> '', '!'=> '', '?'=> '', '...'=> '', '®'=> '',
	'/'=> '', '´'=> '', '+'=> '', '*'=> '', '['=> '', ']'=> ''
);

return strtr($nome, $characteres);

}

// Função para formatar o nome do app para o google play retirando acentos e caracteres especiais
function nome_app_play($texto) {

$characteres = array(
    'S'=>'S', 's'=>'s', 'Ð'=>'Dj','Z'=>'Z', 'z'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
    'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
    'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'f'=>'f', '¹'=> '', '²'=> '', '&'=> 'e',
	'³'=> '', '£'=> '', '$'=> '', '%'=> '', '¨'=> '', '§'=> '', 'º'=> '', 'ª'=> '', '©'=> '', 'Ã£'=> '',
	'('=> '', ')'=> '', "'"=> '', '@'=> '', '='=> '', ':'=> '', '!'=> '', '?'=> '', '...'=> '', ' '=> '',
	'-'=> '', '^'=> '', '~'=> '', '.'=> '', '|'=> '', ','=> '', '<'=> '', '>'=> '', '{'=> '', '}'=> '',
	'®'=> '', '/'=> '', '´'=> '', '+'=> '', '*'=> '', '['=> '', ']'=> ''
);

return strtolower(strtr($texto, $characteres));

}

// Função para formatar o nome do apk do app retirando acentos e caracteres especiais
function nome_app_apk($texto) {

$characteres = array(
    'S'=>'S', 's'=>'s', 'Ð'=>'Dj','Z'=>'Z', 'z'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
    'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
    'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'f'=>'f', '¹'=> '', '²'=> '', '&'=> 'e',
	'³'=> '', '£'=> '', '$'=> '', '%'=> '', '¨'=> '', '§'=> '', 'º'=> '', 'ª'=> '', '©'=> '', 'Ã£'=> '',
	'('=> '', ')'=> '', "'"=> '', '@'=> '', '='=> '', ':'=> '', '!'=> '', '?'=> '', '...'=> '', ' '=> '',
	'-'=> '', '^'=> '', '~'=> '', '.'=> '', '|'=> '', ','=> '', '<'=> '', '>'=> '', '{'=> '', '}'=> '',
	' '=> '', '®'=> '', '/'=> '', '´'=> '', '+'=> '', '*'=> '', '['=> '', ']'=> ''
);

return strtr($texto, $characteres);

}

// Função para copiar o source para o novo app
function copiar_source($DirFont, $DirDest) {
    
    mkdir($DirDest);
    if ($dd = opendir($DirFont)) {
        while (false !== ($Arq = readdir($dd))) {
            if($Arq != "." && $Arq != ".."){
                $PathIn = "$DirFont/$Arq";
                $PathOut = "$DirDest/$Arq";
                if(is_dir($PathIn)){
                    copiar_source($PathIn, $PathOut);
					chmod($PathOut,0777);
                }elseif(is_file($PathIn)){
                    copy($PathIn, $PathOut);
					chmod($PathOut,0777);
                }
            }
        }
        closedir($dd);
	}

}

// Função para criar arquivos de configuração do app
function criar_arquivo_config($arquivo,$conteudo) {

$fd = fopen ($arquivo, "w");
fputs($fd, $conteudo);
fclose($fd);

}

// Função para carregar todos os arquivos e pastas de um diretorio
function browse($dir) {
global $filenames;
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && is_file($dir.'/'.$file)) {
                $filenames[] = $dir.'/'.$file;
            }
            else if ($file != "." && $file != ".." && is_dir($dir.'/'.$file)) {
                browse($dir.'/'.$file);
            }
        }
        closedir($handle);
    }
    return $filenames;
}

// Função para substituir uma string dentro de um arquivo de texto
function replace($arquivo,$string_atual,$string_nova) {

//$str = implode("\n",file($arquivo));
//$fp = fopen($arquivo,'w');
//$str = str_replace($string_atual,$string_nova,$str);

//fwrite($fp,$str,strlen($str));

$str = file_get_contents($arquivo);
$str = str_replace($string_atual,$string_nova,$str);
file_put_contents($arquivo,$str);

}

// Função para remover o source do novo app
function remover_source_app($Dir){
    
    if ($dd = @opendir($Dir)) {
        while (false !== ($Arq = @readdir($dd))) {
            if($Arq != "." && $Arq != ".."){
                $Path = "$Dir/$Arq";
                if(is_dir($Path)){
                    remover_source_app($Path);
                }elseif(is_file($Path)){
                    @unlink($Path);
                }
            }
        }
        @closedir($dd);
    }
    @rmdir($Dir);
}

// Função para mudar a permissão de todos os arquivos e pasta no source do app
function mudar_permissao($Dir){

    if ($dd = opendir($Dir)) {
        while (false !== ($Arq = readdir($dd))) {
            if($Arq != "." && $Arq != ".."){
                $Path = "$Dir/$Arq";
                @chmod($Path,0777);
            }
        }
        closedir($dd);
    }

}

function youtube_parser($url) {

preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);

return $matches[1];
}
?>