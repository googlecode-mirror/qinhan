#!/bin/sh
export PATH=$PATH:/home/httpd:/usr/local/php5_nginx/bin
export ORACLE_BASE=/home/oracle
export ORACLE_SID=pps	
export ORACLE_HOME=/home/oracle/product/10.2.0
export PATH=$PATH:$ORACLE_HOME/bin
export NLS_LANG="Simplified Chinese_china".ZHS16GBK

#日志文件的存储位置.
log_date=/home/webid/logs/`date +%Y_%m_%d`
if [ ! -d "/home/webid/logs/" ]; then
    mkdir "/home/webid/logs/"
fi
#wget一个网址,保证是单一进程在跑.
function callurl()
{
        cmd=`echo $1 |sed -e 's/\(^ *\)//' -e 's/\( *$\)//' `
        if ps aux |  grep -v 'grep ' | grep -q "$cmd" ; then
                echo `date +"%Y-%m-%d %H:%M:%S"`[x]WGET_Fail:$cmd >> ${log_date}_wget.log
        else
                nohup wget $cmd  -O /dev/null >> ${log_date}_wget.log   2>&1 &
        fi
}

#wget一个网址,阻塞模式跑
function callurl2()
{
        cmd=`echo $1 |sed -e 's/\(^ *\)//' -e 's/\( *$\)//' `
        if ps aux |  grep -v 'grep ' | grep -q "$cmd" ; then
                echo `date +"%Y-%m-%d %H:%M:%S"`[x]WGET2_Fail:$cmd >> ${log_date}_wget.log
        else
                wget $cmd -O /dev/null >> ${log_date}_wget.log   2>&1
        fi
}


#wget一个网址,保证是单一进程在跑.
function callact()
{
        exec_pwd=`pwd`
        cmd=`echo $1 |sed -e 's/\(^ *\)//' -e 's/\( *$\)//' `
        if ps aux |  grep -v 'grep ' | grep -q "$cmd" ; then
                echo `date +"%Y-%m-%d %H:%M:%S"` $exec_pwd [x]PHP_Fail:$cmd >> ${log_date}_wget.log
        else
                echo  `date +"%Y-%m-%d %H:%M:%S"` $exec_pwd call@$cmd>> ${log_date}_wget.log    2>&1 &
                nohup $cmd >/dev/null    2>&1 &
        fi
}

#wget一个网址,保证是单一进程在跑.阻塞模式跑
function callact2()
{
        exec_pwd=`pwd`
        cmd=`echo $1 |sed -e 's/\(^ *\)//' -e 's/\( *$\)//' `
        if ps aux |  grep -v 'grep ' | grep -q "$cmd" ; then
                echo `date +"%Y-%m-%d %H:%M:%S"` $exec_pwd [x]PHP2_Fail:$cmd >> ${log_date}_wget.log
        else
                echo  `date +"%Y-%m-%d %H:%M:%S"` $exec_pwd call@$cmd>> ${log_date}_wget.log    2>&1 &
                $cmd >/dev/null
        fi
}


############################每小时跑一次############################################
#[PV相当大的情况下追加执行,每台从机都需要配置的,进行2次队列压缩]  */1 * * * *  脚本 目录 WEB非GZ压缩 如果是源站机设置1
#30 */1 * * *   /home/httpd/ppysq.pt.pps.tv/crontab/monitorphp_pre_1hour.sh ppysq.pt.pps.tv nogz/gz 1/0
#评分体系统一
#callurl "http://ppysq.pt.pps.tv/project.php?act=send_num&host=pt.pps.tv&v1=ppysq.pt.pps.tv(项目满意度)&to_v1=项目总览&to_v2=ppysq.pt.pps.tv(项目满意度)"

############################每1分钟跑一次############################################
#[PV相当大的情况下追加执行,每台从机都需要配置的,进行2次队列压缩]  */1 * * * *  脚本 目录 整理数据库间隔分钟 如果是源站机设置1
#*/1 * * * *   /home/httpd/ppysq.pt.pps.tv/crontab/monitorphp_pre_1min.sh ppysq.pt.pps.tv 5 1/0

