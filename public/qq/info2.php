<?php
	header("Content-type: text/html; charset=utf-8");
	header("Access-Control-Allow-Origin: *"); //利用Access-Control-Allow-Origin响应头解决跨域请求
	
	//if(isset($_POST['item']) && $_POST['item'] == 'talkinfo'){
		
		include "database.php";
		$link = new database(); 
		$res = $link->query("select * from info");

		$json = '';
		while($r = $link->fetch_array($res)){
			$r["date_time"] = format($r["date_time"])."<br>";
			$json .= json_encode($r, JSON_UNESCAPED_UNICODE).',';  //将php数组转成json字符串,JSON_UNESCAPED_UNICODE解决中文变成\dfa6u\这样的问题，需php>=5.4,低版本可以
		}
		exit('['.substr($json, 0 , strlen($json) - 1).']');  //去掉最后一个,再输出，加个[]变成数组形式才能给js使用

	//} else {
	//	exit('deny');
	//}
	//时间转换	
	function format($time) {

			//时间解析
			$formatTime = time() - $time;
			if ($formatTime < 60) {
				$formatTime = '刚刚';
			} else if ($formatTime < 60 * 60) {
				//$formatTime = floor($formatTime / 60).'分钟之前';
				$formatTime = date('m-d', $time);
			} else if (date('Y-m-d') == date('Y-m-d', $time)) {
				//$formatTime = '今天'.date('H:i', $time);
				$formatTime = '今天';
			} else if (date("Y-m-d",strtotime("-1 day")) == date('Y-m-d',$time)) {
				//$formatTime = '昨天'.date('H:i', $time);
				$formatTime = '昨天';
			} else if (date('Y') == date('Y', $time)) {
				//$formatTime = date('m月d日 H:i', $time);
				$formatTime = date('m-d', $time);
			} else {
				//$formatTime = date('Y年m月d日 H:i', $time);
				$formatTime = date('m-d', $time);
			}
			return $formatTime;

	}
	
?>
