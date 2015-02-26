<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			internal.php
//		Description:
//			This file is responsible for internal functions.
//		Copyright:
//			Copyright (c) 2010 Matthew Praetzel.
//		License:
//			This software is licensed under the terms of the End User License Agreement (EULA) 
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit:
//			http://www.ternstyle.us/automatic-video-posts-plugin-for-wordpress/license.html
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
//add_action('init','WP_ayvpp_update_check');
add_action('after_plugin_row_automatic-video-posts/init.php','WP_ayvpp_update_check');
add_action('init','WP_ayvpp_activate');
//add_filter('all_plugins','WP_ayvpp_update');
//                                *******************************                                 //
//________________________________** FUNCTIONS                 **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_update($p='') {
	
}
function WP_ayvpp_update_check() {
	
	global $getWP,$WP_ayvpp_option,$ayvpp_version;
	$ayvpp_options = $getWP->getOption('ayvpp_settings',$WP_ayvpp_options);
	
	if(is_admin()/* and $ayvpp_options['updater_checked']+86400 <= time()*/) {
		
		$c = new tern_curl;
		$m = $c->get(array(
			'url'			=>	'http://www.ternstyle.us/automatic-video-posts-plugin-for-wordpress/software/activate/version/',
			'options'		=>	array(
				'RETURNTRANSFER'	=>	true
			)
		));
		$r = json_decode($m->body);
		
		for($i=0;$i<3;$i++) {
			if((int)$r->version[$i] > (int)$ayvpp_version[$i]) {
				
				echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message">There is a new version of Automatic Video Posts available. <a href="'.$r->changelog.'" title="Automatic Video Posts" target="_blank">View version  details</a>. <em>Automatic update is unavailable for this plugin.</em> <a href="'.$r->login.'" title="Automatic Video Posts" target="_blank">Download Now.</a></div></td></tr>';
				
				/*
				$t = get_site_transient('update_plugins');
				$t->response['automatic-video-posts/init.php'] = array(
					//'id'			=>	'',
					'slug'			=>	'automatic-video-posts',
					'plugin'		=>	'automatic-video-posts/init.php',
					'new_version'	=>	implode('.',$r->version),
					'url'			=>	$r->url
				);
				
				set_site_transient('update_plugins',$t);
				*/
				
				break;
			}
		}
		
		//$ayvpp_options['updater_checked'] = time();
		//$ayvpp_options = $getWP->getOption('ayvpp_settings',$ayvpp_options,true);
		
	}
	
}
function WP_ayvpp_is_activated() {
	global $getWP,$WP_ayvpp_options;
	$ayvpp_options = $getWP->getOption('ayvpp_settings',$WP_ayvpp_options);
	
	if(isset($ayvpp_options['verified']) and ($ayvpp_options['verified'] === true or (int)$ayvpp_options['verified'] === 1)) {
		return true;
	}
	return false;
}
function WP_ayvpp_activate() {
	global $wpdb,$getWP,$ayvpp_options;
	
	switch($_REQUEST['action']) {
		
		case 'tern_activate' :
		
			if(!wp_verify_nonce($_REQUEST['_wpnonce'],'WP_ayvpp_nonce') and !current_user_can('administrator')) {
				$getWP->addError('We were unable to activate your plugin for security reasons.');
				return;
			}
		
			$c = new tern_curl;
			$m = $c->post(array(
				'url'			=>	'http://ternstyle.us/automatic-video-posts-plugin-for-wordpress/software/activate/',
				'data'			=>	array(
					'key'		=>	$_POST['serial'],
					'email'		=>	$_POST['email'],
					'domain'	=>	get_bloginfo('home')
				),
				'options'		=>	array(
					'RETURNTRANSFER'	=>	true
				)
			));
			$r = json_decode($m->body);

			if(isset($r->error)) {
				$getWP->addError(__($r->error,'ayvpp'));
				$ayvpp_options['verified'] = false;
				//$ayvpp_options['level'] = 0;
			}
			elseif(isset($r->success)) {
				$getWP->addAlert(__('Great Success!','ayvpp'));
				$ayvpp_options['verified'] = true;
				//$ayvpp_options['level'] = (int)$r['Response']['Level'];
			}

			$ayvpp_options['email'] = $_POST['email'];
			$ayvpp_options['serial'] = $_POST['serial'];
			$ayvpp_options = $getWP->getOption('ayvpp_settings',$ayvpp_options,true);

			break;
			
		case 'tern_deactivate_start' :
		
			
			
			break;
			
		case 'tern_deactivate' :
			
			if(!wp_verify_nonce($_REQUEST['_wpnonce'],'WP_ayvpp_nonce') and !current_user_can('administrator')) {
				$getWP->addError('We were unable to activate your plugin for security reasons.');
				return;
			}
			
			//reset plugin
			$videos = $wpdb->get_col("select post_id from $wpdb->postmeta where meta_key='_ayvpp_video'");
			foreach((array)$videos as $v) {
				if(!wp_delete_post($v,true)) {
					$getWP->addError('There was an error while deleting a video post "'.get_the_title($v).'". Please try again.');
					return false;
				}
			}
			
			
			//remotely deactivate
			$c = new tern_curl;
			$m = $c->post(array(
				'url'			=>	'http://ternstyle.us/automatic-video-posts-plugin-for-wordpress/software/activate/deactivate/',
				'data'			=>	array(
					'key'		=>	$ayvpp_options['serial'],
					'email'		=>	$ayvpp_options['email'],
					'domain'	=>	get_bloginfo('home')
				),
				'options'		=>	array(
					'RETURNTRANSFER'	=>	true
				)
			));
			$r = json_decode($m->body);

			if(isset($r->error)) {
				$getWP->addError(__($r->error,'ayvpp'));
			}
			elseif(isset($r->success)) {
				$getWP->addAlert(__('Great Success!','ayvpp'));
				$ayvpp_options['verified'] = false;
				$ayvpp_options['email'] = '';
				$ayvpp_options['serial'] ='';
				$ayvpp_options = $getWP->getOption('ayvpp_settings',$ayvpp_options,true);
			}

			break;
		
		default :
			break;
		
	}
	
	
}
function WP_ayvpp_set_activate() {
	global $getWP,$ayvpp_options;
?>
<div class="wrap"><div id="icon-options-general" class="icon32"><br /></div>
	<h2>Automatic Video Posts - <?php _e('Activate!','ayvpp'); ?></h2>
	
	<?php if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'tern_deactivate_start') { ?>
	
	<form method="post" action="">
		<h3>Are you sure you wish to deactivate the plugin?!</h3>
		<h4>All your videos will be deleted and the plugin will be reset.</h4>
		<p>This will take some time.</p>
		<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Deactivate','ayvpp') ;?>" /></p>
		<input type="hidden" name="action" value="tern_deactivate" />
	</form>
	
	<?php } else { ?>
	
	<form method="post" action="">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="email"><?php _e('Your email address','ayvpp'); ?>:</label></th>
				<td>
					<input type="text" name="email" value="<?php echo $ayvpp_options['email']; ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="serial"><?php _e('Activation Key','ayvpp'); ?>:</label></th>
				<td>
					<input type="text" name="serial" value="<?php echo $ayvpp_options['serial']; ?>" />
				</td>
			</tr>
		</table>
		<?php if(!WP_ayvpp_is_activated()) { ?>
			<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Activate!','ayvpp') ;?>" /></p>
			<input type="hidden" name="action" value="tern_activate" />
		<?php } else { ?>
			<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Deactivate','ayvpp') ;?>" /></p>
			<input type="hidden" name="action" value="tern_deactivate_start" />
		<?php } ?>
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ayvpp_nonce'); ?>" />
	</form>
	
	<?php } ?>
	
</div>
<?php }

/****************************************Terminate Script******************************************/
?>