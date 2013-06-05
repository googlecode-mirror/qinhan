<?php

/**
 * 定时任务专用
 *
 * @author 陈兴
 * @since 2012-12-19 14:43:50
 */

include "header.php";
ini_set('include_path', "./" . PATH_SEPARATOR);
$m = new m;
$_GET['act'] = $_GET['act'] ? $_GET['act'] : "index";
$m->$_GET['act']();
class m
{
    /**
     * @desc 当前日活跃用户数
     * @author 白鸽 mailto:baige@ppstream.com
     * @since  2012-12-12 10:46:54
     * @throws 注意:无DB异常处理
     */
    function count_act_user_num()
    {
        $my_conn = _mysqllogon('MYSQL_159');

        $sql = 'select count(*) as total from ysq_coordinates where update_time>=:update_time ';
        $stmt = _mysqlparse($my_conn, $sql);
        $stime = strtotime(date('Y-m-d H:0:0'));
        _mysqlbindbyname($stmt, ':update_time', $stime);
        $oicerror = _mysqlexecute($stmt);
        $row = mysql_fetch_assoc($stmt);
        echo $row['total'];
        _status(intval($row['total']), '当前日活跃用户数', '当前小时活跃用户数', '当前小时活跃用户数', NULL, VIP, 0, 'replace');
    }

    /**
     * @desc 当前日活跃用户数
     * @author 白鸽 mailto:baige@ppstream.com
     * @since  2012-12-12 10:46:54
     * @throws 注意:无DB异常处理
     */
    function count_act_user_num_1day()
    {
        $my_conn = _mysqllogon('MYSQL_159');

        $sql = 'select count(*) as total from ysq_coordinates where update_time>=:update_time ';
        $stmt = _mysqlparse($my_conn, $sql);
        $stime = strtotime(date('Y-m-d'));
        _mysqlbindbyname($stmt, ':update_time', $stime);
        $oicerror = _mysqlexecute($stmt);
        $row = mysql_fetch_assoc($stmt);
        echo $row['total'];
        _status(intval($row['total']), '当前日活跃用户数', '当前日活跃用户数', '当前日活跃用户数', NULL, VIP, 0, 'replace');
    }

    /**
     * @desc mysql内存表监控
     * @author 白鸽 mailto:baige@ppstream.com
     * @since  2012-12-12 10:48:15
     * @throws 注意:无DB异常处理
     */
    function table_space_monitor()
    {
        $my_conn = _mysqllogon('MYSQL_159');

        $sql = "select table_name, table_rows, data_length, index_length
                  from information_schema.tables
                 where table_schema = 'ppysq'";
        $stmt = _mysqlparse($my_conn, $sql);
        $oicerror = _mysqlexecute($stmt);
        while ($_row = mysql_fetch_assoc($stmt)) {
            $length = intval($_row['data_length']) + intval($_row['index_length']);
            $length = round($length / 1024 / 1024, 2);
            _status(intval($_row['table_rows']), 'MYSQL内存表数据监控', $_row['table_name'], $_row['table_name'], NULL, VIP, 0, 'replace');
            _status($length, 'MYSQL内存表空间监控', $_row['table_name'], $_row['table_name'], NULL, VIP, 0, 'replace');
        }
    }

