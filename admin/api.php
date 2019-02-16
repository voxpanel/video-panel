<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclus&atilde;o de classes
require_once('inc/classe.ssh.php');

/*
chave -> query_string('2');
acao -> query_string('3');
login -> query_string('4');
*/

$chave_api = query_string('2');
$acao = query_string('3');

// Verifica se a chave da api foi informada
if($chave_api == "") {
echo "0|Chave da API vazia.";
exit();
}

// Verifica se a chave da api esta configurada
$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."'"));

if($valida_revenda == 0) {
echo "0|Chave da API inv&aacute;lida.";
exit();
}

// Função para cadastrar streaming
if($acao == "cadastrar") {
	
	$login = strtolower(query_string('4'));
	$espectadores = query_string('5');
	$bitrate = query_string('6');
	$espaco = query_string('7');
	$senha = query_string('8');
	$idioma = (query_string('9')) ? query_string('9') : 'pt-br';
	$aplicacao = query_string('10');
	$identificacao = query_string('11');
	$email = query_string('12');
	
	// Valida o login contra caracteres especiais
	if(!preg_match("/^[a-z0-9]+$/", $login)) {
		echo "0|Login inválido, use apenas letra e números.";
		exit();
	}
	
	// Verifica logins restritos
	$array_logins_restritos = array("web", "streaming", "home");
	
	if(in_array($_POST["login"], $array_logins_restritos)) {
		echo "0|Login reservado, login reserved.";
		exit();
	}

	// Verifica se login já esta em uso
	$verificacao_login = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	
	if($verificacao_login > 0) {
		echo "0|Login já existente.";
		exit();
	}

	// Verifica os limites do cliente
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."'"));
	
	$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
	$total_streamings_subrevenda = mysql_fetch_array(mysql_query("SELECT SUM(streamings) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
	$total_streamings_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
	$espectadores_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
	$espectadores_stm_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
	$espaco_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
	$espaco_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

	// Verifica se excedeu o limite de streamings do cliente
	$total_streamings_revenda = $total_streamings_revenda+1;

	if($total_streamings_revenda > $dados_revenda["streamings"]) {
		echo "0|Limite de streamings atingido.";
		exit();
	}

	// Verifica se excedeu o limite de espectadores do cliente
	$total_espectadores_revenda = $espectadores_revenda["total"]+$espectadores_subrevenda_revenda["total"]+$espectadores;

	if($total_espectadores_revenda > $dados_revenda["espectadores"] && $dados_revenda["espectadores"] != 999999) {
		echo "0|Limite de espectadores atingido.";
		exit();
	}

	// Verifica se excedeu o limite de espectadores do cliente
	$total_espaco_revenda = $espaco_revenda["total"]+$espaco_subrevenda_revenda["total"]+$espaco;

	if($total_espaco_revenda > $dados_revenda["espaco"]) {
		echo "0|Limite de espa&ccedil;o para autodj atingido.";
		exit();
	}

	// Verifica se excedeu o limite de bitrate do cliente
	if($bitrate > $dados_revenda["bitrate"]) {
		echo "0|Limite de bitrate atingido.";
		exit();
	}
	
	// Carrega as configura&ccedil;ões do sistema
	$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_config["codigo_servidor_atual"]."'"));
	
	$aplicacao = ($aplicacao) ? $aplicacao : "tvstation";
	
	mysql_query("INSERT INTO video.streamings (codigo_cliente,codigo_servidor,login,senha,senha_transmissao,espectadores,bitrate,espaco,ftp_dir,data_cadastro,idioma_painel,aplicacao,identificacao,email) VALUES ('".$dados_revenda["codigo"]."','".$dados_config["codigo_servidor_atual"]."','".$login."','".$senha."','".$senha."','".$espectadores."','".$bitrate."','".$espaco."','/home/streaming/".$login."',NOW(),'".$idioma_painel."','".$aplicacao."','".$identificacao."','".$email."')");
	
	if(!mysql_error()) {
	
		// Ativa o relay no servidor aacplus
		// Conexão SSH
		$ssh = new SSH();
		$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
		$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
		
		if($aplicacao == 'tvstation') {

		// Cria a home do streaming
		$ssh->executar("/bin/mkdir -v /home/streaming/".strtolower($login).";/bin/chown streaming.streaming /home/streaming/".strtolower($login)."");
		// Copia a playlist demo para home do streaming
		$ssh->executar("/bin/cp -vp /home/streaming/demo.mp4 /home/streaming/".strtolower($login)."/;/bin/cp -vp /home/streaming/demo.smil /home/streaming/".strtolower($login)."/playlists_agendamentos.smil");
		// Configura a playlist demo
		$ssh->executar("/usr/bin/replace LOGIN ".strtolower($login)." -- /home/streaming/".strtolower($login)."/playlists_agendamentos.smil;echo OK");
		}
		
		if($aplicacao == 'vod') {
		// Cria a home do streaming
		$ssh->executar("/bin/mkdir -v /home/streaming/".strtolower($login).";/bin/chown streaming.streaming /home/streaming/".strtolower($login)."");
		}
		
		// Ativa o streaming no Wowza
		$ssh->executar("/usr/local/WowzaMediaServer/ativar ".$login." '".$senha."' ".$bitrate." ".$espectadores." ".$aplicacao."");
		
		// Loga a ação executada
		mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('cadastro_streaming_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Cadastrado streaming ".$login." no servidor ".$dados_servidor["nome"]." pela revenda ".$dados_revenda["nome"]."')");
		
		$nome_servidor = ($dados_revenda["dominio_padrao"]) ? $dados_servidor["nome"].".".$dados_revenda["dominio_padrao"] : $dados_servidor["nome"].".".$dados_config["dominio_padrao"];
		
		echo "1|".strtolower($nome_servidor)."";	
	
	} else {
		echo "0|Erro ao executar query no mysql: ".mysql_error()."";
	}
	
	exit();
}

