<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$page = filter('page', '/^[0-9]{1,9}$/', '页码格式错误');
	$limit = filter('limit', '/^[0-9]{1,9}$/', '偏移格式错误');

	// $page = 1;
	// $limit = 10;

	if($limit <= 0) $limit = 1;
	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;

	$serverModel = model('server');
	$result = $serverModel->serverList($user_id, $start, $limit, 0);
	$count = $serverModel->serverCount($user_id, 0);

	$list = $serverModel->item();
	$item = array();
	foreach ($list as $key => $value) $item[$value['server_item_id']] = $value;

	foreach ($result as $key => $value) {

		$result[$key]['sys_descr'] = jdecode($value['sys_descr']);
		$result[$key]['sys_name'] = jdecode($value['sys_name']);
		$result[$key]['sys_uptime'] = jdecode($value['sys_uptime']);

		//cpu
		$cpu = $serverModel->getDevice($value['server_id'], 1);
		$device_id = array();
		foreach ($cpu as $subvalue) $device_id[] = $subvalue['server_device_id'];
		$last = $serverModel->lastWatch($value['server_id'], $item[3]['table_name'], $device_id);
		if(empty($last)) $result[$key]['cpu'] = -1;
		else{
			$cpu = 0;
			foreach ($last as $subvalue) $cpu += (int)$subvalue['used'];
			$result[$key]['cpu'] = $cpu / count($last);
		}

		// network
		$network = $serverModel->getDevice($value['server_id'], 2);
		$device_id = array();
		foreach ($network as $subvalue) $device_id[] = $subvalue['server_device_id'];
		$last = $serverModel->lastWatch($value['server_id'], $item[2]['table_name'], $device_id);
		// print_r($last);
		if(empty($last)){
			$result[$key]['in_speed'] = -1;
			$result[$key]['out_speed'] = -1;
		}else{
			$result[$key]['in_speed'] = 0;
			$result[$key]['out_speed'] = 0;
			foreach ($last as $subvalue){
				if($subvalue['in_speed'] > $result[$key]['in_speed']) $result[$key]['in_speed'] = $subvalue['in_speed'];
				if($subvalue['out_speed'] > $result[$key]['out_speed']) $result[$key]['out_speed'] = $subvalue['out_speed'];
			}
		}

		//memory
		$last = $serverModel->lastWatch($value['server_id'], $item[5]['table_name']);
		if(empty($last)) $result[$key]['memory'] = -1;
		else{
			if($last['total_amount'] == 0) $result[$key]['memory'] = 0;
			else $result[$key]['memory'] = round($last['used_amount'] / $last['total_amount'], 4) * 100;
		}

		//disk
		$disk = $serverModel->getDevice($value['server_id'], 3);
		$device_id = array();
		foreach ($disk as $subvalue) $device_id[] = $subvalue['server_device_id'];
		$last = $serverModel->lastWatch($value['server_id'], $item[1]['table_name'], $device_id);
		if(empty($last)) $result[$key]['disk'] = -1;
		else{
			$disk = 0; 
			foreach ($last as $subvalue){
				if($subvalue['total_amount'] != 0) $disk += $subvalue['used_amount'] / $subvalue['total_amount'];
			}
			$result[$key]['disk'] = round( $disk / count($last), 4) * 100;
		}
	}

	$array = array(
		'page' => $page,
		'limit' => $limit,
		'list' => $result,
		'total' => $count 
	);

	// print_r($array);
	json(true, $array);



?>