<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//短信息发送函数
function PostTel($curlPost,$url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}


//字符串生成图片函数,把顾客的名称生成图片
function createWord($str,$id){


    if($str)
    {
        $im = imagecreate(200,100);
        $white = imagecolorallocate($im,0x00,0x00,0x00);
        imagecolortransparent($im,$white);  //本函数用来指定某色为透明背景
        $black = imagecolorallocate($im,0xEF,0xD9,0x9B);
        //写 TTF 文字到图中,第一个参数是画布，第二个参数是字体大小，第三个参数是旋转的角度，第四五个参数是原点为左上角的xy左边
        //exit(ROOT_PATH."public/static/font/mnjh.ttf");
	imagettftext($im,28,0,50,40,$black,ROOT_PATH."public/static/font/mnjh.TTF",$str);

        imagepng($im,ROOT_PATH."public/uploads/yqh/temp/".md5($str.$id).".png");  //生成图片

    }
}

//图片合并函数，$imgs是图片信息数组,$row是根据id搜索出的顾客信息
function mergerImg($imgs,$row) {
    /*print_r($row);
    print_r($imgs);
    exit;*/
    list($max_width, $max_height) = getimagesize($imgs['dst']);
    $dests = imagecreatetruecolor($max_width, $max_height);  //根据邀请函图片的大小生成一个画布
    $dst_im = imagecreatefromjpeg($imgs['dst']);  //在画布上载入邀请函图片
    imagecopy($dests,$dst_im,0,0,0,0,$max_width,$max_height); //函数用于拷贝图像或图像的一部分，imagecopy函数多次使用可以合并多张图片

    $src_im = imagecreatefrompng($imgs['src']);  //二维码图片
    $src_info = getimagesize($imgs['src']);  //获取二维码图片的宽高
    //第一个参数是目标图像，第二个是被拷贝的源图像，第三四个参数目标图像开始 x与y 坐标，即把被拷贝的图片合并在目标图片的哪个位置，第五六个参数是拷贝图像开始 x与y 坐标，00表示从被拷贝图像的左上角开始复制，第七八个参数是拷贝的宽度与高度，等于被拷贝图片的宽高即把整个图片复制到目标图片上
    imagecopy($dests,$src_im,220,732,0,0,$src_info[0],$src_info[1]);  //把二维码图片合并在邀请函上，前面两个参数表示在原图片的xy位置处开始拷贝


    createWord($imgs['text'],$row['id']);  //将顾客的名称生成图片

    $filetxt = ROOT_PATH."public/uploads/yqh/temp/".md5($row['name'].$row['id']).".png";  //顾客名称生成的图片
    $txt_im = imagecreatefrompng($filetxt);
    $txt_info = getimagesize($filetxt);

    //根据名称的字符长度来确定名称生成的图片放在邀请函上的位置x,utf-8使用strlen一个中文字符占3个长度
    switch(strlen($row['name']))
    {
        case 3:
            $xposition = 130;
            break;
        case 6:
            $xposition = 115;
            break;
        case 9:
            $xposition = 95;
            break;
        case 12:
            $xposition = 80;
            break;
        default:
            $xposition = 140;
    }

    imagecopy($dests,$txt_im,$xposition,550,0,0,$txt_info[0],$txt_info[1]);  //把名称生成的图片合在邀请函上

    /*
    //用来测试名称图片合并在邀请函上的位置，输出在页面，不进行保存
    Header("Content-type: image/png");
    imagejpeg($dests);
    */

    imagejpeg($dests,$imgs['save'],70); //把合并后的图片保存下来，第三个可选参数是图片保存的质量

}

 /**
 
 * 字符截取 支持UTF8/GBK

 * @param $string    要截取的字符串

 * @param $length    截取长度

 * @param $dot    超出显示省略字符
 
 *原生：$str = str_cut($str,300,’…’); echo $str;
 *tp5：{:str_cut($content,300,’…’)}
 */
/*
function str_cut($string, $length, $dot = '...') {

	$encode = mb_detect_encoding('哈哈', array(

		'ASCII',

		'UTF-8',

		'GB2312',

		'GBK',

		'BIG5'

	));

	$string = strip_tags($string);

	$strlen = strlen($string);

	if ($strlen <= $length) {

		return $string;

	}

	$string = str_replace(array(

							  ' ',

							  '&nbsp;',

							  '&amp;',

							  '&quot;',

							  ''',

							  '&ldquo;',

							  '&rdquo;',

							  '&mdash;',

							  '&lt;',

							  '&gt;',

							  '&middot;',

							  '&hellip;'

						  ), array(

							  '∵',

							  ' ',

							  '&',

							  '"',

							  "'",

							  '“',

							  '”',

							  '—',

							  '<',

							  '>',

							  '·',

							  '…'

						  ), $string);

	$strcut = '';

	if (strtolower($encode) == 'utf-8') {

		$length = intval($length - strlen($dot) - $length / 3);

		$n = $tn = $noc = 0;

		while ($n < strlen($string)) {

			$t = ord($string[$n]);

			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {

				$tn = 1;

				$n++;

				$noc++;

			} elseif (194 <= $t && $t <= 223) {

				$tn = 2;

				$n += 2;

				$noc += 2;

			} elseif (224 <= $t && $t <= 239) {

				$tn = 3;

				$n += 3;

				$noc += 2;

			} elseif (240 <= $t && $t <= 247) {

				$tn = 4;

				$n += 4;

				$noc += 2;

			} elseif (248 <= $t && $t <= 251) {

				$tn = 5;

				$n += 5;

				$noc += 2;

			} elseif ($t == 252 || $t == 253) {

				$tn = 6;

				$n += 6;

				$noc += 2;

			} else {

				$n++;

			}

			if ($noc >= $length) {

				break;

			}

		}

		if ($noc > $length) {

			$n -= $tn;

		}

		$strcut = substr($string, 0, $n);

		$strcut = str_replace(array(

								  '∵',

								  '&',

								  '"',

								  "'",

								  '“',

								  '”',

								  '—',

								  '<',

								  '>',

								  '·',

								  '…'

							  ), array(

								  ' ',

								  '&amp;',

								  '&quot;',

								  ''',

								  '&ldquo;',

								  '&rdquo;',

								  '&mdash;',

								  '&lt;',

								  '&gt;',

								  '&middot;',

								  '&hellip;'

							  ), $strcut);

	} else {

		$dotlen      = strlen($dot);

		$maxi        = $length - $dotlen - 1;

		$current_str = '';

		$search_arr  = array(

			'&',

			' ',

			'"',

			"'",

			'“',

			'”',

			'—',

			'<',

			'>',

			'·',

			'…',

			'∵'

		);

		$replace_arr = array(

			'&amp;',

			'&nbsp;',

			'&quot;',

			''',

			'&ldquo;',

			'&rdquo;',

			'&mdash;',

			'&lt;',

			'&gt;',

			'&middot;',

			'&hellip;',

			' '

		);

		$search_flip = array_flip($search_arr);

		for ($i = 0; $i < $maxi; $i++) {

			$current_str = ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];

			if (in_array($current_str, $search_arr)) {

				$key         = $search_flip[$current_str];

				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);

			}

			$strcut .= $current_str;

		}

	}

	return $strcut . $dot;

}*/
