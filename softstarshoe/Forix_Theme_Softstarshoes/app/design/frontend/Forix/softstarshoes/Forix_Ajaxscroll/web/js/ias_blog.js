define([
    'jquery',
    'productListToolbarForm',
    'ias-core',
    'ias-history',
    'ias-paging-next',
    'ias-spinner',
    'ias-trigger-next',
    'ias-cache'
], function ($, tbar, ias, ias_his, ias_page_next, ias_spinner, ias_trigger_next, ias_cache) {
    return function (config, element) {
        $('.pager').hide();
        var ias = $.ias({
            container: '.post-list-wrapper .post-list',
            item: '.item',
            pagination: '.pager .pages-items',
            next: '.pager .next',
            extensions: [
                new ias_trigger_next({
                    text: 'Older posts', // optionally
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
        return ias;
    };
});