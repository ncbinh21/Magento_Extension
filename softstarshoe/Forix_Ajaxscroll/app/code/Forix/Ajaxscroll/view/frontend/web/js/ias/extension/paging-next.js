/**
 * IAS Paging Extension
 * An IAS extension providing additional events
 * http://infiniteajaxscroll.com
 *
 * This file is part of the Infinite AJAX Scroll package
 *
 * Copyright 2014-2015 Webcreate (Jeroen Fiege)
 */

var IASPagingNextExtension = window.IASPagingNextExtension = function() {
  this.ias = null;
  this.pagebreaks = [];
  this.lastPageNum = 1;
  this.enabled = true;
  this.listeners = {
    pageChange: new IASCallbacks()
  };

  this.onLoad = function() {
    this.pagebreaks.push([0, document.location.toString(), this.ias.getFirstItem().get(0)]);
  };

  /**
   * Keeps track of pagebreaks
   *
   * @param url
   */
  this.onNext = function(url) {
    var currentScrollOffset = this.ias.getCurrentScrollOffset(this.ias.$scrollContainer),
        ias = this.ias,
        self = this;

    var renderRunOne = function(items) {
      self.pagebreaks.push([currentScrollOffset, url, items[0]]);

      // trigger pageChange and update lastPageNum
      var currentPageNum = self.getCurrentPageNum(currentScrollOffset) + 1;

      ias.fire('pageChange', [currentPageNum, currentScrollOffset, url]);

      self.lastPageNum = currentPageNum;
    };

    renderRunOne.gui = jQuery.now();
    ias.one('render', renderRunOne);

  };

  this.gotoLastPage = function() {
    var self = this,
        ias = self.ias;
    var lastPageIndex = self.pagebreaks.length -1;
    //window.console.log("go to last page " +lastPageIndex);
    if (lastPageIndex >= 0) {
      if (this.lastPageNum !== lastPageIndex) {
        var lastPage = self.pagebreaks[lastPageIndex],
            position = jQuery(lastPage[2]).offset().top;
        ias.$scrollContainer.scrollTop(position);
        ias.fire('pageChange', [lastPageIndex, position, lastPage[1]]);
        self.lastPageNum = lastPageIndex;
        jQuery('.grid').trigger('loadImageFanPhoto');
      }
    }
  };

  return this;
};

/**
 * @public
 */
IASPagingNextExtension.prototype.initialize = function(ias) {
  this.ias = ias;
  this.onLoad();

  // expose the extensions listeners
  jQuery.extend(ias.listeners, this.listeners);
  ias.gotoLastPage = jQuery.proxy(this.gotoLastPage, this);
};

/**
 * @public
 */
IASPagingNextExtension.prototype.bind = function(ias) {
  ias.on('next', jQuery.proxy(this.onNext, this), this.priority);
};

/**
 * @public
 * @param {object} ias
 */
IASPagingNextExtension.prototype.unbind = function(ias) {
  ias.off('next', this.onNext);
};

/**
 * Returns current page number based on scroll offset
 *
 * @param {number} scrollOffset
 * @returns {number}
 */
IASPagingNextExtension.prototype.getCurrentPageNum = function(scrollOffset) {
  var pageBreakItem;
  for (var i = (this.pagebreaks.length - 1); i > 0; i--) {
    /*if (scrollOffset > this.pagebreaks[i][0]) {
      return i + 1;
    }*/
    pageBreakItem = jQuery(this.pagebreaks[i][2]);
    if (scrollOffset > pageBreakItem.offset().top + pageBreakItem.height()) {
      return i + 1;
    }
  }

  return 1;
};

/**
 * Returns current pagebreak information based on scroll offset
 *
 * @param {number} scrollOffset
 * @returns {number}|null
 */
IASPagingNextExtension.prototype.getCurrentPagebreak = function(scrollOffset) {
  var pageBreakItem;
  for (var i = (this.pagebreaks.length - 1); i >= 0; i--) {
    /*if (scrollOffset > this.pagebreaks[i][0]) {
      return this.pagebreaks[i];
    }*/
    pageBreakItem = jQuery(this.pagebreaks[i][2]);
    if (scrollOffset > pageBreakItem.offset().top + pageBreakItem.height()) {
      return i + 1;
    }
  }

  return this.pagebreaks[0];
};

/**
 * @public
 * @type {number}
 */
IASPagingNextExtension.prototype.priority = 600;
