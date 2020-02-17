/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/place-order',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Ui/js/model/messages',
        'mage/url',
        'Magento_Customer/js/customer-data',
        'Forix_Payment/js/action/check-zipcode-distributor',
        'Magenest_SagepayUS/js/moment',
        'mage/cookies',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function ($, ko, setPaymentInformationAction, checkoutData,
              quote,
              customer,
              urlBuilder,
              placeOrderService,
              fullScreenLoader,
              errorProcessor,
              additionalValidators,
              redirectOnSuccessAction,
              messageContainer,
              url,
              customerData,
              checkZipcodeDistributor
    ) {
        'use strict';
        return function (target) {
            var paya_payment_code = target.prototype.getCode(), sage_payment_code = 'sage100_service';
            return target.extend({
                defaults: {
                    //template: 'Forix_Payment/payment/checkout-custom-payment-method'
                    template: 'Forix_Payment/payment/hidden-sage'
                },
                isValidate: null,
                isPlaceOrder: null,
                paymentCode: paya_payment_code,
                isUsePayAPayment: ko.observable(true),
                isDistributorCompany: function () {
                    if (customerData.get('is_distributor')()) {
                        console.log(customerData.get('is_distributor')().is_distributor);
                        return customerData.get('is_distributor')().is_distributor;
                    }
                    console.log(false);
                    return false;
                },
                checkResponseZipCode: function (response) {
                    if (undefined !== response['total_count']) {
                        if (0 < response.total_count) {
                            this.paymentCode = paya_payment_code;
                            return this.isUsePayAPayment(true);
                        }
                    }
                    this.paymentCode = sage_payment_code;
                    this.isUsePayAPayment(false);
                },

                getCcAvailableTypes: function(){
                    if (sage_payment_code === this.paymentCode) {
                        console.log(['VI']);
                    }
                    return this._super();
                },

                initObservable: function () {
                    var _self = this;
                    this._super();
                    if(this.isDistributorCompany()){
                        this.isUsePayAPayment(false);
                    }
                    quote.billingAddress.subscribe(function (address) {
                        if (null !== address && address.postcode) {
                            var paymentMethod = quote.paymentMethod();
                            if (null !== this.isValidate) {
                                this.isValidate.abort();
                            }
                            if (paymentMethod) {
                                if (paymentMethod.method === this.getCode()) {
                                    if (!this.isDistributorCompany()) {
                                        this.paymentCode = paya_payment_code;
                                        this.isUsePayAPayment(true);
                                        if (address.postcode) {
                                            fullScreenLoader.startLoader();
                                            this.isValidate = checkZipcodeDistributor(address.postcode)
                                                .done(this.checkResponseZipCode.bind(this))
                                                .done(function () {
                                                    fullScreenLoader.stopLoader();
                                                })
                                                .fail(function (response) {
                                                    if (0 === response.status) {
                                                        fullScreenLoader.stopLoader();
                                                    }
                                                });
                                        }
                                    }else{
                                        this.paymentCode = sage_payment_code;
                                        this.isUsePayAPayment(false);
                                    }
                                }
                            }
                        }
                    }, this);
                    return this;
                },


                /**
                 * Get data
                 * @returns {Object}
                 */
                getData: function () {
                    if(this.isPlaceOrder) {
                        if (sage_payment_code === this.paymentCode) {
                            return {
                                'method': sage_payment_code,
                                additional_data: {
                                    'cc_type': this.selectedCardType() != '' ? this.selectedCardType() : this.creditCardType(),
                                    'cc_exp_year': this.creditCardExpYear(),
                                    'cc_exp_month': this.creditCardExpMonth(),
                                    'cc_number': this.creditCardNumber(),
                                    'cc_cid': this.creditCardVerificationNumber()
                                }
                            };
                        }
                    }
                    return this._super();
                },

                placeOrder: function (data, event) {
                    var self = this;
                    if (this.getCode() == this.paymentCode) {
                        this._super();
                        return this;
                    }

                    if (event) {
                        event.preventDefault();
                    }

                    if (this.validate() && additionalValidators.validate()) {
                        this.isPlaceOrder = true;
                        this.isPlaceOrderActionAllowed(false);
                        this.realPlaceOrder();
                        this.isPlaceOrder = false;
                        return true;
                    }
                    return false;
                }
            });
        }
    });