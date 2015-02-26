<?php

/*
  Easy Pie Coming Soon Plugin
  Copyright (C) 2014, Synthetic Thought LLC
  website: easypiewp.com contact: bob@easypiewp.com

  Easy Pie Coming Soon Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

require_once(dirname(__FILE__) . '/Utilities/class-ezp-cs-utility.php');

require_once(EZP_CS_Utility::$PLUGIN_DIRECTORY . '/../../../wp-admin/includes/upgrade.php');

//require_once("class-easy-pie-options.php");
require_once("Entities/class-ezp-cs-global-entity.php");

require_once('class-ezp-cs-plugin-base.php');
require_once('class-ezp-cs-constants.php');

require_once(dirname(__FILE__) . '/Utilities/class-ezp-cs-render-utility.php');
require_once(dirname(__FILE__) . '/Utilities/class-ezp-cs-test-utility.php');

if (!class_exists('EZP_CS')) {

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS extends EZP_CS_Plugin_Base {

        /**
         * Constructor
         */
        function __construct($plugin_file_path) {

            parent::__construct(EZP_CS_Constants::PLUGIN_SLUG);

            $this->add_class_action('plugins_loaded', 'plugins_loaded_handler');

            $entity_table_present = EZP_CS_Query_Utility::is_table_present(EZP_CS_JSON_Entity_Base::DEFAULT_TABLE_NAME);

            if ($entity_table_present) {

                $global = EZP_CS_Global_Entity::get_instance();

                $config = EZP_CS_Config_Entity::get_by_id($global->config_index);

                $coming_soon_mode_on = $config->coming_soon_mode_on;

                $in_preview = isset($_REQUEST['ezp_cs_preview']) && ($_REQUEST['ezp_cs_preview'] == 'true');
            } else {

                // On activation so we don't have the tables yet
                $coming_soon_mode_on = false;
                $in_preview = false;
            }

            // RSR TODO - is_admin() just says if admin panel is attempting to be displayed - NOT to see if someone is an admin
            if (is_admin() && !$in_preview) {

                //EZP_CS_Utility::debug("admin true");

                if ($coming_soon_mode_on) {

                    $this->add_class_action("admin_notices", "display_admin_notice");
                }

                //- Hook Handlers
                register_activation_hook($plugin_file_path, array('EZP_CS', 'activate'));
                register_deactivation_hook($plugin_file_path, array('EZP_CS', 'deactivate'));
                register_uninstall_hook($plugin_file_path, array('EZP_CS', 'uninstall'));

                //- Actions
                $this->add_class_action('admin_init', 'admin_init_handler');
                $this->add_class_action('admin_menu', 'add_to_admin_menu');

                $this->add_class_action('wp_ajax_EZP_CS_export_all_subscribers', 'ws_export_all_subscribers');

                $this->add_class_action('wp_ajax_EZP_CS_purge_contact', 'ws_purge_contact');

                $this->add_class_action('wp_ajax_EZP_CS_test', 'ws_test');

                $this->add_class_action('wp_ajax_EZP_CS_copy_template', 'ws_copy_template');
            } else {

                //EZP_CS_Utility::debug("admin false");
                if ($coming_soon_mode_on || $in_preview) {
                    EZP_CS_Utility::debug("displaying coming soon page");
                    $this->add_class_action('template_redirect', 'display_coming_soon_page');
                }
            }
        }

        function ws_export_all_subscribers() {

            if (isset($_REQUEST['_wpnonce'])) {

                $_wpnonce = $_REQUEST['_wpnonce'];

                if (wp_verify_nonce($_wpnonce, 'easy-pie-cs-change-subscribers')) {

                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private", false);
                    header("Content-Type: application/octet-stream");
                    header("Content-Disposition: attachment; filename=\"subscribers.csv\";");
                    header("Content-Transfer-Encoding: binary");

                    $subscribers = EZP_CS_Query_Utility::get_subscriber_list(-1);

                    echo "Name, Email Address, Date\r\n";
                    foreach ($subscribers as $subscriber) {

                        if ($subscriber->subscription_date != '') {
                            //   $localized_date = date_i18n(get_option('date_format'), strtotime($subscriber->subscription_date));
                            $date_text = date('n/j/Y', strtotime($subscriber->subscription_date));
                        } else {
                            //   $localized_date = '';
                            $date_text = '';
                        }

                        echo "$subscriber->friendly_name, $subscriber->email_address, $date_text\r\n";
                    }

                    exit;
                } else {

                    EZP_CS_Utility::debug("ws_export_all_subscribers: Security violation. Nonce doesn't properly match!");
                }
            } else {

                EZP_CS_Utility::debug("ws_export_all_subscribers: Security violation. Nonce doesn't exist!");
            }
        }

        function ws_purge_contact() {
            $request = stripslashes_deep($_REQUEST);

            if (isset($request['_wpnonce'])) {

                $_wpnonce = $request['_wpnonce'];

                if (wp_verify_nonce($_wpnonce, 'easy-pie-cs-change-subscribers')) {

                    if (isset($request['contact_id'])) {

                        $contact_id = $request['contact_id'];

                        EZP_Contact_Entity::delete_by_id($contact_id);
                    } else {
                        EZP_CS_Utility::debug("ws_purge_contact: contact id not set");
                    }
                } else {

                    EZP_CS_Utility::debug("ws_purge_contact: Security violation. Nonce doesn't properly match!");
                }
            } else {

                EZP_CS_Utility::debug("ws_purge_contact: Security violation. Nonce doesn't exist!");
            }
        }

        function ws_test() {

            $post = stripslashes_deep($_POST);

            if (isset($post['type'])) {

                $type = $post['type'];

                switch ($type) {
                    case 'add_subscribers':
                        EZP_CS_Test_Utility::add_test_subscribers($post);
                        break;

                    default:
                        EZP_CS_Utility::debug('ws_test: Unknown test type');
                }
            }
        }

        // RSR TODO: Implement for separate contact and subscriber management
