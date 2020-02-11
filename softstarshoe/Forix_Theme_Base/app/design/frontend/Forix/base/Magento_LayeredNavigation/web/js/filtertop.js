/**
 * Created by Eric on 3/2/2016.
 */
define([
    "jquery",
    "forix/toggle",
    'forix/scroll'
], function($) {
    "use strict";

    $('#fgo').click(function(e) {
        e.preventDefault();

        var url = window.location.href;
        var pFrom = $(this).closest('form').find('#price_from').val();
        var pTo = $(this).closest('form').find('#price_to').val();
        if(pFrom == '' && pTo == '') {
            $(this).closest('form').find('#price_from').focus();
            return false;
        }

        var p = '';
        if(pFrom != '' && pTo != '') {
            p = pFrom + '-' + pTo;
        }
        else if(pFrom != '') {
            p = pFrom + '-';
        }
        else {
            p = '-' + pTo;
        }

        url = paramReplace('price', url, p);
        window.location.href = url;
    });

    function paramReplace(name, string, value) {
        var re = new RegExp("[\\?&]" + name + "=([^&#]*)");
        var matches = re.exec(string);
        var newString;

        if (matches === null) {
            if(string.indexOf('?') != -1) {
                newString = string + '&' + name + '=' + value;
            }
            else {
                newString = string + '?' + name + '=' + value;
            }
        } else {
            var delimeter = matches[0].charAt(0);
            newString = string.replace(re, delimeter + name + "=" + value);
        }
        return newString;
    }

    // Reset Jscrollpane
    if($('.filter-subtitle').length){
        $('.filter-subtitle').toggleAdvanced({
            "afterToggle": function(){
                if ($('.option-select-content.have-scroll').data('forixScrollData')) {
                    $('.option-select-content.have-scroll').scrollData('updateScroll');
                }
            }
        });
    }
});