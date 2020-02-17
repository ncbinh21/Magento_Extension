define(['jquery', 'matchMedia', "jquery/ui", 'domReady!'], function($, mediaCheck) {
    'use strict';
    $.widget('forix.backToTop', {
        options: {
            text: "Back to top"
        },
        _create: function() {
            var $backtotop = "<div id='back-to-top' class='back-to-top'><span>" + this.options.text + "</span></div>";
            if ($(this.element).length) {
                $(this.element).append($backtotop);
            }
            this._backToTop();
        },
        _backToTop: function() {
            $('#back-to-top').on('click', function(e) {
                e.preventDefault();
                $('html,body').animate({
                    scrollTop: 0
                }, 700);
            });
        }
    });
    return $.forix.breadcrumbs;
});
