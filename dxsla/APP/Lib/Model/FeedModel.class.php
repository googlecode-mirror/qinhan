<?php
class FeedModel extends Model
{
    public function feed_delete($type, $related, $uid = 0) {
		$uid = $uid ? $uid : $GLOBALS['i']['uid'];
		$feed = M('feed');
		if($type == 1 || $type == 2) {
			$feed->where("uid=$uid AND type=$type")->delete();
		} else {
			$old = $feed->where("uid=$uid AND type=$type")->find();
			if($old['count'] == 1) {
				$feed->where("uid=$uid AND type=$type")->delete();
			} else {
				$old_body = unserialize($old['body']);
				unset($old_body[$related]);
				$edit['count'] = sizeof($old_body);
				$edit['body'] = serialize($old_body);
				$feed->where("uid=$uid AND type=$type")->save($edit);
			}
			//dump($feed->getLastSql());
			//exit();
		}
    }

	/*
	1，写了两句，评论
	2，更新了我想，约她
	3，提了1个问题，回答
	4，回答了9个问题，回答以及评论他的回答
	5，上传了7张照片，查看更多
	6，更新了5个小编专访，每个都评论
	*/
	public function feed_publish($type, $body, $related = 0) {
		$feed = M('feed');
		$data['uid'] = $GLOBALS['i']['uid'];
		//dump($GLOBALS['i']['default_pic']);
		//exit();
		$old = $feed->where("uid={$GLOBALS['i']['uid']} AND type=$type")->find();
		if($old && $old['add_time'] + 3600 > time()) {
			$old_body = unserialize($old['body']);
		} else {
			$old_body = array();
		}
		if($type == 1 || $type == 2) {
			$arr = $body;
			$count = 1;
		} else {
			$arr = $body + $old_body;
			$count = sizeof($arr);
			if($count > 8) {
				$arr = array_slice($arr, 0, 8);
				$count = 8;
			}
		}
		$data['body'] = serialize($arr);
		$data['type'] = $type;
		$data['count'] = $count;
		$data['related'] = $related;
		$data['add_time'] = time();
		if($old) {
			$feed->where("id={$old['id']}")->save($data);
		} else {
			$feed->add($data);
		}
	}	
}
?>