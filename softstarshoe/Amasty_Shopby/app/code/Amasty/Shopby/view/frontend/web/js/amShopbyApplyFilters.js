define([
    "underscore",
    "jquery",
    "jquery/ui",
    "amShopbyFilterAbstract",
], function (_, $) {
    'use strict';

    $.widget('mage.amShopbyApplyFilters', {
        canApplyFilter: false,
        _create: function () {
            var self = this;
            $(function() {
                self.initEvents();

                var element = $(self.element[0]);
                var navigation = element.closest(self.options.navigationSelector);
                if (self.options.buttonPosition == 'sidebar') {
                    navigation.find('#narrow-by-list').append(element.parent());
                } else {
                    navigation.find('strong[role=heading]').addClass('has-apply-button').append(element.parent())
                }

                element.on('click', function (e) {
                    var valid = true;
                    navigation.find('form').each(function(){
                        valid = valid && $(this).valid();
                    });
                    if (valid && self.options.ajaxEnabled && self.canApplyFilter) {
                        var data = $.mage.amShopbyFilterAbstract.prototype.prepareTriggerAjax(this);
                    }
                    if (valid && self.options.ajaxEnabled != 1) {
                        var forms = $('form[data-amshopby-filter]');
                        var data = $.mage.amShopbyFilterAbstract.prototype.normalizeData(forms.serializeArray())
                        var params = $.param(data);

                        var url = self.options.clearUrl +
                            (self.options.clearUrl.indexOf('?') === -1 ? '?' : '&') +
                            params;
                        document.location.href = url;
                    }
                    this.blur();
                    return true;
                });

            });
        },

        initEvents: function() {
            $(document).on("change", "[data-amshopby-filter]", function () {
                this.canApplyFilter = true;
            }.bind(this));
        }
    });
});
