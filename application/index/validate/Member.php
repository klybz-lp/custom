<?php
namespace app\index\validate;
use think\Validate;
class Member extends Validate
{

    protected $rule=[
        'name'          =>      'require|length:2,20|chsDash',
        'tel'           =>      'unique:member|require|number|^1\d{10}$',
        'yaoyue'        =>      'require',
        'kefu'          =>      'require',
        'type'          =>      'require'
    ];


    protected $message=[
        'name.require'=>'名称不得为空！',
        'name.length'=>'名称长度必须在2到20位之间！',
        'tel.unique'=>'电话号码不得重复！',
        'name.chsDash'=>'名称格式不合法！',
        'tel.require'=>'电话号码不得为空！',
        'tel.number'=>'电话号码格式不正确',
        'yaoyue.require'=>'邀约人不得为空！',
        'kefu.require'=>'现场客服不得为空！',
        'type.require'=>'客户级别不得为空！',
    ];

    protected $scene=[
        'add'  =>  ['name','tel','yaoyue','kefu','type'],
        'edit'  =>  ['name','tel'=>'require|number|unique:member,tel^id','yaoyue','kefu','type'],
    ];





    

    




   

	












}
