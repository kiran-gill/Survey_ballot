<?php
/**
 * Configure page builder settings.
 */
function inbound_panels_settings($old_settings){
	$supported_types = inbound_option('pagebuilder_post_types', array ( 'page', 'post', 'banner', 'modal' ) );

	$supported_types = array_merge ( $supported_types, array( 'page', 'post', 'banner', 'modal' ) );
	$supported_types = array_unique ( $supported_types );

	$settings = array (
		'home-page' => false,
		'home-page-default' => false,
		'post-types' => $supported_types,
		'bundled-widgets' => inbound_option('pagebuilder_bundled_widgets', false),
		'responsive' => true,
		'display-teaser' => false,
		'tablet-layout' => inbound_option('pagebuilder_tablet_layout', false),
		'tablet-width' => inbound_option('pagebuilder_tablet_width', 1024),
		'mobile-width' => inbound_option('pagebuilder_mobile_width', 780),
		'margin-bottom' => inbound_option('pagebuilder_row_bottom_margin', 0),
		'margin-sides' => inbound_option('pagebuilder_cell_side_margins', 30),
		'affiliate-id' => '',
		'copy-content' => inbound_option('pagebuilder_copy_content', true),
		'animations' => false,
		'inline-css' => inbound_option('pagebuilder_inline_css', true),
		'add-widget-class' => false
	);

	if ( is_array ( $old_settings ) )
		$settings = array_merge( $old_settings, $settings );

	return $settings;
}
add_filter('siteorigin_panels_settings', 'inbound_panels_settings');

/*
 * Page builder processing
 */

function inbound_siteorigin_panels_css_object ( $css, $panels_data, $post_id ) {
	$settings = siteorigin_panels_setting();
	foreach ( $panels_data['grids'] as $gi => $grid ) {
		// Ony apply gutters to rows with only one cell
		if($grid['cells'] == 0) continue;

		// Let other themes and plugins change the gutter.
		$gutter = apply_filters('siteorigin_panels_css_row_gutter', $settings['margin-sides'].'px', $grid, $gi, $panels_data);

		if( !empty($gutter) ) {
			// We actually need to find half the gutter.
			preg_match('/([0-9\.,]+)(.*)/', $gutter, $match);
			if( !empty( $match[1] ) ) {
				$margin_half = (floatval($match[1])/2) . $match[2];
				$css->add_row_css($post_id, $gi, '', array(
						'margin-left' => '-' . $margin_half,
						'margin-right' => '-' . $margin_half,
				) );
				$css->add_cell_css($post_id, $gi, false, '', array(
						'padding-left' => $margin_half,
						'padding-right' => $margin_half,
				) );

			}
		}
	}
	return $css;
}
add_filter('siteorigin_panels_css_object', 'inbound_siteorigin_panels_css_object', 10, 3);


