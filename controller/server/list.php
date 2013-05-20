<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	// $page = filter('page', '/^[0-9]{1,9}$/', '页码格式错误');
	// $limit = filter('limit', '/^[0-9]{1,9}$/', '偏移格式错误');

	$page = 1;
	$limit = 10;

	if($limit <= 0) $limit = 1;
	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;

	$serverModel = model('server');
	$result = $serverModel->serverList($user_id, $start, $limit, 0);
	$count = $serverModel->serverCount($user_id, 0);

	// $list = $serverModel->item();
	foreach ($result as $key => $value) {
		$result[$key]['cpu'] = 0; 
		$result[$key]['disk'] = 1; 
		$result[$key]['memory'] = 2;
		$result[$key]['sys_descr'] = jdecode($value['sys_descr']);
		$result[$key]['sys_name'] = jdecode($value['sys_name']);
		$result[$key]['sys_uptime'] = jdecode($value['sys_uptime']);
		// foreach ($list as $subkey => $subvalue) {
		// 	$result =  $serverModel->selectWatch($value['server_id'], $subvalue['server_item_id']);
		// 	if(empty($result)) $list[$subkey]['server_watch_id'] = 0;
		// 	else $list[$subkey]['server_watch_id'] = $result['server_watch_id'];
		// }
	}

	$array = array(
		'page' => $page,
		'limit' => $limit,
		'list' => $result,
		'total' => $count 
	);

	json(true, $array);



?>