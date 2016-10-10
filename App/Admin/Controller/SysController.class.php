<?php
namespace Admin\Controller;
use Think\Controller;
// 系统管理控制器
class SysController extends CommonController {

    // 提供表情管理界面
    public function index(){
        $icon = M('icon')->select();
        $this->assign('icon',$icon);
        $this->display();
    }
    // 提供无刷新表情管理界面
    public function show(){
        $icon = M('icon')->select();
        $str = json_encode($icon);
        die($str);
    }

    // 进行表情添加
    public function faceInsert(){
        // 获取文件后缀
        $str = $_FILES['pic']['name'];
        $hz = strrchr($str,'.');
        // $hz = '.jpg';

        $data['icon'] = $_POST['icon'].$hz;
        $data['reg'] = $_POST['icon'];
        $icon = M('icon');
        $uploadInfo = $this->faceUpload();
        // var_dump($uploadInfo);
        if(!is_array($uploadInfo)){
            $error = $uploadInfo;
            echo '<script type="text/javascript">
                    window.parent.addError("'.$error.'");
                </script>';
            die();
        }

        if($num = $icon->add($data)){
            $image = new \Think\Image();
                        // 引入图片处理库
            // @import('ORG.Util.Image.ThinkImage'); 
            // 使用GD库来处理1.gif图片
            // $image = new \Think\Image(THINKIMAGE_GD, './Public/upload/icon/'.$data['icon']); 
            // @$image = new \Think\Image(THINKIMAGE_GD);
            $str = './Public/upload/icon/'.$data['icon'];
            $s_str = './Public/upload/icon/s_'.$data['icon'];
            $m1 = $image->open($str);
            $m2 = $image->thumb(50,50)->save($str);
            $m2 = $image->thumb(16,16)->save($s_str);
            echo '<script type="text/javascript">
                    console.log("ssssss");
                    window.parent.addSuccess('.$num.');
                </script>';
            die();
        }else{
            //添加失败
            $url = U('Public/upload/icon');
            $str = substr($url,0,strrpos($url,'.'));
            unlink($str.'/'.$uploadInfo['pic']['savename']);
            // $this->error('添加失败');
            $error = '数据填写不正确';
            echo '<script type="text/javascript">
                    window.parent.addError("'.$error.'");
                </script>';
            die();
        }
    }

    // 进行表情上传
    public function faceUpload(){
        
        // 实例化上传类    
        $upload = new \Think\Upload();
        // 设置附件上传大小    
        $upload->maxSize   =     3145728 ;
        // 设置附件上传类型    
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
        // 设置附件上传目录
        $upload->rootPath  =      './Public/upload/icon/'; 
        // 上传图片是否创建新文件夹
        $upload->autoSub   =     false;
        // 设置上传文件名
        $upload->saveName  =     $_POST['icon'];
        // dump($upload->saveName);
        // 上传文件     
        $info   =   $upload->upload();
        // var_dump($info);
        if(!$info){
            return $upload->getError();
        }
        return $info;
    }

