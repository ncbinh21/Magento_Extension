define([
    'jquery',
    "mage/collapsible",
    'Forix_ProductWizard/js/model/wizard-data'
], function ($, collapsible, wizardData) {
    "use strict";
    $.widget("mage.collapsible", collapsible, {
        addProcessed: function (index) {
            $('.tab-label-wizard[aria-labeledby*="tab-label-wizard"]').each(function (i, e) {
                if (i < index) {
                    e.classList.add("processed")
                } else {
                    return false;
                }
            });
        },
        activate: function () {
            var nextData = wizardData.get('tab-index')();
            if(nextData.current == 0 && nextData.next < 1) {
                $('.configurator-image-header').removeClass('no-display');
            } else {
                $('.configurator-image-header').addClass('no-display');
            }
            this.addProcessed(nextData.next);
            var skipStep = false;
            if (this.element) {
                if (this.element[0].dataset.index > nextData.next) {
                    return;
                } else if (this.element[0].dataset.index < nextData.next) {
                    wizardData.abortAjaxRequestProduct();
                    wizardData.set('tab-index', {'current' : this.element[0].dataset.index, 'next' : this.element[0].dataset.index});
                    this.addProcessed(this.element[0].dataset.index);
                    return;
                }else if(parseInt(this.element[0].dataset.index) === nextData.current){
                    return;
                }else{
                    skipStep = true;
                }
            }
            this._super();
            if(skipStep) {
                var nextElement = $('.step-index-' + (nextData.next + 1));
                if (1 === nextElement.find('.group-item-option.display').length) {
                    $(nextElement.find('.product-item.display .btn-continue')[0]).prop('checked', true).trigger('click');
                    $(document).trigger('show-notify-message', true);
                }else{
                    $(document).trigger('show-notify-message', false);
                }
            }
        }
    });
    return $.mage.collapsible;
});