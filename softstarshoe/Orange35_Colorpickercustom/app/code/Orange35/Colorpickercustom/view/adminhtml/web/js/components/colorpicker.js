/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'Magento_Ui/js/lib/view/utils/async',
    'mage/template',
    'uiRegistry',
    'prototype',
    'Magento_Ui/js/form/element/abstract',
    'jquery/colorpicker/js/colorpicker',
    'jquery/ui'
], function (_, jQuery, mageTemplate, rg, prototype, Abstract) {
    'use strict';

    /**
     *
     * @param element
     * @returns {string}
     */
    function setContrastYIQ(element) {
        var $element = jQuery(element);
        var rgb = $element.css('background-color');
        var colors = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
        var r = colors[1];
        var g = colors[2];
        var b = colors[3];
        var o = Math.round(((r * 299) + (g * 587) + (b * 114)) / 1000);
        $element.css('color',(o > 125) ? 'black' : 'white');
    }

    /**
     *
     * @param value
     * @param container
     */
    function initColorpicker(value, container) {
        var colorpicker = {
            initialize: function () {
                jQuery(container).on(
                    'click',
                    this.initColorpicker()
                );
                setContrastYIQ(container);
            },

            initColorpicker: function () {
                jQuery(container).ColorPicker({
                    onShow: function () {
                        jQuery(container).ColorPickerSetColor(jQuery(container).val());
                    },

                    onSubmit: function (hsb, hex, rgb, el) {
                        jQuery(el).ColorPickerHide();
                        jQuery(el).val('#' + hex).trigger('change');
                        jQuery(el).css('background-color', '#' + hex);
                        setContrastYIQ(el);
                    }
                });
            }
        };

        colorpicker.initialize();
    }

    return Abstract.extend({
        defaults: {
            fontColor: 'black'
        },

        initialize: function () {
            this._super()
                .configureColorpicker();
            return this;
        },

        configureColorpicker: function () {
            jQuery.async('#' + this.uid, function (elem) {
                initColorpicker(this.value(), elem);
            }.bind(this));

            return this;
        }
    });
});
