<?php
class HotAction extends CommonAction
{
    public function in() {
		if(!$GLOBALS['islogin']) {
			redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
		$member_field = M('member_field');
        $data1['new_receive_score'] = 0;
		$member_fieldlist=$member_field->where("uid={$GLOBALS['i']['uid']}")->find();
		$this->assign('f', $member_fieldlist);
		$member_field->where("uid={$GLOBALS['i']['uid']}")->save($data1);
        $visit = M('visit');
		$count = $visit->where("visit_uid={$GLOBALS['i']['uid']} AND score_impress>0")->count();
		//echo $visit->getLastSql();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $visitlist = $visit->field("A.uid, A.default_pic, A.sex, A.username, A.birth_y, A.hometown_prov, A.hometown_prov,A.height, A.college, A.want_content, A.photonum, qh_visit.score_impress, qh_visit.score_face, qh_visit.score_body, qh_visit.score_temper, qh_visit.add_time")->join("LEFT JOIN qh_member AS A ON qh_visit.uid=A.uid")->where("qh_visit.visit_uid={$GLOBALS['i']['uid']} AND qh_visit.score_impress>0")->order("qh_visit.add_time DESC")->limit($page->firstRow.','.$page->listRows)->select();

        $this->assign('visitlist', $visitlist);
		$this->assign('pages', $page->show());
        $this->display();
    }

    public function out() {
		if(!$GLOBALS['islogin']) {
			redirect($GLOBALS['s']['urlsite'] . '/member/login');
		}
        $visit = M('visit');
		$count = $visit->where("uid={$GLOBALS['i']['uid']} AND score_impress>0")->count();
		//echo $visit->getLastSql();
		import("@.ORG.Page");
        $page = new Page($count, 10);
        $visitlist = $visit->field("A.uid, A.default_pic, A.sex, A.username, A.birth_y, A.hometown_prov, A.hometown_prov, A.college, A.want_content, A.photonum, qh_visit.score_impress, qh_visit.score_face, qh_visit.score_body, qh_visit.score_temper, qh_visit.add_time")->join("LEFT JOIN qh_member AS A ON qh_visit.visit_uid=A.uid")->where("qh_visit.uid={$GLOBALS['i']['uid']} AND qh_visit.score_impress>0")->order("qh_visit.add_time DESC")->limit($page->firstRow.','.$page->listRows)->select();
        //echo $visit->getLastSql();
        $this->assign('visitlist', $visitlist);
		$this->assign('pages', $page->show());
        $this->display();
    }

    public function home() {
		if(!checkpost()) exit('error');
		if(!$GLOBALS['islogin']) exit('{"stat":9, "errno":"未登录", "error":"未登录}');
		$msgModel = D('Msg');	
        $type = postvar('type', array('score_impress', 'score_face', 'score_body', 'score_temper'));
        $u = $msgModel->check_contact('uid', "uid,sex,$type,{$type}_num");

        $visit = M('visit');
        $hot = $visit->where("uid={$GLOBALS['i']['uid']} AND visit_uid={$u['uid']}")->find();
        if(!$hot) exit('error');
        if($hot[$type] != 0) exit('hot yet');
        $data[$type] = floatval($_POST['score']);
        $data['add_time'] = time();
        $visit->where("id={$hot['id']}")->save($data);
		
		$member = M('member');
        $edit[$type] = ($data['score'] - $u[$type]) / ($person[$type . '_num'] + 1) + $person[$type];
        $edit[$type . '_num'] = array('exp', "{$type}_num+1");
        $member->where("uid=$uid")->save($edit);

		if($type == 'score_impress') {
			$member_field = M('member_field');
			$data1['new_receive_score'] = array('exp', 'new_receive_score+1');
			$data1['receive_score_num'] = array('exp', 'receive_score_num+1');
			$member_field->where("uid={$u['uid']}")->save($data1);
			//echo $member_field->getLastSql();
		}
				
        echo '{"stat":2,"score":"' . $u[$type] . '"}';
    }

    public function user_ping() {
		if(!checkpost()) exit('error');
		if(!$GLOBALS['islogin']) exit('{"stat":9, "errno":"未登录", "error":"未登录}');
		$msgModel = D('Msg');	
        $u = $msgModel->check_contact('uid', 'uid, username, sex, default_pic, score_impress, score_impress_num, hometown_prov, hometown_city');
		
        $ping_score = round(floatval($_POST['score']), 1);
		$ping_score = min(max(1, $ping_score), 10);
		$score = ($ping_score - $u['score_impress']) / ($u['score_impress_num'] + 1) + $u['score_impress'];
		$score = round($score, 1);
		
		//visit表中记录打分
		$visit = M('visit');
        $data['uid'] = $GLOBALS['i']['uid'];
        $data['visit_uid'] = $u['uid'];        
        $data['score_impress'] = $ping_score;
        $data['add_time'] = time();
		$hot = $visit->where("uid={$GLOBALS['i']['uid']} AND visit_uid={$u['uid']}")->find();
        if($hot) {
            $visit->where("id={$hot['id']}")->save($data);
        } else {
            $visit->add($data);
        }
		
		//member表和member_field记录变化
        $member = M('member');
        $edit['score_impress'] = $score;
        $edit['score_impress_num'] = array('exp', "score_impress_num+1");
        $member->where("uid={$u['uid']}")->save($edit);
		//echo $member->getLastSql();
		$member_field=M('member_field');
        $data1['new_receive_score'] = array('exp', 'new_receive_score+1');
		$data1['receive_score_num'] = array('exp', 'receive_score_num+1');
		$member_field->where("uid={$u['uid']}")->save($data1);

		//调整打分的指针
        $offset = $GLOBALS['i']['user_ping_offset'] + 1;
        $sex = 3 - $GLOBALS['i']['sex'];
        $count = $member->where("sex=$sex AND default_photonum>0 AND group_type>0")->count();
        $offset = $offset > $count - 1 ? 0 : $offset;
        $member->where("uid={$GLOBALS['i']['uid']}")->setField('user_ping_offset', $offset);

        $rs = $member->field('uid, sex, username, default_pic, photonum, hometown_prov, hometown_city, want_content')->where("sex=$sex AND default_photonum>0 AND group_type>0")->limit("$offset, 2")->select();
        $u1 = $rs[0];
        $offset = $offset + 11 > $count - 1 ? $offset - $count : $offset;
        $u2 = $member->field('uid, default_pic, username')->where("sex=$sex AND default_photonum>0 AND group_type>0")->limit(($offset + 11) . ",1")->select();
        $u2 = $u2[0];
		$u3 = $rs[1];
		
		$accurate = $is_play = '';
		if($ping_score > $score) {
			$accurate = '谢谢你啊！';
		} elseif($ping_score == $score) {
			$accurate = '太准了！';
			$is_play = 1;
		} elseif($ping_score > $score - 0.5) {
			$accurate = '不错哦！';
		} else {
			$accurate = '欠你钱啊！';
		}
		$do_things = do_things($u1['want_content'], $u1['sex']);
        $next_ping_user = array(
            "key" => "2Nsk3l",
            "uid" => $u1['uid'],
            "sex" => $u1['sex'] + 1,
            "photo_count" => $u1['photonum'],
            "face_url" => $GLOBALS['s']['urlupload'] . $u1['default_pic'] . '_240x240.jpg',
            "nickname" => $u1['username'],
            "age_show" => getAge($u1['birth_y']) . '岁，',
            "location_country" => '中国',
            "location_prov" => $u1['hometown_prov'],
            "location_city" => $u1['hometown_city'],
            "do_things" => $do_things,
        );
        $end_ping_user = array(
            "uid" => $u2['uid'],
            "face_url" => $GLOBALS['s']['urlupload'] . $u2['default_pic'] . '_72x72.jpg',
            "nickname" => $u2['username'],
        );
        $last_ping_user = array(
            "photo_count" => $u['photonum'],
            "ta" => ui_sex($u['sex']),
            "ping_score" => $ping_score,
            "score" => $score,
            "home_url" => $GLOBALS['s']['urldomain'] . "/{$u['uid']}",
            "face_url" => $GLOBALS['s']['urlupload'] . $u['default_pic'] . '_72x72.jpg',
            "nickname" => $u['username'],
            "age_show" => getAge($u['birth_y']) . '岁，',
            "location_country" => '中国',
            "location_prov" => $u['hometown_prov'],
            "location_city" => $u['hometown_city'],
            "ico_button" => "",
            "is_play" => $is_play,
            "accurate" => $accurate
        );
        $arr = array(
            "next_ping_user" => $next_ping_user,
            "end_ping_user" => $end_ping_user,
			"last_ping_user" => $last_ping_user,
            "preload_img_url" => $GLOBALS['s']['urlupload'] . $u3['default_pic'] . '_240x240.jpg'            
        );
        echo json_encode($arr);
    }

    public function photo_ping() {
		if(!checkpost()) exit('error');
		if(!$GLOBALS['islogin']) exit('{"stat":9, "errno":"未登录", "error":"未登录}');
		$msgModel = D('Msg');
        $u = $msgModel->check_contact('uid', 'uid');
		
        $photo = M('photo');
        $pid = postvar('photo_id', 1);
        $p = $photo->where("uid={$u['uid']} AND pid=$pid")->find();
		//echo $photo->getLastSql();
        if(!$p) exit('error');
        $data['pid'] = $pid;
        $data['photo_uid'] = $u['uid'];
        $data['uid'] = $GLOBALS['i']['uid'];
		$data['add_time'] = time();
        $photo_hot = M('photo_hot');
        $hot = $photo_hot->where($data)->find();
        if($hot) exit('hot yet');
        $score = floatval(postvar('score'));
        $data['score'] = $score;
        $photo_hot->add($data);
        $edit['score'] = ($data['score'] - $p['score']) / ($p['score_num'] + 1) + $p['score'];
        $edit['score_num'] = array('exp', 'score_num+1');
        $photo->where("uid={$u['uid']} AND pid=$pid}")->save($edit);
        echo '{"stat":1,"ping_score":"' . $p['score'] . '","errno":""}';
    }
}
?>