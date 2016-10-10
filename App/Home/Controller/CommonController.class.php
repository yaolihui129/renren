<?php
//Common类用于服务子类大部分的查询功能
namespace Home\Controller;
use \Think\Controller;
/**
* Common类的定义
*/
class CommonController extends Controller{

	// 判断用户有没有登录
	public function _initialize(){
		if(!isset($_SESSION['homeuser']) || empty($_SESSION['homeuser']) || !($_SESSION['homeuser']['state'])){
			$this->redirect('Public/login');
			exit();
		}
		
	}
	
	//查询用户所有状态
	public function selectUserNews($id){
		$arr=array('talk','album','diary','share','talkpic');
		foreach($arr as $k=>$v){
			$mod=M($v);
			switch ($v) {
				case 'diary':
					$row=$mod->where("uid={$id} AND status <> 0")->select();
					break;
				default:
					$row=$mod->where('uid='.$id)->select();
					break;
			}

			
			
			foreach($row as $key=>$val){
				$list[]=$val;
			}
		}
		foreach ($list as $key => $value) {
			$list[$key]['head']=$this->selectHead($value['uid']);
			$list[$key]['name']=$this->selectInfo($value['uid'])['name'];
			$list[$key]['support']=$this->selectSupportNum($value['typeid'],$value['id']);
			$list[$key]['discuss']=$this->selectDiscussContent($value['typeid'],$value['id']);
			$list[$key]['discussNum']=$this->selectDiscussNum($value['typeid'],$value['id']);
			if($value['typeid'] == 5){
				$list[$key]['talkpic']=$this->selectTalkPic($value['typeid'],$value['id']);
				$list[$key]['talkpicnum']=count($list[$key]['talkpic']);
			}
			if($value['typeid'] == 7){
				$list[$key]['pic']=$this->selectPic($value['id']);
				$list[$key]['picnum']=count($list[$key]['pic']);
			}
		}
		$list = $this->listSort($list,'addtime');
		// var_dump($list);
		return $list;
	}

	//将二维数组按照其中一个字段排序
	public function listSort($arr,$field){
		foreach ($arr as $key => $value) {
			$ziduan[$key]=$value[$field];
		}
		array_multisort($ziduan,$arr);
		return array_reverse($arr);
	}

	//查询所有好友的状态，包括自己,(包括点赞数和点赞状态)
	public function selectAllNews($id){
		$fri=$this->selectFriend($id);
		$fri[]=array('fid'=>strval($id));
		//var_dump($fri);die();
		foreach ($fri as $key => $value) {
			//echo $value['fid'];
			$rows=$this->selectUserNews($value['fid']);
			if(!empty($rows)){
				foreach($rows as $k => $v){
					$list[]=$v;
				}
			}
		}
		//var_dump($list);
		$list=$this->listSort($list,'addtime');
		foreach ($list as $key => $value) {		
			$list[$key]['supportstate']=$this->selectSupportState($id,$value['typeid'],$value['id']);
		}
        // dump($list);
		return $list;
	}

	//查询所有原创动态，包括自己和所有好友(包括点赞数和点赞状态)的数组
	public function selectCreateNews($id){
		$list=$this->selectAllNews($id);
		//var_dump($list);die();
		foreach ($list as $key => $value) {
			if($value['typeid'] != 6){
				$rows[]=$value;
			}
		}
		//var_dump($rows);
		return $rows;
	}

	//查询分组，返回分组列表数组
	public function selectGroup($id){
		$mod=M('group');
		$list=$mod->where("uid='{$id}'")->select();
		$rel=M('Relation');
		foreach($list as $k=>$v){
			$list[$k]['friNum']=$rel->where("gid={$v['id']}")->count();
		}
		// var_dump($list);die();
		return $list;
	}

	//查询未分组的好友
	public function selectNoGroup($id){
		$rel=M('Relation');
		$list=$rel->where("uid={$id} AND gid=0 AND status<>2")->select();
		// var_dump($list);die();
		$list['num']=count($list);
		$list['name']='未分组好友';
		// var_dump($list);die();
		return $list;
	}

	//通过分组id来得到分组的名称
	public function selectGroupName($gid){
		$mod=M('group');
		$name=$mod->where("id={$gid}")->select();
		return $name[0]['gname'];
	}

