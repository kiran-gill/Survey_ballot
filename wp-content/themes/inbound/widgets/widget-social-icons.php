<?php
/**
 * Plugin Name: Inbound Social Icons
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a progress bar.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Social_Icons_Widget' ) ) {
	class Inbound_Social_Icons_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Social Icons', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays social profile links with icons.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-social-icons' )
			);


			// Configure the widget fields
			// fields array
			$args['fields'] = array(
				array(
					'name'  => esc_html__( 'Info', 'inbound' ),
					'desc'  => esc_html__( 'Select display options. You can define social icons to be used for this widget through the profile.', 'inbound' ),
					'id'    => 'info',
					'value' => '',
					'type'  => 'paragraph'
				),
				array(
					'name'     => esc_html__( 'Title', 'inbound' ),
					'desc'     => esc_html__( 'Enter the widget title.', 'inbound' ),
					'id'       => 'title',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => esc_html__( 'Follow us', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Style', 'inbound' ),
					'desc'     => esc_html__( 'Select what style you would like to apply to these social icons.', 'inbound' ),
					'id'       => 'style',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Transparent', 'inbound' ),
							'value' => 'transparent'
						),
						array(
							'name'  => esc_html__( 'White', 'inbound' ),
							'value' => 'white'
						),
						array(
							'name'  => esc_html__( 'Black', 'inbound' ),
							'value' => 'black'
						),
						array(
							'name'  => esc_html__( 'Solid color', 'inbound' ),
							'value' => 'color'
						)
					),
					'std'      => 'transparent',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Icon Size', 'inbound' ),
					'desc'     => esc_html__( 'Select what icon size should be used for this widget.', 'inbound' ),
					'id'       => 'icon_size',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Tiny', 'inbound' ),
							'value' => '1'
						),
						array(
							'name'  => esc_html__( 'Small', 'inbound' ),
							'value' => '2'
						),
						array(
							'name'  => esc_html__( 'Medium', 'inbound' ),
							'value' => '3'
						),
						array(
							'name'  => esc_html__( 'Large', 'inbound' ),
							'value' => '4'
						)
					),
					'std'      => '3',
					'validate' => 'numeric',
					'filter'   => ''
				),
			); // fields array


			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {

			$out = $args['before_widget'];

			$id = inbound_get_widget_uid( 'social-icon' );

			$icon_size = inbound_array_option( 'icon_size', $instance, '3' );

			$out .= '<div class="social-widget style-' . inbound_array_option( 'style', $instance, 'transparent' ) . ' icon-size-' . $icon_size . '">';

			$title = inbound_array_option( 'title', $instance );
			if ( ! empty( $title ) ) {
				$out .= '<h3 class="widget-title">' . esc_html( $title ) . '</h3>';
			}

			$social_icons = inbound_option( 'social_media_profiles' );
			if ( is_serialized( $social_icons ) ) {
				$services = unserialize( $social_icons );
				if ( is_array( $services ) ) {
					$out .= '<ul>';
					$count = 1;
					foreach ( $services as $service ) {
						if ( inbound_array_option( 'show_in_widget', $service, false ) ) {

							$link_title = inbound_array_option( 'title', $service, '' );
							$link       = inbound_array_option( 'link', $service, '#' );
							$icon       = inbound_array_option( 'icon', $service, 'fa-star' );

							$out .= '<li><a href="' . esc_url( $link ) . '" class="social-icon-' . $count . '" title="' . esc_attr( $link_title ) . '" rel="me"><i class="fa ' . $icon . ' fa-' . $icon_size . 'x"></i></a></li>';

							$count ++;
						}
					}
					$out .= '</ul>';
				}
			}

			$out .= '</div>';

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_social_icons_widget' ) ) {
		function register_inbound_social_icons_widget() {
			register_widget( 'Inbound_Social_Icons_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_social_icons_widget', 1 );
	}
}