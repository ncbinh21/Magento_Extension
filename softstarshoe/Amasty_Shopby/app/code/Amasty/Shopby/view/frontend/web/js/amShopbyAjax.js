/**
 * @author    Amasty Team
 * @copyright Copyright (c) Amasty Ltd. ( http://www.amasty.com/ )
 * @package   Amasty_Shopby
 */
define([
    "jquery",
    'amShopbyTopFilters',
    "productListToolbarForm",
    "jquery/ui",
    "amShopbyFilterAbstract"
], function ($, amShopbyTopFilters) {
    'use strict';
    $.widget('mage.amShopbyAjax',{
        prevCall: false,
        $shopbyOverlay : null,
        cached: [],
        blockToolbarProcessing: false,

        _create: function (){
            var self = this;
            $(function(){
                self.initWidget();

                if (typeof window.history.replaceState === "function") {
                    window.history.replaceState({url: document.URL}, document.title);

                    setTimeout(function() {
                        /*
                         Timeout is a workaround for iPhone
                         Reproduce scenario is following:
                         1. Open category
                         2. Use pagination
                         3. Click on product
                         4. Press "Back"
                         Result: Ajax loads the same content right after regular page load
                         */
                        window.onpopstate = function(e) {
                            if(e.state){
                                self.callAjax(e.state.url, []);
                            }
                        };
                    }, 0)
                }
            });

        },

        initWidget: function() {
            var self = this;
            $(document).on('baseCategory', function (event, eventData) {
                self.baseCategoryId = eventData;
            });
            $(document).on('amshopby:submit_filters', function (event, eventData) {
                var clearUrl = eventData.clearUrl; //keep ?p= param
                var data = eventData.data;
                if (self.prevCall) {
                    self.prevCall.abort();
                }
                if (!clearUrl) {
                    //remove filter's url on last option un-check
                    clearUrl = self.options.clearUrl;
                }
                var dataAndUrl = data.slice(0);
                dataAndUrl.push(clearUrl);
                var cacheKey = JSON.stringify(dataAndUrl);
                if (self.cached[cacheKey]) {
                    self.reloadHtml(self.cached[cacheKey]);
                    window.history.pushState({url: self.cached[cacheKey].url}, '', self.cached[cacheKey].url);
                    self.initAjax();
                } else {
                    self.prevCall = self.callAjax(clearUrl, data, true, cacheKey);
                }
            });
            self.initAjax();
        },

        callAjax: function(clearUrl, data, pushState, cacheKey) {
            var self = this;
            this.$shopbyOverlay.show();
            data.every(function (item, key) {
                if (item.name.indexOf('[cat]') != -1 && item.value == self.options.baseCategoryId) {
                    data.splice(key, 1);
                    return false;
                }
            });
            data.push({name: 'shopbyAjax', value: 1});

            return $.ajax({
                url: clearUrl,
                data: data,
                cache: true,
                success: function (response) {
                    try {
                        response = $.parseJSON(response);

                        if (cacheKey) {
                            self.cached[cacheKey] = response;
                        }
                        self.reloadHtml(response);
                        if (pushState) {
                            window.history.pushState({url: response.url}, '', response.url);
                        }
                        self.initAjax();
                        $(document).trigger('amscroll_refresh');
                    } catch(e) {
                        window.location = this.url;
                    }
                },
                error: function(response) {
                    try {
                        if (response.getAllResponseHeaders() != '') {
                            location.reload();
                        }
                    } catch(e) {
                        window.location = this.url;
                    }
                }
            });
        },

        reloadHtml: function(data) {
            var selectSidebarNavigation = '.sidebar.sidebar-main .block.filter';
            var selectOneColumnNavigation = '.column.main .block.filter';

            var selectMainNavigation = false;
            if ($(selectSidebarNavigation).first().length > 0) {
                selectMainNavigation = selectSidebarNavigation;
            }
            if (!selectMainNavigation && $(selectOneColumnNavigation).first().length > 0) {
                selectMainNavigation = selectOneColumnNavigation;
                $('.am_shopby_apply_filters').remove();
            }
            if (!selectMainNavigation) {
                $('.sidebar.sidebar-main').first().prepend("<div class='block filter'></div>");
                selectMainNavigation = selectSidebarNavigation;
            }

            $(selectMainNavigation).first().replaceWith(data.navigation);
            $(selectMainNavigation).first().trigger('contentUpdated');

            $('.catalog-topnav .block.filter').first().replaceWith(data.navigationTop);
            $('.catalog-topnav .block.filter').first().trigger('contentUpdated');

            if (data.categoryProducts) {
                $('#amasty-shopby-product-list').replaceWith(data.categoryProducts);
                $('#amasty-shopby-product-list').trigger('contentUpdated');
            } else if (data.cmsPageData != '') {
                $('#amasty-shopby-product-list').replaceWith(data.cmsPageData);
                $('#amasty-shopby-product-list').trigger('contentUpdated');
            }

            $('#page-title-heading').closest('.page-title-wrapper').replaceWith(data.h1);
            $('#page-title-heading').trigger('contentUpdated');

            $('.breadcrumbs').replaceWith(data.breadcrumbs);
            $('.breadcrumbs').trigger('contentUpdated');

            $('title').html(data.title);
            if (data.categoryData != '') {
                if ($(".category-view").length == 0) {
                    $('<div class="category-view"></div>').insertAfter('.page.messages');
                }
                $(".category-view").replaceWith(data.categoryData);
            }

            mediaCheck({
                media: '(max-width: 768px)',
                entry: function(){
                    amShopbyTopFilters.moveTopFiltersToSidebar();
                },
                exit: function(){

                    amShopbyTopFilters.removeTopFiltersFromSidebar();
                }
            });

            this.$shopbyOverlay.hide();
            if (this.options.scrollUp) {
                $(document).scrollTop($('#amasty-shopby-product-list').offset().top);
            }
        },

        initAjax: function()
        {
            this.$shopbyOverlay = $("#amasty-shopby-overlay");
            //don't overlay top filters
            if ($('#narrow-by-list.filter-options').length == 2) {
                this.$shopbyOverlay.css('top', '140px');
                this.$shopbyOverlay.css('height', 'calc(100% - 140px');
            } else {
                this.$shopbyOverlay.css('top', '0');
                this.$shopbyOverlay.css('height', '100%');
            }

            var self = this;
            if ($.mage.productListToolbarForm) {
                //change page limit
                $.mage.productListToolbarForm.prototype.changeUrl = function (paramName, paramValue, defaultValue) {
                    // Workaround to prevent double method call
                    if (self.blockToolbarProcessing) {
                        return;
                    }
                    self.blockToolbarProcessing = true;
                    setTimeout(function() {
                        self.blockToolbarProcessing = false;
                    }, 300);

                    var decode = window.decodeURIComponent;
                    var urlPaths = this.options.url.split('?'),
                        urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                        paramData = {};
                    for (var i = 0; i < urlParams.length; i++) {
                        var parameters = urlParams[i].split('=');
                        paramData[decode(parameters[0])] = parameters[1] !== undefined
                            ? decode(parameters[1].replace(/\+/g, '%20'))
                            : '';
                    }
                    paramData[paramName] = paramValue;
                    if (paramValue == defaultValue) {
                        delete paramData[paramName];
                    }
                    self.options.clearUrl = self.getNewClearUrl(paramName, paramData[paramName] ? paramData[paramName] : '');

                    //add ajax call
                    $.mage.amShopbyFilterAbstract.prototype.prepareTriggerAjax();
                };
            }

            //change page number
            $(".toolbar .pages a").bind('click', function (e) {
                var newUrl = $(this).prop('href');
                var updatedUrl = null;

                var urlPaths = newUrl.split('?'),
                    urlParams = urlPaths[1].split('&');
                for (var i = 0; i < urlParams.length; i++) {
                    if (urlParams[i].indexOf("p=") === 0) {
                        var pageParam = urlParams[i].split('=');
                        updatedUrl = self.getNewClearUrl(pageParam[0], pageParam[1] > 1 ? pageParam[1] : '');
                        break;
                    }
                }

                $.mage.amShopbyFilterAbstract.prototype.prepareTriggerAjax(document, updatedUrl);

                e.stopPropagation();
                e.preventDefault();
            });
        },

        //Update url after change page size or current page.
        getNewClearUrl: function(param, value) {
            var urlPaths = this.options.clearUrl.replace('&amp;', '&').split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [param + '=' + value],
                replaced = false,
                paramData = {};
            for (var i = 0; i < urlParams.length; i++) {
                var parameters = urlParams[i].split('=');
                paramData[parameters[0]] = parameters[1];
                if (parameters[0] == param) {
                    if (value != '') {
                        paramData[parameters[0]] = value;
                    } else {
                        delete paramData[parameters[0]];
                    }
                    replaced = true;
                }
            }
            if (!replaced && value != '') {
                paramData[param] = value;
            }

            paramData = $.param(paramData);

            return baseUrl + (paramData.length ? '?' + paramData : '');
        },

    });

});
