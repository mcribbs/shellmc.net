/**************************************************************************************************/
/*
/*		File:
/*			errors.js
/*		Description:
/*			This file contains Javascript for ternstyle's Automatic Video Posts Plugin (Errors).
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

/*------------------------------------------------------------------------------------------------
	Initialize
------------------------------------------------------------------------------------------------*/

	add_error = add_alert = null;
	
/*------------------------------------------------------------------------------------------------
	Errors
------------------------------------------------------------------------------------------------*/

	(function($) {
			
		add_error = function (e) {
			$('#wpbody-content').prepend('<div class="tern_error tern_error_new"><div><p>'+e+'</p></div></div>');
			error_show();
		}
		
		add_alert = function (e) {
			$('#wpbody-content').prepend('<div class="tern_alert tern_error_new"><div><p>'+e+'</p></div></div>');
			error_show();
		}
		
		function error_show() {
			$('.tern_error_new').each(function () {
				$(this).stop().animate({ height:$(this).find('> div').outerHeight() },{ duration:500,easing:'easeOutCirc',complete : function () {
					$(this).removeClass('tern_error_new');
				}});
			});
		}
		
	})(jQuery);
	

/****************************************Terminate Script******************************************/