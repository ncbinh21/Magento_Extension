define(['jquery' , 'owl'], function ($ , owl) {
    "use strict";
    return function (config) {
        var callAjax = {};

        $('.category-link-autoload').ready(function (e) {

            var id = $('.category-link-autoload').data('cid');
            if (callAjax[id] === true) {
                return this;
            }
            $.ajax({
                url: config.urlPost,
                type: 'post',
                data: {category_id: $('.category-link-autoload').data('cid')},
                dataType: 'json',
                success: function (result) {
                    $('#category-' + id).html(result.html).show();
                    var owl = $('#category-' + id + ' .owl-homepage-product');
                    owl.owlCarousel({
                        nav: true,
                        dots: false,
                        mouseDrag: true,
                        touchDrag: true,
                        pullDrag: true,
                        lazyLoad: false,
                        responsive:{
                            0:{
                                items: 2
                            },
                            768:{
                                items: 4
                            }
                        }
                    });

                    var run;
                    var run2;

                    owl.find('.owl-next').on("mousedown", function(e) {
                        if (e.which === 1){
                            run = setInterval(function(){
                                $('.owl-next').trigger('click');
                            }, 400);
                        }
                    });
                    owl.find('.owl-next').on("mouseup", function(e)  {
                        if (e.which === 1){
                            clearInterval(run);
                        }
                    });
                    owl.find('.owl-prev').on("mousedown", function(e) {
                        if (e.which === 1){
                            run2 = setInterval(function(){
                                $('.owl-prev').trigger('click');
                            }, 400);
                        }
                    });
                    owl.find('.owl-prev').on("mouseup", function(e) {
                        if (e.which === 1){
                            clearInterval(run2);
                        }
                    });

                    // owl.on('mousewheel', '.owl-stage', function (e) {
                    //     if (e.deltaY>0) {
                    //         owl.trigger('next.owl');
                    //     } else {
                    //         owl.trigger('prev.owl');
                    //     }
                    //     e.preventDefault();
                    // });
                    callAjax[id] = true;
                }
            });
        });

        $('.category-link').each(function (e) {
            $(this).on('click', function (evt) {
                var id = $(this).data('cid');
                if (callAjax[id] === true) {
                    return this;
                }
                $.ajax({
                    url: config.urlPost,
                    type: 'post',
                    data: {category_id: $(this).data('cid')},
                    dataType: 'json',
                    success: function (result) {
                        $('#category-' + id).html(result.html).show();
                        var owl = $('#category-' + id + ' .owl-homepage-product');
                        owl.owlCarousel({
                            nav: true,
                            dots: false,
                            mouseDrag: true,
                            touchDrag: true,
                            pullDrag: true,
                            lazyLoad: false,
                            responsive:{
                                0:{
                                    items: 2
                                },
                                768:{
                                    items: 4
                                }
                            }
                        });
                        var run;
                        var run2;

                        owl.find('.owl-next').on("mousedown", function(e) {
                            if (e.which === 1){
                                run = setInterval(function(){
                                    $('.owl-next').trigger('click');
                                }, 400);
                            }
                        });
                        owl.find('.owl-next').on("mouseup", function(e)  {
                            if (e.which === 1){
                                clearInterval(run);
                            }
                        });
                        owl.find('.owl-prev').on("mousedown", function(e) {
                            if (e.which === 1){
                                run2 = setInterval(function(){
                                    $('.owl-prev').trigger('click');
                                }, 400);
                            }
                        });
                        owl.find('.owl-prev').on("mouseup", function(e) {
                            if (e.which === 1){
                                clearInterval(run2);
                            }
                        });

                        // owl.on('mousewheel', '.owl-stage', function (e) {
                        //     if (e.deltaY>0) {
                        //         owl.trigger('next.owl');
                        //     } else {
                        //         owl.trigger('prev.owl');
                        //     }
                        //     e.preventDefault();
                        // });
                        callAjax[id] = true;
                    }
                });

            })
        });

        $('ul.tabs').each(function(){
            var $active, $content, $links = $(this).find('a');
            $active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
            $active.addClass('active');

            $content = $($active[0].hash);

            $links.not($active).each(function () {
                $(this.hash).hide();
            });
            $(this).on('click', 'a', function(e){
                // Make the old tab inactive.
                $active.removeClass('active');
                $content.hide();

                // Update the variables with the new link and content
                $active = $(this);
                $content = $(this.hash);

                // Make the tab active.
                $active.addClass('active');
                $content.show();

                // Prevent the anchor's default click action
                e.preventDefault();
            });
        });
    }
});
