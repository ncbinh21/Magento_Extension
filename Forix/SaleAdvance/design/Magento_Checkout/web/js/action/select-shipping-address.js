/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'Magento_Checkout/js/model/quote'
    ],
    function(quote) {
        'use strict';
        return function(shippingAddress) {
            var preventRegionList = window.checkoutConfig.region_prevent_list.split(",");
            quote.shippingAddress.isPreventList = false;
            if(preventRegionList.includes(shippingAddress.regionCode)) {
                quote.shippingAddress.isPreventList = true;
            }
            quote.shippingAddress(shippingAddress);
        };
    }
);
