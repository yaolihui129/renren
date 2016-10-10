<?php
//用于用户登录
namespace Home\Controller;
use \Think\Controller;
/**
* 
*/
class PublicController extends Controller{
	//跳转至登录页面
	public function login(){
		//var_dump($_SESSION);
		if(isset($_SESSION['homeuser'])){
			if(!empty($_SESSION['homeuser']['head'])){
				$this->assign('head',$_SESSION['homeuser']['head']);
			}
			if(!empty($_SESSION['homeuser']['user'])){
				$this->assign('user',$_SESSION['homeuser']['user']);
			}
		}
		$this->display();
	}

	//登录验证
	public function check(){
		$user = M("User")->where("user='{$_POST['user']}' AND status<>2")->find();
		//var_dump($user);
        if(empty($user)){
            $this->assign("errorinfo","登录账户不存在，或已被禁用！");
            $this->display("login");
            exit();
		}
		if(md5($_POST['password'])!==$user['pass']){
			$this->assign("errorinfo","用户密码不正确");
            $this->display("login");
            exit();
		}else{
			//alert('aa');
			$_SESSION['homeuser']['user'] = $user['user'];
			$_SESSION['homeuserid'] = $user['id'];
			$_SESSION['homeuser']['state'] = true;
			$this->redirect('Home/HomePage/index/id/'.$user['id']);
		}
	}

	//注册
	public function register(){
		$this->display();
	}

	//验证码
	public function code(){
		$verify = new \Think\Verify();
        $verify->fontSize = 40;
        $verify->length   = 4;
        $verify->useNoise = true;
        $verify->useCurve = false;
        $verify->entry();
	}

	//验证账号唯一性
	public function doCheck(){
		$user = M("User")->where("user='{$_POST['user']}'")->find();
		if($user){
			echo true;
			die();
		}else{
			echo false;
			die();
		}
	}

	//验证验证码是否正确
	function check_verify($code, $id = ''){
	    $verify = new \Think\Verify();
	    return $verify->check($code, $id);
	}

	//注册验证
	function doSubmit(){
		$verify = new \Think\Verify();
		//var_dump($_POST);die();
		if($verify->check($_POST['icode'])){
			echo 'Y';
			die();
		}else{
			echo 'N';
			die();
		}
	}

	//完成第一步注册
	function submit(){
		//var_dump($_POST);die();
		$user = M("User");
		$_POST['pass']=md5($_POST['pass']);
		$_POST['addtime']=time();
		$_POST['level']=1;
		$user->create();
		$userid=$user->add();
		echo $userid;
		die();
	}

	//显示注册详细界面
	public function info($id){
		$user=M("User");
		$info=$user->where("id={$id}")->select();
		$addtime=$info[0]['addtime'];
		$str=date('Y',$addtime).'年'.date('n',$addtime).'月'.date('j',$addtime).'日';
		$this->assign('addtime',$str);

		$pro=M('Province');
		$list=$pro->select();
		$this->assign('id',$id);
		$this->assign('list',$list);
		$this->display();
	}

	//显示市级信息
	public function city($fcode){
		$city=M('City');
		$list=$city->where("provinceCode='{$fcode}'")->select();
		foreach ($list as $key => $value) {
			$rows[]=$value;
		}
		echo json_encode($list);
		die();
	}

	//将详细信息插入到数据库中
	public function insert(){
		//var_dump($_POST);
		//将搜集的信息整合
		$_POST['birth']=mktime(0,0,0,intval($_POST['month']),intval($_POST['day']),intval($_POST['year']));
		$pro=M('Province');
		$sheng=$pro->where("code='{$_POST['sheng']}'")->select();
		$city=M()->table('City')->where("code='{$_POST['city']}'")->select();
		$_POST['address']=$sheng[0]['name'].$city[0]['name'];
		
		$_POST['pingyin']=preg_replace('/ /', '',CommonController::encode($_POST['name'],'all'));
		//var_dump($_POST);
		$user=M('User');
		$user->create();
		$res=$user->where("id={$_POST['id']}")->save();
		$_SESSION['homeuserid'] = $_POST['id'];
		$_SESSION['homeuser']['user'] = $_POST['user'];
		$_SESSION['homeuser']['state'] = true;
		echo $res;
		die();
	}
	
}