<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ftp.php");
require_once("admin/inc/classe.ssh.php");

$login_code = code_decode($_SESSION["login_logado"],"E");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas where codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_playlist_selecionada = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".code_decode(query_string('1'),"D")."'"));

// Salva a Playlist
if($_POST["playlist"]) {

$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".$_POST["playlist"]."'"));

// Remove as vídeos atuais da playlist para gravar as novas vídeo
mysql_query("DELETE FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'");

if(count($_POST["videos_adicionados"]) > 0) {

// Adiciona as videos da playlist ao banco de dados
foreach($_POST["videos_adicionados"] as $ordem => $video) {

list($path, $video, $width, $height, $bitrate, $duracao, $duracao_segundos, $tipo) = explode("|",$video);

$path = str_replace("%20"," ",$path);
$video = str_replace("%20"," ",$video);

// Adiciona vídeo na playlist
mysql_query("INSERT INTO video.playlists_videos (codigo_playlist,path_video,video,width,height,bitrate,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".addslashes($path)."','".addslashes($video)."','".$width."','".$height."','".$bitrate."','".$duracao."','".$duracao_segundos."','".$tipo."','".$ordem."')") or die("Ooops! Ocorreu um erro no mysql: ".mysql_error());

}

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_playlists_resultado_ok']."","ok");

// Inicia a playlist
if($_POST["iniciar_playlist"] == "sim") {

// Gera o arquivo com as playlists e envia para o FTP do streaming
// Carrega as playlists agendadas
$total_agendamentos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."'"));

if($total_agendamentos > 0) {

$query_agendamentos = mysql_query("SELECT * FROM video.playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
while ($dados_agendamento = mysql_fetch_array($query_agendamentos)) {

$data_original = $dados_agendamento["data"]." ".$dados_agendamento["hora"].":".$dados_agendamento["minuto"].":00";

if(strtotime($data_original) > time()) {

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
$config_playlist[$playlist]["total_videos"] = $total_videos_playlist;
$config_playlist[$playlist]["videos"] = substr($lista_videos,0,-1);

unset($lista_videos);
}

}

} // while

// Carrega a playlist que será iniciada agora

$playlist = "001_".formatar_nome_playlist($dados_playlist_selecionada["nome"]);

$total_videos_playlist = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist_selecionada["codigo"]."'"));

if($total_videos_playlist > 0) {

$query_videos = mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist_selecionada["codigo"]."' ORDER by ordem+0,codigo ASC");
while ($dados_playlist_video = mysql_fetch_array($query_videos)) {
$lista_videos .= $dados_playlist_video["path_video"].",";
}

$data_inicio = date("Y-m-d H:i:s");

$config_playlist[$playlist]["playlist"] = "001_".formatar_nome_playlist($dados_playlist_selecionada["nome"]);
$config_playlist[$playlist]["data_inicio"] = $data_inicio;
$config_playlist[$playlist]["total_videos"] = $total_videos_playlist;
$config_playlist[$playlist]["videos"] = substr($lista_videos,0,-1);

unset($lista_videos);
}

} else { // Sem agendamentos

// Carrega a playlist que será iniciada agora

$playlist = "001_".formatar_nome_playlist($dados_playlist_selecionada["nome"]);

$total_videos_playlist = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist_selecionada["codigo"]."'"));

if($total_videos_playlist > 0) {

$query_videos = mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist_selecionada["codigo"]."' ORDER by ordem+0,codigo ASC");
while ($dados_playlist_video = mysql_fetch_array($query_videos)) {
$lista_videos .= $dados_playlist_video["path_video"].",";
}

$data_inicio = date("Y-m-d H:i:s");

$config_playlist[$playlist]["playlist"] = "001_".formatar_nome_playlist($dados_playlist_selecionada["nome"]);
$config_playlist[$playlist]["data_inicio"] = $data_inicio;
$config_playlist[$playlist]["total_videos"] = $total_videos_playlist;
$config_playlist[$playlist]["videos"] = substr($lista_videos,0,-1);

unset($lista_videos);

}

} // Fim checagem agendamentos

$array_config_playlists = array ("login" => $dados_stm["login"], "playlists" => $config_playlist);

$resultado = gerar_playlist($array_config_playlists);

// Envia via FTP
// Conexão FTP
$ftp = new FTP();
$ftp->conectar($dados_servidor["ip"]);
$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);

if($ftp->enviar_arquivo("temp/".$resultado."","playlists_agendamentos.smil")) {

@unlink("temp/".$resultado."");

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");

$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance  ".$dados_stm["login"]."");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_iniciar_playlist_stm_resultado_ok']."","ok");

} else {
// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_iniciar_playlist_stm_resultado_erro']."","erro");
}

}

}

