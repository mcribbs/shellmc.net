/**************************************************************************************************/
/*
/*		File:
/*			meta.js
/*		Description:
/*			This file contains Javascript for ternstyle's Automatic Video Posts Plugin (Meta).
/*		Copyright:
/*			Copyright (c) 2015 Ternstyle LLC.
/*		License:
/*			This software is licensed under the terms of the End User License Agreement (EULA) 
/*			provided with this software. In the event the EULA is not present with this software
/*			or you have not read it, please visit:
/*			http://www.ternstyle.us/automatic-video-posts-plugin-for-wordpress/license.html
/*
/**************************************************************************************************/

/****************************************Commence Script*******************************************/

(function($) { $(document).ready(function () {
	
/*------------------------------------------------------------------------------------------------
	Sync Button
------------------------------------------------------------------------------------------------*/
	
	$('#ayvpp_meta_sync_button').bind('click',function () {
		$(this).parents('#ayvpp_meta_box').find('input[name=WP_ayvpp_action]').val('sync');
		$(this).parents('form').submit();
	});
	
	
/****************************************Terminate Script******************************************/
		
}); })(jQuery);