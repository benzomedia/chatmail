<?php
/**
 * @package Chat_Bot
 * @version 1.0
 */
/*
Plugin Name: ChatMail by Webscope
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: A chat bot lookalike that makes it easy for users to contact the website owner
Author: Webscope
Version: 1.0
Author URI: https://webscopeapp.com
*/

include( 'chatmail-admin.php' );
include( 'chatmail-templates.php' );


//Activation
register_activation_hook( __FILE__, 'ws_chatmail_on_activation' );

function ws_chatmail_on_activation(){
	do_action( 'ws_chatmail_activation' );
}

add_action( 'ws_chatmail_activation', 'ws_chatmail_default_options' );

//Set default values
function ws_chatmail_default_options(){
	$default = array(
		'chatmail_name'=> 'Admin',
		'chatmail_avatar' => null,
		'chatmail_message'=> "Hi, let us know what you think. We'd love to hear from you...",
		'chatmail_color'=> '#59C4F8',
		'use_admin_email' => 1
	);
	if(!get_option('ws_chatmail_option')){
		update_option( 'ws_chatmail_option', $default );
	}
}




//Add Scripts
add_action( 'wp_enqueue_scripts', 'ws_chatmail_enqueue_scripts' );
function ws_chatmail_enqueue_scripts() {
	wp_enqueue_script( 'ws-chatmail-script', plugin_dir_url( __FILE__ ) . '/js/script.js', array( 'jquery' ), null, true );
	wp_localize_script( 'ws-chatmail-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_style( 'ws-chatmail', plugin_dir_url( __FILE__ ) . '/css/style.css' );
}

//Add Scripts
add_action( 'admin_enqueue_scripts', 'ws_chatmail_admin_enqueue_scripts' );
function ws_chatmail_admin_enqueue_scripts() {

	if ( is_admin() ) {

		// Add the color picker css file
		wp_enqueue_style( 'wp-color-picker' );

		// Add custom script
		wp_enqueue_script( 'ws-chatmail-admin-script', plugin_dir_url( __FILE__ ) . '/js/admin-script.js', array(
			'jquery',
			'wp-color-picker'
		), false, true );

	}
}


add_action( 'wp_ajax_ws_chatmail_send_email', 'ws_chatmail_send_email_callback' );
add_action( 'wp_ajax_nopriv_ws_chatmail_send_email', 'ws_chatmail_send_email_callback' );

//Send email
function ws_chatmail_send_email_callback() {
	$email     = $_POST['email'];
	$body      = $_POST['message'];
	$options   = get_option( 'ws_chatmail_option' );
	$blog_name = get_option( 'blogname' );

	$message = includeTemplate( $email, $body, $blog_name);

	$to = getEmails($options);


	$subject = 'New Message from ' . $blog_name;
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	if ( wp_mail( $to, $subject, $message, $headers ) ) {
		echo json_encode( [ "error" => false ] );
	} else {
		echo json_encode( [ "error" => true, "message" => "Could not send email" ] );
	}

	wp_die();
}


function includeTemplate( $email, $body, $blog_name) {
	include 'email-template.php';
	return $message;
}

function getEmails($options) {
	if ( $options['use_admin_email'] ) {
		return get_option( 'admin_email' );
	}

	$emailArray = explode(',', $options['email_list']);
	for($i = 0; $i < count($emailArray); $i++) {
		$emailArray[$i] = str_replace(" ","", $emailArray[$i]);
	}

	return ($emailArray);
}

add_action( 'wp_mail_failed', 'log_email_failure' );
function log_email_failure( $error ) {
	write_log( $error );
}

if ( ! function_exists( 'write_log' ) ) {
	function write_log( $log ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}