header("Location: /playlists");
exit();

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gerenciar Playlists</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="/inc/ajax-streaming-playlists.js"></script>
<script type="text/javascript">
   window.onload = function() {
    carregar_pastas('<?php echo $login_code; ?>');
	carregar_videos_playlist( '<?php echo $dados_playlist_selecionada["codigo"]; ?>' );
	fechar_log_sistema();
   };
   window.onkeydown = function (event) {
		if (event.keyCode == 27) {
			document.getElementById('log-sistema-fundo').style.display = 'none';
			document.getElementById('log-sistema').style.display = 'none';
		}
	}
</script>
</head>

<body> 
<div id="sub-conteudo">
  <form method="post" action="/gerenciar-playlists/<?php echo query_string('1'); ?>" style="padding:0px; margin:0px" name="gerenciador" enctype="multipart/form-data">
    <table width="890" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-top:10px; margin-bottom:10px;">
      <tr>
        <td width="217" scope="col"><table width="200" border="0" align="left" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_playlist_atual']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao" scope="col"><?php echo $dados_playlist_selecionada["nome"]; ?></td>
          </tr>
        </table></td>
        <td width="230" align="center" scope="col"><table width="200" border="0" align="center" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_playlists']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_titulo" scope="col">
            <select name="gerenciar_playlist" class="input" id="gerenciar_playlist" style="width:190px;" onchange="abrir_log_sistema();window.location = '/gerenciar-playlists/'+this.value+'';">
            <optgroup label="<?php echo $lang['lang_info_gerenciador_playlists_playlists']; ?>">
              <?php

$query = mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by nome ASC");
while ($dados_playlist_gerenciar = mysql_fetch_array($query)) {

$total_videos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist_gerenciar["codigo"]."'"));

$playlist_gerenciar_code = code_decode($dados_playlist_gerenciar["codigo"],"E");

