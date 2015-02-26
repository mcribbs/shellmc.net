<?php

class userpro_sc_api {

	function __construct() {

	}
	
	// new notification
	function new_notification($to, $user_id=0, $action) {
		global $userpro;
		
		$builtin = array(
			'{USERPRO_FOLLOWER_NAME}' => userpro_profile_data('display_name', $user_id),
			'{USERPRO_FOLLOWER_LINK}' => $userpro->permalink( $user_id ),
			'{USERPRO_BLOG_NAME}' => userpro_get_option('mail_from_name'),
			'{USERPRO_MY_PROFILE}' => $userpro->permalink( $to->ID ),
		);
		$search = array_keys($builtin);
		$replace = array_values($builtin);

		switch($action){
			
			case 'new_follow':
				$subject = userpro_sc_get_option('mail_new_follow_s');
				$message = userpro_sc_get_option('mail_new_follow');
				break;
				
		}
		
		/* Send Mail */
		$headers = 'From: '.userpro_get_option('mail_from_name').' <'.userpro_get_option('mail_from').'>' . "\r\n";
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
		wp_mail( $to->user_email, $subject, $message, $headers );
	
	}
	
	// log action
	function log_action($action, $user_id, $var1=null, $var2=null, $var3=null) {
		global $userpro, $userpro_social;
		
		$activity = get_option('userpro_activity');
		
		$timestamp = current_time('timestamp');
		
		$status = '';
		
		switch($action){
		
			case 'verified':
		
				$status .= '<div class="userpro-sc-img" data-key="profilepicture"><a href="'.$userpro->permalink( $user_id ).'">'.get_avatar( $user_id, '50' ).'</a></div><div class="userpro-sc-i"><div class="userpro-sc-i-name"><a href="'. $userpro->permalink( $user_id ) .'" title="'. __('View Profile','userpro'). '">'. userpro_profile_data('display_name', $user_id).'</a>'. userpro_show_badges( $user_id, false, array('custom'));
				$status .= '<span class="userpro-sc-i-info">';
				$status .= __('is now a verified account.','userpro');
				$status .= '</span>';
				$status .= '</div><div class="userpro-sc-i-time">{timestamp}</div></div><div class="userpro-clear"></div>';
				$activity[$user_id][$timestamp] = array('user_id' => $user_id, 'status' => $status, 'timestamp' => $timestamp );
				break;
		
			case 'new_post':
		
				$status .= '<div class="userpro-sc-img" data-key="profilepicture"><a href="'. $userpro->permalink( $user_id ).'">'.get_avatar( $user_id, '50' ).'</a></div><div class="userpro-sc-i"><div class="userpro-sc-i-name"><a href="'. $userpro->permalink( $user_id ) .'" title="'. __('View Profile','userpro'). '">'. userpro_profile_data('display_name', $user_id).'</a>'. userpro_show_badges( $user_id, false, array('custom'));
				$status .= '<span class="userpro-sc-i-info">';
				
				$status .= sprintf(__('has published a <a href="%s">new %s</a>.','userpro'), get_permalink($var1), $var3);
				
				if ($var2 != '') {
				$status .= '<span class="userpro-sc-i-sp">"'.$var2.'"</span>';
				}
				
				$status .= '</span>';
				$status .= '</div><div class="userpro-sc-i-time">{timestamp}</div></div><div class="userpro-clear"></div>';
				$activity[$user_id][$timestamp] = array('user_id' => $user_id, 'status' => $status, 'timestamp' => $timestamp );
				break;
			
			case 'update_post':
			
				$status .= '<div class="userpro-sc-img" data-key="profilepicture"><a href="'.$userpro->permalink( $user_id ).'">'.get_avatar( $user_id, '50' ).'</a></div><div class="userpro-sc-i"><div class="userpro-sc-i-name"><a href="'. $userpro->permalink( $user_id ) .'" title="'. __('View Profile','userpro'). '">'. userpro_profile_data('display_name', $user_id).'</a>'. userpro_show_badges( $user_id, false, array('custom'));
				$status .= '<span class="userpro-sc-i-info">';
				
				$status .= sprintf(__('has updated a <a href="%s">%s</a>.','userpro'), get_permalink($var1), $var3);
				
				if ($var2 != '') {
				$status .= '<span class="userpro-sc-i-sp">"'.$var2.'"</span>';
				}
				
				$status .= '</span>';
				$status .= '</div><div class="userpro-sc-i-time">{timestamp}</div></div><div class="userpro-clear"></div>';
				$activity[$user_id][$timestamp] = array('user_id' => $user_id, 'status' => $status, 'timestamp' => $timestamp );
				break;
				
			case 'new_comment':
			
				$status .= '<div class="userpro-sc-img" data-key="profilepicture"><a href="'.$userpro->permalink( $user_id ).'">'.get_avatar( $user_id, '50' ).'</a></div><div class="userpro-sc-i"><div class="userpro-sc-i-name"><a href="'. $userpro->permalink( $user_id ) .'" title="'. __('View Profile','userpro'). '">'. userpro_profile_data('display_name', $user_id).'</a>'. userpro_show_badges( $user_id, false, array('custom'));
				$status .= '<span class="userpro-sc-i-info">';
				$status .= __('has posted a new comment on:','userpro');
				$status .= '<span class="userpro-sc-i-sp">"<a href="'.get_permalink($var1).'">'.$var2.'</a>"</span>';
				$status .= '</span>';
				$status .= '</div><div class="userpro-sc-i-time">{timestamp}</div></div><div class="userpro-clear"></div>';
				$activity[$user_id][$timestamp] = array('user_id' => $user_id, 'status' => $status, 'timestamp' => $timestamp );
				break;
				
			case 'new_follow':
			
				$dest = get_userdata($var1);
			
				$status .= '<div class="userpro-sc-img" data-key="profilepicture"><a href="'.$userpro->permalink( $user_id ).'">'.get_avatar( $user_id, '50' ).'</a></div><div class="userpro-sc-i"><div class="userpro-sc-i-name"><a href="'. $userpro->permalink( $user_id ) .'" title="'. __('View Profile','userpro'). '">'. userpro_profile_data('display_name', $user_id).'</a>'. userpro_show_badges( $user_id, false, array('custom'));
				$status .= '<span class="userpro-sc-i-info">';
				$status .= sprintf(__('has started following <a href="%s">%s</a>','userpro'), $userpro->permalink( $dest->ID ), userpro_profile_data('display_name', $dest->ID) );
				$status .= '</span>';
				$status .= '</div><div class="userpro-sc-i-time">{timestamp}</div></div><div class="userpro-clear"></div>';
				$activity[$user_id][$timestamp] = array('user_id' => $user_id, 'status' => $status, 'timestamp' => $timestamp );

				/* notification */
				$followers=get_user_meta( $dest->ID,'followers_email' ); 
				if (userpro_sc_get_option('notification_on_follow') &&  ($followers[0]=="unsubscribed")){
					$this->new_notification( $dest, $user_id, 'new_follow' );
				}
		
				break;
				
			case 'stop_follow':
			
				$dest = get_userdata($var1);
			
				$status .= '<div class="userpro-sc-img" data-key="profilepicture"><a href="'.$userpro->permalink( $user_id ).'">'.get_avatar( $user_id, '50' ).'</a></div><div class="userpro-sc-i"><div class="userpro-sc-i-name"><a href="'. $userpro->permalink( $user_id ) .'" title="'. __('View Profile','userpro'). '">'. userpro_profile_data('display_name', $user_id).'</a>'. userpro_show_badges( $user_id, false, array('custom'));
				$status .= '<span class="userpro-sc-i-info">';
				$status .= sprintf(__('has stopped following <a href="%s">%s</a>','userpro'), $userpro->permalink( $dest->ID ), userpro_profile_data('display_name', $dest->ID) );
				$status .= '</span>';
				$status .= '</div><div class="userpro-sc-i-time">{timestamp}</div></div><div class="userpro-clear"></div>';
				$activity[$user_id][$timestamp] = array('user_id' => $user_id, 'status' => $status, 'timestamp' => $timestamp );
				break;
				
			case 'new_user' :
			
				$status .= '<div class="userpro-sc-img" data-key="profilepicture"><a href="'.$userpro->permalink( $user_id ).'">'.get_avatar( $user_id, '50' ).'</a></div><div class="userpro-sc-i"><div class="userpro-sc-i-name"><a href="'. $userpro->permalink( $user_id ) .'" title="'. __('View Profile','userpro'). '">'. userpro_profile_data('display_name', $user_id).'</a>'. userpro_show_badges( $user_id, false, array('custom'));
				$status .= '<span class="userpro-sc-i-info">';
				$status .= __('has just registered!','userpro');
				$status .= '</span>';
				$status .= '</div><div class="userpro-sc-i-time">{timestamp}</div></div><div class="userpro-clear"></div>';
				$activity[$user_id][$timestamp] = array('user_id' => $user_id, 'status' => $status, 'timestamp' => $timestamp );
				break;
			

		}
		
		// If disable activity is turned off
		if (userpro_get_option('disable_activity_log') == 0 ) {


			update_option('userpro_activity', $activity);
		}
		
	}
	
