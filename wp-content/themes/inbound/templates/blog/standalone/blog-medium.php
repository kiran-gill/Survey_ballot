<?php
/**
 * The blog index template file for the 'medium' layout.
 * @package inbound
 */
if (!is_archive()) {
	$show_sidebar = inbound_option('blog_sidebar_front', true);
}
else {
	$show_sidebar = inbound_option('blog_sidebar_archives', true);
}
$sidebar_position = inbound_option('blog_sidebar_position', 'left');

if ($show_sidebar) $container_col_class = 'col-3-4'; else $container_col_class = 'fullwidth';
?>

<div class="row">
	<?php if ($show_sidebar && $sidebar_position == 'left') inbound_get_sidebar(); ?>
	<div class="<?php echo $container_col_class; ?>">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php $day = 'j'; $month = 'F'; /*  get_option( 'date_format' ) is not used to retrieve the date as only the numeric day is displayed */ ?>
			<!--BlogPost-->
			<article id="post-<?php the_ID(); ?>" <?php post_class(array('blog_post', 'teaser')); ?>>
				<div class="col-4 meta_data"><span class="meta-date"><span class="month"><?php the_time($month) ?></span> <span class="day"><?php the_time($day) ?></span></span><span class="author_medium"><?php esc_html_e('By', 'inbound'); ?> <?php inbound_posted_by(); ?></span> <?php inbound_categories(); ?> <?php if ( comments_open() ) : ?><span class="meta_comment"><a href="<?php comments_link(); ?>"><?php esc_html_e('Leave a comment', 'inbound'); ?></a></span><?php endif; ?></div>
				<div class="col-3-4">
				<?php if (has_post_format('quote')) : ?>
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title()) ?>">
						<blockquote>
							<q><?php the_content(); ?></q>
							<div class="quote_author"><?php the_title(); ?></div>
						</blockquote>
					</a>
				<?php elseif (has_post_format('gallery') || has_post_format('video') || has_post_format('image') ) : ?>
					<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<?php the_content(); ?>
				<?php elseif (has_post_format('link')) : ?>
					<?php
					$sanitized_content = wp_strip_all_tags( get_the_content() );

					if ( $links = inbound_get_link($sanitized_content) ) {
						$link = $links;
					}
					else {
						$link = $sanitized_content;
					}

					$link = trim ($link);

					if (inbound_is_valid_url($link)) : ?>
						<a href="<?php echo esc_url($link); ?>" class="format-link-content" title="<?php the_title_attribute(); ?>" target="_blank">
							<h2><?php the_title(); ?></h2>
							<div class="link_url"><?php echo $link; ?></div>
						</a>
					<?php else: ?>
						<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<?php endif; ?>

				<?php else : ?>
					<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" class="post-thumbnail" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('post-thumbnail'); ?></a>
					<?php endif; ?>

					<!--Post Content-->
					<?php if ( inbound_option('blog_content_mode_archive', 'excerpt') == 'excerpt' && ( is_archive() || is_post_type_archive() ) ) : ?>
						<p><?php echo get_the_excerpt(); ?></p>
					<?php elseif ( inbound_option('blog_content_mode_index', 'excerpt') == 'excerpt' && is_home() ) : ?>
						<p><?php echo get_the_excerpt(); ?></p>
					<?php else : ?>
						<?php the_content(); ?>
					<?php endif; ?>
					<!--End Post Content-->

					<?php echo inbound_read_more_link(); ?>

				<?php endif; ?>
				</div>
			</article>
			<!--EndBlogPost-->
		<?php endwhile; ?>
		<?php inbound_pagination(); ?>
	</div>
	<?php if ($show_sidebar && $sidebar_position == 'right') inbound_get_sidebar(); ?>
</div>