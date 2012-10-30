<?php
class PayModel extends Model
{
    public function edit_pay($uid, $type = 'link', $num = -1, $msg) {
		$card_log = M("card_log");
		$data['type'] = $type;
		$data['num'] = $num;
		$data['uid'] = $uid;
		$data['add_time'] = time();
		$data['msg'] = $msg;
		$card_log->add($data);
		
		$member = M("member");
		$edit['card_num'] = array('exp', "card_num+$num");
		$member->where("uid=$uid")->save($edit);
		//echo $member->getLastSql();
    }
}
?>