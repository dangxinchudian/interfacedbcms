<?php
/*new*/

class site extends model{

	public function add($domain, $user_id,  $custom_name = '', $port = 80, $path = '/'){
		$insertArray = array(
			'domain' => $domain, 
			'user_id' => $user_id,
			'creat_time' => time(),
			'custom_name' => $custom_name,
			'port' => $port,
			'path' => $path

		);
		$result = $this->db()->insert('site', $insertArray);
		if($result == 0) return false;
		$id = $this->db()->insertId();
		//add the database
		$sql = '';
		$sql .= "CREATE DATABASE `molog_{$id}` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$sql .= "CREATE DATABASE `mosite_{$id}` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$sql .= "USE `mosite_{$id}`;";

		$sql .= "CREATE TABLE IF NOT EXISTS `attack_log_new` (
				`id` char(36) NOT NULL,
				`ip` int(10) unsigned NOT NULL COMMENT '对方ip',
				`port` int(10) unsigned NOT NULL DEFAULT '80' COMMENT '本机port',
				`severity` varchar(30) NOT NULL,
				`url` varchar(500) NOT NULL,
				`protocol` enum('1.1','1.0') NOT NULL DEFAULT '1.1',
				`browser_id` int(10) unsigned NOT NULL DEFAULT '1',
				`brower_version` decimal(9,5) NOT NULL DEFAULT '0.00000',
				`user_agent` varchar(500) NOT NULL,
				`status` int(10) unsigned NOT NULL DEFAULT '200' COMMENT 'http code',
				`referer` varchar(255) NOT NULL COMMENT '来源',
				`referer_domain` varchar(255) NOT NULL COMMENT '来源域名',
				`file_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '文件类型',
				`size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '包大小',
				`method` enum('GET','POST','PUT','DELETE') NOT NULL DEFAULT 'GET',
				`payload` varchar(255) NOT NULL,
				`attack_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '攻击类型',
				`robot_id` int(10) unsigned NOT NULL DEFAULT '1',
				`os_id` int(10) unsigned NOT NULL DEFAULT '1',
				`time` datetime NOT NULL,
				`country_id` int(10) unsigned NOT NULL DEFAULT '1',
				`region_id` int(10) unsigned NOT NULL DEFAULT '1',
				`city_id` int(10) unsigned NOT NULL DEFAULT '1'
				) ENGINE=ARCHIVE DEFAULT CHARSET=utf8
				PARTITION BY RANGE (TO_DAYS (time))
				(PARTITION p201305 VALUES LESS THAN (735354) ENGINE = ARCHIVE,
				 PARTITION p201306 VALUES LESS THAN (735385) ENGINE = ARCHIVE,
				 PARTITION p201307 VALUES LESS THAN (735415) ENGINE = ARCHIVE,
				 PARTITION p201308 VALUES LESS THAN (735446) ENGINE = ARCHIVE,
				 PARTITION p201309 VALUES LESS THAN (735477) ENGINE = ARCHIVE,
				 PARTITION p201310 VALUES LESS THAN (735507) ENGINE = ARCHIVE);
				";

		$sql .= "CREATE TABLE IF NOT EXISTS `constant_log` (
					`id` char(36) NOT NULL,
					`starttransfer_time` decimal(9,6) NOT NULL DEFAULT '0.000000',
					`pretransfer_time` decimal(9,6) NOT NULL DEFAULT '0.000000',
					`total_time` decimal(9,6) NOT NULL DEFAULT '0.000000',
					`namelookup_time` decimal(9,6) NOT NULL DEFAULT '0.000000',
					`connect_time` decimal(9,6) NOT NULL,
					`redirect_time` decimal(9,6) NOT NULL DEFAULT '0.000000',
					`status` smallint(5) unsigned NOT NULL COMMENT '状态值',
					`constant_node_id` int(10) unsigned NOT NULL COMMENT '监测节点/如果为0的话是本机',
					`time` datetime NOT NULL
				) ENGINE=ARCHIVE DEFAULT CHARSET=utf8 COMMENT='中断服务日志'
				PARTITION BY RANGE (TO_DAYS (time))
				(PARTITION p201305 VALUES LESS THAN (735354) ENGINE = ARCHIVE,
				 PARTITION p201306 VALUES LESS THAN (735385) ENGINE = ARCHIVE,
				 PARTITION p201307 VALUES LESS THAN (735415) ENGINE = ARCHIVE,
				 PARTITION p201308 VALUES LESS THAN (735446) ENGINE = ARCHIVE,
				 PARTITION p201309 VALUES LESS THAN (735477) ENGINE = ARCHIVE,
				 PARTITION p201310 VALUES LESS THAN (735507) ENGINE = ARCHIVE);
				";

		$sql .= "CREATE TABLE IF NOT EXISTS `http_log` (
					`id` char(36) NOT NULL,
					`ip` int(10) unsigned NOT NULL COMMENT '对方ip',
					`port` int(10) unsigned NOT NULL DEFAULT '80' COMMENT '本机port',
					`url` varchar(500) NOT NULL,
					`protocol` enum('1.1','1.0') NOT NULL DEFAULT '1.1',
					`browser_id` int(10) unsigned NOT NULL DEFAULT '1',
					`brower_version` decimal(9,5) NOT NULL DEFAULT '0.00000',
					`user_agent` varchar(500) NOT NULL,
					`status` int(10) unsigned NOT NULL DEFAULT '200' COMMENT 'http code',
					`referer` varchar(255) NOT NULL COMMENT '来源',
					`referer_domain` varchar(255) NOT NULL COMMENT '来源域名',
					`file_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '文件类型',
					`size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '包大小',
					`method` enum('GET','POST','PUT','DELETE') NOT NULL DEFAULT 'GET',
					`robot_id` int(10) unsigned NOT NULL DEFAULT '1',
					`os_id` int(10) unsigned NOT NULL DEFAULT '1',
					`time` datetime NOT NULL,
					`country_id` int(10) unsigned NOT NULL DEFAULT '1',
					`region_id` int(10) unsigned NOT NULL DEFAULT '1',
					`city_id` int(10) unsigned NOT NULL DEFAULT '1'
				) ENGINE=ARCHIVE DEFAULT CHARSET=utf8 COMMENT='http服务日志'
				PARTITION BY RANGE (TO_DAYS (time))
				(PARTITION p201305 VALUES LESS THAN (735354) ENGINE = ARCHIVE,
				 PARTITION p201306 VALUES LESS THAN (735385) ENGINE = ARCHIVE,
				 PARTITION p201307 VALUES LESS THAN (735415) ENGINE = ARCHIVE,
				 PARTITION p201308 VALUES LESS THAN (735446) ENGINE = ARCHIVE,
				 PARTITION p201309 VALUES LESS THAN (735477) ENGINE = ARCHIVE,
				 PARTITION p201310 VALUES LESS THAN (735507) ENGINE = ARCHIVE);
				";

		$sql .= "CREATE TABLE IF NOT EXISTS `attack_log` (
					`client_ip` varchar(30) NOT NULL,
					`client_port` int(10) unsigned NOT NULL,
					`server_ip` varchar(30) NOT NULL,
					`server_port` int(10) unsigned NOT NULL,
					`attack_type` varchar(255) NOT NULL,
					`severity` varchar(30) NOT NULL,
					`status` int(10) unsigned NOT NULL,
					`action` varchar(500) NOT NULL,
					`payload` varchar(500) NOT NULL,
					`protocol` decimal(9,2) NOT NULL,
					`referer` varchar(500) NOT NULL,
					`url` varchar(255) NOT NULL,
					`method` enum('GET','POST','PUT','DELETE') NOT NULL DEFAULT 'GET',
					`user_agent` varchar(255) NOT NULL,
					`post_body` varchar(500) NOT NULL,
					`insert_time` int(10) unsigned NOT NULL,
					`time` datetime NOT NULL,
					`geo_country` varchar(255) NOT NULL,
					`geo_region` varchar(255) NOT NULL,
					`geo_city` varchar(255) NOT NULL,
					`zh_country` varchar(255) NOT NULL,
					`zh_region` varchar(255) NOT NULL,
					`zh_city` varchar(255) NOT NULL,
					`zh_net` varchar(255) NOT NULL
				) ENGINE=ARCHIVE DEFAULT CHARSET=utf8 COMMENT='sh_attack服务日志'
				PARTITION BY RANGE (TO_DAYS (time))
				(PARTITION p201305 VALUES LESS THAN (735354) ENGINE = ARCHIVE,
				 PARTITION p201306 VALUES LESS THAN (735385) ENGINE = ARCHIVE,
				 PARTITION p201307 VALUES LESS THAN (735415) ENGINE = ARCHIVE,
				 PARTITION p201308 VALUES LESS THAN (735446) ENGINE = ARCHIVE,
				 PARTITION p201309 VALUES LESS THAN (735477) ENGINE = ARCHIVE,
				 PARTITION p201310 VALUES LESS THAN (735507) ENGINE = ARCHIVE);
				";

		//echo $sql;

		$this->db()->query($sql, 'exec');
		return $id;
	}

	public function get($value, $type = 'site_id'){
		$whereArray = array(
			'site_id' => " site_id = '{$value}' ",
			'domain' => " domain = '{$value}' "
		);
		$sql = "SELECT * FROM site WHERE {$whereArray[$type]}";
		return $this->db()->query($sql, 'row');
	}

	public function update($site_id, $updateArray){
		return $this->db()->update('site', $updateArray, "site_id = '{$site_id}'");
	}

	public function siteList($user_id, $start, $limit, $remove = 0){		//1:remove,0:normal,-1:all
		if($remove == 0) $remove = ' AND remove = \'0\'';
		elseif($remove == 1) $remove = ' AND remove = \'1\'';
		else $remove = '';
		$sql = "SELECT * FROM site WHERE user_id = '{$user_id}' {$remove} LIMIT {$start},{$limit}";
		return $this->db()->query($sql, 'array');
	}

	public function siteCount($user_id, $remove = 0){
		if($remove == 0) $remove = ' AND remove = \'0\'';
		elseif($remove == 1) $remove = ' AND remove = \'1\'';
		else $remove = '';
		$sql = "SELECT count(site_id) FROM site WHERE user_id = '{$user_id}' {$remove}";
		$result = $this->db()->query($sql, 'row');
		return $result['count(site_id)'];
	}



}
?>