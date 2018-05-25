<?php
/*
 * Dynamic CSS Code for Main Banner
 */
$f = $tpl_global['fonts'];

$banner_options       		= inbound_option('the_banner_options');

$banner_background_mode		= inbound_custom_value('sr_inbound_background_mode', $banner_options);
$banner_color_1 			= inbound_custom_value('sr_inbound_color_1', $banner_options, '#ffffff');
$banner_color_2 			= inbound_custom_value('sr_inbound_color_2', $banner_options, '#ffffff');
$banner_image				= inbound_custom_value('sr_inbound_background_image', $banner_options, false, true);

$banner_body_font = inbound_custom_value('sr_inbound_font_banner_text', $banner_options, false, true);
$banner_text_color = '#ffffff';
if ($banner_body_font) {
	if (is_array($banner_body_font)) $banner_text_color = $banner_body_font['color'];
}

if ($banner_background_mode == 'solid') : // solid color
	?>
	#banner {
	background: <?php echo $banner_color_1; ?>;
	}
<?php
elseif ($banner_background_mode == 'gradient') : // gradient
	?>
	#banner {
	background: <?php echo $banner_color_1; ?>;
	background: -moz-linear-gradient(top, <?php echo $banner_color_1; ?> 0%, <?php echo $banner_color_2; ?> 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $banner_color_1; ?>), color-stop(100%,<?php echo $banner_color_2; ?>));
	background: -webkit-linear-gradient(top, <?php echo $banner_color_1; ?> 0%,<?php echo $banner_color_2; ?> 100%);
	background: -o-linear-gradient(top, <?php echo $banner_color_1; ?> 0%,<?php echo $banner_color_2; ?> 100%);
	background: -ms-linear-gradient(top, <?php echo $banner_color_1; ?> 0%,<?php echo $banner_color_2; ?> 100%);
	background: linear-gradient(to bottom, <?php echo $banner_color_1; ?> 0%,<?php echo $banner_color_2; ?> 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $banner_color_1; ?>', endColorstr='<?php echo $banner_color_2; ?>',GradientType=0 );
	}
<?php
elseif ($banner_background_mode == 'image-tile' && $banner_image) : // background image, tiled
	?>
	#banner {
	background: url(<?php echo $banner_image['url']; ?>);
	}
<?php
elseif ($banner_background_mode == 'image-fixed' && $banner_image) : // background image, fixed
	?>
	#banner {
	background: url(<?php echo $banner_image['url']; ?>);
	background-attachment: fixed;
	background-size: cover;
	background-position:center;
	}
<?php
elseif ($banner_background_mode == 'image-parallax' && $banner_image) : // background image, with parallax effect
?>
	#banner {
	background: url(<?php echo $banner_image['url']; ?>);
	background-size: cover;
	}
<?php
elseif ($banner_background_mode == 'image-centered' && $banner_image) : // background image, original size, centered
?>
	#banner {
	background: url(<?php echo $banner_image['url']; ?>) no-repeat center top;
	}
<?php
elseif ($banner_background_mode == 'image-cover' && $banner_image) : // background image, cover
?>
#banner {
	background: url(<?php echo $banner_image['url']; ?>) no-repeat center top;
	background-size:cover;
}
<?php
endif;
?>

#banner h3,
#banner h4,
#banner h5,
#banner h6,
#banner widget-title,
#banner .textwidget
{
color: <?php echo $banner_text_color; ?>;
}

#banner p {
<?php echo $f->get_typography_css( $banner_body_font ); ?>
}

#banner #banner_content h1.regular-title, #banner #banner_content h1.section-title  {
<?php echo $f->get_typography_css( inbound_custom_value('sr_inbound_font_banner_title', $banner_options, false, true) ); ?>
}

#banner #banner_content h2.regular-sub-title, #banner #banner_content h2.section-sub-title {
<?php echo $f->get_typography_css( inbound_custom_value('sr_inbound_font_banner_sub_title', $banner_options, false, true) ); ?>
}

.has-transparent-menu #banner #header-region #logo h1,
.has-transparent-menu #banner #header-region #logo h1 a,
.has-transparent-menu #banner #header-region #logo h2,
.has-transparent-menu #banner #header-region ul li a,
.has-transparent-menu #banner #header-region #tool-navigation-lower,
.has-transparent-menu #banner #header-region #tool-navigation-lower a,
.has-transparent-menu #banner #header-region ul li a:hover {
color: <?php echo $banner_text_color; ?>;
}

.has-transparent-menu #banner #header-region .social-icons.nav-separator {
border-left: 1px solid <?php echo inbound_hex2rgba($banner_text_color, .15); ?>;
}

#header-region .social-icons.nav-separator, .has-transparent-menu #banner.animated-header-shrink #header-region .social-icons.nav-separator {
border-left: 1px solid <?php echo inbound_hex2rgba(inbound_option('header_text'), .15); ?>;
}