<?php
namespace app\index\controller;

use think\Loader;
use think\Request;
//导入微信扩展库
//use weixin\Wechat;  //使用composer命令自动加载则使用该方法引入微信类库o
use wechat\Wechat;  //引入extend目录下的微信类库  //引入extend目录下的微信类库
use wechat\WechatAuth;

class Weixin extends Base
{
	//微信公众号回调地址,验证token，打通与微信公众号的连接
    public function index()
    {
        $token="liping768";  //与公众号后台设置的token保持一致
        $wechat = new Wechat($token);

        $data=$wechat->request();
        if($data && is_array($data)){
            switch($data['MsgType']){
                case "text" :
                $this->Text($wechat,$data);
				break;
            }

        }
    }

    //回复文本消息
    private function Text($wechat,$data){

        if(strstr($data['Content'],"文本")){
            $text="我正在使用Thinkphp开发微信公众号";
            $this->logger("发送消息：\r\n".$text); //把消息写入日志文件，方便错误调试

            $wechat->replyText($text);  

        }else if(strstr($data['Content'],"myself")){
			$this->users($wechat,$data);
		}

    }
	//获取access_token
	public function getAccessToken(){
		$appid="wx66e6d9470bbfce03";
		$appSecret="35a1ff19ad83fda99bb7f51f3caf322f";
        $WechatAuth=new WechatAuth($appid,$appSecret);
		$res = $WechatAuth->getAccessToken();
		$access_token = $res['access_token'];
		echo $access_token;
	}

    //获取用户信息
    private function users($wechat,$data){
      $openid=$data['FromUserName'];
      $appid="wx66e6d9470bbfce03";
      $appSecret="35a1ff19ad83fda99bb7f51f3caf322f";
      $token=session('token') ;

      if($token){

      $WechatAuth=new WechatAuth($appid,$appSecret,$token);

      }else{
        $WechatAuth=new WechatAuth($appid,$appSecret);

        $accsseToken=$WechatAuth->getAccessToken();
        $token=$accsseToken['access_token'];
        session('token',$token);
      }
      $user=$WechatAuth->userInfo($openid);
      $text="你的openid是：".$user['openid']."\n你的昵称是：".$user['nickname']."\n
      你的性别是:".$user['sex']."\n你的城市是：".$user['city']."\n你所在国家是".$user['country']."\n
      你在的省份是：".$user['province'];

      $this->logger("发送用户的信息：\r\n".$text);
      $wechat->replyText($text);

      

    }


    //网页授权获取用户基本信息
    public function webUsers(){
     
      $appid="wx9e583a9d8dacbec8";
      $appSecret="aa2794a1a3c555740542d52d397a7dfd";
      $WechatAuth=new WechatAuth($appid,$appSecret);
      if($_GET['iscode']){
         $url="http://lizhongyi.xd107.com/Home/Index/webUsers";
         $result=$WechatAuth->getRequestCodeURL($url);
         $result;
         header("Location:{$result}");

      }else if($_GET['code']){
          header('Content-type:text/html;charset=utf-8');
          $user=$WechatAuth->getAccessToken('code',$_GET['code']);
          $openid=$user['openid'];
          $users=$WechatAuth->getUserInfo($openid);
          $m=M('users');
          $data['openid']=$users['openid'];
          $data['nickname']=$users['nickname'];
          $result=$m->add($data);
          if($result){
            $text="你的openid是：".$users['openid']."\n你的昵称是：".$users['nickname']."\n
      你的性别是:".$users['sex']."\n你的城市是：".$users['city']."\n你所在国家是".$users['country']."\n
      你在的省份是：".$users['province'];
            echo $text;
          }
         
      }
     

      
      // $user=$WechatAuth->userInfo($openid);
      // $text="你的openid是：".$user['openid']."\n你的昵称是：".$user['nickname']."\n
      // 你的性别是:".$user['sex']."\n你的城市是：".$user['city']."\n你所在国家是".$user['country']."\n
      // 你在的省份是：".$user['province'];

      // $this->logger("发送用户的信息".$text);
      // $wechat->replyText($text);

      

    }


    private function logger($content){
        $logSize=100000;

        $log="log.txt";

        if(file_exists($log) && filesize($log)  > $logSize){
            unlink($log);
        }

        file_put_contents($log,date('H:i:s')." ".$content."\n",FILE_APPEND);

    }


}
