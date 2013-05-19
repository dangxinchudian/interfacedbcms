<?php



	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	// $rule_id = filter('rule_id', '/^[0-9]{1,9}$/', 'rule_id格式错误');
	// $max_limit = filter('max_limit', '/^[0-9]{1,9}$/', 'max_limit格式错误');
	// $min_limit = filter('min_limit', '/^[0-9]{1,9}$/', 'min_limit格式错误');

	// $keep_time = filter('min_limit', '/^[0-9]{1,9}$/', 'keep_time格式错误', 300);
	// $cool_down_time = filter('min_limit', '/^[0-9]{1,9}$/', 'keep_time格式错误', 600);
	// $notice_limit = filter('min_limit', '/^[0-9]{1,9}$/', 'notice_limit格式错误', 3);

	$rule_id = 1;
	$max_limit = 99;
	$min_limit = 0;
	$keep_time = 300;
	$cool_down_time = 600;
	$notice_limit = 3;

	if($min_limit == 0 && $max_limit == 0) json(false, '不能为无限制');

	$faultModel = model('fault');
	$info = $faultModel->getRule($rule_id);
	if(empty($info)) json(false, '该规则不存在');
	if($info['remove'] > 0) json(false, '规则已经被移除');
	if($info['user_id'] != $user_id) json(false, '不允许操作他人规则');

	$updateArray = array(
		'max_limit' => $max_limit,
		'min_limit' => $min_limit,
		'keep_time' => $keep_time,
		'cool_down_time' => $cool_down_time,
		'notice_limit' => $notice_limit
	);

	$result = $faultModel->updateRule($rule_id, $updateArray);
	if($result > 0) json(true, '更改成功');
	json(false, '未更改');



?>