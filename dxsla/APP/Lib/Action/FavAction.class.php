<?php
class FavAction extends CommonAction
{
    public function add() {
		if(!checkpost()) exit('error');
		if(!$GLOBALS['islogin']) exit('{"stat":9, "errno":"未登录", "error":"未登录}');
		$msgModel = D('Msg');
        $u = $msgModel->check_contact('uid', 'uid');
		
		$post_status = postvar('status', array(1, 2));
		$fav_too = 0;
		
		//检查是否已经收藏
        $fav = M('fav');
        $data['uid'] = $GLOBALS['i']['uid'];
        $data['fav_uid'] = $u['uid'];
        $rs = $fav->field('id')->where($data)->find();
        if($rs) exit('2');

		//查看对方是否收藏
		if($post_status == 2) {
			$map['uid'] = $u['uid'];
			$map['fav_uid'] = $GLOBALS['i']['uid'];
			$status = $fav->where($map)->getField('status');
			if($status && $status == 2) {
				$fav_too = 1;
				//通知对方已互相收藏
				$fav->where($map)->setField('fav_too', $fav_too);
			}
		}
        
		//收藏
		$data['status'] = $post_status;
        $data['add_time'] = time();
        $data['fav_too'] = $fav_too;
		$id = $fav->add($data);
		
		//更新双方的统计数据
		if($id) {
			if($post_status == 1) {
				//exit('111');
				$msgModel = D('Msg');
				$msgModel->sys_notifi($u['uid'], '某人悄悄收藏了你');
			}
			$member_field = M('member_field');
			$data1['new_fav_in'] = array('exp', 'new_fav_in+1');
			$data1['fav_in_num'] = array('exp', 'fav_in_num+1');
			$member_field->where("uid={$u['uid']}")->save($data1);		
			$data2['new_fav_out'] = array('exp', 'new_fav_out+1');
			$data2['fav_out_num'] = array('exp', 'fav_out_num+1');
			//收藏人的uid冗余 start
			$data2['ifav_uids'] = rtrim($GLOBALS['i']['ifav_uids'], ',') . ",{$u['uid']},"; 
			//收藏人的uid冗余 end
			$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data2);
			//echo $member_field->getLastSql();
		}
		echo $post_status == 2 ? 1 : 3;
    }

    public function out() {
	    if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$member_field=M('member_field');
		$member_fieldlist=$member_field->where("uid={$GLOBALS['i']['uid']}")->find();
		$this->assign('f', $member_fieldlist);
        $uid = $GLOBALS['i']['uid'];
        $fav = M('fav');
		$count = $fav->where("uid=$uid")->count();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $favlist = $fav->table('qh_fav AS A')
					   ->field("B.uid, B.sex, B.default_pic, B.username, B.hometown_prov, B.hometown_city, B.height,B.login_time, A.add_time, A.fav_remark, A.fav_too")
					   ->join("LEFT JOIN qh_member AS B ON A.fav_uid=B.uid")
					   ->where("A.uid=$uid")
					   ->limit($page->firstRow.','.$page->listRows)
					   ->order("A.add_time DESC")
					   ->select();
        //echo $fav->getLastSql();
        $this->assign('favlist', $favlist);
		$this->assign('pages', $page->show());
        $this->display();
    }

    public function in() {
	    if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$member_field=M('member_field');
		$member_fieldlist=$member_field->where("uid={$GLOBALS['i']['uid']}")->find();
		$this->assign('f', $member_fieldlist);
        $data1['new_fav_in'] = 0;
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data1);
        $uid = $GLOBALS['i']['uid'];
        $fav = M('fav');
		$count = $fav->where("fav_uid=$uid")->count();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $favlist = $fav->table('qh_fav AS A')
                       ->field("B.uid, B.sex, B.default_pic, B.username, B.hometown_prov, B.hometown_city, B.height, A.add_time, A.fav_too, A.status")
                       ->join("LEFT JOIN qh_member AS B ON A.uid=B.uid")
                       ->where("A.fav_uid=$uid")
                       ->limit($page->firstRow.','.$page->listRows)
                       ->order("A.add_time DESC")
					   ->select();
        //echo $fav->getLastSql();
        $this->assign('favlist', $favlist);
		$this->assign('pages', $page->show());
        $this->display();
    }

    public function delete_ifav(){
	    if(!$GLOBALS['islogin']) {
            exit();
        }
        if(!checkpost()) exit();
		$fav_uid = postvar('uid', 1);
		
        $fav = M("fav");
        $map['uid'] = $GLOBALS['i']['uid'];
        $map['fav_uid'] = $fav_uid;		
        $fav->where($map)->delete();
		
		$map2['uid'] = $fav_uid;
		$map2['fav_uid'] = $GLOBALS['i']['uid'];
		$fav_too = $fav->where($map2)->getField('fav_too');
		if($fav_too && $fav_too == 1) {
			$fav->where($map2)->setField('fav_too', 0);
		}
		
		$member_field=M('member_field');
		$data1['fav_in_num'] = array('exp', 'fav_in_num-1');
		$member_field->where("uid=$fav_uid")->save($data1);
		$data2['fav_out_num'] = array('exp', 'fav_out_num-1');
		//收藏人的uid冗余 start
		$data2['ifav_uids'] = str_replace(",{$fav_uid},", '', $GLOBALS['i']['ifav_uids']);
		//dump($data2['ifav_uids']);
		//收藏人的uid冗余 end		
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data2);
        echo 1;
    }
	
	public function fav_online() {
		echo 7;
	}
	
	public function add_remark() {
		$remark = postvar('remark');
		$uid = postvar('uid', 1);
		$fav = M('fav');
		$fav->where("uid={$GLOBALS['i']['uid']} AND fav_uid=$uid")->setField('fav_remark', $remark);
		$array = array(
            "ret" => 1,
            "uid" =>  "$uid",
			"remark" => "$remark",
			"show_remark" => "$remark",
			"bt_img" => "/CDN/app/img/ico_write.png"
		);
		echo json_encode($array);
	}
}
?>