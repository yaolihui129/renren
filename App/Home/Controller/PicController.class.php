<?php
//相册图片部分
namespace Home\Controller;
/**
* 相册图片
*/
class PicController extends BaseController{

    // 显示个人主页
	public function picIndex($id,$aid){
        $picAlbum = M('album')->where('id='.$aid)->find();
        $picNum = M('pic')->where('aid='.$aid)->count();
        $picList = $this->selectPic($aid);
        // dump($picAlbum);die();
        $this->assign('picList',$picList);
        $this->assign('picAlbum',$picAlbum);
        $this->assign('picNum',$picNum);
        $this->index($id);
    }

    // 无刷新查询图片单个图片信息
    public function showPicDetail(){
        $id = $_POST['id'];
        $list = M('pic')->where('id='.$id)->find();
        if($list['picdes'] == ''){
            $list['picdes'] = '还没有图片描述...';
        }
        $list['discussNum'] = $this->selectDiscussNum(2,$id);
        $list['supportNum'] = $this->selectSupportNum(2,$id);
        $list['shareNum'] = $this->selectShareNum(2,$id);
        $list['year'] = date('Y',$list['addtime']);
        $list['mouth'] = date('m',$list['addtime']);
        $list['day'] = date('d',$list['addtime']);
        $listStr = json_encode($list);
        die($listStr);
    }
    // 无刷新设置相册封面
    public function setAlbumCover(){
        $id = $_POST['id'];
        $aid = M('pic')->where('id='.$id)->getField('aid');
        $album = M('album');
        $data['cover'] = M('pic')->where('id='.$id)->getField('picname');
        if($album->where('id='.$aid)->save($data)){
            die('y');
        }else{
            die($album->getError());
        }
    }
    // 无刷新删除图片
    public function delPic(){
        $id = $_POST['id'];
        $picName = $_POST['picName'];
        $pic = M('pic');
        if($pic->where('id='.$id)->delete()){
            unlink('./Public/upload/images/'.$picName);
            unlink('./Public/upload/images/s_'.$picName);
            die('y');
        }else{
            die($pic->getError());
        }
    }
    // 无刷新加载图片
    public function picLoad(){
        $uid = $_POST['uid'];
        $aid = $_POST['aid'];
        $pic = M('pic');
        $list = $pic->where('uid='.$uid.' AND aid='.$aid)->field('id,picname,bili')->select();
        // dump($list);
        $listStr = json_encode($list);
        die($listStr);
    }

    // 上传图片
    public function uploadPic(){
        // print_r($_FILES);die();
        $upload = new \Think\Upload();// 实例化上传类    
        $upload->maxSize   =     313345728 ;// 设置附件上传大小    
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
        $upload->rootPath  =      './Public/upload/images/'; // 设置附件上传目录    // 上传文件     
        $upload->autoSub  =      false; 
        $info   =   $upload->upload();    
        if(!$info) {// 上传错误提示错误信息        
            die($upload->getError());
        }
        $info = array_values($info);
        $list = array();
        foreach($info as $key=>$val){
            $list[] = $val['savename'];
        }
        $jsonList = json_encode($list);
        die($jsonList);
    }
    // 添加图片 加裁剪
    public function addPic(){
        $uid = $_POST['uid'];
        $aid = $_POST['aid'];
        $picName = $_POST['picName'];
        $picList = json_decode($picName);
        $image = new \Think\Image();
        $pic = M('pic');
        foreach($picList as $k=>$v){
            $image->open('./Public/upload/images/'.$v);
            $data['bili'] = round($image->height()/$image->width(),5);
            $min = min($image->width(),$image->height());
            $max = max($image->width(),$image->height());
            $r = $max*(150/$min);
            $image->thumb($r,$r)->save('./Public/upload/images/m_'.$v);
            $image->open('./Public/upload/images/m_'.$v);
            $image->crop(150, 150,\Think\Image::IMAGE_THUMB_CENTER)->save('./Public/upload/images/s_'.$v);
            unlink('./Public/upload/images/m_'.$v);
            $data['uid'] = $uid;
            $data['aid'] = $aid;
            $data['picname'] = $v;
            $data['addtime'] = time();
            $data['typeid'] = 2;
            if(!$pic->add($data)){
                $result = 'n';
            }
        }
        if($result == 'n'){
            foreach($picList as $k=>$v){
                unlink('./Public/upload/images/'.$v);
                unlink('./Public/upload/images/s_'.$v);
            }
            die($pic->getError());
        }else{
            $album = M('album');
            $albumData['addtime'] = time();
            $album->where('id='.$aid)->save($albumData);
            $num = count($picList);
            $this->addLog($uid,$aid,$num);
            die('y');
        }
    }

    // 写入行为日志
    public function addLog($uid,$aid,$num){
        $log = M('log');
        $user = M('user')->where('id='.$uid)->getField('name');
        $data['content'] = $user.'向编号为'.$aid.'的相册中添加了'.$num.'张图片';
        $data['addtime'] = time();
        $log->add($data);
    }

    // 删除图片
    public function unlinkPic(){
        $picName = $_POST['picName'];
        $res = array();
        $res[] = unlink('./Public/home/images/'.$picName);
        $res[] = unlink('./Public/home/images/m_'.$picName);
        $res[] = unlink('./Public/home/images/s_'.$picName);
        echo $picName;
        dump($res);
    }

    // 无刷新加载相册评论
    public function loadAlbumDiscuss(){
        $type = '7';
        $typeid = $_POST['typeid'];
        // $typeid = '17';
        $list = $this->selectDiscussContent($type,$typeid);
        // dump($list);
        $listJson = json_encode($list);
        die($listJson);
    }

    // 添加相册评论
    public function addAlbumDiscuss(){
        $data['type'] = 7;
        $data['typeid'] = $_POST['typeid'];
        $data['fid'] = $_POST['fid'];
        $data['content'] = $_POST['content'];
        $data['addtime'] = time();
        $discuss = M('discuss');
        if($discuss->add($data)){
            die('y');
        }else{
            die('n');
        }
    }

    // 进行批量删除
    public function delMorePic(){
        $pic = M('pic');
        $list = $_POST;
        foreach($list as $key=>$val){
            if($pic->where('id='.$key)->delete()){
                if(file_exists('./Public/upload/images/'.$val)){
                    unlink('./Public/upload/images/'.$val);
                }
                if(file_exists('./Public/upload/images/s_'.$val)){
                    unlink('./Public/upload/images/s_'.$val);
                }
            }else{
                die('n');
            }
        }
        die('y');
    }

    // 加载相册信息
    public function loadAlbumEditPageTwo(){
        $id = $_POST['id'];
        $album = M('album');
        $list = $album->where('id='.$id)->find();
        $listJson = json_encode($list);
        die($listJson);
    }

    // 修改相册信息
    public function updateAlbumEditPageTwo(){
        $id = $_POST['id'];
        $data['title'] = $_POST['title'];
        $data['describe'] = $_POST['describe'];
        $data['address'] = $_POST['address'];
        $album = M('album');
        if($album->where('id='.$id)->save($data)){
            die('y');
        }else{
            die('n');
        }
    }

    // 修改图片信息
    public function updatePicDescribe(){
        $id = $_POST['id'];
        $data['picdes'] = $_POST['describe'];
        $res = M('pic')->where('id='.$id)->save($data);
        if($res){
            die('y');
        }else{
            die('n');
        }
    }
}