////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Função para ligar o streaming
function ligar_streaming( login ) {

  if(login == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/ligar_streaming/"+login , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;
	document.getElementById("log-sistema-conteudo").style.fontSize = "25px";
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para desligar o streaming
function desligar_streaming( login ) {
	
  if(login == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/desligar_streaming/"+login , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para reiniciar o streaming/autodj
function reiniciar_streaming( login ) {

  if(login == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/reiniciar_streaming/"+login , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}  
  
}

// Função para iniciar playlist selecionada
function iniciar_playlist( playlist ) {

  if(playlist == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/iniciar_playlist/"+playlist , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}  
  
}

// Função para iniciar playlist
function menu_iniciar_playlist() {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/menu_iniciar_playlist" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  
}

// Função para checar o status do streaming e autodj
function status_streaming( login ) {
  
  document.getElementById( 'status_streaming' ).innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
	
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/status_streaming/"+login , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById( 'status_streaming' ).innerHTML = resultado;
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para checar a estatistica de uso do plano e criar barra de porcentagem de uso
function estatistica_uso_plano( login,recurso,texto ) {
  
  if(recurso == "espectadores") {
  document.getElementById('estatistica_uso_plano_espectadores').innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
  } else {
  document.getElementById('estatistica_uso_plano_ftp').innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
  }
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/estatistica_uso_plano/"+login+"/"+recurso+"/"+texto , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(recurso == "espectadores") {
  	document.getElementById('estatistica_uso_plano_espectadores').innerHTML = resultado;
  	} else {
  	document.getElementById('estatistica_uso_plano_ftp').innerHTML = resultado;
  	}
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para carregar a lista de players
function carregar_players() {
	
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_players" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para carregar o formulário para geração das estatísticas do streaming
function carregar_estatisticas_streaming( login ) {
	
  if(login == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_estatisticas_streaming/"+login , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para remover uma camera
function remover_ip_camera( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_ip_camera/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}


////////////////////////////////////////////////////////
//////////// Funções Gerenciamento OnDemand ////////////
////////////////////////////////////////////////////////

// Função para remover um agendamento de playlist
function remover_agendamento( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_agendamento/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para remover um agendamento de relay
function remover_agendamento_relay( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_agendamento_relay/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

////////////////////////////////////////////////////////
///////////// Funções Gerenciamento Painel /////////////
////////////////////////////////////////////////////////

// Função para exibir avisos
function exibir_aviso( codigo ) {
	
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/exibir_aviso/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para marcar um aviso como vizualizado
function desativar_exibicao_aviso( codigo, area, codigo_usuario ) {
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/desativar_exibicao_aviso/"+codigo+"/"+area+"/"+codigo_usuario , true);
  http.send(null);
  delete http;
  
}

// Função para sincronizar streaming no servidor
function sincronizar( login ) {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/sincronizar/"+login , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para sincronizar playlists no servidor
function sincronizar_playlists( login ) {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/sincronizar_playlists/"+login , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para atualizar cache player facebook
function atualizar_cache_player_facebook( login ) {

  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/atualizar_cache_player_facebook/"+login , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para gravar transmissão ao vivo
function gravar_transmissao( acao ) {
  
  document.getElementById("status").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("status").style.display = "block";

  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/gravar_transmissao/"+acao , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	resultado_partes = resultado.split("|");
	
	var status = resultado_partes[0];
	var status_msg_erro = resultado_partes[1];
	var arquivo = resultado_partes[1];
	
	if(status == "iniciado") {
		document.getElementById("arquivo").innerHTML = "";
		document.getElementById("status").style.display = "none";
		document.getElementById("status_gravacao").style.display = "block";
		document.getElementById("botao_iniciar").style.display = "none";
		document.getElementById("botao_parar").style.display = "block";
		document.getElementById("arquivo").innerHTML = arquivo;
		contador_gravacao();
	}
	
	if(status == "parado") {	
		document.getElementById("status").style.display = "none";
		document.getElementById("status_gravacao").style.display = "none";
		document.getElementById("botao_iniciar").style.display = "block";
		document.getElementById("botao_parar").style.display = "none";
	}
	
	if(status == "" || status == "erro") {
		document.getElementById("status").innerHTML = status_msg_erro;
		document.getElementById("status_gravacao").style.display = "none";
		document.getElementById("botao_iniciar").style.display = "block";
		document.getElementById("botao_parar").style.display = "none";
		document.getElementById("arquivo").innerHTML = "";
	}	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para executar o script de download de videos do youtube
function youtube_downloader( servidor, login, url ) {
  
  if(url == "") {
  alert("Error!\n\nPortuguês: URL do vídeo inválida.\n\nEnglish: Invalid video URL.\n\nEspañol: URL del vídeo no es válido.");
  document.getElementById("quadro_requisicao").style.display = "none";
  } else {
  
  var id_youtube = youtube_parser(url);
  
  if(id_youtube == "") {
  alert("Error!\n\nPortuguês: URL do vídeo inválida.\n\nEnglish: Invalid video URL.\n\nEspañol: URL del vídeo no es válido.");
  document.getElementById("quadro_requisicao").style.display = "none";
  } else {

  document.getElementById("img_loader").style.display = "block";
  document.getElementById("quadro_requisicao").style.display = "block";
  document.getElementById("resultado_requisicao").innerHTML = "";
  
  var http = new Ajax();
  http.open("GET", "http://"+servidor+":55/youtube.php?login="+login+"&video="+id_youtube+"" , true);
  http.onreadystatechange = function() {
  
  document.getElementById("resultado_requisicao").innerHTML = http.responseText;
  
  // Auto scroll
  var elem = document.getElementById('resultado_requisicao');
  elem.scrollTop = elem.scrollHeight;
  
  if(http.readyState == 4) {
  document.getElementById("img_loader").style.display = "none";
  }
  
  }
  http.send(null);
  delete http;
  }
  }
}

// Função para executar o script de conversão de videos
function converter_video( servidor, login ) {
	
  var dados_video = document.getElementById("video").value;
  
  if(dados_video == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  document.getElementById("quadro_requisicao").style.display = "none";
  } else {

  document.getElementById("img_loader").style.display = "block";
  document.getElementById("quadro_requisicao").style.display = "block";
  document.getElementById("resultado_requisicao").innerHTML = "";
  
  //var lista_videos = document.getElementById("video");
  //var videos_selecionados = [];
  //for (var i = 0; i < lista_videos.length; i++) {
  //      if (lista_videos.options[i].selected) videos_selecionados.push(lista_videos.options[i].value);
  //}
  
  var dados_video_partes = dados_video.split("|");
  var pasta = dados_video_partes[0].replace(" ", "%20");
  var video = dados_video_partes[1].replace(" ", "%20");
  var framerate = dados_video_partes[2];
  var video_resolucao = document.getElementById("video_resolucao").value;
  var video_framerate = document.getElementById("video_framerate").value;
  var video_bitrate = document.getElementById("video_bitrate").value;
  var audio_bitrate = document.getElementById("audio_bitrate").value;
  var audio_samplerate = document.getElementById("audio_samplerate").value;
  var remover_source = (document.getElementById("remover_source").checked === true) ? "sim" : "nao";
  
  var http = new Ajax();
  http.open("GET", "http://"+servidor+":55/conversor-video.php?login="+login+"&pasta="+pasta+"&video="+video+"&video_resolucao="+video_resolucao+"&video_framerate_atual="+framerate+"&video_framerate_novo="+video_framerate+"&video_bitrate="+video_bitrate+"&audio_bitrate="+audio_bitrate+"&audio_samplerate="+audio_samplerate+"&remover_source="+remover_source+"" , true);
  http.onreadystatechange = function() {
  
  document.getElementById("resultado_requisicao").innerHTML = http.responseText;
  
  // Auto scroll
  var elem = document.getElementById('resultado_requisicao');
  elem.scrollTop = elem.scrollHeight;
  
  if(http.readyState == 4) {
  document.getElementById("img_loader").style.display = "none";
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para desligar o streaming
function remover_app_android( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_app_android/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Rotina AJAX
function Ajax() {
var req;

try {
 req = new ActiveXObject("Microsoft.XMLHTTP");
} catch(e) {
 try {
	req = new ActiveXObject("Msxml2.XMLHTTP");
 } catch(ex) {
	try {
	 req = new XMLHttpRequest();
	} catch(exc) {
	 alert("Esse browser não tem recursos para uso do Ajax");
	 req = null;
	}
 }
}

return req;
}