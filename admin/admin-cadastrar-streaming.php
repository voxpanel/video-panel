<?php
require_once("inc/protecao-admin.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM video.configuracoes"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-menu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
</head>

<body>
<div id="topo">
<div id="topo-conteudo" style="background:url(/admin/img/logo-advance-host.gif) no-repeat left;"></div>
</div>
<div id="menu">
<div id="menu-links">
  	<ul>
      <li style="width:150px">&nbsp;</li>
  		<li><a href="/admin/admin-streamings" class="texto_menu">Streamings</a></li>
  		<li><em></em><a href="/admin/admin-revendas" class="texto_menu">Revendas</a></li>
        <li><em></em><a href="/admin/admin-servidores" class="texto_menu">Servidores</a></li>
        <li><em></em><a href="/admin/admin-dicas" class="texto_menu">Dicas</a></li>
        <li><em></em><a href="/admin/admin-avisos" class="texto_menu">Avisos</a></li>
        <li><em></em><a href="/admin/admin-tutoriais" class="texto_menu">Tutoriais</a></li>
        <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configurações</a></li>
        <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
  	</ul>
</div>
</div>
<div id="conteudo">
  <form method="post" action="/admin/admin-cadastra-streaming" style="padding:0px; margin:0px">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="120"  height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Plano</td>
        <td width="380" align="left">
        <select name="plano" class="input" id="plano" style="width:255px;" onchange="configuracao_plano(this.value,'streaming');">
        <option value="" selected="selected" style="font-size:13px; font-weight:bold; background-color:#CCCCCC;">Selecione um plano padrão</option>
        <option value="50|48|10000">Streaming Econômico</option>
        <option value="500|128|50000">Streaming 01</option>
        <option value="1000|128|50000">Streaming 02</option>
        <option value="1500|128|50000">Streaming 03</option>
        <option value="99999|128|80000">Streaming Ilimitado</option>
        </select></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Servidor</td>
        <td align="left">
        <select name="servidor" class="input" id="servidor" style="width:255px;">
<?php
$query_servidor = mysql_query("SELECT * FROM video.servidores ORDER by codigo ASC");
while ($dados_servidor = mysql_fetch_array($query_servidor)) {

$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM video.streamings where codigo_servidor = '".$dados_servidor["codigo"]."'"));

if($dados_config["codigo_servidor_atual"] == $dados_servidor["codigo"]) {
echo '<option value="'.$dados_servidor["codigo"].'" selected="selected">'.$dados_servidor["nome"].' - '.$dados_servidor["ip"].' ('.$total_streamings.')</option>';
} else {
echo '<option value="'.$dados_servidor["codigo"].'">'.$dados_servidor["nome"].' - '.$dados_servidor["ip"].' ('.$total_streamings.')</option>';
}

}
?>
        </select>        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Login</td>
        <td align="left">
          <input name="login" type="text" class="input" id="login" style="width:250px;" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Espectadores</td>
        <td align="left"><input name="espectadores" type="number" class="input" id="espectadores" style="width:250px;" value="0" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"> Bitrate</td>
        <td align="left"><input name="bitrate" type="number" class="input" id="bitrate" style="width:250px;" value="0" /> </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Espaço FTP</td>
        <td align="left" class="texto_padrao_pequeno"><input name="espaco" type="number" class="input" id="espaco" style="width:250px;" value="0" />
        <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('O valor deve ser em megabytes ex.: 1GB = 1000');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Senha</td>
        <td align="left"><input name="senha" type="text" class="input" id="senha" style="width:250px; vertical-align:middle" value="" />
          <img src="/admin/img/icones/img-icone-senha-24x24.png" alt="Gerar Senha" width="16" height="16" align="absmiddle" onclick="gerar_senha('senha');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Identifica&ccedil;&atilde;o</td>
        <td align="left"><input name="identificacao" type="text" class="input" id="identificacao" style="width:250px;" />
          &nbsp;</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">E-mail</td>
        <td align="left"><input name="email" type="text" class="input" id="email" style="width:250px;" />
          &nbsp;</td>
      </tr>
      <tr>
        <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Aplicação</td>
        <td align="left" class="texto_padrao_destaque">
        <select name="aplicacao" id="aplicacao" style="width:255px;">
		  <option value="tvstation">Tv Station (live & ondemand)</option>
          <option value="live" selected="selected">Live</option>
          <option value="vod">OnDemand</option>
          <option value="ipcamera">IP Camera</option>
        </select>
        </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Idioma</td>
        <td align="left">
        <select name="idioma_painel" id="idioma_painel" style="width:255px;">
          <option value="pt-br" selected="selected">Português(Brasil)</option>
          <option value="es">Español</option>
          <option value="en-us">English(USA)</option>
        </select></td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="Cadastrar" />
          <input type="button" class="botao" value="Cancelar" onclick="window.location = '/admin/admin-streamings';" />        </td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
