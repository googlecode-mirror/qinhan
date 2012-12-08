<?php  
$s = '123<!--文字提示区<table width="650" border="0" cellpadding="0" cellspacing="0"> <tbody><tr><td height="30" valign="middle" align="left" style="font-size:12px;color:#FF5500;">亲爱的返利网会员：1&nbsp;&nbsp;<a href="http://huodong.51fanli.com/public/receivecoupon?link_id=7&gift_id=605284&uemail={$email}&code={$code}" target="_blank" style="color:#FF5500;text-decoration:none;">点击查看&gt;&gt;</a></td></tr></tbody></table>-->';

$pattern = array (
	"/\<\!\-\-((?!if).)+?\-\-\>/is",
	"'  '",
);
$replace = array (
	"",
	"",
);
echo $html = preg_replace($pattern, $replace, $s);
?>