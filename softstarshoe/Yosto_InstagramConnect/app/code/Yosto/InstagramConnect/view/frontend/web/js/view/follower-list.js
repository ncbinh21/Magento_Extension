define([
    'jquery',
    'yostocoreowlcarousel'], function($){

    return function (config, element) {
        $(element).owlCarousel({
            loop: false,
            autoplay: true,
            responsiveClass: true,
            navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
            responsive: {
                320: {
                    items: config.config.items360,
                    nav: false
                },
                480: {
                    items: config.config.items480,
                    nav: false
                },
                640: {
                    items: config.config.items640,
                    nav: true
                },
                768: {
                    items: config.config.items768,
                    nav: true
                },
                1024: {
                    items: config.config.items1024,
                    nav: true
                },
                1440: {
                    items: config.config.items1440,
                    nav: true
                }
            }
        });
    };
});