1111、管理员模块都是采用静态方法来操作数据库进行curd操作，这样操作的好处是不用实例化对象从而提高效率
模板常量：定义位置：D:\web\edu\thinkphp\library\think\View.php
基本的模板替换常量：
$baseReplace = [
	'__ROOT__'   => $root,
	'__URL__'    => $base . '/' . $request->module() . '/' . Loader::parseName($request->controller()),
	'__STATIC__' => $root . '/static',
	'__CSS__'    => $root . '/static/css',
	'__JS__'     => $root . '/static/js',
];
__STATIC__：指向的位置是根目录下的public目录下的static目录
2、项目上线前在应用目录下的comnfig.php里开启app_debug，会输出详细的错误信息
3、复制或删除大段代码时可先折叠起来再操作，ctrl+F12直接显示一个弹出层，里面只有这个类的属性和方法，点击就能快速定位了
windows下按下快捷`Ctrl`+`Shift`+`-`，这样就能折叠所有代码了。
windows下按下快捷`Ctrl`+`Shift`+`+`，这样就能恢复打开所有代码了。
如选择header标签，按旁边的-号或者按`Ctrl`+`Shift`+`-`即可折叠该段代码
4、模板引入公共文件：{include file="public/header" /}
5、模板继承：在view/public/目录下创建个基础模板base.html，如果把需要显示不同内容的地方使用block标签及name属性来自定义，子模板中只能出现两者标签：extend即block，虽然base.html与header.html在同一目录下，但引用时还说需要加上public路径，即{include file="public/header" /}，因为其指向的view目录，如果子模板中想使用base.html的block标签里的原本内容，可使用{__block__}，如果页面不需要基础模板中的某部分内容，直接使用对应name的block标签，里面不写内容即可
6、模板变量设置默认值：{$title|default="邀约系统数据管理后台"}
7、 load标签专门用来引入css跟js文件的：如{load href="/Public/js/common.js" /}  {load href="/Public/css/style.css" /}，css文件load可用css标签{css href="/Public/css/style.css" /}
8、去掉url中public方法：把网站地址指向该目录下的public目录即可，其他都无需修改，登录地址：http://www.liping.com/index/user/login
9、验证码：模板没img标签的使用<div>{:captcha_img()}</div>，有img标签的使用<div><img src="{:captcha_src()}" alt="captcha" /></div>，直接放入标签即可获得验证码图片
10、验证登录，因为需要接受表单发送的数据，所以需要引入请求类：use think\Request;
//前端ajax验证登录
$.ajax({
	type: 'POST',
	url: "{:url('checkLogin')}",  //表示当前控制器下的checkLogin方法
	data: $('form').serialize(),  //表单序列化
	dataType: 'json',
	success: function(data){
	  //console.log(data);return;
	  if (data.status == 1) {
		window.location.href="{:url('index/index')}";  //表示index控制器下的index方法
	  } else {
		alert(data.msg);
	  }
	}
  });
  
登录成功写入session:
//创建2个session,用来检测用户登陆状态和防止重复登陆
Session::set('admin_id', $manage -> id);
Session::set('admin_info', $manage -> getData()); //存放该记录的所有数据

 //登出
public function logout()
{
	//退出前先更新登录时间字段,下次登录时就知道上次登录时间了
	ManageModel::update(['last_login'=>time()],['id'=> Session::get('admin_id')]);
	Session::delete('user_id'); //delete方法只会删除当前登录用户的session信息，而destroy会删除所有用户的session信息
	Session::delete('user_info');

	$this -> success('注销登陆,正在返回',url('manage/login'));
}
11、系统可以有多个超级管理员，但名称为admin的超级管理员只能有一个，方便赋予最高权限的管理，其他超级管理员可以临时获得admin的权限进行操作，在修改时，如果用户没进行任何操作，提交按钮是无法点击的，防止没有修改时连接数据库
12、欢迎页面模板系统变量数据：
<p>登录次数：{$Think.session.user_info.login_count}</p>
<p>上次登录IP：{$Request.ip}  上次登录时间：{:date("Y-m-d H:i:s", $Think.session.user_info.login_time)}</p>
服务器计算机名：<td><span id="lbServerName">{$Request.host}</span></td>
服务器IP地址：<td>{$Request.ip}</td>
服务器域名：<td>{$Request.domain}</td><!--解析后的PHP语句:<?php echo \think\Request::instance()->domain(); ?>-->
当前PHP版本：<td>{$Think.const.PHP_VERSION}</td>
服务器版本：<td>{$Think.const.PHP_OS}</td>
当前请求URL：<td>{$Request.url.true}</td><!--解析后的PHP语句:<?php echo \think\Request::instance()->url(true); ?>，传入参数true,显示包括域名的完整绝对URL请求地址-->
当前Session数量：<td>{:count($_SESSION)}</td>   <!--这里用原生$_SESSION,TP5无对应方法-->
当前SessionID：<td>{:session_id()}</td><!--执行原生session_id()方法,因为tp5无对应请求方法-->

