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

require_once(dirname(__FILE__) . '/../class-ezp-cs-verifiers.php');
require_once(dirname(__FILE__) . '/../Utilities/class-ezp-cs-utility.php');

if (!class_exists('EZP_CS_Standard_Entity_Base')) {

    /**
     * Base class for standard entities
     *
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Standard_Entity_Base {

        public $id;
        private $dirty;
        protected $table_name;
        protected $verifiers;

        function __construct($base_table_name) {

            global $wpdb;

            $this->id = -1;
            $this->dirty = false;
            $this->verifiers = array();

            $this->table_name = $wpdb->prefix . $base_table_name;
        }

        protected static function generic_init_table($field_information, $base_table_name, $index_array = null) {

            global $wpdb;

            $table_name = $wpdb->prefix . $base_table_name;

            if ($index_array != null) {
                                
                if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
                    
                    foreach ($index_array as $index_name => $index_columns) {
                        
                        //Image Ratings Table
                        $sql = "ALTER TABLE " . $table_name .
                                " DROP INDEX $index_name";
                        
                        $wpdb->query($sql);
                    }
                }
                else {
                    //EZP_CS_Utility::debug("$table_name table not present so didn't strip indices");
                }
            }
            $query_string = "CREATE TABLE $table_name (\r\n";
            $query_string .= "id INT NOT NULL AUTO_INCREMENT,\r\n";

            foreach ($field_information as $field_name => $field_type) {

                $query_string .= "$field_name $field_type,\r\n";
            }
                
            $query_string .= 'PRIMARY KEY  (id)' . "\r\n);";

            dbDelta($query_string);
            
            $query_string = "";
            
            if($index_array != null) {
                
                foreach ($index_array as $index_name => $index_columns) {

                    //Image Ratings Table
                    $query_string .= "CREATE INDEX $index_name ON $table_name ($index_columns);";
                }
            }
            
            if($query_string != "") {
                
                $wpdb->query($query_string);
            }
        }

        public function insert() {
            global $wpdb;

            $query_string = "INSERT INTO $this->table_name (";

            $properties = EZP_CS_Utility::get_public_properties($this);

            $keys = array_keys($properties);
            $query_string .= implode(',', $keys);
            $query_string .= ') VALUES (';

            foreach ($properties as $property_value) {
                $query_string .= EZP_CS_Utility::get_db_type_format($property_value) . ",";
            }

            if (count($properties) > 0) {

                $query_string = substr($query_string, 0, -1);
            }

            $query_string .= ');';

            $prepared_query = $wpdb->prepare($query_string, $properties);

            $wpdb->query($prepared_query);

            $this->id = $wpdb->insert_id;

            if ($this->id == false) {

                $this->id = -1;

                EZP_CS_Utility::debug("Error inserting. Query: " . $prepared_query);
                
                return false;
            } else {
                
                return true;
            }
        }

        public function update() {
            global $wpdb;

            $query_string = "UPDATE " . $this->table_name;

            $query_string .= " SET ";

            $properties = EZP_CS_Utility::get_public_properties($this);

            foreach ($properties as $prop_name => $prop_value) {
                $type_format = EZP_CS_Utility::get_db_type_format($prop_value);

                $query_string .= "$prop_name = $type_format,";
            }

            if (count($properties) > 0) {

                $query_string = substr($query_string, 0, -1);
            }

            $query_string .= " WHERE id = " . $this->id;

            $prepared_query = $wpdb->prepare($query_string, $properties);

            $wpdb->query($prepared_query);

            $this->dirty = false;
            
            return true;
        }

        /*
         * Only INNODB supports foreign key constraints so no cascading changes!
         */

        public function delete() {

            self::delete_by_id($this->id, $this->table_name);

            $this->id = -1;
            $this->dirty = false;
        }

        /*
         * Only INNODB supports foreign key constraints so no cascading changes!
         */

        public static function delete_by_id_and_table($id, $base_table_name) {
            global $wpdb;

            $table_name = $wpdb->prefix . $base_table_name;

            $query_string = "DELETE FROM " . $table_name;
            $query_string .= " WHERE id = %d;";

            $prepared_query = $wpdb->prepare($query_string, $id);

            $wpdb->query($prepared_query);
        }

        public static function get_by_id_and_type($id, $class_name, $base_table_name) {

            return self::get_by_unique_field_and_type("id", $id, $class_name, $base_table_name);
        }

        public static function get_by_unique_field_and_type($field_name, $field_value, $class_name, $base_table_name) {

            global $wpdb;

            $table_name = $wpdb->prefix . $base_table_name;

            $query_string = "SELECT * FROM " . $table_name;
            $query_string .= " WHERE $field_name = %d;";

            $prepped = $wpdb->prepare($query_string, $field_value);

            $row = $wpdb->get_row($prepped);

            if ($row != NULL) {
                return self::get_instance_from_row($row, $class_name, $table_name);
            } else {
                EZP_CS_Utility::debug("get_by_unique_field_and_type: row is null for $table_name, $field_name, $field_value");
                return null;
            }
        }

        public static function get_all_objects($class_name, $base_table_name, $page = 0) {

            global $wpdb;

            $table_name = $wpdb->prefix . $base_table_name;

            $query_string = "SELECT * FROM " . $table_name;

            if ($page > 0) {

                $records_per_page = 50;

                $offset = ($page - 1) * $records_per_page;

                $query_string .= " LIMIT $offset, $records_per_page";
            }

            $query_string .= ';';

            $rows = $wpdb->get_results($query_string);

            $instances = array();

            foreach ($rows as $row) {

                $instance = self::get_instance_from_row($row, $class_name, $table_name);

                array_push($instances, $instance);
            }

            return $instances;
        }

        public function save() {

            $saved = false;

            if ($this->id == -1) {

                $saved = $this->insert();
                
            } else { //screw the dirty part - too problematic if we update member directlyif ($this->dirty) {
                
                $saved = $this->update();
            }

            $this->dirty = false;

            return $saved;
        }

        public function set_post_variables($post) {

            $error_string = "";

            // First do a verifier scrub and only then let it fall through to set
            foreach ($post as $key => $value) {

                $value = stripslashes($value);

                if (array_key_exists($key, $this->verifiers)) {

                    $local_error = $this->verifiers[$key]->verify($value);

                    if ($local_error != "") {

                        $error_string .= $local_error . ".<br/>";
                    }

                    $this->set($key, $value);
                } else {
                    $this->set($key, $value);
                }
            }

            return $error_string;
        }

        public function set($property_name, $property_value) {

            if(property_exists($this->type, $property_name)) {
                
                $this->$property_name = $property_value;

                $this->dirty = true;
            }                
        }

        public function get($property_name) {

            if (property_exists($this, $property_name)) {

                return $this->$property_name;
            } else {

                return null;
            }
        }

        private static function get_instance_from_row($row, $class_name, $table_name) {
            $instance = new $class_name();
            $instance->id = $row->id;
            $instance->table_name = $table_name;

            $properties = EZP_CS_Utility::get_public_class_properties($class_name);

            foreach ($properties as $prop_name => $prop_value) {

                if (property_exists($row, $prop_name)) {

                    $instance->$prop_name = $row->$prop_name;
                }
            }

            return $instance;
        }
    }
}
?>