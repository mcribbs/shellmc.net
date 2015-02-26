<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			channels.php
//		Description:
//			This file creates and saves configurable YouTube channels.
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
if(!isset($_GET['page']) or $_GET['page'] !== 'ayvpp-channels') {
	return; 
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_ayvpp_channel_actions');
add_action('init','WP_ayvpp_channel_styles');
add_action('init','WP_ayvpp_channel_scripts');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_channel_styles() {
	wp_enqueue_style('thickbox');
}
function WP_ayvpp_channel_scripts() {
	wp_enqueue_script('thickbox');
	wp_enqueue_script('ayvpp-channels');
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_channel_actions() {
	global $getWP,$ayvpp_options;

	//find action
	if(isset($_REQUEST['action']) or isset($_REQUEST['action2'])) {
		$action = empty($_REQUEST['action']) ? $_REQUEST['action2'] : $_REQUEST['action'];
	}
	
	//return if we shouldn't be doing anything
	if(!isset($_REQUEST['_wpnonce']) or !wp_verify_nonce($_REQUEST['_wpnonce'],'WP_ayvpp_nonce') or !current_user_can('manage_options') or !isset($action)) {
		return false;
	}
	
	//perform action
	switch($action) {
		
		case 'delete' :
			foreach((array)$_REQUEST['items'] as $v) {
				unset($ayvpp_options['channels'][$v]);
			}
			$ayvpp_options = $getWP->getOption('ayvpp_settings',$ayvpp_options,true);
			$getWP->addAlert(__('You have successfully deleted your channel/playlist.','ayvpp'));
			break;
			
		case 'add' :
		
			//edit channel
			if(isset($_REQUEST['item']) and (!empty($_REQUEST['item']) or $_REQUEST['item'] === 0 or $_REQUEST['item'] === '0')) {
				$i = $_REQUEST['item'];
			}
			
			//add channel
			else {
				foreach((array)$ayvpp_options['channels'] as $v) {
					if($v['channel'] == $_POST['channel']) {
						$getWP->addError('You have already added the channel: "'.$_POST['channel'].'".');
						break 2;
					}
				}
				if(empty($ayvpp_options['channels'])) {
					$i = 1;
				}
				else {
					$i = array_keys($ayvpp_options['channels']);
					$i = $i[count($i)-1]+1;
				}
			}
			
			//validate channel
			foreach(array('name','channel','type','author') as $v) {
				if(!isset($_POST[$v]) or empty($_POST[$v])) {
					$getWP->addError('Please fill out all the fields for a channel/playlist.');
					return false;
				}
			}
			
			//see if channel exists
			$a = array();
			if($_POST['type'] == 'channel') {
				$c = new tern_curl();
				
				$r = $c->get(array(
					'url'		=>	'https://www.googleapis.com/youtube/v3/channels/?part=id,snippet,contentDetails&id='.$_POST['channel'].'&key='.$ayvpp_options['key'],
					'options'	=>	array(
						'RETURNTRANSFER'	=>	true,
						//'FOLLOWLOCATION'	=>	true
					),
					'headers'	=>	array(
						'Accept-Charset'	=>	'UTF-8'
					)
				));
				$r = json_decode($r->body);
				
				if(isset($r->items) and !empty($r->items)) {
					$a['playlist'] = $r->items[0]->contentDetails->relatedPlaylists->uploads;
				}
				else {
					$r = $c->get(array(
						'url'		=>	'https://www.googleapis.com/youtube/v3/search?type=channel&part=id,snippet&q='.$_POST['channel'].'&key='.$ayvpp_options['key'],
						'options'	=>	array(
							'RETURNTRANSFER'	=>	true,
							//'FOLLOWLOCATION'	=>	true
						),
						'headers'	=>	array(
							'Accept-Charset'	=>	'UTF-8'
						)
					));
					$r = json_decode($r->body);

					if(!isset($r->items) or empty($r->items)) {
						$getWP->addError('This channel cannot be found.'.(isset($r->error->errors[0]->message) ? 'Google API error: '.$r->error->errors[0]->message : ''));
						return;
					}
					else {
						foreach((array)$r->items as $v) {
							if(strtolower($v->snippet->channelTitle) == strtolower($_POST['channel'])) {
								
								$r = $c->get(array(
									'url'		=>	'https://www.googleapis.com/youtube/v3/channels/?part=id,snippet,contentDetails&id='.$v->id->channelId.'&key='.$ayvpp_options['key'],
									'options'	=>	array(
										'RETURNTRANSFER'	=>	true,
										//'FOLLOWLOCATION'	=>	true
									),
									'headers'	=>	array(
										'Accept-Charset'	=>	'UTF-8'
									)
								));
								$r = json_decode($r->body);
								
								$a['playlist'] = $r->items[0]->contentDetails->relatedPlaylists->uploads;
								break;
							}
						}
						if(!isset($a['playlist']) or empty($a['playlist'])) {
							$getWP->addError('This channel cannot be found.');
							return;
						}
					}
				}
				
			}
			elseif($_POST['type'] == 'playlist') {
				$c = new tern_curl();
				$r = $c->get(array(
					'url'		=>	'https://www.googleapis.com/youtube/v3/playlistItems/?playlistId='.$_POST['channel'].'&part=id&key='.$ayvpp_options['key'],
					'options'	=>	array(
						'RETURNTRANSFER'	=>	true,
						//'FOLLOWLOCATION'	=>	true
					),
					'headers'	=>	array(
						'Accept-Charset'	=>	'UTF-8'
					)
				));
				$r = json_decode($r->body);
				if(!isset($r->items) or empty($r->items)) {
					$getWP->addError('This channel cannot be found. Google API error: '.$r->error->errors[0]->message);
					return;
				}
			}
			
			//save channel
			$ayvpp_options['channels'][$i] = array_merge($a,array(
				'id'				=>	intval($i),
				'name'				=>	$_POST['name'],
				'channel'			=>	$_POST['channel'],
				'post_type'			=>	isset($_POST['publish_type']) ? $_POST['publish_type'] : 'post',
				'type'				=>	isset($_POST['type']) ? $_POST['type'] : 'channel',
				'auto_play'			=>	isset($_POST['auto_play']) ? $_POST['auto_play'] : 0,
				'related_show'		=>	isset($_POST['related_show']) ? $_POST['related_show'] : 0,
				'categories'		=>	isset($_POST['categories']) ? $_POST['categories'] : array(),
				'author'			=>	isset($_POST['author']) ? $_POST['author'] : 1,
				'publish'			=>	isset($_POST['publish']) ? $_POST['publish'] : 0
			));
			$ayvpp_options = $getWP->getOption('ayvpp_settings',$ayvpp_options,true);
			$getWP->addAlert('You have successfully added your channel.');
			break;
			
		default :
			break;
	}
	
}
//                                *******************************                                 //
//________________________________** CHANNELS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_channels() {
	global $ayvpp_options;
	
	include(AYVPP_DIR.'/views/channels.php');
	include(AYVPP_DIR.'/views/channel_add.php');
	
}

/****************************************Terminate Script******************************************/
?>