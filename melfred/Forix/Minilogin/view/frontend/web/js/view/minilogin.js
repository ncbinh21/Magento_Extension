/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'jquery',
    'ko',
    'mage/translate',
    'Forix_Minilogin/js/action/login',
    'mage/url',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/section-config',
    'underscore',
    'Forix_Minilogin/js/forix.miniscroll'
], function (Component, $, ko, $t, loginAction, url, customerData, sectionConfig, _) {
    'use strict';
    var storage = $.initNamespaceStorage('mage-cache-storage').localStorage;

    if (!ko.bindingHandlers['dynhtml']) {
        ko.bindingHandlers['dynhtml'] = {
            'init': function () {
                return {'controlsDescendantBindings': true};
            },
            'update': function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                ko.utils.setHtml(element, valueAccessor());
                ko.applyBindingsToDescendants(bindingContext, element);
            }
        };
    }

    return Component.extend({
        registerUrl: window.authenticationPopup.customerRegisterUrl,
        forgotPasswordUrl: window.authenticationPopup.customerForgotPasswordUrl,
        forgotPasswordPostUrl: window.authenticationPopup.baseUrl + 'customer/account/forgotpasswordpost/',
        loginPostUrl: window.miniloginUrl,
        loginAjaxUrl: 'customer/ajax/login',
        registerCompanyUrl: window.authenticationPopup.baseUrl + (window.companyLoginData ? window.companyLoginData.path : ''),
        autocomplete: window.checkout.autocomplete,
        isLoading: ko.observable(false),
        isMobile: false,
        revertWidth: 0,
        isB2BEnabled: false,
        initialize: function () {
            var self = this;
            this._super();
            //initSidebar();
            this.customer = customerData.get('customer');
            url.setBaseUrl(window.authenticationPopup.baseUrl);
            loginAction.registerLoginCallback(function (xhr, response) {
                self.isLoading(false);
                if (_.isObject(response) && !response.errors) {
                    self.reloadCustomerData(xhr, response);
                }
            });
            ko.computed(function () {
                if (this.isLoggedIn()) {
                    $('#mini-login').addClass('logged');
                }
            }, this);
            this.customerInfoFrm = customerData.get('customer-info-frm');
            this.customerLoginHtml = ko.computed(function () {
                if (_.isEmpty(this.customerInfoFrm())) {
                    customerData.reload(['customer-info-frm'], true);
                    this.customerInfoFrm = customerData.get('customer-info-frm');
                }
                if (this.isLoggedIn() || this.customerInfoFrm().isLogged) {
                    $('.action.showlogin').html('<span>My Borzall</span>')
                } else {
                    if (!_.isEmpty(this.customerInfoFrm())) {
                        $('.action.showlogin').html('<span>Login</span>');
                    }
                }
                if (this.customerInfoFrm()) {
                    self.isLoading(false);
                    return this.customerInfoFrm().frm_html;
                }
                return '';
            }, this);

            return this;
        },

        reloadCustomerData: function (xhr, response) {
            if (_.isObject(response) && !response.errors) {
                if (!$.isEmptyObject(response.customer_section)) {
                    _.each(response.customer_section, function (o, i) {
                            customerData.set(i, o);
                    });
                    $(document).trigger('customer-data-reload', [Object.keys(response.customer_section)]);
                }
            }
        },

        isLoggedIn: function () {
            return (this.customer() && this.customer().firstname);
        },

        isDistributorCompany: function () {
            if (customerData.get('is_distributor')()) {
                return customerData.get('is_distributor')().is_distributor;
            }
            return false;
        },
        /** Is login form enabled for current customer */
        isActive: function () {
            var customer = customerData.get('customer');
            return customer() == false;
        },
        /** Provide login action */
        login: function (loginForm) {
            customerData.set('submit-login', false);
            var loginData = {},
                formDataArray = $(loginForm).serializeArray();
            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if ($(loginForm).validation()
                && $(loginForm).validation('isValid')
            ) {
                this.isLoading(true);
                loginAction(loginData, null, false).done(function (response) {
                    if (response.errors) {
                    } else {
                        customerData.set('submit-login', true);
                    }
                });
            }
        },
        forgotpwd: function (theForm) {
            var loginData = {},
                formDataArray = $(theForm).serializeArray();
            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if ($(theForm).validation()
                && $(theForm).validation('isValid')
            ) {
                $(theForm).attr('action', this.forgotPasswordPostUrl);
                theForm.submit();
            }
        },
        callForgotPwdForm: function () {
            if ($('#forgotpwd-form').hasClass('no-display')) {
                $('#forgotpwd-form').removeClass('no-display');
            } else {
                $('#forgotpwd-form').addClass('no-display');
            }

            if ($('.minilogin-scr').data('forixScrollMini')) {
                $('.minilogin-scr').scrollMini('updateScroll');
            } else {
                $('.minilogin-scr').scrollMini();
            }

            return false;
        },
        closeForgotPwdForm: function () {
            $('#forgotpwd-form').addClass('no-display');

            if ($('.minilogin-scr').data('forixScrollMini')) {
                $('.minilogin-scr').scrollMini('updateScroll');
            } else {
                $('.minilogin-scr').scrollMini();
            }
            return false;
        },
        closeSidebar: function () {
            var mini_login = $('[data-block="mini_login"]');
            mini_login.on('click', '[data-action="close"]', function (event) {
                event.stopPropagation();
                mini_login.find('[data-role="dropdownDialog"]').dropdownDialog("close");
            });
            return true;
        }
    });
});
