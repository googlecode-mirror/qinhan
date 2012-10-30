<?php
class ProfileAction extends CommonAction
{
    public function index() {
        if(!$GLOBALS['islogin']) {
            redirect($GLOBALS['s']['urlsite'] . '/member/login');
        }
        $m = $GLOBALS['i'];
        $uid = $m['uid'];
		$college = M("college");
		$list = $college->select();
		//echo $xueyuan->getLastSql();
		$this->assign("list",$list);
        $member = M('member');
        if(checkpost()) {
			
            $data['college'] = postvar('college');
            $data['hometown_prov'] = postvar('hometown_prov');
            $data['hometown_city'] = postvar('hometown_city');
            $data['birth_y'] = postvar('birth_y', 1);
            $data['birth_m'] = postvar('birth_m', 1);
            $data['birth_d'] = postvar('birth_d', 1);
            $data['height'] = postvar('height', 1);
            $data['constellation'] = postvar('constellation');
			$data['blood'] = postvar('blood');
			$data['stature'] = postvar('stature');
			$data['grade'] = postvar('grade');
			$data['makefriend'] = postvar('makefriend');
			
			$count = 0;
			if(!empty($data['college'])) $count ++;
			if(!empty($data['hometown_prov'])) $count ++;
			if(!empty($data['hometown_city'])) $count ++;
			if(!empty($data['birth_y'])) $count ++;
			if(!empty($data['birth_m'])) $count ++;
			if(!empty($data['birth_d'])) $count ++;
			if(!empty($data['height'])) $count ++;
			if(!empty($data['constellation'])) $count ++;
			if(!empty($data['blood'])) $count ++;
			if(!empty($data['stature'])) $count ++;
			if(!empty($data['grade'])) $count ++;
			if(!empty($data['makefriend'])) $count ++;
			$data['profile_completed'] = round(floatval($count / 12), 1);
			
            $member->where("uid=$uid")->data($data)->save();
            //echo $member->getLastSql();
            echo '{"errno":200,"msg":"修改成功！"}';
            exit();
        }
        $this->assign('m', $m);
        $this->display();
    }
}
?>