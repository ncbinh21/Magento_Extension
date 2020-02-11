define([
    'jquery',
    'matchMedia',
    "jquery/ui",
    'domReady!'
], function($, domReady){
    "use strict";

    $.widget('forix.sliderTable', {
        options: {
            numItem: 4,
            numItemTablet : 3,
            tableCls: 'table.table-comparison',
            item: 'td.cell',
            rows: '.table-comparison > thead > tr,.table-comparison > tbody > tr',
            controls: true,
            txtNext: "Next",
            txtPrev: "Prev",
            minW: 227 ,// auto|number 123
            minWTablet: 190,
            customResize: false
        },

        _create: function() {
            var seft = this;
            this._initSlider();

            if(!this.options.customResize){
                $(window).resize(function () {
                    seft.resize();
                });
            }else{
                this._trigger('resizeData');
            }
        },

        _initSlider: function(){
            var seft = this,
                ele= this.element,
                options= this.options,
                numItem= this.options.numItem;

            ele.addClass("slider-table");
            ele.prepend("<div class='slider-table-block'></div>");

            mediaCheck({
                media: '(max-width: 1023px)',
                entry: $.proxy(function() {
                    seft.element.addClass('slider-tablet');
                    seft.numItem = options.numItemTablet;
                    seft.minW = options.minWTablet;
                }, this),
                exit: $.proxy(function() {
                    if(seft.element.hasClass('slider-tablet')){
                        seft.element.removeClass('slider-tablet');
                    }
                    seft.numItem = options.numItem;
                    seft.minW = options.minW;
                }, this)
            });

            if(this.numItem>0){
                ele.find(options.rows).addClass("basic-row");
                ele.find(options.rows).each(function(){
                    $(this).find(options.item).addClass("basic-item");
                    ele.attr('data-limit',$(this).find(options.item).length);
                    ele.attr('data-prev',-1);
                    ele.attr('data-move',0);
                    ele.attr('data-next',this.numItem);
                });
            }
            this._controls();
            this._appendTitle();
            this._calSlider();
            this._trigger('afterLoad');
        },

        _calSlider: function(){
            var ele= this.element,
                options= this.options,
                seft= this,
                next= parseInt(ele.attr('data-next')),
                prev= parseInt(ele.attr('data-prev')),
                move= parseInt(ele.attr('data-move')),
                limit= parseInt(ele.attr('data-limit'));

            this._setWidth(seft.numItem);
            if(seft.numItem>0){
                ele.find(options.rows).each(function(){
                    $(this).find(options.item).each(function(index){
                        if( index >= seft.numItem){
                            $(this).addClass('hide');
                        }else{
                            $(this).addClass('show');
                        }
                        if(index === 0){
                            $(this).addClass('first');
                        }
                    });
                });
            }
            if(seft.numItem < limit){
                if((move + seft.numItem) > limit){
                    ele.attr('data-move',(limit - seft.numItem));
                    ele.attr('data-prev',(limit - seft.numItem -1));
                }else{
                    ele.attr('data-next',(seft.numItem + move));
                }
            }
            this._calTitle();
            this._resetControls();
        },

        _controls: function(){
            var ele = this.element,
                options = this.options,
                isTouchDevice = typeof document.ontouchstart !== 'undefined',
                typeEvent = 'click',
                seft = this;

            if(options.controls){
                var controls="<div class='controls'><button class='slider-arrow slider-prev' title='"+options.txtPrev+"'><span>"+options.txtPrev+"</span></button><button class='slider-arrow slider-next' title='"+options.txtNext+"'><span>"+options.txtNext+"</span></button></div>";
                ele.prepend(controls);
            }
            if(isTouchDevice){
                typeEvent = 'click touchstart';
            }
            $('.slider-prev').on(typeEvent, $.proxy(function(e) {
                seft._prevSlide(e);
            }, this));
            $('.slider-next').on(typeEvent, $.proxy(function(e) {
                seft._nextSlide(e);
            }, this));
        },

        _resetSlider: function(){
            var ele= this.element,
                options= this.options,
                numItem= this.options.numItem;

            ele.find(options.rows).addClass("basic-row");
            ele.find(options.rows).each(function(){
                $(this).find(options.item).addClass("basic-item");
                ele.attr('data-limit',$(this).find(options.item).length);
                ele.attr('data-prev',-1);
                ele.attr('data-move',0);
                ele.attr('data-next',this.numItem);
            });

            this._calMoveItem(0);
        },

        _resetControls: function(){
            var ele= this.element,
                options= this.options,
                numItem = parseInt(this.numItem),
                move = parseInt(ele.attr('data-move')),
                prev = parseInt(ele.attr('data-prev')),
                next = parseInt(ele.attr('data-next')),
                limit= parseInt(ele.attr('data-limit'));

            if(next==limit){
                $('.slider-next').addClass('disabled');
            }else{
                $('.slider-next').removeClass('disabled');
            }
            if(prev==-1){
                $('.slider-prev').addClass('disabled');
            }else{
                $('.slider-prev').removeClass('disabled');
            }
            if(numItem>=limit){
                $('.slider-next').addClass('hidden');
                $('.slider-prev').addClass('hidden');
                this._resetSlider();
            }else{
                $('.slider-next').removeClass('hidden');
                $('.slider-prev').removeClass('hidden');
            }
        },

        _nextSlide: function(e){
            var ele= this.element,
                options= this.options,
                numItem = parseInt(this.numItem),
                move = parseInt(ele.attr('data-move')),
                prev = parseInt(ele.attr('data-prev')),
                next = parseInt(ele.attr('data-next')),
                limit= parseInt(ele.attr('data-limit'));

            if( (limit>numItem) && (next < limit)){
                ele.attr('data-next',next+1);
                ele.attr('data-prev',prev+1);
                ele.attr('data-move',move+1);
                ele.find(options.rows).each(function(){
                    $(this).find(options.item).each(function(index){
                        if( index == prev+1){
                            $(this).removeClass('show').addClass('hide');
                        }
                        if( index == prev+2){
                            $(this).addClass('first');
                        }else{
                            $(this).removeClass('first');
                        }
                        if( index == next){
                            $(this).removeClass('hide').addClass('show');
                        }
                    });
                });
                this._resetControls();
                this._calMoveItem(move+1);
            }
        },

        _prevSlide: function(e){
            var ele= this.element,
                options= this.options,
                numItem = parseInt(this.numItem),
                move = parseInt(ele.attr('data-move')),
                prev = parseInt(ele.attr('data-prev')),
                next = parseInt(ele.attr('data-next')),
                limit= parseInt(ele.attr('data-limit'));

            if((limit > numItem) && (prev < (limit - numItem)) && (prev > -1)){
                ele.attr('data-next',next-1);
                ele.attr('data-prev',prev-1);
                ele.attr('data-move',move-1);
                ele.find(options.rows).each(function(){
                    $(this).find(options.item).each(function(index){
                        if( index == prev){
                            $(this).removeClass('hide').addClass('show');
                        }
                        if( index == prev){
                            $(this).addClass('first');
                        }else{
                            $(this).removeClass('first');
                        }
                        if( index == next-1){
                            $(this).removeClass('show').addClass('hide');
                        }
                    });
                });
                this._calMoveItem(move-1);
                this._resetControls();
            }
        },

        _setWidth: function(numItem){
            var ele= this.element,
                options= this.options,
                wSlider= 0,
                wItem=0;

            wSlider= $('.slider-table-block').width();
            this.wSlider= wSlider;
            if(numItem>0) {
                wItem = wSlider / numItem;
                if(wItem < this.minW && this.minW != 'auto'){
                    this._setWidth(numItem - 1);
                }else{
                    this.wItem= wItem;
                    this.numItem = numItem;
                }
                ele.find(options.item).css('min-width',this.wItem+"px");
                ele.find(options.item).css('max-width',this.wItem+"px");
            }
        },

        _getWidthSlider: function(){
            return this.wSlider;
        },

        _getWidthItem: function(){
            return this.wItem;
        },

        _getHeight: function(){
            var ele= this.element,
                options= this.options,
                objHeight = [];

            ele.find(options.rows).each(function(){
                var item = $(this);
                setTimeout(function(){
                    var tmpHeight = Math.round(item.outerHeight()) + 1;
                    objHeight.push(tmpHeight);
                    item.css('height',tmpHeight);
                },0);
            });

            return objHeight;
        },

        _appendTitle: function(){
            var ele= this.element,
                options= this.options;

            ele.prepend("<div class='slider-table-title'></div>");
            ele.find(options.rows).each(function(){
                if($(this).find('.cell').hasClass('color')){
                    $('.slider-table-title').append("<div class='item color'><div class='item-inner'><span>"+$(this).find('th').text()+"</span></div></div>");
                }else{
                    $('.slider-table-title').append("<div class='item'><div class='item-inner'><span>"+$(this).find('th').text()+"</span></div></div>");
                }
            });
        },

        _calTitle: function(){
            var objHeight= this._getHeight();

            $('.slider-table-title').find('.item-inner').each(function(index){
                var item = $(this);
                setTimeout(function(){
                    item.css('height',objHeight[index]);
                    item.parent().css('height',objHeight[index]).addClass('done');
                },100);
            });
        },

        _calMoveItem: function(move){
            var ele= this.element;

            ele.find(this.options.tableCls).css('left',-(this._getWidthItem() * move));
        },

        setNumItem: function(num){
            this.numItem = num;
        },

        setMinW:function(num){
            this.minW = num;
        },

        update: function(){
            var move = parseInt(this.element.attr('data-move'));
            this._calSlider();
            this._calMoveItem(move);
        },

        destroy: function(){

        },

        resize: function(){
            var seft = this,
                winWidth = $(window).width();

            setTimeout(function(){
                if(seft.element.hasClass('slider-tablet')){
                    seft.setMinW(seft.options.minWTablet);
                    seft.setNumItem(seft.options.numItemTablet);
                }else{
                    seft.setMinW(seft.options.minW);
                    seft.setNumItem(seft.options.numItem);
                }

                seft.update();
            },500);
        },

        _destroy: function(){

        }

    });

    return $.forix.sliderTable;
});