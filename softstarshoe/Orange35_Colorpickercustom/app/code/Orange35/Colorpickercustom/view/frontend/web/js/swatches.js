/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'jquery/ui',
    'jquery/jquery.parsequery',
    'slick'
], function ($, _) {
    'use strict';

    /**
     * Render tooltips by attributes (only to up).
     * Required element attributes:
     *  - value-type (integer, 0-3)
     *  - value-label (string)
     *  - value-tooltip-thumb
     *  - value-tooltip-value
     */
    $.widget('mage.ColorpickerSwatchRendererTooltip', {
        options: {
            delay: 200,                               //how much ms before tooltip to show
            tooltipClass: 'o35-swatch-value-tooltip'  //configurable, but remember about css
        },

        /**
         * @private
         */
        _init: function () {
            var $widget = this,
                $this = this.element,
                $element = $('.' + $widget.options.tooltipClass),
                timer,
                type = parseInt($this.attr('value-type'), 10),
                label = $this.attr('value-label'),
                tooltipType = $this.attr('value-tooltip-type'),
                thumb = $this.attr('value-tooltip-thumb'),
                value = $this.attr('value-tooltip-value'),
                width = $this.attr('value-tooltip-width'),
                height = $this.attr('value-tooltip-height'),
                padding = $this.attr('value-tooltip-padding'),
                $image,
                $title,
                $corner;

            if (!$element.size()) {
                $element = $('<div class="' +
                    $widget.options.tooltipClass + '" style="padding:' + padding +
                    'px; max-width:' + (+width + +padding) + 'px"><div class="title"></div><div class="corner"></div></div>'
                );
                $('body').append($element);
            }

            $image = $element.find('.image');
            $title = $element.find('.title');
            $corner = $element.find('.corner');

            $this.hover(function () {
                timer = setTimeout(
                    function () {
                        var leftOpt = null,
                            leftCorner = 0,
                            left,
                            $window;

                        if (tooltipType != 0) {
                            if (tooltipType == 2) {
                                if (type === 2) {
                                    // Image
                                    $image.css({
                                        'background': 'url("' + thumb + '") no-repeat center', //Background case
                                        'background-size': 'initial',
                                        'width': width,
                                        'height': height
                                    });
                                    $image.show();
                                } else if (type === 1) {
                                    // Color
                                    $image.css({
                                        'background': value,
                                        'background-size': 'initial',
                                        'width': width,
                                        'height': height
                                    });
                                    $image.show();
                                } else if (type === 3) {
                                    // Default
                                    $image.hide();
                                }
                            }
                            else {
                                $image.css({
                                    'width': 0,
                                    'height': 0,
                                    'background': 'none'
                                });
                                $image.show();
                            }

                            $title.text(label);

                            leftOpt = $this.offset().left;
                            left = leftOpt + $this.width() / 2 - $element.width() / 2;
                            $window = $(window);

                            // the numbers (5 and 5) is magick constants for offset from left or right page
                            if (left < 0) {
                                left = 5;
                            } else if (left + $element.width() > $window.width()) {
                                left = $window.width() - $element.width() - 5;
                            }

                            // the numbers (6,  3 and 18) is magick constants for offset tooltip
                            leftCorner = 0;

                            if ($element.width() < $this.width()) {
                                leftCorner = $element.width() / 2 - 3;
                            } else {
                                leftCorner = (leftOpt > left ? leftOpt - left : left - leftOpt) + $this.width() / 2 - 6;
                            }

                            $corner.css({
                                left: leftCorner
                            });
                            $element.css({
                                left: left,
                                top: $this.offset().top - $element.height() - $corner.height() - 18
                            }).show();
                        }
                    },
                    $widget.options.delay
                );
            }, function () {
                $element.hide();
                clearTimeout(timer);
            });
            $(document).on('tap', function () {
                $element.hide();
                clearTimeout(timer);
            });

            $this.on('tap', function (event) {
                event.stopPropagation();
            });
        }
    });

    /**
     * Render swatch controls with options and use tooltips.
     * Required two json:
     *  - jsonConfig (magento's option config)
     *
     */
    $.widget('mage.ColorpickerSwatchRenderer', {
        options: {
            classes: {
                optionClass: 'o35-swatch-option',
                optionSelectedValueLabelClass: 'o35-swatch-option-selected-value',
                optionInput: 'o35-swatch-input',
                optionInputArea: 'o35-swatch-input-area',
                valueClass: 'o35-swatch-value',
                optionLabelClass: 'o35-swatch-option-label'
            },

            jsonConfig: {},

            jsonSliderConfig: {},

            // selector of category product tile wrapper
            selectorProductTile: '.product-item'
        },

        /**
         * @private
         */
        _init: function () {
            this._RenderControls();
            this._InitCarousel();
        },

        /**
         * @private
         */
        _create: function () {
            this.productForm = this.element.parents(this.options.selectorProductTile).find('form:first');
        },

        /**
         * Render controls
         *
         * @private
         */
        _RenderControls: function () {
            var $widget = this,
                container = this.element,
                classes = this.options.classes;

            // Connect Tooltip
            container
                .find('[value-type="1"], [value-type="2"], [value-type="3"]')
                .ColorpickerSwatchRendererTooltip();

            // Handle events like click or change
            $widget._EventListener();
        },

        /**
         * Init carousel for options
         *
         * @private
         */
        _InitCarousel: function () {
            var $widget = this,
                container = this.element;

            //Init carousel for each option
            container.find('.carousel-swatches').each(function () {
                // console.info($(this));
                // console.info($(this).find('.left'));
                var $container = $(this),
                    $carousel = $($container.find('.carousel-inner')),
                    slidesToShow = parseInt($widget.options.jsonSliderConfig['slidesShow']),
                    slidesToScroll = parseInt($widget.options.jsonSliderConfig['slidesStep']),
                    swatchesPerItem = parseInt($widget.options.jsonSliderConfig['swatchesPerItem']),
                    originSlidesToShow = slidesToShow,
                    arrows = 10;

                //Setting number of slides to be shown to correspond to a window size if there is not enough space to show them all
                if (slidesToShow != 1) {
                    var calculated = Math.floor(($container.outerWidth(true) - arrows) / $($carousel.find('.o35-swatch-value')[0]).outerWidth(true));
                    var itemWidth = $($carousel.find('.o35-swatch-value')[0]).outerWidth(true);
                    // $carousel.css({width: slidesToShow*itemWidth});
                    slidesToShow = Math.min(slidesToShow, calculated);
                    $carousel.css({width: slidesToShow*itemWidth});
                } else {
                    //Setting adaptive width for slider to fit its width to a number of swatches
                    $carousel.on('init', function(){
                        var itemWidth = $($carousel.find('.o35-swatch-value')[0]).outerWidth(true),
                            carouselWidth = $carousel.width(),
                            totalWidth = (itemWidth * swatchesPerItem) + 2;

                        //If swatches come at rows, set slider width to fit number of swatches in one row
                        if(carouselWidth < totalWidth) {
                            totalWidth = (itemWidth * Math.floor((carouselWidth-2) / itemWidth)) + 2;
                        }

                        $carousel.css({width: totalWidth});
                    });
                }

                //Setting slidesToScroll to fit total slide numbers if slidesToScroll is > then number of slides
                if(slidesToScroll != 1) {
                    var total = $carousel.find('.item').length - 1;
                    slidesToScroll = Math.min(total, slidesToScroll);
                }

                $carousel.slick({
                    infinite: false,
                    cssEase: 'linear',
                    speed: 200,
                    slidesToShow: slidesToShow,
                    slidesToScroll: slidesToScroll,
                    respondTo: 'slider',
                    appendArrows: this,
                    prevArrow: '<div class="view arrow left"><a class="prev">‹</a></div>',
                    nextArrow: '<div class="view arrow right"><a class="next">›</a></div>',
                    dots: false
                });

                //Setting number of slides to be show to correspond to a window size if there is not enough space to show them all
                $(window).on('resize orientationchange', function () {
                    //TODO: use functions for resize and initial load
                    if (originSlidesToShow != 1) {
                        var calculated = Math.floor(($container.outerWidth(true) - arrows) / $($carousel.find('.o35-swatch-value')[0]).outerWidth(true));
                        slidesToShow = Math.min(originSlidesToShow, calculated);
                        $carousel.slick("slickSetOption", "slidesToShow", slidesToShow, true);
                        var itemWidth = $($carousel.find('.o35-swatch-value')[0]).outerWidth(true);
                        $carousel.css({width: slidesToShow*itemWidth});
                    } else {
                        //Setting adaptive width for slider to fit its width to a number of swatches
                        var itemWidth = $($carousel.find('.o35-swatch-value')[0]).outerWidth(true),
                            totalWidth = (itemWidth * swatchesPerItem) + 2;

                        //Setting width to 100% for later calculations
                        $carousel.css({width: '100%'});

                        //Reinit of slick
                        $carousel.parent('.carousel-swatches').find('.arrow').each(function(){
                            this.remove();
                        });
                        $carousel.slick('reinit');

                        //If swatches come at rows, set slider width to fit number of swatches in one row
                        var carouselWidth = $carousel.width();
                        if(carouselWidth < totalWidth) {
                            totalWidth = (itemWidth * Math.floor((carouselWidth-2) / itemWidth)) + 2;
                        }

                        $carousel.css({width: totalWidth});
                    }
                });
                $carousel.slick('setPosition');
            });
        },

        /**
         * Event listener
         *
         * @private
         */
        _EventListener: function () {
            var $widget = this;

            $widget.element.on('click', '.' + this.options.classes.valueClass, function () {
                return $widget._OnClick($(this), $widget);
            });
        },

        /**
         * Event for swatch options
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnClick: function ($this, $widget) {
            var $parent = $this.parents('.' + $widget.options.classes.optionClass),
                require = $parent.find('.' + $widget.options.classes.optionLabelClass).is('[data-required="1"]'),
                $label = $parent.find('.' + $widget.options.classes.optionSelectedValueLabelClass),
                $select = $parent.find('.product-custom-option'),
                values = [],
                $swatch;

            if ($select.is('[multiple]')) {
                //Clearing out input area, selected swatches ids and titles
                $label.text('');
                $parent.attr('value-selected', '');

                //Toggling select class
                $this.hasClass('selected') ? $this.removeClass('selected') : $this.addClass('selected');

                //For all swatches that are selected
                $parent.find('.selected').each(function (index) {
                    $swatch = $(this);

                    //Appending swatch title to label
                    if (index === 0)
                        $label.append($swatch.attr('value-label'));
                    else
                        $label.append(', ' + $swatch.attr('value-label'));

                    //Appending swatch id to value-selected attribute
                    $parent.attr('value-selected', $parent.attr('value-selected') + $swatch.attr('value-id') + ' ');
                    values.push($swatch.attr('value-id'));
                });
                $select.val(values);
            } else {
                if ($this.hasClass('selected')) {
                    $parent.removeAttr('value-selected').find('.selected').removeClass('selected');
                    $select.val('');
                    $label.html('');
                    $this.closest('.field').removeClass('selected');
                } else {
                    $parent.attr('value-selected', $this.attr('value-id')).find('.selected').removeClass('selected');
                    var image = '<img src="'+ $this.attr('value-tooltip-thumb') +'"/>';
                    $label.text($this.attr('value-label'));
                    $label.append(image);
                    $select.val($this.attr('value-id'));
                    $this.addClass('selected');
                    $this.closest('.field').addClass('selected');
                }
            }
            $select.trigger('change');
        }
    });

    return $.mage.ColorpickerSwatchRenderer;
});
