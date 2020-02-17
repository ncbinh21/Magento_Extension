/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'ko',
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/place-order',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Ui/js/model/messages',
        'mage/url',
        'Magenest_SagepayUS/js/moment',
        'mage/cookies',
        'Magento_Payment/js/model/credit-card-validation/validator',
    ],
    function ($, Component, ko, setPaymentInformationAction, checkoutData,
              quote,
              customer,
              urlBuilder,
              placeOrderService,
              fullScreenLoader,
              additionalValidators,
              redirectOnSuccessAction,
              messageContainer,
              url,
              moment
    ) {
        'use strict';

        var clientId, merchantId, authKey, postbackUrl, salt, requestType, orderNumber, amount, preAuth, doVault, data, token;
        var pay_mode = window.checkoutConfig.payment.magenest_sagepayus.payment_mode;
        var is_test = window.checkoutConfig.payment.magenest_sagepayus.is_test;
        var resp,hash, cardinfo;
        var sage_environment, browser_debug;

        return Component.extend({
            defaults: {
                template: 'Magenest_SagepayUS/payment/sagepayus-payments-method'
            },
            messageContainer: messageContainer,
            expDate: ko.observable(""),
            placeOrderLabel: ko.observable("Place Order"),
            savedCards: ko.observableArray(JSON.parse(window.checkoutConfig.payment.magenest_sagepayus.card_data)),
            selectedCard: ko.observable(""),
            isSelectingCard: ko.observable(false),
            displaySaveCard: ko.observable(false),
            saveCardCheckbox: ko.observable(false),

            initObservable: function(){
                var self = this;
                this._super();
                this.expDate = ko.computed(function () {
                    if(this.creditCardExpYear()){
                        return moment("1/"+this.creditCardExpMonth()+"/"+this.creditCardExpYear(), "D/M/YYYY").format("MMYY");
                    }
                }, this);

                quote.getTotals().subscribe(function (value) {
                    if(pay_mode === 'inline'){
                        $('#paymentDiv').empty();
                    }
                });
                this.loadSageJs(function () {
                });
                if ((pay_mode === 'modal') || (pay_mode === 'inline')) {
                    self.placeOrderLabel("Continue");
                }
                this.selectedCard.subscribe(function (value) {
                    if(typeof value !== 'undefined'){
                        self.isSelectingCard(true);
                        self.displaySaveCard(false);
                    }else{
                        self.isSelectingCard(false);
                        self.displaySaveCard(window.checkoutConfig.payment.magenest_sagepayus.is_save_card);
                    }
                });
                this.displaySaveCard(window.checkoutConfig.payment.magenest_sagepayus.is_save_card);
                return this;
            },

            renderSagepay: function () {
                var df = $.Deferred();
                var self = this;
                fullScreenLoader.startLoader();
                $.ajax({
                    type: 'GET',
                    data: {
                        form_key: $.cookie('form_key'),
                        card_id: self.selectedCard()
                    },
                    url: url.build("sagepayus/checkout/request"),
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            clientId = response.clientId;
                            merchantId = response.merchantId;
                            authKey = response.authKey;
                            postbackUrl = response.postbackUrl;
                            salt = response.salt;
                            requestType = response.requestType;
                            orderNumber = response.orderNumber;
                            amount = response.amount;
                            sage_environment = response.environment;
                            browser_debug = response.debug;
                            preAuth = response.preAuth;
                            doVault = response.doVault;
                            data = response.data;
                            token = response.token;
                            if(sage_environment == 'prod'){
                                browser_debug = false;
                                is_test = false;
                            }

                            require(['jquery', 'PayJS/UI', 'PayJS/Core', 'PayJS/Request', 'PayJS/Response'],
                                function ($, $UI, $CORE, $REQUEST, $RESPONSE) {
                                    var option = {
                                        clientId: clientId,
                                        postbackUrl: postbackUrl,
                                        merchantId: merchantId,
                                        authKey: authKey,
                                        salt: salt,
                                        requestType: requestType,
                                        amount: amount,
                                        orderNumber: orderNumber,
                                        environment: sage_environment,
                                        debug: browser_debug,
                                        preAuth: preAuth,
                                        doVault: doVault,
                                        data: data
                                    };
                                    if(self.isSelectingCard()){
                                        option['token'] = token;
                                        $CORE.Initialize(option);
                                        setupAddress();
                                        fullScreenLoader.startLoader();
                                        $REQUEST.doTokenPayment(token,
                                                                self.creditCardVerificationNumber(),
                                                                callbackHandleResponse)
                                    } else {
                                        if ((pay_mode === 'modal') || (pay_mode === 'inline')) {
                                            var elementId = 'paymentButton';
                                            if (pay_mode === 'inline') {
                                                elementId = 'paymentDiv';
                                            }
                                            option['elementId'] = elementId;
                                            option['addFakeData'] = is_test;
                                            $UI.Initialize(option);
                                            setupAddress();
                                            $UI.setCallback(function ($RESP) {
                                                if ($RESP.getTransactionSuccess()) {
                                                    resp = $RESP.getApiResponse();
                                                    hash = $RESP.getResponseHash().hash;
                                                    cardinfo = $RESP.getPaymentDetails();
                                                    self.realPlaceOrder();
                                                } else {
                                                    resp = $RESP.getApiResponse();
                                                    hash = $RESP.getResponseHash().hash;
                                                    cardinfo = $RESP.getPaymentDetails();
                                                    // self.messageContainer.addErrorMessage({
                                                    //     message: $RESP.getCode() + " " + $RESP.getMessage()
                                                    // });
                                                    // console.log($RESP.getRawResponse());
                                                    // console.log($RESP.getCode() + " " + $RESP.getMessage());
                                                    // fullScreenLoader.stopLoader(true);
                                                }
                                            });
                                        }
                                        if (pay_mode === 'custom') {
                                            $CORE.Initialize(option);
                                            fullScreenLoader.startLoader();
                                            setupAddress();
                                            $REQUEST.doPayment( self.creditCardNumber(),
                                                                self.expDate(),
                                                                self.creditCardVerificationNumber(),
                                                                callbackHandleResponse);
                                            $("#magenest_sagepayus_cc_cid").on("keyup change", function () {
                                                var cvv = $("#magenest_sagepayus_cc_cid").val();
                                                cvv = cvv.replace(/\D/g, '');
                                                $("#magenest_sagepayus_cc_cid").val(cvv);
                                            });
                                        }
                                    }
                                    function setupAddress(){
                                        $CORE.setBilling(getAddress(quote.billingAddress()));
                                        if (!quote.isVirtual()) {
                                            $CORE.setShipping(getAddress(quote.shippingAddress()));
                                        }
                                        if (customer.isLoggedIn()) {
                                            $CORE.setCustomer({
                                                email: customer.customerData.email
                                            });
                                        }
                                    }

                                    function getAddress(address) {
                                        var _address = {
                                            name: address.firstname + " " + address.lastname
                                        };
                                        if (address.street[0]) {
                                            _address['address'] = address.street[0];
                                        }
                                        if (address.city) {
                                            _address['city'] = address.city;
                                        }
                                        if (address.region) {
                                            _address['state'] = address.region;
                                        }
                                        if (address.postcode) {
                                            _address['postalCode'] = address.postcode;
                                        }
                                        if (address.postcode) {
                                            _address['country'] = address.countryId;
                                        }
                                        return _address;
                                    }
                                    function callbackHandleResponse(_resp, status, jqxhr) {
                                        $RESPONSE.tryParse(_resp, status, jqxhr);
                                        if ($RESPONSE.getTransactionSuccess()) {
                                            resp = $RESPONSE.getApiResponse();
                                            hash = $RESPONSE.getResponseHash().hash;
                                            cardinfo = $RESPONSE.getPaymentDetails();
                                            self.realPlaceOrder();
                                        } else {
                                            resp = $RESPONSE.getApiResponse();
                                            hash = $RESPONSE.getResponseHash().hash;
                                            cardinfo = $RESPONSE.getPaymentDetails();
                                            self.realPlaceOrder();
                                            // self.messageContainer.addErrorMessage({
                                            //     message: $RESPONSE.getCode() + " " + $RESPONSE.getMessage()
                                            // });
                                            // console.log($RESPONSE.getCode() + " " + $RESPONSE.getMessage());
                                            // console.log($RESPONSE.getRawResponse());
                                            // fullScreenLoader.stopLoader(true);
                                        }
                                    }

                                    // fullScreenLoader.stopLoader(true);
                                    // df.resolve(true);
                                },
                                function (err) {
                                    resp = $RESP.getApiResponse();
                                    hash = $RESP.getResponseHash().hash;
                                    cardinfo = $RESP.getPaymentDetails();
                                    // df.reject(true);
                                    // self.messageContainer.addErrorMessage({
                                    //     message: "Script load error"
                                    // });
                                    // console.log(err);
                                    // fullScreenLoader.stopLoader(true);
                                }
                            );
                        }

                        if (response.error) {
                            resp = $RESP.getApiResponse();
                            hash = $RESP.getResponseHash().hash;
                            cardinfo = $RESP.getPaymentDetails();
                            // df.reject(true);
                            // self.messageContainer.addErrorMessage({
                            //     message: response.message
                            // });
                            // console.log(response.message);
                            // fullScreenLoader.stopLoader(true);
                        }
                    },
                    error: function (err) {
                        resp = $RESP.getApiResponse();
                        hash = $RESP.getResponseHash().hash;
                        cardinfo = $RESP.getPaymentDetails();
                        // df.reject(true);
                        // self.messageContainer.addErrorMessage({
                        //     message: "An error occurred on the server. Please try to place the order again."
                        // });
                        // console.log(err);
                        // fullScreenLoader.stopLoader(true);
                    }
                });

                return df.promise();
            },

            loadSageJs: function (callback) {
                $.getScript('https://www.sagepayments.net/pay/1.0.2/js/pay.min.js', function () {
                    console.log("Loaded SagePaymentJs v1.02");
                    callback();
                });
            },

            isActive: function () {
                return true;
            },

            hasVerification: function () {
                return true;
            },

            getCode: function () {
                return 'magenest_sagepayus';
            },

            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            },

            validate: function () {
                var self = this;
                if (typeof PayJS === "undefined"){
                    self.messageContainer.addErrorMessage({
                        message: "Cannot load PaymentsJS, please try again"
                    });
                    this.loadSageJs(function () {
                    });
                    return false;
                }
                if(this.getPayMode() === 'custom'){
                    return this.validateForm($('#'+this.getCode()+'-form'));
                }
                return true;
            },

            getPayMode: function () {
                return window.checkoutConfig.payment.magenest_sagepayus.payment_mode
            },

            /**
             * Get data
             * @returns {Object}
             */
            getData: function () {
                var self = this;
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'sage_resp': resp,
                        'sage_hash': hash,
                        'sage_cardinfo': JSON.stringify(cardinfo),
                        'sage_savecard': self.saveCardCheckbox()
                    }
                };
            },

            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && additionalValidators.validate()) {
                    $.when(self.renderSagepay()).then(function () {
                        $('#paymentButton').trigger("click");
                    });
                }

                return false;
            },

            realPlaceOrder: function () {
                var self = this;
                this.getPlaceOrderDeferredObject()
                    .fail(
                        function () {
                            self.isPlaceOrderActionAllowed(true);
                            fullScreenLoader.stopLoader(true);
                        }
                    ).done(
                    function () {
                        self.afterPlaceOrder();

                        if (self.redirectAfterPlaceOrder) {
                            redirectOnSuccessAction.execute();
                        }
                    }
                );
            }
        });
    }
);
