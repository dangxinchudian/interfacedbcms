<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$site_id = filter('site_id', '/^[0-9]{1,9}$/', 'siteID格式错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误', true);
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误', true);

	// $site_id = 0;
	// $start_time = time() - 60 * 60 * 24 * 5;
	// $stop_time = time();
	
	$siteModel = model('site');
	if($site_id == 0) $info = $siteModel->get($user_id, 'user_id');
	else $info = $siteModel->get($site_id);

	if(empty($info)) json(false, '站点不存在');
	if($info['remove'] > 0) json(false, '站点已经被移除');
	if($info['user_id'] != $user_id) json(false, '不允许操作他人站点');

	$attackModel = model('attack');
	$result = $attackModel->summary($info['site_id'], $start_time, $stop_time);

	$attack = array(
		'EMERGENCY' => 0,
		'ALERT' => 0,
		'CRITICAL' => 0,
		'ERROR' => 0,
		'WARNING' => 0,
		'NOTICE' => 0,
		'INFO' => 0,
		'DEBUG' => 0
	);

	foreach ($result as $key => $value) {
		if(isset($attack[$value['severity']])) $attack[$value['severity']] = (int)$value['count(*)'];
	}

	$viewdata = array(
		'high' => $attack['EMERGENCY'] + $attack['ALERT'] + $attack['CRITICAL'],
		'medium' => $attack['ERROR'] + $attack['WARNING'],
		'low' => $attack['INFO'] + $attack['DEBUG'] + $attack['NOTICE']
	);

	$return = array(
		'source' => $attack,
		'view' => $viewdata,
		'site_id' => $info['site_id'],
	);
	json(true, $return);


?>