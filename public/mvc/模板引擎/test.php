<?php
    
	//框架单一入口
	error_reporting(E_ALL & ~E_NOTICE); 
	header("Content-type: text/html; charset=utf-8");
	define('ROOT_PATH', dirname(__FILE__));
	define('VIEW_PATH', ROOT_PATH.'/view/');
	define('COMPILE_PATH', ROOT_PATH.'/compile/');
	define('CACHE_PATH', ROOT_PATH.'/cache/');
	define('IS_CACHE', true);  //是否开启缓存
	IS_CACHE ? ob_start() : null;
	include 'Tpl.php';  //自动加载文件
	$tpl = new Tpl();

	$head = '这是网站头部';
	$title = '这是网站标题';
	$data = ['科比','韦德'];
	$score = ['数学'=>100,'历史'=>200];
	$cj = [
	        ['name'=>'liping','age'=>18],
	        ['name'=>'lijing','age'=>16],
	      ];
	$number = -1;

	$tpl -> assign('head',$head);
	$tpl -> assign('title',$title);
	$tpl -> assign('con','');
	$tpl -> assign('data',$data);
	$tpl -> assign('score',$score);
	$tpl -> assign('cj',$cj);
	$tpl -> assign('number',$number);

    $tpl -> display('test.html');


