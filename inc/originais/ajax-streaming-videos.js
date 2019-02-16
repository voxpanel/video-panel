////////////////////////////////////////////////////////
///////////// Funções Gerenciamento Vídeos /////////////
////////////////////////////////////////////////////////

// Função para carregar as pastas
function carregar_pastas( login ) {

  if(login == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  // Limpa a lista de pastas já carregadas
  document.getElementById("lista-pastas").innerHTML = "";
  
  document.getElementById("status_lista_pastas").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("status_lista_pastas").style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_pastas/"+login , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado) {
	
	array_pastas = resultado.split(";");
	
	for(var cont = 0; cont < array_pastas.length; cont++) {	
	 
	if(array_pastas[cont]) {
	
	dados_pasta = array_pastas[cont].split("|");
	
	var nova_pasta = document.createElement("li");
	
	nova_pasta.innerHTML = "<img src='/img/icones/img-icone-pasta.png' align='absmiddle' />&nbsp;<a href='javascript:carregar_videos_pasta(\""+login+"\",\""+dados_pasta[0]+"\");'>"+dados_pasta[0]+"&nbsp;("+dados_pasta[1]+")</a><a href='javascript:remover_pasta(\""+login+"\",\""+dados_pasta[0]+"\")' style='float:right;'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove' title='Remover/Remove' border='0' align='absmiddle' /></a><a href='javascript:renomear_pasta(\""+login+"\",\""+dados_pasta[0]+"\")' style='float:right;padding-right:5px;'><img src='/img/icones/img-icone-renomear.png' alt='Renomear/Rename' title='Renomear/Rename' border='0' align='absmiddle' /></a>";
  
    document.getElementById("lista-pastas").appendChild(nova_pasta);
	
	document.getElementById("status_lista_pastas").style.display = "none";
	
	document.getElementById('log-sistema-fundo').style.display = "none";
    document.getElementById('log-sistema').style.display = "none";
	
	}
	
	}
	
	} else {
	
	document.getElementById("status_lista_playlists").innerHTML = "Nenhuma pasta encontrada.";
	
	}
  
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para carregar as músicas da pasta do FTP no gerenciamento de videos
function carregar_videos_pasta( login,pasta ) {
	
  if(login == "" || pasta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  // Limpa a lista de músicas já carregadas
  document.getElementById("lista-videos-pasta").innerHTML = "";
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  document.getElementById('msg_pasta').style.display = "none";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_videos_pasta/"+login+"/"+pasta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado) {
	
	array_videos = resultado.split(";");
	
	for(var cont = 0; cont < array_videos.length; cont++) {	
	 
	if(array_videos[cont]) {
	
	dados_video = array_videos[cont].split("|");
	
	var path = dados_video[0];
	var video = add3Dots(dados_video[1],45);
	var width = dados_video[2];
	var height = dados_video[3];
	var bitrate = dados_video[4];
	var duracao = dados_video[5];
	var duracao_segundos = dados_video[6];
	var bitrate_plano = dados_video[8];
	
	var novo_video = document.createElement("li");
	
	if (/[^a-z0-9_\-\. ]/gi.test(dados_video[1])) {
  
    novo_video.innerHTML = "<img src='/img/icones/img-icone-bloqueado.png' width='16' height='16' border='0' align='absmiddle' />&nbsp;["+duracao+"]&nbsp;<span title='Contém acentos/Special Chars' style='color:#FF0000;'>"+video+"</span>&nbsp;("+width+"x"+height+" @ "+bitrate+"Kbps)<span style='float:right;'><a href='javascript:renomear_video_ftp(\""+login+"\",\""+path+"\");' title='Renomear/Rename "+video+"'><img src='/img/icones/img-icone-renomear.png' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_video_ftp(\""+login+"\",\""+path+"\")'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove "+video+"' title='Remover/Remove "+video+"' border='0' align='absmiddle' /></a></span>";
	
	novo_video.style.backgroundColor = "#FFBFBF";
	novo_video.style.cursor = "pointer";
	novo_video.addEventListener("click", function(){ alert("Arquivo inválido com caracteres especiais.\n\nInvalid file with special chars.\n\nNombre de archivo no válido con caracteres especiales."); });
	
	} else if (Number(bitrate) > Number(bitrate_plano)) {
		
	novo_video.innerHTML = "<img src='/img/icones/img-icone-bloqueado.png' width='16' height='16' border='0' align='absmiddle' />&nbsp;["+duracao+"]&nbsp;"+video+"&nbsp;("+width+"x"+height+" @ <span title='Bitrate acima do plano/Bitrate great than package' style='color:#FF0000;'>"+bitrate+"Kbps</span>)<span style='float:right;'><a href='javascript:remover_video_ftp(\""+login+"\",\""+path+"\")'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove "+video+"' title='Remover/Remove "+video+"' border='0' align='absmiddle' /></a></span>";
	
	novo_video.style.backgroundColor = "#FFBFBF";
	novo_video.style.cursor = "pointer";
	novo_video.addEventListener("click", function(){ alert("Bitrate maior que o do plano.\n\nBitrate greater than the package.\n\nBitrate más alta que el límite del plan."); });
	
	} else {
	
	novo_video.innerHTML = "<img src='/img/icones/img-icone-arquivo-video.png' border='0' align='absmiddle' />&nbsp;["+duracao+"]&nbsp;"+video+"&nbsp;("+width+"x"+height+" @ "+bitrate+"Kbps)<span style='float:right;'><a href='javascript:play_video(\""+login+"\",\""+path+"\");' title='Play "+video+"'><img src='/img/icones/img-icone-player.png' width='16' height='16' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:renomear_video_ftp(\""+login+"\",\""+path+"\");' title='Renomear/Rename "+video+"'><img src='/img/icones/img-icone-renomear.png' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_video_ftp(\""+login+"\",\""+path+"\")'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove "+video+"' title='Remover/Remove "+video+"' border='0' align='absmiddle' /></a></span>";
	
	}
  
    document.getElementById("lista-videos-pasta").appendChild(novo_video);
	
	}
	
	}
	
	}
	
  document.getElementById('log-sistema-fundo').style.display = "none";
  document.getElementById('log-sistema').style.display = "none";
  
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para criar uma nova pasta no FTP
function criar_pasta( login ) {
  
  var pasta = prompt('Português: Informe o um nome para a pasta:\n(Não use caracteres especiais e acentos)\n\nEnglish: Type the name to the directory:\n\nEspañol: Introduzca el nombre para la carpeta:');
	
  if(pasta != "" && pasta != null) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/criar_pasta/"+login+"/"+pasta , true);
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

// Função para renomear uma video no FTP
function renomear_pasta( login,pasta ) {

  if(pasta == "") {  
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención."); 
  } else {
	  
  novo = prompt ("Português: Informe o novo nome:\n(Não use caracteres especiais e acentos)\n\nEnglish: Type the new name:\n\nEspañol: Introduzca el nuevo nombre:");
  
  if(novo != "" && novo != null) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/renomear_pasta/"+login+"/"+pasta+"/"+novo , true);
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
}

// Função para remover uma pasta
function remover_pasta( login,pasta ) {
  
  if(window.confirm("Português: Deseja realmente remover esta pasta e todos os seus vídeos?\n\nEnglish: Do you really want to remove this folder and all your videos?\n\nEspañol: ¿De verdad quiere eliminar esta carpeta y todos sus videos?")) {
	  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_pasta/"+login+"/"+pasta , true);
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

// Função para renomear uma video no FTP
function renomear_video_ftp( login,video,novo ) {

  if(video == "") {  
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención."); 
  } else {
	  
  novo = prompt ("Português: Informe o novo nome para o vídeo:\n(Não use caracteres especiais e acentos)\n\nEnglish: Type the new file name:\n\nEspañol: Introduzca el nuevo nombre para el video:");
  
  if(novo != "" && novo != null) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var video = video.replace("/", "|");
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/renomear_video_ftp/"+login+"/"+video+"/"+novo , true);
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
}

// Função para remover uma video no FTP
function remover_video_ftp( login,video ) {

  if(video == "") {  
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención."); 
  } else {
  
  if(window.confirm("Deseja realmente remover esta música?")) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var video = video.replace("/", "|");
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_video_ftp/"+login+"/"+video , true);
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
}

// Função para checar a estatistica de uso do plano e criar barra de porcentagem de uso
function estatistica_uso_plano( login,recurso,texto ) {
  
  document.getElementById('estatistica_uso_plano_ftp').innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/estatistica_uso_plano/"+login+"/"+recurso+"/"+texto , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
  	document.getElementById('estatistica_uso_plano_ftp').innerHTML = resultado;
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para carregar player do video(previa)
function play_video( login, video ) {
	
  if(login != "" && video != null) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/play_video/"+login+"/"+video , true);
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

// Função para obter o host
function get_host() {

var url = location.href;
url = url.split("/");

return url[2];

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