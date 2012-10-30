<?php
class PhotoAction extends CommonAction
{
    public function index() {
		if(!$GLOBALS['islogin'])
			redirect($GLOBALS['s']['urlsite'] . '/member/login');	
        $photo_group = M('photo_group');
        $pglist = $photo_group->where("uid={$GLOBALS['i']['uid']}")->select();
        $this->assign('pglist', $pglist);
        $this->display();
    }

    public function group() {
		if(!$GLOBALS['islogin'])
			redirect($GLOBALS['s']['urlsite'] . '/member/login');	
        $uid = $GLOBALS['i']['uid'];
        $gid = getvar('gid', 1);
        if($gid) {
            $photo_group = M('photo_group');
            $pg = $photo_group->where("uid=$uid AND gid={$gid}")->find();
            if(!$pg) exit('error');
        } else {
            $pg = array('gid' => 0, 'name' => '个人形象照', 'default_pic'=> $GLOBALS['i']['default_pic']);
        }
        $photo = M('photo');
        $photolist = $photo->where("uid=$uid AND gid=$gid")->select();
        $this->assign('gid', $gid);
        $this->assign('pg', $pg);
        $this->assign('photolist', $photolist);
        $this->display();
    }

    public function show() {
		if(!$GLOBALS['islogin'])
			redirect($GLOBALS['s']['urlsite'] . '/member/login');	
        $uid = $GLOBALS['i']['uid'];
        $pid = getvar('pid', 1);
        $photo = M('photo');
        $p = $photo->where("uid=$uid AND pid=$pid")->find();
        if(!$p) exit('error');
        $gid = $p['gid'];
        if($gid) {
            $photo_group = M('photo_group');
            $pg = $photo_group->where("uid=$uid AND gid={$gid}")->find();
            if(!$pg) exit('error');
        } else {
            $pg = array('gid' => 0, 'name' => '个人形象照');
        }
        $next_pid = $photo->where("uid=$uid AND pid<$pid AND gid={$gid}")->order('pid DESC')->limit(1)->getField('pid');
        if(!$next_pid) $next_pid = $photo->where("uid=$uid AND gid={$gid}")->max('pid');
        $prev_pid = $photo->where("uid=$uid AND pid>$pid AND gid={$gid}")->order('pid ASC')->limit(1)->getField('pid');
        if(!$prev_pid) $prev_pid = $photo->where("uid=$uid AND gid={$gid}")->min('pid');

        $this->assign('p', $p);
        $this->assign('pg', $pg);
        $this->assign('prev_pid', $prev_pid);
        $this->assign('next_pid', $next_pid);
        $this->display();
    }

    public function up_form() {
        $uid = $GLOBALS['i']['uid'];
        $gid = getvar('gid', 1);
        $photo_group = M('photo_group');
        if($gid) {
            $pg = $photo_group->field('gid,name')->where("uid=$uid AND gid=$gid")->find();
            if(!$pg) exit();
        } else {
            $pg = array('gid' => 0, 'name' => '个人形象照');
        }

        $pglist = $photo_group->field('gid,name')->where("uid=$uid")->select();
        $from = getvar('from');
        if($from && $pglist) $pg = $pglist[0];
        $this->assign('pg', $pg);
        $this->assign('pglist', $pglist);
        $this->display();
    }

    public function api_upload() {
		if(!checkpost()) exit();
		$gid = postvar('gid', 1);
		$photo_group = M('photo_group');
		if($gid) {
			$pg = $photo_group->getByGid($gid);
			if(!$pg) exit('error');
		} else {
			$pg = array('gid' => 0, 'default_pic' => $GLOBALS['i']['default_pic']);
		}
        $rs = $this->_api_upload();
        if(!is_array($rs)) {
            $msg = $rs;
        } else {
			$this->_api_add_num($rs, $pg);
            $msg = "top.location.href='{$GLOBALS['s']['urlsite']}/photo/group?gid={$gid}';";
        }
        $this->assign('msg', $msg);
        $this->display();
    }

