/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedProducts = config.selectedProducts,
            categoryProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $(config.target_element_id).value = Object.toJSON(categoryProducts);

        if(!gridJsObject.reloadParams){
            gridJsObject.reloadParams = {};
        }
        gridJsObject.reloadParams[config.target_element_name] = categoryProducts.keys();

        /**
         * Register Category Product
         *a
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerCategoryProduct(grid, element, checked) {
            if (checked) {
                categoryProducts.set(element.value, element.value);
            } else {
                categoryProducts.unset(element.value);
            }
            $(config.target_element_id).value = Object.toJSON(categoryProducts);
            grid.reloadParams[config.target_element_name] = categoryProducts.keys();
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function categoryProductRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        gridJsObject.rowClickCallback = categoryProductRowClick;
        gridJsObject.checkboxCheckCallback = registerCategoryProduct;
    };
});
