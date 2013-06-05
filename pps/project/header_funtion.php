<?php
if (PHP_VERSION < '5')
    include dirname(__FILE__) . "/header_php4.php";
ini_set('display_errors', false);
ini_set('date.timezone', 'PRC');
define('START_TIME', microtime(true));
if (strpos(PHP_OS, 'WIN') === false)
    define('VIP', trim(file_get_contents('/proc/sys/kernel/hostname')));
//�Ƿ���Ŀ�ļ�
define('GET_INCLUDED_FILES', $_SERVER['PHP_SELF'] . (isset($_GET['act']) ? '?act=' . $_GET['act'] : ''));

if (strpos(GET_INCLUDED_FILES, 'header_funtion.php') !== false || strpos(GET_INCLUDED_FILES, 'project') !== false || strpos(GET_INCLUDED_FILES, 'header.php') !== false)
    define('ADD_PROJECT', "[��Ŀ]");
else
    define('ADD_PROJECT', NULL);

//���ڷ���IP
if ($_SERVER['REMOTE_ADDR'] && (substr($_SERVER['REMOTE_ADDR'], 0, strpos($_SERVER['REMOTE_ADDR'], '.', 4)) == '192.168' || substr($_SERVER['REMOTE_ADDR'], 0, strpos($_SERVER['REMOTE_ADDR'], '.')) == '10') || substr($_SERVER['SERVER_ADDR'], 0, strrpos($_SERVER['SERVER_ADDR'], '.')) == substr($_SERVER['REMOTE_ADDR'], 0, strrpos($_SERVER['REMOTE_ADDR'], '.')) || strpos($_SERVER['REMOTE_ADDR'], '58.83.190.') === 0) {
    define('IP_NEI', $_SERVER['REMOTE_ADDR']);
}

define('VIMAGE', '/images/');
define('VIMAGE_PATH', './images/');
//ǩ��ģʽ.�ڸ������ݿ�(insert/update/delete)���ݵ�ʱ��,������ڳ����ж� SING==true
//��֤��ǰ�����ǲ���������Ա�վ�ĵ��&sign=<\?=SIGN_KEY?\>
define('SIGN_KEY', md5($_SERVER['REMOTE_ADDR'] . VHOST . '67yu^YHN'));
define('SIGN', SIGN_KEY == $_REQUEST['sign']);

//DOS��ʽ�µ�����
if ($_SERVER['argv'] && !$_SERVER['HTTP_HOST']) {
    $_COOKIE['faceid'] = NULL;
    $str_array = array();
    $str = join('&', $_SERVER['argv']);
    parse_str($str, $str_array);
    settype($str_array, 'array');
    settype($_GET, 'array');
    $_GET = $str_array + $_GET;
    $_UNLOGIN = true;
} else if (!$_COOKIE['faceid'] && !headers_sent())
    setcookie('faceid', $_COOKIE['faceid'] = md5(microtime(true)), time() + 315360000, '/', VHOST, false);

/**
 * ���˵�һЩ�Ƿ��ύ���ƻ�ҳ�������,POST,GET
 * Created on 2009-6-24 by Administrator
 *
 */
$get_magic_quotes_gpc = get_magic_quotes_gpc();
settype($_COOKIE, "array");
settype($_POST, "array");
settype($_GET, "array");
if ($get_magic_quotes_gpc) {
    foreach ($_COOKIE as $k => $v)
        if (is_string($v))
            $_COOKIE[$k] = stripslashes($v);
    foreach ($_POST as $k => $v)
        if (is_string($v))
            $_POST[$k] = stripslashes($v);
    foreach ($_GET as $k => $v)
        if (is_string($v))
            $_GET[$k] = stripslashes($v);
    foreach ($_SERVER as $k => $v)
        if (is_string($v))
            $_SERVER[$k] = stripslashes($v);
}
$_REQUEST = $_POST + $_GET;

/**
 * @desc WHAT? $uptype=replace/utf-8
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-06-22 20:14:54
 * @throws ע��:��DB�쳣����
 */
function _status($num, $v1, $v2, $v3, $v4 = null, $v5 = VIP, $diff_time = 0, $uptype = null, $time = null)
{
    if (strpos(PHP_OS, 'WIN') === false && function_exists('msg_get_queue')) {
        if (!$time)
            $time = time();
        if ($v5 == 'KPI') {
            $IPCS = explode('|', IPCS_KPI);
            $includes = array();
        } else {
            $IPCS = explode('|', IPCS);
            //$includes = get_included_files();
            $includes = array();
        }
        $ipcs_key = $IPCS[rand(0, count($IPCS) - 1)];
        $seg = msg_get_queue($ipcs_key, 0600);
        if ($seg) {
            if ($v5 == VIP)
                $v5 = NULL;
            list($_uptype, $code) = explode('/', $uptype);
            $array = array(
                'vhost' => VHOST,
                'includes' => $includes,
                'num' => $num,
                #����ֵ
                'v1' => $v1,
                #�����
                'v2' => $v2,
                #С����
                'v3' => $v3,
                #��Ҫͳ������
                'v4' => $v4,
                #����ĵ�������
                'v5' => $v5,
                #���ӵ�ַ
                'diff_time' => $diff_time,
                'time' => date('Y-m-d H:i:s', $time),
                'uptype' => $_uptype
            );
            if ($code)
                $array = utf8togbk($array);
            if ($seg) {
                $bool = msg_send($seg, 1, $array, true, false);
                if (!$bool) {
                    error_log("���д���:" . str_pad(dechex($ipcs_key), 8, '0', STR_PAD_LEFT));
                }
            }
        }
    }
}

register_shutdown_function('_php_runtime');

/**
 * @desc ����ű�ִ��ʱ�䣬�Զ���
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since 2012-03-23 14:50:13
 * @throws ��DB�쳣����
 */
function _php_runtime()
{
    if (strpos(PHP_OS, 'WIN') === false && _php_runtime == true) {
        $diff_time = sprintf('%.5f', microtime(true) - START_TIME);
        $get_included_files_2 = get_included_files();

        //�����ļ��������
        foreach ($get_included_files_2 as $k => $v)
            if (strpos($v, '/phpCas/') !== false || strpos($v, '/_end.php') !== false)
                unset($get_included_files_2[$k]);
        $get_included_files_count = count($get_included_files_2);
        if ($get_included_files_count < 10) {
            $diff_time_str = $get_included_files_count . "��";
        } else {
            $diff_time_str = "10s���޸�";
        }
        _status($get_included_files_count, VHOST . "(�����ļ�)" . ADD_PROJECT, $diff_time_str, GET_INCLUDED_FILES, var_export($get_included_files_2, true), VIP, $diff_time);


        $is_html = (bool)strpos(array_pop($get_included_files_2), '.html');
        if (!$is_html)
            $is_html = (bool)strpos(array_pop($get_included_files_2), '.html');

        if (PHP_VERSION > '5.2') {
            $e = error_get_last();
            if (strpos($e['message'], 'Call to undefined') !== false && $_SERVER['REMOTE_ADDR'] <> '180.168.136.230')
                return _status(1, VHOST . "(BUG����)", 'δ���庯��', GET_INCLUDED_FILES, "userIP:{$_SERVER['REMOTE_ADDR']}@referfer:{$_SERVER['HTTP_REFERER']}|" . var_export($e, true) . "|" . var_export($_REQUEST, true) . "|" . var_export($_COOKIE, true), VIP, $diff_time);
            else if ($e['type'] == E_ERROR)
                return _status(1, VHOST . "(BUG����)", 'PHP����' . ADD_PROJECT, GET_INCLUDED_FILES, "userIP:{$_SERVER['REMOTE_ADDR']}@referfer:{$_SERVER['HTTP_REFERER']}|" . var_export($e, true) . "|" . var_export($_REQUEST, true) . "|" . var_export($_COOKIE, true), VIP, $diff_time);
        }

        $diff_time_str = _debugtime($diff_time);
        if ($_SERVER['HTTP_HOST'] && $_SERVER['REMOTE_ADDR'] != '127.0.0.1' && !ADD_PROJECT)
            _status(1, VHOST . '(����Ч��BUG)', $diff_time_str, GET_INCLUDED_FILES, IP_NEI . "(HOST:{$_SERVER['HTTP_HOST']})", VIP, $diff_time);

        //��������IPͳ��
        if (!$_SERVER['HTTP_HOST'] || $_SERVER['REMOTE_ADDR'] == '127.0.0.1')
            _status(1, VHOST . "(����ִ��)", "��ʱ" . ADD_PROJECT, GET_INCLUDED_FILES, IP_NEI . "(HOST:{$_SERVER['HTTP_HOST']})", VIP, $diff_time);
        else if (defined('IP_NEI'))
            _status(1, VHOST . "(����ִ��)", "�����ӿ�" . ADD_PROJECT, GET_INCLUDED_FILES, IP_NEI . "(HOST:{$_SERVER['HTTP_HOST']})", VIP, $diff_time);
        else if ($is_html) {
            _status(1, VHOST . "(����ִ��)", "ҳ�����" . ADD_PROJECT, GET_INCLUDED_FILES, IP_NEI . "(HOST:{$_SERVER['HTTP_HOST']})", VIP, $diff_time);
        } else {
            _status(1, VHOST . "(����ִ��)", "��������" . ADD_PROJECT, GET_INCLUDED_FILES, IP_NEI . "(HOST:{$_SERVER['HTTP_HOST']})", VIP, $diff_time);
        }
    }
}

