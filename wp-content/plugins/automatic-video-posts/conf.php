<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			conf.php
//		Description:
//			This file configures the Wordpress Plugin - Automatic Video Posts Plugin
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

define('AYVPP_URL',get_bloginfo('wpurl').'/wp-content/plugins/automatic-video-posts');
define('AYVPP_ROOT',get_bloginfo('wpurl'));
define('AYVPP_DIR',dirname(__FILE__));
$ayvpp_version = array(4,8,1);

$WP_ayvpp_options = array(
	'updater_checked'			=>	0,
	'key'						=>	'',
	'channels'					=>	array(),
	'cron'						=>	6,
	'last_import'				=>	'',
	'content_display_meta'		=>	1,
	'content_truncate'			=>	1,
	'content_truncate_after'	=>	20,
	'video_responsive'			=>	1,
	'video_responsive_ratio'	=>	'16:9',
	'video_dims'				=>	array(506,304),
	'video_related_show'		=>	0,
	'video_post_list_show'		=>	0,
	'thumbs_show'				=>	1,
	'verified'					=>	false,
	'serial'					=>	''
);

//                                *******************************                                 //
//________________________________** FILE CLASS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
require_once(dirname(__FILE__).'/class/file.php');
$getFILE = new fileClass;
//                                *******************************                                 //
//________________________________** LOAD CLASSES              **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
$l = $getFILE->directoryList(array(
	'dir'	=>	dirname(__FILE__).'/class/',
	'rec'	=>	true,
	'flat'	=>	true,
	'depth'	=>	1
));
if(is_array($l)) {
	foreach($l as $k => $v) {
		require_once($v);
	}
}
//                                *******************************                                 //
//________________________________** LOAD CORE FILES           **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //

$l = $getFILE->directoryList(array(
	'dir'	=>	dirname(__FILE__).'/common/',
	'rec'	=>	true,
	'flat'	=>	true,
	'depth'	=>	1,
	'ext'	=>	array('php')
));
foreach((array)$l as $k => $v) {
	require_once($v);
}

if(WP_ayvpp_is_activated()) {
	if(is_admin()) {
		$l = $getFILE->directoryList(array(
			'dir'	=>	dirname(__FILE__).'/core/',
			'rec'	=>	true,
			'flat'	=>	true,
			'depth'	=>	1,
			'ext'	=>	array('php')
		));
	}
	else {
		$l = $getFILE->directoryList(array(
			'dir'	=>	dirname(__FILE__).'/front/',
			'rec'	=>	true,
			'flat'	=>	true,
			'depth'	=>	1,
			'ext'	=>	array('php')
		));
	}
	foreach((array)$l as $k => $v) {
		require_once($v);
	}
}
elseif($_GET['page'] !== 'ayvpp-activate') {
	$getWP->addError(__('You need to input your key before you can use the Automatic Video Posts Plugin. <a href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=ayvpp-activate">Click Here!</a>','ayvpp'));
}
unset($l,$k,$v);
//                                *******************************                                 //
//________________________________** CHECK DIRECTORIES         **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
if(!is_file(WP_CONTENT_DIR.'/cache/ayvpp.txt') and !$getFILE->createFile('ayvpp.txt','',WP_CONTENT_DIR.'/cache')) {
	$getWP->addError('Automatic Video Posts Plugin file ('.WP_CONTENT_DIR.'/cache/ayvpp.txt) either does not exist or is not writable. You cannot properly use the "Import" aspects of this plugin until this is resolved.');
}
if(!$getFILE->isWritableDirectory(WP_CONTENT_DIR.'/cache/timthumb')) {
	$getWP->addError('Automatic Video Posts Plugin folder ('.WP_CONTENT_DIR.'/cache/timthumb) either does not exist or is not writable. You cannot properly use the "thumbnail" aspects of this plugin until this is resolved.');
}
//                                *******************************                                 //
//________________________________** INITIALIZE PLUGIN         **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_ayvpp_init',-9999);
function WP_ayvpp_init() {
	global $getWP,$WP_ayvpp_options,$ayvpp_options;
	$ayvpp_options = $getWP->getOption('ayvpp_settings',$WP_ayvpp_options);
}

/****************************************Terminate Script******************************************/
?>