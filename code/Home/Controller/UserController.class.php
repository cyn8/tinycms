<?php

namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {
	public function index() {
	
	}
	
	public function register() {
		if($_POST) {
			$username = $_POST['username'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$password0 = $_POST['password0'];
			
			//检查用户名重复
			$check = D('User');
			$c = $check -> where('username='.'\''.$username.'\'') -> find();
			if($c) { $this -> error('昵称已被占用！' ,'javascript:history.back();');}
			
			$_POST['password'] = MD5($_POST['password']);
			$_POST['regtime'] = date('Y-m-d H:i:s',time());
			$user = D('user');
			$user -> create();
			$z = $user -> add();
			if($z) {
				$this -> success("注册成功！请用您的用户名登录！", U('Index/index'));
			} else {
				$this -> error('注册失败！请联系管理员'.$username.print_r($check),'javascript:history.back();');
			}
			
		} else {
            cookie('username', null);
            cookie('uid', null);
            cookie('email', null);
            $front = D("Front");
            $fid = 1;
            $result = $front -> where('fid = '.$fid) -> find();
            $this -> assign('result', $result);
			$this -> display();
		}
	}

    public function login() {
        cookie(null);
        if(!empty($_POST)) {
            $user = D("User");
            $comment = D("Comment");
            $result = $user -> checkEmailPassword($_POST['email'], MD5($_POST['password']));

            if($result == false) {

                $this -> error("用户名或密码错误！",'javascript:history.back();');

            } else {
                //创建cookie，保存登录信息
                cookie('uid', $result['uid']);
                cookie('username', $result['username']);
                cookie('email', $result['email']);

                //实例化一个model来将登录ip和时间写入数据库
                $saveLoginInfo = M("User");
                $saveLoginInfo -> loginip = get_client_ip();
                $saveLoginInfo -> logintime = date('Y-m-d H:i:s',time());
                $saveLoginInfo -> where('uid='.$result['uid']) -> save();
                
                //获取评论的配置信息
                $comment = D("Comment");
                $resultByComment = $comment -> where('cid=1') -> find();
                
                 //多说评论本地认证
                $secret = $resultByComment['key'];
                $token = array(
                    'short_name'=>	$result['username'],
                    'user_key'	=>	$result['uid'],
                    'name'		=>	$result['username']
                );
                Vendor('ds.ds.JWT');
                $jwt = new \JWT();
                $token = $jwt -> encode($token, $secret);
                cookie('duoshuo_token', $token);
                $this -> success("登录成功！欢迎你，".$result['username']."。" ,U('Index/index'));
            }
        } else {
            cookie('username', null);
            cookie('uid', null);
            cookie('email', null);
            $front = D("Front");
            $fid = 1;
            $result = $front -> where('fid = '.$fid) -> find();
            $this -> assign('result', $result);
            $this -> display();
        }
    }

    public function logout() {
        cookie('username', null);
        cookie('uid', null);
        cookie('email', null);
        cookie('duoshuo_token', null);
        $header = getallheaders();
        redirect($header['Referer']);
    }
}