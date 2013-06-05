<?php
if (count(get_included_files()) == 1) $myself = true;
$_UNLOGIN = true;
include "header.php";
if (!$_SERVER['HTTP_HOST'] || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || in_array($_GET['act'], array())) {
} elseif (LOGINING <> true) {
    if (!headers_sent()) setcookie('project_location', $_SERVER["REQUEST_URI"]);
    if (is_file("admin.php"))
        die(header("location: /admin.php"));
    if (is_file("index.php"))
        die(header("location: /index.php"));
    die("No input file specified." . date('r'));
}
if (!headers_sent()) header('Content-Type: text/html;charset=gb2312');
class m extends project_config
{
    /**
     * @desc 对doc文档进行管理
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function index()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "select * from {$this->report_doc} order by doc_name ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $this->all_name = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $this->all_name[] = $_row;
            if (!$this->doc_id) $this->doc_id = $_row['DOC_ID'];
        }

        $sql = "select l.*,d.detail_id,d.detail_name,d.doc_op,d.note from {$this->report_doc_list} l ,{$this->report_doc_detail} d 
        where l.doc_id = :doc_id and l.list_id=d.list_id(+) order by l.list_id,d.detail_id,d.doc_op";
        $stmt = _ociparse($conn_db, $sql);
        if ($_REQUEST['doc_id'] && !empty($_REQUEST['doc_id'])) {
            $this->doc_id = intval(trim($_REQUEST['doc_id']));
        }
        $_GET['doc_id'] = $_REQUEST['doc_id'] = $this->doc_id;
        ocibindbyname($stmt, ':doc_id', $this->doc_id);
        $oicerror = _ociexecute($stmt);
        $this->all_data = $row = array();
        while (ocifetchinto($stmt, $row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if (!$this->all_data[$row['LIST_ID']]) $this->all_data[$row['LIST_ID']] = $row;
            if ($row["DETAIL_ID"]) $this->all_data[$row['LIST_ID']]['detail'][$row["DETAIL_ID"]] = $row;
        }
        include "project/doc_index.html";
    }

    /**
     * @desc 对doc文档进行管理
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_manage()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "select d.*,to_char(d.add_time, 'yyyy-mm-dd hh24:mi:ss') add_date from {$this->report_doc} d order by doc_id ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $this->all_name = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
            $this->all_name[] = $_row;
        include "project/doc_manage.html";
    }

    /**
     * @desc 对doc文档进行管理
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_manage_do()
    {
        $conn_db = _ocilogon($this->db);
        if ($_POST['doc_id']) {
            $sql = "update {$this->report_doc}  set doc_name=:doc_name,add_time=sysdate where doc_id=:doc_id   ";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':doc_id', $_POST['doc_id']);
            ocibindbyname($stmt, ':doc_name', trim($_POST['doc_name']));
            $oicerror = _ociexecute($stmt);
        } else {
            $sql = "insert into {$this->report_doc} (doc_id,doc_name,user_name) values (seq_{$this->report_doc}.nextval,:doc_name,:user_name)  ";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':doc_name', trim($_POST['doc_name']));
            ocibindbyname($stmt, ':user_name', $_COOKIE['user_name']);
            $oicerror = _ociexecute($stmt);
        }
        print_r($oicerror);
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc 对doc文档下的项目进行管理
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_list_manage()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "select * from {$this->report_doc} t";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $this->all_doc = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
            $this->all_doc[] = $_row;

        $sql = "select d.doc_name,l.*,to_char(l.add_time, 'yyyy-mm-dd hh24:mi:ss') add_date 
        from {$this->report_doc} d,{$this->report_doc_list} l 
        where d.doc_id = l.doc_id and l.doc_id=:doc_id order by l.list_id ";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':doc_id', $_GET['doc_id']);
        $oicerror = _ociexecute($stmt);
        $this->all_list = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
            $this->all_list[] = $_row;
        include "project/doc_list_manage.html";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-12-07 15:11:46
     * @throws 注意:无DB异常处理
     */
    function doc_list_edit()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "select * from {$this->report_doc} t";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $this->all_doc = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
            $this->all_doc[] = $_row;

