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

require_once(dirname(__FILE__) .  '/../class-ezp-cs-json-entity-base.php');

if (!class_exists('EZP_CS_Display_Entity')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Display_Entity extends EZP_CS_JSON_Entity_Base {
    
        const TYPE = "EZP_CS_Display_Entity";

        public $background_image_url = "";
        public $builtin_background_image;   // Delayed initialization
        public $background_color = "#00FF00";
        public $background_tiling_enabled = false;
                              
        public $logo_width = "15%";
        public $logo_height = "";
        
        public $content_box_opacity = 0.4;
        public $content_box_color = "#000000";
              
        public $text_headline_font_name = "arial";
        public $text_headline_font_size = "42px";
        public $text_headline_font_color = "#FFFFFF";
      
        public $text_description_font_name = "arial";
        public $text_description_font_size = "20px";
        public $text_description_font_color = "#FFFFFF";
      
        public $text_disclaimer_font_name = "times-new-roman";
        public $text_disclaimer_font_size = "14px";
        public $text_disclaimer_font_color = "#FFFFFF";
                
        public $text_footer_font_name = "times-new-roman";
        public $text_footer_font_size = "13px";
        public $text_footer_font_color = "#FFFFFF";
               
        public $email_button_width = "120px";
        public $email_button_height = "42px";
        
        public $email_button_font_name = "arial";        
        public $email_button_font_size = "16px";
        public $email_button_font_color = "#FFFFFF";
        
        public $email_button_color = "#E34141";        

        public $css = "/* This code adds a shadow around the content box */\r\n#headline { font-weight: bold }\r\n#content-area { box-shadow: 1px 7px 36px -5px rgba(34,34,34,1);}";
     
        function __construct() {
            
            parent::__construct();
            
            $this->background_image_url = EZP_CS_Utility::$PLUGIN_URL . '/images/backgrounds/cloud-1.jpg';
            $font_size_regex = "/(px|em|ex|%|in|cm|mm|pt|pc)+$/";
            $div_size_regex = "/(px|%)+$/";
            $this->verifiers['text_headline_font_size'] = new EZP_CS_Regex_Verifier($font_size_regex, EZP_CS_Utility::__("Headline font must end in a unit (px, em, etc...)"));
            $this->verifiers['text_description_font_size'] = new EZP_CS_Regex_Verifier($font_size_regex, EZP_CS_Utility::__("Description font must end in a unit (px, em, etc...)"));
            $this->verifiers['text_disclaimer_font_size'] = new EZP_CS_Regex_Verifier($font_size_regex, EZP_CS_Utility::__("Disclaimer font must end in a unit (px, em, etc...)"));
            $this->verifiers['text_footer_font_size'] = new EZP_CS_Regex_Verifier($font_size_regex, EZP_CS_Utility::__("Footer font must end in a unit (px, em, etc...)"));
            $this->verifiers['email_button_font_size'] = new EZP_CS_Regex_Verifier($font_size_regex, EZP_CS_Utility::__("Email button font must end in a unit (px, em, etc...)"));
            
//            $this->verifiers['logo_width'] = new EZP_CS_Regex_Verifier($font_size_regex, EZP_CS_Utility::__("Logo width must end in px or %"));
//            $this->verifiers['logo_height'] = new EZP_CS_Regex_Verifier($font_size_regex, EZP_CS_Utility::__("Logo height font must end in px or %"));
            $this->verifiers['email_button_width'] = new EZP_CS_Regex_Verifier($font_size_regex, EZP_CS_Utility::__("Email button height font must end in px or %"));
            $this->verifiers['email_button_height'] = new EZP_CS_Regex_Verifier($font_size_regex, EZP_CS_Utility::__("Email button width must end in px or %"));
            
            $this->verifiers['content_box_opacity'] = new EZP_CS_Range_Verifier(0, 1, EZP_CS_Utility::__("Content box opacity must be between 0 and 1"));                                   
        }        
        
        public function create_with_defaults() {
            
            $instance = new EZP_CS_Display_Entity();                  
        
            return $instance;
        }
        
        /**
         * 
         * @param type $id
         * @return EZP_CS_Display_Entity
         */
        public static function get_by_id($id)
        {
            return EZP_CS_JSON_Entity_Base::get_by_id_and_type($id, self::TYPE);            
        }  
        
        public static function get_all_template_metadata()
        {
            $metadata_array = array();
            $dirs = glob(EZP_CS_Utility::$MINI_THEMES_TEMPLATE_DIRECTORY . "*", GLOB_ONLYDIR);

            sort($dirs);

            foreach ($dirs as $dir) {

                $metadata = null;
                $metadata_path = $dir . "/metadata.json";

                if (file_exists($metadata_path)) {

                    $metadata_text = file_get_contents($metadata_path);

                    if ($metadata_text != false) {

                        $metadata = json_decode($metadata_text);
                        
                    } else {
                        self::debug("No metadata in $metadata_path");
                    }
                } 
                else {
                    self::debug("No metadata file in $dir");
                }

                if ($metadata != null) {

                    $template_key = basename($dir);
                    
                    // RSR TODO: Have a way to give each item a unique key if it conflicts..?
                    EZP_CS_Utility::add_property($metadata, 'template_key', $template_key);
                    EZP_CS_Utility::add_property($metadata, 'screenshot_url', EZP_CS_Utility::$PLUGIN_URL . "/templates/$template_key/screenshot.png");

                    array_push($metadata_array, $metadata);
                }
            }
            
            return $metadata_array;
        }
        
        /**
         * 
         * @param type $template_key
         * @return EZP_CS_Display_Entity
         * Inspired from good solution found at http://stackoverflow.com/questions/5397758/json-decode-to-custom-class
         */
        public static function create_from_mixed($mixed)
        {
            // RSR TODO: Move this into a base class
            $instance = new EZP_CS_Display_Entity();
            
            foreach ($mixed AS $key => $value) {
                $instance->{$key} = $value;
            }
    
            return $instance;
        }
        
        /**
         * 
         * @param type $template_key
         * @return EZP_CS_Display_Entity
         */
        public static function create_from_template ($template_key)
        {
            EZP_CS_Utility::debug("copying from template $template_key");
            
            $template_directory = EZP_CS_Utility::$MINI_THEMES_TEMPLATE_DIRECTORY . $template_key;            
       
            $metadata_path = $template_directory . "/display.json";
            
            $display_json = file_get_contents($metadata_path);
            
            $mixed = json_decode($display_json);                        
            
            EZP_CS_Utility::debug_dump("mixed from template", $mixed);
            $new = self::create_from_mixed($mixed);
            
            EZP_CS_Utility::debug_dump("after copy from template", $new);
            
            return $new;
        }          
                
        public static function display_font_field_row($label, $variable_base_name, $model) {
                           
            $font_name_variable_name = $variable_base_name . '_font_name';
            $font_size_variable_name = $variable_base_name . '_font_size';
            $font_color_variable_name = $variable_base_name . '_font_color';                                   
                        
            $font_name_value = $model->$font_name_variable_name;
            $font_size_value = $model->$font_size_variable_name;
            $font_color_value = $model->$font_color_variable_name;
            
            ?>
                <th scope="row">
                    <?php EZP_CS_Utility::_e($label); ?>
                </th>
                <td>
                    <div class="compound-setting">
                        <select style="height:32px" name="<?php echo $font_name_variable_name; ?>" >
                            <?php
                            
                                EZP_CS_Display_Entity::render_font_options($font_name_value);
                            ?>
                        </select>

                        <input name="<?php echo $font_size_variable_name;?>" class="text-styling-size" placeholder="<?php EZP_CS_Utility::_e("Size");?>" value="<?php echo $font_size_value;?>" size="6" style="line-height:24px;padding-top:1px;margin:0"/> 
                        <input name="<?php echo $font_color_variable_name;?>" class="spectrum-picker" type="text" value="<?php echo $font_color_value; ?>"/>
                    </div>
                </td>
                
            <?php                  
        }
        
        private static function render_font_options($current_font_name_value)
        {
            EZP_CS_Utility::render_option("arial", EZP_CS_Utility::__("Arial"), $current_font_name_value);                        
            EZP_CS_Utility::render_option("courier-new", EZP_CS_Utility::__("Courier New"), $current_font_name_value);
            EZP_CS_Utility::render_option("comic-sans-ms", EZP_CS_Utility::__("Comic Sans MS"), $current_font_name_value);
            EZP_CS_Utility::render_option("georgia", EZP_CS_Utility::__("Georgia"), $current_font_name_value);
            EZP_CS_Utility::render_option("impact", EZP_CS_Utility::__("Impact"), $current_font_name_value);
            EZP_CS_Utility::render_option("times-new-roman", EZP_CS_Utility::__("Times New Roman"), $current_font_name_value);
            EZP_CS_Utility::render_option("verdana", EZP_CS_Utility::__("Verdana"), $current_font_name_value);                    
        }
                        
        public static function get_font_family_by_key($font_key)
        {
            switch($font_key) {
                case "arial":
                    return "Arial, Helvetica, san-serif";
                    break;
                    
                case "courier-new":
                    return "\"Courier New\", Courier, monospace";
                    break;
                    
                case "comic-sans-ms":
                    return "\"Comic Sans MS\", cursive, sans-serif";
                    break;
                
                case "georgia":
                    return "Georgia, serif";
                    break;
                                        
                case "impact":
                    return "Impact, Charcoal, sans-serif";
                    break;
                    
                case "times-new-roman":
                    return "\"Times New Roman\", Times, serif";
                    break;
                
                case "verdana":
                    return "Verdana, Geneva, sans-serif";
                    break;
                
                default:
                    return "";
            }
        }
        
        public function get_font_styling($variable_base_name)
        {
            $font_name_variable_name = $variable_base_name . '_font_name';
            $font_size_variable_name = $variable_base_name . '_font_size';
            $font_color_variable_name = $variable_base_name . '_font_color';                                   
                        
            $font_name_key_value = $this->$font_name_variable_name;
            $font_size_value = $this->$font_size_variable_name;
            $font_color_value = $this->$font_color_variable_name;            
        
            $font_family_value = EZP_CS_Display_Entity::get_font_family_by_key($font_name_key_value);
            
            return "color: $font_color_value; font-size: $font_size_value; font-family: $font_family_value";                
        }        
    }
}
?>