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

        $('product_id').value = Object.keys(JSON.parse(Object.toJSON(categoryProducts)))[0];

        gridJsObject.reloadParams = {
            'selected_products[]': categoryProducts.keys()
        };
        /**
         * Register Category Product
         *a
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerCategoryProduct(grid, element, checked) {
            if (checked) {
                categoryProducts.unset('on');
                categoryProducts.set(element.value, element.value);
                $('product_id').value = element.value;

                grid.rows.each(function (ele) {
                    if(ele.firstElementChild.firstElementChild.firstElementChild.defaultValue  != element.value) {
                        ele.firstElementChild.firstElementChild.firstElementChild.checked = false
                    }
                });
            } else {
                categoryProducts.unset(element.value);
                $('product_id').value = '';
            }
            grid.reloadParams = {
                'selected_products[]': categoryProducts.keys()
            };
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

        /**
         * Initialize category product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function selectedProductRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0];
        }
        gridJsObject.rowClickCallback = categoryProductRowClick;
        gridJsObject.initRowCallback = selectedProductRowInit;
        gridJsObject.checkboxCheckCallback = registerCategoryProduct;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                selectedProductRowInit(gridJsObject, row);
            });
        }
    };
});