    /**
     * 投递接口：投递未审核的的动态到审核后台服务器
     * @author 陈兴 mailto:chenxing@ppstream.com
     * @since  2012-12-26 14:48:55
     * @throws 注意:无DB异常处理
     */
    function push_ping_actions()
    {
        $chinfo = array();
        $url = 'http://admin.keyword.pps.tv/solution.cgi';
        $conn_db = _ocilogon('PPS_70');

        //动态的评论
        $sql = "select user_id, content, user_ip, comment_id, parent_id, to_char(dateline,'yyyy-mm-dd hh24:mi:ss') as ping_add_date from ysq_user_comments
                 where operation = 3 and push_time is null and dateline > sysdate - 1 / 24 / 12";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        if ($oicerror) {
            _status(1, VHOST . "(功能执行)", '定时', "web_crontab.php/{$_GET['act']}", "fetch_error:{$oicerror}", VIP);
            exit();
        }
        $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $post_data['BIZ_NAME'] = '影视圈评论';
            $post_data['CONTENT'] = $_row['CONTENT'];
            $post_data['USER_ID'] = $_row['USER_ID'];
            $post_data['USER_IP'] = $_row['USER_IP'];
            $post_data['ID'] = $_row['COMMENT_ID'];
            $post_data['PARENT_TEXT_ID'] = $_row['PARENT_ID'];
            $post_data['BAIKE_ID'] = 0;
            $post_data['URL_KEY'] = '';
            $post_data['ADD_TIME'] = $_row['PING_ADD_DATE'];

            $rs_data = _curl($chinfo, $url, $post_data);
            $rs = unserialize($rs_data);

            if (!is_array($rs)) {
                _status(1, VHOST . "(功能执行)", '定时', "web_crontab.php/{$_GET['act']}", "接受方出错:{$rs_data}", VIP);
            } else {
                if (empty($rs['FINNAL_SUCCESS'])) {
                    continue;
                }
                $sqls = "update ysq_user_comments
                         set push_time = sysdate
                         where comment_id = :id";
                $stmts = _ociparse($conn_db, $sqls);
                ocibindbyname($stmts, ':id', $_row['COMMENT_ID']);
                $oicerrort = _ociexecute($stmts);
                if ($oicerrort) {
                    _status(1, VHOST . "(功能执行)", '定时', "web_crontab.php/{$_GET['act']}", "update_error:{$oicerrort}", VIP);
                } else {
                    echo $_row['COMMENT_ID'], ',';
                }
            }
        }

        //评论类型的动态
        $sql = "select user_id, content, user_ip, id, bk_id, url_key, to_char(dateline,'yyyy-mm-dd hh24:mi:ss') as ping_add_date from ysq_user_actions
                 where operation = 3 and push_time is null and dateline > sysdate - 1 / 24 / 12";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        if ($oicerror) {
            _status(1, VHOST . "(功能执行)", '定时', "web_crontab.php/{$_GET['act']}", "fetch_error:{$oicerror}", VIP);
            exit();
        }
        $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $post_data['BIZ_NAME'] = '影视圈评论';
            $post_data['CONTENT'] = $_row['CONTENT'];
            $post_data['USER_ID'] = $_row['USER_ID'];
            $post_data['USER_IP'] = $_row['USER_IP'];
            $post_data['ID'] = $_row['ID'];
            $post_data['PARENT_TEXT_ID'] = 0;
            $post_data['BAIKE_ID'] = $_row['BK_ID'];
            $post_data['URL_KEY'] = $_row['URL_KEY'];
            $post_data['ADD_TIME'] = $_row['PING_ADD_DATE'];

            $rs_data = _curl($chinfo, $url, $post_data);
            $rs = unserialize($rs_data);

            if (!is_array($rs)) {
                _status(1, VHOST . "(功能执行)", '定时', "web_crontab.php/{$_GET['act']}", "接受方出错:{$rs_data}", VIP);
            } else {
                if (empty($rs['FINNAL_SUCCESS'])) {
                    continue;
                }
                $sqls = "update ysq_user_actions
                         set push_time = sysdate
                         where id = :id";
                $stmts = _ociparse($conn_db, $sqls);
                ocibindbyname($stmts, ':id', $_row['ID']);
                $oicerrort = _ociexecute($stmts);
                if ($oicerrort) {
                    _status(1, VHOST . "(功能执行)", '定时', "web_crontab.php/{$_GET['act']}", "update_error:{$oicerrort}", VIP);
                } else {
                    echo $_row['ID'], ',';
                }
            }
        }
    }
}
