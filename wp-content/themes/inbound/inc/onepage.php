<?php
/*
 * One Page Menus Features
 */

function inbound_onepage_row_style_attributes($attr, $style) {
	if ( !empty ( $style['menu_slug'] ) ) {
		$id = sanitize_title ( $style['menu_slug'] );
		$attr['id'] = $id;
		$attr['class'][] = 'scroll-menu-anchor';
	}
	return $attr;
}
add_filter('siteorigin_panels_row_style_attributes', 'inbound_onepage_row_style_attributes', 10, 2);
add_filter('siteorigin_panels_widget_style_attributes', 'inbound_onepage_row_style_attributes', 10, 2);


function inbound_onepage_enqueue_scripts() {
	$theme_version = INBOUND_THEME_VERSION;
	wp_enqueue_script( 'inbound-pagescroll2id', get_template_directory_uri() . '/js/jquery.pagescroll2id.js', array( 'jquery' ), $theme_version, true );
}
add_action( 'wp_enqueue_scripts', 'inbound_onepage_enqueue_scripts' );



/*
 * Row Options
 */
function inbound_onepage_row_style_groups( $groups ) {
	$groups['menu_options'] = array(
		'name' => esc_html__('Menu Options', 'inbound'),
		'priority' => 6
	);
	return $groups;
}
add_filter('siteorigin_panels_row_style_groups', 'inbound_onepage_row_style_groups');


function inbound_onepage_row_style_fields($fields) {
	$fields['menu_slug'] = array(
		'name'        => esc_html__( 'Menu Slug', 'inbound' ),
		'type'        => 'text',
		'group'       => 'menu_options',
		'description' => esc_html__( 'This is the slug used to generate an ID to link to in a one-page menu. It needs to be unique among all page builder rows and widgets used, including the banner and must not collide with other CSS IDs used anywhere on the page.', 'inbound' ),
		'priority'    => 6,
	);
	return $fields;
}
add_filter('siteorigin_panels_row_style_fields', 'inbound_onepage_row_style_fields');

/*
 * Widget Options
 */
function inbound_onepage_widget_style_groups( $groups ) {
	$groups['menu_options'] = array(
		'name' => esc_html__('Menu Options', 'inbound'),
		'priority' => 8
	);
	return $groups;
}
add_filter('siteorigin_panels_widget_style_groups', 'inbound_onepage_widget_style_groups');


function inbound_onepage_widget_style_fields( $fields ) {
	$fields['menu_slug'] = array(
		'name'        => esc_html__( 'Menu Slug', 'inbound' ),
		'type'        => 'text',
		'group'       => 'menu_options',
		'description' => esc_html__( 'This is the slug used to generate an ID to link to in a one-page menu. It needs to be unique among all page builder rows and widgets used, including the banner and must not collide with other CSS IDs used anywhere on the page.', 'inbound' ),
		'priority'    => 6,
	);
	return $fields;
}
add_filter('siteorigin_panels_widget_style_fields', 'inbound_onepage_widget_style_fields');
