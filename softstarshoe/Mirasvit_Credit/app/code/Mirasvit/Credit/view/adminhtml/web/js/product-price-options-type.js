/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiRegistry',
    'mageUtils',
    'uiLayout',
    'Magento_Ui/js/form/form',
    'Magento_Ui/js/form/element/ui-select',
    'mage/translate'
], function ($, _, registry, utils, layout, UiForm, UiSelect) {
    'use strict';

    return UiSelect.extend({
        defaults: {
            previousGroup: null,
            groupsConfig: {},
            valuesMap: {},
            indexesMap: {},
            filterPlaceholder: 'ns = ${ $.ns }, parentScope = ${ $.parentScope }'
        },

        /**
         * Initialize component.
         * @returns {Element}
         */
        initialize: function () {
            return this
                ._super()
                .initMapping()
                .updateComponents(this.initialValue, true);
        },

        /**
         * Create additional mappings.
         *
         * @returns {Element}
         */
        initMapping: function () {
            _.each(this.groupsConfig, function (groupData, group) {
                _.each(groupData.values, function (value) {
                    this.valuesMap[value] = group;
                }, this);

                _.each(groupData.indexes, function (index) {
                    if (!this.indexesMap[index]) {
                        this.indexesMap[index] = [];
                    }

                    this.indexesMap[index].push(group);
                }, this);
            }, this);

            return this;
        },

        /**
         * Callback that fires when 'value' property is updated.
         *
         * @param {String} currentValue
         * @returns {*}
         */
        onUpdate: function (currentValue) {
            this.updateComponents(currentValue);

            return this._super();
        },

        /**
         * Show, hide or clear components based on the current type value.
         *
         * @param {String} currentValue
         * @param {Boolean} isInitialization
         * @returns {Element}
         */
        updateComponents: function (currentValue, isInitialization) {
            var currentGroup = this.valuesMap[currentValue];
            var addFirstRow  = true;

            if (currentGroup !== this.previousGroup) {
                _.each(this.indexesMap, function (groups, index) {
                    var template = this.filterPlaceholder + ', index = ' + index,
                        visible = groups.indexOf(currentGroup) !== -1,
                        hideDeleteButton = currentGroup != 'fixed',
                        component;

                    switch (index) {
                        case 'container_type_static':
                        case 'container_type_fields_static':
                        case 'container_price_grid':
                        case 'container_single_price':
                        case 'container_range_price':
                        case 'values':
                            template = 'ns=' + this.ns +
                                ', dataScope=' + this.parentScope +
                                ', index=' + index;
                            break;
                    }

                    /*eslint-disable max-depth */
                    if (isInitialization) {
                        registry.async(template)(
                            function (currentComponent) {
                                if (_.isFunction(currentComponent.visible)) {
                                    currentComponent.visible(visible);
                                }
                            }
                        );
                    } else {
                        component = registry.get(template);

                        if (component) {
                            if (_.isFunction(component.visible)) {
                                component.visible(visible);
                            }
                            /*eslint-disable max-depth */
                            if (_.isFunction(component.clear)) {
                                component.clear();
                            }

                            if (currentGroup == 'range' && typeof component.elems == 'function') {
                                _.each(component.elems(), function (elem) {
                                    if (elem.index == 'price') {
                                        elem.notice = $.mage.__('Price will be paid for 1 credit');
                                    }
                                });
                            }

                            if (addFirstRow && index == "button_add" && currentGroup == 'fixed') {
                                var action = component.actions;
                                template = 'ns=' + this.ns + ', index=container_price_grid';
                                component = registry.get(template);
                                component.processingAddChild(action, 0);
                                component.hideDeleteButton = hideDeleteButton;

                                addFirstRow = false;
                            }
                        }
                    }
                }, this);

                this.previousGroup = currentGroup;
            }

            return this;
        },
    });
});
