<?php

class userpro_admin {

	var $options;

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userpro';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( userpro_path . 'index.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('admin_menu', array(&$this, 'add_menu'), 9);
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
	}

	/* Create export download link */
	function create_export_download_link($echo = false, $setting='userpro_export_options'){
		$site_url = get_bloginfo('url');
		$args = array(
			$setting => 'safe_download',
			'nonce' => wp_create_nonce($setting)
		);
		$export_url = add_query_arg($args, $site_url);
		if ($echo === true)
			echo '<a href="'.$export_url.'" class="button" target="_blank">'.__('Download Export','userpro').'</a>';
		elseif ($echo == 'url')
			return $export_url;
		return '<a href="'.$export_url.'" class="button" target="_blank">'.__('Download Export','userpro').'</a>';
	}
	
	function admin_init() {
		
		$this->tabs = array(
			'settings' => __('Settings','userpro'),
			'fields' => __('Fields','userpro'),
			'requests' => sprintf(__('%s Requests','userpro'), $this->get_pending_verify_requests_count() ),
			'css' => __('Custom CSS','userpro'),
			'mail' => __('Email Notifications','userpro'),
			'restrict' => __('Restrict Content','userpro'),
			'pages' => __('Setup Pages','userpro'),
			'woo' => __('WooCommerce','userpro'),
			'fieldroles' => __('Role-based Fields','userpro'),
			'exportusers' => __('Export Users','userpro'),
			'import_export' => __('Import/Export','userpro'),
			'licensing' => __('Licensing','userpro'),
		);
		$this->default_tab = 'settings';
		
		$this->options = get_option('userpro');
		if (!get_option('userpro')) {
			update_option('userpro', userpro_default_options() );
		}
		
	}
	
	function get_pending_verify_requests_count(){
		$count = 0;
		
		// verification status
		$pending = get_option('userpro_verify_requests');
		if (is_array($pending) && count($pending) > 0){
			$count = count($pending);
		}
		
		// waiting email approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		// waiting admin approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending_admin',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		if ($count > 0){
			return '<span class="upadmin-bubble-new">'.$count.'</span>';
		}
	}
	
	function get_pending_verify_requests_count_only(){
		$count = 0;
		
		// verification status
		$pending = get_option('userpro_verify_requests');
		if (is_array($pending) && count($pending) > 0){
			$count = count($pending);
		}
		
		// waiting email approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		// waiting admin approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending_admin',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		if ($count > 0){
			return $count;
		}
	}
	
	function delete_pending_request($user_id){
		$arr = get_option('userpro_verify_requests');
		if (isset($arr) && is_array($arr)){
			$arr = array_diff($arr, array( $user_id ));
			update_option('userpro_verify_requests', $arr);
		}
	}
	
	function admin_head(){
		$screen = get_current_screen();
		$slug = $this->slug;
		$icon = userpro_url . "admin/images/$slug-32.png";
		echo '<style type="text/css">';
			if (in_array( $screen->id, array( $slug ) ) || strstr($screen->id, $slug) ) {
				print "#icon-$slug {background: url('{$icon}') no-repeat left;}";
			}
		echo '</style>';
		if(is_rtl()){
			?>
				<script type="text/javascript">
					jQuery(function(){
						jQuery('select').attr('class' , jQuery('select').attr('class')+'chosen-rtl');
						jQuery('.chosen-container-single').attr('class' , 'chosen-container chosen-container-single chosen-rtl');
					});
				</script>
			<?php
		}
	}

	function add_styles(){
	
		wp_register_style('userpro_admin', userpro_url.'admin/css/admin.css');
		wp_enqueue_style('userpro_admin');
		
		if ( userpro_get_option('rtl') ) {
			$css = 'css/userpro.min.css';
		} else {
			$css = 'css/userpro-rtl.min.css';
		}
		wp_register_style('userpro_admin_fa', userpro_url . $css);
		wp_enqueue_style('userpro_admin_fa');
		
		wp_register_style('userpro_chosen', userpro_url . 'skins/default/style.css');
		wp_enqueue_style('userpro_chosen');
		
		wp_register_script('userpro_chosen', userpro_url . 'admin/scripts/admin-chosen.js');
		wp_enqueue_script('userpro_chosen');
		
		wp_register_script( 'userpro_admin', userpro_url.'admin/scripts/admin.js', array( 
			'jquery',
			'jquery-ui-core',
			'jquery-ui-draggable',
			'jquery-ui-droppable',
			'jquery-ui-sortable'
		) );
		wp_enqueue_script( 'userpro_admin' );
		
	}
	
	function add_menu() {
	
		$pending_count = $this->get_pending_verify_requests_count_only();
		$pending_title = esc_attr( sprintf(__( '%d new verification requests','userpro'), $pending_count ) );
		if ($pending_count > 0){
		$menu_label = sprintf( __( 'UserPro %s','userpro' ), "<span class='update-plugins count-$pending_count' title='$pending_title'><span class='update-count'>" . number_format_i18n($pending_count) . "</span></span>" );
		} else {
		$menu_label = __('UserPro','userpro');
		}
		
		add_menu_page( __('UserPro','userpro'), $menu_label, 'manage_options', $this->slug, array(&$this, 'admin_page'), userpro_url .'admin/images/'.$this->slug.'-16.png', '199.150');
		
		do_action('userpro_admin_menu_hook');
	
	}

	function admin_tabs( $current = null ) {
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->slug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->slug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	function get_tab_content() {
		$screen = get_current_screen();
		if( strstr($screen->id, $this->slug ) ) {
			if ( isset ( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = $this->default_tab;
			}
			require_once userpro_path.'admin/panels/'.$tab.'.php';
		}
	}
	
	function do_action(){
		global $userpro;
		if ($_GET['userpro_act'] == 'clear_unused_uploads'){
			$files = glob( $userpro->upload_base_dir . '*');
			$i = 0;
			foreach($files as $file){
				if(is_file($file)) {
					$i++;
					unlink($file);
				}
			}
			echo '<div class="updated"><p><strong>'.sprintf(__('%s files deleted.','userpro'), $i).'</strong></p></div>';
		}
		if ($_GET['userpro_act'] == 'clear_deleted_users') {
			$files = glob( $userpro->upload_base_dir . '*');
			$i = 0;
			foreach($files as $file){
				if(!is_file($file)) {
					if (!$userpro->user_exists( basename($file) )) {
						$i++;
						$userpro->delete_folder($file);
					}
				}
			}
			echo '<div class="updated"><p><strong>'.sprintf(__('%s unused folders deleted.','userpro'), $i).'</strong></p></div>';
		}
		if ($_GET['userpro_act'] == 'clear_cache') {
			global $userpro;
			$userpro->clear_cache();
			echo '<div class="updated"><p><strong>'.sprintf(__('%s Members Cache Clear .','userpro'), $i).'</strong></p></div>';
		}
		if ($_GET['userpro_act'] == 'reset_online_users') {
			delete_transient('userpro_users_online');
			echo '<div class="updated"><p><strong>'.__('Online users data is reset.','userpro').'</strong></p></div>';
		}
		if ($_GET['userpro_act'] == 'clear_activity') {
			delete_option('userpro_activity');
			echo '<div class="updated"><p><strong>'.__('Activity stream has been reset.','userpro').'</strong></p></div>';
		}
	}
	
	function save() {
	
		/* restrict tab */
		if (isset($_GET['tab']) && $_GET['tab'] == 'restrict'){
			$this->options['userpro_restricted_pages'] = '';
		}
		
		/* field roles tab */
		if (isset($_GET['tab']) && $_GET['tab'] == 'fieldroles'){
			$fields = get_option('userpro_fields');
			foreach($fields as $key => $field){
				$this->options[$key.'_roles'] = '';
			}
		}
		
		/* roles that can view profiles */
		if (isset($_GET['tab']) && $_GET['tab'] == 'settings'){
			$this->options['roles_can_view_profiles'] = '';
		}
		
		/* other post fields */
		foreach($_POST as $key => $value) {
			if ($key != 'submit') {
				if (!is_array($_POST[$key])) {
					$this->options[$key] = stripslashes( esc_attr($_POST[$key]) );
				} else {
					$this->options[$key] = $_POST[$key];
				}
			}
		}
		
		update_option('userpro', $this->options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro').'</strong></p></div>';
	}

	function reset() {
		update_option('userpro', userpro_default_options() );
		$this->options = array_merge( $this->options, userpro_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro').'</strong></p></div>';
	}
	
	function rebuild_pages() {
		userpro_first_setup($rebuild=1);
		echo '<div class="updated"><p><strong>'.__('Your plugin pages have been rebuilt successfully.','userpro').'</strong></p></div>';
	}
	
	function new_group(){
		global $userpro;
		if (isset($_POST['up-group-name'])){
			if (empty($_POST['up-group-name'])){
				echo '<div class="error"><p><strong>'.__('You did not specify a group name.','userpro').'</strong></p></div>';
			} else {
				$group = strtolower($_POST['up-group-name']);
				$group = trim($group);
				$group = str_replace(' ','',$group);
				$group = str_replace('-','',$group);
				if ( isset($userpro->groups[$group]) ) {
					echo '<div class="error"><p><strong>'.__('This group exists already.','userpro').'</strong></p></div>';
				} else {
					//create group
					$userpro->create_group( $group );
					echo '<div class="updated"><p><strong>'.__('Group created.','userpro').'</strong></p></div>';
				}
			}
		}
	}
	
	function woo_sync() {
		userpro_admin_woo_sync();
		echo '<div class="updated"><p><strong>'.__('WooCommerce fields have been added.','userpro').'</strong></p></div>';
	}
	
	function woo_sync_del(){
		userpro_admin_woo_sync_erase();
		echo '<div class="updated"><p><strong>'.__('WooCommerce fields have been removed.','userpro').'</strong></p></div>';
	}
	
	function reinstall(){
		global $userpro;
		
		// trash current pages
		$pages = get_option('userpro_pages') + get_option('userpro_sc_pages');
		foreach( $pages as $page_id ) {
			wp_delete_post( $page_id, true );
		}
		
		// delete existing pages from settings
		delete_option('userpro_pages');
		
		// trash userpro options
		foreach( wp_load_alloptions() as $k => $v) {
			if (strstr($k, 'userpro')){
				delete_option( $k );
			}
		}
		
		// install default fields again
		userpro_init_setup();

		userpro_update_1006();
		userpro_update_1024();
		userpro_update_1036();
		userpro_update_1046();
		userpro_update_1048();
		userpro_update_1050();
		
		// update icons
		if (!get_option('userpro_pre_icons_setup') ) {
			$userpro->update_field_icons();
		}
		
		echo '<div class="updated"><p><strong>'.__('UserPro has been reset to factory settings.','userpro').'</strong></p></div>';
	}
	
	function verify_license() {
		global $userpro;
		$code = $_POST['userpro_code'];
		if ($code == ''){
			echo '<div class="error"><p><strong>'.__('Please enter a purchase code.','userpro').'</strong></p></div>';
		} else {
			if ( $userpro->verify_purchase($code, '13z89fdcmr2ia646kphzg3bbz0jdpdja', 'DeluxeThemes', '5958681') ){
				$userpro->validate_license($code);
				echo '<div class="updated fade"><p><strong>'.__('Thanks for activating UserPro!','userpro').'</strong></p></div>';
			} else {
				$userpro->invalidate_license($code);
				echo '<div class="error"><p><strong>'.__('You have entered an invalid purchase code or the Envato API could be down at the moment.','userpro').'</strong></p></div>';
			}
		}
	}
	
	function import_groups(){
		if (isset( $_POST['userpro_import_groups'] ) && $_POST['userpro_import_groups'] != ''){
			$import_code = $_POST['userpro_import_groups'];
			$import_code = base64_decode($import_code);
			$import_code = unserialize($import_code);
			if (is_array($import_code)){
			update_option('userpro_fields_groups', $import_code);
			echo '<div class="updated fade"><p><strong>'.__('Your UserPro field groups have been imported.','userpro').'</strong></p></div>';
			} else {
			echo '<div class="error"><p><strong>'.__('This is not a valid import file.','userpro').'</strong></p></div>';
			}
		}
	}
	
	function import_fields(){
		if (isset( $_POST['userpro_import_fields'] ) && $_POST['userpro_import_fields'] != ''){
			$import_code = $_POST['userpro_import_fields'];
			$import_code = base64_decode($import_code);
			$import_code = unserialize($import_code);
			if (is_array($import_code)){
			update_option('userpro_fields', $import_code);
			echo '<div class="updated fade"><p><strong>'.__('Your UserPro fields have been imported.','userpro').'</strong></p></div>';
			} else {
			echo '<div class="error"><p><strong>'.__('This is not a valid import file.','userpro').'</strong></p></div>';
			}
		}
	}
	
	function import_settings(){
		if (isset( $_POST['userpro_import'] ) && $_POST['userpro_import'] != ''){
			$import_code = $_POST['userpro_import'];
			$import_code = base64_decode($import_code);
			$import_code = unserialize($import_code);
			if (is_array($import_code)){
			update_option('userpro', $import_code);
			echo '<div class="updated fade"><p><strong>'.__('Your UserPro settings have been imported.','userpro').'</strong></p></div>';
			} else {
			echo '<div class="error"><p><strong>'.__('This is not a valid import file.','userpro').'</strong></p></div>';
			}
		}
	}
	
	function export_users() {
		global $userpro;
		
		if (!file_exists( $userpro->upload_base_dir . 'downloads/' )) {
			@mkdir( $userpro->upload_base_dir . 'downloads/', 0777, true);
		}
		
		$export = array( 'id' => 'ID', 'user_login' => 'Username', 'user_email' => 'Email');
		
		$export = array_merge( array_keys($export), array_keys($userpro->fields) );

		if (isset($_POST['exp_exclude']) && !empty($_POST['exp_exclude'])) {
		$export = array_diff( array_values($export), explode(',',$_POST['exp_exclude']) );
		}
		
		if (isset($_POST['exp_include']) && !empty($_POST['exp_include'])){
		$export = explode(',',$_POST['exp_include']);
		}
		
		$list[] = $export;
		
		$export = array_unique($export);

		$users = get_users('number='.$_POST['exp_users_num'].'&offset=0');
		foreach($users as $user) {
			foreach($export as $k=>$v) {
				$values[] = userpro_profile_data($v, $user->ID);
			}
			$list[] = $values;
			$values = null;
		}
		
		$file = $userpro->upload_base_dir . 'downloads/' . time() . '.csv';
	
		$fp = fopen( $file, 'w');
		foreach ($list as $fields) {
			fputcsv($fp, $fields);
		}
		fclose($fp);
		
		echo '<div class="updated fade"><p><strong>'.sprintf(__('Download the exported users list <a href="%s">here</a>.','userpro'), $userpro->upload_base_url . 'downloads/' . time() . '.csv').'</strong></p></div>';
	
	}

	function admin_page() {
	
		if (isset($_POST['export_users'])){
			$this->export_users();
		}
		
		if (isset($_POST['import_settings'])){
			$this->import_settings();
		}
		
		if (isset($_POST['import_fields'])){
			$this->import_fields();
		}
		
		if (isset($_POST['import_groups'])){
			$this->import_groups();
		}

		if (isset($_POST['verify-license'])){
			$this->verify_license();
		}
	
		if (isset($_POST['userpro-reinstall'])){
			$this->reinstall();
		}

		if (isset($_POST['up-group-new'])){
			$this->new_group();
		}
		
		if (isset($_POST['submit'])) {
			$this->save();
		}
		
		if (isset($_GET['userpro_act'])){
			$this->do_action();
		}

		if (isset($_POST['reset-options'])) {
			$this->reset();
		}
		
		if (isset($_POST['rebuild-pages'])) {
			$this->rebuild_pages();
		}
		
		if (isset($_POST['woosync'])) {
			$this->woo_sync();
		}
		
		if (isset($_POST['woosync_del'])){
			$this->woo_sync_del();
		}
		
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
		
			<?php userpro_admin_bar(); ?>
			
			<h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>

			<div class="<?php echo $this->slug; ?>-admin-contain">
				
				<?php $this->get_tab_content(); ?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }

}

$userpro_admin = new userpro_admin();