    // 进行表情修改
    public function faceUpdate(){
        // 获取文件后缀
        $str = $_FILES['pic']['name'];
        $hz = strrchr($str,'.');
        
        $id = $_POST['updateId'];
        // 查询原图片信息
        $oldIcon = M('icon')->where("id={$id}")->getField('icon');
        // $oldReg = M('icon')->where("id={$id}")->getField('reg');
        $data['reg'] = $_POST['icon'];
        $data['icon'] = $_POST['icon'].$hz;
        $icon = M('icon');
        if(!empty($_FILES['pic']['name'])){
            if($num = $icon->where("id={$id}")->save($data)){
                // 即更改图片，又更改数据的情况
                // 删除原图片
                if(file_exists('./Public/upload/icon/'.$oldIcon)){
                    unlink('./Public/upload/icon/'.$oldIcon);
                }
                if(file_exists('./Public/upload/icon/s_'.$oldIcon)){
                    unlink('./Public/upload/icon/s_'.$oldIcon);
                }
                // 添加新图片
                $uploadInfo = $this->faceUpload();
                // 如果添加失败，则将数据库还原;
                if(!is_array($uploadInfo)){
                    $data['icon'] = $oldIcon;
                    $data['reg'] = $data['icon'] ;
                    $icon->where("id={$id}")->save($data);
                    die($uploadInfo);
                }
                // 修改图片尺寸
                $image = new \Think\Image();  
                $str = './Public/upload/icon/'.$data['icon'];
                $s_str = './Public/upload/icon/s_'.$data['icon'];
                $back_str = './Public/upload/icon/back_'.$data['icon'];
                // echo $str;
                $m1 = $image->open($str);
                $m2 = $image->thumb(50,50)->save($str);
                $m2 = $image->thumb(16,16)->save($s_str);
                copy($str,$back_str);
                echo '<script type="text/javascript">
                        window.parent.editSuccess();
                    </script>';
                die();
            }else if($data['icon']==$oldIcon){
                // 只更换图片情况
                if(file_exists('./Public/upload/icon/'.$oldIcon)){
                    unlink('./Public/upload/icon/'.$oldIcon);
                }
                if(file_exists('./Public/upload/icon/s_'.$oldIcon)){
                    unlink('./Public/upload/icon/s_'.$oldIcon);
                }
                // 添加新图片
                $uploadInfo = $this->faceUpload();
                if(!is_array($uploadInfo)){
                    die($uploadInfo);
                }else{
                    // 修改图片尺寸
                    $image = new \Think\Image();  
                    $str = './Public/upload/icon/'.$data['icon'];
                    $back_str = './Public/upload/icon/back_'.$data['icon'];
                    $s_str = './Public/upload/icon/s_'.$data['icon'];
                    // echo $str;
                    $m1 = $image->open($str);
                    $m2 = $image->thumb(50,50)->save($str);
                    $m2 = $image->thumb(16,16)->save($s_str);
                    copy($str,$back_str);
                    echo '<script type="text/javascript">
                            window.parent.editSuccess();
                        </script>';
                    die(); 
                }
            }else{
                // echo '22222222222222';
                $url = U('Public/upload/icon');
                $str = substr($url,0,strrpos($url,'.'));
                unlink($str.'/'.$uploadInfo['pic']['savename']);
                // $this->error('修改失败');
                die('n');
            }
        }else{
            // 没有修改图片情况
            $hz = strrchr($oldIcon,'.');
            $data['reg'] = $data['icon'];
            $data['icon'] = $data['icon'].$hz;
            if($num = $icon->where("id={$id}")->save($data)){
                // 重命名图片
                rename('./Public/upload/icon/'.$oldIcon,'./Public/upload/icon/'.$data['icon']);
                rename('./Public/upload/icon/s_'.$oldIcon,'./Public/upload/icon/s_'.$data['icon']);
                $back_str = './Public/upload/icon/back_'.$data['icon'];
                $str = './Public/upload/icon/'.$data['icon'];
                copy($str,$back_str);
                echo '<script type="text/javascript">
                        window.parent.editSuccess();
                    </script>';
                die();
            }else{
                //添加失败
                die('n');
            }
        }
    }

    // 进行表情删除
    public function faceDel($id){
        $icon = M('icon');
        $picName = $icon->where('id='.$id)->getField('icon');
        $num = $icon->where('id='.$id)->delete();
        if($num>0){
            if(file_exists('./Public/upload/icon/'.$picName)){
                unlink('./Public/upload/icon/'.$picName);
            }
            if(file_exists('./Public/upload/icon/s_'.$picName)){
                unlink('./Public/upload/icon/s_'.$picName);
            }
            echo 'y';
        }else{
            echo 'n';
        }
        die();
    }

    // 删除垃圾文件
    public function delBack($url){
        if(file_exists('./Public/upload/icon/back_'.$url)){
            $res = unlink('./Public/upload/icon/back_'.$url);
        }
        echo $res;
        die();
    }

    // 查询表情图片
    public function sysSelectIcon(){
        $list = M('icon')->getField('reg',true);
        echo json_encode($list);
        die();
    }
}