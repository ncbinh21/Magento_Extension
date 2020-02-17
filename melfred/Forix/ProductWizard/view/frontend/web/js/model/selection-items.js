/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 22/06/2018
 * Time: 00:52
 */

/**
 * This file us only for step product check box
 */
define([
    'uiComponent',
    'jquery',
    'ko',
    'Forix_ProductWizard/js/model/wizard-data',
    'Forix_ProductWizard/js/action/set-value',
    "configuratorAddToCart",
    'mage/translate',
    'jquery/jquery.cookie'
], function (Component, jquery, ko, wizardData, setValue, configuratorAddToCart) {
    var productToCheck = window.productWizard.productCheck;
    var isNumeric = function (n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }, findProduct = function (productId) {
        var product = undefined;
        productToCheck.forEach(function (element) {
            var _product = element.find(function (e) {
                return e.id == productId;
            });
            if (_product) {
                product = _product;
                return false;
            }
        });
        return product;
    };
    return Component.extend({
        // defaults: {
        //     addToCartTitle: 'Add To Cart'
        // },
        addToCartTitle: ko.observable(),
        isLoading: wizardData.isLoading,
        selectedProduct: ko.observableArray([]),
        selectedPrevProductList: ko.observableArray([]),
        optionDependChanged: ko.observable(false),
        addedItemSets: [],
        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super();
            var _self = this;
            this.selectDefault = function (item) {
                _self.selectedProduct.push(item);
            };
            var check_cookie = jquery.cookie('domestic-cookie');
            if(check_cookie == 1){
                _self.addToCartTitle('Add To Cart');
            } else {
                _self.addToCartTitle('Add To Quote');
            }
            this.selectedProduct.subscribe(function (items) {
                //Check Attribute Set. if missing show noti message
                items.forEach(function (item, i) {
                    var productEntityId = item.value;
                    var product = null;
                    if (isNumeric(productEntityId)) {
                        product = findProduct(productEntityId);
                        var inputCheck = jquery('input[name="se_pr[' + product.short_key + ']"]');
                        if(inputCheck.length){
                            setValue(inputCheck[0]);
                        }
                    }else if(Object.keys(productEntityId).indexOf('id') !== -1){
                        product = productEntityId;
                    }
                    if (item.status === "deleted") {
                        jquery('[data-product="'+ productEntityId +'"]').addClass('un-checked');
                        var index = _self.addedItemSets.indexOf(product.item_set), isMissing = false;
                        if( -1 < index) {
                            _self.addedItemSets.splice(index, 1);
                        }
                        wizardData.getRequiredItemSets().forEach(function (value) {
                            if(value.identifier == product.item_set) { //Don't need to compare variable type
                                if (!_self.addedItemSets.includes(product.item_set)) {
                                    isMissing = true;
                                }
                                return false;
                            }
                        });
                        if (isMissing) {
                            jquery('.attr-item-warning-' + product.id).removeClass('no-display');
                        }
                    } else {
                        if(product) {
                            _self.addedItemSets.push(product.item_set);
                            jquery('[data-product="' + productEntityId + '"]').removeClass('un-checked');
                            jquery('.attr-warning-' + product.item_set).addClass('no-display');
                        }
                    }
                });
            }, null, "arrayChange");


            /**
             * get Selected product in list
             * @type {*|dependentObservable}
             */
            this.selectedProductLength = ko.computed(function () {
                /* Selected Product Check Product Attribute Set in Here */
                /* Change Url */
                if (_self.selectedPrevProductList().length) {
                    wizardData.buildSharedUrlPath(2);
                }
                return this.selectedProduct().length;
            }.bind(this));


            /**
             * Get List Product in step 3
             * @type {*|dependentObservable}
             */
            this.getProductCount = ko.computed(function () {
                return (this.optionDependChanged() || true) ? jquery('#shopping-cart-table .product-wizard.card.item.display').length + 1 : 0;
            }.bind(this));


            /**
             * Return Summary Final Price
             * @type {*|dependentObservable}
             */
            this.getFinalPrice = ko.computed(function () {
                if (this.selectedProduct().length) {

                    var finalPrice = 0;
                    jquery.each(this.selectedProduct(), function (i, productId) {
                        var product = productId;
                        if (isNumeric(productId)) {
                            product = findProduct(productId);
                        }
                        if (product) {
                            finalPrice += product.price_info.final_price;
                        }

                    });
                    return finalPrice;
                }
                return 0;
            }.bind(this));

            jquery(document).on('product-wizard-items-depended', function (e, optionKey) {
                _self.optionDependChanged(!_self.optionDependChanged());
                var _input = jquery('#shopping-cart-table .product-wizard.card.item.display input[data-option="' + optionKey + '"]');
                if(_input.length && _input[0].type == 'checkbox'){
                    if(!_self.selectedProduct.indexOf(_input[0].dataset.value)) {
                        _self.selectedProduct.push(_input[0].dataset.value);
                    }
                }
            });
            jquery(document).on('product-wizard-items', function (event, items, itemSet) {
                items.forEach(function (item, i) {
                    item.item_set = itemSet.toLowerCase();
                    _self.selectedPrevProductList.removeAll();
                    _self.selectedProduct.removeAll();
                    if (!jquery('#shopping-cart-table .product-wizard.card.item.display input[data-checked="checked"]').length) {
                        jquery('#shopping-cart-table .product-wizard.card.item.display input[data-type="selected"]').each(function () {
                            _self.selectedProduct.push(this.value);
                        });
                    } else {
                        /* If have select default then skip select all */
                        jquery('#shopping-cart-table .product-wizard.card.item.display input[data-checked="checked"]').each(function () {
                            _self.selectedProduct.push(this.value);
                            this.dataset.checked = "";
                        });
                    }
                    _self.selectedPrevProductList.push(item);
                    wizardData.buildSharedUrlPath(2);
                });
            });

            return this;
        },
        /**
         * check domestic
         */
        isDomestic: function () {
            var check_cookie = jquery.cookie('domestic-cookie');
            var result = false;
            if(check_cookie == 1){
                result = true;
            }
            return result;
        },

        setGroupItemValue: function (key, value) {
            wizardData.set(key, value);
        },

        triggerClick: function (element) {
            setValue(element);
        },

        // check list price, your price is same
        isShowListPrice: function () {
            if (this.getSummaryPrice() != this.getFinalPrice()) {
                return true;
            }
            return false;
        },

        addSelectedToCart: function (obj) {
            var _self = this;
            var dataToSave = jquery.map(_self.selectedProduct(), function (productId) {
                if(typeof productId == 'object' && productId.id) {
                    return productId.id;
                }
                if (productId) {
                    return productId;
                }
                return undefined;
            });
            if (dataToSave) {
                _self.isLoading(true);
                (new configuratorAddToCart()).submitForm(window.productWizard.addToCartAction, {
                    'form_key': window.productWizard.formKey,
                    'selected_product': dataToSave
                }, _self.isLoading);
            }
            return false;
        },
        /**
         * Public
         */
        initialize: function () {
            this._super();
            var _self = this;

            /**
             * Return Price without rules
             * @type {*|dependentObservable}
             */
            this.getSummaryPrice = ko.computed(function () {
                if (this.selectedProduct().length) {
                    var maxPrice = 0;
                    jquery.each(this.selectedProduct(), function (i, productId) {
                        var product = productId;
                        if (isNumeric(productId)) {
                            product = findProduct(productId);
                        }
                        if (product) {
                            maxPrice += product.price_info.regular_price
                        }
                    });
                    return maxPrice;
                }
                return 0;
            }.bind(this));

            /**
             * Get All Selected Option in step 1
             * @type {*|dependentObservable}
             */
            this.getSelectedOptions = ko.computed(function () {
                var keys = wizardData.optionKeys();
                var options = ko.observableArray([]);
                keys.forEach(function (key) {
                    if (wizardData.get(key)().title) {
                        options.push(wizardData.get(key)());
                    }
                });
                return options;
            }.bind(this));

            return this;
        },

        /**
         * Format shipping price.
         * @returns {String}
         */
        getFormattedPrice: wizardData.getFormattedPrice
    });
});