<?php
class HomeAction extends CommonAction
{
    public function index() {
		$uid = getvar('uid', 1);
		
        $member = M();
        $m = $member->table('qh_member AS A')
					->field('A.*, B.receive_score_num, B.fav_out_num, B.fav_in_num, B.question_num, B.answer_num, B.diary_num, B.wenwen_num')
					->join("qh_member_field AS B ON A.uid=B.uid")
					->where("A.uid=$uid")
					->find();
        if(!$m) $this->error('找不到此人');
        $this->assign('m', $m);		
		
		$f = $v = NULL;
	    if($GLOBALS['islogin']) {
			$visit = A('Visit');
			$visit->add();
			
			$fav = M('fav');
			$f = $fav->where("uid={$GLOBALS['i']['uid']} AND fav_uid=$uid")->find();
			$visit = M('visit');
			$v = $visit->where("uid={$GLOBALS['i']['uid']} AND visit_uid=$uid")->find();
        }
		$this->assign('f', $f);
		$this->assign('v', $v);		
		
        $photo = M('photo');
        $personal = $photo->where("uid=$uid AND gid=0")->select();
        $this->assign('personal', $personal);
        $photo_group = M('photo_group');
        $pglist = $photo_group->where("uid=$uid AND photonum>0")->limit(4)->select();
        $this->assign('pglist', $pglist);
		
		$wenwen = M("wenwen_question");
		$wenwenlist = $wenwen->field('qh_wenwen_question.*, qh_wenwen_answer.id,username, content')->join("RIGHT JOIN qh_wenwen_answer ON qh_wenwen_answer.qid = qh_wenwen_question.qid ")->where("uid=$uid")->limit(10)->select();
		//echo $wenwen->getLastSql();
		$this->assign('wenwenlist', $wenwenlist);		
		$question = M("question");
		$qlist = $question->where("uid=$uid AND is_anonymity=0")->order('add_time desc')->limit(5)->select();
		$qt = M('question_type');
        $qtlist = $qt->select();
		
        foreach($qtlist as $t) {
            $qtlist[$t['id']] = $t['name'];
        }
		$this->assign('qtlist', $qtlist);
		$this->assign('qlist', $qlist);
		$answer = M("answer");
		$map['qh_answer.uid'] = $uid ;
		$map['qh_question.is_anonymity'] =0;
		$alist = $answer->join("LEFT JOIN qh_question ON qh_answer.q_id=qh_question.id")->where($map)->order('answer_time DESC')->limit(5)->select();
		//echo $answer->getLastSql();
		$this->assign('alist', $alist);
		$diary = M('diary');
		$diarylist = $diary->where("uid=$uid")->order("did DESC")->select();
		$this->assign('diarylist', $diarylist);
		
		//动态
		$feed = M('feed');
		$feedData = $feed->where("uid={$m['uid']}")->order('add_time DESC')->limit(10)->select();
		$this->assign('feedData', $feedData);
		//动态结束
		
		//随缘推荐
		$member = M('member');
		$recommendlist = $member->field('uid, username, default_pic, height, birth_y')->where("uid<{$m['uid']} AND sex={$m['sex']} AND default_photonum>0 AND group_type>0")->order('uid DESC')->limit(6)->select();
		$this->assign('recommendlist', $recommendlist);
		//随缘推荐结束
        $this->display();
    }

    public function photo() {
	     if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
        $uid = getvar('uid');
        $member = M('member');
        $m = $member->getByUid($uid);
        if(!$m) $this->error('找不到此人');
        $this->assign('m', $m);
        $gid = getvar('gid');
        $photo_group = M('photo_group');
        $default = array('gid' => 0, "name" => '个人形象照', 'default_pic' => $m['default_pic'], 'photonum' => $m['default_photonum']);
        if($gid) {
            $pg = $photo_group->where("uid=$uid AND gid=$gid")->find();
            if(!$pg) $this->error('相册可能已经被删除');
        } else {
            $pg = $default;
        }
        $pglist = $photo_group->where("uid=$uid AND gid!=$gid")->select();
        if(!$pglist) $pglist = array();
        if($gid) array_unshift($pglist, $default);
        array_unshift($pglist, $pg);
        //dump($pglist);
        $photo = M('photo');
        $photolist = $photo->field('qh_photo.*, qh_photo_hot.score AS hot_score')->join("LEFT JOIN qh_photo_hot ON qh_photo.pid=qh_photo_hot.pid AND qh_photo_hot.uid={$GLOBALS['i']['uid']}")->where("qh_photo.uid=$uid AND qh_photo.gid=$gid")->select();
		//echo $photo->getLastSql();
		//exit();
        if(!$photolist) $this->error('相册为空');
        $pid = getvar('pid');
        if($pid) {
            $current_photo = $photo->field('qh_photo.*, qh_photo_hot.score AS hot_score')->join("LEFT JOIN qh_photo_hot ON qh_photo.pid=qh_photo_hot.pid AND qh_photo_hot.uid={$GLOBALS['i']['uid']}")->where("qh_photo.uid=$uid AND qh_photo.pid=$pid")->find();
            if(!$current_photo) $this->error('照片未找到，可能已被删除');
        } else {
            $current_photo = $photolist[0];
            $pid = $current_photo['pid'];
        }
        $position = array_search($current_photo, $photolist) + 1;
        //dump($photolist);
        $this->assign('pg', $pg);
        $this->assign('pglist', $pglist);
        $this->assign('photolist', $photolist);
        $this->assign('pid', $pid);
        $this->assign('current_photo', $current_photo);
        $this->assign('position', $position);

        $this->display();
    }

