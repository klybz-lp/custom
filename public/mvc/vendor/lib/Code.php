<?php

    //验证码类
    /**
    * 
    */
    namespace vendor;
    class Code
    {

    	protected $number;       //验证码字符个数
    	protected $codeType;     //验证码类型
    	protected $width;        //验证码宽度
    	protected $height;       //验证码高度
    	protected $image;        //图像资源
    	protected $code;         //验证码字符串
    	
    	function __construct($number=4, $codeType=2, $width=100, $height=30)
    	{
    		//初始化成员属性
    		$this->number     = $number;
    		$this->codeType   = $codeType;
    		$this->width      = $width;
    		$this->height     = $height;

    		//生成验证码
    		$this->code = $this->createCode();
    		//echo $this->code;
    	}

    	//销毁图像资源
    	public function __destruct()
    	{
    		imagedestroy($this->image);
    	}

    	//获取验证码
    	public function __get($name)
    	{
    		if ($name == 'code') {
    			return $this->code;
    		}
    		return false;
    	}

    	//生成验证码类
    	protected function createCode()
    	{
    		//根据类型生成不同的验证码
    		switch ($this->codeType) {
    			case 0:   //纯数字
    				$code = $this->getNumberCode();
    				break;
    			case 1:   //纯字母
    				$code = $this->getCharCode();
    				break;
    			case 2:   //数字与字母混合
    				$code = $this->getNumCharCode();
    				break;	
    			default:
    				$code = $this->getNumberCode();
    				break;
    		}
    		return $code;
    	}

    	protected function getNumberCode()
    	{
    		$num = join('', range(0, 9));  //range生成一个0到9的数组，然后使用空格拼接成字符串
    		return substr(str_shuffle($num), 0, $this->number);  //str_shuffle随机打乱一个字符串
    	}

    	protected function getCharCode()
    	{
    		$str = join('', range('a', 'z')); 
    		$str = $str.strtoupper($str);
    		return substr(str_shuffle($str), 0, $this->number);  
    	}

    	protected function getNumCharCode()
    	{
    		$num = join('', range(0, 9));
    		$str = join('', range('a', 'z')); 
    		$str = $num.$str.strtoupper($str);
    		return substr(str_shuffle($str), 0, $this->number); 
    	}

    	//输出验证码图片
    	public function outImage()
    	{
    		$this->createImage();  //创建画布
    		$this->fillBack();  //填充背景色
    		$this->drawChar();  //将验证码字符串画到画布中
    		$this->drawDisturb();  //添加干扰元素
    		$this->showImage();  //输出并显示
    	}
        //创建画布
    	protected function createImage ()
    	{
    		$this->image = imagecreatetruecolor($this->width, $this->height);
    	}
        //填充背景色
    	protected function fillBack ()
    	{
    		imagefill($this->image, 0, 0, $this->lightColor());
    	}
    	//往画布画字符串
    	protected function drawChar ()
    	{
    		$width = ceil($this->width/$this->number);  //每个字符可使用的宽度
    		for ($i=0; $i < $this->number; $i++) { 
    			$x = mt_rand($i*$width+5, ($i+1)*$width-10);  //每个字符x方向的位置
    			$y = mt_rand(5, $this->height-15);  //每个字符y方向的位置
    			imagechar($this->image, 5, $x, $y, $this->code[$i], $this->darkColor());
    		}
    	}
    	//画干扰元素,画点
    	protected function drawDisturb ()
    	{
    		for ($i=0; $i < 150; $i++) { 
    			$x = mt_rand(0, $this->width);
    			$y = mt_rand(0, $this->height);
    			imagesetpixel($this->image, $x, $y, $this->lightColor());
    		}
    	}
    	//输出验证码
    	protected function showImage ()
    	{
    		header('Content-type:image/png');
    		imagepng($this->image);
    	}

    	//生成浅色
    	protected function lightColor()
    	{
    		return imagecolorallocate($this->image, mt_rand(130,255), mt_rand(130,255), mt_rand(130,255));
    	}

    	//生成深色
    	protected function darkColor()
    	{
    		return imagecolorallocate($this->image, mt_rand(0,129), mt_rand(0,129), mt_rand(0,129));
    	}


    }

?>
