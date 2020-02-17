/**
 * Created by Hidro Le.
 * Sử dụng để thay đổi title của tab trong quá trình xử lý có trường hợp skip tab.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 15/06/2018
 * Time: 15:52
 */
define([
    'jquery',
    'ko',
    'mage/translate'
], function ($, ko, $t) {
    "use strict";
    return {
        titleCodes: ['bits', 'default'],
        titleIndex: ko.observable(-1),
        tabParent: '',
        isNumeric: function (n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        },
        setIndex: function (index) {
            var _index = 0;
            if (!this.isNumeric(index)) {
                for (var i = 0, l = this.titleCodes.length; i < l; i++) {
                    if (this.titleCodes[i] === index) {
                        _index = i;
                        break;
                    }
                }
            } else {
                _index = index;
            }
            return this.titleIndex(_index);
        },
        init: function (tabParent) {
            var self = this;
            this.tabParent = $(tabParent);
            this.titleIndex.subscribe(function (newIndex) {
                var titleIndex = self.titleCodes[newIndex];
                self.tabParent.find('span[class*="step"]').hide();
                self.tabParent.find('span.step-' + titleIndex).show();
            });
        }
    }
});