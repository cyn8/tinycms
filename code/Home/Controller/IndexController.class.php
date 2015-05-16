<?php

namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {
    public function index() {
        //登录检测
        if(isset($_COOKIE['username'])) {
            $loginInf['islogin'] = 1; //标记登录
            $loginInf['username'] = $_COOKIE['username'];
            $loginInf['email'] = $_COOKIE['email'];
            $loginInf['uid'] = $_COOKIE['uid'];
        }
        $this -> assign('loginInf', $loginInf);
		$front = D("Front");
		$fid = 1;
		$result = $front -> where('fid = '.$fid) -> find();
		$this -> assign('result', $result);
		if($_GET['theme'])  {
			$this -> display($_GET['theme']);
        } else {
			$this -> display($result['index_theme']);
        }
    }
    
    public function location() {
        //登录检测
        if(isset($_COOKIE['username'])) {
            $loginInf['islogin'] = 1; //标记登录
            $loginInf['username'] = $_COOKIE['username'];
            $loginInf['email'] = $_COOKIE['email'];
            $loginInf['uid'] = $_COOKIE['uid'];
        }
        $this -> assign('loginInf', $loginInf);
		$front = D("Front");
		$fid = 1;
		$result = $front -> where('fid = '.$fid) -> find();
		$this -> assign('result', $result);
        $this -> display();
    }
    
    public function shoplist() {
        //登录检测
        if(isset($_COOKIE['username'])) {
            $loginInf['islogin'] = 1; //标记登录
            $loginInf['username'] = $_COOKIE['username'];
            $loginInf['email'] = $_COOKIE['email'];
            $loginInf['uid'] = $_COOKIE['uid'];
        }
        $this -> assign('loginInf', $loginInf);

		$ShopList = D("Articles");	//实例化Model
        $Type = D("Type");
		
		
		if(empty($_GET['shopname']) && empty($_GET['type'])) {
            //$result = $ShopList -> select();
            
            //数据分页
            $count = $ShopList -> count();
            $Page = new \Think\Page($count,5);
            $show = $Page->show();
            $result = $ShopList ->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('result',$result);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
		} else {
			$shopname = $_GET['shopname'];
            $type = $_GET['type'];

			$condition['shopname'] = array('like','%'.$shopname.'%');
            if(!empty($type)) $condition['typeid'] = $type;
			$result = $ShopList -> where($condition) -> select();
            
            //当前用户选中的类型
            $presentType = $Type -> where('tid='.$type) -> find();
            $this -> assign('presentType', $presentType);
		}
		
		//调试用
// 		foreach ($result as $key => $value) {
// 			echo $value['shopname'].'<br/>';
// 		}
//		print_r($result);
        $TypeResult = $Type -> select();
		
		$this -> assign('typeResult', $TypeResult);
		$this -> assign('result', $result);	//把数据assign到模板
		
		$front = D("Front");
		$fid = 1;
		$result0 = $front -> where('fid = '.$fid) -> find();
		$this -> assign('result0', $result0);
		
		if($_GET['theme'])  {
			$this -> display($_GET['theme']);
        } else {
			$this -> display($result0['shoplist_theme']);
        }
    }
    
    public function article($id = 1) {
        //登录检测
        if(isset($_COOKIE['username'])) {
            $loginInf['islogin'] = 1; //标记登录
            $loginInf['username'] = $_COOKIE['username'];
            $loginInf['email'] = $_COOKIE['email'];
            $loginInf['uid'] = $_COOKIE['uid'];
        }
        
        $this -> assign('loginInf', $loginInf);
        $Article = D("Articles");	//实例化Model
        $Type = D("Type");
        $result = $Article -> find($id); //不用select()
        

        //对sql返回结果进行二次判断
        if(!empty($result)) {
            $this -> assign('result', $result);	//把数据assign到模板
            
            //获取评论的配置信息
            $comment = D("Comment");
            $resultByComment = $comment -> where('cid=1') -> find();
            $this -> assign('comment', $resultByComment);

            $front = D("Front");
            $fid = 1;
            $result0 = $front -> where('fid = '.$fid) -> find();
            $this -> assign('result0', $result0);
            
            $TypeResult = $Type -> where('tid='.$result[typeid]) -> find();
		
			$this -> assign('typeResult', $TypeResult);	

            if($_GET['theme'])  {
                $this -> display($_GET['theme']);
            } else {
                $this -> display($result0['article_theme']);
            }
		} else {
			$this -> error('非法访问', 'javascript:history.back();');
		}
    }
    
    public function map() {
        //登录检测
        if(isset($_COOKIE['username'])) {
            $loginInf['islogin'] = 1; //标记登录
            $loginInf['username'] = $_COOKIE['username'];
            $loginInf['email'] = $_COOKIE['email'];
            $loginInf['uid'] = $_COOKIE['uid'];
        }
        $this -> assign('loginInf', $loginInf);

		$front = D("Front");
		$fid = 1;
		$result = $front -> where('fid = '.$fid) -> find();
		$this -> assign('result', $result);
        $this -> display();
    }
    
}