/**
 * Created by Bruce on 2/3/16.
 */
define([
    "jquery",
    'jquery/ui'
], function ($) {
    "use strict";
    $.widget('mage.reCaptcha', {
        options: {
            holderClass: "recaptcha-holder",            
            site_key: '',
            secret_key: ''
        },
        wid: [],
        holder: null,
        _init: function () {
            var _this = this;            
            /*this.siteKey = options.site_key;
            this.secretKey = options.secret_key;*/
            _this.holder = _this.options.wrap.find("." + _this.options.holderClass);
            _this.grecaptcha = grecaptcha;
            _this.renderCaptcha();
        },
        /**
         * Generate reCAPTCHA for all holder on page load
         */
        run : function () {
            var _this = this;
            $.each($('form').find("." + _this.options.holderClass), function (i, holder) {
                _this.renderCaptcha(holder);
            });
        },

        /**
         * Generate single reCAPTCHA
         */
        renderCaptcha : function () {
            var _this = this;
            var form = this.holder.parents("form");
            var formId = form.attr("id");
            var idContainer = "recaptcha-" + formId;
            if (this.grecaptcha == undefined) {
                _this.grecaptcha = grecaptcha;
            }
            this.holder.html("<div id='" + idContainer + "'></div>");
            _this.wid[formId] = _this.grecaptcha.render(idContainer, {
                'sitekey': _this.options.site_key,
                'callback': function () {
                    form.find('.required-captcha input[type=checkbox]').attr("checked", "checked");
                },
                'expired-callback': function () {
                    form.find('.required-captcha input[type=checkbox]').removeAttr("checked");
                }
            })
        }
    });
    return $.mage.reCaptcha;
});