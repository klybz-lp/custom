<?PHP
include 'phpqrcode/phpqrcode.php';   //引入二维码生成库
QRcode::png('http://m.zeexin.com/');  //简单应用，传入一个参数，生成http://m.zeexin.com/的二维码

//直接使用phpqrcode类库里的weixin.php然后传入data参数，直接输入url即可生成二维码，显示的二维码尺寸可以在weixin.php里进行设置，生成的二维码图片同时保存在phpphpqrcode里的temp文件夹里，图片的名称是test与data值经过md5加密合并的字符串，推荐使用这种方法生成二维码，既能显示有能自动保存
//http://localhost/ewm/phpqrcode/weixin.php?data=liping  //扫码后显示的结果就是data的值，可以是url
//http://localhost/ewm/phpqrcode/weixin.php?data=http://m.zeexin.com  //注意url必须带http://  ，最好是先用js的encodeURIComponent对网址进行编码

/*************************************************************************************************************************************/

/*
hpqrcode.php提供了一个关键的png()方法，其中
参数$frame表示生成二位的的信息文本；
参数$filename表示是否输出二维码图片 文件，默认否；如果保存图片则在该页面不显示图片，在路径下找
参数$level表示容错率，也就是有被覆盖的区域还能识别，分别是 L（QR_ECLEVEL_L，7%），M（QR_ECLEVEL_M，15%），Q（QR_ECLEVEL_Q，25%），H（QR_ECLEVEL_H，30%）； 
参数$pixelPerPoint表示生成图片大小，默认是3；
参数$outerFrame表示二维码周围边框空白区域间距值；
参数$saveandprint表示是否保存二维码。

//phpqrcode.php生成png图片的方法
public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4,$saveandprint=FALSE) 
        {
            $image = self::image($frame, $pixelPerPoint, $outerFrame);
            
            if ($filename === false) {
                Header("Content-type: image/png");
                ImagePng($image);
            } else {
                if($saveandprint===TRUE){
                    ImagePng($image, $filename);
                    header("Content-type: image/png");
                    ImagePng($image);
                }else{
                    ImagePng($image, $filename);
                }
            }
            
            ImageDestroy($image);
        }  
*/

/*************************************************************************************************************************************/

//生成指定大小的二维码
/*
$frame = 'liping';  //扫描二维码后显示的内容，可以是网址
$filename = 'phpqrcode/temp/test'.time().'.png';  //保存的二维码图片路径，生成图片的话，该php页面就不会显示二维码图片
$errorCorrectionLevel = "L";  //容错率
$pixelPerPoint = "12";  //二维码图片尺寸
$outerFrame="1";  //二维码周围白边的大小
$saveandprint = 'true';  //是否保存二维码图片到本地
QRcode::png($frame, $filename, $errorCorrectionLevel , $pixelPerPoint,$outerFrame,$saveandprint);
*/

/*************************************************************************************************************************************/

//在生成的二维码中间加上logo图片，合成后的图片在页面显示，因为没有传入最后一个参数保存到本地
/*
    bool imagecopyresampled ( resource dstimage,resourcesrc_image , int dstx,intdst_y , int srcx,intsrc_y , int dstw,intdst_h , int srcw,intsrc_h )

    $dst_image：新建的图片
    $src_image：需要载入的图片
    $dst_x：设定需要载入的图片在新图中的x坐标
    $dst_y：设定需要载入的图片在新图中的y坐标
    $src_x：设定载入图片要载入的区域x坐标
    $src_y：设定载入图片要载入的区域y坐标
    $dst_w：设定载入的原图的宽度（在此设置缩放）
    $dst_h：设定载入的原图的高度（在此设置缩放）
    $src_w：原图要载入的宽度
    $src_h：原图要载入的高度
*/
/*
$value = 'http://m.zeexin.com';//二维码内容 
$filename = 'phpqrcode/temp/test'.time().'.png';
$errorCorrectionLevel = 'L';//容错级别
$matrixPointSize = 8;//生成图片大小

QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

//生成二维码图片
$logo = 'phpqrcode/temp/logo.png';//准备好的logo图片
$QR = $filename; //已经生成的原始二维码图

if ($logo !== FALSE) {
//imagecreatefromstring — 从字符串中的图像流新建一图像,成功则返回图像资源,file_get_contents() 把整个文件读入一个字符串中	
$QR = imagecreatefromstring(file_get_contents($QR));  
$logo = imagecreatefromstring(file_get_contents($logo));
$QR_width = imagesx($QR);//二维码图片宽度
$QR_height = imagesy($QR);//二维码图片高度
$logo_width = imagesx($logo);//logo图片宽度
$logo_height = imagesy($logo);//logo图片高度
$logo_qr_width = $QR_width / 5;  //logo在二维码图片上的宽度，表示是整个二维码宽度的五分之一
$scale = $logo_width/$logo_qr_width;  //表示logo显示出来后被缩小的倍数
$logo_qr_height = $logo_height/$scale;  //等比例缩小logo的高度
$from_width = ($QR_width - $logo_qr_width) / 2;  //logo在二维码上居中显示
//重新组合图片并调整大小，imagecopyresampled处理图片缩放
imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
$logo_qr_height, $logo_width, $logo_height);
}

//输出图片
Header("Content-type: image/png");
ImagePng($QR);  //只传入一个参数是在网页显示

//第二个参数是图片保存路径，不在网页输出，保存合成后的图片在指定目录，如果需要保存图片需要注释掉上面一行代码header...
//ImagePng($QR,"./temp/new.png");

*/

