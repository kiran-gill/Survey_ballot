<?php
/**
 * The content template for displaying image attachments.
 * @package inbound
 */
?>

<!--BlogPost-->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<h1><?php the_title(); ?></h1>
		<div class="header_meta"><?php esc_html_e('By', 'inbound'); ?> <?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span> <?php inbound_categories(); ?> <?php if ( comments_open() ) : ?><span class="meta_comment"><a href="<?php comments_link(); ?>"><?php esc_html_e('Leave a comment', 'inbound'); ?></a></span><?php endif; ?></div>
	</header>
	<?php the_post_thumbnail(); ?>

	<!--Post Content-->
	<div class="entry-attachment">
		<div class="attachment">
			<?php
			$k = 0;
			/**
			 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
			 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
			 */
			$attachments = array_values( get_children( array(
					'post_parent'    => $post->post_parent,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => 'ASC',
					'orderby'        => 'menu_order ID'
			) ) );
			foreach ( $attachments as $k => $attachment ) {
				if ( $attachment->ID == $post->ID )
					break;
			}
			$k++;
			// If there is more than 1 attachment in a gallery
			if ( count( $attachments ) > 1 ) {
				if ( isset( $attachments[ $k ] ) )
					// get the URL of the next image attachment
					$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
				else
					// or get the URL of the first image attachment
					$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
			} else {
				// or, if there's only 1 image, get the URL of the image
				$next_attachment_url = wp_get_attachment_url();
			}
			?>

			<a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
				$attachment_size = apply_filters( 'inbound_attachment_size', array( 1200, 1200 ) ); // Filterable image size.
				echo wp_get_attachment_image( $post->ID, $attachment_size );
				?></a>
		</div><!-- .attachment -->

		<?php if ( ! empty( $post->post_excerpt ) ) : ?>
			<div class="entry-caption">
				<?php the_excerpt(); ?>
			</div><!-- .entry-caption -->
		<?php endif; ?>
	</div><!-- .entry-attachment -->


	<?php the_content(); ?>
	<!--End Post Content-->

	<?php if( has_tag() ) : ?>
		<!--Post Tags-->
		<p id="post_tags">
			<span><?php esc_html_e('Tagged under:', 'inbound'); ?></span>
			<?php inbound_tags(); ?>
		</p>
		<!--End Post Tags-->
	<?php endif; ?>

	<!--About The Author-->
	<?php if ( get_the_author_meta('description') ) : ?>
		<aside id="post_author" class="blog_section"><?php echo get_avatar( get_the_author_meta('user_email'), '80' ); ?>
			<h3><?php the_author_posts_link(); ?></h3>
			<p><?php the_author_meta('description'); ?></p>
			<?php inbound_author_social(); ?>
		</aside>
	<?php endif; ?>
	<!--End About The Author-->

	<!--Start Comments-->
	<?php
	// If comments are open or we have at least one comment, load up the comment template
	if ( comments_open() || '0' != get_comments_number() ) :
		comments_template();
	endif;
	?>
	<!--End Comments-->

</article>
<!--EndBlogPost-->
