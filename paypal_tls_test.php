<?php
/**
 * @package Paypal TLS Test
 * @version 1.6
 */
/*
Plugin Name: PayPal TLS Test
Plugin URI: http://eventespresso.com
Description: This plugin verifies your server and WordPress setup will be able to communicate with PayPal once they implement the security changes listed on https://devblog.paypal.com/upcoming-security-changes-notice/
Author: Event Espresso (Mike Nelson)
Version: 1.0
Author URI: http://eventespresso.com
*/

/**
	 * Pings the tls test server for paypal
	 * @return array|WP_Remote_Requests_Response|WP_Error
	 */
function ee_paypal_test_meets_new_tls_requirements() {
	//this forces a problem because not httpS
//		$result = wp_remote_get( 'http://tlstest.paypal.com', array( 'httpversion' => '1.1' );
	//this forces a problem because using http 1.0
//		$result = wp_remote_get( 'https://tlstest.paypal.com', array( 'httpversion' => '1.0') );
	//this forces a problem because forcing an old version of tls
//		add_action( 
//			'http_api_curl', 
//			function( $handle ) {
//				curl_setopt( $handle, CURLOPT_SSLVERSION, 3 );
//			},
//			10,
//			1 
//		);
//		$result = wp_remote_get( 'https://tlstest.paypal.com', array( 'httpversion' => '1.1' ) );
	//this should be ok
	$result = wp_remote_get( 'https://tlstest.paypal.com' );
	if( is_wp_error( $result ) ) {
		$success = false;
		$message = sprintf(
			__( 'Problem communicating with PayPal: %1$s', 'event_espresso' ),
			$result->get_error_message()
		);
	} else {
		$response_body = wp_remote_retrieve_body( $result );
		if( strpos(  $response_body,
				'ERROR' ) !== false ) {
			$success = false;
			$message = sprintf(
				__( 'Problem communicating with PayPal: %1$s', 'event_espresso' ),
				$response_body
			);
		} else {
			$success = true;
			$message = sprintf(
				__( 'Successful communication with Paypal: %1$s', 'event_espresso' ),
				$response_body
			);
		}
	}
	if( ! $success ) {
		$message .= '<br><a href="https://devblog.paypal.com/upcoming-security-changes-notice/">' . __( 'Please Review PayPal\'s Documentation', 'event_espresso' ) . '</a>';
	}
	?><div class="notice <?php echo $success ? 'notice-success' : 'notice-error';?>">
		<h1><?php _e( 'Paypal TLS Communication Test', 'event_espresso' );?></h1>
		<h2><?php echo $success ? __( 'Success!', 'event_espresso' ) : __( 'Error!', 'event_espresso' );?></h2>
		<p><?php echo $message?></p>
		<p><?php echo $success ? __( 'You may now deactivate this plugin.', 'event_espresso' ) : __( 'Please upgrade WordPress, and contact your hosting provider', 'event_espresso' );?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'ee_paypal_test_meets_new_tls_requirements' );
