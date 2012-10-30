<?php
class MemberAction extends CommonAction
{
    public function login() {
        if(checkpost()) {
            $member = M('member');
            $map['username'] = postvar('username');
            $rs = $member->where($map)->find();
            $password = postvar('password');
            if($rs && $rs['password'] != '' && $rs['password'] == $password) {
                cookie('uid', $rs['uid'], 86400);
				$edit['login_time'] = time();
				$edit['login_times'] = array('exp', 'login_times+1');
				$edit['login_ip'] = get_client_ip();
				$member->where("uid={$rs['uid']}")->save($edit);
                //redirect('/');
				//exit();
				echo '{"errno":200}';
            } else {
                //$this->error('用户名或者密码错误');
				$arr = array("errno" => 500, "msg" => "用户名或者密码错误");
				echo json_encode($arr);				
            }
			exit();
        } else {
        	$this->display();
		}
    }

    public function register() {
        if(checkpost()) {
			$user = postvar('username');
			$pass = postvar('password');
			
			$member = M('member');
			$username = postvar('username');
			$rs = $member->field('uid')->where("username='$username'")->find();
			if($rs) {
				$arr = array("errno" => 500, "msg" => "用户名已经存在了");
				echo json_encode($arr);
			} else {
            	cookie('username', $user, 0);
				cookie('password', $pass, 0);
				echo '{"errno":200}';
			}
			exit();
        } else {
        	$this->display();
		}
    }

    public function reg_active() {
		$college = M("college");
		$list = $college->select();
		$username = cookie('open_uid') ? cookie('open_name') : cookie('username');
		
		$invite_error = 0;
		$invite_uid = intval(cookie('invite_uid'));
		if($invite_uid) {
			$member = M('member');
			$login_ip = $member->where("uid=$invite_uid")->getField('login_ip');
			if($login_ip == get_client_ip()) $invite_error = 1;
		}

		$this->assign("list", $list);
		$this->assign("username", $username);
		$this->assign("invite_error", $invite_error);
        $this->display('Member:reg_active');
    }

    public function chk_username() {
        if(!checkpost()) exit();
		
		$username = postvar('username');
		$member = M('member');
		$rs = $member->field('uid')->where("username='$username'")->find();
		if($rs) {
			echo '{"errno":500}';
		} else {
			cookie('username', $username);
			echo '{"errno":200}';
		}
    }

    public function reg_active_post() {
		if(!checkpost()) exit();
		
		$msg = '';
		if($GLOBALS['islogin']) {
			$msg = "top.location.href='/';";
		}
		$member = M('member');
		$username = postvar('username');
		$rs = $member->field('uid')->where("username='$username'")->find();
		if($rs) {
			$msg = "top.upload_fail('用户名已经存在了', 1);";
		}
		
		if(!$msg) {
			$sex = postvar('sex', array(1, 2));
			//$photoAction = A('Photo');
			//$rs = $photoAction->_api_upload();
			//
			$noPhoto = postvar('noPhoto', 1);
			if(!$noPhoto) {
				$photoAction = A('Photo');
				$rs = $photoAction->_api_upload();
			} else {
				$data['default_pic'] = '/000face/s' . $sex . '.jpg';
			}
			//
			if(!$noPhoto && !is_array($rs)) {
				$msg = $rs;
			} else {
				$member = M('member');
				$member_field = M('member_field');		
				$open_uid = cookie('open_uid');
				if($open_uid) {
					$data['username'] = $username;
					$data2['qquid'] = cookie('open_uid');
					$data2['qq_oauth'] = cookie('open_oauth');
				} else {
					$data['username'] = $username;
					$data['password'] = cookie('password');
				}
				$data['email'] = postvar('email');
				$data['sex'] = $sex;
				$data['college'] = postvar('college');
				$data['register_time'] = time();
				$data['login_time'] = time();
				$data['login_times'] = 1;
				$data['login_ip'] = get_client_ip();
				//打分随机起始点start
				$tsex = 3 - $sex;
				$count = $member->where("sex=$tsex AND default_photonum>0 AND group_type>0")->count();
				$data['user_ping_offset'] = rand(0, $count - 12);				
				//打分随机起始点end
				$uid = $member->add($data);
				//同步插入member_field表
				$data2['uid'] = $uid;
				$member_field->add($data2);
				cookie('uid', $uid, 86400);
				//
				if(!$noPhoto) {
					$GLOBALS['i'] = array('uid' => $uid, 'username' => $data['username'], 'default_pic' => '', 'sex' => $data['sex']);
					$pg = array('gid' => 0, 'default_pic' => '');
					$photoAction->_api_add_num($rs, $pg);
				}
				//
				if($open_uid) cookie('open_uid', NULL);
				$msg = "top.location.href='/';";
				
				$invite_uid = intval(cookie('invite_uid'));
				if($invite_uid) {
					$member = M('member');
					$login_ip = $member->where("uid=$invite_uid")->getField('login_ip');
					if($login_ip != get_client_ip()) {
						$payModel = D('Pay');
						$payModel->edit_pay($invite_uid, 'buy', 1, '您邀请1位好友注册获得1颗红豆');
						
						$invite = M("invite");
						$data1['uid'] = $invite_uid;
						$data1['invite_uid'] = $GLOBALS['i']['uid'];
						$data1['add_time'] = time();
						$invite->add($data1);
					}
				}
			}
		}
		//exit($msg);
        $this->assign('msg', $msg);		
        $this->display('Photo:api_upload');
    }

    public function logout() {
        cookie('uid', NULL);
        redirect('/');
    }
}
?>