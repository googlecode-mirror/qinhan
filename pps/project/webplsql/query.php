<?php
/*
* 用json格式列出所有数据库名字。不分页。
* @author			王钟凯 (Kevin)
* @E-mail			328725540@qq.com kevin@dev.ppstream.com
* @version			1.0
*/

class WebPlSqlQuery extends Admin
{
    function exec()
    {
        set_time_limit(300);
        $sql = isset($this->rq_dt['sql']) ? trim($this->rq_dt['sql']) : '';
        $db_name = isset($this->rq_dt['db_name']) ? trim($this->rq_dt['db_name']) : '';
        $sql = iconv('utf-8', 'gbk', $sql);

        $flag_type = $this->filter($db_name, $sql);

        if (empty($sql) || empty($db_name)) {
            $this->json(JSON_FAILED, '参数不完整');
        } else {
            $sql = preg_replace('/\;$/', '', $sql);
            $this->query($flag_type, $db_name, $sql);
        }


    }

    function post()
    {
        $this->exec();
    }

    function filter(&$db_name, &$sql)
    {
        if (preg_match('/^select/i', $sql)) {
            $flag_type = 'read_flag';
        } else if (preg_match('/^update/i', $sql) || preg_match('/^insert/i', $sql)) {
            $flag_type = 'write_flag';
        } else if (preg_match('/^delete/i', $sql)) {
            $flag_type = 'delete_flag';
            $this->json(JSON_FAILED, '执行此语句，您需要' . $db_name . ' 的' . $flag_type . '权限!');
        } else {
            $this->json(JSON_FAILED, '您的SQL语句必须是select , update ,insert ,delete 中的一种');
        }

        //更新
        if ($flag_type == 'read_flag') {
            $reg = '/^select\s+(.+?)\s+from\s+([a-z0-9_]+)\s+(where.*?\s+|)for\s+update$/i';
            $a = array();
            $r = preg_match_all($reg, $sql, $a);
            if ($r) {
                $col = @$a[1][0];
                $table = @$a[2][0];
                $where = @$a[3][0];
                if ($col == '*') {
                    $col = 't.*';
                    $table = $table . ' t';
                }
                $col .= ',rowidtochar(rowid) as db_row_id';
                $sql = "select $col from $table $where";
            }
        }

        return $flag_type;
    }

    function query($flag_type, $db_name, $sql)
    {
        $db = $this->initDb($db_name);
        try{
            $db->execute("ALTER SESSION SET NLS_DATE_FORMAT='yyyy-mm-dd hh24:mi:ss'");
            //$db = $this->db;
            if ($flag_type == 'read_flag') {
                $start = isset($this->rq_dt['start']) ? trim($this->rq_dt['start']) : 0;
                $limit = isset($this->rq_dt['limit']) ? trim($this->rq_dt['limit']) : 25;
                $query_start_time = microtime(1);
                $r = $db->selectLimit($sql, $limit, $start);
                $query_time = microtime(1) - $query_start_time;

                if (count($r) < $limit) {
                    $c = $start + count($r);
                } else {
                    $c = $limit + $start + 1;
                }
                //$r = $this->html($r);
                if (isset($this->rq_dt['csv'])) {
                    $str = '';
                    foreach ($r as $v) {
                        $str .= implode("\t", $v) . "\n";
                    }
                    $r = $str;
                }

                $this->json(JSON_SUCCESS, '', '', array('c' => $c, 'data' => $r, 'query_time' => $query_time));
            } else {
                $query_start_time = microtime(1);
                $r = $db->execute($sql);
                $query_time = microtime(1) - $query_start_time;
                if ($r) {
                    $this->json(JSON_SUCCESS, '执行成功!', '', array('query_time' => $query_time));
                }
            }
        }catch(Exception $e){}

        $this->json(JSON_FAILED,'执行失败！'.$db_name.'<br/>'.print_r($db->errorInfo(),true));

    }

    function html($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    $data[$k] = $this->html($v);
                } else {
                    $data[$k] = htmlspecialchars($v);
                }
            }
        } else {
            $data = htmlspecialchars($data);
        }
        return $data;
    }
}