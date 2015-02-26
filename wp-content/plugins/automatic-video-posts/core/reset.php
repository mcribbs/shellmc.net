<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			reset.php
//		Description:
//			This file resets the plugin's various settings pages.
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
//________________________________** INITIALIZE                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
if(!isset($_GET['page']) or $_GET['page'] !== 'ayvpp-reset') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_ayvpp_reset_actions');
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_reset_actions() {
	global $getWP,$WP_ayvpp_options,$wpdb;
	
	if(!isset($_REQUEST['_wpnonce']) or !wp_verify_nonce($_REQUEST['_wpnonce'],'WP_ayvpp_nonce') or !current_user_can('manage_options')) {
		return;
	}
	
	switch($_REQUEST['submit']) {
	
		case 'Completely Refresh Videos' :
			$videos = $wpdb->get_col("select post_id from $wpdb->postmeta where meta_key='_ayvpp_video' and meta_value != ''");
			foreach((array)$videos as $v) {
				if(!wp_delete_post($v,true)) {
					$getWP->addError('There was an error while deleting a video post "'.get_the_title($v).'". Please try again.');
				}
			}
			break;
			
		case 'Reset this Plugin' :
			$videos = $wpdb->get_col("select post_id from $wpdb->postmeta where meta_key='_ayvpp_video'");
			foreach((array)$videos as $v) {
				if(!wp_delete_post($v,true)) {
					$getWP->addError('There was an error while deleting a video post "'.get_the_title($v).'". Please try again.');
				}
			}
			$getWP->getOption('ayvpp_settings',$WP_ayvpp_options,true);
			break;
			
		case 'Reset this Plugin but keep posts' :
			$getWP->getOption('ayvpp_settings',$WP_ayvpp_options,true);
			break;
			
		case 'Reset Import Field in the Database' :
			update_option('ayvpp_importing',0);
			break;
		
		default :
			break;
		
	}
	
}
//                                *******************************                                 //
//________________________________** SETTINGS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_reset() {
	include(AYVPP_DIR.'/views/reset.php');
}

/****************************************Terminate Script******************************************/
?>