13、为了出错时更好的排查错误，可以查看模板编译后生成的页面方法：把D:\web\edu\runtime\temp里的缓存文件先删除，再刷新一次页面去检查
14、模板里如果使用了标签的分隔符{}会报错，解决办法是不解析某段代码里的{}：
{literal}
<input type="text" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" class="input-text Wdate" style="width:120px;">
{/literal}
总之，所有可能和内置模板引擎的解析规则冲突的地方都可以使用literal标签处理
15、联表查询：$artres=db('article')->field('a.*,b.catename')->alias('a')->join('bk_cate b','a.cateid=b.id')->order('a.id desc')->paginate(2);
16、使用iis服务器的rewrite组件重写url去掉index.php后，分页失效
17、当操作数据库使用的是模型类的静态方法时，可以使用echo AdminModel::getLastSql();来查看操作的sql语句
18、分页：
$this -> view -> count = AdminModel::count();  //获取总记录数
$map=[];  //条件，配合搜索查询的
$list = AdminModel::where($map)->field('id,name,email,role,login_count,update_time,status')->order('id', 'desc')->paginate(1,5);  //paginate方法获取所有记录带分页,第一个参数是每页显示的记录数，第二个参数是模板页码显示的数量
19、搜索
$map=[];
if(input('?param.name') && input('param.name') != ''){
	$map['name'] = ['like', '%'.trim(input('param.name')).'%']; //主持数组where('name','like',['%think','php%'],'OR');
	//$map['status'] = 1;
}
$this -> view -> count = AdminModel::where($map)->count();  //获取总记录数
$list = AdminModel::where($map)->field('id,name,email,role,login_count,update_time,status')->order('id', 'desc')->paginate(1,false,['query'=>request()->param()]);  //获取所有记录带分页,第一个参数是每页显示的记录数，第二个参数是模板页码显示的数量,第三个参数使搜索时的分页的url里不丢失查询的参数

---------------------------------------------------------------------------------------------------------------------------------------
tp5基础：
如果使用虚拟主机则必须把入口文件移到根目录下，因为虚拟主机无法指定网站根目录,php版本需大于等于5.4
西部数码虚拟主机去掉url里的index.php。在控制中心选择伪静态，然后会在根目录下生成web.config文件，使用下面内容替换里面的内容，重新启动
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
<rewrite>
<rules> 
<rule name="OrgPage" stopProcessing="true"> 
<match url="^(.*)$" />
<conditions logicalGrouping="MatchAll"> 
<add input="{HTTP_HOST}" pattern="^(.*)$" /> 
<add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" /> 
<add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" /> 
</conditions>
<action type="Rewrite" url="index.php/{R:1}" /> 
</rule>
</rules>
</rewrite>
 </system.webServer>
</configuration>
在配置文件里重新定义__STATIC__，因为index.php在根目录下了
// 视图输出字符串内容替换
    'view_replace_str'       => [
	    '__STATIC__'    =>  '/public/static/',
	],

