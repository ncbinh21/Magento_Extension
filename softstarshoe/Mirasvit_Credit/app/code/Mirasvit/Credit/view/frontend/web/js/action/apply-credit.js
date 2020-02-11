define([
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/error-processor',
    'Mirasvit_Credit/js/model/payment/messages',
    'mage/storage',
    'Magento_Checkout/js/action/get-totals',
    'mage/translate',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/payment/method-list'
], function (ko,
             $,
             quote,
             urlBuilder,
             paymentService,
             errorProcessor,
             messageContainer,
             storage,
             getTotalsAction,
             $t,
             getPaymentInformationAction,
             paymentMethodList) {
    'use strict';

    return function (apply, isLoading) {
        isLoading(true);

        var url, message;

        if (apply) {
            url = urlBuilder.createUrl('/carts/mine/credit/apply', {});
            message = $t('Store credit was successfully applied');
        } else {
            url = urlBuilder.createUrl('/carts/mine/credit/cancel', {});
            message = $t('Store credit was successfully canceled');
        }

        return storage.post(
            url,
            {},
            false
        ).done(function (response) {
            if (response) {
                var deferred = $.Deferred();
                if (response) {
                    getTotalsAction([], deferred);
                    getPaymentInformationAction(deferred);

                    $.when(deferred).done(function () {
                        paymentService.setPaymentMethods(
                            paymentMethodList()
                        );
                    });
                    messageContainer.addSuccessMessage({'message': message});
                }
            }
        }).fail(function (response) {
            errorProcessor.process(response, messageContainer);
        }).always(function () {
            isLoading(false);
        });
    };
});
