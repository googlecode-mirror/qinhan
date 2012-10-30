<?php
$k = isset($_GET['k']) ? $_GET['k'] : '';

if(!$k) exit();
$arr = explode('|', authcode($k));

//echo "<!--", authcode($k), "-->";

$count = sizeof($arr);
if($count != 3) exit();

$i = $arr[0];
$u = $arr[1];
$roomid = encrypt($arr[2]);
$roomdir = 'pub/cache/' . substr($roomid, 0, 2) . '/' . substr($roomid, 2, 2) . '/' . substr($roomid, 4);

if(isset($_POST['clean'])) {
    exit(0);
}

if(isset($_POST['getdata'])) {
    exit(0);
}

if(isset($_POST['content'])) {
	$content = $_POST['content'];
	$content = isset($_GET['skip']) ? $content : htmlspecialchars(trim($content));
    if(strlen($content) > 800) exit();
	$content = exp_content($content);
	$data = array('uid' => $i, 'content' => $content, 'add_time' => time());
	
    $file = $roomdir . '/' . $i . '.php';
	$tmp = microtime() . json_encode($data);
	if(!is_dir($roomdir)) mkdirs($roomdir);
	file_put_contents($file, $tmp);

    exit(0);
}

$file = $roomdir . '/' . $u . '.php';
$url = $file;
$k = urlencode($k);
$referer = preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']);
if(!$referer) $referer = 'www.dxslaw.com';

function mkdirs($dir){
	return is_dir($dir) || (mkdirs(dirname($dir)) && mkdir($dir, 0777));
}
//匹配表情
function exp_content($content) {
	$pattern = '/\[img\](mr|tsj|kb)(\/\d{3}.gif)\[\/img\]/is';
	$replacement = "<img src=\"http://pic.dxslaw.com/CDN/app/face/\\1\\2\" />";
	return preg_replace($pattern, $replacement, $content);
}
function authcode($string, $operation = 'DECODE', $key = 'TikAs2qtoFbiH7JMM34h7dD8ArhuA8XHipM7', $expiry = 86400) {
    $ckey_length = 4;
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length):

    substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :

    sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0)
            && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
