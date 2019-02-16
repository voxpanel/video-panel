<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclus�o de classes
require_once("inc/classe.ssh.php");
require_once("inc/classe.ftp.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));

// Fun��es gerais para uso com Ajax

$acao = query_string('2');

////////////////////////////////////////////////////////
/////////// Fun��es Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Fun��o para ligar streaming
if($acao == "ligar_streaming") {


	$login = code_decode(query_string('3'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".lang_alerta_manutencao_servidor."</span>";
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status"] == "unloaded" || $status_streaming["status"] == "") {
	
	// Conex�o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$resultado = $ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm["login"]."");
	
	if(!preg_match('/ERROR/i',$resultado)) {
	
	echo "<span class='texto_status_sucesso'>".lang_acao_ligar_stm_resultado_ok."</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["login"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".lang_acao_ligar_stm_resultado_ok."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".lang_acao_ligar_stm_resultado_erro."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".lang_acao_ligar_stm_resultado_erro."");
	
	}
	
	} else { // J� esta ligado
	
	echo "<span class='texto_status_alerta'>".lang_acao_ligar_stm_resultado_alerta."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".lang_acao_ligar_stm_resultado_alerta."");
	
	}
	
	exit();
}

// Fun��o para desligar streaming
if($acao == "desligar_streaming") {


	$login = code_decode(query_string('3'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".lang_alerta_manutencao_servidor."</span>";
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status"] == "loaded") {
	
	// Conex�o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$resultado = $ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	if(!preg_match('/ERROR/i',$resultado)) {
	
	echo "<span class='texto_status_sucesso'>".lang_acao_desligar_stm_resultado_ok."</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["login"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".lang_acao_desligar_stm_resultado_ok."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".lang_acao_desligar_stm_resultado_erro."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".lang_acao_desligar_stm_resultado_erro."");
	
	}
	
	} else { // J� esta desligado
	
	echo "<span class='texto_status_alerta'>".lang_acao_desligar_stm_resultado_alerta."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".lang_acao_desligar_stm_resultado_alerta."");
	
	}
	
	exit();

}

// Fun��o para reiniciar streaming
if($acao == "reiniciar_streaming") {


	$login = code_decode(query_string('3'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".lang_alerta_manutencao_servidor."</span>";
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	// Conex�o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	if($status_streaming["status"] == "loaded") {

	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	}
	
	$resultado = $ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance  ".$dados_stm["login"]."");
	
	if(!preg_match('/ERROR/i',$resultado)) {
	
	echo "<span class='texto_status_sucesso'>".lang_acao_reiniciar_stm_resultado_ok."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".lang_acao_reiniciar_stm_resultado_ok."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".lang_acao_reiniciar_stm_resultado_erro."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".lang_acao_reiniciar_stm_resultado_erro."");
	
	}
	
	exit();
}

// Fun��o para verificar o status do streaming e autodj
if($acao == "status_streaming") {

	$login = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {
	echo "<font color=\"#999999\" size=\"5\"><strong>".lang_info_status_manutencao."</strong></font>";
	exit();
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status_transmissao"] == "aovivo") {
	echo "<font color=\"#009900\" size=\"5\"><strong>".lang_info_status_transmitindo."</strong></font><br><font color=\"#009900\" size=\"2\"><strong>".lang_info_status_aovivo."</strong></font>";
	exit();
	}
	
	if($status_streaming["status"] == "loaded") {
	echo "<font color=\"#009900\" size=\"6\"><strong>".lang_info_status_ligado."</strong></font>";
	exit();
	}
	
	echo "<font color=\"#999999\" size=\"6\"><strong>".lang_info_status_desligado."</strong></font>";
	
	exit();
	
}

// Fun��o para verificar o status do streaming e autodj
if($acao == "status_streaming_interno") {

	$login = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {
	echo "".lang_info_status_manutencao."|#FFB3B3";
	exit();
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status_transmissao"] == "aovivo") {
	echo "".lang_info_status_aovivo."|#A8FFA8";
	exit();
	}
	
	if($status_streaming["status"] == "loaded") {
	echo "".lang_info_status_ligado."|#A8FFA8";
	exit();
	}
	
	echo "".lang_info_status_desligado."|#FFB3B3";
	
	exit();
	
}

