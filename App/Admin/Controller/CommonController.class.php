<?php
// 后台common控制器
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller {
    
    // 判断用户是否登录
    public function _initialize(){
        if(!isset($_SESSION['adminuser']) || empty($_SESSION['adminuser'])){
            $this->redirect('Public/login');
        }
    }

}