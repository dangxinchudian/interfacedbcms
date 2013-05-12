<?php

router('server.add',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$ip =  filter('ip', '/^([0-9]{1,3}.){3}[0-9]{1,3}$/', 'IP格式错误');
	$custom_name = filter('customName', '/^.{0,255}$/', '别名格式错误');

	$server = model('server');
	$info = $server->get($ip, 'ip');

	if(!empty($info)) json(false, '该服务器IP已经被添加');

	$result = $server->add($ip, $user_id, $custom_name);
	if($result === false) json(false, '添加失败');
	json(true, '添加成功');

});

router('server.setName',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$server_id =  filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误');
	$custom_name = filter('customName', '/^.{0,255}$/', '别名格式错误');

	$server = model('server');
	$info = $server->get($server_id);

	if(empty($info)) json(false, '服务器ID不存在');
	if($info['user_id'] != $user_id) json(false, '无权操作该服务器');

	$result = $server->update($server_id, array('custom_name' => $custom_name), "server_id = '{$server_id}'");
	if($result == 0) json(false, '未进行更改');
	json(true, '更改成功');

});

router('server.setPort',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$server_id =  filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误');
	$port = filter('port', '/^([0-9]{1,5}$/', 'port格式错误');

	$server = model('server');
	$info = $server->get($server_id);

	if(empty($info)) json(false, '服务器ID不存在');
	if($info['user_id'] != $user_id) json(false, '无权操作该服务器');

	$result = $server->update($server_id, array('port' => $port), "server_id = '{$server_id}'");
	if($result == 0) json(false, '未进行更改');
	json(true, '更改成功');

});

router('server.setCommunity',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$server_id =  filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误');
	$community = filter('community', '/^.{0,255}$/', 'community格式错误');

	$server = model('server');
	$info = $server->get($server_id);

	if(empty($info)) json(false, '服务器ID不存在');
	if($info['user_id'] != $user_id) json(false, '无权操作该服务器');

	$result = $server->update($server_id, array('snmpv2_community' => $community), "server_id = '{$server_id}'");
	if($result == 0) json(false, '未进行更改');
	json(true, '更改成功');

});

router('server.info',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$update =  filter('update', '/^true|false$/', '是否进行更新');
	$server_id =  filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误');

	$server = model('server');
	$serverInfo = $server->get($server_id);
	if($serverInfo['user_id'] != $user_id) json(false, '不能访问他人server');

	if($update == 'true'){
		$new = $server->updateInfo($server_id);
		$serverInfo = array_merge($serverInfo, $new);
	}

	$serverInfo['last_netstat'] = jdecode($serverInfo['last_netstat']);
	$serverInfo['last_run'] = jdecode($serverInfo['last_run']);
	$serverInfo['last_device'] = jdecode($serverInfo['last_device']);
	$serverInfo['last_cpu'] = jdecode($serverInfo['last_cpu']);
	$serverInfo['last_memory'] = jdecode($serverInfo['last_memory']);
	$serverInfo['last_disk'] = jdecode($serverInfo['last_disk']);
	$serverInfo['last_network'] = jdecode($serverInfo['last_network']);
	json(true, $serverInfo);

});

router('server.list',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$page = filter('page', '/^[0-9]{1,9}$/', 'page格式错误');
	$limit = filter('limit', '/^[0-9]{1,9}$/', '偏移格式错误');	

	/*$page = 1;
	$limit = 5;*/

	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;

	$server = model('server');
	$where = " user_id = '{$user_id}' LIMIT {$start},{$limit}";
	$result = $server->listGet($where);

	foreach ($result as $key => $value) {
		$result[$key]['last_netstat'] = jdecode($result[$key]['last_netstat']);
		$result[$key]['last_run'] = jdecode($result[$key]['last_run']);
		$result[$key]['last_device'] = jdecode($result[$key]['last_device']);
		$result[$key]['last_cpu'] = jdecode($result[$key]['last_cpu']);
		$result[$key]['last_memory'] = jdecode($result[$key]['last_memory']);
		$result[$key]['last_disk'] = jdecode($result[$key]['last_disk']);
		$result[$key]['last_network'] = jdecode($result[$key]['last_network']);
	}

	$return = array(
		'list' => $result,
		'page' => $page,
		'limit' => $limit,
		'total' => $server->countGet(" user_id = '{$user_id}'")
	);

	json(true, $return);

});


router('server.disk',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$server_id =  filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误');
	$name =  filter('name', '/^.{0,255}$/', '磁盘名字格式错误');
	$time_unit = filter('time_unit', '/^day|month|year$/', '时间单位错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');

	$name = base64_encode($name);

	$server = model('server');
	$result = $server->diskGet($server_id, $name, $time_unit, $start_time, $stop_time);
	json(true, $result);

});

router('server.network',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$server_id =  filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误');
	$descr =  filter('descr', '/^.{0,255}$/', '网卡描述名称格式错误');
	$time_unit = filter('time_unit', '/^day|month|year$/', '时间单位错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');

	$descr = base64_encode($descr);

	$server = model('server');
	$result['data'] = $server->networkGet($server_id, $descr, $time_unit, $start_time, $stop_time);
	$result['summary'] = $server->networkSummary($server_id, $descr, $start_time, $stop_time);
	json(true, $result);

});

router('server.cpu',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$server_id =  filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误');
	$time_unit = filter('time_unit', '/^day|month|year$/', '时间单位错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');

	$server = model('server');
	$result = $server->cpuGet($server_id, $time_unit, $start_time, $stop_time);
	json(true, $result);

});

router('server.memory',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$server_id =  filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误');
	$time_unit = filter('time_unit', '/^day|month|year$/', '时间单位错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');

	$server = model('server');
	$result = $server->memoryGet($server_id, $time_unit, $start_time, $stop_time);
	json(true, $result);

});

router('server.run',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$server_id =  filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误');
	$time_unit = filter('time_unit', '/^day|month|year$/', '时间单位错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');

	$server = model('server');
	$result = $server->runGet($server_id, $time_unit, $start_time, $stop_time);
	json(true, $result);

});

router('server.netstat',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$server_id =  filter('server_id', '/^[0-9]{1,9}$/', 'server_id格式错误');
	$time_unit = filter('time_unit', '/^day|month|year$/', '时间单位错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');

	$server = model('server');
	$result = $server->netstatGet($server_id, $time_unit, $start_time, $stop_time);
	json(true, $result);

});


?>