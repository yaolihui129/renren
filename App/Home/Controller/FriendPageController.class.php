<?php
//个人主页部分
namespace Home\Controller;
/**
* 个人主页
*/
class FriendPageController extends BaseController{

    // 显示好友主页
    public function friendPageIndex($id,$fid){
        // dump($_SESSION);die();
        if($id==$fid){
            $this->redirect('UserPage/userPageIndex/id/'.$id);
            return;
        }
        $friendNews = $this->selectUserNews($fid);
        $friendInfo = $this->friendInfo($fid);
        $relationNum = $this->relationInfo($id,$fid);
        switch($relationNum){
            case -1:$relationInfo = '陌生人';break;
            case 0:$relationInfo = '已是好友';break;
            case 1:$relationInfo = '特别关注';break;
            case 2:$relationInfo = '黑名单';break;
            case 3:$relationInfo = '陌生人';break;
        }
        $this->assign('friendNews',$friendNews);
        $this->assign('friendInfo',$friendInfo);
        $this->assign('relationInfo',$relationInfo);
        $this->assign('relationNum',$relationNum);
        // echo '<pre>';
        // print_r($friendInfo);die();
        // dump($info);die();
        $this->index($id);
    }

    // 查询好友关系 输入：id fid 返回
    public function relationInfo($id,$fid){
        $relation = M('relation');
        $num = $relation->where('uid='.$id.' AND fid='.$fid)->getField('status');
        if($num == null){
            return -1;
        }else{
            return $num;
        }
    }

    // 查询好友基本信息 输入fid 输出好友基本信息
    public function friendInfo($fid){
        $list = $this->selectInfo($fid);
        $list['head'] = $this->selectHead($fid);
        $friendDetail = $this->selectDetail($fid);
        $list['work'] = $friendDetail['work'];
        // dump($list);
        return($list);
    }
    //查询用户所有的新鲜事,返回新鲜事数组(包括点赞数和点赞状态)
    public function selectUserNews($id){
        $arr=array('talk','diary','share','talkpic','pic');
        foreach($arr as $k=>$v){
            $mod=M($v);
            $row=$mod->where('uid='.$id)->select();
            foreach($row as $key=>$val){
                $list[]=$val;
            }
        }
        //var_dump($list);die();
        foreach ($list as $key => $value) {
            if($value['typeid'] == 6){
                switch($value['type']){
                    case 0:
                        // 说说
                        $add = M('talk')->where('id='.$value['pretypeid'])->select();
                        $list[$key]['sharecontent'] = $add[0]['content'];
                        $list[$key]['fromuser'] = M('user')->where("id={$add[0]['uid']}")->getField('name') ;
                        break;
                    case 1:
                        // 日志
                        $add = M('diary')->where('id='.$value['pretypeid'])->select();
                        $list[$key]['sharecontent'] = $add[0]['content'];
                        $list[$key]['sharetitle'] = $add[0]['title'];
                        $list[$key]['fromuser'] = M('user')->where("id={$add[0]['uid']}")->getField('name') ;
                        break;
                    case 2:
                        // 图片
                        $add = M('pic')->where('id='.$value['pretypeid'])->select();
                        $list[$key]['sharecontent'] = $add[0]['picdes'];
                        $list[$key]['sharepic'] = $add[0]['picname'];
                        $fromuser = M('album')->where("id={$add[0]['aid']}")->getField('uid');
                        $list[$key]['fromuser'] = M('user')->where("id={$fromuser}")->getField('name') ;
                        $list[$key]['formalbum'] = M('album')->where("id={$add[0]['aid']}")->getField('title') ;
                        $list[$key]['formalbumnum'] = M('pic')->where("aid={$add[0]['aid']}")->count() ;
                        // dump($list[$key]);
                        break;
                }
            }
            $list[$key]['discuss']=$this->selectDiscussContent($value['typeid'],$value['id']);
            $list[$key]['discussNum']=$this->selectDiscussNum($value['typeid'],$value['id']);
            $list[$key]['support']=$this->selectSupportNum($value['typeid'],$value['id']);
            $list[$key]['supportstate']=$this->selectSupportState($id,$value['typeid'],$value['id']);
        }
        $list = $this->listSort($list,'addtime');
        // echo '<pre>';
        // print_r($list);
        return $list;
    }

    // 测试
    public function ceshi(){
        dump($_SESSION);
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

    // 添加好友
    public function addFriend($id,$fid,$gid){
        $relation = M('relation');
        $num = $relation->where('uid='.$id.' AND fid='.$fid)->count();
        if($num>0){
            die('repeat');
        }
        $data['uid'] = $_POST['id'];
        $data['fid'] = $_POST['fid'];
        $data['addtime'] = time();
        $data['status'] = 3;
        // dump($data);die();
        if($res=$relation->add($data)){
            $sys=M('Sys');
            $sysinfo['content']='<a href="/renren/index.php/home/FriendPage/friendPageIndex/id/'.$data["fid"].'/fid/'.$data["uid"].'">'.$this->selectInfo($data["uid"])["name"].'</a> 请求加您好友';
            $sysinfo['uid']=$data['fid'];
            $sysinfo['addtime']=time();
            $sysinfo['info']=$res;
            $sys->add($sysinfo);

            die('y');
        }
        die('n');
    }

    // 删除好友
    public function removeFriend($id,$fid){
        $relation = M('relation');
        $num = $relation->where('uid='.$id.' AND fid='.$fid)->count();
        if($num == 0){
            die('repeat');
        }
        $num = $relation->where('uid='.$id.' AND fid='.$fid)->delete();
        if($num>0){
            die('y');
        }
        die('n');
    }

    // 修改好友分组
    public function updateFriend(){ 
        $relation = M('relation');
        $data['uid'] = $_POST['id'];
        $data['fid'] = $_POST['fid'];
        $data['addtime'] = time();
        $data['status'] = $_POST['gid'];
        // dump($_POST);die();
        $id = $relation->where('uid='.$data['uid'].' AND fid='.$data['fid'])->getField('id');
        if($relation->where('id='.$id)->save($data)){
            die('y');
        }
        die('n');
    }


    /********* 好友资料的查看 *********/
    // 显示个人资料
    public function profileIndex($id){
        $this->index($id);
    }

    // 无刷新显示个人详细资料
    public function detailProfileLoad(){
        $uid = $_POST['uid'];
        // $uid = 5;
        $list = M('userinfo')->where('uid='.$uid)->find();
        $listJson = json_encode($list);
        die($listJson);
    }

    // 无刷新显示个人基本资料
    public function baseProfileLoad(){
        $id = $_POST['id'];
        // $id = 5;
        $list = M('user')->where('id='.$id)->find();
        $list['birthYear'] = date('Y',$list['birth']);
        $list['birthMouth'] = (string)(date('m',$list['birth']) + 0);
        $list['birthDay'] = (string)(date('d',$list['birth']) + 0);
        $sheng = M('province')->getField('name',true);
        foreach($sheng as $v){
            if(substr_count($list['address'],$v)>0){
                $list['addressSheng'] = $v;
                $list['addressCity'] = str_replace($list['addressSheng'],'',$list['address']);
            }
        }
        // dump($list);
        $listJson = json_encode($list);
        die($listJson);
    }

    // 通向好友资料界面
    public function friendProfileIndex($id,$fid){
        $this->index($id);
    }

    

}
