<?php
namespace Home\Controller;
/**
* 定义好友详细新鲜事以及自己的详细新鲜事
*/
class SelfNewsController extends BaseController{
	//定义显示主页的界面
	public function index($id){
		$userNews=$this->selectUserNews($id);
		$this->assign('userNews',$userNews);
		//查询草稿箱里面的内容
		$unDiary=M('Diary');
		//$undiary=$unDiary->where('status=0')->order('addtime desc')->select();
		$this->assign('undiary',$unDiary->where("status=0 AND uid={$id}")->order('addtime desc')->select());
		//var_dump($undiary);die();
		parent::index($id);
	}
	
	//删除新鲜事
	public function delXXS($id,$typeid){
		switch($typeid){
			case '0':
				$mod=M('Talk');
				$res=$mod->delete($id);
				break;
			case '1':
				$mod=M('Diary');
				$str=$mod->field('content')->where("id={$id}")->select();
				//var_dump($str[0]);die();
				
				$list=$this->findPic($str[0]);
				//var_dump($list);die();
				$res=$mod->delete($id);
				foreach ($list as $key => $value) {
					if(file_exists('./Public/upload/images/'.$value)){
						unlink('./Public/upload/images/'.$value);
					}
				}
				break;
			case '5':
				$mod=M();
				$m=$mod->table('Talkpic')->delete($id);
				$piclist=$mod->table('Pic')->where("wtype={$typeid} AND wtypeid={$id}")->select();
				$n=$mod->table('Pic')->where("wtype={$typeid} AND wtypeid={$id}")->delete();
				foreach($piclist as $k=>$v){
					
					if(file_exists('./Public/upload/images/'.$v['picname'])){
						unlink('./Public/upload/images/'.$v['picname']);
						unlink('./Public/upload/images/s_'.$v['picname']);
					}
				}
				if($m && $n){
					echo 1;
				}else{
					echo 0;
				}
				die();
		}





		echo $res;
		die();
	}

	//找出日志中的图片并删除
	public function findPic($str){
		$pattern='/[0-9]*((\.jpg)|(\.png)|(\.gif)|(\.ico))/';
		preg_match_all($pattern, $str, $match);
		//var_dump($match[0]);
		$res=array_unique($match[0]);
		return($res);
	}

	//批量删除日志
	public function plscRiZhi(){
		// var_dump($_GET);
		$diary=M('Diary');
		$m=1;
		foreach ($_GET as $key => $value) {
			$res=$diary->delete($value);
			$m=$m*$res;
		}
		echo $m;
		die();
	}

}