// Função para bloquear streaming
if($acao == "bloquear") {

	$login = query_string('4');
	
	if(empty($login)) {
		echo "0|Dados faltando.";
		exit();
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|Permissao negada.";
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
	
	mysql_query("Update video.streamings set status = '3' where codigo = '".$dados_stm["codigo"]."'");
		
	// Loga a ação executada
	mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('bloquear_streaming_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Bloqueio do streaming ".$dados_stm["login"]."')");
	
	echo "1|Streaming bloqueado com sucesso.";
	
	exit();
}

// Função para desbloquear streaming
if($acao == "desbloquear") {
	
	$login = query_string('4');
	
	if(empty($login)) {
		echo "0|Dados faltando.";
		exit();
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|Permissao negada.";
		exit();
	}
	
	if($dados_stm["status"] == 2) {
		echo "0|Permissao negada.";
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
	
	mysql_query("Update video.streamings set status = '1' where codigo = '".$dados_stm["codigo"]."'");
	
	// Loga a ação executada
	mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('desbloquear_streaming_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Desbloqueio do streaming ".$dados_stm["login"]."')");
	
	echo "1|Streaming desbloqueado com sucesso.";
	
	exit();
}

// Função para desbloquear streaming
if($acao == "alterar_senha") {
	
	$login = query_string('4');
	$senha = query_string('5');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	
	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|Permissao negada.";
		exit();
	}
	
	if($dados_stm["status"] == 2) {
		echo "0|Permissao negada.";
		exit();
	}	
	
	mysql_query("Update video.streamings set senha = '".$senha."' where codigo = '".$dados_stm["codigo"]."'");
	
	if(!mysql_error()) {
	
	// Loga a ação executada
	mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('alterar_senha_streaming_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Alteração de senha do streaming ".$dados_stm["login"]."')");
	
	echo "1|Senha alterada com sucesso.";
	
	} else {
	
	echo "0|Erro ao executar query no mysql: ".mysql_error()."";
	
	}
	
	exit();
}

// Função para remover streaming
if($acao == "remover") {
	
	$login = query_string('4');
	
	if(empty($login)) {
		echo "0|Dados faltando.";
		exit();
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM video.servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|Permissao negada.";
		exit();
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Desliga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
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
	
	// Loga a ação executada
	mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('remover_streaming_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Remoção do streaming ".$dados_stm["login"]." pela revenda ".$dados_revenda["nome"]."')");
	
	echo "1|Streaming removido com sucesso.";
	
	exit();
}

// Função para cadastrar streaming
if($acao == "limite_bitrate") {
	
   	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."'"));

	foreach(array("24","32","48","64","96","128") as $bitrate){
		   
		if($bitrate <= $dados_revenda["bitrate"]) {
		   
			$array_bitrate .= $bitrate.",";

		}
		    
	}
	
	echo substr($array_bitrate,0,-1);

   exit();
}

