<?php
namespace app\index\Model;
use think\Model;
use traits\model\SoftDelete;  //软删除类

class Admin extends Model
{
    //导入软删除方法集
    use SoftDelete;
    //设置软删除字段，只有该字段为NULL,该字段才会在列表显示出来
    protected $deleteTime = 'delete_time';

    // 保存自动完成列表
    protected $auto = [
        'delete_time' => NULL,
        //'is_delete' => 0, //1:已删除 0:未删除，配合恢复软删除的数据，作为条件
    ];
    // 是否需要自动写入时间戳
    protected $autoWriteTimestamp = true; //自动写入
    // 创建时间字段自动写入时间戳 ，默认是create_time，如果数据表字段名与其一致可不写
    protected $createTime = 'create_time';
    // 更新时间字段自动写入时间戳 ，默认是update_time，如果数据表字段名与其一致可不写
    protected $updateTime = 'update_time';
    // 时间字段取出后列表显示的的默认时间格式
    protected $dateFormat = 'Y-m-d';
    // 新增自动完成列表
    protected $insert = [
        'login_time'    =>  NULL,   //新增时登录时间应该为NULL,因为刚创建
        'login_count'   =>  0,      //登录次数初始为0
        //'role'          =>  2,      //角色默认为管理员
    ];
    // 更新自动完成列表
    protected $update = [];

    //数据表中角色字段:role返回值处理
    public function getRoleAttr($value)
    {
        $role = [
            1=>'超级管理员',
            2=> '管理员'
        ];
        return $role[$value];
    }

    //状态字段:status返回值处理
    public function getStatusAttr($value)
    {
        $status = [
            0=>'已停用',
            1=> '已启用'
        ];
        return $status[$value];
    }

    //密码修改器
    public function setPasswordAttr($value)
    {
        return sha1($value);
    }

    //登录时间获取器
    public function getLoginTimeAttr($value)
    {
        return date('Y/m/d H:i', $value);
    }

}