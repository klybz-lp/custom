<?php

	namespace controller;
    use \vendor\Tpl;   //引入模板引擎类
	/**
	* 
	*/
	class Controller extends Tpl
	{
		
		function __construct()
		{
			//重写Tpl类的构造方法
			parent::__construct(VIEW_PATH, COMPILE_PATH, CACHE_PATH);
		}

		//重写Tpl类的构造方法,如果不传参，则使用跟当前方法名同名的模板
		function display($viewName = null, $isInclude = true, $uri = null)
		{
			if (empty($viewName)) {
				$viewName = $_GET['m'].'/'.$_GET['a'].'.html';  //每个控制器对应一个模板目录
			}else{
				$viewName = $_GET['m'].'/'.$viewName;
			}
			parent::display($viewName, $isInclude = true, $uri = null);
		}
	}
?>
