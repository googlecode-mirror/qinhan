<?php
define('APPID', 100236875);
define('APPKEY', '675fcf1976581c143a0dd341385d8441');
define('CALLBACK', $_SERVER['HTTP_HOST'] . '/index.php?s=/user/callback/site/qq');
//define('SCOPE', 'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo');
define('SCOPE', 'get_user_info,add_share');
function do_post($url, $data) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	$ret = curl_exec($ch);
	curl_close($ch);
    return $ret;
}
function get_url_contents($url) {
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	//curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result =  curl_exec($ch);
    curl_close($ch);
    return $result;
}

class qq {

    var $access_token, $openid;

	function getUrl() {
        $state = md5(uniqid(rand(), TRUE));
        setcookie('state', $state, 0, '/');
        $login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
            . APPID . "&redirect_uri=" . urlencode(CALLBACK)
            . "&state=" . $state
            . "&scope=".SCOPE;
		return $login_url;
	}

	function getOpenId() {
        if($_REQUEST['state'] != $_COOKIE['state']) exit("CSRF");
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . APPID. "&redirect_uri=" . urlencode(CALLBACK)
            . "&client_secret=" . APPKEY. "&code=" . $_REQUEST["code"];
        $response = get_url_contents($token_url);
        if(strpos($response, "callback") !== FALSE) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);
            if(isset($msg->error)) {
				//return false;
				exit(var_dump($msg));
			}
        }
		//exit(var_dump($msg));
        $params = array();
        parse_str($response, $params);
        $this->access_token = $params["access_token"];
        setcookie('access_token', $params["access_token"], 0, '/');

        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
            . $params["access_token"];
        $str  = get_url_contents($graph_url);
        if(strpos($str, "callback") !== FALSE) {
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
        }
        $user = json_decode($str);
        if(isset($user->error)) { 
			exit(var_dump($msg));
			//return FALSE;
		}
		//exit(var_dump($msg));
        $this->openid = $user->openid;
        setcookie('openid', $user->openid, 0, '/');
        return $user->openid;
	}
	
	function get_oauth() {
		return $this->access_token;
	}

	//用户资料
	function getUserInfo() {
        $get_user_info = "https://graph.qq.com/user/get_user_info?"
            . "access_token=" . $this->access_token
            . "&oauth_consumer_key=" . APPID
            . "&openid=" . $this->openid
            . "&format=json";

        $info = get_url_contents($get_user_info);
        $arr = json_decode($info, TRUE);
		$user['uid'] = $this->openid;
		$user['name'] = $arr['nickname'];
        $user['pic'] = $arr['figureurl_2'];
		return $user;
	}

	//发布一条微博
	function update($text, $opt) {
		return $this->doClient($opt)->update($text);
	}

	//上传一个照片，并发布一条微博
	function upload2($text, $pic, $opt) {
		$url  = "https://graph.qq.com/t/add_pic_t";
		/*$data = "access_token=" . $opt["access_token"]
			."&oauth_consumer_key=" . APPID
			."&openid=" . $opt["openid"]
			."&content=" . urlencode($text)
			."&pic=" . $pic;*/
			
		$data = array(
			"access_token" => $opt["access_token"],
			"oauth_consumer_key" => APPID,
			"openid" => $opt["openid"],
			"syncflag" => 0,
			"content" => $text,
			"pic" => '@' . realpath($pic)
		);
		
		$ret = do_post($url, $data); 
		return $ret;
	}
		
	function upload($text, $pic, $opt, $url) {
		$graph_url  = "https://graph.qq.com/share/add_share";
		$data = array(
			"access_token" => $opt["access_token"],
			"oauth_consumer_key" => APPID,
			"openid" => $opt["openid"],
			"title" => $text,
			"url" => $url,
			"images" => $pic
		);
		
		$ret = do_post($graph_url, $data);
		$arr = json_decode($ret, TRUE);
		return $arr;
	}

}
?>