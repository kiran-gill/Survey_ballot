<?php
/**
 * Plugin Name: Inbound Portolio Item
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays an image as a portfolio item.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Portfolio_Item_Widget' ) ) {
	class Inbound_Portfolio_Item_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Portfolio Item', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays an image as a portfolio item.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-portfolio-item' )

			);

			// Prepare image sizes
			$image_sizes = array(
				array(
					'name'  => esc_html__( 'Default', 'inbound' ),
					'value' => ''
				),
				array(
					'name'  => esc_html__( 'Large', 'inbound' ),
					'value' => 'large'
				),
				array(
					'name'  => esc_html__( 'Medium', 'inbound' ),
					'value' => 'medium'
				),
				array(
					'name'  => esc_html__( 'Thumbnail', 'inbound' ),
					'value' => 'thumbnail'
				),
				array(
					'name'  => esc_html__( 'Full', 'inbound' ),
					'value' => 'full'
				)
			);

			global $_wp_additional_image_sizes;
			if ( ! empty( $_wp_additional_image_sizes ) ) {
				foreach ( $_wp_additional_image_sizes as $name => $info ) {
					$image_sizes[] = array(
						'name'  => ucwords( strtolower( strtr( $name, "-_", "  " ) ) ),
						'value' => $name
					);
				}
			}

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
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Description', 'inbound' ),
					'desc'     => esc_html__( 'Enter a description or any additional text or mark-up here.', 'inbound' ),
					'id'       => 'description',
					'type'     => 'textarea',
					'rows'     => '5',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Title and description only on hover', 'inbound' ),
					'desc'     => esc_html__( 'If this option is checked, title and description will only be displayed if the user moves the mouse cursor over the item.', 'inbound' ),
					'id'       => 'text_on_hover',
					'type'     => 'checkbox',
					'class'    => 'widefat',
					'std'      => 0,
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'  => esc_html__( 'Image', 'inbound' ),
					'desc'  => esc_html__( 'Upload or select an image.', 'inbound' ),
					'class' => 'img',
					'id'    => 'image',
					'type'  => 'image',
					'std'   => '',
					'validate' => 'numeric',
					'filter' => ''
				),
				array(
					'name'     => esc_html__( 'Thumbnail Image Size', 'inbound' ),
					'desc'     => esc_html__( 'Select a size. All sizes available to WordPress are displayed.', 'inbound' ),
					'id'       => 'thumbnail_size',
					'type'     => 'select',
					'fields'   => $image_sizes,
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Link Type', 'inbound' ),
					'desc'     => esc_html__( 'Select where this link should point to.', 'inbound' ),
					'id'       => 'link_type',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Open URL in browser', 'inbound' ),
							'value' => 'external'
						),
						array(
							'name'  => esc_html__( 'Open lightbox and display image in full size', 'inbound' ),
							'value' => 'lightbox'
						),
					),
					'std'      => 'external',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Link URL', 'inbound' ),
					'desc'     => esc_html__( 'Enter an optional URL to link the image to.', 'inbound' ),
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
					'id'       => 'link_target',
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

			$out = $args['before_widget'];

			if ( inbound_array_option( 'title', $instance, false ) && ! inbound_is_pagebuilder() ) {
				$out .= $args['before_title'];
				$out .= esc_html( $instance['title'] );
				$out .= $args['after_title'];
			}

			$image = inbound_array_option( 'image', $instance );
			$thumbnail_size = inbound_array_option( 'thumbnail_size', $instance, "large" );

			$type        = inbound_array_option( 'link_type', $instance, "external" );
			$link        = '';
			$link_target = '';
			$link_class  = '';

			if ( $type == "external" ) {
				$link_class  = 'link-external';
				$link        = esc_url( trim( inbound_array_option( 'link', $instance, '' ) ) );
				$link_target = inbound_array_option( 'link', $instance, false );
				if ( $link_target == 1 ) {
					$link_target = ' target="_blank"';
				}
			} elseif ( $type == "lightbox" ) {
				$link_class  = 'link-lightbox';
				$link        = 'javascript:void(0);';
				$link_target = '';
			}

			if ( $image ) {
				$image_src = wp_get_attachment_image_src( intval( $image ), "full" );
				if ($thumbnail_size == "full") {
					$thumbnail_image_src = $image_src;
				} else {
					$thumbnail_image_src = wp_get_attachment_image_src( intval( $image ), $thumbnail_size );
				}

				if ( $image_src ) {
					$title = inbound_array_option( 'title', $instance, false );
					if ( $title ) {
						$title_alt = ' alt="' . esc_attr( $title ) . '"';
					} else {
						$attachment  = get_post( intval( $image ) );
						$image_title = $attachment->post_title;

						$title_alt = ' alt="' . esc_attr( $image_title ) . '"';
					}

					$description = inbound_esc_html( inbound_array_option( 'description', $instance, false ), false, true );

					$show_text_on_hover = inbound_array_option( 'text_on_hover', $instance, false );
					if ( $show_text_on_hover ) {
						$on_hover = ' text-on-hover';
					} else {
						$on_hover = '';
					}

					$link_type = inbound_array_option( 'link_type', $instance, 'external' );
					if ( $link_type == "lightbox" ) {
						$link = $image_src[0];
					}

					$out .= '<div class="portfolio-item image-' . $on_hover . '">';

					if ( $link && $link != '' ) {
						// link URLs are escaped as such while retrieved from database
						$out .= '<a href="' . esc_attr( $link ) . '"' . $link_target . ' class="' . $link_class . '">';
					}

					$out .= '<div class="portfolio-image">';
					$out .= '<img src="' . esc_url( $thumbnail_image_src[0] ) . '"' . $title_alt . '>';
					$out .= '</div>';

					if ( ! empty( $title ) || ! empty( $description ) ) {
						$out .= '<div class="portfolio-text">';
						if ( ! empty( $title ) ) {
							$out .= '<h4>' . esc_html( $title ) . '</h4>';
						}
						if ( ! empty( $description ) ) {
							$out .= '<p>' . $description . '</p>';
						}
						$out .= '</div>';
					}

					if ( ! empty( $link ) ) {
						$out .= '</a>';
					}

					$out .= '</div>';
				}
			}

			$out .= $args['after_widget'];

			echo $out;

		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_portfolio_item_widget' ) ) {
		function register_inbound_portfolio_item_widget() {
			register_widget( 'Inbound_Portfolio_Item_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_portfolio_item_widget', 1 );
	}
}