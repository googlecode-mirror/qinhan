<?php
class MainAction extends CommonAction
{
    public function attention() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$attention = A('Attention');
		$attention->_assign_feed(48, 'index');
        $this->display();
    }
	
    public function attention_more() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$attention = A('Attention');
		$attention->_assign_feed(48, 'more');
        $this->display('attention');
    }
	
	public function visiti() {
		$uid = $GLOBALS['i']['uid'];
		$visit = M('visit');
		$visitlist = $visit->field("A.uid, A.sex, A.default_pic, A.username, A.birth_y, A.hometown_prov, A.hometown_prov, A.college, A.height, A.want_content, A.photonum, qh_visit.score_impress, qh_visit.score_face, qh_visit.score_body, qh_visit.score_temper, qh_visit.add_time")->join("LEFT JOIN qh_member AS A ON qh_visit.uid=A.uid")->where("qh_visit.visit_uid=$uid")->order("qh_visit.add_time DESC")->limit(10)->select();
		//echo $visit->getLastSql();
		$this->assign('visitlist', $visitlist);
		$this->display();
	}
	
	public function ivisit() {
		$uid = $GLOBALS['i']['uid'];
		$visit = M('visit');
		$visitlist = $visit->field("A.uid, A.sex, A.default_pic, A.username, A.birth_y, A.hometown_prov, A.hometown_prov, A.height, A.college, A.want_content, A.photonum, qh_visit.score_impress, qh_visit.score_face, qh_visit.score_body, qh_visit.score_temper, qh_visit.add_time")->join("LEFT JOIN qh_member AS A ON qh_visit.visit_uid=A.uid")->where("qh_visit.uid=$uid")->order("qh_visit.add_time DESC")->limit(10)->select();
		//echo $visit->getLastSql();
		$this->assign('visitlist', $visitlist);
		$this->display();
	}
	
	public function favi() {
		$uid = $GLOBALS['i']['uid'];
		$fav = M();
		$favlist = $fav->table('qh_fav AS A')->field("B.uid, B.sex, B.default_pic, B.username, B.hometown_prov, B.hometown_city, B.height, B.college, B.want_content, B.photonum, A.add_time, A.status")->join("LEFT JOIN qh_member AS B ON A.uid=B.uid")->where("A.fav_uid=$uid")->order("add_time DESC")->limit(10)->select();
		$this->assign('favlist', $favlist);
		$this->display();
	}
	
	public function ifav() {
		$uid = $GLOBALS['i']['uid'];
		$fav = M();
		$favlist = $fav->table('qh_fav AS A')->field("B.uid, B.sex, B.default_pic, B.username, B.hometown_prov, B.hometown_city, B.height, B.height, B.college, B.want_content, B.photonum, A.add_time,A.fav_remark,A.fav_too")->join("LEFT JOIN qh_member AS B ON A.fav_uid=B.uid")->where("A.uid=$uid")->order("add_time DESC")->limit(10)->select();
		$this->assign('favlist', $favlist);
		$this->display();
	}
	
	public function operations() {
		$arr = array(
			array('memo' => "<a href=\"{$GLOBALS['s'][urlsite]}/photo/\" style=\"color:#ffffff\">欢迎上传更多自己的生活照片！</a>")
		);
		echo json_encode($arr);
	}
}
?>