    public function _api_upload() {
		import("@.ORG.UploadFile");
		$upload = new UploadFile();
		$upload->hashLevel = 2;
		$upload->allowExts = explode(',', 'jpg,gif,png,jpeg,bmp');
		$upload->savePath = $GLOBALS['s']['pathupload'];
        $upload->cut = '1,1,1,0,0,0,0';
		$upload->thumbSuffix = '_48x48,_72x72,_120x120,_240x240,_999x80,_480x999,_800x999';
		$upload->thumbMaxWidth = '48,72,120,240,999,480,800';
		$upload->thumbMaxHeight = '48,72,120,240,80,999,999';

		if(!$upload->upload()) {
			$rs = "top.upload_fail('" . $upload->getErrorMsg() . "', " . ($upload->getFileIndex() + 1) . ");";
		} else {
            $rs = $upload->getUploadFileInfo();
        }
        return $rs;
    }
	
	public function _api_add_num($uploadlist, $pg) {
		$photo = M('photo');
		$paths = $dot = '';
		foreach($uploadlist as $file) {
			$paths .= "$dot '{$file['savename']}'";
			$dot = ',';
			$dataitem['uid'] = $GLOBALS['i']['uid'];
			$dataitem['gid'] = $pg['gid'];
			$dataitem['path'] = $file['savename'];
			$data[] = $dataitem;
		}
		$photo->addAll($data);
		
		$photoModel = D('Photo');
		$photoModel->edit_photo($pg, sizeof($data), $uploadlist[0]['savename']);
		
		//发布动态
		$result = $photo->field('pid, gid, path')
						->where("uid={$GLOBALS['i']['uid']} AND gid={$pg['gid']} AND path IN($paths)")
						->order('pid DESC')
						->limit(30)
						->select();
		$body = array();
		foreach($result as $row) {
			$body[$row['pid']] = $row;
		}
		//feed_publish(5, $body);
		$feedModel = D('Feed');
		$feedModel->feed_publish(5, $body);			
		//发布动态            
	}

    public function api_add_group() {
        if(!checkpost()) exit();
        $photo_group = M('photo_group');
        $data['name'] = postvar('name');
        $data['content'] = postvar('content');
        $data['uid'] = $GLOBALS['i']['uid'];
        $gid = $photo_group->add($data);
        if($gid) {
            echo '{"status":1,"msg":' . $gid . '}';
        } else {
            echo '{"status":0}';
        }
    }

    public function get_permission_type() {
        echo '{"stat":0}';
    }

    public function wenwenup() {
        if(!checkpost()) exit();
		import("@.ORG.UploadFile");
		$upload = new UploadFile();
		$upload->cut = '1,1,0';
		$upload->hashLevel = 2;
		$upload->allowExts = explode(',', 'jpg,gif,png,jpeg,bmp');
		$upload->savePath = $GLOBALS['s']['pathupload'];
		$upload->thumbSuffix = '_72x72,_120x120,_480x480';
		$upload->thumbMaxWidth = '72,120,480';
		$upload->thumbMaxHeight = '72,120,480';

		if(!$upload->upload()) {
			echo $upload->getErrorMsg();
		} else {
            $uploadList = $upload->getUploadFileInfo();
			$question_photo = M('question_photo');
			$data['img_path'] = $uploadList[0]['savename'];
			$data['status'] = 1;
			$photo_id = $question_photo->add($data);
			echo "$photo_id";
        }
    }

    public function api_querstion_up_photo() {
        if(!checkpost()) exit();
		$photo_id = intval($_POST['data']);
		$question_photo = M('question_photo');
		$p = $question_photo->getByPhoto_id($photo_id);
		if($p) {
			$p['img_path'] = $GLOBALS['s']['urlupload'] . $p['img_path'] . '_120x120.jpg';
			echo json_encode($p);
		}
    }

    //修改相片说明
    public function u_p() {
        if(!checkpost()) exit();
        $pid = postvar('id', 1);
        $photo = M('photo');
        $photo->where("pid=$pid and uid={$GLOBALS['i']['uid']}")->setField('content', $_POST['content']);
        echo "1";
    }
	
	public function api_get_group(){
	    if(!checkpost()) exit();
		$uid = postvar('uid', 1);
		$photo_group=M("photo_group");
		$list=$photo_group->where("uid={$GLOBALS['i']['uid']}")->select();
		//echo $photo_group->getLastSql();
		//dump ($list);
		$array = array(
			"msg" => $list,
			"status" => 1
		);
	
 		echo json_encode($array);
	}
	
