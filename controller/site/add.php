<?php

router('site-add',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain = filter('domain', '/^[a-zA-z0-9\-\.]+\.[a-zA-z0-9\-\.]+$/', '域名格式错误');
	$path = filter('path', '/^\/.{0,255}+$/', '监控路径格式错误', '/');
	$custom_name = filter('name', '/^.{0,255}$/', '别名格式错误', '');
	$port = filter('port', '/^[0-9]{1,5}$/', '端口格式错误', 80);

	$siteModel = model('site');
	$info = $siteModel->get($domain, 'domain');
	if(!empty($info)) json(false, '该站点已经被添加');

	$result = $siteModel->add($domain, $user_id,  $custom_name, $port, $path);
	if($result == false) json(false, '添加失败');

	json(true, '添加成功');

});

?>