function inbound_panels_row_style_fields($fields) {

	if (isset($fields['padding'])) unset($fields['padding']);

	$fields['bottom_margin']['priority'] = 1;

	/* Layout Options */

	$fields['row_justification'] = array(
		'group' => 'layout',
		'name' => esc_html__('Row Text Alignment', 'inbound'),
		'type' => 'select',
		'options' => array(
			'left'   => esc_html__('Left', 'inbound'),
			'center' => esc_html__('Center', 'inbound'),
			'right'  => esc_html__('Right', 'inbound'),
		),
		'default' => 'left'
	);

	$fields['row_flexbox'] = array(
		'group' => 'layout',
		'name' => esc_html__('Equal widget height (experimental)', 'inbound'),
		'type' => 'checkbox',
		'description' => esc_html__('If this option is checked, all widgets in this row will have the same height. Does not work with stacked widgets.', 'inbound'),
	);

	$fields['row_stretch'] = array(
		'name' => esc_html__('Row Layout', 'inbound'),
		'type' => 'select',
		'group' => 'layout',
		'options' => array(
			'' => esc_html__('Standard', 'inbound'),
			'full' => esc_html__('Full Width', 'inbound'),
			'full-stretched' => esc_html__('Full Width (stretched)', 'inbound'),
		),
		'priority' => 10,
	);

	$fields['padding_top'] = array(
		'name' => esc_html__('Padding (Top)', 'inbound'),
		'type' => 'measurement',
		'group' => 'layout',
		'description' => esc_html__('Padding at the top of the row.', 'inbound'),
		'priority' => 2,
	);

	$fields['padding_bottom'] = array(
		'name' => esc_html__('Padding (Bottom)', 'inbound'),
		'type' => 'measurement',
		'group' => 'layout',
		'description' => esc_html__('Padding at the bottom of the row.', 'inbound'),
		'priority' => 3,
	);

	$fields['padding_left'] = array(
		'name' => esc_html__('Padding (Left)', 'inbound'),
		'type' => 'measurement',
		'group' => 'layout',
		'description' => esc_html__('Padding at the left of the row.', 'inbound'),
		'priority' => 4,
	);

	$fields['padding_right'] = array(
		'name' => esc_html__('Padding (Right)', 'inbound'),
		'type' => 'measurement',
		'group' => 'layout',
		'description' => esc_html__('Padding at the right of the row.', 'inbound'),
		'priority' => 5,
	);

	$fields['background_display']['options'] = array (
		'fixed' => esc_html__('Fixed', 'inbound'),
		'cover' => esc_html__('Cover', 'inbound'),
		'center' => esc_html__('Centered, with original size', 'inbound'),
		'inbound-parallax' => esc_html__('Parallax', 'inbound'),
		'tile' => esc_html__('Tile', 'inbound'),
	);

	/* Responsiveness */

	$fields['hide_on_mobile'] = array(
		'name' => esc_html__('Hide on mobile phones', 'inbound'),
		'type' => 'checkbox',
		'group' => 'responsiveness',
		'description' => esc_html__('Hides the entire row on smartphones or other devices with very small screens.', 'inbound'),
		'priority' => 1,
	);

	$fields['hide_on_tablet'] = array(
		'name' => esc_html__('Hide on tablets', 'inbound'),
		'type' => 'checkbox',
		'group' => 'responsiveness',
		'description' => esc_html__('Hides the entire row on tablets or laptops with small screens.', 'inbound'),
		'priority' => 2,
	);

	$fields['hide_on_desktop'] = array(
		'name' => esc_html__('Hide on desktops', 'inbound'),
		'type' => 'checkbox',
		'group' => 'responsiveness',
		'description' => esc_html__('Hides the entire row on desktop or laptop computers with large screens.', 'inbound'),
		'priority' => 3,
	);


	/* Animations */
	if ( ! defined ( 'INBOUND_DISABLE_ANIMATIONS' )) {

		$fields['animation_type'] = array(
			'name'     => esc_html__( 'Type', 'inbound' ),
			'type'     => 'select',
			'group'    => 'animation',
			'options'  => array(
				''                   => esc_html__( 'None', 'inbound' ),
				'bounce'             => esc_html__( 'bounce', 'inbound' ),
				'flash'              => esc_html__( 'flash', 'inbound' ),
				'pulse'              => esc_html__( 'pulse', 'inbound' ),
				'rubberBand'         => esc_html__( 'rubberBand', 'inbound' ),
				'shake'              => esc_html__( 'shake', 'inbound' ),
				'swing'              => esc_html__( 'swing', 'inbound' ),
				'tada'               => esc_html__( 'tada', 'inbound' ),
				'wobble'             => esc_html__( 'wobble', 'inbound' ),
				'jello'              => esc_html__( 'jello', 'inbound' ),
				'bounceIn'           => esc_html__( 'bounceIn', 'inbound' ),
				'bounceInDown'       => esc_html__( 'bounceInDown', 'inbound' ),
				'bounceInLeft'       => esc_html__( 'bounceInLeft', 'inbound' ),
				'bounceInRight'      => esc_html__( 'bounceInRight', 'inbound' ),
				'bounceInUp'         => esc_html__( 'bounceInUp', 'inbound' ),
				'bounceOut'          => esc_html__( 'bounceOut', 'inbound' ),
				'bounceOutDown'      => esc_html__( 'bounceOutDown', 'inbound' ),
				'bounceOutLeft'      => esc_html__( 'bounceOutLeft', 'inbound' ),
				'bounceOutRight'     => esc_html__( 'bounceOutRight', 'inbound' ),
				'bounceOutUp'        => esc_html__( 'bounceOutUp', 'inbound' ),
				'fadeIn'             => esc_html__( 'fadeIn', 'inbound' ),
				'fadeInDown'         => esc_html__( 'fadeInDown', 'inbound' ),
				'fadeInDownBig'      => esc_html__( 'fadeInDownBig', 'inbound' ),
				'fadeInLeft'         => esc_html__( 'fadeInLeft', 'inbound' ),
				'fadeInLeftBig'      => esc_html__( 'fadeInLeftBig', 'inbound' ),
				'fadeInRight'        => esc_html__( 'fadeInRight', 'inbound' ),
				'fadeInRightBig'     => esc_html__( 'fadeInRightBig', 'inbound' ),
				'fadeInUp'           => esc_html__( 'fadeInUp', 'inbound' ),
				'fadeInUpBig'        => esc_html__( 'fadeInUpBig', 'inbound' ),
				'fadeOut'            => esc_html__( 'fadeOut', 'inbound' ),
				'fadeOutDown'        => esc_html__( 'fadeOutDown', 'inbound' ),
				'fadeOutDownBig'     => esc_html__( 'fadeOutDownBig', 'inbound' ),
				'fadeOutLeft'        => esc_html__( 'fadeOutLeft', 'inbound' ),
				'fadeOutLeftBig'     => esc_html__( 'fadeOutLeftBig', 'inbound' ),
				'fadeOutRight'       => esc_html__( 'fadeOutRight', 'inbound' ),
				'fadeOutRightBig'    => esc_html__( 'fadeOutRightBig', 'inbound' ),
				'fadeOutUp'          => esc_html__( 'fadeOutUp', 'inbound' ),
				'fadeOutUpBig'       => esc_html__( 'fadeOutUpBig', 'inbound' ),
				'flipInX'            => esc_html__( 'flipInX', 'inbound' ),
				'flipInY'            => esc_html__( 'flipInY', 'inbound' ),
				'flipOutX'           => esc_html__( 'flipOutX', 'inbound' ),
				'flipOutY'           => esc_html__( 'flipOutY', 'inbound' ),
				'lightSpeedIn'       => esc_html__( 'lightSpeedIn', 'inbound' ),
				'lightSpeedOut'      => esc_html__( 'lightSpeedOut', 'inbound' ),
				'rotateIn'           => esc_html__( 'None', 'inbound' ),
				'rotateInDownLeft'   => esc_html__( 'rotateInDownLeft', 'inbound' ),
				'rotateInDownRight'  => esc_html__( 'rotateInDownRight', 'inbound' ),
				'rotateInUpLeft'     => esc_html__( 'rotateInUpLeft', 'inbound' ),
				'rotateInUpRight'    => esc_html__( 'rotateInUpRight', 'inbound' ),
				'rotateOut'          => esc_html__( 'rotateOut', 'inbound' ),
				'rotateOutDownLeft'  => esc_html__( 'rotateOutDownLeft', 'inbound' ),
				'rotateOutDownRight' => esc_html__( 'rotateOutDownRight', 'inbound' ),
				'rotateOutUpLeft'    => esc_html__( 'rotateOutUpLeft', 'inbound' ),
				'rotateOutUpRight'   => esc_html__( 'rotateOutUpRight', 'inbound' ),
				'hinge'              => esc_html__( 'hinge', 'inbound' ),
				'rollIn'             => esc_html__( 'rollIn', 'inbound' ),
				'rollOut'            => esc_html__( 'rollOut', 'inbound' ),
				'zoomIn'             => esc_html__( 'zoomIn', 'inbound' ),
				'zoomInDown'         => esc_html__( 'zoomInDown', 'inbound' ),
				'zoomInLeft'         => esc_html__( 'zoomInLeft', 'inbound' ),
				'zoomInRight'        => esc_html__( 'zoomInRight', 'inbound' ),
				'zoomInUp'           => esc_html__( 'zoomInUp', 'inbound' ),
				'zoomOut'            => esc_html__( 'zoomOut', 'inbound' ),
				'zoomOutDown'        => esc_html__( 'zoomOutDown', 'inbound' ),
				'zoomOutLeft'        => esc_html__( 'zoomOutLeft', 'inbound' ),
				'zoomOutRight'       => esc_html__( 'zoomOutRight', 'inbound' ),
				'zoomOutUp'          => esc_html__( 'zoomOutUp', 'inbound' ),
				'slideInDown'        => esc_html__( 'slideInDown', 'inbound' ),
				'slideInLeft'        => esc_html__( 'slideInLeft', 'inbound' ),
				'slideInRight'       => esc_html__( 'slideInRight', 'inbound' ),
				'slideInUp'          => esc_html__( 'slideInUp', 'inbound' ),
				'slideOutDown'       => esc_html__( 'slideOutDown', 'inbound' ),
				'slideOutLeft'       => esc_html__( 'slideOutLeft', 'inbound' ),
				'slideOutRight'      => esc_html__( 'slideOutRight', 'inbound' ),
				'slideOutUp'         => esc_html__( 'slideOutUp', 'inbound' ),
			),
			'priority' => 1
		);

		$fields['animation_duration'] = array(
			'name'        => esc_html__( 'Duration', 'inbound' ),
			'type'        => 'text',
			'group'       => 'animation',
			'description' => esc_html__( 'Duration of animation in milliseconds (e.g. 1000).', 'inbound' ),
			'priority'    => 5,
		);

		$fields['animation_delay'] = array(
			'name'        => esc_html__( 'Delay', 'inbound' ),
			'type'        => 'text',
			'group'       => 'animation',
			'description' => esc_html__( 'Delay before the animation starts in milliseconds (e.g. 1000).', 'inbound' ),
			'priority'    => 5,
		);
	}

	return $fields;
}
add_filter('siteorigin_panels_row_style_fields', 'inbound_panels_row_style_fields');


