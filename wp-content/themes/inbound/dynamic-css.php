<?php
/*
 * Dynamic CSS (for header output
 */

/*
 * Body
 */
$body_font = inbound_option('font_body');
$body_font_color = '#000000';
if (is_serialized($body_font)) {
	$body_font = unserialize($body_font);
	if (is_array($body_font)) $body_font_color = $body_font['color'];
}

/*
 * Typography
 */
$font_options = array(
	'font_body',
	'font_logo',
	'font_logo_tagline',
	'font_banner_title',
	'font_banner_sub_title',
	'font_banner_text',
	'font_page_title',
	'font_widget_title',
	'font_quote',
	'font_call_to_action',
	'font_pricing_call_to_action',
	'font_h1',
	'font_h2',
	'font_h3',
	'font_h4',
	'font_h5',
	'font_h6'
);
$f = new SR_Typography();
$f->get_fonts_in_use($font_options);

/*
 * Google Web Fonts
 */
$imports = $f->get_fonts_import();
if ($imports) {
	$subsets = inbound_option('web_fonts_subsets', '');

	if ( is_array($subsets) && count($subsets) > 0 ) {
		$subsets = str_replace(" ", "", implode(',', $subsets));
		inbound_set_option('font_imports', '<link href="//fonts.googleapis.com/css?family=' . $imports . '%26subset=' . $subsets . '" rel="stylesheet" type="text/css">' . "\n");
	} else {
		inbound_set_option('font_imports', '<link href="//fonts.googleapis.com/css?family=' . $imports . '" rel="stylesheet" type="text/css">' . "\n");
	}
}

/*
 * Custom Web Fonts
 */
$custom_fonts = $f->get_custom_fonts_import();
if ($custom_fonts) {
	foreach ($custom_fonts as $font) {
		echo "@font-face {";
		echo "font-family: '" . $font['face'] . "';";

		if (!empty($font['files']['eot'])) {
			echo "src: url('" . $font['files']['eot'] . "');";
			echo "src: url('" . $font['files']['eot'] . "?#iefix') format('embedded-opentype'),";
		}

		if (!empty($font['files']['woff'])) {
			echo "src: url('" . $font['files']['woff'] . "') format('woff');";
		}

		if (!empty($font['files']['ttf'])) {
			echo "src: url('" . $font['files']['ttf'] . "') format('truetype');";
		}

		if (!empty($font['files']['svg'])) {
			echo "src: url('" . $font['files']['svg'] . "#svgFontName') format('svg');";
		}

		echo "}";
		echo "}";
	}
}

/*
 * Body and Sections
 */
inbound_include_file( get_template_directory() . DIRECTORY_SEPARATOR . 'css-dynamic' . DIRECTORY_SEPARATOR . 'dynamic-css-body.php', array('fonts' => $f, 'body_font_color' => $body_font_color) );

/*
 * Typography
 */
inbound_include_file( get_template_directory() . DIRECTORY_SEPARATOR . 'css-dynamic' . DIRECTORY_SEPARATOR . 'dynamic-css-typography.php', array('fonts' => $f ) );

/*
 * Buttons
 */
inbound_include_file ( get_template_directory() . DIRECTORY_SEPARATOR . 'css-dynamic' . DIRECTORY_SEPARATOR . 'dynamic-css-buttons.php', array('fonts' => $f )  );

/*
 * Banner
 */
$banner_id = inbound_option('the_banner_id');
if ($banner_id && !empty($banner_id)) {
	inbound_include_file ( get_template_directory() . DIRECTORY_SEPARATOR . 'css-dynamic' . DIRECTORY_SEPARATOR . 'dynamic-css-banner.php', array('fonts' => $f ) );
}

/*
 * Page Title
*/
?>

h1.page-title, #page-header h1 {
<?php echo $f->get_typography_css( 'font_page_title' ); ?>
}

#page-header, #page-header a, #page-header h1 {
background:<?php echo inbound_option('header_sub_banner_color_background'); ?>;
color:<?php echo inbound_option('header_sub_banner_color_text'); ?>;
}

<?php

/*
 * Script and widget generated CSS
 */

/*
* Widgets and Content Blocks
*/
echo "\n";

$widget_styles = inbound_option('styles');

if ($widget_styles && is_array($widget_styles) && count($widget_styles) > 0) {
	foreach ($widget_styles as $type => $styles) {
		if (is_array($styles) && count($styles) > 0) {
			if (INBOUND_DEBUG) echo '/* Type: ' . ucwords($type) . ' */' . "\n";
			foreach ($styles as $style) {
				echo $style . "\n";
			}
		}
	}
}

/*
* Social Icons Block
*/
$social_icons = inbound_option('social_media_profiles');
if (is_serialized($social_icons)) {
	$services = unserialize($social_icons);
	if (is_array($services)) {
		$count=1;
		foreach ($services as $service) {
			if (inbound_array_option('show_in_widget', $service, false)) {
				$color = inbound_array_option('color_background', $service, 'transparent');

				echo '.social-widget.style-color a.social-icon-' . $count . ' { background-color:'. $color . '; }' . "\n";

				echo '.social-widget.style-transparent a.social-icon-' . $count . ' { background-color: transparent; }' . "\n";
				echo '.social-widget.style-transparent a.social-icon-' . $count . ':hover,
				 .social-widget.style-black a.social-icon-' . $count . ':hover,
  			     .social-widget.style-white a.social-icon-' . $count . ':hover{ background-color:'. $color . '; }' . "\n";

				$count++;
			}
		}
	}
}


/*
 * Custom CSS Implementation
 */

/*
 * Global Custom CSS
 */
echo inbound_option('advanced_global_css', false);

/*
 * Profile Specific Custom CSS
 */
echo inbound_option('advanced_profile_custom_css', false);

/*
 * Page or Post Specific Custom CSS
 */
echo inbound_page_option( 'sr_inbound_advanced_page_custom_css', false);


?>

