/**
 * IAS Trigger Extension
 * An IAS extension to show a trigger link to load the next page
 * http://infiniteajaxscroll.com
 *
 * This file is part of the Infinite AJAX Scroll package
 *
 * Copyright 2014-2015 Webcreate (Jeroen Fiege)
 */

var IASTriggerExtension = window.IASTriggerExtension = function(options) {
  options = jQuery.extend({}, this.defaults, options);

  this.ias = null;
  this.htmlPrev = (options.html).replace('{text}', options.text);
  this.enabled = true;
  this.count = 0;
  this.offset = options.offset;
  this.$triggerPrev = null;

  /**
   * Shows trigger for previous page
   */
  this.showTriggerPrev = function() {
    if (!this.enabled) {
      return true;
    }

    var $trigger = this.$triggerPrev || (this.$triggerPrev = this.createTrigger(this.prev, this.htmlPrev));
    var $firstItem = this.ias.getFirstItem();

    $firstItem.before($trigger);
    $trigger.fadeIn();

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

    $trigger.hide();
    $trigger.on('click', jQuery.proxy(clickCallback, this));

    return $trigger;
  };

  return this;
};

/**
 * @public
 * @param {object} ias
 */
IASTriggerExtension.prototype.bind = function(ias) {
  var self = this;

  this.ias = ias;

  try {
    ias.on('prev', jQuery.proxy(this.showTriggerPrev, this), this.priority);
  } catch (exception) {}
};

/**
 * @public
 * @param {object} ias
 */
IASTriggerExtension.prototype.unbind = function(ias) {
  try {
    ias.off('prev', this.showTriggerPrev);
  } catch (exception) {}
};

/**
 * @public
 */
IASTriggerExtension.prototype.prev = function() {
  this.enabled = false;
  this.ias.pause();

  if (this.$triggerPrev) {
    this.$triggerPrev.remove();
    this.$triggerPrev = null;
  }

  this.ias.prev();
};

/**
 * @public
 */
IASTriggerExtension.prototype.defaults = {
  text: 'Load more items',
  html: '<div class="ias-trigger ias-trigger-next" style="text-align: center; cursor: pointer;"><a>{text}</a></div>',
  offset: 0
};

/**
 * @public
 * @type {number}
 */
IASTriggerExtension.prototype.priority = 1000;
