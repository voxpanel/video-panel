<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

// Verifica se app esta habilitado, se não estiver manda pra pagina inicial
if($dados_stm["exibir_app_android"] == 'nao') {
header("Location: /informacoes");
exit();
}

$dados_app_criado = mysql_fetch_array(mysql_query("SELECT * FROM video.apps where codigo_stm = '".$dados_stm["codigo"]."'"));

$login_code = code_decode($dados_stm["login"],"E");

if(isset($_POST["enviar"])) {

if(isset($dados_app_criado["codigo"]) && $dados_app_criado["status"] < 2) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_app_existente']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

// Remove a requisição atual
mysql_query("Delete From video.apps where codigo = '".$dados_app_criado["codigo"]."' AND status = '2'");

require_once("inc/wideimage/WideImage.php");

// Valida extensão
if($_FILES["logo"]["type"] != "image/png") {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_formato_logo']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($_FILES["icone"]["type"] != "image/png") {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_formato_icone']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($_POST["app_modelo"] == "personalizado") {

if($_FILES["icone_play"]["type"] != "image/png") {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_formato_icone']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($_FILES["icone_site"]["type"] != "image/png") {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_formato_icone']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($_FILES["icone_facebook"]["type"] != "image/png") {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_formato_icone']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

}

if(strlen($_POST["tv_nome"]) > 30) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_max_caracter_nome_tv']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if(empty($_POST["tv_nome"])) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_campo_vazio_nome_tv']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if(empty($_POST["tv_site"])) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_campo_vazio_url_site']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if(!filter_var($_POST["tv_site"], FILTER_VALIDATE_URL)) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_invalido_url_site']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if(!filter_var($_POST["tv_facebook"], FILTER_VALIDATE_URL) && !empty($_POST["tv_facebook"])) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_invalido_url_facebook']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

// Verifica se o primeiro caracter é numérico
if(preg_match('/^\d/',$_POST["tv_nome"])) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_nome_tv_numero']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

$tv_nome = $_POST["tv_nome"];

$source = $_POST["app_modelo"];

$endereco_site = str_replace("http://","",$_POST["tv_site"]);
$endereco_site = "http://".$endereco_site;

if(isset($_POST["tv_facebook"])){
$endereco_facebook = str_replace("http://","",$_POST["tv_facebook"]);
$endereco_facebook = str_replace("https://","",$endereco_facebook);
$endereco_facebook = "https://".$endereco_facebook;
}

$endereco_facebook = (!isset($_POST["tv_facebook"]) || $endereco_facebook == 'https://') ? $endereco_site : $endereco_facebook;

$hash = nome_app_play($tv_nome)."_".md5($tv_nome);
$package = "com.stmvideo.webtv.".nome_app_play($tv_nome)."";
$package_path = str_replace(".","/",$package);

$verifica_package = mysql_num_rows(mysql_query("SELECT * FROM video.apps where package = '".$package."'"));

if($verifica_package > 0) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_nome_tv_existente']."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

$servidor_stm = strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"];

$patch_dir_apps = "app_android/apps";
$patch_app = "app_android/apps/".$hash."";
$patch_tmp = "app_android/apps/tmp";

@copy($_FILES["logo"]["tmp_name"],"".$patch_tmp."/logo_".$hash.".png");
@copy($_FILES["icone"]["tmp_name"],"".$patch_tmp."/icone_".$hash.".png");

if($_POST["app_modelo"] == "personalizado") {
@copy($_FILES["icone_play"]["tmp_name"],"".$patch_tmp."/icone_play_".$hash.".png");
@copy($_FILES["icone_site"]["tmp_name"],"".$patch_tmp."/icone_site_".$hash.".png");
@copy($_FILES["icone_facebook"]["tmp_name"],"".$patch_tmp."/icone_facebook_".$hash.".png");
}

// Valida a dimensão(largura x altura) das imagens
list($logo_width, $logo_height, $logo_type, $logo_attr) = getimagesize("".$patch_tmp."/logo_".$hash.".png");
list($icone_width, $icone_height, $icone_type, $icone_attr) = getimagesize("".$patch_tmp."/icone_".$hash.".png");

