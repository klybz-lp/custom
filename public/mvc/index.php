<?php

	//框架单一入口
	header("Content-type: text/html; charset=utf-8");
	define('ROOT_PATH', dirname(__FILE__));
	
	include 'boot/Psr4AutoLoad.php';  //自动加载文件
	include 'boot/Start.php';   //启动文件
	include 'boot/alias.php';  //命名空间映射文件
	include 'config/config.php';   //配置文件

	Start::router();  //启动路由方法

	
