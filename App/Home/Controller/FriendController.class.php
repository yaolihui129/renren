<?php
namespace Home\Controller;
/**
* 好友列表
*/
class FriendController extends BaseController{
	//删除好友
	public function friendDel($relId){
		$rel=M('Relation');
		$res=$rel->delete($relId);
		echo $res;
		die();
	}

	//创建新的分组
	public function createNewGroup(){
		// var_dump($_GET);
		$group=M('Group');
		$list['uid']=$_GET['uid'];
		$list['gname']=$_GET['gname'];
		$res=$group->add($list);
		echo $res;
		die();
	}

	//重命名分组
	public function renameGroup(){
		$group=M('Group');
		$group->gname=$_GET['gname'];
		$res=$group->where("id={$_GET['gid']}")->save();
		echo $res;
		die();
	}
	//解散分组
	public function dismissGroup(){
		$group=M('Group');
		$res=$group->delete($_GET['gid']);
		$rel=M('Relation');
		$rel->gid='0';
		$rel->where("gid={$_GET['gid']}")->save();
		echo $res;
		die();
	}

	//增加组员
	public function insertGroupMember($gid,$uid){
		// var_dump($_POST);
		// var_dump($_GET);die();
		$gid=$_GET['gid'];
		$uid=$_GET['uid'];
		$data['gid']=$gid;
		$rel=M('Relation');
		$res=1;
		foreach($_POST as $v){
			$cs=$rel->where("uid={$uid} AND fid={$v}")->save($data);
			$res=$res*$cs;
		}
		echo $res;
		die();
	}

	//删除组员
	public function delGroupMember($gid,$uid){
		// var_dump($_POST);
		// var_dump($_GET);die();
		$data['gid']='0';
		$rel=M('Relation');
		$res=1;
		foreach($_POST as $v){
			$cs=$rel->where("uid={$uid} AND fid={$v}")->save($data);
			$res=$res*$cs;
		}
		echo $res;
		die();
	}
}