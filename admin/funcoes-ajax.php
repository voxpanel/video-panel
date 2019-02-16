<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("inc/classe.ssh.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));

// Funções gerais para uso com Ajax

$acao = query_string('2');

////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Função para ligar streaming
if($acao == "ligar_streaming") {


	$login = code_decode(query_string('3'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status"] == "unloaded" || $status_streaming["status"] == "") {
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$resultado = $ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm["login"]."");
	
	if(!preg_match('/ERROR/i',$resultado)) {
	
	echo "<span class='texto_status_sucesso'>Streaming ligado com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","Streaming ligado com sucesso pelo administrador.");
	
	} else {
	
	echo "<span class='texto_status_erro'>Não fopi possível ligar o streaming.</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Fechar]</a>";
	
	}
	
	} else { // Já esta ligado
	
	echo "<span class='texto_status_alerta'>Streaming já esta ligado</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Fechar]</a>";
	
	}
	
	exit();
}

// Função para desligar streaming
if($acao == "desligar_streaming") {


	$login = code_decode(query_string('3'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status"] == "loaded") {
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$resultado = $ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	if(!preg_match('/ERROR/i',$resultado)) {
	
	echo "<span class='texto_status_sucesso'>Streaming desligado com sucesso</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","Streaming desligado com sucesso pelo administrador.");
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível desligar o streaming.</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Fechar]</a>";
	
	}
	
	} else { // Já esta desligado
	
	echo "<span class='texto_status_alerta'>Streaming já esta desligado.</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Fechar]</a>";
	
	}
	
	exit();
}

// Função para reiniciar streaming
if($acao == "reiniciar_streaming") {


	$login = code_decode(query_string('3'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	if($status_streaming["status"] == "loaded") {

	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	}
	
	$resultado = $ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance  ".$dados_stm["login"]."");
	
	if(!preg_match('/ERROR/i',$resultado)) {
	
	echo "<span class='texto_status_sucesso'>Streaming reiniciado com sucesso.</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Fechar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","Streaming reiniciado com sucesso pelo administrador.");
	
	} else {
	
	echo "<span class='texto_status_erro'>".lang_acao_reiniciar_stm_resultado_erro."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Fechar]</a>";
	
	}
	
	exit();
}

// Função para bloquear streaming
if($acao == "bloquear_streaming") {

	// Proteção Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");	
	}
	

	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>Dados faltando!</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Bloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml.lock; echo OK");
	
	// Desliga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	mysql_query("Update video.streamings set status ='2' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>Streaming bloqueado com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] Streaming bloqueado com sucesso pelo administrador.");
	
	}
	
	exit();
}


// Função para desbloquear streaming
if($acao == "desbloquear_streaming") {

	// Proteção Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");	
	}


	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>Dados faltando!</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Bloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml.lock /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml; echo OK");
	
	// Liga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm["login"]."");
	
	echo "<span class='texto_status_sucesso'>Streaming desbloqueado com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	mysql_query("Update video.streamings set status = '1' where codigo = '".$dados_stm["codigo"]."'");
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] Streaming desbloqueado com sucesso pelo administrador.");
	
	}
	
	exit();
}


// Função para remover streaming
if($acao == "remover_streaming") {

	// Proteção Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");	
	}



	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>Dados faltando!</span>";
	
	} else {
	
	$checar_streaming = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	
	if($checar_streaming == 0) {
	
	echo "<span class='texto_status_erro'>Streaming não encontrado!</span>";
	
	exit();
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->executar("/usr/local/WowzaMediaServer/desativar ".$dados_stm["login"]."");
	
	$ssh->executar("nohup rm -rf /home/streaming/".$dados_stm["login"]."; echo ok");
	
	mysql_query("Delete From video.streamings where codigo = '".$dados_stm["codigo"]."'");
	
	// Remove as playlists
	$query_playlists = mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."'");
	while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	
	mysql_query("Delete From video.playlists where codigo = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'");
	
	}
	
	// Remove as estatisticas
	mysql_query("Delete From video.estatisticas where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove os Agendamentos
	mysql_query("Delete From video.playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove logs
	mysql_query("Delete From video.logs_streamings where codigo_stm = '".$dados_stm["codigo"]."'");
	mysql_query("Delete From video.dicas_rapidas_acessos where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove app android
	$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM video.apps where codigo_stm = '".$dados_stm["codigo"]."'"));
	
	mysql_query("Delete From video.apps where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove o apk e imagens do app android
	@unlink("../app_android/apps/".$dados_app["zip"]."");

	if(!mysql_error()) {

	echo "<span class='texto_status_sucesso'>Streaming ".$dados_stm["login"]." removido com sucesso!</span><br /><br/><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível remover o streaming ".$dados_stm["login"]."<br>Log: ".mysql_error()."</span>";
	
	}
	
	}
	
	exit();
}


// Função para verificar o status do streaming e autodj
if($acao == "status_streaming") {

	$login = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {
	echo "manutencao";
	exit();
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status_transmissao"] == "aovivo") {
	echo "aovivo";
	exit();
	}
	
	if($status_streaming["status"] == "loaded") {
	echo "ligado";
	exit();
	}
	
	echo "desligado";
	
	exit();
	
}

// Função para admin/revenda acessar painel de streaming
if($acao == "acessar_painel_streaming") {

	// Proteção Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");	
	}


	
	$login = code_decode(query_string('3'),"D");

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));

	echo code_decode($dados_stm["login"],"E")."@".code_decode($dados_stm["senha"],"E");
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] Acesso administrativo ao painel deo streaming executado com sucesso.");
	
	exit();
	
}

