define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    customerData.reload(['credit'], true);

    return Component.extend({
        initialize: function () {
            this._super();

            this.credit = customerData.get('credit');
        }
    });
});
