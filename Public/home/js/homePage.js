$(function(){

	//右侧边栏的开关
	$('#webpager>a:first-child').click(
		function(){
			$('#kaiguan').show(1000);
			$('#webpager').hide();
			$('#controldialog>div:nth-child(n+2)').fadeOut(1000);
			$('#buddy').animate({width:'0px'});
			$('.nx-main').animate({width:'1349px'});
			$('.bd-main').animate({width:'1189px'});
			$('#controldialog').animate({width:'0px'});
			$('#frameFixedNav').animate({width:'1189px'});
			$('#nxHeader>div:first-child').animate({width:'1370px'});
			$('#webpager>a:first-child').animate({left:'200px'});
			$('#toolBackTo').animate({right:'20px'});
			$('#popup-window-wrap').animate({right:'8px'});
		}
	);

	$('#kaiguan').click(function(){
		$('#kaiguan').hide();
		$('#controldialog>div:nth-child(n+2)').fadeIn(1000);
		$('#webpager').show();
		$('#buddy').animate({width:'240px'});
		$('.nx-main').animate({width:'1080px'});
		$('.bd-main').animate({width:'950px'});
		$('#controldialog').animate({width:'240px'});
		$('#frameFixedNav').animate({width:'950px'});
		$('#nxHeader>div:first-child').animate({width:'1125px'});
		$('#webpager>a:first-child').animate({left:'-40px'});
		$('#toolBackTo').animate({right:'260px'});
		$('#popup-window-wrap').animate({right:'248px'});
	});

	//通知栏的click事件
	$('div[data-tab="notify"]').click(function(){
		$('div[data-tab="notify"]').addClass('on');
		$('div[data-tab="friends"]').removeClass('on');
		$('div[data-tab="recent"]').removeClass('on');
		$('#friendsTab').animate({left:'240px'});
		$('#recentTab').animate({left:'480px'});
		$('#notifyTab').animate({left:'0px'});
	});

	//好友栏的click事件
	$('div[data-tab="friends"]').click(function(){
		$('div[data-tab="notify"]').removeClass('on');
		$('div[data-tab="friends"]').addClass('on');
		$('div[data-tab="recent"]').removeClass('on');
		$('#friendsTab').animate({left:'0px'});
		$('#recentTab').animate({left:'240px'});
		$('#notifyTab').animate({left:'-240px'});
	});

	//最近联系人的click事件
	$('div[data-tab="recent"]').click(function(){
		$('div[data-tab="notify"]').removeClass('on');
		$('div[data-tab="friends"]').removeClass('on');
		$('div[data-tab="recent"]').addClass('on');
		$('#friendsTab').animate({left:'-240px'});
		$('#recentTab').animate({left:'0px'});
		$('#notifyTab').animate({left:'-480px'});
	});

	//Top按钮的单击事件
	$(window).scroll(function(){
		//alert($(window).scrollTop());
		if($(window).scrollTop() > 100){
			$('#toolBackTo>a:first-child').attr('style','display:block');
		}else{
			$('#toolBackTo>a:first-child').hide();
		}
	});

	//发表状态栏的鼠标移入移出事件
	$('#appPublisherBtnPhoto,#appPublisherBtnShare,#appPublisherBtnBlog,#appPublisherBtnMovie').hover(function(){
		$(this).addClass('ui-state-hover');
	},function(){
		$(this).removeClass('ui-state-hover');
	});

	//发表纯文字说说的单击事件
	$('#shuoshuo').click(function(){
		//alert('aa');
		$('#jiakuang').hide(1000);
		$('.nx-right').css('position','relative');
		var str=$('#chunwenzi').html();
		$('.bd-publisher').append(str);
		$('div.textarea-textzone>div:nth-child(2)').focus();
		$('div.textarea-textzone>div:nth-child(2)').html('');
		$('div.textarea-textzone>div:nth-child(1)').html('你现在在想什么？');
		$('.ui-renren-publisher-buttonset>.buttonset>div:nth-child(2)').addClass('ui-button-disabled').addClass('ui-state-disabled');
	});

	//向输入框输入文字和表情的keyup事件
	$(window).keyup(function(){
		//alert('a');
		//alert($('#hd-search').val().length);

		//搜索框的搜索
		if($('#hd-search').val().length>0){
			$.ajax({
				url:"/renren/index.php/Home/Base/searchFriend",
				type:'GET',
				data:'info='+$('#hd-search').val(),
				dataType:'text',
				async:false,
				success:function(list){
					//alert(list);
					//alert()
					$('#s-default-items>li:nth-child(n+2)').remove();
					var row=eval(list);
					//alert(row.length);
					if(eval(list).length>0){
						
						for(var i=0;i<row.length;i++){
							if(row[i].id!=$('#uid').val()){
								$('#s-default-items').append('<li class="s-def-item"><a class="s-item" target="_self" href="/renren/index.php/FriendPage/friendPageIndex/id/'+$('#uid').val()+'/fid/'+row[i].id+'"><div class="s-icon"><img src="/renren/Public/home/images/'+row[i].head+'" alt=""></div><div class="s-info">'+row[i].name+'</div></a></li>');
							}
							
						}
					}
				},
				error:function(){
					alert('ajax传值失败');
				},
			});
		}else{
			$('#s-default-items>li:nth-child(n+2)').remove();
		}
		//alert(typeof($('div.textarea-textzone>div:nth-child(2)').html()));
		//发表说说输入框的改变
		if(typeof($('div.textarea-textzone>div:nth-child(2)').html())!='undefined'){
			if($('div.textarea-textzone>div:nth-child(2)').html().length>0){
				$('div.textarea-textzone>div:nth-child(1)').html('');
				$('.ui-renren-publisher-buttonset>.buttonset>div:nth-child(2)').removeClass('ui-button-disabled+').removeClass('ui-state-disabled');
			}else{
				$('div.textarea-textzone>div:nth-child(1)').html('你现在在想什么？');
				$('.ui-renren-publisher-buttonset>.buttonset>div:nth-child(2)').addClass('ui-button-disabled').addClass('ui-state-disabled');
			}
			$('#counterLength').html($('div.textarea-textzone>div:nth-child(2)').html().replace(/(\<(.*?)\>)/g,'').length+'/240');
		}
				//发表评论的改变
		// if($("div.own-reply-content>div.input-container>div.reply-input").html().length>0){
		// 	$('div.own-reply-content>div.reply-btns>.add-comment').removeClass('disabled');
		// }else{
		// 	$('div.own-reply-content>div.reply-btns>.add-comment').addClass('disabled');
		// }
		$('div.reply-input').each(function(){
			if($(this).html().length>0){
				$(this).parents('.own-reply').find('.add-comment').removeClass('disabled');
			}else{
				$(this).parents('.own-reply').find('.add-comment').addClass('disabled');
			}
			
		});

		
	});

	// 小表情点击事件1
	// $('div.emotion-dialog-box>ul>li').click(function(){
	// 	var e=window.event;
	// 	// alert(e.clientY);
	// 	if(e.clientY<550){
	// 		$('div.textarea-textzone>div:nth-child(2)').html($('div.textarea-textzone>div:nth-child(2)').html()+'<img src="'+$(this).children().attr('src')+'"/>');
	// 		$('div.textarea-textzone>div:nth-child(1)').html('');
	// 		$('.ui-renren-publisher-buttonset>.buttonset>div:nth-child(2)').removeClass('ui-button-disabled+').removeClass('ui-state-disabled');
	// 	}else{
	// 		$(".input-container").children("div.reply-input").html($(".input-container").children("div.reply-input").html()+'<img src="'+$(this).children().attr('src')+'"/>');
	// 		$('div.own-reply-content>div.reply-btns>.add-comment').removeClass('disabled');
	// 	}
		
	// });

	//小表情点击事件2


	//小表情滑入事件
	$('div.emotion-dialog-box>ul>li').mousemove(function(){
		$('.emotion-dialog-preview').show().children().attr('src',$(this).children().attr('data-preview'));
	});

	//小表情预览框的挪动
	$('.emotion-dialog-preview').mouseover(function(){
		if($(this).css('left')=='10px'){
			$(this).css({'right':'10px','left':'auto'});
		}else{
			$(this).css({'left':'10px','right':'auto'});
		}
	});

	//搜索
	$('#hd-search').click(function(){
		event.stopPropagation();
		$('#search-result-box').show();
	});

	//搜索框的消失
	$(window).click(function(){
		$('#search-result-box').hide();
	});

	//关闭图片详情
	$('#closePicDetail').click(function(){
		$('#displayPicDetail').hide();
	});

	//新鲜事的跳转与切换
	$('.newsfeed-filter-list>li').click(function(event){
		event.stopPropagation();
		// event.stopImmediatePropagation();
		$('.a-feed').each(function(){
			$(this).show();
		});
		eval('var arr='+$(this).attr('data-feed-type'));
		// alert(arr[0]);
		
			$('.a-feed').each(function(){
				var n=0;
				for(var i=0;i<arr.length;i++){
					if($(this).attr('typeid')==arr[i]){
						n=1;
						break;
					}
				}
				if(n==0){
					$(this).hide();
				}
			});
		
	});

	//切换好友分组的新鲜事
	$('.nf-group-list>li').click(function(event){
		event.stopPropagation();
		eval('var gflist='+$(this).attr('flist'));
		$('.a-feed').each(function(){
			$(this).show();
		});
		$('.a-feed').each(function(){
			var n=0;
			for(var i=0;i<gflist.length;i++){
				if($(this).attr('uid')==gflist[i]){
					n=1;
					break;
				}
			}
			if(n==0){
				$(this).hide();
			}
		});
		
	});

	//内容的跳转与切换
	$('.fd-nav-list>li').click(function(){
		
		if($(this).find('a').html()=='原创内容'){
			$('li[data-feed-type-label="origianal"]').trigger('click');
		}
		if($(this).find('a').html()=='新鲜事'){
			$('.a-feed').each(function(){
				$(this).show();
			});
		}
		if($(this).find('a').html()=='好友分组'){
			$('.a-feed').each(function(){
				$(this).show();
				if($(this).attr('uid')==$('#uid').val()){
					$(this).hide();
				}
			});
		}
		if($(this).find('a').html()=='关注内容'){
			$('.a-feed').each(function(){
				$(this).hide();
			});
		}

		if($(this).find('a').html()=='特别关注'){
			$('.a-feed').each(function(){
				$(this).show();
			});
			eval('var tbgz='+$('#sFList').val());
			$('.a-feed').each(function(){
				var n=0;
				for(var i=0;i<tbgz.length;i++){
					if($(this).attr('uid')==tbgz[i]){
						n=1;
						break;
					}
				}
				if(n==0){
					$(this).hide();
				}
			});
		}

		$('.fd-nav-list>li').removeClass('fd-nav-cur-item');
		$(this).addClass('fd-nav-cur-item');
	});

	//删除好友的按钮的出现与消失
	$('.sysInfoList').hover(function(){
		$(this).find('span.delfriend').show();
	},function(){
		$(this).find('span.delfriend').hide();
	});

});