1、tp5类文件名与类名首字母大写的驼峰写法，如控制器名Index.php（去掉tp3的controller），类名class Index extends \think\Controller，但login类名需首字母小写，如果类名是UserDemo,url访问则是user_demo，为了去掉url里的public，把网站的根目录指向public目录即可，tp5文件名首字母的就是类文件，小写的就是普通的PHP文件
2、指定模板方法：
public function index(){
	//用户登录界面显示，如果是模板名index.html,则 view方法可以不传参数,如果继承了controller可以使用return ->fetch()
	return view('login');
}
3、url方法代替U方法，模板里则是{:url('...')}代替{:U('...')}，如<a href="{:url('user/login/loginout')}">退出</a>,在当前模块，可以省略模块名，如{:url('login/loginout')}
4、全局配置文件(application/config.php)默认与惯例配置文件(/thinkphp/convention.PHP)内容一致，不建议修改惯例配置文件，可在全局配置文件修改如：
// 视图输出字符串内容替换
    'view_replace_str'       => [
        '__INDEX__' =>  '/jd/public/static/index',
        '__ADMIN__' =>  '/jd/public/static/admin'
    ],
配置文件的方法都在/thinkphp/library/think/config.php，如添加配置set、配置配置是否存在has、获取所有配置项的get方法等
可以各个模块下创建配置文件config.php，来配置该模块的一些配置项
5、自定义配置目录：在入口文件中定义独立的配置目录，添加CONF_PATH常量定义即可，例如：
define('CONF_PATH', __DIR__.'/../config/');  // 定义配置文件目录和应用目录同级，该目录下的配置文件即全局配置文件，可在该目录再创建目录，目录名称对应模块名，即模块的配置文件
注意如果使用了自定义配置目录，默认的全局配置文件(application/config.php)则失效，而且如果需要使用数据跟路由则必须把应用目录下的路由配置文件与数据库配置文件也放在该目录下
extra是扩展的配置目录
6、自动生成模块目录以及文件方法：最简单的就是在public/index.php最后加上\think\Build::module('admin');运行一下页面，如http://www.liping.com/public/，生成完了删除该代码即可，其他方法参考手册-命令行-自动生成目录结构	
使用命令行自动生成目录或文件需先把PHP的安装目录撞到系统环境变量里(如，在我的电脑-属性-高级系统设置-环境变量path里添加D:\phpStudy\php\php-5.4.45，重启cmd，输入php -v查看是否安装成功)，然后在控制台使用cd命令切换到应用根目录
7、iis服务器省略url中的index.php方法：先安装ISAPI_Rewrite，然后在安装目录的httpd.conf文件里加上RewriteRule (.*)$ /index\.php\?s=$1 [I]
但如果服务器帝国网站，则会出现打不开页面的情况，在站点管理里在该网站的isapi里删掉ISAPI_Rewrite即可
8、tp5提示could not find driver是因为程序中用到了PDO对象, 连接MySQL 5. 在php的默认设置中,只打开了php_pdo 模块, 没有打开php_pdo_mysql模块.所以才会出现找不到驱动程序的错误去php.ini配置文件开启extension=php_pdo_mysql.dll
9、路由：系统默认是混合模式即开启路由，并使用路由定义+默认PATH_INFO方式的混合，如果定义了路由规则则的链接必须使用路由规则来访问，没设置路由规则的链接使用默认的pathinfo模式访问
路由定义采用\think\Route类的rule方法注册，通常是在应用的路由配置文件application/route.php进行注册，
一：动态注册路由格式是：
Route::rule(‘路由表达式’,‘路由地址’,‘请求类型’,‘路由参数（数组）’,‘变量规则（数组）’);
think\Route::rule('adminlist','index/admin/adminlist');  //访问域名/adminlist显示的内容是：域名/index/admin/adminlist，注意配置了路由规则后，原先的访问地址则失效，解决方法是关闭该规则，或者在配置文件里关闭路由
配置带参数的路由，即方法是需要传入参数的,原访问地址是http://www.liping.com/admin/index/show/name/liping：
think\Route::rule('show:name','admin/index/show','GET',['ext'=>'shtml'],['name'=>'\w{1,10}']);  
第一个参数设置规则后的访问的地址，:后面的是方法里需要传入的变量名称，第二个参数是原访问地址，第三个参数是请求类型
第四个参数是后缀设置，第五个参数是对url里传入的变量值进行正则限定