// Fun��o para bloquear streaming
if($acao == "bloquear_streaming") {

	// Prote��o Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");	
	}


	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".lang_alerta_manutencao_servidor."</span>";
	exit();	
	}
	
	// Conex�o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Bloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml.lock; echo OK");
	
	// Desliga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	mysql_query("Update video.streamings set status ='3' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".lang_acao_bloquear_stm_resultado_ok."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] ".lang_acao_bloquear_stm_resultado_ok." pela revenda.");
	
	}
	
	exit();
}

// Fun��o para desbloquear streaming
if($acao == "desbloquear_streaming") {

	// Prote��o Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");	
	}


	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>".lang_alerta_manutencao_servidor."</span>";
	
	exit();	
	}
	
	if($_SESSION["type_logged_user"] == "cliente" && $dados_stm["status"] == 2) {
	
	echo "<span class='texto_status_erro'>".lang_acao_desbloquear_stm_resultado_erro_permissao."</span>";
	
	exit();	
	}
	
	// Conex�o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Bloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml.lock /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml; echo OK");
	
	// Liga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm["login"]."");
	
	echo "<span class='texto_status_sucesso'>".lang_acao_desbloquear_stm_resultado_ok."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	mysql_query("Update video.streamings set status = '1' where codigo = '".$dados_stm["codigo"]."'");
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] ".lang_acao_desbloquear_stm_resultado_ok." pela revenda.");
	
	}
	
	exit();
}

// Fun��o para remover streaming
if($acao == "remover_streaming") {

	// Prote��o Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");	
	}



	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
	
	$checar_streaming = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	
	if($checar_streaming == 0) {
	
	echo "<span class='texto_status_erro'>".lang_acao_stm_nao_encontrado."</span>";
	
	exit();
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>".lang_alerta_manutencao_servidor."</span>";
	
	exit();	
	}
	
	// Conex�o SSH
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

	// Loga a a��o executada
	logar_acao("[".$dados_stm["login"]."] Remo��o do streaming pela revenda.");
	
	if(!mysql_error()) {

	echo "<span class='texto_status_sucesso'>".lang_acao_remover_stm_resultado_ok."</span><br /><br/><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] ".lang_acao_remover_stm_resultado_ok." pela revenda.");
	
	} else {
	
	echo "<span class='texto_status_erro'>".lang_acao_remover_stm_resultado_erro."<br>Log: ".mysql_error()."</span>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] ".lang_acao_remover_stm_resultado_erro." Log: ".mysql_error()."");
	
	}
	
	}
	
	exit();
}

// Fun��o para checar a quantidade de espectadores online e criar a barra de porcentagem de uso
if($acao == "estatistica_uso_plano") {

	$login = query_string('3');
	$recurso = query_string('4');
	$texto = query_string('5');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($recurso == "espectadores") {
	
	$dados_wowza = total_espectadores_conectados($dados_servidor["ip"],$dados_servidor["senha"],$login);
	$espectadores_conectados = $dados_wowza["espectadores"];
	
	$porcentagem_uso_espectadores = ($dados_stm["espectadores"] == 0) ? "0" : $espectadores_conectados*100/$dados_stm["espectadores"];	
	$porcentagem_uso_espectadores = ($porcentagem_uso_espectadores < 1 && $espectadores_conectados > 0) ? "1" : $porcentagem_uso_espectadores;
	
	$modo_texto = ($texto == "sim") ? '<span class="texto_padrao_pequeno">('.str_replace("-","",$espectadores_conectados).' de '.$dados_stm["espectadores"].')</span>' : '';
	
	echo barra_uso_plano(str_replace("-","",$porcentagem_uso_espectadores),'('.str_replace("-","",$espectadores_conectados).' de '.$dados_stm["espectadores"].')').'&nbsp;'.$modo_texto;
		
	} else { // -> Recurso FTP
	
	$porcentagem_uso_espaco = ($dados_stm["espaco_usado"] == 0 || $dados_stm["espaco"] == 0) ? "0" : $dados_stm["espaco_usado"]*100/$dados_stm["espaco"];
	
	$modo_texto = ($texto == "sim") ? '<span class="texto_padrao_pequeno">('.tamanho($dados_stm["espaco_usado"]).' de '.tamanho($dados_stm["espaco"]).')</span>' : '';
	
	echo barra_uso_plano($porcentagem_uso_espaco,'('.tamanho($dados_stm["espaco_usado"]).' de '.tamanho($dados_stm["espaco"]).')').'&nbsp;'.$modo_texto;
	
	}
	
	exit();
}

