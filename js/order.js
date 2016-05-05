function order_form_submit(action){
    $.ajax({
      type: 'POST',
      url: action,
      dataType: 'json',
      data: $('#orderForm').serialize(),
      success: function(data){
		// console.log(data);
		$('.messageSet').html('').hide();
		$('.errorSet').html('').hide();
		if (data.result){
			$.each(data.messages, function(){
				$('.messageSet').append('<li>' + this + '</li>');
			});
			$('.messageSet').show();
		}else{
			$.each(data.errors, function(){
				$('.errorSet').append('<li>' + this + '</li>');
			});
			$('#captcha').append(data.captcha);
			$('.errorSet').show();
		}
      }
    });
}

// function order_parseXML(xml){
// 	$('.sets').html('');
	
// 	var result = '';
// 	if ($('resultcode',xml).text() == 0)
// 	{
// 		result += '<ul class="errorSet clear ui-corner-all">';
// 		$('errorset error', xml).each(function(){
// 			result += '<li>' + $(this).text() + '</li>';
// 		})
// 		result += '</ul>';
// 	}
// 	else
// 	{
// 		result += '<ul class="messageSet clear ui-corner-all">';
// 			$('messageset message', xml).each(function(){
// 				result += '<li>' + $(this).text() + '</li>';
// 			})
// 		result += '</ul>';
// 	}
	
// 	$('.sets').append(result);
	
// 	if ($('captcha', xml).text() != '')
// 	{
// 		$('#captcha').append($('captcha', xml).text());
// 	}
// };