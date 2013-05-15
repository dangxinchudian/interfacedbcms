<?php

/*new*/

class aws extends model{

	public function general($month, $site_id){
		$table = $this->checkTable("molog_{$site_id}", 'general');
		if(!$table) return array();
		$sql = "SELECT * FROM molog_{$site_id}.general WHERE general.year_month = '{$month}'";
		return $this->db()->query($sql, 'row');
	}

	public function pages($site_id, $start, $limit){
		$table = $this->checkTable("molog_{$site_id}", 'pages');
		if(!$table) return array();
		$sql = "SELECT * FROM molog_{$site_id}.pages ORDER BY pages DESC LIMIT {$start},{$limit}";
		$result['list'] = $this->db()->query($sql, 'array');
		$sql = "SELECT count(id) FROM molog_{$site_id}.pages ";
		$dbResult = $this->db()->query($sql, 'row');
		$result['total'] = $dbResult['count(id)'];
		return $result;
	}
	
	public function checkTable($database, $table){
		$sql = "SELECT TABLE_NAME from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA='{$database}' and TABLE_NAME='{$table}'";
		$result = $this->db()->query($sql, 'row');
		if(empty($result)) return false;
		return true;
	}
}

?>