// Fun��o para admin/revenda acessar painel de streaming
if($acao == "acessar_painel_streaming_revenda") {

	// Prote��o Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");	
	}



	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".code_decode(query_string('3'),"D")."'"));

	if($dados_stm["codigo_cliente"] == $_SESSION["code_user_logged"]) {

	echo "1|".code_decode($dados_stm["login"],"E")."@".code_decode($dados_stm["senha"],"E")."";
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] Acesso da revenda ao painel de streaming executado com sucesso.");
	
	} else {
	echo "0|<span class='texto_status_erro'>".lang_info_acesso_stm_nao_permitido."</span>";	
	}
	
	exit();
	
}

// Fun��o para sincronizar streaming no servidor AAC+
if($acao == "sincronizar") {


	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$aplicacao = ($dados_stm["aplicacao"]) ? $dados_stm["aplicacao"] : "tvstation";
	
	$aplicacao_xml = $aplicacao;

	if($dados_stm["autenticar_live"] == "nao") {
	
	if($aplicacao == "tvstation" || $aplicacao == "live") {
	$aplicacao_xml = $aplicacao.'-sem-login';
	}
	
	}
	
	// Conex�o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->executar("/usr/local/WowzaMediaServer/sincronizar ".$dados_stm["login"]." '".$dados_stm["senha_transmissao"]."' ".$dados_stm["bitrate"]." ".$dados_stm["espectadores"]." ".$aplicacao_xml."");
	
	// Verifica se a aplica��o � IP Camera e reconfigura os aquivos .stream
	if($aplicacao == 'ipcamera') {
	
	$query_ip_cameras = mysql_query("SELECT * FROM stmvideo.ip_cameras WHERE codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
	while ($dados_ip_camera = mysql_fetch_array($query_ip_cameras)) {
	
	$ssh->executar("echo '".$dados_ip_camera["rtsp"]."' > /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/".$dados_ip_camera["stream"].";echo OK");
	}
	
	}
		
	echo "<span class='texto_status_sucesso'>".lang_acao_sincronizar_stm_resultado_ok."</span>";
	
	}
	
	exit();
	
}

// Fun��o para sincronizar as playlists do streaming no servidor
if($acao == "sincronizar_playlists") {
	

	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	//XXXXXXXXXXXXXXXXX
	
	echo "<span class='texto_status_sucesso'>".lang_acao_sincronizar_playlists_resultado_ok."</span>";
	
	}
	
	exit();
}

// Fun��o para atualizar cache player facebook
if($acao == "atualizar_cache_player_facebook") {


	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.lang_info_pagina_resolver_problemas_tab_titulo_facebook.'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
   <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="left" class="texto_padrao"><br />'.sprintf(lang_acao_pagina_resolver_problemas_player_facebook,"playerv.".$dados_config["dominio_padrao"],$dados_stm["login"],"playerv.".$dados_config["dominio_padrao"],$dados_stm["login"]).'<br /></td>
      </tr>
    </table>
  </div>
</div>';
	
	}
	
	exit();
	
}

// Fun��o para buscar streaming no painel de revenda
if($acao == "buscar_streaming_revenda") {
	
	// Prote��o Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");	
	}


	
	echo code_decode(query_string('3'),"E");
	
	exit();

}

////////////////////////////////////////////////////////
//////////////////// Fun��es Gerais ////////////////////
////////////////////////////////////////////////////////

