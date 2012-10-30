<?php
class UsergroupAction extends CommonAction
{
    public function index() {
	    if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }	
		$this->display();
	}
	
    public function upgrade() {
	    if(!$GLOBALS['islogin']) {
            exit();
        }
		if($GLOBALS['i']['card_num'] < 10) exit('{"errno":5, "msg":"您的红豆总数不足"}');
		$payModel = D('Pay');
		$payModel->edit_pay($GLOBALS['i']['uid'], 'link', -10, '您成为正式会员消耗10颗红豆');
		$member = M("member");
		$data['group_type'] = 2;
		$member->where("uid={$GLOBALS['i']['uid']}")->save($data);
		//echo $member->getLastSql();
		$msgModel = D('Msg');
		$msgModel->sys_notifi($GLOBALS['i']['uid'], '恭喜你成功升级为正式会员！');
		echo '{"errno":200, "msg":"恭喜你成功升级为正式会员"}';
	}
	
    public function share_score() {
	 	$uid = getvar('uid', 1);
	    if($GLOBALS['islogin'] && ($GLOBALS['i']['uid'] == $uid || !$uid)) {
			$m = $GLOBALS['i'];
		} else {
			$member = M('member');
            $m = $member->field('uid, username, sex, default_pic, score_impress')->where("uid=$uid")->find();
			if(!$m) {
				$this->error('找不到此人');
			}
		}
		$img = $GLOBALS['s']['pathupload'] . $m['default_pic'] . '_qq.jpg';
		if(!is_file($img) || filemtime($img) + 86400 < time()) {
			$tpl = $GLOBALS['s']['pathupload'] . '/000face/share_score.jpg';
			$water = $GLOBALS['s']['pathupload'] . $m['default_pic']. '_240x240.jpg';
			$girl = array('邻家女孩','古典美','大眼妹妹','潮女','皮肤好白','性感','奶茶妹妹','似曾相识','乖乖女','时尚的女生','惊艳','眼睛好漂亮','脖子很性感','你很爱笑哦','距离感','风情女','帅气的女生','朋克女生','好迷你好娇小','野性','小资文艺女','温柔的美女','小萝莉','有气质','氧气美女','身材很魔鬼','骨感的女生','冰雪女王','笑得好妩媚好温暖','好淑女');
			
			$boy = array('骑着白马的唐僧','好美啊！','贵族王子','魅力型男','灰太狼','潮男','莲花小王子','背包客','眼神犀利','阳光宅男','黑框眼镜男','迷茫弟','王子范儿','西装笔挺','运动健将','纯爷们','很有潜力','三好学生','火星男','文艺青年','极品好男人','成熟','霸气外露','坏男人','体魄强健','老实本分','心好男人','绅士','这货不简单','猥琐');
			$arr = $m['sex'] == 1 ? $boy : $girl;
			$a1 = rand(0, 29);
			$a2 = ($a1 + rand(1, 3)) % 30;
			$a3 = ($a2 + rand(1, 3)) % 30;
			$a4 = ($a3 + rand(1, 3)) % 30;
			import('@.ORG.Image');
			//font-size, margin-left, margin-top, font-color, text
			$text = array(
				array(20, 50, 0, array(10, 10, 10), '您的照片在网站上获得      分'),
				array(28, 323, -35, array(240, 0, 35), $m['score_impress']),
				array(24, 195 + $a1 - strlen($arr[$a1]) * 5.5, -10, array(153, 0, 153), $arr[$a1]),
				array(24, 195 + $a2 - strlen($arr[$a2]) * 5.5, -10, array(102, 100, 204), $arr[$a2]),
				array(24, 195 + $a3 - strlen($arr[$a3]) * 5.5, -10, array(0, 0, 255), $arr[$a3]),
				array(24, 195 + $a4 - strlen($arr[$a4]) * 5.5, -10, array(0, 204, 51), $arr[$a4]),
			);
			Image::share_pic($tpl, $water, $img, 100, $text);
			//Image::buildString('你们好啊', array(102, 104, 104), $img);
		}
		$this->assign('m', $m);
		$this->display();
	}	
}
?>