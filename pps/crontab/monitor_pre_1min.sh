#!/bin/sh
export PATH=$PATH:/home/httpd:/usr/local/php5_nginx/bin
export ORACLE_BASE=/home/oracle
export ORACLE_SID=pps	
export ORACLE_HOME=/home/oracle/product/10.2.0
export PATH=$PATH:$ORACLE_HOME/bin
export NLS_LANG="Simplified Chinese_china".ZHS16GBK

log_date=/home/httpd/`date +%Y_%m_%d`

#wget一个网址,阻塞模式跑
function callurl2()
{
        if ps aux |  grep -v 'grep ' | grep -q "$1" ; then
                echo `date +"%Y-%m-%d %H:%M:%S"`[x]WGET2_Fail:$1 >> ${log_date}_wget.log
        else
                wget $1 -O /dev/null >> ${log_date}_wget.log   2>&1 
        fi
}

#队列数据进数据库(注意双机)
for i in $(seq $2); do
    #cd /home/httpd/$1
    callurl2 "http://$1/project.php?act=monitor_fix&go=1"
done