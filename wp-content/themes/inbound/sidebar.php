<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package inbound
 */
?>
<!--Start Blog Sidebar-->
<div id="sidebar" class="col-4" role="complementary">
	<?php if ( ! dynamic_sidebar( 'inbound-blog-sidebar' ) ) : ?>

	<?php endif; ?>
</div>
<!--End Blog Sidebar-->
