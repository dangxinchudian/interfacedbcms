<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$watch_id = filter('watch_id', '/^[0-9]{1,9}$/', 'watch_id格式错误', true);
	$server_id = filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误', true);
	$item_id = filter('item_id', '/^[0-9]{1,9}$/', 'item_id格式错误', true);

	// $server_id = 4;
	// $item_id = 1;
	// $watch_id = null;

	$serverModel = model('server');
	if($watch_id != null) $watch = $serverModel->selectWatch($watch_id);
	else $watch = $serverModel->selectWatch($server_id, $item_id);

	if(empty($watch)) json(false, '监控不存在');
	if($watch['remove'] > 0) json(false, '监控已经被移除');
	if($watch['user_id'] != $user_id) json(false, '不允许操作他人监控');

	$watch['item'] = $serverModel->item($watch['server_item_id']);
	$watch['device'] = array();
	if(!empty($watch['last_watch_data'])) $last_watch_data = jdecode($watch['last_watch_data']);
	unset($watch['last_watch_data']);

	if($watch['item']['server_hardware_id'] != 0){
		// $watch['device'] = $serverModel->getDevice($watch['item']['server_hardware_id'], 'hardware_id');
		$watch['device'] = $serverModel->getDevice($watch['server_id'], $watch['item']['server_hardware_id']);
		foreach ($watch['device'] as $key => $value) {
			$watch['device'][$key]['value'] = jdecode($value['value']);
			$watch['device'][$key]['last'] = $last_watch_data[$value['hash']];
		}
	}else{
		$watch['device'] = array();
		$watch['device'][0] = array(
			'server_device_id' => 0,
			'remove' => 0,
			'server_id' => $watch['server_id'],
			'user_id'  => $watch['user_id'],
			'server_hardware_id' => 0,
			'hash' => '',
			'value' => '',
			'name' => ''
		);
		if($last_watch_data) $watch['device'][0]['last'] = $last_watch_data;
	}

	// if(!empty($watch['last_watch_data'])) $watch['last_watch_data'] = jdecode($watch['last_watch_data']);

	// print_r($watch);
	json(true, $watch);

?>