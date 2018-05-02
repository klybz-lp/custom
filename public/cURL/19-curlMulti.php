<?php
//批处理，适合批量采集网页
header('content-type:text/html;charset=utf-8');
$ch1=curl_init();
curl_setopt($ch1,CURLOPT_URL,'http://www.maiziedu.com');
curl_setopt($ch1,CURLOPT_RETURNTRANSFER,1);

$ch2=curl_init();
curl_setopt($ch2,CURLOPT_URL,'http://phpfamily.org');
curl_setopt($ch2,CURLOPT_RETURNTRANSFER,1);

//得到批处理句柄
$mh=curl_multi_init();

//添加句柄到批处理句柄中
curl_multi_add_handle($mh, $ch1);
curl_multi_add_handle($mh, $ch2);

$still_running=null;
do{
    usleep(10000);  //隔1秒执行下一个页面抓取
    //运行当前 cURL 句柄的子连接
    curl_multi_exec($mh, $still_running);
}while($still_running);
//获取内容
$res1=curl_multi_getcontent($ch1);
$res2=curl_multi_getcontent($ch2);

//移除句柄
curl_multi_remove_handle($mh, $ch1);
curl_multi_remove_handle($mh, $ch2);

//关闭批句柄
curl_multi_close($mh);
curl_close($ch1);
curl_close($ch2);
echo $res1.'<hr color="red"/>';
echo $res2;












