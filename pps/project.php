<?php

if (count(get_included_files()) == 1)
    $myself = true;
$_UNLOGIN = true;
include "header.php";
if (LOGINING === true) {

} elseif (!$_SERVER['HTTP_HOST'] || $_SERVER['REMOTE_ADDR'] == '127.0.0.1'
    || in_array($_GET['act'], array(
        'api',
        'sysload',
        'send_num',
        'web_log',
        'project_data',
        'web_status',
        'monitor',
        'monitor_fix',
        'monitor_config',
        'monitor_duty',
        'monitor_check',
        'project_duibi',
        'report_monitor_group',
        'doc_crontab_load_db',
        'crontab_report_pinfen'
    ))
) {

} else {
    if (!headers_sent())
        setcookie('project_location', $_SERVER["REQUEST_URI"]);
    if (is_file("admin.php"))
        die(header("location: /admin.php"));
    if (is_file("index.php"))
        die(header("location: /index.php"));
    die("No input file specified." . date('r'));
}
if (!headers_sent())
    header('Content-Type: text/html;charset=gb2312');
include "project/project_api.php";

class m extends project_api
{

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-05-19 13:01:53
     * @throws 注意:无DB异常处理
     */
    function doc_db()
    {
        include "project/_doc_db.html";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-05-21 09:53:31
     * @throws 注意:无DB异常处理
     */
    function doc_crontab_load_db()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "select * from {$this->report_doc_list} t where t.list_name like 'Table:%@%' ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $_GET['table_name'] = $_row['LIST_NAME'];
            $_GET['list_id'] = $_row['LIST_ID'];
            $this->doc_load_db();
            print_r($_GET);
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-05-19 12:54:28
     * @throws 注意:无DB异常处理
     */
    function doc_load_db()
    {
        if (strpos($_GET['table_name'], ':'))
            $_GET['table_name'] = substr($_GET['table_name'], strpos($_GET['table_name'], ':') + 1);
        if (strpos($_GET['table_name'], '@')) {
            $_GET['db'] = substr($_GET['table_name'], strpos($_GET['table_name'], '@') + 1);
            if ($_GET['db']) {
                $oracleDB_config = new oracleDB_config;
                if ($oracleDB_config->dbconfig[$_GET['db']]['db'])
                    $_GET['dbtype'] = 'mysql';
                else
                    $_GET['dbtype'] = 'oracle';
            }
            $_GET['table_name'] = substr($_GET['table_name'], 0, strpos($_GET['table_name'], '@'));
        }

        if ($_GET['dbtype'] == 'mysql') {
            $conn_db = _mysqllogon($_GET['db']);
            mysql_query("SET NAMES 'gbk'");
            $sql = "show create table {$_GET['table_name']}";
            $stmt = _mysqlparse($conn_db, $sql);
            $_ocierror = _mysqlexecute($stmt);
            $row = mysql_fetch_assoc($stmt);
            _mysqlclose($conn_db);
            print_r($_ocierror);
        } else {
            $conn_db = _ocilogon($_GET['db']);
            //字段长度定义
            $sql = "select t.COLUMN_NAME, t.DATA_TYPE || '(' || t.DATA_LENGTH || ')' as name from user_tab_columns t  where table_name = upper(:table_name) order by column_id  ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':table_name', $_GET['table_name']);
            $ocierror = _ociexecute($stmt);
            $_row_0_all = $_row_0 = array();
            while (ocifetchinto($stmt, $_row_0, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $_row_0_all[$_row_0['COLUMN_NAME']] = $_row_0['NAME'];
            }

            $row['Create Table'] = NULL;
            //列举表格内的全部字段
            $sql = "select * from all_col_comments where table_name=upper(:table_name)";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':table_name', $_GET['table_name']);
            $ocierror = _ociexecute($stmt);
            $_row_1_all = $row = $_row_1 = array();
            while (ocifetchinto($stmt, $_row_1, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $_row_1_all[$_row_1['COLUMN_NAME']] = $_row_1['COMMENTS'];
            }
            foreach ($_row_0_all as $k => $v) {
                //字段过多的情况下,字段的长度定义扔掉
                if (count($_row_0_all) > 30)
                    $v = NULL;
                $row['Create Table'] .= "`$k` " . strtolower($v) . " '" . trim($_row_1_all[$k]) . "'\n";
            }
            $row['Create Table'] .= "\n--[索引]-->\n";
            //列举表格的索引
            $sql = "select t.index_name,t.index_type,uniqueness  from user_indexes t where table_name=upper(:table_name) ";
            $stmt_list = _ociparse($conn_db, $sql);
            ocibindbyname($stmt_list, ':table_name', $_GET['table_name']);
            $ocierror = _ociexecute($stmt_list);
            $_row_0 = array();
            while (ocifetchinto($stmt_list, $_row_0, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                if (strpos($_row_0['INDEX_NAME'], '$') !== false) continue;
                if ($_row_0['UNIQUENESS'] == 'NONUNIQUE') $_row_0['UNIQUENESS'] = $_row_0['INDEX_TYPE'];
                else $_row_0['UNIQUENESS'] = $_row_0['INDEX_TYPE'] . '@' . $_row_0['INDEX_TYPE'];
                $row['Create Table'] .= "{$_row_0['INDEX_NAME']} ({$_row_0['UNIQUENESS']}) [";
                $sql = "select t.INDEX_NAME,t.COLUMN_NAME  from user_ind_columns t where index_name=upper(:index_name) ";
                $stmt = _ociparse($conn_db, $sql);
                ocibindbyname($stmt, ':index_name', $_row_0['INDEX_NAME']);
                $ocierror = _ociexecute($stmt);
                $_row_1 = array();
                while (ocifetchinto($stmt, $_row_1, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                    $row['Create Table'] .= "{$_row_1['COLUMN_NAME']},";
                }
                $row['Create Table'] .= "]\n";
            }


            $row['Create Table'] .= "\n==[表注释]==>\n";
            //表格本身注释
            $sql = "select r.COMMENTS  from all_tab_comments r where table_name = upper(:table_name) and COMMENTS is not null  ";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':table_name', $_GET['table_name']);
            $ocierror = _ociexecute($stmt);
            $_row_2 = array();
            ocifetchinto($stmt, $_row_2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
            $row['Create Table'] .= $_row_2['COMMENTS'];
        }

        if ($row['Create Table']) {
            $row['Create Table'] = substr($row['Create Table'], 0, 4000);
            $conn_db = _ocilogon($this->db);
            $stmt = _ociparse($conn_db, "select list_name from {$this->report_doc_list} where list_id=:list_id ");
            ocibindbyname($stmt, ':list_id', $_GET['list_id']);
            $ocierror = _ociexecute($stmt);
            $_row_3 = array();
            ocifetchinto($stmt, $_row_3, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

            $sql = "update {$this->report_doc_list} t set list_name=:list_name, des=:des where list_id=:list_id ";
            $stmt = _ociparse($conn_db, $sql);
            if (strpos($_row_3['LIST_NAME'], '@'))
                $_row_3['LIST_NAME'] = substr($_row_3['LIST_NAME'], 0, strpos($_row_3['LIST_NAME'], '@'));
            ocibindbyname($stmt, ':list_name', strval($_row_3['LIST_NAME'] . "@" . $_GET['db']));
            ocibindbyname($stmt, ':list_id', $_GET['list_id']);
            ocibindbyname($stmt, ':des', $row['Create Table']);
            $ocierror = _ociexecute($stmt);
        }
        echo "<script>parent.window.location.reload();</script>";
    }

    /**
     * @desc 对doc文档进行管理
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_index()
    {
        $group_name = trim($_GET['group_name']);
        $group_name_2 = trim($_GET['group_name_2']);
        $conn_db = _ocilogon($this->db);
        $sql = "select * from {$this->report_doc} where group_name = :group_name and group_name_2 = :group_name_2 order by doc_name ";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':group_name', $group_name);
        ocibindbyname($stmt, ':group_name_2', $group_name_2);
        $oicerror = _ociexecute($stmt);
        $this->all_name = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $this->all_name[] = $_row;
            if (!$this->doc_id) $this->doc_id = $_row['DOC_ID'];
        }

        //查询满足条件的接口/数据库 总数
        $count_sql = "select count(*) from {$this->report_doc_list} l ,{$this->report_doc_detail} d
        where l.doc_id = :doc_id and l.list_id=d.list_id(+) ";
        if ($_GET['query_infos']) {
            $count_sql .= "and( l.list_name like :list_name ";
            $count_sql .= "or l.des like :des ";
            $count_sql .= "or l.format like :format) ";
        }
        $count_sql .= "order by l.list_id,d.detail_id,d.doc_op";
        $stmt = _ociparse($conn_db, $count_sql);
        if ($_REQUEST['doc_id'] && !empty($_REQUEST['doc_id'])) {
            $this->doc_id = intval(trim($_REQUEST['doc_id']));
        }
        $_GET['doc_id'] = $_REQUEST['doc_id'] = $this->doc_id;
        ocibindbyname($stmt, ':doc_id', $this->doc_id);
        _ocibindbyname($stmt, ':num_1', intval($this->pageObj->limit_1));
        _ocibindbyname($stmt, ':num_3', intval($this->pageObj->limit_3));
        if ($_GET['query_infos']) {
            $query_infos = "%{$_GET['query_infos']}%";
            ocibindbyname($stmt, ':list_name', $query_infos);
            ocibindbyname($stmt, ':des', $query_infos);
            ocibindbyname($stmt, ':format', $query_infos);
        }
        $oicerror = _ociexecute($stmt);
        $count = null;
        ocifetchinto($stmt, $count, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

        //分页查询显示数据
        $this->pageObj = new page($count['COUNT(*)'], 200);
        //$this->fun_count($this->all_data );
        $sql = "select l.*,d.detail_id,d.detail_name,d.data_type,d.encoding_type,d.doc_op,d.detail_type,d.department,d.note from {$this->report_doc_list} l ,{$this->report_doc_detail} d
        where l.doc_id = :doc_id and l.list_id=d.list_id(+) ";
        if ($_GET['query_infos']) {
            $sql .= "and( l.list_name like :list_name ";
            $sql .= "or l.des like :des ";
            $sql .= "or l.format like :format) ";
        }

        $sql .= "order by l.list_id,d.detail_id,d.doc_op";
        $stmt = _ociparse($conn_db, "{$this->pageObj->num_1} {$sql} {$this->pageObj->num_3}");
        if ($_REQUEST['doc_id'] && !empty($_REQUEST['doc_id'])) {
            $this->doc_id = intval(trim($_REQUEST['doc_id']));
        }
        $_GET['doc_id'] = $_REQUEST['doc_id'] = $this->doc_id;
        ocibindbyname($stmt, ':doc_id', $this->doc_id);
        _ocibindbyname($stmt, ':num_1', intval($this->pageObj->limit_1));
        _ocibindbyname($stmt, ':num_3', intval($this->pageObj->limit_3));
        if ($_GET['query_infos']) {
            $query_infos = "%{$_GET['query_infos']}%";
            ocibindbyname($stmt, ':list_name', $query_infos);
            ocibindbyname($stmt, ':des', $query_infos);
            ocibindbyname($stmt, ':format', $query_infos);
        }
        $oicerror = _ociexecute($stmt);
        $this->all_data = $row = array();
        while (ocifetchinto($stmt, $row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if ($row['RETURN_TYPE'] == 'json' && $row['FORMAT'] != '') {
                $row['FORMAT'] = var_export((array)json_decode($row['FORMAT']), true);
            } elseif ($row['RETURN_TYPE'] == 'serialize' && $row['FORMAT'] != '') {
                $row['FORMAT'] = var_export(unserialize($row['FORMAT']), true);
            }

            if (!$this->all_data[$row['LIST_ID']]) {
                $this->all_data[$row['LIST_ID']] = $row;
            }
            if ($row["DETAIL_ID"] && $row['DETAIL_TYPE'] == "contract") {
                $this->all_data[$row['LIST_ID']]['contract'][$row["DETAIL_ID"]] = $row;
            } else if ($row["DETAIL_ID"]) {
                $this->all_data[$row['LIST_ID']]['detail'][$row["DETAIL_ID"]] = $row;
            }
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
        $group_name = trim($_GET['group_name']);
        $group_name_2 = trim($_GET['group_name_2']);
        $conn_db = _ocilogon($this->db);
        $sql = "select d.*,to_char(d.add_time, 'yyyy-mm-dd hh24:mi:ss') add_date from {$this->report_doc} d where group_name = :group_name and group_name_2 = :group_name_2
        order by doc_name ";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':group_name', $group_name);
        ocibindbyname($stmt, ':group_name_2', $group_name_2);
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
        $group_name = trim($_POST['group_name']);
        $group_name_2 = trim($_POST['group_name_2']);
        $conn_db = _ocilogon($this->db);
        if ($_POST['doc_id']) {
            $sql = "update {$this->report_doc}  set doc_name=:doc_name,add_time=sysdate,group_name_2=:group_name_2,group_name=:group_name where doc_id=:doc_id   ";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':doc_id', $_POST['doc_id']);
            ocibindbyname($stmt, ':doc_name', trim($_POST['doc_name']));
            ocibindbyname($stmt, ':group_name', $group_name);
            ocibindbyname($stmt, ':group_name_2', $group_name_2);
            $oicerror = _ociexecute($stmt);
        } else {
            $sql = "insert into {$this->report_doc} (doc_id,doc_name,user_name,group_name_2,group_name) values (seq_{$this->report_doc}.nextval,:doc_name,:user_name,:group_name_2,:group_name)  ";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':doc_name', trim($_POST['doc_name']));
            ocibindbyname($stmt, ':user_name', $_COOKIE['project_user_name']);
            ocibindbyname($stmt, ':group_name', $group_name);
            ocibindbyname($stmt, ':group_name_2', $group_name_2);
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
        $sql = "select * from {$this->report_doc} t order by DOC_NAME,t.group_name_2, t.group_name";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $this->all_doc = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
            $this->all_doc[] = $_row;

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
        $_POST['des'] = strtr($_POST['des'], array("\r" => NULL));
        if ($_GET['list_id']) {
            $sql = "update {$this->report_doc_list}  set doc_id=:doc_id,list_name=:list_name,add_time=sysdate,des=:des, format=:format,return_type=:return_type
            where list_id=:list_id   ";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':list_id', $_GET['list_id']);
            ocibindbyname($stmt, ':doc_id', $_POST['doc_id']);
            ocibindbyname($stmt, ':list_name', $_POST['list_name']);
            ocibindbyname($stmt, ':des', $_POST['des']);
            ocibindbyname($stmt, ':format', $_POST['format']);
            ocibindbyname($stmt, ':return_type', $_POST['return_type']);
            $oicerror = _ociexecute($stmt);
        } else {
            $sql = "insert into {$this->report_doc_list} (doc_id,list_id,list_name,des,format,return_type) values
            (:doc_id,seq_{$this->report_doc}.nextval,:list_name,:des,:format,:return_type)";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':doc_id', $_POST['doc_id']);
            ocibindbyname($stmt, ':list_name', $_POST['list_name']);
            ocibindbyname($stmt, ':des', $_POST['des']);
            ocibindbyname($stmt, ':format', $_POST['format']);
            ocibindbyname($stmt, ':return_type', $_POST['return_type']);
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
        $sql = "select count(*) c ,max(doc_id)  doc_id from {$this->report_doc_list} t where doc_id = (select doc_id from {$this->report_doc_list} t where t.list_id = :list_id )";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':list_id', $_REQUEST['list_id']);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        if ($_row['C'] == 1) {
            $sql = "delete  from {$this->report_doc} where doc_id = :doc_id";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':doc_id', $_row['DOC_ID']);
            $oicerror = _ociexecute($stmt);
        }

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

        //doc_list info
        $sql = "select t.list_id, t.list_name, t.des from BK_DOC_LIST t where t.list_id=:list_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':list_id', $_REQUEST['list_id']);
        $oicerror = _ociexecute($stmt);
        $this->list_info = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
            $this->list_info = $_row;

        //doc_detail column info
        $sql = "select l.list_name,d.*,to_char(d.add_date, 'yyyy-mm-dd hh24:mi:ss') add_time
        from {$this->report_doc_detail} d,{$this->report_doc_list} l where d.list_id = l.list_id and d.list_id=:list_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':list_id', $_REQUEST['list_id']);
        $oicerror = _ociexecute($stmt);
        $this->all_detail = $_row = array();
        $this->all_contract = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if ($_row['DETAIL_TYPE'] == 'contract') {
                $this->all_contract[] = $_row;
            } else {
                $this->all_detail[] = $_row;
            }

        }
        //edit detail info
        $this->detail_info = array();
        if ($_GET['detail_id']) {
            if (isset($_GET['edit_type']) && $_GET['edit_type'] == 'contract') {
                foreach ($this->all_contract as $k => $v) {
                    if ($v['DETAIL_ID'] == $_GET['detail_id']) {
                        $this->detail_info = $v;
                        break;
                    }
                }
            } else {
                foreach ($this->all_detail as $k => $v) {
                    if ($v['DETAIL_ID'] == $_GET['detail_id']) {
                        $this->detail_info = $v;
                        break;
                    }
                }
            }

            if (!$this->detail_info) {
                die('需要编辑的项目不存在');
            }
        }
        include "project/doc_detail_manage.html";
    }

    /**
     * @desc 获取用户信息
     * @author 吴成成 mailto:wuchengcheng@ppstream.com
     * @since  2013-05-31 17:23:00
     * @throws
     */
    function get_people_info_ajax()
    {
        if ($_POST['username']) {
            $url = "http://oa.ppstream.com/getEmpInfo?name=" . $_POST['username'];
            $chinfo = null;
            $result = _curl($chinfo, $url);
            if ($chinfo['http_code'] == '200') {
                echo $result;
            }
        }
    }


    /**
     * @desc 对doc文档下的接口进行测试
     * @author 吴成成 mailto:wuchengcheng@ppstream.com
     * @since  2013-05-28 09:23:00
     * @throws
     */
    function doc_detail_test()
    {
        $conn_db = _ocilogon($this->db);
        if (!$_GET['list_id'] || !$_GET['doc_id']) {
            die('list id or doc id is not passed');
        }
        $this->test_case = 'test_1';
        if (!empty($_GET['test_case'])) {
            switch ($_GET['test_case']) {
                case 'test_2':
                    $this->test_case = 'test_2';
                    break;
                case 'test_3':
                    $this->test_case = 'test_3';
                    break;
                default;
                    $this->test_case = 'test_1';
            }
        }
        //get the information of the api to be tested
        $sql = "select t.list_name, t.des,t.format,t.return_type, t.{$this->test_case} from {$this->report_doc_list} t
        where t.doc_id=:doc_id and t.list_id =:list_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':doc_id', $_GET['doc_id']);
        ocibindbyname($stmt, ':list_id', $_GET['list_id']);
        $ocierror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $this->list_info = $_row;
        if (!$this->list_info) {
            die(编辑的项目接口不存在);
        }

        if ($this->list_info[strtoupper($this->test_case)]) {
            $test_case_data = unserialize($this->list_info[strtoupper($this->test_case)]);
            $this->test_datas = $test_case_data['datas'];
            $this->auto_run = $test_case_data['auto_run'];
            $this->test_index = $test_case_data['test_index'];
        }


        //get the information of the arguments
        //doc_detail column info
        $sql = "select l.list_name,d.*,to_char(d.add_date, 'yyyy-mm-dd hh24:mi:ss') add_time
        from {$this->report_doc_detail} d,{$this->report_doc_list} l where d.list_id = l.list_id and d.list_id=:list_id and d.detail_type=:detail_type";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':list_id', $_GET['list_id']);
        $detail_type = 'item';
        ocibindbyname($stmt, ':detail_type', $detail_type);
        $oicerror = _ociexecute($stmt);
        $this->all_detail = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if (isset($test_data[$_row['DETAIL_NAME']])) {
                $_row['VALUE'] = $test_data[$_row['DETAIL_NAME']];
            }
            $this->all_detail[] = $_row;
        }
        include "project/doc_detail_test.html";
    }

    /**
     * @desc 测试数据执行
     * @author 吴成成 mailto:wuchengcheng@ppstream.com
     * @since  2013-05-28 13:31:00
     * @throws
     */
    function doc_detail_test_ajax()
    {
        if (!$_POST) {
            die('no data posted!');
        }
        $datas = $_POST;
        $post_datas = array();
        $get_datas = array();
        $api = $datas['api'];
        unset($datas['api']);
        $test_act = $datas['action'];
        unset($datas['action']);
        $this->test_index = $datas['test_index'];
        unset($datas['test_index']);
        $auto_run = $datas['auto_run'];
        unset($datas['auto_run']);

        //保存测试例数据
        $conn_db = _ocilogon($this->db);
        $test_case = strtoupper($_GET['test_case']);
        $sql = "update {$this->report_doc_list} t set t.{$test_case}=:test_case where t.LIST_ID=:list_id";
        $stmt = _ociparse($conn_db, $sql);
        $save_datas = array();
        $save_datas['test_index'] = $this->test_index;
        $save_datas['auto_run'] = $auto_run;
        foreach ($datas as $k => $v) {
            if ($v['item_value'] != '' && $v['item_name'] != '') {
                $save_datas['datas'][$v['item_name']]['item_value'] = $v['item_value'];
                $save_datas['datas'][$v['item_name']]['data_type'] = $v['data_type'];
            }
        }
        _ocibindbyname($stmt, ":test_case", serialize($save_datas));
        _ocibindbyname($stmt, ":list_id", $_GET['list_id']);
        $ocierror = _ociexecute($stmt);
        echo "<div  class='tBd'><h1>保存结果</h1>";
        if ($ocierror) {
            die("保存失败");
        } else {
            echo "保存成功<br/>";
        }
        echo "</div><br/>";

        //执行测试
        if ($test_act == "start_test") {
            $chinfo = $result = NULL;
            $save = ($_GET['test_case'] == "test_1");
            list($chinfo, $result) = $this->_doc_test_do();
            echo "<div class='tBd'><h1>测试返回结果</h1>";
            echo var_export($result ? $result : "空");
            echo "</div><br/>";
            echo "<div class='tBd'> <h1>测试返回状态</h1>";
            echo var_export($chinfo);
            echo "</div>";
        }
    }


    /**
     * @param $list_id
     * @param $test_case
     * @internal param $url
     * @internal param $datas
     * @internal param string $test_index
     * @internal param bool $save
     * @return array
     * 执行测试，返回接口返回的头信息和执行结果
     */
    //function _doc_test_do($url,$datas,$list_id,$test_index='',$save=false){
    function _doc_test_do()
    {
        $list_id = $_GET['list_id'];
        $test_case = $_GET['test_case'];

        $conn = _ocilogon($this->db);
        $sql = "select l.* from {$this->report_doc_list} l where l.list_id=:list_id";
        $stmt = _ociparse($conn, $sql);
        _ocibindbyname($stmt, 'list_id', $list_id);
        $errorinfo = _ociexecute($stmt);
        $row = null;
        ocifetchinto($stmt, $row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $test_data = unserialize($row[strtoupper($test_case)]);
        $url = $row['LIST_NAME'];
        $test_index = $test_data['test_index'];
        $datas = $test_data['datas'];
        foreach ($datas as $item_name => $data) {
            if ($data['item_value'] !== '') {
                if ($data['coding_type'] == "utf-8") {
                    $data['item_value'] = iconv('gbk', 'utf-8', $data['item_value']);
                }
                if ($data['data_type'] == 'get') {
                    $get_datas[$item_name] = $data['item_value'];
                } else {
                    $post_datas[$item_name] = $data['item_value'];
                }
            }
        }
        if (strpos($url, "?")) {
            $url .= "&" . http_build_query($get_datas);
        } else {
            $url .= "?" . http_build_query($get_datas);
        }

        $chinfo = array();
        $result = _curl($chinfo, $url, $post_datas);
        //测试，并记录
        include_once "header.php";
        ini_set("display_errors", true);
        if ($result != '' && ($test_index == '' || strpos($result, $test_index))) {
            _status(1, VHOST . "(接口测试)", "成功", "($test_case)" . $url, var_export($post_datas, true));
            //echo "\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n";

            //更新数据库中的测试结果
            if (strtoupper($test_case) == "TEST_1") {
                $conn = _ocilogon($this->db);
                $sql = "update {$this->report_doc_list} set format=:format where list_id=:list_id";
                $stmt = _ociparse($conn, $sql);
                ocibindbyname($stmt, ":format", $result);
                ocibindbyname($stmt, ":list_id", $list_id);
                _ociexecute($stmt);
            }
        } else {
            _status(1, VHOST . "(接口测试)", "失败", "($test_case)" . $url, var_export($post_datas, true));
        }
        return array($chinfo, $result);
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
            $sql = "update {$this->report_doc_detail}  set detail_name=:detail_name,data_type=:data_type,encoding_type=:encoding_type,add_date=sysdate,doc_op=:doc_op, department=:department,note=:note where detail_id=:detail_id   ";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':detail_id', $_POST['detail_id']);
            ocibindbyname($stmt, ':detail_name', trim($_POST['detail_name']));
            ocibindbyname($stmt, ':data_type', trim($_POST['data_type']));
            ocibindbyname($stmt, ':encoding_type', trim($_POST['encoding_type']));
            ocibindbyname($stmt, ':doc_op', trim($_POST['doc_op']));
            ocibindbyname($stmt, ':department', trim($_POST['department']));
            ocibindbyname($stmt, ':note', trim($_POST['note']));
            $oicerror = _ociexecute($stmt);
        } else {
            $sql = "insert into {$this->report_doc_detail} (list_id,detail_id,detail_name,data_type,encoding_type,doc_op,detail_type,department,note)
                values (:list_id,seq_{$this->report_doc}.nextval,:detail_name,:data_type,:encoding_type,:doc_op,:detail_type,:department,:note)";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':list_id', $_POST['list_id']);
            ocibindbyname($stmt, ':detail_name', $_POST['detail_name']);
            ocibindbyname($stmt, ':data_type', trim($_POST['data_type']));
            ocibindbyname($stmt, ':encoding_type', trim($_POST['encoding_type']));
            ocibindbyname($stmt, ':doc_op', $_POST['doc_op']);
            $edit_type = 'item';
            if (isset($_GET['edit_type']) && $_GET['edit_type'] == 'contract') {
                $edit_type = 'contract';
            }
            ocibindbyname($stmt, ':detail_type', $edit_type);
            ocibindbyname($stmt, ':department', trim($_POST['department']));
            ocibindbyname($stmt, ':note', $_POST['note']);
            $oicerror = _ociexecute($stmt);
            if ($oicerror) die(json_encode(array(
                'code' => -2,
                'msg' => 'db error:' . var_export($oicerror, true)
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
            $this->doc_detail_up($up, $a, $list_id);
            $this->doc_detail_up($down, $up, $list_id);
            $this->doc_detail_up($a, $down, $list_id);
        }
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc 删除文档
     * @author 李鑫辉 mailto:lixinhui@ppstream.com
     * @since  2013-05-21 16:00:00
     * @throws
     */
    function doc_del_ajax()
    {
        $doc_id = intval($_POST['doc_id']);
        $conn_db = _ocilogon($this->db);

        if ($doc_id < 0) {
            exit(json_encode(array('status' => -1000, 'msg' => '参数错误！')));
        }

        $sql = "delete from {$this->report_doc_list} where doc_id = :doc_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':doc_id', $doc_id);
        $oicerror = _ociexecute($stmt);

        if ($oicerror) {
            exit(json_encode(array('status' => -1001, 'msg' => $oicerror['message'])));
        }

        $sql = "delete from {$this->report_doc} where doc_id = :doc_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':doc_id', $doc_id);
        $oicerror = _ociexecute($stmt);

        if ($oicerror) {
            exit(json_encode(array('status' => -1002, 'msg' => $oicerror['message'])));
        }

        exit(json_encode(array('status' => 0, 'msg' => '操作成功！')));
    }

    /**
     * @desc 显示配置中心
     * @author 李鑫辉 mailto:lixinhui@ppstream.com
     * @since  2013-05-08 16:00:00
     * @throws
     */
    function doc_config()
    {
        $doc_id = intval($_GET['doc_id']);
        $conn_db = _ocilogon($this->db);

        $sql = "select * from {$this->report_doc} where doc_id = :doc_id";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':doc_id', $doc_id);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        if (empty($_row)) {
            exit('Doc not existed. [' . $doc_id . ']');
        }

        $sql = "select * from {$this->report_doc} where group_name = :group_name and group_name_2 = :group_name_2
        order by doc_name ";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':group_name', $_row['GROUP_NAME']);
        ocibindbyname($stmt, ':group_name_2', $_row['GROUP_NAME_2']);
        $oicerror = _ociexecute($stmt);
        $this->all_name = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $this->all_name[] = $_row;
        }

        $group_name = $_row['GROUP_NAME'];
        $group_name_1 = $_row['GROUP_NAME_1'];
        $group_name_2 = $_row['GROUP_NAME_2'];
        include 'project/doc_config.html';
    }

    /**
     * @desc 配置中心
     * @author 李鑫辉 mailto:lixinhui@ppstream.com
     * @since  2013-05-08 18:00:00
     * @throws
     */
    function doc_config_do()
    {
        $group_name = trim($_POST['group_name']);
        $group_name_1 = trim($_POST['group_name_1']);
        $group_name_2 = trim($_POST['group_name_2']);
        $doc_id = intval($_POST['doc_id']);
        $conn_db = _ocilogon($this->db);

        $sql = "select * from {$this->report_doc} t where doc_id = :doc_id ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':doc_id', $doc_id);
        $ocierror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

        $sql = "update {$this->report_doc} set group_name=:group_name,group_name_1=:group_name_1,group_name_2=:group_name_2 where doc_id = :doc_id ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':doc_id', $doc_id);
        _ocibindbyname($stmt, ':group_name', $group_name);
        _ocibindbyname($stmt, ':group_name_1', $group_name_1);
        _ocibindbyname($stmt, ':group_name_2', $group_name_2);
        $ocierror = _ociexecute($stmt);
        print_r($ocierror);

        // 直接联动修改分组名称, group_name_1
        if ($_POST['show_group'] && $group_name_1 <> $_row['GROUP_NAME_1']) {
            $sql = "update {$this->report_doc} t set group_name_1=:group_name_1
        where group_name=:group_name_old and  group_name_1=:group_name_1_old and group_name_2=:group_name_2_old";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':group_name_old', $_row['GROUP_NAME']);
            _ocibindbyname($stmt, ':group_name_1', $group_name_1);
            _ocibindbyname($stmt, ':group_name_1_old', $_row['GROUP_NAME_1']);
            _ocibindbyname($stmt, ':group_name_2_old', $_row['GROUP_NAME_2']);
            $ocierror = _ociexecute($stmt);
            print_r($ocierror);
        }

        // 直接联动修改分组名称, group_name_2
        if ($_POST['show_group_2'] && $group_name_2 <> $_row['GROUP_NAME_2']) {
            $sql = "update {$this->report_doc} t set  group_name_2=:group_name_2
        where group_name=:group_name_old and group_name_1=:group_name_1_old  and group_name_2=:group_name_2_old ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':group_name_old', $_row['GROUP_NAME']);
            _ocibindbyname($stmt, ':group_name_1_old', $_row['GROUP_NAME_1']);
            _ocibindbyname($stmt, ':group_name_2', $group_name_2);
            _ocibindbyname($stmt, ':group_name_2_old', $_row['GROUP_NAME_2']);
            $ocierror = _ociexecute($stmt);
            print_r($ocierror);
        }

        // 直接联动修改分组名称, $group_name
        if ($_POST['show_group_3'] && $group_name <> $_row['GROUP_NAME']) {
            $sql = "update {$this->report_doc} t set group_name=:group_name
        where group_name=:group_name_old and group_name_1=:group_name_1_old  and group_name_2=:group_name_2_old ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':group_name', $group_name);
            _ocibindbyname($stmt, ':group_name_old', $_row['GROUP_NAME']);
            _ocibindbyname($stmt, ':group_name_1_old', $_row['GROUP_NAME_1']);
            _ocibindbyname($stmt, ':group_name_2_old', $_row['GROUP_NAME_2']);
            $ocierror = _ociexecute($stmt);
            print_r($ocierror);
        }

        header("location: {$_SERVER['HTTP_REFERER']}");
    }


    /**
     * @desc 对doc文档下的项目进行交换顺序
     * @author 黄世密 mailto:hsms@dev.ppstream.com
     * @since  2012-11-16 11:00:00
     * @throws
     */
    function doc_detail_up($detail_id, $id, $list_id)
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

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-05-28 15:05:40
     * @throws 注意:无DB异常处理
     */
    function doc_sh()
    {
        $data = NULL;
        $conn = _ocilogon($this->db);
        $sql = "select l.list_id,l.list_name,l.return_type,l.test_1,l.test_2,l.test_3 from {$this->report_doc_list} l  ";
        $stmt = _ociparse($conn, $sql);
        $errorinfo = _ociexecute($stmt);
        $row = null;
        while (ocifetchinto($stmt, $row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $test_case_1 = unserialize($row["TEST_1"]);
            $test_case_2 = unserialize($row["TEST_2"]);
            $test_case_3 = unserialize($row["TEST_3"]);
            //执行1，若成功保存结果
            if ($test_case_1['auto_run'] == 'run') {
                $data .= "callact \"php project.php act=_doc_test_do list_id={$row['LIST_ID']} test_case=test_1  pwd=\$pwd\"\n";
            }
            //执行2
            if ($test_case_2['auto_run'] == 'run') {
                $data .= "callact \"php project.php act=_doc_test_do list_id={$row['LIST_ID']} test_case=test_2   pwd=\$pwd\"\n";
            }
            //执行3
            if ($test_case_3['auto_run'] == 'run') {
                $data .= "callact \"php project.php act=_doc_test_do list_id={$row['LIST_ID']} test_case=test_3  pwd=\$pwd\"\n";
            }
        }
        chmod('crontab/monitorphp_doc.sh', 0755);
        _file_put_contents('crontab/monitorphp_doc.sh', str_replace("\r", '', $data));
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-05-29 10:05:19
     * @throws 注意:无DB异常处理
     */
    function doc_download()
    {
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename="' . basename($_GET['doc_name']) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        readfile("project/doc/" . basename($_GET['doc_name']));
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-08-30 09:48:59
     * @throws 注意:无DB异常处理
     */
    function index()
    {
        include "project/doc.html";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-04-27 11:39:02
     * @throws 注意:无DB异常处理
     */
    function vtip()
    {
        include "project/_vtip.html";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-04-27 11:46:17
     * @throws 注意:无DB异常处理
     */
    function vtip_do()
    {
        ini_set("display_errors", true);
        $conn_db = _ocilogon($this->db);
        $stmt = _ociparse($conn_db, "select * from {$this->report_monitor_v1} where v1=:v1 ");
        ocibindbyname($stmt, ':v1', $_POST['v1']);
        _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        if (!$_row) {
            $stmt = _ociparse($conn_db, "insert into  {$this->report_monitor_v1} (ID,v1) values (seq_{$this->report_monitor}.nextval,:v1) ");
            ocibindbyname($stmt, ':v1', $_POST['v1']);
            _ociexecute($stmt);
        }
        $stmt = _ociparse($conn_db, "select * from {$this->report_monitor_config} where v1=:v1 and v2=:v2 ");
        ocibindbyname($stmt, ':v1', $_POST['v1']);
        ocibindbyname($stmt, ':v2', $_POST['v2']);
        _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        if (!$_row) {
            $stmt = _ociparse($conn_db, "insert into  {$this->report_monitor_config} (id,v1,v2) values (seq_{$this->report_monitor}.nextval,:v1,:v2) ");
            ocibindbyname($stmt, ':v1', $_POST['v1']);
            ocibindbyname($stmt, ':v2', $_POST['v2']);
            _ociexecute($stmt);
        }
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc 查看某个ipcs里面的数据,把第一行数据读取出来,然后写回去
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-04-02 10:07:09
     * @throws 有/无DB异常处理
     */
    function ipcs_view()
    {
        if (!$_GET['key'])
            die("缺少参数:key=?\n");
        $MSGKey = $_GET['key'];
        $seg = msg_get_queue($MSGKey, 0600);
        $msgtype = 1;
        $msg_array = array();
        //读取第一条队列
        msg_receive($seg, $msgtype, $msgtype, 1024 * 1024 * 10, $msg_array, true, MSG_IPC_NOWAIT);
        print_r($msg_array);
        print_r("<br>\n");
        //写回去队列
        msg_send($seg, 1, $msg_array, true, false);
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-03-21 14:59:37
     * @throws 注意:无DB异常处理
     */
    function ipcs_move()
    {
        if (!$_GET['to'] || !$_GET['from'])
            die("缺少参数:to=? fomr=? \n");
        $seg = msg_get_queue($_GET['from'], 0600);
        $segto = msg_get_queue($_GET['to'], 0600);
        $msgtype = 1;
        $msg_array = array();
        //读取第一条队列
        $i = 0;
        while (msg_receive($seg, $msgtype, $msgtype, 1024 * 1024 * 10, $msg_array, true, MSG_IPC_NOWAIT)) {
            $i++;
            //写回去队列
            msg_send($segto, 1, $msg_array, true, false);
        }
        msg_remove_queue($seg);
        var_dump($i);
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }


    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-07-22 15:25:41
     * @throws 注意:无DB异常处理
     */
    function monitor_config()
    {
        set_time_limit(0);
        ini_set("display_errors", true);
        echo "<pre>";
        $conn_db = _ocilogon($this->db);
        if (!$conn_db) return;
        //书面文档.在project/doc/目录下面
        $dir = "project/doc/";
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($o_file = $file = readdir($dh)) !== false) {
                    $date_time = date('Y-m-d H:i:s', filectime($dir . $o_file));
                    if ($file == '.' || $file == '..') continue;
                    $file = "DOC:{$file}";
                    var_dump($file);
                    $sql = "select * from {$this->report_doc_list} t where  list_name like :list_name    ";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':list_name', $file);
                    $ocierror = _ociexecute($stmt);
                    print_r($ocierror);
                    $_row = array();
                    ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                    if (!$_row) {
                        //补全文档的数据库设计说明
                        $sql = "select doc_id  from  {$this->report_doc} t  where
         group_name_1=:group_name_1 and group_name_2=:group_name_2 and group_name=:group_name and doc_name=:doc_name";
                        $stmt = _ociparse($conn_db, $sql);
                        _ocibindbyname($stmt, ':group_name_1', strval('项目文档'));
                        _ocibindbyname($stmt, ':group_name_2', strval('A.架构'));
                        _ocibindbyname($stmt, ':group_name', strval('3.接口文档'));
                        _ocibindbyname($stmt, ':doc_name', strval('接口文档'));
                        $ocierror = _ociexecute($stmt);
                        print_r($ocierror);
                        $_row_list_doc = array();
                        ocifetchinto($stmt, $_row_list_doc, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                        if (!$_row_list_doc) {
                            $sql = "insert into {$this->report_doc} t (doc_id,doc_name,add_time,GROUP_NAME_1,GROUP_NAME_2,GROUP_NAME) values
        (SEQ_{$this->report_doc}.nextval,'接口文档',sysdate,'项目文档','A.架构','3.接口文档')  ";
                            $stmt = _ociparse($conn_db, $sql);
                            $ocierror = _ociexecute($stmt);
                            print_r($ocierror);
                            $stmt = _ociparse($conn_db, "select SEQ_{$this->report_doc}.currval doc_id from dual  ");
                            $ocierror = _ociexecute($stmt);
                            print_r($ocierror);
                            ocifetchinto($stmt, $_row_list_doc, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

                        }
                        $sql = "insert into {$this->report_doc_list} t (LIST_ID,DOC_ID,LIST_NAME,DES) values
                (seq_{$this->report_doc}.nextval,:DOC_ID,:LIST_NAME,:DES) ";
                        $stmt = _ociparse($conn_db, $sql);
                        _ocibindbyname($stmt, ':DOC_ID', $_row_list_doc['DOC_ID']);
                        _ocibindbyname($stmt, ':LIST_NAME', $file);
                        _ocibindbyname($stmt, ':DES', strval("\nDownload:<a href='?act=doc_download&doc_name=" . urlencode($o_file) . "' class='blue' target='_blank'>{$o_file}</a>.\n最新更新时间:" . $date_time));
                        $ocierror = _ociexecute($stmt);
                        print_r($ocierror);
                        $ocierror = _ociexecute(_ociparse($conn_db, "select  seq_{$this->report_doc}.currval list_id from dual "));
                        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                    }
                    //记录最新更新时间
                    $sql = "select * from {$this->report_doc_detail} t where list_id=:list_id and detail_name=:detail_name ";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':list_id', $_row['LIST_ID']);
                    _ocibindbyname($stmt, ':detail_name', strval('文档更新时间:' . $date_time));
                    $ocierror = _ociexecute($stmt);
                    $_row_detail = array();
                    ocifetchinto($stmt, $_row_detail, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                    if (!$_row_detail) {
                        $sql = "insert into {$this->report_doc_detail} (list_id,detail_id,detail_name,doc_op) values
                        (:list_id,seq_{$this->report_doc}.nextval,:detail_name,'必选') ";
                        $stmt = _ociparse($conn_db, $sql);
                        _ocibindbyname($stmt, ':list_id', $_row['LIST_ID']);
                        _ocibindbyname($stmt, ':detail_name', strval('文档更新时间:' . $date_time));
                        $ocierror = _ociexecute($stmt);

                        $sql = "update {$this->report_doc_list} t set des=:des where list_id=:list_id ";
                        $stmt = _ociparse($conn_db, $sql);
                        _ocibindbyname($stmt, ':list_id', $_row['LIST_ID']);
                        _ocibindbyname($stmt, ':DES', strval("\nDownload:<a href='?act=doc_download&doc_name=" . urlencode($o_file) . "' class='blue' target='_blank'>{$o_file}</a>.\n最新更新时间:" . $date_time));
                        $ocierror = _ociexecute($stmt);
                    }
                }
                closedir($dh);
            }
        }

        //接口文档
        foreach (array(
                     '(功能执行)' => 'A.接口') as $dbkey => $dbvalue) {
            $sql = "select 'http://' || substr(v1,0,instr(v1,'(')-1) || v3 as v3   from {$this->report_monitor_hour} t where t.cal_date > sysdate - 1 / 24   and instr(v3, '/') = 1  and instr(v3,'?')>0 and instr(v3,'project.php') = 0   and v1 like :v1 ";
            $stmt_list = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt_list, ':v1', strval('%' . $dbkey));
            $ocierror = _ociexecute($stmt_list);
            print_r($ocierror);
            $_row_list = array();
            while (ocifetchinto($stmt_list, $_row_list, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                //为空,或者是项目点配置表,直接跳过
                if (!$_row_list['V3']) continue;
                $_row_list['V3'] = "{$_row_list['V3']}";
                $sql = "select * from {$this->report_doc_list} t where  list_name like :list_name    ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':list_name', strval($_row_list['V3'] . '%'));
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
                $_row = array();
                ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                if (!$_row) {
                    //补全文档的数据库设计说明
                    $sql = "select doc_id  from  {$this->report_doc} t  where
         group_name_1=:group_name_1 and group_name_2=:group_name_2 and group_name=:group_name and doc_name=:doc_name";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':group_name_1', strval('项目文档'));
                    _ocibindbyname($stmt, ':group_name_2', strval('A.架构'));
                    _ocibindbyname($stmt, ':group_name', strval('2.接口服务'));
                    _ocibindbyname($stmt, ':doc_name', $dbvalue);
                    $ocierror = _ociexecute($stmt);
                    print_r($ocierror);
                    $_row_list_doc = array();
                    ocifetchinto($stmt, $_row_list_doc, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                    if (!$_row_list_doc) {
                        $sql = "insert into {$this->report_doc} t (doc_id,doc_name,add_time,GROUP_NAME_1,GROUP_NAME_2,GROUP_NAME) values
        (SEQ_{$this->report_doc}.nextval,'{$dbvalue}',sysdate,'项目文档','A.架构','2.接口服务')  ";
                        $stmt = _ociparse($conn_db, $sql);
                        $ocierror = _ociexecute($stmt);
                        print_r($ocierror);
                        $stmt = _ociparse($conn_db, "select SEQ_{$this->report_doc}.currval doc_id from dual  ");
                        $ocierror = _ociexecute($stmt);
                        print_r($ocierror);
                        ocifetchinto($stmt, $_row_list_doc, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                    }

                    $sql = "insert into {$this->report_doc_list} t (LIST_ID,DOC_ID,LIST_NAME) values
                (seq_{$this->report_doc}.nextval,:DOC_ID,:LIST_NAME) ";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':DOC_ID', $_row_list_doc['DOC_ID']);
                    _ocibindbyname($stmt, ':LIST_NAME', $_row_list['V3']);
                    $ocierror = _ociexecute($stmt);
                    print_r($ocierror);
                }
            }
        }

        //数据库设计文档
        $class_var = get_class_vars(__CLASS__);
        foreach ($class_var as $k => $v) {
            if (strpos($k, 'report') === false) {
                unset($class_var[$k]);
            }
            $class_var[$k] = trim($v);
        }
        foreach (array(
                     '(MySQL统计)' => 'A.Mysql',
                     '(SQL统计)' => 'B.Oracle') as $dbkey => $dbvalue) {

            $sql = "select distinct CASE when instr(v3, '@') > 0 then  substr(v3, 0, instr(v3, '@') - 1)  else   v3  END as v3
        from {$this->report_monitor_hour} t where t.cal_date > sysdate - 1 / 24 and substr(v3, 0, instr(v3, '@') - 1) <> 'dual' and instr(v3,'webplsql.php')=0 and v1 like  :v1 ";
            $stmt_list = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt_list, ':v1', strval('%' . $dbkey));
            $ocierror = _ociexecute($stmt_list);
            print_r($ocierror);
            $_row_list = array();
            while (ocifetchinto($stmt_list, $_row_list, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                //为空,或者是项目点配置表,直接跳过
                if (!$_row_list['V3'] || in_array(trim(strtolower($_row_list['V3'])), array_change_key_case($class_var, CASE_LOWER))) continue;
                $_row_list['V3'] = "Table:{$_row_list['V3']}";
                $sql = "select * from {$this->report_doc_list} t where  list_name like :list_name    ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':list_name', strval($_row_list['V3'] . '%'));
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
                $_row = array();
                ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                if (!$_row) {
                    //补全文档的数据库设计说明
                    $sql = "select doc_id  from  {$this->report_doc} t  where
         group_name_1=:group_name_1 and group_name_2=:group_name_2 and group_name=:group_name and doc_name=:doc_name";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':group_name_1', strval('项目文档'));
                    _ocibindbyname($stmt, ':group_name_2', strval('A.架构'));
                    _ocibindbyname($stmt, ':group_name', strval('1.数据库'));
                    _ocibindbyname($stmt, ':doc_name', $dbvalue);
                    $ocierror = _ociexecute($stmt);
                    print_r($ocierror);
                    $_row_list_doc = array();
                    ocifetchinto($stmt, $_row_list_doc, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                    if (!$_row_list_doc) {
                        $sql = "insert into {$this->report_doc} t (doc_id,doc_name,add_time,GROUP_NAME_1,GROUP_NAME_2,GROUP_NAME) values
        (SEQ_{$this->report_doc}.nextval,'{$dbvalue}',sysdate,'项目文档','A.架构','1.数据库')  ";
                        $stmt = _ociparse($conn_db, $sql);
                        $ocierror = _ociexecute($stmt);
                        print_r($ocierror);
                        $stmt = _ociparse($conn_db, "select SEQ_{$this->report_doc}.currval doc_id from dual  ");
                        $ocierror = _ociexecute($stmt);
                        print_r($ocierror);
                        ocifetchinto($stmt, $_row_list_doc, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                    }

                    $sql = "insert into {$this->report_doc_list} t (LIST_ID,DOC_ID,LIST_NAME) values
                (seq_{$this->report_doc}.nextval,:DOC_ID,:LIST_NAME) ";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':DOC_ID', $_row_list_doc['DOC_ID']);
                    _ocibindbyname($stmt, ':LIST_NAME', $_row_list['V3']);
                    $ocierror = _ociexecute($stmt);
                    print_r($ocierror);
                }
            }
        }


        //每小时汇总[上小时+当前小时]
        $hourtime = strtotime(date('Y-m-d H:0:0') . " -1 hour");
        $endtime = time();
        if ($_GET['hour']) {
            $hourtime = strtotime($_GET['hour']);
            $endtime = strtotime("{$_GET['hour']} +1 day");
        }
        //所有配置信息 包含虚列
        $sql = "select * from  {$this->report_monitor_config} t  where id>0";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);
        $this->all_config = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
            $this->all_config[$_row['V1'] . $_row['V2']] = $_row;

        $addwhere = null;
        if ($_GET['v1'])
            $addwhere .= " and v1=:v1 ";
        if ($_GET['v2'])
            $addwhere .= " and v2=:v2 ";
        for ($it = $hourtime; $it <= $endtime; $it += 3600) {
            $hour = date('Y-m-d H:00:00', $it);
            echo "hour:{$hour}\n";
            //每小时数据汇总
            $sql = "select to_char(t.cal_date, 'yyyy-mm-dd hh24') cal_date, t.v1, decode(t.v2,null,'null',v2) v2,
                    decode(t.v3,null,'null',v3) v3, sum(fun_count) fun_count,avg(fun_count) fun_count_avg,max(nvl(v6,0)) DIFF_TIME
                    from {$this->report_monitor} t
                    where cal_date >= to_date(:hour,'yyyy-mm-dd hh24:mi:ss') and cal_date <to_date(:hour,'yyyy-mm-dd hh24:mi:ss')+1/24
                    {$addwhere}
                    group by t.v1, t.v2,t.v3, to_char(t.cal_date, 'yyyy-mm-dd hh24')  ";
            $stmt_list = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt_list, ':hour', $hour);
            if ($_GET['v1'])
                _ocibindbyname($stmt_list, ':v1', $_GET['v1']);
            if ($_GET['v2'])
                _ocibindbyname($stmt_list, ':v2', $_GET['v2']);
            $ocierror = _ociexecute($stmt_list);
            print_r($ocierror);
            $_row = array();

            while (ocifetchinto($stmt_list, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $_row2 = $this->all_config[$_row['V1'] . $_row['V2']];
                //正常情况下从原始表读取数据.如果是按照最后一分钟计算.走min表
                //虚列数据不进行计算
                if ($_row2['VIRTUAL_COLUMNS'] == 0) {
                    if ($_row2['MIN_COUNT_TYPE'] == 1) {
                        //最后一分钟的值
                        if ($_row2['HOUR_COUNT_TYPE'] == 1) {
                            $sql = "select to_char(max(cal_date),'yyyy-mm-dd hh24:mi:ss') cal_date from {$this->report_monitor_min} t
                        where  cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1/24  
                        and  v1=:v1 and v2=:v2 and v3=:v3 ";
                            $stmt = _ociparse($conn_db, $sql);
                            _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                            _ocibindbyname($stmt, ':v1', $_row['V1']);
                            _ocibindbyname($stmt, ':v2', $_row['V2']);
                            _ocibindbyname($stmt, ':v3', $_row['V3']);
                            $ocierror = _ociexecute($stmt);
                            print_r($ocierror);
                            $_row3 = array();
                            ocifetchinto($stmt, $_row3, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

                            $sql = "select sum(fun_count) fun_count from  {$this->report_monitor_min} t
                        where  cal_date=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') 
                        and  v1=:v1 and v2=:v2  and v3=:v3 ";
                            $stmt = _ociparse($conn_db, $sql);
                            _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                            _ocibindbyname($stmt, ':v1', $_row['V1']);
                            _ocibindbyname($stmt, ':v2', $_row['V2']);
                            _ocibindbyname($stmt, ':v3', $_row['V3']);
                            $ocierror = _ociexecute($stmt);
                            print_r($ocierror);
                            $_row4 = array();
                            ocifetchinto($stmt, $_row4, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                            $_row['FUN_COUNT'] = $_row4['FUN_COUNT'];
                        } elseif ($_row2['HOUR_COUNT_TYPE'] == 3) {
                            //当前小时的最高分钟值(有分钟统计)
                            $sql = "select to_char(cal_date,'yyyy-mm-dd hh24:mi:ss') cal_date,sum(fun_count) fun_count from {$this->report_monitor_min} t
                         where  cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1/24  and  v1=:v1 and v2=:v2
                         and v3=:v3 
                        group by v1,v2,v3, to_char(cal_date,'yyyy-mm-dd hh24:mi:ss') order by fun_count desc ";
                            $stmt = _ociparse($conn_db, $sql);
                            _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                            _ocibindbyname($stmt, ':v1', $_row['V1']);
                            _ocibindbyname($stmt, ':v2', $_row['V2']);
                            _ocibindbyname($stmt, ':v3', $_row['V3']);
                            $ocierror = _ociexecute($stmt);
                            print_r($ocierror);
                            $_row3 = array();
                            ocifetchinto($stmt, $_row3, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                            $_row['FUN_COUNT'] = $_row3['FUN_COUNT'];
                        } elseif ($_row2['HOUR_COUNT_TYPE'] == 4) {
                            //当前小时的最高分钟值(有分钟统计)
                            $sql = "select avg(fun_count) fun_count from {$this->report_monitor_min} t
                         where  cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1/24  and  v1=:v1 and v2=:v2
                         and v3=:v3 
                        group by v1,v2,v3  order by fun_count desc ";
                            $stmt = _ociparse($conn_db, $sql);
                            _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                            _ocibindbyname($stmt, ':v1', $_row['V1']);
                            _ocibindbyname($stmt, ':v2', $_row['V2']);
                            _ocibindbyname($stmt, ':v3', $_row['V3']);
                            $ocierror = _ociexecute($stmt);
                            print_r($ocierror);
                            $_row3 = array();
                            ocifetchinto($stmt, $_row3, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                            $_row['FUN_COUNT'] = $_row3['FUN_COUNT'];
                        } else {
                            //总和从分钟算.(如果有分钟数据,这样算才正确)
                            $sql = "select sum(fun_count) fun_count from  {$this->report_monitor_min} t
                            where  cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1/24  and  v1=:v1 and v2=:v2  ";
                            $stmt = _ociparse($conn_db, $sql);
                            _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                            _ocibindbyname($stmt, ':v1', $_row['V1']);
                            _ocibindbyname($stmt, ':v2', $_row['V2']);
                            $ocierror = _ociexecute($stmt);
                            print_r($ocierror);
                            $_row3 = array();
                            ocifetchinto($stmt, $_row3, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                            $_row['FUN_COUNT'] = $_row3['FUN_COUNT'];
                        }
                    } elseif ($_row2['HOUR_COUNT_TYPE'] == 4) {
                        //echo "avg:\n";
                        //print_r($_row);
                        $_row['FUN_COUNT'] = $_row['FUN_COUNT_AVG'];
                    }
                    $sql = "update {$this->report_monitor_hour} set fun_count=:fun_count,diff_time=:diff_time
                where v1=:v1 and v2=:v2 and v3=:v3  and  cal_date=to_date(:cal_date,'yyyy-mm-dd hh24') ";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':v1', $_row['V1']);
                    _ocibindbyname($stmt, ':v2', $_row['V2']);
                    _ocibindbyname($stmt, ':v3', $_row['V3']);
                    _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                    _ocibindbyname($stmt, ':fun_count', $_row['FUN_COUNT']);
                    _ocibindbyname($stmt, ':diff_time', $_row['DIFF_TIME']);
                    $ocierror = _ociexecute($stmt);
                    print_r($ocierror);
                    _status(1, VHOST . "(统计消耗)", 'monitor_hour(update)', $_row['V1'], $_row['V2'], VIP);
                    $ocirowcount = ocirowcount($stmt);
                    if ($ocirowcount < 1) {
                        $sql = "insert into {$this->report_monitor_hour} (cal_date,v1,v2,v3,fun_count,diff_time)
                    values (to_date(:cal_date,'yyyy-mm-dd hh24'),:v1,:v2,:v3,:fun_count,:diff_time) ";
                        $stmt = _ociparse($conn_db, $sql);
                        _ocibindbyname($stmt, ':v1', $_row['V1']);
                        _ocibindbyname($stmt, ':v2', $_row['V2']);
                        _ocibindbyname($stmt, ':v3', $_row['V3']);
                        _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                        _ocibindbyname($stmt, ':fun_count', $_row['FUN_COUNT']);
                        _ocibindbyname($stmt, ':diff_time', $_row['DIFF_TIME']);
                        $ocierror = _ociexecute($stmt);
                        print_r($ocierror);
                        if ($ocierror) {
                            $get_included_files = basename(array_shift(get_included_files()));
                            _status(1, VHOST . "(BUG错误)", 'SQL错误[项目]', $get_included_files . '/' . $_GET['act'], var_export($ocierror, true) . "|" . var_export($_row, true));
                        } else {
                            _status(1, VHOST . "(统计消耗)", 'hour', $_row['V1'], $_row['V2'], VIP);
                        }
                    }

                    //虚数列数据
                    $compare_group = array_filter(explode('|', '|' . $_row2['COMPARE_GROUP']));
                    if (count($compare_group) > 0) {
                        foreach ($compare_group as $v) {
                            $sql = "update {$this->report_monitor_hour} set fun_count=:fun_count,diff_time=:diff_time
                                        where v1=:v1 and v2=:v2 and v3=:v3  and  cal_date=to_date(:cal_date,'yyyy-mm-dd hh24') ";
                            $stmt = _ociparse($conn_db, $sql);
                            _ocibindbyname($stmt, ':v1', $v);
                            _ocibindbyname($stmt, ':v2', $_row['V1'] . '_' . $_row['V2']);
                            _ocibindbyname($stmt, ':v3', $_row['V3']);
                            _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                            _ocibindbyname($stmt, ':fun_count', $_row['FUN_COUNT']);
                            _ocibindbyname($stmt, ':diff_time', $_row['DIFF_TIME']);
                            $ocierror = _ociexecute($stmt);
                            print_r($ocierror);
                            _status(1, VHOST . "(统计消耗)", 'monitor_hour(update)', $_row['V1'], $_row['V2'], VIP);
                            $ocirowcount = ocirowcount($stmt);
                            if ($ocirowcount < 1) {
                                $sql = "insert into {$this->report_monitor_hour} (cal_date,v1,v2,v3,fun_count,diff_time)
                                            values (to_date(:cal_date,'yyyy-mm-dd hh24'),:v1,:v2,:v3,:fun_count,:diff_time) ";
                                $stmt = _ociparse($conn_db, $sql);
                                _ocibindbyname($stmt, ':v1', $v);
                                _ocibindbyname($stmt, ':v2', $_row['V1'] . '_' . $_row['V2']);
                                _ocibindbyname($stmt, ':v3', $_row['V3']);
                                _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                                _ocibindbyname($stmt, ':fun_count', $_row['FUN_COUNT']);
                                _ocibindbyname($stmt, ':diff_time', $_row['DIFF_TIME']);
                                $ocierror = _ociexecute($stmt);
                                print_r($ocierror);
                                if ($ocierror) {
                                    $get_included_files = basename(array_shift(get_included_files()));
                                    _status(1, VHOST . "(BUG错误)", 'SQL错误[项目]', $get_included_files . '/' . $_GET['act'], var_export($ocierror, true) . "|" . var_export($_row, true));
                                } else {
                                    _status(1, VHOST . "(统计消耗)", 'hour', $_row['V1'], $_row['V2'], VIP);
                                }
                            }
                        }
                    }
                }
            }
        }
        //刷新一天的数据
        $sql = "select to_char(t.cal_date, 'yyyy-mm-dd') cal_date, t.v1, decode(t.v2,null,'null',v2) v2,
                  sum(fun_count) fun_count,avg(fun_count) fun_count_avg from {$this->report_monitor_hour} t
                  where cal_date >= to_date(:m_date,'yyyy-mm-dd') and cal_date<to_date(:m_date,'yyyy-mm-dd')+1 {$addwhere}
                  group by t.v1, t.v2, to_char(t.cal_date, 'yyyy-mm-dd')";
        $stmt_list = _ociparse($conn_db, $sql);
        echo htmlspecialchars($sql);
        var_dump(date("Y-m-d", $hourtime));
        //print_r($_GET);
        _ocibindbyname($stmt_list, ':m_date', date("Y-m-d", $hourtime));
        if ($_GET['v1'])
            _ocibindbyname($stmt_list, ':v1', $_GET['v1']);
        if ($_GET['v2'])
            _ocibindbyname($stmt_list, ':v2', $_GET['v2']);
        $ocierror = _ociexecute($stmt_list);
        print_r($ocierror);
        $_row = array();
        while (ocifetchinto($stmt_list, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            //补全v1的信息
            $sql = "select * from {$this->report_monitor_v1} where v1=:v1  ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v1', $_row['V1']);
            $ocierror = _ociexecute($stmt);
            print_r($ocierror);
            $_row_config = array();
            ocifetchinto($stmt, $_row_config, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
            if (!$_row_config) {
                $sql = "insert into {$this->report_monitor_v1} (v1,id) values (:v1,seq_{$this->report_monitor}.nextval) ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1', $_row['V1']);
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
                _status(1, VHOST . "(统计消耗)", 'v1_config', $_row['V1'], NULL, VIP);
            }

            $_row_config = $this->all_config[$_row['V1'] . $_row['V2']];

            //如果是不累计的,重置总量为上个小时的总量
            if ($_row_config['DAY_COUNT_TYPE'] == 1 || $_row_config['DAY_COUNT_TYPE'] == 2 || $_row_config['DAY_COUNT_TYPE'] == 5 || $_row_config['DAY_COUNT_TYPE'] == 7) {
                //echo "只计算最后一小时\n";
                $sql2 = "select to_char(max(cal_date),'yyyy-mm-dd hh24:mi:ss') cal_date from
                {$this->report_monitor_hour} where cal_date>=to_date(:cal_date,'yyyy-mm-dd') 
                and  cal_date<to_date(:cal_date,'yyyy-mm-dd')+1 and v1=:v1 and v2=:v2 ";
                $stmt2 = _ociparse($conn_db, $sql2);
                _ocibindbyname($stmt2, ':v1', $_row['V1']);
                _ocibindbyname($stmt2, ':v2', $_row['V2']);
                _ocibindbyname($stmt2, ':cal_date', $_row['CAL_DATE']);
                $ocierror2 = _ociexecute($stmt2);
                print_r($ocierror2);
                $_row2 = array();
                ocifetchinto($stmt2, $_row2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                //print_r($_row2);
                $sql = "select  t.v1, t.v2,  sum(fun_count) fun_count,avg(fun_count) fun_count_avg
 			from  {$this->report_monitor_hour} t where cal_date=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')
                    and v1=:v1 and v2=:v2  group by t.v1, t.v2";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1', $_row['V1']);
                _ocibindbyname($stmt, ':v2', $_row['V2']);
                _ocibindbyname($stmt, ':cal_date', $_row2['CAL_DATE']);
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
                $_row2 = array();
                ocifetchinto($stmt, $_row2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                $_row['FUN_COUNT'] = $_row2['FUN_COUNT'];
                //v3个数
                if ($_row_config['DAY_COUNT_TYPE'] == 7) {
                    //echo "计算V3个数\n";
                    $sql = "select  count(distinct(t.v3)) num
 			from  {$this->report_monitor_hour} t where cal_date>=to_date(:cal_date,'yyyy-mm-dd')
                    and v1=:v1 and v2=:v2";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':v1', $_row['V1']);
                    _ocibindbyname($stmt, ':v2', $_row['V2']);
                    _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                    $ocierror = _ociexecute($stmt);
                    print_r($ocierror);
                    ocifetchinto($stmt, $_row2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                    $_row['FUN_COUNT'] = $_row2['NUM'];
                    //echo " num:{$_row['FUN_COUNT']} \n";
                }
                //最后一小时的平均值
                if ($_row_config['DAY_COUNT_TYPE'] == 5)
                    $_row['FUN_COUNT'] = $_row2['FUN_COUNT_AVG'];
            }
            //当天的平均数
            if ($_row_config['DAY_COUNT_TYPE'] == 6)
                $_row['FUN_COUNT'] = $_row['FUN_COUNT_AVG'];
            //print_r($_row);
            //echo " num:{$_row['FUN_COUNT']} \n";
            $sql = "update {$this->report_monitor_date} set fun_count=:fun_count
              where v1=:v1 and v2=:v2 and cal_date=to_date(:cal_date,'yyyy-mm-dd') ";
            $stmt2 = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt2, ':v1', $_row['V1']);
            _ocibindbyname($stmt2, ':v2', $_row['V2']);
            _ocibindbyname($stmt2, ':cal_date', $_row['CAL_DATE']);
            _ocibindbyname($stmt2, ':fun_count', $_row['FUN_COUNT']);
            $ocierror = _ociexecute($stmt2);
            print_r($ocierror);
            _status(1, VHOST . "(统计消耗)", 'monitor_date(update)', $_row['V1'], $_row['V2'], VIP);
            $_row_count = ocirowcount($stmt2);
            if (!$_row_count) {
                $sql = "insert into {$this->report_monitor_date} (cal_date,v1,v2,fun_count) values
                    (to_date(:cal_date,'yyyy-mm-dd'),:v1,:v2,:fun_count) ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1', $_row['V1']);
                _ocibindbyname($stmt, ':v2', $_row['V2']);
                _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                _ocibindbyname($stmt, ':fun_count', $_row['FUN_COUNT']);
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
                _status(1, VHOST . "(统计消耗)", 'date', $_row['V1'], $_row['V2'], VIP);
            }
            $compare_group = array_filter(explode('|', '|' . $_row_config['COMPARE_GROUP']));
            if (count($compare_group) > 0) {
                foreach ($compare_group as $v) {
                    $sql = "update {$this->report_monitor_date} set fun_count=:fun_count
                                  where v1=:v1 and v2=:v2 and cal_date=to_date(:cal_date,'yyyy-mm-dd') ";
                    $stmt2 = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt2, ':v1', $v);
                    _ocibindbyname($stmt2, ':v2', $_row['V1'] . '_' . $_row['V2']);
                    _ocibindbyname($stmt2, ':cal_date', $_row['CAL_DATE']);
                    _ocibindbyname($stmt2, ':fun_count', $_row['FUN_COUNT']);
                    $ocierror = _ociexecute($stmt2);
                    print_r($ocierror);
                    _status(1, VHOST . "(统计消耗)", 'monitor_date(update)', $_row['V1'], $_row['V2'], VIP);
                    $_row_count = ocirowcount($stmt2);
                    if (!$_row_count) {
                        $sql = "insert into {$this->report_monitor_date} (cal_date,v1,v2,fun_count) values
                    (to_date(:cal_date,'yyyy-mm-dd'),:v1,:v2,:fun_count) ";
                        $stmt = _ociparse($conn_db, $sql);
                        _ocibindbyname($stmt, ':v1', $v);
                        _ocibindbyname($stmt, ':v2', $_row['V1'] . '_' . $_row['V2']);
                        _ocibindbyname($stmt, ':cal_date', $_row['CAL_DATE']);
                        _ocibindbyname($stmt, ':fun_count', $_row['FUN_COUNT']);
                        $ocierror = _ociexecute($stmt);
                        print_r($ocierror);
                        _status(1, VHOST . "(统计消耗)", 'date', $_row['V1'], $_row['V2'], VIP);
                    }
                }
            }
            //
            if (!$_row_config) {
                $sql = "select count(*) c from {$this->report_monitor_config} where v1=:v1 ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1', $_row['V1']);
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
                $_row2 = array();
                ocifetchinto($stmt, $_row2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                $sql = "select * from {$this->report_monitor_v1} where  v1=:v1 ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1', $_row['V1']);
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
                $_row3 = array();
                ocifetchinto($stmt, $_row3, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

                $sql = "insert into  {$this->report_monitor_config} (v1,v2,orderby,id,day_count_type,hour_count_type,min_count_type,percent_count_type)
                values (:v1,:v2,:orderby,seq_{$this->report_monitor}.nextval,:day_count_type,:hour_count_type,:min_count_type,:percent_count_type) ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1', $_row['V1']);
                _ocibindbyname($stmt, ':v2', $_row['V2']);
                _ocibindbyname($stmt, ':day_count_type', intval($_row3['DAY_COUNT_TYPE']));
                _ocibindbyname($stmt, ':hour_count_type', intval($_row3['HOUR_COUNT_TYPE']));
                _ocibindbyname($stmt, ':min_count_type', intval($_row3['MIN_COUNT_TYPE']));
                _ocibindbyname($stmt, ':percent_count_type', intval($_row3['PERCENT_COUNT_TYPE']));

                if ($_row['V2'] == '汇总')
                    _ocibindbyname($stmt, ':orderby', intval(0));
                else
                    _ocibindbyname($stmt, ':orderby', max(1, $_row2['C'] + 1));
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
                _status(1, VHOST . "(统计消耗)", 'config', $_row['V1'], $_row['V2'], VIP);
            }
        }

        //清除过期数据
        if ($_GET['del']) {
            $sql = "delete from  {$this->report_monitor} where cal_date<=sysdate-10 ";
            $stmt_list = _ociparse($conn_db, $sql);
            $ocierror = _ociexecute($stmt_list);
            print_r($ocierror);
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-03-06 22:06:23
     * @throws 注意:无DB异常处理
     */
    function monitor_fix()
    {
        $IPCS = explode('|', $this->ipcs);
        shuffle($IPCS);
        print_r($IPCS);
        foreach ($IPCS as $ipcs) {
            $ic = $cs = 0;
            $seg = msg_get_queue($ipcs, 0600);
            $msgtype = 1;
            $msg_array = array();
            $monitor = array();
            //读取队列数据
            while (msg_receive($seg, $msgtype, $msgtype, 1024 * 1024 * 5, $msg_array, true, MSG_IPC_NOWAIT)) {
                $uniq_id = md5(date('Y-m-d H', strtotime($msg_array['time'])) . $msg_array['v1'] . $msg_array['v2'] . $msg_array['v3'] . $msg_array['v4'] . $msg_array['v5']);
                $monitor[date('Y-m-d H', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']][$msg_array['v4']][$msg_array['v5']]['uptype'] = $msg_array['uptype'];
                if ($msg_array['uptype'] == 'replace')
                    $monitor[date('Y-m-d H', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']][$msg_array['v4']][$msg_array['v5']]['count'] = $msg_array['num'];
                else
                    $monitor[date('Y-m-d H', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']][$msg_array['v4']][$msg_array['v5']]['count'] += $msg_array['num'];
                //最大耗时
                $monitor[date('Y-m-d H', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']][$msg_array['v4']][$msg_array['v5']]['diff_time'] = max($monitor[date('Y-m-d H', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']][$msg_array['v4']][$msg_array['v5']]['diff_time'], $msg_array['diff_time']);
                if ($ic++ > 10 * 10000)
                    break;
            }
            //压缩回去

            foreach ($monitor as $time => $vtype) {
                foreach ($vtype as $type => $vhost) {
                    foreach ($vhost as $host => $vact) {
                        foreach ($vact as $act => $vkey) {
                            foreach ($vkey as $key => $vhostip) {
                                foreach ($vhostip as $hostip => $v) {
                                    $cs++;
                                    _status($v['count'], $type, $host, $act, $key, $hostip, $v['diff_time'], $v['uptype'], strtotime($time . ":00:00"));
                                }
                            }
                        }
                    }
                }
            }
            _status(($cs / $ic) * 100, VHOST . '(队列服务)', '压缩比例', '压缩比例', date('Y-m-d H:i:s') . '_' . $ipcs, VIP);
            unset($monitor);
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc 处理页面访问队列
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-06-21 10:10:33
     * @throws 注意:无DB异常处理
     */
    function monitor()
    {
        ini_set("display_errors", true);
        $this->_ipcs();
        $conn_db = _ocilogon($this->db);
        if (!$conn_db) return;
        $get_included_files = basename(array_shift(get_included_files()));
        //查找需要精确到一分钟级别的类型
        $sql = "select * from {$this->report_monitor_config} where MIN_COUNT_TYPE='1' and id>0 ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);
        _ocilogoff($conn_db);
        $this->type_min = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
            $this->type_min[$_row['V1'] . $_row['V2']] = $_row;

        $tt1=microtime(true);
        echo "<pre> 准备压缩数据:\n";
        $minitor_count = $files = $monitor = $monitor_min = array();
        $IPCS = explode('|', $this->ipcs);
        shuffle($IPCS);
        $ic = 0;
        print_r($IPCS);
        $config_data = array();
        foreach ($IPCS as $ipcs) {
            $seg = msg_get_queue($ipcs, 0600);
            $msgtype = 1;
            $msg_array = array();
            //读取队列数据
            while (msg_receive($seg, $msgtype, $msgtype, 1024 * 1024 * 5, $msg_array, true, MSG_IPC_NOWAIT)) {
                if ($msg_array['v5'] == null)
                    $msg_array['v5'] = VIP;
                //专门对付SQL不规范的写法
                if (strpos($msg_array['v4'], 'SQL') !== false) {
                    $out = array();
                    preg_match('# in(\s+)?\(#is', $msg_array['v4'], $out);
                    if ($out)
                        $msg_array['v4'] = substr($msg_array['v4'], 0, strpos($msg_array['v4'], ' in')) . ' in....';
                }
                if (strpos($msg_array['v3'], 'SQL') !== false) {
                    preg_match('# in(\s+)?\(#is', $msg_array['v3'], $out);
                    if ($out)
                        $msg_array['v3'] = substr($msg_array['v3'], 0, strpos($msg_array['v3'], ' in')) . ' in....';
                }
                //
                foreach ((array)$msg_array['includes'] as $file)
                    $files[$msg_array['vhost']][$file] = $file;
                //查看命中了哪些监控
                $config_data[$msg_array['v1']][$msg_array['v2']]++;
                //日志数据,不会被删除
                if ($this->type_min[$msg_array['v1'] . $msg_array['v2']]) {
                    $monitor_min[date('Y-m-d H:i', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']]['count'] = $msg_array['num'];
                    //统计数据,会被清除掉
                }

                $monitor[date('Y-m-d H', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']][$msg_array['v4']][$msg_array['v5']]['uptype'] = $msg_array['uptype'];
                if ($msg_array['uptype'] == 'replace')
                    $monitor[date('Y-m-d H', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']][$msg_array['v4']][$msg_array['v5']]['count'] = $msg_array['num'];
                else
                    $monitor[date('Y-m-d H', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']][$msg_array['v4']][$msg_array['v5']]['count'] += $msg_array['num'];
                //最大耗时
                $monitor[date('Y-m-d H', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']][$msg_array['v4']][$msg_array['v5']]['diff_time'] = max($monitor[date('Y-m-d H', strtotime($msg_array['time']))][$msg_array['v1']][$msg_array['v2']][$msg_array['v3']][$msg_array['v4']][$msg_array['v5']]['diff_time'], $msg_array['diff_time']);
                $minitor_count[md5(date('Y-m-d H', strtotime($msg_array['time'])) . $msg_array['v1'] . $msg_array['v2'] . $msg_array['v3'] . $msg_array['v4'] . $msg_array['v5'])] = 1;

                if ($ic++ > 10 * 10000)
                    break;
            }
        }
        $diff_time = sprintf('%.5f', microtime(true) - $tt1);
        echo "\n从{$ic}个压缩到" . count($minitor_count) . "(耗时:{$diff_time})\n";
        echo "命中的类型:\n";
        print_r($config_data);
        echo "\n\n";
        $conn_db = _ocilogon($this->db);
        foreach ($monitor_min as $time => $vtype) {
            foreach ($vtype as $type => $vhost) {
                foreach ($vhost as $host => $vact) {
                    foreach ($vact as $act => $v) {
                        if (!$host)
                            $host = 'null';
                        if (strlen($act) > 200)
                            $act = substr($act, 0, 200);
                        //去掉回车
                        $act = strtr($act, array(
                            "\n" => null,
                            "\r" => null
                        ));
                        $sql = "update {$this->report_monitor_min} set fun_count=:fun_count  where md5=:md5 ";
                        $stmt = _ociparse($conn_db, $sql);
                        _ocibindbyname($stmt, ':md5', md5($time . $type . $host . $act));
                        _ocibindbyname($stmt, ':fun_count', $v['count']);
                        $ocierror = _ociexecute($stmt);
                        print_r($ocierror);
                        if ($ocierror)
                            _status(1, VHOST . "(BUG错误)", 'SQL错误[项目]', "{$get_included_files}/{$_GET['act']}", var_export(array(
                                    'cal_date' => $time,
                                    'v1' => $type,
                                    'v2' => $host,
                                    'v3' => $act,
                                    'fun_count' => $v['count'],
                                    'v6' => $v['diff_time']
                                ), true) . "|" . var_export($ocierror, true), VIP);
                        else
                            _status(1, VHOST . "(统计消耗)", 'monitor_min(update)', $type, $host, VIP);
                        $_row_count = ocirowcount($stmt);
                        if (!$_row_count) {
                            $sql = "insert into {$this->report_monitor_min} (id,v1,v2,v3,fun_count,cal_date,md5)
                            values(seq_{$this->report_monitor}.nextval,:v1,:v2,:v3,:fun_count,to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss'),:md5)";
                            $stmt = _ociparse($conn_db, $sql);
                            _ocibindbyname($stmt, ':md5', md5($time . $type . $host . $act));
                            _ocibindbyname($stmt, ':cal_date', $time);
                            _ocibindbyname($stmt, ':v1', $type);
                            _ocibindbyname($stmt, ':v2', $host);
                            _ocibindbyname($stmt, ':v3', $act);
                            _ocibindbyname($stmt, ':fun_count', $v['count']);
                            $ocierror = _ociexecute($stmt);
                            print_r($ocierror);
                            if ($ocierror)
                                _status(1, VHOST . "(BUG错误)", 'SQL错误[项目]', "{$get_included_files}/{$_GET['act']}", var_export(array(
                                        'cal_date' => $time,
                                        'v1' => $type,
                                        'v2' => $host,
                                        'v3' => $act,
                                        'fun_count' => $v['count'],
                                        'v6' => $v['diff_time']
                                    ), true) . "|" . var_export($ocierror, true), VIP);
                            else
                                _status(1, VHOST . "(统计消耗)", 'min', $type, $host, VIP);
                        }
                    }
                }
            }
        }
        //
        foreach ($monitor as $time => $vtype) {
            foreach ($vtype as $type => $vhost) {
                foreach ($vhost as $host => $vact) {
                    foreach ($vact as $act => $vkey) {
                        foreach ($vkey as $key => $vhostip) {
                            foreach ($vhostip as $hostip => $v) {
                                if (!$host)
                                    $host = 'null';
                                //截取4000字节
                                if (strlen($key) > 4000)
                                    $key = substr($key, 0, 4000);
                                if (strlen($hostip) > 200)
                                    $hostip = substr($hostip, 0, 200);
                                if (strlen($act) > 200)
                                    $act = substr($act, 0, 200);
                                //去掉回车
                                $act = strtr($act, array(
                                    "\n" => null,
                                    "\r" => null
                                ));
                                if ($v['uptype'] == 'replace')
                                    $sql = "update {$this->report_monitor} set fun_count=:fun_count,v6=:v6  where md5=:md5 ";
                                else
                                    $sql = "update {$this->report_monitor} set fun_count=fun_count+:fun_count,v6=:v6  where md5=:md5 ";
                                $stmt = _ociparse($conn_db, $sql);
                                _ocibindbyname($stmt, ':md5', md5($time . $type . $host . $act . $key . $hostip));
                                _ocibindbyname($stmt, ':fun_count', $v['count']);
                                _ocibindbyname($stmt, ':v6', $v['diff_time']);
                                $ocierror = _ociexecute($stmt);
                                print_r($ocierror);
                                if ($ocierror)
                                    _status(1, VHOST . "(BUG错误)", 'SQL错误[项目]', "{$get_included_files}/{$_GET['act']}", var_export(array(
                                            'cal_date' => $time,
                                            'v1' => $type,
                                            'v2' => $host,
                                            'v3' => $act,
                                            'v4' => $key,
                                            'v5' => $hostip,
                                            'fun_count' => $v['count'],
                                            'v6' => $v['diff_time']
                                        ), true) . "|" . var_export($ocierror, true), VIP);
                                else
                                    _status(1, VHOST . "(统计消耗)", 'monitor(update)', $type, $host, VIP);
                                $_row_count = ocirowcount($stmt);
                                if (!$_row_count) {
                                    $sql = "insert into {$this->report_monitor} (id,v1,v2,v3,v4,v5,fun_count,cal_date,v6,md5)
                                    values(seq_{$this->report_monitor}.nextval,:v1,:v2,:v3,:v4,:v5,:fun_count,to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss'),:v6,:md5)";
                                    $stmt = _ociparse($conn_db, $sql);
                                    _ocibindbyname($stmt, ':md5', md5($time . $type . $host . $act . $key . $hostip));
                                    _ocibindbyname($stmt, ':cal_date', $time);
                                    _ocibindbyname($stmt, ':v1', $type);
                                    _ocibindbyname($stmt, ':v2', $host);
                                    _ocibindbyname($stmt, ':v3', $act);
                                    _ocibindbyname($stmt, ':v4', $key);
                                    _ocibindbyname($stmt, ':v5', $hostip);
                                    _ocibindbyname($stmt, ':fun_count', $v['count']);
                                    _ocibindbyname($stmt, ':v6', $v['diff_time']);
                                    $ocierror = _ociexecute($stmt);
                                    print_r($ocierror);
                                    if ($ocierror)
                                        _status(1, VHOST . "(BUG错误)", 'SQL错误[项目]', "{$get_included_files}/{$_GET['act']}", var_export(array(
                                                'cal_date' => $time,
                                                'time' => date('Y-m-d H:i:s'),
                                                'md5' => md5($time . $type . $host . $act . $key . $hostip),
                                                'v1' => $type,
                                                'v2' => $host,
                                                'v3' => $act,
                                                'v4' => $key,
                                                'v5' => $hostip,
                                                'fun_count' => $v['count'],
                                                'v6' => $v['diff_time']
                                            ), true) . "|" . var_export($ocierror, true), VIP);
                                    else
                                        _status(1, VHOST . "(统计消耗)", 'monitor', $type, $host, VIP);
                                }
                            }
                        }
                    }
                }
            }
        }
        _ocilogoff($conn_db);
        //
        if (!is_writable('/dev/shm'))
            return;
        if (!file_exists($dir = '/dev/shm/' . VHOST . '/'))
            mkdir($dir);
        if (!file_exists($dir1 = '/dev/shm/xss_' . VHOST . '/'))
            mkdir($dir1);
        include "project/project_function.php";
        $project_function = new project_function;
        $check_files = array();
        if (date('H') > 8 && date('H') <= 19)
            $time_area = '白天';
        else
            $time_area = '晚上';
        //文件记录
        foreach ($files as $module_name => $_files) {
            foreach (array_unique($_files) as $file) {
                if (in_array($file, array(
                    '/home/httpd/shell.admin.c.pps.tv/crontab/ugc_id2type.php',
                    '/home/httpd/shell.admin.keyword.pps.tv/crontab/ugc_id2type.php',
                    '/home/httpd/shell.admin.keyword.pps.tv/crontab/ugc_id2type.php'
                ))
                )
                    continue;
                if (!is_file($file))
                    continue;
                //文件修改时间
                $newfile = $dir . md5($file);
                //
                if (is_file($newfile) && (filectime($newfile) < filectime($file))) {
                    echo "代码改动\n";
                    _status(1, $module_name . "(代码改动)", "文件改动-{$time_area}", $file, "", VIP, 0);
                    touch($newfile, filectime($file));
                } elseif (!is_file($newfile)) {
                    _status(1, $module_name . "(代码改动)", "新增文件-{$time_area}", $file, "", VIP, 0);
                    touch($newfile, filectime($file));
                }
                //安全校验
                $newfile = $dir1 . md5($file);
                if (is_file($newfile) && (filectime($newfile) < filectime($file)))
                    $check_files[$file] = $module_name;
                elseif (!is_file($newfile))
                    $check_files[$file] = $module_name;
            }
        }
        foreach ($check_files as $file => $module_name) {
            $token = token_get_all(file_get_contents($file));
            //代码所有人统计
            if (strpos($file, '/phpCas/') === false || strpos($file, '/PHPMailer/') === false) {
                $project_function->_function_author($token, $module_name, $file);
                $project_function->_function_count($token, $module_name, $file);
                $project_function->_xss($token, $module_name, $file);
                $project_function->_sign($token, $module_name, $file);
                $project_function->_disable_function($token, $module_name, $file);
            }
            touch($dir1 . md5($file), filectime($file));
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-12-10 09:56:06
     * @throws 注意:无DB异常处理
     */
    function report_sort()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "select * from {$this->report_monitor_config} t where v1=:v1 order by v2_group,decode(as_name,null,v2,as_name) asc ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        $ocierror = _ociexecute($stmt);
        $_row = array();
        $i = 0;
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $i++;
            $sql2 = "update {$this->report_monitor_config} set orderby=:orderby where id=:id ";
            $stmt2 = _ociparse($conn_db, $sql2);
            _ocibindbyname($stmt2, ':id', $_row['ID']);
            _ocibindbyname($stmt2, ':orderby', $i);
            $ocierror2 = _ociexecute($stmt2);
            $_row2 = array();
        }
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-12-11 15:26:37
     * @throws 注意:无DB异常处理
     */
    function report_avg()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "update {$this->report_monitor_v1} set show_avg=:show_avg where v1=:v1  ";
        $stmt = _ociparse($conn_db, $sql);
        $show_avg = 0;
        if ($_REQUEST['show_avg'] == 'true')
            $show_avg = 1;
        _ocibindbyname($stmt, ':show_avg', $show_avg);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        $ocierror = _ociexecute($stmt);
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc 统一修改计算类型
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-11 21:16:57
     * @throws 注意:无DB异常处理
     */
    function report_count_type()
    {
        $conn_db = _ocilogon($this->db);
        if ($_POST['notice']) {
            $this->notice_monitor_config($conn_db);
            return;
        }
        foreach ($_POST['uncount'] as $k => $v) {
            list($v1, $v2) = (explode('#@', $v));
            $_REQUEST['v1'] = $v1;
            $_REQUEST['v2'] = $v2;
            if ($_POST['all_delete']) {
                $this->_report_monitor_delete($conn_db);
            } else {
                $min_count_type_1 = 0;
                $where = array();
                if ($_POST['percent_count_type_1'] <> 'NULL')
                    $where[] = " percent_count_type=:percent_count_type ";
                if ($_POST['day_count_type_1'] <> 'NULL') {
                    $where[] = " day_count_type=:day_count_type ";
                    if (in_array($_POST['day_count_type_1'], array(
                        2,
                        3,
                        4
                    ))
                    ) {
                        $min_count_type_1 = 2;
                        $where[] = " min_count_type=1 ";
                    } elseif ($_POST['min_count_type_1'] <> 'NULL' && !$min_count_type_1) {
                        $min_count_type_1 = 1;
                        if ($_POST['min_count_type_1'] <> 'NULL')
                            $where[] = " min_count_type=:min_count_type ";
                    }
                }
                if ($_POST['hour_count_type_1'] <> 'NULL') {
                    $where[] = " hour_count_type=:hour_count_type ";
                    if (in_array($_POST['hour_count_type_1'], array(
                        1,
                        3,
                        4
                    ))
                    ) {
                        $min_count_type_1 = 2;
                        $where[] = " min_count_type=1 ";
                    } elseif ($_POST['min_count_type_1'] <> 'NULL' && !$min_count_type_1) {
                        $min_count_type_1 = 1;
                        if ($_POST['min_count_type_1'] <> 'NULL')
                            $where[] = " min_count_type=:min_count_type ";
                    }
                }
                if (!$min_count_type_1 && $_POST['min_count_type_1'] <> 'NULL') {
                    $min_count_type_1 = 1;
                    $where[] = " min_count_type=:min_count_type ";
                }
                if ($_POST['v2_compare'] <> 'NULL') {
                    $where[] = " v2_compare=:v2_compare";
                }
                if (!empty($where)) {
                    $where = join(',', $where);
                    $sql = "update {$this->report_monitor_config} set {$where} where v1=:v1 and v2=:v2 ";
                    $stmt = _ociparse($conn_db, $sql);
                    if ($_POST['percent_count_type_1'] <> 'NULL')
                        _ocibindbyname($stmt, ':percent_count_type', $_POST['percent_count_type_1']);
                    if ($_POST['day_count_type_1'] <> 'NULL')
                        _ocibindbyname($stmt, ':day_count_type', $_POST['day_count_type_1']);
                    if ($_POST['hour_count_type_1'] <> 'NULL')
                        _ocibindbyname($stmt, ':hour_count_type', $_POST['hour_count_type_1']);
                    if ($min_count_type_1 == 1)
                        if ($_POST['min_count_type_1'] <> 'NULL')
                            _ocibindbyname($stmt, ':min_count_type', $_POST['min_count_type_1']);
                    if ($_POST['v2_compare'] <> 'NULL')
                        _ocibindbyname($stmt, ':v2_compare', $_POST['v2_compare']);

                    _ocibindbyname($stmt, ':v1', $v1);
                    _ocibindbyname($stmt, ':v2', $v2);
                    $ocierror = _ociexecute($stmt);
                    print_r($ocierror);
                }
                //联动同名不同v1下面的v2
                if ($_POST['group_all']) {
                    $sql = "select * from  {$this->report_monitor_config}  where v1=:v1 and v2=:v2 ";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':v1', $v1);
                    _ocibindbyname($stmt, ':v2', $v2);
                    $ocierror = _ociexecute($stmt);
                    $_row = array();
                    ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

                    $sql = "update {$this->report_monitor_config} t set percent_count_type=:percent_count_type,day_count_type=:day_count_type,
                    hour_count_type=:hour_count_type,min_count_type=:min_count_type,orderby=:orderby,as_name=:as_name
                    where  v2=:v2";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':percent_count_type', $_row['PERCENT_COUNT_TYPE']);
                    _ocibindbyname($stmt, ':day_count_type', $_row['DAY_COUNT_TYPE']);
                    _ocibindbyname($stmt, ':hour_count_type', $_row['HOUR_COUNT_TYPE']);
                    _ocibindbyname($stmt, ':min_count_type', $_row['MIN_COUNT_TYPE']);
                    _ocibindbyname($stmt, ':as_name', $_row['AS_NAME']);
                    _ocibindbyname($stmt, ':orderby', $_row['ORDERBY']);
                    _ocibindbyname($stmt, ':v2', $v2);
                    $ocierror = _ociexecute($stmt);
                    print_r($ocierror);
                }
            }
        }
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-01 22:43:26
     * @throws 注意:无DB异常处理
     */
    function report_monitor_order()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "select * from {$this->report_monitor_config} order by v1, orderby,v2 ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);
        $this->all = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $this->all[$_row['V1']][] = $_row;
        }
        //排序更新初始化
        foreach ($this->all as $k => $v) {
            foreach ($v as $kk => $vv) {
                $sql = "update  {$this->report_monitor_config}  set orderby=:orderby where v1=:v1 and v2=:v2  ";
                $stmt = _ociparse($conn_db, $sql);
                //每次都独立提交,所以这样绑定(相同变量$k,$v)没问题
                _ocibindbyname($stmt, ':v1', $vv['V1']);
                _ocibindbyname($stmt, ':v2', $vv['V2']);
                _ocibindbyname($stmt, ':orderby', intval($kk + 1));
                $ocierror = _ociexecute($stmt);
            }
        }
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-12 21:39:01
     * @throws 注意:无DB异常处理
     */
    function report_monitor_order_top()
    {
        $conn_db = _ocilogon($this->db);
        if (!$_REQUEST['orderby'])
            $this->report_monitor_order();
        //上面的减下来
        $sql = "update  {$this->report_monitor_config} set orderby=orderby+1 where  v1=:v1 and orderby<:orderby  ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        _ocibindbyname($stmt, ':orderby', $_REQUEST['orderby']);
        $ocierror = _ociexecute($stmt);
        //本身上升
        $sql = "update  {$this->report_monitor_config} set orderby=1 where  v1=:v1 and v2=:v2 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        _ocibindbyname($stmt, ':v2', $_REQUEST['v2']);
        $ocierror = _ociexecute($stmt);
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-01 22:50:05
     * @throws 注意:无DB异常处理
     */
    function report_monitor_order_up()
    {
        $conn_db = _ocilogon($this->db);
        if (!$_REQUEST['orderby'])
            $this->report_monitor_order();
        //上面的减下来
        $sql = "update  {$this->report_monitor_config} set orderby=:orderby where v1=:v1 and   orderby=:orderby-1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        _ocibindbyname($stmt, ':orderby', $_REQUEST['orderby']);
        $ocierror = _ociexecute($stmt);
        //本身上升
        $sql = "update  {$this->report_monitor_config} set orderby=:orderby-1 where  v1=:v1 and v2=:v2 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        _ocibindbyname($stmt, ':v2', $_REQUEST['v2']);
        _ocibindbyname($stmt, ':orderby', $_REQUEST['orderby']);
        $ocierror = _ociexecute($stmt);
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-06-28 14:16:08
     * @throws 注意:无DB异常处理
     */
    function report_monitor_config()
    {
        $conn_db = _ocilogon($this->db);

        $sql = "select t.* from {$this->report_monitor_v1} t where v1=:v1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        $ocierror = _ociexecute($stmt);
        $this->row_config = array();
        ocifetchinto($stmt, $this->row_config, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

        $sql = "select t.*,decode(as_name,null,v1,as_name) as_name1 from {$this->report_monitor_v1} t
        order by decode(as_name,null,v1,as_name)  ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);
        $this->v1_config_group = $this->v1_config = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $v1_config_group[$_row['GROUP_NAME_1']][$_row['GROUP_NAME_2']][$_row['GROUP_NAME']][] = $_row;
            if ($_REQUEST['v1'] == $_row['V1'])
                $this->v1_config_act = $_row;
        }
        $this->v1_config = $v1_config_group[$this->row_config['GROUP_NAME_1']][$this->row_config['GROUP_NAME_2']][$this->row_config['GROUP_NAME']];
        $sql = "select * from {$this->report_monitor_config} where v1=:v1 order by orderby ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        $ocierror = _ociexecute($stmt);
        $this->all = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS))
            $this->all[] = $_row;
        include "project/report_monitor_config.html";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-26 13:21:42
     * @throws 注意:无DB异常处理
     */
    function report_monitor_as_name()
    {
        $_POST['as_name'] = mb_convert_encoding($_POST['as_name'], "GBK", "UTF-8");
        $conn_db = _ocilogon($this->db);

        $sql = "select * from {$this->report_monitor_config} where id=:id ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':id', $_POST['id']);
        $ocierror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

        $sql = "update {$this->report_monitor_config} set as_name=:as_name where v2=:v2  ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':as_name', $_POST['as_name']);
        _ocibindbyname($stmt, ':v2', $_row['V2']);
        $ocierror = _ociexecute($stmt);
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-26 13:21:42
     * @throws 注意:无DB异常处理
     */
    function report_monitor_v2_group()
    {
        $_POST['v2_group'] = mb_convert_encoding($_POST['v2_group'], "GBK", "UTF-8");
        $conn_db = _ocilogon($this->db);

        $sql = "select * from {$this->report_monitor_config} where id=:id ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':id', $_POST['id']);
        $ocierror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

        $sql = "update {$this->report_monitor_config} set v2_group=:v2_group where v2=:v2  ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v2_group', $_POST['v2_group']);
        _ocibindbyname($stmt, ':v2', $_row['V2']);
        $ocierror = _ociexecute($stmt);
    }

    /**
     * @desc 修改比较归类
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-26 13:21:42
     * @throws 注意:无DB异常处理
     */
    function report_monitor_compare_group()
    {
        $_POST['compare_group'] = mb_convert_encoding($_POST['compare_group'], "GBK", "UTF-8");
        $conn_db = _ocilogon($this->db);
        //config表
        $sql = "select * from {$this->report_monitor_config} where id=:id ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':id', $_POST['id']);
        $ocierror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        //v1表
        $sql = "select * from {$this->report_monitor_v1} where v1=:v1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_row['V1']);
        $ocierror = _ociexecute($stmt);
        $_row_v1 = array();
        ocifetchinto($stmt, $_row_v1, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $sql = "update {$this->report_monitor_config} set COMPARE_GROUP=:compare_group where v2=:v2  and v1=:v1";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':compare_group', $_POST['compare_group']);
        _ocibindbyname($stmt, ':v2', $_row['V2']);
        _ocibindbyname($stmt, ':v1', $_row['V1']);
        $ocierror = _ociexecute($stmt);

        //比较分类拆分 比较原先数据 插入或者删除虚列
        $arr_com = explode('|', $_row['COMPARE_GROUP']);
        $arr = explode('|', $_POST['compare_group']);
        $arr_add = array_diff($arr, $arr_com);
        $arr_del = array_diff($arr_com, $arr);
        //新增
        foreach ($arr_add as $v) {
            if ($v != '') {
                $sql = "insert into {$this->report_monitor_config}
                            (V1,V2,COUNT_TYPE,V3_LINK,V4_LINK,ORDERBY,PHONE,PHONE_ORDER,PHONE_ORDER_LESS,
                           ID,AS_NAME,DAY_COUNT_TYPE,HOUR_COUNT_TYPE,MIN_COUNT_TYPE,PERCENT_COUNT_TYPE,V2_GROUP,VIRTUAL_COLUMNS) values(:V1,:V2,:COUNT_TYPE,:V3_LINK,
                           :V4_LINK,:ORDERBY,:PHONE,:PHONE_ORDER,:PHONE_ORDER_LESS,
                           seq_{$this->report_monitor}.nextval,:AS_NAME,:DAY_COUNT_TYPE,:HOUR_COUNT_TYPE,:MIN_COUNT_TYPE,:PERCENT_COUNT_TYPE,:V2_GROUP,1)";
                $stmt = _ociparse($conn_db, $sql);
                $as_name = $_row['AS_NAME'] ? $_row['AS_NAME'] : $_row['V2'];
                _ocibindbyname($stmt, ':V1', $v);
                _ocibindbyname($stmt, ':V2', $_row['V1'] . '_' . $_row['V2']);
                _ocibindbyname($stmt, ':COUNT_TYPE', $_row['id']);
                _ocibindbyname($stmt, ':V3_LINK', $_row['V3_LINK']);
                _ocibindbyname($stmt, ':V4_LINK', $_row['V4_LINK']);
                _ocibindbyname($stmt, ':ORDERBY', $_row['ORDERBY']);
                _ocibindbyname($stmt, ':PHONE', $_row['PHONE']);
                _ocibindbyname($stmt, ':PHONE_ORDER', $_row['PHONE_ORDER']);
                _ocibindbyname($stmt, ':PHONE_ORDER_LESS', $_row['PHONE_ORDER_LESS']);
                _ocibindbyname($stmt, ':AS_NAME', $as_name);
                _ocibindbyname($stmt, ':DAY_COUNT_TYPE', $_row['DAY_COUNT_TYPE']);
                _ocibindbyname($stmt, ':HOUR_COUNT_TYPE', $_row['HOUR_COUNT_TYPE']);
                _ocibindbyname($stmt, ':MIN_COUNT_TYPE', $_row['MIN_COUNT_TYPE']);
                _ocibindbyname($stmt, ':PERCENT_COUNT_TYPE', $_row['PERCENT_COUNT_TYPE']);
                _ocibindbyname($stmt, ':V2_GROUP', $_row['V1']);
                $ocierror = _ociexecute($stmt);
                //插入v1表
                $sql = "insert into {$this->report_monitor_v1}
                            (V1,COUNT_TYPE,CHAR_TYPE,START_CLOCK,SHOW_TEMPLATE,SHOW_ALL,ID,DAY_COUNT_TYPE,HOUR_COUNT_TYPE,MIN_COUNT_TYPE,PERCENT_COUNT_TYPE,SHOW_AVG,IS_DUTY) 
                      values(:V1,:COUNT_TYPE,:CHAR_TYPE,:START_CLOCK,:SHOW_TEMPLATE,:SHOW_ALL,
                           seq_{$this->report_monitor}.nextval,:DAY_COUNT_TYPE,:HOUR_COUNT_TYPE,:MIN_COUNT_TYPE,:PERCENT_COUNT_TYPE,:SHOW_AVG,1)";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':V1', $v);
                _ocibindbyname($stmt, ':COUNT_TYPE', $_row_v1['COUNT_TYPE']);
                _ocibindbyname($stmt, ':CHAR_TYPE', $_row_v1['CHAR_TYPE']);
                _ocibindbyname($stmt, ':START_CLOCK', $_row_v1['START_CLOCK']);
                _ocibindbyname($stmt, ':SHOW_TEMPLATE', $_row_v1['SHOW_TEMPLATE']);
                _ocibindbyname($stmt, ':SHOW_ALL', $_row_v1['SHOW_ALL']);
                _ocibindbyname($stmt, ':DAY_COUNT_TYPE', $_row_v1['DAY_COUNT_TYPE']);
                _ocibindbyname($stmt, ':HOUR_COUNT_TYPE', $_row_v1['HOUR_COUNT_TYPE']);
                _ocibindbyname($stmt, ':MIN_COUNT_TYPE', $_row_v1['MIN_COUNT_TYPE']);
                _ocibindbyname($stmt, ':PERCENT_COUNT_TYPE', $_row_v1['PERCENT_COUNT_TYPE']);
                _ocibindbyname($stmt, ':SHOW_AVG', $_row_v1['SHOW_AVG']);
                $ocierror = _ociexecute($stmt);
                var_dump($ocierror);
            }

        }
        //删除
        foreach ($arr_del as $v) {
            if ($v != '') {
                $sql = "delete from {$this->report_monitor_config} where v1=:v1 and v2=:v2";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1', $v);
                _ocibindbyname($stmt, ':v2', $_row['V1'] . '_' . $_row['V2']);
                $ocierror = _ociexecute($stmt);

            }
        }
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-22 10:37:04
     * @throws 注意:无DB异常处理
     */
    function report_monitor_v1_do()
    {
        $conn_db = _ocilogon($this->db);
        //删除v1
        if ($_POST['delete_v1']) {
            $this->_report_monitor_delete($conn_db);
        } else {
            $sql = "select * from {$this->report_monitor_v1} t where v1=:v1 ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v1', $_GET['v1']);
            $ocierror = _ociexecute($stmt);
            $_row = array();
            ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

            $sql = "update {$this->report_monitor_v1} set as_name=:as_name,count_type=:count_type,char_type=:char_type,
        group_name=:group_name,group_name_1=:group_name_1,group_name_2=:group_name_2,start_clock=:start_clock,show_template=:show_template,show_all=1,
        percent_count_type=:percent_count_type,day_count_type=:day_count_type,hour_count_type=:hour_count_type,min_count_type=:min_count_type,
        duibi_name=:duibi_name,is_duty=:is_duty,pinfen_rule_name=:pinfen_rule_name
        where v1=:v1 ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v1', $_GET['v1']);
            _ocibindbyname($stmt, ':as_name', $_POST['as_name']);
            _ocibindbyname($stmt, ':count_type', $_POST['count_type']);
            _ocibindbyname($stmt, ':char_type', $_POST['char_type']);
            _ocibindbyname($stmt, ':group_name', $_POST['group_name']);
            _ocibindbyname($stmt, ':group_name_1', $_POST['group_name_1']);
            _ocibindbyname($stmt, ':group_name_2', $_POST['group_name_2']);
            _ocibindbyname($stmt, ':start_clock', $_POST['start_clock']);
            _ocibindbyname($stmt, ':show_template', $_POST['show_template']);
            _ocibindbyname($stmt, ':percent_count_type', $_POST['percent_count_type']);
            _ocibindbyname($stmt, ':day_count_type', $_POST['day_count_type']);
            _ocibindbyname($stmt, ':hour_count_type', $_POST['hour_count_type']);
            _ocibindbyname($stmt, ':min_count_type', $_POST['min_count_type']);
            _ocibindbyname($stmt, ':duibi_name', $_POST['duibi_name']);
            _ocibindbyname($stmt, ':is_duty', intval($_POST['is_duty']));
            _ocibindbyname($stmt, ':pinfen_rule_name', $_POST['pinfen_rule_name']);
            $ocierror = _ociexecute($stmt);
            print_r($ocierror);
            //排版统一
            if ($_POST['show_template_checkbox'] == 1) {
                $sql = "update {$this->report_monitor_v1} set show_template=:show_template  where group_name=:group_name ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':show_template', $_POST['show_template']);
                _ocibindbyname($stmt, ':group_name', $_POST['group_name']);
                $ocierror = _ociexecute($stmt);
            }
            foreach (array(
                         'percent_count_type',
                         'day_count_type',
                         'hour_count_type',
                         'min_count_type'
                     ) as $k => $v) {
                //统一同类型配置
                if ($_POST[$v] != 'NULL') {
                    $sql = "update {$this->report_monitor_config} set {$v}=:{$v}  where v1=:v1 ";
                    $stmt = _ociparse($conn_db, $sql);
                    _ocibindbyname($stmt, ':v1', $_GET['v1']);
                    _ocibindbyname($stmt, ":{$v}", $_POST[$v]);
                    $ocierror = _ociexecute($stmt);
                    print_r($ocierror);
                }
            }
            //直接联动修改分组名称
            if ($_POST['show_group'] && $_POST['group_name_1'] <> $_row['GROUP_NAME_1']) {
                $sql = "update {$this->report_monitor_v1} t set group_name_1=:group_name_1
            where   group_name=:group_name_old and  group_name_1=:group_name_1_old ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':group_name', $_POST['group_name']);
                _ocibindbyname($stmt, ':group_name_old', $_row['GROUP_NAME']);
                _ocibindbyname($stmt, ':group_name_1', $_POST['group_name_1']);
                _ocibindbyname($stmt, ':group_name_1_old', $_row['GROUP_NAME_1']);
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
            }

            //直接联动修改分组名称
            if ($_POST['show_group_2'] && $_POST['group_name_2'] <> $_row['GROUP_NAME_2']) {
                $sql = "update {$this->report_monitor_v1} t set  group_name_2=:group_name_2
            where group_name=:group_name_old and group_name_1=:group_name_1_old  and group_name_2=:group_name_2_old ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':group_name', $_POST['group_name']);
                _ocibindbyname($stmt, ':group_name_old', $_row['GROUP_NAME']);
                _ocibindbyname($stmt, ':group_name_1', $_POST['group_name_1']);
                _ocibindbyname($stmt, ':group_name_1_old', $_row['GROUP_NAME_1']);
                _ocibindbyname($stmt, ':group_name_2', $_POST['group_name_2']);
                _ocibindbyname($stmt, ':group_name_2_old', $_row['GROUP_NAME_2']);
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
            }

            //直接联动修改分组名称
            if ($_POST['show_group_3'] && $_POST['group_name'] <> $_row['GROUP_NAME']) {
                $sql = "update {$this->report_monitor_v1} t set group_name=:group_name
            where group_name=:group_name_old and group_name_1=:group_name_1_old  and group_name_2=:group_name_2_old ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':group_name', $_POST['group_name']);
                _ocibindbyname($stmt, ':group_name_old', $_row['GROUP_NAME']);
                _ocibindbyname($stmt, ':group_name_1', $_POST['group_name_1']);
                _ocibindbyname($stmt, ':group_name_1_old', $_row['GROUP_NAME_1']);
                _ocibindbyname($stmt, ':group_name_2', $_POST['group_name_2']);
                _ocibindbyname($stmt, ':group_name_2_old', $_row['GROUP_NAME_2']);
                $ocierror = _ociexecute($stmt);
                print_r($ocierror);
            }
        }
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-18 21:28:46
     * @throws 注意:无DB异常处理
     */
    function report_js()
    {
        header("Content-Type:text/javascript; charset=GBK");
        $conn_db = _ocilogon($this->db);
        $s1 = date('Y-m-d', strtotime('-3 day'));
        $s2 = date('Y-m-d', strtotime('+1 day'));
        if ($_REQUEST['s1'])
            $s1 = $_REQUEST['s1'];
        if ($_REQUEST['s2'] && strtotime($_REQUEST['s2']) < time())
            $s2 = $_REQUEST['s2'];
        //显示日志曲线图
        if ($_GET['v3'] == 'all') {
            $sql = "select t.*,to_char(cal_date,'yyyy-mm-dd hh24:mi:ss') cal_date_f from {$this->report_monitor_min} t where v1=:v1 and v2=:v2
                    and cal_date>=to_date(:s1,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:s2,'yyyy-mm-dd hh24:mi:ss')+1 ";
            $stmt = _ociparse($conn_db, $sql);
        } else {
            $v3 = " v3=:v3 ";
            if ($_GET['line2'] && $_GET['line2'] <> 'null')
                $v3 = " (v3=:v3 or v3=:line2) ";
            $sql = "select t.*,to_char(cal_date,'yyyy-mm-dd hh24:mi:ss') cal_date_f from {$this->report_monitor_min} t where v1=:v1 and v2=:v2
                    and cal_date>=to_date(:s1,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:s2,'yyyy-mm-dd hh24:mi:ss')+1
                    and  {$v3} ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v3', $_GET['v3']);
            if ($_GET['line2'] && $_GET['line2'] <> 'null')
                _ocibindbyname($stmt, ':line2', $_GET['line2']);
        }
        _ocibindbyname($stmt, ':v1', $_GET['type']);
        _ocibindbyname($stmt, ':v2', $_GET['host']);
        _ocibindbyname($stmt, ':s1', $s1);
        _ocibindbyname($stmt, ':s2', $s2);
        $ocierror = _ociexecute($stmt);
        $this->all_datashow_pie = $this->all_datashow = $all_data = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $kdt = date('Y-m-d H:i', strtotime($_row['CAL_DATE_F']));
            $all_data[$_row['V3']][$kdt] = $_row['FUN_COUNT'];
            $this->all_datashow_pie[$_row['V3']] += $_row['FUN_COUNT'];
        }
        //一天的曲线图
        $sd = strtotime($s1);
        $ed = strtotime($s2 . " +1day");
        $time_kd = array();
        foreach ($all_data as $k => $v) {
            for ($id = $sd; $id < $ed; $id += 60) {
                $kdts = date('Y-m-d H:i', $id);
                $data = floatval($v[$kdts]);
                if (!$data && $_COOKIE['un_moom'] <> 'true') {
                    $this->all_datashow[$k][$kdts] = $data;
                    for ($i = 1; $i <= 5; $i++) {
                        $kdts2 = date('Y-m-d H:i', $id - ($i * 60));
                        if ($tmp = floatval($v[$kdts2])) {
                            $this->all_datashow[$k][$kdts] = $tmp;
                            break;
                        }
                        $kdts2 = date('Y-m-d H:i', $id + ($i * 60));
                        if ($tmp = floatval($v[$kdts2])) {
                            $this->all_datashow[$k][$kdts] = $tmp;
                            break;
                        }
                    }
                } else
                    $this->all_datashow[$k][$kdts] = $data;
            }
        }
        include "project/report_js.js";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-18 15:19:40
     * @throws 注意:无DB异常处理
     */
    function report_js_day_v3()
    {
        header("Content-Type:text/javascript; charset=GBK");
        $conn_db = _ocilogon($this->db);
        $s1 = date('Y-m-d');
        $s2 = date('Y-m-d', strtotime('+1 day'));
        if ($_REQUEST['s1'])
            $s1 = $_REQUEST['s1'];
        if ($_REQUEST['s2'] && strtotime($_REQUEST['s2']) < time())
            $s2 = $_REQUEST['s2'];
        //全部下级日统计数据
        if ($_GET['v3'] == 'all') {
            $sql = "select t.*,to_char(cal_date, 'yyyy-mm-dd')  CAL_DATE_F  from
            {$this->report_monitor_hour} t where
            	cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<=to_date(:s2,'yyyy-mm-dd')+1  and v1=:v1 and v2=:v2 ";
            $stmt = _ociparse($conn_db, $sql);
        } else {
            $v3 = " v3=:v3 ";
            if ($_GET['line2'] && $_GET['line2'] <> 'null')
                $v3 = " (v3=:v3 or v3=:line2) ";
            //全部下级日统计数据
            $sql = "select t.*,to_char(cal_date, 'yyyy-mm-dd')  CAL_DATE_F  from
                {$this->report_monitor_hour} t where 
            cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<=to_date(:s2,'yyyy-mm-dd')+1  and v1=:v1 and v2=:v2 and  {$v3} ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v3', $_GET['v3']);
            if ($_GET['line2'] && $_GET['line2'] <> 'null')
                _ocibindbyname($stmt, ':line2', $_GET['line2']);
        }
        _ocibindbyname($stmt, ':v1', $_GET['type']);
        _ocibindbyname($stmt, ':v2', $_GET['host']);
        _ocibindbyname($stmt, ':s1', $s1);
        _ocibindbyname($stmt, ':s2', $s2);
        $ocierror = _ociexecute($stmt);
        $this->all_datashow_pie = $this->all_datashow = $all_data = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $kdt = date('Y-m-d', strtotime($_row['CAL_DATE_F']));
            $all_data[$_row['V3']][$kdt] = $_row['FUN_COUNT'];
            $this->all_datashow_pie[$_row['V3']] += $_row['FUN_COUNT'];
        }
        //一天的曲线图
        $sd = strtotime($s1);
        $ed = strtotime($s2 . " +1day");
        $time_kd = array();
        for ($id = $sd; $id < $ed; $id += 3600 * 24) {
            $kdts = date('Y-m-d', $id);
            foreach ($all_data as $k => $v) {
                $this->all_datashow[$k][$kdts] = floatval($all_data[$k][$kdts]);
            }
        }
        include "project/report_js.js";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-18 15:19:40
     * @throws 注意:无DB异常处理
     */
    function report_js_day()
    {
        header("Content-Type:text/javascript; charset=GBK");
        $conn_db = _ocilogon($this->db);
        $s1 = date('Y-m-d');
        $s2 = date('Y-m-d', strtotime('+1 day'));
        if ($_REQUEST['s1'])
            $s1 = $_REQUEST['s1'];
        if ($_REQUEST['s2'] && strtotime($_REQUEST['s2']) < time())
            $s2 = $_REQUEST['s2'];
        //全部下级日统计数据
        if (substr($_GET['v3'], 0, 2) == '-/') {
            $sql = "select t.*,to_char(cal_date, 'yyyy-mm-dd')  CAL_DATE_F  from
            {$this->report_monitor_date} t where
                	cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<=to_date(:s2,'yyyy-mm-dd')  and v1=:v1 and v2<>:v2 ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v2', substr($_GET['v3'], 2));
        } elseif ($_GET['v3'] == 'all') {
            $sql = "select t.*,to_char(cal_date, 'yyyy-mm-dd')  CAL_DATE_F  from
            {$this->report_monitor_date} t where 
        	cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<=to_date(:s2,'yyyy-mm-dd')  and v1=:v1 ";
            $stmt = _ociparse($conn_db, $sql);
        } else {
            $v2 = " v2=:v2 ";
            if ($_GET['line2'] && $_GET['line2'] <> 'null')
                $v2 = " (v2=:v2 or v2=:line2) ";
            //全部下级日统计数据
            $sql = "select t.*,to_char(cal_date, 'yyyy-mm-dd')  CAL_DATE_F  from
            {$this->report_monitor_date} t where 
        	cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<=to_date(:s2,'yyyy-mm-dd')  and v1=:v1  and  {$v2} ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v2', $_GET['v3']);
            if ($_GET['line2'] && $_GET['line2'] <> 'null')
                _ocibindbyname($stmt, ':line2', $_GET['line2']);
        }
        _ocibindbyname($stmt, ':v1', $_GET['type']);
        _ocibindbyname($stmt, ':s1', $s1);
        _ocibindbyname($stmt, ':s2', $s2);
        $ocierror = _ociexecute($stmt);
        $this->all_datashow_pie = $this->all_datashow = $all_data = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $kdt = date('Y-m-d', strtotime($_row['CAL_DATE_F']));
            $all_data[$_row['V2']][$kdt] = $_row['FUN_COUNT'];
            $this->all_datashow_pie[$_row['V2']] += $_row['FUN_COUNT'];
        }
        //一天的曲线图
        $sd = strtotime($s1);
        $ed = strtotime($s2 . " +1day");
        $time_kd = array();
        for ($id = $sd; $id < $ed; $id += 3600 * 24) {
            $kdts = date('Y-m-d', $id);
            foreach ($all_data as $k => $v) {
                $this->all_datashow[$k][$kdts] = floatval($all_data[$k][$kdts]);
            }
        }
        include "project/report_js.js";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-18 15:19:40
     * @throws 注意:无DB异常处理
     */
    function report_js_hour_v3()
    {
        header("Content-Type:text/javascript; charset=GBK");
        $conn_db = _ocilogon($this->db);
        $s1 = date('Y-m-d');
        $s2 = date('Y-m-d', strtotime('+1 day'));
        if ($_REQUEST['s1'])
            $s1 = $_REQUEST['s1'];
        if ($_REQUEST['s2'] && strtotime($_REQUEST['s2']) < time())
            $s2 = $_REQUEST['s2'];
        //全部下级日统计数据
        if ($_GET['v3'] == 'all') {
            $sql = "select V1,V2,V3,sum(FUN_COUNT) FUN_COUNT ,to_char(cal_date, 'yyyy-mm-dd hh24')  CAL_DATE_F  from
            {$this->report_monitor_hour} t where
            	cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<to_date(:s2,'yyyy-mm-dd')+1  and v1=:v1 and v2=:v2
            	group by v1,v2,v3,to_char(cal_date, 'yyyy-mm-dd hh24') ";
            $stmt = _ociparse($conn_db, $sql);
        } else {
            $v3 = " v3=:v3 ";
            if ($_GET['line2'] && $_GET['line2'] <> 'null')
                $v3 = " (v3=:v3 or v3=:line2) ";
            //全部下级日统计数据
            $sql = "select V1,V2,V3,sum(FUN_COUNT) FUN_COUNT ,to_char(cal_date, 'yyyy-mm-dd hh24')  CAL_DATE_F  from
            {$this->report_monitor_hour} t where
            	cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<to_date(:s2,'yyyy-mm-dd')+1  and v1=:v1 and v2=:v2 and {$v3}  
                group by v1,v2,v3,to_char(cal_date, 'yyyy-mm-dd hh24') ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v3', $_GET['v3']);
            if ($_GET['line2'] && $_GET['line2'] <> 'null')
                _ocibindbyname($stmt, ':line2', $_GET['line2']);
        }
        _ocibindbyname($stmt, ':v1', $_GET['type']);
        _ocibindbyname($stmt, ':v2', $_GET['host']);
        _ocibindbyname($stmt, ':s1', $s1);
        _ocibindbyname($stmt, ':s2', $s2);
        $ocierror = _ociexecute($stmt);
        $this->all_datashow_pie = $this->all_datashow = $all_data = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $kdt = date('Y-m-d H', strtotime($_row['CAL_DATE_F'] . ":00:00"));
            $all_data[$_row['V3']][$kdt] = $_row['FUN_COUNT'];
            $this->all_datashow_pie[$_row['V3']] += $_row['FUN_COUNT'];
        }
        //一天的曲线图
        $sd = strtotime($s1);
        $ed = strtotime($s2 . " +1 day");
        $time_kd = array();
        for ($id = $sd; $id < $ed; $id += 3600) {
            $kdts = date('Y-m-d H', $id);
            foreach ($all_data as $k => $v) {
                $this->all_datashow[$k][$kdts] = floatval($all_data[$k][$kdts]);
            }
        }
        include "project/report_js.js";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-18 15:19:40
     * @throws 注意:无DB异常处理
     */
    function report_js_hour()
    {
        header("Content-Type:text/javascript; charset=GBK");
        $conn_db = _ocilogon($this->db);
        $s1 = date('Y-m-d');
        $s2 = date('Y-m-d', strtotime('+1 day'));
        if ($_REQUEST['s1'])
            $s1 = $_REQUEST['s1'];
        if ($_REQUEST['s2'] && strtotime($_REQUEST['s2']) < time())
            $s2 = $_REQUEST['s2'];
        //全部下级日统计数据
        if (substr($_GET['v3'], 0, 2) == '-/') {
            $sql = "select  V1,V2,sum(FUN_COUNT) FUN_COUNT ,to_char(cal_date, 'yyyy-mm-dd hh24')  CAL_DATE_F  from
            {$this->report_monitor_hour} t where
            	cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<=to_date(:s2,'yyyy-mm-dd')  and v1=:v1 and v2<>:v2 
            	group by v1,v2,to_char(cal_date, 'yyyy-mm-dd hh24') ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v2', substr($_GET['v3'], 2));
        } elseif ($_GET['v3'] == 'all') {
            $sql = "select V1,V2,sum(FUN_COUNT) FUN_COUNT ,to_char(cal_date, 'yyyy-mm-dd hh24')  CAL_DATE_F  from
            {$this->report_monitor_hour} t where
            	cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<to_date(:s2,'yyyy-mm-dd')+1  and v1=:v1
            	group by v1,v2,to_char(cal_date, 'yyyy-mm-dd hh24') ";
            $stmt = _ociparse($conn_db, $sql);
        } else {
            $v2 = " v2=:v2 ";
            if ($_GET['line2'] && $_GET['line2'] <> 'null')
                $v2 = " (v2=:v2 or v2=:line2) ";
            //全部下级日统计数据
            $sql = "select V1,V2,sum(FUN_COUNT) FUN_COUNT ,to_char(cal_date, 'yyyy-mm-dd hh24')  CAL_DATE_F  from
                {$this->report_monitor_hour} t where 
            	cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<to_date(:s2,'yyyy-mm-dd')+1  and v1=:v1  and {$v2}  
                group by v1,v2,to_char(cal_date, 'yyyy-mm-dd hh24') ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v2', $_GET['v3']);
            if ($_GET['line2'] && $_GET['line2'] <> 'null')
                _ocibindbyname($stmt, ':line2', $_GET['line2']);
        }
        _ocibindbyname($stmt, ':v1', $_GET['type']);
        _ocibindbyname($stmt, ':s1', $s1);
        _ocibindbyname($stmt, ':s2', $s2);
        $ocierror = _ociexecute($stmt);
        $this->all_datashow_pie = $this->all_datashow = $all_data = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $kdt = date('Y-m-d H', strtotime($_row['CAL_DATE_F'] . ":00:00"));
            $all_data[$_row['V2']][$kdt] = $_row['FUN_COUNT'];
            $this->all_datashow_pie[$_row['V2']] += $_row['FUN_COUNT'];
        }
        //一天的曲线图
        $sd = strtotime($s1);
        $ed = strtotime($s2 . " +1 day");
        $time_kd = array();
        for ($id = $sd; $id < $ed; $id += 3600) {
            $kdts = date('Y-m-d H', $id);
            foreach ($all_data as $k => $v) {
                $this->all_datashow[$k][$kdts] = floatval($all_data[$k][$kdts]);
            }
        }
        include "project/report_js.js";
    }

    /**
     * @desc 显示统计的主页面
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-06-21 10:11:32
     * @throws 注意:无DB异常处理
     */
    function report_monitor()
    {
        $conn_db = _ocilogon($this->db);
        $s1 = date('Y-m-d', strtotime("-1 month"));
        $s2 = date('Y-m-d');
        if ($_REQUEST['s1'])
            $s1 = $_REQUEST['s1'];
        if ($_REQUEST['s2'] && strtotime($_REQUEST['s2']) < time())
            $s2 = $_REQUEST['s2'];

        //
        $start_date = $_REQUEST["start_date"] ? $_REQUEST["start_date"] : date("Y-m-d");
        //强制看今天的第一列
        if ($_COOKIE['direct_date'] == 'true' && !$_REQUEST["start_date"])
            $_REQUEST["start_date"] = $start_date;
        //时间乱传,不在范围之内
        if (strtotime($start_date) > strtotime($s2) || strtotime($start_date) < strtotime($s1))
            unset($start_date, $_REQUEST["start_date"]);
        $start_date1 = $start_date;

        $group_name_2 = '默认';
        $group_name = '默认';
        if ($_COOKIE[md5($_SERVER['SCRIPT_FILENAME']) . '_v1_group_name'])
            $group_name = $_COOKIE[md5($_SERVER['SCRIPT_FILENAME']) . '_v1_group_name'];
        //别名替换
        $sql = "select t.*,decode(as_name,null,v1,as_name) as_name1 from {$this->report_monitor_v1} t where id>0
        order by decode(as_name,null,v1,as_name)  ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);
        $v1_config_group = $this->v1_config = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if ($_REQUEST['type'] == $_row['V1']) {
                $group_name = $_row['GROUP_NAME'];
                $group_name_1 = $_row['GROUP_NAME_1'];
                $group_name_2 = $_row['GROUP_NAME_2'];
                $_COOKIE[md5($_SERVER['SCRIPT_FILENAME']) . '_v1_group_name'] = $group_name;
                setcookie(md5($_SERVER['SCRIPT_ILENAME']) . '_v1_group_name', $group_name);
            }
            $v1_config_group[$_row['GROUP_NAME_1']][$_row['GROUP_NAME_2']][$_row['GROUP_NAME']][$_row['V1']] = $_row;
        }
        $this->v1_config = $v1_config_group[$group_name_1][$group_name_2][$group_name];
        //偏差时差
        if ($this->v1_config[$_REQUEST['type']]['START_CLOCK'])
            $start_date1 = date('Y-m-d H:i:s', strtotime($start_date . " +{$this->v1_config[$_REQUEST['type']]['START_CLOCK']} hour"));
        //所有类型
        $sql = "select v1 from {$this->report_monitor_config} where id>0  group by v1 order by v1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $this->type = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $this->type[] = $_row['V1'];
            if (!$_REQUEST['type'] && $this->v1_config[$_row['V1']]) {
                $_REQUEST['type'] = $_row['V1'];
            }
        }

        //当前类型下面的所有模块
        $sql = "select t.* ,decode(as_name,null,v2,as_name) as_name1  from {$this->report_monitor_config} t where v1=:v1 and v2<>'汇总'
        order by orderby,as_name1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['type']);
        _ociexecute($stmt);
        $this->host = $_row = array();
        $this->v1_config[$_REQUEST['type']]['SHOW_ALL'] = 1;
        if ($this->v1_config[$_REQUEST['type']]['SHOW_ALL'])
            $this->host[] = array(
                'V1' => $_REQUEST['type'],
                'V2' => '汇总',
                'AS_NAME1' => '汇总'
            );
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $this->host[$_row['V2']] = $_row;
            if ($_REQUEST['host'] == $_row['V2'])
                $this->v_config = $_row;
        }
        if ($_COOKIE['direct_date'] == 'true' && !$_REQUEST["host"])
            $_REQUEST["host"] = $this->host[0]['V2'];

        //全部下级日统计数据
        $sql = "select t.*,to_char(cal_date, 'yyyy-mm-dd')  CAL_DATE_F  from
        {$this->report_monitor_date} t where 
        cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<=to_date(:s2,'yyyy-mm-dd')  and v1=:v1 and v2<>'汇总' ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['type']);
        _ocibindbyname($stmt, ':s1', $s1);
        _ocibindbyname($stmt, ':s2', $s2);
        $ocierror = _ociexecute($stmt);
        $this->all_start_date_all = $this->all_start_date_count = $this->all_start_date = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if (!$this->v1_config[$_REQUEST['type']]['START_CLOCK']) {
                $this->all_start_date_count[$_row['V2']]['total'] += $_row['FUN_COUNT'];
                $this->all_start_date_count[$_row['V2']]['total_i']++;
                $this->all_start_date_count[$_row['V2']]['total_avg'] = round($this->all_start_date_count[$_row['V2']]['total'] / $this->all_start_date_count[$_row['V2']]['total_i'], 2);
                $this->all_start_date_all[$_row['CAL_DATE_F']] += $_row['FUN_COUNT'];
                $this->all_start_date[$_row['CAL_DATE_F']][$_row['V2']] += $_row['FUN_COUNT'];

                if ($this->v1_config[$_REQUEST['type']]['SHOW_ALL']) {
                    $this->all_start_date_count['汇总']['total'] += $_row['FUN_COUNT'];
                    $this->all_start_date_count['汇总']['total_i']++;
                    $this->all_start_date_count['汇总']['total_avg'] = round($this->all_start_date_count['汇总']['total'] / $this->all_start_date_count['汇总']['total_i'], 2);
                    if ($this->v1_config[$_REQUEST['type']]['SHOW_AVG'] == 1)
                        $this->all_start_date[$_row['CAL_DATE_F']]['汇总'] = round($this->all_start_date_all[$_row['CAL_DATE_F']] / (count($this->all_start_date_count) - 1), 2);
                    else
                        $this->all_start_date[$_row['CAL_DATE_F']]['汇总'] += $_row['FUN_COUNT'];
                }
            }
            $this->all_start_date[$_row['CAL_DATE_F']]['LOOKUP'] = $_row['LOOKUP'];
        }
        //显示对比数据
        if ($this->host[$_row['V2']]['V2_COMPARE'] == 1) {
            $name = $_row['V2'] . '增量';
            $time = date('Y-m-d', strtotime($_row['CAL_DATE_F'] . " +1 day"));
            $this->all_start_date[$_row['CAL_DATE_F']][$name] = $this->all_start_date[$_row['CAL_DATE_F']][$_row['V2']] - $this->all_start_date[$time][$_row['V2']];
            $this->all_start_date_count[$name]['total'] += $this->all_start_date[$_row['CAL_DATE_F']][$name];
        }
        foreach ($this->all_start_date as $k => $v) {
            foreach ($v as $i => $c) {
                //显示对比数据
                if ($this->host[$i]['V2_COMPARE'] == 1) {
                    $name = $i . '增量';
                    $time = date('Y-m-d', strtotime($k . " -1 day"));
                    $this->all_start_date[$k][$name] = $this->all_start_date[$k][$i] - $this->all_start_date[$time][$i];
                    $this->all_start_date_count[$name]['total'] += $this->all_start_date[$k][$name];
                }
            }
        }
        //获取v2分组
        $sql = "select t.* ,decode(as_name,null,v2,as_name) as_name1  from {$this->report_monitor_config} t where v1=:v1 and v2<>'汇总'
                        order by orderby,as_name1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['type']);
        _ociexecute($stmt);
        $this->group = array();
        if ($this->v1_config[$_REQUEST['type']]['SHOW_ALL'])
            $this->group['汇总'][] = array(
                'V1' => $_REQUEST['type'],
                'V2' => '汇总',
                'AS_NAME1' => '汇总'
            );
        $cospan = 1;
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if ($_row['V2_GROUP'] == '') {
                $_row['V2_GROUP'] = '其它';
            } else {
                $is_group = true;
            }
            $cospan++;
            $this->group[$_row['V2_GROUP']][] = $_row;
            //显示对比数据
            if ($_row['V2_COMPARE'] == 1) {
                $_row['AS_NAME1'] = $_row['AS_NAME1'] . '增量';
                $_row['V2'] = $_row['V2'] . '增量';
                $this->group[$_row['V2_GROUP']][] = $_row;
                $cospan++;
            }
        }
        //获取分组总数
        if ($is_group) {
            foreach ($this->group as $k => $v) {
                foreach ($v as $v2) {
                    foreach ($this->all_start_date_count as $c => $i) {
                        if ($c == $v2['V2']) {
                            $this->group_count[$k]['count'] += $i['total'];
                        }
                    }
                }
            }
        }
        //时区有偏差,改成从小时表读取数据
        if ($this->v1_config[$_REQUEST['type']]['START_CLOCK']) {
            $sql = "select v1,v2,sum(fun_count) fun_count, to_char(t.cal_date, 'yyyy-mm-dd hh24:mi:ss') as cal_date_f
                	from  {$this->report_monitor_hour} t   
                    where cal_date>=to_date(:s1,'yyyy-mm-dd')+:diff/24 and cal_date<=to_date(:s2,'yyyy-mm-dd')+:diff/24 
                    and v1=:v1   
                    group by v1,v2,to_char(t.cal_date, 'yyyy-mm-dd hh24:mi:ss') ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':diff', $this->v1_config[$_REQUEST['type']]['START_CLOCK']);
            _ocibindbyname($stmt, ':s1', $s1);
            _ocibindbyname($stmt, ':s2', $s2);
            _ocibindbyname($stmt, ':v1', $_REQUEST['type']);
            $ocierror = _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                if (date('H', strtotime($_row['CAL_DATE_F'])) < $this->v1_config[$_REQUEST['type']]['START_CLOCK'])
                    $_row['CAL_DATE_F'] = date('Y-m-d', strtotime($_row['CAL_DATE_F'] . " -1 day"));
                else
                    $_row['CAL_DATE_F'] = date('Y-m-d', strtotime($_row['CAL_DATE_F']));

                if ($this->v1_config[$_REQUEST['type']]['SHOW_ALL'] == 1) {
                    $this->all_start_date_count['汇总'] += $_row['FUN_COUNT'];
                    $this->all_start_date[$_row['CAL_DATE_F']]['汇总'] += $_row['FUN_COUNT'];
                }

                $this->all_start_date_count[$_row['V2']] += $_row['FUN_COUNT'];
                $this->all_start_date[$_row['CAL_DATE_F']][$_row['V2']] += $_row['FUN_COUNT'];
                $this->all_start_date_all[$_row['CAL_DATE_F']] += $_row['FUN_COUNT'];
            }
        }
        //
        if ($start_date && $this->v1_config[$_REQUEST['type']]['SHOW_ALL'] && $_REQUEST['host'] == '汇总') {
            //当日数据
            $sql = "  select v2 as v3,sum(fun_count) fun_count,round(avg(fun_count),2) fun_count_avg,to_char(t.cal_date, 'dd hh24') as cal_date_f  from  {$this->report_monitor_hour} t
            where cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1
            and v1=:v1  and v2<>'汇总'
            group by v1,v2,to_char(t.cal_date, 'dd hh24')  ";
            $stmt2 = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt2, ':cal_date', $start_date1);
            _ocibindbyname($stmt2, ':v1', $_REQUEST['type']);
            $ocierror = _ociexecute($stmt2);
            print_r($ocierror);
            $this->fun_count = $this->fun_count2 = $_row = array();
            while (ocifetchinto($stmt2, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $this->fun_count[$_row['V3']]['AS_NAME1'] = $_row['V3'];
                foreach ($this->host as $k => $v)
                    if ($_row['V3'] == $v['V2']) {
                        $this->fun_count[$_row['V3']]['AS_NAME1'] = $v['AS_NAME1'];
                        break;
                    }
                if ($this->v1_config[$_REQUEST['type']]['SHOW_AVG'] == 1)
                    $_row['FUN_COUNT'] = $_row['FUN_COUNT_AVG'];
                $this->fun_count[$_row['V3']][$_row['CAL_DATE_F']] = $_row;
                $this->fun_count[$_row['V3']]['DIFF_TIME'] = max($this->fun_count[$_row['V3']]['DIFF_TIME'], $_row['DIFF_TIME']);
                if ($this->v1_config[$_REQUEST['type']]['SHOW_AVG'] == 1) {
                    $this->fun_count[$_row['V3']]['FUN_COUNT'] = $_row['FUN_COUNT_AVG'];
                } else
                    $this->fun_count[$_row['V3']]['FUN_COUNT'] += $_row['FUN_COUNT'];
                $this->fun_count[$_row['V3']]['FUN_COUNT_I'] += 1;
                $this->fun_count[$_row['V3']]['FUN_COUNT_AVG'] = round($this->fun_count[$_row['V3']]['FUN_COUNT'] / $this->fun_count[$_row['V3']]['FUN_COUNT_I'], 2);
                $this->fun_count2[$_row['CAL_DATE_F']] += $_row['FUN_COUNT'];
            }
            uasort($this->fun_count, create_function('$a,$b', 'if ($a["FUN_COUNT"] == $b["FUN_COUNT"]) return 0; return ($a["FUN_COUNT"]<$b["FUN_COUNT"]);'));
        } elseif ($start_date) {
            $this->pageObj = new page(10000, 300);
            //当日数据
            $sql = "{$this->pageObj->num_1} select v3,sum(fun_count) fun_count from  {$this->report_monitor_hour}
                where cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1 
                and v1=:v1 and v2=:v2  
                group by v1,v2,v3  {$this->pageObj->num_3} ";
            $stmt2 = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt2, ':cal_date', $start_date1);
            _ocibindbyname($stmt2, ':v1', $_REQUEST['type']);
            _ocibindbyname($stmt2, ':v2', $_REQUEST['host']);
            _ocibindbyname($stmt2, ':num_1', intval($this->pageObj->limit_1));
            _ocibindbyname($stmt2, ':num_3', intval($this->pageObj->limit_3));
            $ocierror = _ociexecute($stmt2);
            $this->fun_count = $this->fun_count2 = $_row2 = array();
            while (ocifetchinto($stmt2, $_row2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $sql = "select t.*,to_char(t.cal_date, 'dd hh24') as cal_date_f
                   from {$this->report_monitor_hour} t 
                   where cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1
                   and v1=:v1 and v2=:v2 and v3=:v3   order by fun_count desc";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':cal_date', $start_date1);
                _ocibindbyname($stmt, ':v1', $_REQUEST['type']);
                _ocibindbyname($stmt, ':v2', $_REQUEST['host']);
                _ocibindbyname($stmt, ':v3', $_row2['V3']);
                $ocierror = _ociexecute($stmt);
                $_row = array();
                while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                    $this->fun_count[$_row['V3']]['AS_NAME1'] = $_row['V3'];
                    $this->fun_count[$_row['V3']][$_row['CAL_DATE_F']] = $_row;
                    $this->fun_count[$_row['V3']]['DIFF_TIME'] = max($this->fun_count[$_row['V3']]['DIFF_TIME'], $_row['DIFF_TIME']);
                    $this->fun_count[$_row['V3']]['FUN_COUNT'] += $_row['FUN_COUNT'];
                    $this->fun_count[$_row['V3']]['FUN_COUNT_I'] += 1;
                    $this->fun_count[$_row['V3']]['FUN_COUNT_AVG'] = round($this->fun_count[$_row['V3']]['FUN_COUNT'] / $this->fun_count[$_row['V3']]['FUN_COUNT_I'], 2);
                    $this->fun_count2[$_row['CAL_DATE_F']] += $_row['FUN_COUNT'];
                    $this->fun_count3['FUN_COUNT_I']++;
                    $this->fun_count3['FUN_COUNT'] += $_row['FUN_COUNT'];
                    $this->fun_count3['FUN_COUNT_AVG'] = round($this->fun_count3['FUN_COUNT'] / $this->fun_count3['FUN_COUNT_I'], 2);
                }
            }
            uasort($this->fun_count, create_function('$a,$b', 'if ($a["FUN_COUNT"] == $b["FUN_COUNT"]) return 0; return ($a["FUN_COUNT"]<$b["FUN_COUNT"]);'));
        }
        //加载文件
        include "project/report_monitor.html";
    }

    /**
     * @desc 所有监控更多信息
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-06-21 14:11:32
     * @throws 注意:无DB异常处理
     */
    function report_monitor_more()
    {
        $conn_db = _ocilogon($this->db);
        $this->pageObj = new page(10000, 100);
        if ($_REQUEST['fun_host'] == '汇总') {
            $sql = "select FUN_COUNT,v3 v4  from {$this->report_monitor_hour} t where v1=:v1 and v2=:v2  and cal_date=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') order by FUN_COUNT desc  ";
            $stmt = _ociparse($conn_db, "{$this->pageObj->num_1} {$sql} {$this->pageObj->num_3}");
            _ocibindbyname($stmt, ':v1', $_REQUEST['fun_type']);
            _ocibindbyname($stmt, ':v2', $_REQUEST['fun_act']);
            _ocibindbyname($stmt, ':num_1', intval($this->pageObj->limit_1));
            _ocibindbyname($stmt, ':num_3', intval($this->pageObj->limit_3));
            _ocibindbyname($stmt, ':cal_date', $_REQUEST['cal_date']);
            $ocierror = _ociexecute($stmt);
            $_row = array();
            $monitor_more = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $monitor_more[] = $_row;
            }
        } else {
            if ($_REQUEST['fun_act'])
                $sql = "select * from {$this->report_monitor} where v1=:v1 and v2=:v2 and  v3=:v3 and cal_date=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') order by FUN_COUNT desc ";
            else
                $sql = "select * from {$this->report_monitor} where v1=:v1 and v2=:v2 and v3 is null and cal_date=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') order by FUN_COUNT desc ";
            $stmt = _ociparse($conn_db, "{$this->pageObj->num_1} {$sql} {$this->pageObj->num_3}");
            _ocibindbyname($stmt, ':v1', $_REQUEST['fun_type']);
            _ocibindbyname($stmt, ':v2', $_REQUEST['fun_host']);
            _ocibindbyname($stmt, ':num_1', intval($this->pageObj->limit_1));
            _ocibindbyname($stmt, ':num_3', intval($this->pageObj->limit_3));
            if ($_REQUEST['fun_act'])
                _ocibindbyname($stmt, ':v3', $_REQUEST['fun_act']);
            _ocibindbyname($stmt, ':cal_date', $_REQUEST['cal_date']);
            $ocierror = _ociexecute($stmt);
            $_row = array();
            $monitor_more = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $monitor_more[] = $_row;
            }
        }
        include "project/report_monitor_more.html";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-12 14:55:11
     * @throws 注意:无DB异常处理
     */
    function _report_monitor_delete($conn_db)
    {

        if ($_REQUEST['v2']) {
            $where = 'and v2=:v2';
        }
        $sql = "delete from {$this->report_monitor} where v1=:v1 {$where} and  cal_date>sysdate-10/24 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        if ($_REQUEST['v2']) {
            _ocibindbyname($stmt, ':v2', $_REQUEST['v2']);
        }
        $ocierror = _ociexecute($stmt);
        $sql = "delete from {$this->report_monitor_config} where v1=:v1 {$where} ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        if ($_REQUEST['v2']) {
            _ocibindbyname($stmt, ':v2', $_REQUEST['v2']);
        }
        $ocierror = _ociexecute($stmt);

        $sql = "delete from {$this->report_monitor_date} where v1=:v1 {$where} and cal_date>sysdate-10 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        if ($_REQUEST['v2']) {
            _ocibindbyname($stmt, ':v2', $_REQUEST['v2']);
        }
        $ocierror = _ociexecute($stmt);

        $sql = "delete from {$this->report_monitor_hour} where v1=:v1 {$where}  and cal_date>sysdate-10";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        if ($_REQUEST['v2']) {
            _ocibindbyname($stmt, ':v2', $_REQUEST['v2']);
        }
        $ocierror = _ociexecute($stmt);

        $sql = "select * from {$this->report_monitor_config} where v1=:v1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        $ocierror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        if (!$_row) {
            $sql = "delete from {$this->report_monitor_v1} where v1=:v1   ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
            _ociexecute($stmt);
        }
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-01 17:05:43
     * @throws 注意:无DB异常处理
     */
    function report_check_do()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "update {$this->report_monitor_date} set LOOKUP=sysdate where v1=:v1  and CAL_DATE>=trunc(sysdate-7) ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        $ocierror = _ociexecute($stmt);
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-05 21:04:49
     * @throws 注意:无DB异常处理
     */
    function report_move()
    {
        include "project/report_move.html";
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-05 16:38:45
     * @throws 注意:无DB异常处理
     */
    function report_move_do()
    {
        $conn_db = _ocilogon($this->db);
        if ($_REQUEST['v1_o'] == $_REQUEST['v1_n'] && $_REQUEST['v2_o'] == $_REQUEST['v2_n'])
            die("<script>parent.window.location.reload();</script>");

        //原始表
        $sql = "select * from  {$this->report_monitor}   where v1=:v1 and v2=:v2  ";
        $stmt_list = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt_list, ':v1', $_REQUEST['v1_o']);
        _ocibindbyname($stmt_list, ':v2', $_REQUEST['v2_o']);
        $ocierror = _ociexecute($stmt_list);
        $_row_list = array();
        while (ocifetchinto($stmt_list, $_row_list, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $sql = "select count(*) c from {$this->report_monitor} where v1=:v1 and v2=:v2
            and v3=:v3 and v4=:v4 and v5=:v5  ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v1', $_REQUEST['v1_n']);
            _ocibindbyname($stmt, ':v2', $_REQUEST['v2_n']);
            _ocibindbyname($stmt, ':v3', $_row_list['V3']);
            _ocibindbyname($stmt, ':v4', $_row_list['V4']);
            _ocibindbyname($stmt, ':v5', $_row_list['V5']);
            $ocierror = _ociexecute($stmt);
            $_row = array();
            ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
            if ($_row['C'] < 1) {
                $sql = "update {$this->report_monitor} set v1=:v1 ,v2=:v2  where md5=:md5 ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1', $_REQUEST['v1_n']);
                _ocibindbyname($stmt, ':v2', $_REQUEST['v2_n']);
                _ocibindbyname($stmt, ':md5', $_row_list['MD5']);
                $ocierror = _ociexecute($stmt);
            } else {
                $sql = "update {$this->report_monitor} set fun_count=fun_count+:fun_count  where md5=:md5 ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':fun_count', $_row_list['FUN_COUNT']);
                _ocibindbyname($stmt, ':md5', $_row['MD5']);
                $ocierror = _ociexecute($stmt);

                $sql = "delete from {$this->report_monitor} where md5=:md5 ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':md5', $_row_list['MD5']);
                $ocierror = _ociexecute($stmt);
            }
        }
        foreach (array(
                     $this->report_monitor_config,
                     $this->report_monitor_date,
                     $this->report_monitor_hour,
                     $this->report_monitor_min
                 ) as $tb) {
            //
            $sql = "select count(*) c from {$tb} where v1=:v1_n and v2=:v2_n  ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v1_n', $_REQUEST['v1_n']);
            _ocibindbyname($stmt, ':v2_n', $_REQUEST['v2_n']);
            $ocierror = _ociexecute($stmt);
            $_row = array();
            ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
            if ($_row['C'] < 1) {
                //
                $sql = "update {$tb} set v1=:v1_n, v2=:v2_n where v1=:v1_o and v2=:v2_o ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1_n', $_REQUEST['v1_n']);
                _ocibindbyname($stmt, ':v2_n', $_REQUEST['v2_n']);
                _ocibindbyname($stmt, ':v1_o', $_REQUEST['v1_o']);
                _ocibindbyname($stmt, ':v2_o', $_REQUEST['v2_o']);
                $ocierror = _ociexecute($stmt);
            } else {
                $sql = "delete from {$tb}   where v1=:v1_o and v2=:v2_o ";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1_o', $_REQUEST['v1_o']);
                _ocibindbyname($stmt, ':v2_o', $_REQUEST['v2_o']);
            }
        }
        echo "<script>parent.window.location.reload();</script>";
    }


    /**
     * @desc 刷新数据
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-09-21 11:19:07
     * @throws 注意:无DB异常处理
     */
    function report_flush($chinfo)
    {
        $file_name = basename($_SERVER['SCRIPT_FILENAME']);
        list($usec, $sec) = explode(" ", microtime());
        $t1 = sprintf("%.6f", ((float)$usec + (float)$sec));

        echo _curl($chinfo, "http://{$_SERVER['HTTP_HOST']}/{$file_name}?act=monitor", null, array(
            CURLOPT_TIMEOUT => 100
        ));
        echo _curl($chinfo, "http://{$_SERVER['HTTP_HOST']}/{$file_name}?act=monitor_config", null, array(
            CURLOPT_TIMEOUT => 100
        ));

        list($usec, $sec) = explode(" ", microtime());
        $t2 = sprintf("%.6f", ((float)$usec + (float)$sec));
        echo "刷新完毕.耗时:" . ($t2 - $t1) . "<br>\n";
    }

    /**
     * @desc 通知配置页面
     * @author 聂雨薇 mailto:nyw@ppstream.com
     * @since  2013-01-17 10:27:08
     * @throws 注意:无DB异常处理
     */
    function notice_monitor_config()
    {
        $conn_db = _ocilogon($this->db);

        foreach ($_REQUEST['uncount'] as $k => $v) {
            list($v1, $v2) = (explode('#@', $v));
            $_REQUEST['v1'] = $v1;
            $sql = "select as_name,v2 from {$this->report_monitor_config} where v2=:v2 ";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':v2', $v2);
            $ocierror = _ociexecute($stmt);
            ocifetchinto($stmt, $row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
            if (trim($row['AS_NAME']) != '') {
                $a_v2[$v2] = $row['AS_NAME'];
            } else {
                $a_v2[$v2] = $row['V2'];
            }
        }
        $sql = "select decode(as_name,null,v1,as_name) as_name1 from {$this->report_monitor_v1} t where v1=:v1 ";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        $ocierror = _ociexecute($stmt);
        $this->row_config = array();
        ocifetchinto($stmt, $this->row_config, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

        include "project/notice_monitor_config.html";
    }

    /**
     * @desc 新加
     * @author 聂雨薇 mailto:nyw@ppstream.com
     * @since  2013-01-21 17:08:34
     * @throws 注意:无DB异常处理
     */
    function notice_monitor_v1_do()
    {
        $conn_db = _ocilogon($this->db);
        if ($_POST['receive_type'] != '3') {
            $notice_date = implode($_POST['notice_date'], ',');
            $_POST['notice_hz'] = '';
            $v2 = implode($_POST['v2'], ',');
        } else {
            $notice_date = '0';
            $_POST['notice_time'] = '';
            $v2 = $_POST['v2'];
        }
        $msg_time = implode($_POST['msg_time'], ',');
        //编辑页面
        if (isset($_POST['id']) && $_POST['id'] != '') {
            $sql = "update {$this->notice_monitor_config} set AS_NAME=:AS_NAME,RECEIVE_VALUE=:RECEIVE_VALUE,
            NOTICE_DATE=:NOTICE_DATE,V2=:V2,NOTICE_TIME=:NOTICE_TIME where id=:id";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':AS_NAME', $_REQUEST['as_name']);
            ocibindbyname($stmt, ':RECEIVE_VALUE', $_REQUEST['receive_value']);
            ocibindbyname($stmt, ':NOTICE_DATE', $notice_date);
            ocibindbyname($stmt, ':NOTICE_TIME', $_REQUEST['notice_time']);
            ocibindbyname($stmt, ':V2', $v2);
            ocibindbyname($stmt, ':id', $_POST['id']);
            _ociexecute($stmt);
            echo "<script>parent.window.location.reload();</script>";
        } else {
            $sql = "insert into {$this->notice_monitor_config}(ID,V1,AS_NAME,RECEIVE_TYPE,RECEIVE_VALUE,
                            MEG_TYPE,NOTICE_DATE,NOTICE_TIME,NOTICE_HZ,V2,MSG_TIME,MAX_NUM,MIN_NUM,SID,V3,ADD_TIME) values 
                           (SEQ_{$this->notice_monitor_config}.NEXTVAL,:V1,:AS_NAME,:RECEIVE_TYPE,:RECEIVE_VALUE,:MEG_TYPE,
                           :NOTICE_DATE,:NOTICE_TIME,:NOTICE_HZ,:V2,:MSG_TIME,:MAX_NUM,:MIN_NUM,:SID,:V3,sysdate)";
            $stmt = _ociparse($conn_db, $sql);
            ocibindbyname($stmt, ':V1', $_REQUEST['v1']);
            ocibindbyname($stmt, ':AS_NAME', $_REQUEST['as_name']);
            ocibindbyname($stmt, ':RECEIVE_TYPE', $_REQUEST['receive_type']);
            ocibindbyname($stmt, ':RECEIVE_VALUE', $_REQUEST['receive_value']);
            ocibindbyname($stmt, ':MEG_TYPE', $_REQUEST['meg_type']);
            ocibindbyname($stmt, ':NOTICE_DATE', $notice_date);
            ocibindbyname($stmt, ':NOTICE_TIME', $_REQUEST['notice_time']);
            ocibindbyname($stmt, ':NOTICE_HZ', $_REQUEST['notice_hz']);
            ocibindbyname($stmt, ':MAX_NUM', $_REQUEST['max_num']);
            ocibindbyname($stmt, ':V2', $v2);
            ocibindbyname($stmt, ':MIN_NUM', $_REQUEST['min_num']);
            ocibindbyname($stmt, ':SID', $_REQUEST['sid']);
            ocibindbyname($stmt, ':V3', $_REQUEST['v3']);
            ocibindbyname($stmt, ':MSG_TIME', $msg_time);
            _ociexecute($stmt);
            header("location:?act=show_notice&type={$_POST['receive_type']}");
        }
    }

    /**
     * @desc 通知配置显示
     * @author 聂雨薇 mailto:nyw@ppstream.com
     * @since  2013-01-22 16:59:03
     * @throws 注意:无DB异常处理
     */
    function show_notice()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "select * from {$this->notice_monitor_config} where receive_type=:receive_type order by id asc";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':receive_type', $_REQUEST['type']);
        $error = _ociexecute($stmt);
        $arr = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if ($_row['RECEIVE_TYPE'] == 1) {
                $_row['RECEIVE'] = '邮箱';
            }
            if ($_row['RECEIVE_TYPE'] == 2) {
                $_row['RECEIVE'] = '手机';
            }
            if ($_row['RECEIVE_TYPE'] == 3) {
                $_row['RECEIVE'] = '接口预警';
            }

            $arr_v2 = explode(',', $_row['V2']);
            $_row['v2'] = $arr_v2;
            $notice_arr[] = $_row;
        }
        $this->notice_config = array(
            '1' => '邮箱',
            '3' => '预警接口'
        );
        include "project/notice_index.html";
    }

    /**
     * @desc 配置删除
     * @author 聂雨薇 mailto:nyw@ppstream.com
     * @since  2013-02-05 16:32:20
     * @throws 注意:无DB异常处理
     */
    function notice_delete()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "delete from {$this->notice_monitor_config} where id=:id";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':id', $_REQUEST['id']);
        $ocierror = _ociexecute($stmt);
        header("location:?act=show_notice&type={$_REQUEST['type']}");
    }

    /**
     * @desc 按时邮件发送
     * @author 聂雨薇 mailto:nyw@ppstream.com
     * @since  2013-02-19 13:47:25
     * @throws 注意:无DB异常处理
     */
    function P1H_SendMsg()
    {
        //取出要发送的邮件
        $conn_db = _ocilogon($this->db);
        if ($_GET['id']) { //发送测试邮件
            $sql = "select * from {$this->notice_monitor_config} where receive_type=1";
            $stmt = _ociparse($conn_db, $sql);
            $error = _ociexecute($stmt);
            $arr = $_row = array();
            ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
            $arr[] = $_row;
        } else {
            $data = date('w');
            $hour = date("H");
            $sql = "select * from {$this->notice_monitor_config} where receive_type=1";
            $stmt = _ociparse($conn_db, $sql);
            $error = _ociexecute($stmt);
            $arr = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                if (strpos($_row['NOTICE_DATE'], $data) !== false) {
                    //比对星期数据
                    if ($_row['NOTICE_TIME'] == $hour) {
                        $arr[] = $_row;
                    }
                }
            }
        }
        foreach ($arr as $k => $v) {
            if ($v['V2'] != 'all') {
                $t_arr[$k]['start_date'] = date("Y-m-d", strtotime("-1 day"));
                $t_arr[$k]['end_date'] = date("Y-m-d", time());
                $t_arr[$k]['title'] = $v['AS_NAME'] . '昨天数据';
                $t_arr[$k]['v2'] = $v['V2'];
            } else {
                $t_arr[$k]['title'] = $v['AS_NAME'] . '_' . date('Y-m-d');
                $t_arr[$k]['v2'] = 'all';
            }
            $t_arr[$k]['name'] = $v['V1'];
            $t_arr[$k]['email'] = $v['RECEIVE_VALUE'];
        }
        foreach ($t_arr as $c => $i) {
            $send_arr = explode(',', $i['v2']);
            $name_arr = array();
            $str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">';
            //获取汇总数据
            if ($i['v2'] == 'all') {
                $url = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?act=report_compare_monitor&show_all=1&type=" . rawurlencode($i['name']);
                $content = _curl($chinfo, $url);
                //内容截取
                $arr = $str . substr($content, strpos($content, '<head>'), (strpos($content, '</head>') - (strpos($content, '<head>'))));
                preg_match('/<div class="tBd" id="table_1">(.*)<\/div>/si', $content, $value);
                $con = $this->_strip_selected_tags($arr . $value[0], 'a');
                _file_put_contents($i['title'] . '.html', $con);
                $name_arr[] = $i['title'] . '.html';
            } else {
                foreach ($send_arr as $k => $v) {
                    $url = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?act=report_monitor&start_date={$i['start_date']}&host=" . rawurlencode($v) . "&type=" . rawurlencode($i['name']);
                    $content = _curl($chinfo, $url);
                    //内容截取
                    $arr = $str . substr($content, strpos($content, '<head>'), (strpos($content, '</head>') - (strpos($content, '<head>'))));
                    preg_match('/<div class="tBd" id="table_2">(.*)<\/div>/si', $content, $value);
                    $con = $this->_strip_selected_tags($arr . $value[0], 'a');
                    _file_put_contents($v . '.html', $con);
                    $name_arr[] = $v . '.html';
                }
            }
            //发送邮件
            $email_arr = explode(',', $i['email']);
            foreach ($email_arr as $v) {
                _mail($v, $i['title'], $i['title'], $name_arr, array(
                    'drs_report@ppstream.com' => 'PPS_Drs报表中心'
                ));
            }
        }
        foreach ($name_arr as $v) {
            unlink($v);
        }
        if ($_GET['id'] > 0) {
            header("location:?act=show_notice&type=1");
        }

    }

    /**
     * @desc 发送接口预警
     * @author 聂雨薇 mailto:nyw@ppstream.com
     * @since  2013-02-27 17:24:30
     * @throws 注意:无DB异常处理
     */
    function P1S_SendApi()
    {
        //取出要发送的预警
        $conn_db = _ocilogon($this->db);
        $time = date('Y-n-j G') . ":00:00";
        $sql = "select * from {$this->notice_monitor_config} where receive_type=3";
        $stmt = _ociparse($conn_db, $sql);
        $error = _ociexecute($stmt);
        $arr = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            //发送接口
            if (date('i') % $_row['NOTICE_HZ'] == 0) {
                //取数据比对数据
                $sql = "select * from {$this->report_monitor_hour} where v1=:v1 and v2=:v2 and v3=:v3 and cal_date=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':v1', $_row['V1']);
                _ocibindbyname($stmt, ':v2', $_row['V2']);
                _ocibindbyname($stmt, ':v3', $_row['V3']);
                _ocibindbyname($stmt, ':cal_date', $time);
                $error = _ociexecute($stmt);
                $key = 'YWxhcnQud2Vic2NhY2hlLm5ldA';
                while (ocifetchinto($stmt, $_arr, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                    //预警接口发送
                    if ($_arr['FUN_COUNT'] > $_row['MAX_NUM'] || $_arr['FUN_COUNT'] < $_row['MIN_NUM']) {
                        $arr['sid'] = $_row['SID'];
                        $arr['status'] = 1;
                        if ($_arr['FUN_COUNT'] < $_row['MIN_NUM']) {
                            $msg = $_row['V1'] . '=>' . $_row['V2'] . '=>' . $_row['V3'] . ' 低于预警限制为' . $_arr['FUN_COUNT'];
                        } else {
                            $msg = $_row['V1'] . '=>' . $_row['V2'] . '=>' . $_row['V3'] . ' 超过预警限制为' . $_arr['FUN_COUNT'];
                        }
                        $arr['msg'] = iconv('gbk', 'utf-8', $msg);
                        $arr['time'] = time();
                        $arr['sign'] = substr(md5($key . $arr['time']), 0, 10);
                        $post_arr = json_encode($arr);
                        $url = "http://alart.webscache.net:1219/?name=warn&opt=put&data=" . $post_arr;
                        _curl($chinfo, $url);
                        _status(1, '报警体系', '报警', $_row['V3']);
                        //报警发送至邮箱
                        if ($_row['RECEIVE_VALUE'] != '') {
                            $email_arr = explode(',', $_row['RECEIVE_VALUE']);
                            $title = '接口报警';
                            foreach ($email_arr as $v) {
                                _mail($v, $title, $msg, '', array(
                                    'drs_report@ppstream.com' => 'PPS_Drs报表中心'
                                ));
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @desc 去除标签
     * @author 聂雨薇 mailto:nyw@ppstream.com
     * @since  2013-02-20 11:33:17
     * @throws 注意:无DB异常处理
     */
    function _strip_selected_tags($text, $tags = array())
    {
        $args = func_get_args();
        $text = array_shift($args);
        $tags = func_num_args() > 2 ? array_diff($args, array(
            $text
        )) : (array)$tags;
        foreach ($tags as $tag) {
            if (preg_match_all('/<' . $tag . '[^>]*>(.*)<\/' . $tag . '>/iU', $text, $found)) {
                $text = str_replace($found[0], $found[1], $text);
            }
        }
        return $text;
    }

    /**
     * * @desc 排版显示
     * * @author 聂雨薇 mailto:nyw@ppstream.com
     * * @since  2013-03-12 14:53:57
     * * @throws 注意:无DB异常处理
     * */
    function report_compare_monitor()
    {
        $conn_db = _ocilogon($this->db);
        $s1 = date('Y-m-d', strtotime("-1 month"));
        $s2 = date('Y-m-d');
        if ($_REQUEST['s1'])
            $s1 = $_REQUEST['s1'];
        if ($_REQUEST['s2'] && strtotime($_REQUEST['s2']) < time())
            $s2 = $_REQUEST['s2'];
        $start_date = $_REQUEST["start_date"] ? $_REQUEST["start_date"] : date("Y-m-d");
        //强制看今天的第一列
        if ($_COOKIE['direct_date'] == 'true' && !$_REQUEST["start_date"])
            $_REQUEST["start_date"] = $start_date;
        //时间乱传,不在范围之内
        if (strtotime($start_date) > strtotime($s2) || strtotime($start_date) < strtotime($s1))
            unset($start_date, $_REQUEST["start_date"]);
        $start_date1 = $start_date;

        $group_name_2 = '默认';
        $group_name = '默认';
        if ($_COOKIE[md5($_SERVER['SCRIPT_FILENAME']) . '_v1_group_name'])
            $group_name = $_COOKIE[md5($_SERVER['SCRIPT_FILENAME']) . '_v1_group_name'];
        //别名替换
        $sql = "select t.*,decode(as_name,null,v1,as_name) as_name1 from {$this->report_monitor_v1} t where id>0
        order by decode(as_name,null,v1,as_name)  ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);
        $v1_config_group = $this->v1_config = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if ($_REQUEST['type'] == $_row['V1']) {
                $group_name = $_row['GROUP_NAME'];
                $group_name_2 = $_row['GROUP_NAME_2'];
                $_COOKIE[md5($_SERVER['SCRIPT_FILENAME']) . '_v1_group_name'] = $group_name;
                setcookie(md5($_SERVER['SCRIPT_FILENAME']) . '_v1_group_name', $group_name);
            }
            $v1_config_group[$_row['GROUP_NAME_2']][$_row['GROUP_NAME']][$_row['V1']] = $_row;
            $this->v1_config_group[$_row['GROUP_NAME']] = $_row;
        }
        $this->v1_config = $v1_config_group[$group_name_2][$group_name];
        //偏差时差
        if ($this->v1_config[$_REQUEST['type']]['START_CLOCK'])
            $start_date1 = date('Y-m-d H:i:s', strtotime($start_date . " +{$this->v1_config[$_REQUEST['type']]['START_CLOCK']} hour"));
        //所有类型
        $sql = "select v1 from {$this->report_monitor_config} where id>0  group by v1 order by v1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $this->type = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $this->type[] = $_row['V1'];
            if (!$_REQUEST['type'] && $this->v1_config[$_row['V1']]) {
                $_REQUEST['type'] = $_row['V1'];
            }
        }

        //当前类型下面的所有模块
        $sql = "select t.* ,decode(as_name,null,v2,as_name) as_name1  from {$this->report_monitor_config} t where v1=:v1 and v2<>'汇总'
        order by orderby,as_name1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['type']);
        _ociexecute($stmt);
        $this->host = $_row = array();
        $this->v1_config[$_REQUEST['type']]['SHOW_ALL'] = 1;
        if ($this->v1_config[$_REQUEST['type']]['SHOW_ALL'])
            $this->host[] = array(
                'V1' => $_REQUEST['type'],
                'V2' => '汇总',
                'AS_NAME1' => '汇总'
            );
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $this->host[$_row['V2']] = $_row;
            if ($_REQUEST['host'] == $_row['V2'])
                $this->v_config = $_row;
        }
        if ($_COOKIE['direct_date'] == 'true' && !$_REQUEST["host"])
            $_REQUEST["host"] = $this->host[0]['V2'];

        //全部下级日统计数据
        $sql = "select t.*,to_char(cal_date, 'yyyy-mm-dd')  CAL_DATE_F  from
        {$this->report_monitor_date} t where
        cal_date>=to_date(:s1,'yyyy-mm-dd') and cal_date<=to_date(:s2,'yyyy-mm-dd')  and v1=:v1 and v2<>'汇总' ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['type']);
        _ocibindbyname($stmt, ':s1', $s1);
        _ocibindbyname($stmt, ':s2', $s2);
        $ocierror = _ociexecute($stmt);
        $this->all_start_date_all = $this->all_start_date_count = $this->all_start_date = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if (!$this->v1_config[$_REQUEST['type']]['START_CLOCK']) {
                $this->all_start_date_count[$_row['V2']]['total'] += $_row['FUN_COUNT'];
                $this->all_start_date_count[$_row['V2']]['total_i']++;
                $this->all_start_date_count[$_row['V2']]['total_avg'] = round($this->all_start_date_count[$_row['V2']]['total'] / $this->all_start_date_count[$_row['V2']]['total_i'], 2);
                $this->all_start_date_all[$_row['CAL_DATE_F']] += $_row['FUN_COUNT'];
                $this->all_start_date[$_row['CAL_DATE_F']][$_row['V2']] += $_row['FUN_COUNT'];

                if ($this->v1_config[$_REQUEST['type']]['SHOW_ALL']) {
                    $this->all_start_date_count['汇总']['total'] += $_row['FUN_COUNT'];
                    $this->all_start_date_count['汇总']['total_i']++;
                    $this->all_start_date_count['汇总']['total_avg'] = round($this->all_start_date_count['汇总']['total'] / $this->all_start_date_count['汇总']['total_i'], 2);
                    if ($this->v1_config[$_REQUEST['type']]['SHOW_AVG'] == 1)
                        $this->all_start_date[$_row['CAL_DATE_F']]['汇总'] = round($this->all_start_date_all[$_row['CAL_DATE_F']] / (count($this->all_start_date_count) - 1), 2);
                    else
                        $this->all_start_date[$_row['CAL_DATE_F']]['汇总'] += $_row['FUN_COUNT'];
                }
            }
            $this->all_start_date[$_row['CAL_DATE_F']]['LOOKUP'] = $_row['LOOKUP'];
        }
        //显示对比数据
        if ($this->host[$_row['V2']]['V2_COMPARE'] == 1) {
            $name = $_row['V2'] . '增量';
            $time = date('Y-m-d', strtotime($_row['CAL_DATE_F'] . " +1 day"));
            $this->all_start_date[$_row['CAL_DATE_F']][$name] = $this->all_start_date[$_row['CAL_DATE_F']][$_row['V2']] - $this->all_start_date[$time][$_row['V2']];
            $this->all_start_date_count[$name]['total'] += $this->all_start_date[$_row['CAL_DATE_F']][$name];
        }
        foreach ($this->all_start_date as $k => $v) {
            foreach ($v as $i => $c) {
                //显示对比数据
                if ($this->host[$i]['V2_COMPARE'] == 1) {
                    $name = $i . '增量';
                    $time = date('Y-m-d', strtotime($k . " -1 day"));
                    $this->all_start_date[$k][$name] = $this->all_start_date[$k][$i] - $this->all_start_date[$time][$i];
                    $this->all_start_date_count[$name]['total'] += $this->all_start_date[$k][$name];
                }
            }
        }
        //获取v2分组
        $sql = "select t.* ,decode(as_name,null,v2,as_name) as_name1  from {$this->report_monitor_config} t where v1=:v1 and v2<>'汇总'
                        order by orderby,as_name1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':v1', $_REQUEST['type']);
        _ociexecute($stmt);
        $this->group = array();
        if ($this->v1_config[$_REQUEST['type']]['SHOW_ALL'])
            $this->group['汇总'][] = array(
                'V1' => $_REQUEST['type'],
                'V2' => '汇总',
                'AS_NAME1' => '汇总'
            );
        $cospan = 1;
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if ($_row['V2_GROUP'] == '') {
                $_row['V2_GROUP'] = '其它';
            } else {
                $is_group = true;
            }
            $cospan++;
            $this->group[$_row['V2_GROUP']][] = $_row;
            //显示对比数据
            if ($_row['V2_COMPARE'] == 1) {
                $_row['AS_NAME1'] = $_row['AS_NAME1'] . '增量';
                $_row['V2'] = $_row['V2'] . '增量';
                $this->group[$_row['V2_GROUP']][] = $_row;
                $cospan++;
            }
        }
        //获取分组总数
        if ($is_group) {
            foreach ($this->group as $k => $v) {
                foreach ($v as $v2) {
                    foreach ($this->all_start_date_count as $c => $i) {
                        if ($c == $v2['V2']) {
                            $this->group_count[$k]['count'] += $i['total'];
                        }
                    }
                }
            }
        }
        //时区有偏差,改成从小时表读取数据
        if ($this->v1_config[$_REQUEST['type']]['START_CLOCK']) {
            $sql = "select v1,v2,sum(fun_count) fun_count, to_char(t.cal_date, 'yyyy-mm-dd hh24:mi:ss') as cal_date_f
                	from  {$this->report_monitor_hour} t
                    where cal_date>=to_date(:s1,'yyyy-mm-dd')+:diff/24 and cal_date<=to_date(:s2,'yyyy-mm-dd')+:diff/24
                    and v1=:v1
                    group by v1,v2,to_char(t.cal_date, 'yyyy-mm-dd hh24:mi:ss') ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':diff', $this->v1_config[$_REQUEST['type']]['START_CLOCK']);
            _ocibindbyname($stmt, ':s1', $s1);
            _ocibindbyname($stmt, ':s2', $s2);
            _ocibindbyname($stmt, ':v1', $_REQUEST['type']);
            $ocierror = _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                if (date('H', strtotime($_row['CAL_DATE_F'])) < $this->v1_config[$_REQUEST['type']]['START_CLOCK'])
                    $_row['CAL_DATE_F'] = date('Y-m-d', strtotime($_row['CAL_DATE_F'] . " -1 day"));
                else
                    $_row['CAL_DATE_F'] = date('Y-m-d', strtotime($_row['CAL_DATE_F']));

                if ($this->v1_config[$_REQUEST['type']]['SHOW_ALL'] == 1) {
                    $this->all_start_date_count['汇总'] += $_row['FUN_COUNT'];
                    $this->all_start_date[$_row['CAL_DATE_F']]['汇总'] += $_row['FUN_COUNT'];
                }

                $this->all_start_date_count[$_row['V2']] += $_row['FUN_COUNT'];
                $this->all_start_date[$_row['CAL_DATE_F']][$_row['V2']] += $_row['FUN_COUNT'];
                $this->all_start_date_all[$_row['CAL_DATE_F']] += $_row['FUN_COUNT'];
            }
        }
        //
        if ($start_date && $this->v1_config[$_REQUEST['type']]['SHOW_ALL'] && $_REQUEST['host'] == '汇总') {
            //当日数据
            $sql = "  select v2 as v3,sum(fun_count) fun_count,round(avg(fun_count),2) fun_count_avg,to_char(t.cal_date, 'dd hh24') as cal_date_f  from  {$this->report_monitor_hour} t
            where cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1
            and v1=:v1  and v2<>'汇总'
            group by v1,v2,to_char(t.cal_date, 'dd hh24')  ";
            $stmt2 = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt2, ':cal_date', $start_date1);
            _ocibindbyname($stmt2, ':v1', $_REQUEST['type']);
            $ocierror = _ociexecute($stmt2);
            print_r($ocierror);
            $this->fun_count = $this->fun_count2 = $_row = array();
            while (ocifetchinto($stmt2, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $this->fun_count[$_row['V3']]['AS_NAME1'] = $_row['V3'];
                foreach ($this->host as $k => $v)
                    if ($_row['V3'] == $v['V2']) {
                        $this->fun_count[$_row['V3']]['AS_NAME1'] = $v['AS_NAME1'];
                        break;
                    }
                if ($this->v1_config[$_REQUEST['type']]['SHOW_AVG'] == 1)
                    $_row['FUN_COUNT'] = $_row['FUN_COUNT_AVG'];
                $this->fun_count[$_row['V3']][$_row['CAL_DATE_F']] = $_row;
                $this->fun_count[$_row['V3']]['DIFF_TIME'] = max($this->fun_count[$_row['V3']]['DIFF_TIME'], $_row['DIFF_TIME']);
                if ($this->v1_config[$_REQUEST['type']]['SHOW_AVG'] == 1) {
                    $this->fun_count[$_row['V3']]['FUN_COUNT'] = $_row['FUN_COUNT_AVG'];
                } else
                    $this->fun_count[$_row['V3']]['FUN_COUNT'] += $_row['FUN_COUNT'];
                $this->fun_count[$_row['V3']]['FUN_COUNT_I'] += 1;
                $this->fun_count[$_row['V3']]['FUN_COUNT_AVG'] = round($this->fun_count[$_row['V3']]['FUN_COUNT'] / $this->fun_count[$_row['V3']]['FUN_COUNT_I'], 2);
                $this->fun_count2[$_row['CAL_DATE_F']] += $_row['FUN_COUNT'];
            }
            uasort($this->fun_count, create_function('$a,$b', 'if ($a["FUN_COUNT"] == $b["FUN_COUNT"]) return 0; return ($a["FUN_COUNT"]<$b["FUN_COUNT"]);'));
        } elseif ($start_date) {
            $this->pageObj = new page(10000, 300);
            //当日数据
            $sql = "{$this->pageObj->num_1} select v3,sum(fun_count) fun_count from  {$this->report_monitor_hour}
                where cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1
                and v1=:v1 and v2=:v2
                group by v1,v2,v3  {$this->pageObj->num_3} ";
            $stmt2 = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt2, ':cal_date', $start_date1);
            _ocibindbyname($stmt2, ':v1', $_REQUEST['type']);
            _ocibindbyname($stmt2, ':v2', $_REQUEST['host']);
            _ocibindbyname($stmt2, ':num_1', intval($this->pageObj->limit_1));
            _ocibindbyname($stmt2, ':num_3', intval($this->pageObj->limit_3));
            $ocierror = _ociexecute($stmt2);
            $this->fun_count = $this->fun_count2 = $_row2 = array();
            while (ocifetchinto($stmt2, $_row2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $sql = "select t.*,to_char(t.cal_date, 'dd hh24') as cal_date_f
                   from {$this->report_monitor_hour} t
                   where cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1
                   and v1=:v1 and v2=:v2 and v3=:v3   order by fun_count desc";
                $stmt = _ociparse($conn_db, $sql);
                _ocibindbyname($stmt, ':cal_date', $start_date1);
                _ocibindbyname($stmt, ':v1', $_REQUEST['type']);
                _ocibindbyname($stmt, ':v2', $_REQUEST['host']);
                _ocibindbyname($stmt, ':v3', $_row2['V3']);
                $ocierror = _ociexecute($stmt);
                $_row = array();
                while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                    $this->fun_count[$_row['V3']]['AS_NAME1'] = $_row['V3'];
                    $this->fun_count[$_row['V3']][$_row['CAL_DATE_F']] = $_row;
                    $this->fun_count[$_row['V3']]['DIFF_TIME'] = max($this->fun_count[$_row['V3']]['DIFF_TIME'], $_row['DIFF_TIME']);
                    $this->fun_count[$_row['V3']]['FUN_COUNT'] += $_row['FUN_COUNT'];
                    $this->fun_count[$_row['V3']]['FUN_COUNT_I'] += 1;
                    $this->fun_count[$_row['V3']]['FUN_COUNT_AVG'] = round($this->fun_count[$_row['V3']]['FUN_COUNT'] / $this->fun_count[$_row['V3']]['FUN_COUNT_I'], 2);
                    $this->fun_count2[$_row['CAL_DATE_F']] += $_row['FUN_COUNT'];
                    $this->fun_count3['FUN_COUNT_I']++;
                    $this->fun_count3['FUN_COUNT'] += $_row['FUN_COUNT'];
                    $this->fun_count3['FUN_COUNT_AVG'] = round($this->fun_count3['FUN_COUNT'] / $this->fun_count3['FUN_COUNT_I'], 2);
                }
            }
            uasort($this->fun_count, create_function('$a,$b', 'if ($a["FUN_COUNT"] == $b["FUN_COUNT"]) return 0; return ($a["FUN_COUNT"]<$b["FUN_COUNT"]);'));
        }

        // v1下所有v3的集合
        //当日数据
        $data = $_REQUEST['start_date'] ? $_REQUEST['start_date'] : date("Y-m-d", strtotime("-1 day"));
        $sql = "select v2,v3,sum(fun_count) fun_count,round(avg(fun_count),2) fun_count_avg from  {$this->report_monitor_hour}
                        where cal_date>=to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:cal_date,'yyyy-mm-dd hh24:mi:ss')+1
                        and v1=:v1
                        group by v1,v2,v3 ";
        $stmt2 = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt2, ':cal_date', $data);
        _ocibindbyname($stmt2, ':v1', $_REQUEST['type']);
        _ociexecute($stmt2);
        $this->fun_count = $this->fun_count2 = $_row2 = array();
        while (ocifetchinto($stmt2, $_row2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $this->v3_count[$_row2['V3']]['name'] = $_row2['V3'];
            $this->v3_count[$_row2['V3']][$_row2['V2']]['count'] = $_row2['FUN_COUNT'];
            $this->v3_count[$_row2['V3']][$_row2['V2']]['count_avg'] = $_row2['FUN_COUNT_AVG'];
            $this->v3_count[$_row2['V3']]['FUN_COUNT'] += $_row2['FUN_COUNT'];
            $this->v3_count[$_row2['V3']]['FUN_COUNT_AVG'] += $_row2['FUN_COUNT_AVG'];
        }
        uasort($this->v3_count, create_function('$a,$b', 'if ($a["FUN_COUNT"] == $b["FUN_COUNT"]) return 0; return ($a["FUN_COUNT"]<$b["FUN_COUNT"]);'));
        include "project/report_compare_monitor.html";
    }

    /**
     * * @desc W邮件配置修改
     * * @author 聂雨薇 mailto:nyw@ppstream.com
     * * @since  2013-03-21 16:56:20
     * * @throws 注意:无DB异常处理
     * */
    function notice_update()
    {
        $conn_db = _ocilogon($this->db);
        $sql = "select * from {$this->notice_monitor_config} where id=:id ";
        $stmt = _ociparse($conn_db, $sql);
        _ocibindbyname($stmt, ':id', $_GET['id']);
        _ociexecute($stmt);
        $this->_row = array();
        ocifetchinto($stmt, $this->_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $a_v2 = explode(',', $this->_row['V2']);
        $sql = "select decode(as_name,null,v1,as_name) as_name1 from {$this->report_monitor_v1} t where v1=:v1";
        $stmt = _ociparse($conn_db, $sql);
        ocibindbyname($stmt, ':v1', $_REQUEST['v1']);
        _ociexecute($stmt);
        $this->row_config = array();
        ocifetchinto($stmt, $this->row_config, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        include "project/notice_monitor_config.html";
    }

    /**
     * @desc php错误日志显示最后100条
     * @author 聂雨薇 mailto:nyw@ppstream.com
     * @since  2013-04-03 17:47:05
     * @throws 注意:无DB异常处理
     */
    function php_error_log()
    {
        exec("tail -n 100 /home/webid/logs/php_error.log", $mem);
        include "project/php_error.html";

    }

}

if ($myself) {
    $m = new m;
    $_GET['act'] = $_GET['act'] ? $_GET['act'] : "index";
    $m->$_GET['act']();
}

