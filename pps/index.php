<?php

/* Eclipse: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
//
// +----------------------------------------------------------------------+
// | PHP Version 4-5                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2005-2011    All rights reserved.                      |
// +----------------------------------------------------------------------+
// | This source file is not free   GBK   Encoding!                       |
// +----------------------------------------------------------------------+
// | Authors: xltxlm <resia@dev.ppstream.com>                             |
// +----------------------------------------------------------------------+
//
////wangxinzhao 创建于:2012-08-16 16:32:11
//备注:
//保质期:
include "header.php";
define('APP_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . './app/');
include APP_PATH . "common/common.class.php";

$_GET['act'] = isset($_GET['act']) ? $_GET['act'] : "index";
$_GET['act_method'] = isset($_GET['act_method']) ? $_GET['act_method'] : "index";
$file = APP_PATH . './action/' . $_GET['act'] . '.php';

if (file_exists($file)) {
    include $file;
    if (class_exists($_GET['act'])) {
        $a = new $_GET['act']();
        if (method_exists($a, $_GET['act_method'])) {
            construct_log();
            $a->$_GET['act_method']();
        } else {
            define('_php_runtime', false);
            exit("{$_GET['act_method']} method not found");
        }
    } else {
        define('_php_runtime', false);
        exit("{$_GET['act']} class not found");
    }
} else {
    define('_php_runtime', false);
    exit("{$_GET['act']} file not found");
}
?>