function inbound_panels_widget_style_fields( $fields ) {
	if (isset($fields['padding'])) unset($fields['padding']);

	$fields['text_alignment'] = array(
		'group' => 'layout',
		'name' => esc_html__('Text Alignment', 'inbound'),
		'type' => 'select',
		'options' => array(
			'default'=> esc_html__('Row Default', 'inbound'),
			'left'   => esc_html__('Left', 'inbound'),
			'center' => esc_html__('Center', 'inbound'),
			'right'  => esc_html__('Right', 'inbound'),
		),
		'default' => 'default'
	);

	$fields['font_color']['name'] = esc_html__('Text Color', 'inbound');

	$fields['padding_top'] = array(
		'name' => esc_html__('Padding (Top)', 'inbound'),
		'type' => 'measurement',
		'group' => 'layout',
		'description' => esc_html__('Padding at the top of the widget.', 'inbound'),
		'priority' => 2,
	);

	$fields['padding_bottom'] = array(
		'name' => esc_html__('Padding (Bottom)', 'inbound'),
		'type' => 'measurement',
		'group' => 'layout',
		'description' => esc_html__('Padding at the bottom of the widget.', 'inbound'),
		'priority' => 3,
	);

	$fields['padding_left'] = array(
		'name' => esc_html__('Padding (Left)', 'inbound'),
		'type' => 'measurement',
		'group' => 'layout',
		'description' => esc_html__('Padding at the left of the widget.', 'inbound'),
		'priority' => 4,
	);

	$fields['padding_right'] = array(
		'name' => esc_html__('Padding (Right)', 'inbound'),
		'type' => 'measurement',
		'group' => 'layout',
		'description' => esc_html__('Padding at the right of the widget.', 'inbound'),
		'priority' => 5,
	);

	/* Reponsiveness */
	$fields['hide_on_mobile'] = array(
		'name' => esc_html__('Hide on mobile devices', 'inbound'),
		'type' => 'checkbox',
		'group' => 'responsiveness',
		'description' => esc_html__('Hides this widget on mobile devices.', 'inbound'),
		'priority' => 1,
	);

	$fields['hide_on_tablet'] = array(
		'name' => esc_html__('Hide on tablets', 'inbound'),
		'type' => 'checkbox',
		'group' => 'responsiveness',
		'description' => esc_html__('Hides this widget on tablets or laptops with small screens.', 'inbound'),
		'priority' => 2,
	);

	$fields['hide_on_desktop'] = array(
		'name' => esc_html__('Hide on desktops', 'inbound'),
		'type' => 'checkbox',
		'group' => 'responsiveness',
		'description' => esc_html__('Hides this widget on desktop or laptop computers with large screens.', 'inbound'),
		'priority' => 3,
	);


	/* Animations */
	if ( ! defined ( 'INBOUND_DISABLE_ANIMATIONS' )) {

		$fields['animation_type'] = array(
			'name'     => esc_html__( 'Type', 'inbound' ),
			'type'     => 'select',
			'group'    => 'animation',
			'options'  => array(
				''                   => esc_html__( 'None', 'inbound' ),
				'bounce'             => esc_html__( 'bounce', 'inbound' ),
				'flash'              => esc_html__( 'flash', 'inbound' ),
				'pulse'              => esc_html__( 'pulse', 'inbound' ),
				'rubberBand'         => esc_html__( 'rubberBand', 'inbound' ),
				'shake'              => esc_html__( 'shake', 'inbound' ),
				'swing'              => esc_html__( 'swing', 'inbound' ),
				'tada'               => esc_html__( 'tada', 'inbound' ),
				'wobble'             => esc_html__( 'wobble', 'inbound' ),
				'jello'              => esc_html__( 'jello', 'inbound' ),
				'bounceIn'           => esc_html__( 'bounceIn', 'inbound' ),
				'bounceInDown'       => esc_html__( 'bounceInDown', 'inbound' ),
				'bounceInLeft'       => esc_html__( 'bounceInLeft', 'inbound' ),
				'bounceInRight'      => esc_html__( 'bounceInRight', 'inbound' ),
				'bounceInUp'         => esc_html__( 'bounceInUp', 'inbound' ),
				'bounceOut'          => esc_html__( 'bounceOut', 'inbound' ),
				'bounceOutDown'      => esc_html__( 'bounceOutDown', 'inbound' ),
				'bounceOutLeft'      => esc_html__( 'bounceOutLeft', 'inbound' ),
				'bounceOutRight'     => esc_html__( 'bounceOutRight', 'inbound' ),
				'bounceOutUp'        => esc_html__( 'bounceOutUp', 'inbound' ),
				'fadeIn'             => esc_html__( 'fadeIn', 'inbound' ),
				'fadeInDown'         => esc_html__( 'fadeInDown', 'inbound' ),
				'fadeInDownBig'      => esc_html__( 'fadeInDownBig', 'inbound' ),
				'fadeInLeft'         => esc_html__( 'fadeInLeft', 'inbound' ),
				'fadeInLeftBig'      => esc_html__( 'fadeInLeftBig', 'inbound' ),
				'fadeInRight'        => esc_html__( 'fadeInRight', 'inbound' ),
				'fadeInRightBig'     => esc_html__( 'fadeInRightBig', 'inbound' ),
				'fadeInUp'           => esc_html__( 'fadeInUp', 'inbound' ),
				'fadeInUpBig'        => esc_html__( 'fadeInUpBig', 'inbound' ),
				'fadeOut'            => esc_html__( 'fadeOut', 'inbound' ),
				'fadeOutDown'        => esc_html__( 'fadeOutDown', 'inbound' ),
				'fadeOutDownBig'     => esc_html__( 'fadeOutDownBig', 'inbound' ),
				'fadeOutLeft'        => esc_html__( 'fadeOutLeft', 'inbound' ),
				'fadeOutLeftBig'     => esc_html__( 'fadeOutLeftBig', 'inbound' ),
				'fadeOutRight'       => esc_html__( 'fadeOutRight', 'inbound' ),
				'fadeOutRightBig'    => esc_html__( 'fadeOutRightBig', 'inbound' ),
				'fadeOutUp'          => esc_html__( 'fadeOutUp', 'inbound' ),
				'fadeOutUpBig'       => esc_html__( 'fadeOutUpBig', 'inbound' ),
				'flipInX'            => esc_html__( 'flipInX', 'inbound' ),
				'flipInY'            => esc_html__( 'flipInY', 'inbound' ),
				'flipOutX'           => esc_html__( 'flipOutX', 'inbound' ),
				'flipOutY'           => esc_html__( 'flipOutY', 'inbound' ),
				'lightSpeedIn'       => esc_html__( 'lightSpeedIn', 'inbound' ),
				'lightSpeedOut'      => esc_html__( 'lightSpeedOut', 'inbound' ),
				'rotateIn'           => esc_html__( 'None', 'inbound' ),
				'rotateInDownLeft'   => esc_html__( 'rotateInDownLeft', 'inbound' ),
				'rotateInDownRight'  => esc_html__( 'rotateInDownRight', 'inbound' ),
				'rotateInUpLeft'     => esc_html__( 'rotateInUpLeft', 'inbound' ),
				'rotateInUpRight'    => esc_html__( 'rotateInUpRight', 'inbound' ),
				'rotateOut'          => esc_html__( 'rotateOut', 'inbound' ),
				'rotateOutDownLeft'  => esc_html__( 'rotateOutDownLeft', 'inbound' ),
				'rotateOutDownRight' => esc_html__( 'rotateOutDownRight', 'inbound' ),
				'rotateOutUpLeft'    => esc_html__( 'rotateOutUpLeft', 'inbound' ),
				'rotateOutUpRight'   => esc_html__( 'rotateOutUpRight', 'inbound' ),
				'hinge'              => esc_html__( 'hinge', 'inbound' ),
				'rollIn'             => esc_html__( 'rollIn', 'inbound' ),
				'rollOut'            => esc_html__( 'rollOut', 'inbound' ),
				'zoomIn'             => esc_html__( 'zoomIn', 'inbound' ),
				'zoomInDown'         => esc_html__( 'zoomInDown', 'inbound' ),
				'zoomInLeft'         => esc_html__( 'zoomInLeft', 'inbound' ),
				'zoomInRight'        => esc_html__( 'zoomInRight', 'inbound' ),
				'zoomInUp'           => esc_html__( 'zoomInUp', 'inbound' ),
				'zoomOut'            => esc_html__( 'zoomOut', 'inbound' ),
				'zoomOutDown'        => esc_html__( 'zoomOutDown', 'inbound' ),
				'zoomOutLeft'        => esc_html__( 'zoomOutLeft', 'inbound' ),
				'zoomOutRight'       => esc_html__( 'zoomOutRight', 'inbound' ),
				'zoomOutUp'          => esc_html__( 'zoomOutUp', 'inbound' ),
				'slideInDown'        => esc_html__( 'slideInDown', 'inbound' ),
				'slideInLeft'        => esc_html__( 'slideInLeft', 'inbound' ),
				'slideInRight'       => esc_html__( 'slideInRight', 'inbound' ),
				'slideInUp'          => esc_html__( 'slideInUp', 'inbound' ),
				'slideOutDown'       => esc_html__( 'slideOutDown', 'inbound' ),
				'slideOutLeft'       => esc_html__( 'slideOutLeft', 'inbound' ),
				'slideOutRight'      => esc_html__( 'slideOutRight', 'inbound' ),
				'slideOutUp'         => esc_html__( 'slideOutUp', 'inbound' ),
			),
			'priority' => 1
		);

		$fields['animation_duration'] = array(
			'name'        => esc_html__( 'Duration', 'inbound' ),
			'type'        => 'text',
			'group'       => 'animation',
			'description' => esc_html__( 'Duration of animation in milliseconds (e.g. 1000).', 'inbound' ),
			'priority'    => 5,
		);

		$fields['animation_delay'] = array(
			'name'        => esc_html__( 'Delay', 'inbound' ),
			'type'        => 'text',
			'group'       => 'animation',
			'description' => esc_html__( 'Delay before the animation starts in milliseconds (e.g. 1000).', 'inbound' ),
			'priority'    => 5,
		);

	}

	return $fields;

}
add_filter('siteorigin_panels_widget_style_fields', 'inbound_panels_widget_style_fields');


