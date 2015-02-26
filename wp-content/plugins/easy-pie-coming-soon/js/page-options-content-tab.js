/*
 * Code behind for content tab
 */

jQuery(document).ready(function($) {

    var logo_uploader;


    $('#ezp-countdown-due-date').datepicker({ dateFormat: ezp_cs_datepicker_date_format} );
    $('#easy-pie-cs-logo-button').click(function(e) {

        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (logo_uploader) {
            logo_uploader.open();
            return;
        }

        //Extend the wp.media object
        logo_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        logo_uploader.on('select', function() {
            attachment = logo_uploader.state().get('selection').first().toJSON();
            $('#easy-pie-cs-logo-url').val(attachment.url);
            $('#easy-pie-cs-logo-preview').css("display", "block");
            $('#easy-pie-cs-logo-preview').attr("src", attachment.url);
        });

        //Open the uploader dialog
        logo_uploader.open();
    });
})