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

if (!class_exists('EZP_CS_Content_Entity')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Content_Entity extends EZP_CS_JSON_Entity_Base {

        const TYPE = "EZP_CS_Content_Entity";

        public $logo_url = "";
        public $headline;
        public $description;
        public $disclaimer;
        public $footer;
        public $email_placeholder_text;
        public $name_placeholder_text;
        public $email_button_text;
        public $thank_you_headline;
        public $thank_you_description;
        public $title;
        
        public $countdown_due_date;


        function __construct() {

            $this->headline = EZP_CS_Utility::__("Coming soon");
            $this->description = EZP_CS_Utility::__("Our exciting new website is coming soon! Check back later.");
            $this->disclaimer = EZP_CS_Utility::__("We won't spam you or sell your email address. Pinky swear.");
            $this->footer = EZP_CS_Utility::__("(C)2014 My Company LLC");

            $this->email_placeholder_text = EZP_CS_Utility::__("Enter email");
            $this->name_placeholder_text = EZP_CS_Utility::__("Enter name");
            $this->email_button_text = EZP_CS_Utility::__("Subscribe");

            $this->thank_you_headline = EZP_CS_Utility::__("Thank you!");
            $this->thank_you_description = EZP_CS_Utility::__("You'll hear from us when we launch.");

            $this->title = EZP_CS_Utility::__("Coming soon");
            
            $this->countdown_due_date = "";
            parent::__construct();
        }

        public function create_with_defaults() {

            $instance = new EZP_CS_Content_Entity();

            return $instance;
        }

        /**
         * 
         * @param type $id
         * @return EZP_CS_Content_Entity
         */
        public static function get_by_id($id) {
            return EZP_CS_JSON_Entity_Base::get_by_id_and_type($id, self::TYPE);
        }
    }
}
?>