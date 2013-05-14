<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$page = filter('page', '/^[0-9]{1,9}$/', '页码格式错误');
	$limit = filter('limit', '/^[0-9]{1,9}$/', '偏移格式错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');

	/*$page = 1;
	$limit = 10;
	$start_time = time() - 60 * 60 * 24 * 5;
	$stop_time = time();*/

	if($limit <= 0) $limit = 1;
	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;

	$siteModel = model('site');
	$constantModel = model('constant');
	$result = $siteModel->siteList($user_id, $start, $limit, 0);
	$count = $siteModel->siteCount($user_id, 0);
	foreach ($result as $key => $value) {
		//$result[$key]['work_time'] = $constantModel->work_time($value['site_id'], $start_time, $stop_time, $value['period'], 0);
		$result[$key]['fault_time'] = $constantModel->log_fault_time($value['site_id'], $start_time, $stop_time, $value['period'], 0);		//临时替代
		$result[$key]['available'] = $constantModel->available($value['site_id'], $start_time, $stop_time);

	}
	$array = array(
		'page' => $page,
		'limit' => $limit,
		'list' => $result,
		'total' => $count 
	);

	json(true, $array);


?>