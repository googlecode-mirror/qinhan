<?php
ini_set('display_erros', 'On');
error_reporting(E_ALL);

$arr = array(
    'get_user_actions' => array(
        'url' => '/index.php',
        'act' => 'get_user_actions',
        'method' => 'get',
        'params' => array(
            'show_friends' => '0',
            'cat' => '0',
            'order' => '0',
            'status' => '0',
            'dateline' => '',
            'page' => '1',
            'size' => '25',
        ),
    ),
    'show_action' => array(
        'url' => '/index.php',
        'act' => 'show_action',
        'method' => 'get',
        'params' => array(
            'action_id' => '257725',
            'page' => '1',
            'size' => '25',
        ),
    ),
    'get_user_ping_actions' => array(
        'url' => '/index.php',
        'act' => 'get_user_ping_actions',
        'method' => 'get',
        'params' => array(
            'order' => '0',
            'page' => '1',
            'size' => '25',
        ),
    ),
    'get_user_actions_mem' => array(
        'url' => '/index.php',
        'act' => 'get_user_actions_mem',
        'method' => 'get',
        'params' => array(
            'show_friends' => '0',
            'cat' => '0',
            'order' => '0',
            'status' => '0',
            'dateline' => '',
            'page' => '1',
            'size' => '25',
        ),
    ),
    'get_user_ping_actions_mem' => array(
        'url' => '/index.php',
        'act' => 'get_user_ping_actions_mem',
        'method' => 'get',
        'params' => array(
            'page' => '1',
            'size' => '25',
        ),
    ),
    'receive_user_actions' => array(
        'url' => '/index.php',
        'act' => 'receive_user_actions',
        'method' => 'post',
        'params' => array(
            'operation' => '3',
            'to_user_id' => '186834546',
            'source_id' => '0',
            'parent_id' => '1208839',
            'bk_id' => '0',
			'video_title' => '西游记',
            'content' => '哇哇哇wa，cool',
        ),
    ),
    'del_user_actions' => array(
        'url' => '/index.php',
        'act' => 'del_user_actions',
        'method' => 'post',
        'params' => array(
            'id' => '1208839',
            'status' => '10000',
        ),
    ),
    'del_user_fav' => array(
        'url' => '/index.php',
        'act' => 'del_user_fav',
        'method' => 'post',
        'params' => array(
            'video_id' => '172963'
        ),
    ),
    'clear_user_favorite' => array(
        'url' => '/index.php',
        'act' => 'clear_user_favorite',
        'method' => 'get',
        'params' => array(),
    ),
    'add_user_link' => array(
        'url' => '/index.php',
        'act' => 'add_user_link',
        'method' => 'post',
        'params' => array(
            'link_id' => '172963',
            'link_type' => '3',
        ),
    ),
    'get_user_link' => array(
        'url' => '/index.php',
        'act' => 'get_user_link',
        'method' => 'post',
        'params' => array(
            'link_type' => '3',
        ),
    ),
    'get_link_status' => array(
        'url' => '/index.php',
        'act' => 'get_link_status',
        'method' => 'post',
        'params' => array(
            'link_id' => '172963',
            'link_type' => '3',
        ),
    ),
    'get_user_by_link_all' => array(
        'url' => '/index.php',
        'act' => 'get_user_by_link_all',
        'method' => 'post',
        'params' => array(
            'link_id' => '172963',
            'link_type' => '3',
        ),
    ),
    'is_login' => array(
        'url' => '/index.php',
        'act' => 'is_login',
        'method' => 'get',
        'params' => array(
        ),
    ),
	'user_info' => array(
        'url' => '/index.php',
        'act' => 'user_info',
        'method' => 'post',
        'params' => array(
        ),
    ),
    'get_user_by_link' => array(
        'url' => '/index.php',
        'act' => 'get_user_by_link',
        'method' => 'post',
        'params' => array(
            'link_id' => '172963',
            'link_type' => '3',
        ),
    ),
    'user_avatar' => array(
        'url' => '/index.php',
        'act' => 'user_avatar',
        'method' => 'post',
        'params' => '',
    ),
    'receive_user_info' => array(
        'url' => '/index.php',
        'act' => 'receive_user_info',
        'method' => 'post',
        'params' => array(
            'user_job' => '学生',
            'user_sex' => '1',
            'user_info' => '签名',
            'mobile' => '13900001111',
            'nick_name' => '昵称',
            'user_birth' => '1970-1-1',
            'user_sign' => '用户简介',
            'user_income' => '5000000',
        ),
    ),
    'get_verify_code' => array(
        'url' => '/index.php',
        'act' => 'get_verify_code',
        'method' => 'get',
        'params' => '',
    ),
    'user_bind_mobile' => array(
        'url' => '/index.php',
        'act' => 'user_bind_mobile',
        'method' => 'get',
        'params' => array(
            'mobile' => '13900001111',
            'verify_code' => 'LVG9SW',
        ),
    ),
    'add_user_mac' => array(
        'url' => '/interface.php',
        'act' => 'add_user_mac',
        'method' => 'post',
        'params' => array(
            'mac' => 'd0-57-4c-8e-b3-d6',
        ),
    ),
    'update_user_accept' => array(
        'url' => '/interface.php',
        'act' => 'update_user_accept',
        'method' => 'post',
        'params' => array(
            'accept_type' => '1',
            'accept_status' => '1',
        ),
    ),
    'get_user_info' => array(
        'url' => '/interface.php',
        'act' => 'get_user_info',
        'method' => 'get',
        'params' => '',
    ),
    'receive_user_coordinates' => array(
       'url' => '/index.php',
       'act' => 'receive_user_coordinates',
       'method' => 'get',
       'params' => array(
           'longitude' => '121.00',
           'latitude' => '131.00',
       ),
    ),
    'get_coordinates_users' => array(
       'url' => '/index.php',
       'act' => 'get_coordinates_users',
       'method' => 'get',
       'params' => array(
           'longitude' => '121.00',
           'latitude' => '131.00',
       ),
    ),
    'add_user_follow' => array(
       'url' => '/index.php',
       'act' => 'add_user_follow',
       'method' => 'post',
       'params' => array(
           'origin' => '2',
           'fuser_id' => '258582163',
       ),
    ),
    'del_user_follow' => array(
       'url' => '/index.php',
       'act' => 'del_user_follow',
       'method' => 'post',
       'params' => array(
           'fuser_id' => '258582163',
       ),
    ),
    'get_follow_status' => array(
       'url' => '/index.php',
       'act' => 'get_follow_status',
       'method' => 'get',
       'params' => array(
           'fuser_id' => '258582163',
       ),
    ),
    'get_user_follows' => array(
       'url' => '/index.php',
       'act' => 'get_user_follows',
       'method' => 'get',
       'params' => array(
           'login_user_id' => '258582163',
           'origin' => '0',
           'page' => '1',
           'size' => '20',
       ),
    ),
    'statuses_friends' => array(
       'url' => '/index.php',
       'act' => 'statuses_friends',
       'method' => 'get',
       'params' => array(
           'cursor' => '1',
           'count' => 20,
       ),
    ),
    'get_fans_num' => array(
       'url' => '/index.php',
       'act' => 'get_fans_num',
       'method' => 'get',
       'params' => array(
       ),
    ),
    'get_fans_list' => array(
       'url' => '/index.php',
       'act' => 'get_fans_list',
       'method' => 'get',
       'params' => array(
           'login_user_id' => '258582163',
           'page' => '1',
           'size' => '20',
       ),
    ),
    'get_follows_num' => array(
       'url' => '/index.php',
       'act' => 'get_follows_num',
       'method' => 'get',
       'params' => array(
       ),
    ),
    'push_to_ios' => array(
       'url' => '/index.php',
       'act' => 'push_to_ios',
       'method' => 'get',
       'params' => array(
           'mac' => 'd0-57-4c-8e-b3-d6',
           'msg' => 'test msg',
       ),
    ),
    'check_ping_actions' => array(
       'url' => '/interface.php',
       'act' => 'check_ping_actions',
       'method' => 'post',
       'params' => array(
           'id' => '1308790',
           'notice_status' => '1',
           'check_time' => time(),
       ),
    ),
);

