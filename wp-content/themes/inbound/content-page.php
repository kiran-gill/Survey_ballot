<?php
/**
 * Template to display individual pages
 *
 * @package inbound
 */
?>

<!--BlogPost-->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( !inbound_meta('hide_title') ) : ?>
		<header>
			<h1><?php the_title(); ?></h1>
			<?php if ( inbound_meta('subtitle') ) : ?>
					<h2><?php echo esc_html( inbound_meta('subtitle') ); ?></h2>
			<?php endif; ?>
		</header>
	<?php endif; ?>

	<!--Post Content-->
	<div class="page-content">
		<?php the_content(); ?>
	</div>
	<!--End Post Content-->

	<?php
	inbound_wp_link_pages(array(
			'before' => '<div class="page-link">' . esc_html__('Pages:', 'inbound'),
			'after' => '</div>'
	));
	?>

	<!--Start Comments-->
	<?php
	// If comments are open or we have at least one comment, load up the comment template
	if ( inbound_option('content_page_comments', false) && (comments_open() || '0' != get_comments_number() ) ) :
		comments_template();
	endif;
	?>
	<!--End Comments-->

</article>
<!--EndBlogPost-->
