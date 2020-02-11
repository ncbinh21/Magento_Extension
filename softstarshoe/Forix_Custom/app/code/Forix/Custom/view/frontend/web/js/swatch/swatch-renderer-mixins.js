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
    'mage/validation/validation',
    'owl'
], function ($, _, mageTemplate, keyboardHandler, $t, priceUtils) {
    'use strict';
    return function (SwatchRenderer) {
        $.widget('mage.SwatchRenderer', SwatchRenderer, {

            /**
             * @private
             */
            _init: function () {
                if (_.isEmpty(this.options.jsonConfig.images)) {
                    this.options.useAjax = true;
                    // creates debounced variant of _LoadProductMedia()
                    // to use it in events handlers instead of _LoadProductMedia()
                    this._debouncedLoadProductMedia = _.debounce(this._LoadProductMedia.bind(this), 500);
                }

                if (this.options.jsonConfig !== '' && this.options.jsonSwatchConfig !== '') {
                    // store unsorted attributes
                    this.options.jsonConfig.mappedAttributes = _.clone(this.options.jsonConfig.attributes);
                    this._sortAttributes();
                    this._RenderControls();
                    if (!this.inProductList) {
                        $('.owl-color').owlCarousel({
                            nav: true,
                            margin: 9,
                            navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
                            mouseDrag: true,
                            responsive:{
                                0:{
                                    items: 3
                                },
                                640:{
                                    items: 4
                                },
                                768:{
                                    items: 3
                                },
                                1366:{
                                    items: 4
                                }
                            }
                        });
                    }
                    this._setPreSelectedGallery();
                    $(this.element).trigger('swatch.initialized');
                } else {
                    console.log('SwatchRenderer: No input data received');
                }
                this.options.tierPriceTemplate = $(this.options.tierPriceTemplateSelector).html();
            },

            /**
             * Render controls
             *
             * @private
             */
            _RenderControls: function () {
                var $widget = this,
                    container = this.element,
                    classes = this.options.classes,
                    chooseText = this.options.jsonConfig.chooseText;

                $widget.optionsMap = {};

                $.each(this.options.jsonConfig.attributes, function () {
                    var color = '';
                    var device = false;
                    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                        device = true;
                    }
                    var item = this,
                        controlLabelId = 'option-label-' + item.code + '-' + item.id,
                        options = $widget._RenderSwatchOptions(item, controlLabelId, device),
                        select = $widget._RenderSwatchSelect(item, chooseText),
                        input = $widget._RenderFormInput(item),
                        listLabel = '',
                        label = '';

                    // Show only swatch controls
                    if ($widget.options.onlySwatches && !$widget.options.jsonSwatchConfig.hasOwnProperty(item.id)) {
                        return;
                    }

                    if ($widget.options.enableControlLabel) {
                        label +=
                            '<span id="' + controlLabelId + '" class="' + classes.attributeLabelClass + '">' +
                            item.label +
                            '</span>' +
                            '<span class="' + classes.attributeSelectedOptionLabelClass + '"></span>';
                    }

                    if ($widget.inProductList) {
                        $widget.productForm.append(input);
                        input = '';
                        listLabel = 'aria-label="' + item.label + '"';
                    } else {
                        listLabel = 'aria-labelledby="' + controlLabelId + '"';
                    }
                    if (!$widget.inProductList) {
                        if ('color' === item.code) {
                            if(item.options.length > 8 && !device || item.options.length > 6 && device) {
                                color = 'owl-color owl-carousel';
                            }
                        }
                    }
                    // Create new control
                    var customLink = '';
                    if($widget.options.customizeLink){
                        customLink = '<a class="customize-shoe-color" href="' +  $widget.options.customizeLink + '">customize your shoe color</a>';
                    }
                    container.append(
                        '<div class="' + classes.attributeClass + ' ' + item.code + '" ' +
                        'attribute-code="' + item.code + '" ' +
                        'attribute-id="' + item.id + '">' +
                        label +
                        '<div aria-activedescendant="" ' +
                        'tabindex="0" ' +
                        'aria-invalid="false" ' +
                        'aria-required="true" ' +
                        'role="listbox" ' + listLabel +
                        'class="' + classes.attributeOptionsWrapper + ' clearfix ' + color + '">' +
                        options + select +
                        '</div>' + input +
                        '</div>' + customLink
                    );
                    container.append(customLink);
                    $widget.optionsMap[item.id] = {};

                    // Aggregate options array to hash (key => value)
                    $.each(item.options, function () {
                        if (this.products.length > 0) {
                            $widget.optionsMap[item.id][this.id] = {
                                price: parseInt(
                                    $widget.options.jsonConfig.optionPrices[this.products[0]].finalPrice.amount,
                                    10
                                ),
                                products: this.products
                            };
                        }
                    });
                });

                // Connect Tooltip
                container
                    .find('[option-type="1"], [option-type="2"], [option-type="0"], [option-type="3"]')
                    .SwatchRendererTooltip();

                // Hide all elements below more button
                var i=0;
                var hideInter = setInterval(function(){
                    $('.button-swatch-more').nextAll().hide();
                    i++;
                    if(i == 10) clearInterval(hideInter);
                },500);

                // Handle events like click or change
                $widget._EventListener();

                // Rewind options
                $widget._Rewind(container);

                //Emulate click on all swatches from Request
                $widget._EmulateSelected($.parseQuery());
                $widget._EmulateSelected($widget._getSelectedAttributes());
            },
            /**
             * Render swatch options by part of config
             *
             * @param {Object} config
             * @param {String} controlId
             * @returns {String}
             * @private
             */
            _RenderSwatchOptions: function (config, controlId, device) {
                var $widget = this,
                    optionConfig = this.options.jsonSwatchConfig[config.id],
                    optionClass = this.options.classes.optionClass,
                    moreLimit = parseInt(this.options.numberToShow, 10),
                    moreClass = this.options.classes.moreButton,
                    moreButtonUrl = (undefined !== this.options.moreButtonUrl)?this.options.moreButtonUrl:'#',
                    moreText = this.options.moreButtonText,
                    countAttributes = 0,
                    html = '';

                if (!this.options.jsonSwatchConfig.hasOwnProperty(config.id)) {
                    return '';
                }
                var flag = true;
                var countItem = config.options.length % 2;
                var lastItem = config.options.slice(-1)[0].id;
                $.each(config.options, function () {
                    var id,
                        type,
                        value,
                        thumb,
                        label,
                        attr;

                    if (!optionConfig.hasOwnProperty(this.id)) {
                        return '';
                    }

                    // Add more button
                    if (moreLimit === countAttributes++) {
                        var newMoreText = moreText.replace('%label',$t('Colors'));/*config.label);*/
                        newMoreText = newMoreText.replace('%count', "" + config.options.length - moreLimit);
                        html += '<div class="button-swatch-more"><a href="'+ moreButtonUrl +'" class="' + moreClass + '">' + newMoreText + '</a></div>';
                    }

                    id = this.id;
                    type = parseInt(optionConfig[id].type, 10);
                    value = optionConfig[id].hasOwnProperty('value') ? optionConfig[id].value : '';
                    thumb = optionConfig[id].hasOwnProperty('thumb') ? optionConfig[id].thumb : '';
                    label = this.label ? this.label : '';
                    attr =
                        ' id="' + controlId + '-item-' + id + '"' +
                        ' aria-checked="false"' +
                        ' aria-describedby="' + controlId + '"' +
                        ' tabindex="0"' +
                        ' option-type="' + type + '"' +
                        ' option-id="' + id + '"' +
                        ' option-label="' + label + '"' +
                        ' aria-label="' + label + '"' +
                        ' option-tooltip-thumb="' + thumb + '"' +
                        ' option-tooltip-value="' + value + '"' +
                        ' role="option"';

                    if (!this.hasOwnProperty('products') || this.products.length <= 0) {
                        attr += ' option-empty="true"';
                    }
                    if (!$widget.inProductList) {
                        switch (type) {
                            case 1:
                            case 2:
                                if (flag) {
                                    html += '<div class="item">';
                                }
                                break;
                        }
                    }
                    if (type === 0) {
                        // Text
                        html += '<div class="' + optionClass + ' text" ' + attr + '>' + (value ? value : label) +
                            '</div>';
                    } else if (type === 1) {
                        // Color
                        html += '<div class="' + optionClass + ' color" ' + attr +
                            ' style="background: ' + value +
                            ' no-repeat center; background-size: initial;">' + '' +
                            '</div>';
                    } else if (type === 2) {
                        // Image
                        html += '<div class="' + optionClass + ' image" ' + attr +
                            ' style="background: url(' + value + ') no-repeat center; background-size: initial;">' + '' +
                            '</div>';
                    } else if (type === 3) {
                        // Clear
                        html += '<div class="' + optionClass + '" ' + attr + '></div>';
                    } else {
                        // Default
                        html += '<div class="' + optionClass + '" ' + attr + '>' + label + '</div>';
                    }
                    if(config.options.length < 9 && !device || config.options.length < 7 && device) {
                        flag = !flag;
                    } else {
                        if(countItem == 1 && lastItem == id ) {
                            flag = !flag;
                        }
                    }
                    if (!$widget.inProductList) {
                        switch (type) {
                            case 1:
                            case 2:
                                if (!flag) {
                                    html += '</div>';
                                }
                                break;
                        }
                    }
                    flag = !flag;
                });
                return html;
            },

            /**
             * Event listener
             *
             * @private
             */
            _EventListener: function () {
                var $widget = this,
                    options = this.options.classes,
                    target;

                $widget.element.on('click', '.' + options.optionClass, function () {
                    return $widget._OnClick($(this), $widget);
                });

                $widget.element.on('emulateClick', '.' + options.optionClass, function () {
                    return $widget._OnClick($(this), $widget, 'emulateClick');
                });

                $widget.element.on('change', '.' + options.selectClass, function () {
                    return $widget._OnChange($(this), $widget);
                });

                $widget.element.on('keydown', function (e) {
                    if (e.which === 13) {
                        target = $(e.target);

                        if (target.is('.' + options.optionClass)) {
                            return $widget._OnClick(target, $widget);
                        } else if (target.is('.' + options.selectClass)) {
                            return $widget._OnChange(target, $widget);
                        }
                    }
                });
            }
        });
        return $.mage.SwatchRenderer;
    };
});
