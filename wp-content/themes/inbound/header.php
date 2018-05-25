<?php
/**
 * The Main Header
 *
 * Displays all of the <head> section and everything up until the start of the actual page content
 *
 * @package inbound
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>


	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<!--Device Width Check-->
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php
	/* for WP lower than 4.3 */
	if ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) {
		inbound_favicon();
	}
	?>

	<?php wp_head(); ?>
 
</head>
<body <?php body_class(); ?>>
<div id="skrollr-body">
<?php do_action('inbound_after_body'); ?>
<?php if (inbound_is_layout('boxed')) : ?><div id="wrapper"><?php endif; ?>

	<?php inbound_banner(); ?>

	<!--Start of Main Content-->
	<main>
		<div id="main_content" class="clearfix">
