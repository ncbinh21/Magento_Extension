/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global confirm:true*/
define([
    'jquery',
    'matchMedia',
    'jquery/ui',
    'Magento_Checkout/js/sidebar'
], function ($, _) {

    $.widget("forix.sidebarExt", $.mage.sidebar, {

        _initContent: function () {
            this._super();
            this._removeFocusWhenHidden();
            this._trigger("afterInit",{},{});
        },

        _calcHeight: function () {

        },

        _removeFocusWhenHidden: function(){
            // fixed bug IE
            mediaCheck({
                media: '(min-width: 768px)',
                entry: $.proxy(function () {
                    $('.minicart-wrapper').hover(function () {
                    },function () {
                        $(this).find('.details-qty.qty input').blur();
                    });
                }, this),
                exit: $.proxy(function () {
                }, this)
            });
        }
    });

    return $.forix.sidebarExt;
});
