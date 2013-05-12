<?php


router('constant.active',function(){		//监控打开关闭

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$constant_id = filter('constant_id', '/^[0-9]{1,9}$/', '监测ID错误');	
	$active = filter('active', '/^start|stop$/', '监测动作格式错误');

	$constant = model('constant');
	$updateArray = array('status' => $active);
	$result = $constant->update($constant_id, $updateArray);
	if($result > 0) json(true, '更改监控状态成功');
	json(false, '未更改');

});

router('constant.list',function(){		//中断监测列表

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$start = 0;
	$limit = 10;

	$page = filter('page', '/^[0-9]{1,9}$/', 'page格式错误');
	$limit = filter('limit', '/^[0-9]{1,9}$/', '偏移格式错误');
	$start_time = filter('fault_start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('fault_stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');
	if($limit <= 0) $limit = 1;

	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;

	$constantModel = model('constant');
	$list = $constantModel->userGet($user_id, $start, $limit);
	foreach ($list as $key => $value) {
		$list[$key]['available'] = $constantModel->available($value['constant_id'], $value['creat_time']);
		$list[$key]['fault_time'] = $constantModel->faultTime($value['constant_id'], $start_time, $stop_time);
		$list[$key]['fault_start_time'] = $start_time;
		$list[$key]['fault_stop_time'] = $stop_time;
	}
	$count = $constantModel->userCount($user_id);
	$array = array(
		'page' => $page,
		'limit' => $limit,
		'list' => $list,
		'total' => $count 
	);
	json(true, $array);

});

router('constant.get',function(){		//中断监测单个获得

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$constant_id = filter('constant_id', '/^[0-9]{1,9}$/', '监测ID错误');	
//	$constant_id = 0;

	$constantModel = model('constant');
	$domainModel = model('domain');
	//if($constant_id == 0) $constant_id = 1;
	if($constant_id == 0){
		$result = $constantModel->get($user_id, 'user_id');
	}else $result = $constantModel->get($constant_id);
	$constant_id = $result['constant_id'];
	//print_r($result);
	if(empty($result)) json(false, '监测ID对应对象为空');
	if($result['user_id'] != $user_id) json(false, '用户无法访问此中断监控数据');

	$result['work_time'] = time() - $result['creat_time'];
	$result['fault_count'] = $constantModel->faultCount($constant_id, $result['creat_time'], time());
	//$result['3dayfault'] = $constantModel->faultTime($constant_id, time() - 3600*24*3, time());
	$result['all_fault_time'] = $constantModel->faultTime($constant_id, $result['creat_time'], time());

	//补全domain
	$result['domain'] = $domainModel->get($result['domain_id']);
	//补全server
	if($result['server_id'] != 0){
		$server = model('server');
		$result['server_info'] = $server->get($result['server_id']);
	}else $result['server_info'] = false;

	json(true, $result);

});


router('constant.detail',function(){		//中断监测图表绘制

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$constant_id = filter('constant_id', '/^[0-9]{1,9}$/', '监测ID错误');
	$time_unit = filter('time_unit', '/^day|month|year$/', '时间单位错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');
	$node_id = filter('node_id', '/^[0-9\-]{1,10}$/', 'node_id错误');

	/*$constant_id = 0;
	$time_unit = 'day';
	$start_time = 1367337600;
	$stop_time  = 1367856000;
	$node_id = 0;*/

	if($stop_time < $start_time) json(false, 'time error!');
	$constantModel = model('constant');
	if($constant_id == 0) $constant_id = $constantModel->constant_id($user_id);
	if(!$constant_id) json(false, 'access deny!');
	if($node_id == -1) $node_id = false;

	$result = $constantModel->dataGet($constant_id, $time_unit, $start_time, $stop_time, $node_id);
//	$return['date'] = array();
//	$return['data'] = array();
	$data = array();

	//date&data complete
	for($i = 0 ; $i< ($stop_time - $start_time) / (3600*24); $i++){
		if($time_unit == 'day') $data[date('Y-m-d', $start_time + 3600 * 24 * $i)] = 0;
		elseif($time_unit == 'month') $data[date('Y-m', $start_time + 3600 * 24 * $i * 30)] = 0;
		elseif($time_unit == 'year') $data[date('Y-m', $start_time + 3600 * 24 * $i * 365)] = 0;
	}

	foreach ($result as $key => $value){
		$data[$value['time']] = $value['available'];
	}
	$return = array(
		'data' => array_values($data),
		'date' => array_keys($data)
	);
	//$return['date'] = 
	json(true, $return);

});

router('constant.node',function(){		//中断监测图表绘制

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$constant_id = filter('constant_id', '/^[0-9]{1,9}$/', '监测ID错误');
	
	$constant_id = 2;
	$constantModel = model('constant');
	if($constant_id == 0) $constant_id = $constantModel->constant_id($user_id);
	if(!$constant_id) json(false, 'access deny!');

	$result = $constantModel->nodeList($constant_id, true);
	$constant = $constantModel->get($constant_id);	
	foreach ($result as $key => $value) {
		$result[$key]['time'] = $constantModel->faultNode($constant_id, $value['constant_node_id'], $constant['period']);
	}

	json(true, $result);

});

router('constant.fault',function(){		//故障历史

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$start = 0;

	$constant_id = filter('constant_id', '/^[0-9]{1,9}$/', '监测ID错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始时间单位错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束时间单位错误');
	$page = filter('page', '/^[0-9]{1,10}$/', 'page格式错误');
	$limit = filter('limit', '/^[0-9]{1,10}$/', '偏移位置格式错误');
	$type = filter('type', '/^[0-9\-]{1,5}$/', 'type格式错误');

	/*$constant_id = 1;
	$start_time = time() - 60*60*24*30;
	$stop_time = time();
	$page = 1;
	$limit = 10;
	$type = -1;*/

	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;
	if($type == -1) $type = false;

	$constantModel = model('constant');
	$return = array(
		'page' => $page,
		'limit' => $limit,
		'total' => $constantModel->faultCount($constant_id, $start_time, $stop_time, $type)
	);
	$return['list'] = $constantModel->faultList($constant_id, $start_time, $stop_time, $start, $limit, $type);

	//fix http code
	foreach ($return['list'] as $key => $value) {
		$return['list'][$key]['request_result'] = errorHeader($value['request_status']);
	}

	json(true, $return);

});

router('constant.setPath',function(){		//设置监控路径

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$constant_id = filter('constant_id', '/^[0-9]{1,9}$/', '监测ID错误');
	$path = filter('path', '/^\/.{0,255}+$/', '监控路径格式错误');

	$constantModel = model('constant');
	$result = $constantModel->get($constant_id);
	if(empty($result)) json(false, '监测ID对应对象为空');
	$constantModel->update($constant_id, array('path' => $path));
	json(true, '路径设置成功');
});


?>