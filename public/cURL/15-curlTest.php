<?php
header('content-type:text/html;charset=utf-8');
$username='king + a & b - _ g % !';
$ch=curl_init();
// $username=urlencode($username);
$username=curl_escape($ch, $username);  //对带特殊字符的内容进行编码，避免获取不到特殊字符
curl_setopt($ch,CURLOPT_URL,'http://localhost/PHPAdvance/cURL/doAction2.php?username='.$username);
curl_exec($ch);
curl_close($ch);
