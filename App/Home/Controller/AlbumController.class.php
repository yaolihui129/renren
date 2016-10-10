<?php
//个人主页部分
namespace Home\Controller;
/**
* 个人主页
*/
class AlbumController extends BaseController{

    // 显示个人主页
	public function albumIndex($id){
        $this->assign('albumList',$this->selectAlbum($id));
        $this->index($id);
    }

    public function add(){
        $data['title'] = $_POST['title'];
        $data['describe'] = $_POST['des'];
        $data['address'] = $_POST['address'];
        $data['status'] = $_POST['status'];
        $data['uid'] = $_POST['id'];
        $data['addtime'] = time();
        $album = M('album');
        // dump($data);die();
        $id = $album->add($data);
        echo $id;
        die();
    }

    public function del(){
        $id = $_POST['id'];
        // $id = 18;
        $album = M('album');
        $pic = M('pic');
        $picList = $pic->where('aid='.$id)->getField('picname');
        if($album->where('id='.$id)->delete()){
            if($pic->where('aid='.$id)->delete()){
                foreach($picList as $k=>$v){
                    if(file_exists('./Public/upload/images/'.$v)){
                        unlink('./Public/upload/images/'.$v);
                        unlink('./Public/upload/images/s_'.$v);
                    }
                }
            }
            die('y');
        }else{
            die($album->getError());
        }
    }

    function checkTitle(){
        $title = $_POST['title'];
        $album = M('album');
        if($album->where('title='.$title)->count()>0){
            die('n');
        }else{
            die('y');
        }
    }
}