<?php
class MsgquickAction extends CommonAction
{
    public function index() {
		if(!checkpost()) exit('error');
		if(!$GLOBALS['islogin']) exit('{"stat":9, "errno":"未登录", "error":"未登录}');
		$receiver_uid = postvar('uid', 1);
		$msgModel = D('Msg');
		if(!$msgModel->check_link($receiver_uid)) {
			$msgModel->exitError();
		}
        $u = $msgModel->check_contact('uid', 'sex, uid');
		$msgModel->send_1($u['uid'], 0);
			
		echo '{"stat":9,"error":"打招呼成功","msg":"打招呼成功"}';
	}
}
?>