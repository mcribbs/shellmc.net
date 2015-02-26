<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			youtube_import.php
//		Description:
//			This is a class for parsing Google YouTube Feeds.
//		Date:
//			Created April 30, 2014
//		Version:
//			1.0
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

if(!class_exists('youtube_import')) {

class youtube_import {

/*------------------------------------------------------------------------------------------------
	Variables
------------------------------------------------------------------------------------------------*/

	private $options = array();
	private $memory_max = 32;
	private $page = 0;
	private $import_chunk = false;
	
	private $channels_maxxed = array();
	private $channels_found = array();
	private $channels_for_import = false;
	private $channels_next = array();
	
	private $feed_url = false;
	private $file_text = '';
	
	private $videos_found = array();
	private $videos_added = array();
	
	private $channel_url = '';
	private $channel_feed = array();
	private $channel_id = false;
	
	private $video_id = false;
	private $video_item = array();
	private $video = false;
	private $video_url = false;
	
	private $request_url = 'https://www.googleapis.com/youtube/v3';

/*------------------------------------------------------------------------------------------------
	Initialization
------------------------------------------------------------------------------------------------*/

	public function __construct($o=array(),$x=array()) {
		
		//set options
		ini_set('max_execution_time',600);
		$this->options = $o;
		$this->channels_for_import = $x['channel'];
		//$this->import_page = $x['page'];
		
		//set classes
		$this->classes_set();
		
		//keep multiple imports from happening
		if($this->is_importing()) {
			$this->file_update('<h4 class="req">There is either an import already taking place or the import field in the database needs to be reset.</h4>');
			return false;
		}
		$this->import_set();
		
		//reset file
		$this->file_reset();
		
		//get all existing video IDs
		$this->videos_current_get();
		
		//set channels
		$this->channels_set();
		
		//import videos
		$this->page = 0;
		
		if($x['chunk']) {
			
			session_start();
			
			if(isset($x['reset']) and $x['reset']) {
				$this->chunk_clear();
			}
			
			$this->chunk_set();

			//if($this->page < 20) {
				$this->paging_set();
				$this->videos_import();
			//}
			//else {
			//	$this->channels_force_found();
			//}
			
			if($this->channels_all_found()) {
				$this->chunk_clear();
			}
			else {
				$this->chunk_update();
			}
		}
		else {
			while($this->page < 20) {
				
				if($this->channels_all_found()) {
					break;
				}
				
				$this->paging_set();
				$this->videos_import();
				
				$this->page++;
			}
			
			//complete import
			$this->import_complete();
		}
		
		//make sure we can import again
		$this->import_reset();
		
	}
	private function classes_set() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->file = new fileClass;
	}
	private function options_update() {
		update_option('ayvpp_settings',$this->options);
	}
	private function clean_up() {
		
	}
	
	private function chunk_set() {
		
		$this->import_chunk = true;
		
		if(isset($_SESSION['ayvpp_feed']) and !empty($_SESSION['ayvpp_feed'])) {
			$this->feed = $_SESSION['ayvpp_feed'];
		}
		if(isset($_SESSION['ayvpp_page']) and !empty($_SESSION['ayvpp_page'])) {
			$this->page = $_SESSION['ayvpp_page'];
		}

	}
	private function chunk_update() {
		$_SESSION['ayvpp_feed'] = $this->feed;
		$_SESSION['ayvpp_page'] = $this->page+1;
	}
	private function chunk_clear() {
		unset($_SESSION['ayvpp_feed'],$_SESSION['ayvpp_page']);
	}
	private function import_is_chunk() {
		return $this->import_chunk;
	}
	
/*------------------------------------------------------------------------------------------------
	Videos
------------------------------------------------------------------------------------------------*/
	
	private function paging_set() {
		$this->start = ($this->page*50)+1;
		$this->finish = $this->start+49;
		
	}
	private function videos_current_get() {
		$this->videos_found = $this->wpdb->get_col('select meta_value from '.$this->wpdb->postmeta.' where meta_key="_ayvpp_video_id"');
	}
	private function videos_import() {
		
		foreach((array)$this->channels as $k => $this->channel) {

			//skip if we're only importing from specific channels
			if(is_array($this->channels_for_import) and !in_array((int)$this->channel['id'],$this->channels_for_import)) {
				continue;
			}
			
			$this->channel_name_set();
			
			//check if a channel is configured properly
			if(!$this->channel_is_ready()) {
				$this->file_update('<span>Please set either a type or channel for "'.$this->channel['name'].'".</span>');
				continue;
			}
			
			//if type is playlist and we're looking for additional videos from a channel
			if($this->channel['type'] == 'playlist' and $this->page > 1) {
				//$this->file_update('<h4>Found all videos for playlist "'.$this->channel['name'].'".</h4>');
				$this->playlist_found();
				continue;
			}
			
			//check if channel is maxxed out
			if(in_array($this->channel['name'],$this->channels_maxxed)) {
				$this->channel_found();
				continue;
			}
			elseif($this->page > 0 and !isset($this->feed[$this->channel_name]->nextPageToken)) {
				$this->channel_import_quit();
				$this->channel_found();
				continue;
			}
			
			//get channel
			if($this->channel['type'] == 'channel' and (!isset($this->channel['playlist']) or empty($this->channel['playlist']))) {
				if($this->channel_get()) {
					$this->options['channels'][$k]['playlist'] = $this->channel['playlist'] = $this->channel_playlist;
					$this->options_update();
				}
				else {
					$this->file_update('<h4 class="req">Unable to find channel: "'.$this->channel['name'].'".</h4>');
					continue;
				}
			}
			
			//check channel for errors
			if($this->channel_has_error()) {
				$this->channel_import_quit();
				continue;
			}

			//get latest video
			//$this->channel_get_newest();
			
			//find videos for this channel
			$this->channel_get_videos();
			
			//check videos for errors
			if($this->feed_has_error()) {
				$this->channel_import_quit();
				continue;
			}
			
			//if we have videos
			if(isset($this->feed[$this->channel_name]->items) and !empty($this->feed[$this->channel_name]->items)) {
				
				$this->channels_next[$this->channel_name] = isset($this->feed[$this->channel_name]->nextPageToken) ? $this->feed[$this->channel_name]->nextPageToken : false;
				
				$this->file_update('<h4>Attempting to download videos '.$this->start.' through '.$this->finish.' from '.$this->channel['type'].': "'.$this->channel['name'].'"</h4><h5>Feed URL for this query: <a href="'.$this->feed_url.'" target=_blank">'.$this->feed_url.'</a></h5>');
			
				//add the videos
				$this->videos_add();
			
			}
			
			//no more videos to be found in this channel
			else {
				$this->channel_import_quit();
			}
		
		}
		
	}
	private function videos_add() {
		foreach((array)$this->feed[$this->channel_name]->items as $this->item) {

			//reset loop
			$this->video_id = false;
			$this->video_item = array();
			$this->video = false;
			
			//get video ID
			$this->video_item_set_id();
			
			//compile video info
			$this->video_item_set();
			
			//check if video exists
			if(in_array($this->video_id,$this->videos_found)) {
				if(!$this->import_is_chunk()) {
					$this->channel_found();
				}
				//$this->file_update('<span class="req">Video already exists: '.$this->video_item['post_title'].'</span>');
				continue;
			}
			else {
				$this->videos_found[] = $this->video_id;
				$this->videos_added[$this->channel_name][] = $this->video_id;
			}

			//insert video
			$this->video_item_insert();
			if($this->video) {
				$this->file_update('<span class="imported">Video successfully imported: '.$this->video_item['post_title'].'</span>');
				$this->video_item_meta();
			}

		}
	}
	private function videos_video_get() {
		$this->parse_video_url_set();
		$this->parse_video_get();
	}
	private function video_item_set() {
		$this->video_item_set_post_type();
		$this->video_item_set_date();
		$this->video_item_set_author();
		$this->video_item_set_title();
		//$this->video_item_set_slug();
		$this->video_item_set_content();
		$this->video_item_set_cats();
		$this->video_item_set_publish();
	}
	private function video_item_set_id() {
		$this->video_id = false;
		if(is_string($this->item->id)) {
			$this->video_id = $this->item->id;
		}
		elseif(is_object($this->item->id) and isset($this->item->id->videoId)) {
			$this->video_id = $this->item->id->videoId;
		}
	}
	private function video_item_set_post_type() {
		$this->video_item['post_type'] = isset($this->channel['post_type']) ? $this->channel['post_type'] : 'post';
	}
	private function video_item_set_date() {
		$this->video_item['post_date'] = gmdate('Y-m-d H:i:s',strtotime($this->item->snippet->publishedAt));
	}
	private function video_item_set_author() {
		$this->video_item['post_author'] = $this->channel['author'];
	}
	private function video_item_set_title() {
		$this->video_item['post_title'] = $this->item->snippet->title;
	}
	private function video_item_set_slug() {
		$this->video_item['post_name'] = wp_unique_post_slug(sanitize_title($this->item->snippet->title));
	}
	private function video_item_set_content() {
		
		$s = (string)$this->item->snippet->description;
		
		if($this->options['content_truncate'] and (int)$this->options['content_truncate_after'] > 0) {
			$s = explode(' ',$s);
			if(count($s) > (int)$this->options['content_truncate_after']) {
				$s = array_merge(array_splice($s,0,(int)$this->options['content_truncate_after']),array('<!--more-->'),$s);
			}
			$s = implode(' ',$s);
		}
		
		$this->video_item['post_content'] = $s;
	}
	private function video_item_set_cats() {
		$this->video_item['post_category'] = $this->channel['categories'];
	}
	private function video_item_set_tags() {

	}
	private function video_item_set_publish() {
		$this->video_item['post_status'] = 'draft';
		if(isset($this->channel['publish']) and (int)$this->channel['publish'] == 1) {
			$this->video_item['post_status'] = 'publish';
		}
	}
	private function video_item_insert() {
		$this->video = wp_insert_post($this->video_item);
	}
	private function video_item_meta() {
		$this->video_item_thumbs();
		update_post_meta($this->video,'_ayvpp_video',$this->item->snippet->resourceId->videoId);
		update_post_meta($this->video,'_ayvpp_video_id',$this->video_id);
		update_post_meta($this->video,'_ayvpp_video_url','http://www.youtube.com/watch?v='.$this->item->snippet->resourceId->videoId);
		update_post_meta($this->video,'_ayvpp_published',$this->item->snippet->publishedAt);
		update_post_meta($this->video,'_ayvpp_author',$this->item->snippet->channelTitle);
		update_post_meta($this->video,'_ayvpp_channel',$this->item->snippet->channelId);
	
		update_post_meta($this->video,'_ayvpp_auto_play',(int)$this->channel['auto_play']);
		update_post_meta($this->video,'_ayvpp_show_related',(isset($this->channel['related_show']) ? (int)$this->channel['related_show'] : 0));
	}
	private function video_item_thumbs() {
		$a = array(
			'default'	=>	isset($this->item->snippet->thumbnails->default->url) ? $this->item->snippet->thumbnails->default->url : '',
			'medium'	=>	isset($this->item->snippet->thumbnails->medium->url) ? $this->item->snippet->thumbnails->medium->url : '',
			'high'		=>	isset($this->item->snippet->thumbnails->high->url) ? $this->item->snippet->thumbnails->high->url : '',
			'standard'	=>	isset($this->item->snippet->thumbnails->standard->url) ? $this->item->snippet->thumbnails->standard->url : '',
			'maxres'	=>	isset($this->item->snippet->thumbnails->maxres->url) ? $this->item->snippet->thumbnails->maxres->url : ''
		);
		update_post_meta($this->video,'_ayvpp_thumbs',$a);
		
		foreach(array('standard','maxres','high','medium','default') as $v) {
			if(isset($this->item->snippet->thumbnails->$v->url)) {
				update_post_meta($this->video,'_thumbnail_id',$this->item->snippet->thumbnails->$v->url);
				break;
			}
		}
	}

/*------------------------------------------------------------------------------------------------
	Channels
------------------------------------------------------------------------------------------------*/
	
	private function channels_set() {
		$this->channels = $this->options['channels'];
	}
	private function channel_is_ready() {
		if(!isset($this->channel['type']) or empty($this->channel['type']) or !isset($this->channel['channel']) or empty($this->channel['channel'])) {
			return false;
		}
		return true;
	}
	private function channel_name_set() {
		$this->channel_name = $this->channel['name'];
	}
	private function channel_get() {
		$this->channel_id = false;
		$this->channel_playlist = false;
		
		$this->parse_channel_search_url_set();
		$this->parse_channel_search_get();
		
		if($this->channel_search_has_error()) {
			return false;
		}

		foreach((array)$this->channel_search_feed->items as $v) {
			if(strtolower($v->snippet->channelTitle) == strtolower($this->channel['channel'])) {
				$this->channel_id = $v->id->channelId;
				
				if(!$this->channel_id) {
					return false;
				}
				
				$this->parse_channel_url_set();
				$this->parse_channel_get();
				$this->channel_playlist = $this->channel_feed->items[0]->contentDetails->relatedPlaylists->uploads;
				break;
			}
		}

		if(!$this->channel_playlist) {
			return false;
		}
		return true;
	}
	private function channel_get_newest() {
		$this->newest = $this->wpdb->get_row('select a.*,b.meta_value as published from wp_postmeta as a join wp_postmeta as b on (a.post_id = b.post_id) where a.meta_key="_ayvpp_channel" and a.meta_value="'.$this->channel['channel'].'" and b.meta_key="_ayvpp_published" order by b.meta_value desc limit 1');
	}
	private function channel_get_videos() {
		
		//set feed URL
		if(!$this->parse_feed_url_set()) {
			return false;
		}
		
		//get feed
		$this->parse_feed_get();
		
	}
	private function channel_import_quit() {
		$this->channels_maxxed[] = $this->channel['name'];
	}
	private function channel_found() {
		
		if(is_array($this->channels_for_import) and !in_array($this->channel['id'],$this->channels_for_import)) {
			return;
		}
		
		if(!in_array($this->channel['name'],$this->channels_found)) {
			$this->file_update('<h4>Found all videos for channel "'.$this->channel['name'].'".</h4>');
			$this->channels_found[] = $this->channel['name'];
		}
	}
	private function playlist_found() {
		
		if(is_array($this->channels_for_import) and !in_array($this->channel['id'],$this->channels_for_import)) {
			return;
		}
		
		if(!in_array($this->channel['name'],$this->channels_found)) {
			$this->file_update('<h4>Found all videos for playlist "'.$this->channel['name'].'".</h4>');
			$this->channels_found[] = $this->channel['name'];
		}
	}
	private function channels_all_found() {
		if(count($this->channels_found) == count($this->channels)) {
			return true;
		}
		return false;
	}
	private function channel_search_has_error() {
		if(isset($this->channel_search_feed->error)) {
			$this->file_update('<span class="req">We received the following error from YouTube for channel "'.$this->channel['name'].'": ('.$this->channel_search_feed->error->errors[0]->message.')</span>');
			return true;
		}
		return false;
	}
	private function channel_has_error() {
		if(isset($this->channel_feed->error)) {
			$this->file_update('<span class="req">We received the following error from YouTube for channel "'.$this->channel['name'].'": ('.$this->channel_feed->error->errors[0]->message.')</span>');
			return true;
		}
		return false;
	}
	private function channels_force_found() {
		foreach((array)$this->channels as $k => $this->channel) {
			$this->channel_found();
		}
	}
	
/*------------------------------------------------------------------------------------------------
	Parse Channel
------------------------------------------------------------------------------------------------*/

	private function parse_channel_search_url_set() {

		$this->channel_search_url = $this->request_url.'/search/?type=channel&q='.$this->channel['channel'];
	
		$this->parse_channel_search_url_set_key();
		$this->parse_channel_search_url_set_part();
	}
	private function parse_channel_search_url_set_key() {
		$this->channel_search_url .= '&key='.$this->options['key'];
	}
	private function parse_channel_search_url_set_part() {
		$this->channel_search_url .= '&part=id,snippet';
	}
	private function parse_channel_search_get() {
		$c = new tern_curl();
		$r = $c->get(array(
			'url'		=>	$this->channel_search_url,
			'options'	=>	array(
				'RETURNTRANSFER'	=>	true,
				//'FOLLOWLOCATION'	=>	true
			),
			'headers'	=>	array(
				'Accept-Charset'	=>	'UTF-8'
			)
		));
		$this->channel_search_feed = json_decode($r->body);
	}
	
	private function parse_channel_url_set() {

		$this->channel_url = $this->request_url.'/channels/?id='.$this->channel_id;
	
		$this->parse_channel_url_set_key();
		$this->parse_channel_url_set_part();
	}
	private function parse_channel_url_set_key() {
		$this->channel_url .= '&key='.$this->options['key'];
	}
	private function parse_channel_url_set_part() {
		$this->channel_url .= '&part=id,snippet,contentDetails';
	}
	private function parse_channel_get() {
		$c = new tern_curl();
		$r = $c->get(array(
			'url'		=>	$this->channel_url,
			'options'	=>	array(
				'RETURNTRANSFER'	=>	true,
				//'FOLLOWLOCATION'	=>	true
			),
			'headers'	=>	array(
				'Accept-Charset'	=>	'UTF-8'
			)
		));
		$this->channel_feed = json_decode($r->body);
	}

/*------------------------------------------------------------------------------------------------
	Parse Video
------------------------------------------------------------------------------------------------*/

	private function parse_video_url_set() {
		if(!$this->video_id) {
			return false;
		}
		$this->video_url = $this->request_url.'/videos/?id='.$this->video_id;
	
		$this->parse_video_url_set_key();
		$this->parse_video_url_set_part();
	}
	private function parse_video_url_set_key() {
		$this->video_url .= '&key='.$this->options['key'];
	}
	private function parse_video_url_set_part() {
		$this->video_url .= '&part=id,snippet';
	}
	private function parse_video_get() {
		$c = new tern_curl();
		$r = $c->get(array(
			'url'		=>	$this->video_url,
			'options'	=>	array(
				'RETURNTRANSFER'	=>	true,
				//'FOLLOWLOCATION'	=>	true
			),
			'headers'	=>	array(
				'Accept-Charset'	=>	'UTF-8'
			)
		));
		$this->video = json_decode($r->body);
	}

/*------------------------------------------------------------------------------------------------
	Parse Feed
------------------------------------------------------------------------------------------------*/

	private function parse_feed_url_set() {
		if($this->channel['type'] == 'channel') {
			//$this->feed_url = $this->request_url.'/playlistItems/?channelId='.$this->channel_id;
			$this->feed_url = $this->request_url.'/playlistItems/?playlistId='.$this->channel['playlist'];
		}
		elseif($this->channel['type'] == 'playlist') {
			$this->feed_url = $this->request_url.'/playlistItems/?playlistId='.$this->channel['channel'];
		}

		if(!$this->feed_url) {
			return false;
		}
		
		$this->parse_feed_url_set_key();
		$this->parse_feed_url_set_type();
		$this->parse_feed_url_set_part();
		$this->parse_feed_url_set_how_many();
		$this->parse_feed_url_set_order();
		$this->parse_feed_url_set_when();
		
		return true;
	}
	private function parse_feed_url_set_key() {
		$this->feed_url .= '&key='.$this->options['key'];
	}
	private function parse_feed_url_set_type() {
		if($this->channel['type'] == 'channel') {
			//$this->feed_url .= '&type=video';
		}
	}
	private function parse_feed_url_set_part() {
		//if($this->channel['type'] == 'channel') {
		//	$this->feed_url .= '&part=id,snippet';
		//}
		//elseif($this->channel['type'] == 'playlist') {
		//	$this->feed_url .= '&part=contentDetails,id,snippet,status';
		//}
		$this->feed_url .= '&part=contentDetails,id,snippet,status';
	}
	private function parse_feed_url_set_how_many() {
		$this->feed_url .= '&maxResults=50';
	}
	private function parse_feed_url_set_order() {
		//$this->feed_url .= '&order=date';
	}
	private function parse_feed_url_set_when() {
		//if($this->newest) {
		//	$this->feed_url .= '&publishedAfter='.$this->newest['published'];
		//}
		
		if($this->page > 0 and $this->feed[$this->channel_name]->nextPageToken) {
			$this->feed_url .= '&pageToken='.$this->feed[$this->channel_name]->nextPageToken;
		}
		
	}
	private function parse_feed_get() {
		$c = new tern_curl();
		$r = $c->get(array(
			'url'		=>	$this->feed_url,
			'options'	=>	array(
				'RETURNTRANSFER'	=>	true,
				//'FOLLOWLOCATION'	=>	true
			),
			'headers'	=>	array(
				'Accept-Charset'	=>	'UTF-8'
			)
		));
		$this->feed[$this->channel_name] = json_decode($r->body);
	}
	private function feed_has_error() {
		if(isset($this->feed[$this->channel_name]->error)) {
			$this->file_update('<span class="req">We received the following error from YouTube for channel "'.$this->channel['name'].'": ('.$this->feed[$this->channel_name]->error->errors[0]->message.')</span>');
			return true;
		}
		return false;
	}

/*------------------------------------------------------------------------------------------------
	Progress
------------------------------------------------------------------------------------------------*/
	
	function is_importing() {
		$this->is_importing = get_option('ayvpp_importing');
		if($this->is_importing and $this->is_importing > time()-86400) {
			return true;
		}
		return false;
	}
	function import_complete() {
		$this->file_update('<h4 id="ayvpp_complete">Your import is complete!</h4>');
	}
	function import_set() {
		update_option('ayvpp_importing',time());
	}
	function import_reset() {
		update_option('ayvpp_importing',0);
	}
	function file_reset() {
		$this->file->createFile('ayvpp.txt','',WP_CONTENT_DIR.'/cache');
		$this->file_text = '';
	}
	function file_get() {
		return $this->file->contents(WP_CONTENT_DIR.'/cache/ayvpp.txt');
	}
	function file_update($c='') {
		$this->file_text .= $c;
		if(!$this->import_is_chunk()) {
			$this->file->createFile('ayvpp.txt',$this->file_text,WP_CONTENT_DIR.'/cache');
		}
	}
	function message_get() {
		return $this->file_text;
	}
	function progress() {
		return array(
			'added'				=>	$this->videos_added,
			'channels_next'		=>	$this->channels_next,
			'channels_num'		=>	is_array($this->channels_for_import) ? count($this->channels_for_import) : count($this->channels),
			'channels_maxxed'	=>	count($this->channels_found),
			'message'			=>	$this->message_get()
		);
	}
	
/*------------------------------------------------------------------------------------------------
	Memory
------------------------------------------------------------------------------------------------*/
	
	function memory_set_max() {
		$this->memory_max = isset($_GET['memory']) ? (int)$_GET['memory'] : $this->memory_max;
	}
	function memory_limit_get() {
		$x = ini_get('memory_limit');
		preg_match("/[0-9]+/",$x,$m);
		return (int)$m[0];
	}
	function memory_check() {
		$m = $this->memory_limit_get();
		if(memory_get_usage() > (($m*1048576)-5242880)) {
			$m += 5;
			if($m <= $this->memory_max) {
				ini_set('memory_limit',$m.'M');
				return $m;
			}
			return false;
		}
		return true;
	}
	
}

}
	
/****************************************Terminate Script******************************************/
?>