	// 查询好友列表，返回好友列表数组
	public function selectFriend($id){
		$mod=M('Relation');
		$fri=$mod->where("uid='{$id}' AND status <> '2' AND status <> '3'")->select();
		foreach ($fri as $key => $value) {
			$fri[$key]['head']=$this->selectHead($value['fid']);
			$fri[$key]['name']=$this->selectInfo($value['fid'])['name'];
			//$fri[$key]['samefri']=$this->selectSameFriendNum($id,$value['fid']);
			$fri[$key]['gname']=$this->selectGroupName($value['gid']);
			$fri[$key]['school']=$this->selectInfo($value['fid'])['school'];
		}
		// var_dump($fri);die();
		return $fri;
	}

	//查询分组好友，返回一个三维数组，如$list[分组的组名][索引][分组里面的成员]
	public function selectGroupFriend($id){
		$rows=$this->selectGroup($id);
		foreach ($rows as $key => $value) {
			$mod=M('Relation');
			$list[$value['id']]=$mod->where("uid='{$id}' AND status <> '2'  AND status <> '3' AND gid='{$value['id']}'")->select();
		}
		//var_dump($list);
		return $list;
	}

	//查询分组好友，返回一个一维数组，如$list[分组的组名=>分组里面的成员id组成的字符串]
	public function selectGroupFriendStr($id){
		$list=$this->selectGroupFriend($id);
		foreach ($list as $key => $value) {
			foreach ($value as $k => $v) {
				$row[$key].=','.$v['fid'];
			}
			$row[$key]=ltrim($row[$key],',');
		}
		//var_dump($list);
		//var_dump($row);
		return $row;
	}

	//查询特别关注好友,返回由特别关心好友组成的字符串
	public function selectSpecialFriend($id){
		$fri=M('Relation');
		$list=$fri->field('fid')->where("uid={$id} AND status=1")->select();
		foreach($list as $k=>$v){
			$row[]=$v['fid'];
		}
		$str=implode(',', $row);
		//var_dump($str);
		return $str;
	}

	//查询共同好友的数量
	public function selectSpecialFriendNum($id){
		$fri=M('Relation');
		$list=$fri->field('fid')->where("uid={$id} AND status=1")->select();
		foreach($list as $k=>$v){
			$row[]=$v['fid'];
		}
		
		$num=count($row);
		// var_dump($num);
		return $num;
	}

	//查询共同好友的数目
	public function selectSameFriendNum($uid,$fid){
		$ulist=$this->selectFriend($uid);
		foreach ($ulist as $key => $value) {
			$urows[]=$value['fid'];
		}
		$flist=$this->selectFriend($fid);
		foreach ($flist as $key => $value) {
			$frows[]=$value['fid'];
		}
		$jlist=array_intersect($urows, $frows);
		$num=count($jlist);
		//var_dump($urows);var_dump($frows);var_dump($jlist);
		//echo $num;
		return $num;
	}

	//查询圈子好友动态(包括自己的分组以及关注)
	public function selectContentGroup($id){
		$flist=$this->selectGroupFriend($id);
		//var_dump($flist);die();
		foreach ($flist as $key => $value) {
			foreach ($value as $k => $v) {
				$rows[$key][]=$this->selectUserNews($v['fid']);
			}
			foreach($rows[$key] as $k=>$v){
				foreach($v as $kk=>$vv){
					$context[$key][]=$vv;
				}
				$context[$key]=$this->listSort($context[$key],'addtime');
			}
		}
		//var_dump($context);
		return $context;
	}

	//查询用户基本信息,返回用户信息数组
	public function selectInfo($id){
		$mod=M('User');
		$info=$mod->where("id='{$id}'")->select();
		//var_dump($info[0]);
		return $info[0];
	}

	//查询用户头像,返回字符串，是图像的名称
	public function selectHead($id){
		$mod=M('Head');
		$head=$mod->where("uid='{$id}'")->select();
		//var_dump($head[0]['picname']);
		if(!$head[0]['picname']){
			$head[0]['picname']='men_tiny.gif';
		}
		return $head[0]['picname'];
	}

