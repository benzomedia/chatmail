<?php
/**
 * Created by Benzo Media.
 * http://www.benzomedia.com
 * User: Oren Reuveni
 * Date: 23/03/2017
 * Time: 12:48
 */

//Add HTML
function ws_chatmail_add_html() {
	$options = get_option( 'ws_chatmail_option' );
	$image = wp_get_attachment_url($options['chatmail_avatar']);
	if(!$image) {
		$image = plugin_dir_url( __FILE__ ) . '/images/robot.png';
	}

	?>
	<script>
        var ws_chatmail_color = '<?php echo $options['chatmail_color']; ?>';
	</script>
	<div class="ws-chatmail-chat-window" id="ws-chatmail-chat-window">
		<div class="ws-chatmail-chat-window-header">
			<div class="ws-chatmail-chat-window-avatar"
			     style="background:url(<?php echo $image; ?>)center center / cover;"></div>
			<div class="ws-chatmail-chat-window-text">
				<h3 class="ws-chatmail-admin-name"><?php echo $options['chatmail_name']; ?></h3>
				<p class="ws-chatmail-admin-message"><?php echo $options['chatmail_message']; ?></p>
			</div>
		</div>
		<div class="ws-chatmail-window-body">
			<form id="ws-chatmail-window-form">
				<textarea class="ws-chatmail-message" placeholder="Type your message..." rows="3"></textarea>
                <button id="ws-chatmail-form-submit" class="ws-chatmail-window-form-submit" type="submit"><span>Send message</span>
					<img src="<?php echo plugin_dir_url( __FILE__ ) . '/images/bubble-send.png' ?>"></button>
			</form>
			<div class="ws-chatmail-bubble-container ws-admin-response">
				<div class="ws-chatmail-admin-bubble">
					<div class="ws-arrow"></div>
					<div class="ws-chatmail-chat-avatar" style="background:url(<?php echo $image; ?>)center center / cover;"></div>
					<p>Thanks for reaching out! We
						will get back to you really soon
						via email.</p>
				</div>
			</div>
			<div class="ws-chatmail-bubble-container ws-admin-response">
				<div class="ws-chatmail-admin-bubble ws-form-bubble">
					<p>Leave us your email so we can get back to you.</p>
					<form class="ws-chatmail-email-form"  id="ws-chatmail-email-form">
						<input type="text" required placeholder="Your email" >
						<button type="submit" style="background-color:<?php echo $options['chatmail_color']; ?>;"><img src="<?php echo plugin_dir_url( __FILE__ ) . '/images/bubble-send-white.png' ?>"></button>
					</form>
				</div>
			</div>
            <div class="ws-chatmail-bubble-container ws-admin-error-response">
                <div class="ws-chatmail-admin-bubble">
                    <div class="ws-arrow"></div>
                    <div class="ws-chatmail-chat-avatar" style="background:url(<?php echo $image; ?>)center center / cover;"></div>
                    <p>Oops, something went wrong. Try us again later.</p>
                </div>
            </div>
            <div class="ws-chatmail-bubble-container ws-admin-success-response">
                <div class="ws-chatmail-admin-bubble">
                    <div class="ws-arrow"></div>
                    <div class="ws-chatmail-chat-avatar" style="background:url(<?php echo $image; ?>)center center / cover;"></div>
                    <p>Success! We got your message and will get back to you soon.</p>
                </div>
            </div>
		</div>
		<div id="ws-chatmail-credits" class="ws-chatmail-credits">Free plugin by <a
				href="https://chatmail.webscopeapp.com" target="_blank"><img class="ws-chatmail-logo"
		                                                                    src="<?php echo plugin_dir_url( __FILE__ ) . '/images/ws-logo.png'; ?>"></a>
		</div><!-- credits -->
	</div>
	<button class="ws-chatmail-button" id="ws-chatmail-button"
	        style="background-color:<?php echo $options['chatmail_color']; ?>"></button>
	<?php
}

add_action( 'wp_footer', 'ws_chatmail_add_html' );

function ws_chatmail_admin_add_html() {
	$my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );
	?>
	<script>
        var file_frame;
        var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
        var wp_media_post_id = null; // Store the old id
	</script>
	<?php
}

add_action( 'admin_footer', 'ws_chatmail_admin_add_html' );
