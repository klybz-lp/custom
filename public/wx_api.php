<?php
/**
 * wechat php test
 */
//define your token，与公众号后台设置的保持一致
session_start();	
define("TOKEN", "liping768");
$wechatObj = new wechatCallbackapiTest();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
} elseif(isset($_GET['qrcode'])) {
    $wechatObj->getInsQRCode();
} else {
	$wechatObj->responseMsg();
}
//$wechatObj->createMenu();  //菜单生成一次即可，先改好菜单项，如果没生成则把改行代码防止第8行下面
class wechatCallbackapiTest {
    public function valid() {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }
    public function responseMsg() {
        //get post data, May be due to the different environments
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input"); 
        //extract post data
        if (!empty($postStr)) {
            $this->logger("R " . $postStr);
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
             the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch ($RX_TYPE) {
                case "event":
                    $result = $this->receiveEvent($postObj);
                break;
                case "text":
                    $result = $this->receiveText($postObj);
                break;
                case "image":
                    $result = $this->receiveImage($postObj);
                break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                break;
                case "link":
                    $result = $this->receiveLink($postObj);
                break;
                default:
                    $result = "unknow msg type: " . $RX_TYPE;
                break;
            }
            $this->logger("T " . $result);
            echo $result;
        } else {
            echo "";
            exit;
        }
    }
    //接收事件消息
    private function receiveEvent($object) {
        $content = "";
        switch ($object->Event) {
            case "subscribe":
                $content = "欢迎关注李平工作室 ";
                $content.= (!empty($object->EventKey)) ? ("\n来自二维码场景 " . str_replace("qrscene_", "", $object->EventKey)) : "";
            break;
            case "unsubscribe":
                $content = "取消关注";
            break;
            case "SCAN":
                $content = "扫描场景 " . $object->EventKey;
            break;
            case "CLICK":
                switch ($object->EventKey) {
                    case "COMPANY":
                        $content = "李平工作室提供互联网相关产品与服务。";
                    break;
                    default:
                        $content = "点击菜单：" . $object->EventKey;
                    break;
                }
            break;
            case "LOCATION":
                $content = "上传位置：纬度 " . $object->Latitude . ";经度 " . $object->Longitude;
            break;
            case "VIEW":
                $content = "跳转链接 " . $object->EventKey;
            break;
            default:
                $content = "receive a new event: " . $object->Event;
            break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }
    private function receiveImage($object) {
        $content = array("MediaId" => $object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }
    private function receiveLocation($object) {
        $content = "你发送的是位置，纬度为：" . $object->Location_X . "；经度为：" . $object->Location_Y . "；缩放级别为：" . $object->Scale . "；位置为：" . $object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }
    private function receiveVoice($object) {
        if (isset($object->Recognition) && !empty($object->Recognition)) {
            $content = "你刚才说的是：" . $object->Recognition;
            $result = $this->transmitText($object, $content);
        } else {
            $content = array("MediaId" => $object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }
        return $result;
    }
    private function receiveVideo($object) {
        $content = array("MediaId" => $object->MediaId, "ThumbMediaId" => $object->ThumbMediaId, "Title" => "", "Description" => "");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }
    private function transmitVoice($object, $voiceArray) {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId>
</Voice>";
        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
$item_str
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    private function transmitVideo($object, $videoArray) {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
</Video>";
        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
$item_str
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    private function transmitImage($object, $imageArray) {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";
        $item_str = sprintf($itemTpl, $imageArray['MediaId']);
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
$item_str
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    private function receiveLink($object) {
        $content = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }
    //接收文本消息
    private function receiveText($object) {
        switch ($object->Content) {
            case "文本":
                $content = "这是个文本消息";
            break;
            case "图文":
            case "单图文":
                $content = array();
                $content[] = array("Title" => "单图文标题", "Description" => "单图文内容", "PicUrl" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" => "http://m.cnblogs.com/?u=txw1958");
            break;
            case "多图文":
                $content = array();
                $content[] = array("Title" => "多图文1标题", "Description" => "", "PicUrl" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" => "http://m.cnblogs.com/?u=txw1958");
                $content[] = array("Title" => "多图文2标题", "Description" => "", "PicUrl" => "http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" => "http://m.cnblogs.com/?u=txw1958");
                $content[] = array("Title" => "多图文3标题", "Description" => "", "PicUrl" => "http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" => "http://m.cnblogs.com/?u=txw1958");
            break;
            case "音乐":
                $content = array("Title" => "最炫民族风", "Description" => "歌手：凤凰传奇", "MusicUrl" => "http://121.199.4.61/music/zxmzf.mp3", "HQMusicUrl" => "http://121.199.4.61/music/zxmzf.mp3");
            break;
            default:
                $content = date("Y-m-d H:i:s", time());
            break;
        }
        if (is_array($content)) {
            if (isset($content[0]['PicUrl'])) {
                $result = $this->transmitNews($object, $content);
            } else if (isset($content['MusicUrl'])) {
                $result = $this->transmitMusic($object, $content);
            }
        } else {
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }
    private function transmitText($object, $content) {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    private function transmitNews($object, $newsArray) {
        if (!is_array($newsArray)) {
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item) {
            $item_str.= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $newsTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Content><![CDATA[]]></Content>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";
        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }
    private function transmitMusic($object, $musicArray) {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";
        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
$item_str
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    private function checkSignature() {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    //写入日志
    private function logger($log_content) {
        if (isset($_SERVER['HTTP_APPNAME'])) { //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        } else if ($_SERVER['REMOTE_ADDR'] != "127.0.0.1") { //LOCAL
            $max_size = 10000;
            $log_filename = "wx_log.xml";
            if (file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)) {
                unlink($log_filename);
            }
            file_put_contents($log_filename, date('H:i:s') . " " . $log_content . "\r\n", FILE_APPEND);
        }
    }
	
	public function getWxAccessToken()
	{
		if($_SESSION['access_token'] && $_SESSION['expire_time'] > time()){
			return $_SESSION['access_token'];
		} else {
			$appid = 'wx012edb733ade3952'; //填入自己的appid
			$appsecret = '409ad2daa8da76c2dd5ae8244a870f74';
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
			$res = $this->httpCurl($url); //调用上面提到的curl方法
			$res = json_decode($res, true);
			$access_token = $res['access_token'];
			$_SESSION['access_token'] = $access_token; //将access_token存入缓存，也可用redis、memcache等方式
			$_SESSION['expire_time'] = time()+7000; //获取的access_token的有效期为7200秒，因此缓存的时间应小于7200秒；
			return $access_token;
		}
	}
	public function httpCurl($url, $type='get', $postData='')
	{
		//1.初始化
		$ch = curl_init();
		//2.设置curl参数
		curl_setopt($ch, CURLOPT_URL, $url); //要链接的url
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //将curl_exec()获取的信息以字符串返回，而不是直接输出。
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//部分链接url要以https协议进行，设定以跳过证书验证
		if($type == 'post'){
			curl_setopt($ch, CURLOPT_POST, true); //true时发送post请求；
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}
		//3.执行curl请求
		$res = curl_exec($ch); //将返回一个json格式的字符串
		//4.返回结果并关闭curl连接
		if(curl_errno($ch)){
			return curl_error($ch);
		}
		curl_close($ch);
		return $res;
	}
	//生成自定义菜单
	public function createMenu(){
        $ACCESS_TOKEN=$this->getWxAccessToken();   
        $data = '{
          
         "button":[
             {  
                  "type":"click",
                  "name":"搜索附近",
                  "key":"1"
              },
              { 
                  "type":"click",
                  "name":"最新活动",
                  "key":"2"
              },
              {
                  "name":"更多",
                  "sub_button":[
                    {
                       "type":"click",
                       "name":"关于我们",
                       "key":"3"
                    },
                    {
                       "type":"click",
                       "name":"用户反馈",
                       "key":"4"
                    },
                    {
                       "type":"click",
                       "name":"优倍周边",
                       "key":"5"
                    }]
              }]
         }';
		$this->httpCurl("https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$ACCESS_TOKEN}");

 
    }
	public function getBaseInfo()
	{
		$appid = 'wx012edb733ade3952'; //上文提及的公众号自己的appid
		$redirectUri = urlencode('http://www.liping768.com/wx_api.php');
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appId.'&redirect_uri='.$redirectUri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
		header('location:'.$url); //此步仅做演示，实际业务中引导用户访问调用此方法的链接来跳转至redirectUrl
	}

	public function getUserOpenId()
	{
		$appid = 'wx012edb733ade3952'; //上文提及的公众号自己的appid
		$redirectUri = urlencode('http://www.liping768.com/wx_api.php');
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appId.'&redirect_uri='.$redirectUri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
		$res = $this->httpCurl($url);
		$res = json_decode($res, true);
		$code = $res['code'];
		$appSecret = '409ad2daa8da76c2dd5ae8244a870f74';
		$code = $_GET['code']; //getBaseInfo 方法传来的code
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appId.'&secret='.$appSecret.'&code='.$code.'&grant_type=authorization_code';
		$res = $this->httpCurl($url);
		return $res;
		//返回值的格式：{ "access_token":"ACCESS_TOKEN", "expires_in":7200, "refresh_token":"REFRESH_TOKEN", "openid":"OPENID", "scope":"SCOPE" } 
	}
	public function getUserInfo()
	{
		//获取openid和网页授权access_token
		$appId = 'wx012edb733ade3952';
		$appSecret = '409ad2daa8da76c2dd5ae8244a870f74';
		$redirectUri = urlencode('http://www.liping768.com/wx_api.php');
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appId.'&redirect_uri='.$redirectUri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
		$res = $this->httpCurl($url);
		$res = json_decode($res, true);
		$code = $res['code'];
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appId.'&secret='.$appSecret.'&code='.$code.'&grant_type=authorization_code';
		$res = $this->httpCurl($url);
		//返回值的格式：{ "access_token":"ACCESS_TOKEN", "expires_in":7200, "refresh_token":"REFRESH_TOKEN", "openid":"OPENID", "scope":"SCOPE" }
		$res = json_decode($res, true);
		$access_token = $res['access_token'];
		$openid = $res['openid'];

		//获取用户的详细信息
		$url = ' https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		$arr = $this->httpCurl($url);
		var_dump($arr);
	}
	//获取微信服务器的IP地址
	public function getWxServerIp()
	{
		$access_token = $this->getWxAccessToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$access_token;
		$res = $this->httpCurl($url);
		$arr = json_decode($res,true);
		return $arr;
	}
	//临时二维码
	public function getTmpQRCode()
	{
		 //1.获取ticket票据
			$access_token = $this->getWxAccessToken();
			$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
			$postArr = array(
				'expire_seconds' => 604800, //24*60*60*7
				'action_name'    => 'QR_SCENE',
				'action_info'    => array(
					'scene'      => array('scene_id' => '10000')//扫码事件中的EventKey
				)
			);
			$postJson = json_encode($postArr);
			$res = $this->httpCurl($url, 'post', $postJson);
			$res = json_decode($res, true);
			$ticket = urlencode($res['ticket']);
			//2.使用ticket获取二维码图片
			$url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
			echo "<img src='{$url}' />";
	}
	//永久二维码
	public function getInsQRCode()
	{
		 //1.获取ticket票据
			$access_token = $this->getWxAccessToken();
			$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;      
			$postArr = array(
				'action_name'    => 'QR_LIMIT_SCENE',
				'action_info'    => array(
					'scene'      => array('scene_id' => '10000')//扫码事件中的EventKey
				)
			);
			$postJson = json_encode($postArr);
			$res = $this->httpCurl($url, 'post', $postJson);
			$res = json_decode($res, true);
			var_dump($res);
			$ticket = urlencode($res['ticket']);
			//2.使用ticket获取二维码图片
			$url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
			echo "<img src='{$url}' />";
	}
			
	
}
?>