//XIALINTAI ������:2012-03-31 10:29:36
set_error_handler("_myErrorHandler");

/**
 * @desc �ӹ�PHP���쳣������Ϣ,�Ե����к�̨����
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-04-02 09:50:31
 * @throws ��DB�쳣����
 */
function _myErrorHandler($errno, $errstr, $errfile, $errline)
{
    switch ($errno) {
        case E_NOTICE:
        case E_USER_ERROR:
        case E_USER_NOTICE:
        case E_STRICT:
            return;
    }
    if ($errstr == 'Division by zero')
        return;
    if ($errstr == 'Invalid argument supplied for foreach()')
        return;
    if (strpos($errstr, 'current()') === 0)
        return;
    if (strpos($errstr, 'next()') === 0)
        return;
    if (strpos($errstr, 'ftp_mkdir()') === 0)
        return;
    if (strpos($_GET['act'], 'monitor') === 0 && strpos($errstr, 'msg_send') !== false)
        return;
    //�򿪴����ʱ��,ҳ��ֱ����ʾ������Ϣ
    if (ini_get("display_errors"))
        echo "<b>$errno, $errstr, $errfile, $errline</b>";
    if (strpos($errstr, 'oci') === 0 || strpos($errstr, 'mysql_') === 0)
        _status(1, VHOST . '(BUG����)', "SQL����" . ADD_PROJECT, GET_INCLUDED_FILES, "(line:{$errline}){$errstr}", VIP);
    elseif (strpos($errstr, 'Memcache') === 0)
        _status(1, VHOST . '(BUG����)', "Memcache����", GET_INCLUDED_FILES, "(line:{$errline}){$errstr}", VIP); else
        _status(1, VHOST . '(BUG����)', "PHP����" . ADD_PROJECT, GET_INCLUDED_FILES, "(line:{$errline}){$errstr}", VIP);
}

/**
 * @desc what?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-06-20 18:30:44
 * @throws ע��:��DB�쳣����
 */
function _debugtime($diff_time = 0)
{
    if ($diff_time < 0.01)
        $diff_time_str = "0.00s��0.01s";
    elseif ($diff_time < 0.02)
        $diff_time_str = "0.01s��0.02s"; elseif ($diff_time < 0.03)
        $diff_time_str = "0.02s��0.03s"; elseif ($diff_time < 0.04)
        $diff_time_str = "0.03s��0.04s"; elseif ($diff_time < 0.05)
        $diff_time_str = "0.04s��0.05s"; elseif ($diff_time < 0.1)
        $diff_time_str = "0.05s��0.1s"; elseif ($diff_time < 0.5)
        $diff_time_str = "0.1s��0.5s"; elseif ($diff_time < 1)
        $diff_time_str = "0.5s��1s"; elseif ($diff_time < 5)
        $diff_time_str = "1s��5s"; elseif ($diff_time < 10)
        $diff_time_str = "5s��10s"; else
        $diff_time_str = "10s������";
    return $diff_time_str;
}

/**
 * @desc �������ݿ�
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-06-20 18:30:44
 * @throws ע��:��DB�쳣����
 */
function _mysqllogon($DB)
{
    if (!$DB)
        return null;
    $_SERVER['mysql_oci_sql_ociexecute'] = $_SERVER['oci_sql_ociexecute'];
    $oracleDB_config = new oracleDB_config;

    $dbconfig = $oracleDB_config->dbconfig;
    $DBS = explode('|', $DB);
    $DB = $DBS[time() % count($DBS)];
    $dbconfiginterface = $dbconfig[$DB];
    if (!$dbconfiginterface) {
        _status(1, VHOST . '(BUG����)', "���ݿ����Ӵ���", "δ�������ݿ�:" . $DB, GET_INCLUDED_FILES, VIP);
        return null;
    }
    $conn_db = mysql_connect($dbconfiginterface['TNS'], $dbconfiginterface['user_name'], $dbconfiginterface['password'], true);
    if (!is_resource($conn_db)) {
        _status(1, VHOST . '(BUG����)', "���ݿ����Ӵ���", $DB . '@' . mysql_error($conn_db), GET_INCLUDED_FILES, VIP);
        return null;
    }
    _status(1, VHOST . '(���ݿ�����MySQL)' . ADD_PROJECT, $DB, GET_INCLUDED_FILES, NULL, VIP);
    $bool = mysql_select_db($dbconfiginterface['db'], $conn_db);
    if (!$bool)
        _status(1, VHOST . '(BUG����)', "���ݿ����Ӵ���", $DB . '@' . mysql_error($conn_db), GET_INCLUDED_FILES, VIP);
    //����ʹ��Mysql��һ����utf-8
    mysql_query("SET NAMES 'utf8'");

    $_SERVER['last_mysql_link'][$conn_db] = $DB;
    return $conn_db;
}

/**
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-04-02 22:32:01
 * @throws ע��:��DB�쳣����
 */
function _mysqlparse(&$conn_db, $sql)
{
    $_SERVER['last_mysql_conn'] = $_SERVER['last_mysql_link'][$conn_db];
    return array(
        '$conn_db' => $conn_db,
        '$sql' => $sql
    );
}

/**
 * @desc �޸�mysql�İ��ַ�
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-04-02 22:29:42
 * @throws ע��:��DB�쳣����
 */
function _mysqlbindbyname($stmt, $key, $value, $int = false)
{
    settype($_SERVER['last_mysql_bindname'], 'Array');
    if (!$int)
        $_SERVER['last_mysql_bindname'] += array(
            $key => $value === null ? 'null' : "'" . mysql_real_escape_string($value) . "'"
        );
    else
        $_SERVER['last_mysql_bindname'] += array(
            $key => $value === null ? '0' : (int)mysql_real_escape_string($value)
        );
}

/**
 * @desc �޸�mysql�İ��ַ�
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-04-02 22:29:42
 * @throws ע��:��DB�쳣����
 */
function _mysqlbindbyname2($stmt, $key, $value, $int = false)
{
    settype($_SERVER['last_mysql_bindname'], 'Array');
    if (!$int)
        $_SERVER['last_mysql_bindname'] += array(
            $key => $value === null ? "''" : "'" . mysql_escape_string($value) . "'"
        );
    else
        $_SERVER['last_mysql_bindname'] += array(
            $key => $value === null ? '0' : (int)mysql_escape_string($value)
        );
}

/**
 * @desc ����һ��SQL����Ӧ��ѯ�ı�����
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2013-05-29 15:55:46
 * @throws ע��:��DB�쳣����
 */