二：推荐使用规则表达式路由，规则表达式通常包含静态地址和动态地址，或者两种地址的结合：
//推荐使用数组形式来定义路由规则
return [
    'adminlist' => 'index/admin/adminlist', //静态地址
    //'show/:name'  =>  'admin/index/show', //动态地址，访问:域名+/show/liping,实际访问的地址是 域名+/admin/index/show/name/liping
    //'show/:name'  =>  ['admin/index/show'],
    //'show/:name' =>  ['admin/index/show',['ext'=>'html'],['name'=>'\w{1,10}']],  //设置后缀及变量,默认的后缀是html，如果设置为空，则表示不能写后缀,不设置该参数则后缀可写为html或不写
    'show/:name/[:age]' =>  ['admin/index/show',['ext'=>'html'],['name'=>'\w{1,10}','age'=>'\d{1,3}']],  //设置多个变量的路由规则，如果变量加上中括号则表示是可选的
    '__alias__' =>  [
        'user'  =>  ['index/User', // user 别名路由到 index模块/User 控制器
        [
            'ext'=>'html',  //设置后缀
            'allow'=>'index,read,edit', //设置该控制器里可访问的方法，即白名单
            'except'=>'delete',  //设置黑名单
        ]],
    ],
];
路由分组功能允许把相同前缀的路由定义合并分组，这样可以提高路由匹配的效率，不必每次都去遍历完整的路由规则。
例如，我们有定义如下两个路由规则的话
'blog/:id'   => ['Blog/read', ['method' => 'get'], ['id' => '\d+']],
'blog/:name' => ['Blog/read', ['method' => 'post']],
可以合并到一个blog分组

'[blog]'     => [
    ':id'   => ['Blog/read', ['method' => 'get'], ['id' => '\d+']],
    ':name' => ['Blog/read', ['method' => 'post']],
],
路由地址即访问路由规则实际跳转到的地址，即路由规则的目标地址
//使用闭包，相当于可以在路由配置里直接写函数，不用去访问控制器里的方法，可以用来做些提示，支持参数传递，注意闭包规则需写在数组定义规则的上面，否则无效
think\Route::get('hello',function(){  //可直接访问 域名+hello  get表示请求的类型是get
    return '网站更新中。。。!';
});

路由到重定向地址（默认是301）：重定向的外部地址必须以“/”或者http开头的地址。
如果路由地址以“/”或者“http”开头则会认为是一个重定向地址或者外部地址，例如：
'blog/:id'=>'/blog/read/id/:id'和'blog/:id'=>'blog/read'
虽然都是路由到同一个地址，但是前者采用的是301重定向的方式路由跳转，这种方式的好处是URL可以比较随意（包括可以在URL里面传入更多的非标准格式的参数），而后者只是支持模块和操作地址。
采用重定向到外部地址通常对网站改版后的URL迁移过程非常有用，例如：
'blog/:id'=>'http://blog.thinkphp.cn/read/:id'
别名路由：Route::alias('user','index/User'); // user 别名路由到 index模块/User 控制器
10、自定绑定入口文件到模块，输入域名+admin.php直接访问admin模块
a、在配置文件在配置文件中设置，默认是false：'auto_bind_module'       => true,  // 入口自动绑定模块
b、在public目录下创建admin.php文件，把index.php的内容复制进去
11、控制器：操控基类controller.php源码：
public function checkLogin(Request $request) //Request $request表示以对象依赖注入的方式传参，Request是对参数进行类型约束，表示是参数的Request类型，$request表示传入请求类参数，依赖注入改变了使用对象之前必须创建对象的传统方式，而是从外部注入所依赖的对象，可以在控制器的构造方法或普通方法里使用
依赖注入的实现方法：对方法的参数进行对象类型的约束则会自动触发依赖注入，自动实例化该对象，如上因此checkLogin里不需要创建request对象即可直接使用，使用的句柄即$request
操控基类controller.php定义了一些属性，如view、request、failException、batchValidate、beforeActionList，和方法，
_initialize  初始化方法，beforeAction 前置操作方法，fetch 模板输出，display 渲染内容输出，assign  变量赋值，engine  初始化模板引起  ，validate  验证方法等