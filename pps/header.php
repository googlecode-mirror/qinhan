<?php
if (!headers_sent()) header('Content-Type: text/html;charset=gb2312');
define('VHOST', 'ppysq.pt.pps.tv');
define('TOKEN_KEY', '_pp$tv@2012');
define('IPCS', '0x00000886|0xf0000886|0xe0000886');

//$memcache_server = new memcache_server('79');
class memcache_config
{
    var $config = array(
                        '159'  => array(
                                        array(
                                              'host' => '10.77.0.159',
                                              'port' => 11311
                                        )
                                  ),
                        '160' => array(
                                        array(
                                              'host' => '10.77.0.160',
                                              'port' => 11311
                                        )
                                  )
                   );
}

class project_config
{
    //数据库的配置名称
    var $db = 'PPS_73';
    //apache日志路径
    var $log_path = "/home/httpd/";
    //需要统计的队列
    var $ipcs = IPCS;
    //项目管理表格
    var $prj_task = 'PPYSQ_PRJ_TASK';
    var $prj_name = 'PPYSQ_PRJ_NAME';
    
    //统计的表格
    var $report_monitor = 'PPYSQ_MONITOR';
    var $report_monitor_config = 'PPYSQ_MONITOR_CONFIG';
    var $report_monitor_v1 = 'PPYSQ_MONITOR_V1';
    var $report_monitor_date = 'PPYSQ_MONITOR_DATE';
    var $report_monitor_hour = 'PPYSQ_MONITOR_HOUR';
    var $report_monitor_month = "PPYSQ_MONITOR_MONTH";
    
    var $report_doc = "PPYSQ_DOC";
    var $report_doc_detail = "PPYSQ_DOC_DETAIL";
    var $report_doc_list = "PPYSQ_DOC_LIST";
}

class oracleDB_config
{
    var $dbconfig = array(
            "MYSQL_159" => array(
                    'user_name' => 'ppysq',
                    'password' => 'm2dlHdem6ysiy.hjywaR',
                    'db' => 'ppysq',
                    'TNS' => '10.77.0.160'
            ),
            "MYSQL_160" => array(
                    'user_name' => 'ppysq',
                    'password' => 'm2dlHdem6ysiy.hjywaR',
                    'db' => 'ppysq',
                    'TNS' => '10.77.0.160'
            ),
            "PPS_31" => array(
                    'user_name' => 'ppstream',
                    'password' => 'ppstream',
                    'TNS' => 'PPS_31'
            ),
            "PPS_40" => array(
                    'user_name' => 'ppstream',
                    'password' => 'ppstream',
                    'TNS' => 'PPS_40'
            ),
            "PPS_70" => array(
                    'user_name' => 'ppstream',
                    'password' => 'ppstream',
                    'TNS' => 'PPS_70'
            ),
            "PPS_73" => array(
                    'user_name' => 'ppstream',
                    'password' => 'ppstream',
                    'TNS' => 'PPS_73'
            ),
            "PPS_51" => array(
                    'user_name' => 'ppstream',
                    'password' => 'ppstream',
                    'TNS' => 'PPS_51'
            ),
    );
}

class ftp_config
{
    var $config = array(
            'ppysq' => array(
                    'host' => '10.77.0.193',
                    'user_name' => 'kan',
                    'user_pass' => '0Sn1Ld@kLaOD@EbQNkkv',
                    'dir' => '/ppysq'
            )
    );
}
include "project/header_funtion.php";
if ($_COOKIE['project_user_sign'] == md5($_COOKIE['project_user_id'] . 'DRS' . $_COOKIE['project_user_name']))
    define('LOGINING', true);
else
    define('LOGINING', false);
;
