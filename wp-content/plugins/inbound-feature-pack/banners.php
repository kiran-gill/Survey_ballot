<?php
/*
 * Provides the flexible banners feature for the Inbound for WordPress theme.
 */

define('INBOUND_BANNERS', true);

/*
 * 	Set up banner content type
 */
if ( ! function_exists( 'inbound_banner_setup' ) ) {
	function inbound_banner_setup() {
		if ( ! function_exists ( 'inbound_option' ) ) return false;

		$banners = new SR_Custom_Post_Type(
			'banner',
			array(
				'supports'            => array(
					'title',
					'editor',
					'revisions'
				),
				'menu_position'       => 311,
				'public'              => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => false
			)
		);
	}
}
add_action( 'inbound_admin_setup_after_content_types', 'inbound_banner_setup', 2 );


/*
 * Retrieve and prepare banner settings
 */
if ( ! function_exists( 'inbound_get_the_banner' ) ) {
	function inbound_get_the_banner() {
		global $inbound_options;

		$banner_id           = false;
		$banner_content_mode = null;
		$banner_content      = null;
		$banner_options      = null;
		$custom_banner_id    = false;

		$this_post_type = get_post_type();


		if ( is_page() || is_single() ) {
			if ( $this_post_type == "banner" ) {
				$custom_banner_id = get_the_ID();
			} else {
				$custom_banner_id_from_post = get_post_meta( get_the_ID(), 'sr_inbound_custom_banner', true );
				if ( $custom_banner_id_from_post ) {
					$custom_banner_id = $custom_banner_id_from_post;
				}
			}
		}

		// Exceptions for WooCommerce

		// Custom Banner for Product Category Archives
		if ( $custom_banner_id == false && function_exists( 'is_product_category' ) && is_product_category() ) {
			global $wp_query;
			$category_name = $wp_query->query_vars['product_cat'];
			if ( $category_name ) {
				$category_object                = get_term_by( 'slug', $category_name, 'product_cat' );
				$category_id                    = $category_object->term_id;
				$woocommerce_category_banner_id = $saved_data = get_tax_meta( $category_id, 'sr_inbound_category_banner' );
				if ( intval( $woocommerce_category_banner_id ) != 0 ) {
					$custom_banner_id = intval( $woocommerce_category_banner_id );
				}
			}
		}

		// Custom Banner for All WooCommerce Pages
		if ( $custom_banner_id == false && function_exists( 'is_woocommerce' ) && inbound_is_woocommerce() ) { // if WooCommerce installed and active, and this is a WC page
			$woocommerce_default_banner = inbound_option( 'default_banner_woocommerce' ); // retrieve default banner
			if ( intval( $woocommerce_default_banner ) != 0 ) { // if banner set
				$custom_banner_id = intval( $woocommerce_default_banner );
			}
		}


		// Exceptions for Blog Pages

		/*
		// Custom Banner for Individual Posts
		if ( $custom_banner_id == false && inbound_is_blog() && is_single() ) { // if this is a blog page and a custom banner is selected for the post
			$custom_banner_id_from_post = get_post_meta( get_the_ID(), 'sr_inbound_custom_banner', true );
			if ( intval( $custom_banner_id_from_post ) != 0 ) { // if banner set
				$custom_banner_id = intval( $custom_banner_id_from_post );
			}
		}
		*/


		// Custom Banner for Category
		if ( $custom_banner_id == false && inbound_is_blog() && is_category() ) {
			$category_id = get_query_var( 'cat' );
			if ( $category_id ) {
				$blog_category_banner_id = $saved_data = get_tax_meta( $category_id, 'sr_inbound_category_banner' );
				if ( intval( $blog_category_banner_id ) != 0 ) {
					$custom_banner_id = intval( $blog_category_banner_id );
				}
			}
		}

		// Custom Banner for All Blog Pages
		if ( $custom_banner_id == false && inbound_is_blog() ) { // if this is a blog page and a custom banner is selected for the blog
			$blog_default_banner = inbound_option( 'default_banner_blog' ); // retrieve default banner
			if ( intval( $blog_default_banner ) != 0 ) { // if banner set
				$custom_banner_id = intval( $blog_default_banner );
			}
		}


		$full_width = false;

		if ( $custom_banner_id ) {
			$banner_id = $custom_banner_id;
		} else {
			$banner_id = inbound_option( 'default_banner', false );
		}

		if ( $banner_id ) {
			if (class_exists('SitePress')) { // adjust ID for WPML
				$banner_id = apply_filters( 'wpml_object_id', $banner_id, 'banner', true );
			}
		}

		if ( $banner_id ) { // a banner is defined, either through the profile, or as a custom banner

			$banner_options      = get_post_custom( $banner_id ); // retrieve meta options for that banner
			$banner_content_mode = inbound_custom_value( 'sr_inbound_banner_content_mode', $banner_options, 'content' );
			$custom_content      = inbound_custom_value( 'sr_inbound_banner_content', $banner_options );
			$full_width          = inbound_custom_value( 'sr_inbound_banner_full_width', $banner_options );
			$background          = inbound_custom_value( 'sr_inbound_background_mode', $banner_options );

			if ( $banner_content_mode == "custom" && $custom_content ) { // custom content
				$banner_content = do_shortcode( $custom_content );
			} elseif ( $banner_content_mode == "metaslider" ) {
				$slider_id = inbound_custom_value( 'sr_inbound_metaslider', $banner_options );
				if ( $slider_id != 0 ) { // if a slider has been selected
					$banner_content = do_shortcode( '[metaslider id=' . $slider_id . ']' );
				}
			} elseif ( $banner_content_mode == "revslider" ) {
				$slider_id = inbound_custom_value( 'sr_inbound_revslider', $banner_options );
				if ( $slider_id != 0 ) { // if a slider has been selected
					ob_start();
					putRevSlider( $slider_id );
					$banner_content = ob_get_contents();
					ob_clean();
					ob_end_clean();
				}
			} else { // page content, and if page builder is used, page builder content
				if ( function_exists( 'siteorigin_panels_render' ) ) {
					if ( inbound_panels_is_live_editor() && $this_post_type != "banner" ) {
						$banner_content = '<div class="panel-grid visual-editor-banner"><h2>' . esc_html__( 'Banner Location - Edit in Banner Editor', 'inbound' ) . '</h2></div>';
					} else {
						/* Include script to stretch banner */
						if ( function_exists( 'siteorigin_panels_default_styles_register_scripts' ) ) {
							siteorigin_panels_default_styles_register_scripts();
						}

						/* Pre-render banner content */
						$banner_content = siteorigin_panels_render( $banner_id, true, false );
						$banner_content = preg_replace( '/id="(panel)-([\d+])-([\d+])-([\d+])"/', '/id="banner-panel-${2}-${3}-${4}"/', $banner_content );
					}
				} else {
					$p = get_post( $banner_id );
					if ( isset ( $p->post_content ) ) {
						$banner_content = $p->post_content;
					} else {
						$banner_content = null;
					}
				}
			}

			$banner_content = apply_filters( 'inbound_get_banner', $banner_content );

			// meta information and banner settings
			$inbound_options['the_banner_content_mode'] = $banner_content_mode;
			$inbound_options['the_banner_content']      = $banner_content;
			$inbound_options['the_banner_id']           = $banner_id;
			$inbound_options['the_banner_options']      = $banner_options;
			$inbound_options['the_banner_full_width']   = $full_width;
			$inbound_options['the_banner_background']   = $background;

			// special cases where banner contains slider
			if ( $banner_content_mode == 'revslider' || $banner_content_mode == 'metaslider' ) {
				$inbound_options['the_banner_has_slider'] = true;
			}

			// get font settings
			$font_banner_title     = inbound_custom_value( 'sr_inbound_font_banner_title', $banner_options, false );
			$font_banner_sub_title = inbound_custom_value( 'sr_inbound_font_banner_sub_title', $banner_options, false );
			$font_banner_text      = inbound_custom_value( 'sr_inbound_font_banner_text', $banner_options, false );

			// apply font settings to global options array
			$inbound_options['font_banner_title']     = $font_banner_title;
			$inbound_options['font_banner_sub_title'] = $font_banner_sub_title;
			$inbound_options['font_banner_text']      = $font_banner_text;
		}
	}
}
if ( ! is_admin() ) {
	add_action( 'wp', 'inbound_get_the_banner', 99999 ); // we retrieve the banner options very late
}


