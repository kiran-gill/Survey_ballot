<?php
/**
 * @package inbound
 */
?>

<!--BlogPost-->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<h1><?php the_title(); ?></h1>
		<div class="header_meta"><?php esc_html_e('By', 'inbound'); ?> <?php inbound_posted_by(); ?> <span class="meta-date"><?php inbound_posted_on(); ?></span> <?php inbound_categories(); ?> <?php if ( comments_open() ) : ?><span class="meta_comment"><a href="<?php comments_link(); ?>"><?php esc_html_e('Leave a comment', 'inbound'); ?></a></span><?php endif; ?></div>
	</header>

	<?php the_post_thumbnail('inbound-post-thumbnail'); ?>

	<!--Post Content-->
	<?php the_content(); ?>
	<!--End Post Content-->

	<?php
	inbound_wp_link_pages(array(
		'before' => '<div class="page-link">' . esc_html__('Pages:', 'inbound'),
		'after' => '</div>'
	));
	?>

	<?php if ( has_tag() || inbound_has_share() ) : ?>
		<div class="post-social-tags">
			<?php if( has_tag() ) : ?>
				<!--Post Tags-->
				<p id="post_tags">
					<span><?php esc_html_e('Tagged under:', 'inbound'); ?></span>
					<?php inbound_tags(); ?>
				</p>
				<!--End Post Tags-->
			<?php endif; ?>
			<?php inbound_share(); ?>
		</div>
	<?php endif; ?>

	<!--About The Author-->
	<?php if ( get_the_author_meta('description') ) : ?>
		<aside id="post_author" class="blog-section"><?php echo get_avatar( get_the_author_meta('user_email'), '80' ); ?>
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

	<?php if ( inbound_option('blog_related_posts', false) ) : ?>
		<!--Related Posts-->
		<?php inbound_related_posts(); ?>
		<!--End Related Posts-->
	<?php endif; ?>

	<?php inbound_pagination(); ?>

</article>
<!--EndBlogPost-->