function _sql_table_txt($sql)
{
    $sql_out = array();
    $sql = strtr($sql, array(
            "\n" => ' ',
            "\r" => " "
        )) . " ";
    preg_match_all('# from\s+([^ ]+) #iUs', $sql . " ", $sql_out);
    foreach ($sql_out[1] as $v) {
        if (strpos($v, '(') === false)
            break;
    }
    if (!$v) {
        $sql_out = array();
        preg_match('#update\s+([^ ]+)\s(.*)set #iUs', $sql . " ", $sql_out);
        $v = $sql_out[1];
    }
    if (!$v) {
        $sql_out = array();
        preg_match('#into\s+([^ ]+)[\s|\(]#iUs', $sql . " ", $sql_out);
        $v = $sql_out[1];
    }
    if (!$v) {
        $sql_out = array();
        preg_match('#table\s+([^ ]+) #iUs', $sql . " ", $sql_out);
        $v = $sql_out[1];
    }
    if (!$v) {
        $sql_out = array();
        preg_match('#begin\s+(.*)\(#iUS', $sql . " ", $sql_out);
        $v = "Procedure:" . $sql_out[1];
    }
    return trim($v);
}

/**
 * @desc ִ��SQL���
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-04-02 22:29:12
 * @throws ע��:��DB�쳣����
 */
function _mysqlexecute(&$stmt)
{
    $conn_db = $stmt['$conn_db'];
    $_SERVER['last_mysql_sql'] = strtr($stmt['$sql'], array(
        "\n" => ' ',
        "\r" => " "
    ));
    settype($_SERVER['last_mysql_bindname'], 'Array');
    $sql = strtr($stmt['$sql'], $_SERVER['last_mysql_bindname'] + array(
            'sysdate' => 'now()',
            'SYSDATE' => 'now()'
        ));

    $t1 = microtime(true);
    $stmt = mysql_query($sql, $conn_db);
    $diff_time = sprintf('%.5f', microtime(true) - $t1);

    //����뺯������
    $v = _sql_table_txt($_SERVER['last_mysql_sql']);

    $sql_type = '(��)';
    $last_oci_sql = $_SERVER['last_mysql_sql'];
    $out = array();
    preg_match('# in(\s+)?\(#is', $last_oci_sql, $out);
    if ($out) {
        $last_oci_sql = substr($last_oci_sql, 0, stripos($last_oci_sql, ' in')) . ' in....';
        _status(1, VHOST . '(����MySQL)', "IN�﷨" . ADD_PROJECT, "{$_SERVER['last_mysql_conn']}@" . GET_INCLUDED_FILES, "{$last_oci_sql}", VIP, $diff_time);
    }
    if (stripos($_SERVER['last_mysql_sql'], 'select ') !== false)
        $sql_type = '(��)';
    elseif (stripos($_SERVER['last_mysql_sql'], 'insert ') !== false)
        $sql_type = '(д)'; elseif (stripos($_SERVER['last_mysql_sql'], 'update ') !== false)
        $sql_type = '(��)'; elseif (stripos($_SERVER['last_mysql_sql'], 'delete ') !== false || stripos($_SERVER['last_mysql_sql'], 'truncate ') !== false)
        $sql_type = '(ɾ)';
    _status(1, VHOST . '(MySQLͳ��)', "{$_SERVER['last_mysql_conn']}{$sql_type}", strtolower($v) . "@" . GET_INCLUDED_FILES, $last_oci_sql, VIP, $diff_time);

    $diff_time_str = _debugtime($diff_time);
    _status(1, VHOST . '(MySQLЧ��BUG)' . ADD_PROJECT, $diff_time_str, "{$_SERVER['last_mysql_conn']}." . strtolower($v) . "@" . GET_INCLUDED_FILES, $_SERVER['last_mysql_sql'], VIP, $diff_time);

    $ocierror = mysql_error($conn_db);
    if ($ocierror)
        _status(1, VHOST . "(BUG����)", 'SQL����' . ADD_PROJECT, GET_INCLUDED_FILES, var_export($ocierror, true) . "|" . var_export($_GET, true) . "|" . $_SERVER['last_mysql_sql'], VIP, $diff_time);

    //����ϴε�����
    $_SERVER['last_mysql_bindname'] = array();
    return $ocierror;
}

/**
 * @desc WHAT?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2013-01-29 14:57:50
 * @throws ע��:��DB�쳣����
 */
function _mysqlclose(&$conn_db)
{
    if ($conn_db) {
        mysql_close($conn_db);
        $DB = $_SERVER['last_mysql_link'][$conn_db];
        _status(1, VHOST . '(���ݿ�����MySQL)', $DB . "[�ر�]" . ADD_PROJECT, GET_INCLUDED_FILES, NULL, VIP);
    }

    if ($_SERVER['mysql_oci_sql_ociexecute'] <> $_SERVER['oci_sql_ociexecute'])
        _status(1, VHOST . '(Mysqlʹ�ô���)', "�������ݿ�����", "{$DB}", GET_INCLUDED_FILES);
}

/**
 * @desc �������ݿ�
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-06-20 18:30:44
 * @throws ע��:��DB�쳣����
 */
function _ocilogon($DB)
{
    if (!$DB)
        return null;
    $oracleDB_config = new oracleDB_config;

    $dbconfig = $oracleDB_config->dbconfig;
    $DBS = explode('|', $DB);
    $DB = $DBS[time() % count($DBS)];
    $dbconfiginterface = $dbconfig[$DB];
    if (!$dbconfiginterface) {
        _status(1, VHOST . '(BUG����)', "���ݿ����Ӵ���", "δ�������ݿ�:" . $DB, GET_INCLUDED_FILES, VIP);
        return null;
    }
    $conn_db = ocinlogon($dbconfiginterface['user_name'], $dbconfiginterface['password'], $dbconfiginterface['TNS']);
    if (!is_resource($conn_db)) {
        $err = ocierror();
        _status(1, VHOST . '(BUG����)', "���ݿ����Ӵ���", $DB . '@' . $err['message'], GET_INCLUDED_FILES, VIP);
        return null;
    }
    _status(1, VHOST . '(���ݿ�����)' . ADD_PROJECT, $DB, GET_INCLUDED_FILES, NULL, VIP);
    $_SERVER['last_oci_link'][$conn_db] = $DB;
    return $conn_db;
}

/**
 * @desc �󶨲�ѯ���
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-04-02 09:51:16
 * @param string $db_conn ���ݿ�����
 * @param string $sql SQL���
 * @return resource $stmt
 * @throws ��DB�쳣����
 */
function _ociparse($conn_db, $sql)
{
    $_SERVER['last_db_conn'] = $_SERVER['last_oci_link'][$conn_db];
    //SQL���ܷ���׼��,��ʱ�����SQL���������
    if (is_writable('/dev/shm/') && $_SERVER['last_oci_sql'] <> $sql && !((!$_SERVER['HTTP_HOST'] || $_SERVER['REMOTE_ADDR'] == '127.0.0.1'))) {
        $out = array();
        preg_match('# in(\s+)?\(#is', $sql, $out);
        if (!$out) {
            $get_included_files = $_SERVER['PHP_SELF'];
            $basefile = '/dev/shm/sql_' . VHOST;
            if (is_writable($basefile))
                $sqls = unserialize(_file_get_contents($basefile));
            else
                $sqls = array();
            $sign = md5($_SERVER['last_db_conn'] . $sql);
            if (count($sqls) < 100 && !$sqls[$sign]) {
                $sqls[$sign] = array(
                    'sql' => $sql,
                    'add_time' => date('Y-m-d H:i:s'),
                    'db' => $_SERVER['last_db_conn'],
                    'type' => 'oci',
                    'vhost' => VHOST,
                    'act' => "{$get_included_files}/{$_REQUEST['act']}"
                );
                _file_put_contents($basefile, serialize($sqls));
            }
        }
    }
    $_SERVER['last_oci_sql'] = $sql;
    return ociparse($conn_db, $sql);
}

/**
 * @desc WHAT?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-11-25 17:33:09
 * @throws ע��:��DB�쳣����
 */
function _ocibindbyname($stmt, $key, $value)
{
    settype($_SERVER['last_oci_bindname'], 'Array');
    $_SERVER['last_oci_bindname'][$key] = $value;
    ocibindbyname($stmt, $key, $value);
}

/**
 * @desc ִ��SQL��ѯ���
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-04-02 09:53:56
 * @param resource $stmt ���ݿ�����Դ
 * @return resource $error ������Ϣ
 * @throws ��DB�쳣����
 */