// Função para sincronizar streaming no servidor AAC+
if($acao == "sincronizar") {


	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>Dados faltando!</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$aplicacao = ($dados_stm["aplicacao"]) ? $dados_stm["aplicacao"] : "tvstation";
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->executar("/usr/local/WowzaMediaServer/sincronizar ".$dados_stm["login"]." '".$dados_stm["senha_transmissao"]."' ".$dados_stm["bitrate"]." ".$dados_stm["espectadores"]." ".$aplicacao."");
		
	echo "<span class='texto_status_sucesso'>Streaming sincronizado/reconfigurado com sucesso.</span>";
	
	}
	
	exit();
	
}

////////////////////////////////////////////////////////
//////////// Funções Gerenciamento Revenda /////////////
////////////////////////////////////////////////////////

// Função para bloquear revenda
if($acao == "bloquear_revenda") {

	// Proteção Administrador
	require_once("inc/protecao-admin.php");

	$codigo = code_decode(query_string('3'),"D");
	
	if($codigo == "") {
	
	echo "<span class='texto_status_erro'>Dados faltando!</span>";
	
	} else {

	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas where codigo = '".$codigo."'"));
	
	// Bloqueia os streamings da revenda
	$query_stms_revenda = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_revenda["codigo"]."'");
	while ($dados_stm_revenda = mysql_fetch_array($query_stms_revenda)) {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm_revenda["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Bloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm_revenda["login"]."/Application.xml /usr/local/WowzaMediaServer/conf/".$dados_stm_revenda["login"]."/Application.xml.lock; echo OK");
	
	// Desliga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm_revenda["login"]."");
	
	mysql_query("Update video.streamings set status = '2' where codigo = '".$dados_stm_revenda["codigo"]."'");
	
	}
	
	// Bloqueia as subrevendas
	
	$query_subrevendas = mysql_query("SELECT * FROM video.revendas where codigo_revenda = '".$dados_revenda["codigo"]."'");
	while ($dados_subrevenda = mysql_fetch_array($query_subrevendas)) {
	
	// Bloqueia os streamings da subrevenda
	$query_stms_subrevenda = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_subrevenda["codigo"]."'");
	while ($dados_stm_subrevenda = mysql_fetch_array($query_stms_subrevenda)) {
	
	$dados_servidor_stm_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm_subrevenda["codigo_servidor"]."'"));
	
	if($dados_servidor_stm_subrevenda["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor_stm_subrevenda["ip"],$dados_servidor_stm_subrevenda["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor_stm_subrevenda["senha"],"D"));
	
	// Bloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml.lock; echo OK");
	
	// Desliga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm_subrevenda["login"]."");
	
	mysql_query("Update video.streamings set status = '2' where codigo = '".$dados_stm_subrevenda["codigo"]."'");
	
	}
	
	}
	
	mysql_query("Update video.revendas set status = '2' where codigo = '".$dados_revenda["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>Revenda ".$dados_revenda["id"]." bloqueada com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	}
	
	exit();
}

// Função para desbloquear revenda
if($acao == "desbloquear_revenda") {

	// Proteção Administrador
	require_once("inc/protecao-admin.php");

	$codigo = code_decode(query_string('3'),"D");
	
	if($codigo == "") {
	
	echo "<span class='texto_status_erro'>Dados faltando!</span>";
	
	} else {

	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas where codigo = '".$codigo."'"));
	
	// Bloqueia os streamings da revenda
	$query_stms_revenda = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_revenda["codigo"]."'");
	while ($dados_stm_revenda = mysql_fetch_array($query_stms_revenda)) {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm_revenda["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Desbloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm_revenda["login"]."/Application.xml.lock /usr/local/WowzaMediaServer/conf/".$dados_stm_revenda["login"]."/Application.xml; echo OK");
	
	// Desliga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm_revenda["login"]."");
	
	// Liga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm_revenda["login"]."");
	
	mysql_query("Update video.streamings set status = '1' where codigo = '".$dados_stm_revenda["codigo"]."'");
	
	}
	
	// Desbloqueia as subrevendas	
	$query_subrevendas = mysql_query("SELECT * FROM video.revendas where codigo_revenda = '".$dados_revenda["codigo"]."'");
	while ($dados_subrevenda = mysql_fetch_array($query_subrevendas)) {
	
	// Desbloqueia os streamings da subrevenda
	$query_stms_subrevenda = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_subrevenda["codigo"]."'");
	while ($dados_stm_subrevenda = mysql_fetch_array($query_stms_subrevenda)) {
	
	$dados_servidor_stm_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm_subrevenda["codigo_servidor"]."'"));
	
	if($dados_servidor_stm_subrevenda["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor_stm_subrevenda["ip"],$dados_servidor_stm_subrevenda["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor_stm_subrevenda["senha"],"D"));
	
	// Bloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm_subrevenda["login"]."/Application.xml.lock /usr/local/WowzaMediaServer/conf/".$dados_stm_subrevenda["login"]."/Application.xml; echo OK");
	
	// Liga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm_subrevenda["login"]."");
	
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm_subrevenda["login"]."");
	
	mysql_query("Update video.streamings set status = '1' where codigo = '".$dados_stm_subrevenda["codigo"]."'");
	
	}
	
	}
	
	mysql_query("Update video.revendas set status = '1' where codigo = '".$dados_revenda["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>Revenda ".$dados_revenda["id"]." desbloqueada com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	}
	
	exit();
}

// Função para remover revenda
if($acao == "remover_revenda") {

	// Proteção Administrador
	require_once("inc/protecao-admin.php");



	$codigo = code_decode(query_string('3'),"D");
	
	if($codigo == "") {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando.</span>";
	
	} else {
	
	$checar_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas where codigo = '".$codigo."'"));
	
	if($checar_revenda == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Revenda não encontrada.</span>";
	
	exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas where codigo = '".$codigo."'"));
	
	// Remove os streamings da revenda
	$query_stms = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_revenda["codigo"]."'");
	while ($dados_stm = mysql_fetch_array($query_stms)) {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_stm["codigo"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->executar("/usr/local/WowzaMediaServer/desativar ".$dados_stm["login"]."");
	
	$ssh->executar("nohup rm -rf /home/streaming/".$dados_stm["login"]."; echo ok");
	
	mysql_query("Delete From video.streamings where codigo = '".$dados_stm["codigo"]."'");
	
	// Remove as playlists
	$query_playlists = mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."'");
	while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	
	mysql_query("Delete From video.playlists where codigo = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'");
	
	}
	
	// Remove os Agendamentos
	mysql_query("Delete From video.playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."'");

	// Remove logs
	mysql_query("Delete From video.logs_streamings where codigo_stm = '".$dados_stm["codigo"]."'");
	mysql_query("Delete From video.dicas_rapidas_acessos where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove app android
	$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM video.apps where codigo_stm = '".$dados_stm["codigo"]."'"));
	
	mysql_query("Delete From video.apps where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove o apk e imagens do app android
	@unlink("../app_android/apps/".$dados_app["zip"]."");
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] Streaming removido com sucesso na remoção da revenda ".$dados_revenda["id"]." - ".$dados_revenda["email"]."");
	
	}
	
	// Remove subrevendas
	$query_subrevendas = mysql_query("SELECT * FROM video.revendas where codigo_revenda = '".$dados_revenda["codigo"]."'");
	while ($dados_subrevenda = mysql_fetch_array($query_subrevendas)) {
	
	mysql_query("Delete From video.revendas where codigo = '".$dados_subrevenda["codigo"]."'");
	
	// Bloqueia os streamings da subrevenda
	$query_stms_subrevenda = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_subrevenda["codigo"]."'");
	while ($dados_stm_subrevenda = mysql_fetch_array($query_stms_subrevenda)) {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm_subrevenda["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->executar("/usr/local/WowzaMediaServer/desativar ".$dados_stm_subrevenda["login"]."");
	
	$ssh->executar("nohup rm -rf /home/streaming/".$dados_stm_subrevenda["login"]."; echo ok");
	
	mysql_query("Delete From video.streamings where codigo = '".$dados_stm_subrevenda["codigo"]."'");
	
	// Remove as playlists
	$query_playlists = mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm_subrevenda["codigo"]."'");
	while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	
	mysql_query("Delete From video.playlists where codigo = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'");
	
	}
	
	// Remove os Agendamentos
	mysql_query("Delete From video.playlists_agendamentos where codigo_stm = '".$dados_stm_subrevenda["codigo"]."'");

	// Remove logs
	mysql_query("Delete From video.logs_streamings where codigo_stm = '".$dados_stm_subrevenda["codigo"]."'");
	mysql_query("Delete From video.dicas_rapidas_acessos where codigo_stm = '".$dados_stm_subrevenda["codigo"]."'");
	
	// Remove app android
	$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM video.apps where codigo_stm = '".$dados_stm_subrevenda["codigo"]."'"));
	
	mysql_query("Delete From video.apps where codigo_stm = '".$dados_stm_subrevenda["codigo"]."'");
	
	// Remove o apk e imagens do app android
	@unlink("../app_android/apps/".$dados_app["zip"]."");
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm_subrevenda["login"]."] Streaming removido com sucesso na remoção da sub revenda ".$dados_subrevenda["id"]." - ".$dados_subrevenda["email"]."");
	
	}
	
	}
	
	mysql_query("Delete From video.revendas where codigo = '".$dados_revenda["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>Revenda <strong>".$dados_revenda["nome"]."</strong> removida com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_revenda["nome"]." - ".$dados_revenda["email"]."] Revenda removida com sucesso.");
	
	}
	
	exit();
}

