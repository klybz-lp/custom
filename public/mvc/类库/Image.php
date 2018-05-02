<?php
/**
* 图片处理类
*/

$image = new Image();
$image->water('2.jpg','1.png',5);   //添加水印
class Image
{
	protected $path; //图片路径 
	protected $isRandName; //是否启用随机名字 
	protected $type; //保存的图片类型

	function __construct($path='./', $isRandName=true, $type='png')
	{
		$this->path 		= $path;
		$this->isRandName 	= $isRandName;
		$this->type 		= $type;
	}

	//水印方法,参数分别表示原图的全路径，水印图片的全路径，水印位置，水印透明度，保存后的图片前缀
	public function water($image, $water, $position = 9, $tmd = 90, $prefix='water_')
	{
		//判断原图与水印图片是否存在
		if ((!file_exists($image))||(!file_exists($water))) {
			die('图片资源不存在');
		}
		//获取原图与水印图片的宽高
		$imageInfo = self::getImageInfo($image);
		$waterInfo = self::getImageInfo($water);
		//判断水印图片是否能贴在原图上（水印图片的尺寸需小于原图）
		if (($waterInfo['width'] > $imageInfo['width'])||($waterInfo['height'] > $imageInfo['height'])) {
			die('水印图片尺寸不得大于原图');
		}
		//根据图片后缀来确定打开图片的方法
		$imageRes = self::openImage($image);
		$waterRes = self::openImage($water);
		//根据水印位置计算水印图片的坐标,1-9代表9宫格，0代表随机位置
		$pos = $this->getPosition($position, $imageInfo, $waterInfo);
		//将水印图片贴上原图
		imagecopymerge($imageRes, $waterRes, $pos['x'], $pos['y'], 0, 0, $imageInfo['width'], $imageInfo['height'], $tmd);
		//得到要保存的图片名称
		$newName = $this->createNewName($image, $prefix);
		//得到图片的保存全路径
		$newPath = rtrim($this->path,'/').'/'.$newName;
		//根据图片类型保存图片
		$this->saveImage($imageRes, $newPath);
		//销毁资源
		imagedestroy($imageRes);
		imagedestroy($waterRes);

		return $newPath;
	}

	//缩放方法
	public function suofang($image, $width, $height, $prefix='sf_')
	{
		//得到原图片的宽高
		//
	}

	//获取图片信息方法，如宽、高、类型等
	static function getImageInfo($imagePath)
	{
		$info = getimagesize($imagePath);
		$data['width'] 	= $info[0];
		$data['height'] = $info[1];
		$data['mime']	= $info['mime'];
		return $data;
	}

	//根据图片后缀或mime类型来确定打开图片的方法
	static function openImage($imagePath)
	{
		$mime = self::getImageInfo($imagePath)['mime'];
		switch ($mime) {
			case 'image/png':
				$image = imagecreatefrompng($imagePath);
				break;
			case 'image/gif':
				$image = imagecreatefromgif($imagePath);
				break;
			case 'image/jpeg':
				$image = imagecreatefromjpeg($imagePath);
				break;
			case 'image/wbmp':
				$image = imagecreatefromwbmp($imagePath);
				break;		
			
		}
		return $image;  //返回打开的图像资源
	}

	//计算水印图片的坐标
	protected function getPosition($position, $imageInfo, $waterInfo)
	{
		switch ($position) {
			case 1:
				$x = 0;
				$y = 0;
				break;
			case 2:
				$x = ($imageInfo['width'] - $waterInfo['width']) / 2;
				$y = 0;
				break;
			case 3:
				$x = $imageInfo['width'] - $waterInfo['width'];
				$y = 0;
				break;
			case 4:
				$x = 0;
				$y = ($imageInfo['height'] - $waterInfo['height']) / 2;
				break;
			case 5:
				$x = ($imageInfo['width'] - $waterInfo['width']) / 2;
				$y = ($imageInfo['height'] - $waterInfo['height']) / 2;
				break;
			case 6:
				$x = $imageInfo['width'] - $waterInfo['width'];
				$y = ($imageInfo['height'] - $waterInfo['height']) / 2;
				break;
			case 7:
				$x = 0;
				$y = $imageInfo['height'] - $waterInfo['height'];
				break;
			case 8:
				$x = ($imageInfo['width'] - $waterInfo['width']) / 2;
				$y = $imageInfo['height'] - $waterInfo['height'];
				break;
			case 9:
				$x = $imageInfo['width'] - $waterInfo['width'];
				$y = $imageInfo['height'] - $waterInfo['height'];
				break;
			case 0:
				$x = mt_rand(0, $imageInfo['width'] - $waterInfo['width']);
				$y = mt_rand(0, $imageInfo['height'] - $waterInfo['height']);
				break;	
		}
		return ['x'=>$x, 'y'=>$y];
	}

	//根据图片类型选择保存图片的方法
	function saveImage($imageRes, $newPath)
	{
		$func = 'image'.$this->type;  //变量函数，如果图片类型是PNG则是imagepng()
		$func($imageRes, $newPath);
	}

    //得到新文件名方法
    protected function createNewName($imagePath, $prefix)
    {
    	if ($this->isRandName) {
    		$name = $prefix.uniqid().'.'.$this->type;
    	} else {
    		$name = $prefix.pathinfo($imagePath)['filename'].'.'.$this->type;
    	}
    	return $name;
    }

}
?>
