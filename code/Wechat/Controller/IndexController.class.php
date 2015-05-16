<?php
namespace Wechat\Controller;
use Think\Controller;

class IndexController extends Controller {

    public function index() {
    
		//获取数据库里的TOKEN、APP_ID、APP_SECRET，并宏定义
		$wechat = D("wechat");
		$wid = 1;
		$condition['wid'] = $wid; 
		$result = $wechat -> where() -> find();
		define("TOKEN", $result['token']);
		define("APP_ID", $result['appid']);
		define("APP_SECRET", $result['appsecret']);
//		$ok .= APP_SECRET;
//      @file_put_contents("test.txt",$ok);
		
		//判断请求是验证接口还是请求回复
		if(isset($_GET['echostr'])) {
			$this -> valid();
			exit;
		} else {
			$this -> responseMsg();
		}

		
    }
 
    /**
	* 用户关注时
	*/
	private function onSubscribe($fromUsername, $toUsername, $time) {
		//实例化Model
		$front = D("Front");
		$fid = 1;
		$condition['fid'] = $fid; 
		$result = $front -> where($condition) -> find();
	
		$tpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[news]]></MsgType>
				<ArticleCount>1</ArticleCount>
				<Articles>";
				
		$tpl .="<item>
				<Title><![CDATA["."欢迎关注".$result['title']."]]></Title> 
				<Description><![CDATA[".$result['content']."]]></Description>
				<PicUrl><![CDATA[http://".$_SERVER['HTTP_HOST']."/public/".$result['small_img']."]]></PicUrl>
				<Url><![CDATA[".$_SERVER['HTTP_HOST']."]]></Url>
				</item>";
				
		$tpl .= "</Articles>
				</xml> ";
								
		echo sprintf($tpl, $fromUsername, $toUsername, $time);
	}
	
	/**
	* 用户取消关注时
	*/
	private function onUnsubscribe() {
		
	}
	
	/**
	* 点击了“优惠资讯”按钮
	*/
	private function onClickDISCOUNT($textTpl, $fromUsername, $toUsername, $time, $msgType) {
	
		//实例化Model
		$wechat = D("Wechat");
		$wid = 1;
		$condition['wid'] = $wid; 
		$result = $wechat -> where($condition) -> find();
		
		$str = $result['pushtxt'];
		
		echo sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $str);
	}
	
	/**
	* 点击了“查询商铺”按钮
	*/
	private function onClickSEARCH($textTpl, $fromUsername, $toUsername, $time, $msgType) {
		
		$str = "直接发送关键词可以搜索商铺。";
		
		echo sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $str);
	}
	
	/**
	* 收到文本信息时
	*/
	private function onText($textTpl, $fromUsername, $toUsername, $time, $msgType, $keyword) {
		
		//实例化Model
		$shopList = D("Articles");
		$condition['shopname'] = array('like','%'.$keyword.'%'); 
		$result = $shopList -> where($condition) -> select();
		if(!empty($result)) {
			//若找到商铺，则采用图文模板
			$tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>".count($result)."</ArticleCount>
					<Articles>";
					
			
			$str = '找到'.count($result)."个商铺：";
			$count = 1;
			foreach ($result as $key => $value) {
//				$str .= "\n".$count.'. <a href="'.$_SERVER['HTTP_HOST']."/index.php/Home/Index/Article/id/".$result[$key]['id'].'">'.$result[$key]['shopname'].'</a>';
				$introduce = "商店门号：".$result[$key]['shopid']."\n"
							."联系方式：".$result[$key]['phone']."\n"
							."商铺简介：\n".$result[$key]['introduce'];
				
				$tpl .= "<item>
						<Title><![CDATA[".$result[$key]['shopname']."]]></Title> 
						<Description><![CDATA[".$introduce."]]></Description>
						<PicUrl><![CDATA[http://".$_SERVER['HTTP_HOST']."/public/".$result[$key]['logo']."]]></PicUrl>
						<Url><![CDATA[".$_SERVER['HTTP_HOST']."/index.php/Home/Index/Article/id/".$result[$key]['id']."]]></Url>
						</item>";
				
//				$count ++;
		
			}
				$tpl .= "</Articles>
						</xml> ";
//				$str .= "=======================\nTips: 点击商铺名称可查看详情";
				echo sprintf($tpl, $fromUsername, $toUsername, $time);
		} else {
			$str = '找不到该商铺！';
			echo sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $str);
			exit;
		}
		
		
	}
	
	private function responseMsg()
    {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $msgType = $postObj->MsgType;	//消息类型
                $event = $postObj->Event;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>"; 
				
				$ok .= $postObj -> EventKey;
				
//        @file_put_contents("test.txt",$ok);
				
				//事件处理
				switch($msgType) {
					case 'event':
						switch ($event) {
							//有新关注
							case 'subscribe': $this -> onSubscribe($fromUsername, $toUsername, $time); break;
							
							//有取消关注
							case 'unsubscribe': $this -> onUnsubscribe(); break;
							
							case 'CLICK' : 
								switch ($postObj -> EventKey) {
									case "DISCOUNT": $this -> onClickDISCOUNT($textTpl, $fromUsername, $toUsername, $time, "text"); break;
								}
								
								switch ($postObj -> EventKey) {
									case "SEARCH": $this -> onClickSEARCH($textTpl, $fromUsername, $toUsername, $time, "text"); break;
								}
						}
					break;
					
					case 'text': $this -> onText($textTpl, $fromUsername, $toUsername, $time, $msgType, $keyword); break;

				}
                

        }else {
        	echo "";
        	exit;
        }
        
        
    }
    
    /**
    * 验证接口时调用valid()
    */
    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
 
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
 
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    
    private function valid() {
		$echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
    
    /** 
     * 获取access_token 
     */ 
    private function get_access_token() 
    { 
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APP_ID."&secret=".APP_SECRET; 
        $data = json_decode(file_get_contents($url),true); 
        if($data['access_token']){ 
            return $data['access_token']; 
        }else{ 
            return "获取access_token错误"; 
        } 
    } 
    
    
    
}
