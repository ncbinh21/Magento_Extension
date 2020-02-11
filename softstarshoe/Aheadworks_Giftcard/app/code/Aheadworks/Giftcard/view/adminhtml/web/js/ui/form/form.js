/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'Magento_Ui/js/form/form',
    './adapter',
    'Magento_Ui/js/modal/confirm',
    'moment',
    'mage/translate'
], function ($, Form, adapter, confirm, moment, $t) {
    'use strict';

    return Form.extend({
        defaults: {
            hintText: $t('This Gift Card is scheduled for delivery on {date}. Are you sure you want to send it now?')
        },
        /**
         * Initialize adapter
         * @returns {*}
         */
        initAdapter: function () {
            adapter.on({
                'reset': this.reset.bind(this),
                'save': this.save.bind(this, true, 'save'),
                'saveAndSend': this.saveAndSend.bind(this, true, 'save_and_send'),
                'saveAndContinue': this.save.bind(this, false, 'save_and_continue')
            });

            return this;
        },

        /**
         * Save action
         *
         * @param {String} redirect
         * @param {String} action
         */
        save: function (redirect, action) {
            this.source.set('data.action', action);
            this._super(redirect);
        },

        /**
         * Save and send action
         *
         * @param {String} redirect
         * @param {String} action
         */
        saveAndSend: function (redirect, action) {
            var self = this,
                now = new moment(),
                deliveryDate = this.source.get('data.delivery_date'),
                emailSent = this.source.get('data.email_sent'),
                emailTemplate = this.source.get('data.email_template');

            this.validate();
            if (!this.additionalInvalid && !this.source.get('params.invalid')) {
                // emailSent = 2 - Awaiting, emailSent = 3 - Not Send, emailSent = 4 - Failed
                // emailTemplate = 0 - Do not send
                if (deliveryDate && now.isBefore(moment(deliveryDate, 'MM/DD/YYYY', true), 'day') && emailTemplate != 0
                    && (!emailSent || emailSent == 2 || emailSent == 3 || emailSent == 4)
                ) {
                    confirm({
                        content: this.hintText.replace('{date}', deliveryDate),
                        actions: {
                            cancel: function (event) {
                                if ($(event.toElement).data('role') != 'closeBtn') {
                                    self.save(redirect, 'save');
                                }
                            },
                            confirm: function () {
                                self.save(redirect, action);
                            }
                        },
                        buttons: [{
                            text: $t('Save without sending'),
                            class: 'action-secondary action-dismiss',
                            click: function (event) {
                                this.closeModal(event);
                            }
                        }, {
                            text: $t('Send now'),
                            class: 'action-primary action-accept',
                            click: function (event) {
                                this.closeModal(event, true);
                            }
                        }]
                    });
                } else {
                    self.save(redirect, action);
                }
            }
        }
    });
});
