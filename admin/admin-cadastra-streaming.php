<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("inc/classe.ssh.php");

if(empty($_POST["login"]) or empty($_POST["espectadores"]) or empty($_POST["bitrate"]) or empty($_POST["senha"]) or empty($_POST["identificacao"])) {
die ("<script> alert(\"Você deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

// Verifica se a porta já esta em uso
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where login = '".strtolower($_POST["login"])."'"));

if($total_streamings > 0) {
die ("<script> alert(\"O login ".strtolower($_POST["login"])." já esta em uso\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = '/admin/admin-cadastrar-streaming'; </script>");
}

// Verifica logins restritos
$array_logins_restritos = array("web", "streaming", "home");

if(in_array($_POST["login"], $array_logins_restritos)) { 
die ("<script> alert(\"Login reservado!\\n\\nLogin reserved!\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));

mysql_query("INSERT INTO video.streamings (codigo_cliente,codigo_servidor,login,senha,senha_transmissao,espectadores,bitrate,espaco,ftp_dir,identificacao,data_cadastro,idioma_painel,email,aplicacao) VALUES ('1','".$_POST["servidor"]."','".strtolower($_POST["login"])."','".$_POST["senha"]."','".$_POST["senha"]."','".$_POST["espectadores"]."','".$_POST["bitrate"]."','".$_POST["espaco"]."','/home/streaming/".strtolower($_POST["login"])."','".$_POST["identificacao"]."',NOW(),'".$_POST["idioma_painel"]."','".$_POST["email"]."','".$_POST["aplicacao"]."')") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());
$codigo_streaming = mysql_insert_id();

// Cria o streaming no servidor Wowza
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$_POST["servidor"]."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

if($_POST["aplicacao"] == 'tvstation') {

// Cria a home do streaming
$ssh->executar("/bin/mkdir -v /home/streaming/".strtolower($_POST["login"]).";/bin/chown streaming.streaming /home/streaming/".strtolower($_POST["login"])."");
// Copia a playlist demo para home do streaming
$ssh->executar("/bin/cp -vp /home/streaming/demo.mp4 /home/streaming/".strtolower($_POST["login"])."/;/bin/cp -vp /home/streaming/demo.smil /home/streaming/".strtolower($_POST["login"])."/playlists_agendamentos.smil");
// Configura a playlist demo
$ssh->executar("/usr/bin/replace LOGIN ".strtolower($_POST["login"])." -- /home/streaming/".strtolower($_POST["login"])."/playlists_agendamentos.smil;echo OK");
}

// Ativa o streaming no Wowza
$ssh->executar("/usr/local/WowzaMediaServer/ativar ".strtolower($_POST["login"])." '".$_POST["senha"]."' ".$_POST["bitrate"]." ".$_POST["espectadores"]." ".$_POST["aplicacao"]."");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Streaming ".strtolower($_POST["login"])." cadastrado com sucesso.","ok");

header("Location: /admin/admin-streamings/resultado/".strtolower($_POST["login"])."");
?>