function inbound_panels_row_style_attributes($attr, $style) {
	if ( ! empty( $style['top_border'] ) ) {
		$attr['style'] .= 'border-top: 1px solid ' . $style['top_border'] . '; ';
	}
	if ( ! empty( $style['bottom_border'] ) ) {
		$attr['style'] .= 'border-bottom: 1px solid ' . $style['bottom_border'] . '; ';
	}

	// row bottom margin
	$margin_bottom = 0;
	if ( ! empty( $style['bottom_margin'] ) ) {
		$margin_bottom = esc_attr( $style['bottom_margin'] );
		$attr['style'] .= 'margin-bottom: ' . $margin_bottom . '; ';
	} else {
		$margin_bottom = inbound_option( 'pagebuilder_row_margin_bottom', '0' );
		if ( $margin_bottom != 0 ) {
			$margin_bottom .= 'px';
		}
		$attr['style'] .= 'margin-bottom: ' . $margin_bottom . '; ';
	}

	// row padding
	$pad_left   = 0;
	$pad_right  = 0;
	$pad_top    = 0;
	$pad_bottom = 0;

	if ( ! empty( $style['padding_top'] ) ) {
		$pad_top = esc_attr( $style['padding_top'] );
	} else {
		$pad_top = inbound_option( 'pagebuilder_row_padding_top', '0' );
		if ( $pad_top != 0 ) {
			$pad_top .= 'px';
		}
	}

	if ( ! empty( $style['padding_bottom'] ) ) {
		$pad_bottom = esc_attr( $style['padding_bottom'] );
	} else {
		$pad_bottom = inbound_option( 'pagebuilder_row_padding_bottom', '0' );
		if ( $pad_bottom != 0 ) {
			$pad_bottom .= 'px';
		}
	}

	if ( ! empty( $style['padding_left'] ) ) {
		$pad_left = esc_attr( $style['padding_left'] );
	} else {
		$pad_left = inbound_option( 'pagebuilder_row_padding_left', '0' );
		if ( $pad_left != 0 ) {
			$pad_left .= 'px';
		}
	}

	if ( ! empty( $style['padding_right'] ) ) {
		$pad_right = esc_attr( $style['padding_right'] );
	} else {
		$pad_right = inbound_option( 'pagebuilder_row_padding_right', '0' );
		if ( $pad_right != 0 ) {
			$pad_right .= 'px';
		}
	}

	$attr['style'] .= 'padding: ' . $pad_top . " " . $pad_right . " " . $pad_bottom . " " . $pad_left . '; ';

	// row justification
	if ( ! empty( $style['row_justification'] ) ) {
		if ( $style['row_justification'] == 'left' ) {
			$attr['class'][] = 'row-justification-left';
		} elseif ( $style['row_justification'] == 'center' ) {
			$attr['class'][] = 'row-justification-center';
		} elseif ( $style['row_justification'] == 'right' ) {
			$attr['class'][] = 'row-justification-right';
		}
	} else {
		$attr['class'][] = 'row-justification-left';
	}

	// flexbox model
	if ( ! empty( $style['row_flexbox'] ) ) {
		$attr['class'][] = 'inbound-flexbox';
	}

	//  background image options
	if( !empty( $style['background_image_attachment'] ) && !empty( $style['background_display']  ) ) {
		switch( $style['background_display'] ) {
			case 'inbound-parallax':
				$attr['style'] .= 'background-size: cover;';
				$attr['class'][] = 'row-parallax';
				$attr['data-top'] = 'background-position:0px 0%;';
				$attr['data-300-bottom'] = 'background-position:0px 100%;';
				break;
			case 'fixed':
				$attr['style'] .= 'background-attachment: fixed; background-size: cover; background-position:center;';
				break;
		}
	}

	// animations
	if ( ! defined ( 'INBOUND_DISABLE_ANIMATIONS' )) {
		if ( ! empty( $style['animation_type'] ) ) {
			// type
			$animation_type = $style['animation_type'];

			// duration
			if ( empty( $style['animation_duration'] ) ) {
				$animation_duration = 1000;
			} else {
				$animation_duration = intval( $style['animation_duration'] );
			}

			// delay
			if ( empty( $style['animation_delay'] ) ) {
				$animation_delay = 0;
			} else {
				$animation_delay = intval( $style['animation_delay'] );
			}

			$attr['data-wow-duration'] = $animation_duration . 'ms';
			$attr['data-wow-delay']    = $animation_delay . 'ms';
			$attr['class'][]           = 'inbound-wow';
			$attr['class'][]           = esc_attr( $animation_type );

		} // animation_type set
	}

	if(empty($attr['style'])) unset($attr['style']);
	return $attr;
}
add_filter('siteorigin_panels_row_style_attributes', 'inbound_panels_row_style_attributes', 10, 2);


