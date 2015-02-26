/*
 Easy Pie Coming Soon Plugin
 Copyright (C) 2013, Synthetic Thought LLC
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

easyPie = {};
easyPie.CS = {};



easyPie.CS.toggleAdvancedBox = function() {

    jQuery('#easy-pie-cs-advanced').toggle();
    easyPie.CS.setCookie("advancedDisplay", jQuery("#easy-pie-cs-advanced").css("display"));
}

easyPie.CS.setCookie = function setCookie(c_name, value, exdays) {
    
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = c_name + "=" + c_value;
}

easyPie.CS.getCookie = function(c_name) {
    var c_value = document.cookie;
    var c_start = c_value.indexOf(" " + c_name + "=");
    if (c_start == -1)
    {
        c_start = c_value.indexOf(c_name + "=");
    }
    if (c_start == -1)
    {
        c_value = null;
    }
    else
    {
        c_start = c_value.indexOf("=", c_start) + 1;
        var c_end = c_value.indexOf(";", c_start);
        
        if (c_end == -1)
        {
            c_end = c_value.length;
        }
        c_value = unescape(c_value.substring(c_start, c_end));
    }
    return c_value;
};

easyPie.CS.CopyTemplate = function(text) {
    
    shouldCopy = confirm(text);
    
    if(shouldCopy) {
        alert("copying data attached to " + easyPie.CS.selectedTemplateId);
        var templateKey = jQuery('#' + easyPie.CS.selectedTemplateId).attr('template_key');
        jQuery.post(ajaxurl, { action: 'EZP_CS_copy_template', template_key: templateKey },  function() { window.location = window.location.href; } )
        jQuery.modal.close();
    } else {
        // They don't want to copy it so keep the modal up
        
    }    
};

easyPie.CS.selectedTemplateId = "";

easyPie.CS.selectTemplate = function(imageId) {
    
    var selector = "#" + imageId;

    if((easyPie.CS.selectedTemplateId != "") && (easyPie.CS.selectedTemplateId != imageId)) {
        
        var oldSelector = "#" + easyPie.CS.selectedTemplateId;
        
        jQuery(oldSelector).animate({ opacity:.3 }, {queue: false});           
    }
    
    jQuery(selector).animate({ opacity:1 }, {duration: 200, queue: false});       

    
    easyPie.CS.selectedTemplateId = imageId;
};

easyPie.CS.swappedTbRemove = false;

easyPie.CS.ShowTemplateDialog = function() {
    //var url = '#TB_inline?width=650&height=350&inlineId=dup-dlg-quick-path';
    //how to use thickbox http://codex.wordpress.org/ThickBox
    
//    if(!easyPie.CS.swappedTbRemove) {
//        var old_tb_remove = tb_remove;
//
//        var tb_remove = function() {
//            old_tb_remove(); // calls the tb_remove() of the Thickbox plugin
//            alert('ohai');
//        };
//        
//        easyPie.CS.swappedTbRemove = true;
//    }
//
//    var url = '#TB_inline?width=700&height=384&inlineId=template-dialog';
//    tb_show("<?php EZP_CS_Utility::_e('Quick Template'); ?>", url); // to do a link include class='thickbox'

    jQuery("#template-dialog").modal({overlayClose:true, overlayCss:'background-color:black;'});
    easyPie.CS.selectTemplate("image-0");
};

easyPie.CS.selectedBuiltInBackgroundId = "";

easyPie.CS.selectBuiltInBackground = function(element) {
    
    jQ = jQuery;
        
    var src = jQ(element).attr("src");
    var imageId = jQ(element).attr("id");

    jQ("#easy-pie-cs-background-image-url").val(src);
    jQ('#easy-pie-cs-background-image-preview').css("display", "none");    
    
    if((easyPie.CS.selectedBuiltInBackgroundId != "") && (easyPie.CS.selectedBuiltInBackgroundId != imageId)) {
        
        var oldSelector = "#" + easyPie.CS.selectedBuiltInBackgroundId;
        
        jQ(oldSelector).animate({ opacity:.4 }, {queue: false});           
    }
    
    easyPie.CS.selectedBuiltInBackgroundId = imageId;
    jQ(element).animate({ opacity:1 }, {duration: 200, queue: false});       
}

jQuery(document).ready(function($) {

//   $("a[href='admin.php?page=easy-pie-coming-soon-view']").click(function() { alert('hi'); });

   $("#easy-pie-cs-builtin-background-slider img").click(function() { easyPie.CS.selectBuiltInBackground(this); });
   
   $("#easy-pie-cs-builtin-background-slider img").each(function(index) {
       
       if($(this).css("opacity") == 1) {
           
            easyPie.CS.selectedBuiltInBackgroundId = $(this).attr("id");
       }
   })

    // RSR TODO: Set start slide
    
    //-- Opacity slider
    var opacity = $("#content_box_opacity").val();

    var updateSlide= function(event, ui) { 
                                                var displayValue = ui.value * 100 + "%";                
                                                
                                                $("#content_box_opacity").val(ui.value);// var value = $("#slider").slider("option","value");                                                
                                                $("#opacity-display-value").html(displayValue);
                                                
                                               // $("#opacity-slider").find(".ui-slider-handle").text(op); 
                                           };
                                           
    $("#opacity-slider").slider(
            { 
                min: 0, max: 1, step: 0.1, value: opacity,
                                                                  
                slide: updateSlide,
                change: updateSlide
            });
    
    
    $("#opacity-display-value").html(opacity  * 100 + "%");

    
    (function($) {

        $(".spectrum-picker").spectrum({
            preferredFormat: "hex",
            show: function(color) {
                console.log(color.toHexString());
            },
            change: function(color) {
                $(this).val(color);
                console.log(color.toHexString());
            },
            showInput: true,
            theme: "sp-light"
        });

        $('#easypie-cs-options .pages-selection input[type="checkbox"]').change(function() {
            var values = [];
            var div = $(this).parent().parent();
            div.find('input:checked').each(function(i, e) {
                values.push($(e).val());
            });
            div.children(":first").val(values.join());
        });
    })(jQuery);

    // New Media uploader logic
    var custom_uploader;

    $('#easy-pie-cs-background-image-button').click(function(e) {

        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#easy-pie-cs-background-image-url').val(attachment.url);
            $('#easy-pie-cs-background-image-preview').css("display", "block");
            $('#easy-pie-cs-background-image-preview').attr("src", attachment.url);
            $("#easy-pie-cs-builtin-background-slider img").css("opacity", 0.4);
        });

        //Open the uploader dialog
        custom_uploader.open();
    });

    var advancedDisplay = easyPie.CS.getCookie("advancedDisplay");

    if (advancedDisplay != null) {

        $("#easy-pie-cs-advanced").css("display", advancedDisplay);
    }            
});