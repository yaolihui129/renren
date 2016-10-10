//当前页面的条数
var pageNo=5;
//当前页面数
var pageCur=1;
//总数据个数
var dataNo;
//判断当前显示的种类
var pageType;
$(function(){
	//初始化当前的样式
	$('#frameFixedNav li').removeClass('fd-nav-cur-item');
	$('#frameFixedNav li').each(function(){
		if($('#whichType').val()=='0'){
			if($(this).find('a').html()=='状态'){
				$(this).addClass('fd-nav-cur-item');
			}
		}
		if($('#whichType').val()=='1'){
			if($(this).find('a').html()=='日志'){
				$(this).addClass('fd-nav-cur-item');
			}
		}
		if($('#whichType').val()=='6'){
			if($(this).find('a').html()=='分享'){
				$(this).addClass('fd-nav-cur-item');
			}
		}
	});
	$('a.headhover').trigger('click');
	
	//状态的滑入划出事件
	$('#my-status-list li').each(function(){
		if($(this).attr('typeid')=='0' || $(this).attr('typeid')=='5'){
			
			$(this).hover(function(){
				//alert('aa');
				$(this).find('div.ugc-host-control').show();
			},function(){
				$(this).find('div.ugc-host-control').hide();
			});
		}
	});

	// $('#my-status-list li[typeid="0"]').hover(function(){
	// 	//alert('aa');
	// 	$(this).find('div.ugc-host-control').show();
	// },function(){
	// 	$(this).find('div.ugc-host-control').hide();
	// });

	//批量删除日志选择按钮
	$('ul.multiBList-ul>li').click(function(){
		if($(this).hasClass('selected')){
			$(this).removeClass('selected');
			$(this).find('div.seldiv').removeClass('multiBList-itemIcon-selected');
			$(this).find('div.seldiv').addClass('multiBList-itemIcon');
			$('#multiBList-selectAll').removeClass('multiBList-selectAll-selected');
			$('#multiBList-selectAll').addClass('multiBList-selectAll');
		}else{
			$(this).addClass('selected');
			$(this).find('div.seldiv').addClass('multiBList-itemIcon-selected');
			$(this).find('div.seldiv').removeClass('multiBList-itemIcon');
			//全选
			$('#multiBList-selectAll').addClass('multiBList-selectAll-selected');
			$('#multiBList-selectAll').removeClass('multiBList-selectAll');
			$('ul.multiBList-ul>li').each(function(){
				if(!$(this).hasClass('selected')){
					$('#multiBList-selectAll').removeClass('multiBList-selectAll-selected');
					$('#multiBList-selectAll').addClass('multiBList-selectAll');
				}
			});
		}

		var selNum=0;
		$('ul.multiBList-ul>li').each(function(){
			if($(this).hasClass('selected')){
				selNum++;
			}
		});
		$('span.multiBList-selectNum').html(selNum);
	});

	//加载最大页数
	$('span.multiBList-allNum').html($('ul.multiBList-ul>li').length);

	//全选按钮
	$('div.multiBList-toolBar').click(function(){
		if($('#multiBList-selectAll').hasClass('multiBList-selectAll-selected')){
			$('#multiBList-selectAll').removeClass('multiBList-selectAll-selected');
			$('#multiBList-selectAll').addClass('multiBList-selectAll');
			$('ul.multiBList-ul>li').each(function(){
				$(this).removeClass('selected');
				$(this).find('div.seldiv').removeClass('multiBList-itemIcon-selected');
				$(this).find('div.seldiv').addClass('multiBList-itemIcon');
			});
			$('span.multiBList-selectNum').html(0);
		}else{
			$('#multiBList-selectAll').addClass('multiBList-selectAll-selected');
			$('#multiBList-selectAll').removeClass('multiBList-selectAll');
			$('ul.multiBList-ul>li').each(function(){
				$(this).addClass('selected');
				$(this).find('div.seldiv').addClass('multiBList-itemIcon-selected');
				$(this).find('div.seldiv').removeClass('multiBList-itemIcon');
			})
			$('span.multiBList-selectNum').html($('span.multiBList-allNum').html());
		}
	});
});

//查看文字状态
function qhzht(z,ob){
	$('#my-status-list>li').each(function(){
		$(this).show();
		if($(this).attr('typeid')!=z){
			$(this).hide();
		}
	});
	$('a').removeClass('headhover');
	$(ob).addClass('headhover');
	dataNo=Math.ceil(($('#my-status-list>li[typeid="'+z+'"]').length));
	pageType=z;
	$('span.page-all-num').html(Math.ceil(($('#my-status-list>li:visible').length)/(pageNo)));
	$('.page-num').val(1);
	XXSfy();

}

