<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

if(isset($_POST["alterar"])) {

mysql_query("Update video.streamings set pagina_inicial = '".$_POST["pagina_inicial"]."', idioma_painel = '".$_POST["idioma_painel"]."', timezone = '".$_POST["timezone"]."', formato_data = '".$_POST["formato_data"]."', exibir_atalhos = '".$_POST["exibir_atalhos"]."', aparencia_exibir_stats_espectadores = '".$_POST["aparencia_exibir_stats_espectadores"]."', aparencia_exibir_stats_ftp = '".$_POST["aparencia_exibir_stats_ftp"]."' where codigo = '".$dados_stm["codigo"]."'");

// Cadastra os atalhos
if($_POST["atalhos"]) {

// Remove os atalhos atuais
mysql_query("DELETE FROM video.atalhos where codigo_stm = '".$dados_stm["codigo"]."'");

foreach($_POST["atalhos"] as $ordem => $atalho) {

list($menu, $idioma) = explode("|", $atalho);

mysql_query("INSERT INTO video.atalhos (codigo_stm,menu,lang,ordem) VALUES ('".$dados_stm["codigo"]."','".$menu."','".$idioma."','".$ordem."')") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());

}

}

if(!mysql_error()) {

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_config_painel_resultado_ok']."","ok");

} else {

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_config_painel_resultado_erro']." ".mysql_error()."","erro");

}

die("<script>parent.location.reload();</script><a href='javascript:parent.location.reload()'>[Recarregar/Reload]</a>");

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
<script type="text/javascript" src="inc/selectbox.js"></script>
<script type="text/javascript" src="inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };

