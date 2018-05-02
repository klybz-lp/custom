<?php

	/**
	* 模型基类
	*/
    include 'config/config.php';
    $m = new Model($config);
    //$data = $m->table('admin')->field('id,name, email count')->order('id desc')->where('id>39')->limit('0,5')->select();
    //echo $m->sql;
    //var_dump($data);
    /*$data = ['name'=>'lpd', 'pwd'=>'123321', 'level'=>'1', 'email'=>'lpjsj768@126.com', 'regtime'=>'1495877582'];
    echo $m->table('pp_admin')->insert($data);*/
    //echo $m->table('pp_admin')->delete('id=55');  //等价$m->table('pp_admin')->where('id=55')->delete()
    /*$data = ['name'=>'lpd', 'email'=>'lpjsj768@126.com'];
    echo $m->table('pp_admin')->where('id=55')->update($data);*/
    //echo $m->table('pp_admin')->max('count');
    //echo $m->table('pp_admin')->count();
    //var_dump($m->table('pp_admin')->getByName('liping')); echo $m->sql;
	class Model
	{
		protected $host;  //主机名
		protected $user;  //用户名
		protected $pwd;  //密码
		protected $dbname;  //数据库名
		protected $charset;  //字符集
		protected $prefix;  //表前缀
		
		protected $link;  //数据库连接资源
		protected $tableName;  //数据表名，可自定义表名
		protected $sql;  //sql语句
		protected $options;  //操作数组，存放的是所有的查询条件，实现无序连缀操作效果
		
		//初始化成员变量
		function __construct($config)
		{
			$this->host 	= $config['DB_HOST'];
			$this->user 	= $config['DB_USER'];
			$this->pwd 		= $config['DB_PWD'];
			$this->dbname 	= $config['DB_NAME'];
			$this->charset 	= $config['DB_CHARSET'];
			$this->prefix 	= $config['DB_PERFIX'];

			//连接数据库
			$this->link =  $this->connect();

			//如果没通过table方法设置表名则通过类名与表名的对应关系获取，如user=>UserModel.php
			$this->tableName = $this->getTableName();

			//初始化$options操作数组
			$this->initOptions();
		}

		//数据库连接方法
		protected function connect()
		{
			$link = mysqli_connect($this->host, $this->user, $this->pwd);
			if (!$link) {
				die('数据库连接失败');
			}
			mysqli_select_db($link, $this->dbname);
			mysqli_set_charset($link, $this->charset);
			return $link;  //返回数据库连接资源
		}

		//获取表名
		protected function getTableName()
		{
			//判断是否设置表名的成员变量
			if (!empty($this->tableName)) {
				return $this->prefix.$this->tableName;
			}
			//通过类名来获取表名,如user=>UserModel.php
			$className = get_class($this);  //得到当前类名字符串
			$table = strtolower(substr($className, 0, -5));  //去掉字符串后面的Model得到表名
			return $this->prefix.$table;
		}

		//初始化操作数组
		protected function initOptions()
		{
			$arr = ['where', 'table', 'field', 'order', 'group', 'having', 'limit'];
			foreach ($arr as $value) {
				$this->options[$value] = '';  //初始化为空
				if ($value == 'table') {
					$this->options[$value] = $this->tableName;  //初始化表名
				} elseif ($value == 'field') {
					$this->options[$value] = '*';  //初始化field
				}
			}
		}

		//field方法
		public function field($field)
		{
			if (!empty($field)) {

				if (is_string($field)) {
					$this->options['field'] = $field; 
				} elseif (is_array($field)) {
					$this->options['field'] = join(',', $field);
				}
				
			}
			return $this;  //返回对象本身，实现方法的连缀
		}

		//table方法
		public function table($table)
		{
			if (!empty($table)) {

				$this->options['table'] = $table;
				
			}
			return $this;  
		}
		//where方法
		public function where($where)
		{
			if (!empty($where)) {

				$this->options['where'] = 'WHERE '.$where;
				
			}
			return $this;  
		}
		//group方法
		public function group($group)
		{
			if (!empty($group)) {

				$this->options['group'] = 'GROUP BY '.$group;
				
			}
			return $this;  
		}
		//having方法
		public function having($having)
		{
			if (!empty($having)) {

				$this->options['having'] = 'HAVING '.$having;
				
			}
			return $this;  
		}
		//order方法
		public function order($order)
		{
			if (!empty($order)) {

				$this->options['order'] = 'ORDER BY '.$order;
				
			}
			return $this;  
		}
		//limit方法
		public function limit($limit)
		{
			if (!empty($limit)) {

				if (is_string($limit)) {
					$this->options['limit'] = 'LIMIT '.$limit;
				}elseif (is_array($limit)) {
					$this->options['limit'] = 'LIMIT '.join(',', $limit);
				}
				
			}
			return $this;  
		}
		//select方法
		public function select()
		{
			$sql = 'SELECT %FIELD% FROM %TABLE% %WHERE% %GROUP% %HAVING% %ORDER% %LIMIT%';//预写带占位符的sql语句
			//替换占位符，组装sql语句
			$sql = str_replace(
								['%FIELD%', '%TABLE%', '%WHERE%', '%GROUP%', '%HAVING%', '%ORDER%', '%LIMIT%'], 
								[$this->options['field'], $this->options['table'], $this->options['where'], $this->options['group'], $this->options['having'], $this->options['order'], $this->options['limit']], 
								$sql);
			//保存sql语句，方便测试
			$this->sql = $sql;
			//执行sql语句
			return $this->query($sql);
		}

		//insert方法，$data是关联数组，键是字段名，值是字段值
		public function insert($data)
		{
			//处理字符串，如果value值是字符串时加上单引号或双引号
			$data = $this->parseValue($data);
			$keys = array_keys($data);  //提取数组键，即字段名
			$values = array_values($data);  //提取数组值，即字段值
			$sql = 'INSERT INTO %TABLE%(%FIELD%) VALUES(%VALUES%)';  //准备sql语句
			$sql = str_replace(
								['%TABLE%', '%FIELD%', '%VALUES%'], 
								[$this->options['table'], join(',',$keys), join(',',$values)], 
								$sql);
			$this->sql = $sql;
			//执行sql语句
			return $this->exec($sql, true);						
		}

		//update方法，$data是关联数组，键是字段名，值是字段值
		public function update($data)
		{
			$data = $this->parseValue($data);  //处理字符串
			//拼接数组为update语句需要的字符串格式
			foreach ($data as $key => $value) {
				$newData[] = $key.'='.$value;
			}
			$value = join(',', $newData);
			$sql = 'UPDATE %TABLE% SET %VALUE% %WHERE%';  //准备sql语句
			$sql = str_replace(
								['%TABLE%', '%VALUE%', '%WHERE%'], 
								[$this->options['table'], $value, $this->options['where']], 
								$sql);
			$this->sql = $sql;
			//执行sql语句
			return $this->exec($sql);						
		}

		//delete方法
		public function delete($where = '')
		{
			if (!empty($where)) {
				$this->options['where'] = 'where '.$where;
			}else{
				if (empty($this->options['where'])) {
				    die('where条件不得为空');
			    }
			}
			$sql = 'DELETE FROM %TABLE% %WHERE%';
			$sql = str_replace(
								['%TABLE%', '%WHERE%'], 
								[$this->options['table'], $this->options['where']], 
								$sql);
			$this->sql = $sql;
			//执行sql语句
			return $this->exec($sql);		
		}

		//count方法
		public function count($field = '')
		{
			
			$field = empty($field) ? '*' : $field;
			$result = $this->field('count('.$field.') as count')->select();
			return $result[0]['count'];
		}

		//max方法
		public function max($field)
		{
			$result = $this->field('max('.$field.') as max')->select();
			return $result[0]['max'];
		}

		//需要返回结果集的方法query，如select
		public function query($sql)
		{
			$this->initOptions();  //清空操作数组
			$result = mysqli_query($this->link, $sql);
			if ($result && mysqli_affected_rows($this->link)) {
				while ($r = mysqli_fetch_assoc($result)) {
					$data[] = $r;
				}
				return $data;
			}
		}

		//不需要返回结果集的方法exec，如insert、delete
		public function exec($sql, $isInsert = false)
		{
			$this->initOptions();  //清空操作数组
			$result = mysqli_query($this->link, $sql);
			if ($result && mysqli_affected_rows($this->link)) {
				if ($isInsert) {  //判断是否是插入语句
					return mysqli_insert_id($this->link);
				} else {
					return mysqli_affected_rows($this->link);
				}
			}
			return false;
			
		}

		//字符串处理，加上引号
		protected function parseValue($data)
		{
			foreach ($data as $key => $value) {
				if (is_string($value)) {
					$value = '"'.$value.'"';
				}
				$newData[$key] = $value; 
			}
			return $newData;
		}

		//不需要返回结果集的方法exec，如update、delete
		//获取指定的成员属性方法
		public function __get($name)
		{
			if ($name == 'sql') {
				return $this->sql;
			}
			return false;
		}

		//当调用类中不存在的方法时，会自动调用__call方法，实现getByName、getById等方法
		public function __call($name, $args)
		{
			$str = substr($name, 0, 5);
			$field = strtolower(substr($name, 5));
			if ($str == 'getBy') {
				$res = $this->where($field.'="'.$args[0].'"')->select();
				return $res[0];
			}
			return false;
		}

		//销毁数据库连接资源
		public function __desctruct()
		{
			mysqli_close($this->link);
		}
	}
?>