// Fun��o para exibir avisos
if($acao == "exibir_aviso") {


	$codigo_aviso = query_string('3');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
	$dados_aviso = mysql_fetch_array(mysql_query("SELECT * FROM video.avisos where codigo = '".$codigo_aviso."'"));
	
	$area = ($_SESSION["login_logado"]) ? 'streaming' : 'revenda';
	$codigo_usuario = ($area == "streaming") ? $_SESSION["login_logado"] : $_SESSION["code_user_logged"];

	if(!mysql_error()) {
	
	list($ano,$mes,$dia) = explode("-",$dados_aviso["data"]);
	
	echo "<div id=\"quadro\">
			<div id=\"quadro-topo\"><strong>Aten��o!</strong></div>
				<div class=\"texto_padrao\" id=\"quadro-conteudo\">
				".$dados_aviso["mensagem"]."<br><br>
				<span class=\"texto_padrao_vermelho\">Aviso adicionado em ".$dia."/".$mes."/".$ano."</span><br>
				<span class=\"texto_padrao_pequeno\"><input type=\"checkbox\" onclick=\"desativar_exibicao_aviso('".$codigo_aviso."', '".$area."', '".$codigo_usuario."');\" style=\"vertical-align:middle;\" />&nbsp;Marque esta caixa para n�o exibir novamente este aviso em seu painel de controle.</span>
				</div>
		  </div>";
	
	}
	
	exit();

}

// Fun��o para marcar um aviso como vizualizado
if($acao == "desativar_exibicao_aviso") {

	$codigo_aviso = query_string('3');
	$area = query_string('4');
	$codigo_usuario = query_string('5');
	
	mysql_query("INSERT INTO video.avisos_desativados (codigo_aviso,codigo_usuario,area,data) VALUES ('".$codigo_aviso."','".$codigo_usuario."','".$area."',NOW())");
	
	exit();

}

// Fun��o para desbloquear IP bloqueado no login
if($acao == "desbloquear_ip_login") {


	$codigo = code_decode(query_string('3'),"D");
	
	mysql_query("Delete From video.bloqueios_login where codigo = '".$codigo."'");
	
	echo "<span class='texto_status_sucesso'>".lang_info_ips_bloqueados_resultado_ok."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>";
	
	exit();

}

////////////////////////////////////////////////////////
////////// Fun��es Gerenciamento Sub Revenda ///////////
////////////////////////////////////////////////////////

// Fun��o para bloquear uma sub revenda
if($acao == "bloquear_subrevenda") {
	

	$codigo = code_decode(query_string('3'),"D");
	
	if($codigo == "" || $codigo == 0) {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {

	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas where codigo = '".$codigo."'"));
	
	$lista_streamings_bloquear = array();
	
	// Gera lista de streamings da subrevenda
	$query_stms = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_subrevenda["codigo"]."'");
	while ($dados_stm_subrevenda = mysql_fetch_array($query_stms)) {
	
	$lista_streamings_bloquear[] = $dados_stm_subrevenda["login"];
	
	}
	
	// Gera lista de streamings das subrevendas da suarevenda
	$query_subrevendas_sub = mysql_query("SELECT * FROM video.revendas where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND tipo = '3' ORDER by codigo ASC");
	while ($dados_subrevenda_sub = mysql_fetch_array($query_subrevendas_sub)) {
	
		$query_stms_subrevenda = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_subrevenda_sub["codigo"]."'");
		while ($dados_stm_subrevenda_sub = mysql_fetch_array($query_stms_subrevenda)) {
		
		$lista_streamings_bloquear[] = $dados_stm_subrevenda_sub["login"];
		
		}
	}
	
	// Bloqueia os streamings da lista gerada
	foreach($lista_streamings_bloquear as $login) {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>".lang_alerta_manutencao_servidor."</span>";
	
	exit();	
	}
	
	// Conex�o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Bloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml.lock; echo OK");
	
	// Desliga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	mysql_query("Update video.streamings set status = '2' where codigo = '".$dados_stm["codigo"]."'");
	
	}
	
	// Bloqueia as subrevendas da subrevenda
	mysql_query("Update video.revendas set status = '3' where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND tipo = '3'");
	
	mysql_query("Update video.revendas set status = '3' where codigo = '".$dados_subrevenda["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".lang_acao_bloquear_subrevenda_resultado_ok."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_subrevenda["nome"]." - ".$dados_subrevenda["email"]."] Sub Revenda bloqueada com sucesso");
	
	}
	
	exit();
}

