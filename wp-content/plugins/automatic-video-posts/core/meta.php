<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			meta.php
//		Description:
//			This file compiles video specific meta fields for posts.
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
$pages = array('post.php','edit.php','post-new.php','page.php','page-new.php');
if(!in_array($GLOBALS['pagenow'],$pages)) {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_ayvpp_meta_scripts');
add_action('admin_menu','WP_ayvpp_box');
add_action('save_post','WP_ayvpp_save_post');
add_action('publish_post','WP_ayvpp_save_post');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_meta_scripts() {
	wp_enqueue_script('ayvpp-meta');
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_save_post($i) {
	
	global $ayvpp_options,$getWP;
	
	if(!isset($_POST['WP_ayvpp_nonce']) or !wp_verify_nonce($_POST['WP_ayvpp_nonce'],AYVPP_DIR.'/core/meta.php') or !$i or !current_user_can('edit_post',$i)) {
		return;
	}
	
	if(isset($_POST['WP_ayvpp_action']) and $_POST['WP_ayvpp_action'] == 'sync') {
		$c = new tern_curl();
		$r = $c->get(array(
			'url'		=>	'https://www.googleapis.com/youtube/v3/videos/?part=id,snippet,contentDetails&id='.$_POST['_ayvpp_video'].'&key='.$ayvpp_options['key'],
			'options'	=>	array(
				'RETURNTRANSFER'	=>	true
			),
			'headers'	=>	array(
				'Accept-Charset'	=>	'UTF-8'
			)
		));
		$r = json_decode($r->body);
		if(isset($r->items[0]->id)) {
			remove_action('save_post','WP_ayvpp_save_post');
			remove_action('publish_post','WP_ayvpp_save_post');
			if(wp_update_post(array(
				'ID'			=>	$i,
				'post_date'		=>	gmdate('Y-m-d H:i:s',strtotime($r->items[0]->snippet->publishedAt)),
				'post_title'	=>	$r->items[0]->snippet->title,
				//'post_name'		=>	wp_unique_post_slug(sanitize_title($r->items[0]->snippet->title)),
				'post_content'	=>	WP_ayvpp_meta_content((string)$r->items[0]->snippet->description)
			))) {
				$getWP->addAlert(__('you successfully synbced this video post with YouTube.','ayvpp'));
			}
			else {
				$getWP->addError(__('There was an error syncing your video post with YouTube. Please try again.','ayvpp'));
			}
			
		}
		else {
			$getWP->addError(__('There was an error syncing your video post with YouTube. Please try again.','ayvpp'));
		}
	}
	
	if(!empty($_POST['_ayvpp_video'])) {
		update_post_meta($i,'_ayvpp_video',$_POST['_ayvpp_video']);
		update_post_meta($i,'_ayvpp_video_url',$_POST['_ayvpp_video_url']);
		update_post_meta($i,'_ayvpp_author',$_POST['_ayvpp_author']);
		update_post_meta($i,'_ayvpp_auto_play',(int)$_POST['_ayvpp_auto_play']);
		update_post_meta($i,'_ayvpp_show_related',(int)$_POST['_ayvpp_show_related']);
	}
}
function WP_ayvpp_meta_content($s='') {
	global $ayvpp_options;
	if($ayvpp_options['content_truncate'] and (int)$ayvpp_options['content_truncate_after'] > 0) {
		$s = explode(' ',$s);
		if(count($s) > (int)$ayvpp_options['content_truncate_after']) {
			$s = array_merge(array_splice($s,0,(int)$ayvpp_options['content_truncate_after']),array('<!--more-->'),$s);
		}
		$s = implode(' ',$s);
	}
	return $s;
}
//                                *******************************                                 //
//________________________________** META BOXES                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_box() {
	global $post;
	
	//if(!isset($_GET['post'])) {
	//	return;
	//}
	
	//$post = get_post($_GET['post']);
	//$video = new youtube_video();
	//if(isset($video->meta['_ayvpp_video']) and !empty($video->meta['_ayvpp_video'])) {
		add_meta_box('ayvpp_meta_box','Automatic Video Posts','WP_ayvpp_meta','post','normal');
	//}
}
function WP_ayvpp_meta() {
	global $post;
	
	$meta = get_post_meta($post->ID);
	include(AYVPP_DIR.'/views/meta.php');
	
}

/****************************************Terminate Script******************************************/
?>