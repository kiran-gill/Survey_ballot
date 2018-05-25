<?php
/**
 * Plugin Name: Inbound Image
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays an image.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Image_Widget' ) ) {
	class Inbound_Image_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Image', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a simple image.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-image' )
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
					'name'  => esc_html__( 'Image', 'inbound' ),
					'desc'  => esc_html__( 'Upload or select an image.', 'inbound' ),
					'class' => 'img',
					'id'    => 'image',
					'type'  => 'image',
					'std'   => '',
					//'validate' => '',
					//'filter' => ''
				),
				array(
					'name'     => esc_html__( 'Alignment', 'inbound' ),
					'desc'     => esc_html__( 'Select how to align the image relative to the surrounding content.', 'inbound' ),
					'id'       => 'alignment',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Left', 'inbound' ),
							'value' => 'left'
						),
						array(
							'name'  => esc_html__( 'Center', 'inbound' ),
							'value' => 'center'
						),
						array(
							'name'  => esc_html__( 'Right', 'inbound' ),
							'value' => 'right'
						)
					),
					'std'      => 'left',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Link URL', 'inbound' ),
					'desc'     => esc_html__( 'Enter an optional URL to link the image to.', 'inbound' ),
					'id'       => 'link',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Open Link in New Window', 'inbound' ),
					'id'       => 'link_target',
					'type'     => 'checkbox',
					'class'    => 'widefat',
					'std'      => 0,
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'      => esc_html__( 'Open Modal Window', 'inbound' ),
					'desc'      => esc_html__( 'Optional modal window to open instead of using a link. The feature pack plug-in is required.', 'inbound' ),
					'id'        => 'modal',
					'type'      => 'posts',
					'post_type' => 'modal',
					'std'       => 0,
					'validate'  => 'alpha_dash',
					'filter'    => ''
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

			$image     = inbound_array_option( 'image', $instance );
			$alignment = inbound_array_option( 'alignment', $instance, 'left' );

			$link        = esc_url( trim( inbound_array_option( 'link', $instance, '' ) ) );
			$link_target = inbound_array_option( 'link_target', $instance, false );
			if ( $link_target == 1 ) {
				$link_target = ' target="_blank"';
			}

			// modal window
			$link_data = "";
			$modal     = inbound_array_option( 'modal', $instance, false );
			if (defined('INBOUND_MODALS' ) ) {
				if ( $modal ) {
					inbound_add_modal( $modal );
					$link_data   = ' data-featherlight="#modal-' . $modal . '" data-featherlight-variant="modal-style-' . $modal . '"';
					$link        = 'javascript:void(0);';
					$link_target = '';
				}
			}


			// image output
			if ( $image ) {
				$image_src = wp_get_attachment_image_src( intval( $image ), "full" );

				if ( $image_src ) {
					$title = inbound_array_option( 'title', $instance, false );
					if ( $title ) {
						$title_alt = ' alt="' . esc_attr( $title ) . '"';
					} else {
						$attachment  = get_post( intval( $image ) );
						$image_title = $attachment->post_title;

						$title_alt = ' alt="' . esc_attr( $image_title ) . '"';
					}

					$out .= '<div class="single-image image-' . $alignment . '">';

					if ( $link && $link != '' ) {
						// link URLs are escaped as such while retrieved from database
						$out .= '<a href="' . esc_attr( $link ) . '"' . $link_target . $link_data . '>';
					}
					$out .= '<img src="' . esc_url( $image_src[0] ) . '"' . $title_alt . '>';
					if ( $link && $link != '' ) {
						$out .= '</a>';
					}
					$out .= '</div>';
				}
			}

			$out .= $args['after_widget'];

			echo $out;

		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_image_widget' ) ) {
		function register_inbound_image_widget() {
			register_widget( 'Inbound_Image_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_image_widget', 1 );
	}
}