define([
    'jquery',
    'productListToolbarForm',
    'ias-core',
    'ias-history',
    'ias-paging-next',
    'ias-spinner',
    'ias-trigger-next',
    'ias-cache',
    'Magento_Ui/js/modal/modal',
    'slick'
], function ($, tbar, ias, ias_his, ias_page_next, ias_spinner, ias_trigger_next, ias_cache, modal) {
    return function (config, element) {
        $('.pager').hide();
        var ias = $.ias({
            container: '.photos ul',
            item: '.item',
            pagination: '.pager .pages-items',
            next: '.pager .next',
            extensions: [
                new ias_trigger_next({
                    text: 'Load More Photos', // optionally
                    html: '<div class="box-actions">' +
                    '<button class="action ias-trigger ias-trigger-next btn-load-more">' +
                    '<span>{text}</span></button>' +
                    '</div>',
                    offset: 1
                }),
                new ias_cache({}),
                new ias_his({
                    prev: '.previous'
                }),
                new ias_page_next(),
                new ias_spinner({
                    src: config['srcLoading'], // optionally
                    html: '<div class="ias-spinner" style="text-align: center;"><img src="{src}"/></div>'
                })
            ]
        });
        ias.prev();
        ias.on('rendered', function(items) {
            // $('.slider-for').slick('reinit');
            // var $items = $(items);
            // $items.each(function() {
            //     $('.slider-for').slick('slickAdd',this);
            // });


            // $('.slider-for').slick({
            //     slidesToShow: 1,
            //     slidesToScroll: 1,
            //     arrows: false,
            //     fade: true,
            //     asNavFor: '.slider-nav'
            // });
            //
            // $('.slider-nav').slick({
            //     slidesToShow: 8,
            //     slidesToScroll: 1,
            //     asNavFor: '.slider-for',
            //     dots: false,
            //     centerMode: true,
            //     focusOnSelect: true
            // });

            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                clickableOverlay: false,
                modalClass: 'custom-modal',
                buttons: false
            };

            var popup2 = modal(options, $('.view-photo'));
            $(".photo-item").on('click', function () {
                position = $(this).attr('data-position');
                $('.slider-for').slick('slickGoTo', position);
                $(".view-photo").modal("openModal");
            });
        })
        $('.grid').trigger('loadImageFanPhoto');
        return ias;
    };
});