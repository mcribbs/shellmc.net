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

if (!class_exists('EZP_CS_Set_Entity')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Set_Entity extends EZP_CS_JSON_Entity_Base {
    
        const TYPE = "EZP_CS_Set_Entity";
        
        public $display_index;   
        public $content_index;
        
        function __construct() {
            
            parent::__construct();
        }    
        
        function create($display_index, $content_index)
        {
            $instance = new EZP_CS_Set_Entity();
            
            $instance->set("display_index", $display_index);
            $instance->set("content_index", $content_index);                    
            
            return $instance;
        }

        public static function get_all()
        {
            $base_types = EZP_CS_JSON_Entity_Base::get_by_type(self::TYPE);            
        }    
        
        /**
         * 
         * @param type $id
         * @return EZP_CS_Set_Entity
         */
        public static function get_by_id($id)
        {
            return EZP_CS_JSON_Entity_Base::get_by_id_and_type($id, self::TYPE);
        }
    }
}
?>