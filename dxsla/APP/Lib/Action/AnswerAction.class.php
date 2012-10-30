<?php
class AnswerAction extends CommonAction
{
    public function answer_star() {
		if(!checkpost()) exit('error');
		if(!$GLOBALS['islogin']) exit('{"stat":9, "errno":"未登录", "error":"未登录}');
		
		$a_id = postvar('a_id', 1);
		$a_uid = postvar('a_uid', 1);
		$type = postvar('type', array(0, 1));
		$star_num = postvar('star_num', array(1, 2, 3));
		$stand = $type == 0 ? '好' : '差';
		$chinese = array('', '一', '二', '三');
		$content = $chinese[$star_num] . '个' . $stand . '评';
		
		$answer = M('');
		$rs = $answer->table('qh_answer AS A')
					 ->field('A.uid, A.answer_cont, A.vote, A.username, B.question, B.type_name, B.photo_url')
					 ->join('qh_question AS B ON A.q_id=B.id')
					 ->where("A.id=$a_id AND A.uid=$a_uid")
					 ->find();
		//echo $answer->getLastSql();
		if(!$rs) exit();
		
		$num = $type * 3 + $star_num;
		$answer->table('qh_answer AS A')->where("A.id=$a_id AND A.uid=$a_uid")->setField('star_num', $num);
		
		$str = '<div class="textbg f_6"><p class="word_break"><a href="' . $GLOBALS['s']['urlsite'] . '/' . $GLOBALS['i']['uid'] . '" target="_blank">' . $GLOBALS['i']['username'] . '</a>：' . $rs['question'] . '</p><p>' . $rs['username'] . '：<span class="agree p_l30"></span>' . $rs['answer_cont'] . '</p></div><p><a href="' . $GLOBALS['s']['urlsite'] . '/' . $GLOBALS['i']['uid'] . '" target="_blank">' . $GLOBALS['i']['username'] . '</a>对你的回答评了' . $content . '<a href="javascript:;" onclick="window.open(\'' . $GLOBALS['s']['urlsite'] . '/answer/my_answer/\');event.cancelBubble=true">点击查看</a></p>';

		$msgModel = D('Msg');
		$msgModel->sys_notifi($rs['uid'], $str);
		
		echo '1';
	}
	
	public function answer(){
        if(!checkpost()) exit();
        $uid = postvar('q_uid', 1);
        $member = M("member");
        $u = $member->getByUid($uid);
        $qid = postvar('q_id', 1);
        $question = M("question");
        $old = $question->where("qh_question.id=$qid")->find();
        //dump($question->getLastSql());
        if(!$u || !$old) exit('error');

        $vote = intval($_POST['vote']);
        $vote = $vote == 1 ? 1 : 2;

        $answer=M('answer');
        $data['uid'] = $GLOBALS['i']['uid'];
        $data['username'] = $GLOBALS['i']['username'];
        $data['default_pic'] = $GLOBALS['i']['default_pic'];
        $data['q_id'] = $qid;
        $data['answer_cont'] = exp_content(postvar('answer_cont'));
        $data['vote'] = $vote;
        $data['is_anonymity'] = postvar('is_anonymity', 1);
        $data['is_show'] = postvar('is_show', 1);
        $data['answer_time'] = time();
        $a_id = $answer->add($data);
		
		$member_field = M('member_field');
        $data1['new_answer'] = array('exp', 'new_answer+1');
		$member_field->where("uid=$uid")->save($data1);
		if($data['is_anonymity'] != 1 && $old['is_anonymity'] != 1) {
			$data2['answer_num'] = array('exp', 'answer_num+1');
			$uid1= $GLOBALS['i']['uid'];
			$member_field->where("uid=$uid1")->save($data2);
       	}
		
	    if(!$a_id) exit('unkonw');
        $body = array(
            'q_id' => $qid,
			'q_uid' => $old['uid'],
            'question' => $old['question'],
            'a_id' => $a_id,
			'a_uid' => $GLOBALS['i']['uid'],
			'sex' => $GLOBALS['i']['sex'],
			'a_username' => $GLOBALS['i']['username'],
            'answer' => $data['answer_cont'],
			'photo_url' => $old['photo_url']
        );
		$body_arr[$a_id] = $body;
        //feed_publish(4, $body_arr);
		$feedModel = D('Feed');
		$feedModel->feed_publish(4, $body_arr);
        $edit['answer_count'] = array('exp', 'answer_count+1');
		$edit['new_answer'] = array('exp', 'new_answer+1');
        if($vote == 1) {
            $edit['agree_count'] = array('exp', 'agree_count+1');
        } else {
            $edit['against_count'] = array('exp', 'against_count+1');
        }
        $edit['answer_news'] = array('exp', 'answer_news+1');
        $edit['answer_count'] = array('exp', 'answer_count+1');
		
        if(!empty($_POST['answer_cont']))$edit['answer_cont_num'] = array('exp', 'answer_cont_num+1');
		//问问随机展示问题处理start
		if($old['answer_count'] == 19) {
			$edit['timeline'] = time();
		}
		$GLOBALS['i']['question_t'] = $old['timeline'];
		//问问随机展示问题处理end
        $question->where("id=$qid")->save($edit);

        $questionModel = D("Question");
		$new = $questionModel->getOne(1);
		//问问随机展示问题处理start
		$questionModel->answerTrigger($old);
		//问问随机展示问题处理start

		$wh = getwh($new['photo_url'], 320, 320);
		$wh = explode(',', $wh);        
		$new['photo_width'] = $wh[0];
		$new['photo_height'] = $wh[1];
		$new['photo_url'] = $GLOBALS['s']['urlupload'] . $new['photo_url'] . '_480x480.jpg';
        $array = array(
            "code" => 1,
            "msg" =>  "回答成功",
            "answer"=> array(
                "uid" =>$GLOBALS['i']['uid'],
                "question_id" => $qid,
                "question_uid" => $u['uid'],
                "answer_cont" => $_POST['answer_cont'],
                "answer_time" => $data['answer_time'],
                "vote" => $vote,
                "star_count" => 0,
                "status" => 0,
                "is_anonymity" =>$old['is_anonymity'],
                "is_show" => 0
            ),
           // "daty_num" => 8,
            "m_anwer_num" => 41,
            "question" => $new,
            "q_code" => NULL,
            "prev_userinfo" => array(
                "nickname" =>  $u['username'],
                "question_uid" => $u['uid'],
                "location_prov" => $u['hometown_prov'],
                "location_city" => "",
                "is_anonymity" =>  $old['is_anonymity'],
                "birth_y" => date("Y") - $u['birth_y'],
                "sex" => $u['sex'],
                "u_sex" => $u['sex'] == 1 ? '他' : '她',
                "face" => $GLOBALS['s']['urlupload'] . $u['default_pic'] . '_120x120.jpg',
                "question_type" => $old['type_name']
            )
        );
        echo json_encode($array);
	}

