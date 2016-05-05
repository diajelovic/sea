﻿/**
* Плагин slideContent
* использует библиотеку jQuery 1.3.2
*
* Контент оформленный в виде списка преобразует в слайдер,
* если  все  елементы   не помещаются в одну строку.
* 
* версия 1.3
* Автор Андрей Борха diajelovic@gmail.com
*/
(function(){jQuery.fn.slideContent=function(options){var settings=jQuery.extend({addClass:'slide',resizable:true,slide:'item',contentJumpPosition:1,click:function(){}},options);function slideContentInit($obj){$obj.addClass(settings.addClass+'Content').wrap('<div class="'+settings.addClass+'ItemsContainer"></div>');$obj.parent().wrap('<div class="'+settings.addClass+'Container"></div>');resizeContent($obj);if(settings.resizable){$(window).resize(function(){resizeContent($obj)})}$obj.find('li').click(settings.click)}function contentJump($obj,animate){var left=0;for(var i=0;i<settings.contentJumpPosition-1;i++){left+=parseInt($obj.find('li').get(i).offsetWidth)}if((left)<(settings.galleryWidth-settings.showWidth)){if(animate)$obj.animate({'left':-left+'px'},600);else $obj.css('left',-left+'px');settings.leftPosition=settings.contentJumpPosition;settings.rightPosition='';$obj.parent().prev().find('div').css('visibility','visible');if(settings.contentJumpPosition==1)$obj.parent().prev().find('div').css('visibility','hidden');$obj.parent().next().find('div').css('visibility','visible')}else{if(animate)$obj.animate({'left':settings.showWidth-settings.galleryWidth+'px'},600);else $obj.css('left',settings.showWidth-settings.galleryWidth+'px');settings.leftPosition='';settings.rightPosition=$obj.find('li').length;$obj.parent().next().find('div').css('visibility','hidden');$obj.parent().prev().find('div').css('visibility','visible')}checkPositions($obj)}function checkPositions($obj){var i=0;var width=0;settings.showCount=0;if(settings.rightPosition==''){i=settings.leftPosition-1;width=parseInt($obj.find('li').get(i).offsetWidth);while(width<=settings.showWidth){if(width==settings.showWidth)settings.rightPosition=i+1;width=width+parseInt($obj.find('li').get(++i).offsetWidth);settings.showCount++}}if(settings.leftPosition==''){i=settings.rightPosition-1;width=parseInt($obj.find('li').get(i).offsetWidth);while(width<=settings.showWidth){if(width==settings.showWidth)settings.leftPosition=i+1;settings.showCount++;width=width+parseInt($obj.find('li').get(--i).offsetWidth)}}}function changeDirection($obj){var i=0;var width=0;var left=0;$obj.stop(false,true);if(settings.rightPosition==''&&settings.slide=='item'){i=settings.leftPosition-1;width=parseInt($obj.find('li').get(i).offsetWidth);while(width<=settings.showWidth){width=width+parseInt($obj.find('li').get(++i).offsetWidth)}left=parseInt($obj.css('left'))-width+settings.showWidth;settings.rightPosition=i+1;settings.contentJumpPosition=i+1;$obj.animate({'left':left+'px'},600).parent().prev().find('div').css('visibility','visible');if(settings.rightPosition==$obj.find('li').length)$obj.parent().next().find('div').css('visibility','hidden');settings.leftPosition=''}else if(settings.rightPosition!=''&&settings.slide=='showItems'){settings.rightPosition=settings.rightPosition-settings.showCount;settings.contentJumpPosition=settings.rightPosition;for(var i=settings.contentJumpPosition;i<$obj.find('li').length;i++){left+=parseInt($obj.find('li').get(i).offsetWidth)}if((left)<(settings.galleryWidth-settings.showWidth))$obj.animate({'left':settings.showWidth-settings.galleryWidth+left+'px'},600);else{$obj.animate({'left':'0px'},600);$obj.parent().prev().find('div').css('visibility','hidden');settings.leftPosition=1;settings.rightPosition='';settings.contentJumpPosition=1}$obj.parent().next().find('div').css('visibility','visible')}else if(settings.leftPosition==''&&settings.slide=='item'){i=settings.rightPosition-1;width=parseInt($obj.find('li').get(i).offsetWidth);while(width<=settings.showWidth){width=width+parseInt($obj.find('li').get(--i).offsetWidth)}settings.leftPosition=i+1;settings.contentJumpPosition=settings.leftPosition;left=parseInt($obj.css('left'))+width-settings.showWidth;$obj.animate({'left':left+'px'},600).parent().next().find('div').css('visibility','visible');if(settings.leftPosition==1)$obj.parent().prev().find('div').css('visibility','hidden');settings.rightPosition=''}else if(settings.leftPosition!=''&&settings.slide=='showItems'){settings.leftPosition=settings.leftPosition+settings.showCount;settings.contentJumpPosition=settings.leftPosition;contentJump($obj,true)}}function slideLeft($obj){var left=0;$obj.stop(false,true);if(settings.leftPosition!=''&&settings.slide=='item'){left=parseInt($obj.css('left'))+$obj.find('li').get(settings.leftPosition-2).offsetWidth;$obj.animate({'left':left+'px'},600).parent().next().find('div').css('visibility','visible');settings.leftPosition=settings.leftPosition-1;settings.rightPosition='';settings.contentJumpPosition=settings.leftPosition;if(settings.leftPosition==1)$obj.parent().prev().find('div').css('visibility','hidden')}else if(settings.leftPosition!=''&&settings.slide=='showItems'){settings.rightPosition=settings.leftPosition-1;settings.leftPosition='';settings.contentJumpPosition=settings.rightPosition;for(var i=settings.contentJumpPosition;i<$obj.find('li').length;i++){left+=parseInt($obj.find('li').get(i).offsetWidth)}if((left)<(settings.galleryWidth-settings.showWidth))$obj.animate({'left':settings.showWidth-settings.galleryWidth+left+'px'},600);else{$obj.animate({'left':'0px'},600);$obj.parent().prev().find('div').css('visibility','hidden');settings.leftPosition=1;settings.rightPosition='';settings.contentJumpPosition=1}$obj.parent().next().find('div').css('visibility','visible')}else{changeDirection($obj)}checkPositions($obj)}function slideRight($obj){var left=0;$obj.stop(false,true);if(settings.rightPosition!=''&&settings.slide=='item'){left=parseInt($obj.css('left'))-$obj.find('li').get(settings.rightPosition).offsetWidth;$obj.animate({'left':left+'px'},600).parent().prev().find('div').css('visibility','visible');settings.rightPosition=settings.rightPosition+1;settings.leftPosition='';settings.contentJumpPosition=settings.rightPosition;if(settings.rightPosition==$obj.find('li').length)$obj.parent().next().find('div').css('visibility','hidden')}else if(settings.rightPosition!=''&&settings.slide=='showItems'){settings.leftPosition=settings.rightPosition+1;settings.contentJumpPosition=settings.leftPosition;contentJump($obj,true)}else{changeDirection($obj)}checkPositions($obj)}function resizeContent($obj,test){settings.galleryWidth=contentWidth($obj);$obj.removeAttr('style').parent().removeAttr('style');if(parseInt($obj.parent().parent().width())<settings.galleryWidth){if(!$obj.parent().hasClass('container-withButtons')){$obj.parent().addClass('container-withButtons').after('<div class="'+settings.addClass+'Right"><div></div></div>').before('<div class="'+settings.addClass+'Left"><div></div></div>');settings.showWidth=parseInt($obj.parent().parent().width())-parseInt($obj.parent().next().width())-parseInt($obj.parent().prev().width());$obj.parent().css('width',settings.showWidth+'px');$obj.parent().prev().find('div').click(function(){slideLeft($obj)});$obj.parent().next().find('div').click(function(){slideRight($obj)})}else{settings.showWidth=parseInt($obj.parent().parent().width())-parseInt($obj.parent().next().width())-parseInt($obj.parent().prev().width());$obj.parent().css('width',settings.showWidth+'px')}if(settings.contentJumpPosition<=$obj.find('li').length){contentJump($obj,false)}$obj.css('width',settings.galleryWidth+1+'px')}else{if($obj.parent().hasClass('container-withButtons')){$obj.parent().removeClass('container-withButtons').next().remove();$obj.css('left','0px').parent().removeAttr('style').prev().remove()}}}function contentWidth($obj){var width=0;$obj.find('li').each(function(){width+=this.offsetWidth});return width}return this.each(function(){if(this.tagName=='UL'){slideContentInit($(this))}})}})();