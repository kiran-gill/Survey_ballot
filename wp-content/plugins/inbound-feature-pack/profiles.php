<?php
/*
 * Provides the profiles feature for the Inbound for WordPress theme.
 */

define('INBOUND_PROFILES', true);

/*
 * 	Set up profiles content type
 */
if ( ! function_exists( 'inbound_profile_setup' ) ) {
	function inbound_profile_setup() {
		if ( ! function_exists ( 'inbound_option' ) ) return false;
		$profiles = new SR_Custom_Post_Type(
			'profile',
			array(
				'supports'            =>
					array( 'title' ),
				'menu_position'       => 310,
				'public'              => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false
			)
		);
	}
}
add_action( 'inbound_admin_setup_after_content_types', 'inbound_profile_setup', 1 );


/*
 * Get all profiles
 */

if ( ! function_exists( 'inbound_get_profiles' ) ) {
	function inbound_get_profiles() {
		$profiles = false;
		$args     = array(
			'post_type'        => 'profile',
			'post_status'      => null,
			'posts_per_page'   => - 1,
			'caller_get_posts' => 1
		);

		$profile_query = null;
		$profile_query = new WP_Query( $args );
		if ( $profile_query->have_posts() ) {
			$profiles = array();
			while ( $profile_query->have_posts() ) {
				$profile_query->the_post();
				$the_id              = get_the_ID();
				$profile_options     = get_post_custom( $the_id );
				$profiles[ $the_id ] = array(
					'options' => $profile_options,
					'title'   => get_the_title()
				);
			}

		}
		wp_reset_query();

		return $profiles;
	}
}
