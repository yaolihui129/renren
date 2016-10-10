<?php
// 后台common控制器
namespace Admin\Controller;
use Think\Controller;

class PublicController extends Controller {
    
    // 提供登录页面
    public function login(){
        if(isset($_GET['e'])){
            $this->assign('e',$_GET['e']);
        }
        $this->display();
    }
    
    // 进行后台登录验证
    public function check(){
        $user = M('user')->where("status=0 AND user='{$_POST['user']}'")->find();
        // dump($user['pass']);
        // dump($_POST['pass']);
        if(empty($user)){
            $this->redirect('Public/login',array('e'=>'0'));
        }
        if($user['pass'] != md5($_POST['pass'])){
            $this->redirect('Public/login',array('e'=>'1'));
        }else{
            $_SESSION['adminuser'] = $user;
            $this->redirect('Index/index');
        }
    }

    // 退出登录
    public function logout(){
        $_SESSION['adminuser'] = '';
        $this->redirect('Public/login');
    }
}