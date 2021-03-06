// Created by nktquan@gmail.com

$( document ).ready(function() {

    // Slider

    $('#home-slider').owlCarousel({
        loop:true,
        nav:true,
        items: 1
    });

    $("#globe-sub-slider1").owlCarousel({
        slideSpeed : 300,
        paginationSpeed : 400,
        loop: true,
        autoplay: 1000,
        items: 4,
        nav:true,
        dots: false
    });

    $("#globe-sub-slider2").owlCarousel({
        slideSpeed : 300,
        paginationSpeed : 400,
        loop: true,
        autoplay: 1000,
        items: 4,
        nav:true,
        dots: false
    });

    $("#globe-sub-slider3").owlCarousel({
        slideSpeed : 300,
        paginationSpeed : 400,
        loop: true,
        autoplay: 1000,
        items: 4,
        nav:true,
        dots: false
    });

    $("#globe-sub-slider4").owlCarousel({
        slideSpeed : 300,
        paginationSpeed : 400,
        loop: true,
        autoplay: 1000,
        items: 4,
        nav:true,
        dots: false
    });

    $("#product-viewed").owlCarousel({
        slideSpeed : 300,
        paginationSpeed : 400,
        loop: true,
        nav: true,
        autoplay: 1000,
        responsive : {
            0: {
                items: 3,
            },
            575: {
                items: 4,
            },
            768: {
                items: 5,
            }
        },
        dots: false
    });

    $("#product-viewed-2").owlCarousel({
        slideSpeed : 300,
        paginationSpeed : 400,
        loop: true,
        nav: true,
        autoplay: 1000,
        items: 5,
        dots: false
    });

    $('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });

    $('.other-page .globe-sub').mouseenter(function () {
        if ($(this).hasClass('open')) {
            $(this).removeClass('open');
        } else {
            $(this).addClass('open');
        }
    });

    $('.other-page .globe-sub').mouseleave(function () {
        $(this).removeClass('open');
    });

    $('.detail-block .see-more').click(function() {
        $(this).toggleClass('open');
        $('.detail-block .info-list').toggleClass('open');
    });

    if($(window).width() < 768) {
        $('.navbar-2').click(function () {
            if ($(this).hasClass('open')) {
                return;
            } else {
                $(this).addClass('open');
            }
        });

        $(document).mouseup(function(e) {
            var navbar = $(".navbar-2");
            if (!navbar.is(e.target) && navbar.has(e.target).length === 0) {
                navbar.removeClass('open');
            }
        });
    };

});