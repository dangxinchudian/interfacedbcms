<?php
/*new*/
class server extends model{

	public function add($ip, $user_id, $custom_name = '', $period = 60){
		$insertArray = array(
			'ip' => $ip, 
			'user_id' => $user_id,
			'creat_time' => time(),
			'custom_name' => $custom_name,
			'period' => $period,
			'snmp_token' => jencode('public')
		);
		$result = $this->db()->insert('server', $insertArray);
		if($result == 0) return false;
		$id = $this->db()->insertId();

		$sql = "CREATE DATABASE `moserver_{$id}` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$this->db()->query($sql, 'exec');
		
		return $id;
	}

	public function update($server_id, $updateArray){
		return $this->db()->update('server', $updateArray, "server_id = '{$server_id}'");
	}

	public function get($value, $type = 'server_id'){
		$whereArray = array(
			'server_id' => " server_id = '{$value}' ",
			'ip' => " ip = '{$value}' AND remove = 0 ",
			'user_id' => " user_id = '{$value}' AND remove = 0"
		);
		$sql = "SELECT * FROM server WHERE {$whereArray[$type]} ORDER BY creat_time ASC LIMIT 1";
		return $this->db()->query($sql, 'row');
	}

	public function remove($server_id, $destroy = false){
		if($destroy){
			$sql = "DROP DATABASE `moserver_{$server_id}`;";
			//$sql .= "DROP DATABASE `mosite_{$site_id}`;";
			$this->db()->query($sql, 'exec');
			$updateArray = array('remove' => 2);
			$result = $this->update($server_id, $updateArray);
			return true;
		}else{
			$updateArray = array('remove' => 1);
			$result = $this->update($server_id, $updateArray);
			if($result > 0) return true;
		}
		return false;
		//$this->db()->checkSchema($schema);
	}

	public function serverList($user_id, $start, $limit, $remove = 0){		//1:remove,0:normal,-1:all
		if($remove >= 0) $remove = ' AND remove = \'{$remove}\'';
		else $remove = '';
		$sql = "SELECT * FROM server WHERE user_id = '{$user_id}' {$remove} LIMIT {$start},{$limit}";
		return $this->db()->query($sql, 'array');
	}

	public function serverCount($user_id, $remove = 0){
		if($remove >= 0) $remove = ' AND remove = \'{$remove}\'';
		else $remove = '';
		$sql = "SELECT count(server_id) FROM server WHERE user_id = '{$user_id}' {$remove}";
		$result = $this->db()->query($sql, 'row');
		return $result['count(server_id)'];
	}

	public function setDevice($deviceArray){		//设备名数组-注册设备

	}

	public function item($item_id = false){
		if($item_id){
			$sql = "SELECT * FROM server_item WHERE server_item_id = '{$item_id}' AND remove = 0";
			return $this->db()->query($sql, 'row');
		}else{
			$sql = "SELECT * FROM server_item WHERE remove = 0";
			return $this->db()->query($sql, 'array');
		}
	}

	public function hardware($hardware_id){
		$sql = "SELECT * FROM server_hardware WHERE server_hardware_id = '{$hardware_id}' ";
		return $this->db()->query($sql, 'row');		
	}

	public function partitionSql(){		//生成分区表的sql语句
		$sql = 'PARTITION BY RANGE (TO_DAYS (time))(';
		$year = date('Y');
		$month = date('m');
		//生成之后6个月的分区
		$date = array();
		$sqlArray = array();
		for ($i = 0; $i < 6; $i++) { 
			if($month + $i > 12){
				$date[] = array(
					'year' => $year + 1,
					'month' => str_pad($month + $i - 12, 2 ,'0', STR_PAD_LEFT),
				);
			}else{
				$date[] = array(
					'year' => $year,
					'month' => str_pad($month + $i, 2 ,'0', STR_PAD_LEFT),
				);
			}
		}
		for ($i = 1; $i < 6; $i++) {
			$p = $i - 1;
			$sqlArray[] = "PARTITION p{$date[$p]['year']}{$date[$p]['month']} VALUES LESS THAN (TO_DAYS('{$date[$i]['year']}-{$date[$i]['month']}-01')) ENGINE = ARCHIVE";
		}
		$sql .= implode(',', $sqlArray);
		$sql .= ')';
		return $sql;
	}

	/*public function itemSql($item){
		$array = array(
			'cpu' => "CREATE TABLE IF NOT EXISTS `cpu_log` ( `id` char(36) NOT NULL, `used` tinyint(3) unsigned NOT NULL COMMENT '使用百分比', `device_id` int(10) unsigned NOT NULL COMMENT '设备ID', `time` datetime NOT NULL );",
			'memory' => "CREATE TABLE IF NOT EXISTS `memory_log` ( `id` char(36) NOT NULL, `used_amount` int(10) unsigned NOT NULL COMMENT '使用量', `total_amount` int(10) unsigned NOT NULL COMMENT '总量', `device_id` int(10) unsigned NOT NULL COMMENT '设备ID', `time` datetime NOT NULL );",
			'processcount' => "CREATE TABLE IF NOT EXISTS `processcount_log` ( `id` char(36) NOT NULL, `amount` int(10) unsigned NOT NULL COMMENT '数量', `time` datetime NOT NULL );",
			'disk' => "CREATE TABLE IF NOT EXISTS `disk_log` ( `id` char(36) NOT NULL, `used_amount` int(10) unsigned NOT NULL COMMENT '使用量', `total_amount` int(10) unsigned NOT NULL COMMENT '总量', `device_id` int(10) unsigned NOT NULL COMMENT '设备ID', `time` datetime NOT NULL );",
			'network' => "CREATE TABLE IF NOT EXISTS `network_log` ( `id` char(36) NOT NULL, `in` int(10) unsigned NOT NULL COMMENT '流入流量', `out` int(10) unsigned NOT NULL COMMENT '流出流量', `device_id` int(10) unsigned NOT NULL COMMENT '设备ID', `time` datetime NOT NULL );",
		);
		if(!isset($array[$item])) return false;
		return $array[$item];
	}*/


}
?>