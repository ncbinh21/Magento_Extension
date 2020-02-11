/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'jquery',
    'ko',
    'mage/translate',
    'Magento_Customer/js/action/login',
    'mage/url',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/model/customer',
], function (Component, $, ko, $t, loginAction, url, customerData, customer) {
    'use strict';

    return Component.extend({
        registerUrl: window.authenticationPopup.customerRegisterUrl,
        forgotPasswordUrl: window.authenticationPopup.customerForgotPasswordUrl,
        forgotPasswordPostUrl: window.authenticationPopup.baseUrl + 'customer/account/forgotpasswordpost/',
        loginPostUrl: window.authenticationPopup.baseUrl + 'mlogin/account/loginPost/',
        autocomplete: window.checkout.autocomplete,
        isLoading: ko.observable(false),

        initialize: function () {
            var self = this;
            this._super();
            //initSidebar();
            url.setBaseUrl(window.authenticationPopup.baseUrl);
            loginAction.registerLoginCallback(function() {
                self.isLoading(false);
            });
        },
        /** Is login form enabled for current customer */
        isActive: function() {
            var customer = customerData.get('customer');
            return customer() == false;
        },
        /** Provide login action */
        login: function(loginForm) {
            var loginData = {},
                formDataArray = $(loginForm).serializeArray();
            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if($(loginForm).validation()
                && $(loginForm).validation('isValid')
            ) {
                this.isLoading(true);
                loginAction(loginData, null, false);
            }
        },
        forgotpwd: function(theForm) {
            var loginData = {},
                formDataArray = $(theForm).serializeArray();
            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if($(theForm).validation()
                && $(theForm).validation('isValid')
            ) {
                $(theForm).attr('action', this.forgotPasswordPostUrl);
                theForm.submit();
            }
        },
        callForgotPwdForm: function() {
            if($('#forgotpwd-form').hasClass('no-display')) {
                $('#forgotpwd-form').removeClass('no-display');
                if($("#login-email").val()) {
                    $("#forgotpwd-email").val($("#login-email").val());
                }
            }
            else {
                $('#forgotpwd-form').addClass('no-display');
                if($("#login-email").val()) {
                    $("#forgotpwd-email").val($("#login-email").val());
                }
            }
            return false;
        },        
        closeSidebar: function() {
            var mini_login = $('[data-block="mini_login"]');
            mini_login.on('click', '[data-action="close"]', function(event) {
                event.stopPropagation();
                mini_login.find('[data-role="dropdownDialog"]').dropdownDialog("close");
            });
            return true;
        }
    });
});
