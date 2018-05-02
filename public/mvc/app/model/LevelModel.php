<?php
    /**
    * 注意本类中的方法名与属性名不要与父类重复，除非需要覆盖或重写
    */
    namespace model;
    class LevelModel extends Model
    {
    	protected $model;
    	
    	function __construct()
    	{
    		parent::__construct();
    	}

    	function show()
    	{
		    $data = $this->field('id, levelname, rank')->order('id desc')->where('id>1')->limit('0,5')->select();
		    echo $this->sql;
		    return $data;
    	}
    }
?>
