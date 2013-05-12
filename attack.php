<?php

router('attack.daily',function(){

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

	$attack = model('attack');
	/*$result = $aws->daily($domain, $start_time, $stop_time);
	if($result === false) json(false, '该域名不存在');

	$return['data'] = $result;
	$return['summary'] = $aws->summary($result);
	json(true, $return);*/

});


?>