// Função para alterar senha de uma revenda
if($acao == "alterar_senha_revenda") {

	// Proteção Administrador
	require_once("inc/protecao-admin.php");



	$codigo = code_decode(query_string('3'),"D");
	$nova_senha = query_string('4');
	
	if($codigo == "") {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando.</span>";
	
	} else {
	
	$checar_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas where codigo = '".$codigo."'"));
	
	if($checar_revenda == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Revenda não encontrada.</span>";
	
	exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas where codigo = '".$codigo."'"));
	
	mysql_query("Update video.revendas set senha = PASSWORD('".$nova_senha."') where codigo = '".$dados_revenda["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Senha da revenda <strong>".$dados_revenda["nome"]."</strong> alterada com sucesso para ".$nova_senha."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
		// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_revenda["nome"]." - ".$dados_revenda["email"]."] Senha da revenda alterada com sucesso.");
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível alterar a senha da revenda <strong>".$dados_revenda["nome"]."</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	
	}
	
	exit();

}

// Função para alterar senha de um streaming
if($acao == "alterar_senha_streaming") {

	// Proteção Administrador
	require_once("inc/protecao-admin.php");



	$login = code_decode(query_string('3'),"D");
	$tipo = query_string('4');
	$nova_senha = query_string('5');
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	
	if($tipo == "dj") {
	mysql_query("Update video.streamings set senha = '".$nova_senha."' where codigo = '".$dados_stm["codigo"]."'");
	} else {
	mysql_query("Update video.streamings set senha_admin = '".$nova_senha."' where codigo = '".$dados_stm["codigo"]."'");
	}
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Senha do streaming <strong>".$dados_stm["login"]."</strong> alterada com sucesso para: ".$nova_senha."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] Alterada senha do stmvideo.");
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível alterar a senha do streaming <strong>".$dados_stm["login"]."</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	
	}
	
	exit();

}

// Função para mover um streaming para a revenda principal
if($acao == "mover_streaming_subrevenda_revenda") {



	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
	
	$verifica_subrevenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".$dados_stm["codigo_cliente"]."') AND tipo = '2'"));
	
	// Verifica se o streaming é de uma sub revenda que pertence a revenda logada
	if($verifica_subrevenda > 0) {
	
	mysql_query("Update video.streamings set codigo_cliente = '".$dados_revenda["codigo"]."' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".lang_info_subrevenda_streamings_mover_streaming_resultado_ok."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] Movido para revenda principal ".$dados_revenda["id"]."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".lang_info_subrevenda_streamings_mover_streaming_resultado_alerta."</span>";
	
	}
	
	
	}
	
	exit();

}

////////////////////////////////////////////////////////
//////////// Funções Gerenciamento Servidor ////////////
////////////////////////////////////////////////////////

// Função para listar os streamings do servidor
if($acao == "listar_streamings_servidor") {

	
	$codigo_servidor = code_decode(query_string('3'),"D");
	
	$sql = mysql_query("SELECT * FROM video.streamings where codigo_servidor = '".$codigo_servidor."' ORDER by porta ASC");
	while ($dados_stm = mysql_fetch_array($sql)) {
	
	$streamings .= "".code_decode($dados_stm["login"],"E")."|";
	
	}
	
	echo substr($streamings,0,-1);

	exit();
	
}

// Função para ligar todos os streamings em todos os servidores
if($acao == "listar_streamings_geral") {

	
	$sql = mysql_query("SELECT * FROM video.streamings where status = '1' ORDER by porta ASC");
	while ($dados_stm = mysql_fetch_array($sql)) {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "on") {
	$streamings .= "".code_decode($dados_stm["login"],"E")."|";
	}
	
	}
	
	echo substr($streamings,0,-1);

	exit();
	
}