if ( ! function_exists( 'inbound_update_banner' ) ) {
	function inbound_update_banner( $post_id ) {
		if ( isset( $_REQUEST['post_type'] ) && "banner" == $_REQUEST['post_type'] ) {
			// banner was updated, so delete any cached versions of this banner
			delete_transient( 'sr_inbound_banner_content_' . $post_id );
		}

	}
}
add_action( 'save_post', 'inbound_update_banner' );

/*
 * Template tags and custom banner classes
 */

if (! function_exists('inbound_banner_wrapper_classes')) {
	function inbound_banner_wrapper_classes() {
		$classes=array();
		$classes[] = "animated-header";
		if (count($classes) > 0) {
			echo ' class="'.implode(" ", $classes).'"';
		}
	}
}

if (! function_exists('inbound_banner_data_attrs')) {
	function inbound_banner_data_attrs() {
		if (inbound_option('the_banner_background') == "image-parallax") {
			echo ' data-top="background-position:0px 0%;" data-top-bottom="background-position:0px 100%;"';
		} else {
			return;
		}
	}
}

if ( ! function_exists ('inbound_the_banner' ) ) {
	function inbound_the_banner() {
		$banner_content = inbound_option('the_banner_content');

		$classes = array();
		$add_classes = '';
		if (!inbound_option('the_banner_full_width')) $classes[] = 'row';

		$classes[] = 'banner-mode-' . inbound_option('the_banner_content_mode', 'content');

		if (count($classes) > 0) {
			$add_classes = ' class="' . implode (' ', $classes) . '"';
		}

		if ($banner_content) {
			echo "<!--Banner Content-->\n";
			echo '<div id="banner_content"' . $add_classes . '>' . "\n";
			echo $banner_content;
			echo '</div>' . "\n";
			echo "<!--End Banner Content-->\n";
		}
	}
}

/*
 * Query functions
*/

/*
 * Get all banners
 */

if ( ! function_exists( 'inbound_get_banners' ) ) {
	function inbound_get_banners() {
		$banners = false;
		$args    = array(
			'post_type'        => 'banner',
			'post_status'      => 'publish',
			'posts_per_page'   => - 1,
			'caller_get_posts' => 1
		);

		$banner_query = null;
		$banner_query = new WP_Query( $args );
		if ( $banner_query->have_posts() ) {
			$banners = array();
			while ( $banner_query->have_posts() ) {
				$banner_query->the_post();
				$the_id             = get_the_ID();
				$banner_options     = get_post_custom( $the_id );
				$banners[ $the_id ] = array(
					'options' => $banner_options,
					'title'   => get_the_title()
				);
			}

		}
		wp_reset_query();

		return $banners;
	}
}
