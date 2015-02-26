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
if (isset($_GET['tab'])) {

    $active_tab = $_GET['tab'];
} else {

    $active_tab = 'display';
}
?>

<script type="text/javascript" src='<?php echo EZP_CS_Utility::$PLUGIN_URL . "/js/page-options-$active_tab-tab.js?" . EZP_CS_Constants::PLUGIN_VERSION; ?>'></script>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>

<div class="wrap">

    <?php screen_icon(EZP_CS_Constants::PLUGIN_SLUG); ?>
    <h2>Easy Pie Coming Soon: <?php EZP_CS_Utility::_e('Template'); ?></h2>
    <?php
    if (isset($_GET['settings-updated'])) {
        echo "<div class='updated'><p>" . EZP_CS_Utility::__('If you have a caching plugin, be sure to clear the cache!') . "</p></div>";
    }
    
    $global = EZP_CS_Global_Entity::get_instance();

    $config = EZP_CS_Config_Entity::get_by_id($global->config_index);

    EZP_CS_Utility::display_admin_notice($config->coming_soon_mode_on);
    ?>
    
    <div id="easypie-cs-options" class="inside">
        <h2 class="nav-tab-wrapper">  
            <a href="?page=<?php echo EZP_CS_Constants::PLUGIN_SLUG . '&tab=display' ?>" class="nav-tab <?php echo $active_tab == 'display' ? 'nav-tab-active' : ''; ?>"><?php EZP_CS_Utility::_e('Display'); ?></a>  
            <a href="?page=<?php echo EZP_CS_Constants::PLUGIN_SLUG . '&tab=content' ?>" class="nav-tab <?php echo $active_tab == 'content' ? 'nav-tab-active' : ''; ?>"><?php EZP_CS_Utility::_e('Content'); ?></a>  
            <a href="?page=<?php echo EZP_CS_Constants::PLUGIN_SLUG . '&tab=preview' ?>" class="nav-tab <?php echo $active_tab == 'preview' ? 'nav-tab-active' : ''; ?>"><?php EZP_CS_Utility::_e('Preview'); ?></a>  
        </h2>
        <form id="easy-pie-cs-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_CS_Constants::PLUGIN_SLUG . '&tab=' . $active_tab); ?>" > 
            <?php
            //  settings_fields(EZP_CS_Constants::MAIN_PAGE_KEY);
            //do_settings_sections(EZP_CS_Constants::MAIN_PAGE_KEY);                        

            ?>      
            <div id='tab-holder'>
                <?php
                if ($active_tab == 'display') {
                    include 'page-options-display-tab.php';
                } else if ($active_tab == 'content') {
                    include 'page-options-content-tab.php';
                } else {
                    include 'page-preview-tab.php';
                }
                                
                if (isset($_POST['ezp-cs-submit-type']) && ($_POST['ezp-cs-submit-type'] == 'preview') ){
                    
                    $redirect_url = '?page=' . EZP_CS_Constants::PLUGIN_SLUG . '&tab=preview';
                        
                    echo '<script>'                    
                        . 'window.location="' . $redirect_url . '";'
                        . '</script>';                                                                 
                }
                
                ?>         
                <!-- after redirect -->
            </div>           

            <input type="hidden" id="ezp-cs-submit-type" name="ezp-cs-submit-type" value="save"/>
            
            <p>
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
                <input style="margin-left:15px" type="submit" name="submit" id="submit" class="button button-primary" value="Save & Preview" onclick="document.getElementById('ezp-cs-submit-type').value = 'preview';debugger;return true;"/>
            </p>                

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