// Função para sincronizar streaming no servidor AAC+
if($acao == "sincronizar_servidor") {


	$codigo_servidor = code_decode(query_string('3'),"D");
	
	if($codigo_servidor == "") {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$sql = mysql_query("SELECT * FROM video.streamings where codigo_servidor = '".$codigo_servidor."' ORDER by login ASC");
	while ($dados_stm = mysql_fetch_array($sql)) {
	
	$streamings .= "".code_decode($dados_stm["login"],"E")."|";
	
	}
	
	echo substr($streamings,0,-1);
	
	}
	
	exit();
}

// Função para ativar/desativar manutenção em um servidor
if($acao == "manutencao_servidor") {


	$codigo_servidor = code_decode(query_string('3'),"D");
	$acao = query_string('4');
	$mensagem = query_string('5');
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$codigo_servidor."'"));

	if($acao == "ativar") {
	mysql_query("Update video.servidores set status = 'off', mensagem_manutencao = '".$mensagem."' where codigo = '".$dados_servidor["codigo"]."'");
	} else {
	mysql_query("Update video.servidores set status = 'on', mensagem_manutencao = '' where codigo = '".$dados_servidor["codigo"]."'");
	}
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Manutenção ativada/desativada no servidor <strong>".$dados_servidor["nome"]."</strong> com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível ativar/desativar a manutenção no servidor <strong>".$dados_servidor["nome"]."</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

