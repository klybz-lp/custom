<?php 

    //配置文件
    /*return [
        'TPL_VIEW' => 'app/view',
        'TPL_COMPILE' => './compile',
        'TPL_CACHE' => './cache',
    ];*/

    //模板引擎配置项
	define('VIEW_PATH', ROOT_PATH.'/app/view/');
	define('COMPILE_PATH', ROOT_PATH.'/compile/');
	define('CACHE_PATH', ROOT_PATH.'/cache/');
	define('IS_CACHE', false);  //是否开启缓存
	IS_CACHE ? ob_start() : null;

    //数据库配置参数
    $a = ['a', 'b'];
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PWD', 'root');
    define('DB_NAME', 'ppcms');
    define('DB_CHARSET', 'utf8');
    define('DB_PERFIX', 'pp_');
    /*$config['DB_HOST']      = 'localhost';
    $config['DB_USER']      = 'root';
    $config['DB_PWD']       = 'root';
    $config['DB_NAME']      = 'ppcms';
    $config['DB_CHARSET']   = 'utf8';
    $config['DB_PERFIX']    = 'pp_';*/


	