//打开表情
function biaoqingShow(ob){
	//alert($(ob).offset().left);
	// alert($(ob).offset().top);
	//alert($(ob).offset().top);
	$('div.emotion-dialog-box>ul>li').unbind('click');
	if($(ob).hasClass('duihuabiaoqing')){
		$('#xiaobiaoqing').fadeIn(500).css({'left':$(ob).offset().left-370,'top':$(ob).offset().top-130});
	}else{
		$('#xiaobiaoqing').fadeIn(500).css({'left':$(ob).offset().left,'top':$(ob).offset().top});
	}
	
	if($(ob).parent().parent().hasClass('tools')){
		//给小表情添加点击事件1
		$('div.emotion-dialog-box>ul>li').click(function(){
			$('div.textarea-textzone>div:nth-child(2)').html($('div.textarea-textzone>div:nth-child(2)').html()+'<img src="'+$(this).children().attr('src')+'"/>');
			$('div.textarea-textzone>div:nth-child(1)').html('');
			$('.ui-renren-publisher-buttonset>.buttonset>div:nth-child(2)').removeClass('ui-button-disabled+').removeClass('ui-state-disabled');
		});
	}else{
		//给小表情添加点击事件2
		$('div.emotion-dialog-box>ul>li').click(function(){
			$(ob).parent().parent(".input-container").children("div.reply-input").html($(ob).parent().parent(".input-container").children("div.reply-input").html()+'<img src="'+$(this).children().attr('src')+'"/>');
			if($(ob).parent().parent().hasClass('input-container')){
				$('div.own-reply-content>div.reply-btns>.add-comment').removeClass('disabled');
			}else{
				//对话框的小表情
				if($(ob).hasClass('duihuabiaoqing')){
					$('#dialogIcon').html($('#dialogIcon').html()+'<img src="'+$(this).children().attr('src')+'"/>');
				}
			}
			// $('#dialogIcon').html($('#dialogIcon').html()+'<img src="'+$(this).children().attr('src')+'"/>');

			

		});
		


	}
	
}