	// retrieve activity for this user
	function activity($user_id=0, $offset=0, $per_page=10, $activity_user=0){
		
		// private
		if ($user_id){
		
			$keys = get_user_meta($user_id, '_userpro_following_ids', true);
			$activity = (array)get_option('userpro_activity');
			if (is_array($keys)){
			$result = array_intersect_key($activity, $keys);
			if (isset($result) && is_array($result) && $result != '' && $result != array('') ){
				if (isset($activity_user) && $activity_user != '') { $result = array_intersect_key($result, array_flip( explode(',',$activity_user) )); }
				foreach($result as $uid => $actions){
					foreach($actions as $k=>$action){
						$action = str_replace(  userpro_profile_data('display_name', $user_id), __('you','userpro'), $action);
						$activities[$k] = $action;
					}
				}
				if (isset($activities)){
				// show activities
				$activities = apply_filters('userpro_private_activity_filter', $activities);
				krsort($activities);
				$activities = array_slice($activities, $offset, $per_page );
				return $activities;
				}
			}
			}
		
		// public
		} else {
		
			$result = (array)get_option('userpro_activity');
			if ( isset($result) && is_array($result) && $result != '' && $result != array('') ){
				if (isset($activity_user) && $activity_user != '') { $result = array_intersect_key($result, array_flip( explode(',',$activity_user) )); }
				foreach($result as $uid => $actions){
					foreach($actions as $k=>$action){
						$action = str_replace(  userpro_profile_data('display_name', $user_id), __('you','userpro'), $action);
						$activities[$k] = $action;
					}
				}
				if (isset($activities)){
				// show activities
				$activities = apply_filters('userpro_public_activity_filter', $activities);
				krsort($activities);
				$activities = array_slice($activities, $offset, $per_page );
				return $activities;
				}
			}
		
		}
	}
	