    public function show_group() {
        $uid = getvar('uid');
        $member = M('member');
        $m = $member->getByUid($uid);
        if(!$m) $this->error('找不到此人');
        $this->assign('m', $m);

        $photo_group = M('photo_group');
        $default = array('gid' => 0, "name" => '个人形象照', 'default_pic' => $m['default_pic']);
        $pglist = $photo_group->where("uid=$uid")->select();
        if(!$pglist) $pglist = array();
        array_unshift($pglist, $default);

        $this->assign('pglist', $pglist);
        $this->display();
    }
	
	public function question(){
		if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$uid = getvar('uid',1);
		$qid = getvar('qid',1);
        $member = M('member');
        $m = $member->getByUid($uid);
        if(!$m) $this->error('找不到此人');
        $this->assign('m', $m);
		
        $photo = M('photo');
        $personal = $photo->where("uid=$uid AND gid=0")->select();
        $this->assign('personal', $personal);
        $photo_group = M('photo_group');
        $pglist = $photo_group->where("uid=$uid AND photonum>0")->limit(4)->select();
        $this->assign('pglist', $pglist);
		
		 import("@.ORG.Page");
		$question = M("question");
		$list = $question->where("uid=$uid AND id=$qid")->find();
		//echo $question->getLastSql();
        $qt = M('question_type');
        $qtlist = $qt->select();
		
        foreach($qtlist as $t) {
            $qtlist[$t['id']] = $t['name'];
        }
		$this->assign('list', $list);
		$agree=round(($list['agree_count']/($list['agree_count']+$list['against_count']))*100);
		$against=round(($list['against_count']/($list['agree_count']+$list['against_count']))*100);
		//echo $agree.$against;
		//$this->assign('pages', $p->show(1));
		$this->assign('agree', $agree);
        $this->assign('against', $against);
		 $next_qid = $question->where("uid=$uid AND id<$qid AND is_anonymity=0")->order('id DESC')->limit(1)->getField('id');
		// echo  $question->getLastSql();
        if(!$next_qid) $next_qid = $question->where("uid=$uid AND is_anonymity=0 ")->max('id');
	
        $prev_qid = $question->where("uid=$uid AND id>$qid AND is_anonymity=0")->order('id ASC')->limit(1)->getField('id');
        if(!$prev_qid) $prev_qid = $question->where("uid=$uid AND is_anonymity=0")->min('id');
		$this->assign('prev_qid', $prev_qid);
        $this->assign('next_qid', $next_qid);
		$answer = M('answer');
        $map['q_id'] = $qid;
		$nofilter = getvar('nofilter');
        if(empty($nofilter)) $map['answer_cont'] = array('neq', '');
        $answerlist = $answer->where($map)->order('answer_time DESC')->select();
        //echo $question->getLastSql();
   
		$this->assign('alist', $answerlist);
	  	$this->assign('b', $b);
		$this->display();
	}
	
	public function anonymity(){
		if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		
		$question = M("question");
		$uid = getvar('uid',1);
		$qid = getvar('qid',1);
		$list = $question->where("uid=$uid AND id=$qid ")->find();
		$agree=round(($list['agree_count']/($list['agree_count']+$list['against_count']))*100);
		$against=round(($list['against_count']/($list['agree_count']+$list['against_count']))*100);
		$this->assign('agree', $agree);
        $this->assign('against', $against);
		$member = M('member');
        $m = $member->getByUid($uid);
		$this->assign('m', $m);
		//echo $question->getLastSql();
        $qt = M('question_type');
        $qtlist = $qt->select();
		
        foreach($qtlist as $t) {
            $qtlist[$t['id']] = $t['name'];
        }
		$this->assign('list', $list);
		//$this->assign('pages', $p->show(1));
        $this->assign('qtlist', $qtlist);
		$answer = M('answer');
        $map['q_id'] = $qid;
		$nofilter = getvar('nofilter');
        if(empty($nofilter)) $map['answer_cont'] = array('neq', '');
        $answerlist = $answer->where($map)->order('answer_time DESC')->select();
 
		$this->assign('alist', $answerlist);
	  	$this->assign('b', $b);
		$this->display();
	}
	
