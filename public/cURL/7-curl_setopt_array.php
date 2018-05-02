<?php
header('content-type:text/html;charset=utf-8');
$ch=curl_init();
//支持数组形式批量设置参数
$options=array(
    CURLOPT_URL=>'http://phpfamily.org',
    CURLOPT_RETURNTRANSFER=>1
);
curl_setopt_array($ch, $options);
$res=curl_exec($ch);
curl_close($ch);
echo $res;
