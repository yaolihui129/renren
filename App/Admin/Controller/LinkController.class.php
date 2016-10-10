<?php
namespace Admin\Controller;
use Think\Controller;
class UserManageController extends CommonController {
    public function linkIndex(){
    	$mod=D('link');
    	$list=$mod->select();
    	//var_dump($list);
    	$this->assign('list',$list);
        $this->display();
    }

    //删除用户
    public function delUserInfo(){

    }

    //修改用户
    public function updateUserInfo(){
    	// var_dump($_POST);
    	$user=M('User');
    	$user->create();
    	$res=$user->save();
    	echo $res;
    	die();
    }

    //添加用户
    public function insertUserInfo(){
        // var_dump($_POST);
        $user=M('User');
        $_POST['pass']=md5($_POST['password']);
        $_POST['addtime']=time();
        $user->create();
        $res=$user->add();
        if($res){
            $this->success('添加成功',U('UserManage/index'));
        }else{
            $this->error();
        }
    }
}