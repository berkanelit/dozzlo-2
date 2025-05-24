jQuery(document).ready(function($) {

    // MatchHeight
    if ($('.post-wrap.grid .post').length) {
        $('.post-wrap.grid .post').matchHeight();
    }

    // Slick slider for .small .dazzlo_slides
    if ($('.small .dazzlo_slides').length) {
        $('.small .dazzlo_slides').slick({
            dots: false,
            infinite: true,
            speed: 500,
            autoplay: true,
            autoplaySpeed: 4000,
            slidesToShow: 4,
            slidesToScroll: 1,
            arrows: false,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 880,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    }

    // Slick slider for .main .dazzlo_slides
    if ($('.main .dazzlo_slides').length) {
        $('.main .dazzlo_slides').slick({
            dots: true,
            infinite: true,
            speed: 750,
            fade: true,
            autoplay: true,
            pauseOnHover: true,
            pauseOnFocus: true,
            autoplaySpeed: 5000,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            dotsClass: "slider-dots"
        });
    }

    // Slick slider for .dazzlo_layoutbox2
    if ($('.dazzlo_layoutbox2').length) {
        $('.dazzlo_layoutbox2').slick({
            dots: true,
            infinite: true,
            speed: 750,
            fade: true,
            autoplay: true,
            pauseOnHover: true,
            pauseOnFocus: true,
            prevArrow: '<button type="button" class="slick-nav slick-prev"><i class="fa fa-angle-left"></i></button>',
            nextArrow: '<button type="button" class="slick-nav slick-next"><i class="fa fa-angle-right"></i></button>',
            autoplaySpeed: 5000,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            dotsClass: "slider-dots"
        });
    }

    // Slick slider for .dazzlo_layoutbox4
    if ($('.dazzlo_layoutbox4').length) {
        $('.dazzlo_layoutbox4').slick({
            dots: false,
            infinite: true,
            speed: 750,
            autoplay: true,
            pauseOnHover: true,
            pauseOnFocus: true,
            prevArrow: '<button type="button" class="slick-nav slick-prev"><i class="fa fa-angle-left"></i></button>',
            nextArrow: '<button type="button" class="slick-nav slick-next"><i class="fa fa-angle-right"></i></button>',
            autoplaySpeed: 5000,
            slidesToShow: 3,
            slidesToScroll: 1,
            arrows: true,
            responsive: [
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    }

    // Sticky Sidebar
    if ($('#content').length && $('#sidebar').length) {
        $('#content, #sidebar').theiaStickySidebar({
            additionalMarginTop: 30
        });
    }

    // Ribbon fade-in
    if ($('.ribbon').length) {
        $('.ribbon').fadeIn();
    }

    // Information bar close button
    if ($('.information-bar .container .close').length) {
        $('.information-bar .container .close').on('click', function() {
            $('.information-bar').addClass('hide');
        });
    }

    // Slicknav for menu
    if ($('.dazzlo-top-bar>.menu-wrap .main-nav').length) {
        $('.dazzlo-top-bar>.menu-wrap .main-nav').slicknav({
            prependTo: '.dazzlo-top-bar .top-bar',
            label: '',
            nestedParentLinks: false,
            allowParentLinks: true
        });
    }

    // Window resize handler
    $(window).resize(function() {
        var browserWidth = $(window).width();
        if (browserWidth > 920) {
            $(".main-nav, .secondary-menu").show();
        }
    });

    // Add caret icons to menu
    if ($('.main-nav > li:has(ul) > a').length) {
        var ico = $('<i class="fa fa-caret-down"></i>');
        $('.main-nav > li:has(ul) > a').append(ico);
    }
    if ($('.main-nav li:has(ul) li:has(ul) > a').length) {
        var ico1 = $('<i class="fa fa-caret-right"></i>');
        $('.main-nav li:has(ul) li:has(ul) > a').append(ico1);
    }

    // Search toggle
    if ($('.searchwrap a').length) {
        $('.searchwrap a').on('click', function(e) {
            e.preventDefault();
            $('.display-search-view').toggle('slide');
            $('#modal-1 a.ct_icon.search').toggleClass('inc-zindex');
            $('#modal-1 a.ct_icon.search i').addClass('fa-search').removeClass('fa-times-circle');
            $('a.ct_icon.search.inc-zindex i').addClass('fa-times-circle').removeClass('fa-search');
        });
    }

    // FitVids
    if ($(".post-content iframe").length) {
        $(".post-content iframe").wrap("<div class='fitvid'/>");
        $(".arrayvideo, .fitvid").fitVids();
    }

    // Scroll to top functionality
    $.fn.scrollToTop = function() {
        $(this).hide().removeAttr('href');
        var scrollDiv = $(this);
        $(window).scroll(function() {
            if ($(window).scrollTop() >= 1000) {
                $(scrollDiv).fadeIn('slow');
            } else {
                $(scrollDiv).fadeOut('slow');
            }
        });
        $(this).click(function() {
            $('html, body').animate({
                scrollTop: 0
            }, 'slow');
        });
    };
    if ($('#credits').length) {
        $('#credits').scrollToTop();
    }

    // Add header image class
    if ($('.hearder-holder .header-image').length) {
        $('body').addClass('headerimage');
    }

    // Menu focus handling
    if ($('.main-nav a').length) {
        $('.main-nav a').focus(function() {
            $(this).siblings('.sub-menu').addClass('focused');
        }).blur(function() {
            $(this).siblings('.sub-menu').removeClass('focused');
        });
    }

    // Sub-menu focus handling
    if ($('.sub-menu a').length) {
        $('.sub-menu a').focus(function() {
            $(this).parents('.sub-menu').addClass('focused');
        }).blur(function() {
            $(this).parents('.sub-menu').removeClass('focused');
        });
    }

    // Dazzlo toggle
    if ($(".dazzlo-toggle").length) {
        $(".dazzlo-toggle").click(function() {
            var index = $(this).data("index");
            $(".dazzlo_slides").removeClass("active");
            if ($(".dazzlo_slides").eq(index).length) {
                $(".dazzlo_slides").eq(index).addClass("active");
            }
        });

        $(".dazzlo-toggle").click(function() {
            if (!$(this).hasClass("active")) {
                $(".dazzlo-toggle").removeClass("active").addClass("notactive");
                $(this).removeClass("notactive").addClass("active");
            }
        });
    }

    // Modal handling
    const $open = $('#open-trigger');
    const $close = $('#close-trigger');

    if ($open.length && typeof MicroModal !== 'undefined') {
        $open.on('click', () => {
            MicroModal.show('modal-1', {
                onShow: () => $('body').addClass('howdy'),
                onClose: () => $('body').removeClass('howdy'),
                awaitCloseAnimation: true,
                openClass: 'open'
            });
        });
    }

    if ($close.length && typeof MicroModal !== 'undefined') {
        $close.on('click', () => MicroModal.close('modal-1'));
    }

});