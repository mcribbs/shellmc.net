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

require_once(dirname(__FILE__) .  '/../class-ezp-cs-constants.php');

if (!class_exists('EZP_CS_Query_Utility')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2014 Synthetic Thought LLC
     */
    class EZP_CS_Query_Utility {
       
        const NUMBER_PER_PAGE = 20;
       // const NUMBER_PER_PAGE = 1;
        
        public static function init() {        
        }

        public static function is_table_present($simple_table_name) 
        {
            global $wpdb;
            
            $table_name = $wpdb->prefix . $simple_table_name;
            
            $table_query = "SHOW TABLES LIKE %s";
            $prepared_table_query = $wpdb->prepare($table_query, $table_name);
                        
            $table_rows = $wpdb->get_results($prepared_table_query);
            
            if(count($table_rows) == 0) {
            
                return false;
            }
            else {
                
                return true;
            }
        }
        // Returns list of subscribers to the coming soon plugin
        public static function get_subscriber_list($page_number = 0)
        {
            global $wpdb;

            $subscribers_table_name = $wpdb->prefix . EZP_CS_Subscriber_Entity::$TABLE_NAME;
            $contacts_table_name = $wpdb->prefix . EZP_Contact_Entity::$TABLE_NAME;
            $emails_table_name = $wpdb->prefix . EZP_Email_Entity::$TABLE_NAME;
            
            if($page_number >= 0) {
                
                $offset = $page_number * self::NUMBER_PER_PAGE;
                $limit_expression = " LIMIT $offset, " . self::NUMBER_PER_PAGE . ';';
            } else {
                
                $limit_expression = '';
            }
            
            $query_string = "select a.friendly_name, emails.email_address, a.contact_id, a.subscription_date FROM
                             $emails_table_name as emails inner join
                             (SELECT contacts.id as contact_id, contacts.friendly_name as friendly_name, subs.subscription_date FROM $subscribers_table_name AS subs 
                             INNER JOIN $contacts_table_name as contacts ON subs.contact_id = contacts.id) as a
                             on emails.contact_id = a.contact_id order by a.subscription_date desc" . $limit_expression;
                  
                        
            $rows = $wpdb->get_results($query_string);

            if($rows != NULL)
            {         
                return $rows;
            }
            else {
                return array();
            }
        }
        
        public static function get_subscriber_pages()
        {
            global $wpdb;

            $subscribers_table_name = $wpdb->prefix . EZP_CS_Subscriber_Entity::$TABLE_NAME;
            $contacts_table_name = $wpdb->prefix . EZP_Contact_Entity::$TABLE_NAME;
            $emails_table_name = $wpdb->prefix . EZP_Email_Entity::$TABLE_NAME;
            
            $query_string = "select count(a.friendly_name) as c FROM
                             $emails_table_name as emails inner join
                             (SELECT contacts.id as contact_id, contacts.friendly_name as friendly_name, subs.subscription_date FROM $subscribers_table_name AS subs 
                             INNER JOIN $contacts_table_name as contacts ON subs.contact_id = contacts.id) as a
                             on emails.contact_id = a.contact_id order by a.subscription_date desc";
                  
            $count = $wpdb->get_var($query_string);

            if($count != NULL)
            {    
                $num_pages = $count / self::NUMBER_PER_PAGE;
                
                if(($count % self::NUMBER_PER_PAGE) != 0) {
                    
                    return floor($num_pages ) + 1;    
                } else {
                    
                    return $num_pages;
                }                                     
            }
            else {
                
                return 0;
            }
        }

        public static function add_new_subscriber($friendly_name, $email_address) {
            
            $error_text = null;
            $friendly_name = sanitize_text_field($friendly_name);
            $email_address = sanitize_text_field($email_address);
            
            if(strlen($friendly_name) > 255) {
                
                $error_text = EZP_CS_Utility::__('Name is too long') . '.';
            } 
            
            if((!is_email($email_address)) || strlen($email_address) > 255) {
                
                $error_text = EZP_CS_Utility::__('Email address is not valid') . '.';
            }
            
            // Note: If error with entity, silently let it go. No sense telling user, just admin.
            if($error_text == null) {

                $contact = new EZP_Contact_Entity();            
                $contact->friendly_name = $friendly_name;            

                if($contact->save()) {
                                        
                    $email = new EZP_Email_Entity();            
                    $email->email_address = $email_address;
                    $email->contact_id = $contact->id;                                                    

                    if($email->save()) {
                        $cse = new EZP_CS_Subscriber_Entity();
                        $cse->contact_id = $contact->id;

                        if(!$cse->save()) {
                            
                            EZP_CS_Utility::debug('add_new_subscriber:Error saving subscriber entity to deleting email and contact');
                            $email->delete();
                            $contact->delete();
                        }
                    } else {
                        
                        EZP_CS_Utility::debug('add_new_subscriber:Error saving email entity so deleting contact');
                        $contact->delete();
                    }                        
                } else {
                    
                    EZP_CS_Utility::debug('add_new_subscriber:Error saving contact entity');
                    
                }                      
            }
            
            return $error_text;
        }        
        
        // Delete contact and all dependent data
        public static function purge_contact($contact_id)
        {
            $subscriber = EZP_CS_Subscriber_Entity::get_by_contact_id($contact_id);
            
            if($subscriber != null) {
                
                $subscriber->delete();
            }
            
            $emails = EZP_Email_Entity::get_by_contact_id($contact_id);
            foreach($email as $email) {
                
                /* @var $email EZP_Email_Entity */
                $email->delete();
            }
            
            EZP_Contact_Entity::delete_by_id($contact_id);
        }
    }
    
    EZP_CS_Query_Utility::init();    
}
?>