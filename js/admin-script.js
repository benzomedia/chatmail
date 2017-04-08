/**
 * Created by Benzo Media.
 * http://www.benzomedia.com
 * User: Oren Reuveni
 * Date: 22/03/2017
 * Time: 16:24
 */

jQuery(document).ready(function ($) {

    $('.color-field').wpColorPicker();
    add_avatar_selector($)

    if($('#use_admin_email').is(':checked')) {
        $('#email_list').parents('tr').hide();
    }

    $('input[name="ws_chatmail_option[use_admin_email]"]').on('change', function(e){
        if(e.target.value == 1) {
            $('#email_list').parents('tr').slideUp();
        } else {
            $('#email_list').parents('tr').slideDown();
        }
    })
});

function add_avatar_selector($) {
    // Uploading files
    $('#ws-chatmail-upload-avatar').on('click', function( event ){
        event.preventDefault();
        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            // Set the post ID to what we want
            file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
            // Open frame
            file_frame.open();
            return;
        } else {
            // Set the wp.media post id so the uploader grabs the ID we want when initialised
            wp.media.model.settings.post.id = set_to_post_id;
        }
        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select a image to upload',
            button: {
                text: 'Use this image',
            },
            multiple: false	// Set to true to allow multiple files to be selected
        });
        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();
            // Do something with attachment.id and/or attachment.url here
            $( '#ws-chatmail-avatar-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
            $( '#ws-chatmail-avatar' ).val( attachment.id );
            // Restore the main post ID
            wp.media.model.settings.post.id = wp_media_post_id;
        });
        // Finally, open the modal
        file_frame.open();
    });
    // Restore the main ID when the add media button is pressed
    $( 'a.add_media' ).on( 'click', function() {
        wp.media.model.settings.post.id = wp_media_post_id;
    });
}
