define([
    'jquery'
], function($){

    var aSpan = $(".tab").find('span');
    var currIndex = 0;
    show(currIndex,-1);

    function show(nidx, oidx) {
        if( oidx >= 0 ) {
            $(".banner").eq(oidx).css({opacity:0});
            $(".tab > span").eq(oidx).removeClass('on');
        }
        if( nidx >= aSpan.length) {
            nidx = 0;
            currIndex = -1;
        }
        if(nidx < 0) {
            nidx = aSpan.length - 1;
            currIndex = aSpan.length;
        }
        $(".banner").eq(nidx).css({opacity:1});
        $(".tab > span").eq(nidx).addClass('on');
    }

    $(".next").click(function(){
        show(currIndex+1, currIndex)
        currIndex ++;
    });

    $(".prev").click(function(){
        show(currIndex-1, currIndex)
        currIndex --;
    });

    $(".tag > span").each(function(i) {
        $(this).on('click', function(i) {
            show(i, currIndex);
            currIndex = i;
        });
    });

    function auto() {
        show(currIndex+1, currIndex);
        currIndex ++;
    }
    // clearInterval(timer);
    var timer = setInterval(function(){ auto(); },2000);
    $('#theme_task_bander_wrap').mouseover(function (){
        clearInterval(timer);
    });
    $('#theme_task_bander_wrap').mouseout(function (){
        clearInterval(timer);
        timer = setInterval(function(){ auto();},2000);
    });

});
