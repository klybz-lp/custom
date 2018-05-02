<?php

	//框架单一入口
	header("Content-type: text/html; charset=utf-8");
	/**
	* 自定义自动加载类
	*/

	class Psr4AutoLoad
	{
		
		function __construct()
		{
			spl_autoload_register([$this,'autoload']);  //将本类中的autoload方法注册为自动加载方法
		}

        //自定义自动加载方法
		function autoload($className)
		{
			//echo $className;  //当加载没有引入的类时，输出类名
			//根据类名找到该类的文件路径，引入类文件
			$filePath = str_replace('\\', '/', $className).'.php';
			include $filePath;
		}
	}

	$psr = new Psr4AutoLoad();
	//通过访问的url来得到控制器名即类名，如index.php?m=index&a=demo
	$m = $_GET['m'];
	//完整类名(包含路径+类名)：命名空间+控制器名,第一个\是转义符,先转为小写再首字母大写
	$className = 'controller\\'.ucfirst(strtolower($m)).'Controller';  //结果类似controller\IndexController
	//根据类名创建对象
	$obj = new $className();
	//得到方法名
	$a = $_GET['a'];

	//执行类中的方法,call_user_func函数类似于一种特别的调用函数的方法
	call_user_func([$obj,$a])  //让对象$obj去执行$a方法






?>
