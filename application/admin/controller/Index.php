<?php
namespace app\admin\controller;

class Index
{
    public function index()
    {
        return '后台首页';
    }

    public function show($name,$age=18)
    {
        return '我的名字是'.$name.'年龄是'.$age;
    }
}
