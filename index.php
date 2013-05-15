<?php
/*Twwy's art---安全监测平台*/

date_default_timezone_set('PRC');

preg_match('/\/interface\/(.+)$/', $_SERVER['REQUEST_URI'], $match);
//preg_match('/\/data\/(.+)$/', $_SERVER['REQUEST_URI'], $match);
$uri = (empty($match)) ? 'default' : $match[1];

/*数据库*/
require('./database.php');
$db = new database;

/*路由*/
$router = Array();
function router($path, $func){
	global $router;
	$router[$path] = $func;
}

/*视图*/
/*function view($page, $data = Array(), $onlyBody = false){
	foreach ($data as $key => $value) $$key = $value;
	if($onlyBody) return require("./view/{$page}");
	require("./view/header.html");
	require("./view/{$page}");
	require("./view/footer.html");
}*/

/*会话*/
session_start();

/*JSON格式*/
function json($result, $value){
	if($result) exit(json_encode(array('result' => true, 'data' => $value)));
	exit(json_encode(array('result' => false, 'msg' => $value)));
}

/*POST过滤器*/	//符合rule返回字符串，否则触发callback，optional为真则返回null
function filter($name, $rule, $callback, $optional = false){
	if($optional !== false){
		if(isset($_POST[$name])){
			if(preg_match($rule, $post = trim($_POST[$name]))) return $post;
			else{
				if(is_object($callback)) return $callback();
				else json(false, $callback);			
			}
		}elseif($optional === true) return null;
		else return $optional;
	}else{
		if(isset($_POST[$name]) && preg_match($rule, $post = trim($_POST[$name]))) return $post;
		else{
			if(is_object($callback)) return $callback();
			else json(false, $callback);			
		} 
	}

}

/*模型*/
class model{
	function db(){
		global $db;
		return $db;
	}
}//model中转db类
function model($value){
	require("./model/{$value}.php");
	return new $value;
}

/*扩展函数*/
require('common.php');
require('phpmailer/class.phpmailer.php');

/*================路由表<开始>========================*/

router('user-login', function(){ require('./controller/user/login.php'); });
router('user-reg',function(){ require('./controller/user/reg.php'); });
router('user-mail',function(){ require('./controller/user/mail.php'); });
router('user-mail-verify',function(){ require('./controller/user/mail-verify.php'); });
router('user-reset',function(){ require('./controller/user/reset.php'); });
router('user-reset-verify',function(){ require('./controller/user/reset-verify.php'); });

router('notice-max',function(){ require('./controller/notice/max.php'); });

router('site-add',function(){ require('./controller/site/add.php'); });
router('site-remove',function(){ require('./controller/site/remove.php'); });
router('site-modify',function(){ require('./controller/site/modify.php'); });

router('site-constant-list',function(){ require('./controller/site/constant-list.php'); });
router('site-constant-active',function(){ require('./controller/site/constant-active.php'); });
router('site-constant-node',function(){ require('./controller/site/constant-node.php'); });
router('site-constant-detail',function(){ require('./controller/site/constant-detail.php'); });
router('site-constant-get',function(){ require('./controller/site/constant-get.php'); });

router('site-action-month-list',function(){ require('./controller/aws/list.php'); });
router('site-aws-action-pages',function(){ require('./controller/aws/pages.php'); });
router('site-aws-action-monthly',function(){ require('./controller/aws/monthly.php'); });
router('site-aws-action-daily',function(){ require('./controller/aws/daily.php'); });



router('server-add',function(){ require('./controller/server/add.php'); });
router('server-snmp-check',function(){ require('./controller/server/snmp-check.php'); });
router('server-list',function(){ require('./controller/server/list.php'); });
router('server-modify',function(){ require('./controller/server/modify.php'); });
router('server-remove',function(){ require('./controller/server/remove.php'); });
router('server-device-add',function(){ require('./controller/server/device-add.php'); });
router('server-get',function(){ require('./controller/server/get.php'); });

router('test',function(){
	echo '<form method="POST" action="./user-login"><input name="mail" value="zje2008@qq.com"/><input name="pass" value="b123456"/><input type="submit"/></form>';
});

router('test2',function(){
	echo '<form method="POST" action="./site-add"><input name="domain" value="www.hdu.edu.cn"/><input name="name" value="hdu"/><input type="submit"/></form>';
});

router('test3', function(){
	echo '<form method="POST" action="./server-add"><input name="ip" value=""/><input type="submit"/></form>';
});

/*================路由表<结束>========================*/


/*路由遍历*/
foreach ($router as $key => $value){
	if(preg_match('/^'.$key.'$/', $uri, $matches)) exit($value($matches));
}

/*not found*/
echo 'Page not fonud';

?>