	//查询用户详细信息，返回数组
	public function selectDetail($id){
		$mod=M('Userinfo');
		$info=$mod->where("uid='{$id}'")->select();
		//var_dump($info[0]);
		return $info[0];
	}

	//查询最近系统消息
	public function selectRecentSysMessage($id){
		$sys=M('Sys');
		$list=$sys->where("uid={$id}")->order('addtime desc')->select();
		foreach ($list as $key => $value) {
			$list[$key]['head']=$this->selectHead($value['uid']);
		}
		return $list;
		// var_dump($list);
	}

	//查询最近访客,返回一个二维数组，包括访问时间，访问者，访问者头像$rows[0]['head']/['name']/['head']
	public function selectVisitor($id){
		$mod=M('Visitor');
		$visitor=$mod->where("uid='{$id}'")->order("addtime DESC")->select();
		//var_dump($visitor);
		$field=array();
		foreach($visitor as $k=>$v){
			if(!in_array($v['fid'], $field)){
				$field[]=$v['fid'];
				$list[]=$v;
			}
		}
		$i=0;
		foreach ($list as $key => $value) {
			$rows[$i]['time']=$value['addtime'];
			$info=$this->selectInfo($value['fid']);
			$rows[$i]['name']=$info['name'];
			$head=$this->selectHead($value['fid']);
			$rows[$i]['head']=$head;
			$i++;
		}
		//var_dump($rows);
		return $rows;
	}

	// 查询好友生日 提醒 输入：用户uid  输出：生日为最近俩月的好友列表
    public function selectBirth($uid){
        $relation = M('relation');
        $list = $relation->where("uid='{$uid}' AND status <> '2'  AND status <> '3'")->getField('fid',true);
        $user = M('user')->select();
        $arr = array();
        foreach($user as $k=>$v){
            // 拼装时间戳 今年+出生月+出生日
            $time = mktime(0,0,0,date('m',$v['birth']),date('d',$v['birth']),date('Y',time()));
            $m = ($time-time())/86400;
            if(in_array($v['id'],$list) && $m>0 && $m<60){
                $arr[] = $v;
            }
        }
        // var_dump($arr);
        foreach ($arr as $key => $value) {
        	$row[$key]['head']=$this->selectHead($value['id']);
        	$row[$key]['name']=$value['name'];
        	$row[$key]['birth']=date('m-d',$value['birth']);
        }
        // var_dump($row);
        return $row;
    }
    
    // 查询相册 输入：用户id 返回：该用户的相册数组
    public function selectAlbum($uid){
        $album = M('album');
        $list = $album->where('uid='.$uid)->select();
        foreach($list as $k=>$v){
            $list[$k]['picNum'] = M('pic')->where('aid='.$v['id'])->count();
            if(!file_exists('./Public/upload/images/'.$list[$k]['cover'])){
                $list[$k]['cover'] = '200.png';
            }
        }
        // dump($list);
        return $list;
    }
    
    // 查询某相册图片 输入：相册id 返回：该相册的图片
    public function selectPic($aid){
        $pic = M('pic');
        $list = $pic->where('aid='.$aid)->select();
        return $list;
    }
    
    // 查询最近的三张图片 输入：用户uid 返回：三张最近图片（数组）
    public function selectRecentPic($uid){
        $pic = M('pic');
        $list = $pic->where('uid='.$uid)->order('addtime desc')->limit(3)->select();
        return $list;
    }
    
    // 查询某用户分享 输入：用户uid 返回：用户的所有分享（按时间轴倒序）
    public function selectShare($uid){
        $share = M('share');
        $list = $share->where('uid='.$uid)->order('addtime desc')->select();
        return $list;
    }
    
    // 查询某用户说说 输入：用户uid 返回：用户的所有说说（按时间轴倒序）
    public function selectTalk($uid){
        $talk = M('talk');
        $list = $talk->where('uid='.$uid)->order('addtime desc')->select();
        return $list;
    }
    
    // 查询某用户日志 输入：用户uid 返回：用户的所有日志（按时间轴倒序）
    public function selectDiary($uid){
        $diary = M('diary');
        $list = $diary->where('uid='.$uid)->order('addtime desc')->select();
        return $list;
    }
    
    // 查询友情链接 输入：无 返回：审核通过的友情链接（数组）
    public function selectLink(){
        $link = M('link');
        $list = $link->where('status=0')->select();
        return $list;
    }
    
