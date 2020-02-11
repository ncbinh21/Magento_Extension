/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/alert',
    'mageUtils'
], function($, modal, alert, utils){
    'use strict';

    $.widget('mage.awGiftCardPreview', {
        options: {
            popupSelector: '#aw-gc-product-preview-popup',
            formSelector: '#product_addtocart_form',
            url: ''
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this.element.on('click', $.proxy(this.onClick, this));
            $(document).on('click', $.proxy(this.onClickDocument, this));
            var options = {
                'type': 'popup',
                'modalClass': 'aw-gc-product-previewer',
                'responsive': true,
                'innerScroll': true,
                'buttons': []
            };
            modal(options, $(this.options.popupSelector));
        },

        /**
         * Click on element
         * @private
         */
        onClick: function() {
            var self = this;

            $(this.options.popupSelector).html('');
            if (!this.validate()) {
                return;
            }

            $.ajax({
                url: this.options.url,
                data: $(this.options.formSelector).serializeArray(),
                method: 'post',
                context: this,
                showLoader: true
            }).success(function(response) {
                if (typeof response.success != "undefined") {
                    if (response.success) {
                        $(self.options.popupSelector).html(response.content);
                        $(self.options.popupSelector).modal('openModal');
                    } else {
                        alert({
                            content: response.content
                        });
                    }
                }
            });
        },

        /**
         * Click on document
         * @private
         */
        onClickDocument: function(e) {
            var popupContent = $(this.options.popupSelector),
                popupData = popupContent.data('mageModal');

            if (!utils.isEmpty(popupData) && popupData.options.isOpen
                && !popupContent.is(e.target) && popupContent.has(e.target).length === 0
            ) {
                popupContent.modal('closeModal');
            }
        },

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            return $(this.options.formSelector).validation() && $(this.options.formSelector).validation('isValid');
        }
    });

    return $.mage.awGiftCardPreview;
});