$act = isset($_GET['act']) ? $_GET['act'] : 'get_user_info';
$act_info = $arr[$act];
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
	<title><?php echo $act; ?> API调试工具</title>
    <style type="text/css">
        a {color: blue;}
    </style>
    <script type="text/javascript" src="http://ppysq.pt.pps.tv/project/commstyle/js/jquery.js"></script>
    <script type="text/javascript">
        function chkForm() {
            $('#_form').attr('action', $('#_url').val());
            $('#_form').attr('target', '_iframe');
            $('#_form').submit();
        }
        function chkForm2() {
            $('#_form').attr('action', $('#_url').val());
            $('#_form').attr('target', '_blank');
            $('#_form').submit();
        }
        function md5_haha() {
            var text = $('#_md5').val();
            $('#_form').attr('target', '_iframe');
            $('#_token').val(hex_md5(text + '_pp$tv@2012'));
            return false;
        }
    </script>
</head>
<body bgcolor="#CCE8CF">
<table>
<?php foreach ($arr as $a) { ?>
&nbsp;
<a style="width: 250px; display: block; float: left;" href="?act=<?php echo $a['act']; ?>"><?php echo $a['act']; ?></a>
<? } ?>
</table>
<div style="clear: both; height: 15px;"></div>

<table width="100%"><tr><td width="50%" valign="top">
<?php if ($act_info['method'] == 'get') { ?>
<form id="_form" action="" method="get" target="_iframe">
<?php } else { ?>
<form id="_form" action="" method="post" enctype="multipart/form-data" target="_iframe">
<?php } ?>
    <table>
        <tr>
            <td>接口地址</td>
            <td><input type="text" id="_url" size="50" name="url"
                       value="http://ppysq.pt.pps.tv<?php echo $act_info['url']; ?>?act=<?php echo $act_info['act']; ?>"/>
            </td>
        </tr>
        <tr>
            <td>method</td>
            <td><input type="text" id="_method" name="method" value="<?php echo $act_info['method']; ?>"/></td>
        </tr>
        <tr>
            <td>act</td>
            <td><input type="text" name="act" value="<?php echo $act_info['act']; ?>"/></td>
        </tr>
        <tr>
            <td>token</td>
            <td><input type="text" size="35" name="token" id="_token" value=""/></td>
            <td><input type="test" name="md5" value="186834546" id="_md5"/><a href="javascript:;" onclick="md5_haha()">md5</a></td>
        </tr>
        <tr>
            <td>user_id</td>
            <td><input type="text" name="user_id" value=""/></td>
            <td><input type="file" size="4" name="user_icon"/>user_icon</td>
        </tr>
        <?php if (is_array($act_info['params'])) {
        foreach ($act_info['params'] as $k => $v) {
            ?>
            <tr>
                <td><?php echo $k; ?></td>
                <td><input type="text" name="<?php echo $k; ?>" value="<?php echo $v; ?>"/></td>
            </tr>
            <?php
        }
    } ?>
    </table>
    <button href="javascript:;" onclick="chkForm()">===提交===</button>
    &nbsp;
    <button href="javascript:;" onclick="chkForm2()">新窗口提交</button>