    // 查询分享数目 输入：type typeid 返回：点赞数目
    public function selectShareNum($type,$typeid){
        $share = M('share');
        $num = $share->where('type='.$type.' AND pretypeid='.$typeid)->count();
        // dump($num);
        return $num;
    }

    // 查询点赞数目 输入：type typeid 返回：点赞数目
    public function selectSupportNum($type,$typeid){
        $support = M('support');
        $num = $support->where('type='.$type.' AND typeid='.$typeid)->count();
        return $num;
    }
    
    // 查询点赞状态 输入：点赞人id 新鲜事类型type 此类型的id：typeid  返回true或false
    public function selectSupportState($uid,$type,$typeid){
        $support = M('support');
        $num = $support->where('type='.$type.' AND typeid='.$typeid.' AND uid='.$uid)->count();
        if($num>0){
            return true;
        }else{
            return false;
        }
    }
    
    // 查询某个新鲜事的评论 输入：类型type，类型id typeid 返回：评论内容（数组）+评论人姓名+评论人头像
    public function selectDiscussContent($type,$typeid){
        $discuss = M('discuss');
        $disList = $discuss->where('type='.$type.' AND typeid='.$typeid)->order('addtime desc')->select();
        $headList = M('head')->getField('uid,picname');
        $userNameList = M('user')->getField('id,name');
        foreach($disList as $kkk=>$vvv){
            if(!$headList[$vvv['fid']]){
                $headList[$vvv['fid']] = 'men_tiny.gif';
            }
            $disList[$kkk]['userName'] = $userNameList[$vvv['fid']];
            $disList[$kkk]['headName'] = $headList[$vvv['fid']];
        }
        // dump($disList);
        return $disList;
    }
    
    // 查询评论数目
    public function selectDiscussNum($type,$typeid){
        $discuss = M('discuss');
        $num = $discuss->where('type='.$type.' AND typeid='.$typeid)->count();
        return $num;
    }
    
    // 查询过往的今天 输入：用户id 返回：该用户以及该用户好友过往的今日的新鲜事。。。
    public function selectLastYear($uid){
        $list = $this->selectAllNews($uid);
        $arr = array();
        foreach($list as $v){
            if(date('m',$v['addtime'])==date('m',time()) && date('d',$v['addtime'])==date('d',time())){
                $arr[] = $v;
            }
        }
        // dump($arr);
        return $arr;
    }
    
    // 查询游戏成绩 返回用户得分数组，包含id，用户uid，得分，用户名，用户头像名
    public function selectGameGoal(){
        $game = M('game');
        $gameList = $game->order('score desc')->select();
        $headList = M('head')->where('status=0')->getField('uid,picname');
        $userNameList = M('user')->getField('id,user');
        foreach($gameList as $k=>$v){
            $gameList[$k]['userName'] = $userNameList[$v['uid']];
            $gameList[$k]['headName'] = $headList[$v['uid']];
        }
        // dump($gameList);
        return $gameList;
    }
    
    //查询好友游戏排名
    public function selectGameRank($id){
    	$game=M('Game');
    	$fri=$this->selectFriend($id);
		$fri[]=array('fid'=>strval($id));
    	foreach($fri as $k=>$v){
    		$row[$k]=$game->where("uid={$v['fid']}")->select();
    	}
    	//var_dump($fri);
    	// var_dump($row);
    	$ii=0;
    	foreach ($row as $key => $value) {

    		if($value){
    			$list[$ii]['score']=$value[0]['score'];
    			$list[$ii]['head']=$this->selectHead($value[0]['uid']);
    			$list[$ii]['name']=$this->selectInfo($value[0]['uid'])['name'];
    			$ii++;
    		}
    	}
    	// var_dump($list);
    	$ranklist=$this->listSort($list,'score');
    	return $ranklist;
    }

    // 搜索用户 输入：字符串（模糊查询） 返回：用户名中含有该字段的用户（数组）
    public function selectSearchFriend($str){
        $user = M('user');
        $list = $user->where("name LIKE '%{$str}%'")->select();
        //dump($list);
        return $list;
    }