//获取总页面个数
function totalPage(){
	// alert($('input.myxs').val());
	pageNo=Math.ceil($('input.myxs').val());
	if(isNaN(pageNo) || $('input.myxs').val().length<1){
		$('input.myxs').val(5);
		$('span.page-all-num').html(Math.ceil((dataNo)/(5)));
	}else{
		$('span.page-all-num').html(Math.ceil((dataNo)/(pageNo)));
	}
	// alert(parseInt($('input.page-num').val()));
	// alert(parseInt($('span.page-all-num').html()));
	if(parseInt($('input.page-num').val())>parseInt($('span.page-all-num').html())){
		$('input.page-num').val(parseInt($('span.page-all-num').html()));
		pageNo=Math.ceil($('input.myxs').val());
		XXSfy();
	}else{
		pageNo=Math.ceil($('input.myxs').val());
		huanYe();
	}
	
}


//上一页
function XXSprev(){
	// alert('aa');
	// alert($('input.page-num').val());
	if($('input.page-num').val()>1){
		$('input.page-num').val(parseInt($('input.page-num').val())-1);
	}
	XXSfy();
}

//下一页
function XXSnext(){
	//alert($('span.page-all-num').html());
	if(parseInt($('input.page-num').val())<parseInt($('span.page-all-num').html())){
		//alert('aa');
		$('input.page-num').val(parseInt($('input.page-num').val())+1);
	}
	XXSfy();
}

//换页
function XXSfy(){
	// alert('aa');
	pageCur=Math.ceil($('.page-num').val());
	if(isNaN(pageCur) || $('.page-num').val().length<1){
		$('.page-num').val(1);
	}
	pageCur=Math.ceil($('.page-num').val());
	huanYe();
}

//执行换页动作
function huanYe(){
	//alert(pageCur);
	var i=0;
	$('#my-status-list>li[typeid="'+pageType+'"]').each(function(){
		if(i<pageNo*(pageCur-1)){
			$(this).hide();
		}
		if(i>=pageNo*(pageCur-1) && i<pageNo*pageCur){
			$(this).show();
		}
		if(i>=pageNo*pageCur){
			$(this).hide();
		}
		i++;
	});
}

//删除新鲜事
function delXXS(ob){
	$('#tanchaungneirong').html('您确定要删除该新鲜事吗？');
	$('#tanchuang').show();
	$('button').click(function(){
		if($(this).attr('id')=='alarmConfirmButton'){
			$('#alarmConfirmButton').unbind('click');
			$.ajax({
				url:'/renren/index.php/Home/SelfNews/delXXS',
				type:'GET',
				data:'typeid='+$(ob).attr('status-type')+'&id='+$(ob).attr('status-id'),
				success:function(res){
					if('res'){
						$(ob).parents('li.ugc-list-item').remove();
						dataNo--;
						$('span.page-all-num').html(Math.ceil(dataNo/pageNo));
						if(parseInt($('input.page-current').val())>parseInt($('span.page-all-num').html())){
							$('input.page-current').val($('span.page-all-num').html());
							pageCur=$('input.page-current').val();
							totalPage();
						}
						
					}
				},
				error:function(){
					alert('ajax传值失败');
				},
			});
		}
	});
	
}

//批量删除日志界面
function multiDelDiaryDisplay(){
	$('#plsc').show();
}

//关闭批量删除日志界面
function gbplsc(){
	$('#plsc').hide();
}

//批量删除日志的执行
function plscDiary(){
	var diaryArr=new Array();
	$('ul.multiBList-ul>li').each(function(){
		if($(this).hasClass('selected')){
			diaryArr.push($(this).attr('diaryid'));
		}
	});
	if(diaryArr.length<1){return;}
	len=diaryArr.length;
	for(var i=0;i<diaryArr.length;i++){
		diaryArr[i]='dir'+i+'='+diaryArr[i];
	}
	dirdata=diaryArr.join('&');
	$.ajax({
		url:'/renren/index.php/Home/SelfNews/plscRiZhi',
		type:'GET',
		data:dirdata,
		success:function(res){
			if(res){
				$('ul.multiBList-ul>li').each(function(){
					if($(this).hasClass('selected')){
						var ss=$(this).attr('diaryid');
						//alert(ss);
						$('#my-status-list>li').each(function(){
							//alert(ss);
							if($(this).attr('typeid')=='1' && $(this).attr('xxid')==ss){
								$(this).remove();
								
							}
						});
						
					}
				});
			}
			$('#plsc').hide();
			dataNo=dataNo-len;
			$('span.page-all-num').html(Math.ceil(dataNo/pageNo));
			if(parseInt($('input.page-current').val())>parseInt($('span.page-all-num').html())){
				$('input.page-current').val($('span.page-all-num').html());
				pageCur=$('input.page-current').val();
				totalPage();
			}
		},
		error:function(){
			alert('ajax传值失败');
		},
	});
}