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

    #easy-pie-cs-postbox-inside { /* width: 550px;*/ }
    #easy-pie-cs-postbox-inside h3 { font-size: 16px }

    #easy-pie-cs-delete-confirm { display:none; }
    .ezp-cs-mail-provider {  margin:0px 15px 10px 15px; float:left; }
    .ezp-cs-mail-provider a { text-decoration:none;}
    .ezp-cs-mail-provider img { width:155px; border: 1px solid lightgray;border-radius: 5px; box-shadow: 1px 7px 36px -5px rgba(34,34,34,1);}

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
        ?>
        <div class="postbox" style="margin-top:12px;" >
            <div class="inside" id="easy-pie-cs-postbox-inside" >
                <h2>Create a newsletter using your subscriber list</h2>
                <h3 style="margin-top:25px; margin-bottom:10px;">Step 1. Save subscriber list as a CSV file to your local hard drive.</h3>
                <button style="margin:0px; font-size:1.1em; font-weight:bold;" id="btn-export" type="button" onclick="location.href = ajaxurl + '?action=EZP_CS_export_all_subscribers&_wpnonce=<?php echo $_wpnonce; ?>';
                        return false;"><?php EZP_CS_Utility::_e('Save'); ?></button>
                <h3 style="margin-top:40px;margin-bottom:10px;">Step 2. Import CSV file into an email marketing service.</h3>
                <p>Import the saved CSV file into one of the following providers or use your own.</p>
                <div style="height:100px">
                    <div class='ezp-cs-mail-provider' style="margin-left:0px;"><a href="http://easypiewp.com/aweber" target="_blank"><img src="<?php echo EZP_CS_Utility::$PLUGIN_URL . '/images/affiliates/aweber-200.png'?> "/></a></div>
                    <div class='ezp-cs-mail-provider'><a href="http://easypiewp.com/getresponse" target="_blank"><img src="<?php echo EZP_CS_Utility::$PLUGIN_URL . '/images/affiliates/getresponse-200.png'?> "/></a></div>
                    <div class='ezp-cs-mail-provider'><a href="http://easypiewp.com/MadMimi" target="_blank"><img src="<?php echo EZP_CS_Utility::$PLUGIN_URL . '/images/affiliates/mad-mimi2-200.png'?> "/></a></div>
                    <div class='ezp-cs-mail-provider' style="clear:right"><a href="http://easypiewp.com/mailchimp" target="_blank"><img src="<?php echo EZP_CS_Utility::$PLUGIN_URL . '/images/affiliates/mailchimp-200.png'?> "/></a></div>
                </div>
            </div>
            <p style="margin-left:8px;margin-top:10px;font-style:italic; clear:both">
                *Some of the above are 'affiliate links'. These do not affect the price of paid options.
            </p>
        </div>
    </div>
    <div id="easy-pie-cs-delete-confirm" title="<?php EZP_CS_Utility::_e('Delete User?'); ?>" >
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><span id='easy-pie-cs-delete-confirm-text'></span></p>
    </div>         