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

require_once(dirname(__FILE__) . '/../class-ezp-cs-constants.php');

if (!class_exists('EZP_CS_Utility')) {

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Utility {

        // Pseudo-constants
        public static $MINI_THEMES_TEMPLATE_DIRECTORY;
        public static $PLUGIN_URL;
        public static $PLUGIN_DIRECTORY;
        private static $type_format_array;

        public static function init() {

            $__dir__ = dirname(__FILE__);

            self::$MINI_THEMES_TEMPLATE_DIRECTORY = $__dir__ . "/../templates/";

            self::$PLUGIN_URL = plugins_url() . "/" . EZP_CS_Constants::PLUGIN_SLUG;

            self::$PLUGIN_DIRECTORY = (WP_CONTENT_DIR . "/plugins/" . EZP_CS_Constants::PLUGIN_SLUG);

            self::$type_format_array = array('boolean' => '%s', 'integer' => '%d', 'double' => '%g', 'string' => '%s');
        }

        public static function _e($text) {

            _e($text, EZP_CS_Constants::PLUGIN_SLUG);
        }

        public static function __($text) {

            return __($text, EZP_CS_Constants::PLUGIN_SLUG);
        }

        public static function _he($text) {

            echo htmlspecialchars($text);
        }

        public function get_db_type_format($variable) {

            $type_string = gettype($variable);

            if ($type_string == "NULL") {

                self::debug("get_db_type_format: Error. Variable is not initialized.");
                return "";
            }

            return self::$type_format_array[$type_string];
        }

        public static function get_public_properties($object) {

            $publics = get_object_vars($object);
            unset($publics['id']);
            unset($publics['type']);

            return $publics;
        }

        public static function get_public_class_properties($class_name) {

            $publics = get_class_vars($class_name);
            unset($publics['id']);

            return $publics;
        }

        public static function get_guid() {

            if (function_exists('com_create_guid') === true) {
                return trim(com_create_guid(), '{}');
            }

            return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
        }

        public static function display_admin_notice($coming_soon_on) {
            if ($coming_soon_on) {

                echo "<div class='error'><a href='" . admin_url() . "admin.php?page=" . EZP_CS_Constants::$SETTINGS_SUBMENU_SLUG . "'>" . self::__("Coming Soon is On") . "</a></div>";
            } else {

                echo "<div style='text-decoration:underline' class='updated'><a href='" . admin_url() . "admin.php?page=" . EZP_CS_Constants::$SETTINGS_SUBMENU_SLUG . "'>" . self::__("Coming Soon is Off") . "</a></div>";
            }
        }

        /* -- Option Field Help Methods -- */

        public static function render_option($value, $text, $current_value) {
            $selected = "";

            if ($value == $current_value) {
                $selected = 'selected="selected"';
            }

            echo "<option value='$value' $selected>$text</option>";
        }

        public static function get_manifest_by_key($key) {

            $manifests = self::get_manifests();

            foreach ($manifests as $manifest) {

                if ($manifest->key == $key) {

                    return $manifest;
                }
            }

            return null;
        }

        public static function get_manifests() {

            $user_manifest_array = self::get_manifests_in_directory(self::$MINI_THEMES_USER_DIRECTORY, self::$MINI_THEMES_USER_URL);
            $standard_manifest_array = self::get_manifests_in_directory(self::$MINI_THEMES_STANDARD_DIRECTORY, self::$MINI_THEMES_STANDARD_URL);

            $combined_manifest_array = &$user_manifest_array;

            // stuff in user manifest array can override standard manifests
            foreach ($standard_manifest_array as $sman) {

                $contains = false;

                foreach ($combined_manifest_array as $man) {

                    if ($sman->key == $man->key) {
                        $contains = true;
                        break;
                    }
                }

                if (!$contains) {
                    array_push($combined_manifest_array, $sman);
                }
            }
            return $combined_manifest_array;
        }

        public static function get_manifests_in_directory($directory, $mini_theme_base_url) {

            $manifest_array = array();
            $dirs = glob($directory . "*", GLOB_ONLYDIR);

            sort($dirs);

            foreach ($dirs as $dir) {

                $manifest = null;
                $manifest_path = $dir . "/manifest.json";

                if (file_exists($manifest_path)) {

                    $manifest_text = file_get_contents($manifest_path);

                    if ($manifest_text != false) {

                        $manifest = json_decode($manifest_text);
                    } else {

                        self::debug("Problem reading manifest in $dir ($dirs)");
                    }
                } else {

                    // Manifest not present so assumption is they just want a generic mini-theme
                    $manifest = new stdClass();

                    self::add_property($manifest, 'title', basename($dir));
                    self::add_property($manifest, 'page', 'index.html');
                    self::add_property($manifest, 'description', 'User Mini Theme');
                    self::add_property($manifest, 'author_name', '');
                    self::add_property($manifest, 'website_url', '');
                    self::add_property($manifest, 'google_plus_author_url', '');
                    self::add_property($manifest, 'original_release_date', '2013/01/01');
                    self::add_property($manifest, 'latest_version_date', '2013/01/01');
                    self::add_property($manifest, 'version', '1.0.0');
                    self::add_property($manifest, 'release_notes', '');
                    self::add_property($manifest, 'screenshot', self::$MINI_THEMES_IMAGES_URL . "user-defined.png");
                    self::add_property($manifest, 'autodownload', false);
                    self::add_property($manifest, 'responsive', true);
                }

                if ($manifest != null) {

                    // RSR TODO: Have a way to give each item a unique key if it conflicts..?
                    self::add_property($manifest, 'key', basename($dir));
                    self::add_property($manifest, 'dir', $dir);
                    self::add_property($manifest, 'manifest_path', $manifest_path);
                    self::add_property($manifest, 'mini_theme_url', $mini_theme_base_url . $manifest->key);

                    array_push($manifest_array, $manifest);
                }
            }

            return $manifest_array;
        }

        public static function add_property(&$obj, $property, $value) {

            $obj = (array) $obj;
            $obj[$property] = $value;
            $obj = (object) $obj;
        }

        public static function debug($message) {

            if (WP_DEBUG === true) {
                if (is_array($message) || is_object($message)) {
                    error_log(EZP_CS_Constants::PLUGIN_SLUG . ":" . print_r($message, true));
                } else {
                    error_log(EZP_CS_Constants::PLUGIN_SLUG . ":" . $message);
                }
            }
        }

        public static function debug_object($object) {

            EZP_CS_Utility::debug(var_export($object, true));
        }

        public static function debug_dump($message, $object) {

            EZP_CS_Utility::debug($message . ":" . var_export($object, true));
        }

        public static function is_current_url_unfiltered($config) {
            
            $requested = strtolower($_SERVER['REQUEST_URI']);

            $config->allowed_urls = strtolower($config->unfiltered_urls);
            $urls = preg_split('/\r\n|[\r\n]/', $config->unfiltered_urls);

            $is_unfiltered = false;
            foreach ($urls as $url) {
                
                $trimmed_url = trim($url);
                if ((strpos($requested, $trimmed_url) === 0)) {

                    $is_unfiltered = true;
                    break;
                }
            }

            return $is_unfiltered;
        }
        
        public static function get_coupon_text()
        {
           $text = '';
            
           if(time() < strtotime('8 July 2014')) 
           {
               $r = rand(0, 1);
               
               switch($r) 
               {
                   case 0:
                       $text = '$10 off coupon for upcoming Coming Soon Page Pro';
                       break;
                   
                   case 1:
                       $text = 'Get a $10 off coupon for the upcoming Coming Soon Page Pro';
                       break;
               }
           }

           if($text != '') {
                $text = "<a target='_blank' style='margin-top:17px; display:block; text-align:center' href='http://easypiewp.com/get-coming-soon-page-pro-coupon/'>$text</p>";
           }
           
           return $text;
        }
    }

    EZP_CS_Utility::init();
}
?>