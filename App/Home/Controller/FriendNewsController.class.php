<?php
namespace Home\Controller;
/**
* 好友的详细新鲜事信息
*/
class FriendNewsController extends BaseController{
	public function index($id,$fid){
		$userNews=$this->selectUserNews($fid);
		$this->assign('userNews',$userNews);
		//查询草稿箱里面的内容
		$unDiary=M('Diary');
		//$undiary=$unDiary->where('status=0')->order('addtime desc')->select();
		$this->assign('undiary',$unDiary->where("status=0 AND uid={$fid}")->order('addtime desc')->select());
		parent::index($id);
	}
}