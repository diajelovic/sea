function calendar_form_submit(action, houseId, lang){
    /*$.ajax({
      type: 'POST',
      url: action,
      dataType: 'xml',
      data: ({houseId: houseId, lang: lang}),
      success: calendar_parseXML,
    });*/
}

function calendar_parseXML(xml)
{
	$('#calendar').html('');
	
	$('month', xml).each(function(){
		var result='';
		result += '<div class="ui-datepicker"><div class="ui-datepicker-header  ui-widget-header ui-corner-all"><div class="ui-datepicker-title">'
		result += $(this).attr('monthname') + '</div></div><table class="ui-datepicker-calendar"><thead><tr>';
		$(this).find('week:first date').each(function(){
			result += '<th'; 
			if ($(this).attr('weekend') == 'true')
			{
				result += ' class="ui-datepicker-week-end"';
			}
			result += '><span title="' + $(this).attr('dayname') + '">' + $(this).attr('dayshortname') + '</span></th>'
		});
		result += '</tr></thead><tbody>';
		$(this).find('week').each(function(){
			result += '<tr>';
			$(this).find('date').each(function(){
				result += '<td';
				if ($(this).text() != '0')
				{
					if ($(this).attr('weekend') == 'true')
					{
						result += ' class="week-end"';
					}
					result += '><span class="ui-state-default';
					if ($(this).attr('type') != 'open')
					{
						result += ' ui-state-disabled';
					}
					result += '">' + $(this).text() + '</span></td>'; 
				}
				else
				{
					result += '>&nbsp;</td>';
				}
			})
			result += '</tr>';
		})
		result += '</tbody></table></div>'
		$('#calendar').append(result);
	})
	
	$('#calendar').append('<div class="clear"></div>');
}