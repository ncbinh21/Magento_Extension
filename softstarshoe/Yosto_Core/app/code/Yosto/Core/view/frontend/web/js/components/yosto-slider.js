/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

define(
    ['jquery', 'yostocoreowlcarousel'],
    function ($) {
        return function (config, element) {
            if (config.options.animateOut) {
                config.options.animateOut = '' + config.options.animateOut;
            }
            if (config.options.animateIn) {
                config.options.animateIn = '' + config.options.animateIn;
            }
            var element = config.elementClass;

            $(element).owlCarousel(config.options);

        }
    }
)