        $sql = "select * from {$this->report_doc_list} where list_id=:list_id ";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':list_id', $_GET['list_id']);
        $oicerror = _ociexecute($stmt);
        $this->row = array();
        ocifetchinto($stmt, $this->row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

        include "project/doc_list_edit.html";
    }

    /**
     * @desc 对doc文档进行管理
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_list_do()
    {
        $conn_db = _ocilogon($this->db);
        if ($_GET['list_id']) {
            $sql = "update {$this->report_doc_list}  set doc_id=:doc_id,list_name=:list_name,add_time=sysdate,des=:des, format=:format 
            where list_id=:list_id   ";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':list_id', $_GET['list_id']);
            ocibindbyname($stmt, ':doc_id', $_POST['doc_id']);
            ocibindbyname($stmt, ':list_name', $_POST['list_name']);
            ocibindbyname($stmt, ':des', $_POST['des']);
            ocibindbyname($stmt, ':format', $_POST['format']);
            $oicerror = _ociexecute($stmt);
        } else {
            $sql = "insert into {$this->report_doc_list} (doc_id,list_id,list_name,des,format) values 
            (:doc_id,seq_{$this->report_doc}.nextval,:list_name,:des,:format)";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':doc_id', $_POST['doc_id']);
            ocibindbyname($stmt, ':list_name', $_POST['list_name']);
            ocibindbyname($stmt, ':des', $_POST['des']);
            ocibindbyname($stmt, ':format', $_POST['format']);
            $oicerror = _ociexecute($stmt);
        }
        print_r($oicerror);
        echo "<script>parent.window.location.reload();</script>";
    }

    /**
     * @desc 对doc文档下的项目进行管理
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_list_del()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "delete from {$this->report_doc_list} where list_id = :list_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':list_id', $_REQUEST['list_id']);
        $oicerror = _ociexecute($stmt);
        $sql1 = "delete from  {$this->report_doc_detail}   where list_id = :list_id";
        $stmt1 = _ociparse($conn_db, $sql1);
        ocibindbyname($stmt1, ':list_id', $_REQUEST['list_id']);
        $oicerror = _ociexecute($stmt1);
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc 对doc文档下的项目进行管理
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_detail_manage()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "select l.list_name,d.*,to_char(d.add_date, 'yyyy-mm-dd hh24:mi:ss') add_time 
        from {$this->report_doc_detail} d,{$this->report_doc_list} l where d.list_id = l.list_id and d.list_id=:list_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':list_id', $_REQUEST['list_id']);
        $oicerror = _ociexecute($stmt);
        $this->all_detail = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
            $this->all_detail[] = $_row;
        include "project/doc_detail_manage.html";
    }

    /**
     * @desc 对doc文档进行管理
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_detail_do()
    {
        $conn_db = _ocilogon($this->db);
        if ($_POST['detail_id']) {
            $sql = "update {$this->report_doc_detail}  set detail_name=:detail_name,add_date=sysdate,doc_op=:doc_op, note=:note where detail_id=:detail_id   ";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':detail_id', $_POST['detail_id']);
            ocibindbyname($stmt, ':detail_name', trim($_POST['detail_name']));
            ocibindbyname($stmt, ':doc_op', trim($_POST['doc_op']));
            ocibindbyname($stmt, ':note', trim($_POST['note']));
            $oicerror = _ociexecute($stmt);
        } else {
            $sql = "insert into {$this->report_doc_detail} (list_id,detail_id,detail_name,doc_op,note) values (:list_id,seq_{$this->report_doc}.nextval,:detail_name,:doc_op,:note)";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':list_id', $_POST['list_id']);
            ocibindbyname($stmt, ':detail_name', $_POST['detail_name']);
            ocibindbyname($stmt, ':doc_op', $_POST['doc_op']);
            ocibindbyname($stmt, ':note', $_POST['note']);
            $oicerror = _ociexecute($stmt);
            if ($oicerror) die(json_encode(array(
                'code' => -2,
                'msg' => '数据库错误:' . var_export($oicerror, true)
            )));
        }
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc 对doc文档下的项目进行管理
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_detail_del()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "delete from {$this->report_doc_detail} where detail_id = :detail_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':detail_id', $_REQUEST['detail_id']);
        $oicerror = _ociexecute($stmt);
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc 对doc文档下的项目进行交换顺序
     * @author 黄世密 mailto:hsms@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_exchang()
    {
        $up = $_GET['detail_up'];
        $down = $_GET['detail_down'];
        $list_id = $_GET['list_id'];
        if ($up && $down && $list_id) {
            $a = 0;
            $this->detail_up($up, $a, $list_id);
            $this->detail_up($down, $up, $list_id);
            $this->detail_up($a, $down, $list_id);
        }
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc 对doc文档下的项目进行交换顺序
     * @author 黄世密 mailto:hsms@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function detail_up($detail_id, $id, $list_id)
    {
        $conn_db = _ocilogon($this->db);
        $sql = "update {$this->report_doc_detail} set detail_id=:id where detail_id=:detail_id and list_id=:list_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':detail_id', $detail_id);
        ocibindbyname($stmt, ':list_id', $list_id);
        ocibindbyname($stmt, ':id', $id);
        $ocierror = _ociexecute($stmt);
        return $ocierror;
    }
}

if ($myself) {
    $m = new m;
    $_GET['act'] = $_GET['act'] ? $_GET['act'] : "index";
    $m->$_GET['act']();
}
