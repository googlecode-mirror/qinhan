#!/bin/sh
source /home/httpd/ppysq.pt.pps.tv/crontab/function.sh

callurl "http://ppysq.pt.pps.tv/web_crontab.php?act=count_act_user_num_1day"
callurl "http://ppysq.pt.pps.tv/web_crontab.php?act=table_space_monitor"  


callurl "http://ppysq.pt.pps.tv/project.php?act=send_num&host=pt.pps.tv&v1=ppysq.pt.pps.tv(��Ŀ�����)&to_v1=��Ŀ����&to_v2=ppysq.pt.pps.tv(��Ŀ�����)"
