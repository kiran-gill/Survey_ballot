<?php
/**
 * Plugin Name: Inbound Icon List
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays an unordered list with an icon for the list elements
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Icon_List_Widget' ) ) {
	class Inbound_Icon_List_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Icon List', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays an unordered list with an icon for each list element.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-icon-list' )
			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(
				array(
					'name'     => esc_html__( 'Icon', 'inbound' ),
					'desc'     => esc_html__( 'Select an icon to be used for all elements in this list.', 'inbound' ),
					'id'       => 'icon',
					'type'     => 'fontawesome',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Icon Color', 'inbound' ),
					'desc'     => esc_html__( 'Select a list icon color.', 'inbound' ),
					'id'       => 'icon_color',
					'type'     => 'color',
					// class, rows, cols
					'std'      => '#000000',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'List Items', 'inbound' ),
					'desc'     => esc_html__( 'One item per row.', 'inbound' ),
					'id'       => 'items',
					'type'     => 'textarea',
					'rows'     => '10',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),

			); // fields array

			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {
			$is_pagebuilder = inbound_is_pagebuilder( $args );

			$id    = inbound_get_widget_uid( 'icon-list' );
			$style = '.' . $id . ' li i.fa { color:' . inbound_array_option( 'icon_color', $instance, '#dca400' ) . '; }';
			inbound_add_custom_style( 'icon-block', $style );


			$add_classes = "";
			$out         = $args['before_widget'];

			if ( isset( $instance['items'] ) ) {
				$out .= '<ul class="icon-list fa-ul ' . $id . '">';
				$items = explode( "\n", trim( $instance['items'] ) );
				if ( is_array( $items ) ) {
					foreach ( $items as $item ) {
						$out .= '<li><i class="fa-li fa ' . esc_attr( $instance['icon'] ) . '"></i>' . inbound_esc_html ( $item, false, true ) . '</li>';
					}
				}
				$out .= '</ul>';
			}

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_icon_list_widget' ) ) {
		function register_inbound_icon_list_widget() {
			register_widget( 'Inbound_Icon_List_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_icon_list_widget', 1 );
	}
}

