/**
 * Created by hidro on 7/12/17.
 */
define([
    'ko',
    "jquery",
    'uiComponent',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/shipping-service'
], function (ko, $, Component, modal, shippingService) {
    "use strict";
    return Component.extend({
        defaults: {
            template: 'Forix_AdvanceShipping/checkout/shipping/info-shipping'
        },
        hasHeavyItem: ko.observable(false),
        initialize: function () {
            var self = this;
            this._super();
            this.hasHeavyItem(this.is_heavy);
            return this;
            /*
            shippingService.getShippingRates().subscribe(function(value){
                self.hasHeavyItem(false);

                $.each(value, function(i, method){
                    if('distributor' === method.method_code) {
                        self.hasHeavyItem(true);
                    }
                });
            });
            */
        },
        openModal: function (target) {
            var modalwindow = $(target);
            modal({
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: '',
                buttons: [],
                modalClass: 'checkout-popup-shipping-note'
            }, modalwindow);
            modalwindow.modal('openModal');
        }
    });
});