<?php
header('content-type:text/html;charset=utf-8');
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.liping768.com/cURL/doUpload.php");  //设置出来文件上传的PHP页面
//注意上传的文件名前要加@,而且要使用绝对路径
$data=array('file'=>'@D:\web\yaoyue\public\cURL\myPic.jpg');
curl_setopt($ch,CURLOPT_POST,1);//设置post方式发送数据上传图片
curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//发送数据，数据形式两种字符串或数组，但是文件上传需要发送数组形式的数据，字符串形式如'name=liping&age=18'
curl_exec($ch);
curl_close($ch);
