<?php
//个人主页部分
namespace Home\Controller;
/**
* 个人主页
*/
class UserPageController extends BaseController{

    // 显示个人主页
	public function userPageIndex($id){
        // dump($_SESSION);die();
        $userNews = $this->selectUserNews($id);
        $this->assign('userNews',$userNews);
        // dump($userNews);die();
        $this->index($id);
    }

    //查询用户所有的新鲜事,返回新鲜事数组(包括点赞数和点赞状态)
    public function selectUserNews($id){
        $arr=array('talk','diary','album','talkpic');
        // $arr=array('talkpic');
        foreach($arr as $k=>$v){
            $mod=M($v);
            $row=$mod->where('uid='.$id)->select();
            foreach($row as $key=>$val){
                $list[]=$val;
            }
        }
        // var_dump($list);die();
        foreach ($list as $key => $value) {
            if($value['typeid'] == 7){
                $add = M('album')->select();
                $aid = $list[$key]['id'];
                $list[$key]['pic'] = M('pic')->where('aid='.$aid)->order('addtime desc')->limit(4)->field('id,picname')->select();
            }else if($value['typeid'] == 5){
                $add = M('album')->select();
                $wtypeid = $list[$key]['id'];
                $list[$key]['pic'] = M('pic')->where('wtype=5 AND wtypeid='.$wtypeid)->order('addtime desc')->limit(6)->field('id,picname')->select();
            }
            $list[$key]['discuss']=$this->selectDiscussContent($value['typeid'],$value['id']);
            $list[$key]['discussNum']=$this->selectDiscussNum($value['typeid'],$value['id']);
            $list[$key]['support']=$this->selectSupportNum($value['typeid'],$value['id']);
            $list[$key]['supportstate']=$this->selectSupportState($id,$value['typeid'],$value['id']);
        }
        $list = $this->listSort($list,'addtime');
        // echo '<pre>';
        // print_r($list);die();
        return $list;
    }

    // 测试
    public function ceshi(){
        $id = 5;
        $head = M('head');
        $num = $head->select();
        dump($num);
    }

    // 点赞 输入：type tyeid uid 返回：成功y 失败n
    public function userPageAddSupport($type,$typeid,$uid){
        $support = M('support');
        $data['type'] = $_POST['type'];
        $data['typeid'] = $_POST['typeid'];
        $data['uid'] = $_POST['uid'];
        $data['addtime'] = time();
        if($support->add($data)){
            echo 'y';
            die();
        }else{
            echo 'n';
            die();
        }
    }
    // 取消点赞 输入：type tyeid uid 返回：成功y 失败n
    public function userPageRemoveSupport($type,$typeid,$uid){
        $support = M('support');
        $num = $support->where('type='.$type.' AND typeid='.$typeid.' AND uid='.$uid)->delete();
        // $num = $support->where('uid='.$uid)->select();
        if($num>0){
            echo 'y';
            die();
        }else{
            echo 'n';
            die();
        }
    }

    // 提交评论 输入：type typeid content fid did
    public function addReply($type,$typeid,$content,$fid,$did=0){
        $discuss = M('discuss');
        $_POST['addtime'] = time();
        // dump($_POST);die();
        if($discuss->create()){
            if($discuss->add()){
                die('y');
            }
        }
        die('n');
    }

    // 上传头像 输入：id x y
    // 错误信息:0:文件上传失败;1:文件缩放失败;2:添加头像失败;3:修改头像失败;4:成功
    public function uploadHead(){
        // print_r($_FILES);die();
        $upload = new \Think\Upload();// 实例化上传类    
        $upload->maxSize   =     313345728 ;// 设置附件上传大小    
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
        $upload->rootPath  =      './Public/home/images/'; // 设置附件上传目录    // 上传文件     
        $upload->autoSub  =      false; 
        $info   =   $upload->upload();    
        if(!$info) {// 上传错误提示错误信息        
            die($upload->getError());
        }
        $info = array_values($info);
        $uploadName = $info[0]['savename'];
        die($uploadName);
    }
    public function updateHead(){
        $picName = $_POST['picName'];
        $x = $_POST['x'];
        $y = $_POST['y'];
        $image = new \Think\Image();
        $image->open('./Public/home/images/'.$picName);
        //将图片裁剪为177 50 30
        $width = $image->width();
        if($image->width()>320){
            $bili = $image->width()/320;
            $num = 177*$bili;
            $x = $x*$bili;
            $y = $y*$bili;
            $image->crop($num,$num,$x,$y)->save('./Public/home/images/'.$picName);
            $image->thumb(177,177)->save('./Public/home/images/'.$picName);
            $image->thumb(50,50)->save('./Public/home/images/m_'.$picName);
            $image->thumb(30,30)->save('./Public/home/images/s_'.$picName);
        }else{
            $num = 177;
            $image->crop($num,$num,$x,$y)->save('./Public/home/images/'.$picName);
            $image->thumb(50,50)->save('./Public/home/images/m_'.$picName);
            $image->thumb(30,30)->save('./Public/home/images/s_'.$picName);
        }
        if(!file_exists('./Public/home/images/'.$picName) || !file_exists('./Public/home/images/m_'.$picName) || !file_exists('./Public/home/images/s_'.$picName)){
            die('1');
        }
        $id = $_POST['id'];
        $data['uid'] = $_POST['id'];
        $data['picname'] = $picName;
        $head = M('head');
        $num = $head->where('uid='.$id)->count();
        if($num>0){
            if($head->where('uid='.$id)->save($data)){
                die('2');
            }else{
                die('4'); //修改失败
            }
        }else{
            if($head->add($data)){
                die('2');
            }else{
                die('3'); //添加失败
            }
        }
    }

    public function unlinkPic(){
        $picName = $_POST['picName'];
        $res = array();
        $res[] = unlink('./Public/home/images/'.$picName);
        $res[] = unlink('./Public/home/images/m_'.$picName);
        $res[] = unlink('./Public/home/images/s_'.$picName);
        echo $picName;
        dump($res);
    }
}