// Fun��o para bloquear uma sub revenda
if($acao == "desbloquear_subrevenda") {


	$codigo = code_decode(query_string('3'),"D");
	
	if($codigo == "" || $codigo == 0) {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
	
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas where codigo = '".$codigo."'"));
	
	$lista_streamings_desbloquear = array();
	
	// Gera lista de streamings das subrevendas da suarevenda
	$query_stms = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_subrevenda["codigo"]."'");
	while ($dados_stm_subrevenda = mysql_fetch_array($query_stms)) {
	
	$lista_streamings_desbloquear[] = $dados_stm_subrevenda["login"];
	
	}
	
	// Gera lista de streamings das subrevendas da subrevenda
	$query_subrevendas_sub = mysql_query("SELECT * FROM video.revendas where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND tipo = '3' ORDER by codigo ASC");
	while ($dados_subrevenda_sub = mysql_fetch_array($query_subrevendas_sub)) {
	
		$query_stms_subrevenda = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_subrevenda_sub["codigo"]."'");
		while ($dados_stm_subrevenda_sub = mysql_fetch_array($query_stms_subrevenda)) {
		
		$lista_streamings_desbloquear[] = $dados_stm_subrevenda_sub["login"];
		
		}
	}
	
	// Desbloqueia os streamings da lista gerada
	foreach($lista_streamings_desbloquear as $login) {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Conex�o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Desbloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml.lock /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml; echo OK");
	
	// Liga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm["login"]."");
	
	mysql_query("Update video.streamings set status = '1' where codigo = '".$dados_stm["codigo"]."'");
	
	}
	
	// Desbloqueia as subrevendas da subrevenda
	mysql_query("Update video.revendas set status = '1' where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND tipo = '3'");

	mysql_query("Update video.revendas set status = '1' where codigo = '".$dados_subrevenda["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".lang_acao_desbloquear_subrevenda_resultado_ok."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_subrevenda["nome"]." - ".$dados_subrevenda["email"]."] Sub Revenda desbloqueada com sucesso");
	
	}
	
	exit();

}

// Fun��o para bloquear uma sub revenda
if($acao == "remover_subrevenda") {



	$codigo = code_decode(query_string('3'),"D");
	
	if($codigo == "" || $codigo == 0) {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
	$checar_subrevenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".$codigo."')"));
	
	if($checar_subrevenda == 0) {
	
	echo "<span class='texto_status_erro'>".lang_alerta_subrevenda_nao_encontrada."</span>";
	
	exit();
	}
	
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas where codigo = '".$codigo."'"));
	
	$lista_streamings_remover = array();
	
	// Gera lista de streamings das subrevendas da suarevenda
	$query_stms = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_subrevenda["codigo"]."'");
	while ($dados_stm_subrevenda = mysql_fetch_array($query_stms)) {
	
	$lista_streamings_remover[] = $dados_stm_subrevenda["login"];
	
	}
	
	// Gera lista de streamings das subrevendas da subrevenda
	$query_subrevendas_sub = mysql_query("SELECT * FROM video.revendas where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND tipo = '3' ORDER by codigo ASC");
	while ($dados_subrevenda_sub = mysql_fetch_array($query_subrevendas_sub)) {
	
		$query_stms_subrevenda = mysql_query("SELECT * FROM video.streamings where codigo_cliente = '".$dados_subrevenda_sub["codigo"]."'");
		while ($dados_stm_subrevenda_sub = mysql_fetch_array($query_stms_subrevenda)) {
		
		$lista_streamings_remover[] = $dados_stm_subrevenda_sub["login"];
		
		}
	}
	
	// Remove os streamings da lista gerada
	foreach($lista_streamings_remover as $login) {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));

	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>".lang_alerta_manutencao_servidor."</span>";
	
	exit();	
	}
	
	// Conex�o SSH
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
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] Streaming removido com sucesso na remo��o da sub revenda ".$dados_subrevenda["id"]." - ".$dados_subrevenda["email"]."");
	
	}
	
	// Remove as subrevendas da subrevenda
	mysql_query("Delete From video.revendas where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND tipo = '3'");
	mysql_query("Delete From video.revendas where codigo = '".$dados_subrevenda["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".lang_acao_remover_subrevenda_resultado_ok."</span><br /><br /><a href='/admin/revenda' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_subrevenda["nome"]." - ".$dados_subrevenda["email"]."] Sub Revenda removida com sucesso.");
	
	}
	
	exit();

}

