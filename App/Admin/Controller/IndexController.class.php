<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {

    // 调用自己的方法
    public function index(){
        // 待审核
        $this->assign('numXhw',$this->countXhw());
        // 用户总数
        $this->assign('numAllUser',$this->countAllUser());
        // 今日用户新增
        $this->assign('numNewUser',$this->countNewUser());
        // 网站配置
        $this->assign('config',$this->countConfig());
        // 用户今日行为 
        $this->assign('numTodayLog',$this->countTodayLogNum());
        // 用户每日增加数目
        $this->assign('numDayUser',$this->countDayUser());
        // 拼装用户今日行为(拼装成可执行的js语句，json格式)
        $strTodayLog = $this->strToday();
        $this->assign('listCake',$strTodayLog);
        // dump($_SESSION);
        $this->display();
    }

    // 拼装用户今日行为(拼装成可执行的js语句，json格式)
    public function strToday(){
        $codeArr = $this->countTodayLogNum();
        array_pop($codeArr);
        $strTodayLog = 'var listCake='.json_encode($codeArr);
        $strTodayLog = str_replace('talkpic','发表图文并茂次数',$strTodayLog);
        $strTodayLog = str_replace('talk','发表说说次数',$strTodayLog);
        $strTodayLog = str_replace('diary','发表日志次数',$strTodayLog);
        $strTodayLog = str_replace('share','分享发表数',$strTodayLog);
        $strTodayLog = str_replace('addFriend','添加朋友次数',$strTodayLog);
        $strTodayLog = str_replace('support','点赞次数',$strTodayLog);
        $strTodayLog = str_replace('discuss','评论次数',$strTodayLog);
        $strTodayLog = str_replace('xhw','拉黑次数',$strTodayLog);

        return $strTodayLog;
    }
    // 统计待审核举报数目
    public function countXhw(){
        $num = M('xhw')->where('status=0')->count();
        // echo $num;
        return $num;
    }

    // 统计总用户数目
    public function countAllUser(){
        $num = M('user')->where('status=3')->count();
        // echo $num;
        return $num;
    }

    // 统计今日新增用户数目
    public function countNewUser(){
        $thisTime = mktime(0,0,0,date('m',time()),date('d',time()),date('Y',time()));
        $num = M('user')->where('addtime>'.$thisTime.' AND status=3')->count();
        // echo $num;
        return $num;
    }

    // 统计网站配置 返回数组格式
    public function countConfig(){
        $info = array();
        // php路径信息
        $info['php_url'] = $_SERVER['PHP_SELF'];
        // 当前运行脚本所在的服务器的 IP 地址
        $info['ip'] = $_SERVER['SERVER_ADDR'];
        // 当前运行脚本所在的服务器的主机名
        $info['server_name'] = $_SERVER['SERVER_NAME'];
        // 当前运行脚本所在的文档根目录
        $info['document_url'] = $_SERVER['DOCUMENT_ROOT'];
        // 浏览当前页面的用户的 IP 地址
        $info['remote_url'] = $_SERVER['REMOTE_ADDR'];
        // 包含当前脚本的路径
        $info['script_url'] = $_SERVER['SCRIPT_NAME'];
        // dump($info);
        return $info;
    }

    // 统计用户今日行为数目 返回数组格式
    public function countTodayLogNum(){
        // 设置今日凌晨时间戳
        $thisTime = mktime(0,0,0,date('m',time()),date('d',time()),date('Y',time()));
        $num = array();
        // 统计今日发表新鲜事
        $num['talk'] = M('talk')->where('addtime>'.$thisTime)->count();
        $num['talkpic'] = M('talkpic')->where('addtime>'.$thisTime)->count();
        $num['diary'] = M('diary')->where('addtime>'.$thisTime)->count();
        $num['share'] = M('share')->where('addtime>'.$thisTime)->count();

        // 统计用户添加好友
        $num['addFriend'] = M('relation')->where('addtime>'.$thisTime)->count();

        // 统计今日新增评论 点赞 举报
        $num['support'] = M('support')->where('addtime>'.$thisTime)->count();
        $num['discuss'] = M('discuss')->where('addtime>'.$thisTime)->count();
        $num['xhw'] = M('xhw')->where('addtime>'.$thisTime)->count();
        $num['total'] = $num['talk']+$num['talkpic']+$num['diary']+$num['share']+$num['addFriend']+$num['support']+$num['discuss']+$num['xhw'];
        // dump($num);
        return $num;
    }

    // 统计用户每天增加数目 返回：可执行的js语句
    public function countDayUser(){
        $user = M('user')->select();
        $str = array();
        // 拼装数组（键:日期；值:数目）
        foreach($user as $v){
            $str[] = date('Y',$v['addtime']).'-'.date('m',$v['addtime']).'-'.date('d',$v['addtime']);
        }
        $list = array_count_values($str);
        // 将其转化成json格式，并拼装成可执行的js语句
        $jsonList = json_encode($list);
        $jsonStr = 'var listLine='.$jsonList;
        // dump($jsonStr);
        return $jsonStr;
    }

}