// Função para cadastrar streaming
if($acao == "status_streaming") {

	$login = query_string('4');
	
	if(empty($login)) {
		echo "0|Dados faltando.";
		exit();
	}
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM video.streamings where login = '".$login."'"));
	
	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|Permissao negada.";
		exit();
	}
	
	$status = ($dados_stm["status"] == '1') ? "ativo" : "bloqueado";
	
	echo "1|".$status."";

}

// Função para cadastrar sub revenda
if($acao == "cadastrar_subrevenda") {
	
	$streamings = query_string('4');
	$espectadores = query_string('5');
	$bitrate = query_string('6');
	$espaco = query_string('7');
	$idioma_painel = query_string('8');
	$email_subrevenda = query_string('9');
	$senha = query_string('10');
	$subrevendas = query_string('11');

	if(empty($streamings) or empty($espectadores) or empty($bitrate) or empty($senha) or empty($idioma_painel) or empty($email_subrevenda)) {
		echo "0|Dados faltando.";
		exit();
	}
	
	// Verifica se e-mail ja esta em uso
	$verificacao_email = mysql_num_rows(mysql_query("SELECT * FROM video.revendas where email = '".$email_subrevenda."'"));
	if($verificacao_email > 0) {
		echo "0|O e-mail informado já esta em uso no sistema.";
		exit();
	}
	
	// Verifica os limites da revenda
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."'"));
	
	$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
	$total_streamings_subrevenda = mysql_fetch_array(mysql_query("SELECT SUM(streamings) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
	$total_streamings_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
	$espectadores_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
	$espectadores_stm_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espectadores) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
	$espaco_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
	$espaco_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM video.streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

	// Verifica se excedeu o limite de sub revendas
	$total_subrevendas = $total_subrevendas+1;
	
	if($total_subrevendas > $dados_revenda["subrevendas"]) {
		echo "0|Limite de sub revendas atingido.";
		exit();
	}
	
	// Verifica se excedeu o limite de streamings
	$total_streamings_revenda = $total_streamings_revenda+$total_streamings_subrevenda["total"]+$streamings;

	if($total_streamings_revenda > $dados_revenda["streamings"] && $dados_revenda["streamings"] != 999999) {
		echo "0|Limite de streamings atingido.";
		exit();
	}
	
	// Verifica se excedeu o limite de espectadores
	$total_espectadores_revenda = $espectadores_revenda["total"]+$espectadores_subrevenda_revenda["total"]+$espectadores;
	
	if($total_espectadores_revenda > $dados_revenda["espectadores"] && $dados_revenda["espectadores"] != 999999) {
		echo "0|Limite de espectadores atingido.";
		exit();
	}
	
	// Verifica se excedeu o limite de espaco FTP
	$total_espaco_revenda = $espaco_revenda["total"]+$espaco_subrevenda_revenda["total"]+$espaco;
	
	if($total_espaco_revenda > $dados_revenda["espaco"]) {
		echo "0|Limite de espaco FTP atingido.";
		exit();
	}
	
	// Verifica se excedeu o limite de bitrate
	if($bitrate > $dados_revenda["bitrate"]) {
		echo "0|Limite de bitrate atingido.";
		exit();
	}
	
	$id = gera_id();
	
	mysql_query("INSERT INTO video.revendas (codigo_revenda,id,nome,email,senha,subrevendas,streamings,espectadores,bitrate,espaco,chave_api,idioma_painel,tipo,data_cadastro) VALUES ('".$dados_revenda["codigo"]."','".$id."','".$dados_revenda["nome"]."','".$email_subrevenda."',PASSWORD('".$senha."'),'".$subrevendas."','".$streamings."','".$espectadores."','".$bitrate."','".$espaco."','".code_decode($email_subrevenda,"E")."','".$idioma_painel."','2',NOW())");
	
	if(!mysql_error()) {

		// Loga a ação executada
		mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('cadastro_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Cadastrado sub revenda ".$id." pela revenda ".$dados_revenda["nome"]."')");
	
		echo "1|".$id."|Sub revenda cadastrada com sucesso.";	
	
	} else {
		echo "0|Erro ao executar query no mysql: ".mysql_error()."";
	}
	
	exit();
	
}

