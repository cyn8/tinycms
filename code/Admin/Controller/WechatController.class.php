<?php

/**
 * @author Dr.Chan <cynmsg@126.com>
 */

namespace Admin\Controller;
use Think\Controller;

class WechatController extends Controller {
	function _empty(){
        header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
        $this->display("Public:base");
    }

    public function config() {
        //权限控制
        if(isset($_SESSION['name']) && $_SESSION['role'] == 1) {
            $Wechat = D("Wechat");

            if(!empty($_POST)) {
                $_POST['wid'] = 1;

                $Wechat -> create();
                $z = $Wechat -> save();

                if($z) {
                    $this -> success("配置信息修改成功！",U('config'));
                } else {
                    $this -> error("配置信息修改失败！",U('config'));
                }

            } else {
                $wid = 1;
                $result = $Wechat -> where('wid = '.$wid) -> find();
                $this -> assign('result', $result);

                $this -> assign('id', $_SESSION['id']);
                $this -> assign('name', $_SESSION['name']);
                $this -> assign('role', $_SESSION['role']);

                $front = D("Front");
                $fid = 1;
                $resultByFront = $front -> where('fid = '.$fid) -> find();
                $this -> assign('resultByFront', $resultByFront);

                $this -> display();
            }
        } else {
            $this -> error("非法访问，你不是超级管理员！", U('Wechat/config'));
        }
    }
}