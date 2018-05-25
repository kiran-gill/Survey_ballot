<?php
/**
 * Plugin Name: Inbound Testimonial
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a testimonial.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Testimonial_Widget' ) ) {
	class Inbound_Testimonial_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Testimonial', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a testimonial.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-testimonial' )
			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(

				// Title field
				array(
					'name'     => esc_html__( 'Title', 'inbound' ),
					'id'       => 'title',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => esc_html__( 'Testimonial', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => 'strip_tags|esc_attr'
				),
				array(
					'name'     => esc_html__( 'Layout', 'inbound' ),
					'id'       => 'layout',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Speech Bubble', 'inbound' ),
							'value' => 'bubble'
						),
						array(
							'name'  => esc_html__( 'Elegant', 'inbound' ),
							'value' => 'elegant'
						),
						array(
							'name'  => esc_html__( 'Large', 'inbound' ),
							'value' => 'large'
						)
					),
					'std'      => 'bubble',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Quote', 'inbound' ),
					'desc'     => esc_html__( 'Enter the actual testimonial text here.', 'inbound' ),
					'id'       => 'quote',
					'type'     => 'textarea',
					'rows'     => '5',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => 'esc_textarea'
				),
				array(
					'name'     => esc_html__( 'Name', 'inbound' ),
					'desc'     => esc_html__( 'Enter the quoted person\'s name.', 'inbound' ),
					'id'       => 'name',
					'type'     => 'text',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => 'strip_tags|esc_attr'
				),
				array(
					'name'     => esc_html__( 'Job Title/Description/Company', 'inbound' ),
					'desc'     => esc_html__( 'Enter a job title, function and/or company name here.', 'inbound' ),
					'id'       => 'job_title',
					'type'     => 'text',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => 'strip_tags|esc_attr'
				),
				array(
					'name'  => esc_html__( 'Avatar/Picture', 'inbound' ),
					'desc'  => esc_html__( 'Upload an image to be displayed with this testimonial.', 'inbound' ),
					'class' => 'img',
					'id'    => 'avatar',
					'type'  => 'image',
					'std'   => '',
					//'validate' => '',
					//'filter' => ''
				)
			); // fields array

			$this->create_widget( $args );
		}


		// Output function
		function widget( $args, $instance ) {
			$id = inbound_get_widget_uid( 'testimonial' );

			$out = $args['before_widget'];

			if ( inbound_array_option( 'title', $instance, false ) && ! inbound_is_pagebuilder() ) {
				$out .= $args['before_title'];
				$out .= esc_html ( $instance['title'] );
				$out .= $args['after_title'];
			}

			$layout = inbound_array_option( 'layout', $instance, 'small' );

			$avatar       = inbound_array_option( 'avatar', $instance, false );
			$avatar_image = false;

			if ( $avatar ) {
				$avatar = intval( $avatar );
				if ( $avatar != 0 ) {
					$avatar_image = wp_get_attachment_image_src( $avatar, 'inbound-testimonial-avatar' );
					if ( $avatar_image ) {
						$avatar_image = '<img src="' . esc_url( $avatar_image[0] ) . '" alt="' . esc_attr( inbound_array_option( 'name', $instance, '' ) ) . '" height="60" width="60">';
					}
				}
			}

			$add_classes = array(
				'testimonial',
				'testimonial-layout-' . $layout
			);

			if ( $avatar_image ) {
				$add_classes[] = "has-avatar";
			} else {
				$add_classes[] = 'no-avatar';
			}

			$out .= '<blockquote class="' . implode( ' ', $add_classes ) . '">';

			if ( $layout == "elegant" && $avatar_image ) {
				$out .= $avatar_image;
			}

			$out .= '<q>' . inbound_esc_html( inbound_array_option( 'quote', $instance, '' ), false, true )  . '</q>
            <footer>';

			if ( $layout != "elegant" && $avatar_image ) {
				$out .= $avatar_image;
			}

			$out .= '<div>' . esc_html( inbound_array_option( 'name', $instance, '' ) ) . '</div> ' . '<div class="testimomnial-company">' . esc_html ( inbound_array_option( 'job_title', $instance, '' ) ) . '</div> </footer>
			</blockquote>';

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_testimonial_widget' ) ) {
		function register_inbound_testimonial_widget() {
			register_widget( 'Inbound_Testimonial_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_testimonial_widget', 1 );
	}
}