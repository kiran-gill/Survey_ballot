<?php
/**
 * The template for displaying image attachments.
 *
 * @package inbound
 */

get_header(); ?>
<?php get_template_part( 'templates/banners/banner', 'blog' ); ?>

	<div id="blog_content" class="row">
		<div class="col-3-4">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'image' ); ?>
			<?php endwhile; ?>
		</div>
		<?php inbound_get_sidebar(); ?>
	</div>

<?php get_footer(); ?>