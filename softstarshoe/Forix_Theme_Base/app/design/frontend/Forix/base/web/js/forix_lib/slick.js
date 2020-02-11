define([
    'jquery',
    'matchMedia',
    "jquery/ui",
    "forix/libs",
    "forix/plugins/slick",
    'domReady!'
], function($){
    "use strict";

    $.widget('forix.slickSlider', {
        options: {
            accessibility: true,
            adaptiveHeight: false,
            appendArrows: null,
            appendDots: null,
            arrows: true,
            asNavFor: null,
            prevArrow: '<button type="button" data-role="none" class="slick-prev" aria-label="Previous" tabindex="0" role="button">Previous</button>',
            nextArrow: '<button type="button" data-role="none" class="slick-next" aria-label="Next" tabindex="0" role="button">Next</button>',
            autoplay: false,
            autoplaySpeed: 3000,
            centerMode: false,
            centerPadding: '50px',
            cssEase: 'ease',
            customPaging: function(slider, i) {
                return $('<button type="button" data-role="none" role="button" tabindex="0" />').text(i + 1);
            },
            dots: false,
            dotsClass: 'slick-dots',
            draggable: true,
            easing: 'linear',
            edgeFriction: 0.35,
            fade: false,
            focusOnSelect: false,
            infinite: true,
            initialSlide: 0,
            lazyLoad: 'ondemand',
            mobileFirst: false,
            pauseOnHover: true,
            pauseOnFocus: true,
            pauseOnDotsHover: false,
            respondTo: 'window',
            responsive: null,
            rows: 1,
            rtl: false,
            slide: '',
            slidesPerRow: 1,
            slidesToShow: 1,
            slidesToScroll: 1,
            speed: 500,
            swipe: true,
            swipeToSlide: false,
            touchMove: true,
            touchThreshold: 5,
            useCSS: true,
            useTransform: true,
            variableWidth: false,
            vertical: false,
            verticalSwiping: false,
            waitForAnimate: true,
            zIndex: 1000
        },

        _create: function() {
            this._callSlider();
        },

        _destroy: function(){
            this.element.unslick();
        },

        _callSlider: function(){
            if(!this.options.appendArrows){
                this.options.appendArrows = this.element;
            }else{
                this.options.appendArrows = $(this.options.appendArrows);
            }

            if(!this.options.appendDots){
                this.options.appendDots = this.element;
            }else{
                this.options.appendDots = $(this.options.appendDots);
            }

            this.element.slick(this.options);
        },

        destroy: function(){
            this._destroy();
        }
    });

    return $.forix.slickSlider;
});