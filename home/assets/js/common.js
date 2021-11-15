$(function() {
    let menuWidth = $(window).width();
    window.onload = function(event) {
        $('.header_ul').css('right',-menuWidth + 'px');
    }

    // 모바일 메뉴 슬라이드
    $('.menuopen_menu').on('click',function() {
        $('.header_ul').animate({
            "right" : '0'
        },300)
        $('body').css('overflow','hidden');
    })

    // 모바일 메뉴 닫히기
    $('.close_btn').on('click',function() {
        $('.header_ul').animate({
            "right" : -menuWidth + 'px'
        },300)
        $('body').css('overflow','auto');
        $('body').css('overflow-x','hidden');
    })

    window.onresize = function(event){
        $('.header_ul').css('right',-$(window).width() + 'px');
    }
});