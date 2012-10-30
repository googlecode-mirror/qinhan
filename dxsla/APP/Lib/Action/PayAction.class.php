<?php
class PayAction extends CommonAction
{

	public function card_log(){
	    if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$type = getvar('type', array('link', 'refund', 'buy'));
		$card_log = M("card_log");
		$uid = $GLOBALS['i']['uid'];
		import("@.ORG.Page");
		$count1 = $card_log->where("uid=$uid AND type='$type'")->count();
		$page1 = new Page($count1, 10);
		$list = $card_log->where("uid=$uid AND type='$type'")->order('add_time DESC')->limit($page1->firstRow.','.$page1->listRows)->select();
		//echo $card_log->getLastSql();
		$this->assign('pages1', $page1->show());
		//dump($page1);
		$this->assign('list', $list);
		$this->assign('type', $type);
	    $this->display();
	}
}
?>