<?php
header('content-type:text/html;charset=utf-8');
//模拟表单发送数据
//数组形式发送数据
// $data=array(
//     'username'=>'king',
//     'password'=>'king',
//     'email'=>'382771946@qq.com'
// );
// $data=json_encode($data);
//字符串形式发送数据
$data="username=king&password=king&email=382771946@qq.com";
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'http://www.liping768.com/cURL/doAction1.php');  //处理发送数据的页面
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POST,1);
// curl_setopt($ch,CURLOPT_POSTFIELDS,array('userInfo'=>$data));
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$res=curl_exec($ch);
curl_close($ch);
echo $res;
