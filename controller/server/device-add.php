<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

//	$item_id = filter('item_id', '/^[0-9]{1,9}$/', 'itemID格式错误');
//	$server_id = filter('server_id', '/^[0-9]{1,9}$/', 'serverID格式错误');
	$item_id = 2;
	$server_id = 2;

	$serverModel = model('server');

	$info = $serverModel->get($server_id);
	if(empty($info)) json(false, '服务器不存在');
	if($info['remove'] > 0) json(false, '服务器已经被移除');
	if($info['user_id'] != $user_id) json(false, '不允许操作他人服务器');

	$item = $serverModel->item($item_id);
	if(empty($item)) json(false, '监控项目不存在');
	if($item != 'all' && $item != $info['os']) json(false, '该操作系统不支持该监控项目');
	
	//查询硬件是否注册,如果server_hardware_id为0则无需注册
	if($item['server_hardware_id'] != 0){
		
	}
	print_r($item);


	//echo $serverModel->partitionSql();

	//$itemSql*/


?>