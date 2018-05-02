<?php
header('content-type:text/html;charset=utf-8');
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'http://phpfamily123.org/index.html');
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$res=curl_exec($ch);
//判断内容是否获取成功，===避免获取的内容为0或空时报错
// if(false===$res){
//     echo "cURL Error ".curl_error($ch);
//     exit;
// }
if($errno=curl_errno($ch)){  //错误号
//     echo curl_error($ch);exit;
       echo curl_strerror($errno);exit;
}
$info=curl_getinfo($ch);  //获取采集的内容的相关信息，如文件大小、编码、采集花费的时间等
print_r($info);
curl_close($ch);
