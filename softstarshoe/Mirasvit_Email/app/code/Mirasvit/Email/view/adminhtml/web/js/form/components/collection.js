/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'mageUtils',
    'uiRegistry',
    'uiLayout',
    'Magento_Ui/js/form/components/collection'
], function (_, utils, registry, layout, Collection) {
    'use strict';

    var childTemplate = {
        parent: '${ $.$data.name }',
        name: '${ $.$data.childIndex }',
        dataScope: '${ $.name }',
        nodeTemplate: '${ $.$data.name }.${ $.$data.itemTemplate }'
    };

    return Collection.extend({

        /**
         * Creates new item of collection, based on incoming 'index'.
         * If not passed creates one with 'new_' prefix.
         *
         * @param {String|Object} [index] - Index of a child.
         * @returns {Collection} Chainable.
         */
        addChild: function (index) {
            this.childIndex = !_.isString(index) ?
                'new_' + this.lastIndex++ :
                index;

            var template = utils.template(childTemplate, this);

            layout([template]);

            if (!_.isString(index)) {
                var childName = template.parent + '.' + template.name;
                registry._addRequest(childName, function(component) {
                    // Initialize child component
                    component._elems.forEach(function (item) {
                        component.initElement(item)
                    });
                });
            }

            return this;
        }
    });
});
