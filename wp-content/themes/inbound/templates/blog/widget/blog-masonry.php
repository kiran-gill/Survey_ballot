<?php
/**
 * The blog index template file for the 'masonry' layout.
 * @package inbound
 */
?>
<?php
$col_count = 0;

global $inbound_widget_cols_per_row, $inbound_widget_excerpt_length;
$cols_per_row = $inbound_widget_cols_per_row;

$classes = array();
$classes[] = 'blog-layout-masonry';

$r = $tpl_global['posts'];
?>
<div class="<?php echo implode($classes, ' ') ?>">
	<div class="blog-post-items masonry-columns-<?php echo $cols_per_row; ?> fullwidth" data-columns>
		<?php $item_count = 0; ?>
		<?php while ($r->have_posts() ) : $r->the_post(); ?>
			<!--BlogPost-->
			<?php if ($item_count == 0) $first = "first"; else $first = null; ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(array('blog_post', 'teaser', $first)); ?>>

				<!--Post Content-->
				<?php if ( has_post_format('image')) : ?>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('inbound-post-thumbnail-medium'); ?></a>
					<?php endif; ?>

					<?php echo get_the_content(); ?>

					<header>
						<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<div class="header_meta"><?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span></div>
					</header>

				<?php elseif (has_post_format('video')) : ?>

					<?php
					$embed = new WP_Embed();
					$video = $embed->run_shortcode( '[embed]' . get_the_content() . '[/embed]' );
					if ($video) {
						echo $video;
					} else {
						echo get_the_content();
					}
					?>

					<header>
						<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<div class="header_meta"><?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span></div>
					</header>

				<?php elseif (has_post_format('gallery')) : ?>

					<?php
					$images = inbound_get_gallery(get_the_content());

					if ( !$images ) :
						if (has_post_thumbnail()) :
							the_post_thumbnail('inbound-post-thumbnail');
						endif;
					else:
						?>
						<div class="flexslider-gallery">
							<ul class="slides">
								<?php
								$x=0;
								foreach ( $images as $attachment_id  ) {
									if ($x==0) $currclass = ' class="ts-currslide"'; else $currclass = "";
									$img = wp_get_attachment_image_src( $attachment_id, 'inbound-post-thumbnail' );
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
							<q><?php echo get_the_content(); ?></q>
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
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="post-thumbnail"><?php the_post_thumbnail('inbound-post-thumbnail-medium'); ?></a>
					<?php endif; ?>

					<header>
						<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<div class="header_meta"><?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span></div>
					</header>

					<?php if ( has_excerpt() ) : ?>
						<p><?php echo wp_trim_words( get_the_excerpt(), $inbound_widget_excerpt_length, null ); ?></p>
					<?php else: ?>
						<p><?php echo wp_trim_words( get_the_content(), $inbound_widget_excerpt_length, null ) ?></p>
					<?php endif; ?>

				<?php endif; ?>
				<!--End Post Content-->

				<?php if (!has_post_format()) : ?>
					<?php echo inbound_read_more_link(); ?>
				<?php endif; ?>


			</article>
			<!--EndBlogPost-->
			<?php $item_count++; ?>
		<?php endwhile; ?>
	</div>
</div>
