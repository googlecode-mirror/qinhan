<?php

class project_api extends project_config
{

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-12-01 19:39:46
     * @throws 注意:无DB异常处理
     */
    function _ipcs()
    {
        //监控当前系统的队列个数
        $out = NULL;
        exec('ipcs', $out);
        foreach ($out as $k => $v) {
            if (strpos($v, '0x') === false) {
                unset($out[$k]);
                continue;
            }
            $out[$k] = array_diff(explode(" ", $v), array(
                ""
            ));
        }
        $_num = $_name = null;
        foreach ($out as $k => $v) {
            if (count($v) != 6)
                continue;
            $i = 0;
            foreach ($v as $vv) {
                $i++;
                if ($i == 1)
                    $_name = (string)$vv;
                if ($i == 5)
                    $_num = $vv / 1048576;
            }
            $ipcs_out[] = array(
                'num' => $_num,
                'name' => $_name
            );
        }
        foreach ($ipcs_out as $k => $v)
            _status($v['num'], VHOST . "(队列服务)", $v['name'], $v['name'], date('Y-m-d H:i:s'), VIP);
    }

    /**
     * @desc 责任验收
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-12-17 11:48:39
     * @throws 注意:无DB异常处理
     */
    function monitor_duty()
    {
        $conn_db = _ocilogon($this->db);
        _ociexecute(_ociparse($conn_db, "alter session set nls_date_format='YYYY-MM-DD HH24:MI:SS'"));
        //文档满意分的算法
        _status(100, VHOST . "(项目文档满意分)", "A.基础分", "基础分", NULL, VIP, 0, 'replace');
        $sql = "select * from {$this->report_doc_list} t where t.list_name like 'Table:%'";
        $stmt_list = _ociparse($conn_db, $sql);
        _ociexecute($stmt_list);
        $_row_all = array();
        while (ocifetchinto($stmt_list, $_row_all, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if (!$_row_all['DES']) {
                _status(-pow(10, floor((time() - strtotime($_row_all['ADD_TIME'])) / 86400)), VHOST . "(项目文档满意分)", "没有导入结构", $_row_all["LIST_NAME"]);
            } else {
                foreach (explode("\n", $_row_all['DES']) as $k => $v) {
                    if (strpos(trim($v), '`') !== 0) continue;
                    if (strpos($v, "'") !== false) continue;
                    _status(-2, VHOST . "(项目文档满意分)", "字段没有注释", $_row_all["LIST_NAME"], $v);
                }
                //表索引
                if (strpos($_row_all['DES'], 'PRIMARY KEY') === false && strpos($_row_all['DES'], '@UNIQUE') === false) {
                    _status(-20, VHOST . "(项目文档满意分)", "索引设计", $_row_all["LIST_NAME"], $_row_all['DES']);
                }
                var_dump("{$_row_all["LIST_NAME"]}@" . substr($_row_all['DES'], strpos($_row_all['DES'], 'ENGINE=')));
                echo "\n";
                //表格注释
                if (
                    strpos(substr($_row_all['DES'], strpos($_row_all['DES'], 'ENGINE=')), "COMMENT='") === false
                    and !(
                        strpos($_row_all['DES'], '=>') and
                        trim(substr($_row_all['DES'], strpos($_row_all['DES'], '=>') + 2)))
                ) {
                    _status(-10, VHOST . "(项目文档满意分)", "表格没有注释", $_row_all["LIST_NAME"], $_row_all['DES']);
                }
            }
        }

        //
        $sql = "select * from {$this->report_monitor_v1}  where IS_DUTY=1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $v1_all = $_row_all = array();
        while (ocifetchinto($stmt, $_row_all, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $v1_all[$_row_all['V1']] = $_row_all['V1'];
        }
        $sql = "select t.lookup, trunc(t.cal_date) cal_date, v1
            from {$this->report_monitor_date} t
            where t.cal_date >= trunc(sysdate - 7)
            and t.cal_date < trunc(sysdate - 6) and t.lookup is null
            group by trunc(t.cal_date), t.lookup, v1 ";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if ($v1_all[$_row['V1']])
                continue;
            _status(1, VHOST . "(BUG错误)", "验收责任未到位", $_row['V1'], "", VIP);
        }
        if ($_GET['no_manyi'])
            return;
        //满意度:
        //错误率占10％
        $sql = "select (select nvl(sum(fun_count), 0)
          from {$this->report_monitor_date} t
         where v1 like '%(BUG错误)'
           and v2 = 'SQL错误'
           and t.cal_date = trunc(sysdate)) php_num,
        (select nvl(sum(fun_count), 0)
          from {$this->report_monitor_date} t
         where v1 like '%(BUG错误)'
           and v2 = 'PHP错误'
           and t.cal_date = trunc(sysdate)) sql_num,       
        (select sum(t.fun_count)
          from {$this->report_monitor_date} t
         where v1 like '%(WEB日志分析)'
           and t.cal_date = trunc(sysdate)) web_num  from dual ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $_row['SQLERR'] = round(($_row['PHP_NUM'] + $_row['SQL_NUM']) / $_row['WEB_NUM'] * 100, 2);
        $manyi = 0;
        if ($_row['SQLERR'] > 1)
            $manyi = 0;
        elseif ($_row['SQLERR'] < 0.1)
            $manyi = 100 * 10 / 100; else {
            $manyi = (100 - ($_row['SQLERR'] - 0.1) / (1 - 0.1) * 100) * 10 / 100;
        }
        _status($manyi, VHOST . "(项目满意分)", "PHP+SQL错误率", "PHP+SQL错误率", "PHP_NUM:{$_row['PHP_NUM']},SQL_NUM:{$_row['SQL_NUM']},WEB_NUM:{$_row['WEB_NUM']}@{$_row['SQLERR']}%", VIP, 0, 'replace');
        //sql量40％
        $sql = "select (select nvl(sum(fun_count), 0)
          from {$this->report_monitor_date} t
         where v1 like '%(SQL统计)'
         and t.cal_date = trunc(sysdate - 2/24)) sql_num,
        (select sum(t.fun_count)
          from {$this->report_monitor_date} t
         where v1 like '%(WEB日志分析)'
           and t.cal_date = trunc(sysdate - 2/24)) web_num,
         (select sum(t.fun_count)
          from {$this->report_monitor_date} t
         where v1 like '%(WEB日志分析)'
           and t.cal_date = trunc(sysdate - 1)) y_web_num
        from dual  ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $_row['SQLERR'] = round($_row['SQL_NUM'] / $_row['WEB_NUM'] * 100, 2);
        $manyi = 0;
        if ($_row['Y_WEB_NUM'] >= 2000000) {
            if ($_row['SQLERR'] > 1 * 100)
                $manyi = 0;
            elseif ($_row['SQLERR'] < 6 / 10 * 100)
                $manyi = 100 * 40 / 100; else {
                $manyi = (100 - ($_row['SQLERR'] - 6 / 10 * 100) / (1 * 100 - 6 / 10 * 100) * 100) * 40 / 100;
            }
        }
        if ($_row['Y_WEB_NUM'] < 2000000 && $_row['Y_WEB_NUM'] >= 300000) {
            if ($_row['SQLERR'] > 2 * 100)
                $manyi = 0;
            elseif ($_row['SQLERR'] < 1.2 * 100)
                $manyi = 40; else {
                $manyi = (100 - ($_row['SQLERR'] - 1.2 * 100) / (2 * 100 - 1.2 * 100) * 100) * 40 / 100;
            }
        }
        if ($_row['Y_WEB_NUM'] < 300000) {
            if ($_row['SQLERR'] > 50 * 100)
                $manyi = 0;
            elseif ($_row['SQLERR'] < 30 * 100)
                $manyi = 40; else {
                $manyi = (100 - ($_row['SQLERR'] - 30 * 100) / (50 * 100 - 30 * 100) * 100) * 40 / 100;
            }
        }
        _status($manyi, VHOST . "(项目满意分)", "SQL回源率", "SQL回源率", "SQL_NUM:{$_row['SQL_NUM']},WEB_NUM:{$_row['WEB_NUM']}@{$_row['SQLERR']}%", VIP, 0, 'replace');

