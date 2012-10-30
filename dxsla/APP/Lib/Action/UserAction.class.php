<?php
class UserAction extends CommonAction
{
    public function edit_want() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
        if(!checkpost()) exit('error');
        $want_content = postvar('do_things');
        $member = M('member');
        $member->where("uid={$GLOBALS['i']['uid']}")->setField("want_content", $want_content);
        $body = array(
			'sex' => $GLOBALS['i']['sex'],
            'want_content' => $want_content
        );
        //feed_publish(2, $body);
		$feedModel = D('Feed');
		$feedModel->feed_publish(2, $body);	
        echo 1;
    }

	public function password(){
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
	    $this->display();
	}
	
	public function change_password() {
		if(!$GLOBALS['islogin']) exit();
		if(!checkpost()) exit();
		$password = postvar('password');
		$new_password = postvar('new_password');
		$member = M('member');
		$old_password = $member->where("uid={$GLOBALS['i']['uid']}")->getField('password');
		if($password == $old_password) {
			$member->where("uid={$GLOBALS['i']['uid']}")->setField('password', $new_password);
			echo 1;
		} else {
			echo -3;
		}
	}

	public function email(){
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
	    $this->display();
	}

	public function rsync(){
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
	    $this->display();
	}

	public function signqq() {
		$this->signin ( 'qq' );
	}

	private function signin($site) {
        cookie('referer', getReferer(), 300);
		if (! in_array ( $site, array ('qq', 'sina', 'ren', 'tao' ) )) {
			$this->error('参数错误');
		}
		include_once (APP_PATH . "/Lib/Extend/apilogin/{$site}.class.php");
		$platform = new $site ();
		redirect($platform->getUrl());
	}

	public function callback() {
		$site = getvar('site', array('qq', 'sina', 'ren', 'tao'));
		include_once (APP_PATH . "/Lib/Extend/apilogin/{$site}.class.php");
		$platform = new $site();
		$openid = $platform->getOpenId();
        if(!$openid) {
            $this->error('系统繁忙，请稍候重试一下啊');
            exit();
        }
		$member_field = M('member_field');
		
		//已登录用户同步设置开始
		if($GLOBALS['islogin']) {
			$sync['qquid'] = $openid;
			$sync['qq_oauth'] = $platform->get_oauth();
			$member_field->where("uid={$GLOBALS['i']['uid']}")->save($sync);
            redirect(cookie('referer'));
			exit();		
		}
		//已登录用户同步设置结束
        
        $u = $member_field->field('qh_member_field.uid')->join("qh_member ON qh_member.uid=qh_member_field.uid")->where("qquid='$openid'")->find();
        //exit($member_field->getLastSql());
        if($u) {
            cookie('uid', $u['uid'], 86400);
			$member = M('member');
			$edit['login_time'] = time();
			$edit['login_times'] = array('exp', 'login_times+1');
			$edit['login_ip'] = get_client_ip();
			$member->where("uid={$u['uid']}")->save($edit);
			$member_field->where("uid={$u['uid']}")->setField('qq_oauth', $platform->get_oauth());
            redirect(cookie('referer'));
			exit();
        } else {
            $openuser = $platform->getUserInfo ();

            cookie('open_uid', $openuser['uid'], 0);
            cookie('open_name', $openuser['name'], 0);
            cookie('open_sex', $openuser['sex'], 0);
            cookie('open_pic', $openuser['pic'], 0);
            cookie('open_province', $openuser['province'], 0);
            cookie('open_city', $openuser['city'], 0);
			cookie('open_oauth', $platform->get_oauth(), 0);
			//exit($GLOBALS['s']['urlsite'] . '/member/reg_active');
            $memberAction = A('Member');
			$memberAction->reg_active();
        }
	}
	
	public function share_to_qq() {
		if(!$GLOBALS['islogin']) exit();
		if(!checkpost()) exit();
		if(empty($GLOBALS['i']['qq_oauth']) || empty($GLOBALS['i']['qquid'])) exit(5);
		
		$site = 'qq';
		include_once (APP_PATH . "/Lib/Extend/apilogin/{$site}.class.php");
		$platform = new $site();
		$content = postvar('content');
		if(!$content) $content = '推荐一个现在很火的网站——大学生恋爱网';
		$pic = $GLOBALS['s']['urlupload'] . '/000face/logo.png';
		if($GLOBALS['i']['qqshare_timestamp'] == 0) {
			$url = 'http://jianjiandandan.ivu1314.com/?fr=qzone';
		} else {
			$url = 'http://jianjiandandan.ivu1314.com/?fr=qzone' . time();
		}
		$opt = array(
			'access_token' => $GLOBALS['i']['qq_oauth'], 
			'openid' => $GLOBALS['i']['qquid']
		);
		$rs = $platform->upload($content, $pic, $opt, $url);
		//dump($rs);
		if($rs['ret'] == 0 && $GLOBALS['i']['qqshare_timestamp'] + 86400 < time()) {
			$member_field = M('member_field');
			$member_field->where("uid={$GLOBALS['i']['uid']}")->setField('qqshare_timestamp', time());
			$payModel = D('Pay');
			$payModel->edit_pay($GLOBALS['i']['uid'], 'buy', 0.5, '您分享网站到QQ空间获得0.5颗红豆');
		}
	}
	
	public function share_score() {
		if(!$GLOBALS['islogin']) exit('');
		if(!checkpost()) exit('');
		if(empty($GLOBALS['i']['qq_oauth']) || empty($GLOBALS['i']['qquid'])) exit('5');
		
		$site = 'qq';
		include_once (APP_PATH . "/Lib/Extend/apilogin/{$site}.class.php");
		$platform = new $site();
		$content = '哈哈哈哈，谁能比我高';
		$pic = $GLOBALS['s']['urlupload'] . $GLOBALS['i']['default_pic'] . '_qq.jpg';
		if($GLOBALS['i']['qqshare_timestamp'] == 0) {
			$url = 'http://jianjiandandan.ivu1314.com/?fr=qzone';
		} else {
			$url = 'http://jianjiandandan.ivu1314.com/?fr=qzone' . time();
		}
		$opt = array(
			'access_token' => $GLOBALS['i']['qq_oauth'], 
			'openid' => $GLOBALS['i']['qquid']
		);
		$rs = $platform->upload($content, $pic, $opt, $url);
		dump($rs);
		if($rs['ret'] == 0 && $GLOBALS['i']['qqshare_timestamp'] + 86400 < time()) {
			$member_field = M('member_field');
			$member_field->where("uid={$GLOBALS['i']['uid']}")->setField('qqshare_timestamp', time());
			$payModel = D('Pay');
			$payModel->edit_pay($GLOBALS['i']['uid'], 'buy', 0.5, '您分享网站到QQ空间获得0.5颗红豆');
		}	
	}
}
?>