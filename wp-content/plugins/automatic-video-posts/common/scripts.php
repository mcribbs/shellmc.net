<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			scripts.php
//		Description:
//			This file includes the necerssary CSS and Javascript files.
//		Copyright:
//			Copyright (c) 2014 Ternstyle LLC.
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
add_action('init','WP_ayvpp_register_styles',0);
add_action('init','WP_ayvpp_register_scripts',0);
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_register_styles() {
	wp_register_style('ayvpp-admin',AYVPP_URL.'/css/admin.css',array(),'4.0');
	wp_register_style('ayvpp-style',AYVPP_URL.'/css/style.css',array(),'4.0');
	
	if(is_admin()) {
		wp_enqueue_style('ayvpp-admin');
	}
}
function WP_ayvpp_register_scripts() {
	wp_register_script('easing',AYVPP_URL.'/js/jquery.easing.js',array('jquery'),'1.3',true);
	wp_register_script('ayvpp-errors',AYVPP_URL.'/js/errors.js',array('jquery'),'1.0',true);
	wp_register_script('ayvpp-import',AYVPP_URL.'/js/import.js',array('jquery','easing','ayvpp-errors'),'4.0',true);
	wp_register_script('ayvpp-channels',AYVPP_URL.'/js/channels.js',array('jquery'),'4.0',true);
	wp_register_script('ayvpp-meta',AYVPP_URL.'/js/meta.js',array('jquery'),'1.0',true);
	wp_register_script('ayvpp-scripts',AYVPP_URL.'/js/scripts.js',array('jquery'),'1.0',true);
}

/****************************************Terminate Script******************************************/
?>