function encrypt($id) {
	if($id > 100) {
		$id = (string) $id;
		$len = strlen($id) - 1;
		$id = $id{0} . $id{$len} . substr($id, 2, -1). $id{1};
	}
	$id += 60512868;
	$str = base_convert($id, 10, 36);
    return substr($str, 0, -2) . xchange($str{4}) . xchange($str{5});
}
function decrypt($str) {
	$str = substr($str, 0, -2) . xchange($str{4}, 1) . xchange($str{5}, 1);
    $id = base_convert($str, 36, 10);
	$id -= 60512868;
	if($id > 100) {
		$id = (string) $id;
		$len = strlen($id) - 1;
		return $id{0} . $id{$len} . substr($id, 2, -1). $id{1};
	} else {
        return $id;
    }
}
function xchange($s, $decode = 0) {
	if($decode) {
		$str = "ytuvsrqzxwilng7fed2cbajk1096h53m8o4p";
	} else {
		$str = "poiuytrewqlkjhgfdsamnbvcxz6541239807";
	}
	$s = base_convert($s, 36, 10);
	return $str{$s};
}
function formatTime($time) {
	$dur = time () - $time;
	$today24 = time () - (time () + 3600 * 8) % 86400 + 86400;
	$day = floor(($today24 - $time) / 86400);
	if($dur < 10) {
		return '刚刚';
	} elseif($dur < 60) {
		return $dur . '秒前';
	} elseif($dur < 3600) {
		return floor($dur / 60) . '分钟前';
	} elseif($day < 1) {
		return "今天 " . date('H:i', $time);
	} elseif($day < 2) {
		return "昨天 " . date('H:i', $time);
	} elseif($day < 3) {
        return "前天 " . date('H:i', $time);
    } else {
        return date('Y-m-d H:i', $time);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>大学生恋爱网 在线聊天</title>
<style type="text/css">
* {margin:0; padding:0;}
html,body,table,td,th,img,form, input, ul,ol, li,dl,dt,dd, p, h1, h2, h3, h4, h5, h6,textarea {margin:0; padding:0;}
body{width:100%;color:#000;font-size:13px;line-height:1.5;font-family:Arial,Helvetica,sans-serif ,"宋体","微软雅黑";text-align:left;background:#fff;margin:0;padding:0;}
ul, ol {list-style:none;}
a {color:#1A4DC1; cursor:pointer; outline:medium none;}
.f_6, a.f_6 {color:#666666;}
.fb_13 {font-size:13px;}
.f_0, a.f_0 {color:#000000;}
.btn1,.btn2,.btn3,.btn4{background:url("pub/buttons_bg.png") no-repeat left center; cursor:pointer; padding:3px 6px; line-height:18px; font-size:13px; font-weight:normal; color:#FFF; text-align:center; display:inline-block;}
.btn1{background-position:0 -45px; border:1px solid #225FA4;}
input.btn1{line-height:18px;}
textarea,input{box-shadow:2px 2px 2px rgba(0, 0, 0, 0.1) inset;}
.clear:after{content:"."; display:block; clear:both; height:0px; visibility:hidden;}
.invitebox{min-width:200px;padding:5px 0 0 5px;_padding:10px 0 5px 5px;_display:inline-block;}
.invitebox_l{padding-left:10px;max-width:170px;}
.fl {float: left;}
.cur {cursor: pointer;}
.f_blue, a.f_blue {color:#1A4DC1;}
.invitebox_l .font_list {color:#245902; cursor:pointer; font-size: 14px; font-weight:bolder; text-decoration:underline;}
</style>
<link href="pub/chatbox.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script language="javascript" src="pub/pub_face_all.js"></script>
<script language="javascript" src="pub/client.js"></script>
<script type="text/javascript">
//document.domain = 'sinaapp.com';
var connect_url = '<?php echo $url; ?>';
var post_url = 'im.php?k=<?php echo $k; ?>';
var msg_height = <?php echo $u > 0 ? 225 : 92; ?>;
$(document).ready(function() {
	$('#messages').css('height', $(window).height() - msg_height);
	$(window).bind("resize", function () {
		$('#messages').css('height', $(window).height() - msg_height); 
	});
    gethistory(0);
    //ajaxpost(post_url, 'clean=true', function(data) { setInterval(connect, 1000); }, false);
	setInterval(connect, 1000);
    qh_$('chat_msg').onkeyup = function() {
        var keyCode = getKeyCode();
        if(keyCode == 13) post();
    }	
});
</script>
</head>
<body>
<iframe name="ajaxIframe" src="http://qinhan001-cache.stor.sinaapp.com/domain.html" id="ajaxIframe" style="display:none"></iframe>
<form id="postForm" action="http://<?php echo $referer; ?>/index.php?s=/msg/api/?k=<?php echo $k; ?>&id=<?php echo $roomid; ?>" target="postIframe" style="display:none;" method="post"><input type="text" id="postcontent" name="postcontent" /></form>
<iframe name="postIframe" src="" id="postIframe" style="display:none"></iframe>
<div style="display:none;"><img src="http://pic.dxslaw.com/CDN/app/face/mr.png" /></div>
<div class="w_box">
  <div class="chat_box">
    <div class="interlocutor">
      <iframe src="http://<?php echo $referer; ?>/index.php?s=/msg/userinfo/?k=<?php echo $k; ?>&id=<?php echo $roomid; ?>" frameborder="0" scrolling="no" height="92" width="100%"></iframe>
    </div>
    <div id="messages" class="messages" style="height:121px; overflow-y:scroll;">
      <ul id="msg_history" style="padding-bottom:0;"></ul>
      <ul id="chat_msg_show" style="padding-top:0;">
      </ul>
    </div>
  </div>
  <?php if($u > 0) { ?>
  <div class="type">
    <div class="type-wrap">
      <div contenteditable="true" id="chat_msg" class="textarea"></div>
      <div class="submit"> <span>
        <input type="button" onclick="post()" id="chat_send" class="btn1" value="发送">
        </span><span class="tools clear"><a onclick="face51New.show(this,'chat_msg','_div');" class="smile_door" href="javascript:;"></a>
        <div id="chat_face" style="position:absolute;left:70px;bottom:28px;width:250px;height:240px;background:#f7f7f7;border:1px solid #ccc;display:none;">
          <div class="title">
            <h3>插入表情</h3>
            <a style="position:absolute;top:5px;right:10px;width:9px;height:9px;overflow:hidden;background:url(http://pic.jjdd.com/v1/i/pub/close.gif)" onclick="IM.faceClose()" href="javascript:;"></a></div>
          <div class="contain"> </div>
        </div>
        </span>
	  </div>
    </div>
  </div>
  <?php } ?>
</div>
</body>
</html>
