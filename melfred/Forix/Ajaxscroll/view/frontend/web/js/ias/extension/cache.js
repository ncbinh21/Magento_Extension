/**
 * IAS History Extension
 * An IAS extension to enable browser history
 * http://infiniteajaxscroll.com
 *
 * This file is part of the Infinite AJAX Scroll package
 *
 * Copyright 2014-2015 Webcreate (Jeroen Fiege)
 */
Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
var IASCacheExtension =
    window.IASCacheExtension = function (options) {
    this.options = jQuery.extend({}, this.defaults, options);
    var cacheKeys = false
    var cacheKeysTemp = [];
    this.ias = null;
    this.isPause = false;
    this.isInit = true;
    this.lastItemIndex = -1;
    this.cacheScroll = false;
    this.lastScrollPosition = -1;
    this.forceScrollPosition = -1;

    this.updateUrl = function (url) {
        if (!window.history || !window.history.replaceState) {
            return;
        }
        var state = history.state;

        history.replaceState(state, document.title, url);
    };

    // Get scroll Data
    this.getCacheScrollData = function() {
        if (this.cacheScroll === false) {
            this.cacheScroll = {'length' : 0};
            if (this.supportsSessionStorage()) {
                var cache = sessionStorage.getItem(this.options.cacheKey+'_scroll');
                if (cache) {
                    this.cacheScroll = JSON.parse(cache);
                }
            }
        }
        return this.cacheScroll;
    };

    // Get position of scroll of page
    this.getCacheScrollPosition = function(url) {
        var data = this.getCacheScrollData();
        if (data.hasOwnProperty(url)) {
            return data[url];
        }

        return -1;
    };

    this.storeCacheScrollPosition = function(url, position) {
        url = this.getUrl(url);
        if (this.forceScrollPosition > 0) {
            position = this.forceScrollPosition;
        }
        var myReg = new RegExp("(\\?(?:[^=]+?=[^=]*&)*)" + this.escapeRegExp(this.options.page) + "\\d(&|$)", "m");
        if (myReg.test(url)) {
            var data = this.getCacheScrollData();
            if (data.hasOwnProperty(url)) {
                delete data[url];
                data.length--;
            }
            data[url] = position;
            data.length++;
            if (this.supportsSessionStorage()) {
                sessionStorage.setItem(this.options.cacheKey+'_scroll', JSON.stringify(data));
            }
        }
    };

    this.setPreviousScroll = function() {
        var data = this.getCacheScrollData(),
            url = this.getUrl(document.location.toString());

        if (data.hasOwnProperty(url)) {
            this.lastScrollPosition = data[url];
            delete data[url];
            data.length--;
        }
    };

    this.loadCache = function() {
        if (cacheKeys === false) {
            cacheKeys = [];
            if (this.supportsSessionStorage()) {
                var cache = sessionStorage.getItem(this.options.cacheKey);
                if (cache) {
                    cacheKeys = JSON.parse(cache);
                }

            }
        }
        return cacheKeys;
    };

    this.onPrev = function(url) {
        if (this.isPause) {
            return false;
        }
        this.isPause = true;

        var ias = this.ias;
        var key_url = url;

        var sefl = this;
        var result = true;

        var data = this.getCache(key_url);
        if (data) {
            result = false;

            var items = ias.getItems(data);

            var preUrl = ias.getPrevUrl(data);
            ias.setPrevUrl(preUrl);

            ias.renderBefore(items, function () {
                sefl.isPause = false;
                ias.gotoLastPage();
                if (preUrl) {
                    ias.prev();
                } else {
                    sefl.scrollToPrev();
                }
            });
        } else {
            sefl.scrollToPrev();

            sefl.isPause = false;
        }

        return result;
    };

    this.onRendered = function(items) {
        this.bindItemClick(jQuery(items));
    };

    this.bindItemClick = function(items) {
        var self = this;
        items.on('click.iascache', function() {
            // store position
            self.forceScrollPosition = jQuery(this).offset().top - ((self.ias.$scrollContainer.height() - jQuery(this).height())/2);
        });
    };

    this.scrollToPrev = function() {
        var self = this,
            ias = this.ias;
        if (this.isInit == true) {
            if(this.lastScrollPosition > 0) {
                ias.$scrollContainer.scrollTop(this.lastScrollPosition);
            } else {
                this.ias.gotoLastPage();
            }

            this.isInit = false;
        }
    };

    /**
     * Get url - remove first page param
     * @param url
     * @returns {*}
     */
    this.getUrl = function(url) {
        var myReg = new RegExp("(\\?(?:[^=]+?=[^=]*&)*)" + this.escapeRegExp(this.options.page + '1') + "(&|$)", "mg");
        return url.replace(myReg, "$1$2").replace(/[& \?]+$/mg, "");
    };

    this.escapeRegExp = function(string){
        return string.replace(/([.*+?^${}()|\[\]\/\\])/g, "\\$1");
    };

    this.onLoad = function(data, items, loadEvent) {
        var key_url = loadEvent.url;

        /*var store = {
         'preUrl': this.ias.getPrevUrl(data),
         'nextUrl': this.ias.getPrevUrl(data),
         'items': items
         };*/

        this.addCache(key_url, data);
    };

    this.supportsSessionStorage = function() {
        try {
            return !!sessionStorage.getItem;
        } catch(e) {
            return false;
        }
    };

    this.addCache = function(key, value) {
        key = 'isa_cache_' + this.getUrl(key);
        var isSuccess = false;
        var hasError = false;
        var countTmp = 0;
        while(!isSuccess && countTmp++ < this.options.cacheLimitStep){
            try{
                var expires_at = (new Date).getTime() + (60000 * parseFloat(this.options.minutesToExpiration)),
                    json_string = JSON.stringify({
                        data: value,
                        expires_at: expires_at
                    });

                if (this.supportsSessionStorage()) {
                    cacheKeys.remove(key);

                    if (
                        (this.options.cacheLimitStep > 0) &&
                        ((cacheKeys.length + 1) > this.options.cacheLimitStep) ||
                        hasError
                    ) {
                        var lastKey = cacheKeys.shift();
                        if (lastKey) {
                            this.removeCache(lastKey);
                        }
                    }

                    sessionStorage.setItem(key, json_string);
                    cacheKeys.push(key);

                    sessionStorage.setItem(this.options.cacheKey, JSON.stringify(cacheKeys));
                }
                isSuccess = true;
            }catch (err){
                console.log('error: '+ err);
                hasError = true;
            }
        }


    };

    this.removeCache = function(key) {
        if (this.supportsSessionStorage()) {
            sessionStorage.removeItem(key);
        }
    };

    this.getCache = function(key) {
        key = 'isa_cache_' + this.getUrl(key);
        for(var i = 0; i < cacheKeysTemp.length; i++) {
            if(cacheKeysTemp[i] == key) {
                return false;
            }
        }
        if (this.supportsSessionStorage()) {
            cacheKeysTemp.push(key);
            var json_string = sessionStorage.getItem(key),
                json_object,
                expires_at,
                now = (new Date).getTime();

            if (json_string) {
                json_object = JSON.parse(json_string);
                expires_at = json_object.expires_at;

                if (Math.floor(expires_at) > now) {
                    return json_object.data;
                } else {
                    sessionStorage.removeItem(key);
                }
            }
        }

        return false;
    };

    return this;
};