    //查询用户之间的对话
    public function selectDialog($uid,$fid){
    	$dialog=M('Dialog');
    	$list=$dialog->where("(uid={$uid} AND fid={$fid}) || (uid={$fid} AND fid={$uid})")->order("addtime desc")->select();
    	$list = $this->listSort($list,'addtime');
    	//var_dump($list);
    	//var_dump("(uid={$uid} AND fid={$fid}) || (uid={$fid} AND fid={$uid})");die();
    	return array_reverse($list);
    }

    //按照类型和其id来查询图片
    public function selectTalkPic($type,$typeid){
    	$talkpic=M('Pic');
    	$list=$talkpic->where("wtype={$type} AND wtypeid={$typeid}")->select();
    	//var_dump($list);
    	return $list;
    }

    //查询所有小表情
    public function selectLittleIcon(){
    	$icon=M('Icon');
    	$list=$icon->select();
    	//var_dump($list);
    	return $list;
    }

    //拼音转化成汉字
    private static $_aMaps = array(  
        'a'=>-20319,'ai'=>-20317,'an'=>-20304,'ang'=>-20295,'ao'=>-20292,  
        'ba'=>-20283,'bai'=>-20265,'ban'=>-20257,'bang'=>-20242,'bao'=>-20230,'bei'=>-20051,'ben'=>-20036,'beng'=>-20032,'bi'=>-20026,'bian'=>-20002,'biao'=>-19990,'bie'=>-19986,'bin'=>-19982,'bing'=>-19976,'bo'=>-19805,'bu'=>-19784,  
        'ca'=>-19775,'cai'=>-19774,'can'=>-19763,'cang'=>-19756,'cao'=>-19751,'ce'=>-19746,'ceng'=>-19741,'cha'=>-19739,'chai'=>-19728,'chan'=>-19725,'chang'=>-19715,'chao'=>-19540,'che'=>-19531,'chen'=>-19525,'cheng'=>-19515,'chi'=>-19500,'chong'=>-19484,'chou'=>-19479,'chu'=>-19467,'chuai'=>-19289,'chuan'=>-19288,'chuang'=>-19281,'chui'=>-19275,'chun'=>-19270,'chuo'=>-19263,'ci'=>-19261,'cong'=>-19249,'cou'=>-19243,'cu'=>-19242,'cuan'=>-19238,'cui'=>-19235,'cun'=>-19227,'cuo'=>-19224,  
        'da'=>-19218,'dai'=>-19212,'dan'=>-19038,'dang'=>-19023,'dao'=>-19018,'de'=>-19006,'deng'=>-19003,'di'=>-18996,'dian'=>-18977,'diao'=>-18961,'die'=>-18952,'ding'=>-18783,'diu'=>-18774,'dong'=>-18773,'dou'=>-18763,'du'=>-18756,'duan'=>-18741,'dui'=>-18735,'dun'=>-18731,'duo'=>-18722,  
        'e'=>-18710,'en'=>-18697,'er'=>-18696,  
        'fa'=>-18526,'fan'=>-18518,'fang'=>-18501,'fei'=>-18490,'fen'=>-18478,'feng'=>-18463,'fo'=>-18448,'fou'=>-18447,'fu'=>-18446,  
        'ga'=>-18239,'gai'=>-18237,'gan'=>-18231,'gang'=>-18220,'gao'=>-18211,'ge'=>-18201,'gei'=>-18184,'gen'=>-18183,'geng'=>-18181,'gong'=>-18012,'gou'=>-17997,'gu'=>-17988,'gua'=>-17970,'guai'=>-17964,'guan'=>-17961,'guang'=>-17950,'gui'=>-17947,'gun'=>-17931,'guo'=>-17928,  
        'ha'=>-17922,'hai'=>-17759,'han'=>-17752,'hang'=>-17733,'hao'=>-17730,'he'=>-17721,'hei'=>-17703,'hen'=>-17701,'heng'=>-17697,'hong'=>-17692,'hou'=>-17683,'hu'=>-17676,'hua'=>-17496,'huai'=>-17487,'huan'=>-17482,'huang'=>-17468,'hui'=>-17454,'hun'=>-17433,'huo'=>-17427,  
        'ji'=>-17417,'jia'=>-17202,'jian'=>-17185,'jiang'=>-16983,'jiao'=>-16970,'jie'=>-16942,'jin'=>-16915,'jing'=>-16733,'jiong'=>-16708,'jiu'=>-16706,'ju'=>-16689,'juan'=>-16664,'jue'=>-16657,'jun'=>-16647,  
        'ka'=>-16474,'kai'=>-16470,'kan'=>-16465,'kang'=>-16459,'kao'=>-16452,'ke'=>-16448,'ken'=>-16433,'keng'=>-16429,'kong'=>-16427,'kou'=>-16423,'ku'=>-16419,'kua'=>-16412,'kuai'=>-16407,'kuan'=>-16403,'kuang'=>-16401,'kui'=>-16393,'kun'=>-16220,'kuo'=>-16216,  
        'la'=>-16212,'lai'=>-16205,'lan'=>-16202,'lang'=>-16187,'lao'=>-16180,'le'=>-16171,'lei'=>-16169,'leng'=>-16158,'li'=>-16155,'lia'=>-15959,'lian'=>-15958,'liang'=>-15944,'liao'=>-15933,'lie'=>-15920,'lin'=>-15915,'ling'=>-15903,'liu'=>-15889,'long'=>-15878,'lou'=>-15707,'lu'=>-15701,'lv'=>-15681,'luan'=>-15667,'lue'=>-15661,'lun'=>-15659,'luo'=>-15652,  
        'ma'=>-15640,'mai'=>-15631,'man'=>-15625,'mang'=>-15454,'mao'=>-15448,'me'=>-15436,'mei'=>-15435,'men'=>-15419,'meng'=>-15416,'mi'=>-15408,'mian'=>-15394,'miao'=>-15385,'mie'=>-15377,'min'=>-15375,'ming'=>-15369,'miu'=>-15363,'mo'=>-15362,'mou'=>-15183,'mu'=>-15180,  
        'na'=>-15165,'nai'=>-15158,'nan'=>-15153,'nang'=>-15150,'nao'=>-15149,'ne'=>-15144,'nei'=>-15143,'nen'=>-15141,'neng'=>-15140,'ni'=>-15139,'nian'=>-15128,'niang'=>-15121,'niao'=>-15119,'nie'=>-15117,'nin'=>-15110,'ning'=>-15109,'niu'=>-14941,'nong'=>-14937,'nu'=>-14933,'nv'=>-14930,'nuan'=>-14929,'nue'=>-14928,'nuo'=>-14926,  
        'o'=>-14922,'ou'=>-14921,  
        'pa'=>-14914,'pai'=>-14908,'pan'=>-14902,'pang'=>-14894,'pao'=>-14889,'pei'=>-14882,'pen'=>-14873,'peng'=>-14871,'pi'=>-14857,'pian'=>-14678,'piao'=>-14674,'pie'=>-14670,'pin'=>-14668,'ping'=>-14663,'po'=>-14654,'pu'=>-14645,  
        'qi'=>-14630,'qia'=>-14594,'qian'=>-14429,'qiang'=>-14407,'qiao'=>-14399,'qie'=>-14384,'qin'=>-14379,'qing'=>-14368,'qiong'=>-14355,'qiu'=>-14353,'qu'=>-14345,'quan'=>-14170,'que'=>-14159,'qun'=>-14151,  
        'ran'=>-14149,'rang'=>-14145,'rao'=>-14140,'re'=>-14137,'ren'=>-14135,'reng'=>-14125,'ri'=>-14123,'rong'=>-14122,'rou'=>-14112,'ru'=>-14109,'ruan'=>-14099,'rui'=>-14097,'run'=>-14094,'ruo'=>-14092,  
        'sa'=>-14090,'sai'=>-14087,'san'=>-14083,'sang'=>-13917,'sao'=>-13914,'se'=>-13910,'sen'=>-13907,'seng'=>-13906,'sha'=>-13905,'shai'=>-13896,'shan'=>-13894,'shang'=>-13878,'shao'=>-13870,'she'=>-13859,'shen'=>-13847,'sheng'=>-13831,'shi'=>-13658,'shou'=>-13611,'shu'=>-13601,'shua'=>-13406,'shuai'=>-13404,'shuan'=>-13400,'shuang'=>-13398,'shui'=>-13395,'shun'=>-13391,'shuo'=>-13387,'si'=>-13383,'song'=>-13367,'sou'=>-13359,'su'=>-13356,'suan'=>-13343,'sui'=>-13340,'sun'=>-13329,'suo'=>-13326,  
        'ta'=>-13318,'tai'=>-13147,'tan'=>-13138,'tang'=>-13120,'tao'=>-13107,'te'=>-13096,'teng'=>-13095,'ti'=>-13091,'tian'=>-13076,'tiao'=>-13068,'tie'=>-13063,'ting'=>-13060,'tong'=>-12888,'tou'=>-12875,'tu'=>-12871,'tuan'=>-12860,'tui'=>-12858,'tun'=>-12852,'tuo'=>-12849,  
        'wa'=>-12838,'wai'=>-12831,'wan'=>-12829,'wang'=>-12812,'wei'=>-12802,'wen'=>-12607,'weng'=>-12597,'wo'=>-12594,'wu'=>-12585,  
        'xi'=>-12556,'xia'=>-12359,'xian'=>-12346,'xiang'=>-12320,'xiao'=>-12300,'xie'=>-12120,'xin'=>-12099,'xing'=>-12089,'xiong'=>-12074,'xiu'=>-12067,'xu'=>-12058,'xuan'=>-12039,'xue'=>-11867,'xun'=>-11861,  
        'ya'=>-11847,'yan'=>-11831,'yang'=>-11798,'yao'=>-11781,'ye'=>-11604,'yi'=>-11589,'yin'=>-11536,'ying'=>-11358,'yo'=>-11340,'yong'=>-11339,'you'=>-11324,'yu'=>-11303,'yuan'=>-11097,'yue'=>-11077,'yun'=>-11067,  
        'za'=>-11055,'zai'=>-11052,'zan'=>-11045,'zang'=>-11041,'zao'=>-11038,'ze'=>-11024,'zei'=>-11020,'zen'=>-11019,'zeng'=>-11018,'zha'=>-11014,'zhai'=>-10838,'zhan'=>-10832,'zhang'=>-10815,'zhao'=>-10800,'zhe'=>-10790,'zhen'=>-10780,'zheng'=>-10764,'zhi'=>-10587,'zhong'=>-10544,'zhou'=>-10533,'zhu'=>-10519,'zhua'=>-10331,'zhuai'=>-10329,'zhuan'=>-10328,'zhuang'=>-10322,'zhui'=>-10315,'zhun'=>-10309,'zhuo'=>-10307,'zi'=>-10296,'zong'=>-10281,'zou'=>-10274,'zu'=>-10270,'zuan'=>-10262,'zui'=>-10260,'zun'=>-10256,'zuo'=>-10254  
    );  
  
