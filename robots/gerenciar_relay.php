#!/bin/bash
# /home/streaming/gerenciar_relay - Script para iniciar/parar um agendamento de relay

# Muda prioridade de execussao
renice -20 $$

acao=$1
login=$2
senha=$3
url_relay=$4
pid_file=/home/streaming/relay_$login.pid

# Inicia o relay
if [ "$acao" == "iniciar" ]; then

nohup /usr/local/bin/ffmpeg -re -i $url_relay -c:v libx264 -profile:v baseline -level 3.0 -r 24 -g 48 -keyint_min 48 -sc_threshold 0 -vb 256k -c:a aac -ab 48k -ar 44100 -ac 2 -f flv rtmp://$login:$senha@localhost:1935/$login/live > /dev/null 2>&1 & echo $! > $pid_file

pid_result=`/bin/cat $pid_file`

if [[ $pid_result == ?(-)+([0-9]) ]]; then

echo OK

else

echo ERRO

fi

fi

# Finaliza o relay
if [ "$acao" == "parar" ]; then

if [ -f "$pid_file" ]; then

/bin/kill -9 `/bin/cat $pid_file`

/bin/rm -f $pid_file

echo OK

else

/bin/rm -f $pid_file

echo ERRO_PID

fi

fi

# Finaliza o relay forçadamente
if [ "$acao" == "parar-forcado" ]; then

pid_relay=`/bin/ps aux | /bin/grep ffmpeg | /bin/grep localhost | /bin/grep $login | /bin/awk {'print $2'}`

/bin/kill -9 $pid_relay

/bin/rm -f $pid_file

echo OK

fi