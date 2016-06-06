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
		if (!$('.answer').hasClass('hidden')){
			number += 1;
			loadQuestion(number);
		}
		else if (number < total){
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
			if (isCorrect()){
				number += 1;
				loadQuestion(number);				
			}else{
				$('.answer').removeClass('hidden');
			}
		}
	});	
});

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
				$(this).find('button').addClass('checked');
			}else{
				$(this).find('button').data('checked', 0);
			}
		})
		if (isCorrect()){
			$('.options').find('button.checked').addClass('correct').removeClass('checked');
		}
		$('.answer').removeClass('hidden');
	}else{
		$('.answer').addClass('hidden');
	}
}
