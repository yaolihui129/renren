<?php
namespace Home\Controller;
/**
* 主页类
*/
class HomePageController extends BaseController{
	//插入纯文字说说
	public function insertTalk(){
		// var_dump($_POST);
		$mod=M('Talk');
		$_POST['addtime']=time();
		$mod->create();
		$res=$mod->add();
		echo $res;
		die();
	}

	
	//评论
	public function addDiscuss(){
		// var_dump($_POST);
		$_POST['addtime']=time();
		$discuss=M('Discuss');
		$discuss->create();
		$res=$discuss->add();
		echo $res;
		die();
	}

	//点赞与删除点赞
	public function supportDeal(){
		$support=M('Support');
		if($_POST['state']=='jia'){
			$_POST['addtime']=time();
			$support->create();
			$res=$support->add();
			echo $res;
			die();
		}else{
			$res=$support->where("type={$_POST['type']} AND typeid={$_POST['typeid']} AND uid={$_POST['uid']}")->delete();
			echo $res;
			die();
		}
	}

	//首页显示
	public function index($id){
		$allNews=$this->selectAllNews($id);
		$this->assign('allNews',$allNews);
		parent::index($id);
	}

	//发表图片说说
	public function uploadImages(){
		//var_dump($_FILES);die();
		$upload = new \Think\Upload();// 实例化上传类   
		$image = new \Think\Image(); 
		$upload->maxSize = 3145728 ;// 设置附件上传大小   
		$upload->exts=array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath='./Public/upload/images/'; // 设置附件上传目录   
		$upload->autoSub=false; 
		// 上传文件
		$info=$upload->upload();
		if(!$info) {// 上传错误提示错误信息
		    $this->error($upload->getError());    
		}else{// 上传成功
		    foreach($info as $file){
		    	$list[]=$file['savename'];
		    	$image->open('./Public/upload/images/'.$file['savename']);
		    	$image->thumb(165, 165,\Think\Image::IMAGE_THUMB_CENTER)->save('./Public/upload/images/s_'.$file['savename']);
		    	
		    }  
		}
		$row=json_encode($list);
		echo '<script type="text/javascript">
			parent.document.getElementById("jsonPic").value='.$row.';
			parent.window.closePicFrame();
		</script>';
		die();
	}

	//处理图片上传
	public function insertPicUpload(){
		$_POST['addtime']=time();
		$mod=M();
		$mod->table('talkpic')->create();
		$m=$mod->table('talkpic')->add();
		if($m){
			$n=1;
			$list=explode(',', $_POST['picname']);
			foreach ($list as $key => $value) {
				$row['picname']=$value;
				$row['uid']=$_POST['uid'];
				$row['addtime']=time();
				$row['wtype']=5;
				$row['wtypeid']=$m;
				// var_dump($row);die();
				$a=$mod->table('pic')->add($row);
				$n=$n*$a;
			}
		}
		if($m && $n){
			echo $m;
			die();
		}else{
			echo $n;
			die();
		}

	}
}