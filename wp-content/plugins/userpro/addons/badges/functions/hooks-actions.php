<?php

	/* add custom badges */
	add_filter('userpro_show_badges', 'userpro_badges_show');
	function userpro_badges_show($user_id){
		global $userpro_badges;
		$output = null;
		
		/* Find user badges (get_user_meta - _userpro_badges) */
		$get_badges = $userpro_badges->get_badges($user_id);
		if (is_array($get_badges)){
			foreach($get_badges as $key => $badge) {
				if (isset($badge['badge_url'])) {
					$sanitized = preg_replace('/\s*/', '', $badge['badge_title'] );
					$sanitized = strtolower($sanitized);
					$output .= '<img class="userpro-profile-badge userpro-profile-badge-'.$sanitized.'" src="'.$badge['badge_url'].'" alt="" title="'.$badge['badge_title'].'" />';
				}
			}
		}
		
		return $output;
	
	}