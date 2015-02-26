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

<style>    
    #easy-pie-cs-advanced h3 { cursor: default; margin-left:5px; margin-top:10px;}
    #easy-pie-cs-advanced table { margin-left:10px;}    
    #easy-pie-cs-quick-config-div { margin-top: 10px; width: 670px; overflow:hidden;}

    .template-image-holder { margin: 10px 10px 15px 10px; }
    .template-image { width:200px; opacity:0.4; border: black 1px solid;}
    .template-name { }
    .template-button { margin-top: 10px!important; }
    #easy-pie-cs-template-cancel-btn { }
    #easy-pie-cs-template-copy-btn { float:right;}    
    #template-dialog {background-color:white; box-shadow: 1px 7px 36px -5px rgba(34,34,34,1); border: #777 1px solid; padding:13px}
    #opacity-slider { margin-left:40px; width: 130px; }
    #opacity-slider .ui-slider-handle { width: 8px; text-align: center; }

    .ezp-cs-radiodiv { margin-bottom:10px;}

    #easy-pie-cs-builtin-background-slider { width: 610px}
    #easy-pie-cs-builtin-background-slider img { height:100px; width:100px; margin-right:10px; margin-top:7px;}
</style>

<!--<script type="text/javascript" src="<?php echo EZP_CS_Utility::$PLUGIN_URL . '/jquery-plugins/simple-modal/jquery.simplemodal.1.4.4.min.js?' . EZP_CS_Constants::PLUGIN_VERSION; ?>"></script>-->

<?php
$action_updated = null;

$global = EZP_CS_Global_Entity::get_instance();

$set_index = $global->active_set_index;

$set = EZP_CS_Set_Entity::get_by_id($set_index);

$display = EZP_CS_Display_Entity::get_by_id($set->display_index);

$config = EZP_CS_Config_Entity::get_by_id($global->config_index);

$error_string = "";


if (isset($_POST['action']) && $_POST['action'] == 'save') {

    check_admin_referer('easy-pie-coming-soon-save-display');

    EZP_CS_Utility::debug('past admin check');

    // Artificially set the bools since they aren't part of the postback
    $display->background_tiling_enabled = "false";

    $error_string = $display->set_post_variables($_POST);

    if ($error_string == "") {

        $action_updated = $display->save();
    }       
}
?>

<?php wp_nonce_field('easy-pie-coming-soon-save-display'); ?>
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


<!--<div class="postbox" style="margin-top:12px;">
    <div class="inside" >
        <h3><?php EZP_CS_Utility::_e("Display Template") ?></h3>
        <button onclick="easyPie.CS.ShowTemplateDialog();
                    return false;"><?php EZP_CS_Utility::_e("Templates"); ?></button>
        <div style="margin-top:4px;"><span class="description"><?php EZP_CS_Utility::_e('Templates give you a starting point for your display settings.'); ?></span></div>
    </div>
</div>-->
<div class="postbox"  style="margin-top:12px;">
    <div class="inside" >
        <h3><?php EZP_CS_Utility::_e("Background") ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row">
