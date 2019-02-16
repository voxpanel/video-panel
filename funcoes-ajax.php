<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("admin/inc/classe.ssh.php");
require_once("admin/inc/classe.ftp.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));

// Funções gerais para uso com Ajax

$acao = query_string('1');

////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Função para ligar streaming
if($acao == "ligar_streaming") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	exit();	
	}
	
	if($dados_stm["aplicacao"] == "ipcamera") {
	$total_cameras = mysql_num_rows(mysql_query("SELECT * FROM stmvideo.ip_cameras where codigo_stm = '".$dados_stm["codigo"]."'"));

	if($total_cameras == 0) {
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_sem_cameras_cadastradas']."</span>";
	exit();	
	}
	
	}	
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status"] == "unloaded" || $status_streaming["status"] == "") {
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$resultado = $ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm["login"]."");
	
	if(!preg_match('/ERROR/i',$resultado)) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_ligar_stm_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["login"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_stm_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_ligar_stm_resultado_erro']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_stm_resultado_erro']."");
	
	}
	
	} else { // Já esta ligado
	
	echo "<span class='texto_status_alerta'>".$lang['lang_acao_ligar_stm_resultado_alerta']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_stm_resultado_alerta']."");
	
	}
	
	exit();
}

// Função para desligar streaming
if($acao == "desligar_streaming") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
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
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_desligar_stm_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["login"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_stm_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_desligar_stm_resultado_erro']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_stm_resultado_erro']."");
	
	}
	
	} else { // Já esta desligado
	
	echo "<span class='texto_status_alerta'>".$lang['lang_acao_desligar_stm_resultado_alerta']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_stm_resultado_alerta']."");
	
	}
	
	exit();

}

// Função para reiniciar streaming
if($acao == "reiniciar_streaming") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	exit();	
	}
	
	if($dados_stm["aplicacao"] == "ipcamera") {
	$total_cameras = mysql_num_rows(mysql_query("SELECT * FROM stmvideo.ip_cameras where codigo_stm = '".$dados_stm["codigo"]."'"));

	if($total_cameras == 0) {
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_sem_cameras_cadastradas']."</span>";
	exit();	
	}
	
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
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_reiniciar_stm_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_reiniciar_stm_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_reiniciar_stm_resultado_erro']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_reiniciar_stm_resultado_erro']."");
	
	}

	
	exit();
}

