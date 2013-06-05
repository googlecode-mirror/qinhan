<?php
include "header.php";
$m = new m;
$_GET['act'] = $_GET['act'] ? $_GET['act'] : "index";
$m->$_GET['act']();
class m
{
    /**
     * @desc WHAT?
     * @author 王昕曌 mailto:xzhwang@ppstream.com
     * @since  2012-08-09 15:12:46
     * @throws 注意:无DB异常处理
     */
    function admin_add_user()
    {
        if ($_REQUEST['sign'] != '7c1e35d5b8e232f3495568019711d09b')
        {
            _status(1, VHOST . '(账户日志)', "添加账户签名错误", var_export($_REQUEST, true), NULL, VIP);
            die(json_encode(array(
                    'code' => -1,
                    'msg' => 'err sign',
                    'ip' => $_SERVER['REMOTE_ADDR']
            )));
        }
        $_REQUEST['user_name'] = mb_convert_encoding($_REQUEST['user_name'], 'GBK', 'UTF-8');
        $conn_db = _ocilogon('PPS_70');
        
        $sql = "select oa_id from PPYSQ_OA_USER where user_name = :user_name and oa_id=:oa_id ";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':user_name', $_REQUEST['user_name']);
        ocibindbyname($stmt, ':oa_id', $_REQUEST['certify_id']);
        $oicerror = _ociexecute($stmt);
        if ($oicerror) die(json_encode(array(
                'code' => -2,
                'msg' => $oicerror
        )));
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        if (!empty($_row))
        {
            if (intval($_row['OA_ID']) > 0) die(json_encode(array(
                    'code' => 1,
                    'msg' => 'exist'
            )));
        } else
        {
            _status(1, VHOST . '(账户日志)', "添加账户", "{$_REQUEST['user_name']}({$_REQUEST['certify_id']})", NULL, VIP);
            $sql = "insert into PPYSQ_OA_USER (id,user_name,oa_id,add_time) 
        			values (SEQ_PPYSQ_OA_USER.Nextval,:user_name,:oa_id,sysdate)";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':user_name', $_REQUEST['user_name']);
            ocibindbyname($stmt, ':oa_id', $_REQUEST['certify_id']);
            $oicerror = _ociexecute($stmt);
            if ($oicerror) die(json_encode(array(
                    'code' => -2,
                    'msg' => $oicerror
            )));
            die(json_encode(array(
                    'code' => 1,
                    'msg' => 'new user'
            )));
        }
        die(json_encode(array(
                'code' => -3,
                'msg' => 'what?'
        )));
    }
    
    /**
     * @desc WHAT?
     * @author 王昕曌 mailto:xzhwang@ppstream.com
     * @since  2012-08-09 15:12:46
     * @throws 注意:无DB异常处理
     */
    function admin_del_user()
    {
        if ($_REQUEST['sign'] != '7c1e35d5b8e232f3495568019711d09b')
        {
            _status(1, VHOST . '(账户日志)', "添加账户签名错误", var_export($_REQUEST, true), NULL, VIP);
            die(json_encode(array(
                    'code' => -1,
                    'msg' => 'err sign',
                    'ip' => $_SERVER['REMOTE_ADDR']
            )));
        }
        $conn_db = _ocilogon('PPS_70');
        $sql = "select * from PPYSQ_OA_USER where  oa_id=:oa_id ";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':oa_id', $_REQUEST['certify_id']);
        $ocierror = _ociexecute($stmt);
        if ($oicerror) die(json_encode(array(
                'code' => -2,
                'msg' => $oicerror
        )));
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        _status(1, VHOST . '(账户日志)', "删除账户", "{$_row['USER_NAME']}({$_REQUEST['certify_id']})", NULL, VIP);
        $sql = "delete from PPYSQ_OA_USER   where oa_id = :oa_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':oa_id', $_REQUEST['certify_id']);
        _ociexecute($stmt);
        if ($oicerror) die(json_encode(array(
                'code' => -2,
                'msg' => $oicerror
        )));
        die(json_encode(array(
                'code' => 1
        )));
    }
}
