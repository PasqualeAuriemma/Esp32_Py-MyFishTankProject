/*
 *
 *	BILLY The Carousel jquery plugin v 0.5
 *	Created By Jason Howmans (jasonhowmans@me.com)
 *	
 *	Dual licensed under the MIT or GPL Version 2 licenses.
 *
 *	-- Options
 *	
 *	- transition : 'scroll', or 'fade' ('scroll' by default)
 *	- scrollSpeed : The time a single transition will take
 *	- slidePause : Amount of time between scrolls
 *	- indicators : The target <ul> for displaying the indicators (indicators arent required for basic functionality)
 *	- indicatorLinks : Clicking on indicators jumps to slide, true by default
 *	- activeClass : Class to attach to the active indicator. default is 'active'
 *	- scrollAmount : Amount to scroll by on each transition. Set to 'auto' by default
 *	- nextLink : The element which will scroll next on click
 *	- prevLink : The element which will scroll back on click
 *	- autoAnimate : Do you want the carousel to play without the user pressing the next/prev buttons
 *	- loop : Loops back to the beginning after final slide
 *	- customIndicators : Build your own indicators. This offers developers more functionality than just a standard carousel (false by default). 
 *	- noAnimation : Turns off fancy animation, all actions will be static
 *	
 */

(function($){     
	$.fn.extend({   

		billy: function(options) { 
		
			// Defaults
			var defaults = {
				transition		: 'scroll',
				scrollSpeed		: 500,
				slidePause		: 4000,
				indicators		: $('ul#billy_indicators'),
				indicatorLinks	: true,
				activeClass		: 'active',
				scrollAmount	: 'auto',
				nextLink		: $('#billy_next'),
				prevLink		: $('#billy_prev'),
				autoAnimate		: true,
				loop			: true,
				customIndicators: false,
				noAnimation		: false,
			};
			
			// Set Options
			var options = $.extend(defaults, options);
			
			// Def
			var option;
			var object;
			var slides;
			var slidewidth;
			var slidecount;
			var currentslide;
			var defautindicator;
			var the_indicators;
			var hreftag;
			var indicatorsli;
			var currentindicator;
			
			// Loop throuch Carousels
			return this.each(function() {
				
				// Set Options
				option = options;
				// Set currently selected
				object = $(this);
				// Sets up slide size
				slides = object.find('li');
				if (option.scrollAmount	== 'auto') { 
					slidewidth		= slides.width();
				}else{ 
					slidewidth = option.scrollAmount; 
				}
				// Other vars
				slidecount = (slides.width() * slides.length) / slidewidth;
				slidecount = Math.round(slidecount);
				currentslide = 0;
				
				// If there's slides, continue
				if (slides.length > 0) {
					
					// If the developer wants default indicators
					if (!option.customIndicators) {
						// Loop / no. of slides
						for (var i = 0; i<slidecount; i++) {
							// -- Insert Indicators
							if (!option.indicatorLinks) {
								option.indicators.append('<li></li>');
							}else{
								option.indicators.append('<li><a href="#'+i+'"></a></li>');
							}
						}
					}
	
					// Indicator Functionality
					defautindicator = option.indicators.find('li:eq(0)');
					defautindicator.addClass(option.activeClass)
					the_indicators = option.indicators.find('li a');
			
					// Thanks to Tomas Nikl for the below fix
					the_indicators.click( function() {
						hreftag = $(this).attr('href');
						hreftag = hreftag.substring(hreftag.search('#')+1, hreftag.length);
						jumptospecific(hreftag);
						if (option.autoAnimate) {
							clearInterval(period);
							period = window.setInterval(function() {
								if (currentslide >= (slidecount - 1)) {
									jumptostart();
								}else{
									jumpnext();
								}
							}, option.slidePause);
						}
						return false;
					});
					
					// -- Fading Properties
					if (option.transition == 'fade') {
						object.css({
							position: 'relative',
							margin: '0px'
						});
						object.find('li').css({
							position: 'absolute',
							top: '0px',
							left: '0px',
							display: 'none',
							zIndex: '999'
						});
						// Show First Slide
						object.find('li:eq(0)').css('display','block');
					}
			
					
					// -- Jump Functions
					
					// Jump to Start
					var jumptostart = function() {
					    currentslide = 0;
						if (option.noAnimation) {
							object.css('marginLeft', "0");
						}else{
							// Fade Transition
							if (option.transition == 'fade') {
								object.find('li').css('display','none');
								object.find('li:eq('+(currentslide)+')').css({
									display: 'block',
									zIndex: '990'
								});
								object.find('li:eq('+(slidecount-1)+')').css({
									display: 'block',
									zIndex: '999',
								});
								object.find('li:eq('+(slidecount-1)+')').fadeOut(option.scrollSpeed);
							}else{
								// Scroll Transition
								object.animate({'marginLeft': "0"}, option.scrollSpeed);
							}
						} 
						// Do Indicators
					    indicatorsli = option.indicators.find('li');
						indicatorsli.removeClass();
					   	currentindicator = option.indicators.find('li:eq('+(currentslide)+')');
						currentindicator.addClass(option.activeClass);
					};
					
					// Jump to End	
					var jumptoend = function() {
					    currentslide = slidecount-1;
						if (option.noAnimation) {
							object.css('marginLeft', "-"+(currentslide*slidewidth)+"px");
						}else{
							// Fade Transition
							if (option.transition == 'fade') {
								object.find('li').css('display','none');
								object.find('li:eq('+(currentslide)+')').css({
									display: 'block',
									zIndex: '990'
								});
								object.find('li:eq(0)').css({
									display: 'block',
									zIndex: '999',
								});
								object.find('li:eq(0)').fadeOut(option.scrollSpeed);
							}else{
								// Scroll Transition
					    		object.animate({'marginLeft': "-"+(currentslide*slidewidth)}, option.scrollSpeed);
							}
						}
					    indicatorsli = option.indicators.find('li')
						indicatorsli.removeClass();
					    currentindicator = option.indicators.find('li:eq('+(currentslide)+')');
						currentindicator.addClass(option.activeClass);
					};
					
					// Jump to Next Slide
					var jumpnext = function() {
						if (currentslide < (slidecount - 1)) {
							currentslide ++;
							if (option.noAnimation) {
								object.css('marginLeft', "-"+(slidewidth*currentslide)+"px");
							}else{
								// Fade Transition
								if (option.transition == 'fade') {
									object.find('li').css('display','none');
									object.find('li:eq('+(currentslide)+')').css({
										display: 'block',
										zIndex: '990'
									});
									object.find('li:eq('+(currentslide-1)+')').css({
										display: 'block',
										zIndex: '999',
									});
									object.find('li:eq('+(currentslide-1)+')').fadeOut(option.scrollSpeed);
								}else{
									// Scroll Transition
									object.animate({'marginLeft': "-"+(slidewidth*currentslide)}, option.scrollSpeed);
								}
							}
							indicatorsli = option.indicators.find('li');
							indicatorsli.removeClass();
							currentindicator = option.indicators.find('li:eq('+(currentslide)+')');
							currentindicator.addClass(option.activeClass);
							if (option.autoAnimate) {
								clearInterval(period);
								period = window.setInterval(function() {
									if (currentslide >= (slidecount - 1)) {
										jumptostart();
									}else{
										jumpnext();
									}
								}, option.slidePause);
							}
						}else{
							if (option.loop)
								jumptostart();
						}
					};
					
					// Jump to Prev Slide
					var jumpback = function() {
						if (currentslide > 0) {
							currentslide --;
							if (option.noAnimation) {
								object.css('marginLeft', "-"+(slidewidth*currentslide)+"px");
							}else{
								// Fade Transition
								if (option.transition == 'fade') {
									object.find('li').css('display','none');
									object.find('li:eq('+(currentslide)+')').css({
										display: 'block',
										zIndex: '990'
									});
									object.find('li:eq('+(currentslide+1)+')').css({
										display: 'block',
										zIndex: '999',
									});
									object.find('li:eq('+(currentslide+1)+')').fadeOut(option.scrollSpeed);
								}else{
									// Scroll Transition
									object.animate({'marginLeft': "-"+(slidewidth*currentslide)}, option.scrollSpeed);
								}
							}
							indicatorsli = option.indicators.find('li');
							indicatorsli.removeClass();
							currentindicator = option.indicators.find('li:eq('+(currentslide)+')');
							currentindicator.addClass(option.activeClass);
							if (option.autoAnimate) {
								clearInterval(period);
								period = window.setInterval(function() {
									if (currentslide >= (slidecount - 1)) {
										jumptostart();
									}else{
										jumpnext();
									}
								}, option.slidePause);
							}
						}else{
							if (option.loop)
								jumptoend();
						}
					};
					
					// Jump to Specific Slide
					var jumptospecific = function(frame) {
						if (currentslide !== frame) {
							currentslide = frame;
							if (option.noAnimation) {
								object.css('marginLeft', "-"+(slidewidth*currentslide)+"px");
							}else{
								// Fade Transition
								if (option.transition == 'fade') {
									object.find('li:eq('+currentslide+')').css({
										display: 'block',
										zIndex: '990'
									});
									object.find('li').not('li:eq('+currentslide+')').css({
										zIndex: '999'
									});
									object.find('li').not('li:eq('+currentslide+')').fadeOut(option.scrollSpeed);
								}else{
									// Scroll Transition
						    		object.animate({'marginLeft': "-"+(slidewidth*currentslide)}, option.scrollSpeed);
								}
							}
						    indicatorsli = option.indicators.find('li');
							indicatorsli.removeClass();
						    currentindicator = option.indicators.find('li:eq('+(currentslide)+')');
							currentindicator.addClass(option.activeClass);
						}
					};
					
					// -- Click next/prev
					option.nextLink.click( function() {
						jumpnext();
						return false;
					});
					option.prevLink.click( function() {
						jumpback();
						return false;
					});
					
					// -- Autoanimate
					if (option.autoAnimate) {
						// -- Periodical
						var period;
						// Run
						period = window.setInterval(function() {
				
							if (currentslide >= (slidecount - 1)) {
								jumptostart();
							}else{
								jumpnext();
							}
		
						}, option.slidePause);
					}
				}
				
			});
			
		}
		
	});
})(jQuery);