// Função para verificar o status do streaming e autodj
if($acao == "status_streaming") {
	
	// Proteção contra sessão expirada
	if(empty($_SESSION["login_logado"])) {
	
	echo "<font color=\"#FF0000\" size=\"4\"><strong>Sessão Expirada!</strong></font><br><font color=\"#FF0000\" size=\"2\"><strong>Faça login novamente.</strong></font>";
	exit();	
	}

	$login = code_decode(query_string('2'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {
	echo "<font color=\"#999999\" size=\"5\"><strong>".$lang['lang_info_status_manutencao']."</strong></font>";
	exit();
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status_transmissao"] == "aovivo" && $dados_stm["status_gravando"] == "sim") {
	echo "<font color=\"#009900\" size=\"5\"><strong>".$lang['lang_info_status_aovivo']."</strong></font><br><font color=\"#FF0000\" size=\"2\" style=\"animation: blinker 1s linear infinite;text-decoration: blink;\"><strong>".$lang['lang_info_status_aovivo_gravando']."</strong></font>";
	exit();
	}
	
	if($status_streaming["status_transmissao"] == "aovivo") {
	echo "<font color=\"#009900\" size=\"5\"><strong>".$lang['lang_info_status_transmitindo']."</strong></font><br><font color=\"#009900\" size=\"2\"><strong>".$lang['lang_info_status_aovivo']."</strong></font>";
	exit();
	}
	
	//if($status_streaming["status_transmissao"] == "relay") {
	//echo "<font color=\"#009900\" size=\"5\"><strong>".$lang['lang_info_status_transmitindo']."</strong></font><br><font color=\"#009900\" size=\"2\" style=\"animation: blinker 1s linear infinite;text-decoration: blink;\"><strong>".$lang['lang_info_status_relay']."</strong></font>";
	//exit();
	//}
	
	if($status_streaming["status"] == "loaded") {
	echo "<font color=\"#009900\" size=\"6\"><strong>".$lang['lang_info_status_ligado']."</strong></font>";
	exit();
	}
	
	echo "<font color=\"#999999\" size=\"6\"><strong>".$lang['lang_info_status_desligado']."</strong></font>";
	
	exit();
	
}

// Função para gravar transmissão ao vivo
if($acao == "gravar_transmissao") {
	
	// Proteção contra sessão expirada
	if(empty($_SESSION["login_logado"])) {
	
	echo "<font color=\"#FF0000\" size=\"4\"><strong>Sessão Expirada!</strong></font><br><font color=\"#FF0000\" size=\"2\"><strong>Faça login novamente.</strong></font>";
	exit();	
	}
	
	$acao_gravador = query_string('2');
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {
	echo "<font color=\"#999999\" size=\"5\"><strong>".$lang['lang_info_status_manutencao']."</strong></font>";
	exit();
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($acao_gravador == "iniciar") {
		
	if($status_streaming["status_transmissao"] == "aovivo") {
	
	if($dados_stm["status_gravando"] == "nao") {
	
	// Cria a pasta de gravações
	// Conexão FTP
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);
	
	$ftp->criar_pasta("/record");
	
	// Iniciar gravação	
	$arquivo = "record/rec_".date("Y-m-d_H-i-s").".mp4";
	
	$resultado = gravar_transmissao($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"],$arquivo,'iniciar');
	
	if($resultado == "ok") {
	
	mysql_query("Update video.streamings set status_gravando = 'sim', gravador_arquivo = '".$arquivo."', gravador_data_inicio = NOW() where codigo = '".$dados_stm["codigo"]."'");
	
	echo "iniciado|".$arquivo."";
	
	} else {
	
	mysql_query("Update video.streamings set status_gravando = 'nao', gravador_arquivo = '', gravador_data_inicio = '0000-00-00 00:00:00' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "erro|".$lang['lang_acao_gravar_transmissao_resultado_erro']."";
	
	} // fim resultado comando
	
	} // status_gravando
	
	}  else { // status trasmissao ao vivo
	echo "erro|".$lang['lang_acao_gravar_transmissao_resultado_erro_aovivo']."";
	} // status trasmissao ao vivo
	
	}
	
	if($acao_gravador == "parar") {
	
	// parar gravação
	$resultado = gravar_transmissao($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"],"null",'start');
	
	if($resultado == "ok") {
	
	// Fix permissão dos videos gravados
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

	$ssh->executar("/bin/chown -v streaming.streaming /home/streaming/".$dados_stm["login"]."/record/*.mp4");
	
	mysql_query("Update video.streamings set status_gravando = 'nao', gravador_arquivo = '', gravador_arquivo = '0000-00-00 00:00:00' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "parado|ok";
	
	} else {
	
	mysql_query("Update video.streamings set status_gravando = 'nao', gravador_arquivo = '', gravador_arquivo = '0000-00-00 00:00:00' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "erro|".$lang['lang_acao_gravar_transmissao_resultado_erro']."";
	
	} // fim resultado comando
	
	} // acao parar	
	
	exit();
	
}

/*// Função para gravar transmissão ao vivo
if($acao == "gravar_transmissao") {
	
	// Proteção contra sessão expirada
	if(empty($_SESSION["login_logado"])) {
	
	echo "<font color=\"#FF0000\" size=\"4\"><strong>Sessão Expirada!</strong></font><br><font color=\"#FF0000\" size=\"2\"><strong>Faça login novamente.</strong></font>";
	exit();	
	}
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {
	echo "<font color=\"#999999\" size=\"5\"><strong>".$lang['lang_info_status_manutencao']."</strong></font>";
	exit();
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status_transmissao"] == "aovivo") {
	
	if($dados_stm["status_gravando"] == "nao") {
	
	// Inicia a gravação
	
	$resultado = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"],'start');
	
	if($resultado == "ok") {
	
	mysql_query("Update video.streamings set status_gravando = 'sim' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_gravar_transmissao_resultado_iniciado']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	} else {
	
	mysql_query("Update video.streamings set status_gravando = 'nao' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_gravar_transmissao_resultado_erro']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	} // fim resultado comando
	
	} else { // else status_gravando
	
	// Para a gravação
	
	$resultado = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"],'stop');
	
	if($resultado == "ok") {
	
	// Fix permissão dos videos gravados
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

	$ssh->executar("/bin/chown -v streaming.streaming /home/streaming/".$dados_stm["login"]."/gravacao_*.mp4");
	
	mysql_query("Update video.streamings set status_gravando = 'nao' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_gravar_transmissao_resultado_parado']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_gravar_transmissao_resultado_erro']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	} // fim resultado comando
	
	} // fim status_gravando
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_gravar_transmissao_resultado_erro_aovivo']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	} // fim ao vivo
	
	exit();
	
}
*/

// Função para checar a quantidade de espectadores online e criar a barra de porcentagem de uso
if($acao == "estatistica_uso_plano") {

	$login = query_string('2');
	$recurso = query_string('3');
	$texto = query_string('4');
	
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

// Função para carregar o formulário para geração das estatísticas do streaming
if($acao == "carregar_estatisticas_streaming") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	// Proteção contra usuario não logados
	if(empty($_SESSION["login_logado"])) {
	die("<span class='texto_status_erro'>0x002 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_estatisticas_tab_titulo'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
 <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="140" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">'.$lang['lang_info_estatisticas_estatistica'].'</td>
        <td width="435" align="left">
        <select name="estatistica" class="input" id="estatistica" style="width:255px;" onchange="tipo_estatistica(this.value);">
          <option value="1">'.$lang['lang_info_estatisticas_estatistica_espectadores'].'</option>
          <option value="2">'.$lang['lang_info_estatisticas_estatistica_tempo_conectado'].'</option>
          <option value="3">'.$lang['lang_info_estatisticas_estatistica_paises'].'</option>
		  <option value="4">'.$lang['lang_info_estatisticas_estatistica_players'].'</option>
		  <option value="5">'.$lang['lang_info_estatisticas_estatistica_espectadores_hora'].'</option>
        </select>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="left">
        <table width="545" border="0" cellspacing="0" cellpadding="0" id="tabela_data">
          <tr>
            <td width="140" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">'.$lang['lang_info_estatisticas_periodo'].'</td>
        <td width="405" align="left">
        <select name="mes" class="input" id="mes" style="width:162px;">';
						
						foreach(array("01" => "".$lang['lang_info_estatisticas_periodo_01']."","02" => "".$lang['lang_info_estatisticas_periodo_02']."","03" => "".$lang['lang_info_estatisticas_periodo_03']."","04" => "".$lang['lang_info_estatisticas_periodo_04']."","05" => "".$lang['lang_info_estatisticas_periodo_05']."","06" => "".$lang['lang_info_estatisticas_periodo_06']."","07" => "".$lang['lang_info_estatisticas_periodo_07']."","08" => "".$lang['lang_info_estatisticas_periodo_08']."","09" => "".$lang['lang_info_estatisticas_periodo_09']."","10" => "".$lang['lang_info_estatisticas_periodo_10']."","11" => "".$lang['lang_info_estatisticas_periodo_11']."","12" => "".$lang['lang_info_estatisticas_periodo_12']."") as $mes => $mes_nome){
							if($mes == date("m")) {
								echo "<option value=\"".$mes."\" selected=\"selected\">".$mes_nome."</option>\n";
							} else {
								echo "<option value=\"".$mes."\">".$mes_nome."</option>\n";
							}
						}

        echo '</select>&nbsp;';
        echo '<select name="ano" class="input" id="ano" style="width:90px;">';

				$ano_inicial = date("Y")-1;
				$ano_final = date("Y")+1;
				$qtd = $ano_final-$ano_inicial;
					for($i=0; $i <= $qtd; $i++) {
							if(sprintf("%02s",$ano_inicial+$i) == date("Y")) {
								echo "<option value=\"".sprintf("%02s",$ano_inicial+$i)."\" selected=\"selected\">".sprintf("%02s",$ano_inicial+$i)."</option>\n";
							} else {
								echo "<option value=\"".sprintf("%02s",$ano_inicial+$i)."\">".sprintf("%02s",$ano_inicial+$i)."</option>\n";
							}
					}
					
        echo '</select></td>
          </tr>
        </table>
        </td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="button" class="botao" value="'.$lang['lang_botao_titulo_visualizar'].'" onclick="window.open(\'/estatisticas/\'+document.getElementById(\'estatistica\').value+\'/\'+document.getElementById(\'mes\').value+\'/\'+document.getElementById(\'ano\').value+\'\',\'conteudo\');this.disabled" />
		  </td>
      </tr>
    </table>
	</div>
</div>';
	
	exit();

}

// Função para sincronizar streaming no servidor
if($acao == "sincronizar") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$aplicacao = ($dados_stm["aplicacao"]) ? $dados_stm["aplicacao"] : "tvstation";
	
	$aplicacao_xml = $aplicacao;

	if($dados_stm["autenticar_live"] == "nao") {
	
	if($aplicacao == "tvstation" || $aplicacao == "live") {
	$aplicacao_xml = $aplicacao.'-sem-login';
	}
	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$resultado = $ssh->executar("/usr/local/WowzaMediaServer/sincronizar ".$dados_stm["login"]." '".$dados_stm["senha_transmissao"]."' ".$dados_stm["bitrate"]." ".$dados_stm["espectadores"]." ".$aplicacao_xml."");
	
	if(preg_match('/ERRO/i',$resultado)) {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_sincronizar_stm_resultado_erro']."</span><br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	exit();
	
	}
	
	// Verifica se a aplicação é IP Camera e reconfigura os aquivos .stream
	if($dados_stm["aplicacao"] == 'ipcamera') {
	
	$query_ip_cameras = mysql_query("SELECT * FROM stmvideo.ip_cameras WHERE codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
	while ($dados_ip_camera = mysql_fetch_array($query_ip_cameras)) {
	
	$ssh->executar("echo '".$dados_ip_camera["rtsp"]."' > /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/".$dados_ip_camera["stream"].";echo OK");
	}
	
	}
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_sincronizar_stm_resultado_ok']."</span><br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_sincronizar_stm_resultado_ok']."");
	
	exit();
	
}

// Função para atualizar cache player facebook
if($acao == "atualizar_cache_player_facebook") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	
	if($login == "") {
	
	echo "<span class='texto_status_erro'>".$lang['lang_alerta_dados_faltando']."</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_pagina_resolver_problemas_tab_titulo_facebook'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
   <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="left" class="texto_padrao"><br />'.sprintf($lang['lang_acao_pagina_resolver_problemas_player_facebook'],$dados_config["dominio_padrao"],$dados_stm["login"],"playerv.".$dados_config["dominio_padrao"],$dados_stm["login"]).'<br /></td>
      </tr>
    </table>
  </div>
</div>';
	
	}
	
	exit();
	
}

// Função para carregar a lista de players
if($acao == "carregar_players") {
	
	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	// Proteção contra usuario não logados
	if(empty($_SESSION["login_logado"])) {
	die("<span class='texto_status_erro'>0x002 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($dados_stm["aplicacao"] == 'live' || $dados_stm["aplicacao"] == 'tvstation') {
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_players_tab_players'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
    <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;">
          <select name="players" class="input" id="players" style="width:98%;" onchange="window.open(this.value,\'conteudo\');">
		    <option value="/gerenciar-player">'.$lang['lang_info_players_player_selecione'].'</option>
            <option value="/gerenciar-player">'.$lang['lang_info_players_player_flash_html5'].'</option>
            <option value="/gerenciar-player-celulares">'.$lang['lang_info_players_player_celulares'].'</option>
            <option value="/gerenciar-player-facebook">'.$lang['lang_info_players_player_facebook'].'</option>';
			if($dados_stm["exibir_app_android"] == 'sim') {
			echo '<option value="/app-android">'.$lang['lang_info_players_player_app_android'].'</option>';
			}
            echo '</select>
         </td>
      </tr>
    </table>
  </div>
</div>';
	
	}
	
	if($dados_stm["aplicacao"] == 'vod') {
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_players_tab_players'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
    <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;">
          <select name="players" class="input" id="players" style="width:98%;" onchange="window.open(this.value,\'conteudo\');">
		    <option value="/gerenciar-player-vod">'.$lang['lang_info_players_player_selecione'].'</option>
            <option value="/gerenciar-player-vod">'.$lang['lang_info_players_player_vod'].'</option>
			<option value="/gerenciar-player-facebook">'.$lang['lang_info_players_player_facebook'].'</option>';
            echo '</select>
         </td>
      </tr>
    </table>
  </div>
</div>';
	
	}
	
	if($dados_stm["aplicacao"] == 'ipcamera') {
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_players_tab_players'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
    <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;">
          <select name="players" class="input" id="players" style="width:98%;" onchange="window.open(this.value,\'conteudo\');">
		    <option value="/gerenciar-player-ip-camera">'.$lang['lang_info_players_player_selecione'].'</option>
            <option value="/gerenciar-player-ip-camera">'.$lang['lang_info_players_player_ip_camera'].'</option>';
            echo '</select>
         </td>
      </tr>
    </table>
  </div>
</div>';
	
	}
	
	if($dados_stm["aplicacao"] == 'relayrtsp') {
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_players_tab_players'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
    <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;">
          <select name="players" class="input" id="players" style="width:98%;" onchange="window.open(this.value,\'conteudo\');">
		    <option value="/gerenciar-player">'.$lang['lang_info_players_player_selecione'].'</option>
            <option value="/gerenciar-player">'.$lang['lang_info_players_player_flash_html5'].'</option>
            <option value="/gerenciar-player-celulares">'.$lang['lang_info_players_player_celulares'].'</option>
            <option value="/gerenciar-player-facebook">'.$lang['lang_info_players_player_facebook'].'</option>';
            echo '</select>
         </td>
      </tr>
    </table>
  </div>
</div>';
	
	}

	exit();
	
}

// Função para remover uma camera
if($acao == "remover_ip_camera") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$camera = code_decode(query_string('2'),"D");
	
	$dados_camera = mysql_fetch_array(mysql_query("SELECT * FROM stmvideo.ip_cameras where codigo = '".$camera."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_camera["codigo_stm"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	mysql_query("Delete From stmvideo.ip_cameras where codigo = '".$dados_camera["codigo"]."'");
	
	if(!mysql_error()) {
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
		
	$resultado = $ssh->executar("rm -fv /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/".$dados_camera["stream"]."");
	
	// Reinicia o streaming
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	if($status_streaming["status"] == "loaded") {

	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	}
	
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance  ".$dados_stm["login"]."");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_gerenciador_ip_cameras_remover_resultado_ok']."</span><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_gerenciador_ip_cameras_remover_resultado_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}

////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Playlists /////////////
////////////////////////////////////////////////////////

// Função para carregar as playlists
if($acao == "carregar_lista_playlists") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."'"));
	
	if($total_playlists > 0) {

	$query = mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by nome ASC");
	while ($dados_playlist = mysql_fetch_array($query)) {
	
	$total_videos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo != 'hc'"));
	
	echo "".$dados_playlist["codigo"]."|".$dados_playlist["nome"]."|".$total_videos.";";
	
	}
	
	}
	
	exit();

}

// Função para carregar as pastas(avançado)
if($acao == "carregar_pastas") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$xml_pastas = @simplexml_load_file("http://".$dados_servidor["ip"].":55/listar-pastas.php?login=".$dados_stm["login"]."");
	
	$total_pastas = count($xml_pastas->pasta);

	if($total_pastas > 0) {

	for($i=0;$i<$total_pastas;$i++){
	
	$lista_pastas .= $xml_pastas->pasta[$i]->nome."|".$xml_pastas->pasta[$i]->total.";";
	
	}
	
	}
	
	echo $lista_pastas;
		
	exit();

}

// Função para carregar videos do streaming(avançado)
if($acao == "carregar_videos_pasta") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	//die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	$pasta = str_replace(" ","%20",query_string('3'));
	$ordenar = query_string('4');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
		
	$xml_videos = @simplexml_load_file("http://".$dados_servidor["ip"].":55/listar-videos.php?login=".$dados_stm["login"]."&pasta=".$pasta."&ordenar=".$ordenar."");
	
	$total_videos = count($xml_videos->video);

	if($total_videos > 0) {

	for($i=0;$i<$total_videos;$i++){
	
	$path_separacao = ($pasta == "/" || $pasta == "") ? "" : "/";
	
	$lista_videos .= $pasta.$path_separacao.utf8_decode($xml_videos->video[$i]->nome)."|".utf8_decode($xml_videos->video[$i]->nome)."|".$xml_videos->video[$i]->width."|".$xml_videos->video[$i]->height."|".$xml_videos->video[$i]->bitrate."|".$xml_videos->video[$i]->duracao."|".$xml_videos->video[$i]->duracao_segundos."|".$xml_videos->video[$i]->thumb."|".$dados_stm["bitrate"].";";
	
	}
	
	}
	
	echo $lista_videos;
		
	exit();

}

// Função para carregar videos do streaming(avançado)
if($acao == "carregar_videos_pasta_playlists") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	$pasta = str_replace(" ","%20",query_string('3'));
	$ordenar = query_string('4');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
		
	$xml_videos = @simplexml_load_file("http://".$dados_servidor["ip"].":55/listar-videos.php?login=".$dados_stm["login"]."&pasta=".$pasta."&ordenar=".$ordenar."");
	
	$total_videos = count($xml_videos->video);

	if($total_videos > 0) {

	for($i=0;$i<$total_videos;$i++){
	
	$path_separacao = ($pasta == "/" || $pasta == "") ? "" : "/";
	
	$lista_videos .= $pasta.$path_separacao.utf8_decode($xml_videos->video[$i]->nome)."|".utf8_decode($xml_videos->video[$i]->nome)."|".$xml_videos->video[$i]->width."|".$xml_videos->video[$i]->height."|".$xml_videos->video[$i]->bitrate."|".$xml_videos->video[$i]->duracao."|".$xml_videos->video[$i]->duracao_segundos."|".$xml_videos->video[$i]->thumb."|".$dados_stm["bitrate"].";";
	
	}
	
	}
	
	echo $lista_videos;
		
	exit();

}

// Função para carregar videos do playlist
if($acao == "carregar_videos_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$playlist = query_string('2');
	
	$total_videos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$playlist."'"));

	if($total_videos > 0) {

	$query = mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$playlist."' ORDER by ordem+0,codigo ASC");
	while ($dados_playlist_video = mysql_fetch_array($query)) {
	
	echo "".$dados_playlist_video["path_video"]."|".$dados_playlist_video["video"]."|".$dados_playlist_video["width"]."|".$dados_playlist_video["height"]."|".$dados_playlist_video["bitrate"]."|".$dados_playlist_video["duracao"]."|".$dados_playlist_video["duracao_segundos"]."|".$dados_playlist_video["tipo"]."|".$dados_playlist_video["thumb"]."|".code_decode($_SESSION["login_logado"],"E").";";
	
	// Fix - $_SESSION["login_logado"] -> mostra login pois na funcao ajax nao tem login
	
	}
	
	}
	
	exit();

}

// Função para criar player do video(previa)
if($acao == "play_video") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$login = code_decode(query_string('2'),"D");
	
	$video = (query_string('4')) ? query_string('3')."/".query_string('4') : query_string('3');

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	echo '<iframe src="http://'.$dados_servidor["ip"].':55/play.php?login='.$dados_stm["login"].'&video='.$video.'" frameborder="0" width="320" height="240" scrolling="no"></iframe>';
	
	exit();

}

