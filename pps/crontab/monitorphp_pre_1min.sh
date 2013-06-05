#!/bin/sh
export PATH=$PATH:/home/httpd:/usr/local/php5_nginx/bin
export ORACLE_BASE=/home/oracle
export ORACLE_SID=pps	
export ORACLE_HOME=/home/oracle/product/10.2.0
export PATH=$PATH:$ORACLE_HOME/bin
export NLS_LANG="Simplified Chinese_china".ZHS16GBK

if [ ! -d "/home/webid/logs/" ]; then
    mkdir "/home/webid/logs/"
fi

log_date=/home/webid/logs/`date +%Y_%m_%d`
log_date2=`date +%M`

#wgetһ����ַ,��֤�ǵ�һ��������.����ģʽ��
function callact2()
{
        exec_pwd=`pwd`
        cmd=`echo $1 |sed -e 's/\(^ *\)//' -e 's/\( *$\)//' `
        if ps aux |  grep -v 'grep ' | grep -q "$cmd" ; then
                echo `date +"%Y-%m-%d %H:%M:%S"` ${exec_pwd}[x]PHP2_Fail:$cmd >> ${log_date}_wget.log
        else
                echo  `date +"%Y-%m-%d %H:%M:%S"` ${exec_pwd}call@$cmd>> ${log_date}_wget.log    2>&1 &
                $cmd >/dev/null
        fi
}


#wgetһ����ַ,��֤�ǵ�һ��������.
function callact()
{
        exec_pwd=`pwd`
        cmd=`echo $1 |sed -e 's/\(^ *\)//' -e 's/\( *$\)//' `
        if ps aux |  grep -v 'grep ' | grep -q "$cmd" ; then
                echo `date +"%Y-%m-%d %H:%M:%S"` ${exec_pwd} [x]PHP_Fail:$cmd >> ${log_date}_wget.log
        else
                echo  `date +"%Y-%m-%d %H:%M:%S"` ${exec_pwd} call@$cmd>> ${log_date}_wget.log    2>&1 &
                nohup $cmd >/dev/null    2>&1 &
        fi
}

cd /home/httpd/$1
#·��������ͬ��Ŀͬ�������Ľű�
project_pwd=`pwd`

#�������
callact2 "php project.php act=monitor_fix pwd=$project_pwd"
if [ -e /home/httpd/$1/project2.php ] ; then
    callact2 "php project2.php act=monitor_fix pwd=$project_pwd"
fi

#���������ݿ�
callact2 "php project.php act=monitor go=1 pwd=$project_pwd"
#KPI����
if [ -e /home/httpd/$1/project2.php ] ; then
    callact2 "php project2.php act=monitor go=1 pwd=$project_pwd"
fi


#ÿ��$2����,�Ϳ�ʼͳ������,ͳ�Ʒ���������
mod=`expr $log_date2 % $2`
if [ 0 -eq $mod ] ; then
    callact2 "php project.php act=sysload pwd=$project_pwd"
    #�ӻ����������ϼ���
    if [ 1 -eq $3 ] ; then
        callact2 "php project.php act=monitor_config del=1 pwd=$project_pwd"
        #KPI����
        if [ -e /home/httpd/$1/project2.php ] ; then
            callact2 "php project2.php act=monitor_config del=1 pwd=$project_pwd"
        fi
   fi
fi

#���ɶ�ʱ�ű�
if [ 0 -eq $mod ] ; then
    callact2 "php project.php act=doc_sh  pwd=$project_pwd"
fi

#�ĵ��Ķ�ʱ���
if [ -e /home/httpd/$1/crontab/monitorphp_doc.sh ]; then
    source /home/httpd/$1/crontab/monitorphp_doc.sh
fi
