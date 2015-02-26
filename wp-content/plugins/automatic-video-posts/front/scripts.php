<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			scripts.php
//		Description:
//			This file includes the necerssary CSS and Javascript files the front-end.
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
add_action('wp_enqueue_scripts','WP_ayvpp_styles');
add_action('wp_enqueue_scripts','WP_ayvpp_scripts');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_styles() {
	wp_enqueue_style('ayvpp-style');
}
function WP_ayvpp_scripts() {
	wp_enqueue_script('ayvpp-scripts');
}

/****************************************Terminate Script******************************************/
?>