////////////////////////////////////////////////////////
//////////////////// Funções Gerais ////////////////////
////////////////////////////////////////////////////////

// Função para remover uma dica rápida
if($acao == "remover_dica_rapida") {


	$codigo = code_decode(query_string('3'),"D");
	
	$dados_dica_rapida = mysql_fetch_array(mysql_query("SELECT * FROM video.dicas_rapidas where codigo = '".$codigo."'"));

	mysql_query("Delete From video.dicas_rapidas where codigo = '".$dados_dica_rapida["codigo"]."'");
	mysql_query("Delete From video.dicas_rapidas_acessos where codigo_dica = '".$dados_dica_rapida["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Dica rápida <strong>".$dados_dica_rapida["titulo"]."</strong> removida com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível remover a dica rápida <strong>".$dados_dica_rapida["titulo"]."</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para mudar o status de exibição de um aviso
if($acao == "alterar_status_aviso") {


	$codigo_aviso = code_decode(query_string('3'),"D");
	
	$dados_aviso = mysql_fetch_array(mysql_query("SELECT * FROM video.avisos where codigo = '".$codigo_aviso."'"));
	
	$status = ($dados_aviso["status"] == "sim") ? 'nao' : 'sim';
	
	mysql_query("Update video.avisos set status = '".$status."' where codigo = '".$dados_aviso["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Status do aviso <strong>".$dados_aviso["titulo"]."</strong> alterado com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível alterar o status do aviso <strong>".$dados_aviso["titulo"]."</strong>.</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para remover um aviso
if($acao == "remover_aviso") {


	$codigo_aviso = code_decode(query_string('3'),"D");
	
	$dados_aviso = mysql_fetch_array(mysql_query("SELECT * FROM video.avisos where codigo = '".$codigo_aviso."'"));
	
	mysql_query("Delete From video.avisos where codigo = '".$dados_aviso["codigo"]."'");
	mysql_query("Delete From video.avisos_desativados where codigo_aviso = '".$dados_aviso["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Aviso <strong>".$dados_aviso["titulo"]."</strong> removido com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível remover o aviso <strong>".$dados_aviso["titulo"]."</strong>.</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para desbloquear IP bloqueado no login
if($acao == "desbloquear_ip_login") {


	$codigo = code_decode(query_string('3'),"D");
	
	mysql_query("Delete From video.bloqueios_login where codigo = '".$codigo."'");
	
	echo "<span class='texto_status_sucesso'>".lang_info_ips_bloqueados_resultado_ok."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Fechar]</a>";
	
	exit();

}
?>