//定义一个全变量，来控制对话框的实时刷新
var mytime;

//发表说说是小表情的点击事件
// function shuoshuoClickBiaoqing(){
// 	$('div.textarea-textzone>div:nth-child(2)').html($('div.textarea-textzone>div:nth-child(2)').html()+'<img src="'+$(this).children().attr('src')+'"/>');
// 	$('div.textarea-textzone>div:nth-child(1)').html('');
// 	$('.ui-renren-publisher-buttonset>.buttonset>div:nth-child(2)').removeClass('ui-button-disabled+').removeClass('ui-state-disabled');
// }

//关闭表情
function closeBiaoqing(){

	$('#xiaobiaoqing').fadeOut(500);
	
}

//回到顶部
function turnToTop(){
	$('html,body').animate({
		scrollTop:'0px'
	},1000);
}

//发表图片说说下拉框
function photoAppClick(){
	$('.ui-fileupload').show();
	$('#jiakuang').hide(1000);
	$('.nx-right').css('position','relative');
	var str=$('#photoShow').html();
	//console.log(str);
	$('.bd-publisher').append(str);
	$('div.textarea-textzone>div:nth-child(2)').html('');
	$('div.textarea-textzone>div:nth-child(1)').html('你现在在想什么？');
	$('.ui-renren-publisher-buttonset>.buttonset>div:nth-child(2)').addClass('ui-button-disabled').addClass('ui-state-disabled');
}

