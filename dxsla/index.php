<?php
if(strcmp($_SERVER['HTTP_HOST'], 'landezhao.com') == 0 ||
	strcmp($_SERVER['HTTP_HOST'], 'www.landezhao.com') == 0) {
	include 'landezhao.html';
	exit();
}
if(strcmp($_SERVER['HTTP_HOST'], 'dxslaw.com') == 0) {
	header("HTTP/1.1 301 Moved Permanently");
	header('Location: http://www.dxslaw.com' . $_SERVER['REQUEST_URI']);
	exit();
}
$s = isset($_GET['s']) ? $_GET['s'] : '';
preg_match('/([^?]+)\?([^\=]+)\=([^&]+)/', $s, $match);
if(count($match) == 4) {
    $_GET['s'] = $match[1];
	$_GET[$match[2]] = $match[3];
}

define('THINK_PATH', './ThinkPHP/');
define('APP_NAME', '');
define('APP_PATH', './APP/');
define('APP_DEBUG', FALSE);
//define('APP_DEBUG', TRUE);
require(THINK_PATH."/ThinkPHP.php");
?>