<?php
namespace app\index\controller;

use think\Controller;
use think\Cookie;
use think\Session;

class Base extends Controller
{
    protected function _initialize()
    {
        parent::_initialize();  //继承父类的构造方法
        //dump(Cookie::get('admin_info'));
        if (!Session::has('admin_id') && Cookie::has('admin_id')){  //自动登录
            Session::set('admin_id', Cookie::get('admin_id'));
            Session::set('admin_info', Cookie::get('admin_info'));
        }
        //定义一个常量来判断用户是否登录
        define('ADMIN_ID', Session::has('admin_id') ? Session::get('admin_id'):null);
    }

    //判断用户是否登陆,放在系统后台入口前面: index/index
    protected function isLogin()
    {
        if (is_null(ADMIN_ID)) {
            $this -> error('用户未登陆,无权访问',url('admin/login'));
        }
    }

    //防止用户重复登陆,放在登陆操作前面:admin/login
    protected function alreadyLogin(){
        if (ADMIN_ID) {
            $this -> error('用户已经登陆,请勿重复登陆',url('index/index'));
        }
    }
}
