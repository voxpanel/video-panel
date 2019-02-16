////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Playlist /////////////
////////////////////////////////////////////////////////

// Função para criar uma nova playlist
function criar_playlist( login ) {
  
  var playlist = prompt("Nome:\n(Não use caracteres especiais e acentos)");
	
  if(playlist != "" && playlist != null) {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";	
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/criar_playlist/"+login+"/"+playlist , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	resultado_partes = resultado.split("|");
	
	if(resultado_partes[0] == "ok") {

    window.open('/gerenciar-playlists/'+resultado_partes[1]+'','conteudo');
	
	} else {
	
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>"+resultado_partes[1]+"</span>";
	document.getElementById("log-sistema-fundo").style.display = "block";
    document.getElementById("log-sistema").style.display = "block";	
	
	}
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para carregar as pastas
function carregar_pastas( login ) {

  if(login == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  // Limpa a lista de playlist já carregadas
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
	
	nova_pasta.innerHTML = "<img src='/img/icones/img-icone-pasta.png' align='absmiddle' />&nbsp;<a href='javascript:carregar_videos_pasta(\""+login+"\",\""+dados_pasta[0]+"\");'>"+dados_pasta[0]+"&nbsp;("+dados_pasta[1]+")</a>";
  
    document.getElementById("lista-pastas").appendChild(nova_pasta);
	
	document.getElementById("status_lista_pastas").style.display = "none";
	
	}
	
	}
	
	}
  
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para carregar as vídeos da pasta do FTP no gerenciamento de playlist
function carregar_videos_pasta( login,pasta ) {
	
  if(login == "" || pasta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  // Limpa a lista de vídeos já carregadas
  document.getElementById("lista-videos-pasta").innerHTML = "";
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";
  document.getElementById("msg_pasta").style.display = "none";
  
  if(document.getElementById("ordenar_videos_pasta").checked) {
  var ordenar = "sim";
  }
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_videos_pasta_playlists/"+login+"/"+pasta+"/"+ordenar , true);
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
	
	novo_video.innerHTML = "<img src='/img/icones/img-icone-bloqueado.png' width='16' height='16' border='0' align='absmiddle' />&nbsp;["+duracao+"] <span title='Contém acentos/Special Chars' style='color:#FF0000;'>"+video+"</span>&nbsp;("+width+"x"+height+" @ "+bitrate+"Kbps)";
	
	novo_video.style.backgroundColor = "#FFBFBF";
	novo_video.style.cursor = "pointer";
	novo_video.addEventListener("click", function(){ alert("Arquivo inválido com caracteres especiais.\n\nInvalid file with special chars.\n\nNombre de archivo no válido con caracteres especiales."); });
	
	} else if (Number(bitrate) > Number(bitrate_plano)) {
	
	novo_video.innerHTML = "<img src='/img/icones/img-icone-bloqueado.png' width='16' height='16' border='0' align='absmiddle' />&nbsp;["+duracao+"] "+video+"&nbsp;("+width+"x"+height+" <span title='Bitrate acima do plano/Bitrate great than package' style='color:#FF0000;'>@ "+bitrate+"Kbps)</span>";
	
	novo_video.style.backgroundColor = "#FFBFBF";
	novo_video.style.cursor = "pointer";
	novo_video.addEventListener("click", function(){ alert("Bitrate maior que o do plano.\n\nBitrate greater than the package.\n\nBitrate más alta que el límite del plan."); });
	
	} else {
  
    novo_video.innerHTML = "<input id='videos_pasta' login='"+login+"' video='"+video+"' duracao='"+duracao+"' duracao_segundos='"+duracao_segundos+"' wth='"+width+"' hht='"+height+"' bitrate='"+bitrate+"' type='checkbox' value='"+path+"' style='display:none' checked /><img src='/img/icones/img-icone-arquivo-video.png' border='0' align='absmiddle' />&nbsp;<a href='javascript:adicionar_video_playlist(\""+login+"\",\""+path+"\",\""+video+"\",\""+width+"\",\""+height+"\",\""+bitrate+"\",\""+duracao+"\",\""+duracao_segundos+"\");'>["+duracao+"] "+video+"&nbsp;("+width+"x"+height+" @ "+bitrate+"Kbps)</a><span style='float:right;'><a href='javascript:play_video(\""+login+"\",\""+path+"\");' title='Play "+video+"'><img src='/img/icones/img-icone-player.png' width='16' height='16' border='0' style='padding-right:5px;' align='absmiddle' /></a></span>";
	
	}
  	
    document.getElementById("lista-videos-pasta").appendChild(novo_video);
	
	document.getElementById("log-sistema-fundo").style.display = "none";
    document.getElementById("log-sistema").style.display = "none";
	
	}
	
	}
	
	} else {

	document.getElementById("msg_pasta").innerHTML = "A pasta selecionada não possui vídeos. Você deve enviar os vídeos usando FTP.";
	document.getElementById("msg_pasta").style.display = "block";
	document.getElementById("log-sistema-fundo").style.display = "none";
    document.getElementById("log-sistema").style.display = "none";
	
	}
  
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para carregar as vídeos da playlist
function carregar_videos_playlist( playlist ) {
	
  if(playlist == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  // Seleciona a playlist
  document.getElementById("playlist").value = playlist;
  
  // Limpa as vídeos da última playlist selecionada
  limpar_lista_videos('playlist');
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";
  document.getElementById("msg_playlist").style.display = "none";

  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_videos_playlist/"+playlist , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado) {
	
	array_videos = resultado.split(";");
	
	for(var cont = 0; cont < array_videos.length; cont++) {	
	 
	if(array_videos[cont]) {
	
	dados_video = array_videos[cont].split("|");
	
	var path = dados_video[0];
	var video = dados_video[1];
	var width = dados_video[2];
	var height = dados_video[3];
	var bitrate = dados_video[4];
	var duracao = dados_video[5];
	var duracao_segundos = dados_video[6];
	var tipo = dados_video[6];
	var login = dados_video[9];
	
	document.getElementById("msg_playlist").style.display = "none";
  
  	var lista_videos = document.getElementById("lista-videos-playlist");
  
  	var total_videos = 0;
  
  	for (var i = 0; i < lista_videos.childNodes.length; i++) {
        if (lista_videos.childNodes[i].nodeName == "LI") {
          total_videos++;
        }
 	}
  
  	var novo_id = (total_videos+1);
  
  	var novo_video = document.createElement("li");
  
  	novo_video.setAttribute("id",novo_id);
	novo_video.setAttribute("class","drag");
	
	if(tipo == "video") {
	
	novo_video.innerHTML = "<input name='videos_adicionados[]' type='checkbox' value='"+path+"|"+video+"|"+width+"|"+height+"|"+bitrate+"|"+duracao+"|"+duracao_segundos+"|video' style='display:none' checked /><img src='/img/icones/img-icone-arquivo-video.png' border='0' align='absmiddle' />&nbsp;["+duracao+"] "+path.replace("/", " » ")+"&nbsp;("+width+"x"+height+" @ "+bitrate+"Kbps)<span style='float:right;'><a href='javascript:play_video(\""+login+"\",\""+path+"\");' title='Play "+video+"'><img src='/img/icones/img-icone-player.png' width='16' height='16' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_video(\""+novo_id+"\",\""+duracao_segundos+"\")'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove' title='Remover/Remove' border='0' align='absmiddle' /></a></span>";
	
	} else if(tipo == "comercial") {
	
	novo_video.innerHTML = "<input name='videos_adicionados[]' type='checkbox' value='"+path+"|"+video+"|"+width+"|"+height+"|"+bitrate+"|"+duracao+"|"+duracao_segundos+"|comercial' style='display:none' checked /><img src='/img/icones/img-icone-comercial.png' border='0' align='absmiddle' />&nbsp;["+duracao+"] "+path.replace("/", " » ")+"&nbsp;("+width+"x"+height+" @ "+bitrate+"Kbps)<span style='float:right;'><a href='javascript:play_video(\""+login+"\",\""+path+"\");' title='Play "+video+"'><img src='/img/icones/img-icone-player.png' width='16' height='16' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_video(\""+novo_id+"\",\""+duracao_segundos+"\")'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove' title='Remover/Remove' border='0' align='absmiddle' /></a></span>";
	
	} else if(tipo == "intercalado") {
	
	novo_video.innerHTML = "<input name='videos_adicionados[]' type='checkbox' value='"+path+"|"+video+"|"+width+"|"+height+"|"+bitrate+"|"+duracao+"|"+duracao_segundos+"|intercalado' style='display:none' checked /><img src='/img/icones/img-icone-intercalado.png' border='0' align='absmiddle' />&nbsp;["+duracao+"] "+path.replace("/", " » ")+"&nbsp;("+width+"x"+height+" @ "+bitrate+"Kbps)<span style='float:right;'><a href='javascript:play_video(\""+login+"\",\""+path+"\");' title='Play "+video+"'><img src='/img/icones/img-icone-player.png' width='16' height='16' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_video(\""+novo_id+"\",\""+duracao_segundos+"\")'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove' title='Remover/Remove' border='0' align='absmiddle' /></a></span>";
	
	} else {
	
	novo_video.innerHTML = "<input name='videos_adicionados[]' type='checkbox' value='"+path+"|"+video+"|"+width+"|"+height+"|"+bitrate+"|"+duracao+"|"+duracao_segundos+"|video' style='display:none' checked /><img src='/img/icones/img-icone-arquivo-video.png' border='0' align='absmiddle' />&nbsp;["+duracao+"] "+path.replace("/", " » ")+"&nbsp;("+width+"x"+height+" @ "+bitrate+"Kbps)<span style='float:right;'><a href='javascript:play_video(\""+login+"\",\""+path+"\");' title='Play "+video+"'><img src='/img/icones/img-icone-player.png' width='16' height='16' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_video(\""+novo_id+"\",\""+duracao_segundos+"\")'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove' title='Remover/Remove' border='0' align='absmiddle' /></a></span>";
	
	}  	
  
  	document.getElementById("lista-videos-playlist").appendChild(novo_video);
  
  	quantidade_videos_playlist();
  
  	tempo_execucao_playlist( duracao_segundos, "adicionar" );
  
  	setListeners();
	
	document.getElementById("log-sistema-fundo").style.display = "none";
    document.getElementById("log-sistema").style.display = "none";
	document.getElementById("msg_playlist_nova").style.display = "none";

	}
	
	}
	
	} else {
	
	document.getElementById("msg_playlist_nova").style.display = "block";
	document.getElementById("log-sistema-fundo").style.display = "none";
    document.getElementById("log-sistema").style.display = "none";
	
	}
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para adicionar uma video do FTP na playlist
function adicionar_video_playlist( login,path,video,width,height,bitrate,duracao,duracao_segundos ) {
	
  if(path == "" && video == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  var playlist = document.getElementById("playlist").value;
  
  document.getElementById("msg_playlist").style.display = "none";
  document.getElementById("msg_playlist_nova").style.display = "none";
  
  var lista_videos = document.getElementById("lista-videos-playlist");
  
  var total_videos = 0;
  
  for (var i = 0; i < lista_videos.childNodes.length; i++) {
        if (lista_videos.childNodes[i].nodeName == "LI") {
          total_videos++;
        }
  }
  
  var novo_id = (total_videos+1);
  
  var novo_video = document.createElement("li");
  
  novo_video.setAttribute("id",novo_id);
  novo_video.setAttribute("class","drag");
  
  novo_video.innerHTML = "<input name='videos_adicionados[]' type='checkbox' value='"+path+"|"+video+"|"+width+"|"+height+"|"+bitrate+"|"+duracao+"|"+duracao_segundos+"|video' style='display:none' checked /><img src='/img/icones/img-icone-arquivo-video.png' border='0' align='absmiddle' />&nbsp;["+duracao+"] "+path.replace("/", " » ")+"&nbsp;("+width+"x"+height+" @ "+bitrate+"Kbps)<span style='float:right;'><a href='javascript:play_video(\""+login+"\",\""+path+"\");' title='Play "+video+"'><img src='/img/icones/img-icone-player.png' width='16' height='16' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_video(\""+novo_id+"\",\""+duracao_segundos+"\")'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove' title='Remover/Remove' border='0' align='absmiddle' /></a></span>";
  
  document.getElementById("lista-videos-playlist").appendChild(novo_video);
  
  quantidade_videos_playlist();
  
  tempo_execucao_playlist( duracao_segundos, "adicionar" );
  
  setListeners();
  
  }  
  
}

// Função para adicionar todas as videos do FTP na playlist
function adicionar_tudo() {
	
  var playlist = document.getElementById("playlist").value;
  
  document.getElementById("msg_playlist_nova").style.display = "none";

  var lista_videos_pasta = document.forms["gerenciador"].elements["videos_pasta"];
  
  for (var i = 0; i < lista_videos_pasta.length; i++) {
  
  var path = lista_videos_pasta[i].value;
 
  
  var login = lista_videos_pasta[i].getAttribute('login');
  var video = lista_videos_pasta[i].getAttribute('video');
  var width = lista_videos_pasta[i].getAttribute('wth');
  var height = lista_videos_pasta[i].getAttribute('hht');
  var bitrate = lista_videos_pasta[i].getAttribute('bitrate');
  var duracao = lista_videos_pasta[i].getAttribute('duracao');
  var duracao_segundos = lista_videos_pasta[i].getAttribute('duracao_segundos');
  
  adicionar_video_playlist( login,path,video,width,height,bitrate,duracao,duracao_segundos );
  
  }
  
}

// Função para remover uma vídeo de uma playlist
function remover_video( id, duracao ) {
  
  // Remove a vídeo da lista
  document.getElementById("lista-videos-playlist").removeChild(document.getElementById(id));
  
  // Atualiza a quantidade de videos da playlist
  quantidade_videos_playlist();
  

  // Remove o tempo da video
  tempo_execucao_playlist( duracao, "remover" );

}

// Função para remover uma playlist
function remover_playlist( playlist ) {
	
  if(playlist == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  if(window.confirm("Português: Deseja remover a playlist e todas as suas vídeos?\n\nEnglish: Want to remove the playlist and all your songs?\n\nEspañol: ¿Quieres eliminar la lista de reproducción y todas sus canciones?")) {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_playlist/"+playlist , true);
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

// Função para limpar a lista de vídeos
function limpar_lista_videos( local ) {

  if(local == "ftp") {
  
  document.getElementById("lista-videos-pasta").innerHTML = "";
  document.getElementById("msg_pasta").style.display = "block";
  
  } else {
  
  document.getElementById("lista-videos-playlist").innerHTML = "";
  document.getElementById("msg_playlist").style.display = "block";
    
  quantidade_videos_playlist();
  
  document.getElementById("tempo").value = 0;
	
  document.getElementById("tempo_playlist").innerHTML = "00:00:00";
  
  }
  
}

// Função para contar a quantidade de vídeos na playlist
function quantidade_videos_playlist() {

  var lista_videos = document.getElementById("lista-videos-playlist");
  
  var total_videos = 0;

  for (var i = 0; i < lista_videos.childNodes.length; i++) {
        if (lista_videos.childNodes[i].nodeName == "LI") {
          total_videos++;
        }
  }
  
  document.getElementById("quantidade_videos_playlist").innerHTML = total_videos;
  
  if(total_videos > 1000) {
  document.getElementById("quadro_quantidade_videos_playlist").style.borderColor = "#FFCC00";
  } else {
  document.getElementById("quadro_quantidade_videos_playlist").style.borderColor = "#CCCCCC";
  }

}

// Função para calcular o tempo de execução da playlist
function tempo_execucao_playlist( duracao, operacao ) {
	
	var tempo_atual = document.getElementById("tempo").value;
	
	if(operacao == "adicionar") {
	var novo_tempo = Number(tempo_atual)+Number(duracao);
	} else {
	var novo_tempo = Number(tempo_atual)-Number(duracao);
	}
	
	document.getElementById("tempo").value = novo_tempo;
	
	document.getElementById("tempo_playlist").innerHTML = s2time(novo_tempo);

}

// Função para iniciar transmissão de uma playlist pelo gerenciador de playlists
function iniciar_playlist( playlist ) {
	
  if(playlist == "") {
  alert("Oops! Ocorreu um erro ao processar sua requisição!\n\nContate o suporte para maiores detalhes\n\nErro: Dados faltando.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
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

// Função para remover a configuração de Comerciais
function remover_comerciais_playlist( playlist ) {
	
  if(playlist == "") {
  alert("Oops! Ocorreu um erro ao processar sua requisição!\n\nContate o suporte para maiores detalhes\n\nErro: Dados faltando.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_comerciais_playlist/"+playlist , true);
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

// Função para misturar as videos da playlist
function shuffle(items)
{
    var cached = items.slice(0), temp, i = cached.length, rand;
    while(--i)
    {
        rand = Math.floor(i * Math.random());
        temp = cached[rand];
        cached[rand] = cached[i];
        cached[i] = temp;
    }
    return cached;
}
function misturar_videos( local ) {

var list = document.getElementById(local);

var nodes = list.children, i = 0;
    nodes = Array.prototype.slice.call(nodes);
    nodes = shuffle(nodes);
    while(i < nodes.length)
    {
        list.appendChild(nodes[i]);
        ++i;
    }
	
}

// Função para salvar a playlist
function salvar_playlist() {
  
  var playlist = document.getElementById("playlist").value;
  
  if(playlist == "") {  
  alert("Ooops!\n\nVocê não selecionou uma playlist.\nYou did not select a playlist.\nNo ha seleccionado una lista de reproducción.");  
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";
  
  document.gerenciador.submit();
  }

}

// Função para duplicar(copiar) uma playlist
function duplicar_playlist( playlist ) {
  
  var playlist_nova = prompt("Nome/Name/Nombre:\n\n(Não use caracteres especiais e acentos)\n(Do not use special characters and accents)\n(No utilice caracteres especiales y acentos)");
	
  if(playlist_nova != "" && playlist_nova != null) {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";	
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/duplicar_playlist/"+playlist+"/"+playlist_nova+"" , true);
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

// Função Drag&Drop para organizar as vídeos da playlist
var zxcMseX, zxcMseY;

function zxcMove(event, zxcobj){
    var tgt;
    if (!event) var event = window.event;
    if (event.target) tgt = event.target;
    else if (event.srcElement) tgt = event.srcElement;
    if (tgt.nodeType == 3) tgt = tgt.parentNode;
    if (tgt.tagName != 'A' && tgt.tagName != 'IMG')
    {
        var zxcels = zxcobj.parentNode.getElementsByTagName(zxcobj.tagName);

        zxcobj.ary = [];    
        for (var zxc0 = 0; zxc0 < zxcels.length; zxc0++)
        {
            zxcobj.ary.push(zxcels[zxc0]);
        }
    
        zxcMseDown(event, zxcobj);
    }
}

function zxcMseDown(event, obj)
{
    document.onmousemove = function(event)
    {
        zxcDrag(event);
    }
    document.onmouseup = function(event)
    {
        zxcMseUp(event);
    }
    document.onselectstart = function(event)
    {
        window.event.returnValue = false;
    }
    
    zxcObj = obj;
    zxcObj.style.zIndex = 1;
    
    zxcMse(event);
    zxcDragY = zxcMseY;
}

function zxcMseUp(event)
{
    zxcObj.style.zIndex = 0;
    
    document.onmousemove = null;
    document.onselectstart = null;
    
    zxcDragX = -1;
    zxcDragY = -1;
    
    zxcRePos();
}

function zxcDrag(event)
{
    zxcMse(event);
    zxcObj.style.top = ((zxcMseY - zxcDragY)) + 'px';
}

function zxcMse(event)
{
    if (!event)
        var event = window.event;

    if (document.all)
    {
        zxcMseX = event.clientX+zxcDocS()[0];
        zxcMseY = event.clientY+zxcDocS()[1];
    }
    else
    {
        zxcMseX = event.pageX;
        zxcMseY = event.pageY;
    }
}

function zxcDocS()
{
    var zxcsx, zxcsy;
    
    if (!document.body.scrollTop)
    {
        zxcsx = document.documentElement.scrollLeft;
        zxcsy = document.documentElement.scrollTop;
    }
    else
    {
        zxcsx = document.body.scrollLeft;
        zxcsy = document.body.scrollTop;
    }
    
    return [zxcsx,zxcsy];
}

function zxcRePos()
{
    if (zxcObj.parentNode)
    {
        var zxcpar = zxcObj.parentNode;
        var zxccloneary = [];
    
        for (var zxc0 = 0; zxc0 < zxcObj.ary.length; zxc0++)
        {
            zxccloneary.push([zxcObj.ary[zxc0].cloneNode(true), zxcObj.ary[zxc0].offsetTop]);
        }

        for (var zxc1 = 0; zxc1 < zxcObj.ary.length; zxc1++)
        {
            zxcpar.removeChild(zxcObj.ary[zxc1]);
        }
    
        zxccloneary = zxccloneary.sort(zxcSortPos);
    
        for (var zxc2 = 0; zxc2 < zxccloneary.length; zxc2++)
        {
            zxcpar.appendChild(zxccloneary[zxc2][0]);
            zxccloneary[zxc2][0].style.top = '0px';
        }
    
        setListeners();
    }
}

function zxcSortPos(zxca, zxcb)
{
    var zxcA = zxca[1];
    var zxcB = zxcb[1];
    
    if (zxcA < zxcB)
    {
        return -1;
    }
    
    if (zxcA > zxcB)
    {
        return 1;
    }
    
    return 0;
}

function setListeners()
{
    var item = document.getElementsByClassName("drag");
    
    for (var i = 0; i < item.length; i++)
    {
        if (item[i].addEventListener)
        {
            item[i].addEventListener ("mousedown", function (e) { zxcMove(e, this); }, false);
        }
        else if (item[i].attachEvent)
        {
            item[i].attachEvent ("onmousedown", function (e) { zxcMove(e, this); });
        }
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