// Função para criar nova playlist
if($acao == "criar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	$playlist = query_string('3');

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));

	mysql_query("INSERT INTO video.playlists (codigo_stm,nome,data) VALUES ('".$dados_stm["codigo"]."','".$playlist."',NOW())");
	$codigo_playlist = mysql_insert_id();
	
	if(!mysql_error()) {
	
	echo "ok|".code_decode($codigo_playlist,"E")."";
	
	} else {
	
	echo $lang['lang_acao_gerenciador_playlists_resultado_erro']." ".mysql_error()."";
	
	}
	
	exit();

}

// Função para remover música da playlist
if($acao == "remover_video") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$video = query_string('2')."/".query_string('3');

	$verifica_video = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where path_video = '".$video."'"));
	
	if($verifica_video == 1) {
	
	mysql_query("Delete From video.playlists_videos where path_video = '".$video."'") or die(mysql_error());
	
	}
	
	exit();

}

// Função para remover uma playlist
if($acao == "remover_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$playlist = code_decode(query_string('2'),"D");
	
	$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".$playlist."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_playlist["codigo_stm"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$verifica_playlist = mysql_num_rows(mysql_query("SELECT * FROM video.playlists where codigo = '".$playlist."'"));
	
	if($verifica_playlist == 1) {
	
	mysql_query("Delete From video.playlists where codigo = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From video.playlists_agendamentos where codigo_playlist = '".$dados_playlist["codigo"]."'");

	// Atualiza o arquivo de agendamento de playlists
	$query_agendamentos = mysql_query("SELECT * FROM video.playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
	while ($dados_agendamento = mysql_fetch_array($query_agendamentos)) {
	
	$dados_playlist_agendamento = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".$dados_agendamento["codigo_playlist"]."'"));
	
	$playlist = $dados_agendamento["codigo"]."_".formatar_nome_playlist($dados_playlist_agendamento["nome"]);
	
	$total_videos_playlist = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist_agendamento["codigo"]."'"));
	
	if($total_videos_playlist > 0) {
	
	$query_videos = mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist_agendamento["codigo"]."' ORDER by ordem+0,codigo ASC");
	while ($dados_playlist_video = mysql_fetch_array($query_videos)) {
	$lista_videos .= $dados_playlist_video["path_video"].",";
	}
	
	$data_inicio = formatar_data("Y-m-d H:i:s", "".$dados_agendamento["data"]." ".$dados_agendamento["hora"].":".$dados_agendamento["minuto"].":00", $dados_stm["timezone"]);
	
	$config_playlist[$playlist]["playlist"] = $dados_agendamento["codigo"]."_".formatar_nome_playlist($dados_playlist_agendamento["nome"]);
	$config_playlist[$playlist]["data_inicio"] = $data_inicio;
	$config_playlist[$playlist]["total_videos"] = $total_videos_playlist;
	$config_playlist[$playlist]["videos"] = substr($lista_videos,0,-1);
	
	unset($lista_videos);
	}
	
	}
	
	$array_config_playlists = array ("login" => $dados_stm["login"], "playlists" => $config_playlist);
	
	$resultado = gerar_playlist($array_config_playlists);
	
	// Envia via FTP
	// Conexão FTP
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);
	
	$ftp->enviar_arquivo("/home/painelvideo/public_html/temp/".$resultado."","playlists_agendamentos.smil");
	
	@unlink("/home/painelvideo/public_html/temp/".$resultado."");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_remover_playlist_resultado_ok']."</span><br /><a href='javascript:window.location.reload();' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_playlist_resultado_erro']."</span>";
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_playlist_resultado_alerta']."</span>";
	
	}
	
	exit();

}


