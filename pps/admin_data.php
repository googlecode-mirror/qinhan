<?php

/**
 * 恢复数据的脚本专用
 *
 * @author 陈兴
 * @since 2012-12-19 14:43:50
 */

include "header.php";
ini_set('include_path', "./" . PATH_SEPARATOR);
$m = new m;
$_GET['act'] = $_GET['act'] ? $_GET['act'] : "index";
$m->$_GET['act']();
var_dump($_GET['act']);
class m
{
    function index()
    {
        echo '404';
    }

    /**
     * @desc 恢复mysql数据
     * @author 白鸽 mailto:baige@ppstream.com
     * @since  2012-12-11 15:08:50
     * @throws 注意:无DB异常处理
     */

    function init_mem_table()
    {
        $conn_db = _ocilogon('PPS_70');
        $my_conn = _mysqllogon('MYSQL_159');
        $s = 256524;
        while (1) {
            $sql = "select max(id) as max_id from ysq_user_video_actions t";
            $stmt = _ociparse($conn_db, $sql);
            $oicerror = _ociexecute($stmt);
            ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
            $max_id = intval($_row['MAX_ID']);
            if ($s >= $max_id) {
                break;
            }
            $start = $s;
            $end = $s + 1000;

            $sql = "select to_char(dateline,'yyyy-mm-dd hh24:mi:ss') as add_time ,
            				post_time as dateline,
            				to_char(last_ping_date,'yyyy-mm-dd hh24:mi:ss') as ping_time ,
            				user_id,id act_id,parent_id act_pid,operation cat,status,notice_status
            				from ysq_user_video_actions t where id >= :start_id and id < :end_id";

            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':start_id', $start);
            ocibindbyname($stmt, ':end_id', $end);
            $oicerror = _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $arr_user_list = array();
                $arr_user_list[] = $_row['USER_ID'];

                $sql_2 = "select user_id from ysq_user_follows t where fuser_id = :fuser_id and status = 1  ";
                $stmt_2 = _ociparse($conn_db, $sql_2);
                ocibindbyname($stmt_2, ':fuser_id', $_row['USER_ID']);
                $oicerror = _ociexecute($stmt_2);
                $_row_2 = array();
                while (ocifetchinto($stmt_2, $_row_2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                    $arr_user_list[] = $_row_2['USER_ID'];
                }
                foreach ($arr_user_list as $user_id) {
                    $sql_d = 'delete from ysq_user_video_actions where user_id=:user_id and act_id = :act_id';
                    $stmt_d = _mysqlparse($my_conn, $sql_d);
                    _mysqlbindbyname($stmt_d, ':user_id', $user_id);
                    _mysqlbindbyname($stmt_d, ':act_id', $_row['ACT_ID']);
                    $oicerror = _mysqlexecute($stmt_d);

                    if (intval($_row['ACT_PID']) > 0) {
                        $sql_p = "select to_char(last_ping_date,'yyyy-mm-dd hh24:mi:ss') as ping_time from ysq_user_video_actions where id = :p_id";
                        $stmt_p = _ociparse($conn_db, $sql_p);
                        ocibindbyname($stmt_p, ':p_id', intval($_row['ACT_PID']));
                        $oicerror = _ociexecute($stmt_p);
                        ocifetchinto($stmt_p, $p_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                        $_row['PING_TIME'] = $p_row['PING_TIME'];
                    }
                    $sql_m = 'INSERT INTO ysq_user_video_actions (user_id, act_id, act_pid, dateline, cat, status, is_friends, add_time, ping_time, notice_status)
                    			VALUES (:user_id,:act_id,:act_pid,:dateline,:cat,:status,:is_friends,:add_time,:ping_time,:notice_status)';
                    $stmt_m = _mysqlparse($my_conn, $sql_m);
                    $is_friends = 0;
                    if ($user_id != $_row['USER_ID']) {
                        $is_friends = 1;
                    }
                    _mysqlbindbyname($stmt_m, ':user_id', $user_id);
                    _mysqlbindbyname($stmt_m, ':act_id', $_row['ACT_ID']);
                    _mysqlbindbyname($stmt_m, ':act_pid', intval($_row['ACT_PID']));
                    _mysqlbindbyname($stmt_m, ':dateline', intval($_row['DATELINE']));
                    _mysqlbindbyname($stmt_m, ':cat', intval($_row['CAT']));
                    _mysqlbindbyname($stmt_m, ':status', intval($_row['STATUS']));
                    _mysqlbindbyname($stmt_m, ':is_friends', $is_friends);
                    _mysqlbindbyname($stmt_m, ':add_time', strtotime($_row['ADD_TIME']));
                    _mysqlbindbyname($stmt_m, ':ping_time', strtotime($_row['PING_TIME']));
                    _mysqlbindbyname($stmt_m, ':notice_status', strtotime($_row['NOTICE_STATUS']));
                    $oicerror = _mysqlexecute($stmt_m);
                    print_r($oicerror);
                }
            }
            $s = $end;
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc 更新mysql数据库评论最后回复时间
     * @author 白鸽 mailto:baige@ppstream.com
     * @since  2012-12-25 15:08:50
     * @throws 注意:无DB异常处理

    function update_mem_table()
    {
    $conn_db = _ocilogon('PPS_70');
    $my_conn = _mysqllogon('MYSQL_159');
    $s = 256524;
    while (1)
    {
    $sql = "select max(id) as max_id from ysq_user_video_actions t";
    $stmt = _ociparse($conn_db, $sql);
    $oicerror = _ociexecute($stmt);
    ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
    $max_id = intval($_row['MAX_ID']);
    if ($s >= $max_id)
    {
    break;
    }
    $start = $s;
    $end = $s + 1000;

    $sql = "select id act_id,to_char(last_ping_date,'yyyy-mm-dd hh24:mi:ss') as ping_time
    from ysq_user_video_actions t where (parent_id is null or parent_id = 0)
    and id >= :start_id and id < :end_id";

    $stmt = _ociparse($conn_db, $sql);
    ocibindbyname($stmt, ':start_id', $start);
    ocibindbyname($stmt, ':end_id', $end);
    $oicerror = _ociexecute($stmt);
    $_row = array();
    while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
    {
    $sql_mu = "update ysq_user_video_actions set ping_time = :ping_time where act_id=:p_id or act_pid=:p_id";
    $stmt_mu = _mysqlparse($my_conn, $sql_mu);
    _mysqlbindbyname($stmt_mu, ':ping_time', strtotime($_row['PING_TIME']));
    _mysqlbindbyname($stmt_mu, ':p_id', $_row['ACT_ID']);
    $mysqlerror = _mysqlexecute($stmt_mu);
    print_r($mysqlerror);
    }
    $s = $end;
    }
    die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }*/

    /**
     * 批量投递脚本：投递之前未审核的的动态到审核后台服务器
     * @author 陈兴 mailto:chenxing@ppstream.com
     * @since  2012-12-28 09:48:55
     * @throws 注意:无DB异常处理

    function push_ping_actions_all()
    {
    $chinfo = array();
    $url = 'http://admin.keyword.pps.tv/solution.cgi';
    $conn_db = _ocilogon('PPS_70');
    $s = 0;
    while (1) {
    $s += 1000;

    $sql = "select rownum, t1.* from
    (select user_id, content, user_ip, id, parent_id, bk_id, url_key, to_char(dateline,'yyyy-mm-dd hh24:mi:ss') as ping_add_date from ysq_user_video_actions
    where operation = 3 and push_time is null) t1
    where rownum <= $s";
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
    $post_data['PARENT_TEXT_ID'] = $_row['PARENT_ID'];
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
    $sqls = "update ysq_user_video_actions
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
     */

    /**
     * @desc 删除脏数据的动态（修改$sql_p的sql删除特定的数据）
     * @author 陈兴 mailto:chenxing@ppstream.com
     * @since  2013-01-25 10:08:50
     * @throws 注意:无DB异常处理
     */
    function delete_dirty_action_by_sql()
    {
        $conn_db = _ocilogon('PPS_70');
        $my_conn = _mysqllogon('MYSQL_159');
        include 'interface_api.php';
        $g_bace = new g_base();

        $sql_p = "select id from ysq_user_video_actions where id=742294";

        $stmt_p = _ociparse($conn_db, $sql_p);
        $oicerror = _ociexecute($stmt_p);
        if ($oicerror) {
            print_r($oicerror);
        }
        while (ocifetchinto($stmt_p, $p_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            //删除oracle
            $sql = "update ysq_user_video_actions set status = 10000 where id = :id";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':id', $p_row['ID']);
            $bindNameList = $_SERVER['last_oci_bindname'];
            $oicerror = _ociexecute($stmt);
            if ($oicerror) {
                $response['error'] = 201;
                $response['message'] = "delete oracle fail";
                _status(1, 'delete_dirty_action_by_sql(sql错误)', 'delete_dirty_action_by_sql', $response['message'], var_export($oicerror, true) . '|' . var_export($bindNameList, true));
                exit(json_encode($response));
            }

            //删除mysql
            $sql_m = "update ysq_user_video_actions set status = 10000 where act_id=:act_id and is_friends = 0";
            $stmt_m = _mysqlparse($my_conn, $sql_m);
            _mysqlbindbyname($stmt_m, ':act_id', $p_row['ID']);
            $oicerror = _mysqlexecute($stmt_m);
            if ($oicerror) {
                $response['error'] = 201;
                $response['message'] = "delete mysql fail";
                _status(1, 'delete_dirty_action_by_sql(sql错误)', 'delete_dirty_action_by_sql', $response['message'], var_export($oicerror, true) . '|' . var_export($bindNameList, true));
                exit(json_encode($response));
            }
            $r = $g_bace->ping_action_delete($p_row['ID']);
        }

        echo 'end';
    }

    /**
     * @desc oracle恢复mysql重启期间的数据到mysql
     * @author 陈兴 mailto:chenxing@ppstream.com
     * @since  2013-01-25 10:08:50
     * @throws 注意:无DB异常处理
     */
    function init_mem_table_by_sql()
    {
        $conn_db = _ocilogon('PPS_70');
        $my_conn = _mysqllogon('MYSQL_159');

        $start = 0;
        include "interface_api.php";
        $g_base = new g_base();

        while ($start < 1000) {
            $limit = 100;
            $sql = "select to_char(dateline,'yyyy-mm-dd hh24:mi:ss') as add_time ,
            				post_time as dateline,
            				to_char(last_ping_date,'yyyy-mm-dd hh24:mi:ss') as ping_time ,
            				user_id,id act_id,parent_id act_pid,operation cat,status,notice_status
            				from ysq_user_video_actions t
            				where to_char(dateline,'yyyy-mm-dd hh24:mi:ss') > '2013-01-25 09:54'
            				and to_char(dateline,'yyyy-mm-dd hh24:mi:ss') < '2013-01-25 10:03'";

            $sql = $g_base->oci_limit_sql($sql, $start, $limit);

            $stmt = _ociparse($conn_db, $sql);
            $oicerror = _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $arr_user_list = array();
                $arr_user_list[] = $_row['USER_ID'];

                $sql_2 = "select user_id from ysq_user_follows t where fuser_id = :fuser_id and status = 1  ";
                $stmt_2 = _ociparse($conn_db, $sql_2);
                ocibindbyname($stmt_2, ':fuser_id', $_row['USER_ID']);
                $oicerror = _ociexecute($stmt_2);
                $_row_2 = array();
                while (ocifetchinto($stmt_2, $_row_2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                    $arr_user_list[] = $_row_2['USER_ID'];
                }
                foreach ($arr_user_list as $user_id) {
                    $sql_d = 'delete from ysq_user_video_actions where user_id=:user_id and act_id = :act_id';
                    $stmt_d = _mysqlparse($my_conn, $sql_d);
                    _mysqlbindbyname($stmt_d, ':user_id', $user_id);
                    _mysqlbindbyname($stmt_d, ':act_id', $_row['ACT_ID']);
                    $oicerror = _mysqlexecute($stmt_d);

                    if (intval($_row['ACT_PID']) > 0) {
                        $sql_p = "select to_char(last_ping_date,'yyyy-mm-dd hh24:mi:ss') as ping_time from ysq_user_video_actions where id = :p_id";
                        $stmt_p = _ociparse($conn_db, $sql_p);
                        ocibindbyname($stmt_p, ':p_id', intval($_row['ACT_PID']));
                        $oicerror = _ociexecute($stmt_p);
                        ocifetchinto($stmt_p, $p_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                        $_row['PING_TIME'] = $p_row['PING_TIME'];
                    }
                    $sql_m = 'INSERT INTO ysq_user_video_actions (user_id, act_id, act_pid, dateline, cat, status, is_friends, add_time, ping_time, notice_status)
                    			VALUES (:user_id,:act_id,:act_pid,:dateline,:cat,:status,:is_friends,:add_time,:ping_time,:notice_status)';
                    $stmt_m = _mysqlparse($my_conn, $sql_m);
                    $is_friends = 0;
                    if ($user_id != $_row['USER_ID']) {
                        $is_friends = 1;
                    }
                    _mysqlbindbyname($stmt_m, ':user_id', $user_id);
                    _mysqlbindbyname($stmt_m, ':act_id', $_row['ACT_ID']);
                    _mysqlbindbyname($stmt_m, ':act_pid', intval($_row['ACT_PID']));
                    _mysqlbindbyname($stmt_m, ':dateline', intval($_row['DATELINE']));
                    _mysqlbindbyname($stmt_m, ':cat', intval($_row['CAT']));
                    _mysqlbindbyname($stmt_m, ':status', intval($_row['STATUS']));
                    _mysqlbindbyname($stmt_m, ':is_friends', $is_friends);
                    _mysqlbindbyname($stmt_m, ':add_time', strtotime($_row['ADD_TIME']));
                    _mysqlbindbyname($stmt_m, ':ping_time', strtotime($_row['PING_TIME']));
                    _mysqlbindbyname($stmt_m, ':notice_status', strtotime($_row['NOTICE_STATUS']));
                    $oicerror = _mysqlexecute($stmt_m);
                    if ($oicerror) print_r($oicerror);
                    echo $_row['ACT_ID'] . "\r\n";
                }
            }
            $start += $limit;
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc 初始化以前动态的follow_ping_num和follow_face_num
     * @author 陈兴 mailto:chenxing@ppstream.com
     * @since  2013-02-19 10:08:50
     * @throws 注意:无DB异常处理
     */
    function init_action_ping_num()
    {
        $conn_db = _ocilogon('PPS_70');

        $start = 0;
        $limit = 1000;
        $finished = false;
        include "interface_api.php";
        $g_base = new g_base();

        while (!$finished) {
            $finished = true;
            $sql = "select
                    sum(case when operation=7 then 1 else 0 end) as follow_face_num,
                    sum(case when operation=3 then 1 else 0 end) as follow_ping_num,
                    parent_id
                    from ysq_user_video_actions
                    where (operation=7 or operation=3) and parent_id>0 and status < 10000 and dateline > sysdate-1
                    group by parent_id";

            $sql = $g_base->oci_limit_sql($sql, $start, $limit);

            $stmt = _ociparse($conn_db, $sql);
            $oicerror = _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $finished = false;
                $sql_update = "update ysq_user_video_actions set
                               follow_ping_num=:follow_ping_num,
                               follow_face_num=:follow_face_num
                               where id=:id";
                $stmt_update = _ociparse($conn_db, $sql_update);
                _ocibindbyname($stmt_update, ':follow_ping_num', $_row['FOLLOW_PING_NUM']);
                _ocibindbyname($stmt_update, ':follow_face_num', $_row['FOLLOW_FACE_NUM']);
                _ocibindbyname($stmt_update, ':id', $_row['PARENT_ID']);
                $ocierror = _ociexecute($stmt_update);
                if (!$ocierror) {
                    echo "{$_row['PARENT_ID']},";
                }
            }
            $start += $limit;
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc 初始化以前动态的follow_ping_num和follow_face_num
     * @author 陈兴 mailto:chenxing@ppstream.com
     * @since  2013-02-19 10:08:50
     * @throws 注意:无DB异常处理
     */
    function init_ysq_user_comments()
    {
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);

        $conn_db = _ocilogon('PPS_70');

        $start = 0;
        $limit = 1000;
        $finished = false;

        while (!$finished) {
            $finished = true;
            $sql = "select USER_ID,
                           PARENT_ID,
                           OPERATION,
                           CONTENT,
                           BODY_CONTENT,
                           NOTICE_STATUS,
                           NOTICE_TIME,
                           CHECK_TIME,
                           NOTICE_FROM,
                           PUSH_TIME,
                           VISIBLE_RANGE,
                           STATUS,
                           DATELINE,
                           TO_USER_ID,
                           USER_IP,
                           ID
                      from ysq_user_video_actions t
                     where operation = 7
                       and parent_id > 0 and status != 999";

            $sql = $this->oci_limit_sql($sql, $start, $limit);
            $stmt = _ociparse($conn_db, $sql);
            $oci_error = _ociexecute($stmt);
            if ($oci_error) {
                print_r($oci_error);
            }
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $finished = false;
                $sql_update = "insert into ysq_user_comments
                               (COMMENT_ID,
                               USER_ID,
                               PARENT_ID,
                               OPERATION,
                               CONTENT,
                               BODY_CONTENT,
                               NOTICE_STATUS,
                               NOTICE_TIME,
                               CHECK_TIME,
                               NOTICE_FROM,
                               PUSH_TIME,
                               VISIBLE_RANGE,
                               STATUS,
                               DATELINE,
                               TO_USER_ID,
                               USER_IP)
                               values
                               (seq_ysq_user_comments.nextval,
                               :USER_ID,
                               :PARENT_ID,
                               :OPERATION,
                               :CONTENT,
                               :BODY_CONTENT,
                               :NOTICE_STATUS,
                               :NOTICE_TIME,
                               :CHECK_TIME,
                               :NOTICE_FROM,
                               :PUSH_TIME,
                               :VISIBLE_RANGE,
                               :STATUS,
                               :DATELINE,
                               :TO_USER_ID,
                               :USER_IP)";
                $stmt_update = _ociparse($conn_db, $sql_update);
                _ocibindbyname($stmt_update, ':USER_ID', $_row['USER_ID']);
                _ocibindbyname($stmt_update, ':PARENT_ID', $_row['PARENT_ID']);
                _ocibindbyname($stmt_update, ':OPERATION', $_row['OPERATION']);
                _ocibindbyname($stmt_update, ':CONTENT', $_row['CONTENT']);
                _ocibindbyname($stmt_update, ':BODY_CONTENT', $_row['BODY_CONTENT']);
                _ocibindbyname($stmt_update, ':NOTICE_STATUS', $_row['NOTICE_STATUS']);
                _ocibindbyname($stmt_update, ':NOTICE_TIME', $_row['NOTICE_TIME']);
                _ocibindbyname($stmt_update, ':CHECK_TIME', $_row['CHECK_TIME']);
                _ocibindbyname($stmt_update, ':NOTICE_FROM', $_row['NOTICE_FROM']);
                _ocibindbyname($stmt_update, ':PUSH_TIME', $_row['PUSH_TIME']);
                _ocibindbyname($stmt_update, ':VISIBLE_RANGE', $_row['VISIBLE_RANGE']);
                _ocibindbyname($stmt_update, ':STATUS', $_row['STATUS']);
                _ocibindbyname($stmt_update, ':DATELINE', $_row['PUSH_TIME']);
                _ocibindbyname($stmt_update, ':TO_USER_ID', $_row['VISIBLE_RANGE']);
                _ocibindbyname($stmt_update, ':USER_IP', $_row['USER_IP']);
                $oci_error = _ociexecute($stmt_update);
                if ($oci_error) {
                    print_r($oci_error);
                }
                if (!$oci_error) {
                    $d_sql = "update ysq_user_video_actions set status = 999 where id=:id";
                    $d_stmt = _ociparse($conn_db, $d_sql);
                    _ocibindbyname($d_stmt, ':id', $_row['ID']);
                    $oci_error = _ociexecute($d_stmt);

                    echo "{$_row['PARENT_ID']},";
                }
            }
            //$start += $limit;
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    function init_ysq_user_comments2()
    {
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);

        $conn_db = _ocilogon('PPS_70');

        $start = 0;
        $limit = 1000;
        $finished = false;

        while (!$finished) {
            $finished = true;
            $sql = "select count(parent_id),parent_id from ysq_user_comments t where status = 1 and operation=7 group by parent_id having count(parent_id) > 1";
            $stmt = _ociparse($conn_db, $sql);
            $oci_error = _ociexecute($stmt);
            if ($oci_error) {
                print_r($oci_error);
            }
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $finished = false;

                $sql_2 = "select comment_id,user_id from ysq_user_comments where parent_id=:parent_id and operation=7 and status=1";
                $stmt_2 = _ociparse($conn_db, $sql_2);
                _ocibindbyname($stmt_2, ':parent_id', $_row['PARENT_ID']);
                $oci_error = _ociexecute($stmt_2);
                if ($oci_error) {
                    print_r($oci_error);
                }
                $user_list = array();
                $del_list = array();
                while (ocifetchinto($stmt_2, $_row_2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                    if (in_array($_row_2['USER_ID'], $user_list)) {
                        $del_list[] = $_row_2['COMMENT_ID'];
                    } else {
                        $user_list[] = $_row_2['USER_ID'];
                    }
                }

                $d_sql = "update ysq_user_comments set status = 10000 where comment_id in (" . implode(',', $del_list) . ")";
                $d_stmt = _ociparse($conn_db, $d_sql);
                $oci_error = _ociexecute($d_stmt);
                if ($oci_error) {
                    print_r($oci_error);
                }
                echo implode(',', $del_list);
            }
            //$start += $limit;
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    function oci_limit_sql($sql, $start = 0, $limit = 25)
    {
        $end = $start + $limit;
        $sql = "select * from
                (select rownum rn, t.* from ($sql) t where rownum <= $end)
                where rn > $start";
        return $sql;
    }

    //将订阅的爱频道的频道信息导到ysq_video_info表中
    function init_ipd_video_info() {
        $hash = 'project_init_ipd_video_info';
        $memcache_server = new memcache_server('160');
        $id = (int) $memcache_server->get($hash);
        $memcache_server->close();

        $id = max($id, 59291862);
        $length = 100000;
        $finished = false;

        $conn_db = _ocilogon('PPS_70');

        while (!$finished) {
            $finished = true;

            $sql = "select id, video_id, url_key
                    from ysq_user_actions
                    where id > :min_id
                    and id <= :max_id
                    and operation = 8";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':min_id', $id);
            _ocibindbyname($stmt, ':min_id', $id + $length);
            $oci_error = _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $finished = false;

                if (!empty($_row['URL_KEY'])) {
                    $new_id = 'follow_id_' . $_row['VIDEO_ID'];
                    $sql_u = "insert into ysq_video_info
                              select
                              '$new_id' as UNIQUE_ID,
                              SOURCE_ID,
                              PIC,
                              ADUIT_FLAG,
                              SERIAL_ID,
                              VIDEO_PLAY_URL,
                              VIDEO_DIR_PATH,
                              VIDEO_TITLE,
                              VIDEO_FMT,
                              IS_UGC,
                              BLACK_LIST,
                              WHITE_LIST,
                              PART_TYPE,
                              \"size\",
                              DATELINE,
                              VIDEO_RATE,
                              BK_ID,
                              VIDEO_ID,
                              URL_KEY,
                              PLAY_NUM,
                              FAVORITE_NUM,
                              FOLLOW_ID
                              from ysq_video_info
                              where unique_id = :old_id";
                    $stmt_u = _ociparse($conn_db, $sql_u);
                    _ocibindbyname($stmt_u, ':old_id', 'url_key_' . $_row['URL_KEY']);
                    $oci_error = _ociexecute($stmt_u);

                } elseif (!empty($_row['VIDEO_ID'])) {
                    $sql_u = "update ysq_video_info
                              set unique_id = :new_id
                              where unique_id = :old_id";
                    $stmt_u = _ociparse($conn_db, $sql_u);
                    _ocibindbyname($stmt_u, ':new_id', 'follow_id_' . $_row['VIDEO_ID']);
                    _ocibindbyname($stmt_u, ':old_id', 'video_id_' . $_row['VIDEO_ID']);
                    $oci_error = _ociexecute($stmt_u);

                }
            }

            //记录id到了哪里
            $memcache_server = new memcache_server('160');
            $memcache_server->set($hash, $id + $length, MEMCACHE_COMPRESSED, 0);
            $memcache_server->close();
        }
    }
}
