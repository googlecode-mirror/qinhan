<?php
/*
* ��json��ʽ�г��������ݿ�����,����������һ��Ȩ�ޱ�־��1
* @author			���ӿ� (Kevin)
* @E-mail			328725540@qq.com kevin@dev.ppstream.com
* @version			1.0
*/

class WebPlSqlAuthDb extends Admin
{
	function exec() 
	{
//        echo "{'error':0,'success':true,'msg':'','message':'','data':[{'db_name':'pps239'},{'db_name':'pps_31'}]}";
        $dbs = new oracleDB_config();

        $db_names = array();
        foreach($dbs->dbconfig as $v){
            if(isset($v['db'])) continue;
            $db_names[] = array('db_name'=>$v['TNS']);
        }

        $this->json(JSON_SUCCESS,'',$db_names);
	}
	
	function post()
	{
		$this->exec();
	}
}