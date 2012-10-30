<?php
class TaskAction extends CommonAction
{
    public function index() {
	    if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$task = M('');
		$count = $task->table('qh_task')->count();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $tasklist = $task->table('qh_task AS A')
						 ->field('A.tid, A.uid, A.title, A.content, A.reward, A.need_skill, A.answer_count, A.expire_time, A.add_time, B.username, B.default_pic, B.sex')
						 ->join('qh_member AS B ON A.uid=B.uid')
						 ->limit($page->firstRow.','.$page->listRows)
						 ->order("add_time DESC")
						 ->select();
        $this->assign('tasklist', $tasklist);
		$this->assign('pages', $page->show());
        $this->display();
	}
	
	public function skilllearn(){
		if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$key=trim(isset($_POST['input_skill'])?$_POST['input_skill']:"");

		$task = M('task');
		$count = $task->where("title like '%key%'")->count();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $tasklist = $task->where("title like '%$key%'")->limit($page->firstRow.','.$page->listRows)->order("add_time DESC")->select();
		$this->assign('tasklist', $tasklist);
		$this->assign('pages', $page->show());
        $this->display('index');		
	}
	
	public function my_task() {
	    if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$task = M('task');
		$count = $task->where("uid={$GLOBALS['i']['uid']}")->count();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $tasklist = $task->where("uid={$GLOBALS['i']['uid']}")->limit($page->firstRow.','.$page->listRows)->order("add_time DESC")->select();
		
        $this->assign('tasklist', $tasklist);
		$this->assign('pages', $page->show());
        $this->display();	
	}
	
	public function my_help() {
	    if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$task_answer = M('');
		$count = $task_answer->table('qh_task_answer')->where("uid={$GLOBALS['i']['uid']}")->count();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $tasklist = $task_answer->table('qh_task_answer AS A')
							  ->field('A.*, B.uid AS t_uid, B.username AS t_username, B.title')
							  ->join('qh_task AS B ON A.tid=B.tid')
							  ->where("A.uid={$GLOBALS['i']['uid']}")
							  ->limit($page->firstRow.','.$page->listRows)
							  ->order("A.add_time DESC")
							  ->select();
		//print_r($tasklist);
		$this->assign('tasklist', $tasklist);
		$this->assign('pages', $page->show());
        $this->display();		
	}
	public function taskchange() {
	    if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$uid = getvar('uid',1);
		$member = M('member');
        $m = $member->getByUid($uid);
		if(!$m) $this->error('找不到此人');
		$this->assign('m', $m);	
		$task = M('task');
		$tid= getvar('tid', 1);
		$t = $task->getByTid($tid);
		if(!$m) $this->error('任务不存在或者已被删除');
		$this->assign('t', $t);	
		$task_answer = M('task_answer');
		$answerlist = $task_answer->where("tid=$tid")->order('add_time DESC')->select();
		$this->assign('answerlist', $answerlist);
		$this->display();
	}
	
	public function taskdelete(){
		 if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$tid=getvar('tid');
		$del=M('task');
		$del->where("tid=$tid")->delete();
		
		$task = M('task');
		$count = $task->where("uid={$GLOBALS['i']['uid']}")->count();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $tasklist = $task->where("uid={$GLOBALS['i']['uid']}")->limit($page->firstRow.','.$page->listRows)->order("add_time DESC")->select();
		
        $this->assign('tasklist', $tasklist);
		$this->assign('pages', $page->show());
        $this->display();	
	}
	public function add() {
		$this->display();
	}
	
	public function change() {
  	    if(!$GLOBALS['islogin']) {
            exit('error');
        }
		if(!checkpost()) exit();
		$task = M('task');
		$tid = postvar('t_id');
		$data['title'] = postvar('title');
		$data['content'] = exp_content(postvar('content'));
		$data['reward'] = postvar('reward');
		$data['need_skill'] = postvar('need_skill');
		$data['expire_time'] = time() + postvar('expire_time', 1) * 86400;
		$data['add_time'] = time();
		$task->where("tid='".$tid."'")->save($data);
		exit('{"errno":200, "msg":"修改任务成功！"}');
	}
	
	public function submit_task() {
        if(!$GLOBALS['islogin']) {
            exit('error');
        }
		if(!checkpost()) exit();
		if($GLOBALS['i']['sex'] == 1) exit('{"errno":5, "msg":"目前只限女生发布任务！"}');
		$task = M('task');
		$data['uid'] = $GLOBALS['i']['uid'];
		$data['username'] = $GLOBALS['i']['username'];
		$data['default_pic'] = $GLOBALS['i']['default_pic'];
		$data['title'] = postvar('title');
		$data['content'] = exp_content(postvar('content'));
		$data['reward'] = postvar('reward');
		$data['need_skill'] = postvar('need_skill');
		$data['expire_time'] = time() + postvar('expire_time', 1) * 86400;
		$data['add_time'] = time();
		$tid = $task->add($data);
		if($tid) {
            $body = array(
                'tid' => $tid,
				'uid' => $GLOBALS['i']['uid'],
                'title' => $data['title'],
                'content' => $data['content']
            );
			$body_arr[$tid] = $body;
			$feedModel = D('Feed');
			$feedModel->feed_publish(21, $body_arr);
		}
		exit('{"errno":200, "msg":"发布任务成功！"}');				
	}
	
	public function answer_task() {
        if(!$GLOBALS['islogin']) {
            exit('error');
        }
		if(!checkpost()) exit();
		$uid = postvar('receiver_uid', 1);
		$tid = postvar('related', 1);
		$task = M('task');
		$t = $task->where("tid=$tid")->find();
		if(!$t) exit('error');
		
		$task_answer = M('task_answer');
		$rs = $task_answer->where("tid=$tid AND uid={$GLOBALS['i']['uid']}")->find();
		if($rs) exit('{"stat":5,"error":"\u60a8\u5df2\u7ecf\u5e2e\u8fc7\u4e86"}');
		$data['tid'] = $t['tid'];
		$data['uid'] = $GLOBALS['i']['uid'];
		$data['username'] = $GLOBALS['i']['username'];
		$data['default_pic'] = $GLOBALS['i']['default_pic'];
		$data['content'] = exp_content(postvar('content'));
		$data['is_anonymity'] = postvar('is_anonymity', 1);
		$data['add_time'] = time();
		$rs = $task_answer->add($data);
		if($rs) {
			$edit['answer_count'] = array('exp', 'answer_count+1');
			$task->where("tid={$t['tid']}")->save($edit);
			
			$msgModel = D('Msg');
			$ta = $GLOBALS['i']['sex'] == 1 ? '她' : '他';
			$ta2 = $GLOBALS['i']['sex'] == 1 ? '他' : '她';
			$content = exp_content(postvar('content'));
			$str = "<div class=\"textbg f_6\"><b>你参与了{$ta}发布的任务：{$t['title']}</b></div>";
			$str2 = "<div class=\"textbg f_6\"><b>{$ta2}参与了你发布的任务：{$t['title']}</b></div>";
			$str3 = "<div class=\"textbg f_6\"><b>任务：{$t['title']}</b></div>";
			$main = "<p class=\"word_break\"><span class=\"word_break\">回应：{$data['content']}</span></p>";
			$str = $str . $main;
			$str2 = $str2 . $main;
			$str3 = $str3 . $main;
			$msgModel->chat($GLOBALS['i']['uid'], $uid, $str, 0, $str2, $str3);
		}
		//echo $task->getLastSql();
		exit('{"stat":0}');
	}
}
?>