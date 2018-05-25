<?php
/**
 * Plugin Name: Inbound Divider
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a graphical divider.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Divider_Widget' ) ) {
	class Inbound_Divider_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Divider', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a divider.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-divider' )
			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(

				array(
					'name'     => esc_html__( 'Style', 'inbound' ),
					'desc'     => esc_html__( 'Select a display style for this divider.', 'inbound' ),
					'id'       => 'style',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'solid', 'inbound' ),
							'value' => 'solid'
						),
						array(
							'name'  => esc_html__( 'dashed', 'inbound' ),
							'value' => 'dashed'
						),
						array(
							'name'  => esc_html__( 'dotted', 'inbound' ),
							'value' => 'dotted'
						),
						array(
							'name'  => esc_html__( 'double', 'inbound' ),
							'value' => 'double'
						)
					),
					'std'      => 'solid',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Alignment', 'inbound' ),
					'desc'     => esc_html__( 'Select how you would like to align this divider.', 'inbound' ),
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
					'std'      => 'center',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Height', 'inbound' ),
					'desc'     => esc_html__( 'Enter a value in pixels.', 'inbound' ),
					'id'       => 'height',
					'type'     => 'number',
					'units'    => array('px', '%'),
					// class, rows, cols
					'class'    => '',
					'std'      => '1',
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Width', 'inbound' ),
					'desc'     => esc_html__( 'Enter a value X of 100 percent.', 'inbound' ),
					'id'       => 'width',
					'type'     => 'number',
					'units'    => array('px', '%'),
					// class, rows, cols
					'class'    => '',
					'std'      => '100',
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Color', 'inbound' ),
					'id'       => 'color',
					'type'     => 'color',
					'std'      => '#000000',
					'validate' => false,
					'filter'   => false
				),


			); // fields array

			$this->create_widget( $args );
		}


		// Output function
		function widget( $args, $instance ) {

			$width_val    = inbound_array_option( 'width', $instance, false );
			if ( $width_val && is_array ( $width_val ) && ! empty ( $width_val ) ) {
				$width = $width_val['number'] . $width_val['unit'];
			} else {
				$width = "100%";
			}

			$height_val   = inbound_array_option( 'height', $instance, false );
			if ( $height_val && is_array ( $height_val ) && ! empty ( $height_val ) ) {
				$height = $height_val['number'] . $height_val['unit'];
			} else {
				$height = "1px";
			}

			$color        = inbound_array_option( 'color', $instance, "#000000" );
			$border_style = inbound_array_option( 'style', $instance, "solid" );
			$alignment    = inbound_array_option( 'alignment', $instance, "center" );

			$id    = inbound_get_widget_uid( 'divider' );
			$style = '#' . $id . ' { border-top: ' . $height . ' ' . $border_style . ' ' . $color . '; width: ' . $width . '}';
			inbound_add_custom_style( 'divider', $style );

			$out = $args['before_widget'];
			$out .= '<hr id="' . $id . '" class="divider divider-' . esc_attr( $border_style ) . ' align-' . esc_attr( $alignment ) . '">';
			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_divider_widget' ) ) {
		function register_inbound_divider_widget() {
			register_widget( 'Inbound_Divider_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_divider_widget', 1 );
	}
}