// Função para iniciar transmissão de uma playlist pelo gerenciador de playlists
if($acao == "iniciar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$playlist = code_decode(query_string('2'),"D");
	
	$dados_playlist_selecionada = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".$playlist."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_playlist_selecionada["codigo_stm"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	exit();	
	}
	
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
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_iniciar_playlist_stm_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_iniciar_playlist_stm_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>0x001 ".$lang['lang_acao_iniciar_playlist_stm_resultado_erro']."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_iniciar_playlist_stm_resultado_erro']."");
	
	}
	
	exit();
}

// Função para iniciar transmissão de uma playlist pelo gerenciador de playlists
if($acao == "menu_iniciar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_pagina_informacoes_tab_menu_iniciar_playlist'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
   <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="center" class="texto_padrao">
		<select name="playlist" class="input" id="playlist" style="width:98%;" onchange="iniciar_playlist( document.getElementById(\'playlist\').value );">';
		echo '<option value="" selected="selected">'.$lang['lang_info_selecionar_opcao'].'</option>';
		echo '<optgroup label="Playlists">';
		
		$sql = mysql_query("SELECT * FROM video.playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
		while ($dados_playlist = mysql_fetch_array($sql)) {
		
		$total_videos = mysql_num_rows(mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'"));
		$duracao = mysql_fetch_array(mysql_query("SELECT *,SUM(duracao_segundos) as total FROM video.playlists_videos where codigo_playlist = '".$dados_playlist["codigo"]."'"));
		
		if($total_videos > 0) {
		echo '<option value="'.code_decode($dados_playlist["codigo"],"E").'">'.$dados_playlist["nome"].' ('.gmdate("H:i:s", $duracao["total"]).')</option>';
		} else {
		echo '<option value="'.code_decode($dados_playlist["codigo"],"E").'" disabled="disabled">'.$dados_playlist["nome"].' ('.$lang['lang_info_sem_videos'].')</option>';
		}
		
		}
		
		echo '</optgroup>
		</select>
		 </td>
      </tr>
    </table>
  </div>
</div>';
	
	exit();
}

// Função para remover um agendamento de playlist
if($acao == "remover_agendamento") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo = code_decode(query_string('2'),"D");
	
	$dados_agendamento_atual = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists_agendamentos where codigo = '".$codigo."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_agendamento_atual["codigo_stm"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	mysql_query("Delete From video.playlists_agendamentos where codigo = '".$dados_agendamento_atual["codigo"]."'");

	// Gera o arquivo com as playlists e envia para o FTP do streaming
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
	
	}
	
	$array_config_playlists = array ("login" => $dados_stm["login"], "playlists" => $config_playlist);
	
	$resultado = gerar_playlist($array_config_playlists);
	
	// Envia via FTP
	// Conexão FTP
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);
	
	if(!$ftp->enviar_arquivo("/home/painelvideo/public_html/temp/".$resultado."","playlists_agendamentos.smil")) {
	
	@unlink("/home/painelvideo/public_html/temp/".$resultado."");
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_agendamento_resultado_alerta']."</span>";
	
	exit();
	
	}
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_remover_agendamento_resultado_ok']."</span><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_agendamento_resultado_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para remover agendamentos de relay
if($acao == "remover_agendamento_relay") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo = code_decode(query_string('2'),"D");
	
	mysql_query("Delete From stmvideo.agendamentos_relay where codigo = '".$codigo."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_remover_agendamento_relay_resultado_ok']."</span><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_agendamento_relay_resultado_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}


