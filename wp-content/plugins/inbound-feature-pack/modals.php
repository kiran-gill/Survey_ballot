<?php
/*
 * Provides the modal window feature for the Inbound for WordPress theme.
 */

define('INBOUND_MODALS', true);

/*
 * Set up modal window content type
 */
if ( ! function_exists( 'inbound_modal_setup' ) ) {
	function inbound_modal_setup() {
		if ( ! function_exists ( 'inbound_option' ) ) return false;

		$modals = new SR_Custom_Post_Type(
			'modal',
			array(
				'supports'            => array( 'title', 'editor', 'revisions' ),
				'menu_position'       => 311,
				'public'              => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => false
			)
		);
	}
}
add_action( 'inbound_admin_setup_after_content_types', 'inbound_modal_setup', 3 );


/*
 * Retrieve modal window content and add to global array
 */
if ( ! function_exists( 'inbound_add_modal' ) ) {
	function inbound_add_modal( $id = 0 ) {
		global $inbound_this_page_modals;
		$content = null;

		if ( $id ) {
			if (class_exists('SitePress')) { // adjust ID for WPML
				$id = apply_filters( 'wpml_object_id', $id, 'modal', true );
			}
		}


		if ( ! isset ( $inbound_this_page_modals[ $id ] ) ) {
			$options = get_post_custom( $id );

			$render_mode = inbound_custom_value( 'sr_inbound_modal_render_mode', $options, 'content' );

			$color_background = inbound_custom_value( 'sr_inbound_modal_background', $options, '#ffffff' );
			$color_text       = inbound_custom_value( 'sr_inbound_modal_text', $options, '#000000' );
			$css              = inbound_custom_value( 'sr_inbound_modal_custom_css', $options, false );


			if ( function_exists( 'siteorigin_panels_render' ) && $render_mode == 'pagebuilder' ) {
				if ( inbound_panels_is_live_editor() ) {
					$banner_content = '<div class="panel-grid visual-editor-modal"><h2>' . esc_html__( 'Modal Window Location - Edit in Modal Window Editor', 'inbound' ) . '</h2></div>';
				} else {
					$content = siteorigin_panels_render( $id, true, false );
				}
			} else { // pagebuilder not used

				$p = get_post( $id );

				if ( $render_mode == 'content' ) {
					if (!empty($p->post_content)) {
						$content = do_shortcode( $content );
						$content = wptexturize( $p->post_content );
						$content = wpautop( $content, true );
					}
				} elseif ( $render_mode == 'raw' ) {
					if (!empty($p->post_content)) {
						$content = do_shortcode( $p->post_content );
					}
				} elseif ( $render_mode == 'oembed' ) {
					if (!empty($p->post_content)) {
						$content = wp_oembed_get( $p->post_content );
					}
				}
			} // pagebuilder not used

			$inbound_this_page_modals[ $id ] = array(
				'content'          => $content,
				'color_background' => $color_background,
				'color_text'       => $color_text,
				'css'              => $css
			);

			$style = '.modal-style-' . $id . ' .featherlight-content { background-color:' . $color_background . '; color:' . $color_text . '; }';
			inbound_add_custom_style( 'modal', $style );

			wp_enqueue_script( 'inbound-featherlight', plugin_dir_url( __FILE__ ) . '/js/featherlight.min.js', array( 'jquery' ), INBOUND_THEME_VERSION, true );
		} // if not yet set


		if ( isset( $inbound_this_page_modals[ $id ] ) ) {
			return $inbound_this_page_modals[ $id ];
		} else {
			return false;
		}

	}
}

add_action( 'inbound_before_wp_footer', 'inbound_print_modals' );
if ( ! function_exists( 'inbound_print_modals' ) ) {
	function inbound_print_modals() {
		global $inbound_this_page_modals;
		if ( is_array( $inbound_this_page_modals ) && count( $inbound_this_page_modals ) > 0 ) {
			$out = '';
			foreach ( $inbound_this_page_modals as $id => $options ) {
				$out .= '<div class="lightbox" id="modal-' . $id . '">' . "\n" . $options['content'] . "\n" . '</div>' . "\n";
			}
			echo $out;
		}
	}
}

