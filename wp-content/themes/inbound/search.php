<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package inbound
 */

get_header();
?>
<?php get_template_part( 'templates/banners/banner', 'blog' ); ?>

<?php
$show_sidebar = inbound_option('blog_sidebar_archives', true);
$sidebar_position = inbound_option('blog_sidebar_position', 'right');

if ($show_sidebar) $container_col_class = 'col-3-4'; else $container_col_class = 'fullwidth';
?>

<div class="row">
	<?php if ($show_sidebar && $sidebar_position == 'left') inbound_get_sidebar(); ?>
	<div class="<?php echo $container_col_class; ?>">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<!--BlogPost-->
				<article id="post-<?php the_ID(); ?>" <?php post_class(array('blog_post', 'teaser')); ?>>
					<header>
						<h1><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
						<div class="header_meta"><?php esc_html_e('By', 'inbound'); ?> <?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span> <?php inbound_categories(); ?> <?php if ( comments_open() ) : ?><span class="meta_comment"><a href="<?php comments_link(); ?>"><?php esc_html_e('Leave a comment', 'inbound'); ?></a></span><?php endif; ?></div>
					</header>

					<!--Post Content-->
					<p><?php echo wp_trim_words( strip_shortcodes( get_the_content() ), inbound_option('excerpt_length', 25), null) ?></p>
					<!--End Post Content-->

					<?php echo inbound_read_more_link(); ?>

				</article>
				<!--EndBlogPost-->
			<?php endwhile; ?>
		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>
		<?php inbound_pagination(); ?>
	</div>
	<?php if ($show_sidebar && $sidebar_position == 'right') inbound_get_sidebar(); ?>
</div>

<?php get_footer(); ?>
