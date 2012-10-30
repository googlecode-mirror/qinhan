<?php
class CommonAction extends Action
{
    public function _initialize()
    {
        //$GLOBALS['s']是全局系统变量
        //$GLOBALS['i']是全局当前登录用户信息

        $GLOBALS['s'] = array(
            'urlstatic' => 'http://jjdd01.ivu1314.com/CDN/app',
            'urlsite' => 'http://jianjiandandan.ivu1314.com',						
            'urlupload' => 'http://jjdd02.ivu1314.com/CDN/upload',
            'pathupload' => substr(dirname(__FILE__), 0, -14) . './CDN/upload',
			'urldomain' => 'http://jianjiandandan.ivu1314.com'
        );

        $this->assign('urlstatic', $GLOBALS['s']['urlstatic']);
        $this->assign('urlsite', $GLOBALS['s']['urlsite']);
        $this->assign('urlupload', $GLOBALS['s']['urlupload']);
		$this->assign('urltail', '?gv=0222');
		$this->assign('urlstatic2', '/CDN/app');
		$this->assign('urldomain', $GLOBALS['s']['urldomain']);

		$GLOBALS['islogin'] = 0;
        $uid = intval(cookie('uid'));
        $GLOBALS['i'] = array();
        if($uid && __ACTION__ != '/member/logout') {
            $member = M('member');
            $GLOBALS['i'] = $member->field('A.username, A.default_pic, A.default_pid, A.default_photonum, A.photonum, A.sex, A.is_videoauth, A.group_type, A.login_time, A.register_time, A.class, A.college  ,A.hometown_prov, A.hometown_city, A.birth_y, A.birth_m, A.birth_d, A.height, A.constellation, A.blood, A.stature, A.grade, A.introduce,  A.want_content, A.score_impress, A.score_impress_num, A.score_face, A.score_face_num, A.score_body, A.score_body_num, A.score_temper, A.score_temper_num, A.user_ping_offset, A.diary_name, A.card_num, A.profile_completed, B.*')->table('qh_member AS A')->join('qh_member_field AS B ON A.uid=B.uid')->where("A.uid=$uid")->find();
            /*echo $member->getLastSql();
			$member_field = M('member_field');			
			for($i=23; $i < 75;$i++) {
			$member_field->add(array('uid'=>$i));
			}*/
            if(!$GLOBALS['i']) {
				cookie('uid', NULL);
				$this->error('登录超时，请稍后重试');
				exit();
			}
			$GLOBALS['islogin'] = 1;
			$myuserinfo = array(
				'uid' => $GLOBALS['i']['uid'],
				'sex' => $GLOBALS['i']['sex'],
				'makefriend_do_things' => $GLOBALS['i']['want_content'],
				'is_videoauth' => $GLOBALS['i']['is_videoauth'],
				'profile_completed' => $GLOBALS['i']['profile_completed'],
				'profile_completed_show2' => ($GLOBALS['i']['profile_completed'] * 100) . '%',
				'profile_completed_show3' => "<div class=\"fillbar fl\" title=\"" . $GLOBALS['i']['profile_completed'] * 100 . "% completed\"><span style=\"width:" . $GLOBALS['i']['profile_completed'] * 50 . "px\"></span></div>",
				'card_num' => $GLOBALS['i']['card_num'],
				'group_type' => $GLOBALS['i']['group_type'],
				'qq_api' => empty($GLOBALS['i']['qquid']) ? 0 : 1,
			);
			$this->assign('myuserinfo', $myuserinfo);
        }

	    $uid = $GLOBALS['i']['uid'];
        $visit = M('visit');
        $visitlist1 = $visit->field("qh_member.uid, qh_member.username, qh_member.default_pic")->join("LEFT JOIN qh_member ON qh_visit.uid=qh_member.uid")->where("qh_visit.visit_uid=$uid")->order("qh_visit.add_time DESC")->limit(4)->select();
        //echo $visit->getLastSql();
        $this->assign('visitlist1', $visitlist1);
		$member_field=M('member_field');
		$member_fieldlist=$member_field->where("uid=$uid")->find();
		$this->assign('f', $member_fieldlist);
		//dump($member_fieldlist);
	}
 }
?>