add_filter('siteorigin_panels_row_attributes', 'inbound_panels_row_attributes', 10, 2);
function inbound_panels_row_attributes($attrs, $options) {
	if ( isset($options['style']) && isset($options['style']['hide_on_mobile']) && $options['style']['hide_on_mobile'] == 1) {
		if ( isset($attrs['class']) ) {
			$attrs['class'] .= " hide-on-mobile";
		} else {
			$attrs['class'] = "hide-on-mobile";
		}
	}
	if ( isset($options['style']) && isset($options['style']['hide_on_tablet']) && $options['style']['hide_on_tablet'] == 1) {
		if ( isset($attrs['class']) ) {
			$attrs['class'] .= " hide-on-tablet";
		} else {
			$attrs['class'] = "hide-on-tablet";
		}
	}
	if ( isset($options['style']) && isset($options['style']['hide_on_desktop']) && $options['style']['hide_on_desktop'] == 1) {
		if ( isset($attrs['class']) ) {
			$attrs['class'] .= " hide-on-desktop";
		} else {
			$attrs['class'] = "hide-on-desktop";
		}
	}

	return $attrs;
}

function inbound_panels_widget_style_attributes($attr, $style) {

	// padding
	$pad_left = 0;
	$pad_right = 0;
	$pad_top = 0;
	$pad_bottom = 0;
	if(!empty($style['padding_top']))
		$pad_top = esc_attr($style['padding_top']);
	if(!empty($style['padding_bottom']))
		$pad_bottom = esc_attr($style['padding_bottom']);
	if(!empty($style['padding_left']))
		$pad_left = esc_attr($style['padding_left']);
	if(!empty($style['padding_right']))
		$pad_right = esc_attr($style['padding_right']);

	if ( $pad_left != 0 || $pad_right != 0 || $pad_top != 0 || $pad_bottom != 0) {
		$attr['style'] .= 'padding: ' . $pad_top . " " . $pad_right . " " . $pad_bottom . " " . $pad_left . '; ';
	}

	// font color for widget
	if(!empty($style['font_color'])) {
		$wid = "ws-" . inbound_panels_get_widget_id();
		$attr['id'] = $wid;
		$widget_style = '#wid p, #wid blockquote, #wid q, #wid span, #wid h1, #wid h2, #wid h3, #wid h4, #wid h5, #wid h6, #wid .testimonials footer, #wid .testimonial footer, .social-widget a { color:' .  $style['font_color'] . ' !important; }';
		$widget_style = str_replace("#wid", '#' . $wid, $widget_style );
		inbound_add_custom_style('panel-widget', $widget_style);
	}

	// text alignment
	if(!empty($style['text_alignment'])) {
		if ( $style['text_alignment'] == 'left' ) {
			$attr['class'][] = 'text-align-left';
		} elseif ( $style['text_alignment'] == 'center' ) {
			$attr['class'][] = 'text-align-center';
		} elseif ( $style['text_alignment'] == 'right' ) {
			$attr['class'][] = 'text-align-right';
		}
	} else {
		// We need nothing done here, the row default will be applied.
		//$attr['class'][] = 'text-align-default';
	}

	if (isset($style['hide_on_mobile']) && $style['hide_on_mobile'] == 1) $attr['class'][] = "hide-on-mobile";
	if (isset($style['hide_on_tablet']) && $style['hide_on_tablet'] == 1) $attr['class'][] = "hide-on-tablet";
	if (isset($style['hide_on_desktop']) && $style['hide_on_desktop'] == 1) $attr['class'][] = "hide-on-desktop";


	// animations
	if ( ! defined ( 'INBOUND_DISABLE_ANIMATIONS' )) {
		if ( ! empty( $style['animation_type'] ) ) {
			// type
			$animation_type = $style['animation_type'];

			// duration
			if ( empty( $style['animation_duration'] ) ) {
				$animation_duration = 1000;
			} else {
				$animation_duration = intval( $style['animation_duration'] );
			}

			// delay
			if ( empty( $style['animation_delay'] ) ) {
				$animation_delay = 0;
			} else {
				$animation_delay = intval( $style['animation_delay'] );
			}

			$attr['data-wow-duration'] = $animation_duration . 'ms';
			$attr['data-wow-delay']    = $animation_delay . 'ms';
			$attr['class'][]           = 'inbound-wow';
			$attr['class'][]           = esc_attr( $animation_type );

		} // animation_type set
	}

	return $attr;
}
add_filter('siteorigin_panels_widget_style_attributes', 'inbound_panels_widget_style_attributes', 10, 2);


