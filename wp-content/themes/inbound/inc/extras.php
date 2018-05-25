<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package inbound
 */

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function inbound_page_menu_args( $args ) {
	$args['show_home'] = true;

	return $args;
}

add_filter( 'wp_page_menu_args', 'inbound_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 */
function inbound_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}

add_filter( 'body_class', 'inbound_body_classes' );

/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 */
function inbound_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) ) {
		return $url;
	}

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id ) {
		$url .= '#main';
	}

	return $url;
}

add_filter( 'attachment_link', 'inbound_enhanced_image_navigation', 10, 2 );


/**
 * @param string $code name of the shortcode
 * @param string $content
 *
 * @return string content with shortcode striped
 */
function inbound_strip_shortcode( $code, $content ) {
	global $shortcode_tags;

	$stack          = $shortcode_tags;
	$shortcode_tags = array( $code => 1 );

	$content = strip_shortcodes( $content );

	$shortcode_tags = $stack;

	return $content;
}


function inbound_mime_types( $mime_types ) {
	$mime_types['ico'] = 'image/x-icon';

	return $mime_types;
}

add_filter( 'upload_mimes', 'inbound_mime_types', 1, 1 );