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
require_once("class-rest-base.php");

if (!class_exists('crm_ws')) {

    class Crm_Ws extends Rest_Base {

        public function Process() {
            $func = strtolower(trim(str_replace("/", "", $_REQUEST['cmd'])));

            //  $this->response('{}', 200);
            if ((int) method_exists($this, $func) > 0) {
                // RSR TODO: Dangerous - make this a solid list of known functions        
                /* RSR TODO $this->$func();*/
            } else {

                // RSR figure out better way
                //$this->response('', 404);
                $this->response('bad func', 200);
            }
            // If the method not exist with in this class, response would be "Page not found".
        }
        
        private function export_all()
        {
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false);
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"subscribers.csv\";" );
            header("Content-Transfer-Encoding: binary"); 
            
            echo "bob,riley@riley.com";
            exit;
        }

        // <editor-fold desc="Application related">
        private function AddContact() {

            // Cross validation if the request method is GET else it will return "Not Acceptable" status
            if ($this->get_request_method() != "POST") {
                $this->response('not a post', 406);
            }

            // RSR TODO: need to check for all parameters not just group_id!
            if (!isset($_REQUEST["group_id"])) {
                $this->response('no group_id', 406);
            } else {
                $name = $this->_request["name"];
                $email = $this->_request["email"];
            }

            $dal = new DAL();

            $alertId = $dal->AddAlert($groupId, $name, $expression);

            $dal = null;

            $alert = new stdClass();
            $alert->alert_id = $alertId;

            $this->response($this->json($alert), 200);
        }

        private function UpdateAlert() {

            // Cross validation if the request method is GET else it will return "Not Acceptable" status
            if ($this->get_request_method() != "POST") {
                $this->response('not a post', 406);
            }

            // RSR TODO: need to check for all parameters not just group_id!
            if (!isset($_REQUEST["group_id"])) {
                $this->response('no group_id', 406);
            } else {
                $alertId = $this->_request["alert_id"];
                $groupId = $this->_request["group_id"];
                $name = $this->_request["name"];
                $expression = $this->_request["expression"];
            }

            $dal = new DAL();

            $alertId = $dal->UpdateAlert($alertId, $groupId, $name, $expression);

            $dal = null;

            $alert = new stdClass();
            $alert->alert_id = $alertId;

            $this->response($this->json($alert), 200);
        }

        // <editor-fold desc="Application related">
        private function DeleteAlert() {

            // Cross validation if the request method is GET else it will return "Not Acceptable" status
            if ($this->get_request_method() != "POST") {
                $this->response('not a post', 200);
            }

            // RSR TODO: need to check for all parameters not just group_id!
            if (!isset($_REQUEST["alert_id"])) {
                $this->response('no alert_id', 200);
            } else {
                $alertId = $this->_request["alert_id"];
            }

            $dal = new DAL();

            $dal->DeleteAlert($alertId);

            $dal = null;
        }

        // </editor-fold>
        // <editor-fold desc="Alerts related">
        // 
        // RSR TODO: Consider making other indexes guids instead of ints like group index
        private function GetAppsByGroupID() {

            // Cross validation if the request method is GET else it will return "Not Acceptable" status
            if ($this->get_request_method() != "GET") {
                $this->response('', 406);
            }

            if (!isset($_REQUEST["group_id"])) {
                $this->response('', 406);
            } else {
                $groupId = $_REQUEST["group_id"];
            }

            $dal = new DAL();

            try {
                $result = $dal->GetAppsByGroupID($groupId);
            } catch (Exception $ex) {
                // RSR TODO: come up with a better error reporting mechanism
                $this->response($ex->getMessage(), 200);
            }

            $dal = null;
        }

        // RSR TODO: Consider making other indexes guids instead of ints like group index
        private function AddApplicationToGroup() {

            // Cross validation if the request method is GET else it will return "Not Acceptable" status
            if ($this->get_request_method() != "POST") {
                $this->response('not a post', 406);
            }

            // RSR TODO: need to check for all parameters not just group_id!
            if (!isset($_REQUEST["group_id"])) {
                $this->response('no group_id', 406);
            } else {
                $groupId = $this->_request["group_id"];
                $applicationId = $this->_request["application_id"];
                $genreId = $this->_request["genre_id"];
                $title = $this->_request["title"];
                $iconUrl = $this->_request["icon_url"];
            }

            $dal = new DAL();

            try {
                $dal->SafeAddApplication($applicationId, $genreId, $title, $iconUrl);
                $dal->AddApplicationToGroup($groupId, $applicationId);
            } catch (Exception $ex) {
                $dal->DeleteApplicationFromGroup($groupId, $applicationId);
                $dal->SafeDeleteApplication($applicationId);

                // RSR TODO: come up with a better error reporting mechanism
                $this->response($ex->getMessage(), 200);
            }

            $dal = null;
        }

        private function RemoveApplicationFromGroup() {
            // Cross validation if the request method is GET else it will return "Not Acceptable" status
            if ($this->get_request_method() != "POST") {
                $this->response('not a post', 406);
            }

            // RSR TODO: come up with pattern for checking for all parameters
            if (!isset($_REQUEST["group_id"])) {
                $this->response('no group_id', 406);
            } else {
                $groupId = $this->_request["group_id"];
                $applicationId = $this->_request["application_id"];
            }
            $dal = new DAL();

            try {
                $dal->DeleteApplicationFromGroup($groupId, $applicationId);
                $dal->SafeDeleteApplication($applicationId);
            } catch (Exception $ex) {
                // RSR TODO: How to recover
                // RSR TODO: come up with a better error reporting mechanism
                $this->response($ex->getMessage(), 200);
            }
        }

        // </editor-fold>
    }

}

$api = new Crm_Ws();
$api->Process();
?>
