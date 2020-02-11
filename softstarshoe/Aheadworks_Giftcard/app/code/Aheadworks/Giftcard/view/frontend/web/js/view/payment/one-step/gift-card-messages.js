/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'ko',
    'Magento_Ui/js/view/messages',
    'Aheadworks_Giftcard/js/model/payment/giftcard-messages',
    'Aheadworks_Giftcard/js/view/payment/one-step/apply-by-code-flag'
], function (ko, Component, messageContainer, applyByCodeFlag) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        initialize: function (config) {
            return this._super(config, messageContainer);
        },

        /**
         * @inheritdoc
         */
        isVisible: function () {
            this.isHidden(this.messageContainer.hasMessages());

            return applyByCodeFlag();
        }
    });
});