</form>

</td><td width="50%">
<iframe name="_iframe" id="_iframe" width="100%" height="400" frameborder="0" src="" border="0" marginheight="0"
        marginwidth="0" allowtransparency="true" style="border: 1px dashed gray;"></iframe>
</td></tr></table>

<script type="text/javascript">
var hexcase = 0;
/* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad = "";
/* base-64 pad character. "=" for strict RFC compliance   */
var chrsz = 8;
/* bits per input character. 8 - ASCII; 16 - Unicode      */

function hex_md5(s) {
    return binl2hex(core_md5(str2binl(s), s.length * chrsz));
}
function b64_md5(s) {
    return binl2b64(core_md5(str2binl(s), s.length * chrsz));
}
function str_md5(s) {
    return binl2str(core_md5(str2binl(s), s.length * chrsz));
}
function hex_hmac_md5(key, data) {
    return binl2hex(core_hmac_md5(key, data));
}
function b64_hmac_md5(key, data) {
    return binl2b64(core_hmac_md5(key, data));
}
function str_hmac_md5(key, data) {
    return binl2str(core_hmac_md5(key, data));
}

function md5_vm_test() {
    return hex_md5("abc") == "900150983cd24fb0d6963f7d28e17f72";
}

function core_md5(x, len) {
    /* append padding */
    x[len >> 5] |= 0x80 << ((len) % 32);
    x[(((len + 64) >>> 9) << 4) + 14] = len;

    var a = 1732584193;
    var b = -271733879;
    var c = -1732584194;
    var d = 271733878;

    for (var i = 0; i < x.length; i += 16) {
        var olda = a;
        var oldb = b;
        var oldc = c;
        var oldd = d;

        a = md5_ff(a, b, c, d, x[i + 0], 7, -680876936);
        d = md5_ff(d, a, b, c, x[i + 1], 12, -389564586);
        c = md5_ff(c, d, a, b, x[i + 2], 17, 606105819);
        b = md5_ff(b, c, d, a, x[i + 3], 22, -1044525330);
        a = md5_ff(a, b, c, d, x[i + 4], 7, -176418897);
        d = md5_ff(d, a, b, c, x[i + 5], 12, 1200080426);
        c = md5_ff(c, d, a, b, x[i + 6], 17, -1473231341);
        b = md5_ff(b, c, d, a, x[i + 7], 22, -45705983);
        a = md5_ff(a, b, c, d, x[i + 8], 7, 1770035416);
        d = md5_ff(d, a, b, c, x[i + 9], 12, -1958414417);
        c = md5_ff(c, d, a, b, x[i + 10], 17, -42063);
        b = md5_ff(b, c, d, a, x[i + 11], 22, -1990404162);
        a = md5_ff(a, b, c, d, x[i + 12], 7, 1804603682);
        d = md5_ff(d, a, b, c, x[i + 13], 12, -40341101);
        c = md5_ff(c, d, a, b, x[i + 14], 17, -1502002290);
        b = md5_ff(b, c, d, a, x[i + 15], 22, 1236535329);

        a = md5_gg(a, b, c, d, x[i + 1], 5, -165796510);
        d = md5_gg(d, a, b, c, x[i + 6], 9, -1069501632);
        c = md5_gg(c, d, a, b, x[i + 11], 14, 643717713);
        b = md5_gg(b, c, d, a, x[i + 0], 20, -373897302);
        a = md5_gg(a, b, c, d, x[i + 5], 5, -701558691);
        d = md5_gg(d, a, b, c, x[i + 10], 9, 38016083);
        c = md5_gg(c, d, a, b, x[i + 15], 14, -660478335);
        b = md5_gg(b, c, d, a, x[i + 4], 20, -405537848);
        a = md5_gg(a, b, c, d, x[i + 9], 5, 568446438);
        d = md5_gg(d, a, b, c, x[i + 14], 9, -1019803690);
        c = md5_gg(c, d, a, b, x[i + 3], 14, -187363961);
        b = md5_gg(b, c, d, a, x[i + 8], 20, 1163531501);
        a = md5_gg(a, b, c, d, x[i + 13], 5, -1444681467);
        d = md5_gg(d, a, b, c, x[i + 2], 9, -51403784);
        c = md5_gg(c, d, a, b, x[i + 7], 14, 1735328473);
        b = md5_gg(b, c, d, a, x[i + 12], 20, -1926607734);

        a = md5_hh(a, b, c, d, x[i + 5], 4, -378558);
        d = md5_hh(d, a, b, c, x[i + 8], 11, -2022574463);
        c = md5_hh(c, d, a, b, x[i + 11], 16, 1839030562);
        b = md5_hh(b, c, d, a, x[i + 14], 23, -35309556);
        a = md5_hh(a, b, c, d, x[i + 1], 4, -1530992060);
        d = md5_hh(d, a, b, c, x[i + 4], 11, 1272893353);
        c = md5_hh(c, d, a, b, x[i + 7], 16, -155497632);
        b = md5_hh(b, c, d, a, x[i + 10], 23, -1094730640);
        a = md5_hh(a, b, c, d, x[i + 13], 4, 681279174);
        d = md5_hh(d, a, b, c, x[i + 0], 11, -358537222);
        c = md5_hh(c, d, a, b, x[i + 3], 16, -722521979);
        b = md5_hh(b, c, d, a, x[i + 6], 23, 76029189);
        a = md5_hh(a, b, c, d, x[i + 9], 4, -640364487);
        d = md5_hh(d, a, b, c, x[i + 12], 11, -421815835);
        c = md5_hh(c, d, a, b, x[i + 15], 16, 530742520);
        b = md5_hh(b, c, d, a, x[i + 2], 23, -995338651);

        a = md5_ii(a, b, c, d, x[i + 0], 6, -198630844);
        d = md5_ii(d, a, b, c, x[i + 7], 10, 1126891415);
        c = md5_ii(c, d, a, b, x[i + 14], 15, -1416354905);
        b = md5_ii(b, c, d, a, x[i + 5], 21, -57434055);
        a = md5_ii(a, b, c, d, x[i + 12], 6, 1700485571);
        d = md5_ii(d, a, b, c, x[i + 3], 10, -1894986606);
        c = md5_ii(c, d, a, b, x[i + 10], 15, -1051523);
        b = md5_ii(b, c, d, a, x[i + 1], 21, -2054922799);
        a = md5_ii(a, b, c, d, x[i + 8], 6, 1873313359);
        d = md5_ii(d, a, b, c, x[i + 15], 10, -30611744);
        c = md5_ii(c, d, a, b, x[i + 6], 15, -1560198380);
        b = md5_ii(b, c, d, a, x[i + 13], 21, 1309151649);
        a = md5_ii(a, b, c, d, x[i + 4], 6, -145523070);
        d = md5_ii(d, a, b, c, x[i + 11], 10, -1120210379);
        c = md5_ii(c, d, a, b, x[i + 2], 15, 718787259);
        b = md5_ii(b, c, d, a, x[i + 9], 21, -343485551);

        a = safe_add(a, olda);
        b = safe_add(b, oldb);
        c = safe_add(c, oldc);
        d = safe_add(d, oldd);
    }
    return Array(a, b, c, d);

}

