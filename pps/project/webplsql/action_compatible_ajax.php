<?php
define('JSON_SUCCESS', 0);
define('JSON_FAILED', 1);
define('JSON_BAN', 3);

class Admin {
    function admin() {
        $this->rq_dt = $_REQUEST;
    }

    /**
     * @param $db_name
     *
     * @return MyOci
     */
    function initDb($db_name) {
        return DBFactory::getInstance($db_name);
    }

    function json($error, $msg = '', $data = array('nodata' => 1), $page = '') {

        $o = array(
            'error'=> $error,
            'success'=> $error ? false : true,
            'msg'=>$msg,
            'message'=>$msg,
            'data'=>$data,
        );

        if (is_array($page)) {
            $o['data'] = $page['data'];
            $o['c']    = $page['c'];
            $o['query_time']    = $page['query_time'];
        }
        header("Content-type:application/json");
        $s = json_encode(gbktoutf8($o));
        echo $s;
        die;
    }
}


class DBFactory {
    static private $instances = array();

    static function getInstances() {
        return self::$instances;
    }

    static function getInstance($db) {
        if (!isset(self::$instances[$db])) {
            static $db_obj = '';
            if (!is_object($db_obj)) {
                $db_obj = new oracleDB_config();
            }

            $db_configs = $db_obj->dbconfig;

            if (isset($db_configs[$db])) {
                try {
                    self::$instances[$db] = new MyOci($db);
                    return self::$instances[$db];
                } catch (Exception $e) {
                    print_r($e->getMessage());
                    exit;
                }
            } else {
                echo 'not found tns :' . $db;
                exit;
            }
        } else {
            return self::$instances[$db];
        }
    }
}


class MyOci {
    private $connect_time = 0;

    public function getConn() {
        return $this->conn;
    }

    public function getConnTime() {
        return $this->connect_time;
    }

    function __construct($db) {
        try {
            $start_time   = microtime(true);
            $this->conn   = _ocilogon($db);
            $end_time     = microtime(true);
            $connect_time = round($end_time - $start_time, 3);

            $this->connect_time = $connect_time;
        } catch (Exception $e) {
            print_r($e->getMessage());
            exit;
        }
    }

    public function lastInsertID($sequence_name) {
        $sql = "select " . $sequence_name . ".currval from dual";
        return $this->getOne($sql);
    }

    public function nextSeqID($sequence_name) {
        $sql = "select " . $sequence_name . ".nextval from dual";
        return $this->getOne($sql);
    }

    public function selectLimit($sql, $limit, $limit_from = 0, $params = array(), $driver_options = array()) {
        $limit_to = $limit_from + $limit;
        $sql      = "SELECT * FROM ( SELECT a.*, rownum r FROM ( " . $sql . " ) a WHERE rownum <= :limit_to ) b WHERE r > :limit_from";
        return $this->getAll($sql, array_merge($params, array('limit_to' => $limit_to, 'limit_from' => $limit_from)), $driver_options);
    }

    public function getAll($sql, $params = array()) {

        $query_result = _ociparse($this->conn, $sql);

        foreach ($params as $key => $value) {
            _ocibindbyname($query_result, $key, $value);
        }

        $ocierror = _ociexecute($query_result);
        $data     = array();
        if (!$ocierror) {
            while ($row = oci_fetch_assoc($query_result)) {
                $data[] = array_change_key_case($row, CASE_LOWER);
            }
        }

        if ($query_result) {
            oci_free_statement($query_result);
        }

        return $data;
    }

    public function getRow($sql, $params = array()) {
        $query_result = _ociparse($this->conn, $sql);
        foreach ($params as $key => $value) {
            _ocibindbyname($query_result, $key, $value);
        }
        $ocierror = _ociexecute($query_result);

        $data = array();
        if (!$ocierror) {
            $row  = oci_fetch_assoc($query_result);
            $data = array_change_key_case($row, CASE_LOWER);
        }

        if ($query_result) {
            oci_free_statement($query_result);
        }

        return $data;
    }

    public function getOne($sql, $params = array()) {

        $query_result = _ociparse($this->conn, $sql);
        foreach ($params as $key => $value) {
            _ocibindbyname($query_result, $key, $value);
        }
        $ocierror = _ociexecute($query_result);

        $data = array();
        if (!$ocierror) {
            $data = oci_fetch_array($query_result, OCI_NUM);
        }

        if ($query_result) {
            oci_free_statement($query_result);
        }

        return isset($data[0]) ? $data[0] : NULL;
    }

    public function execute($sql, $params = array()) {
        $query_result = _ociparse($this->conn, $sql);
        foreach ($params as $key => $value) {
            _ocibindbyname($query_result, $key, $value);
        }
        $ocierror = _ociexecute($query_result);
        if ($query_result) {
            oci_free_statement($query_result);
        }
        return !$ocierror;
    }

    public function errorInfo(){
        return oci_error($this->conn);
    }

    public function close($conn = NULL) {
        $conn = $conn === NULL ? $this->conn : $conn;
        _ocilogoff($conn);
    }
}