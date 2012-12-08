<?php if(!defined('IN_SITE')) exit('Access Denied!'); checktplrefresh('D:\MYPHP2\ppp\./templates/default/index.htm', 1292648797); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>无标题文档</title>
</head>

<body>
输出变量：<?php echo $var; ?>
<br />
输出数组：<?php if(is_array($arr)) { foreach($arr as $v) { echo $v; ?> |<?php } } ?></body>
</html>
