<?php
/**
 * The blog index template file for the 'masonry' layout.
 * @package inbound
 */
?>
<?php
$col_count = 0;

if (!is_archive()) {
	$show_sidebar = inbound_option('blog_sidebar_front', true);
	$cols_per_row = inbound_option('blog_grid_columns', '3');
}
else {
	$show_sidebar = inbound_option('blog_sidebar_archives', true);
	$cols_per_row = inbound_option('blog_archive_grid_columns', '3');
}
$sidebar_position = inbound_option('blog_sidebar_position', 'right');

if ($show_sidebar) $container_col_class = 'col-3-4'; else $container_col_class = 'fullwidth';

?>
<div class="row">
	<?php if ($show_sidebar && $sidebar_position == 'left') inbound_get_sidebar(); ?>
	<div class="blog-post-items masonry-columns-<?php echo $cols_per_row; ?> <?php echo $container_col_class; ?>" data-columns>
		<?php while ( have_posts() ) : the_post(); ?>
			<!--BlogPost-->
			<article id="post-<?php the_ID(); ?>" <?php post_class(array('blog_post', 'teaser')); ?>>

				<!--Post Content-->
				<?php if ( has_post_format('image')) : ?>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('inbound-post-thumbnail-medium'); ?></a>
					<?php endif; ?>

					<?php the_content(); ?>

					<header>
						<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<div class="header_meta"><?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span></div>
					</header>

				<?php elseif (has_post_format('video')) : ?>

					<?php the_content(); ?>

					<header>
						<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<div class="header_meta"><?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span></div>
					</header>

				<?php elseif (has_post_format('gallery')) : ?>

					<?php
					$images = inbound_get_gallery(get_the_content());

					if ( !$images ) :
						if (has_post_thumbnail()) :
							the_post_thumbnail('post-thumbnail');
						endif;
					else:
						?>
						<div class="flexslider-gallery">
							<ul class="slides">
								<?php
								$x=0;
								foreach ( $images as $attachment_id  ) {
									if ($x==0) $currclass = ' class="ts-currslide"'; else $currclass = "";
									$img = wp_get_attachment_image_src( $attachment_id, 'post-thumbnail' );
									echo '<li><img src="' . esc_url( $img[0] ) . '"'.$currclass.'></li>';
									$x++;
								}
								?>
							</ul>
						</div>
						<?php
					endif;
					?>

					<header>
						<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<div class="header_meta"><?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span></div>
					</header>

				<?php elseif (has_post_format('quote')) : ?>

					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title()) ?>">
						<blockquote>
							<q><?php the_content(); ?></q>
							<div class="quote_author"><?php the_title(); ?></div>
						</blockquote>
					</a>

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
							<h3><?php the_title(); ?></h3>
							<div class="link_url"><?php echo esc_url($link); ?></div>
						</a>
					<?php else: ?>

					<?php endif; ?>


				<?php else: ?>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="post-thumbnail"><?php the_post_thumbnail('post-thumbnail-medium'); ?></a>
					<?php endif; ?>

					<header>
						<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<div class="header_meta"><?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span></div>
					</header>

					<p><?php echo get_the_excerpt(); ?></p>

				<?php endif; ?>
				<!--End Post Content-->

				<?php if (!has_post_format()) : ?>
					<?php echo inbound_read_more_link(); ?>
				<?php endif; ?>


			</article>
			<!--EndBlogPost-->
		<?php endwhile; ?>
	</div>
	<?php inbound_pagination(); ?>
	<?php if ($show_sidebar && $sidebar_position == 'right') inbound_get_sidebar(); ?>
</div>