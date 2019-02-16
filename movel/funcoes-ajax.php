<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("../admin/inc/classe.ssh.php");
require_once("../admin/inc/classe.ftp.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));

// Funções gerais para uso com Ajax

$acao = query_string('2');

////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Função para ligar streaming
if($acao == "ligar_streaming") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('3'),"D");
	
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
	
	if($status_streaming["status"] == "unloaded") {
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$resultado = $ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm["login"]."");
	
	if(!preg_match('/ERROR/i',$resultado)) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_ligar_stm_resultado_ok']."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
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
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('3'),"D");
		
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
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_desligar_stm_resultado_ok']."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
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
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$login = code_decode(query_string('3'),"D");
		
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
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_reiniciar_stm_resultado_ok']."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
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

	$login = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {
	echo "<font color=\"#999999\" size=\"2\"><strong>".$lang['lang_info_status_manutencao']."</strong></font>";
	exit();
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_servidor["senha"],$dados_stm["login"]);
	
	if($status_streaming["status_transmissao"] == "aovivo" && $dados_stm["status_gravando"] == "sim") {
	echo "<font color=\"#009900\" size=\"2\"><strong>".$lang['lang_info_status_aovivo']."</strong></font>&nbsp;<font color=\"#FF0000\" size=\"2\" style=\"animation: blinker 1s linear infinite;text-decoration: blink;\"><strong>(".$lang['lang_info_status_aovivo_gravando'].")</strong></font>";
	exit();
	}
	
	if($status_streaming["status_transmissao"] == "aovivo") {
	echo "<font color=\"#009900\" size=\"2\"><strong>".$lang['lang_info_status_aovivo']."</strong></font>";
	exit();
	}
	
	if($status_streaming["status"] == "loaded") {
	echo "<font color=\"#009900\" size=\"2\"><strong>".$lang['lang_info_status_ligado']."</strong></font>";
	exit();
	}
	
	echo "<font color=\"#999999\" size=\"2\"><strong>".$lang['lang_info_status_desligado']."</strong></font>";
	
	exit();
	
}

// Função para checar a quantidade de ouvintes online e criar a barra de porcentagem de uso
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
	
	echo barra_uso_plano(str_replace("-","",$porcentagem_uso_espectadores),'('.str_replace("-","",$espectadores_conectados).' de '.$dados_stm["espectadores"].')').'<br />'.$modo_texto;
		
	} else { // -> Recurso FTP
	
	$porcentagem_uso_espaco = ($dados_stm["espaco_usado"] == 0 || $dados_stm["espaco"] == 0) ? "0" : $dados_stm["espaco_usado"]*100/$dados_stm["espaco"];
	
	$modo_texto = ($texto == "sim") ? '<span class="texto_padrao_pequeno">('.tamanho($dados_stm["espaco_usado"]).' de '.tamanho($dados_stm["espaco"]).')</span>' : '';
	
	echo barra_uso_plano($porcentagem_uso_espaco,'('.tamanho($dados_stm["espaco_usado"]).' de '.tamanho($dados_stm["espaco"]).')').'<br />'.$modo_texto;
	
	}
	
	exit();
}

// Função para carregar as playlists do streaming
if($acao == "menu_iniciar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
	
	echo '<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
		  <tr>
			<td scope="col">
			<div id="quadro">
			<div id="quadro-topo"><strong>'.$lang['lang_info_pagina_informacoes_tab_menu_iniciar_playlist'].'</strong></div>
			 <div class="texto_medio" id="quadro-conteudo">
			   <table width="99%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
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
		</div>
    </td>
  </tr>
</table>
';
	
	exit();

}

// Função para iniciar uma playlist
if($acao == "iniciar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$playlist = code_decode(query_string('3'),"D");
	
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
	
	$data_inicio = formatar_data("Y-m-d H:i:s", date("Y-m-d H:i:s"), $dados_stm["timezone"]);
	
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
	
	$data_inicio = formatar_data("Y-m-d H:i:s", date("Y-m-d H:i:s"), $dados_stm["timezone"]);
	
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
	
	if($ftp->enviar_arquivo("../temp/".$resultado."","playlists_agendamentos.smil")) {
	
	@unlink("../temp/".$resultado."");
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	if($status_streaming["status"] == "loaded") {

	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	}
	
	$resultado = $ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance  ".$dados_stm["login"]."");
	
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

// Função para carregar as playlists do streaming
if($acao == "dados_conexao") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$_SESSION["login_logado"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	$stream = ($dados_stm["aplicacao"] == 'tvstation') ? "live" : $dados_stm["login"];
	
	echo '<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
		  <tr>
			<td scope="col">
			<div id="quadro">
			<div id="quadro-topo"><strong>'.$lang['lang_info_streaming_dados_conexao_tab_titulo'].'</strong></div>
			 <div class="texto_medio" id="quadro-conteudo">';
					
			if($dados_stm["aplicacao"] == 'live' || $dados_stm["aplicacao"] == 'tvstation') {
				echo '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7;">
				  <tr>
					<td width="25%" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">'.$lang['lang_info_streaming_dados_conexao_servidor'].'</td>
					<td width="75%" align="left" class="texto_padrao_pequeno">rtmp://'.dominio_servidor($dados_servidor["nome"]).':1935/'.$dados_stm["login"].'</td>
				  </tr>
				  <tr>
					<td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Stream</td>
					<td align="left" class="texto_padrao_pequeno">'.$stream.'</td>
				  </tr>
				  <tr>
					<td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Bitrate</td>
					<td align="left" class="texto_padrao_pequeno">'.$dados_stm["bitrate"].' Kbps</td>
				  </tr>
				  <tr>
					<td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">'.$lang['lang_info_streaming_dados_conexao_usuario'].'</td>
					<td align="left" class="texto_padrao_pequeno">'.$dados_stm["login"].'</td>
				  </tr>
				  <tr>
					<td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">'.$lang['lang_info_streaming_dados_conexao_senha'].'</td>
					<td align="left" class="texto_padrao_pequeno">'.$dados_stm["senha_transmissao"].'</td>
				  </tr>
				</table>';				
			}
					
	echo '</div>
		</div>
    </td>
  </tr>
</table>
';
	
	exit();

}

// Função para gravar transmissão ao vivo
if($acao == "gravar_transmissao") {
	
	// Proteção contra sessão expirada
	if(empty($_SESSION["login_logado"])) {
	
	echo "<font color=\"#FF0000\" size=\"4\"><strong>Sessão Expirada!</strong></font><br><font color=\"#FF0000\" size=\"2\"><strong>Faça login novamente.</strong></font>";
	exit();	
	}
	
	$acao_gravador = query_string('3');
		
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
?>