function _ociexecute($stmt, $mode = OCI_COMMIT_ON_SUCCESS)
{
    $ADD_PROJECT = ADD_PROJECT;
    if (PROJECT_SQL === true)
        $ADD_PROJECT = '[��Ŀ]';
    $_SERVER['oci_sql_ociexecute']++;
    $t1 = microtime(true);
    ociexecute($stmt, $mode);
    $diff_time = sprintf('%.5f', microtime(true) - $t1);

    //����뺯������
    $v = _sql_table_txt($_SERVER['last_oci_sql']);
    $sql_type = '(��)';
    $last_oci_sql = $_SERVER['last_oci_sql'];
    $out = array();
    preg_match('# in(\s+)?\(#is', $last_oci_sql, $out);
    if ($out) {
        $last_oci_sql = substr($last_oci_sql, 0, stripos($last_oci_sql, ' in')) . ' in....';
        _status(1, VHOST . '(����SQL)', "IN�﷨" . $ADD_PROJECT, "{$_SERVER['last_db_conn']}@" . GET_INCLUDED_FILES . "/{$_REQUEST['act']}", "{$last_oci_sql}", VIP, $diff_time);
    }

    if (stripos($_SERVER['last_oci_sql'], 'select ') !== false)
        $sql_type = '(��)';
    elseif (stripos($_SERVER['last_oci_sql'], 'insert ') !== false)
        $sql_type = '(д)'; elseif (stripos($_SERVER['last_oci_sql'], 'update ') !== false)
        $sql_type = '(��)'; elseif (stripos($_SERVER['last_oci_sql'], 'delete ') !== false || stripos($_SERVER['last_oci_sql'], 'truncate ') !== false)
        $sql_type = '(ɾ)';
    _status(1, VHOST . '(SQLͳ��)' . $ADD_PROJECT, "{$_SERVER['last_db_conn']}{$sql_type}", strtolower($v) . "@" . GET_INCLUDED_FILES, $last_oci_sql, VIP, $diff_time);

    $diff_time_str = _debugtime($diff_time);
    _status(1, VHOST . '(SQLЧ��BUG)' . $ADD_PROJECT, $diff_time_str, "{$_SERVER['last_db_conn']}." . strtolower($v) . "@" . GET_INCLUDED_FILES, $_SERVER['last_oci_sql'], VIP, $diff_time);

    $ocierror = ocierror($stmt);
    if ($ocierror) {
        $debug_backtrace = debug_backtrace();
        array_walk($debug_backtrace, create_function('&$v,$k', 'unset($v["function"],$v["args"]);'));
        _status(1, VHOST . "(BUG����)", "SQL����" . $ADD_PROJECT, GET_INCLUDED_FILES, var_export($ocierror, true) . '|' . var_export($_SERVER['last_oci_bindname'], true) . "|" . var_export($_GET, true) . "|" . $last_oci_sql . "|" . var_export($debug_backtrace, true), VIP, $diff_time);
    }

    $_SERVER['last_oci_bindname'] = array();
    return $ocierror;
}

/**
 * @desc �ر����ݿ�����
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-06-20 18:30:44
 * @throws ע��:��DB�쳣����
 */
function _ocilogoff(&$conn_db)
{
    if ($conn_db) {
        ocilogoff($conn_db);
        $DB = $_SERVER['last_oci_link'][$conn_db];
        _status(1, VHOST . '(���ݿ�����)', $DB . "[�ر�]" . ADD_PROJECT, GET_INCLUDED_FILES, NULL, VIP);
    }
}

/**
 * @desc SQL���ܷ���,���ڽӿ�
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-12-01 20:42:35
 * @throws ע��:��DB�쳣����
 */
if ($_REQUEST['act'] == '_ociexplain' && !$_SERVER['HTTP_HOST'])
    _ociexplain();

function _ociexplain()
{
    if (is_writable('/dev/shm/')) {
        $change = false;
        $basefile = '/dev/shm/sql_' . VHOST;
        $sqls = unserialize(file_get_contents($basefile));
        if (empty($sqls)) {
            echo "empty sqls\n";
            $change = true;
        }
        echo "sql_count:" . count($sqls) . "\n";
        foreach ($sqls as $k => $v) {
            if ($v['type'] <> 'oci' || $v['paser_txt'] || $v['vhost'] <> VHOST)
                continue;
            if (strpos($v['sql'], 'alter session') !== false)
                continue;

            $conn_db = _ocilogon($v['db']);
            $sql = "EXPLAIN PLAN SET STATEMENT_ID='pps' FOR " . $v['sql'];
            $stmt = ociparse($conn_db, $sql);
            ociexecute($stmt);

            $sql = "SELECT * FROM TABLE(DBMS_XPLAN.DISPLAY('PLAN_TABLE','pps','BASIC'))";
            $stmt = ociparse($conn_db, $sql);
            ociexecute($stmt);
            $_row = array();
            $row_text = NULL;
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                echo "change:explain\n";
                $change = true;
                $row_text .= "\n" . $_row['PLAN_TABLE_OUTPUT'];
            }
            _ocilogoff($conn_db);
            $sqls[$k]['paser_txt'] = $row_text;
            $vv = _sql_table_txt($v['sql']);
            //
            $type = NULL;
            if (strpos($v['act'], 'project') !== false)
                $type = "(��Ŀ)";
            if (strpos($row_text, 'TABLE ACCESS FULL') !== false)
                _status(1, VHOST . "(����SQL)", "ȫ��ɨ��{$type}", "{$v['db']}.{$vv}@{$v['act']}", $v['sql'] . "\n" . $row_text, VIP, 0);
            if (strpos($row_text, ' JOIN ') !== false)
                _status(1, VHOST . "(����SQL)", "����ѯ{$type}", "{$v['db']}.{$vv}@{$v['act']}", $v['sql'] . "\n" . $row_text, VIP, 0);
        }
        foreach ($sqls as $k => $v) {
            if (time() > strtotime($v['add_time']) + 3600) {
                echo "change:time\n";
                $change = true;
                unset($sqls[$k]);
            }
        }
        if ($change) {
            echo "write file.\n";
            file_put_contents($basefile, serialize($sqls));
        }
        die("OK\n");
    }
}

/**
 * @desc ��¼��Щҳ�治Ӧ�ñ����ʵ�
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-10-18 14:20:14
 * @throws ע��:��DB�쳣����
 */
function _err404()
{
    if (strpos($_SERVER['REMOTE_ADDR'], '180.')===false)
        _status(1, VHOST . "(BUG����)", "ҳ��404", $_SERVER['REQUEST_URI'], $_SERVER['REMOTE_ADDR'] . '@host:' . $_SERVER['HTTP_HOST'] . '@referer:' . $_SERVER['HTTP_REFERER'], VHOST, 0);
    header("HTTP/1.0 404 Not Found");
    echo "<!-- ERR URI:" . htmlspecialchars($_SERVER['REQUEST_URI']) . "@" . date('Y-m-d H:i:s') . " -->";
    die;
}

/**
 * @desc WHAT?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-06-17 23:04:10
 * @throws ע��:��Db�쳣����
 */
