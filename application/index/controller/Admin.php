<?php
namespace app\index\controller;
use app\index\model\Admin as AdminModel;  //因为控制器名称也叫Manage，避免冲突，所以取一个别名
use think\Cookie;
use think\Request;
use think\Session;

class Admin extends Base
{
    //登录界面
    public function login()
    {
        $this->alreadyLogin();
        return $this->fetch();
    }

    //验证登录，Request $request表示以对象依赖注入的方式传参，Request表示是参数的类型必须是Request类的对象或其子类的对象，$request表示实例化后的对象句柄
    /*依赖注入改变了使用对象之前必须创建对象的传统方式，而是从外部注入所依赖的对象，可以在控制器的构造方法或普通方法里使用
    依赖注入的实现方法：对方法的参数进行对象类型的约束则会自动触发依赖注入，自动实例化该对象，如上因此checkLogin里不需要创建request对象即可直接使用*/
    public function checkLogin(Request $request)
    {
        //print_r($_POST);exit();  //打印提交过来的数据
        if (request()->isAjax()){
            $status = 0; //验证失败标志
            $data = $request -> param();
            $validate = validate('Admin');
            $result = $validate ->scene('login') -> check($data);
            //此处必须全等===才可以,因为验证不通过,$result保存错误信息字符串,返回非零
            if ($result === true) {
                //查询条件,下标对应数据库的字段名
                $map = [
                    'name' => $data['name'],
                    'password' => sha1($data['password'])
                ];

                //数据表查询,返回模型对象
                $admin = AdminModel::get($map);
                //echo AdminModel::getLastSql();
                if ($admin == true) {
                    $status = 1;
                    //创建2个session,用来检测用户登陆状态和防止重复登陆
                    Session::set('admin_id', $admin -> id);
                    Session::set('admin_info', $admin -> getData());  //存放该记录的所有数据

                    if (request()->has('isCheck','post')){  //如果勾选了remeber me,则设置cookie来自动登录
                        Cookie::set('admin_id',$admin -> id,2592000);
                        Cookie::set('admin_info',$admin -> getData(),2592000);
                        /*dump(Cookie::get('admin_info'));
                        exit();*/
                    }

                    //更新用户登录次数:自增1
                    $admin -> setInc('login_count');

                    AdminModel::update(['login_time'=>time(),'login_ip'=>request()->ip()],['id'=> $admin -> id]);
                }
            }else{
                //dump($validate->getError());
                if ($validate->getError() == '验证码错误！'){
                    $status = -1;
                }
            }

            return $status;
        }

    }

    //登出
    public function logout()
    {
        //退出前先更新登录时间字段,下次登录时就知道上次登录时间了
        AdminModel::update(['update_time'=>time()],['id'=> Session::get('admin_id')]);
        Session::delete('admin_id'); //delete方法只会删除当前登录用户的session信息，而destroy会删除所有用户的session信息
        Session::delete('admin_info');
        Cookie::delete('admin_id');
        Cookie::delete('admin_info');

        $this -> success('注销登陆,正在返回',url('admin/login'));
    }

    //列表
    public function adminList()
    {
        //$this->isLogin();
        $map=[];
        if(input('?param.name') && input('param.name') != ''){
            $map['name'] = ['like', '%'.trim(input('param.name')).'%']; //组装数组where
            //$map['status'] = 1;
        }
        $this -> view -> count = AdminModel::where($map)->count();  //获取总记录数
        $list = AdminModel::where($map)->field('id,name,email,role,login_count,update_time,status')->order('id', 'desc')->paginate(config
        ('list_rows'),false,['query'=>request()->param()]);  //获取所有记录带分页,第一个参数是每页显示的记录数，第二个参数是模板页码显示的数量,第三个参数使搜索时的分页的url里不丢失查询的参数
        //dump($list);
        $num = input('param.page') > 0 ? input('param.page') : 1;
        $this->view->num = ($num-1)*10;  //编号
        $this -> view -> assign('list', $list);
        return $this->fetch('admin_list');
    }

