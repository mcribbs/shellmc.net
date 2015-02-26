<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			scripts.php
//		Description:
//			This file includes the necerssary CSS and Javascript files the admin.
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
add_action('admin_enqueue_scripts','WP_ayvpp_styles');
add_action('admin_enqueue_scripts','WP_ayvpp_scripts');
add_action('wp_print_scripts','WP_ayvpp_js');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_styles() {
	if(is_admin()) {
		wp_enqueue_style('ayvpp-admin');
	}
}
function WP_ayvpp_scripts() {
	if(is_admin()) {
		wp_enqueue_script('easing');
		wp_enqueue_script('ayvpp-errors');
	}
}
function WP_ayvpp_js() {
	echo '<script type="text/javascript">var ayvpp_root = "'.AYVPP_ROOT.'";</script>'."\n";
}

/****************************************Terminate Script******************************************/
?>