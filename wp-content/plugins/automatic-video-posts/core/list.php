<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			list.php
//		Description:
//			This file compiles and processes the plugin's video list.
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
if(!isset($_GET['page']) or $_GET['page'] !== 'ayvpp-video-posts') {
	return; 
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_ayvpp_list_actions');
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_list_actions() {
	global $wpdb,$getWP;
	
	if(!isset($_REQUEST['_wpnonce']) or !wp_verify_nonce($_REQUEST['_wpnonce'],'WP_ayvpp_nonce') or !current_user_can('manage_options')) {
		return;
	}
	
	$videos = $wpdb->get_col("select post_id from $wpdb->postmeta where meta_key='_ayvpp_video'");
	
	$action = empty($_REQUEST['action']) ? $_REQUEST['action2'] : $_REQUEST['action'];
	switch($action) {
		
		case 'delete' :
			foreach((array)$_REQUEST['videos'] as $v) {
				$p = get_post($v);
				if($p->post_status != 'trash' and !wp_delete_post($v)) {
					$getWP->addError('There was an error while deleting a video post "'.get_the_title($v).'". Please try again.');
				}
			}
			break;
		
		case 'publish' :
			foreach((array)$_REQUEST['videos'] as $v) {
				if(!$wpdb->query("update $wpdb->posts set post_status='publish' where ID=".$v)) {
					$getWP->addError('There was an error while publishing your video post "'.get_the_title($v).'". Please try again.');
				}
			}
			break;
		
		case 'draft' :
			foreach((array)$_REQUEST['videos'] as $v) {
				if(!$wpdb->query("update $wpdb->posts set post_status='draft' where ID=".$v)) {
					$getWP->addError('There was an error while drafting your video post "'.get_the_title($v).'". Please try again.');
				}
			}
			break;
			
		case 'refresh' :
			
			foreach((array)$videos as $v) {
				if(!wp_delete_post($v,true)) {
					$getWP->addError('There was an error while deleting a video post "'.get_the_title($v).'". Please try again.');
				}
			}
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
function WP_ayvpp_video_posts() {
	global $getWP,$wpdb,$post,$ayvpp_options;
	
	$page = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
	$start = (($page-1)*10);
	
	$videos = $wpdb->get_col("select a.ID from $wpdb->posts as a join $wpdb->postmeta as b on (a.ID = b.post_id) where b.meta_key='_ayvpp_video' and b.meta_value <> '' order by a.post_date desc limit $start,10");
	$video_count = $wpdb->get_var("select count(a.ID) from $wpdb->posts as a join $wpdb->postmeta as b on (a.ID = b.post_id) where b.meta_key='_ayvpp_video' and b.meta_value <> ''");
	
	include(AYVPP_DIR.'/views/list.php');
	
}

/****************************************Terminate Script******************************************/
?>