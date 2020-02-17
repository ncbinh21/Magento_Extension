/**
 * @author    Amasty Team
 * @copyright Copyright (c) Amasty Ltd. ( http://www.amasty.com/ )
 * @package   Amasty_Shopby
 */
define([
    "jquery",
    "jquery/ui",
    "mage/tooltip",
    'mage/validation',
    'mage/translate',
    "Amasty_Shopby/js/jquery.ui.touch-punch.min",
    'Amasty_ShopbyBase/js/chosen/chosen.jquery'
], function ($) {
    'use strict';

    $.widget('mage.amShopbyFilterAbstract', {
        filters: {},
        options: {
            isAjax: 0,
            collectFilters: 0
        },
        getFilter: function () {
            var filter = {
                'code': this.element.attr('amshopby-filter-code'),
                'value': this.element.attr('amshopby-filter-value')
            };
            return filter;
        },
        apply: function (link, clearFilter) {
            try {
                if ($.mage.amShopbyAjax) {
                    $.mage.amShopbyAjax.prototype.response = null;
                }

                this.options.isAjax = $.mage.amShopbyAjax != undefined;
                if (!this.options.collectFilters && this.options.isAjax == true) {
                    link = clearFilter ? link : null;
                    this.prepareTriggerAjax(this.element, link, clearFilter);
                } else {
                    if (this.options.collectFilters === 1) {
                        this.prepareTriggerAjax(this.element);
                    } else {
                        window.location = link;
                    }
                }
            } catch(e) {
                window.location = link;
            }
        },
        prepareTriggerAjax: function(element, clearUrl, clearFilter, isSorting) {
            var forms = $('form[data-amshopby-filter]');
            if (typeof this.element !== 'undefined' && clearFilter) {
                var attributeName = this.element.closest(".filter-options-content").find('form').data('amshopby-filter');
                var excludedFormSelector = ((this.element.closest("div.sidebar").length == 0)
                    ? 'div.catalog-topnav' : 'div.sidebar') + ' form[data-amshopby-filter=' + attributeName +']';
                forms = forms.not(excludedFormSelector);
            }

            var existFields = [];
            var isIE = navigator.userAgent.indexOf("MSIE ") > -1 || navigator.userAgent.indexOf("Trident/") > -1;
            forms.each(function (index, item) {

                if ($(item).closest('[class*="am-filter-items"]').length) {
                    var className = '';
                    if (isIE) {
                        className = $(item).closest('[class*="am-filter-items"]')[0].className;
                    } else {
                        className = item.closest('[class*="am-filter-items"]').className;
                    }
                    var startPos = className.indexOf('am-filter-items'),
                        endPos = className.indexOf(' ', startPos + 1) == -1 ? 100 : className.indexOf(' ', startPos + 1),
                        filterClass = className.substring(startPos, endPos);

                    if (existFields[filterClass]) {
                        forms[index] = '';
                    } else {
                        existFields[filterClass] = true;
                    }
                }
            });

            var data = this.normalizeData(forms.serializeArray(), isSorting);
            data.clearUrl = data.clearUrl ? data.clearUrl : clearUrl;
            element = element ? element : document;
            $(element).trigger('amshopby:submit_filters', {data: data, clearFilter: clearFilter, isSorting: isSorting});
            return data;
        },

        normalizeData: function(data, isSorting) {
            var normalizedData = [],
                clearUrl,
                self = this;
            _.each(data, function(item) {
                if (item.value.trim() != '' && item.value != '-1') {
                    var isNormalizeItem = _.find(normalizedData, function (normalizeItem) {
                        return normalizeItem.name === item.name && normalizeItem.value === item.value
                            || item.name == 'amshopby[price][]' && normalizeItem.name === item.name;
                    });

                    if (isSorting && item.name == 'amshopby[price][]') {
                        isNormalizeItem = true;
                    }

                    if (!isNormalizeItem) {
                        item.value = item.value.replace(/[ \r\n]/g, '');
                        normalizedData.push(item);
                        if (window.amShopbyIsCategorySingleSelect && item.name === 'amshopby[cat][]') {
                            clearUrl = $('*[data-amshopby-filter-request-var="cat"] *[value="' + item.value + '"]')
                                .parent().attr('href');
                        }
                    }
                }
            });

            normalizedData = this.groupDataByName(normalizedData);
            normalizedData.clearUrl = clearUrl;
            return normalizedData;
        },

        groupDataByName: function (formData, fn) {
            var hash = Object.create(null);
            return formData.reduce(function (result, currentValue) {
                if (!hash[currentValue['name']]) {
                    hash[currentValue['name']] = {};
                    hash[currentValue['name']]['name'] = currentValue['name'];
                    result.push(hash[currentValue['name']]);
                }

                if (hash[currentValue['name']].value) {
                    hash[currentValue['name']].value += ',' + currentValue.value;
                } else {
                    hash[currentValue['name']].value = currentValue.value;
                }

                return result;
            }, []);
        },

        getSignsCount: function (step, isPrice) {
            if (step < 1 && step > 0) {
                return step.toString().length - step.toString().indexOf(".") - 1;
            }

            return 0;
        },
        getFloatNumber: function (size) {
            if (!size) {
                size = 2;
            }

            return 1 / parseInt(this.buildNumber(size));
        },
        buildNumber: function (size) {
            var str = "1";
            for (var i = 1; i <= size; i++) {
                str += "0";
            }

            return str;
        },
        getFixed: function (value, isPrice) {
            var fixed = 2;
            if (value) {
                fixed = this.getSignsCount(this.options.step, isPrice);
            }

            return fixed;
        },
        isPrice: function () {
            return (typeof this.options.code != 'undefined' && this.options.code == 'price');
        },
        renderShowButton: function (event, element) {
            if ($.mage.amShopbyApplyFilters) {
                $.mage.amShopbyApplyFilters.prototype.renderShowButton(event, element);
            }
        },
        fixPriceForCurrency: function (price, type, rate) {
            if (rate != 1) {
                /* (0.01 / 2) - 0.0001 */
                var delta = 0.0049;
                if (type == 'from') {
                    price -= delta;
                } else {
                    price += delta;
                }
                price /= rate;
            }

            return price;
        }
    });

    $.widget('mage.amShopbyFilterItemDefault', $.mage.amShopbyFilterAbstract, {
        options: {},
        _create: function () {
            var self = this;
            $(function () {
                var link = self.element,
                    parent = link.parents('.item'),
                    checkbox = link.find('input[type=checkbox], input[type=radio]');

                if (link.find('[name="amshopby[cat][]"]').length && parent) {
                    parent = $(null);//get only current category item
                }

                var params = {
                    parent: parent,
                    checkbox: checkbox,
                    link: link
                };

                checkbox.bind('click', params, function (e) {
                    var checkbox = $(this);
                    var link = e.data.link;

                    setTimeout(function () {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        checkbox.trigger('change');
                        checkbox.trigger('sync');
                        $.mage.amShopbyFilterAbstract.prototype.renderShowButton(e, link);
                        self.apply(link.prop('href'));
                    }, 10);
                    e.stopPropagation();
                    e.preventDefault();
                });

                link.bind('click', params, function (e) {
                    var element = e.data.checkbox;
                    element.prop('checked', !element.prop('checked'));
                    element.trigger('change');
                    element.trigger('sync');
                    $.mage.amShopbyFilterAbstract.prototype.renderShowButton(e, element);
                    self.apply(e.data.link.prop('href'));
                    
                    e.stopPropagation();
                    e.preventDefault();
                });

                parent.bind('click', params, function (e) {
                    var element = e.data.checkbox;
                    var link = e.data.link;
                    element.prop('checked', !element.prop('checked'));
                    element.trigger('change');
                    element.trigger('sync');
                    $.mage.amShopbyFilterAbstract.prototype.renderShowButton(e, element);
                    self.apply(link.prop('href'));

                    e.stopPropagation();
                    e.preventDefault();
                    return false;
                });

                checkbox.on('change', function (e) {
                    self.markAsSelected($(this));
                });

                checkbox.on('amshopby:sync_change', function (e) {
                    self.markAsSelected($(this));
                });

                self.markAsSelected(checkbox);
            })
        },
        markAsSelected: function (checkbox) {
            checkbox.closest('form').find('a').each(function () {
                if (!$(this).find('input').prop("checked")) {
                    $(this).removeClass('am_shopby_link_selected');
                } else {
                    $(this).addClass('am_shopby_link_selected');
                }
            });
        }
    });

    $.widget('mage.amShopbyFilterCategoryLabelsFolding', $.mage.amShopbyFilterAbstract, {
        options: {},
        _create: function () {
            var self = this;
            $(function () {

                var link = self.element;
                var parent = $(link.parents('.item')[0]);
                var checkbox = link.find('input[type=checkbox], input[type=radio]');

                var params = {
                    parent: parent,
                    checkbox: checkbox,
                    link: link
                };

                checkbox.bind('click', params, function (e) {
                    var link = e.data.link;
                    $.mage.amShopbyFilterAbstract.prototype.renderShowButton(e, $(e.target));
                    self.apply(link.prop('href'));
                    self.toggleCheckParentAndChildrenCheckboxes(e.data.parent, e.data.checkbox.prop('checked'));
                    e.stopPropagation();
                });

                link.bind('click', params, function (e) {
                    var element = e.data.checkbox;
                    element.prop('checked', !element.prop('checked'));
                    self.toggleCheckParentAndChildrenCheckboxes(e.data.parent, element.prop('checked'));
                    element.trigger('change');
                    element.trigger('sync');
                    $.mage.amShopbyFilterAbstract.prototype.renderShowButton(e, element);
                    self.apply(e.data.link.prop('href'));
                    e.stopPropagation();
                    e.preventDefault();
                });

                checkbox.on('change', function (e) {
                    self.markAsSelected($(this));
                });

                checkbox.on('amshopby:sync_change', function (e) {
                    self.markAsSelected($(this));
                });

                self.markAsSelected(checkbox);
            })
        },
        markAsSelected: function (checkbox) {
            checkbox.closest('form').find('a').each(function () {
                if (!$(this).find('input').prop("checked")) {
                    $(this).removeClass('am_shopby_link_selected');
                } else {
                    $(this).addClass('am_shopby_link_selected');
                }
            });
        },
        toggleCheckParentAndChildrenCheckboxes: function (element, isChecked) {
            element.find('.items input[type="checkbox"]').prop('checked', false);
            element.parents('.item').find('>a input[type=checkbox]').prop('checked', false);
        }
    });

    $.widget('mage.amShopbyFilterDropdown', $.mage.amShopbyFilterAbstract, {
        options: {
            isMultiselect: false,
            placeholderText: $.mage.__('Select Options')
        },
        _create: function () {
            var self = this;
            $(function () {
                var $select = $(self.element[0]),
                    target,
                    value;

                if (self.options.isMultiselect) {
                    $select.chosen({
                        width: '100%',
                        placeholder_text: self.options.placeholderText
                    });
                } else {
                    $select.trigger('sync');
                }

                $select.change(function (e, elem) {
                    $select.trigger('sync');
                    if (self.options.isMultiselect) {
                        elem = elem ? elem : $(ev.target);
                        value = elem.selected ? elem.selected : elem.deselected;
                        target = $(e.target).parent();
                    } else {
                        target = $select[0];
                        value = $select.val();
                    }

                    $.mage.amShopbyFilterAbstract.prototype.renderShowButton(e, target);
                    self.apply($select.find("option[value='" + value + "']").attr("href"));
                });
            })
        }
    });

    $.widget('mage.amShopbyFilterSwatch', $.mage.amShopbyFilterAbstract, {
        options: {},
        _create: function () {
            var self = this;
            $(function () {
                var inputSelector = '[name="amshopby[' + getAtrtibuteCode() + '][]"]';

                function getAtrtibuteCode() {
                    return $(self.element[0]).closest('form[data-amshopby-filter]').attr('data-amshopby-filter');
                }

                function checked($link) {
                    return $link.find(inputSelector).prop('checked') == 1
                }

                $(self.element[0]).find('a').on('click', function (e) {
                    var $link = $(this);
                    var $input = $link.find(inputSelector);
                    $input.prop('checked', checked($link) ? 0 : 1);
                    $input.trigger('change');
                    $input.trigger('sync');
                    self.apply($link.attr('href'));
                    markSelected();
                    $.mage.amShopbyFilterAbstract.prototype.renderShowButton(e, this);
                    e.stopPropagation();
                    e.preventDefault();
                });

                $(self.element[0]).find(inputSelector).on('amshopby:sync_change', markSelected);

                function markSelected() {
                    $(self.element[0]).find('a').each(function () {
                        var $link = $(this);

                        if (checked($link)) {
                            $link.find('.swatch-option').addClass('selected');
                        } else {
                            $link.find('.swatch-option').removeClass('selected');
                        }
                    });
                }
            })
        }
    });

    $.widget('mage.amShopbyFilterSlider', $.mage.amShopbyFilterAbstract, {
        options: {},
        slider: null,
        value: null,
        display: null,
        _create: function () {
            $(function () {
                this.value = this.element.find('[amshopby-slider-id="value"]');
                this.slider = this.element.find('[amshopby-slider-id="slider"]');
                this.display = this.element.find('[amshopby-slider-id="display"]');
                var fromLabel = this.options.from ? this.options.from * this.element.attr('rate') : this.options.min;
                var toLabel = this.options.to ? this.options.to * this.element.attr('rate') : this.options.max;
                this.slider.slider({
                    step: this.options.step,
                    range: true,
                    min: this.options.min,
                    max: this.options.max,
                    values: [fromLabel, toLabel],
                    slide: this.onSlide.bind(this),
                    change: this.onChange.bind(this)
                });

                if (this.options.from && this.options.to) {
                    this.setValue(fromLabel, toLabel, false);
                } else {
                    this.value.trigger('change');
                    this.value.trigger('sync');
                }

                this.renderLabel(fromLabel, toLabel);

                this.value.on('amshopby:sync_change', this.onSyncChange.bind(this));

                if (this.options.hideDisplay) {
                    this.display.hide();
                }
            }.bind(this));
        },

        onChange: function (event, ui) {
            if (this.slider.skipOnChange !== true) {
                this.setValue(ui.values[0], ui.values[1], true, jQuery(ui.handle).closest('[data-am-js="slider-container"]').attr('rate'));
            }

            return true;
        },

        onSlide: function (event, ui) {
            var from = ui.values[0];
            var to = ui.values[1];

            this.setValue(from, to, false);
            this.renderLabel(from, to);
            return true;
        },

        onSyncChange: function (event, values) {
            var value = values[0].split('-'),
                rate = jQuery(event.currentTarget).parent().attr('rate');
            if (value.length === 2) {
                var fixed = 2;
                var from = (parseFloat(value[0])).toFixed(fixed);
                var to = parseFloat(value[1]).toFixed(fixed);
                var options = {
                    values: [
                        parseFloat(from).toFixed(fixed),
                        parseFloat(to).toFixed(fixed)
                    ]
                };
                var from = $.mage.amShopbyFilterAbstract.prototype.fixPriceForCurrency(parseFloat(from), 'from', rate).toFixed(4),
                    to = $.mage.amShopbyFilterAbstract.prototype.fixPriceForCurrency(parseFloat(to), 'to', rate).toFixed(4);

                this.slider.skipOnChange = true;

                this.slider.slider('values', [from, to]);
                this.setValueWtihoutChange(from, to);
                this.slider.skipOnChange = false;
            }
        },
        setValue: function (from, to, apply, rate) {
            var fixed = 2;
            from = (parseFloat(from)).toFixed(fixed);
            to = (parseFloat(to)).toFixed(fixed);
            var newVal = from + '-' + to;

            var changed = this.value.val() != newVal;
            this.value.val(newVal);
            if (changed) {
                this.value.trigger('change');
                this.value.trigger('sync');
            }

            if (apply !== false) {
                var fromNew = $.mage.amShopbyFilterAbstract.prototype.fixPriceForCurrency(parseFloat(from), 'from', rate),
                    toNew = $.mage.amShopbyFilterAbstract.prototype.fixPriceForCurrency(parseFloat(to), 'to', rate);
                newVal = fromNew.toFixed(4) + '-' + toNew.toFixed(4);

                this.value.val(newVal);
                var linkHref = this.options.url.replace('amshopby_slider_from', fromNew).replace('amshopby_slider_to', toNew);
                $.mage.amShopbyFilterAbstract.prototype.renderShowButton(0, this.element[0]);
                this.apply(linkHref);
            }
        },
        setValueWtihoutChange: function(from, to) {
            var fixed = this.getSignsCount(this.options.step, 0);
            from = (parseFloat(from)).toFixed(4);
            to = parseFloat(to).toFixed(4);
            var newVal = from + '-' + to;
            this.value.val(newVal);
        },
        getLabel: function (from, to) {
            return this.options.template.replace('{from}', from.toString()).replace('{to}', to.toString());
        },
        renderLabel: function (from, to) {
            var fixed = this.getSignsCount(this.options.step, 0);
            from = (parseFloat(from)).toFixed(fixed);
            to = (parseFloat(to)).toFixed(fixed);
            this.display.html(this.getLabel(from, to));
        }
    });

    $.widget('mage.amShopbyFilterFromTo', $.mage.amShopbyFilterAbstract, {
        from: null,
        to: null,
        value: null,
        timer: null,
        go: null,
        skip:false,
        _create: function () {
            $(function () {
                this.value = this.element.find('[amshopby-fromto-id="value"]');
                this.from = this.element.find('[amshopby-fromto-id="from"]');
                this.to = this.element.find('[amshopby-fromto-id="to"]');
                this.go = this.element.find('[amshopby-fromto-id="go"]');

                this.value.on('amshopby:sync_change', this.onSyncChange.bind(this));
                var priceSelector = '.am-filter-items-attr_price';

                if (this.value.val()
                    && !this.element.parent().find(priceSelector).length
                    && !this.element.closest(priceSelector).find('[data-am-js="slider-container"]').length
                    && !this.element.closest(priceSelector + '.am-dropdown').length
                ) {
                    var values = this.value.val().split('-'),
                        from = values[0] * this.element.find('.range').attr('rate'),
                        to = values[1] * this.element.find('.range').attr('rate');
                    this.value.trigger('amshopby:sync_change', [[values ? from + '-' + to : '', true]]);
                } else {
                    this.value.trigger('amshopby:sync_change', [[this.value.val() ? this.value.val() : '', true]]);
                }

                if (this.go.length > 0) {
                    this.go.on('click', this.applyFilter.bind(this));
                }

                this.changeEvent(this.from, this.onChange.bind(this));
                this.changeEvent(this.to, this.onChange.bind(this));

                this.element.find('form').mage('validation', {
                    errorPlacement: function (error, element) {
                        var parent = element.parent();
                        if (parent.hasClass('range')) {
                            parent.find(this.errorElement + '.' + this.errorClass).remove().end().append(error);
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    messages: {
                        'am_shopby_filter_widget_attr_price_from': {
                            'greater-than-equals-to': $.mage.__('Please enter a valid price range.'),
                            'validate-digits-range': $.mage.__('Please enter a valid price range.')
                        },
                        'am_shopby_filter_widget_attr_price_to': {
                            'greater-than-equals-to': $.mage.__('Please enter a valid price range.'),
                            'validate-digits-range': $.mage.__('Please enter a valid price range.')
                        }
                    }
                });

            }.bind(this));
        },
        onChange: function (event) {
            var to = this.to.val(),
                from = this.from.val(),
                fixed = this.getFixed(this.isSlider(), this.isPrice()),
                rate =jQuery(event.currentTarget).parent().attr('rate');

            if (!to) {
                to = this.options.max;
            }

            if (!from) {
                from = this.options.min;
            }

            [from, to] = this.checkFromTo(parseFloat(from), parseFloat(to));

            var newVal =  Number(from).toFixed(fixed) + '-' +  Number(to).toFixed(fixed),
                changed = newVal != this.value.val() && from && to;

            this.value.val(newVal);

            if (changed) {
                this.value.trigger('change');
                this.value.trigger('sync');
                to = $.mage.amShopbyFilterAbstract.prototype.fixPriceForCurrency(parseFloat(to), 'to', rate);
                from = $.mage.amShopbyFilterAbstract.prototype.fixPriceForCurrency(parseFloat(from), 'from', rate);
                newVal = parseFloat(Number(from).toFixed(4)) + '-' +  parseFloat(Number(to).toFixed(4));
                this.value.val(newVal);

                if (this.go.length == 0) {
                    $.mage.amShopbyFilterAbstract.prototype.renderShowButton(event, this.element[0]);
                    this.applyFilter();
                }
            }
        },
        applyFilter: function (e) {
            var rate = this.element.find('.range').attr('rate'),
                to = this.to.val(),
                from = this.from.val(),
                isPrice = this.isPrice(),
                fixed = this.getFixed(this.isSlider(), isPrice);

            [from, to] = this.checkFromTo(parseFloat(from), parseFloat(to));
            var fromNew = $.mage.amShopbyFilterAbstract.prototype.fixPriceForCurrency(parseFloat(from), 'from', rate),
                toNew = $.mage.amShopbyFilterAbstract.prototype.fixPriceForCurrency(parseFloat(to), 'to', rate);
            var linkHref = this.options.url
                .replace('amshopby_slider_from', fromNew.toFixed(4))
                .replace('amshopby_slider_to', toNew.toFixed(4));
            this.apply(linkHref);

            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
        },

        onSyncChange: function (event, values) {
            var value = values[0].split('-'),
                fixed = this.getFixed(this.isSlider(), 0),
                max = Number(this.options.max).toFixed(fixed),
                min = Number(this.options.min).toFixed(fixed),
                to = max, from = min;

                if (value.length === 2) {
                    from = value[0] == '' ? 0 : parseFloat(value[0]);
                    to = value[1] == 0 ? this.options.max : parseFloat(value[1]);

                if (this.isDropDown()) {
                    to = Math.ceil(to);
                }

                if (!this.isDropDown()) {
                    from = parseFloat(from).toFixed(fixed);
                    to = parseFloat(to).toFixed(fixed);
                    if (!fixed) {
                        var toValue = parseFloat(to);
                        var newVal = from + '-' + toValue;
                        this.value.val(newVal);
                    }
                }
            }
            if (from == min && to == max) {
                var notChange = true;
            }

            this.element.find('[amshopby-fromto-id="from"]').val(from);
            this.element.find('[amshopby-fromto-id="to"]').val(to);
        },

        checkFromTo: function (from, to) {
            from = from < this.options.min ? this.options.min : from;
            from = from > this.options.max ? this.options.min : from;
            to = to > this.options.max ? this.options.max : to;
            to = to < this.options.min ? this.options.max : to;

            if (from > to) {
                [from, to] = [to, from];
            }

            return [from, to];
        },
        /**
         * trigger keyup on input with delay
         * @param input
         * @param callback
         */
        changeEvent: function (input, callback) {
            input.on('keyup', function (event) {
                if (this.timer != null) {
                    clearTimeout(this.timer);
                }
                if (this.go.length == 0) {
                    this.timer = setTimeout(callback(event), 1000);
                } else {
                    callback(event);
                }
            }.bind(this));
        },

        isSlider: function () {
            return (typeof this.options.isSlider != 'undefined' && this.options.isSlider);
        },

        isDropDown: function () {
            return (typeof this.options.isDropdown != 'undefined' && this.options.isDropdown);
        }
    });

    $.widget('mage.amShopbyFilterSearch', {
        options: {
            highlightTemplate: "",
            itemsSelector: ""
        },

        previousSearch: '',

        _create: function () {
            var self = this;
            var $items = $(this.options.itemsSelector + " .item");
            $(self.element).keyup(function () {
                self.search(this.value, $items);
            });
        },

        search: function (searchText, $items) {
            var self = this;

            searchText = searchText.toLowerCase();
            if (searchText == this.previousSearch) {
                return;
            }
            this.previousSearch = searchText;

            if (searchText != '') {
                $(this.element).trigger('search_active');
            }

            $items.each(function (key, li) {
                if (li.hasAttribute('data-label')) {
                    var val = li.getAttribute('data-label').toLowerCase();
                    if (!val || val.indexOf(searchText) > -1) {
                        if (searchText != '' && val.indexOf(searchText) > -1) {
                            self.hightlight(li, searchText);
                        } else {
                            self.unhightlight(li);
                        }
                        $(li).show();
                        self.showParent($(li).closest('ol').closest('li').show());
                    }
                    else {
                        self.unhightlight(li);
                        $(li).hide();
                    }
                }
            });

            if (searchText == '') {
                $(this.element).trigger('search_inactive');
            }
        },
        showParent: function (parent) {
            if (parent.length !== 0) {
                $(parent).show();
                this.showParent($(parent).closest('ol').closest('li').show());
            }
        },
        hightlight: function (element, searchText) {
            this.unhightlight(element);
            var $a = $(element).find('a').length !== 0 ? $(element).find('a') : $(element);
            var label = $(element).attr('data-label');
            var newLabel = label.replace(new RegExp(searchText, 'gi'), this.options.highlightTemplate);
            $a.find('.label').html(newLabel);
        },
        unhightlight: function (element) {
            var $a = $(element).find('a').length !== 0 ? $(element).find('a') : $(element);
            var label = $(element).attr('data-label');
            $a.find('.label').html(label);
        }
    });

    $.widget('mage.amShopbyFilterHideMoreOptions', {
        options: {
            numberUnfoldedOptions: 0,
            _hideCurrent: false,
            buttonSelector: ""
        },

        _create: function () {
            var self = this;

            if ($(self.options.buttonSelector).first().parents('.filter-options-content').find(".item").length
                > this.options.numberUnfoldedOptions
            ) {
                $(this.options.buttonSelector).show();
            } else {
                return;
            }

            $(this.element).parents('.filter-options-content').on('search_active', function () {
                if (self.options._hideCurrent) {
                    self.toggle(self.options.buttonSelector);
                }
                $(self.options.buttonSelector).hide();
            });

            $(this.element).parents('.filter-options-content').on('search_inactive', function () {
                if (!self.options._hideCurrent) {
                    self.toggle(self.options.buttonSelector);
                }
                $(self.options.buttonSelector).show();
            });

            $(this.options.buttonSelector).unbind('click').click(function () {
                self.toggle(this);
            });

            // for hide in first load
            $(this.options.buttonSelector).each(function (index, element) {
                if (!$(element).attr('first_load')) {
                    $(element).attr('first_load', true);
                    $(element).click();
                }
            });
        },

        toggle: function (button) {
            var $button = $(button);
            if ($(button)[0]._hideCurrent) {
                this.showAll($button);
                $button.html($button.attr('data-text-less'));
                $button.attr('data-is-hide', 'false');
                $(button)[0]._hideCurrent = false;
            } else {
                var count = this.hideAll($button);
                $button.html($button.attr('data-text-more'));
                $button.attr('data-is-hide', 'true');
                $(button)[0]._hideCurrent = true;
                $($button).find('.hide_count').html(count);
            }
        },

        hideAll: function ($button) {
            var self = this,
                count = 0,
                hideCount = 0;
            $($button).parent().find(".item").each(function () {
                count++;
                if (count > self.options.numberUnfoldedOptions) {
                    hideCount++;
                    $(this).hide();
                }
            });

            return hideCount;
        },
        showAll: function ($button) {
            $($button).parent().find(".item").show();
        }
    });

    $.widget('mage.amShopbyFilterAddTooltip', {
        options: {
            content: "",
            tooltipTemplate: ""
        },
        _create: function () {
            var template = this.options.tooltipTemplate.replace('{content}', this.options.content);
            var $template = $(template);

            var $place = $(this.element).parents('.filter-options-item').find('.filter-options-title');
            if ($place.length == 0) {
                $place = $(this.element).parents('dd').prev('dt');
            }

            if ($place.length > 0 && !$place.find('.tooltip').length) {
                $place.append($template);

                $template.tooltip({
                    position: {
                        my: "left bottom-10",
                        at: "left top",
                        collision: "flipfit flip",
                        using: function (position, feedback) {
                            $(this).css(position);
                            $("<div>")
                                .addClass("arrow")
                                .addClass(feedback.vertical)
                                .addClass(feedback.horizontal)
                                .appendTo(this);
                        }
                    },
                    content: function () {
                        return $(this).prop('title');
                    }
                });
            }
        }
    });

    $.widget('mage.amShopbyFilterCategoryDropdown', $.mage.amShopbyFilterAbstract, {
        options: {},
        _create: function () {
            var self = this;
            $(function () {
                var $element = $(self.element[0]);
                $element.click(function (e) {
                    $element.parent().addClass('am-item-removed');
                    $element.trigger('sync');
                    $.mage.amShopbyFilterAbstract.prototype.renderShowButton(e, $element);
                    self.apply($element.data('remove-url'), true);
                    e.preventDefault();
                    e.stopPropagation();
                });
            })
        }
    });

    $.widget('mage.amShopbyFilterContainer', {
        options: {
            collectFilters: 0
        },

        _create: function () {
            var self = this;
            $(function () {
                var $element = $(self.element[0]);
                var links = $element.find('[data-am-js="shopby-item"]');
                var allClear = $element.siblings('.filter-actions');
                var filters = [];
                if (links.length) {
                    $(links).each(function (index, value) {
                        var filter = {
                            attribute: $(value).attr("data-container"),
                            value: $(value).attr("data-value")
                        };
                        filters.push(filter);

                        $(value).find('a').on("click", function (e) {
                            $(this).parent().addClass('am-item-removed');
                            $.mage.amShopbyFilterAbstract.prototype.renderShowButton(e, this);
                            self.apply(filter);
                            if (e) {
                                e.stopPropagation();
                                e.preventDefault();
                            }
                        });
                        if (filters.length) {
                            $.each(filters, function (index, filter) {
                                self.checkInForm(filter);
                            });
                        }
                    });
                }
            })
        },
        apply: function (filter) {
            var link = $('li[data-container="' + filter.attribute + '"][data-value="' + filter.value + '"] a.remove').attr('href');

            try {
                var self = this;

                var value = this.buildValues(filter.value);
                if (filter.attribute == 'price') {
                    value = [value.join('-')];
                }
                $.each(value, function (index, value) {
                    self.setDefault(filter.attribute, value);
                });

                $("[data-amshopby-filter]").trigger('change');
                $("[data-amshopby-filter]").trigger('sync');

                if ($.mage.amShopbyAjax != undefined) {
                    $.mage.amShopbyFilterAbstract.prototype.prepareTriggerAjax(null, link, true);
                } else if(this.options.collectFilters !== 1) {
                    window.location = link;
                }
            } catch(e) {
                window.location = link;
            }
        },

        buildValues: function (value) {
            var array = [];
            if (value.indexOf(',') !== -1) {
                array = value.split(",");
            } else {
                array.push(value);
            }

            return array;
        },

        clearBlock: function () {
            if (!$('[data-am-js="shopby-container"]').find("li").length) {
                $('[data-am-js="shopby-container"]').remove();
                $(".filter-actions").remove();
            }
        },

        setDefault: function (name, value) {
            var valueSelector = '[name="amshopby[' + name + '][]"]',
                filter = $(valueSelector),
                type = $(filter).prop("tagName");

            switch (type) {
                case 'SELECT' :
                    filter.find('[value=' + value + ']').removeAttr('selected', 'selected');
                    break;
                case 'INPUT' :
                    if ($(filter).attr("type") != 'text' && $(filter).attr("type") != 'hidden') {
                        var selected = $(valueSelector + '[value="' + value + '"]');
                        selected.removeAttr("checked");
                        selected.siblings('.swatch-option.selected').removeClass('selected');
                    } else if ($(filter).attr("type") == 'hidden') {
                        var selected = $(valueSelector);
                        selected.val("");
                    }
                    break;
            }
        },

        sliderDefault: function (name) {
            var valueSelector = '[name="amshopby[' + name + '][]"]',
                slider = $(valueSelector).siblings('[amshopby-slider-id="slider"]');
            if (slider.length) {
                var $parent = $(valueSelector).parent();
                $(slider[0]).slider("values", [$parent.attr('data-min'), $parent.attr('data-max')]);
                $(slider).siblings('[data-am-js="slider-display"]').text($parent.attr('data-min') + ' - ' + $parent.attr('data-max'));
            }
        },

        fromToDefault: function (name) {
            var range = $('[name="amshopby[' + name + '][]"]').siblings('.range');
            if (range.length) {
                var from = range.find('[amshopby-fromto-id="from"]'),
                    to = range.find('[amshopby-fromto-id="to"]'),
                    digits = $(from).attr('validate-digits-range'),
                    regexp = /\[([\d\.]+)-([\d\.]+)\]/g,
                    ranges = regexp.exec(digits);
                
                $(from).val(ranges[1]);
                $(to).val(ranges[2]);
            }
        },

        checkInForm: function (filter) {
            var name = filter.attribute;
            var value = filter.value;
            if (!$('[name="amshopby[' + name + '][]"]').length) {
                $('#layered-filter-block').append('<form data-amshopby-filter="' + name + '"><input value="' + value + '" type="hidden" name="amshopby[' + name + '][]"></form>')
            }
        }
    });
});
