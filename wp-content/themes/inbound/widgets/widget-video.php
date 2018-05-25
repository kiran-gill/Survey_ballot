<?php
/**
 * Plugin Name: Inbound Video
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a map.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Video_Widget' ) ) {
	class Inbound_Video_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Embedded Video', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays an embedded video.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-embedded-video' )
			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(

				// Title field
				array(
					'name'     => esc_html__( 'Title', 'inbound' ),
					'desc'     => esc_html__( 'Enter the widget title.', 'inbound' ),
					'id'       => 'title',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Video URL or Custom Embed Code', 'inbound' ),
					'desc'     => esc_html__( 'Enter a plain video URL, or an embed code. If using custom embed codes, make sure you modify it to be responsive.', 'inbound' ),
					'id'       => 'code',
					'type'     => 'textarea',
					'rows'     => '5',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => 'esc_textarea'
				),
				array(
					'name'     => esc_html__( 'Embed Mode', 'inbound' ),
					'desc'     => esc_html__( 'Select which embed mode you would like to use.', 'inbound' ),
					'id'       => 'type',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'oEmbed from plain URL', 'inbound' ),
							'value' => 'embed'
						),
						array(
							'name'  => esc_html__( 'Custom integration code', 'inbound' ),
							'value' => 'custom'
						),
						array(
							'name'  => esc_html__( 'Custom integration code, with responsive wrapper', 'inbound' ),
							'value' => 'custom_wrapper'
						)

					),
					'std'      => 'embed',
					'validate' => 'alpha_dash',
					'filter'   => ''
				)
			); // fields array

			$this->create_widget( $args );
		}


		// Output function
		function widget( $args, $instance ) {

			$out = $args['before_widget'];

			if ( inbound_array_option( 'title', $instance, false ) && ! inbound_is_pagebuilder() ) {
				$out .= $args['before_title'];
				$out .= esc_html ( $instance['title'] );
				$out .= $args['after_title'];
			}


			$type = inbound_array_option( 'type', $instance, false );

			if ( $type == "custom" ) {
				$out .= inbound_esc_html( html_entity_decode( inbound_array_option( 'code', $instance, '' ) ) );
			} elseif ( $type == "custom_wrapper" ) {
				$out .= '<div class="embed_container">' . inbound_esc_html( html_entity_decode( inbound_array_option( 'code', $instance, '' ) ) ) . '</div>';
			} else {
				$embed = new WP_Embed();
				$out .= $embed->run_shortcode( '[embed]' . inbound_array_option( 'code', $instance, '' ) . '[/embed]' );
			}

			$out .= $args['after_widget'];

			echo $out;

		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_video_widget' ) ) {
		function register_inbound_video_widget() {
			register_widget( 'Inbound_Video_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_video_widget', 1 );
	}
}