    //添加
    public function adminAdd(Request $request)
    {
        //$this->isLogin();
        if (request()->isAjax()){
            $status = 1;
            $data = $request -> param();
            $validate = validate('Admin');
            $result = $validate ->scene('add') -> check($data);
            if ($result === true) {
                $_data = [
                    'name'          =>  trim(input('param.name')),
                    'password'      =>  trim(input('param.password')),
                    'email'         =>  trim(input('param.email')),
                    'status'        =>  trim(input('param.status')),
                    'role'          =>  trim(input('param.role')),
                ];
                $res= AdminModel::create($_data);
                if ($res === null) {
                    $status = 0;
                }
            }else{
                //dump($validate->getError());
                $status = 0;
            }


            return $status;
        }
        return $this->fetch('admin_add');
    }

    //编辑admin_edit
    public function adminEdit(Request $request)
    {
        //$this->isLogin();
        if (request()->isAjax()){
            $status = '';
            $data = $request -> param();
            $_data = array();
            //过滤表单空元素
            foreach ($data as $k=>$v){
                if ($v != ''){  //使用empty判断会把为0的元素也过滤掉，如状态
                    $_data[$k] = $v;
                }
            }
            $validate = validate('Admin');
            $result = $validate ->scene('edit') -> check($_data);
            if ($result === true) {

                $condition = ['id'=>$_data['id']] ;
                $result = AdminModel::update($_data, $condition);
                //echo AdminModel::getLastSql();  //查看使用静态方法的sql语句
                //如果是admin用户,更新当前session中用户信息user_info中的角色role,供页面调用
                if (Session::get('admin_info.name') == 'admin') {
                    Session::set('admin_info.role', $_data['role']);
                }

                if (true == $result) {
                    $status = 1;
                } else {
                    $status = 0;
                }
            }else{
                //dump($validate->getError());
                $status = -1;
            }

            return $status;
        }
        $adminId = input('param.id');
        $result = AdminModel::get($adminId);;  //通过id获取一条记录
        //dump($result->getData());
        $this->view->assign('admin_info',$result->getData());  //getData方法获取的是原始数据
        return $this->view->fetch('admin_edit');
    }

    //验证用户名是否被占用
    public function checkName()
    {
        if (request()->isAjax()){
            $adminName = trim(input('param.name'));
            $status = 0;
            if (AdminModel::get(['name'=> $adminName])) {
                $status = 1;
            }
            return $status;
        }
    }

    //验证邮箱是否被占用
    public function checkEmail()
    {
        if (request()->isAjax()){
            $adminEmail = trim(input('param.email'));
            $status = 0;
            if (AdminModel::get(['email'=> $adminEmail])) {
                $status = 1;
            }
            return $status;
        }
    }

    //设置状态
    function setStatus(){
        $adminId = input('param.id');
        $res = AdminModel::get($adminId);
        $status = $res->status;  //使用该方法获取的值是模型获取器修改后的值
        $status = $res->getData('status');  //getData方法获取的数据的原始值，即存放在数据库里的值
        if ($status  == 1){
            AdminModel::update(['status'=>0],['id'=> $adminId]);
        }else{
            AdminModel::update(['status'=>1],['id'=> $adminId]);
        }
    }

    //恢复软删除数据
    function unDelete()
    {
        AdminModel::update(['delete_time'=>NULL,'is_delete'=>0],['is_delete'=>1]);
        //echo AdminModel::getLastSql();  //查看使用静态方法的sql语句
    }

    //软删除
    function adminDel()
    {
        if (request()->isAjax()){
            $adminId = input('param.id');
            if (AdminModel::get($adminId)){
                AdminModel::update(['is_delete'=>1],['id'=> $adminId]);
                AdminModel::destroy($adminId);
            }else{
                return 0;
            }
        }
    }
}
