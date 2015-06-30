$(window).resize(function(){
    $('#left-menu, #plugins').height($(document).height() - $('#main-navbar').height() - 3);
}).resize();
