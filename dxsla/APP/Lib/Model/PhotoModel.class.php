<?php
class PhotoModel extends Model
{
	//数据统计的加减
	//$pg 当前需要改变的照片分类
	//$num 照片的加减数
	//$default_pic 备用默认照片
	//$p 当前要去除的图片，删除移动图片，一次只能操作一张
	//$i 当前用户
    public function edit_photo($pg, $num = -1, $default_pic = '', $p = array(), $i = NULL) {
		$i = $i ? $i : $GLOBALS['i'];
		if($pg['gid'] == 0) {
			$pg['photonum'] = isset($i['default_photonum']) ? $i['default_photonum'] : 0;
			$pg['default_pic'] = isset($i['default_pic']) ? $i['default_pic'] : 0;
		}
		$new_pic = $pg['default_pic'];
		if($pg['photonum'] == 0 || $pg['default_pic'] == '') {
			$new_pic = $default_pic;
		}
		$new_num = $pg['photonum'] + $num;
		if($new_num == 0) {
			$new_pic = '';
		} elseif($num < 0 && $pg['default_pic'] == $p['path']) {
			$photo = M('photo');
			$new_p = $photo->where("gid={$pg['gid']} AND uid={$i['uid']}")->find();
			if($new_p) {
				$new_pic = $new_p['path'];
			} else {
				$new_pic = '';
			}
		}
		if($pg['gid'] != 0) {
			$photo_group = M('photo_group');
			if($pg['default_pic'] != $new_pic) {
				$data['default_pic'] = $new_pic;
			}
			$data['photonum'] = $new_num;
			$photo_group->where("gid={$pg['gid']} AND uid={$i['uid']}")->save($data);
			//echo $photo_group->getLastSql();
			//exit();
		} else {
			if($pg['default_pic'] != $new_pic) {
				if($new_pic == '') {
					$new_pic = '/000face/s' . $i['sex'] . '.jpg';
				}
				$edit['default_pic'] = $new_pic;
				$GLOBALS['i']['default_pic'] = $new_pic;
			}
			$edit['default_photonum'] = $new_num;
		}
		$edit['photonum'] = array('exp', "photonum+$num");
		$member = M('member');
		$member->where("uid={$i['uid']}")->save($edit);	
		//exit();
    }
	
    public function del_photo($p, $i = NULL) {
		$i = $i ? $i : $GLOBALS['i'];
		$gid = $p['gid'];
		if($gid) {
			$photo_group = M("photo_group");
			$pg = $photo_group->where("gid=$gid AND uid={$i['uid']}")->find();
			if(!$pg) exit();
		} else {
			$pg = array('gid' => 0);
		}
		$photo = M("photo");
		$photo->where("pid={$p['pid']} AND uid={$i['uid']}")->delete();
		$this->edit_photo($pg, -1, '', $p, $i);
		
		$feedModel = D('Feed');
		$feedModel->feed_delete(5, $p['pid'], $i['uid']);
		
		$file = $GLOBALS['s']['pathupload'] . $p['path'];
		unlink($file . '_800x999.jpg');
		unlink($file . '_480x999.jpg');
		unlink($file . '_240x240.jpg');					
	}
}
?>