function _curl(&$chinfo, $url, $post_data = null, $config = array(), $upload_file = array())
{
    settype($config, 'array');
    $ch = curl_init();
    $chinfo = array();
    if (substr($url, 0, 5) == 'https') {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Expect:',
        'User-Agent:Mozilla/5.0 (Windows NT 5.1; rv:2.0) Gecko/20100101 Firefox/4.0',
        "Referer:{$url}"
    ));
    foreach ($config as $k => $v)
        curl_setopt($ch, $k, $v);
    if ($post_data) {
        if (function_exists('http_build_query'))
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        else
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    if ($upload_file)
        curl_setopt($ch, CURLOPT_POSTFIELDS, (array)$upload_file + (array)$post_data);

    $curl_error_tmp = $curl_error = NULL;
    $total_time = $i = 0;
    while (!$chinfo['http_code'] && $i <= 3) {
        $curl_data = curl_exec($ch);
        $curl_error_tmp = curl_error($ch);
        if ($curl_error_tmp)
            $curl_error = $curl_error_tmp;
        $chinfo = curl_getinfo($ch);
        $i++;
        $total_time += $chinfo['total_time'];
    }
    curl_close($ch);
    $chinfo['total_time'] = $total_time;

    $url_arr = parse_url($url);
    $url_arr_list = explode('.', $url_arr['host']);
    $url_arr_list_str = $url_arr_list[count($url_arr_list) - 2] . '.' . $url_arr_list[count($url_arr_list) - 1];
    //
    if ($chinfo['http_code'] != '200' && $chinfo['http_code'][0] != '3')
        _status(1, VHOST . '(BUG����)', "��ַץȡ", "{$url_arr['host']}{$url_arr['path']}({$chinfo['http_code']})err:" . $curl_error, GET_INCLUDED_FILES, VIP, $total_time);
    else
        _status(1, VHOST . '(��ַץȡ)', $url_arr_list_str, "{$url_arr['host']}/{$url_arr['path']} ({$chinfo['http_code']})", GET_INCLUDED_FILES, VIP, $total_time);
    //��ʱ�����¼
    $diff_time_str = _debugtime($total_time);
    _status(1, VHOST . '(�ӿ�Ч��)', $diff_time_str, $url_arr['host'] . "{$url_arr['path']} ({$chinfo['http_code']})" . $curl_error, GET_INCLUDED_FILES, VIP, $total_time);
    return $curl_data;
}

/**
 * @desc WHAT?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-07-23 17:08:27
 * @throws ע��:��DB�쳣����
 * _ftp('iosfile','/ftp/dir/','/home/httpd/ios/xxx.rar')   -> ftp:/ftp/dir/xxx.rar
 */
function _ftp($configName, $dir, $file)
{
    $ftp_config = new ftp_config;
    $interfaceConfig = $ftp_config->config[$configName];
    if (!$interfaceConfig)
        return false;
    //����FTP
    $t1 = microtime(true);
    $bool = $connRes = ftp_connect($interfaceConfig['host']);
    $diff_time = sprintf('%.5f', microtime(true) - $t1);
    if ($diff_time > 3)
        _status(1, VHOST . '(FTPЧ��BUG)', 'FTP��ʱ(����)', "{$configName}" . "@" . GET_INCLUDED_FILES, NULL, VIP, $diff_time);

    _status(1, VHOST . '(FTP)', $interfaceConfig['host'], GET_INCLUDED_FILES, NULL, VIP, $diff_time);

    $t1 = microtime(true);
    $bool = ftp_login($connRes, $interfaceConfig['user_name'], $interfaceConfig['user_pass']);
    if (!$bool)
        return false;
    if ($interfaceConfig['dir'] <> '/')
        $bool = ftp_chdir($connRes, $interfaceConfig['dir']);

    $dir_array = explode('/', $dir);
    if (count($dir_array)) {
        foreach ($dir_array as $v) {
            if (!$v)
                continue;
            ftp_mkdir($connRes, $v);
            ftp_chdir($connRes, $v);
        }
    }

    $diff_time = sprintf('%.5f', microtime(true) - $t1);
    if ($diff_time > 3)
        _status(1, VHOST . '(FTPЧ��BUG)', 'FTP��ʱ(��¼�л�Ŀ¼)', "{$configName}" . "@" . GET_INCLUDED_FILES, NULL, VIP, $diff_time);

    $t1 = microtime(true);
    //�ϴ��ļ�
    ftp_pasv($connRes, true);
    $bool = ftp_put($connRes, basename($file), $file, FTP_BINARY);
    $diff_time = sprintf('%.5f', microtime(true) - $t1);
    if ($diff_time > 3)
        _status(1, VHOST . '(FTPЧ��BUG)', 'FTP��ʱ(�ϴ�)', "{$configName}" . "@" . GET_INCLUDED_FILES, NULL, VIP, $diff_time);
    return ftp_close($connRes);
}

/**
 * @desc WHAT?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2013-01-07 11:47:28
 * @throws ע��:��DB�쳣����
 * �÷�: _mail("resia@dev.ppstream.com", "imagae Email", 'Embedded Image: <img alt="PHPMailer" src="cid:phpmailer.gif"> Here is an image!', array(
'PHPMailer/examples/images/phpmailer.gif' ), array('resia@dev.ppstream.com'=>'xltxlm') );
 */
function _mail($to, $subject, $body, $Attachments = array(), $from = array())
{
    include_once('PHPMailer/class.phpmailer.php');
    $mail = new PHPMailer(); // defaults to using php "mail()"
    if ($from) {
        list($from_mail, $from_name) = each($from);
        $mail->SetFrom($from_mail, $from_name);
    } else {
        $mail->SetFrom('root@dev-web05.ppstream.com', 'Root');
    }
    $mail->AddAddress($to);
    $mail->Subject = $subject;
    $mail->CharSet = "GBK";
    $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

    $mail->MsgHTML($body);
    foreach ($Attachments as $Attachment) {
        if (strpos($Attachment, '.png') !== false || strpos($Attachment, '.jpg') !== false || strpos($Attachment, '.gif') !== false)
            $mail->AddEmbeddedImage($Attachment, basename($Attachment), basename($Attachment));
        else
            $mail->AddAttachment($Attachment); // attachment
    }

    $mail->PreSend();
    $header = $mail->CreateHeader();
    $body = $mail->CreateBody();
    $bool = mail($to, $subject, $body, $header);
    _status(1, VHOST . '(�ʼ�ϵͳ)', '�����ʼ�', $to, $subject, VIP);
    if (!$bool)
        _status(1, VHOST . '(�ʼ�����)', '�����ʼ�ʧ��', $to, $subject, VIP);
    return $bool;
}

/**
 * @desc WHAT?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2013-01-07 13:14:33
 * @throws ע��:��DB�쳣����
 * $data=array('name'=>array('2013-1-7 10:9:3'=>10.2))
 * $config=����������Ϣ
 * $filename=���ɵ�ͼƬ����
 * 'linetype' => 'AREA/LINE2'
 */
function _rrd($title = 'test rrd image', $data = array(), $config = array(
    'start' => 'Unix_time',
    'end' => 'Unix_time',
    'step' => 300,
    'linetype' => 'AREA',
    'width' => '800',
    'height' => '180',
    'times' => 60
), $filename = NULL)
{
    $config = $config + array(
            'start' => 'Unix_time',
            'end' => 'Unix_time',
            'step' => 300,
            'linetype' => 'AREA',
            'width' => '800',
            'height' => '180',
            'times' => 60
        );

    $color = array(
        '#9E6BA3',
        '#FCD20A',
        '#2795EB',
        '#CCCCCC',
        '#3E3E3E',
        '#1E1E1E'
    );
    if (!$filename)
        $filename = "a.png";
    //������������
    $lines_key = array_keys($data);
    $i = 0;
    $rrd_name = md5($title . ".rrd");
    foreach ($lines_key as $k => $v) {
        $lines[$v] = "DS:myline{$i}:GAUGE:{$config['step']}:0:100000000000 ";
        $lines_DEF[$v] = "DEF:myline{$i}={$rrd_name}:myline{$i}:AVERAGE ";
        $line_line[$v] = "{$config['linetype']}:myline{$i}{$color[$i]}:'{$v}' GPRINT:myline{$i}:MIN:%13.2lf  GPRINT:myline{$i}:MAX:%13.2lf  " . 'COMMENT:"   \n" ';
        $i++;
    }

    $lines_str = join("  ", $lines);
    $exec = "/usr/local/rrdtool/bin/rrdtool create {$rrd_name} --step {$config['step']} --start {$config['start']}-{$config['step']}  {$lines_str}  RRA:AVERAGE:0.5:1:14400     RRA:AVERAGE:0.5:6:4800   RRA:AVERAGE:0.5:24:1200   RRA:AVERAGE:0.5:288:600";
    exec($exec, $err);
    _status(1, VHOST . "(RRD��ͼ����)", "1.create", GET_INCLUDED_FILES . '@' . var_export($err, true));
    //print_r($err);
    //д����������
    $last = $lines_data = array();
    for ($i = $config['start']; $i <= $config['end']; $i += $config['times']) {
        $cal_date = date('Y-m-d H:i:s', $i);
        foreach ($lines_key as $vv) {
            $lines_data[$cal_date][$vv] = round($data[$vv][$cal_date], 0);
            if ($lines_data[$cal_date][$vv]) {
                $last[$vv] = array(
                    'time' => $cal_date,
                    'num' => $lines_data[$cal_date][$vv]
                );
            } else {
                if ($last[$vv] && $i - strtotime($last[$vv]['time']) < 60 * 10)
                    $lines_data[$cal_date][$vv] = $last[$vv]['num'];
            }
        }
    }
    foreach ($lines_data as $cal_date => $v) {
        $unix_time = strtotime($cal_date);
        $v_str = join(":", $v);
        $exec = "/usr/local/rrdtool/bin/rrdtool update {$rrd_name} {$unix_time}:{$v_str}";
        exec($exec, $err);
        _status(1, VHOST . "(RRD��ͼ����)", "2.update", GET_INCLUDED_FILES . '@' . var_export($err, true));
        //print_r($err);
    }
    $lines_DEF_str = join(" ", $lines_DEF);

    $cal_date_start = date('Y-m-d H_i_s', $config['start']);
    $cal_date_end = date('Y-m-d H_i_s', $config['end']);
    $line_line_str = 'COMMENT:\'   \n\'' . " COMMENT:'                                                         dateline {$cal_date_start}-{$cal_date_end}'  " . ' COMMENT:\'   \n\'' . ' COMMENT:\'   \n\' ' . join(" ", $line_line);
    //��ͼ
    $exec = iconv('gbk', 'utf-8', "/usr/local/rrdtool/bin/rrdtool graph  {$filename}  --font DEFAULT:10:'project/msyh.ttf'  -Y --width {$config['width']} --height {$config['height']} --color=BACK#CCCCCC --color=CANVAS#CCFFFF --color=SHADEB#9999CC  --title \"{$title}\"  --vertical-label ' Bits Per Second'  --start {$config['start']} --end {$config['end']}-{$config['step']}  {$lines_DEF_str}  {$line_line_str}    ");
    exec($exec, $err);
    _status(1, VHOST . "(RRD��ͼ����)", "3.graph", GET_INCLUDED_FILES . '@' . var_export($err, true));
    unlink($rrd_name);
}

