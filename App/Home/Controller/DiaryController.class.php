<?php
namespace Home\Controller;
/**
* 定义日志类
*/
class DiaryController extends BaseController{
	//插入日志
	public function insertDiary(){
		$_POST['addtime']=time();
		$diary=M('Diary');
		$diary->create();
		$res=$diary->add();
		if($res){
			//$_SESSION['diaryid']=$res;
			//var_dump($_SESSION);die();
			echo '<script type="text/javascript">
				parent.window.diarySave();
				setTimeout(function(){},3000);
				parent.location.href="/renren/index.php/Home/Diary/displayDiary/id/'.$_POST['uid'].'/diaryid/'.$res.'";
			</script>';
		}
	}

	//展示编写完的日志
	public function displayDiary($id,$diaryid){
		//$diaryid=$_SESSION['diaryid'];
		//echo $id;die();
		//$this->recentDialog($id);
		// $this->insertDialog();
		$diary=M('Diary');
		$diaryinfo=$diary->where("id='{$diaryid}'")->select();
		//var_dump($_SESSION);die();
		//var_dump($diaryinfo);die();
		$disnum=$this->selectDiscussNum('7',$diaryid);
		// var_dump($disnum);
		$sunum=$this->selectSupportNum('7',$diaryid);
		// var_dump($sunum);
		$dis=$this->selectDiscussContent('7',$diaryid);
		// var_dump($dis);
		$this->assign('diaryinfo',$diaryinfo[0]);
		$this->assign('disnum',$disnum);
		$this->assign('sunum',$sunum);
		$this->assign('dis',$dis);
		//$this->display();

		$this->index($id);
	}

	//编辑日志界面
	public function editDiary($id,$diaryid){
		$diary=M('Diary');
		$diaryinfo=$diary->where('id='.$diaryid)->select();
		// var_dump($diaryinfo);die();
		$this->assign('diaryinfo',$diaryinfo[0]);
		$this->index($id);
	}

	//修改日志
	public function updateDiary($diaryid){
		//var_dump($_POST);die();
		$_POST['id']=$_POST['diaryid'];
		$_POST['addtime']=time();
		$diary=M('Diary');
		$diary->create();
		$res=$diary->save();
		if($res){
			//$_SESSION['diaryid']=$res;
			//var_dump($_SESSION);die();
			echo '<script type="text/javascript">
				parent.window.diarySave();
				setTimeout(function(){},3000);
				parent.location.href="/renren/index.php/Home/Diary/displayDiary/id/'.$_POST['uid'].'/diaryid/'.$_POST['diaryid'].'";
			</script>';
		}
	}

	//删除日志
	public function delDiary($id,$diaryid){
		$diary=M('Diary');
		$res=$diary->delete($diaryid);
		$this->redirect('HomePage/index/id/'.$id);
	}
}