// Função para remover a configuração de Vinhetas & Comerciais
if($acao == "remover_comerciais_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$playlist = code_decode(query_string('2'),"D");
	
	$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".$playlist."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_playlist["codigo_stm"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	mysql_query("Delete From video.playlists_videos where tipo = 'comercial' AND codigo_playlist = '".$dados_playlist["codigo"]."'");
	
	if(!mysql_error()) {
	
	// Marca como desativado na playlist
	mysql_query("Update video.playlists set comerciais = 'nao' where codigo = '".$dados_playlist["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_gerenciador_playlists_remover_comerciais_resultado_ok']."</span><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_gerenciador_playlists_remover_comerciais_resultado_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para duplicar(copiar) uma playlist
if($acao == "duplicar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$playlist_atual = code_decode(query_string('2'),"D");
	$playlist_nova = query_string('3');

	$dados_playlist_atual = mysql_fetch_array(mysql_query("SELECT * FROM video.playlists where codigo = '".$playlist_atual."'"));	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where codigo = '".$dados_playlist_atual["codigo_stm"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	mysql_query("INSERT INTO video.playlists (codigo_stm,nome,data) VALUES ('".$dados_stm["codigo"]."','".$playlist_nova."',NOW())");
	$codigo_playlist = mysql_insert_id();
		
	$sql_playlist_atual_videos = mysql_query("SELECT * FROM video.playlists_videos where codigo_playlist = '".$dados_playlist_atual["codigo"]."'");
	while ($dados_video_playlist_atual = mysql_fetch_array($sql_playlist_atual_videos)) {
	
	// Adiciona música na playlist
	mysql_query("INSERT INTO video.playlists_videos (codigo_playlist,path_video,video,width,height,bitrate,duracao,duracao_segundos,tipo,ordem) VALUES ('".$codigo_playlist."','".addslashes($dados_video_playlist_atual["path_video"])."','".addslashes($dados_video_playlist_atual["video"])."','".$dados_video_playlist_atual["width"]."','".$dados_video_playlist_atual["height"]."','".$dados_video_playlist_atual["bitrate"]."','".$dados_video_playlist_atual["duracao"]."','".$dados_video_playlist_atual["duracao_segundos"]."','".$dados_video_playlist_atual["tipo"]."','".$dados_video_playlist_atual["ordem"]."')");

	}
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_gerenciador_playlists_duplicar_resultado_ok']."</span><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_gerenciador_playlists_duplicar_resultado_erro']." ".mysql_error()."</span>";	
	
	}
	
	exit();

}
	
////////////////////////////////////////////////////////
///////////// Funções Gerenciamento Vídeos /////////////
////////////////////////////////////////////////////////

// Função para criar nova pasta
if($acao == "criar_pasta") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	$pasta = remover_acentos(query_string('3'));

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	// Conexão SSH
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);
	
	$resultado = $ftp->criar_pasta($pasta);
	
	if($resultado) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_criar_pasta_resultado_ok']."</span><br /><a href='javascript:carregar_pastas(\"".query_string('2')."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_criar_pasta_resultado_erro']."</span>";
	
	}
	
	exit();

}

// Função para renomear uma pasta no FTP
if($acao == "renomear_pasta") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	$antigo = query_string('3');
	$novo = query_string('4');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	// Conexão SSH
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);
	
	$resultado = $ftp->renomear($antigo,$novo);
	
	if($resultado) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_renomear_pasta_resultado_ok']."</span><br /><a href='javascript:carregar_pastas(\"".query_string('2')."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_renomear_pasta_resultado_erro']."</span>";
	
	}
	
	exit();

}

