<?php
/**
 * Plugin Name: Inbound Bio Block
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a testimonial.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Bio_Block_Widget' ) ) {
	class Inbound_Bio_Block_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Bio Block', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays picture, name, position and social icons to introduce a person.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-bio-block' )
			);

			// Tab groups
			$args['groups'] = array(
				'general' => esc_html__( 'General', 'inbound' ),
				'social'  => esc_html__( 'Contact Options', 'inbound' ),
			);

			// Configure the widget fields
			// fields array
			$args['fields'] = array(

				// Title field
				array(
					'name'  => esc_html__( 'Avatar/Picture', 'inbound' ),
					'desc'  => esc_html__( 'Upload an image to be displayed.', 'inbound' ),
					'class' => 'img',
					'id'    => 'avatar',
					'group' => 'general',
					'type'  => 'image',
					'std'   => '',
					//'validate' => '',
					//'filter' => ''
				),
				array(
					'name'     => esc_html__( 'Name', 'inbound' ),
					'desc'     => esc_html__( 'Enter this person\'s name.', 'inbound' ),
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
					'name'     => esc_html__( 'Job Title/Description/Company', 'inbound' ),
					'desc'     => esc_html__( 'Enter a job title, function and/or company name here.', 'inbound' ),
					'id'       => 'job_title',
					'group'    => 'general',
					'type'     => 'text',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Description', 'inbound' ),
					'desc'     => esc_html__( 'Enter a short description or introduction here, like a mini biography.', 'inbound' ),
					'id'       => 'description',
					'group'    => 'general',
					'type'     => 'textarea',
					'rows'     => '5',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Display details on hover', 'inbound' ),
					'desc'     => esc_html__( 'If this option is checked, details will only be displayed when the user hovers over the bio block.', 'inbound' ),
					'id'       => 'details_on_hover',
					'type'     => 'checkbox',
					'group'    => 'general',
					'class'    => 'widefat',
					'std'      => 0,
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Link URL', 'inbound' ),
					'desc'     => esc_html__( 'Define a URL to point this link to.', 'inbound' ),
					'id'       => 'link',
					'type'     => 'text',
					'group'    => 'general',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Website', 'inbound' ),
					'id'       => 'social_website',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Email', 'inbound' ),
					'id'       => 'social_email',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Facebook URL', 'inbound' ),
					'id'       => 'social_facebook',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Twitter URL', 'inbound' ),
					'id'       => 'social_twitter',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Google+ URL', 'inbound' ),
					'id'       => 'social_gplus',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'LinkedIn URL', 'inbound' ),
					'id'       => 'social_linkedin',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Instagram URL', 'inbound' ),
					'id'       => 'social_instagram',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Pinterest URL', 'inbound' ),
					'id'       => 'social_pinterest',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Flickr URL', 'inbound' ),
					'id'       => 'social_flickr',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Tumblr URL', 'inbound' ),
					'id'       => 'social_tumblr',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Foursquare URL', 'inbound' ),
					'id'       => 'social_foursquare',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'YouTube URL', 'inbound' ),
					'id'       => 'social_youtube',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Vimeo URL', 'inbound' ),
					'id'       => 'social_vimeo',
					'group'    => 'social',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),


			); // fields array

			$this->create_widget( $args );
		}


		// Output function
		function widget( $args, $instance ) {
			$out = $args['before_widget'];

			$avatar      = inbound_array_option( 'avatar', $instance, false );
			$name        = inbound_array_option( 'title', $instance, false );
			$position    = inbound_array_option( 'job_title', $instance, false );
			$description = inbound_array_option( 'description', $instance, false );

			$details_on_hover = inbound_array_option( 'details_on_hover', $instance, false );

			$link = inbound_array_option( 'link', $instance, false );

			if ( $details_on_hover ) $hover= ' details-on-hover'; else $hover =  '';

			$out .= '<div class="team-member' . $hover . '">';

			$out .= '<div class="team-header">';

			if ( ! empty ( $link ) ) {
				$out .= '<a href="' . esc_url( $link ) . '">';
			}

			if ( $avatar ) {
				$avatar = intval( $avatar );
				if ( $avatar != 0 ) {
					$avatar_image = wp_get_attachment_image_src( $avatar, 'inbound-bio-avatar' );
					if ( $avatar_image ) {
						$out .= '<div class="team-image"><img src="' . esc_url( $avatar_image[0] ) . '" alt="' . esc_attr( inbound_array_option( 'name', $instance, '' ) ) . '"></div>';
					}
				}
			}

			if ( $name || $position ) {
				$out .= '<div class="team-title">';
			}

			if ( $name ) {
				$out .= '<h3>' . esc_html( $name ) . '</h3>';
			}

			if ( $position ) {
				$out .= '<p class="team-position">' . esc_html( $position ) . '</p>';
			}

			if ( $name || $position ) {
				$out .= '</div>';
			}

			if ( ! empty ( $link ) ) {
				$out .= '</a>';
			}

			if ( $details_on_hover ) {
				$out .= $this->social_icons( $instance );
			}

			$out .= '</div>';

			if ( $description ) {
				$out .= '<p class="team-description">' . inbound_esc_html( $description, false, true ) . '</p>';
			}

			if ( ! $details_on_hover ) {
				$out .= $this->social_icons( $instance );
			}


			$out .= '</div>';

			$out .= $args['after_widget'];

			echo $out;
		}


		function social_icons ( $instance ) {
			$icon_size = 1;
			$social_facebook   = inbound_array_option( 'social_facebook', $instance, false );
			$social_twitter    = inbound_array_option( 'social_twitter', $instance, false );
			$social_gplus      = inbound_array_option( 'social_gplus', $instance, false );
			$social_linkedin   = inbound_array_option( 'social_linkedin', $instance, false );
			$social_instagram  = inbound_array_option( 'social_instagram', $instance, false );
			$social_pinterest  = inbound_array_option( 'social_pinterest', $instance, false );
			$social_flickr     = inbound_array_option( 'social_flickr', $instance, false );
			$social_tumblr     = inbound_array_option( 'social_tumblr', $instance, false );
			$social_foursquare = inbound_array_option( 'social_foursquare', $instance, false );
			$social_youtube    = inbound_array_option( 'social_youtube', $instance, false );
			$social_vimeo      = inbound_array_option( 'social_vimeo', $instance, false );

			$social_website = inbound_array_option( 'social_website', $instance, false );
			$social_email   = inbound_array_option( 'social_email', $instance, false );


			if (
				$social_website ||
				$social_email ||
				$social_facebook ||
				$social_twitter ||
				$social_gplus ||
				$social_linkedin ||
				$social_instagram ||
				$social_pinterest ||
				$social_flickr ||
				$social_tumblr ||
				$social_foursquare ||
				$social_youtube ||
				$social_vimeo
			) {
				$out = '<ul class="social-icons icon-size-1">';

				if ( $social_website ) {
					$out .= '<li>
					<a class="website" target="_blank" href="' . esc_url( $social_website ) . '">
					<i class="fa fa-home fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Website', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_email ) {
					$out .= '<li>
					<a class="email" target="_blank" href="mailto:' . esc_html( $social_email ) . '">
					<i class="fa fa-envelope fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Email', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_facebook ) {
					$out .= '<li>
					<a class="facebook" target="_blank" href="' . esc_url( $social_facebook ) . '">
					<i class="fa fa-facebook fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Facebook', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_twitter ) {
					$out .= '<li>
					<a class="twitter" target="_blank" href="' . esc_url( $social_twitter ) . '">
					<i class="fa fa-twitter fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Twitter', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_gplus ) {
					$out .= '<li>
					<a class="googleplus" target="_blank" href="' . esc_url( $social_gplus ) . '">
					<i class="fa fa-googleplus fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Google+', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_linkedin ) {
					$out .= '<li>
					<a class="linkedin" target="_blank" href="' . esc_url( $social_linkedin ) . '">
					<i class="fa fa-linkedin fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'LinkedIn', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_instagram ) {
					$out .= '<li>
					<a class="instagram" target="_blank" href="' . esc_url( $social_instagram ) . '">
					<i class="fa fa-instagram fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Instagram', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_pinterest ) {
					$out .= '<li>
					<a class="pinterest" target="_blank" href="' . esc_url( $social_pinterest ) . '">
					<i class="fa fa-pinterest fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Pinterest', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_flickr ) {
					$out .= '<li>
					<a class="flickr" target="_blank" href="' . esc_url( $social_flickr ) . '">
					<i class="fa fa-flickr fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Flickr', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_tumblr ) {
					$out .= '<li>
					<a class="tumblr" target="_blank" href="' . esc_url( $social_tumblr ) . '">
					<i class="fa fa-flickr fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Tumblr', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_foursquare ) {
					$out .= '<li>
					<a class="foursquare" target="_blank" href="' . esc_url( $social_foursquare ) . '">
					<i class="fa fa-foursquare fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Foursquare', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_youtube ) {
					$out .= '<li>
					<a class="youtube" target="_blank" href="' . esc_url( $social_youtube ) . '">
					<i class="fa fa-youtube fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'YouTube', 'inbound' ) . '</span></a>
				</li>';
				}

				if ( $social_vimeo ) {
					$out .= '<li>
					<a class="vimeo" target="_blank" href="' . esc_url( $social_vimeo ) . '">
					<i class="fa fa-vimeo-square fa-' . $icon_size . 'x"></i>
					<span>' . esc_html__( 'Vimeo', 'inbound' ) . '</span></a>
				</li>';
				}

				$out .= '</ul>';

			} else {
				$out = '';
			}

			return $out;
		}


	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_bio_block_widget' ) ) {
		function register_inbound_bio_block_widget() {
			register_widget( 'Inbound_Bio_Block_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_bio_block_widget', 1 );
	}
}