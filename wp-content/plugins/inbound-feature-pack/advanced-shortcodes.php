<?php
/*
 * Provides shortcodes for advanced customization features for the Inbound for WordPress theme.
 */

define('INBOUND_ADVANCED_SHORTCODES', true);

/*
 * Set up shortcodes
 */
if ( ! function_exists( 'inbound_shortcode_widget' ) ) {
	function inbound_shortcode_widget( $attrs, $content = null ) {
		if ( ! function_exists ( 'inbound_option' ) ) return false;

		global $inbound_custom_widgets;

		if ( isset( $attrs['type'] ) ) {
			$key = trim( strtolower( $attrs['type'] ) );
			if ( isset ( $inbound_custom_widgets[ $key ] ) ) {
				$class = $inbound_custom_widgets[ $key ];
				$args  = array(
					'before_widget' => '<div class="widget ' . $class . '">',
					'after_widget'  => '</div>',
					'before_title'  => null,
					'after_title'   => null
				);

				the_widget( $class, $attrs, $args );
			}
		}
	}
}
add_shortcode( 'inbound_widget', 'inbound_shortcode_widget' );


if ( ! function_exists( 'inbound_shortcode_option' ) ) {
	function inbound_shortcode_option( $attrs, $content = null ) {
		if ( ! function_exists ( 'inbound_option' ) ) return false;

		$action = inbound_array_option( 'action', $attrs, 'echo' );
		$key    = inbound_array_option( 'key', $attrs, false );
		$val    = inbound_array_option( 'val', $attrs, false );

		$output = false;

		if ( $action == 'echo' ) {
			$output = inbound_option( $key );
		} elseif ( $action == 'set' ) {
			if ( $content != null ) {
				$output = $content;
			} else {
				$output = $val;
			}
			inbound_set_option( $key, $output );
		} elseif ( $action == 'add' ) {
			$output = inbound_option( $key );
			if ( $content != null ) {
				$output .= $content;
			} else {
				$output .= $val;
			}
			inbound_set_option( $key, $output );
		} elseif ( $action == 'prepend' ) {
			$output = inbound_option( $key );
			if ( $content != null ) {
				$output = $content . $output;
			} else {
				$output = $val . $output;
			}
			inbound_set_option( $key, $output );
		}

		return $output;
	}
}


if ( ! function_exists( 'inbound_custom_block_shortcode' ) ) {
	function inbound_custom_block_shortcode( $attrs, $content = null ) {
		if ( ! function_exists ( 'inbound_option' ) ) return false;

		global $inbound_custom_blocks;

		extract( shortcode_atts( array(
			'key' => '',
			'val' => ''
		), $attrs ) );

		extract( $attrs );

		$key = trim( strtolower( $key ) );
		$val = trim( $val );

		if ( $val == '' && $content != "" ) {
			$val = $content;
		}

		if ( $val != null ) {
			$inbound_custom_blocks[ $key ] = $val;
		}
	}
}

add_shortcode( 'inbound_option', 'inbound_shortcode_option' );
add_shortcode( 'inbound_custom_block', 'inbound_custom_block_shortcode' );
add_shortcode( 'custom_block', 'inbound_custom_block_shortcode' );