/**
 * @desc WHAT?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2013-02-01 10:41:07
 * @throws ע��:��DB�쳣����
 */
function _file_get_contents($filename)
{
    $tt1 = microtime(true);
    $data = file_get_contents($filename);
    $diff_time = sprintf('%.5f', microtime(true) - $tt1);
    _status(1, VHOST . '(�ļ�ϵͳ��д)' . ADD_PROJECT, date('H'), $filename, GET_INCLUDED_FILES, VIP, $diff_time);
    return $data;
}

/**
 * @desc WHAT?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2013-02-01 10:41:07
 * @throws ע��:��DB�쳣����
 */
function _file_put_contents($filename, $data)
{
    $tt1 = microtime(true);
    $int = file_put_contents($filename, $data);
    $diff_time = sprintf('%.5f', microtime(true) - $tt1);
    _status(1, VHOST . '(�ļ�ϵͳ��д)' . ADD_PROJECT, date('H'), $filename, GET_INCLUDED_FILES, VIP, $diff_time);
    return $int;
}

/**
 * Class file@desc WHAT?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2013-04-01 13:44:14
 * @throws ע��:��DB�쳣����
 */

function _mini_image($file_old, $file_new, $config = array())
{
    $fileObj = new  file;
    $thumb_bg = $fileObj->_cut_image($file_old, $config);
    return imagejpeg($thumb_bg, $file_new, 75);
}

/**
 * @param $data
 * @param $key
 * @param $encodeing
 * @author ���� mailto:fifsky@dev.ppstream.com
 */
function _iconv(&$data, $key, $encodeing)
{
    if (function_exists('mb_convert_encoding')) {
        if ((!empty($data) && !is_numeric($data)) || (strpos(VHOST, 'pt.pps.tv') !== false || strpos(VHOST, 'izhushou.cn') !== false))
            $data = mb_convert_encoding($data, $encodeing[1], $encodeing[0]);
    } else
        if ((!empty($data) && !is_numeric($data)) || (strpos(VHOST, 'pt.pps.tv') !== false || strpos(VHOST, 'izhushou.cn') !== false))
            $data = iconv($encodeing[0], $encodeing[1], $data);
}

/**
 * @param $data
 *
 * @return array|object|string
 * @author ���� mailto:fifsky@dev.ppstream.com
 */
function gbktoutf8($data)
{
    if (is_array($data)) {
        array_walk_recursive($data, '_iconv', array('gbk', 'utf-8'));
    } elseif (is_object($data)) {
        array_walk_recursive(get_object_vars($data), '_iconv', array('gbk', 'utf-8'));
    } else {
        _iconv($data, NULL, array('gbk', 'utf-8'));
    }
    return $data;
}

/**
 * @param $data
 *
 * @return array|object|string
 * @author ���� mailto:fifsky@dev.ppstream.com
 */
function utf8togbk($data)
{
    if (is_array($data)) {
        array_walk_recursive($data, '_iconv', array('utf-8', 'gbk'));
    } elseif (is_object($data)) {
        array_walk_recursive(get_object_vars($data), '_iconv', array('utf-8', 'gbk'));
    } else {
        _iconv($data, NULL, array('utf-8', 'gbk'));
    }
    return $data;
}

