<?php
/**
 * Plugin Name: Inbound Call To Action Box
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a colored box.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_CTA_Box_Widget' ) ) {
	class Inbound_CTA_Box_Widget extends SR_Widget {

		function __construct() {
			// Get available button styles from options
			$styles      = array();
			$styles_temp = inbound_option( 'global_button_styles' );
			$std_style   = '';
			if ( is_array( $styles_temp ) && count( $styles_temp ) > 0 ) {
				foreach ( $styles_temp as $style ) {
					if ( $style['name'] == '' ) {
						$name = $style['uid'];
					} else {
						$name = $style['name'];
					}
					$styles[] = array(
						'name'  => $name,
						'value' => $style['uid']
					);
				}
				$std_style = $styles[0]['value'];
			}

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Call To Action Box', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a call to action box with text and a button.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-call-to-action-box' )
			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(
				array(
					'name'     => esc_html__( 'Text', 'inbound' ),
					'desc'     => esc_html__( 'Enter text or custom mark-up here.', 'inbound' ),
					'id'       => 'text',
					'type'     => 'textarea',
					'rows'     => '5',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Font Size', 'inbound' ),
					'desc'     => esc_html__( 'Enter font size in pixels.', 'inbound' ),
					'id'       => 'size',
					'type'     => 'number',
					'units'    => array('px'),
					'rows'     => '5',
					'class'    => '',
					'std'      => '13',
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Call to Action Style', 'inbound' ),
					'desc'     => esc_html__( 'You can create and edit button styles via the theme\'s options panel.', 'inbound' ),
					'id'       => 'style',
					'type'     => 'select',
					'fields'   => $styles,
					'std'      => $std_style,
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Call to Action Position', 'inbound' ),
					'id'       => 'position',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Left of text', 'inbound' ),
							'value' => 'left'
						),
						array(
							'name'  => esc_html__( 'Right of text', 'inbound' ),
							'value' => 'right'
						),
						array(
							'name'  => esc_html__( 'Above text', 'inbound' ),
							'value' => 'above'
						),
						array(
							'name'  => esc_html__( 'Below text', 'inbound' ),
							'value' => 'below'
						),
					),
					'std'      => 'left',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Call to Action', 'inbound' ),
					'desc'     => esc_html__( 'Enter the button caption or call to action here.', 'inbound' ),
					'id'       => 'action',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Call to Action Link URL', 'inbound' ),
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
					//'desc' => esc_html__( 'Select, whether this link should be opened in a new window.', 'inbound' ),
					'id'       => 'link_target',
					'type'     => 'checkbox',
					'class'    => 'widefat',
					'std'      => 0,
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Add "nofollow" attribute to link.', 'inbound' ),
					'desc'     => esc_html__( 'Instruct search engines not to follow this link.', 'inbound' ),
					'id'       => 'nofollow',
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
					'group'     => 'button',
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

			$id = inbound_get_widget_uid( 'cta-box' );

			$size  = inbound_array_option( 'size', $instance, 0);
			if ( $size && is_array ( $size ) && ! empty ( $size ) ) {
				$size = $size['number'] . 'px';
			} else {
				$size = 13;
			}

			// custom css
			$style = '.' . $id . ' p { font-size:' . $size . "; }\n";
			inbound_add_custom_style( 'cta-box', $style );

			// button position
			$cta_position = inbound_array_option( 'position', $instance, 'left' );


			// nofollow attribute
			if ( inbound_array_option( 'nofollow', $instance, false ) ) {
				$nofollow = ' rel="nofollow"';
			} else {
				$nofollow = '';
			}

			// button style
			$button_style_uid = inbound_array_option( 'style', $instance, false );
			$button_style     = 'button-style-none';
			if ( $button_style_uid ) {
				$button_style_attrs = inbound_get_button_style( $button_style_uid );
				if ( $button_style_attrs ) {
					$button_style = 'button-style-' . $button_style_uid . ' inbound-' . $button_style_attrs['default']['background_mode'] . ' inbound-' . $button_style_attrs['hover']['background_mode'];
				}
			}

			// link
			$link = esc_url ( inbound_array_option( 'link', $instance, '#' ) );
			if ( empty ($link) ) $link = "#";

			// llink tagets
 			$link_target = inbound_array_option( 'link_target', $instance, 0 );

			if ( $link_target == 1 ) {
				$link_target = '_blank';
			} else {
				$link_target = '_self';
			}


			// modal window
			$modal     = inbound_array_option( 'modal', $instance, false );
			$link_data = '';
			if (defined('INBOUND_MODALS' ) ) {
				if ( $modal ) {
					inbound_add_modal( $modal );
					$link_data = ' data-featherlight="#modal-' . $modal . '" data-featherlight-variant="modal-style-' . $modal . '"';
					$link      = 'javascript:void(0);';
				}
			}

			// button HTML code
			$cta = '<div class="cta-link button_type_single"><a class="' . $button_style . '" href="' . esc_attr ( $link ) . '" target="' . esc_attr ( $link_target ) . '" title="' . esc_attr( inbound_array_option( 'action', $instance, '#' ) ) . '"' . $nofollow . $link_data . '>' . inbound_array_option( 'action', $instance, '#' ) . '</a></div>';

			$out .= '<div class="cta-box ' . $id . ' cta-position-' . $cta_position . '">';

			if ( $cta_position == "above" || $cta_position == "left" ) {
				$out .= $cta;
			}

			$out .= '<div class="cta-box-text">';

			$out .= '<p>' . inbound_esc_html( inbound_array_option( 'text', $instance ), false, true ) . '</p>';

			$out .= '</div>';

			if ( $cta_position == "below" || $cta_position == "right" ) {
				$out .= $cta;
			}

			$out .= '</div>';

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_cta_box_widget' ) ) {
		function register_inbound_cta_box_widget() {
			register_widget( 'Inbound_CTA_Box_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_cta_box_widget', 1 );
	}
}