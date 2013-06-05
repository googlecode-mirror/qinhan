<?php
/*
* ��json��ʽ�г��������ݿ����֡�����ҳ��
* @author			���ӿ� (Kevin)
* @E-mail			328725540@qq.com kevin@dev.ppstream.com
* @version			1.0
*/

class WebPlSqlUpdate extends Admin
{
	function post()
	{
		$db_name = $this->rq_dt['db_name'];
		$data = $this->rq_dt['data'];
		$sql = $this->rq_dt['sql'];
		$user_id = $_SESSION['admin']['user_id'];
		$obj = json_decode($data,true);
		
		$flag_type = 'write_flag';

		//����sql
		$reg = '/^select\s+(.+?)\s+from\s+([a-z0-9_]+)\s+(where.*?\s+|)for\s+update$/i';
		$a = array();
		$r = preg_match_all($reg,$sql,$a);
		if($r) {
			$col = @$a[1][0];
			$table = @$a[2][0];
			$where = @$a[3][0];
		}

		//�����ֶ�
		$db = $this->initDb($db_name);
		$sqlc = "select column_name,data_type,data_length,data_precision,data_scale,nullable,column_id,
				default_length,data_default from user_tab_columns where table_name = upper('$table')";
		$cols = $db->getAll($sqlc);
		$str = '';
		$p = array();
		$oobj = $obj;
        $obj = utf8togbk($obj);
		$rowid = $obj['db_row_id'];
		unset($obj['db_row_id']);
		foreach($obj as $k=>$v) {
			foreach($cols as $k2=>$v2) {
				if(strtolower($v2['column_name']) == $k) {
					$str .= "$k = ";
					if($v2['data_type'] == 'DATE') {
						//2010-02-31
						if(strlen($v) == 10) {
							$str .= "to_date(?,'yyyy-mm-dd')";
						} else {
							$str .= "to_date(?,'yyyy-mm-dd hh24:mi:ss')";
						}
					} else {
						$str .= "?";
					}
					
					$str .= ',';
					$p[] = $v;
					break;
				}
			}
		}
		
		//������
		$str = substr($str,0,strlen($str) - 1);
		//����
		if(!$rowid || strlen($rowid) < 10) {
			$this->json(JSON_FAILED,'ִ��ʧ�ܣ�û��rowid');
		}
		//�γ�sql
		$sql = "update $table set $str where rowid = CHARTOROWID('$rowid')";
		
		//ִ��sql
		$r = $db->execute($sql,$p);
		if($r) {
			$this->json(JSON_SUCCESS,'ִ�гɹ�!',$oobj);
		} else {
			$this->json(JSON_FAILED,'ִ��ʧ�ܣ�'.$db_name.'<br/>'.print_r($db->errorInfo(),true),$oobj);
		}
		die;
	}
}