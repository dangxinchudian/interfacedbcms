<?php

class attack extends modelLog{

	public function daily($domain, $start_time, $stop_time){

		$schema = str_replace('.', '_', $domain).'_attack_log';
		$check = $this->checkSchema($schema);
		if(!$check) return false;


	}

	public function checkSchema($schema){
		$sql = "SELECT * FROM information_schema.TABLES WHERE table_schema='{$schema}'";
		$result = $this->db()->query($sql,'row');
		if(empty($result)) return false;
		return true;
	}
}


?>