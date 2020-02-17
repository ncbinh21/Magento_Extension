define([
    'jquery',
    'productListToolbarForm',
    'ias-core',
    'ias-history',
    'ias-paging-next',
    'ias-spinner',
    'ias-noneleft',
    'ias-trigger-next',
    'ias-cache'
], function ($, toolBar, ias, iasHis, iasPageNext, iasSpinner, iasNoneleft, iasTriggerNext, iasCache) {
    'use strict';
    var defaultConfig = {
        srcLoading: '',
        mode: 1,
        container:  '.products.product-items',
        item:       '.product-item',
        pagination: '.pages ',
        prev:       '.previous',
        next:       '.toolbar-products:first .next',
        pageCurrent: '.toolbar-products:first .current span',
        pageTotal: '#toolbar-amount',
        spinnerHtml: '<div class="ias-spinner" style="text-align: center;"><img src="{src}" alt="Loading..."/></div>',
        buttonText: '<span class="current-page">Page {pageCurrent} of {pageTotal}</span> Load more',
        buttonHtml: '<div id="loadmore-container" class="load-more"><div id="product-loadmore" class="block-product-loadmore box-actions" >' +
        '<button id="btn-loadmore" class="action ias-trigger ias-trigger-next btn-load-more" >' +
        '<span>{text}</span>' +
        '</button><style scoped>#product-loadmore .loading-mask {display: none !important;}</style>' +
        '</div>' +
        '</div>'
    };

    return function (config) {
        var options = $.extend({}, defaultConfig, config);
        if (typeof options.htmlCustom.load_more === 'string') {
            options.buttonHtml = options.htmlCustom.load_more;
        }
        if (typeof options.htmlCustom.spinner === 'string') {
            options.spinnerHtml = options.htmlCustom.spinner;
        }

        var extensions = [
            new iasCache({}),
            new iasHis({
                prev: options.prev
            }),
            new iasPageNext(),
            new iasSpinner({
                src: options.srcLoading,
                html: options.spinnerHtml
            })
        ];
        if (options.mode === 1) {
            extensions.push(new iasTriggerNext({
                text: options.buttonText,
                html: options.buttonHtml
            }));
        }

        return $.ias({
            container:  options.container,
            item:       options.item,
            pagination: options.pagination,
            next:       options.next,
            pageCurrent: options.pageCurrent,
            pageTotal:  options.pageTotal,
            extensions: extensions
        });
    };
});