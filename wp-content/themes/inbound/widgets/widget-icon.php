<?php
/**
 * Plugin Name: Inbound Icon
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays an icon.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Icon_Widget' ) ) {
	class Inbound_Icon_Widget extends SR_Widget {

		function __construct() {
			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Icon', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays an icon.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-icon' )
			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(
				array(
					'name'     => esc_html__( 'Icon', 'inbound' ),
					'desc'     => esc_html__( 'Select an icon.', 'inbound' ),
					'id'       => 'icon',
					'type'     => 'fontawesome',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Icon Size', 'inbound' ),
					'desc'     => esc_html__( 'Select the size for the icon you would like to display.', 'inbound' ),
					'id'       => 'icon_size',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => 'Tiny',
							'value' => '1x'
						),
						array(
							'name'  => 'Small',
							'value' => '2x'
						),
						array(
							'name'  => 'Medium',
							'value' => '3x'
						),
						array(
							'name'  => 'Large',
							'value' => '4x'
						),
						array(
							'name'  => 'Extra Large',
							'value' => '5x'
						)
					),
					'std'      => '4x',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Shape', 'inbound' ),
					'desc'     => esc_html__( 'Select the shape for this icon, if a background colour or border is selected.', 'inbound' ),
					'id'       => 'icon_shape',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Square', 'inbound' ),
							'value' => 'square'
						),
						array(
							'name'  => esc_html__( 'Rounded', 'inbound' ),
							'value' => 'rounded'
						),
						array(
							'name'  => esc_html__( 'Circle', 'inbound' ),
							'value' => 'circle'
						),
					),
					'std'      => 'square',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Background Color', 'inbound' ),
					'desc'     => esc_html__( 'Select a background color for this icon.', 'inbound' ),
					'id'       => 'background_color',
					'type'     => 'color',
					// class, rows, cols
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Icon Color', 'inbound' ),
					'desc'     => esc_html__( 'Select a icon color.', 'inbound' ),
					'id'       => 'icon_color',
					'type'     => 'color',
					// class, rows, cols
					'std'      => '#000000',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Border Width', 'inbound' ),
					'desc'     => esc_html__( 'Enter a value in px.', 'inbound' ),
					'id'       => 'border_width',
					'type'     => 'number',
					'units'    => array('px'),
					// class, rows, cols
					'class'    => '',
					'std'      => '',
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Border Style', 'inbound' ),
					'id'       => 'border_style',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Solid', 'inbound' ),
							'value' => 'solid'
						),
						array(
							'name'  => esc_html__( 'Dashed', 'inbound' ),
							'value' => 'dashed'
						),
						array(
							'name'  => esc_html__( 'Dotted', 'inbound' ),
							'value' => 'dotted'
						),
					),
					'std'      => 'solid',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Border Color', 'inbound' ),
					'desc'     => esc_html__( 'Select a border color for this icon.', 'inbound' ),
					'id'       => 'border_color',
					'type'     => 'color',
					// class, rows, cols
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),

			); // fields array

			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {
			$is_pagebuilder = inbound_is_pagebuilder( $args );
			$id             = inbound_get_widget_uid( 'icon' );

			$add_classes = "";
			$out         = $args['before_widget'];

			$icon_color = inbound_array_option( 'icon_color', $instance, '' );
			$background_color = inbound_array_option( 'background_color', $instance, '' );
			$border_color = inbound_array_option( 'border_color', $instance, '' );

			$border_width_val  = inbound_array_option( 'border_width', $instance, false );
			if ( $border_width_val && is_array ( $border_width_val ) && ! empty ( $border_width_val['number'] ) ) {
				$border_width = $border_width_val['number'] . 'px';
			} else {
				$border_width = 0;
			}

			$border_style = inbound_array_option( 'border_style', $instance, 'solid' );

			if ( ! empty( $icon_color ) ) {
				$styles[] = '#' . $id . ' { ';
			}

			if ( ! empty ( $background_color ) ) {
				$styles[] = 'background:' . $background_color . ';';
			}

			if ( ! empty ( $icon_color ) ) {
				$styles[] = 'color:' . $icon_color . ';';
			}

			if ( ! empty ( $border_color ) && ! empty ( $border_width ) ) {
				$styles[] = 'border:' . $border_width . ' ' . $border_style . ' ' . $border_color . ';';
			}

			$styles[] = '}';

			inbound_add_custom_style( 'icon', implode ( "\n", $styles ) );


			$icon_size = inbound_array_option( 'icon_size', $instance, '4x' );

			$classes = array("inbound-icon");

			$icon_shape = inbound_array_option( 'icon_shape', $instance, 'square' );

			if ( ! empty ( $icon_shape ) ) {
				$classes[] = 'icon-shape-' . $icon_shape;
			}

			if (  ! empty ( $background_color ) ||  ! empty ( $border_color ) ) {
				$classes[] = 'icon-size-' . esc_attr( $icon_size );
				$out .= '<div class="' . implode ( ' ', $classes ) . '">';
			}

			$out .= '<i id="' . $id . '" class="' . implode ( ' ', $classes ) . ' fa ' . esc_attr( $instance['icon'] ) . ' fa-' . esc_attr( $icon_size ) . '"></i>';

			if (  ! empty ( $background_color ) ||  ! empty ( $border_color ) ) {
				$out .= '</div>';
			}


			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_icon_widget' ) ) {
		function register_inbound_icon_widget() {
			register_widget( 'Inbound_Icon_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_icon_widget', 1 );
	}
}