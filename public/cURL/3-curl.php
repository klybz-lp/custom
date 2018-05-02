<?php
//初始化
$url="http://phpfamily.org/index.html";
$ch=curl_init($url);
// var_dump($ch);
//设置相关选项

//执行并获取结果
curl_exec($ch);

//释放资源
curl_close($ch);