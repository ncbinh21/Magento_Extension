/**
 * IAS Paging Extension
 * An IAS extension providing additional events
 * http://infiniteajaxscroll.com
 *
 * This file is part of the Infinite AJAX Scroll package
 *
 * Copyright 2014-2015 Webcreate (Jeroen Fiege)
 */

var IASPagingExtension = window.IASPagingExtension = function() {
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
   * Fires pageChange event
   *
   * @param currentScrollOffset
   * @param scrollThreshold
   */
  this.onScroll = function(currentScrollOffset, scrollThreshold) {
    if (!this.enabled) {
      return;
    }

    var ias = this.ias,
        currentPageNum = this.getCurrentPageNum(currentScrollOffset),
        urlPage;

    if (currentPageNum > 0) {
      var currentPagebreak = this.pagebreaks[currentPageNum -1];
      if (this.lastPageNum !== currentPageNum) {
        urlPage = currentPagebreak[1];

        ias.fire('pageChange', [currentPageNum, currentScrollOffset, urlPage]);
      }

      this.lastPageNum = currentPageNum;
    }
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

  /**
   * Keeps track of pagebreaks
   *
   * @param url
   */
  this.onPrev = function(url) {
    var self = this,
        ias = self.ias;

    var renderRunOne = function(items) {
      var currentScrollOffset = ias.getCurrentScrollOffset(ias.$scrollContainer),
          prevCurrentScrollOffset = currentScrollOffset - ias.$scrollContainer.height(),
          $firstItem = ias.getFirstItem();

      self.enabled = false;

      self.pagebreaks.unshift([0, url, items[0]]);

      var currentPageNum = self.getCurrentPageNum(prevCurrentScrollOffset) + 1;
      ias.fire('pageChange', [currentPageNum, prevCurrentScrollOffset, url]);
      self.lastPageNum = currentPageNum;

      self.enabled = true;
    };

    renderRunOne.gui = jQuery.now();
    ias.one('render', renderRunOne);

  };

  this.gotoLastPage = function() {
    var self = this,
        ias = self.ias;
    var lastPageIndex = self.pagebreaks.length -1;
    //window.console.log("test");
    if (lastPageIndex > 0) {
      if (this.lastPageNum !== lastPageIndex) {
        var lastPage = self.pagebreaks[lastPageIndex],
            position = jQuery(lastPage[2]).offset().top;
        ias.$scrollContainer.scrollTop(position);
        ias.fire('pageChange', [lastPageIndex, position, lastPage[1]]);
        self.lastPageNum = lastPageIndex;
      }
    }
  };

  return this;
};

/**
 * @public
 */
IASPagingExtension.prototype.initialize = function(ias) {
  this.ias = ias;
  this.onLoad();

  // expose the extensions listeners
  jQuery.extend(ias.listeners, this.listeners);
  ias.gotoLastPage = jQuery.proxy(this.gotoLastPage, this);
};

/**
 * @public
 */
IASPagingExtension.prototype.bind = function(ias) {
  try {
    ias.on('prev', jQuery.proxy(this.onPrev, this), this.priority);
  } catch (exception) {}

  ias.on('next', jQuery.proxy(this.onNext, this), this.priority);
  ias.on('scroll', jQuery.proxy(this.onScroll, this), this.priority);
};

/**
 * @public
 * @param {object} ias
 */
IASPagingExtension.prototype.unbind = function(ias) {
  try {
    ias.off('prev', this.onPrev);
  } catch (exception) {}

  ias.off('next', this.onNext);
  ias.off('scroll', this.onScroll);
};

/**
 * Returns current page number based on scroll offset
 *
 * @param {number} scrollOffset
 * @returns {number}
 */
IASPagingExtension.prototype.getCurrentPageNum = function(scrollOffset) {
  //window.console.log('current: ' + scrollOffset);
  var pageBreakItem;
  for (var i = (this.pagebreaks.length - 1); i > 0; i--) {
    /*if (scrollOffset > this.pagebreaks[i][0]) {
      return i + 1;
    }*/
    //window.console.log('item ' + i + ' : ' + jQuery(this.pagebreaks[i][2]).offset().top);
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
IASPagingExtension.prototype.getCurrentPagebreak = function(scrollOffset) {
  var pageBreakItem;
  for (var i = (this.pagebreaks.length - 1); i >= 0; i--) {
    /*if (scrollOffset > this.pagebreaks[i][0]) {
      return this.pagebreaks[i];
    }*/
    //window.console.log(jQuery(this.pagebreaks[i][2]).offset().top);
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
IASPagingExtension.prototype.priority = 600;