<?php echo EZP_CS_Utility::_e("Color") ?>
                </th>
                <td>
                    <div class="compound-setting">  
                        <input name="background_color" class="spectrum-picker" type="text" value="<?php echo $display->background_color; ?>"/>                   
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row">
<?php echo EZP_CS_Utility::_e("Image") ?>
                </th>                
                <td>   
                    <div>
                        <span class="description"><?php echo EZP_CS_Utility::__('Instead of background color use one of these images') . ':'; ?></span>
                    </div>
                    <div class="ezp-cs-radiodiv">
                        <div class="compound-setting">
                            <div id="easy-pie-cs-builtin-background-slider"> 
                                <?php
                                $background_dir = EZP_CS_Utility::$PLUGIN_DIRECTORY . '/images/backgrounds/';

                                $file_paths = glob($background_dir . '*.{jpg,png}', GLOB_BRACE);

                                if ($file_paths != FALSE) {

                                    sort($file_paths);
                                    $image_index = 0;
                                    $build_in_background = false;
                                    foreach ($file_paths as $file_path) {

                                        $image_id = "built-in-bg-image-$image_index";

                                        $file_name = basename($file_path);
                                        $file_url = EZP_CS_Utility::$PLUGIN_URL . "/images/backgrounds/$file_name";


                                        if ($display->background_image_url != $file_url) {
                                            $opacity = 0.3;
                                        } else {
                                            $opacity = 1.0;
                                            $build_in_background = true;
                                        }

                                        echo "<img id='$image_id' src='$file_url' style='opacity:$opacity;cursor: pointer;'/>";

                                        $image_index++;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="ezp-cs-radiodiv">                                            
                        <div>
                            <span class="description"><?php echo EZP_CS_Utility::__('or use your own') . ':'; ?></span>
                        </div>
                        <div class="compound-setting">
                            <input id="easy-pie-cs-background-image-url" name="background_image_url" value="<?php echo $display->background_image_url; ?>" />                            
                            <button id="easy-pie-cs-background-image-button"><?php EZP_CS_Utility::_e("Upload"); ?></button>
                            <img id="easy-pie-cs-background-image-preview" style="display: <?php echo ($build_in_background || $display->background_image_url == '') ? 'none' : 'block' ?> ;width:80px;height:80px;margin-top:8px;" src="<?php echo $display->background_image_url; ?>" />
                        </div>                                                   
                    </div>

                </td>
            </tr>            
            <tr>
                <th scope="row">
<?php echo EZP_CS_Utility::_e("Tile") ?>
                </th>
                <td>
                    <div class="compound-setting">                                            
                        <input type="checkbox" name="background_tiling_enabled" value="true" <?php echo $display->background_tiling_enabled == 'true' ? 'checked' : ''; ?> />
                        <span><?php EZP_CS_Utility::_e("Enabled") ?></span>                        
                    </div>                        
                    <div>
                        <span class="description"><?php echo EZP_CS_Utility::__('Image covers screen when tiling is disabled') . '.'; ?></span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="postbox" >
    <div class="inside" >
        <h3><?php EZP_CS_Utility::_e("Logo") ?></h3>

        <table class="form-table">    
            <tr>
                <th scope="row">
<?php echo EZP_CS_Utility::_e("Width") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class='narrow-input' name="logo_width" type="text" value="<?php echo $display->logo_width; ?>" />
                        <span class="description"><?php echo '*' . EZP_CS_Utility::__('Specify px or %'); ?></span>
                    </div>
                </td>
            </tr>   
            <tr>
                <th scope="row">
<?php echo EZP_CS_Utility::_e("Height") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class='narrow-input' name="logo_height" type="text" value="<?php echo $display->logo_height; ?>" />
                        <span class="description"><?php echo '*' . EZP_CS_Utility::__('Specify px or %'); ?></span>
                    </div>
                </td>
            </tr>   
        </table>
    </div>
</div>


<div class="postbox" >
    <div class="inside" >
        <h3><?php EZP_CS_Utility::_e("Content Box") ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row">
<?php echo EZP_CS_Utility::_e("Opacity") ?>
                </th>
                <td>
                    <div class="compound-setting">                                                    
                        <div style="display:none;"><input class='narrow-input' id="content_box_opacity" name="content_box_opacity" type="text" value="<?php echo $display->content_box_opacity ?>" readonly="true"/>                        </div>                        
                        <div id="opacity-display-value" style="float:left;">hi</div>
                        <div style="padding-top:2px;"><div id="opacity-slider"></div></div>                        
                    </div>

                </td>
            </tr>    
            <tr>
                <th scope="row">
<?php echo EZP_CS_Utility::_e("Color") ?>
                </th>
                <td>
                    <div class="compound-setting">     
                        <input name="content_box_color" class="spectrum-picker" type="text" value="<?php echo $display->content_box_color ?>"/>                   
                    </div>
                </td>
            </tr>  
        </table>
    </div>
</div>


<div class="postbox" >
    <div class="inside" >
        <h3><?php EZP_CS_Utility::_e("Text") ?></h3>
        <table class="form-table">
            <tr>        
<?php EZP_CS_Display_Entity::display_font_field_row('Headline', 'text_headline', $display) ?>
            </tr>
            <tr>
<?php EZP_CS_Display_Entity::display_font_field_row('Description', 'text_description', $display) ?>
            </tr>

            <tr>
<?php EZP_CS_Display_Entity::display_font_field_row('Disclaimer', 'text_disclaimer', $display) ?>
            </tr>
            <tr>
<?php EZP_CS_Display_Entity::display_font_field_row('Footer', 'text_footer', $display) ?>
            </tr>          
        </table>
        <div><span class="description"><?php echo '*' . EZP_CS_Utility::__('Specify px or em for sizes'); ?></span></div>
    </div>
</div>

<div class="postbox" >
    <div class="inside" >

        <h3 ><?php EZP_CS_Utility::_e("Email Button") ?></h3>
        <table class="form-table">

            <tr>
                <th scope="row">
<?php echo EZP_CS_Utility::_e("Width") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class='narrow-input' name="email_button_width" type="text" value="<?php echo $display->email_button_width; ?>" />
                        <span class="description"><?php echo '*' . EZP_CS_Utility::__('Append px or %'); ?></span>
                    </div>
                </td>
            </tr>   
            <tr>
                <th scope="row">
<?php echo EZP_CS_Utility::_e("Height") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input class='narrow-input' name="email_button_height" type="text" value="<?php echo $display->email_button_height; ?>" />
                        <span class="description"><?php echo '*' . EZP_CS_Utility::__('Append px or %'); ?></span>
                    </div>
                </td>
            </tr>  
            <tr>
<?php echo EZP_CS_Display_Entity::display_font_field_row("Font", 'email_button', $display); ?>
            </tr>
            <tr>
                <th scope="row">
<?php echo EZP_CS_Utility::_e("Color") ?>
                </th>
                <td>
                    <div class="compound-setting">                            
                        <input name="email_button_color" class="spectrum-picker" type="text" value="<?php echo $display->email_button_color; ?>"/>
                    </div>
                </td>
            </tr>  
        </table>                       
    </div>
</div>


<div class="postbox" >
    <div class="inside" >
        <h3 style="float:left;" ><span style="font-weight:bold"><?php EZP_CS_Utility::_e('Advanced'); ?><span></h3>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php EZP_CS_Utility::_e("Custom CSS") ?></th><td>
                                <div>
                                    <textarea cols="67" rows="9" id="easy-pie-cs-field-junk" name="css"><?php echo $display->css; ?></textarea>
                                </div>             
                            </td>
                        </tr>
                    </table>

                    <div style="margin-top:4px;"><a target="_blank" href="http://easypiewp.com/coming-soon-plugin-css-tips"><span class="description"><?php EZP_CS_Utility::_e('CSS Customization tips'); ?></span></a></div>
                    </div>
                    </div>