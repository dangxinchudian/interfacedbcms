<?php


	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$mobile = filter('mobile', '/^[0-9]{11}$/', '手机需要为11位');

	$info = $user->get($user_id);
	if(!empty($info['mobile'])) json(false, '手机必须要解绑');

	
	// $code = $user->mailCodeCreat($user_id);
	// $html = "<a href=\"http://monitor.secon.me/mail-verify?code={$code}&mail={$info['mail']}\" target=\"_blank\">http://monitor.secon.me/mail-verify?code={$code}&mail={$info['mail']}</a>";
	// send_mail($info['mail'], '邮箱验证', $html);

	// json(true, '发送成功');



?>