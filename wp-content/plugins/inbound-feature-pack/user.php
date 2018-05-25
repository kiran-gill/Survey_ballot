<?php
/*
 * Functions that enhance the user profile fields
 */

define('INBOUND_USER', true);


/*
 * Register User Contact Methods
 */
if ( ! function_exists( 'inbound_user_contact_methods' ) ) {
	function inbound_user_contact_methods( $user_contact_method ) {
		$user_contact_method['facebook']  = esc_html__( 'Facebook Username', 'inbound' );
		$user_contact_method['twitter']   = esc_html__( 'Twitter Username', 'inbound' );
		$user_contact_method['gplus']     = esc_html__( 'Google Plus', 'inbound' );
		$user_contact_method['skype']     = esc_html__( 'Skype Username', 'inbound' );
		$user_contact_method['linkedin']  = esc_html__( 'LinkedIn Profile URL', 'inbound' );
		$user_contact_method['instagram'] = esc_html__( 'Instagram Username', 'inbound' );
		$user_contact_method['pinterest'] = esc_html__( 'Pinterest Username', 'inbound' );
		$user_contact_method['flickr']    = esc_html__( 'Flickr Username', 'inbound' );
		$user_contact_method['tumblr']    = esc_html__( 'Tumblr Profile URL', 'inbound' );
		$user_contact_method['youtube']   = esc_html__( 'YouTube Channel URL', 'inbound' );
		$user_contact_method['vimeo']     = esc_html__( 'Vimeo Username', 'inbound' );

		return $user_contact_method;
	}
}
add_filter( 'user_contactmethods', 'inbound_user_contact_methods' );
