<?php
//初始化
$ch=curl_init();
// var_dump($ch);
//设置相关选项
curl_setopt($ch, CURLOPT_URL, 'http://phpfamily.org/index.html');

//执行并获取结果
curl_exec($ch);

//释放资源
curl_close($ch);