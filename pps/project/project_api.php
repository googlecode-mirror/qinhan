<?php

class project_api extends project_config
{

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-12-01 19:39:46
     * @throws ע��:��DB�쳣����
     */
    function _ipcs()
    {
        //��ص�ǰϵͳ�Ķ��и���
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
            _status($v['num'], VHOST . "(���з���)", $v['name'], $v['name'], date('Y-m-d H:i:s'), VIP);
    }

    /**
     * @desc ��������
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-12-17 11:48:39
     * @throws ע��:��DB�쳣����
     */
    function monitor_duty()
    {
        $conn_db = _ocilogon($this->db);
        _ociexecute(_ociparse($conn_db, "alter session set nls_date_format='YYYY-MM-DD HH24:MI:SS'"));
        //�ĵ�����ֵ��㷨
        _status(100, VHOST . "(��Ŀ�ĵ������)", "A.������", "������", NULL, VIP, 0, 'replace');
        $sql = "select * from {$this->report_doc_list} t where t.list_name like 'Table:%'";
        $stmt_list = _ociparse($conn_db, $sql);
        _ociexecute($stmt_list);
        $_row_all = array();
        while (ocifetchinto($stmt_list, $_row_all, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            if (!$_row_all['DES']) {
                _status(-pow(10, floor((time() - strtotime($_row_all['ADD_TIME'])) / 86400)), VHOST . "(��Ŀ�ĵ������)", "û�е���ṹ", $_row_all["LIST_NAME"]);
            } else {
                foreach (explode("\n", $_row_all['DES']) as $k => $v) {
                    if (strpos(trim($v), '`') !== 0) continue;
                    if (strpos($v, "'") !== false) continue;
                    _status(-2, VHOST . "(��Ŀ�ĵ������)", "�ֶ�û��ע��", $_row_all["LIST_NAME"], $v);
                }
                //������
                if (strpos($_row_all['DES'], 'PRIMARY KEY') === false && strpos($_row_all['DES'], '@UNIQUE') === false) {
                    _status(-20, VHOST . "(��Ŀ�ĵ������)", "�������", $_row_all["LIST_NAME"], $_row_all['DES']);
                }
                var_dump("{$_row_all["LIST_NAME"]}@" . substr($_row_all['DES'], strpos($_row_all['DES'], 'ENGINE=')));
                echo "\n";
                //���ע��
                if (
                    strpos(substr($_row_all['DES'], strpos($_row_all['DES'], 'ENGINE=')), "COMMENT='") === false
                    and !(
                        strpos($_row_all['DES'], '=>') and
                        trim(substr($_row_all['DES'], strpos($_row_all['DES'], '=>') + 2)))
                ) {
                    _status(-10, VHOST . "(��Ŀ�ĵ������)", "���û��ע��", $_row_all["LIST_NAME"], $_row_all['DES']);
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
            _status(1, VHOST . "(BUG����)", "��������δ��λ", $_row['V1'], "", VIP);
        }
        if ($_GET['no_manyi'])
            return;
        //�����:
        //������ռ10��
        $sql = "select (select nvl(sum(fun_count), 0)
          from {$this->report_monitor_date} t
         where v1 like '%(BUG����)'
           and v2 = 'SQL����'
           and t.cal_date = trunc(sysdate)) php_num,
        (select nvl(sum(fun_count), 0)
          from {$this->report_monitor_date} t
         where v1 like '%(BUG����)'
           and v2 = 'PHP����'
           and t.cal_date = trunc(sysdate)) sql_num,       
        (select sum(t.fun_count)
          from {$this->report_monitor_date} t
         where v1 like '%(WEB��־����)'
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
        _status($manyi, VHOST . "(��Ŀ�����)", "PHP+SQL������", "PHP+SQL������", "PHP_NUM:{$_row['PHP_NUM']},SQL_NUM:{$_row['SQL_NUM']},WEB_NUM:{$_row['WEB_NUM']}@{$_row['SQLERR']}%", VIP, 0, 'replace');
        //sql��40��
        $sql = "select (select nvl(sum(fun_count), 0)
          from {$this->report_monitor_date} t
         where v1 like '%(SQLͳ��)'
         and t.cal_date = trunc(sysdate - 2/24)) sql_num,
        (select sum(t.fun_count)
          from {$this->report_monitor_date} t
         where v1 like '%(WEB��־����)'
           and t.cal_date = trunc(sysdate - 2/24)) web_num,
         (select sum(t.fun_count)
          from {$this->report_monitor_date} t
         where v1 like '%(WEB��־����)'
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
        _status($manyi, VHOST . "(��Ŀ�����)", "SQL��Դ��", "SQL��Դ��", "SQL_NUM:{$_row['SQL_NUM']},WEB_NUM:{$_row['WEB_NUM']}@{$_row['SQLERR']}%", VIP, 0, 'replace');

        $sql = "select nvl(sum(fun_count), 0) sql_num
                          from {$this->report_monitor_date} t
                         where v1 like '%(SQLͳ��)'
                           and t.cal_date = trunc(sysdate)";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        //�۷ֵ�СʱSQL����
        $hour = date('H');
        $manyi = 0;
        $sql_error = round($_row['SQL_NUM'] / $hour);
        if ($sql_error >= 300000) {
            $num = 5 * intval(($sql_error - 300000) / 10000);
            $manyi = $manyi - $num;
        }
        _status($manyi, VHOST . "(��Ŀ�����)", "�۷�:��СʱSQL����", "�۷�:��СʱSQL����", "SQL_NUM:{$_row['SQL_NUM']},H:{$hour},ƽ��sql��:{$sql_error}", VIP, 0, 'replace');
        //memcache 20%
        $sql = "select (select nvl(sum(fun_count), 0)
          from {$this->report_monitor_date} t
         where v1 like '%(Memcache)'
           and t.cal_date = trunc(sysdate)) mem_num,
        (select sum(t.fun_count)
          from {$this->report_monitor_date} t
         where v1 like '%(WEB��־����)'
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
        _status($manyi, VHOST . "(��Ŀ�����)", "Memcache��Դ��", "Memcache��Դ��", "MEM_NUM:{$_row['MEM_NUM']},WEB_NUM:{$_row['WEB_NUM']}@" . ($_row['SQLERR'] * 100) . "%", VIP, 0, 'replace');

        $sql = "select sum(t.fun_count) sqlerr
            from {$this->report_monitor_hour} t
           where v1 like '%(BUG����)'
             and v2 = '��������δ��λ'
             and t.cal_date = trunc(sysdate-1/24,'hh24') ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = 10;
        if ($_row['SQLERR'] > 0)
            $manyi = -10;
        _status($manyi, VHOST . "(��Ŀ�����)", "��Ŀ����", "��Ŀ����", $_row['SQLERR'], VIP, 0, 'replace');
        //tcp����� 30%
        $sql = "select nvl(sum(fun_count), 0) TCP
                          from {$this->report_monitor_date} t
                         where v1 like '%(������)' and V2='TCP����'
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
        _status($manyi, VHOST . "(��Ŀ�����)", "TCP������", "TCP������", 'TCP������:' . $_row['TCP'], VIP, 0, 'replace');

        //�۷���
        //������������,ÿСʱ��200��
        $sql = "select fun_count from {$this->report_monitor_date} t where v1 like'%(������)' and v2='��������' and t.cal_date = trunc(sysdate - 1/24 )";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        $manyi = 0;
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        if ($_row['FUN_COUNT'] == '') {
            $manyi = -200;
        }
        _status($manyi, VHOST . "(��Ŀ�����)", "�۷�:��������", "��������", NULL, VIP, 0, 'replace');

        //�Ƕ�ʱ����۷�(�Ƕ�ʱ�������ִ�г���1��ռ������0.1%����,��20��)
        $sql = "select (select nvl(sum(fun_count), 0)
                  from {$this->report_monitor_date} t
                 where v1 like '%(����Ч��BUG)' and (v2 ='1s��5s' or v2='5s��10s' or v2='10s������')
                   and t.cal_date = trunc(sysdate)) sql_num,
               (select sum(t.fun_count)
                  from {$this->report_monitor_date} t
                 where v1 like '%(WEB��־����)'
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
        _status($manyi, VHOST . "(��Ŀ�����)", "�۷�:ִ�г�ʱ", "ִ�г�ʱ", "OVER_NUM:{$_row['SQL_NUM']}", VIP, 0, 'replace');

        //����sqlɨ��
        $sql = "select fun_count from {$this->report_monitor_date} t where v1 like'%(����SQL)' and v2='ȫ��ɨ��' and t.cal_date = trunc(sysdate-1/24)";
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
        _status($manyi, VHOST . "(��Ŀ�����)", "�۷�:����sql", "ȫ��ɨ��", '����SQL' . $_row['FUN_COUNT'], VIP, 0, 'replace');

        // CPU>8 ���� LOAD>8 ��10��
        $sql = "select nvl(avg(fun_count), 0) CPU
                          from {$this->report_monitor_date} t
                         where v1 like '%(������)' and V2='CPU'
                           and t.cal_date = trunc(sysdate-1/24)";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);

        $sql = "select nvl(avg(fun_count), 0) LOAD
                                  from {$this->report_monitor_date} t
                                 where v1 like '%(������)' and V2='Load'
                                   and t.cal_date = trunc(sysdate-1/24)";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row_load = array();
        ocifetchinto($stmt, $_row_load, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = 0;
        if ($_row['CPU'] > 8 || $_row_load['LOAD'] > 8) {
            $manyi = -10;
        }
        _status($manyi, VHOST . "(��Ŀ�����)", "�۷�:CPU LOAD", "CPU��LOAD����", "CPU:{$_row['CPU']};LOAD:{$_row_load['LOAD']}", VIP, 0, 'replace');

        //web 500�۷� WEB��־����5xx���� [ռ0.05% ��1��,û��һ����ֵ�,��1��,������]
        $sql = "select (select nvl(sum(fun_count), 0)
                          from {$this->report_monitor_date} t
                         where v1 like '%(WEB��־����)' and v2 like '5%'
                           and t.cal_date = trunc(sysdate)) err_num,
                        (select sum(t.fun_count)
                          from {$this->report_monitor_date} t
                         where v1 like '%(WEB��־����)'
                           and t.cal_date = trunc(sysdate)) web_num from dual ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = 0;
        $sql = "select nvl(sum(fun_count), 0) err_t_num
                                  from {$this->report_monitor_date} t
                                 where v1 like '%(WEB��־����)' and v2 = '499'
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
        _status($manyi, VHOST . "(��Ŀ�����)", "�۷�:5xx����", "5xx����", "ERR_NUM:{$_row['ERR_NUM']},WEB_NUM:{$_row['WEB_NUM']}@" . ($_row['SQLERR'] * 10000) . "���", VIP, 0, 'replace');

        //[�۷�:�����ļ�] "10�����޸�"ÿ���۳�5��
        $sql = "select nvl(sum(fun_count), 0) fun_count
                          from {$this->report_monitor_date} t
                         where v1 like '%(�����ļ�)' and V2='10s���޸�'
                           and t.cal_date = trunc(sysdate) ";
        $stmt = _ociparse($conn_db, $sql);
        $oicerror = _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = 0;
        if ($_row['FUN_COUNT']) {
            $manyi = $manyi - ($_row['FUN_COUNT'] * 5);
        }
        _status($manyi, VHOST . "(��Ŀ�����)", "�۷�:�����ļ�", "�����ļ�", "�����ļ�������{$_row['FUN_COUNT']}", VIP, 0, 'replace');
        $manyi = 0;
        //�۷�:��ȫ�¹�
        $sql = "select nvl(sum(fun_count), 0) COCK
                                  from {$this->report_monitor_hour} t
                                 where v1 like '%(BUG����)' and V2='�ϴ�ľ������'
                                   and t.cal_date= trunc(sysdate-1/24,'hh24')";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $_row_cock = array();
        ocifetchinto($stmt, $_row_cock, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $manyi = $manyi - $_row_cock['COCK'] * 50;
        _status($manyi, VHOST . "(��Ŀ�����)", "�۷�:��ȫ�¹�", "��ȫ�¹�", "���ָ�����{$_row_cock['COCK']}", VIP, 0, 'replace');

        //�۷�:�����¹�
        $sql = "select fun_count,v3,to_char(cal_date,'yyyy-mm-dd hh24') cal_date
                                  from {$this->report_monitor_hour} t
                                 where v1 like '%(BUG����)' and V2='PHP����'
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
        _status($manyi, VHOST . "(��Ŀ�����)", "�۷�:�����¹�", "�����¹�", NULL, VIP, 0, 'replace');

        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-11-19 15:29:24
     * @throws ע��:��DB�쳣����
     */
    function monitor_check()
    {
        //���֮ǰxss�ļ�,���¼��
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
                    _status($x["bytes"] / 1048576, VHOST . "(Memcache״̬)", '��ʹ��(M)', "{$memcache_server->current_host['host']}:{$memcache_server->current_host['port']}", NULL, VIP, 0, 'replace');
                    _status($x["limit_maxbytes"] / 1048576, VHOST . "(Memcache״̬)", '�ܿռ�(M)', "{$memcache_server->current_host['host']}:{$memcache_server->current_host['port']}", NULL, VIP, 0, 'replace');
                    _status($x["curr_items"], VHOST . "(Memcache״̬)", 'KEY����', "{$memcache_server->current_host['host']}:{$memcache_server->current_host['port']}", NULL, VIP, 0, 'replace');
                    _status(round($x["uptime"] / 86400, 0), VHOST . "(Memcache״̬)", '��������', "{$memcache_server->current_host['host']}:{$memcache_server->current_host['port']}", NULL, VIP, 0, 'replace');
                }
            }
        }

        $oracleDB_config = new oracleDB_config;
        print_r($oracleDB_config->dbconfig);
        foreach ($oracleDB_config->dbconfig as $db => $v) {
            if (isset($v['db']))
                continue;
            $conn_db = _ocilogon($db);
            //�������ݿ�ı�ռ��С
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
                _status($_row['USED'], VHOST . "(���ݿ��ռ�)", $db, ($num * 10) . '-' . ($num * 10 + 10) . "G", "{$_row['TABLESPACE_NAME']}|free:{$_row['FREE']}%", VIP, 0, 'replace');
            }

            $sql = "select table_name,  round(blocks * 8192 / 1024 / 1024 / 1024, 2) as table_size
  				from user_tables order by 2 desc nulls last ";
            $stmt = _ociparse($conn_db, $sql);
            _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $num = intval(floor($_row['TABLE_SIZE'] / 10));
                _status($_row['TABLE_SIZE'], VHOST . "(���ݿ���С)", $db, ($num * 10) . '-' . ($num * 10 + 10) . "G", $_row['TABLE_NAME'], VIP, 0, 'replace');
            }
            //˭ʹ�������ݿ�
            $sql = "select t.MODULE, t.sql_text, t.MODULE, t.sql_text, sum(t.EXECUTIONS) c from v\$sqlarea t where  t.last_active_time >= sysdate - 2 / 24
            and t.last_active_time < sysdate - 1 / 24  group by t.MODULE, t.sql_text ";
            $stmt = _ociparse($conn_db, $sql);
            _ociexecute($stmt);
            $_row = array();
            while (ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                _status($_row['C'], VHOST . '(���ݿⱻ����)', $db, $_row['MODULE'], $_row['SQL_TEXT'], VIP, 0, 'replace');
            }
        }

        die('OK');
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2013-01-10 14:20:51
     * @throws ע��:��DB�쳣����
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
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-12-31 10:24:56
     * @throws ע��:��DB�쳣����
     */
    function report_monitor_group()
    {
        $conn_db = _ocilogon($this->db);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='������Դ', GROUP_NAME_2='��Դ:���ݿ�OCI', GROUP_NAME='����ͳ��',as_name=null  where  V1 like '%(SQLͳ��)%'
            or v1 like '%(���ݿ���С)%' or v1 like '%(���ݿ��ռ�)%'  or v1 like '%(���ݿⱻ����)%' or v1 like '%(ͳ������)%' or v1 like '%(���ݿ�����)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='������Դ', GROUP_NAME_2='��Դ:���ݿ�OCI', GROUP_NAME='����/Ч��',as_name=null  where V1 like '%(����SQL)%' or V1 like '%(SQLЧ��BUG)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='������Դ', GROUP_NAME_2='��Դ:���ݿ�MySQL', GROUP_NAME='����ͳ��',as_name=null  where v1 like '%(MySQLͳ��)' or v1 like '%(���ݿ�����MySQL)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='������Դ', GROUP_NAME_2='��Դ:���ݿ�MySQL', GROUP_NAME='����/Ч��',as_name=null  where v1 like '%(MySQLЧ��BUG)%' or v1 like '%(Mysqlʹ�ô���)%' or v1 like '%(����MySQL)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='����ָ��', GROUP_NAME_2='����ܹ�', GROUP_NAME='����ͳ��',as_name=null  where v1 like '%(�����ֲ�)%' or  V1 like '%(���븺����)%' or  V1 like '%(��������)%'  or  V1 like '%(����Ķ�)%' or  V1 like '%(�����ļ�)%'  ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='����ָ��',  GROUP_NAME_2='����ܹ�', GROUP_NAME='����/Ч��',as_name=null  where v1 like '%(����ִ��)%' or  V1 like '%(BUG����)%'  or v1 like '%(����Ч��BUG)%' or v1 like '%(�ӿڲ���)'   ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='����ָ��', GROUP_NAME_2='����ܹ�', GROUP_NAME='��ȫ����',as_name=null  where v1 like '%(��ȫBUG)%' or v1 like '%(��¼��־%' or v1 like '%(��¼����)%' or v1 like '%(�˻���־)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='����ָ��', GROUP_NAME_2='��Դ:�ļ�ϵͳ', GROUP_NAME='����ͳ��',as_name=null  where v1 like '%(�ļ�ϵͳ��ȡ)%' or v1 like '%(�ļ�ϵͳд��)%'  ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='������Դ', GROUP_NAME_2='��Դ:Memcache', GROUP_NAME='����ͳ��',as_name=null  where v1 like '%(Memcache)%' or v1 like '%(Memcache״̬)%' or v1 like '%(Memcahe����)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='������Դ', GROUP_NAME_2='��Դ:Memcache', GROUP_NAME='����/Ч��',as_name=null  where v1 like '%(Memcahe����Ч��)%' or  v1 like '%(Memcahe�����ʱ)%' or  v1 like '%(Memcahe���Ӵ���)%' or v1 like '%(MemcaheЧ��BUG)%' or v1 like '%(Memcahe����)%' or v1 like '%(Memcacheʹ�ô���)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='������Դ', GROUP_NAME_2='��Դ:WEB������', GROUP_NAME='����ͳ��',as_name=null  where v1 like '%(������)%' or v1 like '%(WEB��־����)%' or v1 like '%(���з���)%' or v1 like '%(����������)%' or v1 like '%(������Ϣ)' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='����ָ��', GROUP_NAME_2='��Դ:API�ӿ�', GROUP_NAME='����ͳ��',as_name=null  where v1 like '%(��ַץȡ)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='����ָ��', GROUP_NAME_2='��Դ:API�ӿ�', GROUP_NAME='����/Ч��',as_name=null  where v1 like '%(�ӿ�Ч��)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='����ָ��', GROUP_NAME_2='��Դ:FTP', GROUP_NAME='����/Ч��',as_name=null  where v1 like '%(FTPЧ��BUG)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='����ָ��', GROUP_NAME_2='��Դ:FTP', GROUP_NAME='����ͳ��',as_name=null  where v1 like '%(FTP)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='����ָ��', GROUP_NAME_2='��Դ:�ʼ�', GROUP_NAME='����ͳ��',as_name=null  where v1 like '%(�ʼ�ϵͳ)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1} t set GROUP_NAME_1='����ָ��', GROUP_NAME_2='��Դ:�ļ�ϵͳ', GROUP_NAME='����ͳ��',as_name=null  where v1 like '%(�ļ�ϵͳ��д)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1}  t set  GROUP_NAME_1='����ָ��', GROUP_NAME_2='1.��Ŀ�����', GROUP_NAME='B.���������',as_name=null  where V1 like '%(��Ŀ�����)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1}  t set  GROUP_NAME_1='����ָ��', GROUP_NAME_2='1.��Ŀ�����', GROUP_NAME='A.�ĵ������',as_name=null  where V1 like '%(��Ŀ�ĵ������)%' ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_v1}  t set  AS_NAME='" . VHOST . "(�����������)' where V1 like '%(��Ŀ�����)%'";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);

        foreach (array(
                     $this->report_monitor_v1,
                     $this->report_monitor_config
                 ) as $table) {
            $sql = "update  {$table} t set   hour_count_type=4  where v1 like '%(������)%' or v1 like '%(Memcache״̬)%'  ";
            $stmt = _ociparse($conn_db, $sql);
            $ocierror = _ociexecute($stmt);

            $sql = "update  {$table} t set  day_count_type=1   where v1 like '%(Memcache״̬)%' or  v1 like '%(������)%' or v1 like '%(��������)%' or v1 like '%(�����ֲ�)%'  or v1 like '%(���ݿ���С)%' or v1 like '%(���ݿ��ռ�)%' or  V1 like '%(���븺����)%' or  V1 like '%[��Ŀ]'  ";
            $stmt = _ociparse($conn_db, $sql);
            $ocierror = _ociexecute($stmt);

            $sql = "update  {$table} t set day_count_type=5,hour_count_type=4  where V1 like '%(��Ŀ�����)%' or V1 like '%(��Ŀ�ĵ������)%' or v1 like '%(���з���)%' or v1 like '����:%'  ";
            $stmt = _ociparse($conn_db, $sql);
            $ocierror = _ociexecute($stmt);

            //������ʾΪV3�ĸ���
            $sql = "update  {$table} t set day_count_type=7  where V1 like '%(����SQL)%' or V1 like '%(�����ļ�)%' ";
            $stmt = _ociparse($conn_db, $sql);
            $ocierror = _ociexecute($stmt);

        }

        //���õ�ϵͳ����������Ҫ����
        $sql = "update  {$this->report_monitor_v1} t set is_duty=1  where  (t.GROUP_NAME_1 = '������Դ' or t.GROUP_NAME_1 = '����ָ��')  ";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);


        //v2����
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='A.̬��'  where  t.V2 = '�۷�:�����¹�'  and v1 like '%(��Ŀ�����)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='B.���ο���'  where  (t.V2 = 'SQL��Դ��' or t.V2 = 'TCP������' or t.v2='��Ŀ����') and v1 like '%(��Ŀ�����)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='C.�������'  where  (t.V2 = 'PHP+SQL������' or t.V2 = '�۷�:����sql') and v1 like '%(��Ŀ�����)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='D.��ȫ'  where  t.V2 = '�۷�:��ȫ�¹�'  and v1 like '%(��Ŀ�����)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='E.ά���ɱ�'  where  t.V2 = '�۷�:�����ļ�' and v1 like '%(��Ŀ�����)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='F.��������'  where  (t.V2 = 'Memcache��Դ��' or t.V2 = '�۷�:��СʱSQL����' or t.v2='�۷�:ִ�г�ʱ') and v1 like '%(��Ŀ�����)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='G.��ά����'  where  (t.V2 = '�۷�:5xx����' or t.V2 = '�۷�:CPU LOAD' or t.v2='�۷�:��������') and v1 like '%(��Ŀ�����)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        //��������
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='��:����' where v2='�۷�:�����¹�'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='��:��ȫ' where v2='�۷�:��ȫ�¹�'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='��:SQL����' where v2='�۷�:��СʱSQL����'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='��:����' where v2='�۷�:CPU LOAD'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='��:����' where v2='�۷�:��������'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='TCP' where v2='TCP������'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='��:�ļ���' where v2='�۷�:�����ļ�'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='����' where v2='PHP+SQL������'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='��:��ʱ' where v2='�۷�:ִ�г�ʱ'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='��:sql' where v2='�۷�:����sql'";
        _ociexecute(_ociparse($conn_db, $sql));
        //
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='Mem����' ,V2_GROUP='����' where v2='Memcache����' ";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='Mem���Ӵ���' ,V2_GROUP='����' where v2='Memcahe���Ӵ���'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='��������' ,V2_GROUP='����' where v2='��������δ��λ'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set AS_NAME='ľ��',V2_GROUP='����'  where v2='�ϴ�ľ������'";
        _ociexecute(_ociparse($conn_db, $sql));
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='����'  where v2='PHP����' or v2='SQL����' or v2='δ���庯��' ";
        _ociexecute(_ociparse($conn_db, $sql));


        //�޸�CPU,load�ļ��㷽ʽ
        $sql = "update  {$this->report_monitor_config } set day_count_type=6,hour_count_type=4  where V1 like '%(������)%' and (v2='CPU' OR  v2='Load') ";
        $stmt = _ociparse($conn_db, $sql);
        $ocierror = _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set  day_count_type=6,hour_count_type=4  where  t.V2 = 'ѹ������'  and v1 like '%(���з���)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);


        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='<1S'  where  (t.V2 = '0.00s��0.01s' or t.V2 = '0.01s��0.02s' or t.v2='0.02s��0.03s' 
                or t.V2 = '0.03s��0.04s' or t.V2 = '0.04s��0.05s' or t.v2='0.05s��0.1s' or t.v2='0.1s��0.5s' or t.v2='0.5s��1s') 
                and v1 like '%(����Ч��BUG)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='>1S'  where  (t.V2 = '10s������' or t.V2 = '1s��5s' or t.v2='5s��10s') 
                and v1 like '%(����Ч��BUG)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);

        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='A.����'  where  (t.V2 like '2%' or t.V2 like '3%')  and v1 like '%(WEB��־����)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='B.��ַ�쳣'  where  t.V2 like '4%' and v1 like '%(WEB��־����)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $sql = "update  {$this->report_monitor_config} t set V2_GROUP='C.�������쳣'  where  t.V2 like '5%'  and v1 like '%(WEB��־����)%'";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        header("location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-11-09 11:21:51
     * @throws ע��:��DB�쳣����
     */
    function nbi_num()
    {
        header("Content-Type:text/javascript; charset=GBK");
        $conn_db = _ocilogon($this->db);
        //ʣ��û�����
        $sql = "select sum(t.fun_count) c from {$this->report_monitor_date} t where t.v1 like '%(��Ŀ�����)'  and t.cal_date = trunc(sysdate)";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $_row = array();
        ocifetchinto($stmt, $_row, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $_row['C'] = sprintf('%02d', $_row['C']);

        $sql = "select sum(t.fun_count) c from {$this->report_monitor_date} t where t.v1 like '%(��Ŀ�ĵ������)'  and t.cal_date = trunc(sysdate)";
        $stmt = _ociparse($conn_db, $sql);
        _ociexecute($stmt);
        $_row2 = array();
        ocifetchinto($stmt, $_row2, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
        $_row2['C'] = sprintf('%02d', $_row2['C']);

        if ($_row['C'])
            echo "\$('#nbi_num_xm').html('�ĵ�:{$_row2['C']}��');\$('#nbi_num_1').html('����:{$_row['C']}��');";

        //��ʾ�������Ƶķ���
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
                echo "try{\$('#nbi_num_{$ki}').html('{$_row2['PINFEN_RULE_NAME']}:{$_row3['C']}��');}catch(e){}";
            }
        }
    }

    /**
     * @desc ϵͳ�Ļ�����Ϣ
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-12-07 15:49:34
     * @throws ע��:��DB�쳣����
     */
    function sysload()
    {
        ini_set("display_errors", true);
        exec("uptime", $uptime); //��ȡϵͳ����
        exec('cat /proc/sys/kernel/hostname', $hostname); //��ȡ������
        print_r($hostname);
        $_POST['hostname'] = $hostname[0];

        //ϵͳ����
        preg_match('#load average: ([0-9|.]+),#iUs', $uptime[0], $out);
        print_r($out);
        _status(round($out[1], 2), VHOST . "(������)", 'Load', $_POST['hostname'], date('H:i:s'), VIP, 0, 'replace');

        //����ʱ��
        preg_match('#up ([0-9]+) day#iUs', $uptime[0], $out);
        echo "����ʱ��\n";
        print_r($out);
        _status($out[1], VHOST . "(������)", '��������', $_POST['hostname'], date('Y-m-d H'), VIP, 0, 'replace');

        //����ڴ�ʣ��
        exec("cat /proc/meminfo | head -2 | tail -1", $mem); //mem
        print_r($mem);
        preg_match('#.*([0-9]+) KB#iUs', $mem[0], $out);
        _status(round($out[1] / 1024, 2), VHOST . "(������)", 'Mem�ڴ�ʣ��', $_POST['hostname'], date('H:i:s'), VIP, 0, 'replace');

        //CPU���
        exec("top -b -n 1 | awk 'NR==3 {print $5}'", $cpu); //cpuinfo
        print_r($cpu);
        $_POST['cpu'] = str_replace('%id,', '', $cpu[0]);
        $_POST['cpu'] = 100 - $_POST['cpu'];
        _status($_POST['cpu'], VHOST . "(������)", 'CPU', $_POST['hostname'], $_POST['cpu'] . '%-' . date('H:i:s'), VIP, 0, 'replace');

        //����
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
            _status($num, VHOST . "(������)", '����', $_POST['hostname'] . '-' . $mnt_name, $row . '-' . date('H:i:s'), VIP, 0, 'replace');
        }

        //���TCP������

        exec("netstat -na|grep ESTABLISHED | awk '{print $4}' | grep :80$ | wc -l", $web_link); //web_link������
        print_r($web_link);
        _status($web_link[0], VHOST . "(������)", 'TCP����', '80�˿�������', date('H:i:s'), VIP);

        exec("netstat -na|grep ESTABLISHED | awk '{print $(NF-1)}' | grep :3306$ | grep -v ':ffff:' ", $mysql); //mysql_link������
        print_r($mysql);
        foreach ($mysql as $v) {
            _status(1, VHOST . "(������)", 'TCP����', 'Mysql������(' . $v . ')', date('H:i:s'), VIP);
        }
        exec("netstat -na|grep ESTABLISHED | awk '{print $(NF-1)}' | grep :1521$ | grep -v ':ffff:' ", $oracle_link); //oracle������
        print_r($oracle_link);
        foreach ($oracle_link as $k => $v) {
            _status(1, VHOST . "(������)", 'TCP����', 'oracle������(' . $v . ')', date('H:i:s'), VIP);
        }
        exec("netstat -na|grep ESTABLISHED | awk '{print $(NF-1)}' | grep :1131[0-9]$ | grep -v ':ffff:' ", $memcache_link); //memcache_link������
        print_r($memcache_link);
        foreach ($memcache_link as $v) {
            _status(1, VHOST . "(������)", 'TCP����', 'Memcache������(' . $v . ')', date('H:i:s'), VIP);
        }
        exec("netstat -na|grep ESTABLISHED | awk '{print $(NF-1)}' | grep -v :1131[0-9]$ | grep -v :1521$ | grep -v  :3306$ | grep -v :80$| grep -v ':ffff:' ", $other_link); //memcache_link������
        foreach ($other_link as $v) {
            $v = substr($v, 0, count($v) - 7);
            _status(1, VHOST . "(������)", 'TCP����', '����������', date('H:i:s') . ' ' . $v, VIP);
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc ��apache��־���з�����¼
     * @author ������
     * @since  2012-12-28 10:20:00
     * @throws
     */
    function web_log()
    {
        exec("rm -f  /home/webid/logs/" . date('Y_m_d', strtotime('-7 day')) . "*.log");
        //web��־
        if ($_GET['gz']) {
            for ($i = strtotime(date('Y-m-d H:00:00', strtotime('-2 hours'))); $i < strtotime(date('Y-m-d H:00:00', strtotime('-1 hours'))); $i += 600) {
                $gz_dir = $_GET['gz'] . '/' . date('Y-m-d', $i) . '/' . VHOST;
                $logfilename = date('Y_m_d_H_i', $i) . VHOST . '_access.log';
                $i_linux = substr(strval($i), 0, -1);
                $tt1 = microtime(true);
                echo "tar zxvf {$gz_dir}*{$i_linux}* -O >/home/webid/logs/{$logfilename}\n";
                exec("tar zxvf {$gz_dir}*{$i_linux}* -O >/home/webid/logs/{$logfilename}");
                $diff_time = sprintf('%.5f', microtime(true) - $tt1);
                _status(1, VHOST . '(�ļ�ϵͳ��д)' . ADD_PROJECT, date('H'), "{$gz_dir}*{$i_linux}*", GET_INCLUDED_FILES . "/{$_GET['act']}", VIP, $diff_time);
                $this->_web_log("/home/webid/logs/{$logfilename}");
            }
        } else {
            $logfilename = date('Y_m_d_H', strtotime('-1 hours')) . '_access.log';
            $this->_web_log($this->log_path . $logfilename);
        }

        //php������־
        $log_file = '/home/webid/logs/php_error.log';
        $arr = file($log_file);
        foreach ($arr as $k => $v) {
            if (trim($v) !== '' || $v != 0) {
                if (strpos($v, 'PHP Warning')) {
                    _status(1, VHOST . '(BUG����)', 'PHP����[��־]', 'PHP������־', NULL, VIP);
                } else {
                    $v = substr($v, 22);
                    _status(1, VHOST . '(BUG����)', 'PHP����[��־]', 'PHP������־', $v, VIP);
                }
            }
        }
        die("\n" . date("Y-m-d H:i:s") . ',file:' . __FILE__ . ',line:' . __LINE__ . "\n");
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-11-27 17:11:52
     * @throws ע��:��DB�쳣����
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
                    $ip = '����ip';
                if ($log_status == 1) {
                    _status($value, VHOST . '(WEB��־����)', $status, $ip, null, VIP, 0, NULL, strtotime('-1 hours'));
                } else {
                    _status($value, VHOST . '(WEB��־����)', $status, $ip, $result[$status][$ip], VIP, 0, NULL, strtotime('-1 hours'));
                }
            }
        }
    }

    /**
     * @desc WHAT?
     * @author ��꿕� mailto:xzhwang@ppstream.com
     * @since  2013-02-21 10:41:49
     * @throws ע��:��DB�쳣����
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
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2013-03-25 15:36:11
     * @throws ע��:��DB�쳣����
     */

    function memcache_clear()
    {
        if (!$_GET['host'] || !$_GET['port'])
            die("ȱ�ٲ���:host/port=?\n");

        //֮ǰ
        $memcache_server = new memcache;
        $memcache_server->connect($_GET['host'], $_GET['port']);
        $memcache_server->set("flush_memcache", date('Y-m-d H:i:s'));
        var_dump($memcache_server->get("flush_memcache"));
        $x = $memcache_server->getStats();
        $memcache_server->flush();
        $memcache_server->close();
        print_r($_GET);
        print_r('��ʹ��(M):' . $x["bytes"] / 1048576);
        echo "\n";
        print_r('KEY����:' . $x["curr_items"]);
        echo "\n";

        //֮��
        $memcache_server->connect($_GET['host'], $_GET['port']);
        $x = $memcache_server->getStats();
        print_r('��ʹ��(M):' . $x["bytes"] / 1048576);
        echo "\n";
        print_r('KEY����:' . $x["curr_items"]);
        echo "\n";
        var_dump($memcache_server->get("flush_memcache"));
        $memcache_server->close();
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2013-05-24 13:25:32
     * @throws ע��:��DB�쳣����
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
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2013-05-24 15:51:45
     * @throws ע��:��DB�쳣����
     */
    function crontab_report_pinfen()
    {
        ini_set("display_errors", true);
        $conn_db = _ocilogon($this->db);
        //��ȡV1���������Ҫ��
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
        //��ȡV2���������Ҫ��
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