// Função para remover uma pasta
if($acao == "remover_pasta") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	$pasta = query_string('3');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	// Conexão SSH
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);
	
	$resultado = $ftp->remover_pasta($pasta);
	
	if($resultado) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_remover_pasta_resultado_ok']."</span><br /><a href='javascript:carregar_pastas(\"".query_string('2')."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_pasta_resultado_erro']."</span>";
	
	}
	
	exit();

}

// Função para renomear uma video no FTP
if($acao == "renomear_video_ftp") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	list($pasta, $video) = explode("|",query_string('3'));
	$novo = query_string('4');
	
	$pasta = ($pasta == "") ? '/' : $pasta;
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	// Conexão SSH
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);
	
	$resultado = $ftp->renomear($pasta."/".$video,$pasta."/".$novo.".mp4");
	
	if($resultado) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_renomear_video_resultado_ok']."</span><br /><a href='javascript:carregar_videos_pasta(\"".query_string('2')."\",\"".$pasta."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_renomear_video_resultado_erro']."</span>";
	
	}
	
	exit();

}

// Função para remover uma música no FTP
if($acao == "remover_video_ftp") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('2'),"D");
	list($pasta, $video) = explode("|",query_string('3'));
	
	$pasta = ($pasta == "") ? '/' : $pasta;
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	// Conexão SSH
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["login"],$dados_stm["senha"]);
	
	$resultado = $ftp->remover_arquivo($pasta."/".$video);
	
	if($resultado) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_remover_video_resultado_ok']."</span><br /><a href='javascript:carregar_videos_pasta(\"".query_string('2')."\",\"".$pasta."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_video_resultado_erro']."</span>";
	
	}
	
	exit();

}

