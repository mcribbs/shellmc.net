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
?>
<?php
$global = EZP_CS_Global_Entity::get_instance();

$config = EZP_CS_Config_Entity::get_by_id($global->config_index);
?>

<script type="text/javascript" src='<?php echo EZP_CS_Utility::$PLUGIN_URL . "/js/page-subscribers.js?" . EZP_CS_Constants::PLUGIN_VERSION; ?>'></script>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}

    #easypie-cs-subscriber-table {font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
                                  font-size: 12px;
                                  background: #fff;
                                  width: 100%;
                                  border-collapse: collapse;
                                  text-align: left;
                                  margin: 20px;}
    #easypie-cs-subscriber-table th  {
        font-weight:bold;
        text-decoration: underline;
        padding-bottom: 4px;        
        padding-left:10px;
        width: 150px;
        text-align:left;

    }

    #easypie-cs-subscriber-table td  {        
        border-bottom: 1px solid #ccc;
        color: #669;
        padding-bottom: 4px;        
        padding-left:10px;
        text-align:left;
        max-width: 150px;
        width: 150px;
        text-overflow: ellipsis;
        overflow: hidden;

    }    

    #easypie-cs-subscriber-table button  {
        float:right;
        /*padding: 6px 8px;*/
    }    

    #easy-pie-cs-subscriber-controls {
        text-align:left;
        margin-left:15px;
    }

    #easy-pie-cs-postbox-inside { width: 550px; }

    #easy-pie-cs-delete-confirm { display:none; }

    #easy-pie-cs-subscriber-delete-column { width: 30px!important;}
</style>

<div class="wrap">

    <div id="easypie-cs-options" class="inside">

        <?php
        $_wpnonce = wp_create_nonce('easy-pie-cs-change-subscribers');
        $current_page_idx = 0;
        $prev_page_idx = 0;
        $next_page_idx = 1;

        if (isset($_GET['page_idx'])) {

            $current_page_idx = $_GET['page_idx'];
            $prev_page_idx = $current_page_idx > 0 ? $current_page_idx - 1 : 0;
            $next_page_idx = $current_page_idx + 1;
        }

        $subscribers = EZP_CS_Query_Utility::get_subscriber_list($current_page_idx);

        $next_url = admin_url("admin.php?page=easy-pie-coming-soon-subscribers&page_idx=$next_page_idx");
        $prev_url = admin_url("admin.php?page=easy-pie-coming-soon-subscribers&page_idx=$prev_page_idx");

        if ($current_page_idx == 0) {

            $prev_disabled = 'disabled';
        } else {

            $prev_disabled = '';
        }

        $num_pages = EZP_CS_Query_Utility::get_subscriber_pages();

        if ($next_page_idx >= $num_pages) {

            $next_disabled = 'disabled';
        } else {

            $next_disabled = '';
        }

        if ($num_pages == 0) {

            $controls_display = 'none';
        } else {

            $controls_display = 'block';
        }
        ?>
        <div class="postbox" style="margin-top:12px;" >
            <div class="inside" id="easy-pie-cs-postbox-inside" >

                                
                <table id="easypie-cs-subscriber-table">
                    <tr>
                        <th><?php EZP_CS_Utility::_e('Name'); ?></th>
                        <th><?php EZP_CS_Utility::_e('Email'); ?></th>
                        <th><?php EZP_CS_Utility::_e('Date'); ?></th>
                        <th id='easy-pie-cs-subscriber-delete-column'><?php EZP_CS_Utility::_e('Delete'); ?></th>
                    </tr>

                    <?php
                    foreach ($subscribers as $subscriber) {

                        if ($subscriber->subscription_date != '') {
                            $localized_date = date_i18n(get_option('date_format'), strtotime($subscriber->subscription_date));
                        } else {
                            $localized_date = EZP_CS_Utility::__('unknown');
                        }
                        //      $localized_date = strtotime($subscriber->subscription_date);

                        $delete = EZP_CS_Utility::__('Delete');
                        $confirm_text = trim($subscriber->friendly_name) == '' ? EZP_CS_Utility::__("Delete user with email $subscriber->email_address?") : EZP_CS_Utility::__("Delete $subscriber->friendly_name?");
                        $yes_text = EZP_CS_Utility::__('Yes');
                        $no_text = EZP_CS_Utility::__('No');
                        $delete_text = EZP_CS_Utility::__('Delete');
                        $scrubbed_email = esc_html($subscriber->email_address);
                        $scrubbed_name = esc_html($subscriber->friendly_name);

                        echo "<tr><td title='$scrubbed_name'>$scrubbed_name</td><td title='$scrubbed_email'><a target='_blank' href='mailto:$scrubbed_email'>$scrubbed_email</a></td><td>$localized_date</td><td><a href='#' onclick='easyPie.CS.PurgeContact(\"$confirm_text\", \"$yes_text\", \"$no_text\", $subscriber->contact_id, \"$_wpnonce\", function() { location.reload(); });return false;'><span title='$delete_text' class='ui-icon ui-icon-closethick'></a></tr>";
                    }
                    ?>        
                </table>
                <div id="easy-pie-cs-subscriber-controls" style="display: <?php echo $controls_display; ?>; text-align:center;">
                    <button id="btn-prev" <?php echo $prev_disabled; ?> name="submit" type="submit" value="previous" onclick="window.location = '<?php echo $prev_url; ?>';
                            return false;"><span style='float:left; margin-top:1px' class="ui-icon ui-icon-triangle-1-w"></span><?php EZP_CS_Utility::_e("Prev"); ?></button>
                    <span ><?php echo EZP_CS_Utility::__('Page') . ' ' . ($current_page_idx + 1) . ' ' . EZP_CS_Utility::__('of') . ' ' . $num_pages; ?></span>
                    <button id="btn-next" <?php echo $next_disabled; ?> name="submit" type="submit" value="next" onclick="window.location = '<?php echo $next_url; ?>';
                            return false;"><?php EZP_CS_Utility::_e("Next"); ?><span style='float:right; margin-top:1px' class="ui-icon ui-icon-triangle-1-e"></span></button>

                </div>
                <!-- RSR TODO: time box; TODO: random wording -->
                <?php echo EZP_CS_Utility::get_coupon_text(); ?>                
            </div>
        </div>
    </div>
    <div id="easy-pie-cs-delete-confirm" title="<?php EZP_CS_Utility::_e('Delete User?'); ?>" >
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><span id='easy-pie-cs-delete-confirm-text'></span></p>
    </div>         