<?php
namespace app\index\validate;
use think\Validate;
class Admin extends Validate
{

    protected $rule=[
        'name'          =>      'unique:admin|require|length:2,20|chsDash',
        'password'      =>      'require|length:6,20',
        'nopass'        =>      'require|length:6,20|confirm:password',
        'email'         =>      'unique:admin|require|email',
        'captcha'       =>      'require|captcha'
    ];


    protected $message=[
        'name.require'=>'管理员名称不得为空！',
        'name.unique'=>'管理员名称不得重复！',
        'name.length'=>'管理员名称长度必须在2到20位之间！',
        'name.chsDash'=>'管理员名称不合法！',
        'password.require'=>'管理员密码不得为空！',
        'password.length'=>'管理员密码长度必须在6到20位之间！',
        'nopass.require'=>'管理员密码确认不得为空！',
        'nopass.length'=>'管理员密码确认长度必须在6到20位之间！',
        'nopass.confirm'=>'管理员密码与密码确认不一致！',
        'email.require'=>'管理员邮箱不得为空！',
        'email.unique'=>'管理员邮箱不得重复！',
        'captcha.require'=>'验证码不得为空！',
        'captcha.captcha'=>'验证码错误！',
    ];

    protected $scene=[
        'add'  =>  ['name','password','nopass','email'],
        'login'=>['name'=>'require|length:4,20','password'=>'require|length:6,20','captcha'],
        'edit'  =>  ['name'=>'require|length:4,20|unique:admin,name^id','password'=>'length:6,20','email'=>'require|unique:admin,email^id'],
        //可以在定义场景的时候对某些字段的规则重新设置，name^id不是加入主键的唯一性就可以了，解决没修改用户名时也验证唯一性，id需要在验证前传递的data数组里传递过来
    ];





    

    




   

	












}
