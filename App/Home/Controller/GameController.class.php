<?php
namespace Home\Controller;
/**
* 应用中心
*/
class GameController extends BaseController{
	//页面显示\
	public function index($id){
		$this->assign('gameRank',$this->selectGameRank($id));
		parent::index($id);
	}
	//战绩上传
	public function uploadMyGoal(){
		// var_dump($_POST);
		$game=M('Game');
		$yscore=$game->field('score')->where("uid={$_POST['uid']}")->select();
		// var_dump($yscore[0]);
		// var_dump($_POST['score']);die();
		if($yscore && ($_POST['score'] < $yscore[0]['score'])){
			echo '';
			die();
		}else{
			$game->where("uid={$_POST['uid']}")->delete();
			$game->create();
			$res=$game->add();
			echo $res;
			die();
		}
	}

	//Rank排行
	public function findRank($id){
		$rank=$this->selectGameRank($id);
		$res=json_encode($rank);
		echo $res;
		die();
	}
}