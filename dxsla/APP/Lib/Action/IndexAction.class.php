<?php
class IndexAction extends CommonAction
{
    public function index() {
        if(!$GLOBALS['islogin']) {
            $this->index_login();
            exit();
        }
		
        $offset = $GLOBALS['i']['user_ping_offset'];
        $member = M('member');
        $sex = 3 - $GLOBALS['i']['sex'];
        $count = $member->where("sex=$sex AND default_photonum>0 AND group_type>0")->count();
		$update_offset = FALSE;
        if($GLOBALS['i']['login_time'] + 60 < time()) {
            $offset += rand(1, 8);
            $update_offset = TRUE;
        }
        if($offset > $count - 13) {
            $offset = 0;
            $update_offset = TRUE;
        }
		if($update_offset) {
			$member->where("uid={$GLOBALS['i']['uid']}")->setField('user_ping_offset', $offset);
		}
        $userlist = $member->where("sex=$sex AND default_photonum>0 AND group_type>0")->limit("$offset, 12")->select();
		//echo $member->getLastSql();
		
        $this->assign('userlist', $userlist);
        $this->display();
    }

    public function index_login() {
		$invite_uid = getvar('uid', 1);
		if($invite_uid) cookie('invite_uid', $invite_uid, 180);
		$member = M('member');
		$memberlist1 = $member->field('uid, sex, username, default_pic, college, want_content, photonum, score_impress')->where("default_pic!='' AND sex=1 AND default_photonum>0 AND group_type>0")->order('uid DESC')->limit('5, 2')->select();
		$memberlist2 = $member->field('uid, sex, username, default_pic, college, want_content, photonum, score_impress')->where("default_pic!='' AND sex=2 AND default_photonum>0 AND group_type>0")->order('uid DESC')->limit('1, 8')->select();
		$memberlist3 = $member->field('uid, sex, username, default_pic, college, want_content, photonum, score_impress')->where("default_pic!='' AND sex=1 AND default_photonum>0 AND group_type>0")->order('uid DESC')->limit('7, 17')->select();
		$memberlist = array_merge($memberlist1, $memberlist2, $memberlist3);
		$rand = rand(1, 9);
		$mosaic = '';
		for($i = 1; $i < 33; $i ++) {
			$pixel = 120;
			if($i > 25) {
				$pixel = 32;
			} elseif($i > 15) {
				$pixel = 48;
			} elseif($i > 5) {
				$pixel = 72;
			} else {
				$pixel = 120;
			}
			$pz = $pixel == 32 ? 48 : $pixel;
			$mosaic .= "<div id=\"mp_c{$i}\" class=\"mp_c mp_cs{$pixel}\">";
			if(($i + $rand) % 5 == 0) {
			//if(1) {
				$rand = ($i + $rand + 3) % 9 + 1;
				$mosaic .= "<div class=\"mp_c_p mp_c_p{$rand} mp_c_tz\">";
			} else {
				$m = current($memberlist);
				$mj = json_encode($m);
				$mj = str_replace('"', "'", $mj);
				next($memberlist);
				$mosaic .= '<div class="mp_c_ph mp_c_tz">';
				$mosaic .= "<a class=\"mp_c_ph_l\" target=\"_blank\" href=\"{$GLOBALS['s']['urldomain']}/{$m['uid']}\">
<img id=\"img_0_{$i}\" class=\"mp_c_ph_img\" width=\"{$pixel}\" height=\"{$pixel}\" src=\"{$GLOBALS['s']['urlupload']}{$m['default_pic']}_{$pz}x{$pz}.jpg\" title=\"{$m['username']}的个人档案\" alt=\"{$m['username']}的个人档案\" onmouseover=\"show_user_tips({$i},{$mj})\" /></a>";
			}
			$mosaic .= '</div></div>';
		}
		$count = $member->count() + 1212;
		$counter = '';
		$n = $count;
		$i = 1;
		while($n > 0) {
			$num = $n % 10;	
			if($i % 3 == 1 && $i > 1) {
				$counter = '<span class="mp_cnt_d mp_cnt_space"><span class="mp_cnt_d_os"></span></span>' . $counter;
			}
			$counter = "<span class=\"mp_cnt_d mp_cnt_d_{$num}\"><span class=\"mp_cnt_d_o\">{$num}</span> <i class=\"mp_cnt_d_f\"></i></span>" . $counter;
			$n = ($n - $num) / 10;
			$i ++;
		}
		
		$this->assign('mosaic', $mosaic);
		$this->assign('counter', $counter);
        $this->display('index_login');
    }
}
?>