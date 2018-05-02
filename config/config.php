<?php
//自定义的配置文件，注意如果使用了自定义配置目录，默认的全局配置文件(application/config.php)则失效，而且如果需要使用数据跟路由则必须把应用目录下的路由配置文件与数据库配置文件也放在该目录下
return [
    // 应用调试模式，项目部署后关闭
    'app_debug'              => false,
    // 应用Trace，项目部署后关闭
    'app_trace'              => false,
    // 是否开启路由
    'url_route_on'           => true,
    // 入口自动绑定模块
    'auto_bind_module'       => true,
	'default_filter'         => 'htmlspecialchars',  //变量过滤
    //验证码配置
    'captcha'  => [
        // 验证码字符集合
        'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        // 验证码字体大小(px)
        'fontSize' => 25,
        // 是否画混淆曲线
        'useCurve' => false,
        // 验证码图片高度
        'imageH'   => 50,
        // 验证码图片宽度
        'imageW'   => 212,
        // 验证码位数
        'length'   => 4,
        // 验证成功后是否重置
        'reset'    => true
    ],
    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 10,
    ],
    //错误页无法使用全局参数 请自行修改，开启404单独配置模板，需把app_debug设为false,即部署模式
	'http_exception_template'    =>  [
		// 定义404错误的重定向页面地址
		404 =>  APP_PATH.'404.html',
		// 还可以定义其它的HTTP status
		401 =>  APP_PATH.'401.html',
	]
];
