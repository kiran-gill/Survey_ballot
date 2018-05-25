<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package inbound
 */
$sidebar_layout = inbound_option('template_layout', false);
get_header(); ?>

<?php
if ( function_exists('is_woocommerce') && ( is_woocommerce() || is_cart() || is_checkout() || is_checkout_pay_page() ) ) {
	get_template_part( 'templates/banners/banner', 'woocommerce' );
} else {
	if (inbound_option('show_banner', false) ) {
		get_template_part( 'templates/banners/banner', 'page' );
	}
}
?>
	<div id="blog_content" class="row<?php if (!$sidebar_layout) : ?> clearfix<?php endif; ?>">
		<?php if ($sidebar_layout == "sidebar-left") inbound_get_sidebar('inbound-blog-sidebar'); ?>
		<?php if ($sidebar_layout) : ?><div class="col-3-4"><?php endif; ?>
		<?php while ( have_posts() ) : the_post(); ?>
				<?php if ( function_exists('is_woocommerce') && ( is_woocommerce() || is_cart() || is_checkout() || is_checkout_pay_page() ) ) {
					get_template_part( 'content', 'woocommerce' );
				} else  {
					get_template_part( 'content', 'page' );
				}
				?>
		<?php endwhile; ?>
		<?php if ($sidebar_layout) : ?></div><?php endif; ?>
		<?php if ($sidebar_layout == "sidebar-right") inbound_get_sidebar('inbound-blog-sidebar'); ?>
	</div>

<?php get_footer(); ?>