//�ϴ�,�ļ���������
class file
{

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-07-16 11:21:04
     * @throws ע��:��DB�쳣����
     */
    function _cut_image($filepath, $config = array())
    {
        list($width, $height, $type, $attr) = getimagesize($filepath);
        $source = null;
        if ($type == 1)
            $source = imagecreatefromgif($filepath);
        if ($type == 2)
            $source = imagecreatefromjpeg($filepath);
        if ($type == 3)
            $source = imagecreatefrompng($filepath);
        if ($type == 6) {
            include_once "proect/bmp.php";
            $source = imagecreatefrombmp($filepath);
        }

        if (!$source)
            return null;

        if (isset($config['width']) && isset($config['height'])) {
            $new_width = $config['width'];
            $new_height = $config['height'];
            $thumb_bg = imagecreatetruecolor($new_width, $new_height);

            $percent = $new_width / $new_height;
            if ($width / $height > $percent) {
                $height2 = ($new_height / $new_width) * $width;
                $thumb_bg2 = imagecreatetruecolor($width, $height2);
                $fff = imagecolorallocate($thumb_bg2, 255, 255, 255);
                imagefill($thumb_bg2, 0, 0, $fff);
                $q_h = ($height2 - $height) / 2;
                imagecopyresampled($thumb_bg2, $source, 0, $q_h, 0, 0, $width, $height, $width, $height);
                // Resize
                imagecopyresampled($thumb_bg, $thumb_bg2, 0, 0, 0, 0, $new_width, $new_height, $width, $height2);
            } else {
                $width2 = ($new_width / $new_height) * $height;
                $thumb_bg2 = imagecreatetruecolor($width2, $height);
                $fff = imagecolorallocate($thumb_bg2, 255, 255, 255);
                imagefill($thumb_bg2, 0, 0, $fff);
                $q_h = ($width2 - $width) / 2;
                imagecopyresampled($thumb_bg2, $source, $q_h, 0, 0, 0, $width, $height, $width, $height);
                // Resize
                imagecopyresampled($thumb_bg, $thumb_bg2, 0, 0, 0, 0, $new_width, $new_height, $width2, $height);
            }
        } elseif ($config['width']) {
            $new_width = $config['width'];
            $new_height = $height * ($config['width'] / $width);
            $thumb_bg = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($thumb_bg, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        } elseif ($config['height']) {
            $new_width = $width * ($config['height'] / $height);
            $new_height = $config['height'];
            $thumb_bg = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($thumb_bg, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        } else {
            $new_width = $width;
            $new_height = $height;
            $thumb_bg = $source;
            if ($config['maxheight']) {
                $new_height = $config['maxheight'];
                $new_width = $width / $height * $new_height;
                $thumb_bg = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($thumb_bg, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            }
            if ($config['maxwidth']) {
                $new_width = $config['maxwidth'];
                $new_height = $height / $width * $new_width;
                $thumb_bg = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($thumb_bg, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            }
        }
        //�ٴβü�,�޶�����߶�
        if ($config['maxheight'] && $new_height > $config['maxheight']) {
            $source = $thumb_bg;
            $thumb_bg = imagecreatetruecolor($new_width, $config['maxheight']);
            $fff = imagecolorallocate($thumb_bg, 255, 255, 255);
            imagefill($thumb_bg, 0, 0, $fff);
            $new_width2 = $new_width / $new_height * $config['maxheight'];

            $thumb_bg2 = imagecreatetruecolor($new_width2, $config['maxheight']);
            $fff = imagecolorallocate($thumb_bg2, 255, 255, 255);
            imagefill($thumb_bg2, 0, 0, $fff);
            //�ȱ�����ͼ
            imagecopyresampled($thumb_bg2, $source, 0, 0, 0, 0, $new_width2, $config['maxheight'], $new_width, $new_height);
            $q_w = ($new_width - $new_width2) / 2;
            imagecopyresampled($thumb_bg, $thumb_bg2, $q_w, 0, 0, 0, $new_width2, $config['maxheight'], $new_width2, $config['maxheight']);
        }
        if ($config['maxwidth'] && $new_width > $config['maxwidth']) {
            $source = $thumb_bg;
            $thumb_bg = imagecreatetruecolor($config['maxwidth'], $new_height);
            $fff = imagecolorallocate($thumb_bg, 255, 255, 255);
            imagefill($thumb_bg, 0, 0, $fff);

            $new_height2 = $new_height / $new_width * $config['maxwidth'];
            $thumb_bg2 = imagecreatetruecolor($config['maxwidth'], $new_height2);
            $fff = imagecolorallocate($thumb_bg2, 255, 255, 255);
            imagefill($thumb_bg2, 0, 0, $fff);

            //�ȱ�����ͼ
            imagecopyresampled($thumb_bg2, $source, 0, 0, 0, 0, $config['maxwidth'], $new_height2, $new_width, $new_height);
            $q_h = ($new_height - $new_height2) / 2;
            imagecopyresampled($thumb_bg, $thumb_bg2, 0, $q_h, 0, 0, $config['maxwidth'], $new_height2, $config['maxwidth'], $new_height2);
        }

        return $thumb_bg;
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-07-05 16:56:01
     * @throws ע��:��DB�쳣����
     * @param $config['width']���տ������,$config['height']���ո߶�����,������,���ձ����ü�������
     */
    function fetchimg($url, $filename = null, $config = array())
    {
        if (!$filename)
            $filename = md5($url);
        $imageData = _curl($chinfo, $url);
        if ($imageData) {
            $date = date('Y-m') . '/';
            $path = VIMAGE_PATH . $date;
            if (!is_dir($path))
                mkdir($path);
            $filepath = $path . $filename . ".jpg";

            $bool = file_put_contents($filepath, $imageData);
            if ($config) {
                $thumb_bg = $this->_cut_image($filepath, $config);
                $bool = imagejpeg($thumb_bg, $filepath, 100);
            }
            list($width, $height, $type, $attr) = getimagesize($filepath);

            if ($bool)
                return array(
                    'url' => VIMAGE . $date . $filename . '.jpg',
                    'width' => $width,
                    'height' => $height
                );
        }
        return null;
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-07-05 16:34:43
     * @throws ע��:��DB�쳣����
     * @param $config['width']���տ������,$config['height']���ո߶�����,������,���ձ����ü�������,$config['maxheight']���ĸ߶�,$config['maxwidth']=420�����
     */
    function uploadimg($filepath, $filename, $config = array())
    {
        $date = date('Y-m') . '/';
        $path = VIMAGE_PATH . $date;
        if (!is_dir($path))
            mkdir($path);
        $file = $path . $filename . '.jpg';

        if ($config) {
            $thumb_bg = $this->_cut_image($filepath, $config);
            $bool = imagejpeg($thumb_bg, $file, 100);
        } else
            $bool = copy($filepath, $file);
        list($width, $height, $type, $attr) = getimagesize($file);
        if ($bool)
            return array(
                'url' => VIMAGE . $date . $filename . '.jpg',
                'width' => $width,
                'height' => $height
            );
        else
            return null;
    }

}

//class memcache_server2 extends memcache_server
//{
//
//    /**
//     * @desc ��λ������
//     * @author ����̩ mailto:resia@dev.ppstream.com
//     * @since  2013-01-30 15:33:26
//     * @throws ע��:��DB�쳣����
//     */
//    function _key_connect($key = null)
//    {
//        $this->current_host = $this->config[$key % count($this->config)];
//    }
//
//}
// ���÷�ʽ: $memcache_server = new memcache_server('74');
class memcache_server
{

    //�����ļ�
    var $config = array();
    var $current_host = null;
    var $memcacheObj = null;
    var $start_time = null;
    var $db_link_count = 0;

    /**
     * @desc memcache������ֵ�����㷨.
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-04-02 09:58:12
     * @param Array $config Memcache��������
     * @return Object
     * @throws ��DB�쳣����
     */
    function memcache_server($config = array())
    {
        $this->db_link_count = $_SERVER['oci_sql_ociexecute'];
        $this->start_time = microtime(true);
        if (is_array($config))
            $this->config = $config;
        else {
            $memcache_config = new memcache_config;
            $this->config = $memcache_config->config[$config];
        }
    }

    /**
     * @desc ��λ������,������Զ�����㷨, ÿ������,����Ҫnewһ���µ�memcache����
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2013-01-30 15:33:26
     * @throws ע��:��DB�쳣����
     */
    function _key_connect($key = null)
    {
        $key = (string)$key;
        $hashCode = 0;
        for ($i = 0, $len = min(100, strlen($key)); $i < $len; $i++)
            $hashCode = (int)(($hashCode * 33) + ord($key[$i])) & 0x7fffffff;
        $this->current_host = $this->config[$hashCode % count($this->config)];
        if (!$this->current_host)
            _status(1, VHOST . '(Memcahe����)', "û���е�ǰ����", GET_INCLUDED_FILES, var_export(debug_backtrace(), true), VIP);
    }

    /**
     * @desc ����KEY��ѡ�����ݿ�
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-04-02 09:58:12
     * @throws ��DB�쳣����
     */
    function connect($key = NULL)
    {
        if (!$key) {
            _status(1, VHOST . '(Memcahe����)', "û�д�KEY", GET_INCLUDED_FILES, var_export(debug_backtrace(), true), VIP);
            return null;
        }
        if (!$this->config) {
            _status(1, VHOST . '(Memcahe����)', "�����ļ�Ϊ��", GET_INCLUDED_FILES, var_export(debug_backtrace(), true), VIP);
            return null;
        }
        $this->_key_connect($key);
        $this->memcacheObj = $_SERVER['memcache_server']["{$this->current_host['host']}:{$this->current_host['port']}"];
        //����֮ǰ����ʷ��¼
        if (!is_object($this->memcacheObj)) {
            $memcache = new Memcache;
            $t1 = microtime(true);
            $bool = $memcache->connect($this->current_host['host'], $this->current_host['port']);
            if (!$bool)
                $bool = $memcache->connect($this->current_host['host'], $this->current_host['port']);
            $diff_time = sprintf('%.5f', microtime(true) - $t1);

            $diff_time_str = _debugtime($diff_time);
            _status(1, VHOST . '(Memcahe����Ч��)', $diff_time_str, "{$this->current_host['host']}:{$this->current_host['port']}(connect)", GET_INCLUDED_FILES, VIP, $diff_time);
            _status(1, VHOST . '(Memcahe����)', "{$this->current_host['host']}:{$this->current_host['port']}[��]", GET_INCLUDED_FILES, NULL, VIP);
            if (!$bool)
                _status(1, VHOST . '(BUG����)', "Memcahe���Ӵ���", "{$this->current_host['host']}:{$this->current_host['port']}", GET_INCLUDED_FILES, VIP);
            $this->memcacheObj = $_SERVER['memcache_server']["{$this->current_host['host']}:{$this->current_host['port']}"] = & $memcache;
            $_SERVER['memcache_server_connect']++;
        }
        return $this->memcacheObj;
    }

    /**
     * @desc ��ȡ����
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-04-02 09:58:12
     * @throws ��DB�쳣����
     */
    function get($key = NULL)
    {
        if (get_class($this) == 'memcache_server')
            $this->connect($key);
        if ($this->memcacheObj) {
            $t1 = microtime(true);
            $bool = $this->memcacheObj->get($key);
            $diff_time = sprintf('%.5f', microtime(true) - $t1);

            _status(1, VHOST . '(Memcache)', "{$this->current_host['host']}:{$this->current_host['port']}(get)", GET_INCLUDED_FILES, var_export((bool)$bool, true), VIP, $diff_time);
            $diff_time_str = _debugtime($diff_time);
            _status(1, VHOST . '(MemcaheЧ��BUG)', $diff_time_str, "{$this->current_host['host']}:{$this->current_host['port']}(get)", GET_INCLUDED_FILES, VIP, $diff_time);
            return $bool;
        }
        return false;
    }

    /**
     * @desc д���޸�����
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-04-02 09:58:12
     * @throws ��DB�쳣����
     */
    function set($key = NULL, $var = null, $flag = MEMCACHE_COMPRESSED, $expire = 0)
    {
        if (get_class($this) == 'memcache_server')
            $this->connect($key);
        if ($this->memcacheObj) {
            $t1 = microtime(true);
            $bool = $this->memcacheObj->set($key, $var, $flag, $expire);
            $diff_time = sprintf('%.5f', microtime(true) - $t1);

            _status(1, VHOST . '(Memcache)', "{$this->current_host['host']}:{$this->current_host['port']}(set)", GET_INCLUDED_FILES, NULL, VIP, $diff_time);
            $diff_time_str = _debugtime($diff_time);
            _status(1, VHOST . '(MemcaheЧ��BUG)', $diff_time_str, "{$this->current_host['host']}:{$this->current_host['port']}(set)", GET_INCLUDED_FILES, VIP, $diff_time);
            return $bool;
        }
        return false;
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-11-29 18:13:14
     * @throws ע��:��DB�쳣����
     */
    function delete($key)
    {
        if (get_class($this) == 'memcache_server')
            $this->connect($key);
        if ($this->memcacheObj) {
            $t1 = microtime(true);
            $bool = $this->memcacheObj->delete($key, 0);
            $diff_time = sprintf('%.5f', microtime(true) - $t1);

            _status(1, VHOST . '(Memcache)', "{$this->current_host['host']}:{$this->current_host['port']}(delete)", GET_INCLUDED_FILES, NULL, VIP, $diff_time);
            $diff_time_str = _debugtime($diff_time);
            _status(1, VHOST . '(MemcaheЧ��BUG)', $diff_time_str, "{$this->current_host['host']}:{$this->current_host['port']}(delete)", GET_INCLUDED_FILES, VIP, $diff_time);
            return $bool;
        }
        return false;
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2013-03-14 21:03:28
     * @throws ע��:��DB�쳣����
     */
    function increment($key, $num = 1, $flag = MEMCACHE_COMPRESSED, $expire = 0)
    {
        if (get_class($this) == 'memcache_server')
            $this->connect($key);
        if ($this->memcacheObj) {
            $t1 = microtime(true);
            $bool = $this->memcacheObj->increment($key, $num);
            if ($bool === false) {
                //����ʧ��,����Ϊ֮ǰkey����,ɾ��֮��,������ر������ٴ����ӻ�ȥ
                $this->memcacheObj->delete($key, 0);
                $this->close();
                $this->connect($key);
                $this->memcacheObj->set($key, 0, $flag, $expire);
                $bool = $this->memcacheObj->increment($key, $num);
            }
            $diff_time = sprintf('%.5f', microtime(true) - $t1);

            _status(1, VHOST . '(Memcache)', "{$this->current_host['host']}:{$this->current_host['port']}(increment)", GET_INCLUDED_FILES, NULL, VIP, $diff_time);
            $diff_time_str = _debugtime($diff_time);
            _status(1, VHOST . '(MemcaheЧ��BUG)', $diff_time_str, "{$this->current_host['host']}:{$this->current_host['port']}(increment)", GET_INCLUDED_FILES, VIP, $diff_time);
            return $bool;
        }
        return false;
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-07-03 16:08:37
     * @throws ע��:��DB�쳣����
     */
    function close()
    {
        if (is_object($this->memcacheObj) && method_exists($this->memcacheObj, 'close')) {
            $this->memcacheObj->close();
            _status(1, VHOST . '(Memcahe����)', "{$this->current_host['host']}:{$this->current_host['port']}[�ر�]", GET_INCLUDED_FILES, NULL, VIP);
            $_SERVER['memcache_server']["{$this->current_host['host']}:{$this->current_host['port']}"] = $this->memcacheObj = null;
            unset($this->memcacheObj, $_SERVER['memcache_server']["{$this->current_host['host']}:{$this->current_host['port']}"]);
        }
        $diff_time = sprintf('%.5f', microtime(true) - $this->start_time);
        $diff_time_str = _debugtime($diff_time);
        _status(1, VHOST . '(Memcahe�����ʱ)', $diff_time_str, "{$this->current_host['host']}:{$this->current_host['port']}", GET_INCLUDED_FILES, VIP, $diff_time);
        if ($this->db_link_count <> $_SERVER['oci_sql_ociexecute'])
            _status(1, VHOST . '(Memcacheʹ�ô���)', "�������ݿ�����", "{$this->current_host['host']}:{$this->current_host['port']}", GET_INCLUDED_FILES, VIP, $diff_time);
    }

}

/**
 * @desc WHAT?
 * @author ����̩ mailto:resia@dev.ppstream.com
 * @since  2012-06-16 12:11:22
 * @throws ע��:��DB�쳣����
 */
function _p($pageID, $is_page = true, $pagefirst = null)
{
    static $page_tp, $page_first;
    if ($is_page) {
        if ($pageID < 2) {
            return $page_first;
        } else
            return str_replace('{p}', $pageID, $page_tp);
    } else {
        $page_tp = $pageID;
        $page_first = $pagefirst;
    }
}

if (!in_array(VHOST, array('pay2.pps.tv')) || strpos(GET_INCLUDED_FILES, 'project') !== false) {
    class page
    {

        var $limit_1 = 0;
        var $limit_2 = 0;
        var $num_1 = "select t_page_1.* from (select rownum rn ,t_page_0.* from (\n";
        var $num_3 = "\n) t_page_0 where rownum <= :num_3) t_page_1 where rn >:num_1 ";

        /**
         * @desc  ��ҳ����
         * @author ����̩ mailto:resia@dev.ppstream.com
         * @since  2012-04-02 09:58:12
         * @throws ��DB�쳣����
         */
        function page($total = 0, $everpage = 10, $query = array())
        {
            $this->total = $this->totalItems = $total;
            $this->everpage = $everpage;
            $this->pages = max(1, abs(ceil(($total / $everpage))));
            $this->currentPage = max((int)$_GET['pageID'], 1); //2
            $num = ceil(3 / 2);
            $this->max = MIN(MAX($this->currentPage + $num, 3), $this->pages);
            $this->min = MAX(MIN($this->currentPage - $num, $this->pages - 3), 1);

            $this->limit_1 = ($this->currentPage - 1) * $this->everpage;
            $this->limit_2 = $this->everpage;
            $this->limit_3 = $this->currentPage * $this->everpage;

            if (strpos(VHOST, 'bk.pps.tv') !== false || strpos(VHOST, '.c.pps.tv') !== false) {
                $this->query_string = "";
                if (!$query)
                    $query = $_REQUEST;
                //
                unset($query['pageID']);
                settype($query, 'array');
                $this->query = array();
                foreach ($query as $k => $v)
                    $this->query[] = $k . '=' . urldecode($v);
                $this->query_string = join('&', $this->query);
            }
        }

        /**
         * @desc  ��ҳ��ʾ
         * @author ����̩ mailto:resia@dev.ppstream.com
         * @since  2012-04-02 09:58:12
         * @throws ��DB�쳣����
         */
        function show($tp = 'm/page.standard.html')
        {
            include $tp;
        }

    }
}
