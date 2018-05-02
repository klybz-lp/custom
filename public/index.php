<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
header("Content-type: text/html; charset=utf-8");
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 定义配置文件目录和应用目录同级
define('CONF_PATH', __DIR__.'/../config/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
//\think\Build::module('pay');  //运行任意页面自动生成admin模块的目录即文件
//设置默认模块，url里可省略模块名
define('BIND_MODULE', 'index');
