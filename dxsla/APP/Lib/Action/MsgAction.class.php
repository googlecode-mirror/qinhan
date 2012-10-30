<?php
class MsgAction extends CommonAction
{
    public function index() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$uid = $GLOBALS['i']['uid'];
        $member_field = M('member_field');
		$member_fieldlist = $member_field->where("uid=$uid")->find();
		$this->assign('f', $member_fieldlist);
        $msg = M('msg');
        $count = $msg->where("uid={$GLOBALS['i']['uid']} AND count > 0 AND status=1")->count();
		//dump($count);
        import("@.ORG.Page");
        $page = new Page($count, 10);
        $msglist = $msg->field("B.username, B.sex, B.want_content, B.default_pic, B.college, A.id, A.receiver_uid as uid, A.content, A.new, A.is_last, A.count")
                       ->table('qh_msg AS A')
                       ->join("LEFT JOIN qh_member AS B ON A.receiver_uid=B.uid")
                       ->where("A.uid={$GLOBALS['i']['uid']} AND A.count > 0 AND A.status = 1")
					   ->order('A.new DESC, A.add_time DESC')
                       ->limit($page->firstRow.','.$page->listRows)
                       ->select();
        //echo $msg->getLastSql();
        $this->assign('sex', $GLOBALS['i']['sex']);
        $this->assign('msglist', $msglist);
		//dump($msglist);
        $this->assign('pages', $page->show());
        $this->display();
    }

    public function del() {
        if(!$GLOBALS['islogin']) {
            exit();
        }
        if(!checkpost()) exit();
		$ids = postvar('fids');
		$str = orsql('id', $ids);
        $msg = M('msg');
		$rs = $msg->field('SUM(new)')->where("uid={$GLOBALS['i']['uid']} AND ($str)")->find();
		$sum = $rs['SUM(new)'];

		$num = $GLOBALS['i']['new_msg'] - $sum;
		$member_field = M('member_field');
		$edit['new_msg'] = $num < 0 ? 0 : $num;
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($edit);
		//echo $member_field->getLastSql();
		//exit();
        
		$data['status'] = 0;
		$data['new'] = 0;
        $msg->where("uid={$GLOBALS['i']['uid']} AND ($str)")->save($data);
		//echo $msg->getLastSql();
        //exit();
		echo '200';
    }

    public function slient_all() {
        if(!$GLOBALS['islogin']) {
            exit();
        }
        if(!checkpost()) exit();

        $msg = M('msg');
		$rs = $msg->field('SUM(new)')->where("uid={$GLOBALS['i']['uid']}")->find();
		$sum = $rs['SUM(new)'];
		
		if($sum) {
			$num = $GLOBALS['i']['new_msg'] - $sum;
			$member_field = M('member_field');
			$edit['new_msg'] = $num < 0 ? 0 : $num;
			$member_field->where("uid={$GLOBALS['i']['uid']}")->save($edit);
			//echo $member_field->getLastSql();
			//exit();
			
			$data['new'] = 0;
			$msg->where("uid={$GLOBALS['i']['uid']}")->save($data);
			//echo $msg->getLastSql();
			//exit();
		}
		echo '200';
    }	

    public function look() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
        $i = $GLOBALS['i']['uid'];
        $u = getvar('u', 1);
		if($i == $u) {
			$this->assign('type', 1);
			$this->error('不能自言自语哦');
		}
        $msg = M('msg');
        $rs = $msg->where("uid=$i AND receiver_uid=$u")->find();
        if(!$rs) {
			$data['uid'] = $i;
			$data['receiver_uid'] = $u;
			$id = $msg->add($data);
			$msg->where("id=$id")->setField('roomid', $id);
			$data['uid'] = $u;
			$data['receiver_uid'] = $i;
			$data['roomid'] = $id;
			$msg->add($data);
			$roomid = $id;
        } else {
			$roomid = $rs['roomid'];
			$num = $GLOBALS['i']['new_msg'] - $rs['new'];
			$member_field = M('member_field');			
			$edit['new_msg'] = $num < 0 ? 0 : $num;
			$member_field->where("uid={$GLOBALS['i']['uid']}")->save($edit);
			//echo $member_field->getLastSql();
			$edit2['new'] = 0;
			$msg->where("id={$rs['id']}")->save($edit2);
		}
		$msgModel = D('Msg');
		if(!$msgModel->check_link($u)) {
			$this->assign('type', 1);
			$this->error($msgModel->getError());
		}		
        $k = urlencode(authcode("$i|$u|$roomid", 1));
        redirect("http://qinhan001.sinaapp.com/im.php?k=$k");
		//redirect("/im.php?k=$k");
    }

    public function userinfo() {
        $k = getvar('k');
        $arr = explode('|', authcode($k));
        $count = sizeof($arr);
        if($count != 3) exit(print_r($arr));
        $i = $arr[0];
        $u = $arr[1];
        $roomid = $arr[2];
		$id = getvar('id');
        if($roomid != decrypt($id)) exit();
        $member = M('member');
        $m = $member->getByUid($u);
        if(!$m && $u != 0) exit('not found');
        $this->assign('m', $m);
		$this->assign('uid', $u);
        $this->display();
    }

    public function api() {
        if(!checkpost()) exit();
        $k = getvar('k');
        $arr = explode('|', authcode($k));
        $count = sizeof($arr);
        if($count != 3) exit(print_r($arr));
        $i = $arr[0];
        $u = $arr[1];
        $roomid = $arr[2];
		$id = getvar('id');
        if($roomid != decrypt($id)) exit();
		$msg = M('msg');
		$result = $msg->where("uid=$i AND receiver_uid=$u")->getField('roomid');
		if(!$result || $result != $roomid) exit();
		$content = exp_content(postvar('postcontent'));
		
		$msgModel = D('Msg');
		if(!$msgModel->check_link($u, $i)) {
			$msgModel->exitError();
		}
		$msgModel->chat($i, $u, $content, 0, '', '', 1);
		//聊天过程中新消息自动清零
		$rs = $msg->where("uid=$i AND receiver_uid=$u")->find();
		$member_field = M('member_field');
		$edit['new_msg'] = array('exp', "new_msg-{$rs['new']}");
		$member_field->where("uid=$i")->save($edit);
		$edit2['new'] = 0;
		$msg->where("id={$rs['id']}")->save($edit2);
		//聊天过程中新消息自动清零
    }

    public function send() {
		if(!checkpost()) exit('error');
		if(!$GLOBALS['islogin']) exit('{"stat":9, "errno":"未登录", "error":"未登录}');
		$receiver_uid = postvar('receiver_uid', 1);
		$msgModel = D('Msg');
		if(!$msgModel->check_link($receiver_uid)) {
			$msgModel->exitError();
		}
        $u = $msgModel->check_contact('receiver_uid', 'sex, uid');
		
		$type = postvar('type', 1);
		
		$action = 'send_' . $type;
		$id = postvar('related', 1);
		$msgModel->$action($u['uid'], $id);
		
        echo '{"stat":0,"error":"\u53d1\u9001\u6210\u529f","pay_card":0}';
    }
	
	public function check() {
		echo '{"stat":0,"error":"\u53d1\u9001\u6210\u529f","pay_card":0}';
	}
	
	public function get_new() {
		if($GLOBALS['i']['login_time'] + 60 < time()) {
			$member = M('member');
			$member->where("uid={$GLOBALS['i']['uid']}")->setField('login_time', time());
		}
		$lose = getvar('lose', 1);
		if($lose) exit('{"news":0}');
		$member_field = M('member_field');
		$new_msg = $member_field->where("uid={$GLOBALS['i']['uid']}")->getField('new_msg');
		//$new_msg = 2;
		exit("{\"news\":$new_msg}");
	}
	
	public function browser() {
		echo '';
	}

}
?>