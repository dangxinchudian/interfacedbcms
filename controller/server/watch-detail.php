<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	// $watch_id = filter('watch_id', '/^[0-9]{1,9}$/', 'watch_id格式错误');
	// $time_unit = filter('time_unit', '/^day|month|year$/', '时间单位错误');
	// $start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	// $stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');

	$watch_id = 1;
	$time_unit = 'day';
	$start_time = time() - 3600*24*5;
	$stop_time = time();

	$serverModel = model('server');
	$watch = $serverModel->selectWatch($watch_id);

	if(empty($watch)) json(false, '监控不存在');
	if($watch['remove'] > 0) json(false, '监控已经被移除');
	if($watch['user_id'] != $user_id) json(false, '不允许操作他人监控');

	$item = $serverModel->item($watch['server_item_id']);

	$result = $serverModel->log_data($watch['server_id'], $item['table_name'], $time_unit, $start_time, $stop_time);

	$data = array();
	//date&data complete
	if($time_unit == 'day'){
		for($i = 0 ; $i<= ($stop_time - $start_time) / (3600*24); $i++){
			$max[date('Y-m-d', $start_time + 3600 * 24 * $i)] = 0;
			$min[date('Y-m-d', $start_time + 3600 * 24 * $i)] = 0;
			$avg[date('Y-m-d', $start_time + 3600 * 24 * $i)] = 0;
		}
	}elseif($time_unit == 'month'){
		for($i = 0 ; $i<= ($stop_time - $start_time) / (3600*24*30); $i++){
			$max[date('Y-m', $start_time + 3600 * 24 * $i * 30)] = 0;
			$min[date('Y-m', $start_time + 3600 * 24 * $i * 30)] = 0;
			$avg[date('Y-m', $start_time + 3600 * 24 * $i * 30)] = 0;		
		}
	}elseif($time_unit == 'year'){
		for($i = 0 ; $i<= ($stop_time - $start_time) / (3600*24*365); $i++){
			$max[date('Y', $start_time + 3600 * 24 * $i * 365)] = 0;
			$min[date('Y', $start_time + 3600 * 24 * $i * 365)] = 0;
			$avg[date('Y', $start_time + 3600 * 24 * $i * 365)] = 0;
		}			
	}

	foreach ($result as $key => $value){
		$max[$value['group_time']] = $value['max'];
		$min[$value['group_time']] = $value['min'];
		$avg[$value['group_time']] = $value['avg'];
	}

	$return = array(
		'data' => array_values($max),
		'max' => array_keys($max),
		'min' => array_keys($min),
		'avg' => array_keys($avg)
	);
	json(true, $result);

?>