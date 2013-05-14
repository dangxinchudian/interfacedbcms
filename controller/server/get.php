<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$server_id = filter('server_id', '/^[0-9]{1,9}$/', 'serverID格式错误');

	//$server_id = 0;

	$serverModel = model('server');
	if($server_id == 0) $info = $serverModel->get($user_id, 'user_id');
	else $info = $serverModel->get($server_id);

	if(empty($info)) json(false, '服务器不存在');
	if($info['remove'] > 0) json(false, '服务器已经被移除');
	if($info['user_id'] != $user_id) json(false, '不允许操作他人服务器');

	$info['cpu'] = 0; 
	$info['disk'] = 1; 
	$info['memory'] = 2;
	$info['sys_descr'] = jdecode($info['sys_descr']);
	$info['sys_name'] = jdecode($info['sys_name']);
	$info['sys_uptime'] = jdecode($info['sys_uptime']);

	json(true, $info);


?>