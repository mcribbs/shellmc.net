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
 
require_once(dirname(__FILE__) . '/../Utilities/class-ezp-cs-query-utility.php');

require_once('class-ezp-cs-standard-entity-base.php');
require_once(dirname(__FILE__) . '/crm/class-ezp-cs-subscriber-entity.php');
require_once(dirname(__FILE__) . '/crm/class-ezp-contact-entity.php');
require_once(dirname(__FILE__) . '/crm/class-ezp-email-entity.php');

require_once('class-ezp-cs-json-entity-base.php');
require_once(dirname(__FILE__) . '/admin/class-ezp-cs-display-entity.php');
require_once(dirname(__FILE__) . '/admin/class-ezp-cs-content-entity.php');
require_once(dirname(__FILE__) . '/admin/class-ezp-cs-config-entity.php');
require_once(dirname(__FILE__) . '/admin/class-ezp-cs-set-entity.php');

if (!class_exists('EZP_CS_Global_Entity')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Global_Entity extends EZP_CS_JSON_Entity_Base {

        const TYPE = "EZP_CS_Global_Entity";

        public $active_set_index;
        public $config_index;
        public $plugin_version;

        function __construct() {

            parent::__construct();

            $plugin_version = EZP_CS_Constants::PLUGIN_VERSION;
        }

        public static function initialize_plugin_data() {

            $globals = EZP_CS_JSON_Entity_Base::get_by_type(self::TYPE);
            
            if($globals == null) {
                
                // RSR TODO: error checking here to ensure data doesnt get out of sync
                $display = EZP_CS_Display_Entity::create_with_defaults();

                $display->save();

                $content = EZP_CS_Content_Entity::create_with_defaults();

                $content->save();

                $set = EZP_CS_Set_Entity::create($display->id, $content->id);

                $set->save();

                $config = EZP_CS_Config_Entity::create_with_defaults();

                $config->save();

                $global = new EZP_CS_Global_Entity();

                $global->set("active_set_index", $set->id);

                $global->set("config_index", $config->id);

                $global->save();
            }
        }

        public static function get_instance() {
            
            $global = null;
            $globals = EZP_CS_JSON_Entity_Base::get_by_type(self::TYPE);

            if($globals != null) {
                
                $global = $globals[0];
            } 
           
            return $global;
        }
    }
}
?>