<?php
/*
Plugin Name: UserPro
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: The ultimate user profiles and memberships plugin for WordPress.
Version: 2.13
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

define('userpro_url',plugin_dir_url(__FILE__ ));
define('userpro_path',plugin_dir_path(__FILE__ ));

	/* init */


//Start Yogesh added usermeta entry for search members
function userpro_add_userin_meta() {

	// Activation code here...
	$args=array();
	$allusers=get_users( $args );
		
	foreach($allusers as $user)
	{
		$key = 'display_name';
		$single = true;
		$display_name = get_user_meta($user->ID, $key, $single );
		if($display_name!=$user->display_name)
		{
			add_user_meta($user->ID, $key, $user->display_name );
		
		}
	
	}
	
	
}
register_activation_hook( __FILE__, 'userpro_add_userin_meta' );

//End Yogesh usermeta entry for search members

	function userpro_init() {
		
		if(!isset($_SESSION))
		{
			session_start();
		}
		
		global $userpro;
		
		$userpro->do_uploads_dir();
		
		load_plugin_textdomain('userpro', false, dirname(plugin_basename(__FILE__)) . '/languages');
		
		/* include libs */
		require_once userpro_path . '/lib/envato/Envato_marketplaces.php';
		if (!class_exists('UserProMailChimp')){
			require_once userpro_path . '/lib/mailchimp/MailChimp.php';
		}
		
	}
	add_action('init', 'userpro_init');

	/* functions */
		require_once userpro_path . "functions/_trial.php";
		require_once userpro_path . "functions/ajax.php";
		require_once userpro_path . "functions/api.php";
		require_once userpro_path . "functions/badge-functions.php";
		require_once userpro_path . "functions/common-functions.php";
		require_once userpro_path . "functions/custom-alerts.php";
		require_once userpro_path . "functions/defaults.php";
		require_once userpro_path . "functions/fields-filters.php";
		require_once userpro_path . "functions/fields-functions.php";
		require_once userpro_path . "functions/fields-hooks.php";
		require_once userpro_path . "functions/fields-setup.php";
		require_once userpro_path . "functions/frontend-publisher-functions.php";
		require_once userpro_path . "functions/global-actions.php";
		require_once userpro_path . "functions/buddypress.php";
		require_once userpro_path . "functions/hooks-actions.php";
		require_once userpro_path . "functions/hooks-filters.php";
		require_once userpro_path . "functions/icons-functions.php";
		require_once userpro_path . "functions/initial-setup.php";
		require_once userpro_path . "functions/mail-functions.php";
		require_once userpro_path . "functions/member-search-filters.php";
		require_once userpro_path . "functions/memberlist-functions.php";
		require_once userpro_path . "functions/msg-functions.php";
		require_once userpro_path . "functions/security.php";
		require_once userpro_path . "functions/shortcode-extras.php";
		require_once userpro_path . "functions/shortcode-functions.php";
		require_once userpro_path . "functions/shortcode-main.php";
		require_once userpro_path . "functions/shortcode-private-content.php";
		require_once userpro_path . "functions/shortcode-social-connect.php";
		require_once userpro_path . "functions/social-connect.php";
		require_once userpro_path . "functions/template-redirects.php";
		require_once userpro_path . "functions/terms-agreement.php";
		require_once userpro_path . "functions/user-functions.php";

	/* administration */
	if (is_admin()){
		foreach (glob(userpro_path . 'admin/*.php') as $filename) { include $filename; }
	}
	
	/* updates */
	foreach (glob(userpro_path . 'updates/*.php') as $filename) { include $filename; }
	
	/* load addons */
	require_once userpro_path . 'addons/multiforms/index.php';
	require_once userpro_path . 'addons/badges/index.php';
	require_once userpro_path . 'addons/social/index.php';
	require_once userpro_path . 'addons/emd/index.php';
	require_once userpro_path . 'addons/redirects/index.php';
