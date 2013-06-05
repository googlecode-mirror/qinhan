#!/bin/sh
source /home/httpd/ppysq.pt.pps.tv/crontab/function.sh

callurl "http://ppysq.pt.pps.tv/web_crontab.php?act=count_act_user_num"
callurl "http://ppysq.pt.pps.tv/web_crontab.php?act=push_ping_actions"

for (( i=0; i<=0; i=i+1 )); do
    callurl "http://ppysq.pt.pps.tv/index.php?act=web_crontab&act_method=action_insert&robot=$i"
done

for (( i=0; i<=0; i=i+1 )); do
    callurl "http://ppysq.pt.pps.tv/index.php?act=web_crontab&act_method=action_update&robot=$i"
done

for (( i=1; i<=10; i=i+1 )); do
    callurl "http://ppysq.pt.pps.tv/index.php?act=web_crontab&act_method=receive_action&page=$i"
done

callurl "http://ppysq.pt.pps.tv/index.php?act=web_crontab&act_method=deal_queue_clear_favorite"