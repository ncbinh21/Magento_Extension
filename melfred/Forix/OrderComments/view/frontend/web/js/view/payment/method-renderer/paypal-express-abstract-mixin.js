/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery'
], function ($) {
    'use strict';

    var mixin = {

        /**
         *
         * @param {Column} elem
         */
        getData: function() {
            var parent = this._super(),
                additionalData = null;

            if(additionalData === null){
                additionalData = {};
            }
            if ($('.payment-method._active').find("textarea[name=comment-code]").val()){
                additionalData.comments = $('.payment-method._active').find("textarea[name=comment-code]").val();
            }
            if ($('.payment-method._active').find("input[name=po-number]").val()){
                additionalData.ponumber = $('.payment-method._active').find("input[name=po-number]").val();
            }
            return $.extend(true, parent, {'additional_data': additionalData});
        }
    };

    return function (target) { // target == Result that Magento_Ui/.../columns returns.
        return target.extend(mixin); // new result that all other modules receive
    };
 });