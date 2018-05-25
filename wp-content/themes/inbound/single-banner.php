<?php
/**
 * Template to preview banners
 * For Internal Use Only
 * Not available for user selection, not used to render banner on regular pages
 *
 * @package inbound
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<!--Page Title-->
	<title><?php wp_title( '|', true, 'right' ); ?></title>

	<!--Device Width Check-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php do_action('inbound_after_body'); ?>
<?php if (inbound_option('design_boxed')) : ?><div id="wrapper"><?php endif; ?>
	<!--Start of Banner-->
	<?php if ( ! inbound_custom_block('banner') ) : ?>
		<section id="banner" role="banner" <?php inbound_banner_wrapper_classes(); ?><?php inbound_banner_data_attrs(); ?>>
			<!--Header Region-->
			<!--End Header Region-->
			<?php if ( ! inbound_custom_block('banner_content') ) : ?>
				<?php inbound_the_banner();	?>
			<?php endif; ?>
		</section>
	<?php endif; /* is custom banner */ ?>
	<!--End of Banner-->
	<?php if (inbound_is_layout('boxed')) : ?></div><!--End of Wrapper--><?php endif; ?>
</body>
</html>