        $sql = "select nvl(sum(fun_count), 0) sql_num
                          from {$this->report_monitor_date} t
                         where v1 like '%(SQL统计)'
                           and t.cal_date = trunc(sysdate)";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        //扣分单小时SQL上限
        $hour = date('H');
        $manyi = 0;
        $sql_error = round($_row['SQL_NUM'] / $hour);
        if ($sql_error >= 300000) {
            $num = 5 * intval(($sql_error - 300000) / 10000);
            $manyi = $manyi - $num;
        }
        _status($manyi, VHOST . "(项目满意分)", "扣分:单小时SQL上限", "扣分:单小时SQL上限", "SQL_NUM:{$_row['SQL_NUM']},H:{$hour},平均sql量:{$sql_error}", VIP, 0, 'replace');
        //memcache 20%
        $sql = "select (select nvl(sum(fun_count), 0)
          from {$this->report_monitor_date} t
         where v1 like '%(Memcache)'
           and t.cal_date = trunc(sysdate)) mem_num,
        (select sum(t.fun_count)
          from {$this->report_monitor_date} t
         where v1 like '%(WEB日志分析)'
           and t.cal_date = trunc(sysdate)) web_num from dual ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $_row['SQLERR'] = round($_row['MEM_NUM'] / $_row['WEB_NUM']);
        $manyi = 0;
        if ($_row['SQLERR'] > 6)
            $manyi = 0;
        elseif ($_row['SQLERR'] < 3)
            $manyi = 100 * 20 / 100; else {
            $manyi = (100 - ($_row['SQLERR'] - 3) / (6 - 3) * 100) * 20 / 100;
        }
        _status($manyi, VHOST . "(项目满意分)", "Memcache回源率", "Memcache回源率", "MEM_NUM:{$_row['MEM_NUM']},WEB_NUM:{$_row['WEB_NUM']}@" . ($_row['SQLERR'] * 100) . "%", VIP, 0, 'replace');

        $sql = "select sum(t.fun_count) sqlerr
            from {$this->report_monitor_hour} t
           where v1 like '%(BUG错误)'
             and v2 = '验收责任未到位'
             and t.cal_date = trunc(sysdate-1/24,'hh24') ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = 10;
        if ($_row['SQLERR'] > 0)
            $manyi = -10;
        _status($manyi, VHOST . "(项目满意分)", "项目验收", "项目验收", $_row['SQLERR'], VIP, 0, 'replace');
        //tcp满意度 30%
        $sql = "select nvl(sum(fun_count), 0) TCP
                          from {$this->report_monitor_date} t
                         where v1 like '%(服务器)' and V2='TCP连接'
                           and t.cal_date = trunc(sysdate-1/24)";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = 0;
        if ($_row['TCP'] > 120)
            $manyi = 0;
        elseif ($_row['TCP'] < 70)
            $manyi = 100 * 30 / 100; else {
            $manyi = (70 - ($_row['TCP'] - 70) / 100 * 100) * 30 / 100;
        }
        _status($manyi, VHOST . "(项目满意分)", "TCP连接数", "TCP连接数", 'TCP连接数:' . $_row['TCP'], VIP, 0, 'replace');

        //扣分项
        //机器重启当天,每小时扣200分
        $sql = "select fun_count from {$this->report_monitor_date} t where v1 like'%(服务器)' and v2='运行天数' and t.cal_date = trunc(sysdate - 1/24 )";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        $manyi = 0;
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        if ($_row['FUN_COUNT'] == '') {
            $manyi = -200;
        }
        _status($manyi, VHOST . "(项目满意分)", "扣分:机器重启", "机器重启", NULL, VIP, 0, 'replace');

        //非定时任务扣分(非定时任务代码执行超过1秒占总量的0.1%以上,扣20分)
        $sql = "select (select nvl(sum(fun_count), 0)
                  from {$this->report_monitor_date} t
                 where v1 like '%(程序效率BUG)' and (v2 ='1s到5s' or v2='5s到10s' or v2='10s到∞秒')
                   and t.cal_date = trunc(sysdate)) sql_num,
               (select sum(t.fun_count)
                  from {$this->report_monitor_date} t
                 where v1 like '%(WEB日志分析)'
                   and t.cal_date = trunc(sysdate)) web_num
          from dual ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $_row['SQLERR'] = $_row['SQL_NUM'] / $_row['WEB_NUM'];
        $manyi = 0;
        if ($_row['SQL_NUM'] >= 1000) {
            $num = 5 * intval($_row['SQL_NUM'] / 1000);
            $manyi = $manyi - $num;
        }
        _status($manyi, VHOST . "(项目满意分)", "扣分:执行超时", "执行超时", "OVER_NUM:{$_row['SQL_NUM']}", VIP, 0, 'replace');

        //问题sql扫描
        $sql = "select fun_count from {$this->report_monitor_date} t where v1 like'%(问题SQL)' and v2='全表扫描' and t.cal_date = trunc(sysdate-1/24)";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        $manyi = 0;
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        if ($_row['FUN_COUNT'] > 5) {
            $manyi = -10;
        } else {
            $manyi = -($_row['FUN_COUNT'] * 2.5);
        }
        _status($manyi, VHOST . "(项目满意分)", "扣分:问题sql", "全表扫描", '问题SQL' . $_row['FUN_COUNT'], VIP, 0, 'replace');

        // CPU>8 或者 LOAD>8 扣10分
        $sql = "select nvl(avg(fun_count), 0) CPU
                          from {$this->report_monitor_date} t
                         where v1 like '%(服务器)' and V2='CPU'
                           and t.cal_date = trunc(sysdate-1/24)";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

        $sql = "select nvl(avg(fun_count), 0) LOAD
                                  from {$this->report_monitor_date} t
                                 where v1 like '%(服务器)' and V2='Load'
                                   and t.cal_date = trunc(sysdate-1/24)";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row_load = array();
        ocifetchinto($stmt, $_row_load, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = 0;
        if ($_row['CPU'] > 8 || $_row_load['LOAD'] > 8) {
            $manyi = -10;
        }
        _status($manyi, VHOST . "(项目满意分)", "扣分:CPU LOAD", "CPU或LOAD过高", "CPU:{$_row['CPU']};LOAD:{$_row_load['LOAD']}", VIP, 0, 'replace');

        //web 500扣分 WEB日志出现5xx错误 [占0.05% 扣1分,没加一个万分点,扣1分,无上限]
        $sql = "select (select nvl(sum(fun_count), 0)
                          from {$this->report_monitor_date} t
                         where v1 like '%(WEB日志分析)' and v2 like '5%'
                           and t.cal_date = trunc(sysdate)) err_num,
                        (select sum(t.fun_count)
                          from {$this->report_monitor_date} t
                         where v1 like '%(WEB日志分析)'
                           and t.cal_date = trunc(sysdate)) web_num from dual ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = 0;
        $sql = "select nvl(sum(fun_count), 0) err_t_num
                                  from {$this->report_monitor_date} t
                                 where v1 like '%(WEB日志分析)' and v2 = '499'
                                   and t.cal_date = trunc(sysdate)";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $_row_t = array();
        ocifetchinto($stmt, $_row_t, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        if ($_row_t['ERR_T_NUM'] > 500) {
            $_row['ERR_NUM'] = $_row['ERR_NUM'] + $_row_t['ERR_T_NUM'];
        }
        $_row['SQLERR'] = round($_row['ERR_NUM'] / $_row['WEB_NUM'], 4);
        if ($_row['SQLERR'] >= 0.0005) {
            $manyi = ($manyi - ($_row['SQLERR'] - 0.0005)) * 10000;
        }
        _status($manyi, VHOST . "(项目满意分)", "扣分:5xx错误", "5xx错误", "ERR_NUM:{$_row['ERR_NUM']},WEB_NUM:{$_row['WEB_NUM']}@" . ($_row['SQLERR'] * 10000) . "万分", VIP, 0, 'replace');

        //[扣分:包含文件] "10个到∞个"每个扣除5分
        $sql = "select nvl(sum(fun_count), 0) fun_count
                          from {$this->report_monitor_date} t
                         where v1 like '%(包含文件)' and V2='10s到∞个'
                           and t.cal_date = trunc(sysdate) ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = 0;
        if ($_row['FUN_COUNT']) {
            $manyi = $manyi - ($_row['FUN_COUNT'] * 5);
        }
        _status($manyi, VHOST . "(项目满意分)", "扣分:包含文件", "包含文件", "包含文件个数：{$_row['FUN_COUNT']}", VIP, 0, 'replace');
        $manyi = 0;
        //扣分:安全事故
        $sql = "select nvl(sum(fun_count), 0) COCK
                                  from {$this->report_monitor_hour} t
                                 where v1 like '%(BUG错误)' and V2='上传木马入侵'
                                   and t.cal_date= trunc(sysdate-1/24,'hh24')";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $_row_cock = array();
        ocifetchinto($stmt, $_row_cock, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = $manyi - $_row_cock['COCK'] * 50;
        _status($manyi, VHOST . "(项目满意分)", "扣分:安全事故", "安全事故", "入侵个数：{$_row_cock['COCK']}", VIP, 0, 'replace');

        //扣分:故障事故
        $sql = "select fun_count,v3,to_char(cal_date,'yyyy-mm-dd hh24') cal_date
                                  from {$this->report_monitor_hour} t
                                 where v1 like '%(BUG错误)' and V2='PHP错误'
                                   and t.cal_date>= trunc(sysdate-1,'hh24') order by cal_date desc";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $_row_php = array();
        $manyi = 0;
        $data = $arr = array();
        $time = date('Y-m-d H', time() - 3600);
        while (ocifetchinto($stmt, $_row_php, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $data[$_row_php['V3']][$_row_php['CAL_DATE']]['count'] = $_row_php['FUN_COUNT'];
        }
        foreach ($data as $k => $v) {
            if (!isset($v[$time]['count']) || $v[$time]['count'] <= 0) {
                unset($data[$k]);
            } else {
                for ($i = time() - 3600; $i >= time() - 3600 * 24; $i--) {
                    $i_time = date('Y-m-d H', $i);
                    if (!isset($v[$time]) || $v[$i_time]['count'] <= 0) {
                        break;
                    } else {
                        $arr[$k][$i_time] = $v[$i_time]['count'];
                    }
                }
            }
        }
        foreach ($arr as $k => $v) {
            if (count($v) >= 6) {
                $manyi = $manyi - (count($v) - 5) * 100;
            }
        }
        _status($manyi, VHOST . "(项目满意分)", "扣分:故障事故", "故障事故", NULL, VIP, 0, 'replace');

        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-19 15:29:24
     * @throws 注意:无DB异常处理
     */
    function monitor_check()
    {
        //清空之前xss文件,重新检测
        $dirs = glob('/dev/shm/xss_' . VHOST . '/*');
        foreach ($dirs as $k => $v)
            unlink($v);
        if (class_exists('memcache_config')) {
            $memcache_config = new memcache_config;
            print_r($memcache_config->config);
            foreach ($memcache_config->config as $k => $v) {
                foreach ($v as $vv) {
                    $memcache_server = new memcache_server(array(
                        $vv
                    ));
                    $memcache_server->connect('testkey');
                    $x = $memcache_server->memcacheObj->getStats();
                    $memcache_server->close();
                    _status($x["bytes"] / 1048576, VHOST . "(Memcache状态)", '已使用(M)', "{$memcache_server->current_host['host']}:{$memcache_server->current_host['port']}", NULL, VIP, 0, 'replace');
                    _status($x["limit_maxbytes"] / 1048576, VHOST . "(Memcache状态)", '总空间(M)', "{$memcache_server->current_host['host']}:{$memcache_server->current_host['port']}", NULL, VIP, 0, 'replace');
                    _status($x["curr_items"], VHOST . "(Memcache状态)", 'KEY个数', "{$memcache_server->current_host['host']}:{$memcache_server->current_host['port']}", NULL, VIP, 0, 'replace');
                    _status(round($x["uptime"] / 86400, 0), VHOST . "(Memcache状态)", '运行天数', "{$memcache_server->current_host['host']}:{$memcache_server->current_host['port']}", NULL, VIP, 0, 'replace');
                }
            }
        }

        $oracleDB_config = new oracleDB_config;
        print_r($oracleDB_config->dbconfig);
        foreach ($oracleDB_config->dbconfig as $db => $v) {
            if (isset($v['db']))
                continue;
            $conn_db = _ocilogon($db);
            //计算数据库的表空间大小
            $sql = 'select a.tablespace_name TABLESPACE_NAME,
               round((a.total / (1024 * 1024 * 1024)), 2) "TOLAL",
               round((a.used / (1024 * 1024 * 1024)), 2) "USED",
               round((a.used / nullif(a.total, 0) * 100), 2) "USED",
               100 - round((a.used / nullif(a.total, 0) * 100), 2) "FREE"
          	   from (select tablespace_name, sum(maxbytes) total, sum(BYTES) used
               from dba_data_files
               group by tablespace_name) a order by 5';
            $stmt = _ociparse($conn_db, $sql);
            _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $num = intval(floor($_row['USED'] / 10));
                _status($_row['USED'], VHOST . "(数据库表空间)", $db, ($num * 10) . '-' . ($num * 10 + 10) . "G", "{$_row['TABLESPACE_NAME']}|free:{$_row['FREE']}%", VIP, 0, 'replace');
            }

            $sql = "select table_name,  round(blocks * 8192 / 1024 / 1024 / 1024, 2) as table_size
  				from user_tables order by 2 desc nulls last ";
            $stmt = _ociparse($conn_db, $sql);
            _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $num = intval(floor($_row['TABLE_SIZE'] / 10));
                _status($_row['TABLE_SIZE'], VHOST . "(数据库表大小)", $db, ($num * 10) . '-' . ($num * 10 + 10) . "G", $_row['TABLE_NAME'], VIP, 0, 'replace');
            }
            //谁使用了数据库
            $sql = "select t.MODULE, t.sql_text, t.MODULE, t.sql_text, sum(t.EXECUTIONS) c from v\$sqlarea t where  t.last_active_time >= sysdate - 2 / 24
            and t.last_active_time < sysdate - 1 / 24  group by t.MODULE, t.sql_text ";
            $stmt = _ociparse($conn_db, $sql);
            _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                _status($_row['C'], VHOST . '(数据库被连接)', $db, $_row['MODULE'], $_row['SQL_TEXT'], VIP, 0, 'replace');
            }
        }

        die('OK');
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-01-10 14:20:51
     * @throws 注意:无DB异常处理
     */
    function project_data()
    {
        header('Content-Type: text/html;charset=utf-8');
        $conn_db = _ocilogon($this->db);
        $stmt_session = _ociparse($conn_db, "alter session set nls_date_format='YYYY-MM-DD HH24:MI:SS'");
        _ociexecute($stmt_session);

        $table = $this->report_monitor_hour;
        if ($_GET['type'] == 'date')
            $table = $this->report_monitor_date;

        if ($_GET['v3']) {
            $sql = "select v1,v2,v3,cal_date,sum(FUN_COUNT) FUN_COUNT from {$table} where cal_date>=to_date(:start_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:end_date,'yyyy-mm-dd hh24:mi:ss') and v1=:v1 and v2=:v2 and v3=:v3   group by v1,v2,v3,cal_date order by cal_date ";
        } else if ($_GET['v2']) {
            $count_add = NULL;
            if ($_GET['type'] != 'date')
                $count_add = ",count(v3) count_v3 ";
            $sql = "select v1,v2,cal_date,sum(FUN_COUNT) FUN_COUNT {$count_add} from {$table} where cal_date>=to_date(:start_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:end_date,'yyyy-mm-dd hh24:mi:ss') and v1=:v1 and v2=:v2   group by v1,v2,cal_date order by cal_date ";
        } else if ($_GET['v1']) {
            $sql = "select v1,cal_date,sum(FUN_COUNT) FUN_COUNT ,count(v2) count_v2  from {$table} where cal_date>=to_date(:start_date,'yyyy-mm-dd hh24:mi:ss') and cal_date<to_date(:end_date,'yyyy-mm-dd hh24:mi:ss') and v1=:v1 group by v1,cal_date order by cal_date  ";
        }
        $stmt = _ociparse($conn_db, $sql);
        if ($_GET['v1'])
            _ocibindbyname($stmt, ':v1', $_GET['v1']);
        if ($_GET['v2'])
            _ocibindbyname($stmt, ':v2', $_GET['v2']);
        if ($_GET['v3'])
            _ocibindbyname($stmt, ':v3', $_GET['v3']);
        _ocibindbyname($stmt, ':start_date', $_GET['start_date']);
        _ocibindbyname($stmt, ':end_date', $_GET['end_date']);
        _ociexecute($stmt);
        $data = $_row = array();
        while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            array_walk($_row, create_function('&$v,$k', '$v=iconv("GBK","UTF-8",$v);'));
            $data[] = $_row;
        }
        die(json_encode($data));
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-12-31 10:24:56
     * @throws 注意:无DB异常处理
     */
    function report_monitor_group()
    {
        $conn_db = _ocilogon($this->db);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='核心资源', GROUP_NAME_2='资源:数据库OCI', GROUP_NAME='基本统计',as_name=null  where  V1 like '%(SQL统计)%'
            or v1 like '%(数据库表大小)%' or v1 like '%(数据库表空间)%'  or v1 like '%(数据库被连接)%' or v1 like '%(统计消耗)%' or v1 like '%(数据库连接)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='核心资源', GROUP_NAME_2='资源:数据库OCI', GROUP_NAME='错误/效率',as_name=null  where V1 like '%(问题SQL)%' or V1 like '%(SQL效率BUG)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='核心资源', GROUP_NAME_2='资源:数据库MySQL', GROUP_NAME='基本统计',as_name=null  where v1 like '%(MySQL统计)' or v1 like '%(数据库连接MySQL)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='核心资源', GROUP_NAME_2='资源:数据库MySQL', GROUP_NAME='错误/效率',as_name=null  where v1 like '%(MySQL效率BUG)%' or v1 like '%(Mysql使用错误)%' or v1 like '%(问题MySQL)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='数据指标', GROUP_NAME_2='代码架构', GROUP_NAME='基本统计',as_name=null  where v1 like '%(函数分布)%' or  V1 like '%(代码负责人)%' or  V1 like '%(代码行数)%'  or  V1 like '%(代码改动)%' or  V1 like '%(包含文件)%'  ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='数据指标',  GROUP_NAME_2='代码架构', GROUP_NAME='错误/效率',as_name=null  where v1 like '%(功能执行)%' or  V1 like '%(BUG错误)%'  or v1 like '%(程序效率BUG)%' or v1 like '%(接口测试)'   ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='数据指标', GROUP_NAME_2='代码架构', GROUP_NAME='安全隐患',as_name=null  where v1 like '%(安全BUG)%' or v1 like '%(登录日志%' or v1 like '%(登录错误)%' or v1 like '%(账户日志)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='数据指标', GROUP_NAME_2='资源:文件系统', GROUP_NAME='基本统计',as_name=null  where v1 like '%(文件系统读取)%' or v1 like '%(文件系统写入)%'  ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='核心资源', GROUP_NAME_2='资源:Memcache', GROUP_NAME='基本统计',as_name=null  where v1 like '%(Memcache)%' or v1 like '%(Memcache状态)%' or v1 like '%(Memcahe连接)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='核心资源', GROUP_NAME_2='资源:Memcache', GROUP_NAME='错误/效率',as_name=null  where v1 like '%(Memcahe连接效率)%' or  v1 like '%(Memcahe整体耗时)%' or  v1 like '%(Memcahe连接错误)%' or v1 like '%(Memcahe效率BUG)%' or v1 like '%(Memcahe错误)%' or v1 like '%(Memcache使用错误)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='核心资源', GROUP_NAME_2='资源:WEB服务器', GROUP_NAME='基本统计',as_name=null  where v1 like '%(服务器)%' or v1 like '%(WEB日志分析)%' or v1 like '%(队列服务)%' or v1 like '%(服务器进程)%' or v1 like '%(队列信息)' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='数据指标', GROUP_NAME_2='资源:API接口', GROUP_NAME='基本统计',as_name=null  where v1 like '%(网址抓取)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='数据指标', GROUP_NAME_2='资源:API接口', GROUP_NAME='错误/效率',as_name=null  where v1 like '%(接口效率)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='数据指标', GROUP_NAME_2='资源:FTP', GROUP_NAME='错误/效率',as_name=null  where v1 like '%(FTP效率BUG)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='数据指标', GROUP_NAME_2='资源:FTP', GROUP_NAME='基本统计',as_name=null  where v1 like '%(FTP)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='数据指标', GROUP_NAME_2='资源:邮件', GROUP_NAME='基本统计',as_name=null  where v1 like '%(邮件系统)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='数据指标', GROUP_NAME_2='资源:文件系统', GROUP_NAME='基本统计',as_name=null  where v1 like '%(文件系统读写)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1}  t set  GROUP_NAME_1='数据指标', GROUP_NAME_2='1.项目满意分', GROUP_NAME='B.技术满意分',as_name=null  where V1 like '%(项目满意分)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1}  t set  GROUP_NAME_1='数据指标', GROUP_NAME_2='1.项目满意分', GROUP_NAME='A.文档满意分',as_name=null  where V1 like '%(项目文档满意分)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1}  t set  AS_NAME='" . VHOST . "(技术的满意分)' where V1 like '%(项目满意分)%'";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        foreach (array(
                     $this->report_monitor_v1,
                     $this->report_monitor_config
                 ) as $table) {
            $sql = "update  {$table} t set   hour_count_type=4  where v1 like '%(服务器)%' or v1 like '%(Memcache状态)%'  ";
            $stmt = _ociparse($conn_db, $sql);
            $ocierror = _ociexecute($stmt);

            $sql = "update  {$table} t set  day_count_type=1   where v1 like '%(Memcache状态)%' or  v1 like '%(服务器)%' or v1 like '%(代码行数)%' or v1 like '%(函数分布)%'  or v1 like '%(数据库表大小)%' or v1 like '%(数据库表空间)%' or  V1 like '%(代码负责人)%' or  V1 like '%[项目]'  ";
            $stmt = _ociparse($conn_db, $sql);
            $ocierror = _ociexecute($stmt);

            $sql = "update  {$table} t set day_count_type=5,hour_count_type=4  where V1 like '%(项目满意分)%' or V1 like '%(项目文档满意分)%' or v1 like '%(队列服务)%' or v1 like '评分:%'  ";
            $stmt = _ociparse($conn_db, $sql);
            $ocierror = _ociexecute($stmt);

            //日期显示为V3的个数
            $sql = "update  {$table} t set day_count_type=7  where V1 like '%(问题SQL)%' or V1 like '%(包含文件)%' ";
            $stmt = _ociparse($conn_db, $sql);
            $ocierror = _ociexecute($stmt);

        }

        //内置的系统环境都不需要验收
        $sql = "update  {$this->report_monitor_v1} t set is_duty=1  where  (t.GROUP_NAME_1 = '核心资源' or t.GROUP_NAME_1 = '数据指标')  ";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);


        //v2分组
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='A.态度'  where  t.V2 = '扣分:故障事故'  and v1 like '%(项目满意分)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='B.责任考核'  where  (t.V2 = 'SQL回源率' or t.V2 = 'TCP连接数' or t.v2='项目验收') and v1 like '%(项目满意分)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='C.编程能力'  where  (t.V2 = 'PHP+SQL错误率' or t.V2 = '扣分:问题sql') and v1 like '%(项目满意分)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='D.安全'  where  t.V2 = '扣分:安全事故'  and v1 like '%(项目满意分)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='E.维护成本'  where  t.V2 = '扣分:包含文件' and v1 like '%(项目满意分)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='F.基础考核'  where  (t.V2 = 'Memcache回源率' or t.V2 = '扣分:单小时SQL上限' or t.v2='扣分:执行超时') and v1 like '%(项目满意分)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='G.运维考核'  where  (t.V2 = '扣分:5xx错误' or t.V2 = '扣分:CPU LOAD' or t.v2='扣分:机器重启') and v1 like '%(项目满意分)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        //别名换算
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='扣:故障' where v2='扣分:故障事故'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='扣:安全' where v2='扣分:安全事故'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='扣:SQL上限' where v2='扣分:单小时SQL上限'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='扣:负载' where v2='扣分:CPU LOAD'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='扣:重启' where v2='扣分:机器重启'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='TCP' where v2='TCP连接数'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='扣:文件数' where v2='扣分:包含文件'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='错误' where v2='PHP+SQL错误率'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='扣:超时' where v2='扣分:执行超时'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='扣:sql' where v2='扣分:问题sql'";
        _ociexecute(_ociparse($conn_db, $sql));
        //
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='Mem错误' ,V2_GROUP='问题' where v2='Memcache错误' ";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='Mem连接错误' ,V2_GROUP='问题' where v2='Memcahe连接错误'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='验收责任' ,V2_GROUP='问题' where v2='验收责任未到位'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='木马',V2_GROUP='问题'  where v2='上传木马入侵'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='问题'  where v2='PHP错误' or v2='SQL错误' or v2='未定义函数' ";
        _ociexecute(_ociparse($conn_db, $sql));


        //修改CPU,load的计算方式
        $sql = "update  {$this->report_monitor_config } set day_count_type=6,hour_count_type=4  where V1 like '%(服务器)%' and (v2='CPU' OR  v2='Load') ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set  day_count_type=6,hour_count_type=4  where  t.V2 = '压缩比例'  and v1 like '%(队列服务)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);


        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='<1S'  where  (t.V2 = '0.00s到0.01s' or t.V2 = '0.01s到0.02s' or t.v2='0.02s到0.03s' 
                or t.V2 = '0.03s到0.04s' or t.V2 = '0.04s到0.05s' or t.v2='0.05s到0.1s' or t.v2='0.1s到0.5s' or t.v2='0.5s到1s') 
                and v1 like '%(程序效率BUG)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='>1S'  where  (t.V2 = '10s到∞秒' or t.V2 = '1s到5s' or t.v2='5s到10s') 
                and v1 like '%(程序效率BUG)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='A.正常'  where  (t.V2 like '2%' or t.V2 like '3%')  and v1 like '%(WEB日志分析)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='B.地址异常'  where  t.V2 like '4%' and v1 like '%(WEB日志分析)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='C.服务器异常'  where  t.V2 like '5%'  and v1 like '%(WEB日志分析)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-09 11:21:51
     * @throws 注意:无DB异常处理
     */
    function nbi_num()
    {
        header("Content-Type:text/javascript; charset=GBK");
        $conn_db = _ocilogon($this->db);
        //剩下没解决的
        $sql = "select sum(t.fun_count) c from {$this->report_monitor_date} t where t.v1 like '%(项目满意分)'  and t.cal_date = trunc(sysdate)";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $_row['C'] = sprintf('%02d', $_row['C']);

        $sql = "select sum(t.fun_count) c from {$this->report_monitor_date} t where t.v1 like '%(项目文档满意分)'  and t.cal_date = trunc(sysdate)";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $_row2 = array();
        ocifetchinto($stmt, $_row2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $_row2['C'] = sprintf('%02d', $_row2['C']);

        if ($_row['C'])
            echo "\$('#nbi_num_xm').html('文档:{$_row2['C']}分');\$('#nbi_num_1').html('技术:{$_row['C']}分');";

        //显示其他定制的分数
        $sql = "select *  from {$this->report_monitor_v1} t where t.PINFEN_RULE_NAME is not null ";
        $stmt_list = _ociparse($conn_db, $sql);
        _ociexecute($stmt_list);
        $_row = $_row2 = array();
        $ki = 1;
        while (ocifetchinto($stmt_list, $_row2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $_row = unserialize($_row2['PINFEN_RULE']);
            if ($_row2['PINFEN_RULE_NAME'] && $_row['pinfen_name'] && $_row['koufen_name'] && $_row['base_num'] && $_row['just_rule'] && $_row['pinfen_step'] && $_row['rule_num']) {
                $ki++;
                $sql = "select sum(t.fun_count) c from {$this->report_monitor_date} t where t.v1 =:v1  and t.cal_date = trunc(sysdate)";
                $stmt = _ociparse($conn_db, $sql);
                ocibindbyname($stmt, ':v1', $_row['pinfen_name']);
                _ociexecute($stmt);
                $_row3 = array();
                ocifetchinto($stmt, $_row3, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
                $_row3['C'] = sprintf('%02d', $_row3['C']);
                echo "try{\$('#nbi_num_{$ki}').html('{$_row2['PINFEN_RULE_NAME']}:{$_row3['C']}分');}catch(e){}";
            }
        }
    }

    /**
     * @desc 系统的基本信息
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-12-07 15:49:34
     * @throws 注意:无DB异常处理
     */
    function sysload()
    {
        ini_set("display_errors", true);
        exec("uptime", $uptime); //获取系统负载
        exec('cat /proc/sys/kernel/hostname', $hostname); //获取服务器
        print_r($hostname);
        $_POST['hostname'] = $hostname[0];

        //系统负载
        preg_match('#load average: ([0-9|.]+),#iUs', $uptime[0], $out);
        print_r($out);
        _status(round($out[1], 2), VHOST . "(服务器)", 'Load', $_POST['hostname'], date('H:i:s'), VIP, 0, 'replace');

        //运行时间
        preg_match('#up ([0-9]+) day#iUs', $uptime[0], $out);
        echo "运行时间\n";
        print_r($out);
        _status($out[1], VHOST . "(服务器)", '运行天数', $_POST['hostname'], date('Y-m-d H'), VIP, 0, 'replace');

        //监控内存剩余
        exec("cat /proc/meminfo | head -2 | tail -1", $mem); //mem
        print_r($mem);
        preg_match('#.*([0-9]+) KB#iUs', $mem[0], $out);
        _status(round($out[1] / 1024, 2), VHOST . "(服务器)", 'Mem内存剩余', $_POST['hostname'], date('H:i:s'), VIP, 0, 'replace');

        //CPU监控
        exec("top -b -n 1 | awk 'NR==3 {print $5}'", $cpu); //cpuinfo
        print_r($cpu);
        $_POST['cpu'] = str_replace('%id,', '', $cpu[0]);
        $_POST['cpu'] = 100 - $_POST['cpu'];
        _status($_POST['cpu'], VHOST . "(服务器)", 'CPU', $_POST['hostname'], $_POST['cpu'] . '%-' . date('H:i:s'), VIP, 0, 'replace');

        //磁盘
        exec("df -h | awk 'NR>1{print $6,$5}'", $disk); //disk
        print_r($disk);
        foreach ($disk as $row) {
            if (strlen(trim($row)) <= 0) {
                continue;
            }
            $tmp = explode(" ", $row);
            $mnt_name = $tmp[0];
            $num = $tmp[1];
            $num = str_replace('%', '', $num);
            _status($num, VHOST . "(服务器)", '磁盘', $_POST['hostname'] . '-' . $mnt_name, $row . '-' . date('H:i:s'), VIP, 0, 'replace');
        }

        //监控TCP连接数

        exec("netstat -na|grep ESTABLISHED | awk '{print $4}' | grep :80$ | wc -l", $web_link); //web_link连接数
        print_r($web_link);
        _status($web_link[0], VHOST . "(服务器)", 'TCP连接', '80端口连接数', date('H:i:s'), VIP);

        exec("netstat -na|grep ESTABLISHED | awk '{print $(NF-1)}' | grep :3306$ | grep -v ':ffff:' ", $mysql); //mysql_link连接数
        print_r($mysql);
        foreach ($mysql as $v) {
            _status(1, VHOST . "(服务器)", 'TCP连接', 'Mysql连接数(' . $v . ')', date('H:i:s'), VIP);
        }
        exec("netstat -na|grep ESTABLISHED | awk '{print $(NF-1)}' | grep :1521$ | grep -v ':ffff:' ", $oracle_link); //oracle连接数
        print_r($oracle_link);
        foreach ($oracle_link as $k => $v) {
            _status(1, VHOST . "(服务器)", 'TCP连接', 'oracle连接数(' . $v . ')', date('H:i:s'), VIP);
        }
        exec("netstat -na|grep ESTABLISHED | awk '{print $(NF-1)}' | grep :1131[0-9]$ | grep -v ':ffff:' ", $memcache_link); //memcache_link连接数
        print_r($memcache_link);
        foreach ($memcache_link as $v) {
            _status(1, VHOST . "(服务器)", 'TCP连接', 'Memcache连接数(' . $v . ')', date('H:i:s'), VIP);
        }
        exec("netstat -na|grep ESTABLISHED | awk '{print $(NF-1)}' | grep -v :1131[0-9]$ | grep -v :1521$ | grep -v  :3306$ | grep -v :80$| grep -v ':ffff:' ", $other_link); //memcache_link连接数
        foreach ($other_link as $v) {
            $v = substr($v, 0, count($v) - 7);
            _status(1, VHOST . "(服务器)", 'TCP连接', '其它连接数', date('H:i:s') . ' ' . $v, VIP);
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc 对apache日志进行分析记录
     * @author 黄世密
     * @since  2012-12-28 10:20:00
     * @throws
     */
    function web_log()
    {
        exec("rm -f  /home/webid/logs/" . date('Y_m_d', strtotime('-7 day')) . "*.log");
        //web日志
        if ($_GET['gz']) {
            for ($i = strtotime(date('Y-m-d H:00:00', strtotime('-2 hours'))); $i < strtotime(date('Y-m-d H:00:00', strtotime('-1 hours'))); $i += 600) {
                $gz_dir = $_GET['gz'] . '/' . date('Y-m-d', $i) . '/' . VHOST;
                $logfilename = date('Y_m_d_H_i', $i) . VHOST . '_access.log';
                $i_linux = substr(strval($i), 0, -1);
                $tt1 = microtime(true);
                echo "tar zxvf {$gz_dir}*{$i_linux}* -O >/home/webid/logs/{$logfilename}\n";
                exec("tar zxvf {$gz_dir}*{$i_linux}* -O >/home/webid/logs/{$logfilename}");
                $diff_time = sprintf('%.5f', microtime(true) - $tt1);
                _status(1, VHOST . '(文件系统读写)' . ADD_PROJECT, date('H'), "{$gz_dir}*{$i_linux}*", GET_INCLUDED_FILES . "/{$_GET['act']}", VIP, $diff_time);
                $this->_web_log("/home/webid/logs/{$logfilename}");
            }
        } else {
            $logfilename = date('Y_m_d_H', strtotime('-1 hours')) . '_access.log';
            $this->_web_log($this->log_path . $logfilename);
        }

        //php错误日志
        $log_file = '/home/webid/logs/php_error.log';
        $arr = file($log_file);
        foreach ($arr as $k => $v) {
            if (trim($v) !== '' || $v != 0) {
                if (strpos($v, 'PHP Warning')) {
                    _status(1, VHOST . '(BUG错误)', 'PHP错误[日志]', 'PHP错误日志', NULL, VIP);
                } else {
                    $v = substr($v, 22);
                    _status(1, VHOST . '(BUG错误)', 'PHP错误[日志]', 'PHP错误日志', $v, VIP);
                }
            }
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2012-11-27 17:11:52
     * @throws 注意:无DB异常处理
     */
    function _web_log($log_file)
    {
        echo $log_file, "\n";
        $log = explode("\n", _file_get_contents($log_file));
        echo "\n line:" . count($log);
        $data = $result = array();
        foreach ($log as $value) {
            if (!trim($value)) continue;
            preg_match_all("/\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}+ +-/U", $value, $c);
            preg_match_all("/HTTP\/1.\d{1}\" \d{2,3} /U", $value, $b);
            $ip = substr($c[0][0], 0, count($value) - 2);
            $status = substr($b[0][0], 10, 3);
            $data[$status][$ip] += 1;
            $first = substr($status, 0, 1);
            if ($first != '2' && $first != '3')
                $result[$status][$ip] .= $value . "\n";
        }
        foreach ($data as $status => $v) {
            arsort($v);
            $i = 1;
            foreach ($v as $ip => $value) {
                $log_status = 0;
                $first = substr($status, 0, 1);
                if ($first == '2' || $first == '3') {
                    $log_status = 1;
                }
                if ($i++ > 50)
                    $ip = '其它ip';
                if ($log_status == 1) {
                    _status($value, VHOST . '(WEB日志分析)', $status, $ip, null, VIP, 0, NULL, strtotime('-1 hours'));
                } else {
                    _status($value, VHOST . '(WEB日志分析)', $status, $ip, $result[$status][$ip], VIP, 0, NULL, strtotime('-1 hours'));
                }
            }
        }
    }

    /**
     * @desc WHAT?
     * @author 王昕曌 mailto:xzhwang@ppstream.com
     * @since  2013-02-21 10:41:49
     * @throws 注意:无DB异常处理
     */
    function send_num()
    {
        if (!$_GET['to_v1']) {
            $_GET['to_v1'] = $_GET['v1'];
            $_GET['to_v2'] = $_GET['v2'];
        }

        if (!$_GET['host']) {
            true;
        }
        $str_v2 = '';
        if ($_GET['v2']) {
            $str_v2 = "&v2=" . $_GET['v2'];
        }

        if (!$_GET['to_v2']) {
            $_GET['to_v2'] = $_GET['v1'];
        }

        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime("+1 day"));
        $chinfo = array();
        $url = "http://{$_SERVER['HTTP_HOST']}/project.php?act=project_data&v1={$_GET['v1']}{$str_v2}&start_date={$start_date}&end_date={$end_date}&type=date";
        $curl_data = _curl($chinfo, $url);
        $server_date = json_decode($curl_data, 1);
        $web_date = $server_date[0];
        $url = "http://{$_GET['host']}/project.php?act=web_status&v1={$_GET['to_v1']}&v2={$_GET['to_v2']}&v3={$_GET['to_v2']}&num={$web_date['FUN_COUNT']}";
        $curl_data = _curl($chinfo, $url);
        echo $curl_data;
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-03-25 15:36:11
     * @throws 注意:无DB异常处理
     */

    function memcache_clear()
    {
        if (!$_GET['host'] || !$_GET['port'])
            die("缺少参数:host/port=?\n");

        //之前
        $memcache_server = new memcache;
        $memcache_server->connect($_GET['host'], $_GET['port']);
        $memcache_server->set("flush_memcache", date('Y-m-d H:i:s'));
        var_dump($memcache_server->get("flush_memcache"));
        $x = $memcache_server->getStats();
        $memcache_server->flush();
        $memcache_server->close();
        print_r($_GET);
        print_r('已使用(M):' . $x["bytes"] / 1048576);
        echo "\n";
        print_r('KEY个数:' . $x["curr_items"]);
        echo "\n";

        //之后
        $memcache_server->connect($_GET['host'], $_GET['port']);
        $x = $memcache_server->getStats();
        print_r('已使用(M):' . $x["bytes"] / 1048576);
        echo "\n";
        print_r('KEY个数:' . $x["curr_items"]);
        echo "\n";
        var_dump($memcache_server->get("flush_memcache"));
        $memcache_server->close();
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-05-24 13:25:32
     * @throws 注意:无DB异常处理
     */
    function report_pinfen()
    {
        $conn_db = _ocilogon($this->db);
        if ($_POST['v2']) {
            $sql = "update  {$this->report_monitor_config}  t set PINFEN_RULE=:PINFEN_RULE where v1=:v1 and v2=:v2 ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v1', $_POST['v1']);
            _ocibindbyname($stmt, ':v2', $_POST['v2']);
            _ocibindbyname($stmt, ':PINFEN_RULE', serialize($_POST));
            $ocierror = _ociexecute($stmt);
        } else {

            $sql = "update  {$this->report_monitor_v1}  t set PINFEN_RULE=:PINFEN_RULE where v1=:v1  ";
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':v1', $_POST['v1']);
            _ocibindbyname($stmt, ':PINFEN_RULE', serialize($_POST));
            $ocierror = _ociexecute($stmt);
        }
        print_r($ocierror);
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc WHAT?
     * @author 夏琳泰 mailto:resia@dev.ppstream.com
     * @since  2013-05-24 15:51:45
     * @throws 注意:无DB异常处理
     */
    function crontab_report_pinfen()
    {
        ini_set("display_errors", true);
        $conn_db = _ocilogon($this->db);
        //获取V1级别的评分要求
        $_row_infos = array();
        $sql = "select * from {$this->report_monitor_v1} t where pinfen_rule is not null ";
        $stmt_list = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt_list);
        $_row = array();
        while (ocifetchinto($stmt_list, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $_row = unserialize($_row['PINFEN_RULE']);
            if ($_row['pinfen_name'] && $_row['koufen_name'] && $_row['base_num'] && $_row['just_rule'] && $_row['pinfen_step'] && $_row['rule_num'])
                $_row_infos[] = $_row;
        }
        //获取V2级别的评分要求
        $sql = "select * from {$this->report_monitor_config} t where pinfen_rule is not null ";
        $stmt_list = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt_list);
        $_row = array();
        while (ocifetchinto($stmt_list, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $_row = unserialize($_row['PINFEN_RULE']);
            if ($_row['pinfen_name'] && $_row['koufen_name'] && $_row['base_num'] && $_row['just_rule'] && $_row['pinfen_step'] && $_row['rule_num'])
                $_row_infos[] = $_row;
        }
        print_r($_row_infos);
        foreach ($_row_infos as $_row_info) {
            if ($_row_info['v2']) {
                if ($_row_info['just_rule'] == '>') {
                    $sql = "select   case  when nvl(t.fun_count,0) > :base_num then  - round((nvl(t.fun_count,0) - :base_num) / :pinfen_step)  else  0  end as num from {$this->report_monitor_date} t where v1 = :v1  and v2 = :v2  and cal_date = trunc(sysdate) ";
                } else {
                    $sql = "select t.fun_count, case  when nvl(t.fun_count,0) < :base_num then  - round((:base_num - nvl(t.fun_count,0)) / :pinfen_step)  else  0  end as num from {$this->report_monitor_date} t where v1 = :v1   and v2 = :v2  and cal_date = trunc(sysdate) ";
                }
            } else {
                if ($_row_info['just_rule'] == '>') {
                    $sql = "select  case  when sum(nvl(t.fun_count,0)) > :base_num then  - round((  sum(nvl(t.fun_count,0)) - :base_num) / :pinfen_step)  else  0  end as num from {$this->report_monitor_date} t where v1 = :v1    and cal_date = trunc(sysdate) ";
                } else {
                    $sql = "select  case  when  sum(nvl(t.fun_count,0)) < :base_num then  - round((:base_num -  sum(nvl(t.fun_count,0)) ) / :pinfen_step)  else  0  end as num from {$this->report_monitor_date} t where v1 = :v1     and cal_date = trunc(sysdate) ";
                }

            }
            $stmt = _ociparse($conn_db, $sql);
            _ocibindbyname($stmt, ':base_num', $_row_info['base_num']);
            _ocibindbyname($stmt, ':pinfen_step', $_row_info['pinfen_step']);
            _ocibindbyname($stmt, ':v1', $_row_info['v1']);
            if ($_row_info['v2'])
                _ocibindbyname($stmt, ':v2', $_row_info['v2']);
            $ocierror = _ociexecute($stmt);
            print_r($ocierror);

            $_row_num = array();
            ocifetchinto($stmt, $_row_num, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
            _status($_row_num['NUM'], $_row_info['pinfen_name'], $_row_info['koufen_name'], $_row_info['v1'] . "@" . $_row_info['v2']);
            print_r($_row_num);
        }
    }
}

