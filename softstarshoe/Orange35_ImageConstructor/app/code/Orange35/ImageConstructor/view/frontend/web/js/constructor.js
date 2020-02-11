define([
    'jquery',
    'underscore',
    'mage/gallery/gallery',
    'magnifier/magnifier'
], function ($, _, gallery) {
    "use strict";

    $.widget('orange35.imageConstructor', {
        options: {
            productFormSelector: '#product_addtocart_form',
            mediaGalleryPlaceholderSelector: '[data-gallery-role=gallery-placeholder]',
            mediaGallerySelector: '[data-gallery-role="gallery"]',
            imageHolderSelector: '.fotorama__stage__frame:first',
            imageSelector: '.fotorama__img',
            imageZoomSelector: '.fotorama__img--full'
        },
        chosenImages: {},

        _create: function () {
            this.prepareLayers(this.getFormValues($(this.options.productFormSelector)));
            this.applyLayers();
            this.applyLayers2Zoom();
        },

        applyLayers: function () {
            var _this = this;
            $(this.options.productFormSelector).change(function () {
                _this.prepareLayers(_this.getFormValues(this));
                _this.addLayers(_this.options.imageSelector, 'mediumImage');
            });
        },

        applyLayers2Zoom: function () {
            var _this = this;
            $(this.options.mediaGalleryPlaceholderSelector).on('gallery:loaded', function () {
                $(_this.options.mediaGallerySelector).on('fotorama:fullscreenenter', function () {
                    _this.addLayers(_this.options.imageZoomSelector, 'largeImage');
                });
                $(_this.options.mediaGallerySelector).on('fotorama:load', function () {
                    _this.addLayers(_this.options.imageSelector, 'mediumImage');
                    _this.addLayers(_this.options.imageZoomSelector, 'largeImage');
                });
            });
        },

        addLayers: function (templateSelector, image) {
            var imageHolder = $(this.options.imageHolderSelector);
            var imageNode = $(templateSelector).first();
            $(this.options.imageHolderSelector + ' ' + templateSelector).slice(1).remove();
            _(this.chosenImages).each(function (layer) {
                imageHolder.append(imageNode.clone().attr('src', layer[image]));
            });
        },

        prepareLayers: function(formValues) {
            var chosenImages = _.pick(this.options.layers, function (value) {
                return !(_.isNull(value.largeImage)) && _.contains(formValues, value.optionId);
            });
            _.sortBy(chosenImages, function (o) {
                return o.sortOrderOption;
            });
            this.chosenImages = chosenImages;
        },

        getFormValues: function (form) {
            var values = [];
            _.each($(form).serializeArray(), function (v) {
                if (v.name.startsWith("options") && v.value != "") {
                    values = _(values).union([v.value]);
                }
            });
            return values;
        }


    });

    return $.orange35.imageConstructor;
});
