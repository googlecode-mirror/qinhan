<?php
class Admin2qinhan2orgAction extends CommonAction
{
	public function _initialize() {
		parent::_initialize();
		if(!$GLOBALS['islogin'] || !in_array($GLOBALS['i']['uid'], array(20133, 20033,124,82,20046))) {
			header("HTTP/1.0 404 Not Found");
			//echo $GLOBALS['i']['username'];
			exit();
		}
		$GLOBALS['s']['urladmin'] = $GLOBALS['s']['urlsite'] . '/' . strtolower(MODULE_NAME);
		$this->assign('urladmin', $GLOBALS['s']['urladmin']);
	}

	public function index() {
		redirect($GLOBALS['s']['urladmin'] . '/chk_photo/');
	}
	
	public function chk_photo() {
		if(checkpost()) {
			$act = getvar('act', array('pass', 'del', 'change_sex'));
			$path = postvar('path');
			$uid = postvar('uid', 1);
			if($act == 'pass') {
				$photo = M("photo");
				$data['is_pass'] = 1;
				$photo->where("path='$path'")->save($data);
			} elseif($act == 'del') {
				$photo = M("photo");
				$p = $photo->where("path='$path'")->find();
				if(!$p) exit();
				$member = M('member');
				$m = $member->where("uid={$p['uid']}")->find();
				if(!$m) exit();
				
				$photoModel = D('Photo');
				$photoModel->del_photo($p, $m);
				
				$str = '<p class="mbox_sys_back_t clear">抱歉，您上传的照片因为以下原因未通过审核：<br><br>1、照片模糊不清，或尺寸太小 (<span class="f_r">长宽必须大于 120 像素</span>)；<br>2、非本人照片。<br>3、合照<br>4、经过PS处理后的照片<br></p><p class="box_main_box3_bg clear" style="width:200px;">未通过审核的照片<br><img src="' . $GLOBALS['s']['urlupload'] . $p['path'] . '_72x72.jpg"></p><p></p>';
				$msgModel = D('Msg');
				$msgModel->sys_notifi($p['uid'], $str);
		
			} else {
				$member = M('member');
				$edit['sex'] = array('exp', '3-sex');
				$member->where("uid=$uid")->save($edit);
				//echo $member->getLastSql();
			}
			exit('1');
		}
		$sex = getvar('sex', array(1, 2));
		import("@.ORG.Page");
		$photo = M("photo");
		$count = $photo->where("is_pass=0 AND gid=0 AND sex=$sex")->count();
		$page = new Page($count, 10);
		$photolist = $photo->field("qh_photo.uid, pid,qh_member.username, default_pic, group_concat( path ) AS paths")->join("qh_member ON qh_photo.uid=qh_member.uid")->where("is_pass=0 AND gid=0 AND sex=$sex")->group("qh_photo.uid")->order('qh_photo.add_time DESC')->limit($page->firstRow.','.$page->listRows)->select();
		//echo $photo->getLastSql();
		$this->assign('photolist', $photolist);
		$this->assign('pages', $page->show());
		$this->assign('sex', $sex);
		$this->display();
	}

	public function other() {
		if(checkpost()) {
			$act = getvar('act', array('b', 'c', 'd'));
			$uid = postvar('uid', 1);
			$content = postvar('content');
			if($act == 'b') {
				$member = M('member');
				$member->where("uid=$uid")->setField('group_type', 2);
			} elseif($act == 'c') {
				$photo = M('photo');
				$photo->where("uid=$uid AND gid=0")->setField('is_pass', 0);
			} elseif($act == 'd') {
				$msgModel = D('Msg');
				$msgModel->sys_notifi($uid, $content);
			}
			exit(1);
		}
		$this->display();
	}
	
	public function chk_word(){
	
	import("@.ORG.Page");
    $kf = M("question");
	$count =$kf->count();
	$page = new Page($count, 10);
	$list =$kf->limit($page->firstRow.','.$page->listRows)->select();
	$this->assign('list',$list);
	$this->assign('count',$count);
	$this->assign('page',$page->show());
	$this->display();
	}	
	
	public function user(){
	import("@.ORG.Page");
	if(isset($_POST['submit'])){
	$type=$_REQUEST['type'];
	$nr=trim($_REQUEST['nr']);
	if($type==1){
	$map['uid']=array('eq',$nr);
	}else{
	$map['username']=array('eq',$nr);
	}}
		$sex = getvar('sex', 1);
	if($sex) {
			$map["sex"]=$sex;
		}
    $user = M("member");
	$count =$user->where($map)->count();
	$page = new Page($count, 10);
	$list =$user->where($map)->order('login_time desc')->limit($page->firstRow.','.$page->listRows)->select();
	//echo $user->getLastSql();
	$dur = time()-65;
	$count_online =$user->where("login_time > $dur")->count();
	$this->assign('list',$list);
	$this->assign('count',$count);
	$this->assign('count_online',$count_online);
	$this->assign('page',$page->show());
	$this->display();
	}	

}
?>