function selectAll(campo) 
    { 
        selectBox = document.getElementById(campo);

        for (var i = 0; i < selectBox.options.length; i++) 
        { 
             selectBox.options[i].selected = true; 
        } 
}
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
<form method="post" action="/configuracoes-painel" style="padding:0px; margin:0px" name="config_painel" onsubmit="selectAll('atalhos');">
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
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_painel_pagina_inicial']; ?></td>
            <td width="540" align="left">
            <select name="pagina_inicial" class="input" id="pagina_inicial" style="width:255px;">
          		<option value="/informacoes"<?php if($dados_stm["pagina_inicial"] == "/informacoes") { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_config_painel_pagina_inicial_info']; ?></option>
          		<option value="/espectadores-conectados"<?php if($dados_stm["pagina_inicial"] == "/espectadores-conectados") { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_config_painel_pagina_inicial_espectadores']; ?></option>
         	</select>            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_painel_idioma']; ?></td>
            <td align="left">
            <select name="idioma_painel" class="input" id="idioma_painel" style="width:255px;">
          		<option value="pt-br"<?php if($dados_stm["idioma_painel"] == "pt-br") { echo ' selected="selected"'; } ?>>Portugu�s(Brasil)</option>
		  		<option value="es"<?php if($dados_stm["idioma_painel"] == "es") { echo ' selected="selected"'; } ?>>Espa�ol</option>
          		<option value="en-us"<?php if($dados_stm["idioma_painel"] == "en-us") { echo ' selected="selected"'; } ?>>English(USA)</option>		  
         	</select>         </td>
         </tr>
         <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_painel_time_zone']; ?></td>
            <td align="left">
            <select name="timezone" id="timezone" style="width:255px;">
<optgroup label="Afeganist�o (AF)">
<option <?php if($dados_stm["timezone"] == "Asia/Kabul") { echo 'selected="selected"'; } ?> value="Asia/Kabul">Cabul</option>
</optgroup>
<optgroup label="�frica do Sul (ZA)">
<option <?php if($dados_stm["timezone"] == "Africa/Johannesburg") { echo 'selected="selected"'; } ?> value="Africa/Johannesburg">Joanesburgo</option>
</optgroup>
<optgroup label="Alb�nia (AL)">
<option <?php if($dados_stm["timezone"] == "Europe/Tirane") { echo 'selected="selected"'; } ?> value="Europe/Tirane">Tirana</option>
</optgroup>
<optgroup label="Alemanha (DE)">
<option <?php if($dados_stm["timezone"] == "Europe/Berlin") { echo 'selected="selected"'; } ?> value="Europe/Berlin">Berlim</option>
<option <?php if($dados_stm["timezone"] == "Europe/Busingen") { echo 'selected="selected"'; } ?> value="Europe/Busingen">B�singen</option>
</optgroup>
<optgroup label="Andorra (AD)">
<option <?php if($dados_stm["timezone"] == "Europe/Andorra") { echo 'selected="selected"'; } ?> value="Europe/Andorra">Andorra</option>
</optgroup>
<optgroup label="Angola (AO)">
<option <?php if($dados_stm["timezone"] == "Africa/Luanda") { echo 'selected="selected"'; } ?> value="Africa/Luanda">Luanda</option>
</optgroup>
<optgroup label="Anguilla (AI)">
<option <?php if($dados_stm["timezone"] == "America/Anguilla") { echo 'selected="selected"'; } ?> value="America/Anguilla">Anguilla</option>
</optgroup>
<optgroup label="Ant�rtica (AQ)">
<option <?php if($dados_stm["timezone"] == "Antarctica/Casey") { echo 'selected="selected"'; } ?> value="Antarctica/Casey">Casey</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Davis") { echo 'selected="selected"'; } ?> value="Antarctica/Davis">Davis</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/DumontDUrville") { echo 'selected="selected"'; } ?> value="Antarctica/DumontDUrville">Dumont D'Urville</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Mawson") { echo 'selected="selected"'; } ?> value="Antarctica/Mawson">Mawson</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/McMurdo") { echo 'selected="selected"'; } ?> value="Antarctica/McMurdo">McMurdo</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Palmer") { echo 'selected="selected"'; } ?> value="Antarctica/Palmer">Palmer</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/South_Pole") { echo 'selected="selected"'; } ?> value="Antarctica/South_Pole">P�lo Sul</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Rothera") { echo 'selected="selected"'; } ?> value="Antarctica/Rothera">Rothera</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Syowa") { echo 'selected="selected"'; } ?> value="Antarctica/Syowa">Syowa</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Vostok") { echo 'selected="selected"'; } ?> value="Antarctica/Vostok">Vostok</option>
</optgroup>
<optgroup label="Ant�gua e Barbuda (AG)">
<option <?php if($dados_stm["timezone"] == "America/Antigua") { echo 'selected="selected"'; } ?> value="America/Antigua">Antigua</option>
</optgroup>
<optgroup label="Antilhas Neerlandesas (AN)"></optgroup>
<optgroup label="Ar�bia Saudita (SA)">
<option <?php if($dados_stm["timezone"] == "Asia/Riyadh") { echo 'selected="selected"'; } ?> value="Asia/Riyadh">Riade</option>
</optgroup>
<optgroup label="Arg�lia (DZ)">
<option <?php if($dados_stm["timezone"] == "Africa/Algiers") { echo 'selected="selected"'; } ?> value="Africa/Algiers">Alg�ria</option>
</optgroup>
<optgroup label="Argentina (AR)">
<option <?php if($dados_stm["timezone"] == "America/Argentina/Buenos_Aires") { echo 'selected="selected"'; } ?> value="America/Argentina/Buenos_Aires">Buenos Aires</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Catamarca") { echo 'selected="selected"'; } ?> value="America/Argentina/Catamarca">Catamarca</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Cordoba") { echo 'selected="selected"'; } ?> value="America/Argentina/Cordoba">C�rdoba</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Jujuy") { echo 'selected="selected"'; } ?> value="America/Argentina/Jujuy">Jujuy</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/La_Rioja") { echo 'selected="selected"'; } ?> value="America/Argentina/La_Rioja">La Rioja</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Mendoza") { echo 'selected="selected"'; } ?> value="America/Argentina/Mendoza">Mendoza</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Rio_Gallegos") { echo 'selected="selected"'; } ?> value="America/Argentina/Rio_Gallegos">Rio Gallegos</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Salta") { echo 'selected="selected"'; } ?> value="America/Argentina/Salta">Salta</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/San_Juan") { echo 'selected="selected"'; } ?> value="America/Argentina/San_Juan">San Juan</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/San_Luis") { echo 'selected="selected"'; } ?> value="America/Argentina/San_Luis">San Lu�s</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Tucuman") { echo 'selected="selected"'; } ?> value="America/Argentina/Tucuman">Tucum�n</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Ushuaia") { echo 'selected="selected"'; } ?> value="America/Argentina/Ushuaia">Ushuaia</option>
</optgroup>
<optgroup label="Arm�nia (AM)">
<option <?php if($dados_stm["timezone"] == "Asia/Yerevan") { echo 'selected="selected"'; } ?> value="Asia/Yerevan">Yerevan</option>
</optgroup>
<optgroup label="Aruba (AW)">
<option <?php if($dados_stm["timezone"] == "America/Aruba") { echo 'selected="selected"'; } ?> value="America/Aruba">Aruba</option>
</optgroup>
<optgroup label="Austr�lia (AU)">
<option <?php if($dados_stm["timezone"] == "Australia/Adelaide") { echo 'selected="selected"'; } ?> value="Australia/Adelaide">Adelaide</option>
<option <?php if($dados_stm["timezone"] == "Australia/Brisbane") { echo 'selected="selected"'; } ?> value="Australia/Brisbane">Brisbane</option>
<option <?php if($dados_stm["timezone"] == "Australia/Broken_Hill") { echo 'selected="selected"'; } ?> value="Australia/Broken_Hill">Broken Hill</option>
<option <?php if($dados_stm["timezone"] == "Australia/Currie") { echo 'selected="selected"'; } ?> value="Australia/Currie">Currie</option>
<option <?php if($dados_stm["timezone"] == "Australia/Darwin") { echo 'selected="selected"'; } ?> value="Australia/Darwin">Darwin</option>
<option <?php if($dados_stm["timezone"] == "Australia/Eucla") { echo 'selected="selected"'; } ?> value="Australia/Eucla">Eucla</option>
<option <?php if($dados_stm["timezone"] == "Australia/Hobart") { echo 'selected="selected"'; } ?> value="Australia/Hobart">Hobart</option>
<option <?php if($dados_stm["timezone"] == "Australia/Lindeman") { echo 'selected="selected"'; } ?> value="Australia/Lindeman">Lindeman</option>
<option <?php if($dados_stm["timezone"] == "Australia/Lord_Howe") { echo 'selected="selected"'; } ?> value="Australia/Lord_Howe">Lord Howe</option>
<option <?php if($dados_stm["timezone"] == "Australia/Macquarie") { echo 'selected="selected"'; } ?> value="Antarctica/Macquarie">Macquarie</option>
<option <?php if($dados_stm["timezone"] == "Australia/Melbourne") { echo 'selected="selected"'; } ?> value="Australia/Melbourne">Melbourne</option>
<option <?php if($dados_stm["timezone"] == "Australia/Perth") { echo 'selected="selected"'; } ?> value="Australia/Perth">Perth</option>
<option <?php if($dados_stm["timezone"] == "Australia/Sydney") { echo 'selected="selected"'; } ?> value="Australia/Sydney">Sydney</option>
</optgroup>
<optgroup label="�ustria (AT)">
<option <?php if($dados_stm["timezone"] == "Europe/Vienna") { echo 'selected="selected"'; } ?> value="Europe/Vienna">Viena</option>
</optgroup>
<optgroup label="Azerbaij�o (AZ)">
<option <?php if($dados_stm["timezone"] == "Asia/Baku") { echo 'selected="selected"'; } ?> value="Asia/Baku">Baku</option>
</optgroup>
<optgroup label="Bahamas (BS)">
<option <?php if($dados_stm["timezone"] == "America/Nassau") { echo 'selected="selected"'; } ?> value="America/Nassau">Nassau</option>
</optgroup>
<optgroup label="Bahrein (BH)">
<option <?php if($dados_stm["timezone"] == "Asia/Bahrain") { echo 'selected="selected"'; } ?> value="Asia/Bahrain">Bahrain</option>
</optgroup>
<optgroup label="Bangladesh (BD)">
<option <?php if($dados_stm["timezone"] == "Asia/Dhaka") { echo 'selected="selected"'; } ?> value="Asia/Dhaka">Dhaka</option>
</optgroup>
<optgroup label="Barbados (BB)">
<option <?php if($dados_stm["timezone"] == "America/Barbados") { echo 'selected="selected"'; } ?> value="America/Barbados">Barbados</option>
</optgroup>
<optgroup label="B�lgica (BE)">
<option <?php if($dados_stm["timezone"] == "Europe/Brussels") { echo 'selected="selected"'; } ?> value="Europe/Brussels">Bruxelas</option>
</optgroup>
<optgroup label="Belize (BZ)">
<option <?php if($dados_stm["timezone"] == "America/Belize") { echo 'selected="selected"'; } ?> value="America/Belize">Belize</option>
</optgroup>
<optgroup label="Benim (BJ)">
<option <?php if($dados_stm["timezone"] == "Africa/Porto-Novo") { echo 'selected="selected"'; } ?> value="Africa/Porto-Novo">Porto-Novo</option>
</optgroup>
<optgroup label="Bermudas (BM)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Bermuda") { echo 'selected="selected"'; } ?> value="Atlantic/Bermuda">Bermuda</option>
</optgroup>
<optgroup label="Bielorr�ssia (BY)">
<option <?php if($dados_stm["timezone"] == "Europe/Minsk") { echo 'selected="selected"'; } ?> value="Europe/Minsk">Minsk</option>
</optgroup>
<optgroup label="Bol�via (BO)">
<option <?php if($dados_stm["timezone"] == "America/La_Paz") { echo 'selected="selected"'; } ?> value="America/La_Paz">La Paz</option>
</optgroup>
<optgroup label="Bonaire, Santo Eust�quio e Saba (BQ)">
<option <?php if($dados_stm["timezone"] == "America/Kralendijk") { echo 'selected="selected"'; } ?> value="America/Kralendijk">Kralendijk</option>
</optgroup>
<optgroup label="B�snia e Herzegovina (BA)">
<option <?php if($dados_stm["timezone"] == "Europe/Sarajevo") { echo 'selected="selected"'; } ?> value="Europe/Sarajevo">Sarajevo</option>
</optgroup>
<optgroup label="Botswana (BW)">
<option <?php if($dados_stm["timezone"] == "Africa/Gaborone") { echo 'selected="selected"'; } ?> value="Africa/Gaborone">Gaborone</option>
</optgroup>
<optgroup label="Brasil (BR)">
<option <?php if($dados_stm["timezone"] == "America/Araguaina") { echo 'selected="selected"'; } ?> value="America/Araguaina">Aragua�na</option>
<option <?php if($dados_stm["timezone"] == "America/Bahia") { echo 'selected="selected"'; } ?> value="America/Bahia">Bahia</option>
<option <?php if($dados_stm["timezone"] == "America/Belem") { echo 'selected="selected"'; } ?> value="America/Belem">Bel�m</option>
<option <?php if($dados_stm["timezone"] == "America/Boa_Vista") { echo 'selected="selected"'; } ?> value="America/Boa_Vista">Boa Vista</option>
<option <?php if($dados_stm["timezone"] == "America/Campo_Grande") { echo 'selected="selected"'; } ?> value="America/Campo_Grande">Campo Grande</option>
<option <?php if($dados_stm["timezone"] == "America/Cuiaba") { echo 'selected="selected"'; } ?> value="America/Cuiaba">Cuiab�</option>
<option <?php if($dados_stm["timezone"] == "America/Eirunepe") { echo 'selected="selected"'; } ?> value="America/Eirunepe">Eirunep�</option>
<option <?php if($dados_stm["timezone"] == "America/Noronha") { echo 'selected="selected"'; } ?> value="America/Noronha">Fernando de Noronha</option>
<option <?php if($dados_stm["timezone"] == "America/Fortaleza") { echo 'selected="selected"'; } ?> value="America/Fortaleza">Fortaleza</option>
<option <?php if($dados_stm["timezone"] == "America/Maceio") { echo 'selected="selected"'; } ?> value="America/Maceio">Macei�</option>
<option <?php if($dados_stm["timezone"] == "America/Manaus") { echo 'selected="selected"'; } ?> value="America/Manaus">Manaus</option>
<option <?php if($dados_stm["timezone"] == "America/Porto_Velho") { echo 'selected="selected"'; } ?> value="America/Porto_Velho">Porto Velho</option>
<option <?php if($dados_stm["timezone"] == "America/Recife") { echo 'selected="selected"'; } ?> value="America/Recife">Recife</option>
<option <?php if($dados_stm["timezone"] == "America/Rio_Branco") { echo 'selected="selected"'; } ?> value="America/Rio_Branco">Rio Branco</option>
<option <?php if($dados_stm["timezone"] == "America/Santarem") { echo 'selected="selected"'; } ?> value="America/Santarem">Santar�m</option>
<option <?php if($dados_stm["timezone"] == "America/Sao_Paulo") { echo 'selected="selected"'; } ?> value="America/Sao_Paulo">S�o Paulo (Hor�rio de Brasilia)</option>
</optgroup>
<optgroup label="Brunei (BN)">
<option <?php if($dados_stm["timezone"] == "Asia/Brunei") { echo 'selected="selected"'; } ?> value="Asia/Brunei">Brunei</option>
</optgroup>
<optgroup label="Bulg�ria (BG)">
<option <?php if($dados_stm["timezone"] == "Europe/Sofia") { echo 'selected="selected"'; } ?> value="Europe/Sofia">Sofia</option>
</optgroup>
<optgroup label="Burkina Faso (BF)">
<option <?php if($dados_stm["timezone"] == "Africa/Ouagadougou") { echo 'selected="selected"'; } ?> value="Africa/Ouagadougou">Ouagadougou</option>
</optgroup>
<optgroup label="Bur�ndi (BI)">
<option <?php if($dados_stm["timezone"] == "Africa/Bujumbura") { echo 'selected="selected"'; } ?> value="Africa/Bujumbura">Bujumbura</option>
</optgroup>
<optgroup label="But�o (BT)">
<option <?php if($dados_stm["timezone"] == "Asia/Thimphu") { echo 'selected="selected"'; } ?> value="Asia/Thimphu">Thimphu</option>
</optgroup>
<optgroup label="Cabo Verde (CV)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Cape_Verde") { echo 'selected="selected"'; } ?> value="Atlantic/Cape_Verde">Cabo Verde</option>
</optgroup>
<optgroup label="Camar�es (CM)">
<option <?php if($dados_stm["timezone"] == "Africa/Douala") { echo 'selected="selected"'; } ?> value="Africa/Douala">Douala</option>
</optgroup>
<optgroup label="Camboja (KH)">
<option <?php if($dados_stm["timezone"] == "Asia/Phnom_Penh") { echo 'selected="selected"'; } ?> value="Asia/Phnom_Penh">Phnom Penh</option>
</optgroup>
<optgroup label="Canad� (CA)">
<option <?php if($dados_stm["timezone"] == "America/Atikokan") { echo 'selected="selected"'; } ?> value="America/Atikokan">Atikokan</option>
<option <?php if($dados_stm["timezone"] == "America/Blanc-Sablon") { echo 'selected="selected"'; } ?> value="America/Blanc-Sablon">Blanc-Sablon</option>
<option <?php if($dados_stm["timezone"] == "America/Cambridge_Bay") { echo 'selected="selected"'; } ?> value="America/Cambridge_Bay">Cambridge Bay</option>
<option <?php if($dados_stm["timezone"] == "America/Creston") { echo 'selected="selected"'; } ?> value="America/Creston">Creston</option>
<option <?php if($dados_stm["timezone"] == "America/Dawson") { echo 'selected="selected"'; } ?> value="America/Dawson">Dawson</option>
<option <?php if($dados_stm["timezone"] == "America/Dawson_Creek") { echo 'selected="selected"'; } ?> value="America/Dawson_Creek">Dawson Creek</option>
<option <?php if($dados_stm["timezone"] == "America/Edmonton") { echo 'selected="selected"'; } ?> value="America/Edmonton">Edmonton</option>
<option <?php if($dados_stm["timezone"] == "America/Glace_Bay") { echo 'selected="selected"'; } ?> value="America/Glace_Bay">Glace Bay</option>
<option <?php if($dados_stm["timezone"] == "America/Goose_Bay") { echo 'selected="selected"'; } ?> value="America/Goose_Bay">Goose Bay</option>
<option <?php if($dados_stm["timezone"] == "America/Halifax") { echo 'selected="selected"'; } ?> value="America/Halifax">Halifax</option>
<option <?php if($dados_stm["timezone"] == "America/Inuvik") { echo 'selected="selected"'; } ?> value="America/Inuvik">Inuvik</option>
<option <?php if($dados_stm["timezone"] == "America/Iqaluit") { echo 'selected="selected"'; } ?> value="America/Iqaluit">Iqaluit</option>
<option <?php if($dados_stm["timezone"] == "America/Moncton") { echo 'selected="selected"'; } ?> value="America/Moncton">Moncton</option>
<option <?php if($dados_stm["timezone"] == "America/Montreal") { echo 'selected="selected"'; } ?> value="America/Montreal">Montreal</option>
<option <?php if($dados_stm["timezone"] == "America/Nipigon") { echo 'selected="selected"'; } ?> value="America/Nipigon">Nipigon</option>
<option <?php if($dados_stm["timezone"] == "America/Pangnirtung") { echo 'selected="selected"'; } ?> value="America/Pangnirtung">Pangnirtung</option>
<option <?php if($dados_stm["timezone"] == "America/Rainy_River") { echo 'selected="selected"'; } ?> value="America/Rainy_River">Rainy River</option>
<option <?php if($dados_stm["timezone"] == "America/Rankin_Inlet") { echo 'selected="selected"'; } ?> value="America/Rankin_Inlet">Rankin Inlet</option>
<option <?php if($dados_stm["timezone"] == "America/Regina") { echo 'selected="selected"'; } ?> value="America/Regina">Regina</option>
<option <?php if($dados_stm["timezone"] == "America/Resolute") { echo 'selected="selected"'; } ?> value="America/Resolute">Resolute</option>
<option <?php if($dados_stm["timezone"] == "America/St_Johns") { echo 'selected="selected"'; } ?> value="America/St_Johns">St Johns</option>
<option <?php if($dados_stm["timezone"] == "America/Swift_Current") { echo 'selected="selected"'; } ?> value="America/Swift_Current">Swift Current</option>
<option <?php if($dados_stm["timezone"] == "America/Thunder_Bay") { echo 'selected="selected"'; } ?> value="America/Thunder_Bay">Thunder Bay</option>
<option <?php if($dados_stm["timezone"] == "America/Toronto") { echo 'selected="selected"'; } ?> value="America/Toronto">Toronto</option>
<option <?php if($dados_stm["timezone"] == "America/Vancouver") { echo 'selected="selected"'; } ?> value="America/Vancouver">Vancouver</option>
<option <?php if($dados_stm["timezone"] == "America/Whitehorse") { echo 'selected="selected"'; } ?> value="America/Whitehorse">Whitehorse</option>
<option <?php if($dados_stm["timezone"] == "America/Winnipeg") { echo 'selected="selected"'; } ?> value="America/Winnipeg">Winnipeg</option>
<option <?php if($dados_stm["timezone"] == "America/Yellowknife") { echo 'selected="selected"'; } ?> value="America/Yellowknife">Yellowknife</option>
</optgroup>
<optgroup label="Catar (QA)">
<option <?php if($dados_stm["timezone"] == "Asia/Qatar") { echo 'selected="selected"'; } ?> value="Asia/Qatar">Qatar</option>
</optgroup>
<optgroup label="Cazaquist�o (KZ)">
<option <?php if($dados_stm["timezone"] == "Asia/Almaty") { echo 'selected="selected"'; } ?> value="Asia/Almaty">Almaty</option>
<option <?php if($dados_stm["timezone"] == "Asia/Aqtau") { echo 'selected="selected"'; } ?> value="Asia/Aqtau">Aqtau</option>
<option <?php if($dados_stm["timezone"] == "Asia/Aqtobe") { echo 'selected="selected"'; } ?> value="Asia/Aqtobe">Aqtobe</option>
<option <?php if($dados_stm["timezone"] == "Asia/Oral") { echo 'selected="selected"'; } ?> value="Asia/Oral">Oral</option>
<option <?php if($dados_stm["timezone"] == "Asia/Qyzylorda") { echo 'selected="selected"'; } ?> value="Asia/Qyzylorda">Qyzylorda</option>
</optgroup>
<optgroup label="Chade (TD)">
<option <?php if($dados_stm["timezone"] == "Africa/Ndjamena") { echo 'selected="selected"'; } ?> value="Africa/Ndjamena">Ndjamena</option>
</optgroup>
<optgroup label="Chile (CL)">
<option <?php if($dados_stm["timezone"] == "Pacific/Easter") { echo 'selected="selected"'; } ?> value="Pacific/Easter">P�scoa</option>
<option <?php if($dados_stm["timezone"] == "America/Santiago") { echo 'selected="selected"'; } ?> value="America/Santiago">Santiago</option>
</optgroup>
<optgroup label="China (CN)">
<option <?php if($dados_stm["timezone"] == "Asia/Chongqing") { echo 'selected="selected"'; } ?> value="Asia/Chongqing">Chongqing</option>
<option <?php if($dados_stm["timezone"] == "Asia/Harbin") { echo 'selected="selected"'; } ?> value="Asia/Harbin">Harbin</option>
<option <?php if($dados_stm["timezone"] == "Asia/Kashgar") { echo 'selected="selected"'; } ?> value="Asia/Kashgar">Kashgar</option>
<option <?php if($dados_stm["timezone"] == "Asia/Urumqi") { echo 'selected="selected"'; } ?> value="Asia/Urumqi">Urumqi</option>
<option <?php if($dados_stm["timezone"] == "Asia/Shanghai") { echo 'selected="selected"'; } ?> value="Asia/Shanghai">Xangai</option>
</optgroup>
<optgroup label="Chipre (CY)">
<option <?php if($dados_stm["timezone"] == "Asia/Nicosia") { echo 'selected="selected"'; } ?> value="Asia/Nicosia">Nicosia</option>
</optgroup>
<optgroup label="Coletividade de S�o Martinho (MF)">
<option <?php if($dados_stm["timezone"] == "America/Marigot") { echo 'selected="selected"'; } ?> value="America/Marigot">Marigot</option>
</optgroup>
<optgroup label="Col�mbia (CO)">
<option <?php if($dados_stm["timezone"] == "America/Bogota") { echo 'selected="selected"'; } ?> value="America/Bogota">Bogot�</option>
</optgroup>
<optgroup label="Comores (KM)">
<option <?php if($dados_stm["timezone"] == "Indian/Comoro") { echo 'selected="selected"'; } ?> value="Indian/Comoro">Comoro</option>
</optgroup>
<optgroup label="Coreia do Norte (KP)">
<option <?php if($dados_stm["timezone"] == "Asia/Pyongyang") { echo 'selected="selected"'; } ?> value="Asia/Pyongyang">Pyongyang</option>
</optgroup>
<optgroup label="Coreia do Sul (KR)">
<option <?php if($dados_stm["timezone"] == "Asia/Seoul") { echo 'selected="selected"'; } ?> value="Asia/Seoul">Seoul</option>
</optgroup>
<optgroup label="Costa do Marfim (CI)">
<option <?php if($dados_stm["timezone"] == "Africa/Abidjan") { echo 'selected="selected"'; } ?> value="Africa/Abidjan">Abidjan</option>
</optgroup>
<optgroup label="Costa Rica (CR)">
<option <?php if($dados_stm["timezone"] == "America/Costa_Rica") { echo 'selected="selected"'; } ?> value="America/Costa_Rica">Costa Rica</option>
</optgroup>
<optgroup label="Cro�cia (HR)">
<option <?php if($dados_stm["timezone"] == "Europe/Zagreb") { echo 'selected="selected"'; } ?> value="Europe/Zagreb">Zagreb</option>
</optgroup>
<optgroup label="Cuba (CU)">
<option <?php if($dados_stm["timezone"] == "America/Havana") { echo 'selected="selected"'; } ?> value="America/Havana">Havana</option>
</optgroup>
<optgroup label="Cura�ao (CW)">
<option <?php if($dados_stm["timezone"] == "America/Curacao") { echo 'selected="selected"'; } ?> value="America/Curacao">Cura�ao</option>
</optgroup>
<optgroup label="Dinamarca (DK)">
<option <?php if($dados_stm["timezone"] == "Europe/Copenhagen") { echo 'selected="selected"'; } ?> value="Europe/Copenhagen">Copenhaga</option>
</optgroup>
<optgroup label="Dom�nica (DM)">
<option <?php if($dados_stm["timezone"] == "America/Dominica") { echo 'selected="selected"'; } ?> value="America/Dominica">Dominica</option>
</optgroup>
<optgroup label="Egito (EG)">
<option <?php if($dados_stm["timezone"] == "Africa/Cairo") { echo 'selected="selected"'; } ?> value="Africa/Cairo">Cairo</option>
</optgroup>
<optgroup label="El Salvador (SV)">
<option <?php if($dados_stm["timezone"] == "America/El_Salvador") { echo 'selected="selected"'; } ?> value="America/El_Salvador">El Salvador</option>
</optgroup>
<optgroup label="Emirados �rabes Unidos (AE)">
<option <?php if($dados_stm["timezone"] == "Asia/Dubai") { echo 'selected="selected"'; } ?> value="Asia/Dubai">Dubai</option>
</optgroup>
<optgroup label="Equador (EC)">
<option <?php if($dados_stm["timezone"] == "Pacific/Galapagos") { echo 'selected="selected"'; } ?> value="Pacific/Galapagos">Gal�pagos</option>
<option <?php if($dados_stm["timezone"] == "America/Guayaquil") { echo 'selected="selected"'; } ?> value="America/Guayaquil">Guayaquil</option>
</optgroup>
<optgroup label="Eritreia (ER)">
<option <?php if($dados_stm["timezone"] == "Africa/Asmara") { echo 'selected="selected"'; } ?> value="Africa/Asmara">Asmara</option>
</optgroup>
<optgroup label="Eslov�quia (SK)">
<option <?php if($dados_stm["timezone"] == "Europe/Bratislava") { echo 'selected="selected"'; } ?> value="Europe/Bratislava">Bratislava</option>
</optgroup>
<optgroup label="Eslov�nia (SI)">
<option <?php if($dados_stm["timezone"] == "Europe/Ljubljana") { echo 'selected="selected"'; } ?> value="Europe/Ljubljana">Ljubljana</option>
</optgroup>
<optgroup label="Espanha (ES)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Canary") { echo 'selected="selected"'; } ?> value="Atlantic/Canary">Can�rias</option>
<option <?php if($dados_stm["timezone"] == "Africa/Ceuta") { echo 'selected="selected"'; } ?> value="Africa/Ceuta">Ceuta</option>
<option <?php if($dados_stm["timezone"] == "Europe/Madrid") { echo 'selected="selected"'; } ?> value="Europe/Madrid">Madrid</option>
</optgroup>
<optgroup label="Estados Unidos (US)">
<option <?php if($dados_stm["timezone"] == "America/Adak") { echo 'selected="selected"'; } ?> value="America/Adak">Adak</option>
<option <?php if($dados_stm["timezone"] == "America/Anchorage") { echo 'selected="selected"'; } ?> value="America/Anchorage">Anchorage</option>
<option <?php if($dados_stm["timezone"] == "America/Boise") { echo 'selected="selected"'; } ?> value="America/Boise">Boise</option>
<option <?php if($dados_stm["timezone"] == "America/Chicago") { echo 'selected="selected"'; } ?> value="America/Chicago">Chicago</option>
<option <?php if($dados_stm["timezone"] == "America/North_Dakota/Beulah") { echo 'selected="selected"'; } ?> value="America/North_Dakota/Beulah">Dakota do Norte - Beulah</option>
<option <?php if($dados_stm["timezone"] == "America/North_Dakota/Center") { echo 'selected="selected"'; } ?> value="America/North_Dakota/Center">Dakota do Norte - Center</option>
<option <?php if($dados_stm["timezone"] == "America/North_Dakota/New_Salem") { echo 'selected="selected"'; } ?> value="America/North_Dakota/New_Salem">Dakota do Norte - New Salem</option>
<option <?php if($dados_stm["timezone"] == "America/Denver") { echo 'selected="selected"'; } ?> value="America/Denver">Denver</option>
<option <?php if($dados_stm["timezone"] == "America/Detroit") { echo 'selected="selected"'; } ?> value="America/Detroit">Detroit</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Honolulu") { echo 'selected="selected"'; } ?> value="Pacific/Honolulu">Honolulu</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Indianapolis") { echo 'selected="selected"'; } ?> value="America/Indiana/Indianapolis">Indiana - Indian�polis</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Knox") { echo 'selected="selected"'; } ?> value="America/Indiana/Knox">Indiana - Knox</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Marengo") { echo 'selected="selected"'; } ?> value="America/Indiana/Marengo">Indiana - Marengo</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Petersburg") { echo 'selected="selected"'; } ?> value="America/Indiana/Petersburg">Indiana - Petersburg</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Tell_City") { echo 'selected="selected"'; } ?> value="America/Indiana/Tell_City">Indiana - Tell City</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Vevay") { echo 'selected="selected"'; } ?> value="America/Indiana/Vevay">Indiana - Vevay</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Vincennes") { echo 'selected="selected"'; } ?> value="America/Indiana/Vincennes">Indiana - Vincennes</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Winamac") { echo 'selected="selected"'; } ?> value="America/Indiana/Winamac">Indiana - Winamac</option>
<option <?php if($dados_stm["timezone"] == "America/Juneau") { echo 'selected="selected"'; } ?> value="America/Juneau">Juneau</option>
<option <?php if($dados_stm["timezone"] == "America/Kentucky/Louisville") { echo 'selected="selected"'; } ?> value="America/Kentucky/Louisville">Kentucky - Louisville</option>
<option <?php if($dados_stm["timezone"] == "America/Kentucky/Monticello") { echo 'selected="selected"'; } ?> value="America/Kentucky/Monticello">Kentucky - Monticello</option>
<option <?php if($dados_stm["timezone"] == "America/Los_Angeles") { echo 'selected="selected"'; } ?> value="America/Los_Angeles">Los Angeles</option>
<option <?php if($dados_stm["timezone"] == "America/Menominee") { echo 'selected="selected"'; } ?> value="America/Menominee">Menominee</option>
<option <?php if($dados_stm["timezone"] == "America/Metlakatla") { echo 'selected="selected"'; } ?> value="America/Metlakatla">Metlakatla</option>
<option <?php if($dados_stm["timezone"] == "America/Nome") { echo 'selected="selected"'; } ?> value="America/Nome">Nome</option>
<option <?php if($dados_stm["timezone"] == "America/New_York") { echo 'selected="selected"'; } ?> value="America/New_York">Nova Iorque</option>
<option <?php if($dados_stm["timezone"] == "America/Phoenix") { echo 'selected="selected"'; } ?> value="America/Phoenix">Phoenix</option>
<option <?php if($dados_stm["timezone"] == "America/Shiprock") { echo 'selected="selected"'; } ?> value="America/Shiprock">Shiprock</option>
<option <?php if($dados_stm["timezone"] == "America/Sitka") { echo 'selected="selected"'; } ?> value="America/Sitka">Sitka</option>
<option <?php if($dados_stm["timezone"] == "America/Yakutat") { echo 'selected="selected"'; } ?> value="America/Yakutat">Yakutat</option>
</optgroup>
<optgroup label="Est�nia (EE)">
<option <?php if($dados_stm["timezone"] == "Europe/Tallinn") { echo 'selected="selected"'; } ?> value="Europe/Tallinn">Tallinn</option>
</optgroup>
<optgroup label="Eti�pia (ET)">
<option <?php if($dados_stm["timezone"] == "Africa/Addis_Ababa") { echo 'selected="selected"'; } ?> value="Africa/Addis_Ababa">Adis Abeba</option>
</optgroup>
<optgroup label="Fiji (FJ)">
<option <?php if($dados_stm["timezone"] == "Pacific/Fiji") { echo 'selected="selected"'; } ?> value="Pacific/Fiji">Fiji</option>
</optgroup>
<optgroup label="Filipinas (PH)">
<option <?php if($dados_stm["timezone"] == "Asia/Manila") { echo 'selected="selected"'; } ?> value="Asia/Manila">Manila</option>
</optgroup>
<optgroup label="Finl�ndia (FI)">
<option <?php if($dados_stm["timezone"] == "Europe/Helsinki") { echo 'selected="selected"'; } ?> value="Europe/Helsinki">Hels�nquia</option>
</optgroup>
<optgroup label="Fran�a (FR)">
<option <?php if($dados_stm["timezone"] == "Europe/Paris") { echo 'selected="selected"'; } ?> value="Europe/Paris">Paris</option>
</optgroup>
<optgroup label="Gab�o (GA)">
<option <?php if($dados_stm["timezone"] == "Africa/Libreville") { echo 'selected="selected"'; } ?> value="Africa/Libreville">Libreville</option>
</optgroup>
<optgroup label="G�mbia (GM)">
<option <?php if($dados_stm["timezone"] == "Africa/Banjul") { echo 'selected="selected"'; } ?> value="Africa/Banjul">Banjul</option>
</optgroup>
<optgroup label="Gana (GH)">
<option <?php if($dados_stm["timezone"] == "Africa/Accra") { echo 'selected="selected"'; } ?> value="Africa/Accra">Accra</option>
</optgroup>
<optgroup label="Ge�rgia (GE)">
<option <?php if($dados_stm["timezone"] == "Asia/Tbilisi") { echo 'selected="selected"'; } ?> value="Asia/Tbilisi">Tbilisi</option>
</optgroup>
<optgroup label="Gibraltar (GI)">
<option <?php if($dados_stm["timezone"] == "Europe/Gibraltar") { echo 'selected="selected"'; } ?> value="Europe/Gibraltar">Gibraltar</option>
</optgroup>
<optgroup label="Granada (GD)">
<option <?php if($dados_stm["timezone"] == "America/Grenada") { echo 'selected="selected"'; } ?> value="America/Grenada">Grenada</option>
</optgroup>
<optgroup label="Gr�cia (GR)">
<option <?php if($dados_stm["timezone"] == "Europe/Athens") { echo 'selected="selected"'; } ?> value="Europe/Athens">Atenas</option>
</optgroup>
<optgroup label="Groel�ndia (GL)">
<option <?php if($dados_stm["timezone"] == "America/Danmarkshavn") { echo 'selected="selected"'; } ?> value="America/Danmarkshavn">Danmarkshavn</option>
<option <?php if($dados_stm["timezone"] == "America/Godthab") { echo 'selected="selected"'; } ?> value="America/Godthab">Godthab</option>
<option <?php if($dados_stm["timezone"] == "America/Scoresbysund") { echo 'selected="selected"'; } ?> value="America/Scoresbysund">Scoresbysund</option>
<option <?php if($dados_stm["timezone"] == "America/Thule") { echo 'selected="selected"'; } ?> value="America/Thule">Thule</option>
</optgroup>
<optgroup label="Guadalupe (GP)">
<option <?php if($dados_stm["timezone"] == "America/Guadeloupe") { echo 'selected="selected"'; } ?> value="America/Guadeloupe">Guadalupe</option>
</optgroup>
<optgroup label="Guam (GU)">
<option <?php if($dados_stm["timezone"] == "Pacific/Guam") { echo 'selected="selected"'; } ?> value="Pacific/Guam">Guam</option>
</optgroup>
<optgroup label="Guatemala (GT)">
<option <?php if($dados_stm["timezone"] == "America/Guatemala") { echo 'selected="selected"'; } ?> value="America/Guatemala">Guatemala</option>
</optgroup>
<optgroup label="Guernsey (GG)">
<option <?php if($dados_stm["timezone"] == "Europe/Guernsey") { echo 'selected="selected"'; } ?> value="Europe/Guernsey">Guernsey</option>
</optgroup>
<optgroup label="Guiana (GY)">
<option <?php if($dados_stm["timezone"] == "America/Guyana") { echo 'selected="selected"'; } ?> value="America/Guyana">Guiana</option>
</optgroup>
<optgroup label="Guiana Francesa (GF)">
<option <?php if($dados_stm["timezone"] == "America/Cayenne") { echo 'selected="selected"'; } ?> value="America/Cayenne">Cayenne</option>
</optgroup>
<optgroup label="Guin� (GN)">
<option <?php if($dados_stm["timezone"] == "Africa/Conakry") { echo 'selected="selected"'; } ?> value="Africa/Conakry">Conakry</option>
</optgroup>
<optgroup label="Guin� Equatorial (GQ)">
<option <?php if($dados_stm["timezone"] == "Africa/Malabo") { echo 'selected="selected"'; } ?> value="Africa/Malabo">Malabo</option>
</optgroup>
<optgroup label="Guin�-Bissau (GW)">
<option <?php if($dados_stm["timezone"] == "Africa/Bissau") { echo 'selected="selected"'; } ?> value="Africa/Bissau">Bissau</option>
</optgroup>
<optgroup label="Haiti (HT)">
<option <?php if($dados_stm["timezone"] == "America/Port-au-Prince") { echo 'selected="selected"'; } ?> value="America/Port-au-Prince">Port-au-Prince</option>
</optgroup>
<optgroup label="Honduras (HN)">
<option <?php if($dados_stm["timezone"] == "America/Tegucigalpa") { echo 'selected="selected"'; } ?> value="America/Tegucigalpa">Tegucigalpa</option>
</optgroup>
<optgroup label="Hong Kong (HK)">
<option <?php if($dados_stm["timezone"] == "Asia/Hong_Kong") { echo 'selected="selected"'; } ?> value="Asia/Hong_Kong">Hong Kong</option>
</optgroup>
<optgroup label="Hungria (HU)">
<option <?php if($dados_stm["timezone"] == "Europe/Budapest") { echo 'selected="selected"'; } ?> value="Europe/Budapest">Budapeste</option>
</optgroup>
<optgroup label="I�men (YE)">
<option <?php if($dados_stm["timezone"] == "Asia/Aden") { echo 'selected="selected"'; } ?> value="Asia/Aden">Aden</option>
</optgroup>
<optgroup label="Ilha Bouvet (BV)"></optgroup>
<optgroup label="Ilha Christmas (CX)">
<option <?php if($dados_stm["timezone"] == "Indian/Christmas") { echo 'selected="selected"'; } ?> value="Indian/Christmas">Natal</option>
</optgroup>
<optgroup label="Ilha de Man (IM)">
<option <?php if($dados_stm["timezone"] == "Europe/Isle_of_Man") { echo 'selected="selected"'; } ?> value="Europe/Isle_of_Man">Ilha de Man</option>
</optgroup>
<optgroup label="Ilha Heard e Ilhas McDonald (HM)"></optgroup>
<optgroup label="Ilha Norfolk (NF)">
<option <?php if($dados_stm["timezone"] == "Pacific/Norfolk") { echo 'selected="selected"'; } ?> value="Pacific/Norfolk">Norfolk</option>
</optgroup>
<optgroup label="Ilhas Aland (AX)">
<option <?php if($dados_stm["timezone"] == "Europe/Mariehamn") { echo 'selected="selected"'; } ?> value="Europe/Mariehamn">Mariehamn</option>
</optgroup>
<optgroup label="Ilhas Cayman (KY)">
<option <?php if($dados_stm["timezone"] == "America/Cayman") { echo 'selected="selected"'; } ?> value="America/Cayman">Ilhas Cayman</option>
</optgroup>
<optgroup label="Ilhas Cocos (CC)">
<option <?php if($dados_stm["timezone"] == "Indian/Cocos") { echo 'selected="selected"'; } ?> value="Indian/Cocos">Cocos</option>
</optgroup>
<optgroup label="Ilhas Cook (CK)">
<option <?php if($dados_stm["timezone"] == "Pacific/Rarotonga") { echo 'selected="selected"'; } ?> value="Pacific/Rarotonga">Rarotonga</option>
</optgroup>
<optgroup label="Ilhas Fero� (FO)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Faroe") { echo 'selected="selected"'; } ?> value="Atlantic/Faroe">Faro�</option>
</optgroup>
<optgroup label="Ilhas Ge�rgia do Sul e Sandwich do Sul (GS)">
<option <?php if($dados_stm["timezone"] == "Atlantic/South_Georgia") { echo 'selected="selected"'; } ?> value="Atlantic/South_Georgia">South Georgia</option>
</optgroup>
<optgroup label="Ilhas Malvinas (FK)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Stanley") { echo 'selected="selected"'; } ?> value="Atlantic/Stanley">Stanley</option>
</optgroup>
<optgroup label="Ilhas Marianas do Norte (MP)">
<option <?php if($dados_stm["timezone"] == "Pacific/Saipan") { echo 'selected="selected"'; } ?> value="Pacific/Saipan">Saipan</option>
</optgroup>
<optgroup label="Ilhas Marshall (MH)">
<option <?php if($dados_stm["timezone"] == "Pacific/Kwajalein") { echo 'selected="selected"'; } ?> value="Pacific/Kwajalein">Kwajalein</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Majuro") { echo 'selected="selected"'; } ?> value="Pacific/Majuro">Majuro</option>
</optgroup>
<optgroup label="Ilhas Menores Distantes dos Estados Unidos (UM)">
<option <?php if($dados_stm["timezone"] == "Pacific/Johnston") { echo 'selected="selected"'; } ?> value="Pacific/Johnston">Johnston</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Midway") { echo 'selected="selected"'; } ?> value="Pacific/Midway">Midway</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Wake") { echo 'selected="selected"'; } ?> value="Pacific/Wake">Wake</option>
</optgroup>
<optgroup label="Ilhas Pitcairn (PN)">
<option <?php if($dados_stm["timezone"] == "Pacific/Pitcairn") { echo 'selected="selected"'; } ?> value="Pacific/Pitcairn">Pitcairn</option>
</optgroup>
<optgroup label="Ilhas Salom�o (SB)">
<option <?php if($dados_stm["timezone"] == "Pacific/Guadalcanal") { echo 'selected="selected"'; } ?> value="Pacific/Guadalcanal">Guadalcanal</option>
</optgroup>
<optgroup label="Ilhas Turcas e Caicos (TC)">
<option <?php if($dados_stm["timezone"] == "America/Grand_Turk") { echo 'selected="selected"'; } ?> value="America/Grand_Turk">Grand Turk</option>
</optgroup>
<optgroup label="Ilhas Virgens Americanas (VI)">
<option <?php if($dados_stm["timezone"] == "America/St_Thomas") { echo 'selected="selected"'; } ?> value="America/St_Thomas">St Thomas</option>
</optgroup>
<optgroup label="Ilhas Virgens Brit�nicas (VG)">
<option <?php if($dados_stm["timezone"] == "America/Tortola") { echo 'selected="selected"'; } ?> value="America/Tortola">Tortola</option>
</optgroup>
<optgroup label="�ndia (IN)">
<option <?php if($dados_stm["timezone"] == "Asia/Kolkata") { echo 'selected="selected"'; } ?> value="Asia/Kolkata">Kolkata</option>
</optgroup>
<optgroup label="Indon�sia (ID)">
<option <?php if($dados_stm["timezone"] == "Asia/Jakarta") { echo 'selected="selected"'; } ?> value="Asia/Jakarta">Jakarta</option>
<option <?php if($dados_stm["timezone"] == "Asia/Jayapura") { echo 'selected="selected"'; } ?> value="Asia/Jayapura">Jayapura</option>
<option <?php if($dados_stm["timezone"] == "Asia/Makassar") { echo 'selected="selected"'; } ?> value="Asia/Makassar">Makassar</option>
<option <?php if($dados_stm["timezone"] == "Asia/Pontianak") { echo 'selected="selected"'; } ?> value="Asia/Pontianak">Pontianak</option>
</optgroup>
<optgroup label="Ir� (IR)">
<option <?php if($dados_stm["timezone"] == "Asia/Tehran") { echo 'selected="selected"'; } ?> value="Asia/Tehran">Teer�o</option>
</optgroup>
<optgroup label="Iraque (IQ)">
<option <?php if($dados_stm["timezone"] == "Asia/Baghdad") { echo 'selected="selected"'; } ?> value="Asia/Baghdad">Bagdade</option>
</optgroup>
<optgroup label="Irlanda (IE)">
<option <?php if($dados_stm["timezone"] == "Europe/Dublin") { echo 'selected="selected"'; } ?> value="Europe/Dublin">Dublim</option>
</optgroup>
<optgroup label="Isl�ndia (IS)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Reykjavik") { echo 'selected="selected"'; } ?> value="Atlantic/Reykjavik">Reykjavik</option>
</optgroup>
<optgroup label="Israel (IL)">
<option <?php if($dados_stm["timezone"] == "Asia/Jerusalem") { echo 'selected="selected"'; } ?> value="Asia/Jerusalem">Jerusal�m</option>
</optgroup>
<optgroup label="It�lia (IT)">
<option <?php if($dados_stm["timezone"] == "Europe/Rome") { echo 'selected="selected"'; } ?> value="Europe/Rome">Roma</option>
</optgroup>
<optgroup label="Jamaica (JM)">
<option <?php if($dados_stm["timezone"] == "America/Jamaica") { echo 'selected="selected"'; } ?> value="America/Jamaica">Jamaica</option>
</optgroup>
<optgroup label="Jap�o (JP)">
<option <?php if($dados_stm["timezone"] == "Asia/Tokyo") { echo 'selected="selected"'; } ?> value="Asia/Tokyo">T�quio</option>
</optgroup>
<optgroup label="Jersey (JE)">
<option <?php if($dados_stm["timezone"] == "Europe/Jersey") { echo 'selected="selected"'; } ?> value="Europe/Jersey">Jersey</option>
</optgroup>
<optgroup label="Jibuti (DJ)">
<option <?php if($dados_stm["timezone"] == "Africa/Djibouti") { echo 'selected="selected"'; } ?> value="Africa/Djibouti">Djibuti</option>
</optgroup>
<optgroup label="Jord�nia (JO)">
<option <?php if($dados_stm["timezone"] == "Asia/Amman") { echo 'selected="selected"'; } ?> value="Asia/Amman">Am�</option>
</optgroup>
<optgroup label="Kiribati (KI)">
<option <?php if($dados_stm["timezone"] == "Pacific/Enderbury") { echo 'selected="selected"'; } ?> value="Pacific/Enderbury">Enderbury</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Kiritimati") { echo 'selected="selected"'; } ?> value="Pacific/Kiritimati">Kiritimati</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Tarawa") { echo 'selected="selected"'; } ?> value="Pacific/Tarawa">Tarawa</option>
</optgroup>
<optgroup label="Kosovo (XK)"></optgroup>
<optgroup label="Kuwait (KW)">
<option <?php if($dados_stm["timezone"] == "Asia/Kuwait") { echo 'selected="selected"'; } ?> value="Asia/Kuwait">Kuwait</option>
</optgroup>
<optgroup label="Laos (LA)">
<option <?php if($dados_stm["timezone"] == "Asia/Vientiane") { echo 'selected="selected"'; } ?> value="Asia/Vientiane">Vientiane</option>
</optgroup>
<optgroup label="Lesoto (LS)">
<option <?php if($dados_stm["timezone"] == "Africa/Maseru") { echo 'selected="selected"'; } ?> value="Africa/Maseru">Maseru</option>
</optgroup>
<optgroup label="Let�nia (LV)">
<option <?php if($dados_stm["timezone"] == "Europe/Riga") { echo 'selected="selected"'; } ?> value="Europe/Riga">Riga</option>
</optgroup>
<optgroup label="L�bano (LB)">
<option <?php if($dados_stm["timezone"] == "Asia/Beirut") { echo 'selected="selected"'; } ?> value="Asia/Beirut">Beirute</option>
</optgroup>
<optgroup label="Lib�ria (LR)">
<option <?php if($dados_stm["timezone"] == "Africa/Monrovia") { echo 'selected="selected"'; } ?> value="Africa/Monrovia">Monrovia</option>
</optgroup>
<optgroup label="L�bia (LY)">
<option <?php if($dados_stm["timezone"] == "Africa/Tripoli") { echo 'selected="selected"'; } ?> value="Africa/Tripoli">Tr�poli</option>
</optgroup>
<optgroup label="Liechtenstein (LI)">
<option <?php if($dados_stm["timezone"] == "Europe/Vaduz") { echo 'selected="selected"'; } ?> value="Europe/Vaduz">Vaduz</option>
</optgroup>
<optgroup label="Litu�nia (LT)">
<option <?php if($dados_stm["timezone"] == "Europe/Vilnius") { echo 'selected="selected"'; } ?> value="Europe/Vilnius">Vilnius</option>
</optgroup>
<optgroup label="Luxemburgo (LU)">
<option <?php if($dados_stm["timezone"] == "Europe/Luxembourg") { echo 'selected="selected"'; } ?> value="Europe/Luxembourg">Luxemburgo</option>
</optgroup>
<optgroup label="Macau (MO)">
<option <?php if($dados_stm["timezone"] == "Asia/Macau") { echo 'selected="selected"'; } ?> value="Asia/Macau">Macau</option>
</optgroup>
<optgroup label="Maced�nia (MK)">
<option <?php if($dados_stm["timezone"] == "Europe/Skopje") { echo 'selected="selected"'; } ?> value="Europe/Skopje">Skopje</option>
</optgroup>
<optgroup label="Madagascar (MG)">
<option <?php if($dados_stm["timezone"] == "Indian/Antananarivo") { echo 'selected="selected"'; } ?> value="Indian/Antananarivo">Antananarivo</option>
</optgroup>
<optgroup label="Mal�sia (MY)">
<option <?php if($dados_stm["timezone"] == "Asia/Kuala_Lumpur") { echo 'selected="selected"'; } ?> value="Asia/Kuala_Lumpur">Kuala Lumpur</option>
<option <?php if($dados_stm["timezone"] == "Asia/Kuching") { echo 'selected="selected"'; } ?> value="Asia/Kuching">Kuching</option>
</optgroup>
<optgroup label="Malawi (MW)">
<option <?php if($dados_stm["timezone"] == "Africa/Blantyre") { echo 'selected="selected"'; } ?> value="Africa/Blantyre">Blantyre</option>
</optgroup>
<optgroup label="Maldivas (MV)">
<option <?php if($dados_stm["timezone"] == "Indian/Maldives") { echo 'selected="selected"'; } ?> value="Indian/Maldives">Maldivas</option>
</optgroup>
<optgroup label="Mali (ML)">
<option <?php if($dados_stm["timezone"] == "Africa/Bamako") { echo 'selected="selected"'; } ?> value="Africa/Bamako">Bamako</option>
</optgroup>
<optgroup label="Malta (MT)">
<option <?php if($dados_stm["timezone"] == "Europe/Malta") { echo 'selected="selected"'; } ?> value="Europe/Malta">Malta</option>
</optgroup>
<optgroup label="Marrocos (MA)">
<option <?php if($dados_stm["timezone"] == "Africa/Casablanca") { echo 'selected="selected"'; } ?> value="Africa/Casablanca">Casablanca</option>
</optgroup>
<optgroup label="Martinica (MQ)">
<option <?php if($dados_stm["timezone"] == "America/Martinique") { echo 'selected="selected"'; } ?> value="America/Martinique">Martinica</option>
</optgroup>
<optgroup label="Maur�cio (MU)">
<option <?php if($dados_stm["timezone"] == "Indian/Mauritius") { echo 'selected="selected"'; } ?> value="Indian/Mauritius">Maur�cias</option>
</optgroup>
<optgroup label="Maurit�nia (MR)">
<option <?php if($dados_stm["timezone"] == "Africa/Nouakchott") { echo 'selected="selected"'; } ?> value="Africa/Nouakchott">Nouakchott</option>
</optgroup>
<optgroup label="Mayotte (YT)">
<option <?php if($dados_stm["timezone"] == "Indian/Mayotte") { echo 'selected="selected"'; } ?> value="Indian/Mayotte">Mayotte</option>
</optgroup>
<optgroup label="M�xico (MX)">
<option <?php if($dados_stm["timezone"] == "America/Bahia_Banderas") { echo 'selected="selected"'; } ?> value="America/Bahia_Banderas">Bahia Banderas</option>
<option <?php if($dados_stm["timezone"] == "America/Cancun") { echo 'selected="selected"'; } ?> value="America/Cancun">Canc�n</option>
<option <?php if($dados_stm["timezone"] == "America/Chihuahua") { echo 'selected="selected"'; } ?> value="America/Chihuahua">Chihuahua</option>
<option <?php if($dados_stm["timezone"] == "America/Mexico_City") { echo 'selected="selected"'; } ?> value="America/Mexico_City">Cidade do M�xico</option>
<option <?php if($dados_stm["timezone"] == "America/Hermosillo") { echo 'selected="selected"'; } ?> value="America/Hermosillo">Hermosillo</option>
<option <?php if($dados_stm["timezone"] == "America/Matamoros") { echo 'selected="selected"'; } ?> value="America/Matamoros">Matamoros</option>
<option <?php if($dados_stm["timezone"] == "America/Mazatlan") { echo 'selected="selected"'; } ?> value="America/Mazatlan">Mazatlan</option>
<option <?php if($dados_stm["timezone"] == "America/Merida") { echo 'selected="selected"'; } ?> value="America/Merida">M�rida</option>
<option <?php if($dados_stm["timezone"] == "America/Monterrey") { echo 'selected="selected"'; } ?> value="America/Monterrey">Monterrey</option>
<option <?php if($dados_stm["timezone"] == "America/Ojinaga") { echo 'selected="selected"'; } ?> value="America/Ojinaga">Ojinaga</option>
<option <?php if($dados_stm["timezone"] == "America/Santa_Isabel") { echo 'selected="selected"'; } ?> value="America/Santa_Isabel">Santa Isabel</option>
<option <?php if($dados_stm["timezone"] == "America/Tijuana") { echo 'selected="selected"'; } ?> value="America/Tijuana">Tijuana</option>
</optgroup>
<optgroup label="Micron�sia (FM)">
<option <?php if($dados_stm["timezone"] == "Pacific/Chuuk") { echo 'selected="selected"'; } ?> value="Pacific/Chuuk">Chuuk</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Kosrae") { echo 'selected="selected"'; } ?> value="Pacific/Kosrae">Kosrae</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Pohnpei") { echo 'selected="selected"'; } ?> value="Pacific/Pohnpei">Pohnpei</option>
</optgroup>
<optgroup label="Mo�ambique (MZ)">
<option <?php if($dados_stm["timezone"] == "Africa/Maputo") { echo 'selected="selected"'; } ?> value="Africa/Maputo">Maputo</option>
</optgroup>
<optgroup label="Mold�via (MD)">
<option <?php if($dados_stm["timezone"] == "Europe/Chisinau") { echo 'selected="selected"'; } ?> value="Europe/Chisinau">Chisinau</option>
</optgroup>
<optgroup label="M�naco (MC)">
<option <?php if($dados_stm["timezone"] == "Europe/Monaco") { echo 'selected="selected"'; } ?> value="Europe/Monaco">M�naco</option>
</optgroup>
<optgroup label="Mong�lia (MN)">
<option <?php if($dados_stm["timezone"] == "Asia/Choibalsan") { echo 'selected="selected"'; } ?> value="Asia/Choibalsan">Choibalsan</option>
<option <?php if($dados_stm["timezone"] == "Asia/Hovd") { echo 'selected="selected"'; } ?> value="Asia/Hovd">Hovd</option>
<option <?php if($dados_stm["timezone"] == "Asia/Ulaanbaatar") { echo 'selected="selected"'; } ?> value="Asia/Ulaanbaatar">Ulaanbaatar</option>
</optgroup>
<optgroup label="Montenegro (ME)">
<option <?php if($dados_stm["timezone"] == "Europe/Podgorica") { echo 'selected="selected"'; } ?> value="Europe/Podgorica">Podgorica</option>
</optgroup>
<optgroup label="Montserrat (MS)">
<option <?php if($dados_stm["timezone"] == "America/Montserrat") { echo 'selected="selected"'; } ?> value="America/Montserrat">Montserrat</option>
</optgroup>
<optgroup label="Myanmar (MM)">
<option <?php if($dados_stm["timezone"] == "Asia/Rangoon") { echo 'selected="selected"'; } ?> value="Asia/Rangoon">Rangoon</option>
</optgroup>
<optgroup label="Nam�bia (NA)">
<option <?php if($dados_stm["timezone"] == "Africa/Windhoek") { echo 'selected="selected"'; } ?> value="Africa/Windhoek">Windhoek</option>
</optgroup>
<optgroup label="Nauru (NR)">
<option <?php if($dados_stm["timezone"] == "Pacific/Nauru") { echo 'selected="selected"'; } ?> value="Pacific/Nauru">Nauru</option>
</optgroup>
<optgroup label="Nepal (NP)">
<option <?php if($dados_stm["timezone"] == "Asia/Kathmandu") { echo 'selected="selected"'; } ?> value="Asia/Kathmandu">Kathmandu</option>
</optgroup>
<optgroup label="Nicar�gua (NI)">
<option <?php if($dados_stm["timezone"] == "America/Managua") { echo 'selected="selected"'; } ?> value="America/Managua">Man�gua</option>
</optgroup>
<optgroup label="N�ger (NE)">
<option <?php if($dados_stm["timezone"] == "Africa/Niamey") { echo 'selected="selected"'; } ?> value="Africa/Niamey">Niamey</option>
</optgroup>
<optgroup label="Nig�ria (NG)">
<option <?php if($dados_stm["timezone"] == "Africa/Lagos") { echo 'selected="selected"'; } ?> value="Africa/Lagos">Lagos</option>
</optgroup>
<optgroup label="Niue (NU)">
<option <?php if($dados_stm["timezone"] == "Pacific/Niue") { echo 'selected="selected"'; } ?> value="Pacific/Niue">Niue</option>
</optgroup>
<optgroup label="Noruega (NO)">
<option <?php if($dados_stm["timezone"] == "Europe/Oslo") { echo 'selected="selected"'; } ?> value="Europe/Oslo">Oslo</option>
</optgroup>
<optgroup label="Nova Caled�nia (NC)">
<option <?php if($dados_stm["timezone"] == "Pacific/Noumea") { echo 'selected="selected"'; } ?> value="Pacific/Noumea">Noumea</option>
</optgroup>
<optgroup label="Nova Zel�ndia (NZ)">
<option <?php if($dados_stm["timezone"] == "Pacific/Auckland") { echo 'selected="selected"'; } ?> value="Pacific/Auckland">Auckland</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Chatham") { echo 'selected="selected"'; } ?> value="Pacific/Chatham">Chatham</option>
</optgroup>
<optgroup label="Om� (OM)">
<option <?php if($dados_stm["timezone"] == "Asia/Muscat") { echo 'selected="selected"'; } ?> value="Asia/Muscat">Muscat</option>
</optgroup>
<optgroup label="Pa�ses Baixos (NL)">
<option <?php if($dados_stm["timezone"] == "Europe/Amsterdam") { echo 'selected="selected"'; } ?> value="Europe/Amsterdam">Amsterd�o</option>
</optgroup>
<optgroup label="Palau (PW)">
<option <?php if($dados_stm["timezone"] == "Pacific/Palau") { echo 'selected="selected"'; } ?> value="Pacific/Palau">Palau</option>
</optgroup>
<optgroup label="Panam� (PA)">
<option <?php if($dados_stm["timezone"] == "America/Panama") { echo 'selected="selected"'; } ?> value="America/Panama">Panam�</option>
</optgroup>
<optgroup label="Papua-Nova Guin� (PG)">
<option <?php if($dados_stm["timezone"] == "Pacific/Port_Moresby") { echo 'selected="selected"'; } ?> value="Pacific/Port_Moresby">Port Moresby</option>
</optgroup>
<optgroup label="Paquist�o (PK)">
<option <?php if($dados_stm["timezone"] == "Asia/Karachi") { echo 'selected="selected"'; } ?> value="Asia/Karachi">Karachi</option>
</optgroup>
<optgroup label="Paraguai (PY)">
<option <?php if($dados_stm["timezone"] == "America/Asuncion") { echo 'selected="selected"'; } ?> value="America/Asuncion">Asunci�n</option>
</optgroup>
<optgroup label="Peru (PE)">
<option <?php if($dados_stm["timezone"] == "America/Lima") { echo 'selected="selected"'; } ?> value="America/Lima">Lima</option>
</optgroup>
<optgroup label="Polin�sia Francesa (PF)">
<option <?php if($dados_stm["timezone"] == "Pacific/Gambier") { echo 'selected="selected"'; } ?> value="Pacific/Gambier">Gambier</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Marquesas") { echo 'selected="selected"'; } ?> value="Pacific/Marquesas">Marquesas</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Tahiti") { echo 'selected="selected"'; } ?> value="Pacific/Tahiti">Tahiti</option>
</optgroup>
<optgroup label="Pol�nia (PL)">
<option <?php if($dados_stm["timezone"] == "Europe/Warsaw") { echo 'selected="selected"'; } ?> value="Europe/Warsaw">Vars�via</option>
</optgroup>
<optgroup label="Porto Rico (PR)">
<option <?php if($dados_stm["timezone"] == "America/Puerto_Rico") { echo 'selected="selected"'; } ?> value="America/Puerto_Rico">Porto Rico</option>
</optgroup>
<optgroup label="Portugal (PT)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Azores") { echo 'selected="selected"'; } ?> value="Atlantic/Azores">A�ores</option>
<option <?php if($dados_stm["timezone"] == "Europe/Lisbon") { echo 'selected="selected"'; } ?> value="Europe/Lisbon">Lisboa</option>
<option <?php if($dados_stm["timezone"] == "Atlantic/Madeira") { echo 'selected="selected"'; } ?> value="Atlantic/Madeira">Madeira</option>
</optgroup>
<optgroup label="Qu�nia (KE)">
<option <?php if($dados_stm["timezone"] == "Africa/Nairobi") { echo 'selected="selected"'; } ?> value="Africa/Nairobi">Nairobi</option>
</optgroup>
<optgroup label="Quirguist�o (KG)">
<option <?php if($dados_stm["timezone"] == "Asia/Bishkek") { echo 'selected="selected"'; } ?> value="Asia/Bishkek">Bishkek</option>
</optgroup>
<optgroup label="Reino Unido (GB)">
<option <?php if($dados_stm["timezone"] == "Europe/London") { echo 'selected="selected"'; } ?> value="Europe/London">Londres</option>
</optgroup>
<optgroup label="Rep�blica Centro-Africana (CF)">
<option <?php if($dados_stm["timezone"] == "Africa/Bangui") { echo 'selected="selected"'; } ?> value="Africa/Bangui">Bangui</option>
</optgroup>
<optgroup label="Rep�blica Democr�tica do Congo (CD)">
<option <?php if($dados_stm["timezone"] == "Africa/Kinshasa") { echo 'selected="selected"'; } ?> value="Africa/Kinshasa">Kinshasa</option>
<option <?php if($dados_stm["timezone"] == "Africa/Lubumbashi") { echo 'selected="selected"'; } ?> value="Africa/Lubumbashi">Lubumbashi</option>
</optgroup>
<optgroup label="Rep�blica do Congo (CG)">
<option <?php if($dados_stm["timezone"] == "Africa/Brazzaville") { echo 'selected="selected"'; } ?> value="Africa/Brazzaville">Brazzaville</option>
</optgroup>
<optgroup label="Rep�blica Dominicana (DO)">
<option <?php if($dados_stm["timezone"] == "America/Santo_Domingo") { echo 'selected="selected"'; } ?> value="America/Santo_Domingo">Santo Domingo</option>
</optgroup>
<optgroup label="Rep�blica Tcheca (CZ)">
<option <?php if($dados_stm["timezone"] == "Europe/Prague") { echo 'selected="selected"'; } ?> value="Europe/Prague">Praga</option>
</optgroup>
<optgroup label="Reuni�o (RE)">
<option <?php if($dados_stm["timezone"] == "Indian/Reunion") { echo 'selected="selected"'; } ?> value="Indian/Reunion">Reuni�o</option>
</optgroup>
<optgroup label="Rom�nia (RO)">
<option <?php if($dados_stm["timezone"] == "Europe/Bucharest") { echo 'selected="selected"'; } ?> value="Europe/Bucharest">Bucareste</option>
</optgroup>
<optgroup label="Ruanda (RW)">
<option <?php if($dados_stm["timezone"] == "Africa/Kigali") { echo 'selected="selected"'; } ?> value="Africa/Kigali">Kigali</option>
</optgroup>
<optgroup label="R�ssia (RU)">
<option <?php if($dados_stm["timezone"] == "Asia/Anadyr") { echo 'selected="selected"'; } ?> value="Asia/Anadyr">Anadyr</option>
<option <?php if($dados_stm["timezone"] == "Asia/Irkutsk") { echo 'selected="selected"'; } ?> value="Asia/Irkutsk">Irkutsk</option>
<option <?php if($dados_stm["timezone"] == "Europe/Kaliningrad") { echo 'selected="selected"'; } ?> value="Europe/Kaliningrad">Kaliningrad</option>
<option <?php if($dados_stm["timezone"] == "Asia/Kamchatka") { echo 'selected="selected"'; } ?> value="Asia/Kamchatka">Kamchatka</option>
<option <?php if($dados_stm["timezone"] == "Asia/Khandyga") { echo 'selected="selected"'; } ?> value="Asia/Khandyga">Khandyga</option>
<option <?php if($dados_stm["timezone"] == "Asia/Krasnoyarsk") { echo 'selected="selected"'; } ?> value="Asia/Krasnoyarsk">Krasnoyarsk</option>
<option <?php if($dados_stm["timezone"] == "Asia/Magadan") { echo 'selected="selected"'; } ?> value="Asia/Magadan">Magadan</option>
<option <?php if($dados_stm["timezone"] == "Europe/Moscow") { echo 'selected="selected"'; } ?> value="Europe/Moscow">Moscovo</option>
<option <?php if($dados_stm["timezone"] == "Asia/Novokuznetsk") { echo 'selected="selected"'; } ?> value="Asia/Novokuznetsk">Novokuznetsk</option>
<option <?php if($dados_stm["timezone"] == "Asia/Novosibirsk") { echo 'selected="selected"'; } ?> value="Asia/Novosibirsk">Novosibirsk</option>
<option <?php if($dados_stm["timezone"] == "Asia/Omsk") { echo 'selected="selected"'; } ?> value="Asia/Omsk">Omsk</option>
<option <?php if($dados_stm["timezone"] == "Asia/Sakhalin") { echo 'selected="selected"'; } ?> value="Asia/Sakhalin">Sakhalin</option>
<option <?php if($dados_stm["timezone"] == "Europe/Samara") { echo 'selected="selected"'; } ?> value="Europe/Samara">Samara</option>
<option <?php if($dados_stm["timezone"] == "Asia/Ust-Nera") { echo 'selected="selected"'; } ?> value="Asia/Ust-Nera">Ust-Nera</option>
<option <?php if($dados_stm["timezone"] == "Asia/Vladivostok") { echo 'selected="selected"'; } ?> value="Asia/Vladivostok">Vladivostok</option>
<option <?php if($dados_stm["timezone"] == "Europe/Volgograd") { echo 'selected="selected"'; } ?> value="Europe/Volgograd">Volgograd</option>
<option <?php if($dados_stm["timezone"] == "Asia/Yakutsk") { echo 'selected="selected"'; } ?> value="Asia/Yakutsk">Yakutsk</option>
<option <?php if($dados_stm["timezone"] == "Asia/Yekaterinburg") { echo 'selected="selected"'; } ?> value="Asia/Yekaterinburg">Yekaterinburg</option>
</optgroup>
<optgroup label="Saara Ocidental (EH)">
<option <?php if($dados_stm["timezone"] == "Africa/El_Aaiun") { echo 'selected="selected"'; } ?> value="Africa/El_Aaiun">El Aaiun</option>
</optgroup>
<optgroup label="Saint-Pierre e Miquelon (PM)">
<option <?php if($dados_stm["timezone"] == "America/Miquelon") { echo 'selected="selected"'; } ?> value="America/Miquelon">Miquelon</option>
</optgroup>
<optgroup label="Samoa (WS)">
<option <?php if($dados_stm["timezone"] == "Pacific/Apia") { echo 'selected="selected"'; } ?> value="Pacific/Apia">Apia</option>
</optgroup>
<optgroup label="Samoa Americana (AS)">
<option <?php if($dados_stm["timezone"] == "Pacific/Pago_Pago") { echo 'selected="selected"'; } ?> value="Pacific/Pago_Pago">Pago Pago</option>
</optgroup>
<optgroup label="San Marino (SM)">
<option <?php if($dados_stm["timezone"] == "Europe/San_Marino") { echo 'selected="selected"'; } ?> value="Europe/San_Marino">San Marino</option>
</optgroup>
<optgroup label="Santa Helena (SH)">
<option <?php if($dados_stm["timezone"] == "Atlantic/St_Helena") { echo 'selected="selected"'; } ?> value="Atlantic/St_Helena">St Helena</option>
</optgroup>
<optgroup label="Santa L�cia (LC)">
<option <?php if($dados_stm["timezone"] == "America/St_Lucia") { echo 'selected="selected"'; } ?> value="America/St_Lucia">St Lucia</option>
</optgroup>
<optgroup label="S�o Bartolomeu (BL)">
<option <?php if($dados_stm["timezone"] == "America/St_Barthelemy") { echo 'selected="selected"'; } ?> value="America/St_Barthelemy">St Barthelemy</option>
</optgroup>
<optgroup label="S�o Crist�v�o e Nevis (KN)">
<option <?php if($dados_stm["timezone"] == "America/St_Kitts") { echo 'selected="selected"'; } ?> value="America/St_Kitts">St Kitts</option>
</optgroup>
<optgroup label="S�o Martinho (Pa�ses Baixos) (SX)">
<option <?php if($dados_stm["timezone"] == "America/Lower_Princes") { echo 'selected="selected"'; } ?> value="America/Lower_Princes">Lower Princes</option>
</optgroup>
<optgroup label="S�o Tom� e Pr�ncipe (ST)">
<option <?php if($dados_stm["timezone"] == "Africa/Sao_Tome") { echo 'selected="selected"'; } ?> value="Africa/Sao_Tome">S�o Tom�</option>
</optgroup>
<optgroup label="S�o Vicente e Granadinas (VC)">
<option <?php if($dados_stm["timezone"] == "America/St_Vincent") { echo 'selected="selected"'; } ?> value="America/St_Vincent">St Vincent</option>
</optgroup>
<optgroup label="Senegal (SN)">
<option <?php if($dados_stm["timezone"] == "Africa/Dakar") { echo 'selected="selected"'; } ?> value="Africa/Dakar">Dakar</option>
</optgroup>
<optgroup label="Serra Leoa (SL)">
<option <?php if($dados_stm["timezone"] == "Africa/Freetown") { echo 'selected="selected"'; } ?> value="Africa/Freetown">Freetown</option>
</optgroup>
<optgroup label="S�rvia (RS)">
<option <?php if($dados_stm["timezone"] == "Europe/Belgrade") { echo 'selected="selected"'; } ?> value="Europe/Belgrade">Belgrado</option>
</optgroup>
<optgroup label="S�rvia e Montenegro (CS)"></optgroup>
<optgroup label="Seychelles (SC)">
<option <?php if($dados_stm["timezone"] == "Indian/Mahe") { echo 'selected="selected"'; } ?> value="Indian/Mahe">Mahe</option>
</optgroup>
<optgroup label="Singapura (SG)">
<option <?php if($dados_stm["timezone"] == "Asia/Singapore") { echo 'selected="selected"'; } ?> value="Asia/Singapore">Singapura</option>
</optgroup>
<optgroup label="S�ria (SY)">
<option <?php if($dados_stm["timezone"] == "Asia/Damascus") { echo 'selected="selected"'; } ?> value="Asia/Damascus">Damasco</option>
</optgroup>
<optgroup label="Som�lia (SO)">
<option <?php if($dados_stm["timezone"] == "Africa/Mogadishu") { echo 'selected="selected"'; } ?> value="Africa/Mogadishu">Mogad�scio</option>
</optgroup>
<optgroup label="Sri Lanka (LK)">
<option <?php if($dados_stm["timezone"] == "Asia/Colombo") { echo 'selected="selected"'; } ?> value="Asia/Colombo">Colombo</option>
</optgroup>
<optgroup label="Suazil�ndia (SZ)">
<option <?php if($dados_stm["timezone"] == "Africa/Mbabane") { echo 'selected="selected"'; } ?> value="Africa/Mbabane">Mbabane</option>
</optgroup>
<optgroup label="Sud�o (SD)">
<option <?php if($dados_stm["timezone"] == "Africa/Khartoum") { echo 'selected="selected"'; } ?> value="Africa/Khartoum">Cartum</option>
</optgroup>
<optgroup label="Sud�o do Sul (SS)">
<option <?php if($dados_stm["timezone"] == "Africa/Juba") { echo 'selected="selected"'; } ?> value="Africa/Juba">Juba</option>
</optgroup>
<optgroup label="Su�cia (SE)">
<option <?php if($dados_stm["timezone"] == "Europe/Stockholm") { echo 'selected="selected"'; } ?> value="Europe/Stockholm">Estocolmo</option>
</optgroup>
<optgroup label="Su��a (CH)">
<option <?php if($dados_stm["timezone"] == "Europe/Zurich") { echo 'selected="selected"'; } ?> value="Europe/Zurich">Zurique</option>
</optgroup>
<optgroup label="Suriname (SR)">
<option <?php if($dados_stm["timezone"] == "America/Paramaribo") { echo 'selected="selected"'; } ?> value="America/Paramaribo">Paramaribo</option>
</optgroup>
<optgroup label="Svalbard e Jan Mayen (SJ)">
<option <?php if($dados_stm["timezone"] == "Arctic/Longyearbyen") { echo 'selected="selected"'; } ?> value="Arctic/Longyearbyen">Longyearbyen</option>
</optgroup>
<optgroup label="Tail�ndia (TH)">
<option <?php if($dados_stm["timezone"] == "Asia/Bangkok") { echo 'selected="selected"'; } ?> value="Asia/Bangkok">Banguecoque</option>
</optgroup>
<optgroup label="Taiwan (TW)">
<option <?php if($dados_stm["timezone"] == "Asia/Taipei") { echo 'selected="selected"'; } ?> value="Asia/Taipei">Taipe</option>
</optgroup>
<optgroup label="Tajiquist�o (TJ)">
<option <?php if($dados_stm["timezone"] == "Asia/Dushanbe") { echo 'selected="selected"'; } ?> value="Asia/Dushanbe">Dushanbe</option>
</optgroup>
<optgroup label="Tanz�nia (TZ)">
<option <?php if($dados_stm["timezone"] == "Africa/Dar_es_Salaam") { echo 'selected="selected"'; } ?> value="Africa/Dar_es_Salaam">Dar es Salaam</option>
</optgroup>
<optgroup label="Terras Austrais e Ant�rticas Francesas (TF)">
<option <?php if($dados_stm["timezone"] == "Indian/Kerguelen") { echo 'selected="selected"'; } ?> value="Indian/Kerguelen">Kerguelen</option>
</optgroup>
<optgroup label="Territ�rio Brit�nico do Oceano �ndico (IO)">
<option <?php if($dados_stm["timezone"] == "Indian/Chagos") { echo 'selected="selected"'; } ?> value="Indian/Chagos">Chagos</option>
</optgroup>
<optgroup label="Territ�rios Palestinianos (PS)">
<option <?php if($dados_stm["timezone"] == "Asia/Gaza") { echo 'selected="selected"'; } ?> value="Asia/Gaza">Gaza</option>
<option <?php if($dados_stm["timezone"] == "Asia/Hebron") { echo 'selected="selected"'; } ?> value="Asia/Hebron">Hebron</option>
</optgroup>
<optgroup label="Timor-Leste (TL)">
<option <?php if($dados_stm["timezone"] == "Asia/Dili") { echo 'selected="selected"'; } ?> value="Asia/Dili">Dili</option>
</optgroup>
<optgroup label="Togo (TG)">
<option <?php if($dados_stm["timezone"] == "Africa/Lome") { echo 'selected="selected"'; } ?> value="Africa/Lome">Lom�</option>
</optgroup>
<optgroup label="Tokelau (TK)">
<option <?php if($dados_stm["timezone"] == "Pacific/Fakaofo") { echo 'selected="selected"'; } ?> value="Pacific/Fakaofo">Fakaofo</option>
</optgroup>
<optgroup label="Tonga (TO)">
<option <?php if($dados_stm["timezone"] == "Pacific/Tongatapu") { echo 'selected="selected"'; } ?> value="Pacific/Tongatapu">Tongatapu</option>
</optgroup>
<optgroup label="Trinidad e Tobago (TT)">
<option <?php if($dados_stm["timezone"] == "America/Port_of_Spain") { echo 'selected="selected"'; } ?> value="America/Port_of_Spain">Port of Spain</option>
</optgroup>
<optgroup label="Tun�sia (TN)">
<option <?php if($dados_stm["timezone"] == "Africa/Tunis") { echo 'selected="selected"'; } ?> value="Africa/Tunis">T�nis</option>
</optgroup>
<optgroup label="Turquemenist�o (TM)">
<option <?php if($dados_stm["timezone"] == "Asia/Ashgabat") { echo 'selected="selected"'; } ?> value="Asia/Ashgabat">Ashgabat</option>
</optgroup>
<optgroup label="Turquia (TR)">
<option <?php if($dados_stm["timezone"] == "Europe/Istanbul") { echo 'selected="selected"'; } ?> value="Europe/Istanbul">Istanbul</option>
</optgroup>
<optgroup label="Tuvalu (TV)">
<option <?php if($dados_stm["timezone"] == "Pacific/Funafuti") { echo 'selected="selected"'; } ?> value="Pacific/Funafuti">Funafuti</option>
</optgroup>
<optgroup label="Ucr�nia (UA)">
<option <?php if($dados_stm["timezone"] == "Europe/Kiev") { echo 'selected="selected"'; } ?> value="Europe/Kiev">Kiev</option>
<option <?php if($dados_stm["timezone"] == "Europe/Simferopol") { echo 'selected="selected"'; } ?> value="Europe/Simferopol">Simferopol</option>
<option <?php if($dados_stm["timezone"] == "Europe/Uzhgorod") { echo 'selected="selected"'; } ?> value="Europe/Uzhgorod">Uzhgorod</option>
<option <?php if($dados_stm["timezone"] == "Europe/Zaporozhye") { echo 'selected="selected"'; } ?> value="Europe/Zaporozhye">Zaporozhye</option>
</optgroup>
<optgroup label="Uganda (UG)">
<option <?php if($dados_stm["timezone"] == "Africa/Kampala") { echo 'selected="selected"'; } ?> value="Africa/Kampala">Kampala</option>
</optgroup>
<optgroup label="Uruguai (UY)">
<option <?php if($dados_stm["timezone"] == "America/Montevideo") { echo 'selected="selected"'; } ?> value="America/Montevideo">Montevid�u</option>
</optgroup>
<optgroup label="Uzbequist�o (UZ)">
<option <?php if($dados_stm["timezone"] == "Asia/Samarkand") { echo 'selected="selected"'; } ?> value="Asia/Samarkand">Samarkand</option>
<option <?php if($dados_stm["timezone"] == "Asia/Tashkent") { echo 'selected="selected"'; } ?> value="Asia/Tashkent">Tashkent</option>
</optgroup>
<optgroup label="Vanuatu (VU)">
<option <?php if($dados_stm["timezone"] == "Pacific/Efate") { echo 'selected="selected"'; } ?> value="Pacific/Efate">Efate</option>
</optgroup>
<optgroup label="Vaticano (VA)">
<option <?php if($dados_stm["timezone"] == "Europe/Vatican") { echo 'selected="selected"'; } ?> value="Europe/Vatican">Vaticano</option>
</optgroup>
<optgroup label="Venezuela (VE)">
<option <?php if($dados_stm["timezone"] == "America/Caracas") { echo 'selected="selected"'; } ?> value="America/Caracas">Caracas</option>
</optgroup>
<optgroup label="Vietn� (VN)">
<option <?php if($dados_stm["timezone"] == "Asia/Ho_Chi_Minh") { echo 'selected="selected"'; } ?> value="Asia/Ho_Chi_Minh">Ho Chi Minh</option>
</optgroup>
<optgroup label="Wallis e Futuna (WF)">
<option <?php if($dados_stm["timezone"] == "Pacific/Wallis") { echo 'selected="selected"'; } ?> value="Pacific/Wallis">Wallis</option>
</optgroup>
<optgroup label="Z�mbia (ZM)">
<option <?php if($dados_stm["timezone"] == "Africa/Lusaka") { echo 'selected="selected"'; } ?> value="Africa/Lusaka">Lusaka</option>
</optgroup>
<optgroup label="Zimb�bue (ZW)">
<option <?php if($dados_stm["timezone"] == "Africa/Harare") { echo 'selected="selected"'; } ?> value="Africa/Harare">Harare</option>
</optgroup>
</select>            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_painel_formato_data']; ?></td>
            <td align="left" class="texto_padrao_vermelho">
            <select name="formato_data" class="input" id="formato_data" style="width:255px;">
          		<option value="d/m/Y H:i:s"<?php if($dados_stm["formato_data"] == "d/m/Y H:i:s") { echo ' selected="selected"'; } ?>>DD/MM/YYYY</option>
		  		<option value="m/d/Y H:i:s"<?php if($dados_stm["formato_data"] == "m/d/Y H:i:s") { echo ' selected="selected"'; } ?>>MM/DD/YYYY</option>
          		<option value="Y-m-d H:i:s"<?php if($dados_stm["formato_data"] == "Y-m-d H:i:s") { echo ' selected="selected"'; } ?>>YYYY-MM-DD</option>		  
         	</select>&nbsp;<?php echo formatar_data($dados_stm["formato_data"], date("Y-m-d H:i:s"), $dados_stm["timezone"]); ?>         </td>
         </tr>
        </table>
   	  </div>
      <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_config_painel_aba_atalhos']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_painel_exibir_atalhos']; ?></td>
            <td width="540" align="left" class="texto_padrao"><input name="exibir_atalhos" type="radio" value="sim" style="vertical-align:middle"<?php if($dados_stm["exibir_atalhos"] == "sim") { echo ' checked="checked"'; } ?> />
              &nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;
              <input name="exibir_atalhos" type="radio" value="nao" style="vertical-align:middle"<?php if($dados_stm["exibir_atalhos"] == "nao") { echo ' checked="checked"'; } ?> />
              &nbsp;<?php echo $lang['lang_label_nao']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_painel_exibir_atalhos_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="160" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_painel_atalhos']; ?></td>
            <td align="left" class="texto_padrao">
              <table width="540" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="215" align="center" scope="col">
                  <select name="menus" id="menus" multiple="multiple" style="width:215px; height:150px" onDblClick="moveSelectedOptions(this.form['menus'],this.form['atalhos'],false)">
                	<optgroup label="<?php echo $lang['lang_acao_label_streaming']; ?>">
                    <option value='streaming-informacoes|lang_acao_stm_info'><?php echo $lang['lang_acao_stm_info']; ?></option>
				    <option value='streaming-dados-conexao|lang_acao_stm_dados_conexao'><?php echo $lang['lang_acao_stm_dados_conexao']; ?></option>
					<option value='streaming-configurar|lang_acao_stm_config'><?php echo $lang['lang_acao_stm_config']; ?></option>
					<option value='streaming-players|lang_acao_stm_players'><?php echo $lang['lang_acao_stm_players']; ?></option>
					</optgroup>
					<optgroup label="<?php echo $lang['lang_acao_label_espectadores']; ?>">
					<option value='espectadores-espectadores-conectados|lang_acao_espectadores_espectadores_conectados'><?php echo $lang['lang_acao_espectadores_espectadores_conectados']; ?></option>
					<option value='espectadores-estatisticas|lang_acao_espectadores_stats'><?php echo $lang['lang_acao_espectadores_stats']; ?></option>
					</optgroup>
					<?php if($dados_stm["aplicacao"] == 'tvstation') { ?>
                    <optgroup label="<?php echo $lang['lang_acao_label_ondemand']; ?>">
					<option value='ondemand-gerenciar-videos|lang_acao_ondemand_gerenciar_videos'><?php echo $lang['lang_acao_ondemand_gerenciar_videos']; ?></option>
					<option value='ondemand-gerenciar-playlists|lang_acao_ondemand_gerenciar_playlists'><?php echo $lang['lang_acao_ondemand_gerenciar_playlists']; ?></option>
					<option value='ondemand-gerenciar-agendamentos|lang_acao_ondemand_gerenciar_agendamentos'><?php echo $lang['lang_acao_ondemand_gerenciar_agendamentos']; ?></option>
					<option value='ondemand-gerenciar-comerciais|lang_acao_ondemand_gerenciar_comerciais'><?php echo $lang['lang_acao_ondemand_gerenciar_comerciais']; ?></option>
					</optgroup>
                    <?php } ?>
                    <?php if($dados_revenda["stm_exibir_tutoriais"] == 'sim') { ?>
					<optgroup label="<?php echo $lang['lang_acao_label_painel']; ?>">
					<option value='painel-ajuda|lang_acao_painel_ajuda'><?php echo $lang['lang_acao_painel_ajuda']; ?></option>
					</optgroup>
                    <?php } ?>
                    <?php if($dados_stm["aplicacao"] == 'tvstation') { ?>
					<optgroup label="<?php echo $lang['lang_acao_label_ferramentas']; ?>">
					<option value='ferramentas-renomear-videos|lang_acao_ferramentas_renomear_videos'><?php echo $lang['lang_acao_ferramentas_renomear_videos']; ?></option>
					</optgroup>
                    <?php } ?>
					<optgroup label="<?php echo $lang['lang_acao_label_solucao_problemas']; ?>">
					<option value='solucao-problemas-sincronizar|lang_acao_solucao_problemas_sincronizar'><?php echo $lang['lang_acao_solucao_problemas_sincronizar']; ?></option>
                    <?php if($dados_stm["aplicacao"] == 'tvstation') { ?>
                    <option value='solucao-problemas-sincronizar-playlists|lang_acao_solucao_problemas_sincronizar_playlist'><?php echo $lang['lang_acao_solucao_problemas_sincronizar_playlist']; ?></option>
                    <?php } ?>
					<option value='solucao-problemas-player-facebook|lang_acao_solucao_problemas_player_facebook'><?php echo $lang['lang_acao_solucao_problemas_player_facebook']; ?></option>
					</optgroup>
           		  </select>                  </td>
                  <td width="50" align="center" scope="col">
                  <input type="Button" onClick="moveSelectedOptions(this.form['menus'],this.form['atalhos'],false)" value="&#8594;" style="background: #FFFFFF; border:solid 1px #CCCCCC; height:27px; padding:5px; cursor:pointer; color: #000000; font-family: Geneva, Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; width:30px"><br /><br />
                  <input type="Button" onClick="moveSelectedOptions(this.form['atalhos'],this.form['menus'],false)" value="&#8592;" style="background: #FFFFFF; border:solid 1px #CCCCCC; height:27px; padding:5px; cursor:pointer; color: #000000; font-family: Geneva, Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; width:30px">                  </td>
                  <td width="215" align="center" scope="col">
                    <select name="atalhos[]" id="atalhos" size="10"  multiple="multiple" onDblClick="moveSelectedOptions(this.form['atalhos'],this.form['menus'],false)" style="width:215px; height:150px">
                    <?php
					$sql_atalhos = mysql_query("SELECT * FROM video.atalhos where codigo_stm = '".$dados_stm["codigo"]."' ORDER by ordem ASC");
					while ($dados_atalhos = mysql_fetch_array($sql_atalhos)) {
						echo '<option value="' . $dados_atalhos["menu"] . '|' . $dados_atalhos["lang"] . '">' . $lang[''.$dados_atalhos["lang"].''] . '</option>';
					}
					?>
           		    </select>
                    </td>
                  <td width="60" align="center" scope="col"><input type="Button" onClick="moveOptionUp(this.form['atalhos'])" value="&#8593;" style="background: #FFFFFF; border:solid 1px #CCCCCC; height:27px; padding:5px; cursor:pointer; color: #000000; font-family: Geneva, Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; width:30px">
                    <br />
                    <br />
                  <input type="Button" class="botao_padrao" onClick="moveOptionDown(this.form['atalhos'])" value="&#8595;" style="background: #FFFFFF; border:solid 1px #CCCCCC; height:27px; padding:5px; cursor:pointer; color: #000000; font-family: Geneva, Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; width:30px"></td>
                </tr>
              </table>
              </td>
          </tr>
        </table>
   	  </div>
      <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_config_painel_aba_aparencia']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_painel_exibir_stats_espectadores']; ?></td>
            <td width="540" align="left" class="texto_padrao"><input name="aparencia_exibir_stats_espectadores" type="radio" value="sim" style="vertical-align:middle"<?php if($dados_stm["aparencia_exibir_stats_espectadores"] == "sim") { echo ' checked="checked"'; } ?> />
              &nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;
              <input name="aparencia_exibir_stats_espectadores" type="radio" value="nao" style="vertical-align:middle"<?php if($dados_stm["aparencia_exibir_stats_espectadores"] == "nao") { echo ' checked="checked"'; } ?> />
              &nbsp;<?php echo $lang['lang_label_nao']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_painel_exibir_stats_espectadores_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_painel_exibir_stats_ftp']; ?></td>
            <td width="540" align="left" class="texto_padrao"><input name="aparencia_exibir_stats_ftp" type="radio" value="sim" style="vertical-align:middle"<?php if($dados_stm["aparencia_exibir_stats_ftp"] == "sim") { echo ' checked="checked"'; } ?> />
              &nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;
              <input name="aparencia_exibir_stats_ftp" type="radio" value="nao" style="vertical-align:middle"<?php if($dados_stm["aparencia_exibir_stats_ftp"] == "nao") { echo ' checked="checked"'; } ?> />
              &nbsp;<?php echo $lang['lang_label_nao']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_painel_exibir_stats_ftp_info']; ?>');" style="cursor:pointer" /></td>
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
<!-- In�cio div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>