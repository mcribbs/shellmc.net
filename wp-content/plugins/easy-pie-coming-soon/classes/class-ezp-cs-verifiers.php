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

if (!class_exists('EZP_CS_Verifier_Base')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Verifier_Base {

        protected $error_text;
        
        function __construct($error_text) {            
            
            $this->error_text = $error_text;
        } 
        
        // Returns an error string if succeeded or empty string if failed.
        public function Verify($value) {
            return "";
        }
    }
}

if (!class_exists('EZP_CS_Range_Verifier')) {

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Range_Verifier extends EZP_CS_Verifier_Base {

        private $min = 0;
        private $max = 0;
                
        function __construct($min, $max, $error_text) {
            
            parent::__construct($error_text);
            
            $this->min = $min;
            $this->max = $max;
        } 
        
        // Returns an error string if succeeded or empty string if failed.
        public function Verify($value) {

            if(($value < $this->min) || ($value > $this->max)) {
                
                return $this->error_text;
            } else {
                
                return "";
            }
        }
    }
}

if (!class_exists('EZP_CS_Regex_Verifier')) {

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Regex_Verifier extends EZP_CS_Verifier_Base {

        private $regex = 0;
                
        function __construct($regex, $error_text) {
            
            parent::__construct($error_text);
            
            $this->regex = $regex;
        } 
        
        // Returns an error string if succeeded or empty string if failed.
        public function Verify($value) {

            if(preg_match($this->regex, $value) != 1) {
                            
                return $this->error_text;
            } else {
                
                return "";
            }
        }
    }
}
?>