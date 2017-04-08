<?php

class MySettingsPage {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	public function ws_add_error($message, $name) {
		add_settings_error(
			$name,
			esc_attr( 'settings_updated' ),
			$message,
			'error'
		);
    }

    public function isEmail($email) {
	    $regex = '/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/';
        return preg_match($regex, $email);
    }

	private $defaultOptions;
	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_ws_chatmail_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );

		$this->defaultOptions = [
		    'chatmail_name'=> 'Admin',
            'chatmail_avatar' => array('id'=>null, 'guid'=> plugin_dir_url( __FILE__ ) . '/images/robot.png' ),
		    'chatmail_message'=> "Hi, let us know what you think. We'd love to hear from you...",
		    'chatmail_color'=> '#59C4F8',
            'use_admin_email' => 1
        ];
	}

	/**
	 * Add options page
	 */
	public function add_ws_chatmail_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			'Webscope ChatMail',
			'manage_options',
			'ws-chatmail-setting-admin',
			array( $this, 'create_ws_chatmail_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_ws_chatmail_admin_page() {
		// Set class property
		$this->options = get_option( 'ws_chatmail_option' );
		?>
        <div class="wrap">
            <h1>Webscope Chatmail</h1>
            <form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'ws_chatmail_option_group' );
				do_settings_sections( 'ws-chatmail-setting-admin' );
				submit_button();
				?>
            </form>
            <br/>
            <div>
                <a target="_blank" href="https://webscopeapp.com/?utm_source=chatmail_plugin&utm_medium=settings_ad">
                    <img src="https://s3-eu-west-1.amazonaws.com/webscopeapp/banner.png"
                         style="max-width:700px;width:100%;"/>
                </a>
            </div>
        </div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'ws_chatmail_option_group', // Option group
			'ws_chatmail_option', // Option name
			array( $this, 'ws_chatmail_sanitize' ) // Sanitize
		);

		add_settings_section(
			'chatmail_settings', // ID
			'', // Title
			array( $this, 'print_section_info' ), // Callback
			'ws-chatmail-setting-admin' // Page
		);

		add_settings_field(
			'chatmail_name',
			'Admin name',
			array( $this, 'chatmail_name_callback' ),
			'ws-chatmail-setting-admin',
			'chatmail_settings'
		);

		add_settings_field(
			'avatar_selector', // ID
			'Admin avatar:', // Title
			array( $this, 'chatmail_avatar_selector_callback' ), // Callback
			'ws-chatmail-setting-admin', // Page
			'chatmail_settings' // Section
		);


		add_settings_field(
			'chatmail_message', // ID
			'ChatMail Initial Message', // Title
			array( $this, 'chatmail_message_callback' ), // Callback
			'ws-chatmail-setting-admin', // Page
			'chatmail_settings' // Section
		);

		add_settings_field(
			'chatmail_color',
			'ChatMail Color',
			array( $this, 'chatmail_color_callback' ), //Callback
			'ws-chatmail-setting-admin', // Page
			'chatmail_settings' // Section
		);

		add_settings_field(
			'use_admin_email', // ID
			'Email recipient:', // Title
			array( $this, 'use_admin_email_callback' ), // Callback
			'ws-chatmail-setting-admin', // Page
			'chatmail_settings' // Section
		);

		add_settings_field(
			'email_list', // ID
			'Custom Email List:', // Title
			array( $this, 'email_list_callback' ), // Callback
			'ws-chatmail-setting-admin', // Page
			'chatmail_settings' // Section
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function ws_chatmail_sanitize( $input ) {
		$output = array();
        //validate and sanitize avatar
		if ( isset( $input['chatmail_avatar'] ) && $input['chatmail_avatar'] != "" ) {
			$output['chatmail_avatar'] = sanitize_text_field( $input['chatmail_avatar'] );
		} else {
			$output['chatmail_avatar'] = $this->defaultOptions['chatmail_avatar'];
        }
        //validate and use admin email
		if ( isset( $input['use_admin_email'] ) ) {
			$output['use_admin_email'] = sanitize_text_field( $input['use_admin_email'] );
		} else {
			$output['use_admin_email'] = $this->defaultOptions['use_admin_email'];
        }
        //sanitize email_list
		if ( isset( $input['email_list'] ) ) {
			$output['email_list'] = sanitize_text_field( $input['email_list'] );
		}
		//In case email list is empty and admin email is not selected
		if(!$output['use_admin_email'] && $output['email_list'] === "" ) {
			$output['use_admin_email'] = $this->defaultOptions['use_admin_email'];
			$message = __( 'Email list cannot be empty', 'webscope_chatmail' );
			$this->ws_add_error($message, 'email_list');
        }

        //In case email list doesn't seperate into neat emails
		if ( isset( $input['email_list'] ) && $input['email_list'] !== "") {
		    $emails = explode( ', ', $input['email_list'] );
		    foreach($emails AS $email) {
		        if(!$this->isEmail($email)){
			        $message = __( 'Email list cannot be empty', 'webscope_chatmail' );
			        $this->ws_add_error($message, 'email_list');
                }
            }
        }

		if ( isset( $input['chatmail_name'] ) && $input['chatmail_name'] != "" ) {
			$output['chatmail_name'] = sanitize_text_field( $input['chatmail_name'] );
		} else {
			$output['chatmail_name'] = $this->defaultOptions['chatmail_name'];
			$message = __( 'Name can not be empty', 'webscope_chatmail' );
            $this->ws_add_error($message, 'chatmail_name');
		}

		if ( isset( $input['chatmail_message'] ) && $input['chatmail_message'] != "" ) {
			$output['chatmail_message'] = sanitize_text_field( $input['chatmail_message'] );
		} else {
			$output['chatmail_message'] = $this->defaultOptions['chatmail_message'];
			$message = __( 'Message can not be empty', 'webscope_chatmail' );
			$this->ws_add_error($message, 'chatmail_message');
		}


		if ( isset( $input['chatmail_color'] ) && $input['chatmail_color'] != "" ) {
			$output['chatmail_color'] = sanitize_text_field( $input['chatmail_color'] );
		} else {
			$output['chatmail_color'] = $this->defaultOptions['chatmail_color'];
			$message = __( 'Color can not be empty', 'webscope_chatmail' );
			$this->ws_add_error($message, 'chatmail_color');

		}


		return apply_filters( 'ws_chatmail_sanitize', $output, $input );
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		print 'Webscope chatmail is a simple chat interface that encourages your visitors to send you an email.';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function use_admin_email_callback() {

		$html = '<fieldset><label><input type="radio" id="use_admin_email" name="ws_chatmail_option[use_admin_email]" value="1"' . checked( 1, $this->options['use_admin_email'], false ) . '/>';
		$html .= '<span for="use_admin_email">Use Admin Email</span></label><br/>';
		$html .= '<label><input type="radio" id="use_admin_email" name="ws_chatmail_option[use_admin_email]" value="0"' . checked( 1, ! $this->options['use_admin_email'], false ) . '/>';
		$html .= '<span for="use_admin_email">Use Custom Email List</span></label></fieldset>';

		echo $html;
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function email_list_callback() {
	    ?>
        <p>List of email addresses seperated by a ", "</p>
        <?php
		printf(
			'<textarea id="email_list" name="ws_chatmail_option[email_list]" cols="50" rows="6">%s</textarea>',
			isset( $this->options['email_list'] ) ? esc_attr( $this->options['email_list'] ) : ''
		);
	}

	function chatmail_avatar_selector_callback() {
		wp_enqueue_media();
		$image = wp_get_attachment_url($this->options['chatmail_avatar']);
		if(!$image) {
		    $image = plugin_dir_url( __FILE__ ) . '/images/robot.png';
        }


		?>
        <div class='ws-chatmail-avatar-preview-wrapper'>
            <img id='ws-chatmail-avatar-preview' src='<?php echo $image ? $image : ""; ?>' width='100'
                 height='100'
                 style='max-height: 100px; width: 100px;'>
        </div>
        <input id="ws-chatmail-upload-avatar" type="button" class="button" value="<?php _e( 'Upload avatar' ); ?>"/>
        <input type='hidden' id='ws-chatmail-avatar' name='ws_chatmail_option[chatmail_avatar]'
               value='<?php echo $this->options['chatmail_avatar'] ?>'><?php
	}


	/**
	 * Get the settings option array and print one of its values
	 */
	public function chatmail_name_callback() {
		printf(
			'<input type="text" id="name" name="ws_chatmail_option[chatmail_name]" value="%s" />',
			isset( $this->options['chatmail_name'] ) ? esc_attr( $this->options['chatmail_name'] ) : ''
		);
	}


	/**
	 * Get the settings option array and print one of its values
	 */
	public function chatmail_message_callback() {
		printf(
			'<textarea id="message" name="ws_chatmail_option[chatmail_message]" cols="50" rows="6">%s</textarea>',
			isset( $this->options['chatmail_message'] ) ? esc_attr( $this->options['chatmail_message'] ) : ''
		);
	}

	public function chatmail_color_callback() {
		printf(
			'<input type="text" id="color" name="ws_chatmail_option[chatmail_color]" class="color-field" value="%s" />',
			isset( $this->options['chatmail_color'] ) ? esc_attr( $this->options['chatmail_color'] ) : ''
		);
	}
}

if ( is_admin() ) {
	$my_settings_page = new MySettingsPage();
}