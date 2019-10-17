/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

spnoConflict(function($){

	var homeClass 	= $('#sp-menu').find('a.home'),
		homePage 	= $('body').hasClass('homepage');
		
	if (homeClass) {
		var slideHeight = $(window).height(),
		homeItem 	= $('#sp-menu ul li a.home'),
		homeUrl 	= homeItem.attr('href'),
		homeId 		= $('.body-innerwrapper').find('section');
		
		if (homePage) {
			newHomeUrl 	= homeUrl+'#'+$(homeId[0]).attr('id');
		} else {
			newHomeUrl 	= homeUrl;
		}

		homeItem.attr('href',newHomeUrl);
	};

	if(homePage) {
		$('.sp-menu.level-0').onePageNav({
			currentClass: 'active',
		    changeHash: false,
		    scrollSpeed: 900,
		    scrollOffset: 40,
		    scrollThreshold: 0.3,
		    filter: ':not(.no-scroll)'
		});

		$('a.home').on('click',function() {
			$('body,html').animate({scrollTop:0},900);
		});
	}

});