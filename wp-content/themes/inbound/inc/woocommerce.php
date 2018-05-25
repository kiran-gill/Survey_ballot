<?php
/*
 * Inbound for WordPress
 * WooCommerce Integration
 */

add_action( 'after_setup_theme', 'inbound_woocommerce_support' );
function inbound_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}


add_action( 'wp_enqueue_scripts', 'inbound_woocommerce_enqueue_scripts');
if (!function_exists(('inbound_woocommerce_enqueue_scripts'))) {
	function inbound_woocommerce_enqueue_scripts() {
		wp_register_style( 'inbound-woocommerce',  get_template_directory_uri() . '/css/woocommerce.css', array('inbound-style'), INBOUND_THEME_VERSION );
		wp_enqueue_style( 'inbound-woocommerce' );
	}
}


add_filter('add_to_cart_fragments', 'inbound_woocommerce_header_add_to_cart_fragment');
function inbound_woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;

	ob_start();

	?>
	<a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" title="<?php esc_attr_e('View your shopping cart', 'inbound'); ?>"<?php if ( $woocommerce->cart->cart_contents_count > 0 ) : ?>class="header-cart-notempty"<?php endif; ?> id="header-cart-trigger">
		<?php if ( $woocommerce->cart->cart_contents_count > 0 ) : ?><span id="header-cart-total"><?php echo $woocommerce->cart->cart_contents_count; ?></span><?php endif; ?>
		<i class="fa fa-shopping-cart fa-1x"></i>
	</a>
	<?php

	$fragments['a#header-cart-trigger'] = ob_get_clean();

	if (isset($_REQUEST['product_id'])) {
		$product_id = $_REQUEST['product_id'];
		$_pf = new WC_Product_Factory();
		$_product = $_pf->get_product($product_id);
		$fragments['#header-cart-notification span.cart-notification'] = '<span class="cart-notification">&quot;' . $_product->get_title() . '&quot; ' . esc_html__('has been added to the cart.', 'inbound') . '</span>';
	}

	return $fragments;
}


function inbound_woocommerce_override_page_title() {
	return false;
}
add_filter('woocommerce_show_page_title', 'inbound_woocommerce_override_page_title');

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);


function inbound_woocommerce_before_content () {
	$sidebar_layout = inbound_option('woocommerce_sidebar_position', false);

	get_template_part( 'templates/banners/banner', 'woocommerce' );

	if (!$sidebar_layout) {
		echo '<div class="row clearfix">';

	} else {
		echo '<div class="row">';
	}

	if ($sidebar_layout == "sidebar-left") {
		if ( inbound_option('enable_woocommerce_sidebars', true) && is_active_sidebar('inbound-woocommerce-sidebar') ) { // do we have a WooCommerce-specific sidebar and are we instructed to use it?
			echo '<div id="sidebar" class="col-4" role="complementary">';
			inbound_get_sidebar('inbound-woocommerce-sidebar');
			echo '</div>';
		} else {
			inbound_get_sidebar('inbound-blog-sidebar');
		}
	}

	if ($sidebar_layout) echo '<div class="col-3-4">';
}
add_action('woocommerce_before_main_content', 'inbound_woocommerce_before_content');



function inbound_woocommerce_after_content () {
	$sidebar_layout = inbound_option('woocommerce_sidebar_position', false);
	if ($sidebar_layout) echo '</div><!--End Col-3-4-->';

	if ($sidebar_layout == "sidebar-right") {
		if ( inbound_option('enable_woocommerce_sidebars', true) && is_active_sidebar('inbound-woocommerce-sidebar') ) { // do we have a WooCommerce-specific sidebar and are we instructed to use it?
			echo '<div id="sidebar" class="col-4" role="complementary">';
			inbound_get_sidebar('inbound-woocommerce-sidebar');
			echo '</div>';
		} else {
			inbound_get_sidebar('inbound-blog-sidebar');
		}
	}

	echo '</div><!--End Row-->';

}
add_action('woocommerce_after_main_content', 'inbound_woocommerce_after_content');