	public function api_change_group(){
		if(!checkpost()) exit();
	    $gid = postvar('gid', 1);
		$pid = postvar('pid', 1);
		$photo_group = M("photo_group");
		$photo = M("photo");
		if($gid) {
			$pg = $photo_group->where("gid=$gid AND uid={$GLOBALS['i']['uid']}")->find();
			if(!$pg) exit();
		} else {
			$pg = array('gid' => 0);
		}
		$p = $photo->where("pid=$pid AND uid={$GLOBALS['i']['uid']}")->find();
		if(!$p) exit();
		$gid2 = $p['gid'];
		if($gid2) {
			$pg2 = $photo_group->where("gid=$gid2 AND uid={$GLOBALS['i']['uid']}")->find();
			if(!$pg2) exit();
		} else {
			$pg2 = array('gid' => 0);
		}

		$photo->where("pid=$pid AND uid={$GLOBALS['i']['uid']}")->setField('gid', $gid);
		$photoModel = D('Photo');
		$photoModel->edit_photo($pg, 1, $p['img_path']);
		$photoModel->edit_photo($pg2, -1, '', $p);
		
	    echo '{"status":1,"msg":""}';
	}
	
   	public function d_p(){
		if(!checkpost()) exit();
	    $pid = postvar('id', 1);
	    $photo = M("photo");
		$p = $photo->where("pid=$pid AND uid={$GLOBALS['i']['uid']}")->find();
		if(!$p) exit();
		if($p['path'] == $GLOBALS['i']['default_pic']) exit(5);
		
		$photoModel = D('Photo');
		$photoModel->del_photo($p);
		
		echo 1;
	}
	
	public function api_edit_group(){
		if(!checkpost()) exit();
	    $gid = postvar('gid', 1);
		$default_pic = postvar('group_face');
		$photo_group = M("photo_group");
		//设为封面
		if($default_pic) {
			$data['default_pic'] = $default_pic;
			if($gid == 0) {
				$member = M('member');
				$member->where("uid={$GLOBALS['i']['uid']}")->save($data);
			} else {
				$photo_group->where("gid=$gid AND uid={$GLOBALS['i']['uid']}")->save($data);
			}
		}
		$name = postvar('name');
		$content = postvar('content');
		$per_type = postvar('per_type', 1);
		//编辑相册
		if($name){                           
			$data1['name'] = $name;
			$data1['content'] = $content;
			$data1['per_type'] = $per_type;
			$photo_group->where("gid=$gid")->save($data1);
			//echo $photo_group->getLastSql();
		}
		//echo $photo_group->getLastSql();
		echo '{"status":1,"msg":""}';
	}
	
	public function api_del_photo_bygroup(){
		if(!checkpost()) exit();
	    $gid = postvar('gid',1);
		$photo_group = M("photo_group");
		$photo_group->where("gid=$gid AND uid={$GLOBALS['i']['uid']}")->delete();
	    echo '{"status":1,"msg":""}';
	}
	
	public function in(){
		if(!$GLOBALS['islogin']) {
			redirect($GLOBALS['s']['urlsite'] . '/member/login');
		}
        $member_field = M('member_field');
        $data1['new_receive_score'] = 0;
		$member_fieldlist = $member_field->where("uid={$GLOBALS['i']['uid']}")->find();
		$this->assign('f', $member_fieldlist);
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data1);
        $photo_hot = M('photo_hot');
		$count = $photo_hot->where("photo_uid={$GLOBALS['i']['uid']}")->count();
		//echo $photo_hot->getLastSql();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $photo_hotlist = $photo_hot->field("A.uid, A.default_pic, A.sex, A.username, A.birth_y, A.hometown_prov, A.hometown_prov, A.college, A.want_content, A.photonum, qh_photo_hot.score,  qh_photo_hot.add_time, qh_photo_hot.pid, qh_photo.path")
								   ->join("LEFT JOIN qh_member AS A ON qh_photo_hot.uid=A.uid")
								   ->join("LEFT JOIN qh_photo ON qh_photo_hot.pid=qh_photo.pid")
								   ->where("qh_photo_hot.photo_uid={$GLOBALS['i']['uid']}")
								   ->order("qh_photo_hot.add_time DESC")
								   ->limit($page->firstRow.','.$page->listRows)
								   ->select();
		//echo $photo_hot->getLastSql();
								   
        $this->assign('photo_hotlist', $photo_hotlist);
		$this->assign('pages', $page->show());
        $this->display();
	}	
}
?>