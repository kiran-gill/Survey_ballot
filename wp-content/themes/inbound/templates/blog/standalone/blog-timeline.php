<?php
/**
 * The blog index template file for the 'timeline' layout.
 * @package inbound
 */
?>
<?php
if (!is_archive()) {
	$group_by = inbound_option('blog_timeline_group_by', 'day');
} else {
	$group_by = inbound_option('blog_archive_timeline_group_by', 'day');
}

if ($group_by == 'day') { // by day
	$compare_string = 'j';
	$display_format = '';
} elseif ($group_by == 'month') { // by month
	$compare_string = 'n';
	$display_format = 'F o';
} else { // by year
	$compare_string = 'o';
	$display_format = 'o';
}

?>
<div class="row">
	<div class="timeline">
		<?php
		$day_check = '';
		$post_position = '';
		$day_open = false;
		while ( have_posts() ) : the_post();
			$day = get_the_date($compare_string);
			if ($day != $day_check) {
				if ($day_check != '') {
					echo '</div><!--EndDay-->'; // close the day's container here
					$day_open = false;
					$post_position = 'left';
				}
				echo '<div class="timeline_day"><div class="timeline_date"><span>' . get_the_date($display_format) . '</span></div>';
				$day_open = true;
			}
			else {
				if ($post_position == 'left') {
					$post_position = 'right';
				}
				elseif ($post_position == 'right') {
					$post_position = 'left';
				}
			}
			if ($post_position == '') $post_position = 'left';

			?>
			<!--BlogPost-->
			<article id="post-<?php the_ID(); ?>" <?php post_class(array('col-2', 'blog_post', 'teaser', $post_position)); ?>>
				<!--Post Content-->
				<?php if (has_post_format('image')) : ?>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('inbound-post-thumbnail-medium'); ?></a>
					<?php endif; ?>

					<?php the_content(); ?>

					<header>
						<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
						<div class="header_meta"><?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span></div>
					</header>

				<?php elseif (has_post_format('video')) : ?>

					<?php the_content(); ?>

					<header>
						<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
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
						<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
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
							<h2><?php the_title(); ?></h2>
							<div class="link_url"><?php echo $link; ?></div>
						</a>
					<?php else: ?>

					<?php endif; ?>

				<?php else: ?>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="post-thumbnail"><?php the_post_thumbnail('inbound-post-thumbnail-medium'); ?></a>
					<?php endif; ?>

					<header>
						<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
						<div class="header_meta"><?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span></div>
					</header>

					<!--Post Content-->
					<p><?php echo get_the_excerpt(); ?></p>
					<!--End Post Content-->

				<?php endif; ?>
				<!--End Post Content-->

				<?php if (!has_post_format() ) : ?>
					<?php echo inbound_read_more_link(); ?>
				<?php endif; ?>
			</article>
			<!--EndBlogPost-->
			<?php
			$day_check = $day;
		endwhile;
		if ($day_open == true) echo '</div><!--EndDay-->' // if day was left open, close container;
		?>
	</div><!--EndTimeline-->
	<?php inbound_pagination(); ?>
</div>