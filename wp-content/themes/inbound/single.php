<?php
/**
 * The Template for displaying all single posts.
 *
 * @package inbound
 */

$is_sidebar = inbound_option('blog_sidebar_posts');
if ($is_sidebar) {
	$sidebar_layout = inbound_option( 'blog_sidebar_position', false );
} else {
	$sidebar_layout = false;
}
get_header();
?>
<?php get_template_part( 'templates/banners/banner', 'blog' ); ?>

	<div id="blog_content" class="row<?php if (!$sidebar_layout) : ?> clearfix<?php endif; ?>">
		<?php if ($sidebar_layout == "left") inbound_get_sidebar('inbound-blog-sidebar'); ?>
		<?php if ($sidebar_layout) : ?><div class="col-3-4"><?php endif; ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'single' ); ?>
			<?php endwhile; ?>
			<?php if ($sidebar_layout) : ?></div><?php endif; ?>
		<?php if ($sidebar_layout == "right") inbound_get_sidebar('inbound-blog-sidebar'); ?>
	</div>

<?php get_footer(); ?>