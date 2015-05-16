<?php
namespace Wechat\Controller;
use Think\Controller;

class MenuController extends Controller {

    public function index() {
    
		//获取数据库里的TOKEN、APP_ID、APP_SECRET，并宏定义
		$wechat = D("wechat");
		$wid = 1;
		$condition['wid'] = $wid; 
		$result = $wechat -> where() -> find();
		define("TOKEN", $result['token']);
		define("APP_ID", $result['appid']);
		define("APP_SECRET", $result['appsecret']);
		
		//获得access_token并创建菜单
		$access_token = $this->get_access_token();
		$ok = $this->createmenu($access_token, $result['url']);
		
		//打印结果
		$this -> success("服务器返回：".$ok, 'javascript:history.back();');
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
    
     /** 
     * 创建菜单 
     * @param $access_token 已获取的ACCESS_TOKEN 
     */ 
    private function createmenu($access_token, $website_url) 
    { 
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token; 
        $arr = array(  
            'button' =>array( 
                array( 
                    'name'=>urlencode("商都微站"), 
                    'type'=>'view',
                    "url"=> $website_url
                ), 
                array( 
                    'name'=>urlencode("优惠资讯"), 
                    'type'=>'click',
                    "key"=> "DISCOUNT"
                ), 
                array( 
                    'name'=>urlencode("快捷入口"), 
                    'sub_button'=>array( 
						 array( 
                            'name'=>urlencode("商都位置"), 
                            'type'=>'view', 
                            "url"=> $website_url."/index.php/Home/Index/location.html" 
                        ),
                        array( 
                            'name'=>urlencode("商铺列表"), 
                            'type'=>'view', 
                            "url"=> $website_url."/index.php/Home/Index/shopList.html" 
                        ), 
                        array( 
                            'name'=>urlencode("查询商铺"), 
                            'type'=>'click', 
                            'key'=>'SEARCH' 
                        ) 
                    ) 
                ) 
            ) 
        ); 
        $jsondata = urldecode(json_encode($arr)); 
        $ch = curl_init(); 
        curl_setopt($ch,CURLOPT_URL,$url); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
        curl_setopt($ch,CURLOPT_POST,1); 
        curl_setopt($ch,CURLOPT_POSTFIELDS,$jsondata); 
        $ok = curl_exec($ch); 
        curl_close($ch); 
        
        return $ok;
    } 
    
    
}
