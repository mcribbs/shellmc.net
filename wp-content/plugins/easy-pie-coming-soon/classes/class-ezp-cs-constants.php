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

if (!class_exists('EZP_CS_Constants')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Constants {

        const COMPOUND_OPTION_NAME = 'easy-pie-cs-options';
        const MAIN_PAGE_KEY = 'easy-pie-cs-main-page';
        const PLUGIN_SLUG = 'easy-pie-coming-soon';
        const PLUGIN_VERSION = "0.5.8"; // RSR Version
        

        
        const PLUGIN_VERSION_OPTION_KEY = "easy_pie_cs_version"; // RSR Version

        /* Pseudo constants */
        public static $PLUGIN_DIR;
        public static $TEMPLATE_SUBMENU_SLUG;
        public static $SETTINGS_SUBMENU_SLUG;
        public static $SUBSCRIBERS_SUBMENU_SLUG;
        public static $PREVIEW_SUBMENU_SLUG;
        public static $COMING_SOON_PRO_SUBMENU_SLUG;

        public static function init() {

            $__dir__ = dirname(__FILE__);
            
            self::$PLUGIN_DIR = $__dir__ . "../" . self::PLUGIN_SLUG;
            
            self::$TEMPLATE_SUBMENU_SLUG = EZP_CS_Constants::PLUGIN_SLUG;
            self::$SETTINGS_SUBMENU_SLUG = EZP_CS_Constants::PLUGIN_SLUG . '-settings';
            self::$SUBSCRIBERS_SUBMENU_SLUG = EZP_CS_Constants::PLUGIN_SLUG . '-subscribers';            
            self::$PREVIEW_SUBMENU_SLUG = EZP_CS_Constants::PLUGIN_SLUG . '-view';            
            self::$COMING_SOON_PRO_SUBMENU_SLUG = EZP_CS_Constants::PLUGIN_SLUG . '-coming-soon-pro';
        }

    }

    EZP_CS_Constants::init();
}
?>