//收起发表说说下拉框
function shou(){

	$('#jiakuang').show(1000);
	$('.bd-publisher').empty();
}

//发布说说
function fabuShuoshuo(ob){
	// alert($('div.textarea-textzone>div:nth-child(2)').html());
	// alert(typeof($(ob).css('color')));
	// alert($(ob).attr('class'));
	if($(ob).hasClass('ui-state-disabled')){
		return false;
	}else{
		$.ajax({
			url:'/renren/index.php/HomePage/insertTalk',
			type:'POST',
			data:'uid='+$('#uid').val()+'&content='+$('div.textarea-textzone>div:nth-child(2)').html(),
			dataType:'text',
			async:false,
			success:function(res){
				if(res){
					// alert('success');
					var date=new Date(); 
					//alert($('div.textarea-textzone>div:nth-child(2)').html());
					$('#ajaxShuoshuo>div:first-child').attr('typeid',res);
					$('#ajaxShuoshuo span.pub-time').html(date.getFullYear()+'-'+(date.getMonth()+1)+'-'+(date.getDate())+' '+(date.getHours())+':'+(date.getMinutes())+':'+(date.getSeconds()));
					$('#ajaxShuoshuo a.like').attr('data-like','{type:"0",typeid:"'+res+'",uid:"'+$('#uid').val()+'"}');
					$('#ajaxShuoshuo div.feed-content').html($('div.textarea-textzone>div:nth-child(2)').html());
					//console.log($('#ajaxShuoshuo').html());
					
					$('#ajaxShuoshuo a.add-comment').attr('typeid',res);

					$('#feed-list>div:first-child').after($('#ajaxShuoshuo').html());
				}else{
					alert('说说发表失败');
				}
			},
			error:function(){
				alert('ajax传值失败');
			},
		});
	}

	shou();

}

