define([
    'jquery',
    'wizard-tabs',
    'ko',
    'mage/translate',
    'tabs-title',
    'Forix_ProductWizard/js/model/wizard-data',
    'Magento_Ui/js/model/messageList'
], function ($, tabs, ko, $t, tabTitles, wizardData, globalMessageList) {
    "use strict";

    $.widget('mage.configuratorTabs', tabs, {
        current: false,
        _create: function () {
            this._super();
            var self = this;
            tabTitles.init(this.element);
            tabTitles.setIndex('default');
            wizardData.set('tab-index', {});
            wizardData.get('tab-index').subscribe(function (nextData) {
                if (nextData.next) {
                    var hasBits = false, nextTo = nextData.next;
                    // If don't have current, won do anything.
                    if (nextData.current >= 0) {
                        var current = parseInt(nextData.current), _form = $('.wizard-form-main-' + current),
                            inputList = _form.find('[required="required"]');
                        if (inputList.length) {
                            /*Get form data of current step*/
                            if (!_form.validation().validation('isValid')) {
                                //globalMessageList.addErrorMessage({'message': 'Please choose options'});
                                return;
                            }
                        }
                        if (nextData.switch) {
                            var stepBits = _form.find('[data-switchto="' + element.dataset.switch + '"]');
                            if (stepBits.length) {
                                stepBits.each(function (i, e) {
                                    if (e.value) {
                                        hasBits = true;
                                        if (e.dataset.nextindex) {
                                            nextTo = e.dataset.nextindex;
                                        }
                                        return true;
                                    }
                                });
                            }
                            if (hasBits) {
                                tabTitles.setIndex(element.dataset.switch);
                            } else {
                                tabTitles.setIndex('default');
                            }
                        }
                    }
                    wizardData.buildSharedUrlPath(nextTo);
                    wizardData.isLoading(true);
                    self.activate(nextTo);

                    /// Check if next step has one option. this selection will not working if configurator has 4 steps.
                    nextTo = parseInt(nextTo);
                    /// Check if next step has one option. this selection will not working if configurator has 4 steps.

                    $('.product-wizard-step-' + (nextTo + 1) + ' input.display').prop('disabled', false);

                    wizardData.isLoading(false);
                }
            });
        },
        activate: function (index) {
            $('.tab-label-wizard').removeClass('processed');
            this._super(index);
        }
    });
    return $.mage.configuratorTabs;
});