if($_POST["app_modelo"] == "personalizado") {
list($icone_play_width, $icone_play_height, $icone_play_type, $icone_play_attr) = getimagesize("".$patch_tmp."/icone_play_".$hash.".png");
list($icone_site_width, $icone_site_height, $icone_site_type, $icone_site_attr) = getimagesize("".$patch_tmp."/icone_site_".$hash.".png");
list($icone_facebook_width, $icone_facebook_height, $icone_facebook_type, $icone_facebook_attr) = getimagesize("".$patch_tmp."/icone_facebook_".$hash.".png");
}

if($logo_width != 235 || $logo_height != 235) {
die ("<script> alert(\"Ooops!\\n\\nA logomarca esta com dimensão inválida!\\n\\nEnvie uma logomarca com 235 pixels de largura e 235 pixels de altura.\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($icone_width != 144 || $icone_height != 144) {
die ("<script> alert(\"Ooops!\\n\\nO ícone esta com dimensão inválida!\\n\\nEnvie um ícone com 144 pixels de largura e 144 pixels de altura.\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($_POST["app_modelo"] == "personalizado") {

if($icone_play_width != 128 || $icone_play_height != 128) {
die ("<script> alert(\"Ooops!\\n\\nO ícone do play esta com dimensão inválida!\\n\\nEnvie um ícone com 128 pixels de largura e 128 pixels de altura.\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($icone_site_width != 64 || $icone_site_height != 64) {
die ("<script> alert(\"Ooops!\\n\\nO ícone do site esta com dimensão inválida!\\n\\nEnvie um ícone com 64 pixels de largura e 64 pixels de altura.\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($icone_facebook_width != 64 || $icone_facebook_height != 64) {
die ("<script> alert(\"Ooops!\\n\\nO ícone do facebook esta com dimensão inválida!\\n\\nEnvie um ícone com 64 pixels de largura e 64 pixels de altura.\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}
}

// Copia o source do app para o novo app
copiar_source("app_android/".$source."/", $patch_app);

// Muda nome do package do source para o nome do package da radio
@rename("".$patch_app."/src/com/stmvideo/webtv/tv_nome","".$patch_app."/src/com/stmvideo/webtv/".nome_app_play($tv_nome)."");

// Copia os icones personalizados
if($_POST["app_modelo"] == "personalizado") {
@copy("".$patch_tmp."/icone_play_".$hash.".png","".$patch_app."/assets/img-icone-play.png");
@copy("".$patch_tmp."/icone_site_".$hash.".png","".$patch_app."/assets/img-icone-site.png");
@copy("".$patch_tmp."/icone_facebook_".$hash.".png","".$patch_app."/assets/img-icone-facebook.png");
}

// Copia o ícone
$icone = WideImage::load("".$patch_tmp."/icone_".$hash.".png");
$icone = $icone->resize(144, 144);
$icone->saveToFile("".$patch_app."/res/drawable-xxhdpi/ic_launcher.png");

$icone = WideImage::load("".$patch_tmp."/icone_".$hash.".png");
$icone = $icone->resize(96, 96);
$icone->saveToFile("".$patch_app."/res/drawable-xhdpi/ic_launcher.png");

$icone = WideImage::load("".$patch_tmp."/icone_".$hash.".png");
$icone = $icone->resize(72, 72);
$icone->saveToFile("".$patch_app."/res/drawable-hdpi/ic_launcher.png");

$icone = WideImage::load("".$patch_tmp."/icone_".$hash.".png");
$icone = $icone->resize(48, 48);
$icone->saveToFile("".$patch_app."/res/drawable-mdpi/ic_launcher.png");

$icone = WideImage::load("".$patch_tmp."/icone_".$hash.".png");
$icone = $icone->resize(36, 36);
$icone->saveToFile("".$patch_app."/res/drawable-ldpi/ic_launcher.png");

// Copia a logo
$logo = WideImage::load("".$patch_tmp."/logo_".$hash.".png");
$logo = $logo->resize(235, 235);
$logo->saveToFile("".$patch_app."/res/drawable-mdpi/logo.png");

// Cria icone para o Play
$play_icone = WideImage::load("".$patch_tmp."/logo_".$hash.".png");
$play_icone = $play_icone->resize(512, 512);
$play_icone->saveToFile("".$patch_app."/arquivos_google_play/google-play-logo.png");

// Cria a imagem de destaque para o Play com a logo da radio
$destaque = WideImage::load("".$patch_app."/arquivos_google_play/img-play-destaque.jpg");
$logo_destaque = WideImage::load("".$patch_tmp."/logo_".$hash.".png");
$play_destaque = $destaque->merge($logo_destaque, 'center', 'center', 100);
$play_destaque->saveToFile("".$patch_app."/arquivos_google_play/img-play-destaque.jpg");

// Escreve nome da radio no print do app
$printapp = WideImage::load("".$patch_app."/arquivos_google_play/img-play-app1.png");
$printapp_canvas = $printapp->getCanvas();
$printapp_canvas->useFont("".$patch_app."/assets/ASansBlack.ttf", 80, $printapp->allocateColor(255, 255, 255));
$printapp_canvas->writeText("center", "90", formatar_nome_webtv($tv_nome));
$printapp->saveToFile("".$patch_app."/arquivos_google_play/img-play-app1.png");

// Insere os icones do app personalizado
if($_POST["app_modelo"] == "personalizado") {
$printapp_final_icone_play = WideImage::load("".$patch_app."/arquivos_google_play/img-play-app1.png");
$printapp_final_icone_play_icone = WideImage::load("".$patch_tmp."/icone_play_".$hash.".png");
$printapp_final_icone_play = $printapp_final_icone_play->merge($printapp_final_icone_play_icone, 'center', 'center', 100);
$printapp_final_icone_play->saveToFile("".$patch_app."/arquivos_google_play/img-play-app1.png");

$printapp_final_icone_site = WideImage::load("".$patch_app."/arquivos_google_play/img-play-app1.png");
$printapp_final_icone_site_icone = WideImage::load("".$patch_tmp."/icone_site_".$hash.".png");
$printapp_final_icone_site = $printapp_final_icone_site->merge($printapp_final_icone_site_icone, 'left', 'center', 100);
$printapp_final_icone_site->saveToFile("".$patch_app."/arquivos_google_play/img-play-app1.png");

$printapp_final_icone_facebook = WideImage::load("".$patch_app."/arquivos_google_play/img-play-app1.png");
$printapp_final_icone_facebook_icone = WideImage::load("".$patch_tmp."/icone_facebook_".$hash.".png");
$printapp_final_icone_facebook = $printapp_final_icone_facebook->merge($printapp_final_icone_facebook_icone, 'right', 'center', 100);
$printapp_final_icone_facebook->saveToFile("".$patch_app."/arquivos_google_play/img-play-app1.png");
}

// Modifica o app com os dados do streaming
replace("".$patch_app."/src/".$package_path."/SplashActivity.java","com.stmvideo.webtv.tv_nome",$package);
replace("".$patch_app."/src/".$package_path."/MainActivity.java","com.stmvideo.webtv.tv_nome",$package);
replace("".$patch_app."/AndroidManifest.xml","com.stmvideo.webtv.tv_nome",$package);

replace("".$patch_app."/res/values/strings.xml","tv_nome",$tv_nome);
replace("".$patch_app."/.project","tv_nome",formatar_nome_webtv($tv_nome));
replace("".$patch_app."/build.xml","tv_nome",nome_app_apk($tv_nome));

// Configura pagina inicial
replace("".$patch_app."/assets/index.html","tv_nome",$tv_nome);
replace("".$patch_app."/assets/index.html","URL_SITE",$endereco_site);
replace("".$patch_app."/assets/index.html","URL_FACEBOOK",$endereco_facebook);

// Configura o player
replace("".$patch_app."/assets/player.html","SERVIDOR",$servidor_stm);
replace("".$patch_app."/assets/player.html","LOGIN",$dados_stm["login"]);

if($_POST["versao"] == '1.0') {
$codigo_versao = 1;
} elseif($_POST["versao"] == '1.1') {
$codigo_versao = 2;
} elseif($_POST["versao"] == '1.2') {
$codigo_versao = 3;
} elseif($_POST["versao"] == '1.3') {
$codigo_versao = 4;
} elseif($_POST["versao"] == '1.4') {
$codigo_versao = 5;
} elseif($_POST["versao"] == '1.5') {
$codigo_versao = 6;
} elseif($_POST["versao"] == '1.6') {
$codigo_versao = 7;
} elseif($_POST["versao"] == '1.7') {
$codigo_versao = 8;
} elseif($_POST["versao"] == '1.8') {
$codigo_versao = 9;
} elseif($_POST["versao"] == '1.9') {
$codigo_versao = 10;
} elseif($_POST["versao"] == '1.10') {
$codigo_versao = 11;
} else {
$codigo_versao = 1;
}

replace("".$patch_app."/res/values/strings.xml","numero_versao",$_POST["versao"]);
replace("".$patch_app."/AndroidManifest.xml","codigo_versao",$codigo_versao);
replace("".$patch_app."/AndroidManifest.xml","numero_versao",$_POST["versao"]);

// Muda o idioma do app conforme o idioma do painel
if($dados_stm["idioma_painel"] == "pt-br") {

replace("".$patch_app."/src/".$package_path."/MainActivity.java","MSGSAIR","Deseja realmente sair?");
replace("".$patch_app."/src/".$package_path."/MainActivity.java","MSGSIM","OK");
replace("".$patch_app."/src/".$package_path."/MainActivity.java","MSGCANCELAR","Cancelar");

} elseif($dados_stm["idioma_painel"] == "en-us") {

replace("".$patch_app."/src/".$package_path."/MainActivity.java","MSGSAIR","Really want to quit?");
replace("".$patch_app."/src/".$package_path."/MainActivity.java","MSGSIM","OK");
replace("".$patch_app."/src/".$package_path."/MainActivity.java","MSGCANCELAR","Cancel");

} else {

replace("".$patch_app."/src/".$package_path."/MainActivity.java","MSGSAIR","Quieres cerrar el app?");
replace("".$patch_app."/src/".$package_path."/MainActivity.java","MSGSIM","OK");
replace("".$patch_app."/src/".$package_path."/MainActivity.java","MSGCANCELAR","Cancelar");

}

// Insere os dados no banco de dados
mysql_query("INSERT INTO video.apps (codigo_stm,tv_nome,tv_site,tv_facebook,package,data,hash) VALUES ('".$dados_stm["codigo"]."','".$tv_nome."','".$endereco_site."','".$endereco_facebook."','".$package."',NOW(),'".$hash."')") or die("<script> alert(\"Ooops!\\n\\nOcorreu um erro ao tentar inserir os dados no banco de dados!\\n\\nEntre em contato com nosso suporte.\\n\\nLog: ".mysql_error()."\"); window.location = 'javascript:history.back(-1)'; </script>");
$codigo_app = mysql_insert_id();

// Remove o source do app
@unlink("".$patch_tmp."/logo_".$hash.".png");
@unlink("".$patch_tmp."/icone_".$hash.".png");

if($_POST["app_modelo"] == "personalizado") {
@unlink("".$patch_tmp."/icone_play_".$hash.".png");
@unlink("".$patch_tmp."/icone_site_".$hash.".png");
@unlink("".$patch_tmp."/icone_facebook_".$hash.".png");
}

$dados_app_criado = mysql_fetch_array(mysql_query("SELECT * FROM video.apps where codigo = '".$codigo_app."'"));

// Compila o app
$nome_apk = nome_app_apk($dados_app_criado["tv_nome"]);

//Bug fix
remover_source_app("app_android/apps/".$dados_app_criado["hash"]."/src/com/stmvideo/webtv/tv_nome");

// Compila o App
$resultado = shell_exec("cd /home/painelvideo/public_html/app_android/apps/".$dados_app_criado["hash"].";/opt/ant/bin/ant release 2>&1");

if(preg_match('/BUILD SUCCESSFUL/i',$resultado)) {

@copy("app_android/apps/".$dados_app_criado["hash"]."/bin/".$nome_apk."-release.apk","app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/App-".$nome_apk.".apk");

// Cria o zip com o conteudo para publicação no google play
$zip = new ZipArchive();
if ($zip->open("app_android/apps/".$dados_app_criado["hash"].".zip", ZIPARCHIVE::CREATE)!==TRUE) {
    die("Não foi possível criar o arquivo ZIP: ".$dados_app_criado["hash"].".zip");
}

$zip->addEmptyDir("".$dados_app_criado["hash"]."");
$zip->addFile("app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/App-".$nome_apk.".apk","".$dados_app_criado["hash"]."/App-".$nome_apk.".apk");
$zip->addFile("app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/img-play-logo.png","".$dados_app_criado["hash"]."/img-play-logo.png");
$zip->addFile("app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/img-play-destaque.jpg","".$dados_app_criado["hash"]."/img-play-destaque.jpg");
$zip->addFile("app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/img-play-app1.png","".$dados_app_criado["hash"]."/img-play-app1.png");
$zip->addFile("app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/img-play-app2.png","".$dados_app_criado["hash"]."/img-play-app2.png");
$status=$zip->getStatusString();
$zip->close();

if(!file_exists("app_android/apps/".$dados_app_criado["hash"].".zip")) {
shell_exec("cd app_android/apps/;/usr/bin/zip -1 ".$dados_app_criado["hash"].".zip ".$dados_app_criado["hash"].";/usr/bin/zip -1 ".$dados_app_criado["hash"].".zip ".$dados_app_criado["hash"]."/arquivos_google_play/*");
}

mysql_query("Update video.apps set apk = 'App-".$nome_apk.".apk', compilado = 'sim', zip = '".$dados_app_criado["hash"].".zip', status = '1' where codigo = '".$dados_app_criado["codigo"]."'");

// Remove source
if($dados_app_criado["hash"] != "") {
remover_source_app("app_android/apps/".$dados_app_criado["hash"]."");
}

} else {

$resultado_build .= "Path: cd /home/painel/public_html/app_android/apps/".$dados_app_criado["hash"]."\n";
$resultado_build .= "Cmd: /opt/ant/bin/ant release\n";
$resultado_build .= $resultado;

mysql_query("Update video.apps set log_build = '".addslashes($resultado_build)."' where codigo = '".$dados_app_criado["codigo"]."'");

// Avisa administrador sobre a requisição do novo app
$headers = "";
$headers .= 'MIME-Version: 1.0'."\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
$headers .= 'From: Advance Host App Android <atendimento@advancehost.com.br>'."\r\n";
$headers .= 'To: atendimento@advancehost.com.br'."\r\n";
$headers .= "X-Sender: Advance Host App Android <atendimento@advancehost.com.br>\n";
$headers .= 'X-Mailer: PHP/' . phpversion();
$headers .= "X-Priority: 1\n";
$headers .= "Return-Path: atendimento@advancehost.com.br\n";

$mensagem = "";
$mensagem .= "==========================================<br>";
$mensagem .= "Nova requisição de App Android WebTV<br>";
$mensagem .= "==========================================<br>";
$mensagem .= "WebTV: ".$tv_nome."<br>";
$mensagem .= "Login: ".$dados_stm["login"]."<br>";
$mensagem .= "Log:<br>";
$mensagem .= "".$resultado_build."<br>";
$mensagem .= "==========================================";

mail("atendimento@advancehost.com.br","[APP ANDROID WebTV][".$dados_stm["login"]."] Erro ao compilar app automaticamente",$mensagem,$headers);

}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">

<?php if(isset($dados_app_criado["codigo"])) { ?>
<table width="740" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><div id="quadro">
      <div id="quadro-topo"> <strong><?php echo $lang['lang_info_streaming_app_android_tab_titulo']; ?></strong>      </div>
      <div class="texto_medio" id="quadro-conteudo">
      <table width="720" border="0" align="center" cellpadding="0" cellspacing="0" style="border:#D5D5D5 1px solid;">
    <tr style="background:url(img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="120" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_app_android_data']; ?></td>
      <td width="470" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_app_android_status']; ?></td>
      <td width="130" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_app_android_acao']; ?></td>
    </tr>
<?php
$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y %H:%i:%s') AS data FROM video.apps WHERE codigo_stm = '".$dados_stm["codigo"]."'");
while ($dados_app = mysql_fetch_array($sql)) {

$app_code = code_decode($dados_app["codigo"],"E");

if($dados_app["status"] == 1) {

$status = $lang['lang_info_streaming_app_android_requisicao_concluida'];

$acao = "<a href=\"/app_android/apps/".$dados_app["zip"]."\" target=\"_blank\">[Download]</a>&nbsp;<a href=\"javascript:executar_acao_streaming('".$app_code."','remover-app-android' );\">".$lang['lang_info_streaming_app_android_botao_remover_app']."</a>";

$cor_status = '#C6FFC6';

} elseif($dados_app["status"] == 2) {

$status = $dados_app["aviso"];
$acao = "<a href=\"javascript:executar_acao_streaming('".$app_code."','remover-app-android' );\">".$lang['lang_info_streaming_app_android_botao_remover_app']."</a>";
$cor_status = '#FFB9B9';

} else {

$status = $lang['lang_info_streaming_app_android_requisicao_em_andamento'];
$acao = "<a href=\"javascript:executar_acao_streaming('".$app_code."','remover-app-android' );\">".$lang['lang_info_streaming_app_android_botao_remover_app']."</a>";
$cor_status = '#FFFFFF';
}

echo "<tr style='background-color:".$cor_status.";'>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_app["data"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$status."</td>
<td height='25' align='center' scope='col' class='texto_padrao_pequeno'>&nbsp;".$acao."</td>
</tr>";

}
?>
  </table>
      </div>
    </div></td>
  </tr>
</table>
<br />
<?php } ?>
<?php if(!isset($dados_app_criado["codigo"]) || $dados_app_criado["status"] == 2) { ?>
<form action="/app-android" method="post" name="form" enctype="multipart/form-data">
<table width="740" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
    <div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_streaming_app_android_tab_titulo_instrucoes']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="100%" height="40" align="left" class="texto_padrao_pequeno">
      <?php echo $lang['lang_info_streaming_app_android_instrucoes_1']; ?><br />
	  <?php echo $lang['lang_info_streaming_app_android_instrucoes_2']; ?><br />
	  <?php echo $lang['lang_info_streaming_app_android_instrucoes_3']; ?><br />
	  <?php echo $lang['lang_info_streaming_app_android_instrucoes_4']; ?><br />
	  <?php echo $lang['lang_info_streaming_app_android_instrucoes_5']; ?>
      </td>
      </tr>
  </table>
  </div>
  </div>
    <br />
	<div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_streaming_app_android_tab_titulo_info_radio']; ?></strong>

              <input name="enviar" type="hidden" id="enviar" value="sim" />
            </div>
          <div class="texto_medio" id="quadro-conteudo">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="20%" height="50" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_app_android_tv_nome']; ?></td>
      <td width="80%" class="texto_padrao_pequeno">
        <input name="tv_nome" type="text" id="tv_nome" style="width:350px" value="" />
        <br />
        <?php echo $lang['lang_info_streaming_app_android_tv_nome_info']; ?></td>
    </tr>
    <tr>
      <td height="50" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_app_android_tv_site']; ?></td>
      <td class="texto_padrao_pequeno"><input name="tv_site" type="text" id="tv_site" style="width:350px" value=""/>
        <br />
        <?php echo $lang['lang_info_streaming_app_android_tv_site_info']; ?></td>
    </tr>
    <tr>
      <td height="50" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_app_android_tv_facebook']; ?></td>
      <td class="texto_padrao_pequeno"><input name="tv_facebook" type="text" id="tv_facebook" style="width:350px" />
        <br />
        <?php echo $lang['lang_info_streaming_app_android_tv_facebook_info']; ?></td>
    </tr>
    <tr>
      <td height="50" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_app_android_versao']; ?></td>
      <td class="texto_padrao_pequeno">
        <select name="versao" id="versao">
          <option value="1.0" selected="selected">1.0</option>
          <option value="1.1">1.1</option>
          <option value="1.2">1.2</option>
          <option value="1.3">1.3</option>
          <option value="1.4">1.4</option>
          <option value="1.5">1.5</option>
          <option value="1.6">1.6</option>
          <option value="1.7">1.7</option>
          <option value="1.8">1.8</option>
          <option value="1.9">1.9</option>
          <option value="1.10">1.10</option>
        </select>
        <br />
        <?php echo $lang['lang_info_streaming_app_android_versao_info']; ?></td>
    </tr>
  </table>
  </div>
  </div>
  <br />
<div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_streaming_app_android_tab_titulo_personalizacao_app']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="20%" height="50" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_app_android_tv_logo']; ?></td>
      <td width="80%" class="texto_padrao_pequeno"><input name="logo" type="file" id="logo" style="width:350px" />
        <br />
        <?php echo $lang['lang_info_streaming_app_android_tv_logo_info']; ?></td>
    </tr>
    <tr>
      <td height="50" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_app_android_tv_icone']; ?></td>
      <td class="texto_padrao_pequeno"><input name="icone" type="file" id="icone" style="width:350px" />
        <br />
        <?php echo $lang['lang_info_streaming_app_android_tv_icone_info']; ?></td>
    </tr>
  </table>
  </div>
  </div>
  <br />
  <div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_streaming_app_android_tab_titulo_modelos']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="33%" height="200" align="center" class="texto_padrao_pequeno"><img src="img/img-modelo-app-android-1.png" alt="App1" width="300" height="169" /><br />
<input name="app_modelo" type="radio" id="app_modelo" value="source1" checked="checked" onclick="document.getElementById('tab_app_personalizado_icones').style.display = 'none';" />
&nbsp;App 1</td>
      <td width="33%" align="center" class="texto_padrao_pequeno"><img src="img/img-modelo-app-android-2.png" alt="App2" width="300" height="169" /><br />
          <input name="app_modelo" type="radio" id="app_modelo" value="source2" onclick="document.getElementById('tab_app_personalizado_icones').style.display = 'none';" />
        &nbsp;App 2</td>
      </tr>
    <tr>
      <td height="200" align="center" class="texto_padrao_pequeno"><img src="img/img-modelo-app-android-3.png" alt="App3" width="300" height="169" /><br />
<input name="app_modelo" type="radio" id="app_modelo" value="source3" onclick="document.getElementById('tab_app_personalizado_icones').style.display = 'none';" />
&nbsp;App 3</td>
      <td height="200" align="center" class="texto_padrao_pequeno"><img src="img/img-modelo-app-android-4.png" alt="App4" width="300" height="169" /><br />
          <input name="app_modelo" type="radio" id="app_modelo" value="source4" onclick="document.getElementById('tab_app_personalizado_icones').style.display = 'none';" />
  &nbsp;App 4</td>
    </tr>
    <tr>
      <td height="200" align="center" class="texto_padrao_pequeno"><img src="img/img-modelo-app-android-personalizado.png" alt="App4" width="300" height="169" /><br />
          <input name="app_modelo" type="radio" id="app_modelo" value="personalizado" onclick="document.getElementById('tab_app_personalizado_icones').style.display = 'block';" />
  &nbsp;<?php echo $lang['lang_info_streaming_app_android_tab_titulo_personalizacao_app_icones_info']; ?></td>
      <td height="200" align="center" class="texto_padrao_pequeno">&nbsp;</td>
    </tr>
  </table>
  </div>
</div>
<br />
<div id="tab_app_personalizado_icones" style="display:none">
<div id="quadro">
<div id="quadro-topo"> <strong><?php echo $lang['lang_info_streaming_app_android_tab_titulo_personalizacao_app_icones']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="20%" height="50" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_app_android_icone_play']; ?></td>
      <td width="80%" class="texto_padrao_pequeno"><input name="icone_play" type="file" id="icone_play" style="width:350px" />
        <br />
        <?php echo $lang['lang_info_streaming_app_android_icone_play_info']; ?></td>
    </tr>
    <tr>
      <td width="20%" height="50" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_app_android_icone_site']; ?></td>
      <td width="80%" class="texto_padrao_pequeno"><input name="icone_site" type="file" id="icone_site" style="width:350px" />
        <br />
        <?php echo $lang['lang_info_streaming_app_android_icone_site_info']; ?></td>
    </tr>
    <tr>
      <td height="50" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_app_android_icone_facebook']; ?></td>
      <td class="texto_padrao_pequeno"><input name="icone_facebook" type="file" id="icone_facebook" style="width:350px" />
        <br />
        <?php echo $lang['lang_info_streaming_app_android_icone_facebook_info']; ?></td>
    </tr>
  </table>
  </div>
  </div>
</div>
<br />
    <br />
  <center><input name="button" type="submit" class="botao" id="button" value="<?php echo $lang['lang_info_streaming_app_android_botao_submit']; ?>" onclick="abrir_log_sistema();" /></center>
  </td>
  </tr>
</table>
</form>
<br />
  <br />
  <br />
<?php } ?>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"><img src='/img/ajax-loader.gif' /></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>