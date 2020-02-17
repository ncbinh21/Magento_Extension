define([
    'jquery',
    'ko',
    'uiComponent',
    'mage/storage',
    'jquery/jquery.cookie'
], function ($, ko, Component, storage) {
    'use strict';
    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();

            return this;
        },

        /**
         * check domestic
         */
        isDomestic: function () {
            var storage;
            storage = $.initNamespaceStorage('mage-cache-storage').localStorage;
            var check_storage = storage.get('customer-info');
            var check_cookie = $.cookie('domestic-cookie');
            var result = false;
            if(check_cookie == 1){
                result = true;
            }
            if(check_storage) {
                result = check_storage.is_domestic == 1 || check_cookie == 1;
            }
            return result;
        }
    });
});