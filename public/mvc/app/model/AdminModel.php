<?php
    /**
    * 注意本类中的方法名与属性名不要与父类重复，除非需要覆盖或重写
    */
    namespace model;
    class AdminModel extends Model
    {
    	protected $model;
    	
    	function __construct()
    	{

    		/*$this->tableName = 'member';  //重新设置数据表
    		$this->dbname = 'yaoyue';  //重新设置数据库名
    		$this->prefix = '';  //重新设置表前缀*/
    		parent::__construct();
    	}

    	function show()
    	{
		    $data = $this->field('id,name, email,count')->order('id desc')->where('id>39')->limit('0,5')->select();
		    //echo $this->sql.'<br />';
		    return $data;
    	}
        //调用yaoyue数据库
    	function show2()
    	{
		    $data = $this->field('id, name, tel')->order('id desc')->where('id>39')->limit('0,5')->select();
		    //echo $this->sql.'<br />';
		    return $data;
    	}
    }
?>
