<?php
/*
 * 总体配置文件，包括API Key, Secret Key，以及所有允许调用的API列表
 * This file for configure all necessary things for invoke, including API Key, Secret Key, and all APIs list
 *
 * @Modified by Edison tsai on 16:34 2011/01/13 for remove call_id & session_key in all parameters.
 * @Created: 17:21:04 2010/11/23
 * @Author:	Edison tsai<dnsing@gmail.com>
 * @Blog:	http://www.timescode.com
 * @Link:	http://www.dianboom.com
 */

class configClass
{
	var $APIURL		= 'http://api.xiaonei.com/restserver.do'; //RenRen网的API调用地址，不需要修改
	var $APIKey		= 'f6ff8ce905c749b5852624b35ccdd9a1';	//你的API Key，请自行申请
	var $SecretKey	= '97a8d77eb74f485f9a4201b82cddf41e';	//你的API 密钥
	var $APIVersion	= '1.0';	//当前API的版本号，不需要修改
	var $decodeFormat	= 'json';	//默认的返回格式，根据实际情况修改，支持：json,xml
	
	/*
	 *@ 以下接口内容来自http://wiki.dev.renren.com/wiki/API，编写时请遵守以下规则：
	 *  key  (键名)		: API方法名，直接Copy过来即可，请区分大小写
	 *  value(键值)		: 把所有的参数，包括required及optional，除了api_key,method,v,format不需要填写之外，
	 *					  其它的都可以根据你的实现情况来处理，以英文半角状态下的逗号来分割各个参数。
	 */
	var $APIMapping		= array(
			'admin.getAllocation' => '',
			'connect.getUnconnectedFriendsCount' => '',
			'friends.areFriends' => 'uids1,uids2',
			'friends.get' => 'page,count',
			'friends.getFriends' => 'page,count',
			'notifications.send' => 'to_ids,notification',
			'users.getInfo'	=> 'uids,fields',
			/* 更多的方法，请自行添加 
			   For more methods, please add by yourself.
			*/
	);
}

define( "URL_CALLBACK", urlencode('http://www.xiudang.com/user/callback/site/ren') );
define( "URL_RECEIVER", urlencode('http://www.xiudang.com/xd_receiver.html') );
?>