// Remove each style one by one
add_filter( 'woocommerce_enqueue_styles', 'inbound_dequeue_styles' );
function inbound_dequeue_styles( $enqueue_styles ) {
	unset( $enqueue_styles['woocommerce-general'] );		// Remove the gloss
	unset( $enqueue_styles['woocommerce-layout'] );			// Remove the layout
	unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
	return $enqueue_styles;
}

remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20, 0);


add_action( 'wp_footer', 'inbound_woocommerce_disable_lightbox' );
function inbound_woocommerce_disable_lightbox() {
		update_option( 'woocommerce_enable_lightbox', false	);
}


function inbound_woocommerce_init_sidebars() {
	register_sidebar( array(
			'name'          => esc_html__( 'WooCommerce Sidebar', 'inbound' ),
			'description'   => esc_html__( 'This sidebar is displayed on all WooCommerce pages.', 'inbound' ),
			'id'            => 'inbound-woocommerce-sidebar',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
	) );

	register_sidebar( array(
			'name'          => esc_html__( 'WooCommerce Footer Widgets', 'inbound' ),
			'description'   => esc_html__( 'These footer widgets is displayed on all WooCommerce pages.', 'inbound' ),
			'id'            => 'inbound-woocommerce-footer',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
	) );
}
add_action( 'widgets_init', 'inbound_woocommerce_init_sidebars' );

if (!function_exists('inbound_loop_columns')) {
	function inbound_loop_columns() {
		return intval(inbound_option('woocommerce_grid_columns', 3));
	}
}
add_filter('loop_shop_columns', 'inbound_loop_columns', 999);


if (!function_exists('inbound_loop_show_per_page')) {
	function inbound_loop_show_per_page() {
		return intval(inbound_option('woocommerce_products_per_page', 12));
	}
}
add_filter( 'loop_shop_per_page', 'inbound_loop_show_per_page', 20 );


add_filter( 'woocommerce_breadcrumb_defaults', 'inbound__change_breadcrumb_delimiter' );
function inbound__change_breadcrumb_delimiter( $defaults ) {
	// Change the breadcrumb delimeter from '/' to '>'
	$defaults['delimiter'] = '';
	return $defaults;
}


// Customize the WooCommerce breadcrumb
if ( ! function_exists( 'woocommerce_breadcrumb' ) ) {
	function woocommerce_breadcrumb( $args = array() ) {
		$args = array(
				'delimiter'  => '',
				'wrap_before'  => '<ul class="breadcrumb visible-desktop">',
				'wrap_after' => '</ul>',
				'before'   => '<li>',
				'after'   => '</li>',
				'home'    => get_bloginfo('name')
		);

		$breadcrumbs = new WC_Breadcrumb();
		if ( $args['home'] ) {
			$breadcrumbs->add_crumb( $args['home'], esc_url( home_url( '/' ) ) );
		}
		$args['breadcrumb'] = $breadcrumbs->generate();
		wc_get_template( 'global/breadcrumb.php', $args );
	}
}

add_filter('body_class','inbound_woocommerce_add_body_classes');
if (!function_exists('inbound_woocommerce_add_body_classes')) {
	function inbound_woocommerce_add_body_classes($classes) {

		if ( in_array('woocommerce', $classes) || in_array('woocommerce-page', $classes) )
			$is_woocommerce = true;
		else
			$is_woocommerce = false;

		if ( $is_woocommerce ) {
			$classes[] = 'woocommerce-' . inbound_option( 'woocommerce_grid_columns', 4 ) . '-columns';
			if (  !is_checkout() && !is_checkout_pay_page() && !is_cart() ) {
				if ( inbound_option( 'woocommerce_sidebar_position', false ) ) {
					$classes[] = 'woocommerce-has-sidebar';
					$classes[] = inbound_option( 'woocommerce_sidebar_position', 'right' );
				}
			} else {
				$classes[] = 'no-sidebar';
			}
		}
		return $classes;
	}
}


if ( ! function_exists( 'inbound_woocommerce_template_loop_product_title' ) ) {
	function inbound_woocommerce_template_loop_product_title() {
		echo '<h3 class="woocommerce-loop-product__title">' . get_the_title() . '</h3>';
	}
}
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'inbound_woocommerce_template_loop_product_title', 10 );