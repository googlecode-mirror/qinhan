<?php
class WenwenAction extends CommonAction
{

    public function index() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
        $uid = $GLOBALS['i']['uid'];
        $wenwen_question = M(wenwen_question);
        $list = $wenwen_question->field('qh_wenwen_question.*, qh_wenwen_answer.id, content')->join("LEFT JOIN qh_wenwen_answer ON qh_wenwen_answer.qid=qh_wenwen_question.qid AND uid=$uid")->where("sex=0 OR sex={$GLOBALS['i']['sex']}")->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function delete(){
		if(!$GLOBALS['islogin']) exit();
        if(!checkpost()) exit();
		
		$qid = postvar('qid', 1);		
        $answer = M('wenwen_answer');
        $answer->where("qid=$qid AND uid={$GLOBALS['i']['uid']}")->delete();
		$member_field = M('member_field');
        $data1['wenwen_num'] = array('exp', 'wenwen_num-1');
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data1);
        
		$feedModel = D('Feed');
		$feedModel->feed_delete(6, $qid, $GLOBALS['i']['uid']);	
		//echo $answer->getLastSql();
        echo 1;
    }

    public function answer() {
		if(!$GLOBALS['islogin']) exit();
        if(!checkpost()) exit();
        $qid = postvar('qid', 1);
		$uid=$GLOBALS['i']['uid'];
        $wenwen_question = M('wenwen_question');
        $wq = $wenwen_question->where("qid=$qid")->find();
        if(!$wq) exit('not found');
        $answer = M("wenwen_answer");
        $data['qid'] = $qid;
        $data['content'] = postvar('content');
        $data['rsync_tsina'] = postvar('rsync_tsina', 1);
        $data['uid'] = $GLOBALS['i']['uid'];
        $data['username'] = $GLOBALS['i']['username'];
        $date['add_time']= time();
        $condition['qid'] = $qid;
		$condition['uid'] = $GLOBALS['i']['uid'];
        $a = $answer->where($condition)->find();
        if($a) {
            $id = $a['id'];
            $answer->where($condition)->save($data);
        } else {
            $id = $answer->where($condition)->add($data);
        	$member_field=M('member_field');
            $data1['wenwen_num'] = array('exp', 'wenwen_num+1');
		    $member_field->where("uid=$uid")->save($data1);
			//echo $member_field->getLastSql();
		}
        $body = array(
            'id' => $id,
            'qid' => $qid,
            'wenwen_question' => $wq['wenwen_question'],
            'wenwen_answer' => $data['content']
        );
		$body_arr[$id] = $body;
        //feed_publish(6, $body_arr);
		$feedModel = D('Feed');
		$feedModel->feed_publish(6, $body_arr);		
        //echo $answer->getLastSql();
        echo 0;
    }
}
?>
