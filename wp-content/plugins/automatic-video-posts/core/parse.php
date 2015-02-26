<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			parse.php
//		Description:
//			This file imports Google YouTube feeds.
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
//________________________________** INITIALIZE VARIABLES      **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //

//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
if($GLOBALS['pagenow'] != 'admin-ajax.php') {
	add_action('init','WP_ayvpp_add_posts',10);
}
//                                *******************************                                 //
//________________________________** IMPORT VIDEOS             **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_add_posts($x=false) {
	global $getWP,$ayvpp_options;
	
	//don't import if we haven't waited long enough
	if(!$x and $ayvpp_options['last_import'] > (time()-($ayvpp_options['cron']*3600))) {
		return false;
	}
	
	//import videos
	$parse = new youtube_import($ayvpp_options,array(
		'channel'	=>	((isset($_REQUEST['channel']) and !empty($_REQUEST['channel'])) ? array((int)$_REQUEST['channel']) : false),
		'chunk'		=>	($x ? true : false),
		'reset'		=>	(isset($_REQUEST['page']) and (int)$_REQUEST['page'] == 1 ? true : false),
	));
	
	//finish import
	$ayvpp_options['last_import'] = time();
	$ayvpp_options = $getWP->getOption('ayvpp_settings',$ayvpp_options,true);
	
	return $parse->progress();
	
}

/****************************************Terminate Script******************************************/
?>