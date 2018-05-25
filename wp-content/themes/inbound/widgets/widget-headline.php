<?php
/**
 * Plugin Name: Inbound Headline
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a headline.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Headline_Widget' ) ) {
	class Inbound_Headline_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Headline', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a headline.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-headline' )
			);

			// Configure the widget fields

			// Tab groups
			$args['groups'] = array(
				'general' => esc_html__( 'General', 'inbound' ),
				'style'  => esc_html__( 'Text Style', 'inbound' ),
				'design'  => esc_html__( 'Design', 'inbound' ),
			);


			// fields array
			$args['fields'] = array(
				array(
					'name'     => esc_html__( 'Headline Style', 'inbound' ),
					'id'       => 'headline_style',
					'group'    => 'design',
					'group-selector' => true,
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'No Decoration', 'inbound' ),
							'value' => 'no-decoration'
						),
						array(
							'name'  => esc_html__( 'Solid Background', 'inbound' ),
							'value' => 'solid-background'
						),
						array(
							'name'  => esc_html__( 'Border Top', 'inbound' ),
							'value' => 'border-top'
						),
						array(
							'name'  => esc_html__( 'Border Bottom', 'inbound' ),
							'value' => 'border-bottom'
						),
						array(
							'name'  => esc_html__( 'Border Top/Bottom', 'inbound' ),
							'value' => 'border-top-bottom'
						),
						array(
							'name'  => esc_html__( 'Border Left', 'inbound' ),
							'value' => 'border-left'
						),
						array(
							'name'  => esc_html__( 'Border Right', 'inbound' ),
							'value' => 'border-right'
						),
						array(
							'name'  => esc_html__( 'Border Left/Right', 'inbound' ),
							'value' => 'border-left-right'
						),
					),
					'std'      => 'regular',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Border Width', 'inbound' ),
					'desc'     => esc_html__( 'Enter a value in px or %.', 'inbound' ),
					'id'       => 'border_width',
					'type'     => 'number',
					'units'    => array('px', '%'),
					'group'    => 'design',
					// class, rows, cols
					'class'    => '',
					'std'      => '0',
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Border Height', 'inbound' ),
					'desc'     => esc_html__( 'Enter a value in px or %.', 'inbound' ),
					'id'       => 'border_height',
					'type'     => 'number',
					'units'    => array('px'),
					'group'    => 'design',
					// class, rows, cols
					'class'    => '',
					'std'      => '0',
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Border/Background Color', 'inbound' ),
					'desc'     => esc_html__( 'Select a border color. The same color will be used if a solid background is selected.', 'inbound' ),
					'id'       => 'background_color',
					'group'    => 'design',
					'type'     => 'color',
					// class, rows, cols
					'std'      => '#000000',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Text Color', 'inbound' ),
					'desc'     => esc_html__( 'Select a text color.', 'inbound' ),
					'id'       => 'text_color',
					'group'    => 'design',
					'is-group' => 'style',
					'group-value' => array( 'solid-background' ),
					'type'     => 'color',
					// class, rows, cols
					'std'      => '#000000',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Headline', 'inbound' ),
					'id'       => 'title',
					'group'    => 'general',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Headline Type', 'inbound' ),
					'id'       => 'type',
					'group'    => 'general',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => 'H1',
							'value' => 'h1'
						),
						array(
							'name'  => 'H2',
							'value' => 'h2'
						),
						array(
							'name'  => 'H3',
							'value' => 'h3'
						),
						array(
							'name'  => 'H4',
							'value' => 'h4'
						),
						array(
							'name'  => 'H5',
							'value' => 'h5'
						),
						array(
							'name'  => 'H6',
							'value' => 'h6'
						),
					),
					'std'      => 'h1',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Sub Headline', 'inbound' ),
					'desc'     => esc_html__( 'Enter the sub headline title.', 'inbound' ),
					'id'       => 'sub_headline_title',
					'group'    => 'general',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Sub Headline Type', 'inbound' ),
					'id'       => 'sub_headline_type',
					'group'    => 'general',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => 'H1',
							'value' => 'h1'
						),
						array(
							'name'  => 'H2',
							'value' => 'h2'
						),
						array(
							'name'  => 'H3',
							'value' => 'h3'
						),
						array(
							'name'  => 'H4',
							'value' => 'h4'
						),
						array(
							'name'  => 'H5',
							'value' => 'h5'
						),
						array(
							'name'  => 'H6',
							'value' => 'h6'
						),
					),
					'std'      => 'h2',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Line Height', 'inbound' ),
					'desc'     => esc_html__( 'Enter a value in pixels.', 'inbound' ),
					'id'       => 'height',
					'type'     => 'number',
					'group'    => 'style',
					'units'    => array('px'),
					// class, rows, cols
					'class'    => '',
					'std'      => '0',
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Letter Spacing', 'inbound' ),
					'desc'     => esc_html__( 'Enter a value in pixels.', 'inbound' ),
					'id'       => 'spacing',
					'type'     => 'number',
					'group'    => 'style',
					'units'    => array('px'),
					// class, rows, cols
					'class'    => '',
					'std'      => '0',
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),


			); // fields array

			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {

			$out = $args['before_widget'];

			$id = inbound_get_widget_uid( 'headline' );
			$style = '';

			$design_style = inbound_array_option( 'headline_style', $instance, 'no-decoration' );

			$bg_color = inbound_array_option( 'background_color', $instance, '' );
			$text_color = inbound_array_option( 'text_color', $instance, '' );


			/* Letter Spacing and Line Height */

			$height_val = inbound_array_option( 'height', $instance, false );

			if ( $height_val && is_array ( $height_val ) && ! empty ( $height_val ) ) {
				if ( isset ( $height_val['number'] ) && $height_val['number'] > 0 ) {
					$height = $height_val['number'] . 'px';
				} else {
					$height = false;
				}
			} else {
				$height = false;
			}

			$spacing_val = inbound_array_option( 'spacing', $instance, false );
			if ( $spacing_val && is_array ( $spacing_val ) && ! empty ( $spacing_val ) ) {
				if ( isset ( $spacing_val['number'] ) && $spacing_val['number'] > 0 ) {
					$spacing = $spacing_val['number'] . 'px';
				} else {
					$spacing = false;
				}
			} else {
				$spacing = false;
			}


			/* Border */

			$border_height_val  = inbound_array_option( 'border_height', $instance, false );
			if ( $border_height_val && is_array ( $border_height_val ) && ! empty ( $border_height_val ) ) {
				$border_height = $border_height_val['number'] . 'px';
			} else {
				$border_height = 0;
			}

			$border_width_val  = inbound_array_option( 'border_width', $instance, false );
			if ( $border_width_val && is_array ( $border_width_val ) && ! empty ( $border_width_val ) ) {
				$border_width = $border_width_val['number'] . $border_width_val['unit'];
			} else {
				$border_width = 0;
			}


			/*
			 * Dynamic CSS
			 */

			/* Colors, Borders*/

			if ( ! empty( $bg_color ) && $border_height > 0 && ! empty ( $border_width ) ) {

				switch ( $design_style ) {
					case 'solid-background':
						$style .= '#' . $id . ' { background:' . $bg_color . '; color: ' . $text_color . ' }';
						$style .= '#' . $id . ' .regular-title, #' . $id . ' .regular-sub-title { color: ' . $text_color . ' }';
						break;
					case 'border-top':
						$style .= '#' . $id . ':before { background:' . $bg_color . '; height:' . $border_height . '; width:' . $border_width . ';  }';
						break;
					case 'border-bottom':
						$style .= '#' . $id . ':after { background:' . $bg_color . '; height:' . $border_height . '; width:' . $border_width . ';  }';
						break;
					case 'border-top-bottom':
						$style .= '#' . $id . ':before { background:' . $bg_color . '; height:' . $border_height . '; width:' . $border_width . ';  } ';
						$style .= '#' . $id . ':after { background:' . $bg_color . '; height:' . $border_height . '; width:' . $border_width . ';  }';
						break;
					case 'border-left':
						$style .= '#' . $id . ' .regular-title:before { background:' . $bg_color . '; height:' . $border_height . '; width:' . $border_width . ';  }';
						break;
					case 'border-right':
						$style .= '#' . $id . ' .regular-title:after { background:' . $bg_color . '; height:' . $border_height . '; width:' . $border_width . '; content:" "; }';
						break;
					case 'border-left-right':
						$style .= '#' . $id . ' .regular-title:before, #' . $id . ' .regular-title:after { background:' . $bg_color . '; height:' . $border_height . '; width:' . $border_width . ';  } ';
						break;
					default:
						$style = '';
						break;
				}

			}


			/* Style for headline */

			if ( $height || $spacing ) {
				$style .= '#' . $id . ' { ';
				if ( $spacing > 0 ) {
					$style .= 'letter-spacing:' . $spacing . ';' ;
				}
				if ( $height > 0 ) {
					$style .= 'line-height:' . $height . ';' ;
				}
				$style .= '}';
			}

			inbound_add_custom_style( 'headline', $style );

			/*
			 * Actual HTML
			 */

			$out .= '<div id="' . $id . '" class="inbound-headline headline-' . $design_style . '">';

			/* headline */
			$headline_type  = inbound_array_option( 'type', $instance, 'h2' );
			$headline_title = inbound_array_option( 'title', $instance, '' );

			if ( ! empty( $headline_title ) ) {
				$h       = '<' . esc_html( $headline_type ) . ' class="regular-title">';
				$h_close = '</' . esc_html( $headline_type ) . '>';
				$out .= $h . esc_html( $headline_title ) . $h_close;
			}

			/* sub headline */

			$sub_headline_type  = inbound_array_option( 'sub_headline_type', $instance, 'h3' );
			$sub_headline_title = inbound_array_option( 'sub_headline_title', $instance, '' );

			if ( ! empty ( $sub_headline_title ) ) {
				$h       = '<' . esc_html( $sub_headline_type ) . ' class="regular-sub-title">';
				$h_close = '</' . esc_html( $sub_headline_type ) . '>';
				$out .= $h . esc_html( $sub_headline_title ) . $h_close;
			}

			$out .= '</div>';

			$out .= $args['after_widget'];



			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_headline_widget' ) ) {
		function register_inbound_headline_widget() {
			register_widget( 'Inbound_Headline_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_headline_widget', 1 );
	}
}