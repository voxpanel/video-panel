var _$_c1e7=["","Error!\x0A\x0APortugu\xEAs: Dados faltando, tente novamente ou contate o suporte.\x0A\x0AEnglish: Missing data try again or contact support.\x0A\x0AEspa\xF1ol: Los datos que faltaban int\xE9ntelo de nuevo o contacte con Atenci\xF3n.","innerHTML","lista-pastas","getElementById","status_lista_pastas","<img src='http://","/img/ajax-loader.gif' />","display","style","block","GET","/funcoes-ajax/carregar_pastas/","open","onreadystatechange","readyState","responseText",";","split","length","|","li","createElement","<img src='/img/icones/img-icone-pasta.png' align='absmiddle' />&nbsp;<a href='javascript:carregar_videos_pasta(\"","\",\"","\");'>","&nbsp;(",")</a><a href='javascript:remover_pasta(\"","\")' style='float:right;'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove' title='Remover/Remove' border='0' align='absmiddle' /></a><a href='javascript:renomear_pasta(\"","\")' style='float:right;padding-right:5px;'><img src='/img/icones/img-icone-renomear.png' alt='Renomear/Rename' title='Renomear/Rename' border='0' align='absmiddle' /></a>","appendChild","none","log-sistema-fundo","log-sistema","status_lista_playlists","Nenhuma pasta encontrada.","send","lista-videos-pasta","log-sistema-conteudo","msg_pasta","/funcoes-ajax/carregar_videos_pasta/","/","test","<img src='/img/icones/img-icone-bloqueado.png' width='16' height='16' border='0' align='absmiddle' />&nbsp;[","]&nbsp;<span title='Cont\xE9m acentos/Special Chars' style='color:#FF0000;'>","</span>&nbsp;(","x"," @ ","Kbps)<span style='float:right;'><a href='javascript:renomear_video_ftp(\"","\");' title='Renomear/Rename ","'><img src='/img/icones/img-icone-renomear.png' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_video_ftp(\"","\")'><img src='/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove ","' title='Remover/Remove ","' border='0' align='absmiddle' /></a></span>","backgroundColor","#FFBFBF","cursor","pointer","click","Arquivo inv\xE1lido com caracteres especiais.\x0A\x0AInvalid file with special chars.\x0A\x0ANombre de archivo no v\xE1lido con caracteres especiales.","addEventListener","]&nbsp;"," @ <span title='Bitrate acima do plano/Bitrate great than package' style='color:#FF0000;'>","Kbps</span>)<span style='float:right;'><a href='javascript:remover_video_ftp(\"","Bitrate maior que o do plano.\x0A\x0ABitrate greater than the package.\x0A\x0ABitrate m\xE1s alta que el l\xEDmite del plan.","<img src='/img/icones/img-icone-arquivo-video.png' border='0' align='absmiddle' />&nbsp;[","Kbps)<span style='float:right;'><a href='javascript:play_video(\"","\");' title='Play ","'><img src='/img/icones/img-icone-player.png' width='16' height='16' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:renomear_video_ftp(\"","Portugu\xEAs: Informe o um nome para a pasta:\x0A(N\xE3o use caracteres especiais e acentos)\x0A\x0AEnglish: Type the name to the directory:\x0A\x0AEspa\xF1ol: Introduzca el nombre para la carpeta:","/funcoes-ajax/criar_pasta/","Portugu\xEAs: Informe o novo nome:\x0A(N\xE3o use caracteres especiais e acentos)\x0A\x0AEnglish: Type the new name:\x0A\x0AEspa\xF1ol: Introduzca el nuevo nombre:","/funcoes-ajax/renomear_pasta/","Portugu\xEAs: Deseja realmente remover esta pasta e todos os seus v\xEDdeos?\x0A\x0AEnglish: Do you really want to remove this folder and all your videos?\x0A\x0AEspa\xF1ol: \xBFDe verdad quiere eliminar esta carpeta y todos sus videos?","confirm","/funcoes-ajax/remover_pasta/","Portugu\xEAs: Informe o novo nome para o v\xEDdeo:\x0A(N\xE3o use caracteres especiais e acentos)\x0A\x0AEnglish: Type the new file name:\x0A\x0AEspa\xF1ol: Introduzca el nuevo nombre para el video:","replace","/funcoes-ajax/renomear_video_ftp/","Deseja realmente remover esta m\xFAsica?","/funcoes-ajax/remover_video_ftp/","estatistica_uso_plano_ftp","/img/spinner.gif' />","/funcoes-ajax/estatistica_uso_plano/","/funcoes-ajax/play_video/","href","Microsoft.XMLHTTP","Msxml2.XMLHTTP","Esse browser n\xE3o tem recursos para uso do Ajax"];function carregar_pastas(_0xEFF2){if(_0xEFF2== _$_c1e7[0]){alert(_$_c1e7[1])}else {document[_$_c1e7[4]](_$_c1e7[3])[_$_c1e7[2]]= _$_c1e7[0];document[_$_c1e7[4]](_$_c1e7[5])[_$_c1e7[2]]= _$_c1e7[6]+ get_host()+ _$_c1e7[7];document[_$_c1e7[4]](_$_c1e7[5])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];var _0xEFC3= new Ajax();_0xEFC3[_$_c1e7[13]](_$_c1e7[11],_$_c1e7[12]+ _0xEFF2,true);_0xEFC3[_$_c1e7[14]]= function(){if(_0xEFC3[_$_c1e7[15]]== 4){resultado= _0xEFC3[_$_c1e7[16]];if(resultado){array_pastas= resultado[_$_c1e7[18]](_$_c1e7[17]);for(var _0xF021=0;_0xF021< array_pastas[_$_c1e7[19]];_0xF021++){if(array_pastas[_0xF021]){dados_pasta= array_pastas[_0xF021][_$_c1e7[18]](_$_c1e7[20]);var _0xF050=document[_$_c1e7[22]](_$_c1e7[21]);_0xF050[_$_c1e7[2]]= _$_c1e7[23]+ _0xEFF2+ _$_c1e7[24]+ dados_pasta[0]+ _$_c1e7[25]+ dados_pasta[0]+ _$_c1e7[26]+ dados_pasta[1]+ _$_c1e7[27]+ _0xEFF2+ _$_c1e7[24]+ dados_pasta[0]+ _$_c1e7[28]+ _0xEFF2+ _$_c1e7[24]+ dados_pasta[0]+ _$_c1e7[29];document[_$_c1e7[4]](_$_c1e7[3])[_$_c1e7[30]](_0xF050);document[_$_c1e7[4]](_$_c1e7[5])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[31];document[_$_c1e7[4]](_$_c1e7[32])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[31];document[_$_c1e7[4]](_$_c1e7[33])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[31]}}}else {document[_$_c1e7[4]](_$_c1e7[34])[_$_c1e7[2]]= _$_c1e7[35]}}};_0xEFC3[_$_c1e7[36]](null);delete _0xEFC3}}function carregar_videos_pasta(_0xEFF2,_0xF07F){if(_0xEFF2== _$_c1e7[0]|| _0xF07F== _$_c1e7[0]){alert(_$_c1e7[1])}else {document[_$_c1e7[4]](_$_c1e7[37])[_$_c1e7[2]]= _$_c1e7[0];document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= _$_c1e7[6]+ get_host()+ _$_c1e7[7];document[_$_c1e7[4]](_$_c1e7[32])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];document[_$_c1e7[4]](_$_c1e7[33])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];document[_$_c1e7[4]](_$_c1e7[39])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[31];var _0xEFC3= new Ajax();_0xEFC3[_$_c1e7[13]](_$_c1e7[11],_$_c1e7[40]+ _0xEFF2+ _$_c1e7[41]+ _0xF07F,true);_0xEFC3[_$_c1e7[14]]= function(){if(_0xEFC3[_$_c1e7[15]]== 4){resultado= _0xEFC3[_$_c1e7[16]];if(resultado){array_videos= resultado[_$_c1e7[18]](_$_c1e7[17]);for(var _0xF021=0;_0xF021< array_videos[_$_c1e7[19]];_0xF021++){if(array_videos[_0xF021]){dados_video= array_videos[_0xF021][_$_c1e7[18]](_$_c1e7[20]);var _0xF1C8=dados_video[0];var _0xF1F7=add3Dots(dados_video[1],45);var _0xF226=dados_video[2];var _0xF16A=dados_video[3];var _0xF0AE=dados_video[4];var _0xF10C=dados_video[5];var _0xF13B=dados_video[6];var _0xF0DD=dados_video[8];var _0xF199=document[_$_c1e7[22]](_$_c1e7[21]);if(/[^a-z0-9_\-\. ]/gi[_$_c1e7[42]](dados_video[1])){_0xF199[_$_c1e7[2]]= _$_c1e7[43]+ _0xF10C+ _$_c1e7[44]+ _0xF1F7+ _$_c1e7[45]+ _0xF226+ _$_c1e7[46]+ _0xF16A+ _$_c1e7[47]+ _0xF0AE+ _$_c1e7[48]+ _0xEFF2+ _$_c1e7[24]+ _0xF1C8+ _$_c1e7[49]+ _0xF1F7+ _$_c1e7[50]+ _0xEFF2+ _$_c1e7[24]+ _0xF1C8+ _$_c1e7[51]+ _0xF1F7+ _$_c1e7[52]+ _0xF1F7+ _$_c1e7[53];_0xF199[_$_c1e7[9]][_$_c1e7[54]]= _$_c1e7[55];_0xF199[_$_c1e7[9]][_$_c1e7[56]]= _$_c1e7[57];_0xF199[_$_c1e7[60]](_$_c1e7[58],function(){alert(_$_c1e7[59])})}else {if(Number(_0xF0AE)> Number(_0xF0DD)){_0xF199[_$_c1e7[2]]= _$_c1e7[43]+ _0xF10C+ _$_c1e7[61]+ _0xF1F7+ _$_c1e7[26]+ _0xF226+ _$_c1e7[46]+ _0xF16A+ _$_c1e7[62]+ _0xF0AE+ _$_c1e7[63]+ _0xEFF2+ _$_c1e7[24]+ _0xF1C8+ _$_c1e7[51]+ _0xF1F7+ _$_c1e7[52]+ _0xF1F7+ _$_c1e7[53];_0xF199[_$_c1e7[9]][_$_c1e7[54]]= _$_c1e7[55];_0xF199[_$_c1e7[9]][_$_c1e7[56]]= _$_c1e7[57];_0xF199[_$_c1e7[60]](_$_c1e7[58],function(){alert(_$_c1e7[64])})}else {_0xF199[_$_c1e7[2]]= _$_c1e7[65]+ _0xF10C+ _$_c1e7[61]+ _0xF1F7+ _$_c1e7[26]+ _0xF226+ _$_c1e7[46]+ _0xF16A+ _$_c1e7[47]+ _0xF0AE+ _$_c1e7[66]+ _0xEFF2+ _$_c1e7[24]+ _0xF1C8+ _$_c1e7[67]+ _0xF1F7+ _$_c1e7[68]+ _0xEFF2+ _$_c1e7[24]+ _0xF1C8+ _$_c1e7[49]+ _0xF1F7+ _$_c1e7[50]+ _0xEFF2+ _$_c1e7[24]+ _0xF1C8+ _$_c1e7[51]+ _0xF1F7+ _$_c1e7[52]+ _0xF1F7+ _$_c1e7[53]}};document[_$_c1e7[4]](_$_c1e7[37])[_$_c1e7[30]](_0xF199)}}};document[_$_c1e7[4]](_$_c1e7[32])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[31];document[_$_c1e7[4]](_$_c1e7[33])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[31]}};_0xEFC3[_$_c1e7[36]](null);delete _0xEFC3}}function criar_pasta(_0xEFF2){var _0xF07F=prompt(_$_c1e7[69]);if(_0xF07F!= _$_c1e7[0]&& _0xF07F!= null){document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= _$_c1e7[6]+ get_host()+ _$_c1e7[7];document[_$_c1e7[4]](_$_c1e7[32])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];document[_$_c1e7[4]](_$_c1e7[33])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];var _0xEFC3= new Ajax();_0xEFC3[_$_c1e7[13]](_$_c1e7[11],_$_c1e7[70]+ _0xEFF2+ _$_c1e7[41]+ _0xF07F,true);_0xEFC3[_$_c1e7[14]]= function(){if(_0xEFC3[_$_c1e7[15]]== 4){resultado= _0xEFC3[_$_c1e7[16]];document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= resultado}};_0xEFC3[_$_c1e7[36]](null);delete _0xEFC3}}function renomear_pasta(_0xEFF2,_0xF07F){if(_0xF07F== _$_c1e7[0]){alert(_$_c1e7[1])}else {novo= prompt(_$_c1e7[71]);if(novo!= _$_c1e7[0]&& novo!= null){document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= _$_c1e7[6]+ get_host()+ _$_c1e7[7];document[_$_c1e7[4]](_$_c1e7[32])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];document[_$_c1e7[4]](_$_c1e7[33])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];var _0xEFC3= new Ajax();_0xEFC3[_$_c1e7[13]](_$_c1e7[11],_$_c1e7[72]+ _0xEFF2+ _$_c1e7[41]+ _0xF07F+ _$_c1e7[41]+ novo,true);_0xEFC3[_$_c1e7[14]]= function(){if(_0xEFC3[_$_c1e7[15]]== 4){resultado= _0xEFC3[_$_c1e7[16]];document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= resultado}};_0xEFC3[_$_c1e7[36]](null);delete _0xEFC3}}}function remover_pasta(_0xEFF2,_0xF07F){if(window[_$_c1e7[74]](_$_c1e7[73])){document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= _$_c1e7[6]+ get_host()+ _$_c1e7[7];document[_$_c1e7[4]](_$_c1e7[32])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];document[_$_c1e7[4]](_$_c1e7[33])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];var _0xEFC3= new Ajax();_0xEFC3[_$_c1e7[13]](_$_c1e7[11],_$_c1e7[75]+ _0xEFF2+ _$_c1e7[41]+ _0xF07F,true);_0xEFC3[_$_c1e7[14]]= function(){if(_0xEFC3[_$_c1e7[15]]== 4){resultado= _0xEFC3[_$_c1e7[16]];document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= resultado}};_0xEFC3[_$_c1e7[36]](null);delete _0xEFC3}}function renomear_video_ftp(_0xEFF2,_0xF1F7,_0xF2E2){if(_0xF1F7== _$_c1e7[0]){alert(_$_c1e7[1])}else {_0xF2E2= prompt(_$_c1e7[76]);if(_0xF2E2!= _$_c1e7[0]&& _0xF2E2!= null){document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= _$_c1e7[6]+ get_host()+ _$_c1e7[7];document[_$_c1e7[4]](_$_c1e7[32])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];document[_$_c1e7[4]](_$_c1e7[33])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];var _0xF1F7=_0xF1F7[_$_c1e7[77]](_$_c1e7[41],_$_c1e7[20]);var _0xEFC3= new Ajax();_0xEFC3[_$_c1e7[13]](_$_c1e7[11],_$_c1e7[78]+ _0xEFF2+ _$_c1e7[41]+ _0xF1F7+ _$_c1e7[41]+ _0xF2E2,true);_0xEFC3[_$_c1e7[14]]= function(){if(_0xEFC3[_$_c1e7[15]]== 4){resultado= _0xEFC3[_$_c1e7[16]];document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= resultado}};_0xEFC3[_$_c1e7[36]](null);delete _0xEFC3}}}function remover_video_ftp(_0xEFF2,_0xF1F7){if(_0xF1F7== _$_c1e7[0]){alert(_$_c1e7[1])}else {if(window[_$_c1e7[74]](_$_c1e7[79])){document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= _$_c1e7[6]+ get_host()+ _$_c1e7[7];document[_$_c1e7[4]](_$_c1e7[32])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];document[_$_c1e7[4]](_$_c1e7[33])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];var _0xF1F7=_0xF1F7[_$_c1e7[77]](_$_c1e7[41],_$_c1e7[20]);var _0xEFC3= new Ajax();_0xEFC3[_$_c1e7[13]](_$_c1e7[11],_$_c1e7[80]+ _0xEFF2+ _$_c1e7[41]+ _0xF1F7,true);_0xEFC3[_$_c1e7[14]]= function(){if(_0xEFC3[_$_c1e7[15]]== 4){resultado= _0xEFC3[_$_c1e7[16]];document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= resultado}};_0xEFC3[_$_c1e7[36]](null);delete _0xEFC3}}}function estatistica_uso_plano(_0xEFF2,_0xF255,_0xF284){document[_$_c1e7[4]](_$_c1e7[81])[_$_c1e7[2]]= _$_c1e7[6]+ get_host()+ _$_c1e7[82];var _0xEFC3= new Ajax();_0xEFC3[_$_c1e7[13]](_$_c1e7[11],_$_c1e7[83]+ _0xEFF2+ _$_c1e7[41]+ _0xF255+ _$_c1e7[41]+ _0xF284,true);_0xEFC3[_$_c1e7[14]]= function(){if(_0xEFC3[_$_c1e7[15]]== 4){resultado= _0xEFC3[_$_c1e7[16]];document[_$_c1e7[4]](_$_c1e7[81])[_$_c1e7[2]]= resultado}};_0xEFC3[_$_c1e7[36]](null);delete _0xEFC3}function play_video(_0xEFF2,_0xF1F7){if(_0xEFF2!= _$_c1e7[0]&& _0xF1F7!= null){document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= _$_c1e7[6]+ get_host()+ _$_c1e7[7];document[_$_c1e7[4]](_$_c1e7[32])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];document[_$_c1e7[4]](_$_c1e7[33])[_$_c1e7[9]][_$_c1e7[8]]= _$_c1e7[10];var _0xEFC3= new Ajax();_0xEFC3[_$_c1e7[13]](_$_c1e7[11],_$_c1e7[84]+ _0xEFF2+ _$_c1e7[41]+ _0xF1F7,true);_0xEFC3[_$_c1e7[14]]= function(){if(_0xEFC3[_$_c1e7[15]]== 4){resultado= _0xEFC3[_$_c1e7[16]];document[_$_c1e7[4]](_$_c1e7[38])[_$_c1e7[2]]= resultado}};_0xEFC3[_$_c1e7[36]](null);delete _0xEFC3}}function get_host(){var _0xF2B3=location[_$_c1e7[85]];_0xF2B3= _0xF2B3[_$_c1e7[18]](_$_c1e7[41]);return _0xF2B3[2]}function Ajax(){var _0xEF94;try{_0xEF94=  new ActiveXObject(_$_c1e7[86])}catch(e){try{_0xEF94=  new ActiveXObject(_$_c1e7[87])}catch(ex){try{_0xEF94=  new XMLHttpRequest()}catch(exc){alert(_$_c1e7[88]);_0xEF94= null}}};return _0xEF94}