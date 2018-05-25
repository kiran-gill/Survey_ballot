<?php
/**
 * Plugin Name: Inbound Icon Block
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a block with an icon, a headline and a description.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Icon_Block_Widget' ) ) {
	class Inbound_Icon_Block_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Icon Block', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a block with an icon, a headline and a description.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-icon-block' )
			);

			// Configure the widget fields
			// Tab groups
			$args['groups'] = array(
				'general' => esc_html__( 'General', 'inbound' ),
				'link'    => esc_html__( 'Link', 'inbound' ),
			);


			// fields array
			$args['fields'] = array(
				array(
					'name'     => esc_html__( 'Title', 'inbound' ),
					'desc'     => esc_html__( 'Title or headline for this item.', 'inbound' ),
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
					'name'     => esc_html__( 'Icon', 'inbound' ),
					'desc'     => esc_html__( 'Select an icon for this item.', 'inbound' ),
					'id'       => 'icon',
					'group'    => 'general',
					'type'     => 'fontawesome',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Icon Color', 'inbound' ),
					'desc'     => esc_html__( 'Select a icon color.', 'inbound' ),
					'id'       => 'icon_color',
					'group'    => 'general',
					'type'     => 'color',
					// class, rows, cols
					'std'      => '#000000',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'  => esc_html__( 'Custom Icon Image', 'inbound' ),
					'desc'  => esc_html__( 'Upload an image to be displayed instead of an icon font icon.', 'inbound' ),
					'class' => 'img',
					'id'    => 'custom_icon',
					'group' => 'general',
					'type'  => 'image',
					'std'   => '',
					//'validate' => '',
					//'filter' => ''
				),
				array(
					'name'     => esc_html__( 'Icon Size', 'inbound' ),
					'desc'     => esc_html__( 'Select the size for the icon you would like to display.', 'inbound' ),
					'id'       => 'icon_size',
					'group'    => 'general',
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
					'name'     => esc_html__( 'Icon Position', 'inbound' ),
					'desc'     => esc_html__( 'Select where you would like the icon to be displayed.', 'inbound' ),
					'id'       => 'icon_position',
					'group'    => 'general',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Left of description', 'inbound' ),
							'value' => 'left'
						),
						array(
							'name'  => esc_html__( 'Right of description', 'inbound' ),
							'value' => 'right'
						),
						array(
							'name'  => esc_html__( 'Above description', 'inbound' ),
							'value' => 'above'
						),
						array(
							'name'  => esc_html__( 'No Icon', 'inbound' ),
							'value' => 'none'
						)
					),
					'std'      => 'left',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Description', 'inbound' ),
					'desc'     => esc_html__( 'Description for this item.', 'inbound' ),
					'id'       => 'description',
					'group'    => 'general',
					'type'     => 'textarea',
					'rows'     => '5',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Link Text', 'inbound' ),
					'desc'     => esc_html__( 'This is the actual anchor text.', 'inbound' ),
					'id'       => 'link_caption',
					'group'    => 'link',
					'type'     => 'text',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Link URL', 'inbound' ),
					'desc'     => esc_html__( 'Define a link to point this button to.', 'inbound' ),
					'id'       => 'link',
					'group'    => 'link',
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
					'group'    => 'link',
					'type'     => 'checkbox',
					'class'    => 'widefat',
					'std'      => 0,
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Add "nofollow" attribute to link.', 'inbound' ),
					'id'       => 'link_nofollow',
					'group'    => 'link',
					'type'     => 'checkbox',
					'class'    => 'widefat',
					'std'      => 0,
					'validate' => 'alpha_dash',
					'filter'   => ''
				),

			); // fields array

			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {
			$is_pagebuilder = inbound_is_pagebuilder( $args );
			$id             = inbound_get_widget_uid( 'icon-block' );

			$add_classes = "";
			$out         = $args['before_widget'];

			$icon_color = inbound_array_option( 'icon_color', $instance, '#000000' );
			if ( ! empty( $icon_color ) ) {
				$style = '.' . $id . ' .icon { color:' . $icon_color . '; }';
				inbound_add_custom_style( 'icon-block', $style );
			}


			$icon_position = inbound_array_option( 'icon_position', $instance, 'none' );
			$custom_icon   = inbound_array_option( 'custom_icon', $instance );
			$icon_size     = inbound_array_option( 'icon_size', $instance, '4x' );

			$icon = '';

			if ( $custom_icon ) {
				$image_src = wp_get_attachment_image_src( intval( $custom_icon ), "full" );

				if ( $image_src ) {
					$title = inbound_array_option( 'title', $instance, false );
					if ( $title ) {
						$title_alt = ' alt="' . esc_attr( $title ) . '"';
					} else {
						$attachment  = get_post( intval( $custom_icon ) );
						$image_title = $attachment->post_title;

						$title_alt = ' alt="' . esc_attr( $image_title ) . '"';
					}

					$icon = '<img src="' . esc_url( $image_src[0] ) . '"' . $title_alt . '>';
				}
			} else {
				$icon = '<i class="fa ' . $instance['icon'] . ' fa-' . esc_attr( $icon_size ) . '"></i>';
			}


			$block_title = inbound_array_option( 'title', $instance, "" );
			if ( trim( $block_title ) != "" ) {
				$block_title = '<h4>' . inbound_esc_html ( $block_title ) . '</h4>';
			}


			/*
			 * Captions
			 */
			$caption = inbound_esc_html ( inbound_array_option( 'link_caption', $instance, '' ), false , true );
			$description = inbound_esc_html ( inbound_array_option( 'description', $instance, '' ), false , true );

			/*
			 * Links
			 */
			$link = inbound_array_option( 'link', $instance, '' );

			/*
			 * Link Tagets
			 */
			$link_target = inbound_array_option( 'link_target', $instance, 0 );
			if ( empty( $link_target ) ) {
				$link_target = "_self";
			}
			$link_data   = '';

			/*
			 * No Follow
			 */
			if ( inbound_array_option( 'link_nofollow', $instance, false ) ) {
				$link_nofollow = ' rel="nofollow"';
			} else {
				$link_nofollow = '';
			}


			if ( $icon_position == "left" ) {
				$out .= '<div class="icon-block style-icon-left icon-size-' . $icon_size . ' ' . $id . '"><div class="icon">' . $icon . '</div><div class="icon-block-description">' . $block_title . '<p>' . $description . '</p>';
				if ( ! empty( $caption ) ) {
					$out .= '<a href="' . esc_url( $link ) . '" target="' . $link_target . '"' . $link_nofollow . $link_data . '>';
					$out .= $caption;
					$out .= '</a>';
				}
				$out .= '</div></div>';
			} elseif ( $icon_position == "right" ) {
				$out .= '<div class="icon-block style-icon-right icon-size-' . $icon_size . ' ' . $id . '"><div class="icon-block-description">' . $block_title . '<p>' . $description . '</p>';
				if ( ! empty( $caption ) ) {
					$out .= '<a href="' . esc_url( $link ) . '" target="' . $link_target . '"' . $link_nofollow . $link_data . '>';
					$out .= $caption;
					$out .= '</a>';
				}
				$out .= '</div><div class="icon">' . $icon . '</div></div>';
			} elseif ( $icon_position == "above" ) {
				$out .= '<div class="icon-block style-icon-above icon-size-' . $icon_size . ' ' . $id . '"><div class="icon">' . $icon . '</div>' . $block_title . '<p>' . $description . '</p>';
				if ( ! empty( $caption ) ) {
					$out .= '<a href="' . esc_url( $link ) . '" target="' . $link_target . '"' . $link_nofollow . $link_data . '>';
					$out .= $caption;
					$out .= '</a>';
				}
				$out .= '</div>';
			} else { // no icon
				$out .= '<div class="icon-block style-icon-none ' . $id . '">' . $block_title . '<p>' . $description . '</p>';
				if ( ! empty( $caption ) ) {
					$out .= '<a href="' . esc_url( $link ) . '" target="' . $link_target . '"' . $link_nofollow . $link_data . '>';
					$out .= $caption;
					$out .= '</a>';
				}
				$out .= '</div>';
			}

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_icon_block_widget' ) ) {
		function register_inbound_icon_block_widget() {
			register_widget( 'Inbound_Icon_Block_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_icon_block_widget', 1 );
	}
}