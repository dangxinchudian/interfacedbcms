<?php

class attack extends model{

	public function summary($site_id, $start_time, $stop_time){
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$table = "mosite_{$site_id}.attack_log";
		$sql = "SELECT count(*),severity FROM {$table} WHERE time > '{$start_time}' AND time <= '{$stop_time}' GROUP BY severity";
		return $this->db()->query($sql, 'array');
	}


	public function ip($site_id, $start_time, $stop_time, $start, $limit){
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$table = "mosite_{$site_id}.attack_log";
		$sql = "SELECT count(*) AS count,client_ip,time FROM {$table} WHERE time > '{$start_time}' AND time <= '{$stop_time}' GROUP BY client_ip ORDER BY count DESC LIMIT {$start},{$limit}";
		$result['list'] = $this->db()->query($sql, 'array');
		$sql = "SELECT COUNT(1) FROM (SELECT client_ip FROM {$table} GROUP BY client_ip) AS g";
		$dbResult = $this->db()->query($sql, 'row');
		$result['total'] = $dbResult['COUNT(1)'];
		return $result;
	}

}


?>