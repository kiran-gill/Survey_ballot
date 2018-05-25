<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package inbound
 */

$is_sidebar = inbound_option('blog_sidebar_front');
if ($is_sidebar) {
	$sidebar_layout = inbound_option( 'template_layout', false );
} else {
	$sidebar_layout = false;
}
?>

<?php
get_header(); ?>


<?php
get_template_part( 'templates/banners/banner', 'blog' );
?>

<div id="blog_content" class="row<?php if (!$sidebar_layout) : ?> clearfix<?php endif; ?>">
	<?php if ($sidebar_layout == "sidebar-left") inbound_get_sidebar('inbound-blog-sidebar'); ?>
	<?php if ($sidebar_layout) : ?><div class="col-3-4"><?php endif; ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', 'page' ); ?>
		<?php endwhile; ?>
		<?php if ($sidebar_layout) : ?></div><?php endif; ?>
	<?php if ($sidebar_layout == "sidebar-right") inbound_get_sidebar('inbound-blog-sidebar'); ?>
</div>

<?php get_footer(); ?>
