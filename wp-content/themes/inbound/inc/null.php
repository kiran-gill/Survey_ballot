<?php
/*
 * Replaces function calls with dummy functions to be used if the theme's feature pack plug-in is not installed
 */

if ( ! defined ('INBOUND_FEATURE_PACK') ) {

	if ( ! function_exists('inbound_banner_wrapper_classes') ) {
		function inbound_banner_wrapper_classes() {
			$classes=array();
			$classes[] = "animated-header";
			if (count($classes) > 0) {
				echo ' class="'.implode(" ", $classes).'"';
			}
		}
	}

	if ( ! function_exists('inbound_banner_data_attrs') ) {
		function inbound_banner_data_attrs() {
			return '';
		}
	}

	if ( ! function_exists('inbound_the_banner') ) {
		function inbound_the_banner() {
			return '';
		}
	}

	if ( ! function_exists('inbound_get_site_title') ) {
		function inbound_get_site_title() {
			return get_bloginfo( 'name' );
		}
	}

	if ( ! function_exists('inbound_get_site_tagline') ) {
		function inbound_get_site_tagline() {
			return get_bloginfo( 'description' );
		}
	}

	if ( ! function_exists('inbound_footer_copyright') ) {
		function inbound_footer_copyright() {
			return '<a href="' . esc_url( __( 'https://wordpress.org/', 'inbound' ) ) . '">' . sprintf( esc_html__( 'Proudly powered by %s', 'inbound' ), 'WordPress' ) . '</a>';
		}
	}

}

