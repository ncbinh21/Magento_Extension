/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'jquery',
    'ko',
    'mage/translate',
    'underscore',
    'mage/storage'
], function (Component, $, ko, $t, _) {
    'use strict';
    var storage, dataProvider;
    storage = $.initNamespaceStorage('mage-cache-storage').localStorage;

    return Component.extend({
        current_ground_condition: null,

        initialize: function () {
            this._super();
            if (undefined !== this.in_category && this.in_category) {
                this.initProductEvent();
            } else {
                if (undefined === storage.get('filter_ground_current_ground_condition')) {
                    this.current_ground_condition = this.back_data;
                } else {
                    this.current_ground_condition = storage.get('filter_ground_current_ground_condition');
                    storage.remove('filter_ground_current_ground_condition');
                }
            }
        },
        initProductEvent: function () {
            var self = this;
            $(document).on('click', '.product-item .product-outer a', function (e) {
                self.filter_ground_condition.url = window.location.href;
                storage.set('filter_ground_current_ground_condition', self.filter_ground_condition);
                return true;
            });
        },
        getUrl: function () {
            return this.current_ground_condition.url;
        },

        getName: function () {
            return this.current_ground_condition.name;
        }
    });
});