// Função para bloquear sub revenda
if($acao == "bloquear_subrevenda") {
	
	$id = query_string('4');
	
	if(empty($id)) {
		echo "0|Dados faltando.";
		exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."'"));
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".$id."'"));	
	
	// Verifica se a chave da api informada é do cliente proprietario da sub revenda
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_subrevenda["codigo_revenda"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$id."|Permissao negada.";
		exit();
	}
	
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
	
	if($dados_servidor["status"] == "on") {	
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Bloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml.lock; echo OK");
	
	// Desliga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	}
	
	mysql_query("Update video.streamings set status = '2' where codigo = '".$dados_stm["codigo"]."'");
	
	}
	
	// Bloqueia as subrevendas da subrevenda
	mysql_query("Update video.revendas set status = '3' where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND tipo = '3'");
	
	mysql_query("Update video.revendas set status = '3' where codigo = '".$dados_subrevenda["codigo"]."'");
	
	echo "1|".$id."|Sub revenda bloqueada com sucesso.";
	
	// Loga a ação executada
	mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('bloquear_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Bloqueio da sub revenda ".$id."')");
	
	exit();
	
}

// Função para desbloquear sub revenda
if($acao == "desbloquear_subrevenda") {
	
	$id = query_string('4');
	
	if(empty($id)) {
		echo "0|Dados faltando.";
		exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."'"));
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".$id."') AND tipo = '2'"));	
	
	// Verifica se a chave da api informada é do cliente proprietario da sub revenda
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_subrevenda["codigo_revenda"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$id."|Permissao negada.";
		exit();
	}
	
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
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Bloqueia o streaming no servidor
	$ssh->executar("mv -f /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml.lock /usr/local/WowzaMediaServer/conf/".$dados_stm["login"]."/Application.xml; echo OK");
	
	// Liga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin startAppInstance ".$dados_stm["login"]."");
	
	mysql_query("Update video.streamings set status = '1' where codigo = '".$dados_stm["codigo"]."'");
	
	}
	
	// Desbloqueia as subrevendas da subrevenda
	mysql_query("Update video.revendas set status = '1' where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND tipo = '3'");

	mysql_query("Update video.revendas set status = '1' where codigo = '".$dados_subrevenda["codigo"]."'");
	
	echo "1|".$id."|Sub revenda desbloqueada com sucesso.";
	
	// Loga a ação executada
	mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('desbloquear_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Desbloqueio da sub revenda ".$id."')");
	
	exit();

}

// Função para remover sub revenda
if($acao == "remover_subrevenda") {
	
	$id = query_string('4');
	
	if(empty($id)) {
		echo "0|Dados faltando.";
		exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."'"));
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".$id."') AND tipo = '2'"));	
	
	// Verifica se a chave da api informada é do cliente proprietario da sub revenda
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_subrevenda["codigo_revenda"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$id."|Permissao negada.";
		exit();
	}
	
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
		echo "0|Servidor em manutencao, tente mais tarde.";
		exit();
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Desliga o streaming no servidor
	$ssh->executar("/usr/bin/java -cp /usr/local/WowzaMediaServer JMXCommandLine -jmx service:jmx:rmi://localhost:8084/jndi/rmi://localhost:8085/jmxrmi -user admin -pass admin shutdownAppInstance ".$dados_stm["login"]."");
	
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
	
	// Loga a ação executada
	mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('remover_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Streaming ".$dados_stm["login"]." removido com sucesso na remoção da sub revenda ".$id."')");
	
	}
	
	// Remove as subrevendas da subrevenda
	mysql_query("Delete From video.revendas where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND tipo = '3'");
	mysql_query("Delete From video.revendas where codigo = '".$dados_subrevenda["codigo"]."'");
		
	// Loga a ação executada
	mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('remover_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Remoção da sub revenda ".$id."')");

	echo "1|".$id."|Sub revenda removida com sucesso.";
	
	exit();	
	
}

// Função para desbloquear sub revenda
if($acao == "alterar_senha_subrevenda") {
	
	$id = query_string('4');
	$senha = query_string('5');
	
	if(empty($id) or empty($senha)) {
		echo "0|Dados faltando.";
		exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."'"));
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM video.revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".$id."') AND tipo = '2'"));	
	
	// Verifica se a chave da api informada é do cliente proprietario da sub revenda
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM video.revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_subrevenda["codigo_revenda"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$id."|Permissao negada.";
		exit();
	}

	// Altera a senha da sub revenda
	mysql_query("Update video.revendas set senha = PASSWORD('".$senha."') where codigo = '".$dados_subrevenda["codigo"]."'");
	
	echo "1|".$id."|Senha alterada com sucesso.";
	
	// Loga a ação executada
	mysql_query("INSERT INTO video.logs (acao,data,ip,log) VALUES ('alterar_senha_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Alteração de senha da sub revenda ".$id."')");
	
	exit();

}
?>