//        function ws_delete_subscriber() {
//            EZP_CS_Utility::debug("delete subscriber");
//
//            $post = stripslashes_deep($_POST);
//
//            EZP_CS_Utility::debug_object($post);
//            if (isset($post['subscriber_id'])) {
//
//
//                $subscriber_id = $post['subscriber_id'];
//
//                EZP_CS_Utility::debug("subscriber id set to $subscriber_id");
//
//                $subscriber = EZP_Coming_Soon_Subscriber::get_by_id($subscriber_id);
//
//                if ($subscriber != null) {
//
//                    $subscriber->delete();
//                }
//            } else {
//                EZP_CS_Utility::debug("subscriber id not set");
//            }
//        }

        function ws_copy_template() {

            $post = stripslashes_deep($_POST);

            if (isset($post['template_key'])) {

                $template_key = $post['template_key'];

                $global = EZP_CS_Global_Entity::get_instance();

                $set_index = $global->active_set_index;

                $set = EZP_CS_Set_Entity::get_by_id($set_index);

                $display = EZP_CS_Display_Entity::get_by_id($set->display_index);

                // RSR TODO: Have to be careful here - ensure that a single error won't hose up the state of the system - TODO: Maybe a reset to defaults option to clean out db?
                $display->delete();

                $new_display = EZP_CS_Display_Entity::create_from_template($template_key);

                $new_display->save();

                $set->display_index = $new_display->id;

                $set->save();
            }
        }

        function add_class_action($tag, $method_name) {

            return add_action($tag, array($this, $method_name));
        }

        function add_class_filter($tag, $method_name) {

            return add_filter($tag, array($this, $method_name));
        }

        public function display_admin_notice() {

            $display_notice = true;

            if (isset($_REQUEST['page']) && (strpos($_REQUEST['page'], EZP_CS_Constants::PLUGIN_SLUG) === 0)) {

                $display_notice = false;
            }

            if ($display_notice) {

                //echo "<div class='error'><a href='" . admin_url() . "admin.php?page=" . EZP_CS_Constants::$SETTINGS_SUBMENU_SLUG . "'>" . $this->__("Coming Soon is On") . "</a></div>";                                
                EZP_CS_Utility::display_admin_notice(true);
            }
        }

        /**
         * Display the maintenance page
         */
        public function display_coming_soon_page() {
            $global = EZP_CS_Global_Entity::get_instance();

            $set_index = $global->active_set_index;

            $set = EZP_CS_Set_Entity::get_by_id($set_index);

            $config = EZP_CS_Config_Entity::get_by_id($global->config_index);

            $in_preview = isset($_REQUEST['ezp_cs_preview']) && ($_REQUEST['ezp_cs_preview'] == 'true');

            if(trim($config->unfiltered_urls) != "") {
                
                $is_unfiltered = EZP_CS_Utility::is_current_url_unfiltered($config);
            } else {
                
                $is_unfiltered = false;
            }

            if (!$is_unfiltered && (!is_user_logged_in() || $in_preview)) {

                if ($config->return_code == 503) {

                    header('HTTP/1.1 503 Service Temporarily Unavailable');
                    header('Status: 503 Service Temporarily Unavailable');
                    header('Retry-After: 86400'); // RSR TODO: Put in the retry time later
                } else {

                    header('HTTP/1.1 200 OK');
                }

                $__dir__ = dirname(__FILE__);

                $page = $__dir__ . "/../mini-themes/base-responsive/index.php";

                $page_url = content_url('plugins/easy-pie-coming-soon/mini-themes/base-responsive');

                require($page);


                exit();
            }
        }

        // <editor-fold defaultstate="collapsed" desc="Hook Handlers">
        public static function activate() {

            EZP_CS_Utility::debug("activate");

            $installed_ver = get_option(EZP_CS_Constants::PLUGIN_VERSION_OPTION_KEY);

            //rsr todo       if($installed_ver != EZP_CS_Constants::PLUGIN_VERSION)
            {
                EZP_CS_JSON_Entity_Base::init_table();

                EZP_Contact_Entity::init_table();
                EZP_Email_Entity::init_table();
                EZP_CS_Subscriber_Entity::init_table();

                EZP_CS_Global_Entity::initialize_plugin_data();

                update_option(EZP_CS_Constants::PLUGIN_VERSION_OPTION_KEY, EZP_CS_Constants::PLUGIN_VERSION);
            }
        }

        public static function deactivate() {

            EZP_CS_Utility::debug("deactivate");
        }

        public static function uninstall() {

            EZP_CS_Utility::debug("uninstall");
        }

        // </editor-fold>

        public function enqueue_scripts() {

            $jsRoot = plugins_url() . "/" . EZP_CS_Constants::PLUGIN_SLUG . "/js";

            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');

            $jQueryPluginRoot = plugins_url() . "/" . EZP_CS_Constants::PLUGIN_SLUG . "/jquery-plugins";

            if (isset($_GET['page'])) {


                if ($_GET['page'] == EZP_CS_Constants::$TEMPLATE_SUBMENU_SLUG) {

                    if (!isset($_GET['tab']) || ($_GET['tab'] == 'display')) {


                        wp_enqueue_script('jquery-ui-slider');
                        wp_enqueue_script('spectrum.min.js', $jQueryPluginRoot . '/spectrum-picker/spectrum.min.js', array('jquery'), EZP_CS_Constants::PLUGIN_VERSION);
                    } else {
                        // Implies it is the content tab
                        wp_enqueue_script('jquery-ui-datepicker');
                    }

                    wp_enqueue_media();
                } else if ($_GET['page'] == EZP_CS_Constants::$SUBSCRIBERS_SUBMENU_SLUG) {


                    wp_enqueue_script('jquery-ui-dialog');
                }
            }
        }

        /**
         *  enqueue_styles
         *  Loads the required css links only for this plugin  */
        public function enqueue_styles() {
            $styleRoot = plugins_url() . "/" . EZP_CS_Constants::PLUGIN_SLUG . "/styles";

            wp_register_style('jquery-ui-min-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css', array(), EZP_CS_Constants::PLUGIN_VERSION);
            wp_enqueue_style('jquery-ui-min-css');



            wp_register_style('easy-pie-cs-styles.css', $styleRoot . '/easy-pie-cs-styles.css', array(), EZP_CS_Constants::PLUGIN_VERSION);
            wp_enqueue_style('easy-pie-cs-styles.css');

            $jQueryPluginRoot = plugins_url() . "/" . EZP_CS_Constants::PLUGIN_SLUG . "/jquery-plugins";
            //       wp_enqueue_style('jquery.eyecon.colorpicker.colorpicker', $jQueryPluginRoot . '/colorpicker/css/colorpicker.css', array(), EZP_CS_Constants::PLUGIN_VERSION);

            if (isset($_GET['page']) && ($_GET['page'] == EZP_CS_Constants::$TEMPLATE_SUBMENU_SLUG)) {
                if (!isset($_GET['tab']) || ($_GET['tab'] == 'display')) {
                    wp_enqueue_style('spectrum.css', $jQueryPluginRoot . '/spectrum-picker/spectrum.css', array(), EZP_CS_Constants::PLUGIN_VERSION);
                }
            }
        }

        // <editor-fold defaultstate="collapsed" desc=" Action Handlers ">
        public function plugins_loaded_handler() {

            $this->init_localization();
            $this->upgrade_processing();
        }

        public function init_localization() {

            load_plugin_textdomain(EZP_CS_Constants::PLUGIN_SLUG, false, EZP_CS_Constants::PLUGIN_SLUG . '/languages/');
        }

        public function admin_init_handler() {

            //   register_setting(EZP_CS_Constants::MAIN_PAGE_KEY, EZP_CS_Constants::COMPOUND_OPTION_NAME, array($this, 'validate_options'));
            // $this->add_settings_sections();
            $this->add_filters_and_actions();
        }

        private function add_filters_and_actions() {

            add_filter('plugin_action_links', array($this, 'get_action_links'), 10, 2);

            // adding new filter here? http://sumtips.com/2012/12/add-remove-tab-wordpress-3-5-media-upload-page.html
            // selctive display of tabs depending on where you are http://wordpress.org/support/topic/hide-media-upload-library-tabs-leave-url-tab?replies=17
            //          $this->add_class_filter( 'media_upload_tabs',  'add_custom_wallpaper_tab');
//            $this->add_class_action('media_upload_epcs', 'add_custom_wallpaper_tab_content');
        }

        function get_action_links($links, $file) {

            if ($file == "easy-pie-coming-soon/easy-pie-coming-soon.php") {

                $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=' . EZP_CS_Constants::PLUGIN_SLUG . '">Settings</a>';

                array_unshift($links, $settings_link);
            }

            return $links;
        }

        function upgrade_processing() {
            // RSR TODO: In future versions compare where we are at with what's in the system and take action            
        }

        // </editor-fold>
        
        public function add_to_admin_menu() {

            $perms = 'manage_options';


            add_menu_page('Easy Pie Coming Soon', 'Coming Soon', $perms, EZP_CS_Constants::PLUGIN_SLUG, array($this, 'display_template_options_page'), EZP_CS_Utility::$PLUGIN_URL . '/images/easy-pie-cs-menu-icon.png');
            $template_page_hook_suffix = add_submenu_page(EZP_CS_Constants::PLUGIN_SLUG, $this->__('Easy Pie Coming Soon Template'), $this->__('Template'), $perms, EZP_CS_Constants::$TEMPLATE_SUBMENU_SLUG, array($this, 'display_template_options_page'));
            $settings_page_hook_suffix = add_submenu_page(EZP_CS_Constants::PLUGIN_SLUG, $this->__('Easy Pie Coming Soon Settings'), $this->__('Settings'), $perms, EZP_CS_Constants::$SETTINGS_SUBMENU_SLUG, array($this, 'display_settings_options_page'));
            $subscribers_page_hook_suffix = add_submenu_page(EZP_CS_Constants::PLUGIN_SLUG, $this->__('Easy Pie Coming Soon Subscribers'), $this->__('Subscribers'), $perms, EZP_CS_Constants::$SUBSCRIBERS_SUBMENU_SLUG, array($this, 'display_subscribers_options_page'));
            $coming_soon_pro_page_suffix = add_submenu_page(EZP_CS_Constants::PLUGIN_SLUG, $this->__('Coming Soon Pro'), $this->__('Coming Soon Pro'), $perms, EZP_CS_Constants::$COMING_SOON_PRO_SUBMENU_SLUG, array($this, 'display_coming_soon_pro_page'));
        //    $preview_page_hook_suffix = add_submenu_page(EZP_CS_Constants::PLUGIN_SLUG, $this->__('Easy Pie Coming Soon Preview'), $this->__('Preview'), $perms, EZP_CS_Constants::$PREVIEW_SUBMENU_SLUG, array($this, 'display_preview_page'));

            add_action('admin_print_scripts-' . $template_page_hook_suffix, array($this, 'enqueue_scripts'));
            add_action('admin_print_scripts-' . $settings_page_hook_suffix, array($this, 'enqueue_scripts'));
            add_action('admin_print_scripts-' . $subscribers_page_hook_suffix, array($this, 'enqueue_scripts'));

            //Apply Styles
            add_action('admin_print_styles-' . $template_page_hook_suffix, array($this, 'enqueue_styles'));
            add_action('admin_print_styles-' . $settings_page_hook_suffix, array($this, 'enqueue_styles'));
            add_action('admin_print_styles-' . $subscribers_page_hook_suffix, array($this, 'enqueue_styles'));
        }

        // </editor-fold>

        function display_options_page($page) {

            $relative_page_path = '/../pages/' . $page;

            $__dir__ = dirname(__FILE__);

            include($__dir__ . $relative_page_path);
        }

        function display_template_options_page() {
            $this->display_options_page('page-options.php');
        }

        function display_settings_options_page() {
            $this->display_options_page('page-options-settings.php');
        }

        function display_subscribers_options_page() {
            $this->display_options_page('page-subscribers.php');
        }
        
        function display_coming_soon_pro_page() {
            $this->display_options_page('page-coming-soon-pro.php');
        }

        function display_preview_page() {
            $this->display_options_page('page-preview.php');
        }

    }

}