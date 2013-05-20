<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$site_id = filter('site_id', '/^[0-9]{1,9}$/', 'siteID格式错误');
	$type = filter('type', '/^constant|attack$/', 'type格式错误', true);
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');
	$page = filter('page', '/^[0-9]{1,9}$/', '页码格式错误');
	$limit = filter('limit', '/^[0-9]{1,9}$/', '偏移格式错误');

	// $site_id = 8;
	// $type = null;
	// $start_time = time() - 3600 * 24 *5;
	// $stop_time = time();
	// $page = 1;
	// $limit = 10;

	if($limit <= 0) $limit = 1;
	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;

	$siteModel = model('site');
	if($site_id == 0) $info = $siteModel->get($user_id, 'user_id');
	else $info = $siteModel->get($site_id);

	if(empty($info)) json(false, '站点不存在');
	if($info['remove'] > 0) json(false, '站点已经被移除');
	if($info['user_id'] != $user_id) json(false, '不允许操作他人站点');
	
	$alarmModel = model('alarm');
	if($type != null){
		$result = $alarmModel->alarmList($info['site_id'], $start_time, $stop_time, $start, $limit, $type);	
	}else{
		$result = $alarmModel->alarmList($info['site_id'], $start_time, $stop_time, $start, $limit);	
	}
	$result['page'] = $page;
	$result['limit'] = $limit; 

	json(true, $result);


?>