<?php
    /**
    * 上传类
    */
    namespace vendor;
    class Upload
    {
    	protected $path = './upload/';  //文件上传保存路径
    	protected $allowSuffix = array('jpg', 'jpeg', 'png', 'gif', 'wpmb');  //允许上传的文件类型
    	protected $allowMime = array('image/jpeg', 'image/png', 'image/gif', 'image/wpmb');  //允许上传的mime类型
    	protected $maxSize = 2000000;  // 允许上传的文件大小
    	protected $isRandName = true;  //是否重命名上传的文件
    	protected $prefix = 'up_';  //文件上传后的命名前缀
    	protected $errorNumber;  //自定义的错误号
    	protected $errorInfo;  //自定义的错误信息

    	protected $oldName;   //原文件名
    	protected $suffix;  //原文件后缀
    	protected $size;  //原文件大小
    	protected $mime;  //原文件大小mime类型
    	protected $tmpName;  //临时文件路径

    	protected $newName;   //保存后的文件名称

    	function __construct($arr=[])
    	{
    		foreach ($arr as $key => $value) {
    			$this->setOption($key, $value);  
    		}
    	}

    	//初始化成员属性
    	protected function setOption($key, $value)
    	{
    		$keys = array_keys(get_class_vars(__CLASS__));  //得到本类的所有成员属性的属性名 

    		//判断$key是否是成员属性
    		if (in_array($key, $keys)) {
    			$this->$key = $value;
    		}
    	}

    	//文件上传方法，$name是上传文件input的name属性值
    	public function uploadFile($name)
    	{
    		//判断是否设置上传路径
    		if (empty($this->path)) {
    			$this->setOption('errorNumber', -1); //自定义错误号
    			return false;
    		}
    		//判断上传路径是否存在，是否可写
    		if (!$this->check()) {
    			$this->setOption('errorNumber', -2); 
    			return false;
    		}
    		//判断文件上传是否出错，如果为0则表示上传成功
    		$error = $_FILES[$name]['error'];
    		if ($error) {
    			$this->setOption('errorNumber', $error); //如果上传失败，保存系统错误号
    			return false;
    		} else {  //如果上传成功，提取文件信息保存到对应的成员属性中
    			$this->getFilesInfo($name);
    		}
    		
    		//判断文件大小、后缀、mime是否合法
    		if ($this->size > $this->maxSize) {
    			$this->setOption('errorNumber', -3); 
    			return false;
    		}
    		if (!in_array($this->mime, $this->allowMime)) {
    			$this->setOption('errorNumber', -4); 
    			return false;
    		}
    		if (!in_array($this->suffix, $this->allowSuffix)) {
    			echo 1;
    			$this->setOption('errorNumber', -5); 
    			return false;
    		}
    		//判断是否启动随机名，得到新的文件名 
    		$this->newName = $this->createNewName();
    		//判断是否是上传文件，移动文件并保存
    		if (!is_uploaded_file($this->tmpName)) {
    			$this->setOption('errorNumber', -6); 
    			return false;
    		} else {
    			if (move_uploaded_file($this->tmpName, $this->path.$this->newName)) {
    				return $this->path.$this->newName;  //文件移动成功则返回移动后的路径
    			} else {
    				$this->setOption('errorNumber', -7); 
    			    return false;
    			}
    			
    		}
    	}

    	//检测上传目录
    	protected function check()
    	{
    		//如果文件不存在或不是目录则新建该目录
            if (!file_exists($this->path) || !is_dir($this->path)) {
                return mkdir($this->path,0777,true);  //true表示支持递归创建
            }
            //判断目录是否有读写权限
            if (!is_writable($this->path)) {
                return chmod($this->path, 0777);
            }
            return true;
    	}

    	//提取上传文件的信息
    	protected function getFilesInfo($name)
    	{
    		$this->oldName = $_FILES[$name]['name'];  //文件名
    		$this->mime = $_FILES[$name]['type'];  //文件名
    		$this->tmpName = $_FILES[$name]['tmp_name'];  //文件临时路径
    		$this->size = $_FILES[$name]['size'];  //文件大小
    		$this->suffix = pathinfo($this->oldName)['extension'];  //文件后缀

    	}

    	//设置上传后的文件名
    	protected function createNewName()
    	{
    		if ($this->isRandName) {
    			$name = $this->prefix.uniqid().'.'.$this->suffix;
    		} else {
    			$name = $this->prefix.'.'.$this->oldName;
    		}
    		return $name;
    		
    	}

    	//输出错误号与错误信息
	    public function __get($name)
	    {
	    	if ($name == 'errorNumber') {
	    		return $this->errorNumber;
	    	} elseif ($name == 'errorInfo') {
	    		return $this->getErrorInfo();
	    	}
	    }

	    //获取错误信息
	    protected function getErrorInfo()
	    {
	    	switch ($this->errorNumber) {
	    		case -1:
	    			$str = '没有设置上传路径';
	    			break;
	    		case -2:
	    			$str = '上传路径不是目录或没有权限';
	    			break;
	    		case -3:
	    			$str = '文件大小超过指定范围';
	    			break;
	    		case -4:
	    			$str = '文件mime类型不合法';
	    			break;
	    		case -5:
	    			$str = '文件后缀不合法';
	    			break;
	    		case -6:
	    			$str = '不是上传文件';
	    			break;
	    		case -7:
	    			$str = '文件移动失败';
	    			break;		
	    		case 1:
	    			$str = '文件超出php.ini设置的大小';
	    			break;	
	    		case 2:
	    			$str = '文件超出HTML表单设置的大小';
	    			break;	
	    		case 3:
	    			$str = '文件部分上传';
	    			break;	
	    		case 4:
	    			$str = '没有文件上传';
	    			break;	
	    		case 6:
	    			$str = '临时文件不存在';
	    			break;	
	    		case 7:
	    			$str = '文件写入失败';
	    			break;		
	    	}
	    	return $str;
	    }


    }

    
?>