/*************************************************************************************************************************************/

//图片合并函数,一次3张图片合并，$imgs是图片信息数组,$row是根据id搜索出的顾客信息
function mergerImg($imgs,$row) {
 
 	
	list($max_width, $max_height) = getimagesize($imgs['dst']);
	$dests = imagecreatetruecolor($max_width, $max_height);  //根据邀请函图片的大小生成一个画布
	$dst_im = imagecreatefromjpeg($imgs['dst']);  //在画布上载入邀请函图片
	imagecopy($dests,$dst_im,0,0,0,0,$max_width,$max_height);  //函数用于拷贝图像或图像的一部分，imagecopy函数多次使用可以合并多张图片
	
	$src_im = imagecreatefrompng($imgs['src']);  //二维码图片
	$src_info = getimagesize($imgs['src']); //获取二维码图片的宽高
	//第一个参数是目标图像，第二个是被拷贝的源图像，第三四个参数目标图像开始 x与y 坐标，即把被拷贝的图片合并在目标图片的哪个位置，第五六个参数是拷贝图像开始 x与y 坐标，00表示从被拷贝图像的左上角开始复制，第七八个参数是拷贝的宽度与高度，等于被拷贝图片的宽高即把整个图片复制到目标图片上
	imagecopy($dests,$src_im,220,712,0,0,$src_info[0],$src_info[1]);  //把二维码图片合并在邀请函上
	
	
	$txt_im = imagecreatefrompng($imgs['text']);  //名称生成图片
	$txt_info = getimagesize($imgs['text']);  //获取名称生成图片的宽高
	
	//根据名称的字符长度来确定名称生成的图片放在邀请函上的位置x,utf-8一个中文字符占3个长度
	switch(strlen($row['name']))
	{
		case 3:
			$xposition = 120;
		break;
		case 6:
			$xposition = 110;
		break;
		case 9:
			$xposition = 100;
		break;
		case 12:
			$xposition = 80;
		break;
		default:
			$xposition = 70;
	}
	
	imagecopy($dests,$txt_im,$xposition,548,0,0,$txt_info[0],$txt_info[1]); //把名称生成的图片合在邀请函上
	
	//用来测试名称图片合并在邀请函上的位置，输出在页面，不进行保存
	Header("Content-type: image/png");
	imagejpeg($dests);
	
	//imagejpeg($dests,$imgs['save'],70);  //项目运行时，不直接显示图片，把合并后的图片保存下来，第三个可选参数是图片保存的质量
	
}

$imgs = array(
        'dst' => "D:/ecms/ewm/yqh.jpg",  //源图片即邀请函
        'src' => "D:/ecms/ewm/temp/new.png",  //需要载入的图片，根据url生成的二维码图片
		'save' => "D:/ecms/ewm/test.jpg",  //合成后的图片保存路径
		'text' => "D:/ecms/ewm/temp/cdcc38d00d0754ed5301d0cdbcef3977.png"  //添加的文字图片，即顾客名称生成的图片
		);
//$row实际是从数据库取出的数据
$row = array(
        'id' => "1",  //源图片即邀请函
        'name' => "李陪你过",  //顾客名称的长度
		);
//mergerImg($imgs,$row);
//echo strlen($row['name']);  //utf-8编码一个中文字符占三个长度

/*************************************************************************************************************************************/
//字符串生成图片函数
function createWord($str){
	
	
	if($str)
	{	
		$im = imagecreate(200,100);  //创建画布
		$white = imagecolorallocate($im,0x00,0x00,0x00);  //用来匹配图形的颜色
		imagecolortransparent($im,$white);  //本函数用来指定某色为透明背景
		$black = imagecolorallocate($im,0xEF,0xD9,0x9B);
		//写 TTF 文字到图中,第一个参数是画布，第二个参数是字体大小，第三个参数是旋转的角度，第四五个参数是原点为左上角的xy左边
		imagettftext($im,28,0,50,40,$black,"mnjh.ttf",$str);  //写 TTF 文字到图中
	
		imagepng($im,"temp/".md5($str).".png");
		
	}
}
//createWord('李陪你过');