if($dados_playlist_gerenciar["codigo"] == code_decode(query_string('1'),"D")) {
echo '<option value="' . $playlist_gerenciar_code . '" selected="selected">' . $dados_playlist_gerenciar["nome"] . ' (' . $total_videos . ')</option>';
} else {
echo '<option value="' . $playlist_gerenciar_code . '">' . $dados_playlist_gerenciar["nome"] . ' (' . $total_videos . ')</option>';

}

}
?>
            </optgroup>
            </select>
            </td>
          </tr>
        </table></td>
        <td width="230" align="center" scope="col"><table width="200" border="0" align="center" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;" id="quadro_quantidade_videos_playlist">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_videos_playlist']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_titulo" scope="col"><span id="quantidade_videos_playlist">0</span></td>
          </tr>
        </table></td>
        <td width="217" align="center" scope="col"><table width="200" border="0" align="right" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_tempo_execucao']; ?>
                <input name="tempo" type="hidden" id="tempo" value="0" /></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_titulo" scope="col"><span id="tempo_playlist">00:00:00</span></td>
          </tr>
        </table></td>
      </tr>
    </table>
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="2"><div style="width:272px; text-align:left; float:left; padding:5px 0px 5px 5px; overflow: auto;" class="texto_padrao_destaque"> <?php echo $lang['lang_info_gerenciador_playlists_pastas']; ?> </div>
            <div style="width:20px; text-align:left; float:left; padding:5px 0px 5px 5px; overflow: auto;" class="texto_padrao_destaque"> <img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" align="absmiddle" onclick="hide_show('quadro_pastas_videos_ftp');" style="cursor:pointer" title="Ocultar/Hide" /> </div>
          <div style="width:372px; text-align:right; float:right; padding:5px 0px 5px 0px; overflow: auto;" class="texto_padrao_vermelho">
              <input name="ordenar_videos_pasta" id="ordenar_videos_pasta" type="checkbox" value="sim" style="vertical-align: middle;" checked="checked" />
            &nbsp;<?php echo $lang['lang_info_gerenciador_playlists_videos_pasta_ordenar']; ?> </div>
          <div style="width:200px; text-align:left; float:right; padding:5px 0px 5px 0px; overflow: auto;" class="texto_padrao_destaque"> <?php echo $lang['lang_info_gerenciador_playlists_videos_pasta']; ?> </div></td>
      </tr>
      <tr>
        <td colspan="2" align="left" valign="top"><div id="quadro_pastas_videos_ftp" style="display:block">
            <div id="quadro_lista_pastas" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:285px; height:200px; text-align:left; float:left; padding:5px; overflow: auto; resize: vertical"> <span id="status_lista_pastas" class="texto_padrao_pequeno"></span>
                <ul id="lista-pastas">
                </ul>
            </div>
          <div id="videos_ftp" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:560px; height:200px; text-align:left; float:right; padding:5px; overflow: auto; resize: vertical"> <span id="msg_pasta" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_playlists_info_lista_videos_pasta']; ?></span>
                <ul id="lista-videos-pasta">
                </ul>
          </div>
          <div style="width:297px; text-align:right; float:left; padding:5px 0px 5px 0px; overflow: auto;"> <img src="/img/icones/img-icone-atualizar.png" width="16" height="16" align="absmiddle" border="0" />&nbsp;<a href="javascript:carregar_pastas('<?php echo $login_code; ?>');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_recarregar_pastas']; ?></a></div>
          <div style="width:572px; text-align:right; float:right; padding:5px 0px 5px 0px; overflow: auto;"> <img src="/img/icones/img-icone-pasta-adicionar.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:adicionar_tudo();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_adicionar_tudo']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-lixo.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:limpar_lista_videos('ftp');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_limpar_lista']; ?></a></div>
        </div></td>
      </tr>
      
      <tr>
        <td colspan="2"><div style="width:883px; text-align:left; float:left; padding:5px 0px 9px 5px; overflow: auto;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_playlists_videos_playlist']; ?></div>
        <div id="videos_playlist" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:877px; height:550px; text-align:left; float:left; padding:5px; overflow: auto; resize: vertical">
        <span id="msg_playlist" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_playlists_info_lista_videos_playlists']; ?></span>
        <span id="msg_playlist_nova" class="texto_padrao_pequeno" style="display:none"><?php echo $lang['lang_info_gerenciador_playlists_info_lista_videos_playlist_nova']; ?></span>
        <ul id="lista-videos-playlist">
        </ul>
        </div>
        </td>
      </tr>
      <tr>
        <td width="310" height="30" align="left"><img src="/img/icones/img-icone-atualizar.png" width="16" height="16" align="absmiddle" border="0" />&nbsp;<a href="javascript:carregar_videos_playlist( '<?php echo $dados_playlist_selecionada["codigo"]; ?>' );" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_recarregar_playlists']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-janela-64x64.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:abrir_janela('/gerenciar-playlists/<?php echo query_string('1'); ?>',920,650 );" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_nova_janela']; ?></a></td>
        <td width="580" height="30" align="right"><img src="/img/icones/img-icone-salvar.png" width="16" height="16" align="absmiddle" onclick="misturar_videos('lista-videos-playlist');" />&nbsp;<a href="javascript:salvar_playlist();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_salvar_playlist']; ?></a>&nbsp;&nbsp;<a href="#" onclick="document.getElementById('iniciar_playlist').value = 'sim';salvar_playlist();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_salvar_playlist_iniciar']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-shuffle-64x64.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:misturar_videos('lista-videos-playlist');" class="texto_padrao_vermelho"><?php echo $lang['lang_info_gerenciador_playlists_botao_misturar_videos']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-lixo.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:limpar_lista_videos('playlist');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_limpar_lista']; ?></a>
        <input name="playlist" type="hidden" id="playlist" value="<?php echo $dados_playlist_selecionada["codigo"]; ?>" />
        <input name="iniciar_playlist" type="hidden" id="iniciar_playlist" value="nao" />
        </td>
      </tr>
    </table>
  </form>
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; margin-bottom:10px; margin-left:0 auto; margin-right:0 auto; border: #CCCCCC 1px solid">
    <tr>
      <td width="160" height="25" align="left" class="texto_padrao_pequeno" scope="col" style="border-right:#CCCCCC 1px solid; padding-left:5px"><img src="/img/icones/img-icone-arquivo-video.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_legenda_video']; ?></td>
      <td width="160" align="left" class="texto_padrao_pequeno" style="border-right:#CCCCCC 1px solid; padding-left:5px" scope="col"><img src="/img/icones/img-icone-vinheta.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_legenda_comercial']; ?></td>
      <td width="280" align="left" class="texto_padrao_pequeno" scope="col" style="padding-left:5px"><img src="/img/icones/img-icone-bloqueado.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_legenda_bloqueado']; ?></td>
    </tr>
  </table>
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:20px; background-color:#FFFF66; border:#DFDF00 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="660" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_info_caracteres_especiais']; ?></td>
  </tr>
</table>
<br />
<br />
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';document.getElementById('log-sistema-conteudo').innerHTML = '';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>