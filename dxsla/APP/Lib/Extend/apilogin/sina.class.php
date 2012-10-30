<?php 
include_once( 'sina/config.php' );
include_once( 'sina/weibooauth.php' );

class sina {

	var $me;
	var $sina_oauth;

	function getUrl() {
		$o = new WeiboOAuth(WB_AKEY, WB_SKEY);
		$keys = $o->getRequestToken();
		$sinaurl = $o->getAuthorizeURL($keys['oauth_token'], false, URL_CALLBACK);
		setcookie('keys[oauth_token]', $keys['oauth_token'], 0, '/');
		setcookie('keys[oauth_token_secret]', $keys['oauth_token_secret'], 0, '/');
		return $sinaurl;
	}
	
	function getOpenId() {
		$o = new WeiboOAuth( WB_AKEY , WB_SKEY , $_COOKIE['keys']['oauth_token'] , $_COOKIE['keys']['oauth_token_secret']  );
		$opt = $o->getAccessToken($_REQUEST['oauth_verifier']) ;
		$this->sina_oauth = $opt;
		//绑定以前未同步的
		setcookie ( 'open[sina_oauth]', json_encode($opt), 0, '/', COOKIE_DOMAIN );
		$this->doClient($opt)->follow(1780798503);
		//绑定以前未同步的
		$this->me = $this->doClient($opt)->verify_credentials();
		if(empty($this->me['error'])) {
			return $this->me['id'];
		} else {
			return false;
		}
	}
	
	function getUserInfo() {
		//$this->doClient($this->sina_oauth)->follow(1780798503);
		$user['uid'] = $this->me['id'];
		$user['name'] = $this->me['screen_name'];
		$user['sex'] = $this->me['gender'] == 'm' ? '男' : '女';
		$user['pic'] = $this->me['profile_image_url'];
		$location = explode(" ", $this->me['location']);
		$user['province'] = $location[0];
		$user['city'] = $location[1];
		$user['sina_oauth'] = json_encode($this->sina_oauth);
		return $user;
	}

	private function doClient($opt) {
		$oauth_token = $opt['oauth_token'] ? $opt['oauth_token'] : $_COOKIE['keys']['oauth_token'];
		$oauth_token_secret = $opt['oauth_token_secret'] ? $opt['oauth_token_secret'] : $_COOKIE['keys']['oauth_token_secret'];
		return new WeiboClient( WB_AKEY , WB_SKEY , $oauth_token , $oauth_token_secret );
	}

	function update($text, $opt) {
		return $this->doClient($opt)->update($text);
	}
	
	function upload($text, $pic, $opt) {
		return $this->doClient($opt)->upload($text, $pic);
	}
}
?>