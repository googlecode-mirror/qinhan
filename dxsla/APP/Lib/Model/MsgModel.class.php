<?php
class MsgModel extends Model
{

	var $link_status = 0; //0正常 1静默处理 2阻止
	var $m = NULL;
	var $error = '';

	public function check_link($uid, $myuid = 0) {
		$myuid = !$myuid ? $GLOBALS['i']['uid'] : $myuid;
		$link = M('link');
		$rs = $link->where("uid=$uid AND forbid_uid=$myuid")->find();
		if($rs) $this->status = $rs['status'];
		if($this->status == 2) {
			$this->error = '对方阻止了和你联系';
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_contact($name = 'uid', $field = 'uid') {
		$uid = postvar($name, 1);
		$member = M('member');
		$m = $member->field($field)->getByUid($uid);
		if(!$m) {
			$this->error = '用户不存在';
			$this->exitError();
		}
		if($GLOBALS['i']['sex'] == $m['sex']) {
			$this->error = '同性之间不能互通消息';
			$this->exitError();
		}
		//检查是否在黑名单中，静默处理
		//检查是否为热度用户...
		$this->m = $m;
		return $m;
	}
	
	public function exitError() {
		$arr['stat'] = 9;
		$arr['errno'] = $this->error;
		$arr['error'] = $this->error;
		echo json_encode($arr);
		exit();
	}

	public function getError() {
		return $this->error;
	}
	
	public function getM() {
		return $this->m;
	}

	//打招呼
    public function send_1($uid, $id) {
		$comment = M('comment');
		$time = time() - 86400;
		$rs = $comment->where("uid={$GLOBALS['i']['uid']} AND receiver_uid=$uid AND add_time>$time AND type=1")->find();
		if($rs) exit('{"stat":2, "error":"\u60a8\u5df2\u7ecf\u6253\u8fc7\u62db\u547c\u4e86\uff0c\u660e\u5929\u518d\u6765\u5427\uff01", "msg":"\u60a8\u5df2\u7ecf\u6253\u8fc7\u62db\u547c\u4e86\uff0c\u660e\u5929\u518d\u6765\u5427\uff01"}');
		$content = exp_content(postvar('content'));
		$arr = array('Hi~', 'Hi，你好', '你好，我们交个朋友吧', '哈喽');
		$content = !$content ? $arr[rand(0, 3)] : $content;
		$this->chat($GLOBALS['i']['uid'], $uid, $content);
		$this->comment($uid, 1, $content);
    }

	//小编专访评论
    public function send_4($uid, $id) {
		$wenwen_answer = M('');
		$rs = $wenwen_answer->table('qh_wenwen_answer AS A')
							->field('A.username, A.content AS answer, B.wenwen_question')
							->join('qh_wenwen_question AS B ON A.qid=B.qid')
							->where("A.id=$id AND A.uid=$uid")
							->find();
		//echo $wenwen_answer->getLastSql();
		if(!$rs) exit();
		$ta = $GLOBALS['i']['sex'] == 1 ? '她' : '他';
		$ta2 = $GLOBALS['i']['sex'] == 1 ? '他' : '她';
		$str = "<p>你对{$ta}专访的评论：</p>";
		$str2 = "<p>{$ta2}对你专访的评论：</p>";
		$str3 = '<b>' . $GLOBALS['i']['username'] . '</b>' . '的评论：';
		$content = exp_content(postvar('content'));
		$main = '<div class="textbg f_6"><p class="word_break">小编：' . $rs['wenwen_question'] . '</p><p>回答：' . $rs['answer'] . '</p></div><p>' . $content . '</p>';
		$main3 = '<div class="textbg f_6"><p class="word_break">小编：' . $rs['wenwen_question'] . '</p><p>' . $rs['username'] . '的回答：' . $rs['answer'] . '</p></div><p>' . $content . '</p>';
		$str .= $main;
		$str2 .= $main;
		$str3 .= $main3;
		$this->chat($GLOBALS['i']['uid'], $uid, $str, 0, $str2, $str3);
		$this->comment($uid, 4, $content);
    }
	
	//照片评论
    public function send_5($uid, $id) {
		$photo = M('photo');
		$p = $photo->where("pid=$id AND uid=$uid")->find();
		if(!$p) exit();
		$ta = $GLOBALS['i']['sex'] == 1 ? '她' : '他';
		$ta2 = $GLOBALS['i']['sex'] == 1 ? '他' : '她';
		$str = "你对{$ta}照片的评论：";
		$str2 = "{$ta2}对你照片的评论：";
		$str3 = '<b>' . $GLOBALS['i']['username'] . '</b>' . '的评论：';
		$content = exp_content(postvar('content'));		
		$main = "<p class=\"textbg\"><a target=\"_blank\" href=\"{$GLOBALS['s']['urlsite']}/home/photo/?uid={$uid}&amp;gid={$p['gid']}&amp;pid={$p['pid']}\"><img src=\"{$GLOBALS['s']['urlupload']}{$p['path']}_72x72.jpg\"></a></p><p>{$content}</p>";
		$str .= $main;
		$str2 .= $main;
		$str3 .= $main;
		$this->chat($GLOBALS['i']['uid'], $uid, $str, 0, $str2, $str3);
		$this->comment($uid, 5, $content);
    }
	
	//约会
    public function send_8($uid, $id) {
		$comment = M('comment');
		$time = time() - 86400;
		$rs = $comment->where("uid={$GLOBALS['i']['uid']} AND receiver_uid=$uid AND add_time>$time AND type=1")->find();
		if($rs) exit('{"stat":2, "error":"你今天已经约过了"}');
		$meet_do = postvar('meet_do', 1);
		if($meet_do == 2) {
			$want_content = $GLOBALS['i']['want_content'];
		} else {
			$member = M('member');
			$want_content = $member->where("uid=$uid")->getField('want_content');
		}
		if(!$want_content) exit();
		$ta = $GLOBALS['i']['sex'] == 1 ? '她' : '他';
		$ta2 = $GLOBALS['i']['sex'] == 1 ? '他' : '她';
		$str = "<span class=\"f_yelo p_l30\">你想约{$ta}{$want_content}</span>";
		$str2 = "<span class=\"f_yelo p_l30\">{$ta2}想约你{$want_content}</span>";
		$str3 = "<span class=\"f_yelo p_l30\">约会：{$want_content}</span>";
		$this->chat($GLOBALS['i']['uid'], $uid, $str, 0, $str2, $str3);
		$this->comment($uid, 8, $content);
    }
	
	//提问的回答的评论
    public function send_10($uid, $id) {
		$answer = M('');
		$rs = $answer->table('qh_answer AS A')
					 ->field('A.answer_cont, A.vote, A.username AS a_username, B.username, B.question, B.type_name, B.photo_url')
					 ->join('qh_question AS B ON A.q_id=B.id')
					 ->where("A.id=$id AND A.uid=$uid")
					 ->find();
		if(!$rs) exit();
		$ta = $GLOBALS['i']['sex'] == 1 ? '她' : '他';
		$ta2 = $GLOBALS['i']['sex'] == 1 ? '他' : '她';
		$content = exp_content(postvar('content'));
		$str = "<p>你对{$ta}回答的评论：{$content}</p>";
		$str2 = "<p>{$ta2}对你回答的评论：{$content}</p>";
		$str3 = '<b>' . $GLOBALS['i']['username'] . '</b>' . "的评论：{$content}";
		$class = $vote == 1 ? 'agree' : 'opposition';
		$main = "<div class=\"textbg f_6\"><p class=\"word_break\"><span class=\"word_break\">{$rs['username']}：[{$rs['type_name']}]</span>{$rs['question']}</p>";
		$main .= !empty($rs['photo_url']) ? "<img src=\"{$GLOBALS['s']['urlupload']}{$rs['photo_url']}_72x72.jpg\">" : '';
		$main3 = $main . "<p><b>{$rs['a_username']}</b>的回答：<span class=\"{$class} p_l30\"></span>{$rs['answer_cont']}</p></div>";
		$main .= "<p>回答：<span class=\"{$class} p_l30\"></span>{$rs['answer_cont']}</p></div>";
		
		$str = $main . $str;
		$str2 = $main . $str2;
		$str3 = $main3 . $str3;
		$this->chat($GLOBALS['i']['uid'], $uid, $str, 0, $str2, $str3);
		$this->comment($uid, 10, $content);
    }
	
	//写两句评论
    public function send_16($uid, $id) {
		$diary = M('diary');
		$rs = $diary->where("did=$id AND uid=$uid")->find();
		if(!$rs) exit();
		$ta = $GLOBALS['i']['sex'] == 1 ? '她' : '他';
		$ta2 = $GLOBALS['i']['sex'] == 1 ? '他' : '她';
		$content = exp_content(postvar('content'));
		$str = "<div class=\"textbg f_6\"><b>你对{$ta}“写两句”的评论：{$content}</b></div>";
		$str2 = "<div class=\"textbg f_6\"><b>{$ta2}对你“写两句”的评论：{$content}</b></div>";
		$str3 = "<div class=\"textbg f_6\"><b>{$GLOBALS['i']['username']}的评论：{$content}</b></div>";
		$bak = "<p><span class=\"word_break f_green\">评论：{$content}</span></p>";
		$main = "<p class=\"word_break\"><span class=\"word_break\">“写两句”：{$rs['content']}</span></p>";
		$str = $main . $str;
		$str2 = $main . $str2;
		$str3 = $main . $str3;
		$this->chat($GLOBALS['i']['uid'], $uid, $str, 0, $str2, $str3);	
		$this->comment($uid, 16, $content);
    }
	
	public function home_perfect($uid) {
	
	}
	
	public function answer_star($uid) {
	
	}	
	
	public function diary_parise($uid) {
	
	}
	
	public function chat($uid, $receiver_uid, $content, $silent = 0, $content2 = '', $content3 = '', $from_api = 0) {
		//echo $content3;
		//exit();
		if(!$silent) $silent = $this->status;
		$msg = M('msg');
		$rs = $msg->where("uid=$uid AND receiver_uid=$receiver_uid")->find();
		$data['uid'] = $uid;
		$data['receiver_uid'] = $receiver_uid;
		$data['content'] = $content;
		$data['add_time'] = time();
		$data['is_last'] = 1;
		$data['count'] = !$rs ? 1 : array('exp', 'count+1');
		$data['status'] = 1;
		$data2 = $data;
		$data2['uid'] = $receiver_uid;
		$data2['receiver_uid'] = $uid;
		$data2['content'] = $content2 ? $content2 : $content;
		$data2['is_last'] = 0;
		$data2['new'] = !$rs ? 1 : array('exp', 'new+1');
		if($silent) unset($data2['new']);
		$id = 0;
		if(!$rs) {			
			$id = $msg->add($data);
			$msg->where("id=$id")->setField('roomid', $id);
			$data2['roomid'] = $id;
			$msg->add($data2);			
		} else {
			$id = $rs['roomid'];
			$msg->where("uid=$uid AND receiver_uid=$receiver_uid")->save($data);
			$msg->where("uid=$receiver_uid AND receiver_uid=$uid")->save($data2);
			//echo $msg->getLastSql();
		}
		$content3 = $content3 ? $content3 : $content;
		if(!$from_api) $this->sys_curl($uid, $receiver_uid, $id, $content3);
		if(!$silent) {
			$member_field = M('member_field');
			$edit['new_msg'] = array('exp', 'new_msg+1');
			$member_field->where("uid=$receiver_uid")->save($edit);
			//echo $member_field->getLastSql();
		}
	}
	
	public function comment($uid, $type, $content, $related = 0) {
		$comment = M('comment');
		$data['uid'] = $GLOBALS['i']['uid'];
		$data['type'] = $type;
		$data['receiver_uid'] = $uid;
		$data['related'] = $related;
		$data['content'] = $content;
		$data['add_time'] = time();
		$comment->add($data);
	}
	
	public function sys_notifi($uid, $content) {
		$msg = M('msg');
		$rs = $msg->where("uid=$uid AND receiver_uid=0")->find();
		$data['uid'] = $uid;
		$data['receiver_uid'] = 0;
		$data['content'] = $content;
		$data['add_time'] = time();
		$data['new'] = !$rs ? 1 : array('exp', 'new+1');
		$data['count'] = !$rs ? 1 : array('exp', 'new+1');
		$data['status'] = 1;	
		if(!$rs) {
			$id = $msg->add($data);
			$msg->where("id=$id")->setField('roomid', $id);		
		} else {
			$id = $rs['id'];
			$msg->where("uid=$uid AND receiver_uid=0")->save($data);
		}
		$member_field = M('member_field');
		$edit['new_msg'] = array('exp', 'new_msg+1');
		$member_field->where("uid=$uid")->save($edit);
		$this->sys_curl(0, $uid, $id, $content);
	}
	
	public function sys_curl($uid, $receiver_uid, $id, $content) {
		$str = "$uid|$receiver_uid|$id";
		$url = "http://qinhan001.sinaapp.com/im.php?skip=1&k=" . urlencode(authcode($str, 1));
		//$url = 'http://qinhan001.sinaapp.com/1.php';
		//$url = 'http://jianjiandandan.ivu1314.com/';
		$data = "content=" . urlencode($content);	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$ret = curl_exec($ch);
		//dump($ret);
		//exit();
		curl_close($ch);	
	}
}
?>