//展开评论
function zhankaipinglun(ob){
	
	$(ob).siblings().show();
	$(ob).children(".input-container").addClass('active-textarea');
	$(ob).children(".input-container").children(".replay-input").addClass('ui-textbox').html();
	$(ob).children(".input-container").children(".tool-bar").show();
	$(ob).children(".reply-btns").show();
	// $(ob).find('.add-comment').show();
	// $(ob).find('.cancel-comment').show();
	
}

//发表评论
function fabupinglun(ob){
	if($(ob).hasClass('disabled')){
		return false;
	}
	var content=$(ob).parents("div.own-reply-content").children('div.input-container').children('div.reply-input').html();
	var type=$(ob).attr('type');
	var typeid=$(ob).attr('typeid');
	var did=$(ob).attr('did');
	var fid=$(ob).attr('fid');
	$.ajax({
		url:'/renren/index.php/HomePage/addDiscuss',
		type:'POST',
		data:'content='+content+'&type='+type+'&typeid='+typeid+'&did='+did+'&fid='+fid,
		dataType:'text',
		success:function(res){
			var date=new Date();
			$('#pinglunlouceng').find('.user-avatar').attr('src','/renren/Public/home/images/'+$('#head').val());
			$('#pinglunlouceng').find('.user-name').html($('#name').val());
			$('#pinglunlouceng').find('span.time').html(date.getFullYear()+'-'+(date.getMonth()+1)+'-'+(date.getDate())+' '+(date.getHours())+':'+(date.getMinutes())+':'+(date.getSeconds()));
			$('#pinglunlouceng').find('p.text').html(content);
			$(ob).parents('.a-feed').find('.feed-replies').append($('#pinglunlouceng').html());
			shouqipinglun(ob)
		},
		error:function(){
			alert('ajax传值失败');
		}
	});

}

//收起评论
function shouqipinglun(ob){
	event.stopPropagation();
	//alert('aa');
	//$(ob).parents('.own-reply-content').siblings().hide();
	$(ob).parents('.own-reply-content').find(".input-container").removeClass('active-textarea');
	$(ob).parents('.own-reply-content').find(".reply-input").removeClass('ui-textbox').empty();
	$(ob).parents('.own-reply-content').find(".tool-bar").hide(500);
	$(ob).parent('.reply-btns').hide(500);
	// $(ob).parents('.own-reply-content').find(".reply-btns").hide();
	// alert($(ob).parent('.reply-btns').height());
	
	
}


