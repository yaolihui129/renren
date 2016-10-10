$(function(){
	//好友栏获得焦点
	$('li[data-tip="首页"]').removeClass('app-nav-item-cur');
	$('li[data-tip="好友"]').addClass('app-nav-item-cur');

	//控制删除好友的按钮的出现与消失
	$('li.friend-detail').hover(function(){
		$(this).find('div.friend-tips').show();
	},function(){
		$(this).find('div.friend-tips').hide();
	});

	//查看好友信息
	$('a.tiny_photo').click(function(){
		window.location.href=$(this).attr('turnto');
	});

	//删除好友
	$('div.friend-tips').click(function(){
		$('#tanchaungneirong').html('您确定要删除该好友吗?');
		$('#tanchuang').show();
		var relId=$(this).attr('data-id');
		$('button').click(function(){
			if($(this).attr('id')=='alarmConfirmButton'){
				$.ajax({
					url:'/renren/index.php/Home/Friend/friendDel',
					type:'GET',
					data:'relId='+relId,
					success:function(res){
						if(res){
							$('div.friend-tips[data-id="'+relId+'"]').parents('li.friend-detail').remove();
						}
					},
					error:function(){
						alert('ajax传值失败');
					},
				});
			}
		});
		
	});

	//好友的显示
	$('#groups-list-con>li.group').live('click',function(){
		$('#f_group-edit-detail').hide();
		$('li.friend-detail').unbind('hover');
		$('a.tiny_photo').unbind('click');
		//控制删除好友的按钮的出现与消失
		$('li.friend-detail').hover(function(){
			$(this).find('div.friend-tips').show();
		},function(){
			$(this).find('div.friend-tips').hide();
		});
		//查看好友信息
		$('a.tiny_photo').click(function(){
			window.location.href=$(this).attr('turnto');
		});
		$(this).siblings('li').removeClass('on');
		$(this).addClass('on');
		$('#f_group-name').html($(this).find('span.gname').html());
		$('#f_group-num').html($(this).find('span.group-num').html());
		if($(this).attr('data-type')=='all'){
			$('#friends-list-con>li').show();
			$('#group_batch-edit').hide();
			$('#group_edit').hide();
			$('#list-empty-friend').hide();
		}
		if($(this).attr('data-status')=='1'){
			//alert('aa');
			$('#friends-list-con>li').hide();
			$('#friends-list-con>li[data-status="1"]').show();
			$('#group_batch-edit').hide();
			$('#group_edit').hide();
			$('#list-empty-friend').hide();
		}
		if(typeof($(this).attr('data-gid'))!='undefined'){
			if($(this).attr('data-gid').length>0){
				$('#friends-list-con>li').hide();
				$('#friends-list-con>li[group-id="'+$(this).attr('data-gid')+'"]').show();
			}
		}
		
		if($(this).attr('data-gid')=='0'){
			$('#group_batch-edit').hide();
			$('#group_edit').hide();
			$('#list-empty-friend').hide();
		}
		

	});

	//分组单击事件
	$('li.normal-group').live('click',function(){
		//如果分组是空的，那么添加提示消息
		if($(this).find('span.group-num').html()=='0'){
			$('#list-empty-friend').show();
			$('#list-empty-friend a.edit-group').attr('data-gid',$(this).attr('data-gid'));
			
			//$('#friend_group-edit')
		}else{
			$('#list-empty-friend').hide();
		}
		$('#group_batch-edit').hide();
		$('#group_edit').attr('data-gid',$(this).attr('data-gid'));
		$('#group_edit').attr('data-name',$(this).attr('data-name'));
		$('#group_edit').removeClass('add');
		$('#group_edit').show();
	});

	//编辑分组
	$('#group_edit').click(function(event){
		event.stopPropagation();
		if($('#group_edit-option:hidden').length>0){
			$('#group_edit-option').show();
		}else{
			$('#group_edit-option').hide();
		}
	});

	//关闭分组
	$(window).click(function(){
		$('#group_edit-option').hide();
		$('#sort-container').hide();
	});

	//重命名分组
	$('#group_edit-option>li:first-child').click(function(){
		$('#tanchaungneirong').html('<span>分组名称: &nbsp;</span><input type="text" name="newGroupName" style="width:200px;height:20px" value="'+$('#group_edit').attr('data-name')+'">');
		$('#tanchuang').show();
		$('button').click(function(){
			if($(this).attr('id')=='alarmConfirmButton'){
				$.ajax({
					url:'/renren/index.php/Home/Friend/renameGroup',
					type:'GET',
					data:'gid='+$('#group_edit').attr('data-gid')+'&gname='+$('input[name="newGroupName"]').val(),
					success:function(res){
						if(res){
							$('li.normal-group[data-gid="'+$('#group_edit').attr('data-gid')+'"] span.gname').html($('input[name="newGroupName"]').val());
							$('#f_group-name').html($('input[name="newGroupName"]').val());
						}
					},
					error:function(){
						alert('ajax传值失败');
					}
				});
			}
		});
	});

	//添加组员
	$('a.edit-group').click(function(){
		$('#group_edit-option>li:nth-child(2)').trigger('click');
	});
	$('#group_edit-option>li:nth-child(2)').click(function(){
		$('#group_edit').addClass('add');
		$('#f_group-edit-detail').show();
		$('#friends-list-con>li').show();
		$('#friends-list-con>li[group-id="'+$('#group_edit').attr('data-gid')+'"]').hide();
		$('#list-empty-friend').hide();
		$('li.friend-detail').unbind('hover');
		$('a.tiny_photo').unbind('click');
		$('a.tiny_photo').click(function(){
			if($('#f_checked-con:hidden').length>0){
				$('#f_checked-con:hidden').show();
			}
			if(!$(this).parents("li.friend-detail").hasClass('on')){
				$('#friends_selected').append('<a class="fl selected-wrap" data-id="'+$(this).parents("li.friend-detail").attr("data-id")+'" href="javascript:;">'+$(this).parents("li.friend-detail").attr("data-name")+'<span class="batch-selected png "></span></a>');
				$(this).parents("li.friend-detail").addClass('on');
				$('#friends_selected>a').unbind('click');
				$('#friends_selected>a').click(function(){
					if($('#f_checked-con:hidden').length>0){
						$('#f_checked-con:hidden').show();
					}
					$('li.friend-detail[data-id="'+$(this).attr('data-id')+'"]').removeClass('on');
					$(this).remove();
					if($('#friends_selected>a.selected-wrap').length<1){
						$('#f_checked-con').hide();
					}
				});
			}
			
		});
		$('#btn-batch-submit').unbind('click');
		//处理增加组员的信息
		$('#btn-batch-submit').click(function(){
			var dataGid=new Array();
			var k=0;
			$('#friends_selected>a').each(function(){
				dataGid.push('gid'+k+'='+$(this).attr('data-id'));
				k++;
			});
			var str=dataGid.join('&');
			$.ajax({
				url:'/renren/index.php/Home/Friend/insertGroupMember/gid/'+$('#group_edit').attr('data-gid')+'/uid/'+$('#uid').val(),
				type:'POST',
				data:str,
				success:function(res){
					if(res){
						// alert(res);
						// ...............................此处可升级！！
						window.location.href="/renren/index.php/Home/Friend/index/id/"+$('#uid').val();
					}
				},
				error:function(){
					alert('ajax传值失败');
				},
			});
		});
	});
	//清除并推出
	$('#btn-batch-cancel').click(function(){
		$('#friends_selected').empty();
		$('#f_checked-con').hide();
		$('li.friend-detail').removeClass('on');
	});
	

	//删除组员
	$('#group_edit-option>li:nth-child(3)').click(function(){
		$('#group_edit').addClass('delete');
		$('#f_group-edit-detail').show();
		$('#friends-list-con>li').hide();
		$('#friends-list-con>li[group-id="'+$('#group_edit').attr('data-gid')+'"]').show();
		$('#list-empty-friend').hide();
		$('li.friend-detail').unbind('hover');
		$('a.tiny_photo').unbind('click');
		$('a.tiny_photo').click(function(){
			if($('#f_checked-con:hidden').length>0){
				$('#f_checked-con:hidden').show();
			}
			if(!$(this).parents("li.friend-detail").hasClass('on')){
				$('#friends_selected').append('<a class="fl selected-wrap" data-id="'+$(this).parents("li.friend-detail").attr("data-id")+'" href="javascript:;">'+$(this).parents("li.friend-detail").attr("data-name")+'<span class="batch-selected png "></span></a>');
				$(this).parents("li.friend-detail").addClass('on');
				$('#friends_selected>a').unbind('click');
				$('#friends_selected>a').click(function(){
					if($('#f_checked-con:hidden').length>0){
						$('#f_checked-con:hidden').show();
					}
					$('li.friend-detail[data-id="'+$(this).attr('data-id')+'"]').removeClass('on');
					$(this).remove();
					if($('#friends_selected>a.selected-wrap').length<1){
						$('#f_checked-con').hide();
					}
				});
			}
			
		});
		$('#btn-batch-submit').unbind('click');
		//处理删除组员的信息
		$('#btn-batch-submit').click(function(){
			var dataGid=new Array();
			var k=0;
			$('#friends_selected>a').each(function(){
				dataGid.push('gid'+k+'='+$(this).attr('data-id'));
				k++;
			});
			var str=dataGid.join('&');
			$.ajax({
				url:'/renren/index.php/Home/Friend/delGroupMember/gid/'+$('#group_edit').attr('data-gid')+'/uid/'+$('#uid').val(),
				type:'POST',
				data:str,
				success:function(res){
					if(res){
						//...............................此处可升级！！
						window.location.href="/renren/index.php/Home/Friend/index/id/"+$('#uid').val();
					}
				},
				error:function(){
					alert('ajax传值失败');
				},
			});
		});

	});
	

	//解散分组
	$('#group_edit-option>li:nth-child(5)').click(function(){
		$('#tanchaungneirong').html('您确定要解散分组 '+$('#group_edit').attr('data-name')+' 吗?');
		$('#tanchuang').show();
		$('button').click(function(){
			if($(this).attr('id')=='alarmConfirmButton'){
				$.ajax({
					url:'/renren/index.php/Home/Friend/dismissGroup',
					type:'GET',
					data:'gid='+$('#group_edit').attr('data-gid'),
					success:function(res){
						//................此处可修改................//
						window.location.href="/renren/index.php/Home/Friend/index/id/"+$('#uid').val();
					},
					error:function(){
						alert('ajax传值失败');
					},
				});
			}
		});
		
	});

	//排序界面的出现与关闭
	$('#friend-sort').click(function(){
		event.stopPropagation();
		if($('#sort-container:hidden').length>0){
			$('#sort-container').show();
		}else{
			$('#sort-container').hide();
		}
	});
	//排序样式
	$('#sort-container>li').click(function(){
		$(this).siblings().removeClass('on');
		$(this).addClass('on');
		if($(this).find('a').hasClass('up')){
			$(this).find('a').removeClass('up');
			$(this).find('a').addClass('down');
			paixu($(this).find('a').attr('data-type'),'down');
		}else{
			$(this).find('a').removeClass('down');
			$(this).find('a').addClass('up');
			paixu($(this).find('a').attr('data-type'),'up');
		}
	});

	//全选按钮
	// $('#f_check').click(function(){
	// 	// $(this).addClass('on');
	// 	if($(this).hasClass('on')){
	// 		$(this).removeClass('on');
	// 	}else{
	// 		$(this).addClass('on');
	// 	}
	// });

});

