<?php

namespace Admin\Controller;
use Think\Controller;

class CommentController extends Controller {

    function _empty(){
        header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
        $this->display("Public:base");
    }

    public function index() {
        //只有超级管理员才能管理评论
        if(isset($_SESSION['name']) && $_SESSION['role'] == 1) {
			
            if(empty($_POST)) {
                
                $front = D("Front");
                $comment = D("Comment");

                $this -> assign('id', $_SESSION['id']);
                $this -> assign('name', $_SESSION['name']);
                $this -> assign('role', $_SESSION['role']);
    
                $fid = 1;
                $resultByFront = $front -> where('fid = '.$fid) -> find();
                $this -> assign('resultByFront', $resultByFront);
                
                $resultByComment = $comment -> where('cid=1') -> find();
                $this -> assign('comment', $resultByComment);
                
                $this -> assign('result', $result);
                $this -> display();
            } else {
                $data['key'] = $_POST['key'];
                $data['code'] = $_POST['code'];
        
                $comment = M("Comment");
                $z = $comment -> where('cid=1') -> save($data);
                if($z) {
					$this -> success("评论信息修改成功！", 'javascript:history.back();');
				} else {
					$this -> error("评论信息修改失败！", 'javascript:history.back();');
				}
            }
        } else {
            $this -> error("非法访问，你不是超级管理员！", U('Index/index'));
        }

    }


}