function inbound_panels_get_widget_id() {
	global $inbound_last_widget_id;
	if (empty($inbound_last_widget_id)) $inbound_last_widget_id = 0;
	$inbound_last_widget_id++;
	return $inbound_last_widget_id;
}

function inbound_panels_widget_style_groups( $groups ) {
	if (isset($groups['theme'])) unset($groups['theme']);

	if ( ! defined ( 'INBOUND_DISABLE_ANIMATIONS' )) {
		$groups['animation'] = array(
			'name'     => esc_html__( 'Animation', 'inbound' ),
			'priority' => 20
		);
	}

	$groups['responsiveness'] = array(
		'name' => esc_html__('Responsiveness', 'inbound'),
		'priority' => 25
	);

	return $groups;
}
add_filter('siteorigin_panels_widget_style_groups', 'inbound_panels_widget_style_groups');

function inbound_panels_row_style_groups( $groups ) {
	if (isset($groups['theme'])) unset($groups['theme']);

	if ( ! defined ( 'INBOUND_DISABLE_ANIMATIONS' )) {
		$groups['animation'] = array(
			'name'     => esc_html__( 'Animation', 'inbound' ),
			'priority' => 20
		);
	}

	$groups['responsiveness'] = array(
		'name' => esc_html__('Responsiveness', 'inbound'),
		'priority' => 25
	);

	return $groups;
}
add_filter('siteorigin_panels_row_style_groups', 'inbound_panels_row_style_groups');


