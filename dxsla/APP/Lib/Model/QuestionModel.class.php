<?php
class QuestionModel extends Model
{
	//触发器，如果question_t1离当前时间太远，则question_t=0，question_t1=question_t3
    public function trigger() {
		if($GLOBALS['i']['question_t1'] < time() - 86400 * 3 && $GLOBALS['i']['question_t1'] != 0) {
			$member_field = M('member_field');
			$data['question_t'] = 0;
			$data['question_t1'] = $GLOBALS['i']['question_t3'];
			$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data);
		}
    }
	
	//获得1个问题
    public function getOne($offset = 0) {
		$addsql = '';
		$addsql .= "(timeline>{$GLOBALS['i']['question_t1']} OR timeline<{$GLOBALS['i']['question_t2']})";
		$addsql .= " AND ";
		$addsql .= "(add_time>{$GLOBALS['i']['question_t1']} OR add_time<{$GLOBALS['i']['question_t2']})";
		//echo $GLOBALS['i']['question_t'];
		if($GLOBALS['i']['question_t'] > 0) {
			$addsql .= " AND timeline<{$GLOBALS['i']['question_t']}";
		}
		$addsql .= " AND status=1";
		$question = M("question");
		$q = $question->where($addsql)->order("timeline DESC")->limit("$offset,1")->select();
		//echo $question->getLastSql();
		if(!$q) {
			$question_t1 = $question_t2 = $question_t3 = $question_t = 0;
			$q = $question->order("timeline DESC")->find();
			
			$member_field = M('member_field');
			$data['question_t1'] = 0;
			$data['question_t2'] = 0;
			$data['question_t3'] = 0;
			$data['question_t'] = 0;
			$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data);
		} else {
			$q = $q[0];
		}
		//echo $question->getLastSql();
		return $q;
    }
	
	public function answerTrigger($old) {
		$member_field = M('member_field');
		if($old['timeline'] < $GLOBALS['i']['question_t2'] || $GLOBALS['i']['question_t2'] == 0) {
			$data['question_t2'] = $old['timeline'];
		}
		if($old['timeline'] > $GLOBALS['i']['question_t1']) {
			$data['question_t3'] = $old['timeline'];
		}
		$data['question_t'] = $old['timeline'];

		//可要可不要，因为定位字段question_t没有清零
		if($old['timeline'] <= $GLOBALS['i']['question_t1']) {
			$data['question_t1'] = $GLOBALS['i']['question_t3'];
		}

		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data);
	}
}
?>