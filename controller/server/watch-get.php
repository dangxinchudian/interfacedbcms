<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$watch_id = filter('watch_id', '/^[0-9]{1,9}$/', 'watch_id格式错误');
	// $watch_id = 2;

	$serverModel = model('server');
	$watch = $serverModel->selectWatch($watch_id);

	if(empty($watch)) json(false, '监控不存在');
	if($watch['remove'] > 0) json(false, '监控已经被移除');
	if($watch['user_id'] != $user_id) json(false, '不允许操作他人监控');

	$watch['item'] = $serverModel->item($watch['server_item_id']);
	$watch['device'] = array();
	if($watch['item']['server_hardware_id'] != 0){
		$watch['device'] = $serverModel->getDevice($watch['item']['server_hardware_id'], 'hardware_id');
		foreach ($watch['device'] as $key => $value) {
			$watch['device'][$key]['value'] = jdecode($value['value']);
		}
	}

	json(true, $watch);

?>