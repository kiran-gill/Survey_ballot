<?php
/**
 * Inbound for WordPress by ShapingRain.com
 * Author: ShapingRain.com
 * URL: http://www.shapingrain.com
 *
 * This file contains the admin panel for Inbound; as well as definitions
 * of custom fields etc.
 *
 * @package inbound
 */

// admin textdomain
add_action( 'after_setup_theme', 'inbound_admin_textdomain_setup' );
function inbound_admin_textdomain_setup() {
	load_theme_textdomain( 'inbound', get_template_directory() . '/languages' );
}

// helper function
function inbound_add_admin_menu_separator( $position ) {

	global $menu;
	$index = 0;

	foreach ( $menu as $offset => $section ) {
		if ( substr( $section[2], 0, 9 ) == 'separator' ) {
			$index ++;
		}
		if ( $offset >= $position ) {
			$menu[ $position ] = array( '', 'read', "separator{$index}", '', 'wp-menu-separator' );
			break;
		}
	}

	ksort( $menu );
}

function admin_menu_separator() {
	inbound_add_admin_menu_separator( 308 );
}

add_action( 'admin_menu', 'admin_menu_separator' );


add_action( 'after_setup_theme', 'inbound_init_admin' );
if ( ! function_exists( 'inbound_init_admin' ) ) {
	function inbound_init_admin() {
		// prefix for all options
		do_action( 'inbound_register_content_types' );
		do_action( 'inbound_admin_setup_after_content_types' );

		/*
		 * Profiles
		 */
		if ( is_admin() ) {

			/*
			 * Meta Box: Title Option
            */
			if ( function_exists( 'siteorigin_panels_render' ) ) {
				$config        = array(
					'id'             => 'inbound_pagebuilder',
					'title'          => __( 'Page Builder Options', 'inbound' ),
					'pages'          => array( 'page' ),
					'context'        => 'normal',
					'priority'       => 'high',
					'fields'         => array(),
					'local_images'   => false,
					'use_with_theme' => get_template_directory_uri() . '/lib/admin'
				);
				$meta_pbuilder = new SR_Meta_Box( $config );
				$meta_pbuilder->addCheckbox( 'sr_inbound_bypass_page_builder', array(
					'name'    => __( 'Bypass Page Builder', 'inbound' ),
					'caption' => __( 'Do not use page builder content', 'inbound' ),
					'std'     => false,
					'class'   => 'no-fancy',
					'desc'    => __( 'If this option is checked, page builder content will not be rendered and the editor contents will be used instead.', 'inbound' )
				) );

				$meta_pbuilder->Finish();
			}


			/*
			 * Meta Box: Title Option
			 */
			$config     = array(
				'id'             => 'inbound_title',
				'title'          => __( 'Title Options', 'inbound' ),
				'pages'          => array( 'page' ),
				'context'        => 'normal',
				'priority'       => 'high',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_title = new SR_Meta_Box( $config );
			$meta_title->addCheckbox( 'sr_inbound_hide_title', array(
				'name'    => __( 'Hide Title', 'inbound' ),
				'caption' => __( 'Hide title on this page', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the title header block will be hidden on this page.', 'inbound' )
			) );

			$meta_title->Finish();

			/*
			 * Meta Box: Layout Options
 			*/
			$config      = array(
				'id'             => 'inbound_layout',
				'title'          => __( 'Layout and Design', 'inbound' ),
				'pages'          => array( 'page' ),
				'context'        => 'normal',
				'priority'       => 'high',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_layout = new SR_Meta_Box( $config );
			$meta_layout->addPosts( 'sr_inbound_custom_banner', array( 'post_type' => 'banner' ), array(
				'class' => 'no-fancy',
				'name'  => __( 'Custom Banner', 'inbound' ),
				'desc'  => __( 'If a banner is selected, it will be displayed instead of the default banner selected for the active profile.', 'inbound' )
			) );
			$meta_layout->addCheckbox( 'sr_inbound_hide_header', array(
				'name'    => __( 'Hide Header', 'inbound' ),
				'caption' => __( 'Remove the header section from this page', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the header containing logo, site title, tagline, menu and social icons, will be hidden on this page.', 'inbound' )
			) );
			$meta_layout->addCheckbox( 'sr_inbound_hide_footer_widgets', array(
				'name'    => __( 'Hide Footer Widgets', 'inbound' ),
				'caption' => __( 'Remove footer widgets section from this page', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the footer widgets section will not be displayed on this particular page.', 'inbound' )
			) );
			$meta_layout->addCheckbox( 'sr_inbound_design_show_banner', array(
				'name'    => __( 'Display Sub Header', 'inbound' ),
				'caption' => __( 'Display sub header underneath header and banner', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, a narrow sub header will be displayed above the content.', 'inbound' )
			) );
			$meta_layout->Finish();


			/*
			 * Meta Box: Layout Options
			 */
			$config      = array(
				'id'             => 'inbound_layout',
				'title'          => __( 'Layout and Design', 'inbound' ),
				'pages'          => array( 'post' ),
				'context'        => 'normal',
				'priority'       => 'high',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_layout_post = new SR_Meta_Box( $config );
			$meta_layout_post->addPosts( 'sr_inbound_custom_banner', array( 'post_type' => 'banner' ), array(
				'class' => 'no-fancy',
				'name'  => __( 'Custom Banner', 'inbound' ),
				'desc'  => __( 'If a banner is selected, it will be displayed instead of the default banner selected for the active profile.', 'inbound' )
			) );
			$meta_layout_post->Finish();


			/*
			 * Meta Box: Product Layout Options
			 */
			if ( function_exists( 'is_woocommerce' ) ) {
				$config          = array(
					'id'             => 'inbound_layout',
					'title'          => __( 'Layout and Design', 'inbound' ),
					'pages'          => array( 'product' ),
					'context'        => 'normal',
					'priority'       => 'default',
					'fields'         => array(),
					'local_images'   => false,
					'use_with_theme' => get_template_directory_uri() . '/lib/admin'
				);
				$meta_layout_woo = new SR_Meta_Box( $config );
				$meta_layout_woo->addPosts( 'sr_inbound_custom_banner', array( 'post_type' => 'banner' ), array(
					'class' => 'no-fancy',
					'name'  => __( 'Custom Banner', 'inbound' ),
					'desc'  => __( 'If a banner is selected, it will be displayed instead of the default banner selected for the active profile.', 'inbound' )
				) );
				$meta_layout_woo->addCheckbox( 'sr_inbound_hide_header', array(
					'name'    => __( 'Hide Header', 'inbound' ),
					'caption' => __( 'Remove the header section from this page', 'inbound' ),
					'std'     => false,
					'class'   => 'no-fancy',
					'desc'    => __( 'If this option is checked, the header containing logo, site title, tagline, menu and social icons, will be hidden on this page.', 'inbound' )
				) );
				$meta_layout_woo->addCheckbox( 'sr_inbound_hide_footer_widgets', array(
					'name'    => __( 'Hide Footer Widgets', 'inbound' ),
					'caption' => __( 'Remove footer widgets section from this page', 'inbound' ),
					'std'     => false,
					'class'   => 'no-fancy',
					'desc'    => __( 'If this option is checked, the footer widgets section will not be displayed on this particular page.', 'inbound' )
				) );
				$meta_layout_woo->Finish();
			}

			/*
			 * Meta Box: Advanced
 			*/
			$config             = array(
				'id'             => 'inbound_page_advanced',
				'title'          => __( 'Advanced', 'inbound' ),
				'pages'          => array( 'page', 'post' ),
				'context'        => 'normal',
				'priority'       => 'high',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_page_advanced = new SR_Meta_Box( $config );

			if ( function_exists ( 'inbound_shortcode_option' ) ) {
				$meta_page_advanced->addTextarea( 'sr_inbound_advanced_page_shortcodes', array(
					'name'           => __( 'Customization Shortcodes', 'inbound' ),
					'label_location' => 'top',
					'desc'           => __( 'Use advanced customization shortcodes to modify theme options and replace sections.', 'inbound' ),
					'rows'           => '10'
				) );
			}

			$meta_page_advanced->addTextarea( 'sr_inbound_advanced_page_custom_css', array(
				'name'           => __( 'Custom CSS', 'inbound' ),
				'label_location' => 'top',
				'desc'           => __( 'Custom CSS code to be added to the page header.', 'inbound' ),
				'rows'           => '10'
			) );
			$meta_page_advanced->addTextarea( 'sr_inbound_advanced_page_custom_scripts', array(
				'name'           => __( 'Custom Scripts', 'inbound' ),
				'label_location' => 'top',
				'desc'           => __( 'Custom scripts to be added to the page header.', 'inbound' ),
				'rows'           => '10'
			) );
			$meta_page_advanced->Finish();

			/*
			 * Meta Box: Development Options
 			*/
			if ( inbound_option( 'support_options_dev_mode' ) ) {
				$config                = array(
					'id'             => 'inbound_page_development',
					'title'          => __( 'Development', 'inbound' ),
					'pages'          => array( 'page' ),
					'context'        => 'normal',
					'priority'       => 'high',
					'fields'         => array(),
					'local_images'   => false,
					'use_with_theme' => get_template_directory_uri() . '/lib/admin'
				);
				$meta_page_development = new SR_Meta_Box( $config );
				$meta_page_development->addTextarea( 'sr_inbound_development_package_description', array(
					'name'           => __( 'Template Description', 'inbound' ),
					'label_location' => 'top',
					'desc'           => __( 'This description will be used for exported template packages.', 'inbound' ),
					'rows'           => '5'
				) );
				$meta_page_development->addImage( 'sr_inbound_development_package_preview', array(
					'name' => __( 'Template Preview', 'inbound' ),
					'desc' => __( 'This image will be used as a template preview.', 'inbound' )
				) );
				$meta_page_development->addText( 'sr_inbound_development_package_group', array(
					'name' => __( 'Group ID', 'inbound' ),
					'size' => 65
				) );
				$meta_page_development->addText( 'sr_inbound_development_package_min_version', array(
					'name' => __( 'Minimum Req. Version', 'inbound' ),
					'size' => 65
				) );
				$meta_page_development->addText( 'sr_inbound_development_package_sort_order', array(
					'name' => __( 'Sort Order', 'inbound' ),
					'size' => 65
				) );
				$meta_page_development->Finish();
			}


			/*
			 * Meta Box: Layout
			 */
			$config               = array(
				'id'             => 'inbound_layout_template',
				'title'          => __( 'Template', 'inbound' ),
				'pages'          => array( 'page', 'post' ),
				'context'        => 'side',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_layout_template = new SR_Meta_Box( $config );
			$meta_layout_template->addSelect( 'sr_inbound_template_layout', array(
				false           => __( 'No Sidebar', 'inbound' ),
				'sidebar-left'  => __( 'Sidebar Left', 'inbound' ),
				'sidebar-right' => __( 'Sidebar Right', 'inbound' )
			), array(
				'name'           => __( 'Sidebar', 'inbound' ),
				'label_location' => 'top',
				'desc'           => __( 'Select a page template.', 'inbound' ),
				'std'            => false
			) );
			$meta_layout_template->Finish();


			/*
			 * Meta Box: Profile
			 */
			if ( defined ('INBOUND_FEATURE_PACK') ) {
				$config       = array(
					'id'             => 'inbound_profile',
					'title'          => __( 'Settings Profile', 'inbound' ),
					'pages'          => array( 'page', 'post' ),
					'context'        => 'side',
					'priority'       => 'default',
					'fields'         => array(),
					'local_images'   => false,
					'use_with_theme' => get_template_directory_uri() . '/lib/admin'
				);
				$meta_profile = new SR_Meta_Box( $config );
				$meta_profile->addPosts( 'sr_inbound_profile',
					array( 'post_type' => 'profile' ),
					array(
						'class'          => 'no-fancy',
						'name'           => __( 'Profile', 'inbound' ),
						'label_location' => 'top',
						'desc'           => __( 'Select a settings profile to be applied to this page. If none is selected, the default profile will be used.', 'inbound' )
					) );
				$meta_profile->Finish();
			}

			/*
			 * Meta Box: Design
			 */
			$config      = array(
				'id'             => 'inbound_profile_design',
				'title'          => __( 'Layout and Design', 'inbound' ),
				'pages'          => array( 'profile' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_design = new SR_Meta_Box( $config );

			$meta_design->addSelect( 'sr_inbound_content_layout', array(
				'default'    => __( 'Default', 'inbound' ),
				'boxed'      => __( 'Boxed', 'inbound' ),
				'full-width' => __( 'Full Width', 'inbound' )
			), array(
				'name'           => __( 'Layout', 'inbound' ),
				'std'            => 'default',
				'class'          => 'no-fancy',
				'group-selector' => true
			) );


			$meta_design->addSelect( 'sr_inbound_body_background_mode', array(
				'solid'       => __( 'Solid Color', 'inbound' ),
				'image-fixed' => __( 'Image (fixed)', 'inbound' ),
				'image-tile'  => __( 'Image (tile)', 'inbound' )
			), array(
				'name'           => __( 'Background Mode', 'inbound' ),
				'std'            => 'solid',
				'class'          => 'no-fancy',
				'group-selector' => true
			) );

			$meta_design->addColor( 'sr_inbound_color_body_background', array(
				'name' => __( 'Background Color', 'inbound' ),
				'std'  => '#ffffff'
			) );
			$meta_design->addColor( 'sr_inbound_color_body_background_boxed_wrapper', array(
				'name'        => __( 'Content Background Color', 'inbound' ),
				'std'         => '#f5f5f5',
				'is-group'    => 'sr_inbound_content_layout',
				'group-value' => array( 'boxed' )
			) );

			$meta_design->addImage( 'sr_inbound_body_background_image', array(
				'name'        => __( 'Background Image', 'inbound' ),
				'is-group'    => 'sr_inbound_body_background_mode',
				'group-value' => array(
					'image-fixed',
					'image-tile'
				)
			) );

			$meta_design->addColor( 'sr_inbound_color_body_link', array(
				'name' => __( 'Link Color', 'inbound' ),
				'std'  => '#000000'
			) );
			$meta_design->addColor( 'sr_inbound_color_body_link_hover', array(
				'name' => __( 'Link Hover Color', 'inbound' ),
				'std'  => '#000000'
			) );

			// Get available button styles from options
			$styles      = array();
			$styles['0'] = __( 'None', 'inbound' );
			$styles_temp = inbound_option( 'global_button_styles' );
			$std_style   = '';
			if ( is_array( $styles_temp ) && count( $styles_temp ) > 0 ) {
				foreach ( $styles_temp as $style ) {
					if ( $style['name'] == '' ) {
						$name = $style['uid'];
					} else {
						$name = $style['name'];
					}
					$styles[ $style['uid'] ] = $name;
				}
			}
			$meta_design->addSelect( 'sr_inbound_default_button_style',
				$styles,
				array(
					'name'  => __( 'Default Button Style', 'inbound' ),
					'std'   => '0',
					'class' => 'no-fancy',
					'desc'  => __( 'Select the default style used for all buttons for which no specific style can be selected, e.g. form submit buttons.', 'inbound' ),
				)
			);

			if ( ! defined ( 'INBOUND_DISABLE_ANIMATIONS' )) {
				$meta_design->addCheckbox( 'mobile_animations', array(
								'name'    => __( 'Mobile Animations', 'inbound' ),
								'caption' => __( 'Support row and widget animations for mobile devices', 'inbound' ),
								'std'     => false,
								'class'   => 'no-fancy',
								'desc'    => __( 'If this option is checked, row and widget animations added in the page builder will also be used on mobile devices.', 'inbound' )

						)
				);
			}

			$meta_design->Finish();


			/*
			 * Meta Box: Fonts
			 */
			$config          = array(
				'id'             => 'inbound_profile_fonts',
				'title'          => __( 'Typography', 'inbound' ),
				'pages'          => array( 'profile' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_typography = new SR_Meta_Box( $config );

			$meta_typography->addParagraph( 'sr_inbound_toolbar_info', array('value' => __( 'Typography settings are applied to all pages using this profile. Please note that banners have additional individual settings. Button styles also have their own typography settings.', 'inbound' )));

			$meta_typography->addTypography(
				'sr_inbound_font_body',
				array(
					'name' => __( 'Body', 'inbound' ),
					'desc' => __( 'This font is used for all text for which no other font is defined, such as plain text paragraphs.', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_logo',
				array(
					'name' => __( 'Logo', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_logo_tagline',
				array(
					'name' => __( 'Logo Tagline', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_page_title',
				array(
					'name' => __( 'Page Title', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_widget_title',
				array(
					'name' => __( 'Widget Title', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_quote',
				array(
					'name' => __( 'Quotes', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_h1',
				array(
					'name' => __( 'H1', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_h2',
				array(
					'name' => __( 'H2', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_h3',
				array(
					'name' => __( 'H3', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_h4',
				array(
					'name' => __( 'H4', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_h5',
				array(
					'name' => __( 'H5', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->addTypography(
				'sr_inbound_font_h6',
				array(
					'name' => __( 'H6', 'inbound' ),
					'std'  => array(
						'face'   => 'helvetica',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_typography->Finish();


			/*
			 * Meta Box: Social Icons
			 */
			$config      = array(
				'id'             => 'inbound_profile_social',
				'title'          => __( 'Social Media Profiles', 'inbound' ),
				'pages'          => array( 'profile' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_social = new SR_Meta_Box( $config );

			$meta_social->addParagraph( 'sr_inbound_toolbar_info', array('value' => __( 'These are the social media profiles displayed in header, toolbar and the social icons widget.', 'inbound' )));

			$social_fields   = array();
			$social_fields[] = $meta_social->addSelect( 'preset', array(
				''           => __( 'None', 'inbound' ),
				'facebook'   => __( 'Facebook', 'inbound' ),
				'twitter'    => __( 'Twitter', 'inbound' ),
				'googleplus' => __( 'Google+', 'inbound' ),
				'youtube'    => __( 'YouTube', 'inbound' ),
				'linkedin'   => __( 'LinkedIn', 'inbound' ),
				'instagram'  => __( 'Instagram', 'inbound' ),
				'pinterest'  => __( 'Pinterest', 'inbound' ),
				'flickr'     => __( 'Flickr', 'inbound' ),
				'tumblr'     => __( 'Tumblr', 'inbound' ),
				'foursquare' => __( 'Foursquare', 'inbound' ),
				'vimeo'      => __( 'Vimeo', 'inbound' ),
				'lastfm'     => __( 'last.fm', 'inbound' ),
				'soundcloud' => __( 'Soundcloud', 'inbound' ),
				'yelp'       => __( 'Yelp', 'inbound' ),
				'slideshare' => __( 'Slideshare', 'inbound' ),
				'dribbble'   => __( 'Dribbble', 'inbound' ),
				'behance'    => __( 'Behance', 'inbound' ),
				'github'     => __( 'GitHub', 'inbound' ),
				'reddit'     => __( 'Reddit', 'inbound' ),
				'weibo'      => __( 'Weibo', 'inbound' ),
				'deviantart' => __( 'DeviantArt', 'inbound' ),
				'skype'      => __( 'Skype', 'inbound' ),
				'spotify'    => __( 'Spotify', 'inbound' ),
				'xing'       => __( 'Xing', 'inbound' ),
				'vine'       => __( 'Vine', 'inbound' ),
				'digg'       => __( 'Digg', 'inbound' ),
			), array(
				'name'  => __( 'Preset', 'inbound' ),
				'std'   => 'solid',
				'class' => 'social-service-select'
			), true );
			$social_fields[] = $meta_social->addText( 'title', array(
				'name' => __( 'Title', 'inbound' ),
				'size' => 65
			), true );
			$social_fields[] = $meta_social->addText( 'link', array(
				'name' => __( 'Link URL', 'inbound' ),
				'size' => 65
			), true );
			$social_fields[] = $meta_social->addIcon( 'icon', array(
				'name' => __( 'Icon', 'inbound' ),
				'std'  => ''
			), true );
			$social_fields[] = $meta_social->addColor( 'color_background', array(
				'name' => __( 'Background', 'inbound' ),
				'std'  => '#828282'
			), true );
			$social_fields[] = $meta_social->addCheckbox( 'show_in_toolbar', array(
				'name'    => __( 'Toolbar', 'inbound' ),
				'caption' => __( 'Activate this service in the toolbar', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, this profile link will be displayed in the site toolbar.', 'inbound' )
			), true );
			$social_fields[] = $meta_social->addCheckbox( 'show_in_header', array(
				'name'    => __( 'Header', 'inbound' ),
				'caption' => __( 'Activate this service in the header', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, this profile link will be displayed in the site header.', 'inbound' )
			), true );
			$social_fields[] = $meta_social->addCheckbox( 'show_in_widget', array(
				'name'    => __( 'Widgets', 'inbound' ),
				'caption' => __( 'Activate this service in social widgets', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, this profile link will be displayed in widgets.', 'inbound' )
			), true );

			$meta_social->addRepeaterBlock(
				'social_media_profiles',
				array(
					'sortable'       => true,
					'inline'         => false,
					'name'           => __( 'Profiles', 'inbound' ),
					'fields'         => $social_fields,
					'desc'           => __( 'Add, edit and re-order social media profiles.', 'inbound' ),
					'label_location' => 'none',
					'title'          => 'title'
				)
			);

			$meta_social->Finish();

			/*
			 * Meta Box: Twitter Account
			 */
			$config       = array(
				'id'             => 'inbound_profile_twitter',
				'title'          => __( 'Twitter Account', 'inbound' ),
				'pages'          => array( 'profile' ),
				'context'        => 'normal',
				'priority'       => 'high',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_twitter = new SR_Meta_Box( $config );

			$meta_twitter->addParagraph( 'twitter_intro', array( 'value' => sprintf( __( 'You need to <a href="%1$s" target="_blank">register your website</a> as an application with Twitter in order to use the Twitter API.', 'inbound' ), esc_url( 'https://apps.twitter.com/' ) ) ) );
			$meta_twitter->addText( 'sr_inbound_twitter_user', array(
				'name' => __( 'Twitter User', 'inbound' ),
				'size' => 65
			) );
			$meta_twitter->addText( 'sr_inbound_twitter_consumer_key', array(
				'name' => __( 'Consumer Key', 'inbound' ),
				'size' => 65
			) );
			$meta_twitter->addText( 'sr_inbound_twitter_consumer_secret', array(
				'name' => __( 'Consumer Secret', 'inbound' ),
				'size' => 65
			) );
			$meta_twitter->addText( 'sr_inbound_twitter_access_token', array(
				'name' => __( 'Access Token', 'inbound' ),
				'size' => 65
			) );
			$meta_twitter->addText( 'sr_inbound_twitter_access_token_secret', array(
				'name' => __( 'Access Token Secret', 'inbound' ),
				'size' => 65
			) );
			$meta_twitter->Finish();


			/*
			 * Meta Box: Header
			 */
			$config      = array(
				'id'             => 'inbound_profile_header',
				'title'          => __( 'Header', 'inbound' ),
				'pages'          => array( 'profile' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_header = new SR_Meta_Box( $config );

			$meta_header->addParagraph( 'sr_inbound_header_info', array('value' => __( 'The header is the layout element at the very top that contains logo, site title, tagline, main menu, social icons etc.', 'inbound' )));

			$meta_header->addCheckbox( 'sr_inbound_header_menu_transparent', array(
				'name'    => __( 'Transparent', 'inbound' ),
				'caption' => __( 'Display header items as transparent on banner', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the menu bar will be transparent on top of the banner until scrolling reaches content.', 'inbound' )
			) );
			$meta_header->addCheckbox( 'sr_inbound_header_menu_sticky', array(
				'name'    => __( 'Sticky', 'inbound' ),
				'caption' => __( 'Make header elements \'stick\' to the top of the page', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the menu will be fixed to the top.', 'inbound' )
			) );
			$meta_header->addCheckbox( 'sr_inbound_header_full_width', array(
				'name'    => __( 'Full Width', 'inbound' ),
				'caption' => __( 'Render the header in full width', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the header will stretch from the outer left to the outer right edge of the window.', 'inbound' )
			) );

			$meta_header->addImage( 'sr_inbound_header_logo_image', array( 'name' => __( 'Logo Image', 'inbound' ) ) );

			$meta_header->addImage( 'sr_inbound_header_logo_image_secondary', array( 'name' => __( 'Logo Image (Secondary)', 'inbound' ), 'desc'    => __( 'If a secondary logo is uploaded, it will be displayed when a sticky header is used, when the user scrolls down to reveal the scroll state of the header.', 'inbound' ) ) );

			$meta_header->addCheckbox( 'sr_inbound_header_logo_image_large', array(
				'name'    => __( 'Display extra-large logo.', 'inbound' ),
				'caption' => __( 'Provide more space to fit a large logo.', 'inbound' )
			) );


			if ( ! function_exists( 'has_site_icon' ) ) {
				$meta_header->addImage( 'sr_inbound_favicon', array(
					'name' => __( 'Fav Icon', 'inbound' ),
					'desc' => __( 'This is the small icon displayed in the browser tab.', 'inbound' )
				) );
			}

			$meta_header->addCheckbox( 'sr_inbound_header_title_hide', array(
				'name'    => __( 'Hide Site Title', 'inbound' ),
				'caption' => __( 'Do not display site title in header', 'inbound' )
			) );
			$meta_header->addCheckbox( 'sr_inbound_header_tagline_hide', array(
				'name'    => __( 'Hide Site Tagline', 'inbound' ),
				'caption' => __( 'Do not display site tagline header', 'inbound' )
			) );
			$meta_header->addText( 'sr_inbound_header_link', array(
				'name'  => __( 'Custom Link', 'inbound' ),
				'desc'  => __( 'A custom URL to link site title and logo to.', 'inbound' ),
				'class' => 'widefat'
			) );
			$meta_header->addText( 'sr_inbound_header_title', array(
				'name'  => __( 'Custom Site Title', 'inbound' ),
				'desc'  => __( 'A custom title to use instead of the site title set in WordPress.', 'inbound' ),
				'class' => 'widefat'
			) );
			$meta_header->addText( 'sr_inbound_header_tagline', array(
				'name'  => __( 'Custom Tagline', 'inbound' ),
				'desc'  => __( 'A custom tagline to use instead of the tagline set in WordPress.', 'inbound' ),
				'class' => 'widefat'
			) );
			$meta_header->addSelect( 'sr_inbound_header_menu_alignment', array(
				'left'  => __( 'Left', 'inbound' ),
				'center'=> __( 'Center', 'inbound' ),
				'right' => __( 'Right', 'inbound' )
			), array(
				'name'           => __( 'Menu Alignment', 'inbound' ),
				'desc'           => __( 'Select where the menu should be positioned.', 'inbound' ),
				'std'            => 'left',
				'class'          => 'no-fancy'
			) );
			$meta_header->addTaxonomy( 'sr_inbound_header_menu', array(
				'taxonomy' => 'nav_menu',
				'type'     => 'selectbox'
			), array(
				'name'  => __( 'Custom Menu', 'inbound' ),
				'class' => 'no-fancy',
				'none'  => __( 'Default Menu', 'inbound' ),
				'desc'  => __( 'The selected menu will be displayed as a flat toolbar navigation, so only the first level will be displayed. You must also set a default for all profiles via Appearance &#8594; Menus, otherwise WordPress will see this menu as empty and this option will not be applied.', 'inbound' )
			) );
			$meta_header->addCheckbox( 'sr_inbound_header_menu_hide', array(
				'name'    => __( 'Hide Menu', 'inbound' ),
				'caption' => __( 'Do not display the header menu', 'inbound' )
			) );
			$meta_header->addCheckbox( 'sr_inbound_search_bar_hide', array(
				'name'    => __( 'Hide Search Bar', 'inbound' ),
				'caption' => __( 'Do not display search icon in the header', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the search icon and search bar will be hidden.', 'inbound' )
			) );

			if (class_exists('SitePress')) {
				$meta_header->addCheckbox( 'sr_inbound_language_switcher_hide', array(
						'name'    => __( 'Hide Language Switcher', 'inbound' ),
						'caption' => __( 'Do not display language switcher in header.', 'inbound' ),
						'std'     => false,
						'class'   => 'no-fancy',
						'desc'    => __( 'If this option is checked, the language switcher will be hidden from the header.', 'inbound' )
				) );
			}

			$meta_header->addColor( 'sr_inbound_header_background', array(
				'name' => __( 'Background Color', 'inbound' ),
				'std'  => '#ffffff'
			) );
			$meta_header->addColor( 'sr_inbound_header_text', array(
				'name' => __( 'Menu Link Color', 'inbound' ),
				'std'  => '#000000',
				'desc' => __( 'This colour is used for menu items.', 'inbound' )
			) );
			$meta_header->addColor( 'sr_inbound_menu_link_hover', array(
				'name' => __( 'Menu Link Hover Color', 'inbound' ),
				'desc' => __( 'This colour is used for menu items in the hover state.', 'inbound' ),
				'std'  => '#000000'
			) );

			$meta_header->addParagraph( 'sr_inbound_menu_links_info', array('value' => __( 'Please note that the theme uses the banner text color setting instead of the header link color settings in case the header is displayed transparently on top of the banner.', 'inbound' ),  'no-border' => true));



			$meta_header->Finish();

			/*
			 * Meta Box: Sub Header
			 */
			$config          = array(
				'id'             => 'inbound_profile_sub_header',
				'title'          => __( 'Sub Header', 'inbound' ),
				'pages'          => array( 'profile' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);

			$meta_sub_header = new SR_Meta_Box( $config );

			$meta_sub_header->addParagraph( 'sr_inbound_sub_header_info', array('value' => __( 'The sub header is a navigational aid that is displayed underneath the header and banner elements and contains the breadcrumb. It is displayed by default on all blog and supported e-commerce plug-in pages.', 'inbound' )));

			$meta_sub_header->addCheckbox( 'sr_inbound_header_sub_banner_full_width', array(
				'name'    => __( 'Full Width', 'inbound' ),
				'caption' => __( 'Render the sub header in full width', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the sub header will stretch from the outer left to the outer right edge of the window.', 'inbound' )
			) );
			$meta_sub_header->addColor( 'sr_inbound_header_sub_banner_color_background', array(
				'name' => __( 'Background Color', 'inbound' ),
				'std'  => '#ffffff'
			) );
			$meta_sub_header->addColor( 'sr_inbound_header_sub_banner_color_text', array(
				'name' => __( 'Text Color', 'inbound' ),
				'std'  => '#000000'
			) );
			$meta_sub_header->Finish();

			/*
			 * Meta Box: Toolbar
			 */
			$config            = array(
				'id'             => 'inbound_profile_multipurpose',
				'title'          => __( 'Toolbar', 'inbound' ),
				'pages'          => array( 'profile' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_multipurpose = new SR_Meta_Box( $config );

			$meta_multipurpose->addParagraph( 'sr_inbound_toolbar_info', array('value' => __( 'The toolbar is a narrow bar that is optionally displayed above the header. It can contain a one-dimensional menu or any custom text.', 'inbound' )));

			$meta_multipurpose->addCheckbox( 'sr_inbound_header_multipurpose', array(
				'name'    => __( 'Display Toolbar', 'inbound' ),
				'caption' => __( 'Display toolbar above header', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the toolbar will be displayed above the header. The toolbar can contain free form text or an additional one-level menu.', 'inbound' )
			) );
			$meta_multipurpose->addCheckbox( 'sr_inbound_header_multipurpose_full_width', array(
				'name'    => __( 'Full Width', 'inbound' ),
				'caption' => __( 'Render the toolbar in full width', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the toolbar will stretch from the outer left to the outer right edge of the window.', 'inbound' )
			) );
			$meta_multipurpose->addSelect( 'sr_inbound_header_multipurpose_mode', array(
				'content' => __( 'Custom Content', 'inbound' ),
				'menu'    => __( 'Custom Menu', 'inbound' )
			), array(
				'name'           => __( 'Mode', 'inbound' ),
				'desc'           => __( 'Select what you would like to display in the toolbar.', 'inbound' ),
				'std'            => 'content',
				'class'          => 'no-fancy',
				'group-selector' => true
			) );
			$meta_multipurpose->addWysiwyg( 'sr_inbound_header_multipurpose_custom', array(
				'name'        => __( 'Custom Content', 'inbound' ),
				'is-group'    => 'sr_inbound_header_multipurpose_mode',
				'group-value' => array( 'content' ),
				'settings'    => array(
					'textarea_rows' => 3,
					'teeny'         => true,
					'media_buttons' => false
				)
			) );
			$meta_multipurpose->addTaxonomy( 'sr_inbound_header_multipurpose_menu', array(
				'taxonomy' => 'nav_menu',
				'type'     => 'selectbox'
			), array(
				'name'        => __( 'Custom Menu', 'inbound' ),
				'none'        => __( 'Default Menu', 'inbound' ),
				'desc'        => __( 'The selected menu will be displayed as a flat toolbar navigation, so only the first level will be displayed. You must also set a default for all profiles via Appearance &#8594; Menus, otherwise WordPress will see this menu as empty and this option will not be applied.', 'inbound' ),
				'is-group'    => 'sr_inbound_header_multipurpose_mode',
				'class' => 'no-fancy',
				'group-value' => array( 'menu' )
			) );
			$meta_multipurpose->addColor( 'sr_inbound_header_multipurpose_background', array(
				'name' => __( 'Background Color', 'inbound' ),
				'std'  => '#ffffff'
			) );
			$meta_multipurpose->addColor( 'sr_inbound_header_multipurpose_text', array(
				'name' => __( 'Text Color', 'inbound' ),
				'std'  => '#000000',
				'desc' => __( 'This colour is used for the actual text, as well as for links.', 'inbound' )
			) );
			$meta_multipurpose->Finish();


			/*
			 * Meta Box: Footer
			 */
			$config      = array(
				'id'             => 'inbound_profile_footer',
				'title'          => __( 'Footer', 'inbound' ),
				'pages'          => array( 'profile' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);

			$meta_footer = new SR_Meta_Box( $config );

			$meta_footer->addParagraph( 'sr_inbound_footer_info', array('value' => __( 'The footer is the last element rendered at the very bottom of the page. It can contain custom text and/or a custom one-dimensional menu.', 'inbound' )));

			$meta_footer->addWysiwyg( 'sr_inbound_footer_copyright', array(
				'name'     => __( 'Footer Notice', 'inbound' ),
				'desc' => __ ('This element is part of the footer and usually contains a copyright notice, disclaimer text etc.', 'inbound'),
				'settings' => array(
					'textarea_rows' => 3,
					'teeny'         => true,
					'media_buttons' => false
				)
			) );
			$meta_footer->addCheckbox( 'sr_inbound_footer_full_width', array(
				'name'    => __( 'Full Width', 'inbound' ),
				'caption' => __( 'Render the footer in full width', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the footer will stretch from the outer left to the outer right edge of the window.', 'inbound' )
			) );
			$meta_footer->addTaxonomy( 'sr_inbound_footer_menu', array(
				'taxonomy' => 'nav_menu',
				'type'     => 'selectbox'
			), array(
				'name'  => __( 'Custom Menu', 'inbound' ),
				'desc'  => __( 'The selected menu will be displayed as a flat toolbar navigation, so only the first level will be displayed. You must also set a default for all profiles via Appearance &#8594; Menus, otherwise WordPress will see this menu as empty and this option will not be applied.', 'inbound' ),
				'class' => 'no-fancy',
				'none'  => __( 'Default Menu', 'inbound' )
			) );
			$meta_footer->addCheckbox( 'sr_inbound_footer_menu_hide', array(
				'name'    => __( 'Hide Menu', 'inbound' ),
				'caption' => __( 'Do not display the footer menu', 'inbound' )
			) );
			$meta_footer->addColor( 'sr_inbound_footer_background', array(
				'name' => __( 'Background Color', 'inbound' ),
				'std'  => '#ffffff'
			) );
			$meta_footer->addColor( 'sr_inbound_footer_text', array(
				'name' => __( 'Text Color', 'inbound' ),
				'std'  => '#000000',
				'desc' => __( 'This colour is used for the actual text, as well as for links.', 'inbound' )
			) );
			$meta_footer->Finish();

			/*
			 * Meta Box: Sub Footer
			 */
			$config          = array(
				'id'             => 'inbound_profile_subfooter',
				'title'          => __( 'Sub Footer', 'inbound' ),
				'pages'          => array( 'profile' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);

			$meta_sub_footer = new SR_Meta_Box( $config );

			$meta_sub_footer->addParagraph( 'sr_inbound_subfooter_info', array('value' => __( 'The sub footer is a widgetized area displayed right above the footer. It is positioned at the bottom of the page.', 'inbound' )));

			$meta_sub_footer->addCheckbox( 'sr_inbound_subfooter_full_width', array(
				'name'    => __( 'Full Width', 'inbound' ),
				'caption' => __( 'Render the sub footer in full width', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the sub footer will stretch from the outer left to the outer right edge of the window.', 'inbound' )
			) );
			$meta_sub_footer->addColor( 'sr_inbound_subfooter_background', array(
				'name' => __( 'Background Color', 'inbound' ),
				'std'  => '#ffffff'
			) );
			$meta_sub_footer->addColor( 'sr_inbound_subfooter_text', array(
				'name' => __( 'Text Color', 'inbound' ),
				'std'  => '#000000',
				'desc' => __( 'This colour is used for the actual text, as well as for links.', 'inbound' )
			) );
			$meta_sub_footer->Finish();


			/*
			 * Meta Box (Banner): Background
			 */
			$config                 = array(
				'id'             => 'inbound_banner_background',
				'title'          => __( 'Background', 'inbound' ),
				'pages'          => array( 'banner' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_banner_background = new SR_Meta_Box( $config );
			$meta_banner_background->addSelect( 'sr_inbound_background_mode', array(
				'solid'          => __( 'Solid Color', 'inbound' ),
				'gradient'       => __( 'Gradient', 'inbound' ),
				'image-fixed'    => __( 'Image (fixed)', 'inbound' ),
				'image-cover'    => __( 'Image (cover)', 'inbound' ),
				'image-centered' => __( 'Image (original size, centered)', 'inbound' ),
				'image-parallax' => __( 'Image (parallax)', 'inbound' ),
				'image-tile'     => __( 'Image (tile)', 'inbound' ),
			), array(
				'name'           => __( 'Mode', 'inbound' ),
				'std'            => 'solid',
				'class'          => 'no-fancy',
				'group-selector' => true
			) );
			$meta_banner_background->addColor( 'sr_inbound_color_1', array(
				'name' => __( 'Color 1', 'inbound' ),
				'std'  => '#ffffff'
			) );
			$meta_banner_background->addColor( 'sr_inbound_color_2', array(
				'name'        => __( 'Color 2', 'inbound' ),
				'std'         => '#ffffff',
				'is-group'    => 'sr_inbound_background_mode',
				'group-value' => array( 'gradient' )
			) );
			$meta_banner_background->addImage( 'sr_inbound_background_image', array(
				'name'        => __( 'Image', 'inbound' ),
				'is-group'    => 'sr_inbound_background_mode',
				'group-value' => array(
					'image-fixed',
					'image-cover',
					'image-centered',
					'image-tile',
					'image-parallax'
				)
			) );
			$meta_banner_background->Finish();


			/*
 			* Meta Box (Banner): Content
 			*/
			$config = array(
				'id'             => 'inbound_banner_content',
				'title'          => __( 'Content', 'inbound' ),
				'pages'          => array( 'banner' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);

			$meta_banner_content = new SR_Meta_Box( $config );

			// slider options
			$is_slider_plugin       = false;
			$banner_content_options = array(
				'content' => __( 'Banner Content', 'inbound' ),
				'custom'  => __( 'Custom Code', 'inbound' )
			);


			$is_metaslider = false;
			$is_revolution = false;

			if ( class_exists( 'MetaSliderPlugin' ) ) {
				$is_metaslider = true;
			}
			if ( class_exists( 'RevSlider' ) ) {
				$is_revolution = true;
			}

			if ( $is_metaslider ) {
				$banner_content_options['metaslider'] = __( 'Meta Slider', 'inbound' );
			}

			if ( $is_revolution ) {
				$banner_content_options['revslider'] = __( 'Revolution Slider', 'inbound' );
			}

			$meta_banner_content->addSelect( 'sr_inbound_banner_content_mode', $banner_content_options, array(
				'name'           => __( 'Content', 'inbound' ),
				'std'            => 'content',
				'class'          => 'no-fancy',
				'group-selector' => true
			) );

			$meta_banner_content->addTextarea( 'sr_inbound_banner_content', array(
				'rows'           => 8,
				'name'           => __( 'Custom Code', 'inbound' ),
				'label_location' => 'top',
				'desc'           => __( 'This field\'s content will replace the entire banner.', 'inbound' ),
				'is-group'       => 'sr_inbound_banner_content_mode',
				'group-value'    => array( 'custom' )
			) );


			if ( $is_metaslider ) {
				$meta_banner_content->addPosts( 'sr_inbound_metaslider', array( 'post_type' => 'ml-slider' ), array(
					'class'       => 'no-fancy',
					'name'        => __( 'Meta Slider', 'inbound' ),
					'desc'        => __( 'Select which slider you would like to use in this banner.', 'inbound' ),
					'is-group'    => 'sr_inbound_banner_content_mode',
					'group-value' => array( 'metaslider' )
				) );
			}

			if ( $is_revolution ) {
				try {
					$slider     = new RevSlider();
					$arrSliders = $slider->getArrSlidersShort();
					$meta_banner_content->addSelect( 'sr_inbound_revslider', $arrSliders, array(
						'class'       => 'no-fancy',
						'name'        => __( 'Revolution Slider', 'inbound' ),
						'desc'        => __( 'Select which slider you would like to use in this banner.', 'inbound' ),
						'is-group'    => 'sr_inbound_banner_content_mode',
						'group-value' => array( 'revslider' )
					) );
				} catch ( Exception $e ) {
				}
			}

			$meta_banner_content->addTypography(
				'sr_inbound_font_banner_title',
				array(
					'name' => __( 'Title (H1)', 'inbound' ),
					'std'  => array(
						'face'   => 'verdana',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_banner_content->addTypography(
				'sr_inbound_font_banner_sub_title',
				array(
					'name' => __( 'Sub Title (H2)', 'inbound' ),
					'std'  => array(
						'face'   => 'verdana',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);

			$meta_banner_content->addTypography( // was: text_color = text color
				'sr_inbound_font_banner_text',
				array(
					'name' => __( 'Text', 'inbound' ),
					'std'  => array(
						'face'   => 'verdana',
						'weight' => 'regular',
						'size'   => '13px',
						'color'  => '#000000'
					)
				)
			);


			$meta_banner_content->addCheckbox( 'sr_inbound_banner_full_width', array(
				'name'    => __( 'Full Width', 'inbound' ),
				'caption' => __( 'Render the banner in full width, for use with sliders or other third party plug-ins', 'inbound' ),
				'std'     => false,
				'class'   => 'no-fancy',
				'desc'    => __( 'If this option is checked, the banner will use 100% of the available window width.', 'inbound' )
			) );
			$meta_banner_content->Finish();


			/*
			 * Meta Box: Advanced
			 */
			$config        = array(
				'id'             => 'inbound_profile_advanced',
				'title'          => __( 'Advanced', 'inbound' ),
				'pages'          => array( 'profile' ),
				'context'        => 'normal',
				'priority'       => 'default',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_advanced = new SR_Meta_Box( $config );

			if ( function_exists ( 'inbound_shortcode_option' ) ) {
				$meta_advanced->addTextarea( 'sr_inbound_advanced_shortcodes', array(
					'name'           => __( 'Customization Shortcodes', 'inbound' ),
					'label_location' => 'top',
					'desc'           => __( 'Use advanced customization shortcodes to modify theme options and replace sections.', 'inbound' ),
					'rows'           => '10'
				) );
			}

			$meta_advanced->addTextarea( 'sr_inbound_advanced_profile_custom_css', array(
				'name'           => __( 'Custom CSS', 'inbound' ),
				'label_location' => 'top',
				'desc'           => __( 'Custom CSS code to be added to the page header.', 'inbound' ),
				'rows'           => '10'
			) );
			$meta_advanced->addTextarea( 'sr_inbound_advanced_profile_custom_scripts', array(
				'name'           => __( 'Custom Scripts', 'inbound' ),
				'label_location' => 'top',
				'desc'           => __( 'Custom scripts to be added to the page header.', 'inbound' ),
				'rows'           => '10'
			) );
			$meta_advanced->Finish();


			/*
			 * Meta Box: Modal Windows - Options
 			*/
			$config             = array(
				'id'             => 'inbound_page_modal',
				'title'          => __( 'Options', 'inbound' ),
				'pages'          => array( 'modal' ),
				'context'        => 'normal',
				'priority'       => 'high',
				'fields'         => array(),
				'local_images'   => false,
				'use_with_theme' => get_template_directory_uri() . '/lib/admin'
			);
			$meta_modal_options = new SR_Meta_Box( $config );
			$meta_modal_options->addSelect( 'sr_inbound_modal_render_mode', array(
				'content'     => __( 'Modal Content (w/ Basic Content Filters)', 'inbound' ),
				'raw'         => __( 'Modal Content (Raw HTML/Text)', 'inbound' ),
				'oembed'      => __( 'Modal Content (oEmbed URL)', 'inbound' ),
				'pagebuilder' => __( 'Page Builder (Experimental)', 'inbound' ),
			), array( 'name' => __( 'Content', 'inbound' ), 'std' => 'content', 'class' => 'no-fancy' ) );
			$meta_modal_options->addColor( 'sr_inbound_modal_background', array(
				'name' => __( 'Background Color', 'inbound' ),
				'std'  => '#ffffff'
			) );
			$meta_modal_options->addColor( 'sr_inbound_modal_text', array(
				'name' => __( 'Text Color', 'inbound' ),
				'std'  => '#000000'
			) );
			//$meta_modal_options->addTextarea( 'sr_inbound_modal_custom_css', array( 'name' => __( 'Custom CSS', 'inbound' ), 'label_location' => 'top', 'desc' => __( 'Custom CSS to be applied to this modal window.', 'inbound' ), 'rows' => '10' ) );
			$meta_modal_options->Finish();
		}


		/* *******************************************************************************
		 * Theme Options Panel
		   *******************************************************************************/

		/**
		 * Set up main theme options page
		 */
		$config = array(
			'menu'           => 'theme',
			//sub page to settings page
			'page_title'     => __( 'Theme Options', 'inbound' ),
			//The name of this page
			'icon_url'       => 'div',
			'capability'     => 'edit_theme_options',
			// The capability needed to view the page
			'option_group'   => 'inbound_options',
			//the name of the option to create in the database
			'id'             => 'inbound_admin_page',
			// meta box id, unique per page
			'fields'         => array(),
			// list of fields (can be added by field arrays)
			'local_images'   => false,
			// Use local or hosted images (meta box images for add/remove)
			'use_with_theme' => get_template_directory_uri() . '/lib/admin',
			//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
			'google_fonts'   => true
		);


		$options_panel = new SR_Admin_Page( $config );
		$options_panel->OpenTabs_container( '' );

		$global_tabs = array();

		$global_tabs['general'] = __( 'General', 'inbound' );

		$global_tabs['blog']    = __( 'Blog', 'inbound' );
		$global_tabs['mailing'] = __( 'Forms', 'inbound' );

		if ( function_exists( 'is_woocommerce' ) ) {
			$global_tabs['woocommerce'] = __( 'WooCommerce', 'inbound' );
		}


		if ( function_exists( 'siteorigin_panels_render' ) ) {
			$global_tabs['pagebuilder'] = __( 'Page Builder', 'inbound' );
		}

		$global_tabs['advanced'] = __( 'Advanced', 'inbound' );

		$global_tabs['export'] = __( 'Export', 'inbound' );


		if ( ! inbound_hide_support_links() ) {
			$global_tabs['about'] = __( 'Support', 'inbound' );
		}


		$options_panel->TabsListing( array(
			'links' => $global_tabs
		) );


		/*
		 * About
		 */
		if ( ! inbound_hide_support_links() ) {

			$options_panel->OpenTab( 'about' );
			$options_panel->Title( __( "About Inbound", 'inbound' ) );


			$options_panel->Subtitle( __( "Theme", 'inbound' ) );

			$options_panel->addParagraph( sprintf( __( 'Inbound is a multi-purpose online marketing and lead generation theme by <a href="%1$s" target="_blank">ShapingRain.com</a>.', 'inbound' ), esc_url( 'http://www.shapingrain.com' ) ) );

			if ( is_child_theme() ) {
				$options_panel->addTextLabel( 'theme_version', array(
					'name'  => __( "Installed Version", 'inbound' ),
					'value' => INBOUND_THEME_VERSION . __( " (Parent Theme)", 'inbound' )
				) );
			} else {
				$options_panel->addTextLabel( 'theme_version', array(
					'name'  => __( "Installed Version", 'inbound' ),
					'value' => INBOUND_THEME_VERSION
				) );
			}


			$options_panel->Subtitle( esc_html__( "Customer Support", 'inbound' ) );
			$options_panel->addParagraph( sprintf( inbound_esc_html( __( 'With your purchase of Inbound you have access to free premium support <a href="%1$s" target="_blank">via email</a>. We do not provide support via the comments section on themeforest.', 'inbound' ) ), esc_url( 'https://shapingrain.zendesk.com/hc/en-us/requests/new'  ) ) );
			$options_panel->addCheckbox( 'support_options_debugging', array(
					'name'           => esc_html__( 'Allow Option Changes', 'inbound' ),
					'caption'        => esc_html__( 'Allow overwriting of theme options via debugging GET variables', 'inbound' ),
					'desc'           => esc_html__( 'If this option is checked, the theme will allow users to allow GET variables in the browser\'s address bar to overwrite theme options. Use with care and disable when not used.', 'inbound' ),
					'class'          => 'no-toggle',
					'std'            => false,
					'group-selector' => true
			) );
			$options_panel->addTextarea( 'support_options_debugging_allowed_parameters', array(
					'name'        => esc_html__( 'Allowed Parameters', 'inbound' ),
					'desc'        => esc_html__( 'Only options listed here, one per line, can be overwritten using GET variables', 'inbound' ),
					'std'         => '',
					'is-group'    => 'support_options_debugging',
					'group-value' => array( 'checked' )
			) );
			$options_panel->addText( 'support_options_debugging_token', array(
					'name'        => esc_html__( 'Support Access Token', 'inbound' ),
					'desc'        => esc_html__( 'You must provide this token to support, if requested, to enable them to overwrite options using GET variables', 'inbound' ),
					'std'         => inbound_generate_random_string(),
					'is-group'    => 'support_options_debugging',
					'group-value' => array( 'checked' )
			) );
			$options_panel->addCheckbox( 'support_options_dev_mode', array(
					'name'    => esc_html__( 'Development Mode', 'inbound' ),
					'caption' => esc_html__( 'Enable development mode', 'inbound' ),
					'desc'    => esc_html__( 'Enable additional options for developers.', 'inbound' ),
					'class'   => 'no-toggle',
					'std'     => false
			) );

			$options_panel->CloseTab();
		}


		/*
		 * General Settings
 		*/
		$options_panel->OpenTab( 'general' );

		$options_panel->Title( esc_html__( "General Settings", 'inbound' ) );

		$options_panel->Subtitle( esc_html__( "Layout and Design", 'inbound' ) );

		if ( defined ('INBOUND_FEATURE_PACK') ) {
			$options_panel->addPosts( 'default_profile', array( 'post_type' => 'profile' ), array(
				'name'  => esc_html__( 'Default Profile', 'inbound' ),
				'desc'  => esc_html__( 'A profile is a set of setings, e.g. colours, background images, social profiles etc. that can be applied to the entire site or specific pages. This settings profile will be used when no profile is selected for a particular page, as well as for the blog and all pages generated by third-party plug-ins.', 'inbound' ),
				'class' => 'no-fancy',
				'none'  => false
			) );
			$options_panel->addPosts( 'default_banner', array( 'post_type' => 'banner' ), array(
				'class' => 'no-fancy',
				'name'  => esc_html__( 'Default Banner', 'inbound' ),
				'desc'  => esc_html__( 'If a banner is selected, that banner will be used for all pages for which no individual banner has been selected.', 'inbound' )
			) );
		}

		// button styles
		$repeater_fields = array();

		$repeater_fields[] = $options_panel->addText( 'name', array(
			'name'  => esc_html__( 'Name', 'inbound' ),
			'class' => 'widefat at-block-title-input'
		), true );
		$repeater_fields[] = $options_panel->addUid( 'uid', array( 'name' => esc_html__( 'Unique ID', 'inbound' ) ), true );
		$repeater_fields[] = $options_panel->addTypography(
			'font',
			array(
				'name' => esc_html__( 'Font', 'inbound' ),
				'std'  => array(
					'face'   => 'helvetica',
					'weight' => 'regular',
					'size'   => '13px',
					'color'  => false
				)
			),
			true
		);
		$repeater_fields[] = $options_panel->addCheckbox( 'force_fonts', array(
			'name'    => esc_html__( 'Apply fonts to default', 'inbound' ),
			'caption' => esc_html__( 'Enforce font styles to be used for default button style.', 'inbound' ),
			'class'   => 'no-toggle',
			'std'     => 0
		), true );
		$repeater_fields[] = $options_panel->addCheckbox( 'shadow', array(
			'name'    => esc_html__( 'Shadow', 'inbound' ),
			'caption' => esc_html__( 'Add drop shadow to buttons.', 'inbound' ),
			'class'   => 'no-toggle',
			'std'     => 0
		), true );
		$repeater_fields[] = $options_panel->addCheckbox( 'border', array(
			'name'    => esc_html__( 'Border', 'inbound' ),
			'caption' => esc_html__( 'Add a border around the button.', 'inbound' ),
			'class'   => 'no-toggle',
			'std'     => 0
		), true );

		$repeater_fields[] = $options_panel->addText(
			'radius',
			array(
				'name'       => esc_html__( 'Rounded Corners', 'inbound' ),
				'std'        => '5',
				'class'      => 'no-fancy',
				'validate'   => array(
					'numeric' => array(
						'param'   => '',
						'message' => esc_html__( "must be a numeric value", 'inbound' )
					)
				),
				'field_type' => 'number',
				'text_after' => 'px',
			),
			true
		);

		// button default state
		$repeater_fields[] = $options_panel->addSection( esc_html__( 'Default State', 'inbound' ), array(), true );

		$repeater_fields[] = $options_panel->addSelect( 'default_background_mode', array(
			'solid'       => esc_html__( 'Solid Color', 'inbound' ),
			'gradient'    => esc_html__( 'Gradient', 'inbound' ),
			'transparent' => esc_html__( 'Transparent', 'inbound' )
		), array(
			'name'           => esc_html__( 'Background Mode (Default)', 'inbound' ),
			'std'            => 'solid',
			'class'          => 'no-fancy',
			'group-selector' => true
		), true );

		$repeater_fields[] = $options_panel->addColor( 'default_color_1', array(
			'name' => esc_html__( 'Color 1', 'inbound' ),
			'std'  => '#ffffff'
		), true );
		$repeater_fields[] = $options_panel->addColor( 'default_color_2', array(
			'name'        => esc_html__( 'Color 2', 'inbound' ),
			'std'         => '#ffffff',
			'is-group'    => 'default_background_mode',
			'group-value' => array( 'gradient' )
		), true );

		$repeater_fields[] = $options_panel->addColor( 'default_color_text', array(
			'name' => esc_html__( 'Text Color', 'inbound' ),
			'std'  => '#ffffff'
		), true );

		// button hover state
		$repeater_fields[] = $options_panel->addSection( esc_html__( 'Hover State', 'inbound' ), array(), true );

		$repeater_fields[] = $options_panel->addSelect( 'hover_background_mode', array(
			'solid'       => esc_html__( 'Solid Color', 'inbound' ),
			'gradient'    => esc_html__( 'Gradient', 'inbound' ),
			'transparent' => esc_html__( 'Transparent', 'inbound' )
		), array(
			'name'           => esc_html__( 'Background Mode (Default)', 'inbound' ),
			'std'            => 'solid',
			'class'          => 'no-fancy',
			'group-selector' => true
		), true );

		$repeater_fields[] = $options_panel->addColor( 'hover_color_1', array(
			'name' => esc_html__( 'Color 1', 'inbound' ),
			'std'  => '#ffffff'
		), true );
		$repeater_fields[] = $options_panel->addColor( 'hover_color_2', array(
			'name'        => esc_html__( 'Color 2', 'inbound' ),
			'std'         => '#ffffff',
			'is-group'    => 'hover_background_mode',
			'group-value' => array( 'gradient' )
		), true );

		$repeater_fields[] = $options_panel->addColor( 'hover_color_text', array(
			'name' => esc_html__( 'Text Color', 'inbound' ),
			'std'  => '#ffffff'
		), true );


		$options_panel->addRepeaterBlock(
			'global_button_styles',
			array(
				'sortable'       => true,
				'inline'         => false,
				'name'           => esc_html__( 'Button Styles', 'inbound' ),
				'fields'         => $repeater_fields,
				'desc'           => esc_html__( 'Add, edit and re-order button styles.', 'inbound' ),
				'label_location' => 'none'
			)
		);


		/*
		 * Embedded Media Responsiveness
		 */
		$options_panel->Subtitle( esc_html__( "Embedded Media Responsiveness", 'inbound' ) );
		$options_panel->addCheckbox( 'content_responsive_images', array(
			'name'    => esc_html__( 'Content Section', 'inbound' ),
			'caption' => esc_html__( 'Remove width and height attributes from all images in content section', 'inbound' ),
			'desc'    => esc_html__( 'If this option is checked, the theme will remove all static width and height attributes from thumbnails, featured images, gallery images etc., for responsiveness.', 'inbound' ),
			'class'   => 'no-toggle',
			'std'     => true
		) );
		$options_panel->addCheckbox( 'content_responsive_videos', array(
			'name'    => esc_html__( 'Embedded Media', 'inbound' ),
			'caption' => esc_html__( 'Remove width and height attributes from supported embedded media', 'inbound' ),
			'desc'    => esc_html__( 'If this option is checked, the theme will remove all static width and height attributes from media embeds, for responsiveness.', 'inbound' ),
			'class'   => 'no-toggle',
			'std'     => true
		) );

		/*
		 * Theme Features
		 */
		$options_panel->Subtitle( esc_html__( "Theme Features", 'inbound' ) );
		$options_panel->addCheckbox( 'content_page_comments', array(
			'name'    => esc_html__( 'Page Comments', 'inbound' ),
			'caption' => esc_html__( 'Enable comments for pages', 'inbound' ),
			'desc'    => esc_html__( 'If this option is checked, a comment form will be displayed underneath pages, if comments are active for that page.', 'inbound' ),
			'std'     => false
		) );


		/*
		 * Web Fonts
		 */
		$options_panel->Subtitle( esc_html__( "Web Fonts", 'inbound' ) );

		$subsets = array(
			'latin'        => esc_html__( 'Latin', 'inbound' ),
			'latin-ext'    => esc_html__( 'Latin (Extended)', 'inbound' ),
			'menu'         => esc_html__( 'Menu', 'inbound' ),
			'greek'        => esc_html__( 'Greek', 'inbound' ),
			'greek-ext'    => esc_html__( 'Greek (Extended)', 'inbound' ),
			'cyrillic'     => esc_html__( 'Cyrillic', 'inbound' ),
			'cyrillic-ext' => esc_html__( 'Cyrillic (Extended)', 'inbound' ),
			'vietnamese'   => esc_html__( 'Vietnamese', 'inbound' ),
			'arabic'       => esc_html__( 'Arabic', 'inbound' ),
			'khmer'        => esc_html__( 'Khmer', 'inbound' ),
			'lao'          => esc_html__( 'Lao', 'inbound' ),
			'tamil'        => esc_html__( 'Tamil', 'inbound' ),
			'bengali'      => esc_html__( 'Bengali', 'inbound' ),
			'hindi'        => esc_html__( 'Hindi', 'inbound' ),
			'korean'       => esc_html__( 'Korean', 'inbound' )
		);
		$options_panel->addCheckboxList( 'web_fonts_subsets',
			$subsets,
			array(
				'name'  => 'Google Web Fonts Subsets',
				'std'   => array( 'latin' ),
				'class' => 'no-fancy',
				'desc'  => esc_html__( 'Select which subsets should be loaded. Subsets are only applied when the selected fonts support the selection.', 'inbound' )
			)
		);


		$repeater_fields = array();

		$repeater_fields[] = $options_panel->addText( 'name', array(
			'name'  => esc_html__( 'Display Name', 'inbound' ),
			'class' => 'widefat',
			'desc'  => esc_html__( 'This name will be displayed in the font selection interface. It serves no other function.', 'inbound' )
		), true );
		$repeater_fields[] = $options_panel->addText( 'face_name', array(
			'name'  => esc_html__( 'Font Face Name', 'inbound' ),
			'class' => 'widefat',
			'desc'  => esc_html__( 'This name will be used in the font-face CSS attribute to identify the font.', 'inbound' )
		), true );
		$repeater_fields[] = $options_panel->addText( 'face_fallback', array(
			'name'  => esc_html__( 'Font Face Fallback', 'inbound' ),
			'class' => 'widefat',
			'desc'  => esc_html__( 'Comma-separated and escaped list of fallback fonts to be used.', 'inbound' ),
			'std'   => 'Helvetica, sans-serif'
		), true );
		$repeater_fields[] = $options_panel->addText( 'url_eot', array(
			'name'  => esc_html__( 'EOT URL', 'inbound' ),
			'class' => 'widefat',
			'desc'  => esc_html__( 'This is the URL pointing to the .eot (Open Type) file.', 'inbound' )
		), true );
		$repeater_fields[] = $options_panel->addText( 'url_woff', array(
			'name'  => esc_html__( 'WOFF  URL', 'inbound' ),
			'class' => 'widefat',
			'desc'  => esc_html__( 'This is the URL pointing to the .woff (Web Open Font Format) file.', 'inbound' )
		), true );
		$repeater_fields[] = $options_panel->addText( 'url_ttf', array(
			'name'  => esc_html__( 'TTF  URL', 'inbound' ),
			'class' => 'widefat',
			'desc'  => esc_html__( 'This is the URL pointing to the .ttf (True Type Font) file.', 'inbound' )
		), true );
		$repeater_fields[] = $options_panel->addText( 'url_svg', array(
			'name'  => esc_html__( 'SVG  URL', 'inbound' ),
			'class' => 'widefat',
			'desc'  => esc_html__( 'This is the URL pointing to the .svg (Scalable Vector Graphics Font) file.', 'inbound' )
		), true );

		$options_panel->addRepeaterBlock( 'web_fonts_custom', array(
			'sortable' => true,
			'inline'   => false,
			'name'     => esc_html__( 'Custom Web Fonts', 'inbound' ),
			'fields'   => $repeater_fields,
			'desc'     => esc_html__( 'This feature enables you to use compatible web font files from external sources. The fonts database needs to be refreshed after each change.', 'inbound' )
		) );

		$options_panel->addButton( 'web_fonts_refresh', array(
			'name'    => esc_html__( "Update Database", 'inbound' ),
			'caption' => esc_html__( "Refresh Web Fonts Database", 'inbound' ),
			'desc'    => esc_html__( "Retrieve and process new web fonts and write changes to the database. If new fonts are added, this will make them available to the theme.", 'inbound' )
		) );

		/*
		 * Maps API Key
		 */
		$options_panel->Subtitle( esc_html__( "Google Maps API", 'inbound' ) );

		$options_panel->addText( 'google_api_key', array(
			'name' => esc_html__( 'Google Maps API Key', 'inbound' ),
			'desc' => esc_html__( 'You need to provide a Google Map API Key in order to include maps.', 'inbound' ),
			'std'  => ''
		) );



		$options_panel->CloseTab();



		/*
		 * Blog Settings
		 */

		$options_panel->OpenTab( 'blog' );

		$options_panel->Title( esc_html__( "Blog Settings", 'inbound' ) );


		if ( defined ('INBOUND_FEATURE_PACK') ) {
			$options_panel->Subtitle( esc_html__( "General", 'inbound' ) );

			$options_panel->addPosts( 'default_profile_blog', array( 'post_type' => 'profile' ), array(
				'name'  => esc_html__( 'Blog Default Profile', 'inbound' ),
				'desc'  => esc_html__( 'This settings profile will be used for the blog, including all archives. If no profile is selected, the site\'s default profile will be used.', 'inbound' ),
				'class' => 'no-fancy',
				'none'  => false
			) );
			$options_panel->addPosts( 'default_banner_blog', array( 'post_type' => 'banner' ), array(
				'name'  => esc_html__( 'Blog Default Banner', 'inbound' ),
				'desc'  => esc_html__( 'This overrides the default profile\'s banner setting and displays the selected banner on every blog page by default. If no selection is made, the profile\'s banner will be used instead, if set.', 'inbound' ),
				'class' => 'no-fancy',
				'none'  => false
			) );
		}

		$options_panel->Subtitle( esc_html__( "Layout", 'inbound' ) );

		$blog_layout_list = array(
			'minimal'  => array(
				'label' => esc_html__( 'List (Minimal)', 'inbound' ),
				'image' => SR_ADMIN_URL . '/images/icons/blog-layouts/layout_minimal.png'
			),
			'medium'   => array(
				'label' => esc_html__( 'List (Medium)', 'inbound' ),
				'image' => SR_ADMIN_URL . '/images/icons/blog-layouts/layout_list_medium.png'
			),
			'list'     => array(
				'label' => esc_html__( 'List (Large)', 'inbound' ),
				'image' => SR_ADMIN_URL . '/images/icons/blog-layouts/layout_list_default.png'
			),
			'grid'     => array(
				'label' => esc_html__( 'Grid', 'inbound' ),
				'image' => SR_ADMIN_URL . '/images/icons/blog-layouts/layout_grid.png'
			),
			'masonry'  => array(
				'label' => esc_html__( 'Masonry', 'inbound' ),
				'image' => SR_ADMIN_URL . '/images/icons/blog-layouts/layout_masonry.png'
			),
			'timeline' => array(
				'label' => esc_html__( 'Timeline', 'inbound' ),
				'image' => SR_ADMIN_URL . '/images/icons/blog-layouts/layout_timeline.png'
			)
		);

		$timeline_group_display_options = array(
			'day'   => esc_html__( 'Day', 'inbound' ),
			'month' => esc_html__( 'Month', 'inbound' ),
			'year'  => esc_html__( 'Year', 'inbound' )
		);

		$options_panel->addSelect( 'blog_layout',
			$blog_layout_list,
			array(
				'name'           => 'Blog Layout',
				'std'            => 'list',
				'image-picker'   => true,
				'desc'           => esc_html__( 'This layout is used for the blog index page.', 'inbound' ),
				'group-selector' => true
			)
		);

		$options_panel->addSelect( 'blog_timeline_group_by',
			$timeline_group_display_options,
			array(
				'name'        => 'Group Posts By',
				'std'         => '3',
				'class'       => 'no-fancy',
				'desc'        => esc_html__( 'Select whether you would like posts to be grouped by day or month.', 'inbound' ),
				'is-group'    => 'blog_layout',
				'group-value' => array( 'timeline' )
			)
		);

		$options_panel->addSelect( 'blog_grid_columns',
			array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
			),
			array(
				'name'        => 'Columns per Row',
				'std'         => '3',
				'class'       => 'no-fancy',
				'desc'        => esc_html__( 'Amount of columns per row for blog index pages.', 'inbound' ),
				'is-group'    => 'blog_layout',
				'group-value' => array( 'grid', 'masonry' )
			)
		);

		$options_panel->addSelect( 'blog_archive_layout',
			$blog_layout_list,
			array(
				'name'           => 'Archive Layout',
				'std'            => 'list',
				'image-picker'   => true,
				'desc'           => esc_html__( 'This layout is used for all archives, e.g. category, tag and author archives.', 'inbound' ),
				'group-selector' => true
			)
		);

		$options_panel->addSelect( 'blog_archive_timeline_group_by',
			$timeline_group_display_options,
			array(
				'name'        => 'Group Posts By',
				'std'         => '3',
				'class'       => 'no-fancy',
				'desc'        => esc_html__( 'Select whether you would like posts to be grouped by day or month.', 'inbound' ),
				'is-group'    => 'blog_archive_layout',
				'group-value' => array( 'timeline' )
			)
		);

		$options_panel->addSelect( 'blog_archive_grid_columns',
			array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
			),
			array(
				'name'        => 'Columns per Row',
				'std'         => '3',
				'class'       => 'no-fancy',
				'desc'        => esc_html__( 'Amount of columns per row for all archives.', 'inbound' ),
				'is-group'    => 'blog_archive_layout',
				'group-value' => array( 'grid', 'masonry' )
			)
		);

		$options_panel->Subtitle( esc_html__( "Custom Headlines", 'inbound' ) );

		$options_panel->addText( 'blog_headline', array(
			'name' => esc_html__( 'Blog Headline', 'inbound' ),
			'desc' => esc_html__( 'This headline is displayed on all blog index pages (posts page or front page, if set to display the blog).', 'inbound' ),
			'std'  => ''
		) );
		$options_panel->addText( 'blog_archive_headline', array(
			'name' => esc_html__( 'Archive Headline (Prefix)', 'inbound' ),
			'desc' => esc_html__( 'This headline is displayed on all blog archives.', 'inbound' ),
			'std'  => ''
		) );

		$options_panel->Subtitle( esc_html__( "Content", 'inbound' ) );

		$display_mode_options = array(
			'excerpt' => esc_html__( 'Excerpt', 'inbound' ),
			'full'    => esc_html__( 'Full Post', 'inbound' ),
		);

		$options_panel->addSelect(
			'blog_content_mode_index',
			$display_mode_options,
			array(
				'name'        => 'List View (Index Pages)',
				'std'         => 'excerpt',
				'desc'        => esc_html__( 'This sets the display mode applied to posts displayed on index pages, such as a blog set as the front page, or a static posts page.', 'inbound' ),
				'is-group'    => 'blog_layout',
				'group-value' => array( 'minimal', 'list', 'medium' )
			) );

		$options_panel->addSelect(
			'blog_content_mode_archive',
			$display_mode_options,
			array(
				'name'        => 'List View (Archives)',
				'std'         => 'excerpt',
				'desc'        => esc_html__( 'This sets the display mode applied to posts displayed on archive pages, such as category or tag archives.', 'inbound' ),
				'is-group'    => 'blog_archive_layout',
				'group-value' => array( 'minimal', 'list', 'medium' )
			) );

		$options_panel->addText(
			'blog_excerpt_length',
			array(
				'name'       => esc_html__( 'Excerpt Length', 'inbound' ),
				'std'        => '25',
				'validate'   => array(
					'numeric' => array(
						'param'   => '',
						'message' => esc_html__( "must be a numeric value", 'inbound' )
					)
				),
				'desc'       => esc_html__( 'This defines the length of excerpts as displayed on blog index and archive pages, in words.', 'inbound' ),
				'text_after' => esc_html__( 'words', 'inbound' ),
				'field_type' => 'number'
			)
		);

		$options_panel->addCheckbox( 'blog_related_posts', array(
			'name'    => esc_html__( 'Related Posts', 'inbound' ),
			'desc'    => esc_html__( 'If this option is checked, related posts will be displayed underneath single page comments.', 'inbound' ),
			'caption' => esc_html__( 'Display related posts', 'inbound' ),
			'class'   => 'no-toggle',
			'std'     => true
		) );


		$options_panel->Subtitle( esc_html__( "Sidebars", 'inbound' ) );

		$options_panel->addCheckbox( 'blog_sidebar_front', array(
			'name'    => esc_html__( 'Index Page', 'inbound' ),
			'caption' => esc_html__( 'Display sidebar on index page', 'inbound' ),
			'desc'    => esc_html__( 'If this option is checked, a sidebar will be displayed on post index pages, that is a blog set as the front page, or a static posts page.', 'inbound' ),
			'class'   => 'no-toggle',
			'std'     => true
		) );
		$options_panel->addCheckbox( 'blog_sidebar_archives', array(
			'name'    => esc_html__( 'Archive Pages', 'inbound' ),
			'caption' => esc_html__( 'Display sidebar on archive pages', 'inbound' ),
			'desc'    => esc_html__( 'If this option is checked, a sidebar will be displayed on post archive pages, e.g. category and tag archives.', 'inbound' ),
			'class'   => 'no-toggle',
			'std'     => true
		) );
		$options_panel->addCheckbox( 'blog_sidebar_posts', array(
			'name'    => esc_html__( 'Single Post Pages', 'inbound' ),
			'caption' => esc_html__( 'Display sidebar on single post pages', 'inbound' ),
			'desc'    => esc_html__( 'If this option is checked, a sidebar will be displayed on single post pages.', 'inbound' ),
			'class'   => 'no-toggle',
			'std'     => true
		) );

		$options_panel->addSelect( 'blog_sidebar_position',
			array(
				'left'  => esc_html__( 'left', 'inbound' ),
				'right' => esc_html__( 'right', 'inbound' )
			),
			array(
				'name'  => 'Sidebar Position',
				'std'   => 'right',
				'class' => 'no-fancy',
				'desc'  => esc_html__( 'Select where you would like the sidebar to be displayed.', 'inbound' )
			)
		);

		$options_panel->Subtitle( esc_html__( "Navigation", 'inbound' ) );

		$nav_types = array(
			false      => esc_html__( 'None (only first page)', 'inbound' ),
			'numeric'  => esc_html__( 'Numeric (1...10)', 'inbound' ),
			'nextprev' => esc_html__( 'Older posts/newer posts', 'inbound' )
		);


		$options_panel->addSelect( 'blog_posts_navigation',
			$nav_types,
			array(
				'name'    => 'Blog',
				'caption' => 'Posts navigation on blog',
				'std'     => 'nextprev',
				'class'   => 'no-fancy',
				'desc'    => esc_html__( 'Define which posts navigation type you would like to use for blog index pages.', 'inbound' )
			)
		);

		$options_panel->addSelect( 'archive_posts_navigation',
			$nav_types,
			array(
				'name'    => 'Archives',
				'caption' => 'Posts navigation on archive pages',
				'std'     => 'nextprev',
				'class'   => 'no-fancy',
				'desc'    => esc_html__( 'Define which posts navigation type you would like to use for blog archive pages, such as the category and author archives.', 'inbound' )
			)
		);

		$nav_types_single = array(
			false      => esc_html__( 'None', 'inbound' ),
			'nextprev' => esc_html__( 'Previous post/next post', 'inbound' )
		);

		$options_panel->addSelect( 'single_posts_navigation',
			$nav_types_single,
			array(
				'name'    => 'Single Posts',
				'caption' => 'Posts navigation on single posts',
				'std'     => 'nextprev',
				'class'   => 'no-fancy',
				'desc'    => esc_html__( 'Define which posts navigation type you would like to use for single post pages only.', 'inbound' )
			)
		);



		$options_panel->Subtitle( esc_html__( "Social Sharing", 'inbound' ) );

		$options_panel->addCheckbox( 'blog_share_enabled', array(
			'name'           => esc_html__( 'Blog Posts', 'inbound' ),
			'caption'        => esc_html__( 'Enable social sharing icons for blog posts', 'inbound' ),
			'class'          => 'no-toggle',
			'std'            => true,
			'group-selector' => true
		) );

		$share_options = array(
			'facebook'    => esc_html__( 'Facebook', 'inbound' ),
			'twitter'     => esc_html__( 'Twitter', 'inbound' ),
			'googleplus'  => esc_html__( 'Google+', 'inbound' ),
			'tumblr'      => esc_html__( 'Tumblr', 'inbound' ),
			'pinterest'   => esc_html__( 'Pinterest', 'inbound' ),
			'linkedin'    => esc_html__( 'LinkedIn', 'inbound' ),
			'reddit'      => esc_html__( 'reddit', 'inbound' ),
			'stumbleupon' => esc_html__( 'StumbleUpon', 'inbound' ),
			'vk'          => esc_html__( 'VK', 'inbound' ),
		);

		$share_options = apply_filters( 'inbound_share_icons', $share_options );

		$style_types = array(
			'transparent' => esc_html__( 'Transparent', 'inbound' ),
			'white'       => esc_html__( 'White', 'inbound' ),
			'black'       => esc_html__( 'Black', 'inbound' ),
			'color'       => esc_html__( 'Solid Color', 'inbound' )
		);
		$options_panel->addSelect( 'blog_share_style',
			$style_types,
			array(
				'name'        => 'Style',
				'caption'     => 'Icon style',
				'std'         => 'transparent',
				'class'       => 'no-fancy',
				'desc'        => esc_html__( 'Select which style you would like to use for the sharing icons. This setting can be overwritten by widget settings.', 'inbound' ),
				'is-group'    => 'blog_share_enabled',
				'group-value' => array( 'checked' )
			)
		);

		$sizes = array(
			'1' => esc_html__( 'Tiny', 'inbound' ),
			'2' => esc_html__( 'Small', 'inbound' ),
			'3' => esc_html__( 'Medium', 'inbound' ),
			'4' => esc_html__( 'Large', 'inbound' )
		);
		$options_panel->addSelect( 'blog_share_size',
			$sizes,
			array(
				'name'        => 'Size',
				'caption'     => 'Icon size',
				'std'         => '1',
				'class'       => 'no-fancy',
				'desc'        => esc_html__( 'Select a size for the sharing icons. This setting can be overwritten by widget settings.', 'inbound' ),
				'is-group'    => 'blog_share_enabled',
				'group-value' => array( 'checked' )
			)
		);


		$options_panel->addCheckboxList( 'blog_share_options',
			$share_options,
			array(
				'name'        => 'Display icons for these share-enabled services',
				'std'         => array( 'facebook', 'googleplus', 'twitter' ),
				'class'       => 'no-fancy',
				'is-group'    => 'blog_share_enabled',
				'group-value' => array( 'checked' ),
				'desc'        => esc_html__( 'Select which icons you would like to display. These icons will be added to the end of your blog posts if social sharing icons are enabled. Please note that not all social networks have an option to share posts.', 'inbound' ),
				'sortable'    => true
			)
		);

		$options_panel->addText( 'blog_share_custom',
			array(
				'name'        => esc_html__( 'Custom Shortcode', 'inbound' ),
				'desc'        => esc_html__( 'If not empty, the content of this field will be parsed for shortcodes and the output added after your post content instead of the built-in social sharing icons.', 'inbound' ),
				'std'         => '',
				'is-group'    => 'blog_share_enabled',
				'group-value' => array( 'checked' ),
			)
		);

		$options_panel->CloseTab();

		/*
		 * Mailing List Services
		 */
		$options_panel->OpenTab( 'mailing' );
		$options_panel->Title( esc_html__( "Mailing Lists and Forms", 'inbound' ) );

		$options_panel->Subtitle( esc_html__( "Embed Codes", 'inbound' ) );

		$options_panel->addParagraph( inbound_esc_html( __( 'This section allows you to enter third party HTML embed/integration code to be used globally with the INB Opt-In widget. This enables the integration of popular third party mailing list services like MailChimp and AWeber.<br />Contact Form 7 and Gravity Forms are also supported, for contact forms, and they come with their own native and custom widgets.', 'inbound' ) ) );

		$options_panel->addNonce( 'clean_form', array() );

		$repeater_fields = array();

		$repeater_fields[] = $options_panel->addText( 'form_name', array(
			'name'  => esc_html__( 'Name', 'inbound' ),
			'desc'  => esc_html__( 'This name helps you identify your forms.', 'inbound' ),
			'class' => 'widefat'
		), true );
		$repeater_fields[] = $options_panel->addTextarea( 'form_code', array(
			'name'  => esc_html__( 'HTML Embed Code', 'inbound' ),
			'desc'  => esc_html__( 'This field contains plain HTML, just the form tag and the form fields, no additional scripts, styling, labels, wrappers etc.; the Clean Form Code button sanitizes the form input and removes unnecessary mark-up from the raw embed code.', 'inbound' ),
			'class' => 'widefat',
			'form'  => true,
			'code'  => true
		), true );
		$repeater_fields[] = $options_panel->addUid( 'form_uid', array( 'name' => esc_html__( 'Unique ID', 'inbound' ) ), true );

		$options_panel->addRepeaterBlock( 'forms', array(
			'sortable' => false,
			'inline'   => false,
			'name'     => esc_html__( 'Forms', 'inbound' ),
			'fields'   => $repeater_fields,
			'desc'     => esc_html__( 'Create as many forms as you need. Forms can be used in sidebar widgets or within the page builder they can be contact or sign-up forms.', 'inbound' )
		) );


		$options_panel->CloseTab();


		/*
		 * WooCommerce Settings
		 */

		if ( function_exists( 'is_woocommerce' ) ) {
			$options_panel->OpenTab( 'woocommerce' );
			$options_panel->Title( esc_html__( "WooCommerce Settings", 'inbound' ) );

			$options_panel->Subtitle( esc_html__( "General", 'inbound' ) );

			if ( defined ('INBOUND_FEATURE_PACK') ) {
				$options_panel->addPosts( 'default_profile_woocommerce', array( 'post_type' => 'profile' ), array(
					'name'  => esc_html__( 'WC Default Profile', 'inbound' ),
					'desc'  => esc_html__( 'This settings profile will be used for all WooCommerce pages. If no profile is selected, the site\'s default profile will be used.', 'inbound' ),
					'class' => 'no-fancy',
					'none'  => false
				) );
				$options_panel->addPosts( 'default_banner_woocommerce', array( 'post_type' => 'banner' ), array(
					'name'  => esc_html__( 'WC Default Banner', 'inbound' ),
					'desc'  => esc_html__( 'This overrides the default profile\'s banner setting and displays the selected banner on every WooCommerce page by default. If no selection is made, the profile\'s banner will be used instead, if set.', 'inbound' ),
					'class' => 'no-fancy',
					'none'  => false
				) );
			}

			$options_panel->addCheckbox( 'enable_woocommerce_header_cart', array(
				'name'    => esc_html__( 'Shopping Cart', 'inbound' ),
				'caption' => esc_html__( 'Enable WooCommerce shopping cart in header', 'inbound' ),
				'desc'    => esc_html__( 'If this option is checked, a shopping cart icon with a shopping cart widget will be placed in the header.', 'inbound' ),
				'std'     => true
			) );

			$options_panel->Subtitle( esc_html__( "Layout", 'inbound' ) );

			$options_panel->addSelect( 'woocommerce_grid_columns',
				array(
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				array(
					'name'  => 'Columns per Row',
					'std'   => '3',
					'class' => 'no-fancy',
					'desc'  => esc_html__( 'Amount of columns to display per row.', 'inbound' )
				)
			);

			$options_panel->addText(
				'woocommerce_products_per_page',
				array(
					'name'       => esc_html__( 'Products per Page', 'inbound' ),
					'std'        => '12',
					'validate'   => array(
						'numeric' => array(
							'param'   => '',
							'message' => esc_html__( "must be a numeric value", 'inbound' )
						)
					),
					'desc'       => esc_html__( 'This defines how many products are displayed per page. This option applies to all product lists, on index and archive pages, in search results and related products.', 'inbound' ),
					'field_type' => 'number'
				)
			);

			$options_panel->Subtitle( esc_html__( "Sidebar", 'inbound' ) );

			$options_panel->addSelect( 'woocommerce_sidebar_position',
				array(
					false           => esc_html__( 'No Sidebar', 'inbound' ),
					'sidebar-left'  => esc_html__( 'Left', 'inbound' ),
					'sidebar-right' => esc_html__( 'Right', 'inbound' )
				),
				array(
					'name'  => 'Sidebar position',
					'std'   => 'sidebar-right',
					'class' => 'no-fancy',
					'desc'  => esc_html__( 'Select whether and where you would like the sidebar to be displayed.', 'inbound' )
				)
			);

			$options_panel->addCheckbox( 'enable_woocommerce_sidebars', array(
				'name'    => esc_html__( 'Specific Sidebars', 'inbound' ),
				'caption' => esc_html__( 'Enable WooCommerce-specific sidebars', 'inbound' ),
				'desc'    => esc_html__( 'If this option is checked, theme default sidebars will be replaced with WooCommerce-specific sidebars.', 'inbound' ),
				'class'   => 'no-toggle',
				'std'     => true
			) );
			$options_panel->CloseTab();

		}


		/*
		 * Import and Export Settings
		 */

		$options_panel->OpenTab( 'export' );
		$options_panel->Title( esc_html__( "Import and Export", 'inbound' ) );
		$options_panel->addImportExport();
		$options_panel->CloseTab();
		$options_panel->CloseTab();

		/*
		 * Page Builder
		 */
		if ( function_exists( 'siteorigin_panels_render' ) ) {
			$options_panel->OpenTab( 'pagebuilder' );
			$options_panel->Title( esc_html__( "Page Builder", 'inbound' ) );

			$options_panel->addParagraph( sprintf( inbound_esc_html( __( 'Inbound uses the <a href="%1$s" target="_blank">SiteOrigin Page Builder plug-in</a> as its page builder. It comes with its own <a href="%2$s" target="_blank">documentation</a>.', 'inbound' ) ), esc_url( 'https://siteorigin.com/page-builder/' ), esc_url( 'https://siteorigin.com/page-builder/documentation/' ) ) );

			$options_panel->Subtitle( esc_html__( "General Settings", 'inbound' ) );
			$options_panel->addCheckbox( 'pagebuilder_copy_content', array(
				'name'    => esc_html__( 'Copy Content', 'inbound' ),
				'caption' => esc_html__( 'Copy page builder content into default editor', 'inbound' ),
				'desc'    => esc_html__( 'If this option is checked, rendered content will be copied into the default content editor, for plug-ins to be able to pick up that content for analysis.', 'inbound' ),
				'std'     => true
			) );
			$options_panel->addCheckbox( 'pagebuilder_bundled_widgets', array(
				'name'    => esc_html__( 'Bundled Widgets', 'inbound' ),
				'caption' => esc_html__( 'Enable bundled widgets', 'inbound' ),
				'desc'    => esc_html__( 'If this option is checked, the default widgets that ship with the page builder will be available.', 'inbound' ),
				'std'     => true
			) );
			$options_panel->addCheckbox( 'pagebuilder_inline_css', array(
				'name'    => esc_html__( 'Inline CSS', 'inbound' ),
				'caption' => esc_html__( 'Generate inline CSS code', 'inbound' ),
				'std'     => true
			) );

			$options_panel->Subtitle( esc_html__( "Layout and Design", 'inbound' ) );
			$options_panel->addText(
				'pagebuilder_mobile_width',
				array(
					'name'       => esc_html__( 'Mobile Width', 'inbound' ),
					'std'        => '780',
					'validate'   => array(
						'numeric' => array(
							'param'   => '',
							'message' => esc_html__( "must be a numeric value", 'inbound' )
						)
					),
					'desc'       => esc_html__( 'Mobile content width', 'inbound' ),
					'text_after' => 'px',
					'field_type' => 'number'
				)
			);
			$options_panel->addCheckbox( 'pagebuilder_tablet_layout', array(
				'name'    => esc_html__( 'Use Tablet Layout', 'inbound' ),
				'caption' => esc_html__( 'Collapses the layout differently on tablet devices, using the tablet width set below.', 'inbound' ),
				'std'     => true
			) );
			$options_panel->addText(
				'pagebuilder_tablet_width',
				array(
					'name'       => esc_html__( 'Tablet Width', 'inbound' ),
					'std'        => '1024',
					'validate'   => array(
						'numeric' => array(
							'param'   => '',
							'message' => esc_html__( "must be a numeric value", 'inbound' )
						)
					),
					'desc'       => esc_html__( 'Mobile content width (tablet only)', 'inbound' ),
					'text_after' => 'px',
					'field_type' => 'number'
				)
			);
			$options_panel->addText(
				'pagebuilder_row_bottom_margin',
				array(
					'name'       => esc_html__( 'Row Bottom Margin', 'inbound' ),
					'std'        => '0',
					'validate'   => array(
						'numeric' => array(
							'param'   => '',
							'message' => esc_html__( "must be a numeric value", 'inbound' )
						)
					),
					'desc'       => esc_html__( 'Fixed bottom margin for every row', 'inbound' ),
					'text_after' => 'px',
					'field_type' => 'number'
				)
			);
			$options_panel->addText(
				'pagebuilder_cell_side_margins',
				array(
					'name'       => esc_html__( 'Cell Side Margins', 'inbound' ),
					'std'        => '30',
					'validate'   => array(
						'numeric' => array(
							'param'   => '',
							'message' => esc_html__( "must be a numeric value", 'inbound' )
						)
					),
					'desc'       => esc_html__( 'Margin to be added to the sides of each cell', 'inbound' ),
					'text_after' => 'px',
					'field_type' => 'number'
				)
			);

			$options_panel->Subtitle( esc_html__( "Row Defaults", 'inbound' ) );

			$options_panel->addText(
				'pagebuilder_row_margin_bottom',
				array(
					'name'       => esc_html__( 'Margin (Bottom)', 'inbound' ),
					'std'        => '0',
					'validate'   => array(
						'numeric' => array(
							'param'   => '',
							'message' => esc_html__( "must be a numeric value", 'inbound' )
						)
					),
					'desc'       => esc_html__( 'Default bottom margin if none is set for the row.', 'inbound' ),
					'text_after' => 'px',
					'field_type' => 'number'
				)
			);


			$options_panel->addText(
				'pagebuilder_row_padding_top',
				array(
					'name'       => esc_html__( 'Padding (Top)', 'inbound' ),
					'std'        => '45',
					'validate'   => array(
						'numeric' => array(
							'param'   => '',
							'message' => esc_html__( "must be a numeric value", 'inbound' )
						)
					),
					'desc'       => esc_html__( 'Default padding at the top of each row. Used if no value is set in the page builder\'s row options.', 'inbound' ),
					'text_after' => 'px',
					'field_type' => 'number'
				)
			);

			$options_panel->addText(
				'pagebuilder_row_padding_bottom',
				array(
					'name'       => esc_html__( 'Padding (Bottom)', 'inbound' ),
					'std'        => '45',
					'validate'   => array(
						'numeric' => array(
							'param'   => '',
							'message' => esc_html__( "must be a numeric value", 'inbound' )
						)
					),
					'desc'       => esc_html__( 'Default padding at the bottom of each row. Used if no value is set in the page builder\'s row options.', 'inbound' ),
					'text_after' => 'px',
					'field_type' => 'number'
				)
			);

			$options_panel->addText(
				'pagebuilder_row_padding_left',
				array(
					'name'       => esc_html__( 'Padding (Left)', 'inbound' ),
					'std'        => '0',
					'validate'   => array(
						'numeric' => array(
							'param'   => '',
							'message' => esc_html__( "must be a numeric value", 'inbound' )
						)
					),
					'desc'       => esc_html__( 'Default padding at the left of each row. Used if no value is set in the page builder\'s row options.', 'inbound' ),
					'text_after' => 'px',
					'field_type' => 'number'
				)
			);

			$options_panel->addText(
				'pagebuilder_row_padding_right',
				array(
					'name'       => esc_html__( 'Padding (Right)', 'inbound' ),
					'std'        => '0',
					'validate'   => array(
						'numeric' => array(
							'param'   => '',
							'message' => esc_html__( "must be a numeric value", 'inbound' )
						)
					),
					'desc'       => esc_html__( 'Default padding at the right of each row. Used if no value is set in the page builder\'s row options.', 'inbound' ),
					'text_after' => 'px',
					'field_type' => 'number'
				)
			);


			$options_panel->Subtitle( esc_html__( "Post Types", 'inbound' ) );

			$options_panel->addPostTypes( 'pagebuilder_post_types',
				array(),
				array(
					'name'  => 'Supported Post Types',
					'std'   => array( 'post', 'page', 'banner', 'modal' ),
					'class' => 'no-fancy',
					'desc'  => esc_html__( 'Select the post types for which the page builder should be available. Note that some features designed for posts and pages may not or only partially work with custom post types.', 'inbound' )
				)
			);


			$options_panel->CloseTab();
		}

		/*
		 * Advanced
		 */

		$options_panel->OpenTab( 'advanced' );

		$options_panel->Title( esc_html__( "Advanced Settings", 'inbound' ) );

		$options_panel->Subtitle( esc_html__( "Global Custom Code", 'inbound' ) );
		$options_panel->addParagraph( esc_html__( "Code added to this section is always applied. It can be overwritten by profiles or individual pages.", 'inbound' ) );
		$options_panel->addTextarea( 'advanced_global_css', array(
			'name' => esc_html__( 'Custom CSS', 'inbound' ),
			'desc' => esc_html__( 'This custom CSS will be added to the theme\'s header.', 'inbound' ),
			'std'  => ''
		) );
		$options_panel->addTextarea( 'advanced_global_scripts_header', array(
			'name' => esc_html__( 'Custom Scripts (Header)', 'inbound' ),
			'desc' => esc_html__( 'Custom scripts entered here will be added to the theme\'s header.', 'inbound' ),
			'std'  => ''
		) );
		$options_panel->addTextarea( 'advanced_global_scripts_footer', array(
			'name' => esc_html__( 'Custom Scripts (Footer)', 'inbound' ),
			'desc' => esc_html__( 'Custom scripts entered here will be added to the theme\'s footer.', 'inbound' ),
			'std'  => ''
		) );

		if ( function_exists ( 'inbound_shortcode_option' ) ) {
			$options_panel->addTextarea( 'advanced_global_shortcodes', array(
				'name' => esc_html__( 'Customization Shortcodes', 'inbound' ),
				'desc' => esc_html__( 'Shortcodes will be executed prior to the templates being rendered. You can use this option to change settings, modify template output or to remove theme features dynamically.', 'inbound' ),
				'std'  => ''
			) );

			$options_panel->addCheckbox( 'advanced_global_parse_custom_blocks', array(
				'name'    => esc_html__( 'Parse for custom blocks', 'inbound' ),
				'caption' => esc_html__( 'Parse customization shortcodes block', 'inbound' ),
				'desc'    => esc_html__( 'If this option is checked, the theme will parse for and execute custom blocks in this order: defined by shortcode, defined by custom PHP function through plug-in or child theme, defined by custom template.', 'inbound' ),
				'class'   => 'no-toggle',
				'std'     => true
			) );
		}

		$options_panel->Subtitle( esc_html__( "Performance Settings", 'inbound' ) );

		$options_panel->addHidden( 'advanced_css_handling',
			array(
				'inline' => esc_html__( 'Output to page header', 'inbound' ),
				//'external_css' 	=> esc_html__( 'External CSS file', 'inbound' )
			),
			array(
				'name'  => 'Dynamic CSS Handling',
				'std'   => 'inline',
				'class' => 'no-fancy',
				'desc'  => esc_html__( 'Select how you would like dynamically generated CSS code to be handled.', 'inbound' )
			)
		);

		$options_panel->addCheckbox( 'advanced_css_minify', array(
			'name'    => esc_html__( 'Minify CSS', 'inbound' ),
			'caption' => esc_html__( 'Minify generated dynamic CSS code.', 'inbound' ),
			'desc'    => esc_html__( 'If this option is checked, the theme will compress or minify all generated CSS code before it is sent to the browser.', 'inbound' ),
			'std'     => true
		) );

		$options_panel->addCheckbox( 'advanced_no_animations', array(
			'name'    => esc_html__( 'Disable animations', 'inbound' ),
			'caption' => esc_html__( 'Disable animations for rows and widgets.', 'inbound' ),
			'desc'    => esc_html__( 'If this option is checked, the tbeme will not load resources for animations and disable the animation feature globally.', 'inbound' ),
			'std'     => false
		) );


		$options_panel->CloseTab();


		// Taxonomy Meta Fields
		$config        = array(
			'id'             => 'offthehelf_category',
			// meta box id, unique per meta box
			'title'          => 'Inbound',
			// meta box title
			'pages'          => array( 'category', 'product_cat' ),
			// taxonomy name, accept categories, post_tag and custom taxonomies
			'context'        => 'normal',
			// where the meta box appear: normal (default), advanced, side; optional
			'fields'         => array(),
			// list of meta fields (can be added by field arrays)
			'local_images'   => true,
			// Use local or hosted images (meta box images for add/remove)
			'use_with_theme' => get_template_directory_uri() . '/lib/admin'
		);
		$category_meta = new SR_Tax_Meta( $config );
		$category_meta->addPosts( 'sr_inbound_category_banner', array( 'args' => array( 'post_type' => 'banner' ) ), array(
			'name' => esc_html__( 'Custom Category Banner', 'inbound' ),
			'desc' => esc_html__( 'If a banner is selected, it will be displayed for this category\'s archive pages.', 'inbound' )
		) );
		$category_meta->Finish();


	}
}


function inbound_change_default_editor_title( $title ) {
	$screen = get_current_screen();
	if ( 'profile' == $screen->post_type ) {
		$title = esc_html__( 'Enter profile name here', 'inbound' );
	} elseif ( 'banner' == $screen->post_type ) {
		$title = esc_html__( 'Enter banner title here', 'inbound' );
	}
}

add_filter( 'enter_title_here', 'inbound_change_default_editor_title' );


/*
 * Add menu to Admin Bar (Toolbar as of WP 3.3)
 */
function inbound_custom_adminbar_menu( $meta = true ) {
	global $wp_admin_bar;
	if ( ! is_user_logged_in() ) {
		return;
	}
	if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
		return;
	}

	if ( ! inbound_hide_theme_options_toolbar() ) {
		$wp_admin_bar->add_menu( array(
						'id'    => 'inbound_menu',
						'href'  => admin_url( 'themes.php?page=inbound_admin_page' ),
						'title' => esc_html__( 'Theme Options', 'inbound' )
				)
		);

		$wp_admin_bar->add_menu( array(
						'parent' => 'inbound_menu',
						'id'     => 'custom_links',
						'title'  => esc_html__( 'Inbound Options', 'inbound' ),
						'href'   => admin_url( 'themes.php?page=inbound_admin_page' ),
						'meta'   => array()
				)
		);

		do_action( "inbound_admin_menu_after_options" );

		$wp_admin_bar->add_menu( array(
						'parent' => 'inbound-external',
						'id'     => 'inbound_about',
						'title'  => esc_html__( 'About Inbound', 'inbound' ),
						'href'   => 'http://www.shapingrain.com/products/inbound-for-wordpress/',
						'meta'   => array()
				)
		);

		$wp_admin_bar->add_menu( array(
						'parent' => 'inbound-external',
						'id'     => 'shapingrain_link',
						'title'  => esc_html__( 'ShapingRain.com', 'inbound' ),
						'href'   => 'http://www.shapingrain.com/',
						'meta'   => array( 'target' => '_blank' )
				)
		);

		$wp_admin_bar->add_menu( array(
						'parent' => 'inbound-external',
						'id'     => 'shapingrain_support',
						'title'  => esc_html__( 'Customer Support', 'inbound' ),
						'href'   => 'http://www.shapingrain.com/support/',
						'meta'   => array( 'target' => '_blank' )
				)
		);

		do_action( "inbound_admin_menu_after_support" );

		$wp_admin_bar->add_group( array(
				'parent' => 'inbound_menu',
				'id'     => 'inbound-external',
				'meta'   => array(
						'class' => 'ab-sub-secondary',
				),
		) );
	}

}

add_action( 'admin_bar_menu', 'inbound_custom_adminbar_menu', 15 );

function inbound_custom_menu_css() {
	$custom_menu_css = '<style type="text/css">
	<!--/*--><![CDATA[/*><!--*/
	li#wp-admin-bar-inbound_finish_setup a:before {
		content: \'\f109\';
		color: #ffffff;
	}
	li#wp-admin-bar-inbound_finish_setup {
		background: #3ca14c!important;
	}
	li#wp-admin-bar-inbound_finish_setup a {
		color: #ffffff;
		font-weight: bold;
	}
	#adminmenu li#menu-posts-profile div.wp-menu-image:before {
		content: \'\f157\';
	}
	#adminmenu li#menu-posts-banner div.wp-menu-image:before {
		content: \'\f136\';
	}
    #wp-admin-bar-inbound_menu > .ab-item:before {
		content: "\f111";
        top: 2px;
    }

	/*]]>*/-->
    </style>';
	echo $custom_menu_css;
}

if ( is_user_logged_in() && ( is_super_admin() || ! is_admin_bar_showing() ) ) {
	add_action( 'wp_head', 'inbound_custom_menu_css' );
}
add_action( 'admin_head', 'inbound_custom_menu_css' );


function inbound_custom_admin_css() {

	global $post_type;
	$additional_css = '';
	if ( isset( $post_type ) && $post_type == 'profile' ) {
		$additional_css = '#edit-slug-box,#view-post-btn,#post-preview,.updated p a, #misc-publishing-actions,#minor-publishing-actions{display: none;}';
	}

	echo '<style>
	<!--/*--><![CDATA[/*><!--*/
        #TB_window {
        	z-index:9999999;
        }
		' . $additional_css . '
	/*]]>*/-->
	</style>';
}

add_action( 'admin_head', 'inbound_custom_admin_css' );


/*
 * Set defaults from within profile editor
 */
function inbound_add_profile_default_metabox() {
	add_meta_box( 'sr_inbound_profile_defaults', esc_html__( 'Default Profile', 'inbound' ), 'inbound_profile_default_metabox', 'profile', 'side', 'default' );
}

add_action( 'add_meta_boxes', 'inbound_add_profile_default_metabox' );

function inbound_profile_default_metabox() {
	global $post;

	echo '<input type="hidden" name="profile_defaults_nonce" id="profile_defaults_nonce" value="' .
	     wp_create_nonce( 'edit_profile' ) . '" />';

	$default             = inbound_option( 'default_profile' );
	$default_blog        = inbound_option( 'default_profile_blog' );
	$default_woocommerce = inbound_option( 'default_profile_woocommerce' );

	$id = intval( $post->ID );

	echo '<table class="form-table"><tbody>';

	if ( $id == $default ) {
		$readonly_default = ' disabled="disabled"';
	} else {
		$readonly_default = '';
	}

	echo '<tr><td class="at-field at-field-last">';
	echo '<input type="checkbox" class="rw-checkbox no-fancy" name="sr_inbound_is_default_site" id="sr_inbound_is_default_site" value="1" ' . checked( $id, $default, false ) . $readonly_default . '>';
	echo '<label for="sr_inbound_is_default_site"><span class="at-checkbox-label">' . esc_html__( 'Site Default', 'inbound' ) . '</span></label>';
	echo '</td></tr>';

	echo '<tr><td class="at-field at-field-last">';
	echo '<input type="checkbox" class="rw-checkbox no-fancy" name="sr_inbound_is_default_blog" id="sr_inbound_is_default_blog" value="1" ' . checked( $id, $default_blog, false ) . '>';
	echo '<label for="sr_inbound_is_default_blog"><span class="at-checkbox-label">' . esc_html__( 'Blog Default', 'inbound' ) . '</span></label>';
	echo '</td></tr>';

	if ( function_exists( 'is_woocommerce' ) ) {
		echo '<tr><td class="at-field at-field-last">';
		echo '<input type="checkbox" class="rw-checkbox no-fancy" name="sr_inbound_is_default_woocommerce" id="sr_inbound_is_default_woocommerce" value="1" ' . checked( $id, $default_woocommerce, false ) . '>';
		echo '<label for="sr_inbound_is_default_woocommerce"><span class="at-checkbox-label">' . esc_html__( 'WooCommerce Default', 'inbound' ) . '</span></label>';
		echo '</td></tr>';
	}

	echo '</tbody></table>';

}


function inbound_set_profile_defaults( $post_id, $post ) {
	if ( $post->post_type != 'profile' ) {
		return $post->ID;
	}
	if ( ! isset ( $_POST['profile_defaults_nonce'] ) ) {
		return $post->ID;
	}

	if ( $post->post_type != 'profile' ) {
		return $post->ID;
	}

	if ( ! wp_verify_nonce( $_POST['profile_defaults_nonce'], 'edit_profile' ) ) {
		return $post->ID;
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return $post->ID;
	}

	$id = $post->ID; //inbound_lang_id($post->ID, 'profile');

	// retrieve settings from post submission
	if ( ! empty( $_POST['sr_inbound_is_default_site'] ) ) {
		$set_site_default = $_POST['sr_inbound_is_default_site'];
	} else {
		$set_site_default = 0;
	}

	if ( ! empty( $_POST['sr_inbound_is_default_blog'] ) ) {
		$set_blog_default = $_POST['sr_inbound_is_default_blog'];
	} else {
		$set_blog_default = 0;
	}

	if ( ! empty( $_POST['sr_inbound_is_default_woocommerce'] ) ) {
		$set_woocommerce_default = $_POST['sr_inbound_is_default_woocommerce'];
	} else {
		$set_woocommerce_default = 0;
	}

	// retrieve current defaults
	$default             = inbound_option( 'default_profile' );
	$default_blog        = inbound_option( 'default_profile_blog' );
	$default_woocommerce = inbound_option( 'default_profile_woocommerce' );

	// site default
	if ( $set_site_default == 1 && $default != $id ) {
		inbound_save_options( array( 'default_profile' => $id ) );
	}

	// blog default
	if ( $set_blog_default == 1 && $default_blog != $id ) {
		inbound_save_options( array( 'default_profile_blog' => $id ) );
	}

	if ( $set_blog_default == 0 && $default_blog == $id ) {
		inbound_save_options( array( 'default_profile_blog' => 0 ) );
	}

	// woocommerce default
	if ( $set_woocommerce_default == 1 && $default_woocommerce != $id ) {
		inbound_save_options( array( 'default_profile_woocommerce' => $id ) );
	}

	if ( $set_woocommerce_default == 0 && $default_woocommerce == $id ) {
		inbound_save_options( array( 'default_profile_woocommerce' => 0 ) );
	}

	return $post->ID;
}

add_action( 'save_post', 'inbound_set_profile_defaults', 1, 2 );


function inbound_custom_profile_statuses( $post_statuses ) {
	$scr = get_current_screen();
	if ( $scr->post_type !== 'profile' ) {
		return $post_statuses;
	}

	global $post;
	$id = intval( $post->ID );

	$default_blog        = inbound_option( 'default_profile_blog' );
	$default             = inbound_option( 'default_profile' );
	$default_woocommerce = inbound_option( 'default_profile_woocommerce' );

	$post_statuses = array();

	if ( $default == $id ) {
		$post_statuses['default-site'] = '<span class="dashicons dashicons-star-filled"></span> ' . esc_html__( 'Site Default', 'inbound' );
	}
	if ( $default_blog == $id ) {
		$post_statuses['default-blog'] = '<span class="dashicons dashicons-welcome-write-blog"></span> ' . esc_html__( 'Blog Default', 'inbound' );
	}
	if ( $default_woocommerce == $id ) {
		$post_statuses['default-woocommerce'] = '<span class="dashicons dashicons-cart"></span> ' . esc_html__( 'WooCommerce Default', 'inbound' );
	}

	return $post_statuses;
}

add_filter( 'display_post_states', 'inbound_custom_profile_statuses' );


function inbound_profiles_bulk_actions( $actions ) {
	unset( $actions['inline'] );
	unset( $actions['edit'] );

	return $actions;
}

add_filter( 'bulk_actions-edit-profile', 'inbound_profiles_bulk_actions' );


add_action( 'wp_trash_post', 'inbound_delete_profile' );
add_action( 'wp_delete_post', 'inbound_delete_profile' );
function inbound_delete_profile( $postid ) {
	if ( 'profile' != get_post_type( $postid ) ) {
		return;
	}

	$default_blog        = inbound_option( 'default_profile_blog' );
	$default             = inbound_option( 'default_profile' );
	$default_woocommerce = inbound_option( 'default_profile_woocommerce' );

	if ( $postid == $default ) {
		wp_redirect( admin_url( 'edit.php?post_type=profile&profile_delete=site_default' ) );
		exit();
	}

	if ( $postid == $default_blog ) {
		inbound_save_options( array( 'default_profile_blog' => 0 ) );
	}

	if ( $postid == $default_woocommerce ) {
		inbound_save_options( array( 'default_profile_woocommerce' => 0 ) );
	}

}


function inbound_admin_notices() {
	if ( ! isset( $_GET['profile_delete'] ) ) {
		return;
	}

	if ( $_GET['profile_delete'] == "site_default" ) :
		?>
		<div class="error">
			<p><?php esc_html_e( 'You cannot delete your Site Default Profile. In order to do so, create a new profile, make it your Site Default Profile and then delete the original one.', 'inbound' ); ?></p>
		</div>
		<?php
	endif;
}

add_action( 'admin_init', 'inbound_admin_notices' );


add_filter( 'post_row_actions', 'inbound_remove_row_actions', 10, 2 );
function inbound_remove_row_actions( $actions, $post ) {
	global $current_screen;
	if ( $current_screen && $current_screen->post_type != 'profile' && $current_screen->post_type != 'banner' ) {
		return $actions;
	}
	unset( $actions['view'] );
	unset( $actions['inline hide-if-no-js'] );

	return $actions;
}


/*
 * Additional functions for custom content types
 */
add_action( 'admin_enqueue_scripts', 'inbound_admin_pointers' );
function inbound_admin_pointers() {

	$pointers = array(
		array(
			'id'       => 'page_visual_editor',
			'screen'   => 'page',
			'target'   => '#content-panels',
			'title'    => esc_html__( 'Visual Page Editor', 'inbound' ),
			'content'  => esc_html__( 'We recommend that you build pages with the visual page editor. That way you can profit from the theme\'s many built-in widgets and design settings.', 'inbound' ),
			'position' => array(
				'edge'  => 'top', // top, bottom, left, right
				'align' => 'left' // top, bottom, left, right, middle
			)
		),
		array(
			'id'       => 'post_visual_editor',
			'screen'   => 'banner',
			'target'   => '#content-panels',
			'title'    => esc_html__( 'Visual Banner Editor', 'inbound' ),
			'content'  => esc_html__( 'We recommend that you edit your banners using the visual page editor. That way you can profit from the theme\'s many built-in widgets and design settings.', 'inbound' ),
			'position' => array(
				'edge'  => 'top', // top, bottom, left, right
				'align' => 'left' // top, bottom, left, right, middle
			)
		),
		array(
			'id'       => 'select_setting',
			'screen'   => 'page',
			'target'   => '#inbound_profile',
			'title'    => esc_html__( 'Select a settings profile', 'inbound' ),
			'content'  => esc_html__( 'Each page can use a different settings profile. That way you can make each page look different, with unique colours, fonts and settings.', 'inbound' ),
			'position' => array(
				'edge'  => 'bottom', // top, bottom, left, right
				'align' => 'left' // top, bottom, left, right, middle
			)
		),
		array(
			'id'       => 'modal_editor',
			'screen'   => 'modal',
			'target'   => '#wp-content-editor-container',
			'title'    => esc_html__( 'Edit Modal Window Content', 'inbound' ),
			'content'  => esc_html__( 'This is where you add the content to be displayed inside the modal window. The content is parsed for shortcodes and may contain media.', 'inbound' ),
			'position' => array(
				'edge'  => 'top', // top, bottom, left, right
				'align' => 'middle' // top, bottom, left, right, middle
			)
		),
		array(
			'id'       => 'sr_settings',
			'screen'   => 'appearance_page_inbound_admin_page',
			'target'   => '.current',
			'title'    => __( 'Global Theme Settings', 'inbound' ),
			'content'  => inbound_esc_html( __( 'This screen contains <strong>global theme settings</strong> that apply to the theme as a whole. They control the blog, theme extensions like third party integrations and some advanced settings.', 'inbound' ) ),
			'position' => array(
				'edge'  => 'left', // top, bottom, left, right
				'align' => 'right' // top, bottom, left, right, middle
			)
		),
		array(
			'id'       => 'sr_profiles',
			'screen'   => 'appearance_page_inbound_admin_page',
			'target'   => '#sr-profiles',
			'title'    => __( 'Profiles', 'inbound' ),
			'content'  => inbound_esc_html( __( 'Edit <strong>profiles</strong> to control visual aspects of the theme, like fonts, colors and background images. By default there is only one design profile, but you can create as many as you like and assign separate profiles to different pages. That way you can create pages with completely unique designs without affecting the overall look and feel of the rest of your site.', 'inbound' ) ),
			'position' => array(
				'edge'  => 'top', // top, bottom, left, right
				'align' => 'left' // top, bottom, left, right, middle
			)
		),

	);
	new SR_Admin_Pointer( $pointers );
}


?>