    /** 
     * 将中文编码成拼音 
     * @param string $utf8Data utf8字符集数据 
     * @param string $sRetFormat 返回格式 [head:首字母|all:全拼音] 
     * @return string 
     */  
    public static function encode($utf8Data, $sRetFormat='head'){  
        $sGBK = iconv('UTF-8', 'GBK', $utf8Data);  
        $aBuf = array();  
        for ($i=0, $iLoop=strlen($sGBK); $i<$iLoop; $i++) {  
            $iChr = ord($sGBK{$i});  
            if ($iChr>160)  
                $iChr = ($iChr<<8) + ord($sGBK{++$i}) - 65536;  
            if ('head' === $sRetFormat)  
                $aBuf[] = substr(self::zh2py($iChr),0,1);  
            else  
                $aBuf[] = self::zh2py($iChr);  
        }  
        if ('head' === $sRetFormat)  
            return implode('', $aBuf);  
        else  
            return implode(' ', $aBuf);  
    }  
  
    /** 
     * 中文转换到拼音(每次处理一个字符) 
     * @param number $iWORD 待处理字符双字节 
     * @return string 拼音 
     */  
    private static function zh2py($iWORD) {  
        if($iWORD>0 && $iWORD<160 ) {  
            return chr($iWORD);  
        } elseif ($iWORD<-20319||$iWORD>-10247) {  
            return '';  
        } else {  
            foreach (self::$_aMaps as $py => $code) {  
                if($code > $iWORD) break;  
                $result = $py;  
            }  
            return $result;  
        }  
    }  

}