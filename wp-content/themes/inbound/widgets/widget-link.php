<?php
/**
 * Plugin Name: Inbound Link
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a text link.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Link_Widget' ) ) {
	class Inbound_Link_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Link', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a text link.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-link' )
			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(

				array(
					'name'     => esc_html__( 'Link Text', 'inbound' ),
					'id'       => 'caption',
					'type'     => 'text',
					'desc'     => esc_html__( 'This is the actual anchor text.', 'inbound' ),
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Font Size', 'inbound' ),
					'desc'     => esc_html__( 'Define the font size for this link in px.', 'inbound' ),
					'id'       => 'font_size',
					'units'    => array('px'),
					'type'     => 'number',
					'class'    => 'widefat',
					'std'      => 13,
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Icon', 'inbound' ),
					'desc'     => esc_html__( 'Select an icon for this item.', 'inbound' ),
					'id'       => 'icon',
					'type'     => 'fontawesome',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Icon Position', 'inbound' ),
					'desc'     => esc_html__( 'Select where you would like the icon to be displayed.', 'inbound' ),
					'id'       => 'icon_position',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Left of caption', 'inbound' ),
							'value' => 'left'
						),
						array(
							'name'  => esc_html__( 'Right of caption', 'inbound' ),
							'value' => 'right'
						)
					),
					'std'      => 'left',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Link URL', 'inbound' ),
					'desc'     => esc_html__( 'Define a URL to point this link to.', 'inbound' ),
					'id'       => 'link',
					'type'     => 'text',
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
					'id'       => 'link_nofollow',
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
			$is_pagebuilder = inbound_is_pagebuilder( $args );

			/*
			 * Captions
			 */
			$caption = inbound_esc_html( inbound_array_option( 'caption', $instance, '' ), false, true );

			/*
			 * Links
			 */
			$link = esc_url( inbound_array_option( 'link', $instance, '' ) );

			/*
			 * Link Tagets
			 */
			$link_target = inbound_array_option( 'link_target', $instance, 0 );
			/*
			 * No Follow
			 */
			if ( inbound_array_option( 'link_nofollow', $instance, false ) ) {
				$link_nofollow = ' rel="nofollow"';
			} else {
				$link_nofollow = '';
			}

			/*
			 * Modal Windows
			 */
			$modal = inbound_array_option( 'modal', $instance, false );

			$link_data = '';

			if (defined('INBOUND_MODALS' ) ) {
				if ( $modal ) {
					inbound_add_modal( $modal );
					$link_data = ' data-featherlight="#modal-' . $modal . '" data-featherlight-variant="modal-style-' . $modal . '"';
					$link      = 'javascript:void(0);';
				}
			}


			/*
			 * Output
			 */
			$id = inbound_get_widget_uid( 'link' );


			$size  = inbound_array_option( 'font_size', $instance, 0);
			if ( $size && is_array ( $size ) && ! empty ( $size ) ) {
				$size = $size['number'] . 'px';
			} else {
				$size = 0;
			}


			if ( $size > 0 ) {
				$style = '#' . $id . ' { font-size:' . $size . '; }';
				inbound_add_custom_style( 'link', $style );
			}


			$icon = inbound_array_option( 'icon', $instance, '' );

			$icon_position = inbound_array_option( 'icon_position', $instance, 'left' );

			$add_classes = "";
			$out         = $args['before_widget'];

			$type = inbound_array_option( 'type', $instance, 'single' );


			if ( $link_target == 1 ) {
				$link_target = '_blank';
			} else {
				$link_target = '_self';
			}

			// Link output
			$button_style_class = "text-link";

			// Left
			if ( ! empty( $caption ) ) {
				if ( ! empty( $icon ) && $icon_position == "left" ) {
					$button_style_class .= ' link-icon-left';
				}
				if ( ! empty( $icon ) && $icon_position == "right" ) {
					$button_style_class .= ' link-icon-right';
				}

				if ( $link ) {
					// link URLs are escaped as such while retrieved from database
					$out .= '<a href="' . esc_attr( $link ) . '" id="' . $id . '" class="' . $button_style_class . '" target="' . esc_attr ( $link_target ) . '"' . $link_nofollow . $link_data . '>';
				}
				if ( ! empty( $icon ) && $icon_position == "left" ) {
					$out .= '<i class="fa ' . esc_attr( $icon ) . '"></i> ';
				}
				$out .= $caption;
				if ( ! empty( $icon ) && $icon_position == "right" ) {
					$out .= ' <i class="fa ' . esc_attr( $icon ) . '"></i>';
				}
				if ( $link ) {
					$out .= '</a>';
				}
			}

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_link_widget' ) ) {
		function register_inbound_link_widget() {
			register_widget( 'Inbound_Link_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_link_widget', 1 );
	}
}