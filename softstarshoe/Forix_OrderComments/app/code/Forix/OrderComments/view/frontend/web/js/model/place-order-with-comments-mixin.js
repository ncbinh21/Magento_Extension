/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (placeOrderAction) {
        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderAction, function(originalAction, paymentData, messageContainer) {
			// adding order comments
			var orderCommentConfig = window.checkoutConfig.show_hide_custom_block;
			if(orderCommentConfig) // true
			{
				var order_comments = jQuery('.payment-method._active').find("textarea[name=comment-code]").val();

                // var order_comments=$('#comment-code').val();

                if(typeof paymentData.additional_data == "undefined" || paymentData.additional_data == null ){
                    paymentData.additional_data = new Object();
                }

                paymentData.additional_data.comments = order_comments;

                // console.log(paymentData);
			}
            return originalAction(paymentData, messageContainer);
        });
    };
});