/**
 * @public
 */
IASCacheExtension.prototype.initialize = function (ias) {
    var self = this;

    this.ias = ias;

    this.bindItemClick(jQuery(ias.itemSelector, ias.getItemsContainer().get(0)));

    self.loadCache();
    self.setPreviousScroll();


    jQuery(window).on('unload', function(){
        self.storeCacheScrollPosition(document.location.toString(), ias.$scrollContainer.scrollTop());
    });

    var currentUrl = window.location.href;
    self.addCache(currentUrl, jQuery('html>body').html());
};

/**
 * Bind to events
 *
 * @public
 * @param ias
 */
IASCacheExtension.prototype.bind = function (ias) {
    ias.on('loaded', jQuery.proxy(this.onLoad, this), this.priority);
    ias.on('rendered', jQuery.proxy(this.onRendered, this), this.priority);
    try {
        ias.on('prev', jQuery.proxy(this.onPrev, this), this.priority);
    } catch (exception) {}
};

/**
 * @public
 * @param {object} ias
 */
IASCacheExtension.prototype.unbind = function(ias) {
    ias.off('loaded', this.onLoad);
    ias.off('rendered', this.onRendered);
    try {
        ias.off('prev', this.onPrev, this);
    } catch (exception) {}
};

/**
 * @public
 */
IASCacheExtension.prototype.defaults = {
    cacheLimitStep: 100,
    'cacheKey' : 'isa_cache',
    'minutesToExpiration' : 3000,
    'page': 'p='
};
IASCacheExtension.prototype.priority = 100;