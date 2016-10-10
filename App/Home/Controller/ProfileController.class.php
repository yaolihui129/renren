<?php
// 个人资料部分
namespace Home\Controller;
/**
* 个人资料
*/
class ProfileController extends BaseController{

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
    // 个人详细信息修改
    public function updateDetail(){
        $uid = $_POST['id'];
        $key = $_POST['type'];
        $data[$key] = $_POST['content'];
        $userInfo = M('userinfo');
        $num = $userInfo->where('uid='.$uid)->count();
        if($num>0){
            if($userInfo->where('uid='.$uid)->save($data)){
                die('y');
            }else{
                die('修改失败');
            }
        }else{
            $data['uid'] = $uid;
            if($userInfo->add($data)){
                die('y');
            }else{
                die('添加失败');
            }
        }
    }
    // 个人基本信息修改
    public function saveBaseProfileLoad(){
        $id = $_POST['id'];
        $data['name'] = $_POST['name'];
        $data['sex'] = $_POST['sex'];
        $province = M('province')->where('code='.$_POST['provinceCode'])->getField('name');
        $city = M('city')->where('code='.$_POST['cityCode'])->getField('name');
        $data['address'] = $province.$city;
        $data['school'] = $_POST['school'];
        $data['birth'] = mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']);
        // echo '<pre>';print_r($data);die();
        $user = M('user');
        if($user->where('id='.$id)->save($data)){
            die('y');
        }else{
            die('n');
        }
    }

    // 地区二级联动
    public function loadAddress(){
        $city = M('city');
        $code = $_POST['code'];
        $list = $city->where('provinceCode='.$code)->select();
        $listJson = json_encode($list);
        die($listJson);
    }
    
}