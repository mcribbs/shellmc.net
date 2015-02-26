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

    $active_tab = 'list';
}
?>

<script type="text/javascript" src='<?php echo EZP_CS_Utility::$PLUGIN_URL . "/js/page-subscribers-$active_tab-tab.js?" . EZP_CS_Constants::PLUGIN_VERSION; ?>'></script>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>

<div class="wrap">

    <?php screen_icon(EZP_CS_Constants::PLUGIN_SLUG); ?>
    <h2>Easy Pie Coming Soon: <?php EZP_CS_Utility::_e('Subscriber Management'); ?></h2>
    <?php
    
    $global = EZP_CS_Global_Entity::get_instance();

    $config = EZP_CS_Config_Entity::get_by_id($global->config_index);

    EZP_CS_Utility::display_admin_notice($config->coming_soon_mode_on);
    ?>
    
    <div id="easypie-cs-options" class="inside">
        <h2 class="nav-tab-wrapper">
            <a href="?page=<?php echo EZP_CS_Constants::$SUBSCRIBERS_SUBMENU_SLUG . '&tab=list' ?>" class="nav-tab <?php echo $active_tab == 'list' ? 'nav-tab-active' : ''; ?>"><?php EZP_CS_Utility::_e('Subscribers'); ?></a>  
            <a href="?page=<?php echo EZP_CS_Constants::$SUBSCRIBERS_SUBMENU_SLUG . '&tab=newsletter' ?>" class="nav-tab <?php echo $active_tab == 'newsletter' ? 'nav-tab-active' : ''; ?>"><?php EZP_CS_Utility::_e('Create Newsletter'); ?></a>  
        </h2>
        <form id="easy-pie-cs-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_CS_Constants::$SUBSCRIBERS_SUBMENU_SLUG . '&tab=' . $active_tab); ?>" > 
            <?php
            //  settings_fields(EZP_CS_Constants::MAIN_PAGE_KEY);
            //do_settings_sections(EZP_CS_Constants::MAIN_PAGE_KEY);                        

            ?>      
            <div id='tab-holder'>
                <?php
                if ($active_tab == 'list') {
                    include 'page-subscribers-list-tab.php';
                } else {
                    include 'page-subscribers-newsletter-tab.php';
                }
                ?>         
            </div>             

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

