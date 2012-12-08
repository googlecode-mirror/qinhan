<?php
$root_url = $_SERVER['PHP_SELF'];
$cmd = '';
$result = array();
$query = '';
if(!empty($_GET['query'])) {
	$query = $_GET['query'];
	$result = query($query);
}

if(!empty($_GET['del'])) {
	$del = $_GET['del'];
	del($del);
}

if(!empty($_GET['delAll'])) {
	$delAll = $_GET['delAll'];
	delAll($delAll);
}

function query($query) {
	$client = 'C:\redis\redis-cli.exe -h 192.168.100.60 -p 6379 ';
	exec($client . $query, $result);
	return $result;
}

function del($del, $over = true) {
	$query = 'del ' . $del;
	$result = query($query);
	if($over) {
		alert('删除成功');
	} else {
		return $result;
	}
}

function delAll($delAll) {
	$result = query($delAll);
	foreach($result as $r) {
		del($r, false);
	}
	alert('删除成功');
}

function alert($msg) {
	header("Content-Type:text/html; charset=utf-8");
	echo '<script type="text/javascript">alert("' . $msg . '"); history.back(-1);</script>';
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>redis管理中心</title>
<style type="text/css">
body {background: #CCE8CF}
#warp {margin-left: 50px;}
form {display: inline;}
a {color:gray;}
</style>
<script type="text/javascript">
var root_url = '<?php echo $root_url; ?>';
function $(id) {
	return document.getElementById(id);
}
function query() {
	var cmd = $('cmd').value;
	window.location.href = root_url + '?query=' + cmd;
}
function delAll() {
	var cmd = $('cmd').value;
	if(confirm('确定删除吗')) {
		window.location.href = root_url + '?delAll=' + cmd;
	}
}
function del(key) {
	if(confirm('确定删除吗')) {
		window.location.href = root_url + '?del=' + key;
	}
}
</script>
</head>
<body>
<div id="warp">

<div class="line">
<textarea id="cmd" name="cmd" rows="2" cols="80"><?php echo $query; ?></textarea>
<input type="button" value="查询" onclick="query()" />
<input type="button" value="删除全部" onclick="delAll()" />
</div>

<?php foreach($result as $r) { ?>
<div class="line">
<a href="javascript:;" onclick="del('<?php echo $r; ?>')">删除</a>
<?php echo $r; ?>
</div>
<?php } ?>

</div>
</body>
</html>