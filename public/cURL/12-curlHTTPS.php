<?php
header('content-type:text/html;charset=utf-8');
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://github.com/');
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);//跳过https的证书检查，不然抓取不到数据
curl_exec($ch);
curl_close($ch);
