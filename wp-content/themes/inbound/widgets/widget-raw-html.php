<?php
/**
 * Plugin Name: Inbound Raw HTML
 * Plugin URI: http://www.shapingrain.com
 * Description: Renders any custom HTML and executes shortcodes.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Raw_HTML_Widget' ) ) {
	class Inbound_Raw_HTML_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Raw HTML', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Renders any custom HTML with shortcodes.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-raw-html' )

			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(
				array(
					'name'   => esc_html__( 'Text', 'inbound' ),
					'desc'   => esc_html__( 'Enter text or custom mark-up here.', 'inbound' ),
					'id'     => 'text',
					'type'   => 'textarea_code',
					'rows'   => '15',
					'class'  => 'widefat',
					'std'    => '',
					'filter' => 'esc_html'
				),
			); // fields array

			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {
			$out = $args['before_widget'];

			$out .= do_shortcode( html_entity_decode( inbound_array_option( 'text', $instance ) ) );

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_raw_html_widget' ) ) {
		function register_inbound_raw_html_widget() {
			register_widget( 'Inbound_Raw_HTML_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_raw_html_widget', 1 );
	}
}