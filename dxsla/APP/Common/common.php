<?php
function getReferer() {
	$url = isset($_SERVER ['HTTP_REFERER']) ? $_SERVER ['HTTP_REFERER'] : '/';
	$host = preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $url);
	if(!strstr($host, 'jianjiandandan.ivu1314.com') || strstr($url, $GLOBALS['s']['urlsite'] . '/member')) $url = '/';
	return $url;
}

function checkpost() {
    $referer = preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']);
    $host = preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST']);
    if($_SERVER['REQUEST_METHOD'] == 'POST' && ($referer == $host || $referer == 'qinhan001.sinaapp.com')) {
	    return TRUE;
	} else {
	    return FALSE;
	}
}

//type=0 得到的是普通的
//type=1 得到的是整型
//type=array() 限定得到的值必须是array里面的元素
function getvar($name, $type = 0) {
    $value = isset($_GET[$name]) ? $_GET[$name] : '';
	return exp_var($value, $type);
}

function postvar($name, $type = 0) {
    $value = isset($_POST[$name]) ? $_POST[$name] : '';
	return exp_var($value, $type);
}

function exp_var($value, $type) {
    $value = trim($value);
    if(is_array($type)) {
		return in_array($value, $type) ? $value : $type[0];
	} elseif($type == 1) {
		return intval($value);
	} else {
		return htmlspecialchars($value);
	}
}

//匹配表情
function exp_content($content) {
	$pattern = '/\[img\](mr|tsj|kb)(\/\d{3}.gif)\[\/img\]/is';
	$replacement = "<img src=\"{$GLOBALS['s']['urlstatic']}/face/\\1\\2\" />";
	return preg_replace($pattern, $replacement, $content);
}

//以","连接的字符串，变成用or连接的where查询语句
//字段必须为整型
function orsql($field, $str) {
	$arr = explode(',', $str);
	$str = $dot = '';
	foreach($arr as $id) {
		$id = intval($id);
		$str .= "{$dot}{$field}=$id";
		$dot = " OR ";
	}
	return $str;
}

function getwh($path, $w, $h) {
	$str = substr(strrchr($path, '_'), 1, -4);
	$arr = explode('x', $str);
	$scale = min($w/$arr[0], $h/$arr[1]);
	if($scale > 1) {
		return join(',', $arr);
	} else {
		$w = round($arr[0] * $scale);
		$h = round($arr[1] * $scale);
		return "$w,$h";
	}
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
function is_online($time, $type = 0) {
	$rs = '';
	$dur = time () - $time;
	if($type == 1) {
		$rs = $dur < 7200 ? 'gif' : 'png';
	} elseif($type == 2) {
		$rs = $dur < 7200 ? 'fb_14' : 'fs_12';
 	} else {
		if($dur < 7200) {
			$rs = '当前在线！';
		} elseif($dur < 14400) {
			$rs = '刚才在线！';
		} elseif($dur < 86400) {
			$rs = '今天来过(24H)';
		} else {
			$rs = '最近登录';
		}
	}
	return $rs;
}

function getAge($y) {
	if(!$y) return 20;
    else return date("Y") - $y;
}

function ui_sex($sex = 1, $type = 1) {
	$rs = '';
	switch($type) {
		case 0:
			$rs = $sex == 1 ? '男' : '女';
			break;
		case 1:
			$rs = $sex == 1 ? '他' : '她';
			break;
		case 2:
			$rs = $sex == 1 ? '找美女' : '找帅哥';
			break;
		case 3:
			$rs = $sex == 1 ? '女' : '男';
			break;
		case 4:
			$rs = $sex == 1 ? 'm' : 'w';
			break;
		case 5:
			$rs = $sex == 1 ? '安全感' : '气质';
			break;
		case 6:
			$rs = $sex == 1 ? 'boy' : 'girl';
			break;
		case 7:
			$rs = $sex == 1 ? 'man' : 'woman';
			break;
		case 8:
			$rs = $sex == 1 ? 'man' : 'women';
			break;
		case 9:
			$rs = $sex == 1 ? '一片叶子' : '一朵小花';
			break;				
		default:
			break;	
	}
	return $rs ;
}

function user_tag($m) {
	$str = '';
	$str .= empty($m['height']) ? '' : $m['height'] . 'cm';
	$str .= empty($m['hometown_prov']) ? '' : '，' . $m['hometown_prov'];
	$str .= empty($m['hometown_city']) ? '' : ' ' . $m['hometown_city'];
	$str .= empty($m['birth_y']) ? '' : '，' . getAge($m['birth_y']) . '岁';
	$str .= empty($m['constellation']) ? '' : '，' . $m['constellation'];
	$str = '&nbsp;' . ltrim($str, '，');
	return $str;	
}

function do_things($want_content, $sex) {
	if(!$want_content) return '';
	$rs = '<span class="fb_13">“</span><span>想和一个' . ui_sex($sex, 3) . '生,' . $want_content . '</span><span class="fb_13 f_0">”</span>';
	return $rs;
}

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
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
?>