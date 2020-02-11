/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            isLoggedIn: false
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super();
            var customerInfo = customerData.get('customer');

            if (this.isLoggedIn && !customerInfo().fullname) {
                customerData.reload('customer', false);
            }
            this.awGiftcardCustomer = customerInfo;
        }
    });
});
