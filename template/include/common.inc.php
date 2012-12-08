<?php
define('IN_SITE', TRUE);
define('SITE_ROOT', substr(dirname(__FILE__), 0, -7));

require_once SITE_ROOT.'./include/template.func.php';

$tplrefresh = 1;                              //设置是否检查更新
$tpldir = SITE_ROOT.'./templates/default/';   //模板存放目录
$objdir = SITE_ROOT.'./sitedata/templates/';  //模板编译文件存放目录
?>
