$(function(){
	$('ul').on('click', '.checkbox:not(.disabled)', function(){
		var $button = $(this).find('button');
		if ($button.data('checked') == 0){
			$button.data('checked', 1);
			$button.addClass('checked');
		}else{
			$button.data('checked', 0);
			$button.removeClass('checked');
		}
	});
	$('#pre').click(function(event) {
		if (number != 1){
			number -= 1;
			loadQuestion(number);
		}
	});
	$('#next').click(function(){
		if ($('.answer').hasClass('hidden')){
			var correct = true;
			var userData = new Array();
			if ($('.options button.checked').length == 0){
				alert('学霸，请留下你的答案');
				return;
			}
			$('.options').children('.checkbox').each(function(i){
				var $button = $(this).children('button');
				userData[i] = $button.data('checked');
				$(this).addClass('disabled');
			});
			userStorage.set(number, userData);
			var result = showCorrection();
			if (result && number <= total){
				setTimeout(function(){
					if (number == total){
						location.href = resultUrl;
					}else{						
						number += 1;
						loadQuestion(number);
					}
				}, 400);
			}else{
				$('.answer').removeClass('hidden');
			}
		}else if (number < total){
			number += 1;
			loadQuestion(number);
		}else{
			userStorage.upload();
			location.href = resultUrl;
		}
	});	
});
function jumpToPage(num){
	var userData = userStorage.getAll();
	if (!userData){
		length = 0;
	}else{
		length = userData.length;
	}
	if (num > length+1){
		alert('对不起，不可以跳题哦');
		return false;
	}else{
		number = num;
		loadQuestion(number);
		$('#questionListModal').modal('hide');
		return true;		
	}
}
function showCorrection(){
	var correct = true;
	$('.options button').each(function(){
		if ($(this).data('checked') == 1){
			if ($(this).data('correct') == 1){
				$(this).text('✔');
				$(this).addClass('correct').removeClass('checked');
			}else{
				$(this).text('✘')
				$(this).addClass('incorrect').removeClass('checked');
				correct = false;
			}
		}else{
			if ($(this).data('correct') == 1){
				$(this).addClass('correct');
				correct = false;
			}
		}
	});
	return correct;
}
function isCorrect(){
	var correct = true;
	$('.options').find('.checkbox button').each(function(){
		if ($(this).data('checked') != $(this).data('correct')){
			correct = false;
			return false;
		}			
	})
	return correct;
}
function loadQuestion(number){
	var question = bankStorage.get(number);
	$('.content,.answer').addClass('hidden');
	$('.number').text(number);
	$('.title').text(question.title);
	$options = $('.options').empty();
	for (var i=0; i<question.options.length; i++){
		var option = $('template[name=option]').html();
		var alphaID = String.fromCharCode(65+i);
		option = option.replace("{correct}", question.options[i].correct);
		option = option.replace("{alpha}", alphaID);
		option = option.replace("{option}", question.options[i].option);
		$options.append(option);
	}
	$('.answer').find('.key').html(question.key);
	if (question.analysis == ''){
		$('.analysis').parent('p').addClass('hidden');
	}else{
		$('.answer').find('.analysis').html(question.analysis);		
		$('.analysis').parent('p').removeClass('hidden');	
	}
	if (number == total){
		$('#next').text('提交');
	}
	$('.content').removeClass('hidden');
	loadUserData(number);
}
function loadUserData(number){
	var answer = userStorage.get(number);
	if (answer){
		$('.options').find('.checkbox').each(function(i){
			$(this).addClass('disabled');
			if (answer[i] == 1){
				$(this).find('button').data('checked', 1);
			}else{
				$(this).find('button').data('checked', 0);
			}
		})
		showCorrection();
		$('.answer').removeClass('hidden');
	}else{
		$('.answer').addClass('hidden');
	}
}