	public function load_more_answer() {
		if(!$GLOBALS['islogin']) exit();
        if(!checkpost()) exit();
		$qid = intval($_POST['question_id']);
		$question = M('question');
		$q = $question->where("id={$qid}")->find();
        $data1['new_answer'] = 0;
		$question->where("id={$qid}")->save($data1);
		$member_field = M('member_field');
        $data1['new_answer'] = array('exp', "new_answer-{$q['new_answer']}");
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data1);
        $answer = M('answer');
        $map['q_id'] = $qid;
        if(!empty($_POST['filter'])) $map['answer_cont'] = array('neq', '');
		$limit = postvar('limit', 1);
		$startnum = ($limit - 1) * 10;
        $answerlist = $answer->where($map)->order('answer_time desc')->limit("$startnum,10")->select();
		$this->assign('answerlist', $answerlist);
		$last_answer = $answerlist ? end($answerlist) : array('id' => 0);
        $arr = array(
            "last_answer" => $last_answer['id'],
            "errno" => 200,
            "more" => $this->fetch(),
            "limit" => 0,
			"hide_more" => sizeof($answerlist) == 10 ? 0 : 1,
        );
        echo json_encode($arr);
	}
	
    public function my_answer(){
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
        import("@.ORG.Page");
        $answer = M();
		$order = getvar('order', 1);
        if($order==1) $map['answer_cont'] = array('neq', '');
        $map['A.uid'] = $GLOBALS['i']['uid'];
		//$map['B.is_anonymity']=0;
        $count = $answer->table('qh_answer AS A')->where($map)->count();
        //echo $answer->getLastSql();
        $p = new Page($count, 10);
        $list = $answer->table('qh_answer AS A')
					   ->field('B.id AS qid, B.uid, B.username, B.question, B.add_time, B.photo_id, B.photo_url, B.default_pic, B.is_anonymity, B.answer_count, B.type_name, B.agree_count, B.against_count, A.id, A.answer_cont, A.vote, A.star_num, A.answer_time')
					   ->join("LEFT JOIN qh_question AS B ON A.q_id=B.id")
					   ->where($map)
					   ->limit($p->firstRow.','.$p->listRows)
					   ->order('answer_time desc')
					   ->select();
        //echo $answer->getLastSql();
		//dump($list);
        $this->assign("page", $p->show());
        $this->assign("list", $list);
		$this->assign("order", $order);
        $this->display();
    }

    public function delete() {
		if(!$GLOBALS['islogin']) exit();
        if(!checkpost()) exit();
		
		$aid = postvar('aid_list', 1);
        $answer = M('answer');
        $answer->where("id=$aid AND uid={$GLOBALS['i']['uid']}")->delete();
		
		$member_field = M('member_field');
        $data1['answer_num'] = array('exp', 'answer_num-1');
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data1);
        
		$feedModel = D('Feed');
		$feedModel->feed_delete(4, $aid, $GLOBALS['i']['uid']);	
        //echo $answer->getLastSql();
        echo 1;
    }
		
}
?>