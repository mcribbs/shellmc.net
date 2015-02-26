<?php
$global = EZP_CS_Global_Entity::get_instance();

$set_index = $global->active_set_index;

$set = EZP_CS_Set_Entity::get_by_id($set_index);

$display = EZP_CS_Display_Entity::get_by_id($set->display_index);

$content = EZP_CS_Content_Entity::get_by_id($set->content_index);

$config = EZP_CS_Config_Entity::get_by_id($global->config_index);

$error_display = 'none';
$error_text = '';

$js_thank_you = "var thankYouDisplayed=false;";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subscriber = new EZP_CS_Subscriber_Entity();

    $error_text = EZP_CS_Query_Utility::add_new_subscriber($_POST['ezp_cs_name'], $_POST['ezp_cs_email']);

    if ($error_text == null) {
        $js_thank_you = "var thankYouDisplayed=true;";
        $initial_section_display = 'none';
        $thank_you_section_display = 'block';
    } else {

        $error_display = 'block';
        $initial_section_display = 'block';
        $thank_you_section_display = 'none';
    }
} else {

    $initial_section_display = 'block';
    $thank_you_section_display = 'none';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <!-- Title here -->
        <title><?php echo $content->title; ?></title>

        <?php
        echo "

        <meta name='description' content='$config->meta_description'>

        <meta name='keywords' content='$config->meta_keywords'>

        <link rel='author' href='$config->author_url' />"
        ?>        

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!--Fonts-->
        <!-- SEtting: {font-link-list} - list of all fonts referenced in the template -->
        <!-- <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>-->

        <!-- Styles -->

        <!-- Bootstrap CSS -->
<!--        <link href="<?php //echo $page_url . '/css/bootstrap.min.css'   ?>" rel="stylesheet">-->
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font awesome CSS -->
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="<?php echo $page_url . '/css/style.css?' . EZP_CS_Constants::PLUGIN_VERSION ?>" rel="stylesheet">

        <style type="text/css">
<?php
list($bgr, $bgg, $bgb) = sscanf($display->content_box_color, "#%02x%02x%02x");

$background_color = "background-color: $display->background_color;";

if (trim($display->background_image_url) != '') {

    $background_color = "";
}

if ($display->background_tiling_enabled == 'true') {

    $background_size_styles = "body { background-image: url('$display->background_image_url'); $background_color }";
} else {

    $background_size_styles = "{ margin: 0; padding: 0; }
                                            body {                                                 
                                                 background: url('$display->background_image_url') no-repeat center center fixed;
                                                $background_color                                               
                                                -webkit-background-size: cover;
                                                -moz-background-size: cover;
                                                -o-background-size: cover;
                                                background-size: cover;
                                            }";
}

$email_display = EZP_CS_Render_Utility::get_display($config->collect_email, "block");
$name_display = EZP_CS_Render_Utility::get_display($config->collect_name, "inline-block");

$logo_display = EZP_CS_Render_Utility::get_display($content->logo_url, "inline");

$background_image_url = $display->builtin_background_image == "" ? $display->background_image_url : EZP_CS_Utility::$PLUGIN_URL . "/images/backgrounds/" . $display->builtin_background_image;

if (trim($display->logo_width) == "") {

    $logo_width_adjustment = "";
} else {

    $logo_width_adjustment = "width: $display->logo_width;";
}

if (trim($display->logo_height) == "") {

    $logo_height_adjustment = "";
} else {

    $logo_height_adjustment = "height: $display->logo_height;";
}

if(trim($content->countdown_due_date) == "") {
    
    $countdown_display = "none";
} else {
    
    $countdown_display = "block";
}

echo "
            
            $background_size_styles            
            #content-area { background:rgba($bgr, $bgg, $bgb, $display->content_box_opacity); }           
            #headline, #thank-you-headline { {$display->get_font_styling('text_headline')}  }            
            #description, #thank-you-text { {$display->get_font_styling('text_description')}  }
            #disclaimer { {$display->get_font_styling('text_disclaimer')}  }
            #footer { {$display->get_font_styling('text_footer')}  }
                        
            #logo { display:$logo_display; $logo_height_adjustment; $logo_width_adjustment;  }
            #email-submit-button { margin-left:3px; {$display->get_font_styling('email_button')}; background-color: $display->email_button_color; height: $display->email_button_height; width: $display->email_button_width; }
            
            #email-collection-box { display:$email_display; }
            #name-form-group { display:$name_display; }
            #email-form-group { margin-left:auto;margin-right:auto;}
            #email-form-group, #name-form-group { width: 180px; }
            /* #name-input, #email-input { width: 180px; } */
                
            #initial-section { display:$initial_section_display; }
            #thank-you-section { display: $thank_you_section_display; }
            #error-block { display: $error_display; color:red; margin-top:5px; }
            #countdown { display: $countdown_display; }

              
            /* Custom CSS */
            $display->css
            ";
?>
        </style>

        <script type="text/javascript">
<?php echo $js_thank_you; ?>
            //RSR TODO: Set up variable for clock 
            clockEndDate = "<?php echo $content->countdown_due_date; ?>"            
        </script>

        <!-- Analytics Code -->
        <?php echo $config->analytics_code; ?>
    </head>

    <body>

        <div class="container">	

            <!-- Subscribe Starts -->
            <div id="content-area" class="text-center">

                <img id="logo" src="<?php echo $content->logo_url ?>"/> 


                <div id="initial-section">
                    <header class="text-center">
                        <!-- Setting: {{headline}} -->
                        <h1 id="headline"><?php echo $content->headline ?></h1>

                        <!-- Setting: {{description}} -->                   
                        <p id="description"><?php echo $content->description; ?></p>

                        <p id="custom-html" style="display:"><!--Setting: {{custom-html}} --></p>
                    </header>

                    <div id="countdown"></div>

                    <form id="email-collection-box" name="email-collection-box" class="form-inline" role="form" action="" method="post">

                        <!-- Setting: {{name-collection-on}}-->
                        <div id="name-form-group" class="form-group">
                            <label class="sr-only" for="ezp_cs_name"><?php EZP_CS_Utility::_e("Name"); ?></label>
                            <!-- Setting: {{name-placeholder}}-->
                            <input id="name-input" name="ezp_cs_name" type="text" class="form-control" placeholder="<?php echo $content->name_placeholder_text; ?>"/>
                        </div>
                        <div id="email-form-group" class="form-group">
                            <label class="sr-only" for="ezp_cs_email"><?php EZP_CS_Utility::_e("Email"); ?></label>                            
                            <input id="email-input" name="ezp_cs_email" type="email" class="form-control" placeholder="<?php echo $content->email_placeholder_text; ?>"/>
                        </div>

                        <button id="email-submit-button" form="email-collection-box" type="submit" class="btn btn-danger"><?php echo $content->email_button_text; ?></button>                        
                        <div id="error-block"><?php
                            if ($error_text != null) {
                                echo $error_text;
                            }
                            ?></div>
                        <p id="disclaimer"><?php echo $content->disclaimer; ?></p>
                    </form>
                </div>
                <div id="thank-you-section">
                    <header class="text-center">
                        <!-- Setting: {{thank-you-headline}} -->
                        <h1 id="thank-you-headline"><?php echo $content->thank_you_headline; ?></h1>

                        <!-- Setting: {{thank-you-text}} -->                   
                        <p id="thank-you-text"><?php echo $content->thank_you_description; ?></p>

                        <p id="custom-html"><!--Setting: {{custom-html}} --></p>
                    </header>                    
                </div>

            </div>

            <!-- Social Networks -->
            <div id="social" class="text-center">
                <a target="_blank" href="<?php echo $config->facebook_url ?>" class="br-blue" style="display:<?php echo EZP_CS_Render_Utility::get_display($config->facebook_url, "inline-block"); ?>"><i class="fa fa-facebook"></i></a>
                <a target="_blank" href="<?php echo $config->twitter_url ?>" class="br-lblue" style="display:<?php echo EZP_CS_Render_Utility::get_display($config->twitter_url, "inline-block"); ?>"><i class="fa fa-twitter"></i></a>
                <a target="_blank" href="<?php echo $config->google_plus_url ?>" class="br-orange" style="display:<?php echo EZP_CS_Render_Utility::get_display($config->google_plus_url, "inline-block"); ?>"><i class="fa fa-google-plus"></i></a>
            </div>


            <!-- Footer -->
            <footer class="text-center">
                <p id="footer"><?php echo $content->footer; ?> </p> 
            </footer>          

        </div>

        <!-- Javascript files -->
        <!-- jQuery -->

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <!-- Countdown Plugin-->
        <script src="<?php echo $page_url . '/js/jquery.countdown.min.js' ?>"></script>
        <!-- Slider backgrounds -->
        <!--<script src="js/jquery.vegas.min.js"></script>-->
        <!-- Respond JS for IE8 -->
        <script src="<?php echo $page_url . '/js/respond.min.js?' . EZP_CS_Constants::PLUGIN_VERSION ?>"></script>
        <!-- HTML5 Support for IE -->
        <script src="<?php echo $page_url . '/js/html5shiv.js?' . EZP_CS_Constants::PLUGIN_VERSION ?>"></script>
        <!-- Custom JS -->
        <script src="<?php echo $page_url . '/js/custom.js?' . EZP_CS_Constants::PLUGIN_VERSION ?>"></script>

    </body>	
</html>
