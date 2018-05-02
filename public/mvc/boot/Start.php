<?php

	/**
	* 启动类
	*/
	class Start
	{
		static public $auto;  //自动加载对象

		//启动方法，即创建自动加载类的方法
		static function init()
		{
			self::$auto = new Psr4AutoLoad();
		}

		//路由方法
		static function router()
		{
			//通过访问的url来得到控制器名即类名和方法名，如index.php?m=index&a=demo,设置默认值为index
			$_GET['m'] = empty($_GET['m']) ? 'index' : $_GET['m'];
			$_GET['a'] = empty($_GET['a']) ? 'index' : $_GET['a'];

			//完整类名(包含路径+类名)：命名空间+控制器名,第一个\是转义符,先转为小写再首字母大写
			$className = 'controller\\'.ucfirst(strtolower($_GET['m'])).'Controller';  //结果类似controller\IndexController
			//根据类名创建对象
			$obj = new $className();

			//执行类中的方法,call_user_func函数类似于一种特别的调用函数的方法
			if (method_exists ( $obj, $_GET['a'] ) ) {
				call_user_func([$obj,$_GET['a']]);  //让对象$obj去执行$a方法
			} else {
				exit($_GET['a'].'方法不存在');
			}
		}
	}

	Start::init();
?>
