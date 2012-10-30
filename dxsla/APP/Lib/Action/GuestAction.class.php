<?php
class GuestAction extends CommonAction
{
	public function _initialize() {
		parent::_initialize();
		if($GLOBALS['islogin']) redirect('/');
		
		$guest_name = cookie('username');
		if(!$guest_name) $guest_name = '打酱油的';
		$guest_pic = cookie('guest_pic');
		if(!$guest_pic) {
			$guest_pic = '/000face/ask_' . rand(1, 9) . '.jpg';
			cookie('guest_pic', $guest_pic);
		}
		$this->assign('guest_name', $guest_name);
		$this->assign('guest_pic', $guest_pic);		
	}

	public function index() {
		redirect('/');
	}
	
    public function xiaoyuannannv() {
		$guest_name = cookie('username');
		$guest_pic = cookie('guest_pic');
		if(!$guest_pic) {
			$guest_pic = '/000face/ask_' . rand(1, 9) . '.jpg';
			cookie('guest_pic', $guest_pic);
		}
		
		$GLOBALS['i'] = array(
			'uid' => 0,
			'username' => $guest_name ? $guest_name : '游客',
			'default_pic' => '/000face/s1.jpg'
		);

		$college = M("college");
		$collegeList = $college->select();
		$this->assign('collegeList', $collegeList);
		$cid = getvar('college', 1);
		$c = $college->where("id=$cid")->find();
		$c = $c["name"];
	    //dump($c);
        $sex = getvar('sex', 1);
		$addsql = '';
		if($sex) {
			$addsql .= "AND sex=$sex";
		}
		if($cid != "") {
			$addsql .= " AND college='$c'";
			$b = "class=currt";
		}
		$orderby = getvar('orderby', array('login_time', 'register_time'));
        $member = M('member');
        $count = $member->where("default_photonum>0 AND group_type>0 $addsql")->count();
		//echo $member->getLastSql();
        import("@.ORG.Page");
        $page = new Page($count, 10);
        $memberlist = $member->field('uid, sex, username, default_pic, is_videoauth, photonum, login_time, class, college, height, hometown_prov, hometown_city, birth_y, constellation, blood, stature, grade, introduce, want_content')->where("default_photonum>0 AND group_type>0 $addsql")->order("$orderby DESC")->limit($page->firstRow.','.$page->listRows)->select();
		//echo $member->getLastSql();
        $this->assign('memberlist', $memberlist);
        $this->assign('sex', $sex);
		$this->assign('orderby', $orderby);
		$this->assign('b', $b);
		$this->assign('cid', $cid);
        $this->assign('pages', $page->show(1));
        $this->display();
    }
	
    public function task() {
		$guest_name = cookie('guest_name');
		$GLOBALS['i'] = array(
			'uid' => 0,
			'username' => $guest_name ? $guest_name : '游客',
			'default_pic' => '/000face/s1.jpg'
		);	
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
	
	public function ruhepinglun() {
		$this->display();
	}
}
?>