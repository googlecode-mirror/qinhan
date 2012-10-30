<?php
class AttentionAction extends CommonAction
{
    public function index() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
	    $member_field=M('member_field');
		$member_fieldlist=$member_field->where("uid={$GLOBALS['i']['uid']}")->find();
		$this->assign('f', $member_fieldlist);
        $data1['new_attention'] = 0;
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data1);
        $this->_assign_feed(72, 'index');
        $this->display();
    }
	
    public function more() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$this->_assign_feed(72, 'more');
        $this->display();
    }

	public function _assign_feed($face_size, $type = 'more', $page = 0, $is_last = 0, $last_id = 0) {
        $feed = M('feed');
		$page = max(0, $page);
		$perpage = 10;
		$startnum = $perpage * $page;
		$where = '';
		if($type == 'index') {
			$where .= orsql('qh_feed.uid', trim($GLOBALS['i']['ifav_uids'], ','));
		}
        $result = $feed->field('qh_feed.uid, qh_feed.body, qh_feed.count, qh_feed.type, qh_feed.body, qh_feed.add_time, qh_member.username, qh_member.default_pic, qh_member.sex')->join("LEFT JOIN qh_member ON qh_feed.uid=qh_member.uid")->where($where)->order('add_time DESC')->limit($startnum . ',' . $perpage)->select();
		//echo $feed->getLastSql();
        $feedlist = array();
        foreach($result as $row) {
            $feedlist[$row['uid']]['uid'] = $row['uid'];
            $feedlist[$row['uid']]['username'] = $row['username'];
            $feedlist[$row['uid']]['default_pic'] = $row['default_pic'];
			$feedlist[$row['uid']]['sex'] = $row['sex'];
            $feedlist[$row['uid']]['data'][] = $row;
        }
		$this->assign('face_size', $face_size);
		$this->assign('feedlist', $feedlist);
		$this->assign('is_last', $is_last);
		$this->assign('last_id', $last_id);		
	}
	
	private function _load_feed($type = 'more') {
		if(!checkpost()) exit();
		$face_size = postvar('face_size', 1);
		$page = postvar('page', 1);
		$is_last = postvar('is_last', 1);
		$last_id = postvar('last_id', 1);		
		$this->_assign_feed($face_size, $type, $page, $is_last, $last_id);
		$feed = $this->fetch('feed');
		//if(!$feed) exit();
		$arr = array(
			'errno' => 200,
			'more' => $feed,
			'page' => $page,
			'is_new_dt' => 0
		);
		if($type == 'more') {
			$arr['is_last']	= 0;
			$arr['last_id']	= 555484;
			$arr['show_member_tips'] = 0;
			$arr['show_member_tips_ssesion'] = NULL;							
		}
		echo json_encode($arr);
	}
	
	public function load_index() {
		$this->_load_feed('index');
	}
	
	public function load_more() {
		$this->_load_feed('more');
	}
}
?>