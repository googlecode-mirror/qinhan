<?php
/*
* Web PL/SQL主页
* @author			王钟凯 (Kevin) 蔡旭东(fifsky)
* @E-mail			328725540@qq.com kevin@dev.ppstream.com fifsky@qq.com
* @version			1.0
*
* @Update 2013-05-10 从admin.y后台扣出来
*/
include 'header.php';
header('Content-Type: text/html;charset=gbk');
if (LOGINING !== true) {
    if (!headers_sent())
        setcookie('project_location', $_SERVER["REQUEST_URI"]);
    if (is_file("admin.php"))
        die(header("location: /admin.php"));
    if (is_file("index.php"))
        die(header("location: /index.php"));
    die("No input file specified." . date('r'));
}

include 'project/webplsql/action_compatible_ajax.php';

$c = $_REQUEST['c'] ? $_REQUEST['c'] : "";

if($c == 'index'){
    include 'project/webplsql/view/index.php';
    exit;
}

if(file_exists('project/webplsql/'.$c.'.php')){
    include 'project/webplsql/'.$c.'.php';
    $class_name = 'WebPlSql'.$c;
    $controller = new $class_name();

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $controller->post();
    }else{
        $controller->exec();
    }
}else{
    die("No input file specified." . date('r'));
}