	// get array of "following"
	function following($user_id){
		$array = get_user_meta($user_id, '_userpro_following_ids', true);
		if (is_array($array)){
			return $array;
		} else {
			return 0;
		}
	}
	
	// get array of "followers"
	function followers($user_id){
		$array = get_user_meta($user_id, '_userpro_followers_ids', true);
		if (is_array($array)){
			return $array;
		} else {
			return 0;
		}
	}
	
	// show following count
	function following_count($user_id){
		$arr = get_user_meta($user_id, '_userpro_following_ids', true);
		if (is_array($arr) && !empty($arr)){
		$count = count($arr);
		} else {
		$count = 0;
		}
		$count = number_format_i18n($count);
		return sprintf(__('<span>%s</span> following','userpro'), $count);
	}
	
	// show following count plain
	function following_count_plain($user_id){
		$arr = get_user_meta($user_id, '_userpro_following_ids', true);
		if (is_array($arr) && !empty($arr)){
		$count = count($arr);
		} else {
		$count = 0;
		}
		$count = number_format_i18n($count);
		return $count;
	}
	
	// remove a following user
	function unset_following($user_id, $id_to_remove) {
		$arr = get_user_meta($user_id, '_userpro_following_ids', true);
		unset( $arr[ $id_to_remove ] );
		update_user_meta($user_id, '_userpro_following_ids', $arr);
	}
	
