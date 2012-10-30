<?php
class DiaryAction extends CommonAction
{
    public function index() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
        $diary = M('diary');
        $count = $diary->where("uid={$GLOBALS['i']['uid']}")->count();
        import("@.ORG.Page");
        $page = new Page($count, 4);
        $diarylist = $diary->where("uid={$GLOBALS['i']['uid']}")->order("did DESC")->limit($page->firstRow.','.$page->listRows)->select();
        //echo $diary->getLastSql();
        $this->assign('diarylist', $diarylist);
        $this->assign('pages', $page->show());
        $this->display();
    }

    public function add() {
        if(!$GLOBALS['islogin']) {
            exit();
        }
        if(!checkpost()) exit();
        $diary = M('diary');
		$uid=$GLOBALS['i']['uid'];
        $data['uid'] = $GLOBALS['i']['uid'];
		
        $content = postvar('content');
		$data['original_cont'] = exp_content($content);		
		$content = msubstr($content, 0, 100, 'utf-8', false);
		$data['content'] = exp_content($content);
		
        $data['mood'] = postvar('mood');
        $data['read_type'] = postvar('read_type', 1);
        $data['add_time'] = time();
        $data['set_time'] = strtotime(postvar('set_time'));
        $did = $diary->add($data);
		//echo $diary->getLastSql();
		
		$member_field=M('member_field');
        $data1['diary_num'] = array('exp', 'diary_num+1');
		$member_field->where("uid=$uid")->save($data1);
        $body = array(
            'did' => $did,
            'content' => $data['content']
        );
        //feed_publish(1, $body, $did);
		$feedModel = D('Feed');
		$feedModel->feed_publish(1, $body, $did);		
        echo '{"errno":200}';
    }

    public function diary_name() {
        if(!$GLOBALS['islogin']) {
            exit();
        }
        if(!checkpost()) exit();
        $member = M('member');
        $data['diary_name'] = postvar('diary_name');
        $member->where("uid={$GLOBALS['i']['uid']}")->save($data);
        //echo $member->getLastSql();
        echo '{"errno":200,"msg":"\u4fee\u6539\u6210\u529f"}';
    }
	
    public function to_recy() {
        if(!$GLOBALS['islogin']) {
            exit();
        }
        if(!checkpost()) exit();
        $diary = M('diary');
        $did = postvar('diary_id');
        $diary->where("did=$did AND uid={$GLOBALS['i']['uid']}")->delete();
		
		$feedModel = D('Feed');
		$feedModel->feed_delete(1, $did, $GLOBALS['i']['uid']);	
        
		echo '{"errno":200}';
    }

    public function praise() {
        if(!$GLOBALS['islogin']) {
            exit();
        }
        if(!checkpost()) exit();
		$did = postvar('diary_id', 1);
		$type = postvar('type', array(0, 1));

        $diary = M('diary');
        $d = $diary->where("did=$did")->find();
		if(!$d) exit();
		
		if(strstr($d['praise_uids'], ",{$GLOBALS['i']['uid']},")) {
			exit('{"errno":500, "msg":"\u60a8\u5df2\u7ecf\u9001\u8fc7\u4e86"}');
		}
		$data['praise_' . $type] = array('exp', "praise_{$type}+1");
		$data['praise_uids'] = rtrim($d['praise_uids'], ',') . ",{$GLOBALS['i']['uid']},";
		$diary->where("did=$did")->save($data);
		//echo $diary->getLastSql();
		$msgModel = D('Msg');
		//$msgModel->comment($d['uid'], 26, $type, $did);
		$str = "<div class=\"textbg f_6\"><p class=\"word_break\">“写两句”：{$d['content']}</p></div>";
		$classifier = $img = '';
		if($type == 0) {
			$classifier = $GLOBALS['i']['sex'] == 1 ? '朵' : '片';
			$img = $GLOBALS['i']['sex'] == 1 ? 'ico_diay.gif' : 'ico_diay1.gif';
		} else {
			$classifier = '块';
			$img = 'ico_brick.gif';
		}
		$str .= "<p><a target=\"_blank\" href=\"{$GLOBALS['s']['urlsite']}/{$GLOBALS['i']['uid']}\">{$GLOBALS['i']['username']}</a>给了你一{$classifier}<img src=\"{$GLOBALS['s']['urlstatic']}/img/{$img}\" class=\"ico\"></p>";
		$msgModel->sys_notifi($d['uid'], $str);
        echo '{"errno":200, "msg":""}';
    }
}
?>