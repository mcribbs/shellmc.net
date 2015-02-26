<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			settings.php
//		Description:
//			This file compiles and processes the plugin's various settings pages.
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
if(!isset($_GET['page']) or $_GET['page'] !== 'ayvpp-settings') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_ayvpp_settings_actions');
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_settings_actions() {
	global $getWP,$ayvpp_options,$WP_ayvpp_options,$WP_ayvpp_ip;
	
	//check for user's IP address
	$c = new tern_curl;
	$m = $c->get(array(
		'url'			=>	'http://www.ternstyle.us/automatic-video-posts-plugin-for-wordpress/software/activate/ip/',
		'options'		=>	array(
			'RETURNTRANSFER'	=>	true
		)
	));

	$r = json_decode($m->body);
	if($r->success) {
		$WP_ayvpp_ip = $r->ip;
	}
	else {
		$WP_ayvpp_ip = false;
		$getWP->addWarning('We were not able to determine the outbound IP Address of your server. You may have to contact your hosting provider.');
	}
	
	//update settings
	if(!isset($_REQUEST['_wpnonce']) or !wp_verify_nonce($_REQUEST['_wpnonce'],'WP_ayvpp_nonce') or !current_user_can('manage_options')) {
		return;
	}
	
	switch($_REQUEST['action']) {
	
		case 'update' :
			$ayvpp_options = $getWP->updateOption('ayvpp_settings',$WP_ayvpp_options,'WP_ayvpp_nonce');
			
			//validate API key
			$c = new tern_curl();
			$r = $c->get(array(
				'url'		=>	'https://www.googleapis.com/youtube/v3/search?part=id&q=9eujhd74hbnsjd874ndeidme8emuw7wqhjsmxosowmw0sk7834rghiwrfuh&key='.$ayvpp_options['key'],
				'options'	=>	array(
					'RETURNTRANSFER'	=>	true,
					//'FOLLOWLOCATION'	=>	true
				),
				'headers'	=>	array(
					'Accept-Charset'	=>	'UTF-8'
				)
			));
			$r = json_decode($r->body);

			if(isset($r->error) and $r->error->errors[0]->reason == 'keyInvalid') {
				$getWP->addError('The Google API key you provided is not valid. Please visit your Google API console to configure your key: <a href="https://code.google.com/apis/console" target="_blank">https://code.google.com/apis/console</a>');
			}
			elseif(isset($r->error) and $r->error->errors[0]->reason == 'accessNotConfigured') {
				$getWP->addError('Your API Key is correct but the project it belongs to in your Google API console is not configured properly to use the YouTube&reg; API. Try logging in to the Google API console to fix this: <a href="https://code.google.com/apis/console" target="_blank">https://code.google.com/apis/console</a>');
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
function WP_ayvpp_settings() {
	global $ayvpp_options,$ternSel,$WP_ayvpp_ip;
	include(AYVPP_DIR.'/views/settings.php');
}

/****************************************Terminate Script******************************************/
?>