//发布图文并茂
function fabuTuwen(ob){
	if($('#jsonPic').val().length<1){return;}
	var content=$(ob).parents('div.ui-renren-publisher').find('div[contenteditable="true"]').html();
	var uid=$('#uid').val();
	var picname=$('#jsonPic').val();
	$('#tuwenbingmaosuipian').find('.photo-content-1').empty();
	$.ajax({
		url:'/renren/index.php/Home/HomePage/insertPicUpload',
		type:'POST',
		data:'uid='+uid+'&content='+content+'&picname='+picname,
		dataType:'text',
		success:function(res){
			if(res){
				var date=new Date();
				$('#tuwenbingmaosuipian').find('div.a-feed').attr('typeid',res);
				//$('#tuwenbingmaosuipian').find('a.avatar').find('img').attr({src:'/renren/Public/home/images/'+$('#head').val(),title:$('#name').val()});
				//$('#tuwenbingmaosuipian').find('a.feed-user-name').html($('#name').val());
				$('#tuwenbingmaosuipian').find('span.pub-time').html(date.getFullYear()+'-'+(date.getMonth()+1)+'-'+(date.getDate())+' '+(date.getHours())+':'+(date.getMinutes())+':'+(date.getSeconds()));
				$('#tuwenbingmaosuipian').find('span.photo-desc').html($(ob).parents('div.ui-renren-publisher').find('div[contenteditable="true"]').html());
				$('#tuwenbingmaosuipian').find('a.like').attr('data-like','{type:"5",typeid:"'+res+'",uid:"'+$('#uid').val()+'"}');
				var list=picname.split(',');
				// console.log(picname);
				// console.log(list);
				
				for(var i=0;i<list.length;i++){
					if(i>5){
						$('#tuwenbingmaosuipian').find('.photo-content-1').append('<a href="javascript:void(0)" style="display:none" class="cursor-all location-left" data-pic="{picNum:\''+list.length+'\',presentSrc:\'/renren/Public/upload/images/'+list[i]+'\',presentNum:\''+(parseInt(i))+'\'}" onclick="xsPicdes(this)"><img width="150" height="150" class="photo-feed-pic1" src="/renren/Public/upload/images/s_'+list[i]+'"/></a>');
					}else{
						$('#tuwenbingmaosuipian').find('.photo-content-1').append('<a href="javascript:void(0)" class="cursor-all location-left" data-pic="{picNum:\''+list.length+'\',presentSrc:\'/renren/Public/upload/images/'+list[i]+'\',presentNum:\''+(parseInt(i))+'\'}" onclick="xsPicdes(this)"><img width="150" height="150" class="photo-feed-pic1" src="/renren/Public/upload/images/s_'+list[i]+'"/></a>');
					}
				}
				$('#tuwenbingmaosuipian a.add-comment').attr('typeid',res);
				$('#feed-list>div:first-child').after($('#tuwenbingmaosuipian').html());


				shou();
				$('#tuwenbingmaosuipian div.photo-content-1:nth-child(n+2)').remove();
			}else{
				alert('由于未知原因，您的新鲜事已发到了火星');
			}
		},
		error:function(){
			alert('ajax传值失败');
		},

	});

}

//发布图文并茂 
function uploadImageFile(ob){
	//alert($(ob).val());
	event.stopPropagation();

	$('#uploadForm').trigger('submit');
	
	$('.photo-grids').find('img').remove();
	$('#tuozhuaishangchuangkuang').find('p.tips').empty().html('正在上传中。。。');
}

//展示图片
function closePicFrame(){
	$('.ui-fileupload').hide();
	$('.photo-grids').find('img').remove();

	//alert($('#jsonPic').val());
	var picList=$('#jsonPic').val();
	//alert(picList);
	var list=picList.split(',');
	$('.ui-fileupload').hide();
	for(var i=0;i<list.length;i++){
		$('.photo-grids').append('<img src="/renren/Public/upload/images/s_'+list[i]+'" width="150">');
		if(i>4){
			i=list.length;
		}
	}
	$('.photo-number>strong').html(list.length);
	$('.buttonset').find('div[role="button"]').removeClass('ui-state-disabled').removeClass('ui-button-disabled');
	$('#tupiancharuweizhi').html($('#tpsckuang').html());
	
}



