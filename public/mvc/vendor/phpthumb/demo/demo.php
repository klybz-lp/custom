<?php

require_once '../ThumbLib.inc.php';

$thumb = PhpThumbFactory::create('srcpic/small.jpg');  //引入原图,要处理的图片的地址可以是网络地址，也可以是本地地址


//方法都在GdThumb.inc.php文件
//$thumb->resize(1400, 400);  //会按传入的宽度或者高度中较小的的值进行等比例缩放，当只输入一个参数的时候，是限制最宽的尺寸
//$thumb->resizePercent(50); //把图片等比缩小到原来的百分数，比如50就是原来的50%。即宽高都是原来的一般，等比跟等比例有区别 
//$thumb->adaptiveResize(500, 375); //截取一个500px * 375px的图片，注意这个是截取，先按较小的参数值进行等比例缩放，超出的部分直接裁切掉，适合做有固定大小容器里的图片，如文章列表缩略图，生成后的图片大小就是这个指定的尺寸。
//$thumb->cropFromCenter(400, 400);  //从图片的中心计算，截取400px * 400px的图片。
//$thumb->crop(100, 100, 300, 200);  //截图，前两个参数分别是需要解出的图片的右上角的坐标X,Y。 后面两个参数是需要解出的图片宽，高。
//$thumb->rotateImageNDegrees(280);  //把图片顺时针反转180度，可以传入任意角度为参数
//$thumb->rotateImage(-90);   //默认参数是90，把图片逆时针反转90度，传入参数-90则顺时针翻转90度，传入其他参数没意义
// $options = array 
// 			(
// 				'resizeUp'				=> false,
// 				'jpegQuality'			=> 100,  //图片质量,0-100
// 				'correctPermissions'	=> false,
// 				'preserveAlpha'			=> true,
// 				'alphaMaskColor'		=> array (255, 255, 255),
// 				'preserveTransparency'	=> true,
// 				'transparencyMaskColor'	=> array (0, 0, 0)
// 			);
//$thumb->setOptions($options);  //参数是一个数组 
$thumb->setWorkingImage(10);


$thumb->show();  //在页面显示图片
$thumb->save( './c.jpg ' ); //保存图片

?>
