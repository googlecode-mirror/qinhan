<?php

//���ֱ�־�ж�
if (!defined('VHOST')) {
    $get_included_files = get_included_files();
    array_pop($get_included_files);
    $arr_remove = array(
        '/httpd/dou/',
        '/httpd/pay.game.pps.tv/',
        '/httpd/events/',
        '/httpd/pay.game.test.pps.tv/',
        '/httpd/paytest/',
        '/httpd/testdou/'
    );
    foreach($arr_remove as $v){
        if(strpos($get_included_files[0],$v)){
            return;
        }
    }
    $dirname = dirname(__FILE__);

    include_once $dirname . "/header.php";
    _status(1, VHOST . '(��ȫBUG)', "�ϴ�ľ������", var_export($get_included_files, true), var_export($_SERVER, true), VIP, $diff_time);
    _status(1, VHOST . '(BUG����)', "�ϴ�ľ������", var_export($get_included_files, true), var_export($_SERVER, true), VIP, $diff_time);
}
