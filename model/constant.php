<?php

/*new*/

class constant extends model{

	public function work_time($site_id, $start_time, $stop_time, $period){		//总time
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$sql = "SELECT count(id) FROM mosite_{$site_id}.constant_log WHERE time >= '{$start_time}' AND time <= '{$stop_time}'";
		$result = $this->db()->query($sql, 'row');
		return $result['count(id)'] * $period;
	}


	public function avail_time($site_id, $start_time, $stop_time, $period){		//可用time
		$start_time = date('Y-m-d H:i:s', $start_time);
		$stop_time = date('Y-m-d H:i:s', $stop_time);
		$sql = "SELECT count(id) FROM mosite_{$site_id}.constant_log WHERE time >= '{$start_time}' AND time <= '{$stop_time}' AND status = '200'";
		$result = $this->db()->query($sql, 'row');
		return $result['count(id)'] * $period;	
	}


}



?>