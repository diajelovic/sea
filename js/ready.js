$(function(){
    var intervalId = 0;

    function ImagesRotate(panel){
        var $list = $(panel).find('img');
        clearInterval(intervalId);
        intervalId = setInterval(function(){
            var $current = $list.filter('.current');
            var $next = $current.next();
            if(!$next.size()){
                $next = $list.eq(0);
            }
            $next.fadeIn(500);
            $current.fadeOut(500, function(){
                $next.addClass('current');
                $current.removeClass('current');
            });
        }, 4000);
    }

    $('#housesTabs').tabs({selected: 0,
        select: function(e, ui){
            ImagesRotate(ui.panel);
        }
    });

    ImagesRotate(document.getElementById('housePhotos1'));

    $('.showCollapse').click(function(){
        if ($(this).next().hasClass('show')) {
            $(this).next().fadeOut().removeClass('show');
        }else{
            $(this).next().fadeIn().addClass('show');
        }
    }).hover(function(){$(this).addClass('hover');},function(){$(this).removeClass('hover');});
    $('.collapse').click(function(){
        $(this).fadeOut().removeClass('show');
    });
    $('.housesInfo h2 a').click(function(e){
        e.preventDefault();
        $( "#housesTabs" ).tabs( "select", $(this).attr('href'));
    });
});