function inbound_panels_widget_tabs($tabs) {
	$inbound_tabs[] = array(
			'title' => esc_html__('Inbound', 'inbound'),
			'filter' => array(
				'groups' => array('inbound-widgets')
			)
		);

	array_splice($tabs, 1, 0, $inbound_tabs);
	$tabs = array_values($tabs);
	return $tabs;
}
add_filter('siteorigin_panels_widget_dialog_tabs', 'inbound_panels_widget_tabs', 20);


function inbound_panels_add_recommended_widgets($widgets){
	global $inbound_custom_widgets;

	// Inbound Widgets
	foreach($inbound_custom_widgets as $slug => $widget) {
		if( isset( $widgets[$widget] ) ) {
			$icon = strtolower(str_replace('_', '-', $widget));
			$widgets[$widget]['groups'] = array('inbound-widgets');
			$widgets[$widget]['icon'] = 'dashicons ' . $icon;
		}
	}


	if (function_exists('is_woocommerce')) {
		// WooCommerce Widgets
		$woocommerce_widgets = array(
			'WC_Widget_Cart',
			'WC_Widget_Layered_Nav_Filters',
			'WC_Widget_Layered_Nav',
			'WC_Widget_Price_Filter',
			'WC_Widget_Product_Categories',
			'WC_Widget_Product_Search',
			'WC_Widget_Product_Tag_Cloud',
			'WC_Widget_Products',
			'WC_Widget_Recent_Reviews',
			'WC_Widget_Recently_Viewed',
			'WC_Widget_Top_Rated_Products'
		);
		foreach ($woocommerce_widgets as $widget) {
			if (class_exists($widget)) {
				$widgets[$widget]['icon'] = 'woocommerce-icon';
			}
		}
	}

	return $widgets;
}
add_filter('siteorigin_panels_widgets', 'inbound_panels_add_recommended_widgets');


