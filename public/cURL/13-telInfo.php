<?php
header('content-type:text/html;charset=utf-8');
//请求号码归属地查询接口API
$phone='18635579617';
$ch = curl_init();
$url = 'http://apis.baidu.com/apistore/mobilenumber/mobilenumber?phone='.$phone;
$header = array(
    'apikey: f1f70f89078d7af7e436e1b59ed10dc4',
);
// 添加apikey到header
curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 执行HTTP请求
curl_setopt($ch , CURLOPT_URL , $url);
$res = curl_exec($ch);

var_dump(json_decode($res));
