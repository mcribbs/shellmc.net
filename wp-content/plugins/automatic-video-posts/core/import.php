<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			import.php
//		Description:
//			This file compiles the import form and performs import upon ajax request.
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
if((!isset($_GET['page']) or $_GET['page'] !== 'ayvpp-import-videos') and $GLOBALS['pagenow'] != 'admin-ajax.php') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_ayvpp_import_scripts');

add_action('wp_ajax_ayvpp_is_importing','WP_ayvpp_import_actions');
add_action('wp_ajax_ayvpp_import','WP_ayvpp_import_actions');
add_action('wp_ajax_ayvpp_file','WP_ayvpp_import_actions');
add_action('wp_ajax_ayvpp_status','WP_ayvpp_import_actions');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_import_scripts() {
	wp_enqueue_script('ayvpp-import');
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_import_actions() {

	if(!wp_verify_nonce($_REQUEST['_wpnonce'],'WP_ayvpp_nonce') or !current_user_can('manage_options')) {
		return;
	}
	
	switch($_REQUEST['action']) {
	
		case 'ayvpp_is_importing' :
		
			$i = get_option('ayvpp_importing');
			if($i and $i > time()-86400) {
				echo json_encode(array(
					'code'		=>	500
				));
			}
			else {
				echo json_encode(array(
					'code'		=>	200
				));
			}
			
			die();
	
		case 'ayvpp_import' :
			
			$parse = WP_ayvpp_add_posts(true);
			
			echo json_encode($parse);
			
			die();
			
		case 'ayvpp_status' :
			
			echo file_get_contents(WP_CONTENT_DIR.'/cache/ayvpp.txt');
			die();
		
		default :
			break;
		
	}
	
}
//                                *******************************                                 //
//________________________________** SETTINGS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_import_videos() {
	global $ayvpp_options;
	include(AYVPP_DIR.'/views/import.php');
}

/****************************************Terminate Script******************************************/
?>