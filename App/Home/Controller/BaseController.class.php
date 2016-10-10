<?php
//用来显示主页基本框架内容
namespace Home\Controller;
/**
* 
*/
class BaseController extends CommonController{
	public function index($id){
		//防止用户随意切换用户
		if($_SESSION['homeuserid']!=$id){
			$this->redirect('HomePage/index/id/'.$_SESSION['homeuserid']);
		}
		setcookie("PHPSESSID",session_id(),time()+1440,"/");
		$_SESSION['homeuser']['head']=$this->selectHead($id);
		//var_dump($_SESSION);
		if($this->selectHead($id)){
			$this->assign('head',$this->selectHead($id));
		}else{
			$this->assign('head','men_tiny.gif');
		}
		
		$info=$this->selectInfo($id);
		//var_dump($info);
		$level=array(0,'码农','程序猿','攻城狮','大牛');
		$info['level']=$level[$info['level']];
		$this->assign('info',$info);
		$friendList=$this->selectFriend($id);
		$this->assign('friend',$this->selectFriend($id));
		$this->assign('friendNum',count($friendList));
		//var_dump($this->selectFriend($id));die();
		$this->assign('group',$this->selectGroup($id));
		$this->assign('iconList',$this->selectLittleIcon());
		$specialFriendList=$this->selectSpecialFriend($id);
		$this->assign('sFList',$specialFriendList);
		$this->assign('sFListNum',$this->selectSpecialFriendNum($id));
		$this->assign('gFList',$this->selectGroupFriendStr($id));
		$this->assign('recBirth',$this->selectBirth($id));
		$this->assign('noGroup',$this->selectNoGroup($id));
		$this->assign('sysMessage',$this->selectRecentSysMessage($id));
		// var_dump($id);die();
		//var_dump($this->selectGroup($id));die();
		$this->display();
	}



	//处理用户退出
	public function quitUser(){
		$_SESSION['homeuser']['state']=false;
		$this->redirect('Public/login');
	}

	//查询最近对话
	public function recentDialog(){
		//var_dump($_POST);
		$list=$this->selectDialog($_POST['uid'],$_POST['fid']);
		foreach($list as $k=>$v){
			foreach ($v as $key => $value) {
				if($key=="addtime"){
					$str=date("Y-m-d H:i:s",$value);
					$list[$k][$key]=$str;
				}
			}
		}
		echo json_encode($list);
		die();
	}

	//向数据库加入对话
	public function insertDialog(){
		// var_dump($_POST);
		$_POST['addtime']=time();
		$dialog=M('Dialog');
		$dialog->create();
		$id=$dialog->add();
		echo $id;
		die();
	}

	//搜索
	public function searchFriend(){
		//var_dump($_GET);
		$friend=M('User');
		$map['name']=array('like','%'.$_GET['info'].'%');
		$map['pingyin']=array('like','%'.$_GET['info'].'%');
		$map['_logic'] = 'OR';
		//var_dump($map);
		$list=$friend->where($map)->select();
		foreach($list as $k=>$v){
			$list[$k]['head']=$this->selectHead($v['id']);
		}
		echo json_encode($list);
		die();
	}

	//拒绝好友申请
	public function delNewFriend(){
		// var_dump($_POST);
		$sys=M('Sys');
		$m=$sys->delete($_POST['sid']);
		$rel=M('Relation');
		$n=$rel->delete($_POST['rid']);
		$res=$m*$n;
		echo $res;
		die();
	}

	//添加好友
	public function addNewFriend(){
		// var_dump($_POST);die();
		$sys=M('Sys');
		$m=$sys->delete($_POST['sid']);
		$rel=M('Relation');
		$rel->status='0';
		$n=$rel->where("id={$_POST['rid']}")->save();
		$res=$m*$n;
		$list=$rel->where("id={$_POST['rid']}")->select();
		$data['uid']=$list[0]['fid'];
		$data['fid']=$list[0]['uid'];
		$data['addtime']=time();
		$data['gid']='0';
		$data['status']='0';
		$rel->add($data);
		$row['name']=$this->selectInfo($data['fid'])['name'];
		$row['head']=$this->selectHead($data['fid']);
		$row['fid']=$data['fid'];
		$str=json_encode($row);
		echo $str;
		die();
	}
}