/*
 * These functions implement the four basic operations the algorithm uses.
 */
function md5_cmn(q, a, b, x, s, t) {
    return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s), b);
}
function md5_ff(a, b, c, d, x, s, t) {
    return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t) {
    return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t) {
    return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t) {
    return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}

/*
 * Calculate the HMAC-MD5, of a key and some data
 */
function core_hmac_md5(key, data) {
    var bkey = str2binl(key);
    if (bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);

    var ipad = Array(16), opad = Array(16);
    for (var i = 0; i < 16; i++) {
        ipad[i] = bkey[i] ^ 0x36363636;
        opad[i] = bkey[i] ^ 0x5C5C5C5C;
    }

    var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
    return core_md5(opad.concat(hash), 512 + 128);
}

/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function safe_add(x, y) {
    var lsw = (x & 0xFFFF) + (y & 0xFFFF);
    var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
    return (msw << 16) | (lsw & 0xFFFF);
}

/*
 * Bitwise rotate a 32-bit number to the left.
 */
function bit_rol(num, cnt) {
    return (num << cnt) | (num >>> (32 - cnt));
}

/*
 * Convert a string to an array of little-endian words
 * If chrsz is ASCII, characters >255 have their hi-byte silently ignored.
 */
function str2binl(str) {
    var bin = Array();
    var mask = (1 << chrsz) - 1;
    for (var i = 0; i < str.length * chrsz; i += chrsz)
        bin[i >> 5] |= (str.charCodeAt(i / chrsz) & mask) << (i % 32);
    return bin;
}

