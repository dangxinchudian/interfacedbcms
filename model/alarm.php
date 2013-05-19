<?php
/*new*/
class alarm extends model{

	public function addRule($user_id, $site_id, $type, $max_limit = 0, $min_limit = 0, $keep_time = 300, $cool_down_time = 600, $notice_limit = 3){

		$insertArray = array(
			'user_id' => $user_id, 
			'site_id' => $site_id, 
			'type' => $type,
			'max_limit' => $max_limit,
			'min_limit' => $min_limit,
			'keep_time' => $keep_time,
			'cool_down_time' => $cool_down_time,
			'notice_limit' => $notice_limit
		);
		$result = $this->db()->insert('alarm_rule', $insertArray);
		if($result == 0) return false;
		return $this->db()->insertId();
	}

	public function selectRule($site_id, $type  = false){
		if($type === false){
			$sql = "SELECT * FROM alarm_rule WHERE site_id = '{$site_id}' AND remove = '0'";
			return $this->db()->query($sql, 'array');
		}else{
			$sql = "SELECT * FROM alarm_rule WHERE site_id = '{$site_id}' AND type = '{$type}' AND remove = '0'";
			return $this->db()->query($sql, 'row');
		}
	}

	public function getRule($rule_id){
		$sql = "SELECT * FROM alarm_rule WHERE alarm_rule_id = '{$rule_id}'";
		return $this->db()->query($sql, 'row');		
	}

	public function updateRule($rule_id, $updateArray){
		return $this->db()->update('alarm_rule', $updateArray, "alarm_rule_id = '{$rule_id}'");
	}


}
?>