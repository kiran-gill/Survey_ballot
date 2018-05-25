<?php
/**
 * Plugin Name: Inbound Feature Image
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a feature image.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Feature_Media_Widget' ) ) {
	class Inbound_Feature_Media_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Feature Media', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a feature image, slider, video clip or custom HTML on a shelf.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-feature-media' )
			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(
				array(
					'name'     => esc_html__( 'Presentation Image', 'inbound' ),
					'desc'     => esc_html__( 'Pick a presentation image this item should be presented on.', 'inbound' ),
					'id'       => 'presentation_image',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Book shelf (Wood)', 'inbound' ),
							'value' => 'shelf-wood'
						),
						array(
							'name'  => esc_html__( 'Book shelf (Glass)', 'inbound' ),
							'value' => 'shelf-glass'
						),
						array(
							'name'  => esc_html__( 'Book shelf (Metal)', 'inbound' ),
							'value' => 'shelf-metal'
						),
					),
					'std'      => 'shelf',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Media', 'inbound' ),
					'desc'     => esc_html__( 'Select which content should be displayed in this widget.', 'inbound' ),
					'id'       => 'mode',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Custom Content', 'inbound' ),
							'value' => 'custom'
						),
						array(
							'name'  => esc_html__( 'Video (oEmbed)', 'inbound' ),
							'value' => 'video'
						),
						array(
							'name'  => esc_html__( 'Single Image', 'inbound' ),
							'value' => 'image'
						),
						array(
							'name'  => esc_html__( 'Image Slider', 'inbound' ),
							'value' => 'slider'
						),
					),
					'std'      => 'image',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'   => esc_html__( 'Custom Content or Video URL', 'inbound' ),
					'desc'   => esc_html__( 'Custom HTML, video URL or integration code.', 'inbound' ),
					'id'     => 'text',
					'type'   => 'textarea_code',
					'rows'   => '15',
					'class'  => 'widefat',
					'std'    => '',
					'validate' => 'alpha_dash',
					'filter' => 'esc_html'
				),
				array(
					'name'  => esc_html__( 'Feature Image', 'inbound' ),
					'desc'  => esc_html__( 'Upload the feature image, e.g. a product shot.', 'inbound' ),
					'class' => 'img',
					'id'    => 'image',
					'type'  => 'image',
					'std'   => '',
					//'validate' => '',
					//'filter' => ''
				),
				array(
					'name'     => esc_html__( 'Featured Image Slider', 'inbound' ),
					'desc'     => esc_html__( 'Select multiple images if you would like to display a slider.', 'inbound' ),
					'id'       => 'items',
					'type'     => 'gallery',
					'class'    => '',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Slider Options', 'inbound' ),
					'id'       => 'slider_options',
					'type'     => 'slider',
					'std'      => array(
						'controls'      => true,
						'pips'          => false,
						'transition'    => 'fade',
						'pauseonhover'  => null,
						'pauseonaction' => true,
						'randomize'     => null,
						'speed'         => '7000'
					),
					'validate' => '',
					'filter'   => ''
				),

			); // fields array

			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {
			$is_pagebuilder = inbound_is_pagebuilder( $args );

			$id = inbound_get_widget_uid( 'feature-image' );

			$out = $args['before_widget'];

			$presentation_image = inbound_array_option( 'presentation_image', $instance, 'shelf' );

			/* media mode */
			$mode = inbound_array_option( 'mode', $instance, 'image' );

			/* single image */
			$image = inbound_array_option( 'image', $instance );

			/* slider */
			$images = explode( ",", trim( inbound_array_option( 'items', $instance ) ) );

			/* custom content */
			$custom_content = inbound_array_option( 'text', $instance );
			if ( $custom_content ) {
				$custom_content = do_shortcode( html_entity_decode( $custom_content ) );
			}

			if ( $mode == "slider" ) { // image slider
				if ( is_array( $images ) ) {
					$slider_options = inbound_slider_options( inbound_array_option( 'slider_options', $instance, false ) );
					$out .= '<div class="feature-media feature-media-slider">';
					$out .= '<div class="flexslider' . $slider_options['classes'] . '"' . $slider_options['data'] . '><ul class="slides">';
					foreach ( $images as $id ) {
						if ( $image = wp_get_attachment_image( intval( $id ), "full" ) ) {
							$out .= '<li>' . $image . '</li>';
						}
					}
					$out .= '</ul></div></div>';
				}
			} elseif ( $mode == "image" ) { // single image
				if ( $image ) {
					$image_src = wp_get_attachment_image_src( intval( $image ), "full" );
					if ( $image_src ) {
						$image = $image = wp_get_attachment_image( intval( $image ), "full" );
						$out .= '<div class="feature-media feature-media-image">' . $image . '</div>';
					}
				}
			} elseif ( $mode == "video" ) { // video
				$out .= '<div class="feature-media feature-media-video"><div class="embed_container">' . wp_oembed_get( $custom_content ) . '</div></div>';
			} else { // custom content
				$out .= '<div class="feature-media feature-media-custom">' . inbound_esc_html ( $custom_content, false, true ) . '</div>';
			}

			$out .= '<img class="feature-media-shelf" alt="' . esc_attr( ucwords( str_replace( "-", " ", $presentation_image ) ) ) . '" src="' . esc_url( get_template_directory_uri() . '/images/shelfs/' . str_replace( "-", "_", $presentation_image ) . '.png' ) . '">';

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_feature_media_widget' ) ) {
		function register_inbound_feature_media_widget() {
			if ( basename( $_SERVER['PHP_SELF'], '.php' ) != "widgets" ) {
				register_widget( 'Inbound_Feature_Media_Widget' );
			}
		}

		add_action( 'widgets_init', 'register_inbound_feature_media_widget', 1 );
	}
}