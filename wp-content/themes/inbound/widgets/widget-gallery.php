<?php
/**
 * Plugin Name: Inbound Gallery
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a featured testimonial block.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Gallery_Widget' ) ) {
	class Inbound_Gallery_Widget extends SR_Widget {

		function __construct() {
			global $_wp_additional_image_sizes;

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Gallery', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a gallery.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-gallery' )
			);

			// Prepare image sizes
			$image_sizes = array(
				array(
					'name'  => esc_html__( 'Default', 'inbound' ),
					'value' => ''
				),
				array(
					'name'  => esc_html__( 'Large', 'inbound' ),
					'value' => 'large'
				),
				array(
					'name'  => esc_html__( 'Medium', 'inbound' ),
					'value' => 'medium'
				),
				array(
					'name'  => esc_html__( 'Thumbnail', 'inbound' ),
					'value' => 'thumbnail'
				),
				array(
					'name'  => esc_html__( 'Full', 'inbound' ),
					'value' => 'full'
				)
			);

			if ( ! empty( $_wp_additional_image_sizes ) ) {
				foreach ( $_wp_additional_image_sizes as $name => $info ) {
					$image_sizes[] = array(
						'name'  => ucwords( strtolower( strtr( $name, "-_", "  " ) ) ),
						'value' => $name
					);
				}
			}

			// Configure the widget fields

			// fields array
			$args['fields'] = array(
				array(
					'name'     => esc_html__( 'Items', 'inbound' ),
					'desc'     => esc_html__( 'Select items to be displayed in this gallery. This is a comma-separated list of attachment IDs. Defaults to all current page\'s attachments.', 'inbound' ),
					'id'       => 'ids',
					'type'     => 'gallery',
					'class'    => '',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Image Size', 'inbound' ),
					'desc'     => esc_html__( 'Select a size. All sizes available to WordPress are displayed.', 'inbound' ),
					'id'       => 'size',
					'type'     => 'select',
					'fields'   => $image_sizes,
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Columns', 'inbound' ),
					'desc'     => esc_html__( 'How many items should be displayed per row?', 'inbound' ),
					'id'       => 'columns',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => '1',
							'value' => '1'
						),
						array(
							'name'  => '2',
							'value' => '2'
						),
						array(
							'name'  => '3',
							'value' => '3'
						),
						array(
							'name'  => '4',
							'value' => '4'
						),
						array(
							'name'  => '5',
							'value' => '5'
						),
						array(
							'name'  => '6',
							'value' => '6'
						),
					),
					'std'      => 4,
					'validate' => 'numeric',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Thumbnail Links', 'inbound' ),
					'desc'     => esc_html__( 'Select where you want thumbnails to link to.', 'inbound' ),
					'id'       => 'link',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Attachment Page', 'inbound' ),
							'value' => ''
						),
						array(
							'name'  => esc_html__( 'File', 'inbound' ),
							'value' => 'file'
						),
						array(
							'name'  => esc_html__( 'None', 'inbound' ),
							'value' => 'none'
						),


					),
					'std'      => 'file',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),

			); // fields array

			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {

			$out = $args['before_widget'];

			$shortcode_attr = array();
			foreach ( $instance as $k => $v ) {
				if ( empty( $v ) ) {
					continue;
				}
				if ( ! is_array( $v ) ) {
					$shortcode_attr[] = $k . '="' . esc_attr( $v ) . '"';
				}
			}

			$out .= do_shortcode( '[gallery ' . implode( ' ', $shortcode_attr ) . ']' );

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_gallery_widget' ) ) {
		function register_inbound_gallery_widget() {
			register_widget( 'Inbound_Gallery_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_gallery_widget', 1 );
	}
}