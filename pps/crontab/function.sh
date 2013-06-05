#!/bin/sh
export PATH=$PATH:/home/httpd:/usr/local/php5_nginx/bin
export ORACLE_BASE=/home/oracle
export ORACLE_SID=pps	
export ORACLE_HOME=/home/oracle/product/10.2.0
export PATH=$PATH:$ORACLE_HOME/bin
export NLS_LANG="Simplified Chinese_china".ZHS16GBK

#��־�ļ��Ĵ洢λ��.
log_date=/home/webid/logs/`date +%Y_%m_%d`
if [ ! -d "/home/webid/logs/" ]; then
    mkdir "/home/webid/logs/"
fi
#wgetһ����ַ,��֤�ǵ�һ��������.
function callurl()
{
        cmd=`echo $1 |sed -e 's/\(^ *\)//' -e 's/\( *$\)//' `
        if ps aux |  grep -v 'grep ' | grep -q "$cmd" ; then
                echo `date +"%Y-%m-%d %H:%M:%S"`[x]WGET_Fail:$cmd >> ${log_date}_wget.log
        else
                nohup wget $cmd  -O /dev/null >> ${log_date}_wget.log   2>&1 &
        fi
}

#wgetһ����ַ,����ģʽ��
function callurl2()
{
        cmd=`echo $1 |sed -e 's/\(^ *\)//' -e 's/\( *$\)//' `
        if ps aux |  grep -v 'grep ' | grep -q "$cmd" ; then
                echo `date +"%Y-%m-%d %H:%M:%S"`[x]WGET2_Fail:$cmd >> ${log_date}_wget.log
        else
                wget $cmd -O /dev/null >> ${log_date}_wget.log   2>&1
        fi
}


#wgetһ����ַ,��֤�ǵ�һ��������.
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

#wgetһ����ַ,��֤�ǵ�һ��������.����ģʽ��
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


############################ÿСʱ��һ��############################################
#[PV�൱��������׷��ִ��,ÿ̨�ӻ�����Ҫ���õ�,����2�ζ���ѹ��]  */1 * * * *  �ű� Ŀ¼ WEB��GZѹ�� �����Դվ������1
#30 */1 * * *   /home/httpd/ppysq.pt.pps.tv/crontab/monitorphp_pre_1hour.sh ppysq.pt.pps.tv nogz/gz 1/0
#������ϵͳһ
#callurl "http://ppysq.pt.pps.tv/project.php?act=send_num&host=pt.pps.tv&v1=ppysq.pt.pps.tv(��Ŀ�����)&to_v1=��Ŀ����&to_v2=ppysq.pt.pps.tv(��Ŀ�����)"

############################ÿ1������һ��############################################
#[PV�൱��������׷��ִ��,ÿ̨�ӻ�����Ҫ���õ�,����2�ζ���ѹ��]  */1 * * * *  �ű� Ŀ¼ �������ݿ������� �����Դվ������1
#*/1 * * * *   /home/httpd/ppysq.pt.pps.tv/crontab/monitorphp_pre_1min.sh ppysq.pt.pps.tv 5 1/0

