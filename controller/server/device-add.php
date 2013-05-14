<?php

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$serverModel = model('server');
	echo $serverModel->partitionSql();


?>