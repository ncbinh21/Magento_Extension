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

            if ($('.payment-method._active').find("textarea[name=comment-code]").val()){
                if(additionalData === null){
                    additionalData = {};
                }
                additionalData.comments = $('.payment-method._active').find("textarea[name=comment-code]").val();
            }
            return $.extend(true, parent, {'additional_data': additionalData});
        }
    };

    return function (target) { // target == Result that Magento_Ui/.../columns returns.
        return target.extend(mixin); // new result that all other modules receive
    };
 });