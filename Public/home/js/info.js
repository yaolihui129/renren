$(function(){
	$('#realname').blur(function(){
		if(!$('#realname').val().match(/^[\u4E00-\u9FA5\uF900-\uFA2D]{2,4}$/g)){
			$('#base-info-tip').show();
			return;
		}else{
			$('#base-info-tip').hide();
		}
	});
	
	$('#male').click(function(){
		$(this).addClass('male');
		$('#female').removeClass('female');
		$('#sex').val('1');
	});

	$('#female').click(function(){
		$(this).addClass('female');
		$('#male').removeClass('male');
		$('#sex').val('0');
	});

	$('#birth-year').change(function(){
		$('#birth-month>option:nth-child(n+2)').remove();
		$('#birth-day>option:nth-child(n+2)').remove();
		if($(this).val().length>0){
			for(var i=1;i<13;i++){
				$('#birth-month').append('<option value="'+i+'">'+i+'</option>');
			}
		}else{
			$('#birth-month>option:nth-child(n+2)').remove();
		}
	});
		
	$('#birth-month').change(function(){
		if($(this).val().length>0){
			$('#birth-day>option:nth-child(n+2)').remove();
			var $m=$(this).val();
			//alert($m);
			switch(parseInt($m)){
				case 1:
				case 3:
				case 5:
				case 7:
				case 8:
				case 10:
				case 12:
					for(var i=1;i<32;i++){
						$('#birth-day').append('<option value="'+i+'">'+i+'</option>');
					};
					break;
				case 4:
				case 6:
				case 9:
				case 11:
					for(var i=1;i<31;i++){
						$('#birth-day').append('<option value="'+i+'">'+i+'</option>');
					}
					break;
				case 2:
					if($('#birth-year').val()%400==0 || ($('#birth-year').val()%4==0 && $('#birth-year').val()%100!=0)){
						for(var i=1;i<30;i++){
							$('#birth-day').append('<option value="'+i+'">'+i+'</option>');
						}
					}else{
						for(var i=1;i<29;i++){
							$('#birth-day').append('<option value="'+i+'">'+i+'</option>');
						}
					}
					break;
				default:
					$('#birth-day>option:nth-child(n+2)').remove();
			}
		}else{
			$('#birth-day>option:nth-child(n+2)').remove();
		}
	});

	$('#birth-day').change(function(){
		if($(this).val().length>0){
			$('#born-timeline').addClass('show');
			$('#born-timeline>span:first-child').addClass('itemflag');
			$('#born-timeline>div').remove();
			$('#born-timeline').append('<div class="item-infos"><h3 class="info-title">'+$('#realname').val()+'出生啦</h3><span class="info-content">'+$('#birth-year').val()+'年'+$('#birth-month').val()+'月'+$('#birth-day').val()+'日</span></div></li>');
		}
	});

	$('#homeprovince').change(function(){
		if($('#homeprovince').val().length>1){
			$(this).addClass('high');
			$('#homeprovince_city').addClass('high').show();
			$('#homeprovince_city')
			$.ajax({
				url:"/renren/index.php/Public/city",
				type:"GET",
				data:"fcode="+$(this).val(),
				success:function(list){
					if(list){
						$('#homeprovince_city>option:nth-child(n+2)').remove();
						for(var i=0;i<eval(list).length;i++){
							$('#homeprovince_city').append('<option value="'+eval(list)[i].code+'">'+eval(list)[i].name+'</option>');
						}
					}
				},
				error:function(){
					alert('ajax传值失败');
				}
			});
		}else{
			$('#homeprovince_city>option:nth-child(n+2)').remove();
		}
		$('#homesheng').val($('#homeprovince').val());
	});

	$('#homeprovince_city').change(function(){
		if($('#homeprovince_city').val().length>1){
			$('#hometown').val($('#homeprovince_city').val());

		}
	});

	$('#base-info-save').click(function(){
		$('#base-info-tip').hide();
		//判断必填项是否完整
		if($('#realname').val().length<1){
			$('#base-info-tip').attr('data-type','errname').attr('style','top: -37px; left: 150px; display:block;').html('请输入真实中文名称，让朋友更容易找到你');
			return false;
		}
		if($('#birth-year').val().length<1){
			$('#base-info-tip').attr('data-type','birthYear').attr('style','top: 35px; left: 147px; display:block;').html('请输入年份');
			return false;
		}
		if($('#birth-month').val().length<1){
			$('#base-info-tip').attr('data-type','birthMonth').attr('style','top: 35px; left: 315px; display:block;').html('请输入月份');
			return false;
		}
		if($('#birth-day').val().length<1){
			$('#base-info-tip').attr('data-type','birthDay').attr('style','top: 35px; left: 315px; display:block;').html('请输入日期');
			return false;
		}
		if($('#reg-school1').val().length<1){
			$('#base-info-tip').attr('data-type','schoolsubinfo').attr('style','width: 294px; top: 88px; left: 150px;').html('请输入学校');
			return false;
		}

		//通过ajax传值
		$.ajax({
			url:"/renren/index.php/Public/insert",
			type:"POST",
			data:"name="+$('#realname').val()+"&sex="+$('#sex').val()+"&year="+$('#birth-year').val()+"&month="+$('#birth-month').val()+"&day="+$('#birth-day').val()+"&school="+$('#reg-school1').val()+"&sheng="+$('#homesheng').val()+"&city="+$('#hometown').val()+"&id="+$('#uuid').val(),
			dataType:"text",
			success:function(res){
				window.location.href='/renren/index.php/HomePage/index/id/'+$('#uuid').val();
			},
			error:function(){
				alert('ajax传值失败');
			},
		});

	});

});