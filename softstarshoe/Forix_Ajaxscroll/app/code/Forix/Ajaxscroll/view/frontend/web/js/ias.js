define([
    'jquery',
    'productListToolbarForm',
    'ias-core',
    'ias-history',
    'ias-paging-next',
    'ias-spinner',
    'ias-trigger-next',
    'ias-cache',
    "Magento_Catalog/js/catalog-add-to-cart-ext",
    'Forix_AlternateImage/js/alternate-image'
], function ($, tbar, ias, ias_his, ias_page_next, ias_spinner, ias_trigger_next, ias_cache) {
    return function (config, element) {
        $('#paging-label').hide();
        var html = '<div class="box-actions">' +
            '<button class="action ias-trigger ias-trigger-next btn-load-more">' +
            '<span class="pager-totals">{currentItemCount} of ' +config['totalItem']+ ' items </span><span>{text}</span></button>' +
            '</div>';
        var triggerNext = new ias_trigger_next({
            text: 'Load more items', // optionally
            html: html.replace('{currentItemCount}', config['currentItemCount']).replace('{text}', 'Load more items'),
            offset: 1
        });
        var ias = $.ias({
            container: '.column.main .product-items',
            //container:  '#maincontent .column.main:first',
            item: '.product-item',
            pagination: '.pages .pages-items',
            next: '.toolbar-products:first .next',
            currentItemCount: config['currentItemCount'], 
            extensions: [
                triggerNext,
                //new ias_cache({}),
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
        ias.on('rendered', function (args) {
            var $items = $(args);
            $items.alternateImage();
            $('.items.product-items').alternateImage();
            ias.currentItemCount = $('.product-items>li.product-item').length;
            //var $next = $('.btn-load-more .pager-totals');
            //$next.html(ias.currentItemCount + ' of ' + config['totalItem'] + ' items');
            triggerNext.html = html.replace('{currentItemCount}', ias.currentItemCount).replace('{text}', 'Load more items');
            args.forEach(function (li, index) {
                $(li).find("[data-role=tocart-form]").catalogAddToCartExt({});
            });
        });
        ias.on('loaded', function(data, items) {
            var $items = $(items);
            $items.alternateImage();
            $('.items.product-items').alternateImage();
        });
        ias.on('ready', function(items) {
            var $items = $(items);
            $items.alternateImage();
            $('.items.product-items').alternateImage();
        });
        ias.prev();
        return ias;
    };
});