//////////////////////////////////////////////////////
/////////// Funções Gerenciamento Painel /////////////
//////////////////////////////////////////////////////

// Função para exibir avisos
if($acao == "exibir_aviso") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo_aviso = query_string('2');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
	$dados_aviso = mysql_fetch_array(mysql_query("SELECT * FROM video.avisos where codigo = '".$codigo_aviso."'"));
	
	$area = ($_SESSION["login_logado"]) ? 'streaming' : 'revenda';
	$codigo_usuario = ($area == "streaming") ? $_SESSION["login_logado"] : $_SESSION["code_user_logged"];

	if(!mysql_error()) {
	
	echo "<div id=\"quadro\">
			<div id=\"quadro-topo\"><strong>".$lang['lang_info_aviso_titulo']."</strong></div>
				<div class=\"texto_padrao\" id=\"quadro-conteudo\">
				".$dados_aviso["mensagem"]."<br><br>
				<span class=\"texto_padrao_vermelho\">".$lang['lang_info_aviso_data']." ".formatar_data($dados_stm["formato_data"], $dados_aviso["data"], $dados_stm["timezone"])."</span><br>
				<span class=\"texto_padrao_pequeno\"><input type=\"checkbox\" onclick=\"desativar_exibicao_aviso('".$codigo_aviso."', '".$area."', '".$codigo_usuario."');\" style=\"vertical-align:middle;\" />&nbsp;".$lang['lang_info_aviso_desativar']."</span>
				</div>
		  </div>";
	
	}
	
	exit();

}

// Função para marcar um aviso como vizualizado
if($acao == "desativar_exibicao_aviso") {

	$codigo_aviso = query_string('2');
	$area = query_string('3');
	$codigo_usuario = query_string('4');
	
	@mysql_query("INSERT INTO video.avisos_desativados (codigo_aviso,login,area,data) VALUES ('".$codigo_aviso."','".$codigo_usuario."','".$area."',NOW())");
	
	exit();

}

// Função para obter o domínio dos servidores CDN
if($acao == "get_host_cdn") {
	
	echo $dados_config["dominio_cdn"];

	exit();
	
}


// Função para remover uma requisição de app android
if($acao == "remover_app_android") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo_app = code_decode(query_string('2'),"D");
	
	$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM video.apps where codigo = '".$codigo_app."'"));
	
	mysql_query("Delete From video.apps where codigo = '".$dados_app["codigo"]."'");
	
	// Remove o apk e imagens
	@unlink("app_android/apps/".$dados_app["zip"]."");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_streaming_app_android_resultado_remover_app_ok']."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	exit();

}
?>