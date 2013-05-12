<?php

router('server-add',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$domain = filter('domain', '/^[a-zA-z0-9\-\.]+\.[a-zA-z0-9\-\.]+$/', '域名格式错误');
	$path = filter('path', '/^\/.{0,255}+$/', '监控路径格式错误');
	$custom_name = filter('name', '/^.{0,255}$/', '别名格式错误');
	$port = filter('port', '/^[0-9]{1,5}$/', '端口格式错误');

	/*$domainModel = model('domain');
	$constantModel = model('constant');
	$info = $domainModel->get($domain, 'domain');

	if(!empty($info)) json(false, '该域名已经被添加');

	$result = $domainModel->add($domain, $user_id, $custom_name);
	if($result == false) json(false, '添加失败');
	$constantModel->add($result);
	$constantModel->update($result, array('path' => $path));
	json(true, '添加成功');*/

});

?>
