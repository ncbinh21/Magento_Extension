/**
 * Created by Eric on 6/6/2016.
 */
define([
    "jquery",
    'mage/translate',
    'matchMedia',
    'domReady!',
    "Magento_Catalog/js/catalog-add-to-cart"
], function($, $t){
    "use strict";
    $.widget("mage.catalogAddToCartExt", $.mage.catalogAddToCart, {
        enableAddToCartButton: function(form) {
            var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Added');
            var self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.find('span').text(addToCartButtonTextAdded);
            addToCartButton.attr('title', addToCartButtonTextAdded);

            setTimeout(function() {
                var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Add to Cart');
                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextDefault);
                addToCartButton.attr('title', addToCartButtonTextDefault);
            }, 1000);

            //hide messages
            setTimeout(function() {
                $('[class="messages"]').hide('slow', function(){ $(this).html('').show(); });
            }, 4000);

            // Show dropdown cart after added success
            //open minicart after adding product to cart
            if($(window).width()>767){
                $('.minicart-wrapper').addClass('active');
            }
            //scroll to top
            $('html,body').animate({scrollTop:0}, 1000);
        }
    });

    return $.mage.catalogAddToCartExt;
});