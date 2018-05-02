<?php
// 自定义路由配置文件
//think\Route::rule(['new','new/:id'],'index/News/read');
//think\Route::rule('adminlist','index/admin/adminlist');  //访问域名/adminlist显示的内容是：域名/index/admin/adminlist，注意配置了路由规则后，原先的访问地址则失效，解决方法是关闭该规则，或者在配置文件里关闭路由
//think\Route::rule('show:name','admin/index/show','GET',['ext'=>'html'],['name'=>'\w{1,10}']);
//使用闭包，相当于可以在路由配置里直接写函数，不用去访问控制器里的方法，可以用来做些提示，支持参数传递，注意闭包规则需写在数组定义规则的上面，否则无效
think\Route::get('hello',function(){  //可直接访问 域名+hello   get表示请求的类型是get
    return '网站更新中。。。!';
});
think\Route::alias('user','index/User'); // user 别名路由到 index模块/User 控制器
//推荐使用数组形式来定义路由规则
return [
    //'adminlist' => 'index/admin/adminlist', //静态地址
    //'show/:name'  =>  'admin/index/show', //动态地址，访问:域名+/show/liping,实际访问的地址是 域名+/admin/index/show/name/liping
    //'show/:name'  =>  ['admin/index/show'],
    //'show/:name' =>  ['admin/index/show',['ext'=>'html'],['name'=>'\w{1,10}']],  //设置后缀及变量,默认的后缀是html，如果设置为空，则表示不能写后缀,不设置该参数则后缀可写为html或不写
    'show/:name/[:age]' =>  ['admin/index/show',['ext'=>'html'],['name'=>'\w{1,10}','age'=>'\d{1,3}']],  //设置多个变量的路由规则，如果变量加上中括号则表示是可选的
   /* '__alias__' =>  [
        'user'  =>  ['index/User', // user 别名路由到 index模块/User 控制器
        [
            'ext'=>'html',  //设置后缀
            'allow'=>'index,read,edit', //设置该控制器里可访问的方法，即白名单
            'except'=>'delete',  //设置黑名单
        ]],
    ],*/
];