//点赞
function clicksupport(ob){
	eval('var dataLike='+$(ob).attr('data-like'));
	if($(ob).hasClass('liked')){
		
		$.ajax({
			url:"/renren/index.php/Home/HomePage/supportDeal",
			type:'POST',
			data:'type='+dataLike.type+'&typeid='+dataLike.typeid+'&uid='+dataLike.uid+'&state=jian',
			dataType:'text',
			async:false,
			success:function(res){
				if(res){
					//alert(res);
					$(ob).removeClass('liked');
					$(ob).find('span').html(parseInt($(ob).find('span').html())-1);
					
				}
			},
			error:function(){
				alert('ajax传值失败');
			},
		});

	}else{
		// $(ob).addClass('liked');
		// $(ob).find('span').html(parseInt($(ob).find('span').html())+1);
		// eval('var dataLike='+$(ob).attr('data-like'));
		//console.log(dataLike.type+dataLike.typeid+dataLike.uid);
		$.ajax({
			url:"/renren/index.php/Home/HomePage/supportDeal",
			type:'POST',
			data:'type='+dataLike.type+'&typeid='+dataLike.typeid+'&uid='+dataLike.uid+'&state=jia',
			dataType:'text',
			async:false,
			success:function(res){
				if(res){
					//alert(res);
					$(ob).addClass('liked');
					$(ob).find('span').html(parseInt($(ob).find('span').html())+1);
					
				}
			},
			error:function(){
				alert('ajax传值失败');
			},
		});
	}
}

//显示图片详情
function xsPicdes(ob){
	//alert('aa');
	$('#displayPicDetail').hide();
	eval("var picInfo="+$(ob).attr('data-pic'));
	//alert(picInfo.presentNum);
	picInfo.presentNum=parseInt(picInfo.presentNum)+1;
	$('#viewerNext').unbind('click').show();
	$('#viewerPrev').unbind('click').show();

	$('#displayPicDetail').find('img.pop-content-img').attr('src',picInfo.presentSrc).show();
	$('#photoIndex').html(picInfo.presentNum);
	$('#viewerPhotoCount').html(picInfo.picNum);
	$('#viewerNext').click(function(){
		$(ob).next().trigger('click');
	});
	$('#viewerPrev').click(function(){
		$(ob).prev().trigger('click');
	});


	if(picInfo.presentNum == picInfo.picNum){
		$('#viewerNext').hide();
	}
	if(picInfo.presentNum == 1){
		$('#viewerPrev').hide();
	}

	$('#displayPicDetail').show();


}










//弹出对话框
function alertDialog(ob){
	if(mytime){
		clearTimeout(mytime);
	}
	dialogFresh(ob);
}

//对话框的实时刷新
function dialogFresh(ob){
	$('#popup-window-wrap').hide().show();
	$('.chat-con').empty();
	var uid=$('#uid').val();
	var fid=$(ob).attr('friendid');//alert(fid);
	var fhead=$(ob).children('img').attr('src');
	var fname=$(ob).siblings('div').children('span.piece_name').html();
	$("a.friend-name").html(fname);
	$('li.friends-msg>a').attr('title',fname);
	$('li.friends-msg>a>img').attr('src',fhead);
	// dialogFresh(uid,fid);
	// clearInterval(t);
	// t= setInterval('dialogFresh('+uid+','+fid+')',1000);
	$('.chat-con').empty();
	$.ajax({
		url:"/renren/index.php/Base/recentDialog",
		type:"POST",
		data:'uid='+uid+'&fid='+fid,
		dataType:'text',
		async:false,
		success:function(res){
			//alert('ok');
			var arr=eval(res);
			//alert(arr[0]['addtime']);
			if(arr){
				for(var i=0;i<arr.length;i++){
					if(arr[i].uid==uid){
						$('#udialog>li.sys-time>span.time').html(arr[i].addtime);
						$('#udialog div.chatmsg').html($('#udialog div.chatmsg').html(arr[i].content).text());
						$('.chat-con').append($('#udialog').html());
					}else{
						$('#fdialog>li.sys-time>span.time').html(arr[i].addtime);
						$('#fdialog div.chatmsg').html($('#fdialog div.chatmsg').html(arr[i].content).text());
						$('.chat-con').append($('#fdialog').html());
					}
				}
			}
			
			$('a.btn-msg-send').attr('friendid',fid);
			if($('.chatmsg-wrap').scrollTop()==0){
				$('.chatmsg-wrap').scrollTop($('.chat-con').height()+100);
			}
			if($('.chatmsg-wrap').scrollTop()>($('.chat-con').height()-400)){
				$('.chatmsg-wrap').scrollTop($('.chat-con').height());
			}
			// alert($('.chatmsg-wrap').scrollTop());
			// alert($('.chat-con').height());
		},
		error:function(){
			alert('ajax传值失败');
		},
	});

	//dialogFresh(uid,fid);
	//setInterval(dialogFresh(uid,fid),1000);
	mytime=setTimeout(function(){
		alertDialog(ob)
	},1000);

}

