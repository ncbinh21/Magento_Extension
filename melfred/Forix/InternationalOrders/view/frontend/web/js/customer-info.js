define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery/jquery.cookie'
], function ($, ko, Component, customerData) {
    'use strict';
    return Component.extend({
        defaults: {
            url: null,
            isBackOrder: null
        },
        /** @inheritdoc */
        initialize: function () {
            this._super();
            var self = this;
            self.customer = customerData.get('customer');
            self.customerInfo = customerData.get('customer-info');
            self.idDomesticCustomer = ko.computed(function(){
                var check_cookie = $.cookie('domestic-cookie');
                var result = self.customerInfo().is_domestic == 1 || check_cookie == 1;
                if(result){
                    self.customerInfo().is_domestic = 1;
                    if(!check_cookie){
                        $.cookie('domestic-cookie', 1, self.getCookieParams());
                    }
                    if(self.isBackOrder) {
                        $('#product-addtocart-button span').text('Back Order');
                    } else {
                        $('#product-addtocart-button span').text('Add to Cart');
                    }
                    $('.product-item-details .actions-primary .tocart span').each(function () {
                        if(!this.textContent) {
                            this.append('Add to Cart');
                        }
                    });
                    $('.price-box').removeClass("no-display");
                    $('.price-container').removeClass("no-display");
                }else{
                    if ($.isEmptyObject(self.customer()) && check_cookie === null) {
                        $("#is-domestic").removeClass("no-display");
                        $("body").addClass("is-domestic-hidden");
                    }
                    $('#product-addtocart-button span').text('Add to Quote');
                    $('.product-item-details .actions-primary .tocart span').each(function () {
                        if(!this.textContent) {
                            this.append('Add to Quote');
                        }
                    });
                    $('.price-box').addClass("no-display");
                    $('.price-container').addClass("no-display");
                }
                $('.tocart').removeClass('no-display');

                return result;
            });
        },

        chooseLocation: function(value_check){
            $.ajax({
                url: this.url,
                type: 'GET',
                data: {
                    'domestic-value': value_check
                }
            }).done(function (data) {
                location.reload();
            });
            // customerData.set('customer-info', {'is_domestic': value_check});
            // $.cookie('domestic-cookie', value_check, this.getCookieParams()); // Expire Cookie
            // $("#is-domestic").addClass("no-display");
            // $("body").removeClass("is-domestic-hidden");
            // location.reload();
        },
        chooseDomestic: function(){
            this.chooseLocation(1);
        },
        chooseInternational: function(){
            this.chooseLocation(0);
        },
        getCookieExpire: function(){
            var date = new Date();
            date.setTime(date.getTime() + 30 * 24 * 3600 * 1000);
            return date;
        },
        getCookieParams: function(){
            return {path: '/', expires: this.getCookieExpire(), secure: false};
        }
    });
});