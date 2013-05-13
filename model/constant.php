<?php

/*new*/

class constant extends model{

	public function log_work_time($site_id, $start_time, $stop_time, $period, $node = 0){		//work_time
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$node = " AND constant_node_id = '{$node}' ";
		$sql = "SELECT count(id) FROM mosite_{$site_id}.constant_log WHERE time >= '{$start_time}' AND time <= '{$stop_time}' {$node}";
		$result = $this->db()->query($sql, 'row');
		return $result['count(id)'] * $period;
	}

	public function log_fault_time($site_id, $start_time, $stop_time, $period, $node = 0){		//可用time
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$node = " AND constant_node_id = '{$node}' ";
		$sql = "SELECT count(id) FROM mosite_{$site_id}.constant_log WHERE time >= '{$start_time}' AND time <= '{$stop_time}' AND status != '200' {$node}";
		$result = $this->db()->query($sql, 'row');
		return $result['count(id)'] * $period;	
	}

	public function available($site_id, $start_time, $stop_time, $node = -1){
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		if($node >= 0) $node = " AND constant_node_id = '{$node}' ";
		else $node = '';
		$sql = "SELECT count(id) FROM mosite_{$site_id}.constant_log WHERE time >= '{$start_time}' AND time <= '{$stop_time}' AND status = '200' {$node}";
		$result = $this->db()->query($sql, 'row');
		$avail_count = $result['count(id)'];

		$sql = "SELECT count(id) FROM mosite_{$site_id}.constant_log WHERE time >= '{$start_time}' AND time <= '{$stop_time}' {$node}";
		$result = $this->db()->query($sql, 'row');
		$total_count = $result['count(id)'];
		return round($avail_count / $total_count * 100, 2);
	}

	public function get_last($site_id, $node = 0){
		$node = " constant_node_id = '{$node}' ";
		$sql = "SELECT * FROM mosite_{$site_id}.constant_log WHERE {$node} ORDER BY time DESC LIMIT 1";
		return $this->db()->query($sql, 'row');
	}

	public function node(){
		$sql = "SELECT constant_node_id,name FROM constant_node";
		return $this->db()->query($sql, 'array');
	}


}



?>