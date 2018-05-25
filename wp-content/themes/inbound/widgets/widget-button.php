<?php
/**
 * Plugin Name: Inbound Button
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a button
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Button_Widget' ) ) {
	class Inbound_Button_Widget extends SR_Widget {

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
				'label'       => esc_html__( 'INB Button', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a button.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-button' )
			);

			// Tab groups
			$args['groups'] = array(
				'button' => esc_html__( 'Button', 'inbound' ),
				'hint'   => esc_html__( 'Hint', 'inbound' ),
			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(
				/*
				 * Other Options
				 */
				array(
					'name'     => esc_html__( 'Hint or Notice', 'inbound' ),
					'desc'     => esc_html__( 'Optional hint to be displayed below the button.', 'inbound' ),
					'id'       => 'hint',
					'group'    => 'hint',
					'type'     => 'textarea',
					'rows'     => '3',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => 'esc_textarea'
				),
				/*
				 * Left Button
				 */
				array(
					'name'     => esc_html__( 'Style', 'inbound' ),
					'desc'     => esc_html__( 'You can create and edit button styles via the theme\'s options panel.', 'inbound' ),
					'id'       => 'style',
					'group'    => 'button',
					'type'     => 'select',
					'fields'   => $styles,
					'std'      => $std_style,
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Button Caption', 'inbound' ),
					'id'       => 'caption',
					'group'    => 'button',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Icon', 'inbound' ),
					'desc'     => esc_html__( 'Select an icon for this item.', 'inbound' ),
					'group'    => 'button',
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
					'group'    => 'button',
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
					'name'     => esc_html__( 'Button Link URL', 'inbound' ),
					'desc'     => esc_html__( 'Define a link to point this button to.', 'inbound' ),
					'id'       => 'link',
					'group'    => 'button',
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
					'group'    => 'button',
					'type'     => 'checkbox',
					'class'    => 'widefat',
					'std'      => 0,
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Add "nofollow" attribute to link.', 'inbound' ),
					'id'       => 'link_nofollow',
					'group'    => 'button',
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
			$is_pagebuilder = inbound_is_pagebuilder( $args );

			/*
			 * Captions
			 */
			$caption = inbound_array_option( 'caption', $instance, '' );

			/*
			 * Links
			 */
			$link = esc_url( inbound_array_option( 'link', $instance, '' ) );
			if ( empty ($link) ) $link = "#";

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
			$id = inbound_get_widget_uid( 'button' );

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

			$button_style_class = '';

			if ( $button_style_tag = inbound_array_option( 'style', $instance, false ) ) {
				$button_style_class = 'button-style-' . $button_style_tag;
				$button_style       = inbound_get_button_style( $button_style_tag );
				if ( $button_style ) {
					$button_style_class .= ' inbound-' . $button_style['default']['background_mode'] . ' inbound-' . $button_style['hover']['background_mode'];
				}
			} else {
				$button_style = 'button-style-none';
			}

			// Link output

			// Left
			if ( ! empty( $caption ) ) {
				if ( ! empty( $icon ) && $icon_position == "left" ) {
					$button_style_class .= ' button-icon-left';
				}
				if ( ! empty( $icon ) && $icon_position == "right" ) {
					$button_style_class .= ' button-icon-right';
				}

				if ( $link ) {
					// link URLs are escaped as such while retrieved from database
					$out .= '<a href="' . esc_attr( $link ) . '" class="' . esc_attr( $button_style_class ) . '" target="' . esc_attr ( $link_target ) . '"' . $link_nofollow . $link_data . '>';
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


			if ( isset( $instance['hint'] ) && trim( $instance['hint'] ) != "" ) {
				$out .= '<p class="hint">' . inbound_esc_html( inbound_array_option( 'hint', $instance, '' ), false, true ) . '</p>';
			}


			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_button_widget' ) ) {
		function register_inbound_button_widget() {
			register_widget( 'Inbound_Button_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_button_widget', 1 );
	}
}