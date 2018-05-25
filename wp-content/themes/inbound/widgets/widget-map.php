<?php
/**
 * Plugin Name: Inbound Map
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a map.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Map_Widget' ) ) {
	class Inbound_Map_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Map', 'inbound' ),
				// Widget Backend Description								
				'description' => esc_html__( 'Displays a map.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-map' )
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
					'std'      => esc_html__( 'Map', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'        => esc_html__( 'Address or Coordinates', 'inbound' ),
					'desc'        => esc_html__( 'Enter an address or geographical coordinates here.', 'inbound' ),
					'id'          => 'address',
					'type'        => 'textarea',
					'rows'        => '5',
					// class, rows, cols
					'class'       => 'widefat',
					'std'         => '',
					'placeholder' => esc_html__( '1600 Amphitheatre Pkwy, Mountain View, CA 94043', 'inbound' ),
					'validate'    => 'alpha_dash',
					'filter'      => 'esc_textarea'
				),
				array(
					'name'     => esc_html__( 'Map Type', 'inbound' ),
					'desc'     => esc_html__( 'Select which map type to use for this widget.', 'inbound' ),
					'id'       => 'type',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Hybrid', 'inbound' ),
							'value' => 'hybrid'
						),
						array(
							'name'  => esc_html__( 'Roadmap', 'inbound' ),
							'value' => 'roadmap'
						),
						array(
							'name'  => esc_html__( 'Satellite', 'inbound' ),
							'value' => 'satellite'
						),
						array(
							'name'  => esc_html__( 'Terrain', 'inbound' ),
							'value' => 'terrain'
						)
					),
					'std'      => 'hybrid',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Zoom Level', 'inbound' ),
					'desc'     => esc_html__( 'Enter a value.', 'inbound' ),
					'id'       => 'zoom',
					'type'     => 'number',
					'units'    => array('x'),
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => 10,
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Height', 'inbound' ),
					'desc'     => esc_html__( 'Define the default height of this map in pixels.', 'inbound' ),
					'id'       => 'height',
					'type'     => 'text',
					'class'    => 'widefat',
					'std'      => '300',
					'validate' => 'numeric',
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

			$id = inbound_get_widget_uid( 'map' );

			$zoom = inbound_array_option( 'zoom', $instance, '10' );
			if ( !empty ($zoom['number']) ) {
				$zoom = $zoom['number'];
			} else {
				$zoom = 10;
			}

			$out .= '<div id="' . $id . '-container" class="map-container"><div id="' . esc_attr ( $id ) . '" class="map-widget-canvas" data-zoom="' . esc_attr ( $zoom ) . '" data-type="' . esc_attr ( inbound_array_option( 'type', $instance, 'hybrid' ) ) . '" data-location="' . esc_attr( inbound_array_option( 'address', $instance, '' ) ) . '"></div></div>';

			$out .= $args['after_widget'];

			echo $out;

			$style = '#' . $id . '-container .map-widget-canvas { height:' . inbound_array_option( 'height', $instance, '300' ) . 'px; }';
			inbound_add_custom_style( 'map', $style );

			$api_key = inbound_option('google_api_key');

			wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&key=' . $api_key . '&sensor=false', array(), '3.0', true );
			wp_enqueue_script( 'inbound-initmap', get_template_directory_uri() . '/js/initmap.min.js', array(
				'jquery',
				'googlemaps'
			), INBOUND_THEME_VERSION );

		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_map_widget' ) ) {
		function register_inbound_map_widget() {
			register_widget( 'Inbound_Map_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_map_widget', 1 );
	}
}