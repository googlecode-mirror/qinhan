<?php
/*
* ��ȡtables ����һ��û���κ�Ȩ�޵�����˵��tablesҲ�ǲ��ɻ�õġ�
* @author			���ӿ� (Kevin)
* @E-mail			328725540@qq.com kevin@dev.ppstream.com
* @version			1.0
*/

class WebPlSqlTables extends Admin
{
	function exec()
	{
		$db_name = isset($this->rq_dt['db_name']) ? trim($this->rq_dt['db_name']) : '';
		if(empty($db_name)) {
			$this->json(JSON_FAILED,'����������,��ѡ��һ�����ݿ�');
		}

		$db = $this->initDb($db_name);
        $sql = "select lower(table_name) as table_name from user_tables order by table_name";
        $r = $db->getAll($sql);
        $this->json(JSON_SUCCESS,'',$r);
	}
	
	function post()
	{
		$this->exec();
	}
}