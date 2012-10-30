<?php
class QuestionAction extends CommonAction
{
    public function sender() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$question = M("question");
		$map['uid'] = $GLOBALS['i']['uid'];
		$order = getvar('order', 1);
		if($order == 0){
			$b = "new_answer desc,";
		}
		$list = $question->where($map)->order("$b add_time desc")->select();
		//echo $question->getLastSql();
		$this->assign('list', $list);
		$this->assign('order', $order);
		$this->display();
    }

	public function plaza() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
        $questionModel = D("Question");
		//问问随机展示问题定时器处理start
		$questionModel->trigger();
		//问问随机展示问题定时器处理end
		
		$q1 = $questionModel->getOne(rand(0, 5));
		$wh = getwh($q1['photo_url'], 320, 320);
		$wh = explode(',', $wh);
		$q1['photo_width'] = $wh[0];
		$q1['photo_height'] = $wh[1];
		
        $GLOBALS['i']['question_t'] = $q1['timeline'];
        $q2 = $questionModel->getOne();
        $q2['photo_url'] = $GLOBALS['s']['urlupload'] . $q2['photo_url'] . '_480x480.jpg';

        $this->assign("question", $q1);
        $this->assign("question2", $q2);
        $this->display();
    }

    public function add() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
        $this->display();
    }

	public function submit_question() {
	    if(!checkpost()) exit();
        $photo_id = postvar('photo_id', 1);
        $question_photo = M('question_photo');
        $qp = $question_photo->getByPhoto_id($photo_id);
        $qp = $qp ? $qp : array('photo_id' => 0, 'img_path' => '');

		$type = postvar('question_type', 1);
		$question_type = M('question_type');
		$type_name = $question_type->where("id=$type")->getField('name');
		if(!$type_name) exit();
		
		$question=M('question');
		$uid=$GLOBALS['i']['uid'];
		$data['question'] = postvar('question');
		$data['photo_id'] = $qp['photo_id'];
        $data['photo_url'] = $qp['img_path'];
		$data['type'] = $type;
		$data['type_name'] = $type_name;
        $data['vote'] = postvar('vote', array(1, 2));
		$data['is_anonymity'] = postvar('is_anonymity', 1);
		$data['is_show'] = postvar('is_show', 1);
		$data['is_attention'] = postvar('is_attention', 1);
		$data['uid'] = $GLOBALS['i']['uid'];
        $data['sex'] = $GLOBALS['i']['sex'];
        $data['username'] = $GLOBALS['i']['username'];
        $data['default_pic'] = $GLOBALS['i']['default_pic'];
		$data['add_time'] = time();
		$data['timeline'] = $data['add_time'];
		$q_id = $question->add($data);
		if($data['is_anonymity']==0){
		$member_field=M('member_field');
        $data1['question_num'] = array('exp', 'question_num+1');
		$member_field->where("uid=$uid")->save($data1);
		}
		if($q_id) {
            $body = array(
                'q_id' => $q_id,
				'q_uid' => $GLOBALS['i']['uid'],
                'question' => $data['question'],
                'photo_url' => $data['photo_url']
            );
			$body_arr[$q_id] = $body;
            //feed_publish(3, $body_arr);
			$feedModel = D('Feed');
			$feedModel->feed_publish(3, $body_arr);
			$question_photo = M('question_photo');
			$edit['question_id'] = $q_id;
			$edit['status'] = 0;
			$question_photo->where("photo_id={$qp['photo_id']}")->save($edit);
			echo '{"errno":200}';
		}
        //echo $question->getLastSql();
	}

    public function delete(){
		if(!$GLOBALS['islogin']) exit();
        if(!checkpost()) exit();
		
		$qid = postvar('qid_list', 1);
		
		$feedModel = D('Feed');
		
        $question = M('question');
        $question->where("id=$qid AND uid={$GLOBALS['i']['uid']}")->delete();
		$feedModel->feed_delete(3, $qid, $GLOBALS['i']['uid']);		
		
		$answer = M('answer');
		$rs = $answer->field('id')->where("q_id=$qid AND uid={$GLOBALS['i']['uid']}")->limit(8)->select();
		foreach($rs as $a) {
			$feedModel->feed_delete(4, $a['id'], $GLOBALS['i']['uid']);	
		}
		$sum = $answer->where("q_id=$qid AND uid={$GLOBALS['i']['uid']}")->count();
		$answer->where("q_id=$qid AND uid={$GLOBALS['i']['uid']}")->delete();
		
		$member_field = M('member_field');
        $data1['question_num'] = array('exp', 'question_num-1');
		$data1['answer_num'] = array('exp', "answer_num-$sum");
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data1);
        //echo $answer->getLastSql();
        echo 1;
    }

}
?>