//新建分组界面的展示
function createNewGroupDisplay(){
	$('#tanchaungneirong').html('<span>分组名称: &nbsp;</span><input type="text" name="newGroupName" style="width:200px;height:20px">');
	$('#tanchuang').show();
	
	$('button').click(function(){
		if($(this).attr('id')=='alarmConfirmButton'){
			$('#alarmConfirmButton').unbind('click');
			$.ajax({
				url:'/renren/index.php/Home/Friend/createNewGroup',
				type:'GET',
				data:'uid='+$('#uid').val()+'&gname='+$('input[name="newGroupName"]').val(),
				async:true,
				success:function(res){
					if(res){
						$('li.createNewGroup').before('<li data-name="'+$('input[name="newGroupName"]').val()+'" class="group normal-group" data-gid="'+res+'"><a class="normal" title="'+$('input[name="newGroupName"]').val()+'" data-name="'+$('input[name="newGroupName"]').val()+'" href="javascript:;"> <span class="gname">'+$('input[name="newGroupName"]').val()+'</span><span class="group-num">0</span> </a></li>');
						$('li[data-gid="'+res+'"]').trigger('click');

					}
				},
				error:function(){
					alert('ajax传值失败');
				},
			});
		}
	});
}

//执行排序
function paixu(type,uod){
	if(typeof($('#friends-list-con>li:visible'))=='undefined'){return;}
	for(var i=0;i<$('#friends-list-con>li:visible').length;i++){
		for(var j=i;j<$('#friends-list-con>li:visible').length;j++){
			if(uod=='up'){
				if($('#friends-list-con>li:visible').eq(i).attr(type)>$('#friends-list-con>li:visible').eq(j).attr(type)){
					ob=$('#friends-list-con>li:visible').eq(i).clone();
					$('#friends-list-con>li:visible').eq(i).replaceWith($('#friends-list-con>li:visible').eq(j).clone());
					$('#friends-list-con>li:visible').eq(j).replaceWith(ob);
				}
			}else{
				if($('#friends-list-con>li:visible').eq(i).attr(type)<$('#friends-list-con>li:visible').eq(j).attr(type)){
					ob=$('#friends-list-con>li:visible').eq(i).clone();
					$('#friends-list-con>li:visible').eq(i).replaceWith($('#friends-list-con>li:visible').eq(j).clone());
					$('#friends-list-con>li:visible').eq(j).replaceWith(ob);
				}
			}
		}
	}
	
	
}