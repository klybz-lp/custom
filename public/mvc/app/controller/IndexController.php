<?php

	namespace controller;  //根据路径定义命名空间
	//use model\Model;   //引入模型基类
	use model\AdminModel;   //引入admin模型
	use model\LevelModel;   //引入admin模型
	use phpexcel\PHPExcel;   //引入phpexcel库

	class IndexController extends Controller
	{
		

		function index()
		{
			$this->display();  //等价于display('index.html')
		}

		function lst()
		{
			$this->display();
		}

		function db()
		{
            //直接在控制器操作数据库
			/*$m = new AdminModel();
		    $data = $m->field('id,name, email count')->order('id desc')->where('id>39')->limit('0,5')->select();
		    echo $m->sql.'<br />';
		    var_dump($data);*/
		    /*$data = ['name'=>'lpd', 'pwd'=>'123321', 'level'=>'1', 'email'=>'lpjsj768@126.com', 'regtime'=>'1495877582'];
		    echo $m->insert($data);*/
		    //echo $m->table('pp_admin')->delete('id=55');  //等价$m->table('pp_admin')->where('id=55')->delete()
		    /*$data = ['name'=>'lpdd', 'email'=>'l1pjsj768@126.com'];
		    echo $m->where('id=57')->update($data);*/
		    //echo $m->max('count');
		    //echo $m->count();
		    //var_dump($m->getByName('liping')); echo $m->sql;
		    
		    //通过模型类操作数据库
		    $m = new AdminModel();
		    var_dump($m->show());
            echo $m->sql.'<br />';
            
            //没有对应模型，可以直接实例化model基类，但必须使用tabel方法传入数据表名(带前缀)
            /*$m = new Model();
            $data = $m->table('pp_admin')->field('id,name, email count')->order('id desc')->where('id>39')->limit('0,5')->select();
		    echo $m->sql.'<br />';
		    var_dump($data);*/

		}
        //phpexcel操作
		function excel()
		{
			$excel = new PHPExcel();
			echo 1;
		}
	}
?>
