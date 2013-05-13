<?php

//router('notice-max',function(){

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$max = filter('max', '/^[0-9]{1,5}$/', '次数只能为数字');
	//$max = 100;

	$updateArray = array('day_notice_max' => $max);
	$user->update($user_id, $updateArray);
	
	json(true, '更新成功');
//});


?>