jQuery(function ($) {
    'use strict';

    // Mean Menu
    jQuery('.mean-menu').meanmenu({
        meanScreenWidth: "991"
    });

    // Navbar JS
    $(window).on('scroll',function() {
        if ($(this).scrollTop()>150){
            $('.navbar-area').addClass("is-sticky");
        }
        else{
            $('.navbar-area').removeClass("is-sticky");
        }
    });

    // Candidate Slider JS
    $('.condidate-slider').owlCarousel({
        loop:true,
        margin:30,
        nav:false,
        smartSpeed:1500,
        dots:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:2
            },
            992:{
                items:3
            },
            1200: {
                items:4
            }
        }
    })

    // Tastimonial Slider JS
    $('.testimonial-slider').owlCarousel({
        loop:true,
        margin:30,
        nav:true,
        dots:false,
        items:1,
        smartSpeed:2500,
        autoplay:false,
        autoplayTimeout:4000,
        navText:[
            "<i class='bx bx-chevrons-left'></i>",
            "<i class='bx bx-chevrons-right bx-tada'></i>"
        ]
    })

    // Nice Select
    $('select').niceSelect();


    // Tastimonial Two Slider JS
    $('.testimonial-slider-two').owlCarousel({
        loop:true,
        margin:30,
        nav:true,
        dots:false,
        smartSpeed:2500,
        autoplay:false,
        autoplayTimeout:4000,
        navText:[
            "<i class='bx bx-chevrons-left bx-tada'></i>",
            "<i class='bx bx-chevrons-right bx-tada'></i>"
        ],
        responsive:{
            0:{
                items:1
            },
            768:{
                items:2
            }
        }
    })

    // Subscribe form
    $('#subscribeForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend:function(){
                $(document).find('span.error-text').text('');
            },
            success: function(response) {
                if (response.status == 400) {
                    $.each(response.error, function(prefix, val){
                        $('span.'+prefix+'_error').text(val[0]);
                    })
                }else{
                    $('#subscribeForm')[0].reset();
                    $('#subscribed').show();
                    setTimeout(function(){
                        $('#subscribed').hide();
                    }, 3000);
                }
            }
        });
    });

    // FAQ JS
    $(".accordion-title").click(function(e){
        var accordionitem = $(this).attr("data-tab");
        $("#"+accordionitem).slideToggle().parent().siblings().find(".accordion-content").slideUp();

        $(this).toggleClass("active-title");
        $("#"+accordionitem).parent().siblings().find(".accordion-title").removeClass("active-title");
    });

    // Back To Top
    $(window).scroll(function () {
        if ($(this).scrollTop() != 0) {
                $('.top-btn').addClass('active');
            }
        else {
            $('.top-btn').removeClass('active');
        }
    });

    $('.top-btn').on('click',function(){
        $("html, body").animate({ scrollTop: 0 }, 2500);
        return false;
    });

    // Pre Loader
    $(window).on('load',function(){
        $(".loader-content").fadeOut(200);
    })

    // Switch Btn
	$('body').append("<div class='switch-box'><label id='switch' class='switch'><input type='checkbox' onchange='toggleTheme()' id='slider'><span class='slider round'></span></label></div>");

}(jQuery));

// function to set a given theme/color-scheme
function setTheme(themeName) {
    localStorage.setItem('frontend_theme', themeName);
    document.documentElement.className = themeName;
}
// function to toggle between light and dark theme
function toggleTheme() {
    if (localStorage.getItem('frontend_theme') === 'theme-dark') {
        setTheme('theme-light');
    } else {
        setTheme('theme-dark');
    }
}
// Immediately invoked function to set the theme on initial load
(function () {
    if (localStorage.getItem('frontend_theme') === 'theme-dark') {
        setTheme('theme-dark');
        document.getElementById('slider').checked = false;
    } else {
        setTheme('theme-light');
        document.getElementById('slider').checked = true;
    }
})();