function inbound_panels_options_admin_menu() {
	remove_submenu_page('options-general.php', 'siteorigin_panels');
}
add_action( 'admin_menu', 'inbound_panels_options_admin_menu', 99);


function inbound_panels_widget_classes ( $classes, $widget, $instance ) {
	$x=0;
	foreach ($classes as $class) {
		$classes[$x] = str_replace("widget_inbound", "inbound", $class);
		$x++;
	}
	return $classes;
}
add_filter('siteorigin_panels_widget_classes', 'inbound_panels_widget_classes', 10, 3);




add_filter( 'body_class', 'inbound_panels_add_body_classes' );
function inbound_panels_add_body_classes( $classes ) {
	if ( inbound_is_panel() && ! get_post_meta( get_the_ID(), 'sr_inbound_bypass_page_builder', false ) ) {
		$classes[] = 'inbound-panels';
	} else {
		$classes[] = 'inbound-no-panels';
	}
	return $classes;
}



function inbound_panels_filter_content( $content ) {
	if ( !inbound_is_woocommerce() && ( is_single() || is_page() ) && inbound_is_panel() && !get_post_meta( get_the_ID(), 'sr_inbound_bypass_page_builder', false ) )
		$panel_content = inbound_option('post_content', '');
	else
		$panel_content = $content;

	return $panel_content;
}

function inbound_pre_the_content () {
	global $post;
	$content = '';

	if ( siteorigin_panels_is_panel() ) {
		if ( post_password_required() )
		{
			$content = get_the_password_form();
		} else {
			$panel_content = siteorigin_panels_render( $post->ID );
		}
		if ( !empty( $panel_content ) ) $content = $panel_content;
	}
	inbound_set_option('post_content', $content);
}

if (!is_admin() && function_exists('siteorigin_panels_render')) {
	remove_filter( 'the_content', 'siteorigin_panels_filter_content' );
	add_action( 'wp', 'inbound_pre_the_content', 9999 );
	add_filter( 'the_content', 'inbound_panels_filter_content' );
}



function inbound_panels_enqueue_styling_script() {
	wp_enqueue_script('siteorigin-panels-front-styles');

	if ( is_category() || is_archive() ) {
		wp_enqueue_style('siteorigin-panels-front');
	}
}
add_action('wp_enqueue_scripts', 'inbound_panels_enqueue_styling_script', 99);

function inbound_is_panel() {
	return get_post_meta(get_the_ID(), 'panels_data', false);
}

function inbound_panels_disable_feature () {
	return false;
}
add_filter( 'siteorigin_panels_learn', 'inbound_panels_disable_feature', 999 );
add_filter( 'siteorigin_premium_upgrade_teaser', 'inbound_panels_disable_feature', 999 );
