/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/template',
    'mage/smart-keyboard-handler',
    'mage/translate',
    'priceUtils',
    'jquery/ui',
    'jquery/jquery.parsequery',
    'mage/validation/validation'
], function ($, _, mageTemplate, keyboardHandler, $t, priceUtils) {
    'use strict';
    return function (SwatchRenderer) {
        $.widget('mage.SwatchRenderer', SwatchRenderer, {

            /**
             * Callback for product media
             *
             * @param {Object} $this
             * @param {String} response
             * @param {Boolean} isInProductView
             * @private
             */
            _ProductMediaCallback: function ($this, response, isInProductView) {
                var $main = isInProductView ? $this.parents('.column.main') : $this.parents('.product-item-info'),
                    $widget = this,
                    images = [],

                    /**
                     * Check whether object supported or not
                     *
                     * @param {Object} e
                     * @returns {*|Boolean}
                     */
                    support = function (e) {
                        return e.hasOwnProperty('large') && e.hasOwnProperty('medium') && e.hasOwnProperty('small');
                    };

                if (_.size($widget) < 1 || !support(response)) {
                    this.updateBaseImage(this.options.mediaGalleryInitial, $main, isInProductView);

                    return;
                }

                images.push({
                    full: response.large,
                    img: response.medium,
                    thumb: response.small,
                    altSrc: response.altSrc,
                    isMain: true
                });

                if (response.hasOwnProperty('gallery')) {
                    $.each(response.gallery, function () {
                        if (!support(this) || response.large === this.large) {
                            return;
                        }
                        images.push({
                            full: this.large,
                            img: this.medium,
                            altSrc: response.altSrc,
                            thumb: this.small
                        });
                    });
                }

                this.updateBaseImage(images, $main, isInProductView);
            },

            /**
             * Update [gallery-placeholder] or [product-image-photo]
             * @param {Array} images
             * @param {jQuery} context
             * @param {Boolean} isInProductView
             * @param {Object} gallery
             */
            processUpdateBaseImage: function (images, context, isInProductView, gallery) {
                var justAnImage = images[0],
                    initialImages = this.options.mediaGalleryInitial,
                    imagesToUpdate,
                    isInitial;

                if (isInProductView) {
                    imagesToUpdate = images.length ? this._setImageType($.extend(true, [], images)) : [];
                    isInitial = _.isEqual(imagesToUpdate, initialImages);

                    if (this.options.gallerySwitchStrategy === 'prepend' && !isInitial) {
                        imagesToUpdate = imagesToUpdate.concat(initialImages);
                    }

                    imagesToUpdate = this._setImageIndex(imagesToUpdate);
                    gallery.updateData(imagesToUpdate);

                    if (isInitial) {
                        $(this.options.mediaGallerySelector).AddFotoramaVideoEvents();
                    } else {
                        $(this.options.mediaGallerySelector).AddFotoramaVideoEvents({
                            selectedOption: this.getProduct(),
                            dataMergeStrategy: this.options.gallerySwitchStrategy
                        });
                    }

                    gallery.first();
                } else if (justAnImage && justAnImage.img) {
                    if(justAnImage.altSrc){
                        //altImage
                        context.find('.product-image-photo').data('alt-src', justAnImage.altSrc);
                    }
                    context.find('.product-image-photo').attr('src', justAnImage.img).data('alt-source', justAnImage.img);
                }
            },
        });
        return $.mage.SwatchRenderer;
    };
});
