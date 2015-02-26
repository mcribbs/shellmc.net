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

<style lang = "text/css">
    .compound-setting { line-height:20px;
    }
    .narrow-input { width:66px;
    }
    .long-input { width: 345px;
    }
    
    #easy-pie-seedprod-coming-soon-pro, #easy-pie-seedprod-coming-soon-pro p { font-size: 1.2em!important; }
    #easy-pie-seedprod-coming-soon-pro ul { list-style: circle; padding-left: 40px;}
    #easy-pie-seedprod-coming-soon-pro li { line-height: 1.4em;}
</style>

<div class = "wrap">

    <?php screen_icon(EZP_CS_Constants::PLUGIN_SLUG);
    ?>
    <h2>Coming Soon Pro</h2>
    <?php
    $global = EZP_CS_Global_Entity::get_instance();

    $config = EZP_CS_Config_Entity::get_by_id($global->config_index);

    EZP_CS_Utility::display_admin_notice($config->coming_soon_mode_on);
    ?>

    <div id="easypie-cs-options" class="inside">
        <div class="postbox" style="padding:16px;" id="easy-pie-seedprod-coming-soon-pro">
            <div style="text-align:center">                               
                <a target="_blank" href="http://www.shareasale.com/r.cfm?b=576780&u=948111&m=51809&urllink=&afftrack="><img style="width:745px" src="<?php echo EZP_CS_Utility::$PLUGIN_URL . '/images/affiliates/banner-coming-soon-pro.jpg'?>" alt="SeedProd Coming Soon Pro" /></a>
                
                <p>Our friends at SeedProd have created a great plugin: <strong><a target="_blank" href="http://www.shareasale.com/r.cfm?b=576780&u=948111&m=51809&urllink=&afftrack=">Coming Soon Pro!</strong></a></p>
            </div>
            
            <p style="font-weight:bold">Features</p>
            <ul>
                <li><span style="font-weight:bold">Social Follow Icons.</span> Twitter, Facebook, LinkedIn, Google+, YouTube, Flickr, Vimeo, Pinterest, Instagram, Foursquare, Tumblr, RSS</li>
                <li><span style="font-weight:bold">Social Share Icons.</span> Twitter, Google+, LinkedIn, Pinterest, Tumblr</li>
                <li><span style="font-weight:bold">Email Marketing Integrations.</span> MailChimp, Mad Mimi, AWeber, GetResponse, Constant Contact, Campaign Monitor, Gravity Forms</li>
                <li><span style="font-weight:bold">Landing Page Mode.</span> Convert page into a landing page after launch</li>
                <li><strong>Progress Bar.</strong> Let visitors know where you're at</li>
                <li><strong>Shortcode Support.</strong> Use shortcodes right in your page!</li>
                <li><strong>Google Fonts.</strong> Google's 600+ font library is at your disposal!</li>
                <li><strong>Full Screen Slideshows.</strong> Go to the next level with full screen slideshows</li>
                <li><strong>Let users view site</strong></li>
                <ul>
                    <li>by IP</li>
                    <li>by Role</li>
                    <li>using secret link</li>
                </ul>
                </li>
                <li><strong>Allows 3rd party web forms</strong> Use the web forms you've come to love in your page</li>
                <li><strong>...and many more options!</strong></li>
            </ul>
            <p style="text-align:center; font-size:1.7em!important; margin-top:25px">
                <a target="_blank" href="http://www.shareasale.com/r.cfm?b=576780&u=948111&m=51809&urllink=&afftrack=">Check out Coming Soon Pro!</a>
            </p>
            <p style="margin-top:20px;font-size:14px!important">Coming Soon Pro was not created by Easy Pie.</p>
        </div>
    </div>
</div>

