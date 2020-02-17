/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 14/06/2018
 * Time: 13:59
 */
define([
    'jquery',
    'underscore',
    'ko',
    'mage/storage',
    './url-builder',
    'Magento_Catalog/js/price-utils',
    'mage/storage',
    'jquery/jquery-storageapi'
], function ($, _, ko, mageStorage, urlBuilder, priceutils) {
    'use strict';
    var storage,
        options,
        storageInvalidation,
        dataProvider,
        buffer,
        wizardData;
    //TODO: remove global change, in this case made for initNamespaceStorage
    $.cookieStorage.setConf({
        path: '/'
    });

    storage = $.initNamespaceStorage('wizard-cache-storage').localStorage;
    storageInvalidation = $.initNamespaceStorage('wizard-cache-storage-section-invalidation').localStorage;

    dataProvider = {
        /**
         * @param {String} productSku
         * @return {*}
         */
        getProductFromServer: function (productSku) {
            var apiUrl = urlBuilder.createUrl(options.getProductUrl, {'sku': productSku, 'storeId': options.storeId});
            return mageStorage.get(apiUrl);
        }
    };

    buffer = {
        data: {},

        /**
         * @param {String} sectionName
         */
        bind: function (sectionName) {
            this.data[sectionName] = ko.observable({});
        },

        /**
         * @param {String} sectionName
         * @return {Object}
         */
        get: function (sectionName) {
            if (!this.data[sectionName]) {
                this.bind(sectionName);
            }

            return this.data[sectionName];
        },

        /**
         * @return {Array}
         */
        keys: function () {
            return _.keys(this.data);
        },

        /**
         * @param {String} sectionName
         * @param {Object} sectionData
         */
        notify: function (sectionName, sectionData) {
            if (!this.data[sectionName]) {
                this.bind(sectionName);
            }
            this.data[sectionName](sectionData);
        },

        /**
         * @param {Object} sections
         */
        update: function (sections) {
            _.each(sections, function (sectionData, sectionName) {
                storage.set(sectionName, sectionData);
                storageInvalidation.remove(sectionName);
                buffer.notify(sectionName, sectionData);
            });
        },

        /**
         * @param {Object} sections
         */
        remove: function (sections) {
            _.each(sections, function (sectionName) {
                storage.remove(sectionName);
            });
        }
    };


    wizardData = {
        /**
         * @param {String} sectionName
         * @return {*}
         */
        get: function (sectionName) {
            return buffer.get(sectionName);
        },
        keys: buffer.keys,
        isLoading: ko.observable(false),
        optionKeys: ko.observableArray([]),
        xhrLoadProduct: null,
        getProductData: function (productSku, itemSet, isLoading) {
            isLoading(true);
            this.xhrLoadProduct = dataProvider.getProductFromServer(productSku).done(function (response) {
                isLoading(false);
                $(document).trigger('product-wizard-items', [response, itemSet]);
            }).always(function () {
                isLoading(false);
            }).fail(function () {
                isLoading(false);
            });
            return this.xhrLoadProduct;
        },
        abortAjaxRequestProduct: function () {
            if (null !== this.xhrLoadProduct) {
                this.xhrLoadProduct.abort();
                this.xhrLoadProduct = null;
            }
        },
        getWizardUrl: function () {
            return options.wizard_url;
        },
        getPageTitle: function () {
            return options.page_title;
        },
        getRequiredItemSets: function () {
            return options.required_item_sets;
        },
        getNotificationMessage: function () {
            return options.notify_message;
        },
        buildDependData: function (dependSectionKey, data) {
            var _Object = [];
            if (data) {
                data.forEach(function (e, i) {
                    var groupKey = e.split('_')[0];
                    if (!_Object[groupKey]) {
                        _Object[groupKey] = {
                            dependData: [{
                                index: e
                            }],
                            hide: false
                        };
                    } else {
                        _Object[groupKey].dependData.push({
                            index: e
                        });
                    }
                });
            }
            this.set(dependSectionKey, _Object);
        },

        buildBestOnData: function (bestOnSectionKey, data) {
            var _Object = [];
            if (data) {
                data.forEach(function (e, i) {
                    var groupKey = e.split('_')[0];
                    if (!_Object[groupKey]) {
                        _Object[groupKey] = {
                            bestData: [{
                                index: e
                            }],
                            hide: true
                        };
                    } else {
                        _Object[groupKey].bestData.push({
                            index: e
                        });
                    }
                });
                if (data.length) {
                    $("." + bestOnSectionKey).prop('disabled', true).addClass('not-recommended no-display');
                }
            }
            this.set(bestOnSectionKey, _Object);
        },

        checkDepend: function (dependSectionKey, data) {
            if (data) {
                var dependData = this.get(dependSectionKey)(), hasHide = false, hasDepend = false,
                    groupKey = data.split('_')[0];
                if (dependData[groupKey]) {
                    dependData.forEach(function (e, _groupKey) {
                        if (_groupKey === parseInt(groupKey)) {
                            /* Neu chung 1 group thi kiem tra xem co chon best on hay ko (Or condition)*/
                            e.dependData.forEach(function (value) {
                                if (value.index === data) {
                                    hasDepend = true;
                                    e.hide = false;
                                    return false;
                                } else {
                                    e.hide = true;
                                }
                            });

                            var checkboxGroup = $('.display .group-input-item-' + _groupKey);
                            if (checkboxGroup.length) {
                                checkboxGroup.each(function () {
                                    var el = this;
                                    e.bestData.forEach(function (value) {
                                        if (value.index === el.dataset.option) {
                                            hasDepend = true;
                                            e.hide = false;
                                            return false;
                                        }
                                    });
                                });
                            }

                            if (hasDepend) {
                                e.hide = false;
                            }
                        } else {
                            /*
                            * e.hide check tai vi tri khac input khac, neu toan bo bestOnData hide == false thi hien thi
                            * (And Condition)
                            * */
                            if (true === e.hide) {
                                hasHide = true;
                            }
                        }
                    });
                }
                var optionKey = dependSectionKey.replace('depend_', '');
                if (hasHide || hasDepend) {
                    $('[data-option="' + optionKey + '"]').prop('disabled', true)
                        .addClass('not-recommended no-display')
                        .removeClass('display');
                } else {
                    $('[data-option="' + optionKey + '"]').prop('disabled', false)
                        .attr('disabled', false)
                        .removeClass('not-recommended no-display')
                        .addClass('display');
                }
                $(document).trigger('product-wizard-items-depended', [optionKey]);
            }
        },
        checkBestOn: function (bestOnSectionKey, data) {
            var hasHide = false, hasBestOn = false, bestOnData = this.get(bestOnSectionKey)();

            bestOnData.forEach(function (e, _groupKey) {
                var checkboxGroup = $('.group-input-item-' + _groupKey);
                if(data){
                    var groupKey = data.split('_')[0];
                    if (_groupKey === parseInt(groupKey)) {
                        /* Neu chung 1 group thi kiem tra xem co chon best on hay ko (Or condition)*/
                        e.bestData.forEach(function (value) {
                            if (value.index === data) {
                                hasBestOn = true;
                                e.hide = false;
                                return false;
                            } else {
                                e.hide = true;
                            }
                        });
                        if (checkboxGroup.length) {
                            checkboxGroup.each(function () {
                                var el = this;
                                e.bestData.forEach(function (value) {
                                    if (value.index === el.dataset.option) {
                                        hasBestOn = true;
                                        e.hide = false;
                                        return false;
                                    }
                                });
                            });
                        }
                        if (hasBestOn) {
                            e.hide = false;
                        }
                        return true;
                    }
                }
                /*
                * e.hide check tai vi tri khac input khac, neu toan bo bestOnData hide == false thi hien thi
                * (And Condition)
                * */
                if (true === e.hide) {
                    hasHide = true;
                }

                // Kiểm tra element ở vị trí khác có required hay không
                if (checkboxGroup.length && !checkboxGroup.prop('required')) {
                    if (checkboxGroup.val()) {
                        if (true === e.hide) {
                            hasHide = true;
                        }
                    } else {
                        hasBestOn = true;
                        hasHide = false;
                        e.hide = false;
                        return false;
                    }
                }
            });
            var optionKey = bestOnSectionKey.replace('best_', '');
            if (hasHide || !hasBestOn) {
                $('[data-option="' + optionKey + '"]')
                    .prop('disabled', true)
                    .addClass('not-recommended no-display')
                    .removeClass('display');
            } else if (hasBestOn) {
                $('[data-option="' + optionKey + '"]')
                    .prop('disabled', false)
                    .attr('disabled', false)
                    .removeClass('not-recommended no-display')
                    .addClass('display');
            }
            $(document).trigger('product-wizard-items-depended', [optionKey]);
        },
        /**
         * Format shipping price.
         * @returns {String}
         */
        getFormattedPrice: function (price) {
            return priceutils.formatPrice(price, options.priceFormat);
        },

        addOptionKeys: function (key) {
            this.optionKeys.remove(key);
            this.optionKeys.push(key);
            $('.show-all').show();
            $('.not-recommended').addClass('no-display');
            $(document).trigger('product-wizard-items-depended', [key]);
        },
        /**
         * @param {String} sectionName
         * @param {Object} sectionData
         */
        set: function (sectionName, sectionData) {
            var data = {};

            data[sectionName] = sectionData;
            buffer.update(data);
        },

        _getUrlPath: function (tabIndex) {
            if (tabIndex >= 0) {
                var part = $('.wizard-form-main-' + tabIndex).serialize(), prevPart = this._getUrlPath(tabIndex - 1);
                return prevPart ? prevPart + '&' + part : part;
            }
            return '';
        },

        buildSharedUrlPath: function (tabIndex) {
            if (this.get('change-url')()) {
                if (undefined !== tabIndex && parseInt(tabIndex) >= 0) {
                    /* Get form data of current step */
                    var fullPath = this._getUrlPath(tabIndex);
                    /* Push parsed url to navigate bar*/
                    history.pushState({}, this.getPageTitle(), this.getWizardUrl() + '?step=' + tabIndex + '&' + fullPath);
                }
            }
            return false;
        },
        /**
         * @param {Object} settings
         * @constructor
         */
        'Forix_ProductWizard/js/model/wizard-data': function (settings) {
            wizardData.set('change-url', false);
            options = settings;
        }
    };
    return wizardData;
});