<?php

	/**
	* 自定义自动加载类
	*/

	class Psr4AutoLoad
	{
		protected $maps = array();  //存放命名空间映射
		function __construct()
		{
			spl_autoload_register([$this,'autoload']);  //将本类中的autoload方法注册为自动加载方法
		}

        //自定义自动加载方法
		function autoload($className)
		{
			//echo $className;  //当加载没有引入的类时，输出类名
			//完整类名(命名空间名+类名)：命名空间+控制器名,第一个\是转义符
			//得到命名空间名，根据命名空间得到类的真实路径
			$pos = strrpos($className, '\\');  //得到最后一个\的位置,strrpos()查找字符串在另一字符串中最后一次出现的位置
			$namespace = substr($className, 0,$pos);  //得到命名空间名，如controller
			$reallClass = substr($className, $pos+1);  //得到类名，如IndexController
			//根据命名空间名+类名找到该类的文件路径，引入类文件
			$this->mapLoad($namespace, $reallClass);
		}

		//根据命名空间名得到目录路径并且拼接真正文件全路径
		function mapLoad($namespace, $reallClass)
		{
			//判断命名空间映射是否已经存在
			if (array_key_exists($namespace, $this->maps)) {
				$namespace = $this -> maps[$namespace];  //得到真实的全路径
			}

			//处理路径,将\或/替换成/，rtrim去掉右侧的/，再拼接上/，避免有的路径最后带了/，有的没带
			$namespace = rtrim(str_replace('\\/', '/', $namespace),'/').'/';
			//拼接文件全路径
			$filePath = $namespace.$reallClass.'.php';
			//包含文件
			if (file_exists($filePath)) {
				include $filePath;
			} else {
				exit($filePath.'文件不存在');
			}
			
		}

		//命名空间映射方法,即把命名空间映射到一个实际的路径，如controller映射到的真实路径是app/controller
		function addMaps($namespace, $path)
		{
			if (array_key_exists($namespace, $this->maps)) {  //判断映射是否存在
				exit('该命名空间已经被映射');
			} 

			$this-> maps[$namespace] = $path;
			
		}
	}
