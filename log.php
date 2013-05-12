<?php

router('log.daily',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始日期格式错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束日期格式错误');

	$domain = 'www.firefoxbug.net';
	/*$start_time = time() - 60*60*24*5;
	$stop_time = time();*/

	$aws = model('aws');
	$result = $aws->daily($domain, $start_time, $stop_time);
	if($result === false) json(false, '该域名不存在');

	$return['data'] = $result;
	$return['summary'] = $aws->summary($result);
	json(true, $return);

});

router('log.browser',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$time = filter('time', '/^[0-9]{1,10}$/', '日期格式错误');

	if($time == 0) $time = time();
	$domain = 'www.firefoxbug.net';
	/*$time = time() - 60*60*24*10;*/

	$aws = model('aws');
	$result = $aws->browser($domain, $time);
	if($result === false) json(false, '该域名不存在');

	$return = array();
	foreach ($result as $key => $value) {
		$name = preg_replace('/[0-9\.]/', '', $value['name']);
		//$result[$key]['a'] = preg_replace('/[0-9\.]/', '', $value['name']);
		if(isset($return[$name])){
			$return[$name] = $return[$name] + $value['hits'];
		}else{
			$return[$name] = $value['hits'];
			//echo $name;
		}
	}

	json(true, $return);

});

router('log.os',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$time = filter('time', '/^[0-9]{1,10}$/', '日期格式错误');

	if($time == 0) $time = time();
	$domain = 'www.firefoxbug.net';
	//$time = time() - 60*60*24*10;

	$aws = model('aws');
	$result = $aws->os($domain, $time);
	if($result === false) json(false, '该域名不存在');

	$return = array();
	foreach ($result as $key => $value) {
		$return[$value['name']] = $value['hits'];
	}

	json(true, $return);

});


router('log.general', function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$start_time = filter('start_time', '/^[0-9]{1,10}$/', '起始日期格式错误');
	$stop_time = filter('stop_time', '/^[0-9]{1,10}$/', '结束日期格式错误');

	$domain = 'www.firefoxbug.net';
	/*$start_time = time() - 60*60*24*5;
	$stop_time = time();*/

	$aws = model('aws');
	$result = $aws->general($domain, $start_time, $stop_time);
	if($result === false) json(false, '该域名不存在');

	$return['data'] = $result;
	$return['summary'] = $aws->summary($result);
	json(true, $return);
});

router('log.errors', function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$time = filter('time', '/^[0-9]{1,10}$/', '日期格式错误');

	$domain = 'www.firefoxbug.net';

	$aws = model('aws');
	$result = $aws->errors($domain);
	if($result === false) json(false, '该域名不存在');

	$return['data'] = $result;
	$return['summary'] = array(
		'hits' => 0,
		'bandwidth' => 0
	);
	foreach ($result as $key => $value) {
		$return['summary']['hits'] = $value['hits'] + $return['summary']['hits'];
		$return['summary']['bandwidth'] = $value['bandwidth'] + $return['summary']['bandwidth'];
	}
	json(true, $return);
});

router('log.errorsPath', function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$time = filter('time', '/^[0-9]{1,10}$/', '日期格式错误');
	$type = filter('type', '/^404$/', 'type格式错误');
	$page = filter('page', '/^[0-9]{1,10}$/', 'page格式错误');
	$limit = filter('limit', '/^[0-9]{1,10}$/', '偏移位置格式错误');

	$domain = 'www.firefoxbug.net';
	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;
	/*$start = 0;
	$limit = 10;
	$type = '404';*/

	$aws = model('aws');
	$result = $aws->errorsPath($domain, $type, $start, $limit);
	if($result === false) json(false, '该域名不存在');

	json(true, $result);

});

router('log.filetypes', function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$time = filter('time', '/^[0-9]{1,10}$/', '日期格式错误');

	$domain = 'www.firefoxbug.net';

	$aws = model('aws');
	$result = $aws->filetypes($domain);
	if($result === false) json(false, '该域名不存在');

	/*$return['data'] = $result;
	$return['summary'] = array(
		'hits' => 0,
		'bandwidth' => 0,
		'bwwithoutcompress' => 0,
		'bwaftercompress' => 0
	);
	foreach ($result as $key => $value) {
		$return['summary']['hits'] = $value['hits'] + $return['summary']['hits'];
		$return['summary']['bandwidth'] = $value['bandwidth'] + $return['summary']['bandwidth'];
		$return['summary']['bwwithoutcompress'] = $value['bwwithoutcompress'] + $return['summary']['bwwithoutcompress'];
		$return['summary']['bwaftercompress'] = $value['bwaftercompress'] + $return['summary']['bwaftercompress'];
	}*/
	json(true, $result);
});

router('log.hours', function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$time = filter('time', '/^[0-9]{1,10}$/', '日期格式错误');

	$domain = 'www.firefoxbug.net';

	$aws = model('aws');
	$result = $aws->hours($domain);
	if($result === false) json(false, '该域名不存在');

	json(true, $result);
});

router('log.pageref', function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$time = filter('time', '/^[0-9]{1,10}$/', '日期格式错误');
	$page = filter('page', '/^[0-9]{1,10}$/', 'page格式错误');
	$limit = filter('limit', '/^[0-9]{1,10}$/', '偏移位置格式错误');

	$domain = 'www.firefoxbug.net';
	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;

	$aws = model('aws');
	$result = $aws->pageref($domain, $start, $limit);
	if($result === false) json(false, '该域名不存在');

	$return['data'] = $result;
	$return['summary'] = array(
		'hits' => 0,
		'pages' => 0
	);
	foreach ($result as $key => $value) {
		$return['summary']['hits'] = $value['hits'] + $return['summary']['hits'];
		$return['summary']['pages'] = $value['pages'] + $return['summary']['pages'];
	}

	json(true, $return);
});

router('log.searchref', function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$time = filter('time', '/^[0-9]{1,10}$/', '日期格式错误');

	$domain = 'www.firefoxbug.net';

	$aws = model('aws');
	$result = $aws->searchref($domain);
	if($result === false) json(false, '该域名不存在');

	json(true, $result);
});

router('log.searchwords', function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$time = filter('time', '/^[0-9]{1,10}$/', '日期格式错误');
	$page = filter('page', '/^[0-9]{1,10}$/', 'page格式错误');
	$limit = filter('limit', '/^[0-9]{1,10}$/', '偏移位置格式错误');

	$domain = 'www.firefoxbug.net';
	if($page < 1) $page = 1;
	$start = ($page - 1) * $limit;

	$aws = model('aws');
	$result = $aws->searchwords($domain, $start, $limit);
	if($result === false) json(false, '该域名不存在');

	json(true, $result);
});


router('log.session', function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain_id = filter('domain_id', '/^[0-9]{1,9}$/', '域名ID格式错误');
	$time = filter('time', '/^[0-9]{1,10}$/', '日期格式错误');

	$domain = 'www.firefoxbug.net';

	$aws = model('aws');
	$result = $aws->session($domain);
	if($result === false) json(false, '该域名不存在');

	json(true, $result);
});

?>