<?php
header('content-type:text/html;charset=utf-8');
$ch=curl_init();
$url="https://s.taobao.com/search?q=a%2Bb+c+%2B+d&imgfile=&commend=all&ssid=s5-e&search_type=item&sourceId=tb.index&spm=a21bo.50862.201856-taobao-item.1&ie=utf8&initiative_id=tbindexz_20160405";
$url="http://search.jd.com/Search?keyword=a%2Bb%20%20c%20%2B%20d&enc=utf-8&wq=a%2Bb%20%20c%20%2B%20d&pvid=2g4o3nmi.m1gw83&test=hello+world";
echo curl_unescape($ch, $url);
echo '<hr/>';
echo urldecode($url);