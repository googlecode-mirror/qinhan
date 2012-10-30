<?php 
require_once 'renren/config.inc.php'; #Include configure resources
require_once 'renren/RESTClient.class.php';
require_once 'renren/RenRenClient.class.php';

class ren {

	var $renuid;

	function getUrl() {
		$config	= new configClass();
		$url = "http://login.api.renren.com/connect/login?return_session=1&nochrome=1&xnconnect=1&connect_display=popup&api_key=".$config->APIKey."&v=1.0&next=".URL_CALLBACK."%3Fxn_login%26fname%3D_opener&cancel_url=".URL_RECEIVER."%23fname%3D_opener%26%257B%2522t%2522%253A3%252C%2522h%2522%253A%2522xnCancelLogin%2522%252C%2522sid%2522%253A%25220.804%2522%257D&channel_url=".URL_RECEIVER;
		return $url;
	}
	
	function getOpenId() {
		$obj = json_decode($_GET['session']);
		if(!empty($obj->uid)) {
			$this->renuid = $obj->uid;
			return $obj->uid;
		} else {
			return false;
		}
	}
	
	//用户资料
	function getUserInfo() {
		$rrObj = new RenRenClient;
		$rrObj->setSessionKey($re->session_key);
		$rs = $rrObj->POST('users.getInfo', array($this->renuid, 'uid,name,sex,star,birthday,tinyurl,headurl,mainurl,hometown_location'));
		$this->me = $rs[0];
		
		$user['uid'] = $me->uid;
		$user['name'] = $me->screen_name;
		$user['sex'] = $me->sex ? '男' : '女';
		$user['pic'] = $me->headurl;
		$user['province'] = $me->hometown_location->province;
		$user['city'] = str_replace('市', '', $me->hometown_location->city);	
		return $this->user;
	}
	
	private function doClient($opt){
		$oauth_token = ( $opt['oauth_token'] )? $opt['oauth_token']:$_SESSION['sina']['access_token']['oauth_token'];
		$oauth_token_secret = ( $opt['oauth_token_secret'] )? $opt['oauth_token_secret']:$_SESSION['sina']['access_token']['oauth_token_secret'];
		return new WeiboClient( $this->_sina_akey , $this->_sina_skey ,  $oauth_token, $oauth_token_secret  );
	}
	
	//发布一条微博
	function update($text,$opt){
		return $this->doClient($opt)->update($text);
	}
	
	//上传一个照片，并发布一条微博
	function upload($text,$pic,$opt){
		return $this->doClient($opt)->upload($text,$pic);
	}
	
}
?>