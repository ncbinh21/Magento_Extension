/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/error-processor',
        'Magento_GiftCardAccount/js/model/payment/gift-card-messages'
    ],
    function (
        $,
        urlBuilder,
        storage,
        customer,
        quote,
        getPaymentInformationAction,
        fullScreenLoader,
        errorProcessor,
        messageList
    ) {
        'use strict';

        return function (giftCardCode) {
            var serviceUrl;

            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/carts/guest-carts/:cartId/giftCards/:giftCardCode', {
                    cartId: quote.getQuoteId(),
                    giftCardCode: giftCardCode
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/giftCards/:giftCardCode', {
                    giftCardCode: giftCardCode
                });
            }

            fullScreenLoader.startLoader();

            return storage.delete(
                serviceUrl
            ).done(
                function (response) {
                    if (response) {
                        $.when(getPaymentInformationAction()).always(function () {
                            fullScreenLoader.stopLoader();
                            // Update Scroll
                            var miniCart = $('[data-block=\'summary-minicart\']');
                            if (miniCart.data('forixScrollData')) {
                                miniCart.scrollData('updateScroll');
                            }
                        });
                    }
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageList);
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);
