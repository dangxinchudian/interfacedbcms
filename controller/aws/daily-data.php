<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$site_id = filter('site_id', '/^[0-9]{1,9}$/', 'siteID格式错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');

	// $site_id = 0;
	// $start_time = time() - 60 * 60 * 24 * 5 -3000;
	// $stop_time = time();

	$siteModel = model('site');
	if($site_id == 0) $info = $siteModel->get($user_id, 'user_id');
	else $info = $siteModel->get($site_id);

	if(empty($info)) json(false, '站点不存在');
	if($info['remove'] > 0) json(false, '站点已经被移除');
	if($info['user_id'] != $user_id) json(false, '不允许操作他人站点');

	$awsModel = model('aws');
	$info = $awsModel->daily($info['site_id'], $start_time, $stop_time);

	$http = array();
	$attack = array();
	for($i = 0 ; $i <= ($stop_time - $start_time) / (3600*24); $i++){
		$http[date('Ymd', $start_time + 3600 * 24 * $i)] = 0;
		$attack[date('Ymd', $start_time + 3600 * 24 * $i)] = 0;
	}

	foreach ($info as $key => $value) {
		$http[$value['day']] = (int)$value['hits'];
	}
	$result = array(
		0 => array_keys($http),
		1 => array_values($http),
		2 => array_values($attack)
	);

	json(true, $result);


?>