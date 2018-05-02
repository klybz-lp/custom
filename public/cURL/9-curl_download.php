<?php
header('content-type:text/html;charset=utf-8');
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/PHPAdvance/cURL.rar");  //设置需要下载的文件的地址
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$data=curl_exec($ch);
file_put_contents('uploads/cURL.rar', $data);
