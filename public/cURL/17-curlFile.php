<?php
//php5.5推荐文件上传方法
header('content-type:text/html;charset=utf-8');
$curlFile=curl_file_create('G:\4.jpg','image/jpeg','test4.jpg');  //第三个参数是保存的文件名
$data=array('file'=>$curlFile);
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'http://localhost/PHPAdvance/cURL/doUpload.php');
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
curl_exec($ch);
curl_close($ch);
