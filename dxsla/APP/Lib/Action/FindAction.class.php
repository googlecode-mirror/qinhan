<?php
class FindAction extends CommonAction
{
    public function index() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$college = M("college");
		$collegeList = $college->select();
		$this->assign('collegeList', $collegeList);
		$cid = getvar('college', 1);
		$c = $college->where("id=$cid")->find();
		$c = $c["name"];
	    //dump($c);
        $sex = $GLOBALS['i']['sex'] == 1 ? 2 : 1;
		$a = '';
		if($cid != "") {
			$a = " AND college='$c'";
			$b = "class=currt";
		}
		$a .= $GLOBALS['i']['sex'] == 2 ? ' AND default_photonum>0' : '';
		$orderby = getvar('orderby', array('login_time', 'register_time'));
        $member = M('member');
        $count = $member->where("group_type>0 AND sex=$sex.$a")->count();
		//echo $member->getLastSql();
        import("@.ORG.Page");
        $page = new Page($count, 10);
        $memberlist = $member->field('uid, sex, username, default_pic, is_videoauth, photonum, login_time, class, college, height, hometown_prov, hometown_city, birth_y, constellation, blood, stature, grade, introduce, want_content')->where("group_type>0 AND sex=$sex.$a")->order("$orderby DESC")->limit($page->firstRow.','.$page->listRows)->select();
		//echo $member->getLastSql();
        $this->assign('memberlist', $memberlist);
        $this->assign('sex', $sex);
		$this->assign('orderby', $orderby);
		$this->assign('b', $b);
		$this->assign('cid', $cid);
        $this->assign('pages', $page->show(1));
        $this->display();
    }
}
?>