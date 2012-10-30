<?php 

class tao {

	private $taouid;
	private $username;

	function getUrl() {
		return 'http://container.open.taobao.com/container?appkey=12131492';
	}
	
	function getOpenId() {
		$top_appkey = !empty($_GET['top_appkey'])? iconv("GB2312", "utf-8//IGNORE", trim($_GET['top_appkey'])):'';
		$top_parameters = !empty($_GET['top_parameters'])? iconv("GB2312", "utf-8//IGNORE", trim($_GET['top_parameters'])):'';
		$top_session = !empty($_GET['top_session'])? iconv("GB2312", "utf-8//IGNORE", trim($_GET['top_session'])):'';
		$top_sign = !empty($_GET['top_sign'])? iconv("GB2312", "utf-8//IGNORE", trim($_GET['top_sign'])):'';
		$app_secret = 'ee363b0bcbe797cc4f6c3dbbb5c6f05e';
		
		$parameters = base64_decode($top_parameters)."&";
		preg_match_all('/visitor_id=([^&]+)&visitor_nick=([^&]+)&/', $parameters, $arr);
		$this->username = iconv("GB2312", "utf-8//IGNORE", $arr[2][0]);
		$this->taouid = intval($arr[1][0]);
		return $this->taouid;
	}
	
	//用户资料
	function getUserInfo() {
		$user['uid'] = $this->taouid;
		$user['name'] = $this->username;
		return $user;
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