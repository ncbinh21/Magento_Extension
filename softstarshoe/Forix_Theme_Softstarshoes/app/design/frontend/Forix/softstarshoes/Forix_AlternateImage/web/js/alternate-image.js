define([
    "jquery",
    'jquery/ui'
], function ($) {
    "use strict";

    $.widget("mage.alternateImage", {
        options: {
            productItemClasses: '.product-image-container',
            productImageWrapper: '.product-image-wrapper',
            productImagePhoto: '.product-image-photo',
            loadingImageUrl: '',
            img: undefined
        },
        _create: function () {
            this._alternateImage();
        },
        _loadingImage: function (targetElement, show) {
            if (undefined === this.img) {
                this.img = $('<img class="loading alt-image" alt="Please wait while your content is loading."/>');
                var loadingImage = window.loadingImageGif;
                if (this.options.loadingImageUrl) {
                    loadingImage = this.options.loadingImageUrl;
                }
                this.img.attr('src', loadingImage);
            }
            if (show) {
                var wrapper = $(targetElement).closest(this.options.productImageWrapper);
                wrapper.append(this.img);
            } else {
                this.img.remove();
                this.img = undefined;
            }
        },
        _replaceImage: function (element, newUrl) {
            var self = this;
            if (newUrl) {
                self._loadingImage(element,true);
                element.attr('src', newUrl);
                element.hide();
                element.load(function () {
                    self._loadingImage(element, false);
                    element.fadeIn( 200, function() {
                        // Animation complete
                    });
                });
            }
        },
        _mouseEffect: function (e, enter) {
            var self = this, currentItem = $(e).find(self.options.productImagePhoto),
                newUrl = currentItem.data('alt-src');
            if (newUrl != '') {
                if(!currentItem.data('alt-source')) {
                    var oldUrl =  currentItem.attr('src');
                    currentItem.data('alt-source', oldUrl);
                }
                if(!enter){
                    self._replaceImage(currentItem, currentItem.data('alt-source'));
                    return false;
                }
                self._replaceImage(currentItem, newUrl);
            }
        },
        _alternateImage: function () {
            var self = this, element = this.element, options = this.options;
            element.find(options.productItemClasses).bind('mouseenter mouseleave', function (e) {
                self._mouseEffect(this, 'mouseenter' == e.type);
            });
        }
    });
});