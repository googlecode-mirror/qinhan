<?php
header("Content-type:text/html; charset=utf-8");
require_once "./include/common.inc.php";

$var = 'abc';
$arr = array(1, 2, 3);

include template('index');
?>
