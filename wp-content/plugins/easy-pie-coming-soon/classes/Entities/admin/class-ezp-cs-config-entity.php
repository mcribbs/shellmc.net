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

if (!class_exists('EZP_CS_Config_Entity')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Config_Entity extends EZP_CS_JSON_Entity_Base {

        const TYPE = "EZP_CS_Config_Entity";

        public $coming_soon_mode_on;
        
        public $collect_email;
        public $collect_name;
        public $return_code;
        public $author_url;
        public $meta_description;
        public $meta_keywords;
        
        public $analytics_code;
        
        public $facebook_url;
        public $twitter_url;
        public $google_plus_url;
        
        public $unfiltered_urls;
               
        function __construct() {

            $this->coming_soon_mode_on = false;
            
            $this->collect_email = true;
            $this->collect_name = true;

            $this->return_code = 503;

            $this->author_url = "";
            $this->meta_description = "";
            $this->meta_keywords = "";

            $this->analytics_code = "";
            
            $this->facebook_url = "";
            $this->twitter_url = "";
            $this->google_plus_url = "";
            
            $this->allowed_urls = "";

            parent::__construct();
        }

        /**
         * 
         * @return EZP_CS_Config_Entity
         */
        public function create_with_defaults() {

            $instance = new EZP_CS_Config_Entity();

            return $instance;
        }

        /**
         * 
         * @param type $id
         * @return EZP_CS_Config_Entity
         */
        public static function get_by_id($id) {
            return EZP_CS_JSON_Entity_Base::get_by_id_and_type($id, self::TYPE);
        }
        
        public function fix_url_fields() {
                       
            $this->fix_url_field($this->facebook_url);
            $this->fix_url_field($this->google_plus_url);
            $this->fix_url_field($this->twitter_url);
        }
        
        private function fix_url_field(&$field) {

             if(!empty($field)) {
            
                if(strpos($field, 'http') === false) {
                    
                    $field = 'https://' . $field;
                }                    
            }
        }
    }
}
?>