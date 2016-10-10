$(function(){
	//验证用户名是否唯一
	$('#regEmail').blur(function(){
		if($(this).val().length==0){
			//alert($('this').html());
			$('#error-show').show();
			$('#error-show').html('用户名不能为空');
			return;
		}
		$.ajax({
			url:"/renren/index.php/Public/doCheck",
			type:'POST',
			data:'user=' + $(this).val(),
			dataType:'text',
			success:function(res){
				if(!res){
					$('#eg').addClass('mobileright');
					if($('#error-show').html()=='当前用户已被注册'){
						$('#error-show').html('');
						$('#error-show').hide();
					}
				}else{
					$('#eg').removeClass('mobileright');
					$('#error-show').show();
					$('#error-show').html('当前用户已被注册');
				}
			},
			error:function(){
				alert('ajax传值失败');
			},
		});
	});

	//验证密码是否符合规范
	$('#pwd').blur(function(){
		if($('#pwd').val().length < 6 || $('#pwd').val().length > 20){
			$('#wg').removeClass('mobileright');
			$('#error-show').show();
			$('#error-show').html('密码短');
		}else{
			$('#wg').addClass('mobileright');
			if($('#error-show').html()=='密码短'){
				$('#error-show').html('');
				$('#error-show').hide();
			}
		}
	});

	//用于查缺补漏
	$('#icode').focusin(function(){
		if($('#eg').attr('class')!='mobileright'){
			$('#error-show').show();
			$('#error-show').html('当前用户已被注册');
		}
		if($('#wg').attr('class')!='mobileright'){
			$('#error-show').show();
			$('#error-show').html('密码短');
		}
		if($('#eg').attr('class')=='mobileright' && $('#wg').attr('class')=='mobileright'){
			$('#error-show').hide();
			$('#error-show').html('');
		}
	});

	//验证验证码及登录
	$('#mail_button_submit1').click(function(){
		$.ajax({
			// url:"doSubmit?user="+$('#regEmail').val()+'&pwd='+$('#pwd').val()+'&icode='+$('#icode').val(),
			url:"/renren/index.php/Public/doSubmit",
			data:'user='+$('#regEmail').val()+'&pwd='+$('#pwd').val()+'&icode='+$('#icode').val(),
			type:'POST',

			success:function(res){
				if(res=='N'){
					$('#error-show').show();
					$('#error-show').html('验证码不正确');
					return false;
				}else{
					$.ajax({
						url:"submit",
						data:'user='+$('#regEmail').val()+'&pass='+$('#pwd').val(),
						type:'POST',
						success:function(uuid){
							if(uuid>0){
								window.location.href='info/id/'+uuid;
							}else{
								alert('数据写入失败');
							}
						},
						error:function(){
							alert('ajax传值失败');
						},
					});

					

				}
			},
			error:function(){
				alert('ajax传值失败');
			},
		});

	});
	
	
});

function change(){
	document.getElementById('verifyPic').src='code?a=Math.random()';
}