/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'moment',
    'mage/calendar',
    'Aheadworks_Giftcard/js/lib/moment-timezone',
    'Aheadworks_Giftcard/js/lib/moment-timezone-with-data-2012-2022'
], function($, moment) {
    'use strict';

    $.widget('mage.awGiftCardCalendar', {
        options: {
            addDays: '',
            timezoneSelector: '',
            timezoneSelectorValue: '',
            calendarDateFormat: 'mm/dd/yyyy',
            momentDateFormat: 'MM/DD/YYYY'
        },
        previousDate: '',
        minDate: '',
        timezoneOffset: 0,

        /**
         * Initialize widget
         */
        _create: function() {
            if (!this.options.timezoneSelectorValue) {
                $(this.options.timezoneSelector).val(moment.tz.guess()).trigger('change');
            }
            this.timezoneChange();

            this.element.on('change', $.proxy(this.dateChange, this));
            $(this.options.timezoneSelector).on('change', $.proxy(this.timezoneChange, this));
            this.element.on('focus', $.proxy(this.dateInputFocus, this));
        },

        /**
         * Date change on input element
         */
        dateChange: function() {
            var newDate = $(this.element).val();

            if (newDate && (!moment(newDate, this.options.momentDateFormat, true).isValid()
                || moment(newDate, this.options.momentDateFormat, true).isBefore(this.minDate, 'day'))
            ) {
                if (moment(this.previousDate, this.options.momentDateFormat, true).isBefore(this.minDate, 'day')) {
                    $(this.element).val(this.previousDate);
                } else {
                    $(this.element).val(this.minDate.format(this.options.momentDateFormat));
                }
            }
        },

        /**
         * Focus on input element
         */
        dateInputFocus: function() {
            this.previousDate = $(this.element).val();
        },

        /**
         * Timezone change
         */
        timezoneChange: function() {
            var newTimezone = $(this.options.timezoneSelector).val();

            this.timezoneOffset = moment.tz(newTimezone)._offset;

            this.changeMinDate();
            this.destroyCalendar();
            this.initCalendar();
            this.dateChange();
        },

        /**
         * Change min date for new timezone
         */
        changeMinDate: function() {
            var dateInSelectedTimezone = new moment()
                .add(this.timezoneOffset - moment.tz(moment.tz.guess())._offset, 'minutes');

            this.minDate = this.options.addDays
                ? dateInSelectedTimezone.add(this.options.addDays, 'days')
                : dateInSelectedTimezone;
        },

        /**
         * Initialize Calendar
         */
        initCalendar: function() {
            $(this.element).calendar({
                dateFormat: this.options.calendarDateFormat,
                minDate: this.minDate.toDate(),
                setDate: 0,
                showOn: 'button'
            });
        },

        /**
         * Destroy Calendar
         */
        destroyCalendar: function() {
            if ($(this.element).data('datepicker') != null) {
                $(this.element).calendar('destroy');
            }
        }
    });

    return $.mage.awGiftCardCalendar;
});