//关闭对话框
function closeDialog(){
	clearTimeout(mytime);
	$('#popup-window-wrap').hide();
}

//对话
function sendMessage(ob){
	// alert('aa');
	
	if($('#dialogIcon').html().length<1){
		return;
	}
	var uid=$('#uid').val();
	//alert($(ob).attr('friendid'));return;
	var fid=$(ob).attr('friendid');
	var content=$('#dialogIcon').html();
	$.ajax({
		url:'/renren/index.php/Base/insertDialog',
		type:'POST',
		data:'uid='+uid+'&fid='+fid+'&content='+content,
		dataType:'text',
		async:false,
		success:function(res){
			if(res){
				
				
				var date=new Date();
				$('#udialog span.time').html(date.getFullYear()+'-'+(date.getMonth()+1)+'-'+(date.getDate())+' '+(date.getHours())+':'+(date.getMinutes())+':'+(date.getSeconds()));
				$('#udialog div.chatmsg').empty().html(content);
				$('.chat-con').append($('#udialog').html());

				$('#dialogIcon').html('');
				$('.chatmsg-wrap').scrollTop($('.chatmsg-wrap').scrollTop()+300);
				
			}
		},
		error:function(){},
	});

}


//弹框取消按钮
function alarmCancel(){
	$('#tanchuang').hide();
	return false;
}

//弹框确认按钮
function alarmConform(){
	$('#tanchuang').hide();
	return true;
}

//拒绝好友申请
function delNewFriend(ob){
	//alert('aa');
	$('#tanchaungneirong').html("消息删除后不可恢复你确定要这样做吗?");
	$('#tanchuang').show();
	$('button').click(function(){
		if($(this).attr('id')=='alarmConfirmButton'){
			$.ajax({
				url:'/renren/index.php/Home/Base/delNewFriend',
				type:'POST',
				data:'sid='+$(ob).attr('data-id')+'&rid='+$(ob).attr('data-rid'),
				success:function(res){
					//alert('ok');
					if(res){
						$('#tanchuang').hide();
						$(ob).parents('li').remove();
					}else{
						alert('删除失败');
					}
					
				},
				error:function(){
					alert('ajax传值失败');
				},
			});
		}
	});
	
	
}

//同意好友添加邀请
function addNewFriend(ob){
	$('#tanchaungneirong').html("您确定要添加该好友,并添加对方为好友吗?");
	$('#tanchuang').show();
	$('button').click(function(){
		if($(this).attr('id')=='alarmConfirmButton'){
			$.ajax({
				url:'/renren/index.php/Home/Base/addNewFriend',
				type:'POST',
				data:'sid='+$(ob).attr('data-id')+'&rid='+$(ob).attr('data-rid'),
				success:function(res){
					if(res){
						$('#tanchuang').hide();
						$(ob).parents('li').remove();
						$('div.collector_title span.num').html(parseInt($('div.collector_title span.num').html())+1);
						eval('var friendInfo='+res);
						$('#friends ul').append('<li class="piece_box" data-name="'+friendInfo.name+'" data-url="/renren/Upload/home/images/'+friendInfo.head+'" data-status="0" ><div class="piece_icon" ondblclick="alertDialog(this)" friendid="'+friendInfo.fid+'"><img class="piece_url" namecard="453172326" src="/renren/Public/home/images/m_'+friendInfo.head+'"></div><div class="piece_info"><span class="piece_name">'+friendInfo.name+'</span><span class="piece_client png wp-none"></span><span class="piece_online png wp-none"></span></div></li>');
					}else{
						alert('添加失败');
					}
				},
				error:function(){
					alert('ajax传值失败')
				},
			});
		}
	});
}