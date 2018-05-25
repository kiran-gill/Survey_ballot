<?php
/*
 * Buttons
 */

/*
 * Button Styles
 */
$f = $tpl_global['fonts'];
$styles = inbound_option('global_button_styles', false);
$default_button_style = inbound_option('default_button_style', false);
$default_button_style_css = array();
$f->get_button_style_fonts($styles);

if ($styles && is_array($styles)) {
	foreach ($styles as $style) :
		if (!isset($style['shadow'])) $style['shadow'] = false;
		if (!isset($style['border'])) $style['border'] = false;
		if (!isset($style['force_fonts'])) $style['force_fonts'] = false;

		$uid = $style['uid'];

		$button_style['meta'] = array(
			'uid' => $style['uid'],
			'shadow' => $style['shadow'],
			'radius' => $style['radius'].'px',
			'font' => $style['font'],
			'force_fonts' => $style['force_fonts']
		);


		$button_style['default'] = array(
			'background_mode' => $style['default_background_mode'],
			'color_1' => $style['default_color_1'],
			'color_2' => $style['default_color_2'],
			'text' => $style['default_color_text'],
		);


		$button_style['hover'] = array(
			'background_mode' => $style['hover_background_mode'],
			'color_1' => $style['hover_color_1'],
			'color_2' => $style['hover_color_2'],
			'text' => $style['hover_color_text'],
		);

		// default style
		$css_default = '';
		if ($button_style['default']['background_mode'] == "gradient") {
			$css_default = 'background: '. $button_style['default']['color_1'] .';
		background: -moz-linear-gradient(top, ' . $button_style['default']['color_1'] .' 0%, ' . $button_style['default']['color_2'] . ' 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,' . $button_style['default']['color_1'] . '), color-stop(100%,' . $button_style['default']['color_2'] . '));
		background: -webkit-linear-gradient(top, ' . $button_style['default']['color_1'] . ' 0%,' . $button_style['default']['color_2'] . ' 100%);
		background: -o-linear-gradient(top, ' . $button_style['default']['color_1'] . ' 0%,' . $button_style['default']['color_2'] . ' 100%);
		background: -ms-linear-gradient(top, ' . $button_style['default']['color_1'] . ' 0%,' . $button_style['default']['color_2'] . ' 100%);
		background: linear-gradient(to bottom, ' . $button_style['default']['color_1'] . ' 0%,' . $button_style['default']['color_2'] . ' 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'' . $button_style['default']['color_1'] . '\', endColorstr=\'' . $button_style['default']['color_2'] . '\',GradientType=0 );';
		} elseif ($button_style['default']['background_mode'] == "solid") {
			$css_default .= 'background:' . $button_style['default']['color_1'] . ';';
		} elseif ($button_style['default']['background_mode'] == "transparent") {
			$css_default .= 'background:none;filter:none;';
		}

		if ($style['border']) {
			$css_default .= 'border-color:' . inbound_hex2rgba($button_style['default']['color_1'], 1) . ' !important;';
		} else {
			$css_default .= 'border:0!important;';
		}

		$css_default .= 'color:' . $button_style['default']['text'] . '!important;';

		// mouse over/hover style
		$css_hover  = '';
		if ($button_style['hover']['background_mode'] == "gradient") {
			$css_hover = 'background: '. $button_style['hover']['color_1'] .';
		background: -moz-linear-gradient(top, ' . $button_style['hover']['color_1'] .' 0%, ' . $button_style['hover']['color_2'] . ' 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,' . $button_style['hover']['color_1'] . '), color-stop(100%,' . $button_style['hover']['color_2'] . '));
		background: -webkit-linear-gradient(top, ' . $button_style['hover']['color_1'] . ' 0%,' . $button_style['hover']['color_2'] . ' 100%);
		background: -o-linear-gradient(top, ' . $button_style['hover']['color_1'] . ' 0%,' . $button_style['hover']['color_2'] . ' 100%);
		background: -ms-linear-gradient(top, ' . $button_style['hover']['color_1'] . ' 0%,' . $button_style['hover']['color_2'] . ' 100%);
		background: linear-gradient(to bottom, ' . $button_style['hover']['color_1'] . ' 0%,' . $button_style['hover']['color_2'] . ' 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'' . $button_style['hover']['color_1'] . '\', endColorstr=\'' . $button_style['hover']['color_2'] . '\',GradientType=0 );';
		} elseif ($button_style['hover']['background_mode'] == "solid") {
			$css_hover .= 'background:' . $button_style['hover']['color_1'] . ';';
		} elseif ($button_style['hover']['background_mode'] == "transparent") {
			$css_hover .= 'background:none;filter:none;';
		}

		if ($style['border']) {
			$css_hover .= 'border-color:' . inbound_hex2rgba( $button_style['hover']['color_1'], 1 ) . ' !important;';
		} else {
			$css_default .= 'border:0!important;';
		}

		$css_hover .= 'color:' . $button_style['hover']['text'] . '!important;';

		if ($button_style['meta']['shadow']) {
			$css_default .= '-webkit-box-shadow: 0 1px 13px rgba(0, 0, 0, 0.3); -moz-box-shadow: 0 1px 13px rgba(0, 0, 0, 0.3); box-shadow: 0 1px 13px rgba(0, 0, 0, 0.3);' . "\n";
		}

		$css_default .= 'border-radius:' . $button_style['meta']['radius'] . '; -webkit-border-radius:' . $button_style['meta']['radius'] . '; -moz-border-radius:' . $button_style['meta']['radius'] . ';' . "\n";

		if ( $uid == $default_button_style ) {
			$default_button_style_css['default'] = $css_default;

			if ( isset ( $button_style['meta']['force_fonts'] ) && $button_style['meta']['force_fonts']  ) {
				$default_button_style_css['default'] .= $f->get_typography_css( $button_style['meta']['font'] );
			}

			$default_button_style_css['hover'] = $css_hover;
		}

		?>
		.button-style-<?php echo $button_style['meta']['uid']; ?> {
		<?php echo $f->get_typography_css( $button_style['meta']['font'] ); ?>
		<?php echo $css_default; ?>
		}
		.button-style-<?php echo $button_style['meta']['uid']; ?>:hover {
		<?php echo $css_hover; ?>
		}
		<?php
	endforeach;
}



/*
 * General Button Styling
 */
?>

.button_cta p.hint {
<?php echo $f->get_font_family('font_body'); ?>
}


<?php if (!empty($default_button_style_css)) : ?>
	#submit, .widget_search form:after, .widget_product_search form:after, input.wpcf7-submit, #searchsubmit, .button, input[type="submit"], .single_add_to_cart_button, .button, #tool-navigation-lower .buttons a.button, .product-options a {
	<?php echo $default_button_style_css['default']; ?>
	}

	#submit:hover, .widget_search form:after:hover, .widget_product_search form:after:hover, input.wpcf7-submit:hover, .button:hover, #searchsubmit:hover, input[type="submit"]:hover, .single_add_to_cart_button:hover, .button:hover, #tool-navigation-lower .buttons a.button:hover, .product-options a:hover {
	<?php echo $default_button_style_css['hover']; ?>
	}
<?php endif ?>

<?php
/* TODO: Obsolete code that needs to be reimplemented. */
/*
#header-cart-total:after {
color: <?php echo inbound_option('navigation_background_hover'); ?>;
}
*/
?>