	// show followers count
	function followers_count($user_id){
		$arr = get_user_meta($user_id, '_userpro_followers_ids', true);
		if (is_array($arr) && !empty($arr)){
		$count = count($arr);
		} else {
		$count = 0;
		}
		$count = number_format_i18n($count);
		return sprintf(__('<span>%s</span> followers','userpro'), $count);
	}
	
	// show followers count plain
	function followers_count_plain($user_id){
		$arr = get_user_meta($user_id, '_userpro_followers_ids', true);
		if (is_array($arr) && !empty($arr)){
		$count = count($arr);
		} else {
		$count = 0;
		}
		$count = number_format_i18n($count);
		return $count;
	}
	
	// remove a follower user
	function unset_follower($user_id, $id_to_remove) {
		$arr = get_user_meta($user_id, '_userpro_followers_ids', true);
		unset( $arr[ $id_to_remove ] );
		update_user_meta($user_id, '_userpro_followers_ids', $arr);
	}
	
	// follow user
	function do_follow($to, $from) {
	
		$followers_ids = get_user_meta($to, '_userpro_followers_ids', true);
		$followers_ids[$from] = 1;
		update_user_meta($to, '_userpro_followers_ids', $followers_ids);

		$following_ids = get_user_meta($from, '_userpro_following_ids', true);
		$following_ids[$to] = 1;
		update_user_meta($from, '_userpro_following_ids', $following_ids);
		
		$array = array( 'to' => $to, 'from' => $from );
		do_action('userpro_sc_after_follow', $array);
		
	}
	
	// unfollow user
	function do_unfollow($to, $from) {
	
		$followers_ids = get_user_meta($to, '_userpro_followers_ids', true);
		if (isset($followers_ids[$from])) unset($followers_ids[$from]);
		update_user_meta($to, '_userpro_followers_ids', $followers_ids);
		
		$following_ids = get_user_meta($from, '_userpro_following_ids', true);
		if (isset($following_ids[$to])) unset($following_ids[$to]);
		update_user_meta($from, '_userpro_following_ids', $following_ids);
		
		$array = array( 'to' => $to, 'from' => $from );
		do_action('userpro_sc_after_unfollow', $array);

	}
	
	// mutual follow
	function mutual_follow($user1, $user2){
		$user1_fans = get_user_meta($user1, '_userpro_following_ids', true);
		$user2_fans = get_user_meta($user2, '_userpro_following_ids', true);
		if (isset($user1_fans[$user2]) && isset($user2_fans[$user1]))
			return true;
		return false;
	}
	
	// show text
	function follow_text($to, $from){
		if ($to != $from && userpro_is_logged_in() ) {
			$iamfollowing = get_user_meta($from, '_userpro_following_ids', true);
			if (isset($iamfollowing[$to])){
				return '<a href="#" class="userpro-button userpro-follow following" data-follow-text="'.__('Follow','userpro').'" data-unfollow-text="'.__('Unfollow','userpro').'" data-following-text="'.__('Following','userpro').'" data-follow-to="'.$to.'" data-follow-from="'.$from.'">'.__('Following','userpro').'</a>';
			} else {
				return '<a href="#" class="userpro-button secondary userpro-follow notfollowing" data-follow-text="'.__('Follow','userpro').'" data-unfollow-text="'.__('Unfollow','userpro').'" data-following-text="'.__('Following','userpro').'" data-follow-to="'.$to.'" data-follow-from="'.$from.'"><i class="userpro-icon-share"></i>'.__('Follow','userpro').'</a>';
			}
		}
	}
	
}

$userpro_social = new userpro_sc_api();