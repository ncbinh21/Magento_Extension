var initedMegamenu = false;
function playMegamenuJs($, alias, mobileTemplate, event, scrolltofixed) {
	var parent_container = '.sections.nav-sections';
	if (mobileTemplate == 3) {
		$('.ves-drill-down-menu').find('.opener').addClass('ves-click');

		$(window).on('load resize',function(e){
			e.preventDefault();
			var back        	= '<div class="hide-submenu"></div>';
			var subHide     	= $(back);
			var subMenu       	= $('.ves-drill-down-menu .submenu');
			
			// Add submenu hide bar
			if (subHide.children('hide-submenu').length ==0) {
				subHide.prependTo(subMenu);
			}
			var subHideToggle 	= $('.ves-drill-down-menu .hide-submenu');
			// Hide submenu
			subHideToggle.on("click", function() {
				$(this).parent().parent().removeClass('view-submenu');
				$(this).parent().parent().parent().removeClass('view-submenu');
				$(this).parent().parent().parent().parent().parent().parent().parent().removeClass('view-submenu');
				$(this).parent().parent().parent().parent().parent().parent().parent().parent().removeClass('view-submenu');
				$(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().removeClass('view-submenu');
				$(this).parent().hide();

			});

			if ($(window).width() <= 768){
				
				$('.ves-drill-down-menu').find('.opener').addClass('fa fa-arrow-right').removeClass('opener');
				$('.ves-drill-down-menu').find('.navigation').addClass('navdrilldown').removeClass('navigation');
				$(".ves-drill-down-menu #"+alias+" .ves-click").on('click', function(e) {
					e.preventDefault();
					if ($(window).width() <= 768){	
						
						$(this).removeClass('.item-active');
						$(this).parents('.dropdown-submenu').addClass('view-submenu');
						$(this).parents('.submenu').addClass('view-submenu');
						$(this).parents('ul.ves-megamenu').addClass('view-submenu');
						var a = $(this).parents('li.nav-item').offset().top;
						var b = $(this).parents('ul.ves-megamenu').offset().top;
						var c = $(this).parent().parent().offset().top;

						$(this).parents('li.nav-item').children('.submenu').css('top',b-a+'px');
						$(this).parent().parent().children('.submenu').css('top',b-c+'px');
						$('.submenu.dropdown-menu').hide();
						$(this).parents('.submenu').show();
						$(this).parent().parent().children('.submenu').show();
						return false;

					}	
				});
			}else {
				$('.ves-drill-down-menu').find('.fa-arrow-right').addClass('opener').removeClass('fa fa-arrow-right');
				$('.ves-drill-down-menu').find('.navdrilldown').addClass('navigation').removeClass('navdrilldown');
			}
		});//end load resize window
	}
	jQuery("#"+alias+"-menu .ves-megamenu .level0").hover(function() {
		var mParentTop = jQuery(this).parents('.ves-megamenu').offset().top;
		var mParentHeight = $(this).parent().height();
		var mTop =  $(this).height();
		var mHeight = $(this).height();
		var mParent = $(this).parent();
		if (mHeight < mParentHeight) {
			mTop = $(this).offset().top - mParent.offset().top + mHeight;
		}
		$(this).children('.submenu').css({top:mTop});	
	});

	if(scrolltofixed){ //check option scroll to fixed enabled
		$('.nav-sections-items > .nav-sections-item-content').scrollToFixed({
			zIndex: 99
		});

		$(window).on("resize load", function(){
			if ($(window).width() < 768){
				$('.nav-sections-items > .nav-sections-item-content').css({position: '', top: '', width: '100%'});
			}
		});
		if($("#"+alias+"-menu").parents(parent_container).length > 0) {
			var menuParentPosition = $("#"+alias+"-menu").parents(parent_container).offset().top;
			$(window).scroll(function() {
				var height = $(window).scrollTop();
				if (height<(menuParentPosition) - $("#"+alias+"-menu").outerHeight()) {
					$('.nav-sections-items > .nav-sections-item-content').css({position: '', top: '', width: '100%'});
				}
				$('.section-items.nav-sections-items').find('div').each(function(index, el) {
					if ($(this).html() == '' && $(this).attr('class')=='') {
						$(this).remove();
					}
				});
			});
		}
	}//end check scroll to fixed

	jQuery('p').each(function() {
		var $this = $(this);
		if ($this.html().replace(/\s|&nbsp;/g, '').length == 0)
		$this.remove();
	});

	if(!initedMegamenu){
		var menuToogle = function () {
			if ($('html').hasClass('nav-open')) {
				$('html').removeClass('nav-open');
				setTimeout(function () {
					$('html').removeClass('nav-before-open');
				}, 300);
			} else {
				$('html').addClass('nav-before-open');
				setTimeout(function () {
					$('html').addClass('nav-open');
				}, 42);
			}
		}
		$(document).on("click", ".action.nav-toggle", menuToogle);
    }

	$(document).on("click", ".ves-overlay"+alias, function(){
		$("#"+alias).css("left","");
		$('html').removeClass('ves-navopen');
		setTimeout(function () {
			$('html').removeClass('ves-nav-before-open');
		}, 300);
		$(this).remove();
		return false;
	});

	$("#"+alias+" .dynamic-items li").hover(function(){
		$(this).parents(".dynamic-items").find("li").removeClass("dynamic-active");
		$(this).addClass("dynamic-active");
		var id = $(this).data("dynamic-id");
		$("#"+alias+" ."+id).parent().find(".dynamic-item").removeClass("dynamic-active");
		$("#"+alias+" ."+id).addClass("dynamic-active");
	});
	var mImg = '';
	$("#"+alias+" img").hover(function(){
		mImg = '';
		mImg = $(this).attr('src');
		if ($(this).data('hoverimg')){
			$(this).attr('src',$(this).data('hoverimg'));
		}
	},function(){
		$(this).attr('src',mImg);
	});

	$("#"+alias+" li a").hover(function(){
		$(this).css({
			"background-color": $(this).data("hover-bgcolor"),
			"color": $(this).data("hover-color")
		});
	}, function(){
		$(this).css({
			"background-color": $(this).data("bgcolor"),
			"color": $(this).data("color")
		});
	});

	$(window).on("resize load", function(){

		if($("#"+alias).data("disable-bellow") && $("#"+alias).data("disable-above")){
			var window_width = $(window).width();
			if ((window_width <= $("#"+alias).data("disable-bellow")) || (window_width >= $("#"+alias).data("disable-above"))){
				$("#"+alias+"-menu").hide();
			}else{
				$("#"+alias+"-menu").show();
			}

			$("#"+alias).find("li").each(function(index, element){
				if ((window_width <= $(this).data("disable-bellow")) || (window_width >= $(this).data("disable-above"))){
					$(this).addClass("hidden");
				} else if ($(this).hasClass("hidden")){
					$(this).removeClass("hidden");
				}
			});

		} else if($("#"+alias).data("disable-bellow") && !$("#"+alias).data("disable-above")) {
			if ($(window).width() <= $("#"+alias).data("disable-bellow")){
				$("#"+alias+"-menu").hide();
			}else{
				$("#"+alias+"-menu").show();
			}

			$("#"+alias).find("li").each(function(index, element){
				if ($(window).width() <= $(this).data("disable-bellow")){
					$(this).addClass("hidden");
				}else if ($(this).hasClass("hidden")){
					$(this).removeClass("hidden");
				}
			});
		} else if($("#"+alias).data("disable-above") && !$("#"+alias).data("disable-bellow")) {
			if ($(window).width() >= $("#"+alias).data("disable-above")){
				$("#"+alias+"-menu").hide();
			}else{
				$("#"+alias+"-menu").show();
			}

			$("#"+alias).find("li").each(function(index, element){
				if($(window).width() >= $(this).data("disable-above")) {
					$(this).addClass("hidden");
				} else if ($(this).hasClass("hidden")){
					$(this).removeClass("hidden");
				}
			});
		}
		
		if ($(window).width() >= 768 && $(window).width() <= 1024){
			$("#"+alias+" .nav-anchor").off().click(function(){
				var iParent = $(this).parent('.nav-item');
				iParent.addClass("clicked");
				if ($(iParent).children('.submenu').length == 1){
					iParent.trigger('hover');
					if (iParent.hasClass('submenu-alignleft') || iParent.hasClass('submenu-alignright')){
						if ((iParent.offset().left + iParent.find('.submenu').eq(0).width()) > $(window).width()){
							iParent.find('.submenu').eq(0).css('max-width','100%');
							iParent.css('position','static');
						}
					}
					return false;
				}
			});
		}else{
			$("#"+alias).find('.submenu').css('max-width','');
			$("#"+alias).find('.submenu-alignleft').css('position','relative');
		}
		if ($(window).width() <= 768){
			if($(parent_container).length > 0) {
				$(parent_container).removeAttr( "style" );
			}
			$("#"+alias).addClass("nav-mobile");
		}else{
			$("#"+alias).find(".submenu").css({'display':''});
			$("#"+alias).find("div").removeClass("mbactive");
			$("#"+alias).removeClass("nav-mobile");
		}
	}).resize();

	//Toggle mobile menu
	$('.ves-megamenu-mobile #'+alias+' .opener').on('click', function(e) {
		e.preventDefault();
		$("#"+alias+" .nav-item").removeClass("item-active");
		var parent = $(this).parents(".nav-item").eq(0);
		$(this).toggleClass('item-active');
		$(parent).find(".submenu").eq(0).slideToggle();
		return false;
	});

	if(event == 'hover'){
		$(document).ready(function(){
			$('header.page-header .container_navigation ul li.dropdown').on('mouseover', function() {
				   $('.mega_menu').hide();
				   $(this).find('.mega_menu').show();
				});

			   $('html').click(function() {
			   		$(this).find('.mega_menu').hide();
			   });
			 });

			$('.mega_menu').click(function(event){
				event.stopPropagation();
			});
	} else {
		$(document).mouseup(function(e) {
		    var container = $("header.page-header .container_navigation ul li .mega_menu");

		    // if the target of the click isn't the container nor a descendant of the container
		    if (!container.is(e.target) && container.has(e.target).length === 0) 
		    {
		        $(container).stop().hide();
		    }
		});
		$('header.page-header .container_navigation ul li .openmenu').on('click', function(e) {

			e.preventDefault();
						
			var parent = $(this).parents(".nav-item").eq(0);
			$(this).toggleClass('item-active');

			 var $this =  $(parent).find(".mega_menu").eq(0);
			$(".mega_menu").not($this).hide();
		   $(parent).find(".mega_menu").eq(0).stop().toggle();
		 });
	}
	initedMegamenu = true;
}