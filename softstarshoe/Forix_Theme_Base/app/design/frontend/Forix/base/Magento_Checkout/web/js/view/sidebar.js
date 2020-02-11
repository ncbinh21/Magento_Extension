/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'uiComponent',
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/sidebar'
    ],
    function(Component, quote, ko, totals, $, sidebarModel) {
        'use strict';
        return Component.extend({

            /**
             * @param {HTMLElement} element
             */
            setModalElement: function(element) {
                sidebarModel.setPopup($(element));
            },

            getItemsQty: function() {
                if (totals.totals()) {
                    return parseFloat(totals.totals()['items_qty']);
                }

                return 0;
            }
        });
    }
);
