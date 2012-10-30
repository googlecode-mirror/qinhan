<?php
$top_appkey = !empty($_GET['top_appkey'])? iconv("GB2312", "utf-8//IGNORE", trim($_GET['top_appkey'])):'';
$top_parameters = !empty($_GET['top_parameters'])? iconv("GB2312", "utf-8//IGNORE", trim($_GET['top_parameters'])):'';
$top_session = !empty($_GET['top_session'])? iconv("GB2312", "utf-8//IGNORE", trim($_GET['top_session'])):'';
$top_sign = !empty($_GET['top_sign'])? iconv("GB2312", "utf-8//IGNORE", trim($_GET['top_sign'])):'';
$app_secret = 'ee363b0bcbe797cc4f6c3dbbb5c6f05e';
		
$parameters = base64_decode($top_parameters)."&";
preg_match_all('/visitor_id=([^&]+)&visitor_nick=([^&]+)&/', $parameters, $arr);
$taouid = intval($arr[1][0]);
$username = iconv("GB2312", "utf-8//IGNORE", $arr[2][0]);

setcookie('open[type]', 'tao', time() + 86400, '/', 'xiudang.com');
$field = 'taouid'; 
$value = $taouid;
if($value == 0) exit('系统繁忙，请稍候再试');
require_once('../../includes/config.inc.php');
require_once('../../includes/function.inc.php');

$Sql = "SELECT js_user.uid, username, email, password, isrank FROM  js_user JOIN js_user_open ON js_user.uid=js_user_open.uid WHERE js_user_open.$field = '$value'";
$result = mysql_query($Sql);
if ($rs = mysql_fetch_array($result)) {
	$cookietime = $cookietime1 = time()+86400;
	if ($rs['isrank']<1) exit('用户名已被锁定');
	$xiuCode = md5($rs['password'].COOKIE_CODE.$rs['email']);
	setcookie(COOKIE_NAME . '_xiuUserID', $rs['uid'], $cookietime1, "/", 'xiudang.com');
	setcookie(COOKIE_NAME . '_xiuUserName', $username, $cookietime1, "/", 'xiudang.com');
	setcookie(COOKIE_NAME . '_xiuCode', $xiuCode, $cookietime, "/", 'xiudang.com');
	$Sql = "UPDATE `js_user` SET logintime='{$logintime}', loginip='{$loginip}', loginnum = loginnum+1, logintype = 'tao' WHERE `uid`='{$rs['uid']}'";
	mysql_query($Sql);
	
	$referer = 'http://www.xiudang.com/';	
	if (isset($_COOKIE['referer'])) $referer = $_COOKIE['referer'];
	header("Location: $referer");
	die();
} else {
	setcookie('open[uid]', $taouid, 0, '/', 'xiudang.com');
	setcookie('open[name]', $username, 0, '/', 'xiudang.com');
	//$sex = $me->sex ? '男' : '女';
	setcookie('open[sex]', '', 0, '/', 'xiudang.com');
	setcookie('open[pic]', '', 0, '/', 'xiudang.com');
	setcookie('open[province]', '', 0, '/', 'xiudang.com');
	setcookie('open[city]', '', 0, '/', 'xiudang.com');
	

	header("Location: /apilogin.html");
}
?>