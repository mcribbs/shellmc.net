<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			youtube_video.php
//		Description:
//			This is a class for rendering Google YouTube videos.
//		Date:
//			Created May 7, 2014
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

if(!class_exists('youtube_video')) {

class youtube_video {

/*------------------------------------------------------------------------------------------------
	Variables
------------------------------------------------------------------------------------------------*/

	private $options = array();
	
	private $iframe = '';
	private $video_src = '';
	
	public $meta = array();
	private $meta_fields = array('_ayvpp_video','_ayvpp_video_id','_ayvpp_published','_ayvpp_author','_ayvpp_channel','_ayvpp_thumbs','_ayvpp_auto_play','_ayvpp_related_show');
	

/*------------------------------------------------------------------------------------------------
	Initialization
------------------------------------------------------------------------------------------------*/

	public function __construct($o=array()) {
		global $post;

		$this->options = $o;
		$this->post = $post;
		
		$this->meta_get();
	}
	
/*------------------------------------------------------------------------------------------------
	Videos
------------------------------------------------------------------------------------------------*/
	
	public function video() {

		$this->video_start();
		$this->video_src();
		$this->video_title();
		$this->video_class();
		$this->video_width();
		$this->video_height();
		$this->video_ratio();
		$this->video_settings();
		$this->video_end();
		
		return $this->iframe;
		//return '<iframe title="YouTube video player" class="youtube" width="'.$this->options['video_dims'][0].'" height="'.$this->options['video_dims'][1].'" data-ratio="'.$this->options['video_responsive_ratio'].'" src="'.$this->video_url().'" frameborder="0" allowfullscreen allowTransparency="true"></iframe>';
	}
	private function video_start() {
		$this->iframe = '<iframe ';
	}
	private function video_end() {
		$this->iframe .= ' ></iframe>';
	}
	private function video_src() {
		$this->iframe .= ' src="'.$this->video_url().'" ';
	}
	private function video_title() {
		$this->iframe .= ' title="YouTube video player" ';
	}
	private function video_class() {
		$this->iframe .= ' class="ayvpp '.((isset($this->options['video_responsive']) and $this->options['video_responsive']) ? 'responsive' : '').'" ';
	}
	private function video_width() {
		$this->iframe .= isset($this->options['video_dims'][0]) ? ' width="'.$this->options['video_dims'][0].'" ' : ' width="506" ';
	}
	private function video_height() {
		$this->iframe .= isset($this->options['video_dims'][1]) ? ' height="'.$this->options['video_dims'][1].'" ' : ' height="304" ';
	}
	private function video_ratio() {
		$this->iframe .= (isset($this->options['video_responsive']) and $this->options['video_responsive'] and isset($this->options['video_responsive_ratio'])) ? ' data-ratio="'.$this->options['video_responsive_ratio'].'" ' : '';
	}
	private function video_settings() {
		$this->iframe .= ' frameborder="0" allowfullscreen allowTransparency="true" ';
	}
	public function video_url() {
		$this->video_url_start();
		$this->video_url_auto_play();
		$this->video_url_related_show();
		return $this->video_src;
	}
	private function video_url_start() {
		$this->video_src = 'http://www.youtube.com/embed/'.$this->meta['_ayvpp_video'].'?';
	}
	private function video_url_auto_play() {
		$this->video_src .= ((int)$this->meta['_ayvpp_auto_play'] == 1 and is_single()) ? '&autoplay=1' : '';
	}
	private function video_url_related_show() {
		$this->video_src .= (int)$this->meta['_ayvpp_related_show'] == 1 ? '&rel=1' : '&rel=0';
	}
	public function video_watch_url() {
		return 'http://www.youtube.com/watch?v='.$this->meta['_ayvpp_video'];
	}
	public function video_date() {
		return get_the_time('D, F j, Y g:ia');
	}
	
/*------------------------------------------------------------------------------------------------
	Images
------------------------------------------------------------------------------------------------*/
	
	public function thumb($x='standard') {

		if($x == '*') {
			foreach(array('standard','maxres','high','medium','default') as $v) {
				if(isset($this->meta['_ayvpp_thumbs'][$v]) and !empty($this->meta['_ayvpp_thumbs'][$v])) {
					return $this->meta['_ayvpp_thumbs'][$v];
				}
			}
		}
		
		if(isset($this->meta['_ayvpp_thumbs'][$x])) {
			return $this->meta['_ayvpp_thumbs'][$x];
		}
		elseif(isset($this->meta['_ayvpp_thumbs']['standard'])) {
			return $this->meta['_ayvpp_thumbs']['standard'];
		}
		return false;
	}
	
/*------------------------------------------------------------------------------------------------
	Meta
------------------------------------------------------------------------------------------------*/
	
	public function meta_get() {
		if(!isset($this->post->ID)) {
			return;
		}
		foreach((array)$this->meta_fields as $v) {
			$this->meta[$v] = get_post_meta($this->post->ID,$v,true);
		}
		return $this->meta;
	}
	public function meta_show($e=true) {
		global $post;

		$s = '<div class="ayvpp_video_meta_data"><div class="ayvpp_video_meta">';
		$s .= $this->author_url();
		$s .= '<span>'.$this->video_date().'</span>';
		$s .= '<label>URL:</label><input type="text" value="'.$this->video_url().'" onmouseup="this.select();" /><br />';
		$s .= '<label>Embed:</label><input type="text" value="'.htmlentities($this->video()).'" onmouseup="this.select();" />';
		$s .= '</div></div>';
		
		return $s;
	}
	public function author_url() {
		return '<a href="http://www.youtube.com/channel/'.$this->meta['_ayvpp_channel'].'" target="_blank">'.$this->meta['_ayvpp_author'].'</a>';
	}
	

}

}
	
/****************************************Terminate Script******************************************/
?>