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

//- Settings Logic -
$action_updated = null;

$global = EZP_CS_Global_Entity::get_instance();

$set_index = $global->active_set_index;

$set = EZP_CS_Set_Entity::get_by_id($set_index);

$content = EZP_CS_Content_Entity::get_by_id($set->content_index);

$config = EZP_CS_Config_Entity::get_by_id($global->config_index);

$error_string = "";

if (isset($_POST['action']) && $_POST['action'] == 'save') {

    check_admin_referer('easy-pie-coming-soon-save-content');

    // Artificially set the bools since they aren't part of the postback    
    // TODO
    $error_string = $content->set_post_variables($_POST);

    if ($error_string == "") {

        $action_updated = $content->save();
    }
}
?>
<script type="text/javascript">
   ezp_cs_datepicker_date_format = "<?php echo EZP_CS_Render_Utility::get_datepicker_date_format(); ?>";       
</script>

<?php wp_nonce_field('easy-pie-coming-soon-save-content'); ?>
<input type="hidden" name="action" value="save"/>

<?php
//EZP_CS_Utility::display_admin_notice($config->coming_soon_mode_on);

if ($error_string != "") :
    ?>
    <div id="message" class="error below-h2"><p><?php echo EZP_CS_Utility::__('Errors present:') . "<br/> $error_string" ?></p></div>
<?php endif; ?>

<?php if ($action_updated) : ?>
    <div id="message" class="updated below-h2"><p><span><?php echo EZP_CS_Utility::__('Settings Saved.'); ?></span><strong style="margin-left:7px;"><?php echo '  ' . EZP_CS_Utility::__('If you have a caching plugin be sure to clear it.'); ?></strong></p></div>
<?php endif; ?>

<div class="postbox" style="margin-top:12px;" >
    <div class="inside" >
        <h3 ><?php EZP_CS_Utility::_e("Logo") ?></h3>
        <table class="form-table"> 
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Image") ?>
                </th>
                <td>
                    <div class="compound-setting">
                        <input id="easy-pie-cs-logo-url" name="logo_url" value="<?php echo $content->logo_url; ?>" />                            
                        <button id="easy-pie-cs-logo-button"><?php EZP_CS_Utility::_e("Upload"); ?></button>
                        <img id="easy-pie-cs-logo-preview" style="display: <?php echo $content->logo_url == '' ? 'none' : 'block' ?> ;width:80px;height:80px;margin-top:8px;" src="<?php echo $content->logo_url; ?>" />
                    </div>                                                   
                </td>
            </tr>
        </table>
    </div></div>

<div class="postbox" >
    <div class="inside" >
        <h3 ><?php EZP_CS_Utility::_e("Title") ?></h3>
        <table class="form-table"> 
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Title") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class="long-input" name="title" type="text" value="<?php EZP_CS_Utility::_he($content->title); ?>" />
                    </div>
                </td>
            </tr>   
        </table>
    </div>
</div>

<div class="postbox" >
    <div class="inside" >
        <h3 ><?php EZP_CS_Utility::_e("Main Text") ?></h3>
        <table class="form-table"> 
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Headline") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class="long-input"  name="headline" type="text" value="<?php EZP_CS_Utility::_he($content->headline); ?>" />
                    </div>
                </td>
            </tr>   
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Description") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <textarea rows="5" cols="67" name="description" type="text" ><?php EZP_CS_Utility::_he($content->description); ?></textarea>
                    </div>
                </td>
            </tr>   
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Disclaimer") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class="long-input"  name="disclaimer" type="text" value="<?php EZP_CS_Utility::_he($content->disclaimer); ?>" />              
                    </div>
                </td>
            </tr>               
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Footer") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class="long-input" name="footer" type="text" value="<?php EZP_CS_Utility::_he($content->footer); ?>" />
                    </div>
                </td>
            </tr>      
        </table> 
    </div></div>

<div class="postbox" >
    <div class="inside" >
        <h3><?php EZP_CS_Utility::_e("Email Text") ?></h3>        

        <table class="form-table"> 
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Email Placeholder") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class="long-input"  name="email_placeholder_text" type="text" value="<?php EZP_CS_Utility::_he($content->email_placeholder_text); ?>" />
                    </div>
                </td>
            </tr>   
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Name Placeholder") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class="long-input"  name="name_placeholder_text" type="text" value="<?php EZP_CS_Utility::_he($content->name_placeholder_text); ?>" />
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Button") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input name="email_button_text" type="text" value="<?php echo $content->email_button_text; ?>" />
                    </div>
                </td>
            </tr>       
        </table>
        <div style="margin-top:17px"><span class="description"><?php echo '*' . EZP_CS_Utility::__('Section relevant only if email collection is enabled in') . ' <a href="' . admin_url() . 'admin.php?page=' . EZP_CS_Constants::$SETTINGS_SUBMENU_SLUG . '">' . self::__('settings') . '</a>'; ?></span></div>
    </div>
</div>

<div class="postbox" >
    <div class="inside" >
        <h3 ><?php EZP_CS_Utility::_e("Thank You Text") ?></h3>
        <table class="form-table"> 
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Headline") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class="long-input"  name="thank_you_headline" type="text" value="<?php EZP_CS_Utility::_he($content->thank_you_headline); ?>" />
                    </div>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Text") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <textarea rows="5" cols="67" name="thank_you_description" type="text"><?php EZP_CS_Utility::_he($content->thank_you_description); ?></textarea>
                    </div>        
                </td>
            </tr>
        </table>
        <div style="margin-top:17px"><span class="description"><?php echo '*' . EZP_CS_Utility::__('Section relevant only if email collection is enabled in') . ' <a href="' . admin_url() . 'admin.php?page=' . EZP_CS_Constants::$SETTINGS_SUBMENU_SLUG . '">' . self::__('settings') . '</a>'; ?></span></div>
    </div></div>

<div class="postbox" >
    <div class="inside" >
        <h3 ><?php EZP_CS_Utility::_e("Countdown") ?></h3>
        <table class="form-table"> 
            <tr>
                <th scope="row">
                    <?php echo EZP_CS_Utility::_e("Due Date") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input style="width:90px;" id="ezp-countdown-due-date" class="long-input" name="countdown_due_date" type="text" value="<?php EZP_CS_Utility::_he($content->countdown_due_date); ?>" />
                        <div><span class="description"><?php EZP_CS_Utility::_e('Countdown timer will display when populated'); ?></span></div>
                    </div>
                </td>
            </tr>


        </table>
    </div></div>