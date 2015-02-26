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

require_once(dirname(__FILE__) .  '/../class-ezp-cs-standard-entity-base.php');

if (!class_exists('EZP_Contact_Entity')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_Contact_Entity extends EZP_CS_Standard_Entity_Base {
 
        public $friendly_name = "";   
        public $public_id = "";
        public $creation_date;
        
        public static $TABLE_NAME = "easy_pie_contacts";
        
        function __construct() {
            
            parent::__construct(self::$TABLE_NAME);
            
            $this->public_id = EZP_CS_Utility::get_guid();
            $this->creation_date = date("Y-m-d H:i:s");
        }   
        
        public static function init_table() {
            
            $field_info = array();
            
            $field_info['friendly_name'] = 'varchar(255)';
            $field_info['public_id'] = 'char(36)';
            $field_info['creation_date'] = 'datetime';
            
            $index_array = array();
            $index_array["public_id_idx"] = "public_id";
            
            self::generic_init_table($field_info, self::$TABLE_NAME, $index_array);
        } 
        
        public static function delete_by_id($id) {
        
            self::delete_by_id_and_table($id, self::$TABLE_NAME);
        }
        
        public static function get_all()
        {
            return self::get_all_objects(get_class(), self::$TABLE_NAME);
        }
//        
        /**
         * 
         * @param type $id
         * @return EZP_CS_Contact_Entity
         */
        public static function get_by_id($id)
        {
            return self::get_by_id_and_type($id, get_class(), self::$TABLE_NAME);
        }
    }
    
 //   EZP_Contact_Entity::init_class();
}
?>