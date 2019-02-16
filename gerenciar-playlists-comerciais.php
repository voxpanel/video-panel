<?php
require_once("admin/inc/protecao-final.php");

if($_POST["codigo_playlist"]) {
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".code_decode($_POST["codigo_playlist"],"D")."'"));
} else {
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".code_decode(query_string('1'),"D")."'"));
}

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($_POST["cadastrar"]) {

if($_POST["pasta_comerciais"] != "") {

$pasta = $_POST["pasta_comerciais"];

$xml_comerciais = @simplexml_load_file("http://".$dados_servidor["ip"].":55/listar-videos.php?login=".$dados_stm["login"]."&pasta=".$_POST["pasta_comerciais"]."&ordenar=nao");
	
$total_comerciais = count($xml_comerciais->video);

if($total_comerciais > 0) {

	for($i=0;$i<$total_comerciais;$i++){
	
		$path_separacao = ($pasta == "/" || $pasta == "") ? "" : "/";
		
		if($xml_comerciais->video[$i]->bitrate < $dados_stm["bitrate"]) { // Verifica limite bitrate
		
		if(!preg_match('/[^A-Za-z0-9\_\-\. ]/',$xml_comerciais->video[$i]->nome)) { // Verifica caracteres especiais nome video
	
		$array_comerciais[] = $pasta.$path_separacao.utf8_decode($xml_comerciais->video[$i]->nome)."|".utf8_decode($xml_comerciais->video[$i]->nome)."|".$xml_comerciais->video[$i]->width."|".$xml_comerciais->video[$i]->height."|".$xml_comerciais->video[$i]->bitrate."|".$xml_comerciais->video[$i]->duracao."|".$xml_comerciais->video[$i]->duracao_segundos."|".$xml_comerciais->video[$i]->thumb;
		}
		
		}
	}
}

}

// Comerciais
if(count($array_comerciais) > 0 && $_POST["pasta_comerciais"] != "") {

// Remove os comerciais atuais
mysql_query("DELETE FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'comercial'");

$total_comerciais = count($array_comerciais);

$contador_videos = 1;
$contador_insercoes = 0;
$contador_comerciais_inseridos = 0;

$query = mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'video' ORDER by ordem+0 ASC");
while ($dados_video = mysql_fetch_array($query)) {

if($contador_videos == $_POST["frequencia_comerciais2"]) {

for($i=1; $i < $_POST["frequencia_comerciais"]+1; $i++) {

$contador_comerciais_inseridos++;

$ordem = "".$dados_video["ordem"].".".$i."";

list($path, $comercial, $width, $height, $bitrate, $duracao, $duracao_segundos, $thumb) = explode("|",$array_comerciais[$contador_insercoes]);

$path = str_replace("%20"," ",$path);
$video = str_replace("%20"," ",$video);

mysql_query("INSERT INTO video.playlists_videos (codigo_playlist,path_video,video,width,height,bitrate,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".addslashes($path)."','".addslashes($comercial)."','".$width."','".$height."','".$bitrate."','".$duracao."','".$duracao_segundos."','comercial','".$ordem."')");

if($contador_comerciais_inseridos == $total_comerciais) {
$contador_insercoes = 0;
$contador_comerciais_inseridos = 0;
} else {
$contador_insercoes++;
}

}

$contador_videos = 0;
}

$contador_videos++;
}

// Marca como ativado na playlist
mysql_query("Update video.playlists set comerciais = 'sim' where codigo = '".$dados_playlist["codigo"]."'");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_info_gerenciador_playlists_comerciais_resultado_comerciais_ok']."","ok");

}

header("Location: /playlists");
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
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo-pequeno">
<form method="post" action="/gerenciar-playlists-comerciais/<?php echo code_decode($dados_playlist["codigo"],"E"); ?>" style="padding:0px; margin:0px" name="hora-certa">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_playlists_comerciais_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
      <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/img/icones/ajuda.gif" width="16" height="16" /></td>
        <td width="660" align="left" class="texto_padrao" scope="col"><span class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_comerciais_info']; ?></span></td>
      </tr>
    </table>
    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
<?php if(query_string('1') == "") { ?>
      <tr>
        <td width="180" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_playlists_comerciais_playlist']; ?></td>
        <td width="510" align="left" class="texto_padrao">
          <select name="codigo_playlist" class="input" id="codigo_playlist" style="width:410px;">
<?php

$query = mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by nome ASC");
while ($dados_playlist_gerenciar = mysql_fetch_array($query)) {

$total_videos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist_gerenciar["codigo"]."'"));

$playlist_code = code_decode($dados_playlist_gerenciar["codigo"],"E");

echo '<option value="' . $playlist_code . '">' . $dados_playlist_gerenciar["nome"] . ' (' . $total_videos . ')</option>';


}
?>
            </select>          </td>
      </tr>
<?php } ?>
        <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_playlists_comerciais_comerciais']; ?></td>
        <td align="left" class="texto_padrao">
          <select name="pasta_comerciais" class="input" id="pasta_comerciais" style="width:410px;">
          <option value="" selected="selected"><?php echo $lang['lang_info_gerenciador_playlists_comerciais_opcao_selecionar_comerciais']; ?></option>
          <optgroup label="<?php echo $lang['lang_info_gerenciador_playlists_comerciais_opcao_pastas']; ?>">
<?php
$xml_pastas = @simplexml_load_file("http://".$dados_servidor["ip"].":55/listar-pastas.php?login=".$dados_stm["login"]."");
	
$total_pastas = count($xml_pastas->pasta);

if($total_pastas > 0) {

	for($i=0;$i<$total_pastas;$i++){
	
		echo '<option value="' . $xml_pastas->pasta[$i]->nome . '">' . $xml_pastas->pasta[$i]->nome . ' (' . $xml_pastas->pasta[$i]->total . ')</option>';
	
	}
	
}
?>
		  </optgroup>
          </select>          </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
        <td align="left" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_comerciais_executar']; ?>&nbsp;
          <input name="frequencia_comerciais" type="text" class="input" id="frequencia_comerciais" style="width:50px;" value="0" />
        &nbsp;<?php echo $lang['lang_info_gerenciador_playlists_comerciais_a_cada']; ?>&nbsp;
        <input name="frequencia_comerciais2" type="text" class="input" id="frequencia_comerciais2" style="width:50px;" value="10" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_comerciais_frequencia_video']; ?>
        <img src="/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_gerenciador_playlists_comerciais_frequencia_info']; ?>');" style="cursor:pointer" /></td>
      </tr>
      
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_configurar']; ?>" />&nbsp;<input type="button" class="botao" value="<?php echo $lang['lang_botao_titulo_voltar']; ?>" onclick="window.location = '/playlists';" />
          <input name="cadastrar" type="hidden" id="cadastrar" value="sim" />          </td>
      </tr>
    </table>
    </div>
    </div>
  </form>
  <br />
        <div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_instrucoes_tab_titulo']; ?></strong></div>
          <div class="texto_medio" id="quadro-conteudo">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td height="25" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_playlists_comerciais_instrucoes']; ?></td>
                </tr>
              </table>
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
