<?php

class attack extends model{

	public function severity($site_id, $start_time, $stop_time){
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$table = "mosite_{$site_id}.attack_log";
		$sql = "SELECT count(*),severity FROM {$table} WHERE time > '{$start_time}' AND time <= '{$stop_time}' GROUP BY severity";
		return $this->db()->query($sql, 'array');
	}

	public function ip_count($site_id, $start_time, $stop_time){
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$table = "mosite_{$site_id}.attack_log";
		$sql = "SELECT COUNT(DISTINCT client_ip) as count FROM {$table} WHERE time > '{$start_time}' AND time <= '{$stop_time}' ";
		$dbResult = $this->db()->query($sql, 'row');
		$result = $dbResult['count'];
		return $result;
	}

	public function total_count($site_id, $start_time, $stop_time){
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$table = "mosite_{$site_id}.attack_log";
		$sql = "SELECT COUNT(client_ip) as count FROM {$table} WHERE time > '{$start_time}' AND time <= '{$stop_time}' ";
		$dbResult = $this->db()->query($sql, 'row');
		$result = $dbResult['count'];
		return $result;
	}


	public function ip($site_id, $start_time, $stop_time, $start, $limit){
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$table = "mosite_{$site_id}.attack_log";
		$sql = "SELECT count(*) AS count,client_ip,time FROM {$table} WHERE time > '{$start_time}' AND time <= '{$stop_time}' GROUP BY client_ip ORDER BY count DESC LIMIT {$start},{$limit}";
		$result['list'] = $this->db()->query($sql, 'array');
		//$sql = "SELECT COUNT(1) FROM (SELECT client_ip FROM {$table} GROUP BY client_ip) AS g";
		$sql = "SELECT COUNT(DISTINCT client_ip) as count FROM {$table} WHERE time > '{$start_time}' AND time <= '{$stop_time}' ";
		$dbResult = $this->db()->query($sql, 'row');
		$result['total'] = $dbResult['count'];
		return $result;
	}

	public function locationZh($site_id, $start_time, $stop_time){
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$table = "mosite_{$site_id}.attack_log";
		$sql = "SELECT count(*) AS count,zh_region FROM {$table} WHERE time > '{$start_time}' AND time <= '{$stop_time}' GROUP BY zh_region";
		return $this->db()->query($sql, 'array');		
	}

	public function mode($site_id, $start_time, $stop_time){
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$table = "mosite_{$site_id}.attack_log";
		$sql = "SELECT count(*) AS count ,attack_type FROM {$table} WHERE time > '{$start_time}' AND time <= '{$stop_time}' GROUP BY attack_type ORDER BY count DESC";
		return $this->db()->query($sql, 'array');
	}

	public function detail(){

	}

}


?>