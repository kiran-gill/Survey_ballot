<?php
/**
 * Plugin Name: Image Comparison
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a map.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Comparison_Widget' ) ) {
	class Inbound_Comparison_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Image Comparison', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays an image comparison slider.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-comparison' )
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
					'std'      => esc_html__( 'Comparison', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => ''
				),

				array(
					'name'     => esc_html__( 'Mode', 'inbound' ),
					'id'       => 'mode',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Horizontal', 'inbound' ),
							'value' => 'horizontal'
						),
						array(
							'name'  => esc_html__( 'Vertical', 'inbound' ),
							'value' => 'vertical'
						),
					),
					'std'      => 'horizontal',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),


				// Image 1
				array(
					'name'  => esc_html__( 'Image 1', 'inbound' ),
					'desc'  => esc_html__( 'Upload or select an image.', 'inbound' ),
					'class' => 'img',
					'id'    => 'image_1',
					'type'  => 'image',
					'std'   => '',
					//'validate' => '',
					//'filter' => ''
				),
				array(
					'name'     => esc_html__( 'Label 1', 'inbound' ),
					'desc'     => esc_html__( 'Enter a label for the first image.', 'inbound' ),
					'id'       => 'label_1',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => esc_html__( 'Label 1', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => ''
				),

				// Image 2
				array(
					'name'  => esc_html__( 'Image 2', 'inbound' ),
					'desc'  => esc_html__( 'Upload or select an image.', 'inbound' ),
					'class' => 'img',
					'id'    => 'image_2',
					'type'  => 'image',
					'std'   => '',
					//'validate' => '',
					//'filter' => ''
				),
				array(
					'name'     => esc_html__( 'Label 2', 'inbound' ),
					'desc'     => esc_html__( 'Enter a label for the first image.', 'inbound' ),
					'id'       => 'label_2',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => esc_html__( 'Label 2', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => ''
				),

				array(
					'name'     => esc_html__( 'Starting Position', 'inbound' ),
					'desc'     => esc_html__( 'Enter a value in percent.', 'inbound' ),
					'id'       => 'position',
					'type'     => 'number',
					'group'    => 'style',
					'units'    => array('%'),
					// class, rows, cols
					'class'    => '',
					'std'      => '50',
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),

			); // fields array

			$this->create_widget( $args );
		}


		// Output function
		function widget( $args, $instance ) {

			$out = $args['before_widget'];

			if ( inbound_array_option( 'title', $instance, false ) && ! inbound_is_pagebuilder() ) {
				$out .= $args['before_title'];
				$out .= esc_html( $instance['title'] );
				$out .= $args['after_title'];
			}

			$id = inbound_get_widget_uid( 'comparison' );

			$image_1     = inbound_array_option( 'image_1', $instance );
			$label_1     = inbound_array_option( 'label_1', $instance );

			$image_2     = inbound_array_option( 'image_2', $instance );
			$label_2     = inbound_array_option( 'label_2', $instance );

			$mode        = inbound_array_option( 'mode', $instance );

			$animate = "true";

			$position    = inbound_array_option( 'position', $instance );
			if ( !empty ($position['number']) ) {
				$position = $position['number'];
			} else {
				$position = 50;
			}

			$out .= '<div id="' . $id . '-container" class="juxtapose" data-showcredits="false" data-startingposition="' . esc_attr ( $position ) . '%" data-animate="' . $animate . '" data-mode="' . esc_attr ( $mode ) . '">';

			if ( $image_1 ) {
				$image_src = wp_get_attachment_image_src( intval( $image_1 ), "full" );

				if ( $image_src ) {

					if ( empty ( $label_1 ) ) {
						$attachment  = get_post( intval( $image_1 ) );
						$image_title = $attachment->post_title;
						$data_label = '';
					} else {
						$image_title = $label_1;
						$data_label = ' data-label="' . esc_attr( $label_1 ) . '"';
					}

					$title_alt = ' alt="' . esc_attr( $image_title ) . '"';
					$out .= '<img src="' . esc_url( $image_src[0] ) . '"' . $title_alt . $data_label . '>';
				}
			}

			if ( $image_2 ) {
				$image_src = wp_get_attachment_image_src( intval( $image_2 ), "full" );

				if ( $image_src ) {

					if ( empty ( $label_2 ) ) {
						$attachment  = get_post( intval( $image_2 ) );
						$image_title = $attachment->post_title;
						$data_label = '';
					} else {
						$image_title = $label_2;
						$data_label = ' data-label="' . esc_attr( $label_2 ) . '"';
					}

					$title_alt = ' alt="' . esc_attr( $image_title ) . '"';
					$out .= '<img src="' . esc_url( $image_src[0] ) . '"' . $title_alt . $data_label . '>';
				}
			}

			$out .= '</div>';

			$out .= $args['after_widget'];

			echo $out;

			//$style = '#' . $id . '-container .map-widget-canvas { height:' . inbound_array_option( 'height', $instance, '300' ) . 'px; }';
			//inbound_add_custom_style( 'map', $style );

			wp_enqueue_script( 'inbound-juxtapose-js', get_template_directory_uri() . '/js/juxtapose.min.js', array(
				'jquery',
			), INBOUND_THEME_VERSION, true );

			/*
			wp_enqueue_script( 'inbound-initjuxtapose', get_template_directory_uri() . '/js/initjuxtapose.min.js', array(
				'jquery',
				'googlemaps'
			), INBOUND_THEME_VERSION );
			*/

		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_comparison_widget' ) ) {
		function register_Inbound_Comparison_Widget() {
			register_widget( 'Inbound_Comparison_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_comparison_widget', 1 );
	}
}