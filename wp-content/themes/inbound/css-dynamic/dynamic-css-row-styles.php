<?php
$styles = inbound_option('row_styles');
if ($styles && is_array($styles)) {
	foreach ($styles as $style) :
		$color_1 	                = $style['row_style_color_1'];
		$color_2 	                = $style['row_style_color_2'];
		$color_text                 = $style['row_style_color_text'];
		$color_headline_section     = $style['row_style_headline_section'];
		$color_headline_subtitle    = $style['row_style_headline_subtitle'];
		$mode		                = $style['row_style_mode'];
		?>
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?>,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> h1,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> h2,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> h3,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> h4,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> h5,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> h6,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> .testimonials footer,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> .testimonial footer,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> .testimonial-layout-large q,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> .testimonial-layout-elegant q
		{
		color: <?php echo $color_text; ?>;
		}
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> {
		<?php if ($mode == 'solid') : ?>
		background: <?php echo $color_1; ?>;
	<?php elseif ($mode == 'gradient') : ?>
		background: <?php echo $color_1; ?>;
		background: -moz-linear-gradient(top, <?php echo $color_1; ?> 0%, <?php echo $color_2; ?> 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $color_1; ?>), color-stop(100%,<?php echo $color_2; ?>));
		background: -webkit-linear-gradient(top, <?php echo $color_1; ?> 0%,<?php echo $color_2; ?> 100%);
		background: -o-linear-gradient(top, <?php echo $color_1; ?> 0%,<?php echo $color_2; ?> 100%);
		background: -ms-linear-gradient(top, <?php echo $color_1; ?> 0%,<?php echo $color_2; ?> 100%);
		background: linear-gradient(to bottom, <?php echo $color_1; ?> 0%,<?php echo $color_2; ?> 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $color_1; ?>', endColorstr='<?php echo $color_2; ?>',GradientType=0 );
	<?php elseif ($mode == 'image_fixed') : ?>
		background: url(<?php echo $style['row_style_image']['src']; ?>);
		background-attachment: fixed;
		background-size: cover;
		-moz-background-size: cover;
		-webkit-background-size: cover;
		-o-background-size: cover;
		background-position:center;
	<?php elseif ($mode == 'image_tile') : ?>
		background: url(<?php echo $style['row_style_image']['src']; ?>);
	<?php endif; ?>
		}
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> .section-title,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> .regular-title
		{
		color: <?php echo $color_headline_section; ?>;
		}

		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> .section-sub-title,
		.panel-row-style.panel-row-style-<?php echo trim(strtolower(sanitize_html_class($style['row_style_name']))); ?> .regular-sub-title {
		color: <?php echo $color_headline_subtitle; ?>;
		}
	<?php
	endforeach;
}
