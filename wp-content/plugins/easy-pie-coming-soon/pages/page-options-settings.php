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

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>

<div class="wrap">

    <?php screen_icon(EZP_CS_Constants::PLUGIN_SLUG); ?>
    <h2>Easy Pie Coming Soon: <?php EZP_CS_Utility::_e('Settings') ?></h2>
    <?php
    if (isset($_GET['settings-updated'])) {
        echo "<div class='updated'><p>" . EZP_CS_Utility::__('If you have a caching plugin, be sure to clear the cache!') . "</p></div>";
    }
    ?>
    <div id="easypie-cs-options" class="inside">
        <form id="easy-pie-cs-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_CS_Constants::$SETTINGS_SUBMENU_SLUG); ?>" > 
            <?php
            $action_updated = null;

            $global = EZP_CS_Global_Entity::get_instance();

            $config = EZP_CS_Config_Entity::get_by_id($global->config_index);

            $error_string = "";

            if (isset($_POST['action']) && $_POST['action'] == 'save') {

                check_admin_referer('easy-pie-coming-soon-save-settings');

                // Artificially set the bools since they aren't part of the postback
                $config->collect_email = false;
                $config->collect_name = false;

                $error_string = $config->set_post_variables($_POST);

                if ($error_string == "") {

                    $config->fix_url_fields();

                    $action_updated = $config->save();
                }
            }
            ?>

            <?php wp_nonce_field('easy-pie-coming-soon-save-settings'); ?>
            <input type="hidden" name="action" value="save"/>            
            <?php
            EZP_CS_Utility::display_admin_notice($config->coming_soon_mode_on);

            if ($error_string != "") :
                ?>
                <div id="message" class="error below-h2"><p><?php echo EZP_CS_Utility::__('Errors present:') . "<br/> $error_string" ?></p></div>
            <?php endif; ?>

            <?php if ($action_updated) : ?>
                <div id="message" class="updated below-h2"><p><span><?php echo EZP_CS_Utility::__('Settings Saved.'); ?></span><strong style="margin-left:7px;"><?php echo '  ' . EZP_CS_Utility::__('If you have a caching plugin be sure to clear it.'); ?></strong></p></div>
            <?php endif; ?>

            <div class="postbox" style="margin-top:12px;" >
                <div class="inside" >
                    <h3 ><?php EZP_CS_Utility::_e("General") ?></h3>
                    <table class="form-table"> 
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Status") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <input type="radio" name="coming_soon_mode_on" value="true" <?php echo $config->coming_soon_mode_on ? 'checked' : ''; ?>/><span><?php echo EZP_CS_Utility::__('On'); ?></span>
                                    <input type="radio" name="coming_soon_mode_on" value="" <?php echo $config->coming_soon_mode_on ? '' : 'checked'; ?>/><span><?php echo EZP_CS_Utility::__('Off'); ?></span>                                    
                                </div>
                            </td>
                        </tr>                          
                    </table>
                </div>
            </div>

            <div class="postbox" style="margin-top:12px;" >
                <div class="inside" >
                    <h3 ><?php EZP_CS_Utility::_e("Collection") ?></h3>
                    <table class="form-table"> 
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Collect Email") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <input type="checkbox" name="collect_email" <?php echo $config->collect_email ? 'checked' : ''; ?> />                                                                                                                
                                    <span><?php EZP_CS_Utility::_e("Yes") ?></span>
                                </div>
                            </td>
                        </tr>   
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Collect Name") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <input type="checkbox" name="collect_name" <?php echo $config->collect_name ? 'checked' : ''; ?> />                                                                                                                
                                    <span><?php EZP_CS_Utility::_e("Yes") ?></span>
                                </div>
                            </td>
                        </tr>   
                    </table>
                </div>
            </div>

            <div class="postbox" >
                <div class="inside" >
                    <h3 ><?php EZP_CS_Utility::_e("HTTP") ?></h3>
                    <table class="form-table"> 
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Return Code") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <input type="radio" name="return_code" value="200" <?php echo $config->return_code == 200 ? 'checked' : ''; ?> /><?php echo EZP_CS_Utility::_e("200") ?>
                                    <input type="radio" name="return_code" value="503" <?php echo $config->return_code == 503 ? 'checked' : ''; ?> /><?php echo EZP_CS_Utility::_e("503") ?>
                                </div>
                            </td>
                        </tr>  
                    </table>
                </div></div>

            <div class="postbox" >
                <div class="inside" >
                    <h3><?php EZP_CS_Utility::_e("Social") ?></h3>
                    <table class="form-table"> 
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Facebook URL") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <input class="long-input" name="facebook_url" type="text" value="<?php echo $config->facebook_url; ?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Google Plus URL") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <input class="long-input" name="google_plus_url" type="text" value="<?php echo $config->google_plus_url; ?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Twitter URL") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <input class="long-input" name="twitter_url" type="text" value="<?php echo $config->twitter_url; ?>" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="postbox" >
                <div class="inside" >
                    <h3><?php EZP_CS_Utility::_e("SEO") ?></h3>
                    <table class="form-table"> 
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Author URL") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <input class="long-input" name="author_url" type="text" value="<?php echo $config->author_url; ?>" />
                                    <div><span class="description"><?php EZP_CS_Utility::_e('Google+ or other identifying URL'); ?></span></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Meta Description") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <textarea rows="5" cols="60" name="meta_description" type="text" ><?php echo EZP_CS_Utility::_he($config->meta_description); ?></textarea>
                                </div>
                            </td>
                        </tr>    
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Meta Keywords") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <input class="long-input" name="meta_keywords" type="text" value="<?php echo $config->meta_keywords; ?>" />
                                    <div><span class="description"><?php EZP_CS_Utility::_e('Comma separated list'); ?></span></div>
                                </div>
                            </td>
                        </tr>                      
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Analytics Code") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <textarea rows="5" cols="60" name="analytics_code" type="text" ><?php echo EZP_CS_Utility::_he($config->analytics_code); ?></textarea>                        
                                    <div><span class="description"><?php echo EZP_CS_Utility::__('Analytics tracking code') . ' (' . EZP_CS_Utility::__('include') . '&lt;script&gt;&lt;/script&gt;)'; ?></span></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>  

            <div class="postbox" >
                <div class="inside" >
                    <h3><?php EZP_CS_Utility::_e("Filters") ?></h3>
                    <table class="form-table"> 
                        <tr>
                            <th scope="row">
                                <?php echo EZP_CS_Utility::_e("Unfiltered URLs") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <textarea rows="5" cols="60" name="unfiltered_urls" type="text" ><?php echo $config->unfiltered_urls; ?></textarea>
                                    <div><span class="description"><?php EZP_CS_Utility::_e('Each line should contain a relative URL you don\'t want the page shown on (e.g. for http://mysite.com/mypage enter /mypage)'); ?></span></div>
                                </div>
                            </td>
                        </tr>                           
                    </table>
                </div>
            </div>    

            <?php
            submit_button();
            ?>
            <a href="http://easypiewp.com/easy-pie-coming-soon-faq" target="_blank"><?php EZP_CS_Utility::_e('FAQ'); ?></a>
            |
            <a href="http://wordpress.org/support/view/plugin-reviews/easy-pie-coming-soon" target="_blank"><?php echo EZP_CS_Utility::__('Rate'); ?></a>
            |            
            <a href="http://easypiewp.com/donate/" target="_blank"><?php EZP_CS_Utility::_e('Donate') ?></a>
            |
            <a href="http://easypiewp.com/about/" target="_blank"><?php EZP_CS_Utility::_e('Contact') ?></a>
            |
            <a href="<?php echo admin_url() . 'admin.php?page=' . EZP_CS_Constants::$COMING_SOON_PRO_SUBMENU_SLUG ?>">Coming Soon Pro</a>
        </form>
    </div>
</div>

