<?php

class attack extends model{

	public function summary($site_id, $start_time, $stop_time){
		$table = "mosite_{$site_id}.attack_log";
		$sql = "SELECT count(*),severity FROM {$table} GROUP BY severity";
		return $this->db()->query($sql, 'array');
	}

	public function checkSchema($schema){
		$sql = "SELECT * FROM information_schema.TABLES WHERE table_schema='{$schema}'";
		$result = $this->db()->query($sql,'row');
		if(empty($result)) return false;
		return true;
	}
}


?>