// Fun��o para mover um streaming para a revenda principal
if($acao == "mover_streaming_subrevenda_revenda") {



	$login = code_decode(query_string('3'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
	
	$verifica_subrevenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".$dados_stm["codigo_cliente"]."') AND tipo = '2'"));
	
	// Verifica se o streaming � de uma sub revenda que pertence a revenda logada
	if($verifica_subrevenda > 0) {
	
	mysql_query("Update video.streamings set codigo_cliente = '".$dados_revenda["codigo"]."' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".lang_info_subrevenda_streamings_mover_streaming_resultado_ok."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] Movido para revenda principal ".$dados_revenda["id"]."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".lang_info_subrevenda_streamings_mover_streaming_resultado_alerta."</span>";
	
	}
	
	}
	
	exit();

}

// Fun��o para mover um streaming para outra sub revenda
if($acao == "mover_streaming_subrevenda_subrevenda") {



	$login = code_decode(query_string('3'),"D");
	$id = query_string('4');
	
	if($login == "" || $id == "") {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
	
	$verifica_subrevenda_atual = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".$dados_stm["codigo_cliente"]."') AND tipo = '2'"));
	
	$verifica_subrevenda_nova = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".$id."') AND tipo = '2'"));
	
	// Verifica se o streaming � de uma sub revenda que pertence a revenda logada
	if($verifica_subrevenda_atual > 0 && $verifica_subrevenda_nova > 0) {
	
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE id = '".$id."'"));

	mysql_query("Update video.streamings set codigo_cliente = '".$dados_subrevenda["codigo"]."' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".lang_info_subrevenda_streamings_mover_streaming_resultado_ok."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	// Insere a a��o executada no registro de logs.
	logar_acao("[".$dados_stm["login"]."] Movido para sub revenda ".$dados_subrevenda["id"]."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".lang_info_subrevenda_streamings_mover_streaming_resultado_alerta."</span>";
	
	}
	
	}
	
	exit();

}

// Fun��o para buscar uma sub revenda diretamente pelo ID
if($acao == "buscar_subrevenda") {
	
	// Prote��o Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");	
	}


	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
	
	$verifica_subrevenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".query_string('3')."') AND tipo = '2'"));
	
	if($verifica_subrevenda > 0) {
	
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".query_string('3')."') AND tipo = '2'"));
	
	echo "1|".code_decode($dados_subrevenda["codigo"],"E")."";
	
	} else {
	
	echo "0|<span class='texto_status_erro'>".sprintf(lang_info_buscar_subrevenda_resultado_erro,query_string('3'))."</span>";
	
	}
	
	exit();

}

// Fun��o para remover um plano
if($acao == "remover_plano") {
	
	// Prote��o Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");	
	}


	
	$plano = code_decode(query_string('3'),"D");
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
	
	mysql_query("Delete From video.revendas_planos where codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".$plano."'");
	
	echo "<span class='texto_status_sucesso'>".lang_pagina_gerenciar_planos_remover_resultado_ok."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	exit();

}


// Fun��o para remover uma requisi��o de app android
if($acao == "remover_app_android") {


	$codigo_app = code_decode(query_string('3'),"D");
	
	$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM video.apps where codigo = '".$codigo_app."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_app["codigo_stm"]."'"));
	
	mysql_query("Delete From video.apps where codigo = '".$dados_app["codigo"]."'");
	
	if(!mysql_error()) {
	
	// Remove o apk e imagens
	@unlink("../app_android/apps/".$dados_app["zip"]."");
	
	echo "<span class='texto_status_sucesso'>Requisi��o do streaming <strong>".$dados_stm["login"]."</strong> removido com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".lang_botao_titulo_atualizar."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>N�o foi poss�vel remover a requisi��o do streaming <strong>".$dados_stm["login"]."</strong>.</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

?>