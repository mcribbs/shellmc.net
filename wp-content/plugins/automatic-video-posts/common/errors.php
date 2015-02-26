<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			errors.php
//		Description:
//			This file renders errors for the plugin's administrative tasks.
//		Copyright:
//			Copyright (c) 2010 Matthew Praetzel.
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
add_action('all_admin_notices','WP_ayvpp_errors');
//                                *******************************                                 //
//________________________________** ERRORS                    **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_errors() {
	global $getWP;

	$e = $getWP->renderErrors();
	if($e) {
		echo '<div class="tern_errors">'.$e.'</div>';
	}
	
	$e = $getWP->renderWarnings();
	if($e) {
		echo '<div class="tern_warnings">'.$e.'</div>';
	}
	
	$a = $getWP->renderAlerts();
	if($a) {
		echo '<div class="tern_alerts">'.$a.'</div>';
	}
}
/*
function WP_ayvpp_errors() {
	global $getWP,$WP_ayvpp_options;
	$o = $getWP->getOption('ayvpp_settings',$WP_ayvpp_options);
	if(empty($o['channels'])) {
		$getWP->addError('Please remember to add at least one channel to automatically import your video posts.');
	}
	
	$e = $getWP->renderErrors();
	if($e) {
		echo '<div class="tern_error tern_error_show"><div><p>'.$e.'</p></div></div>';
	}
	
	$a = $getWP->renderAlerts();
	if($a) {
		echo '<div class="tern_alert tern_error_show"><div><p>'.$a.'</p></div></div>';
	}
}
*/

/****************************************Terminate Script******************************************/
?>