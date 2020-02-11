/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define(
    [
        'jquery',
        'Aheadworks_Giftcard/js/view/payment/giftcard',
        'Aheadworks_Giftcard/js/action/apply-giftcard-code',
        'Aheadworks_Giftcard/js/view/payment/one-step/apply-by-code-flag',
        'Aheadworks_Giftcard/js/model/payment/giftcard-messages',
        'Aheadworks_OneStepCheckout/js/model/payment-option/message-processor'
    ],
    function (
        $,
        Component,
        applyGiftCardCodeAction,
        applyByCodeFlag,
        messageContainer,
        messageProcessor
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Aheadworks_Giftcard/payment/one-step/gift-card',
                inputSelector: '#giftcard_code'
            },

            /**
             * @inheritdoc
             */
            apply: function() {
                var self = this,
                    input = $(this.inputSelector);

                if (this.validate()) {
                    applyByCodeFlag(false);
                    applyGiftCardCodeAction(this.giftcardCode())
                        .done(function () {
                            self.giftcardCode('');
                            messageProcessor.processSuccess(input, messageContainer)
                        })
                        .fail(function () {
                            messageProcessor.processError(input, messageContainer)
                        });
                }
            },

            /**
             * @inheritdoc
             */
            applyByCode: function(giftcardCode) {
                applyByCodeFlag(true);
                applyGiftCardCodeAction(giftcardCode);
            },

            /**
             * @inheritdoc
             */
            validate: function() {
                messageProcessor.resetImmediate($(this.inputSelector));

                return this._super();
            }
        });
    }
);
