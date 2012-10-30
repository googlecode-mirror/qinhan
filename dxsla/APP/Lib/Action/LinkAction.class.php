<?php
class LinkAction extends CommonAction
{
    public function forbid_list() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$link=M('link');
		$count=$link->where("uid={$GLOBALS['i']['uid']}")->count();
        import("@.ORG.Page");
        $page = new Page($count, 12);
        $linklist = $link->table('qh_link AS A')
		                 ->field("B.uid, B.username, B.default_pic, A.forbid_uid, A.status, A.add_time")
					    ->join("LEFT JOIN qh_member AS B ON B.uid = A.forbid_uid")
					   ->where("A.uid={$GLOBALS['i']['uid']}")
					   ->order("A.id DESC")
					   ->limit($page->firstRow.','.$page->listRows)
					   ->select();
       // echo $link->getLastSql();
        $this->assign('linklist', $linklist);
        $this->assign('pages', $page->show());
		$this->display();
    }
	
    public function cancel_forbid(){
	     if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$forbid_uid=postvar('forbid_uid'); 
		$link=M("link");
		
		$link->where("forbid_uid=$forbid_uid AND uid={$GLOBALS['i']['uid']}")->delete(); 
		//echo $link->getLastSql(); 
		echo 1;
	}
	
    public function forbid() {
        if(!$GLOBALS['islogin']) {
            exit();
        }
        if(!checkpost()) exit();
		$forbid_uid = postvar('forbid_uid', 1);
        $data['uid'] = $GLOBALS['i']['uid'];
        $data['forbid_uid'] = $forbid_uid;		
        $link = M('link');
		$rs = $link->where($data)->find();
		if($rs) {
			echo '-2';
		} else {
			$status = postvar('status', array(1, 2));
			$data['status'] = $status;
			$data['add_time'] = time();
			$link->add($data);
			echo $status == 2 ? 1 : 2;
		}
    }
}
?>