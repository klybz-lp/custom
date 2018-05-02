<?php
header('content-type:text/html;charset=utf-8');
$ch1=curl_init();
curl_setopt($ch1,CURLOPT_URL,'http://phpfamily.org');
curl_setopt($ch1,CURLOPT_RETURNTRANSFER,1);

//复制句柄
$ch2=curl_copy_handle($ch1);
//重置选项
curl_reset($ch2);
curl_setopt($ch2,CURLOPT_URL,'http://phpfamily.org');
$res=curl_exec($ch2);
curl_close($ch1);
curl_close($ch2);
// echo $res;