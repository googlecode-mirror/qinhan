<?php
class VisitAction extends CommonAction
{
    public function add() {
        if($GLOBALS['islogin']) {
            $uid = $GLOBALS['i']['uid'];
            $visit_uid = getvar('uid', 1);
            if($uid != $visit_uid) {
                $member = M('member');
                $u = $member->field('sex')->where("uid=$visit_uid")->find();
                if($u && $u['sex'] != $GLOBALS['i']['sex']) {
                    $visit = M('visit');
                    $data['uid'] = $uid;
                    $data['visit_uid'] = $visit_uid;
                    $rs = $visit->field('id')->where($data)->find();
					//echo '<!--', $visit->getLastSql(), '-->';
                    if(!$rs) {
						$data['add_time'] = time();
						$id = $visit->add($data);
				    } else {
						$visit->where($data)->setField('add_time', time());
					}
					$member_field = M('member_field');
					$data1['new_visitor'] = array('exp', 'new_visitor+1');
					$member_field->where("uid=$visit_uid")->save($data1);					
                }
            }
        }
    }

    public function out() {
	    if(!$GLOBALS['islogin']) {
			redirect($GLOBALS['s']['urlsite'] . '/member/login');
		}
		$member_field = M('member_field');
		$member_fieldlist = $member_field->where("uid={$GLOBALS['i']['uid']}")->find();
		$this->assign('f', $member_fieldlist);
        $uid = $GLOBALS['i']['uid'];
        $visit = M('visit');
		$count = $visit->where("uid=$uid")->count();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $visitlist = $visit->field("A.uid, A.sex, A.default_pic, A.username, A.birth_y, A.hometown_prov, A.hometown_prov,A.height,A.height, A.college, A.want_content, A.photonum, qh_visit.score_impress, qh_visit.score_face, qh_visit.score_body, qh_visit.score_temper, qh_visit.add_time")->join("LEFT JOIN qh_member AS A ON qh_visit.visit_uid=A.uid")->where("qh_visit.uid=$uid")->order("qh_visit.add_time DESC")->limit($page->firstRow.','.$page->listRows)->select();
        //echo $visit->getLastSql();
        $this->assign('visitlist', $visitlist);
		$this->assign('pages', $page->show());
        $this->display();
    }

    public function in() {
	    if(!$GLOBALS['islogin']) {
			redirect($GLOBALS['s']['urlsite'] . '/member/login');
		}	
	    $member_field = M('member_field');
        $data1['new_visitor'] = 0;
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data1);
        $uid = $GLOBALS['i']['uid'];
        $visit = M('visit');
		$count = $visit->where("visit_uid=$uid")->count();
		//echo $visit->getLastSql();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $visitlist = $visit->field("A.uid, A.sex, A.default_pic, A.username, A.birth_y, A.hometown_prov, A.hometown_prov, A.college,A.height,A.want_content, A.photonum, qh_visit.score_impress, qh_visit.score_face, qh_visit.score_body, qh_visit.score_temper, qh_visit.add_time")->join("LEFT JOIN qh_member AS A ON qh_visit.uid=A.uid")->where("qh_visit.visit_uid=$uid")->order("qh_visit.add_time DESC")->limit($page->firstRow.','.$page->listRows)->select();
        //echo $visit->getLastSql();
        $this->assign('visitlist', $visitlist);
		$this->assign('pages', $page->show());
        //dump($page);
        $this->display();
    }
}
?>