// Funções Gerais
function get_host() {

var url = location.href;
url = url.split("/");

return url[2];

}

function executar_acao_streaming_movel( codigo,acao ) {

	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "informacoes") {
		window.location = "/movel/streaming";
	} else if(acao == "dados-conexao") {
		dados_conexao();
	} else if(acao == "ligar") {
		ligar_streaming( codigo );
	} else if(acao == "desligar") {
		desligar_streaming( codigo );
	} else if(acao == "reiniciar") {
		reiniciar_streaming( codigo );
	} else if(acao == "iniciar-playlist") {
		menu_iniciar_playlist();
	} else if(acao == "espectadores-conectados") {
		window.location = "/movel/espectadores-conectados";
	} else if(acao == "gravador") {
		window.location = "/movel/gravador";
	}

	document.getElementsByName("menu_executar_acao")[0].selectedIndex = 0;

}