/**
 * IAS Trigger-next Extension
 * An IAS extension to show a trigger link to load the next page
 * http://infiniteajaxscroll.com
 *
 * This file is part of the Infinite AJAX Scroll package
 *
 * Copyright 2014-2015 Webcreate (Jeroen Fiege)
 */

var IASTriggerNextExtension = window.IASTriggerNextExtension = function(options) {
    options = jQuery.extend({}, this.defaults, options);

    this.ias = null;
    this.html = (options.html).replace('{text}', options.text);
    this.enabled = true;
    this.count = 0;
    this.offset = options.offset;
    this.$triggerNext = null;

    /**
     * Shows trigger for next page
     */
    this.showTriggerNext = function() {
        if (!this.enabled) {
            return true;
        }

        if (false === this.offset || ++this.count < this.offset) {
            return true;
        }

        var pageCurrent = this.ias.getPageCurrent();
        var pageTotal = this.ias.getPageTotal(),
            buttonText = (options.text).replace('{pageCurrent}', pageCurrent)
                .replace('{pageTotal}', pageTotal);
        this.html = (options.html).replace('{text}', buttonText);

        if (!this.ias.isNewDomRender()) {
            jQuery("[id^='ias_trigger_']").remove();
            this.$triggerNext = this.createTrigger(this.next, this.html);
            this.ias.hidePagination();
            let $lastItem = this.ias.getLastItem();
            $lastItem.after(this.$triggerNext);
        }

        this.$triggerNext.fadeIn(0);
        return false;
    };

    this.onRendered = function() {
        this.enabled = true;
    };



    /**
     * @param clickCallback
     * @returns {*|jQuery}
     * @param {string} html
     */
    this.createTrigger = function(clickCallback, html) {
        var uid = (new Date()).getTime(),
            $trigger;

        html = html || this.html;
        $trigger = jQuery(html).attr('id', 'ias_trigger_' + uid);
        var buttonAction = $trigger.find('.action');
        if (buttonAction.length == 0) {
            buttonAction = $trigger;
        }
        $trigger.remove();
        buttonAction.on('click', jQuery.proxy(clickCallback, this));

        return $trigger;
    };

    return this;
};

/**
 * @public
 * @param {object} ias
 */
IASTriggerNextExtension.prototype.bind = function(ias) {
    var self = this;
    this.ias = ias;

    ias.on('next', jQuery.proxy(this.showTriggerNext, this), this.priority);
    ias.on('rendered', jQuery.proxy(this.onRendered, this), this.priority);
};

/**
 * @public
 * @param {object} ias
 */
IASTriggerNextExtension.prototype.unbind = function(ias) {
    ias.off('next', this.showTriggerNext);
    ias.off('rendered', this.onRendered);
};

/**
 * @public
 */
IASTriggerNextExtension.prototype.next = function() {
    this.enabled = false;
    this.ias.pause();
    this.ias.next();
};

/**
 * @public
 */
IASTriggerNextExtension.prototype.defaults = {
    text: 'Load more items',
    html: '<div class="ias-trigger ias-trigger-next" style="text-align: center; cursor: pointer;"><a>{text}</a></div>',
    offset: 0
};

/**
 * @public
 * @type {number}
 */
IASTriggerNextExtension.prototype.priority = 1000;
