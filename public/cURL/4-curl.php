<?php
header('content-type:text/html;charset=utf-8');
//1.初始化
$ch=curl_init();

//2.设置选项
curl_setopt($ch,CURLOPT_URL,'http://www.phpfamily.org/index.html');
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//返回结果不直接输出

//3.执行cURL
$res=curl_exec($ch);
$res=str_ireplace('king','麦子',$res);  //替换字符串，str_ireplace表示不区分大小写替换
echo $res;

//4.关闭
curl_close($ch);
