define([
    'Magento_Ui/js/view/messages',
    '../../model/payment/messages'
], function (Component, messageContainer) {
    'use strict';

    return Component.extend({
        initialize: function (config) {
            return this._super(config, messageContainer);
        }
    });
});
