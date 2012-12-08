<?php
//$conf = '/home/sambauser/vhosts.conf';
$conf = 'D:\wamp\bin\apache\Apache2.2.17\conf\extra\httpd-vhosts.conf';
$db_file = 'db.json';
$root_url = $_SERVER['PHP_SELF'];
$config_paths = array(
	'default' => array('/Conf'),
	'static2.51fanli.net' => array(),
	'tg.51fanli.com' => array('/Conf', '/web/Conf'),
	'search.51fanli.com' => array('/Conf', '/web/Conf'),
	'rbac.51fanli.com' => array('/Conf', '/Conf/Activity', '/Conf/Channel', '/Conf/Fltong', '/Conf/Goshop', '/Conf/Homepage', '/Conf/Order', '/Conf/Shopcity', '/Conf/Taobao', '/Conf/Tongji', '/Conf/Tuan', '/Conf/Catemanage', '/Conf/Config', '/Conf/Coupon', '/Conf/Game', '/Conf/Home', '/Conf/Operation', '/Conf/Permission', '/Conf/Tgfanli', '/Conf/Travel'),
);
$config_replace_params = array(
	'search' => array(
		'192.168.0.175',
		'192.168.0.178',
		'sqlsrv:server=192.168.0.197;Database=51fanli',
		'sqlsrv:server=192.168.0.142;Database=51fanli',
		'192.168.0.197',
		'odbc:TEST1dsn'
	),
	'replace' => array(
		'192.168.100.60',
		'192.168.100.60',
		'sqlsrv:server=mdb.zz.51fanli.it;Database=51fanli',
		'sqlsrv:server=mdb.zz.51fanli.it;Database=51fanli',
		'192.168.100.60',
		'sqlsrv:server=mdb.zz.51fanli.it;Database=51fanli',
	)
);

$str = file_get_contents($conf);
//modifiyConf(1, 2, 'static2.51fanli.net');
preg_match_all('/<VirtualHost[\s\S]+<\/VirtualHost>/isU', $str, $arr);
$vhosts = array();
foreach($arr[0] as $k => $host) {
	preg_match('/ServerName[\s]+([\S]+)/is', $host, $arr1);
	preg_match('/DocumentRoot[\s]+"([^"]+)"/is', $host, $arr2);
	$vhosts[$k]['serverName'] = $arr1[1];
	$vhosts[$k]['root'] = htmlentities($arr2[1]);
}
$db = json_decode(@file_get_contents($db_file), true);

if(!empty($_POST)) {
	$old_root = trim($_POST['old_root']);
	$new_root = trim($_POST['new_root']);
	$serverName = $_GET['serverName'];
	
	recordHistory($old_root, $new_root, $serverName); //记录历史
	modifiyConf($old_root, $new_root, $serverName); //修改apache的conf
	modifyConfFile($new_root, $serverName, $serverName); //修改配置文件
	restartApache(); //重启apache
	echo '<script type="text/javascript">alert(\'修改成功\');location.href=\'' . $root_url . '\';</script>';
	exit();
}

if(isset($_GET['serverName']) && isset($_GET['id'])) {
	$serverName = $_GET['serverName'];
	$id = $_GET['id'];

	foreach($vhosts as $host) {
		if($host['serverName'] == $serverName) {
			$old_root = $host['root'];
			break;
		}
	}	
	if(!$old_root) exit('error');
	$new_root = $db[$serverName][$id];
	
	recordHistory($old_root, $new_root, $serverName); //记录历史
	modifiyConf($old_root, $new_root, $serverName); //修改apache的conf
	restartApache(); //重启apache
	echo '<script type="text/javascript">alert(\'switch success\');location.href=\'' . $root_url . '\';</script>';
	exit();
}

function recordHistory($old_root, $new_root, $serverName) {
	global $vhosts, $db, $db_file;
	if($old_root == $new_root) {
		return false;
	}
	if(empty($db[$serverName])) {
		$db[$serverName] = array();
	}
	if($index = array_search($new_root, $db[$serverName])) {
		$db[$serverName] = array_splice($db[$serverName], $index - 1, 1);
	}
	if(!in_array($old_root, $db[$serverName])) {
		array_unshift($db[$serverName], $old_root);
		$db[$serverName] = array_slice($db[$serverName], 0, 3);
		file_put_contents($db_file, json_encode($db));
	}
}

function modifiyConf($old_root, $new_root, $serverName) {
	global $conf;
	$old_root = "\"$old_root\"";
	$new_root = "\"$new_root\"";
	$str = file_get_contents($conf);
	//$mark = str_replace('.', '\.', $serverName);
	//$mark = substr($mark, 0, 5);
	//$mark = 'localhost';
	//preg_match('/<VirtualHost(?(localhost).)+?<\/VirtualHost>/is', $str, $arr);
	//print_r($arr);
	//echo '/[VirtualHost((?!VirtualHost).)+?(' . $mark . ').+?((?!VirtualHost).)+?<\/VirtualHost]/is';
	//exit();
	//$old_str = $arr[0];
	//$new_str = str_replace($old_root, $new_root, $old_str);
	$str = str_replace($old_root, $new_root, $str);
	file_put_contents($conf, $str);
}

function modifyConfFile($new_root, $serverName) {
	global $config_paths, $config_replace_params;
	$paths = isset($config_paths[$serverName]) ? $config_paths[$serverName] : $config_paths['default'];
	//var_dump($paths);
	foreach($paths as $path) {
		$file = $new_root . $path . '/config.php';
		if(is_file($file)) {
			$str = file_get_contents($file);
			$str = str_replace($config_replace_params['search'], $config_replace_params['replace'], $str);
			file_put_contents($file, $str);
		}
	}
}

function restartApache() {
	if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
	} else {
		echo shell_exec("/usr/bin/sudo /usr/sbin/apachectl -k restart");
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>vhost管理中心</title>
<style type="text/css">
body {background: #CCE8CF}
#warp {margin-left: 50px;}
form {display: inline;}
a {color:gray;}
</style>
</head>
<body>
<div id="warp">

<?php foreach($vhosts as $host) { ?>
<div class="line" onmouseover="this.style.background = '#CCE8CF'" onmouseout="this.style.background = '#CCE8CF'">
<input type="text" name="serverName" value="<?php echo $host['serverName']; ?>" size="20" disabled="disabled" />
<form action="<?php echo $root_url . '?serverName=' . $host['serverName']; ?>" method="post">
<input type="text" name="new_root" value="<?php echo $host['root']; ?>" size="50" />
<input type="hidden" name="old_root" value="<?php echo $host['root']; ?>" />
<input type="submit" value="修改" />
</form>
<?php
if(!empty($db[$host['serverName']])) {
foreach($db[$host['serverName']] as $k => $root) {
?>
<input type="text" name="history_root" value="<?php echo $root; ?>" size="30" disabled="disabled" /><a href="<?php echo $root_url; ?>?serverName=<?php echo $host['serverName']; ?>&id=<?php echo $k; ?>">切换</a>
<?php
}}
?>
</div>
<?php } ?>

</div>
</body>
</html>