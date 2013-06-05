#!/bin/sh
source /home/httpd/ppysq.pt.pps.tv/crontab/function.sh

callurl "http://ppysq.pt.pps.tv/web_crontab.php?act=count_act_user_num_1day"
callurl "http://ppysq.pt.pps.tv/web_crontab.php?act=table_space_monitor"  


callurl "http://ppysq.pt.pps.tv/project.php?act=send_num&host=pt.pps.tv&v1=ppysq.pt.pps.tv(项目满意度)&to_v1=项目总览&to_v2=ppysq.pt.pps.tv(项目满意度)"
