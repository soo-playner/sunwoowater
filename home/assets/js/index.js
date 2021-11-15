function scrollBtnDisplay(aboutTop) {
    if($(window).scrollTop() < aboutTop - 1) {
        $('.scroll_down').show();
        $('.scroll_top').hide();
    } else {
        $('.scroll_down').hide();
        $('.scroll_top').show();
    }
}

function scrollBtnDisplayMobile(aboutTop) {
    if($(window).scrollTop() < aboutTop) {
        $('.scroll_top').hide();
    } else {
        $('.scroll_top').show();
    }
}

$(function() {
    // about 위치 체크
    let aboutTop = 0;
    let menuWidth = 0;
    window.onload = function(event) {
        aboutTop = $('.about').offset().top;

        menuScroll();

        if($(document).width() > 768) {
            scrollBtnDisplay(aboutTop)

            // 상단 메뉴 클릭시 class 추가
            $('.header_ul li a').on('click',function() {
                $('.header_ul li a').removeClass('active');
                $(this).addClass('active');
            })
        } else {
            $('.scroll_down').hide();
            $('.scroll_top').hide();
        }

        if($(window).width() <= 1200) {
            $('.scroll_down').hide();
            $('.scroll_top').hide();

            
        }

        menuWidth = $(window).width();
        $('.header_ul').css('right',-menuWidth + 'px');

        menuClass();
    }

    $('.header_ul li a').on('click',function() {
        var location = $(this).attr('data-location');
        $("html, body").stop().animate({scrollTop :  $('.' + location).offset().top}, 300);

        if($(document).width() < 768) {
            $('.header_ul').css('right',-$(window).width() + 'px');
            $('.header_ul li a').removeClass('active');
            $(this).addClass('active');
            $('body').css('overflow','inherit');
            $('body').css('overflow-x','hidden');
        }
    })


    // 스크롤 탑
    $('.scroll_top').on('click',function() {
        // $('html').scrollTop(0)
        $("html, body").stop().animate({scrollTop : 0}, 300);
    })

    $('.scroll_down').on('click',function() {
        $("html, body").stop().animate({scrollTop : $('.about').offset().top}, 300);
    })

    // 메뉴 스크롤시 변경
    $(document).scroll(function(){
        menuClass();

        menuScroll();

        if($(window).width() > 768) {
            scrollBtnDisplay(aboutTop)
        } else {
            $('.scroll_down').hide();
            $('.scroll_top').hide();
            scrollBtnDisplayMobile(aboutTop);
        }
    })

    function menuClass() {
        if($('.home').offset().top >= $(window).scrollTop() || $(window).scrollTop() < $('.about').offset().top - 40) { 
            $('.header_ul .menu a').removeClass('active');
            $('.header_ul .menu').eq(0).children('a').addClass('active');
        } else if($('.about').offset().top >= $(window).scrollTop() || $(window).scrollTop() < $('.business').offset().top - 40) {
            $('.header_ul .menu a').removeClass('active');
            $('.header_ul .menu').eq(1).children('a').addClass('active');
        } else if($('.business').offset().top >= $(window).scrollTop() || $(window).scrollTop() < $('.service').offset().top - 40) {
            $('.header_ul .menu a').removeClass('active');
            $('.header_ul .menu').eq(2).children('a').addClass('active');
        } else if($('.service').offset().top >= $(window).scrollTop() || $(window).scrollTop() < $('.faq').offset().top - 40) {
            $('.header_ul .menu a').removeClass('active');
            $('.header_ul .menu').eq(3).children('a').addClass('active');
        } else if($('.faq').offset().top >= $(window).scrollTop() - 40) {
            $('.header_ul .menu a').removeClass('active');
            $('.header_ul .menu').eq(4).children('a').addClass('active');
        }
    }

    function menuScroll() {
        if($(window).scrollTop() > 100) {
            $('header').addClass('floating');
            $('header > img').attr('src','./img/logo_color.png');
            $('header .myoffice_menu').attr('src','./img/ico_myoffice_color.png');
            $('header .menuopen_menu').attr('src','./img/ico_menuopen_color.png');
        } else {
            $('header').removeClass('floating');
            $('header > img').attr('src','./img/logo_white.png');
            $('header .myoffice_menu').attr('src','./img/ico_myoffice_white.png');
            $('header .menuopen_menu').attr('src','./img/ico_menuopen_white.png');
        }
    }

    // faq toggle
    $('.faq_list .question').on('click',function() {
        $(this).toggleClass('active');
        $(this).next().slideToggle(300);
    })

})
