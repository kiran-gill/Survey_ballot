<?php
/**
 * Template to display individual pages in WooCommerce
 *
 * @package inbound
 */
?>

<!--BlogPost-->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<!--Post Content-->
	<?php the_content(); ?>
	<!--End Post Content-->

</article>
<!--EndBlogPost-->