/*
 * Convert an array of little-endian words to a string
 */
function binl2str(bin) {
    var str = "";
    var mask = (1 << chrsz) - 1;
    for (var i = 0; i < bin.length * 32; i += chrsz)
        str += String.fromCharCode((bin[i >> 5] >>> (i % 32)) & mask);
    return str;
}

/*
 * Convert an array of little-endian words to a hex string.
 */
function binl2hex(binarray) {
    var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
    var str = "";
    for (var i = 0; i < binarray.length * 4; i++) {
        str += hex_tab.charAt((binarray[i >> 2] >> ((i % 4) * 8 + 4)) & 0xF) +
                hex_tab.charAt((binarray[i >> 2] >> ((i % 4) * 8  )) & 0xF);
    }
    return str;
}

/*
 * Convert an array of little-endian words to a base-64 string
 */
function binl2b64(binarray) {
    var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    var str = "";
    for (var i = 0; i < binarray.length * 4; i += 3) {
        var triplet = (((binarray[i >> 2] >> 8 * ( i % 4)) & 0xFF) << 16)
                | (((binarray[i + 1 >> 2] >> 8 * ((i + 1) % 4)) & 0xFF) << 8 )
                | ((binarray[i + 2 >> 2] >> 8 * ((i + 2) % 4)) & 0xFF);
        for (var j = 0; j < 4; j++) {
            if (i * 8 + j * 6 > binarray.length * 32) str += b64pad;
            else str += tab.charAt((triplet >> 6 * (3 - j)) & 0x3F);
        }
    }
    return str;
}
</script>
</body>
</html>
