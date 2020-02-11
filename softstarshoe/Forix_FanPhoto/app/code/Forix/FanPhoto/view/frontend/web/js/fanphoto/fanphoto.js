require(['jquery', 'domReady', 'mason'], function ($, domReady, Masonry) {
    "use strict";
    domReady(function () {
        setTimeout(function () {
            $('.block-fanphoto').removeClass('loading');
            var elem = document.querySelector('.grid');
            var msnry = new Masonry(elem, {
                itemSelector: '.grid-item'
            });
        }, 1000);
    });
    $('.grid').on('loadImageFanPhoto', function () {
        setTimeout(function () {
            var elem = document.querySelector('.grid');
            var msnry = new Masonry(elem, {
                itemSelector: '.grid-item'
            });
        }, 1000);
    });
    var countFlag;
    var target = $(".grid")[0];
    var observer = new MutationObserver(function (mutations) {
        // mutations.forEach(function (mutation) {
            if (mutations[0].target.children[0].children[0].children[0].children[0].tagName && (mutations[0].target.children[0].children[0].children[0].children[0].tagName == 'IMG')) {
                var newNodes = mutations[0].addedNodes; // DOM NodeList
                var count = 0;
                if (newNodes !== null) { // If there are new nodes added
                    $('.photo-item .img').each(function () {
                        var img = new Image();
                        img.onload = function () {
                            count++;
                            if($(".grid")[0].childElementCount == count && countFlag != $(".grid")[0].childElementCount) {
                                countFlag = $(".grid")[0].childElementCount;
                                setTimeout(function () {
                                    var elem = document.querySelector('.grid');
                                    var msnry = new Masonry(elem, {
                                        itemSelector: '.grid-item'
                                    });
                                }, 1000);
                            }
                        }
                        img.src = $(this).attr('src');
                    });
                }
            }
        // });
    });

// Configuration of the observer:
    var config = {
        attributes: true,
        characterData: true
    };
// Pass in the target node, as well as the observer options
    observer.observe(target, config);
});
