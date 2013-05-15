<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$page = filter('page', '/^[0-9]{1,9}$/', '页码格式错误');
	$limit = filter('limit', '/^[0-9]{1,9}$/', '偏移格式错误');

	/*$page = 1;
	$limit = 10;*/

	if($limit <= 0) $limit = 1;
	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;

	$siteModel = model('site');
	$awsModel = model('aws');
	$result = $siteModel->siteList($user_id, $start, $limit, 0);
	$count = $siteModel->siteCount($user_id, 0);
	foreach ($result as $key => $value) {
		$general = $awsModel->general(date('Ym'), $value['site_id']);
		$result[$key]['visits'] = (isset($general['visits'])) ? $general['visits'] : 0;
		$result[$key]['visits_unique'] = (isset($general['visits_unique'])) ? $general['visits_unique'] : 0;
		$result[$key]['hits'] = (isset($general['hits'])) ? $general['hits'] : 0;
	}
	$array = array(
		'page' => $page,
		'limit' => $limit,
		'list' => $result,
		'total' => $count 
	);

	json(true, $array);


?>