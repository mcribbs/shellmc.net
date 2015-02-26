/**************************************************************************************************/
/*
/*		File:
/*			scripts.js
/*		Description:
/*			This file contains Javascript for ternstyle's Automatic Video Posts Plugin (Scripts).
/*		Copyright:
/*			Copyright (c) 2014 Ternstyle LLC.
/*		License:
/*			This software is licensed under the terms of the End User License Agreement (EULA) 
/*			provided with this software. In the event the EULA is not present with this software
/*			or you have not read it, please visit:
/*			http://www.ternstyle.us/automatic-video-posts-plugin-for-wordpress/license.html
/*
/**************************************************************************************************/

/****************************************Commence Script*******************************************/

(function($) { $(document).ready(function() {

/*------------------------------------------------------------------------------------------------
	Responsive Videos
------------------------------------------------------------------------------------------------*/

	function resize() {
		$('.ayvpp').each(function () {
			if($(this).hasClass('responsive')) {
				var r = $(this).attr('data-ratio').split(':');
				r = r[1]/r[0];
				var w = $(this).parent().innerWidth();
				$(this).css({ width:'100%',height:(w*r) });
			}
		});
	}
	resize();
	
	var timer = time = null;
	$(window).bind('resize orientationchange',function () {
		time = new Date();
		timer = timer ? timer : setInterval(function () {
			if((new Date()-time) > 500) {
				clearInterval(timer);
				time = timer = null;
				resize();
			}
		},100);
	});

/****************************************Terminate Script******************************************/

}); })(jQuery);