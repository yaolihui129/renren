$(function(){
	//取消发表日志
	$('#cancelDiary').click(function(){
		// alert($('#container').html().length);
		if($('input[name="title"]').val().length<1 && $('#container').html().length<14473){
			window.location.href="/renren/index.php/HomePage/index/id/"+$('#uid').val();
			return false;
		}else{
			$('#tanchaungneirong').html('您有内容未保存,确定要离开吗？');
			$('#tanchuang').show();
			$("button").click(function(){
				//alert($(this).attr('id'));
				if($(this).attr('id')=="alarmConfirmButton"){
					window.location.href="/renren/index.php/HomePage/index/id/"+$('#uid').val();
					return false;
				}
			});
			
		}
		
	});
	


	//发表草稿
	$('#buttonDiary').click(function(){
		// if($('#container').html().length<14473){
		// 	return false;
		// }
		if($('input[name="title"]').val().length<1){
			$('#tanchaungneirong').html('您未填写标题，确定使用日期作为标题吗？');
			$('#tanchuang').show();
			$("button").click(function(){
				//alert($(this).attr('id'));
				if($(this).attr('id')=="alarmConfirmButton"){
					var date=new Date();
					$('.rijixinzeng').remove();
					$('input[name="title"]').val(date.getFullYear()+'-'+(date.getMonth()+1)+'-'+(date.getDate()));
					$('form[name="mydiaryform"]').append('<input type="hidden" name="status" class="rijixinzeng" value="0">');
					$('form[name="mydiaryform"]').trigger('submit');
				}
			});
		}else{
			$('.rijixinzeng').remove();
			//$('input[name="title"]').val(date.getFullYear()+'-'+(date.getMonth()+1)+'-'+(date.getDate()));
			$('form[name="mydiaryform"]').append('<input type="hidden" name="status" class="rijixinzeng" value="0">');
			$('form[name="mydiaryform"]').trigger('submit');
		}

		
	});

	//发表日志
	$('#releseDiary').click(function(){
		//alert($('#container').html().length);
		// if($('#container').html().length<14473){
		// 	return false;
		// }
		if($('input[name="title"]').val().length<1){
			$('#tanchaungneirong').html('您未填写标题，确定使用当前日期作为标题吗？');
			$('#tanchuang').show();
			$("button").click(function(){
				//alert($(this).attr('id'));
				if($(this).attr('id')=="alarmConfirmButton"){

					var date=new Date();
				
					$('.rijixinzeng').remove();
					$('input[name="title"]').val(date.getFullYear()+'-'+(date.getMonth()+1)+'-'+(date.getDate()));
					$('form[name="mydiaryform"]').append('<input type="hidden" name="status" class="rijixinzeng" value="1">');
					$('form[name="mydiaryform"]').trigger('submit');
				}
			});
		}else{

			$('.rijixinzeng').remove();
			//$('input[name="title"]').val(date.getFullYear()+'-'+(date.getMonth()+1)+'-'+(date.getDate()));
			$('form[name="mydiaryform"]').append('<input type="hidden" name="status" class="rijixinzeng" value="1">');
			$('form[name="mydiaryform"]').trigger('submit');
				
		}

		
	});

	//删除日志
	$('#delDiaryButton').click(function(){
		$('#tanchaungneirong').html('您确定要删除该日志吗？');
			$('#tanchuang').show();
			$("button").click(function(){
				//alert($(this).attr('id'));
				if($(this).attr('id')=="alarmConfirmButton"){

					window.location.href='/renren/index.php/Home/Diary/delDiary/id/'+$('input[name="uid"]').val()+'/diaryid/'+$('input[name="diaryid"]').val();
				}
			});
	});
});


//日志保存成功
function diarySave(){
	$('.ui-dialog-buttonset').hide();
	$('#tanchaungneirong').html('日志保存中。。。');
	$('#tanchuang').show();
}	



//日志删除成功
// function diaryDel(){
// 	$('.ui-dialog-buttonset').hide();
// 	$('#tanchaungneirong').html('日志删除成功，跳回首页中');
// 	$('#tanchuang').show();
// }	