<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			menus.php
//		Description:
//			This file initializes menus for the plugin's administrative tasks
//		Copyright:
//			Copyright (c) 2011 Matthew Praetzel.
//		License:
//			This software is licensed under the terms of the End User License Agreement (EULA) 
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit:
//			http://www.ternstyle.us/automatic-video-posts-plugin-for-wordpress/license.html
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('admin_menu','WP_ayvpp_menu');
//                                *******************************                                 //
//________________________________** MENUS                     **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_menu() {
	if(function_exists('add_menu_page')) {
		
		if(WP_ayvpp_is_activated()) {
			add_menu_page('Automatic Video Posts','Automatic Video',10,'ayvpp-settings','WP_ayvpp_settings','dashicons-video-alt3');
			add_submenu_page('ayvpp-settings','Automatic Video Posts','Settings',10,'ayvpp-settings','WP_ayvpp_settings');
			add_submenu_page('ayvpp-settings','Channels/Playlists','Channels/Playlists',10,'ayvpp-channels','WP_ayvpp_channels');
			add_submenu_page('ayvpp-settings','Import Videos','Import Videos',10,'ayvpp-import-videos','WP_ayvpp_import_videos');
			add_submenu_page('ayvpp-settings','Video Posts','Video Posts',10,'ayvpp-video-posts','WP_ayvpp_video_posts');
			add_submenu_page('ayvpp-settings','Reset','Reset',10,'ayvpp-reset','WP_ayvpp_reset');
			add_submenu_page('ayvpp-settings','Trouble Shooting','Trouble Shooting',10,'ayvpp-trouble','WP_ayvpp_trouble');
			add_submenu_page('ayvpp-settings','Activated','Activated',10,'ayvpp-activate','WP_ayvpp_set_activate');
		}
		else {
			add_menu_page('Automatic Video Posts','Automatic Video',10,'ayvpp-activate','WP_ayvpp_set_activate','dashicons-video-alt3');
		}
	}
}

/****************************************Terminate Script******************************************/
?>