	public function task() {
		$uid = getvar('uid',1);
		$member = M('member');
        $m = $member->getByUid($uid);
		if(!$m) $this->error('找不到此人');
		$this->assign('m', $m);	
		$task = M('task');
		$tid= getvar('tid', 1);
		$t = $task->getByTid($tid);
		if(!$m) $this->error('任务不存在或者已被删除');
		
        $photo = M('photo');
        $personal = $photo->where("uid=$uid AND gid=0")->select();
        $this->assign('personal', $personal);
        $photo_group = M('photo_group');
        $pglist = $photo_group->where("uid=$uid AND photonum>0")->limit(4)->select();
        $this->assign('pglist', $pglist);

		$next_tid = $task->where("uid=$uid AND tid<$tid")->order('id DESC')->limit(1)->getField('tid');
        if(!$next_tid) $next_tid = $task->where("uid=$uid")->max('tid');
	
        $prev_tid = $task->where("uid=$uid AND tid>$tid")->order('id ASC')->limit(1)->getField('tid');
        if(!$prev_tid) $prev_tid = $task->where("uid=$uid")->min('tid');
		$this->assign('prev_tid', $prev_tid);
        $this->assign('next_tid', $next_tid);

		$this->assign('t', $t);	
		$task_answer = M('task_answer');
		$answerlist = $task_answer->where("tid=$tid")->order('add_time DESC')->select();
		$this->assign('answerlist', $answerlist);
		$this->display();
	}
	
	public function perfect() {
		if(!$GLOBALS['islogin']) {
            exit();
        }
		$type = postvar('type', array(1, 2, 3, 4, 5, 6, 7));
		$uid = postvar('uid', 1);
		if($uid == $GLOBALS['i']['uid']) {
			exit();
		}
		$member = M('member');
        $m = $member->field('uid')->getByUid($uid);
		if(!$m) exit('error');
		$visit = M('visit');
		$v = $visit->field('perfect_' . $type)->where("uid={$GLOBALS['i']['uid']} AND visit_uid={$m['uid']}")->find();
		if(!$v) exit('db error');
		if($v['perfect_' . $type] + 86400 > time()) {
			exit('{"errno":503,"msg":"\u8bf7\u4e0d\u8981\u91cd\u590d\u9080\u8bf7\uff01"}');
		}
		$i = $GLOBALS['i'];
		$str = "<div class=\"invitebox clear\"><div onclick=\"window.open('{$GLOBALS['s']['urlsite']}/{$i['uid']}')\" class=\"fl cur\"><img src=\"{$GLOBALS['s']['urlupload']}{$i['default_pic']}_48x48.jpg\"></div><div class=\"fl invitebox_l\"><p><span class=\"underline f_blue cur\" onclick=\"window.open('{$GLOBALS['s']['urlsite']}/{$i['uid']}')\">{$i['username']}</span>";
		if($type == 1) {
			$str .= "邀请你上传更多照片。</p><p onclick=\"window.open('{$GLOBALS['s']['urlsite']}/photo/');event.cancelBubble=true\" class=\"font_list\">进入上传照片</p></div></div>";
		} elseif($type == 2) {
			$str .= "希望看到你更多的小编专访。</p><p onclick=\"window.open('{$GLOBALS['s']['urlsite']}/wenwen/');event.cancelBubble=true\" class=\"font_list\">进入小编专访</p></div></div>";
		} elseif($type == 3) {
			$str .= "邀请你发布任务。</p><p onclick=\"window.open('{$GLOBALS['s']['urlsite']}/task/');event.cancelBubble=true\" class=\"font_list\">进入师兄帮帮忙</p></div></div>";
		} elseif($type == 4) {
			$str .= "邀请你完成视频认证。</p><p onclick=\"window.open('{$GLOBALS['s']['urlsite']}/videoauth/');event.cancelBubble=true\" class=\"font_list\">进入视频认证</p></div></div>";
		} elseif($type == 5) {
			$str .= "邀请你完善更多基本资料。</p><p onclick=\"window.open('{$GLOBALS['s']['urlsite']}/profile/');event.cancelBubble=true\" class=\"font_list\">进入基本资料</p></div></div>";
		} elseif($type == 6) {
			$str .= "邀请你写两句。</p><p onclick=\"window.open('{$GLOBALS['s']['urlsite']}/diary/');event.cancelBubble=true\" class=\"font_list\">进入写两句</p></div></div>";
		} elseif($type == 7) {
			$str .= "邀请你参与问问。</p><p onclick=\"window.open('{$GLOBALS['s']['urlsite']}/question/plaza/');event.cancelBubble=true\" class=\"font_list\">进入问问</p></div></div>";
		}
		$msgModel = D('Msg');
		$msgModel->sys_notifi($m['uid'], $str);
		$visit->where("uid={$GLOBALS['i']['uid']} AND visit_uid={$m['uid']}")->setField('perfect_' . $type, time());	
		echo '{"errno":200,"msg":"\u9080\u8bf7\u6210\u529f"}';		
	}
}
?>