////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////
 
// Função para ligar o streaming
function ligar_streaming( login ) {

  if(login == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/ligar_streaming/"+login , true);
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
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/desligar_streaming/"+login , true);
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

// Função para reiniciar o streaming
function reiniciar_streaming( login ) {
	
  if(login == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/desligar_streaming/"+login , true);
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

// Função para checar o status do streaming e autodj
function status_streaming( login ) {
  
  document.getElementById( login ).innerHTML = "<img src='http://"+get_host()+"/movel/img/spinner.gif' />";
	
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/status_streaming/"+login , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById( login ).innerHTML = resultado;
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para checar a estatistica de uso do plano e criar barra de porcentagem de uso
function estatistica_uso_plano( login,recurso,texto ) {
  
  if(recurso == "espectadores") {
  document.getElementById('estatistica_uso_plano_espectadores').innerHTML = "<img src='http://"+get_host()+"/movel/img/spinner.gif' />";
  } else {
  document.getElementById('estatistica_uso_plano_ftp').innerHTML = "<img src='http://"+get_host()+"/movel/img/spinner.gif' />";
  }
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/estatistica_uso_plano/"+login+"/"+recurso+"/"+texto , true);
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

// Função para iniciar playlist
function menu_iniciar_playlist() {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/menu_iniciar_playlist" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  
}

// Função para iniciar uma playlist
function iniciar_playlist( login ) {
	
  if(login == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/iniciar_playlist/"+login , true);
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

// Função para carregar dados de conexão ao vivo
function dados_conexao() {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/dados_conexao" , true);
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
  http.open("GET", "/movel/funcoes-ajax/gravar_transmissao/"+acao , true);
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