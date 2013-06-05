<?php
/*
* 获取tables 对于一个没有任何权限的人来说，tables也是不可获得的。
* @author			王钟凯 (Kevin)
* @E-mail			328725540@qq.com kevin@dev.ppstream.com
* @version			1.0
*/

class WebPlSqlTables extends Admin
{
	function exec()
	{
		$db_name = isset($this->rq_dt['db_name']) ? trim($this->rq_dt['db_name']) : '';
		if(empty($db_name)) {
			$this->json(JSON_FAILED,'参数不完整,请选择一个数据库');
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