<?php

	/* Follow user */
	add_action('wp_ajax_nopriv_userpro_sc_follow', 'userpro_sc_follow');
	add_action('wp_ajax_userpro_sc_follow', 'userpro_sc_follow');
	function userpro_sc_follow(){
		global $userpro_social;
		extract($_POST);
		
		$userpro_social->do_follow($to, $from);
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* Unfollow user */
	add_action('wp_ajax_nopriv_userpro_sc_unfollow', 'userpro_sc_unfollow');
	add_action('wp_ajax_userpro_sc_unfollow', 'userpro_sc_unfollow');
	function userpro_sc_unfollow(){
		global $userpro_social;
		extract($_POST);

		$userpro_social->do_unfollow($to, $from);
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* refresh activity */
	add_action('wp_ajax_nopriv_userpro_sc_refreshactivity', 'userpro_sc_refreshactivity');
	add_action('wp_ajax_userpro_sc_refreshactivity', 'userpro_sc_refreshactivity');
	function userpro_sc_refreshactivity(){
		global $userpro, $userpro_social;
		extract($_POST);
		$output['res'] = '';
		
		if (!$activity_user || $activity_user == 'undefined'){
			$activity_user = null;
		}

		$activity = $userpro_social->activity($user_id, $offset, $per_page, $activity_user);
		if (isset($activity) && is_array($activity)):
		foreach($activity as $timestamp=>$status) :
		
		$content = str_replace('{timestamp}', $userpro->time_elapsed( $status['timestamp'] ), $status['status']);
		
		$output['res'] .= '<div class="userpro-sc">';
		
		$output['res'] .= $content;
						
		$output['res'] .= '<div class="userpro-sc-btn">'.$userpro_social->follow_text($status['user_id'], get_current_user_id()).'</div></div>';
		
		endforeach;
		endif;
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* load activity */
	add_action('wp_ajax_nopriv_userpro_sc_loadactivity', 'userpro_sc_loadactivity');
	add_action('wp_ajax_userpro_sc_loadactivity', 'userpro_sc_loadactivity');
	function userpro_sc_loadactivity(){
		global $userpro, $userpro_social;
		extract($_POST);
		$output['res'] = '';
		
		if (!$activity_user || $activity_user == 'undefined'){
			$activity_user = null;
		}

		$activity = $userpro_social->activity($user_id, $offset, $per_page, $activity_user);
		if (isset($activity) && is_array($activity)):
		foreach($activity as $timestamp=>$status) :
		
		$content = str_replace('{timestamp}', $userpro->time_elapsed( $status['timestamp'] ), $status['status']);
		
		$output['res'] .= '<div class="userpro-sc">';
		
		$output['res'] .= $content;
						
		$output['res'] .= '<div class="userpro-sc-btn">'.$userpro_social->follow_text($status['user_id'], get_current_user_id()).'</div></div>';
		
		endforeach;
		endif;
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}