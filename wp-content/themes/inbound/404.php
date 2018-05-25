<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package inbound
 */

$sidebar_layout = inbound_option('template_layout', false);
get_header(); ?>

<div class="row<?php if (!$sidebar_layout) : ?> clearfix<?php endif; ?>">
	<?php if ($sidebar_layout == "sidebar-left") inbound_get_sidebar('inbound-blog-sidebar'); ?>
	<?php if ($sidebar_layout) : ?><div class="col-3-4"><?php endif; ?>

<div class="col-2">
<img src="<?php echo get_template_directory_uri(); ?>/images/404-mug.jpg" alt="<?php esc_attr_e( 'Not Found', 'inbound' ); ?>"/>
</div>

<div class="col-2">
		<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Oops!', 'inbound' ); ?></h1>
		<h2><span><?php esc_html_e( 'That page can&rsquo;t be found.', 'inbound' ); ?></span></h2>
		</header>
		<!-- .page-header -->
		<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try searching for something else?', 'inbound' ); ?></p>
		<div class="widget_search">
        <?php get_search_form(); ?>
        </div>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="take-me-home"><?php esc_html_e( 'Take me to the home page', 'inbound' ); ?></a>
</div>

		<?php if ($sidebar_layout) : ?></div><?php endif; ?>
	<?php if ($sidebar_layout == "sidebar-right") inbound_get_sidebar('inbound-blog-sidebar'); ?>
</div>

<?php get_footer(); ?>