/*************************************************************************************************************************************/

//用于对图片进行等比例缩放，因为没改缩放后图片的名称，所以会覆盖之前的图片

    function thumb($filename,$width=200,$height=200){
        //获取原图像$filename的宽度$width_orig和高度$height_orig
        list($width_orig,$height_orig) = getimagesize($filename);
        //根据参数$width和$height值，换算出等比例缩放的高度和宽度
        if ($width && ($width_orig<$height_orig)){
            $width = ($height/$height_orig)*$width_orig;
        }else{
            $height = ($width / $width_orig)*$height_orig;
        }
 
        //将原图缩放到这个新创建的图片资源中
        $image_p = imagecreatetruecolor($width, $height);
        //获取原图的图像资源
        $image = imagecreatefromjpeg($filename);
 
        //使用imagecopyresampled()函数进行缩放设置
        imagecopyresampled($image_p,$image,0,0,0,0,$width,$height,$width_orig,$height_orig);
 
        //将缩放后的图片$image_p保存，100(质量最佳，文件最大)
        imagejpeg($image_p,$filename);
 
        imagedestroy($image_p);
        imagedestroy($image);
    }
 
    //thumb("phpqrcode/temp/banner.png",100,100);
	
	
	

/*************************************************************************************************************************************/	
//给图片添加图片水印
/**
 * 图片加水印（适用于png/jpg/gif格式）
 * @param $srcImg 原图片
 * @param $waterImg 水印图片
 * @param $savepath 保存路径，添加水印后的路径跟名字都是null就可以覆盖之前的图片
 * @param $savename 保存名字
 * @param $positon 水印位置 
 * 1:顶部居左, 2:顶部居右, 3:居中, 4:底部局左, 5:底部居右 
 * @param $alpha 水印图片透明度 -- 0:完全透明, 100:完全不透明
 * 
 * @return 成功 -- 加水印后的新图片地址
 *          失败 -- -1:原文件不存在, -2:水印图片不存在, -3:原文件图像对象建立失败
 *          -4:水印文件图像对象建立失败 -5:加水印后的新图片保存失败
 */
 
function img_water_mark($srcImg, $waterImg, $savepath=null, $savename=null, $positon=5, $alpha=30)
{
    $temp = pathinfo($srcImg);
    $name = $temp['basename'];
    $path = $temp['dirname'];
    $exte = $temp['extension'];
    $savename = $savename ? $savename : $name;
    $savepath = $savepath ? $savepath : $path;
    $savefile = $savepath .'/'. $savename;
    $srcinfo = @getimagesize($srcImg);
    if (!$srcinfo) {
        return -1; //原文件不存在
    }
    $waterinfo = @getimagesize($waterImg);
    if (!$waterinfo) {
        return -2; //水印图片不存在
    }
    $srcImgObj = image_create_from_ext($srcImg);
    if (!$srcImgObj) {
        return -3; //原文件图像对象建立失败
    }
    $waterImgObj = image_create_from_ext($waterImg);
    if (!$waterImgObj) {
        return -4; //水印文件图像对象建立失败
    }
    switch ($positon) {
    //1顶部居左
    case 1: $x=$y=0; break;
    //2顶部居右
    case 2: $x = $srcinfo[0]-$waterinfo[0]; $y = 0; break;
    //3居中
    case 3: $x = ($srcinfo[0]-$waterinfo[0])/2; $y = ($srcinfo[1]-$waterinfo[1])/2; break;
    //4底部居左
    case 4: $x = 0; $y = $srcinfo[1]-$waterinfo[1]; break;
    //5底部居右
    case 5: $x = $srcinfo[0]-$waterinfo[0]; $y = $srcinfo[1]-$waterinfo[1]; break;
    default: $x=$y=0;
    }
    imagecopymerge($srcImgObj, $waterImgObj, $x, $y, 0, 0, $waterinfo[0], $waterinfo[1], $alpha);
    switch ($srcinfo[2]) {
    case 1: imagegif($srcImgObj, $savefile); break;
    case 2: imagejpeg($srcImgObj, $savefile); break;
    case 3: imagepng($srcImgObj, $savefile); break;
    default: return -5; //保存失败
    }
    imagedestroy($srcImgObj);
    imagedestroy($waterImgObj);
    return $savefile;
}
 
 
function image_create_from_ext($imgfile)
{
    $info = getimagesize($imgfile);
    $im = null;
    switch ($info[2]) {
    case 1: $im=imagecreatefromgif($imgfile); break;
    case 2: $im=imagecreatefromjpeg($imgfile); break;
    case 3: $im=imagecreatefrompng($imgfile); break;
    }
    return $im;
}

//img_water_mark('phpqrcode/temp/banner.png', 'phpqrcode/temp/logo.png', 'phpqrcode/temp/', 'b_logo.png', 3, $alpha=100)


?>
