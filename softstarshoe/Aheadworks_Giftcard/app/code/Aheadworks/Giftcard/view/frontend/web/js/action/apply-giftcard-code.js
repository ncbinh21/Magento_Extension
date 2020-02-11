/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Aheadworks_Giftcard/js/model/resource-url-manager',
    'Magento_Checkout/js/model/error-processor',
    'Aheadworks_Giftcard/js/model/payment/giftcard-messages',
    'mage/storage',
    'Magento_Checkout/js/model/totals',
    'mage/translate',
    'Magento_Checkout/js/action/get-payment-information',
    'Aheadworks_Giftcard/js/action/get-customer-giftcards',
    'Magento_Checkout/js/model/full-screen-loader'
], function (
    $,
    quote,
    urlManager,
    errorProcessor,
    messageContainer,
    storage,
    totals,
    $t,
    getPaymentInfoAction,
    getCustomerGiftcardsAction,
    fullScreenLoader
) {
    'use strict';
    return function (giftcardCode) {
        var quoteId = quote.getQuoteId(),
            url = urlManager.getApplyGiftcardUrl(giftcardCode, quoteId),
            message = $t('Your Gift Card code was successfully applied');

        fullScreenLoader.startLoader();
        return storage.put(
            url,
            {},
            false
        ).done(
            function (response) {
                if (response) {
                    var deferred = $.Deferred();

                    getCustomerGiftcardsAction();
                    totals.isLoading(true);
                    getPaymentInfoAction(deferred);
                    $.when(deferred).done(function () {
                        totals.isLoading(false);
                    });
                    messageContainer.addSuccessMessage({
                        'message': message
                    });
                }
            }
        ).fail(
            function (response) {
                totals.isLoading(false);
                errorProcessor.process(response, messageContainer);
            }
        ).always(
            function() {
                fullScreenLoader.stopLoader();
            }
        );
    };
});
