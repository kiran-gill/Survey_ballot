<?php
/**
 * Plugin Name: Inbound Contact Form
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a contact form
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'WPCF7_ContactForm' ) ) // this widget is only displayed if Contact Form 7 is installed and active.
{
	return;
}

if ( ! class_exists( 'Inbound_Contact_Widget' ) ) {
	class Inbound_Contact_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Contact Form 7', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a contact form.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-contact-form-7' )

			);

			// fields array
			$args['fields'] = array(

				// Title field
				array(
					'name'     => esc_html__( 'Title', 'inbound' ),
					'desc'     => esc_html__( 'Enter the widget title.', 'inbound' ),
					'id'       => 'title',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => esc_html__( 'We will call you back', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => 'strip_tags|esc_attr'
				),
				array(
					'name'      => esc_html__( 'Form', 'inbound' ),
					'desc'      => esc_html__( 'Select the form to be displayed. You can create new forms in Contact Form 7.', 'inbound' ),
					'id'        => 'form',
					'type'      => 'posts',
					'post_type' => 'wpcf7_contact_form',
					'multiple'  => false,
					'std'       => '',
					'validate'  => 'alpha_dash',
					'filter'    => ''
				),
				array(
					'name'     => esc_html__( 'Display as', 'inbound' ),
					'desc'     => esc_html__( 'Select whether you would like form fields to be displayed in rows (stacked) or columns (next to each other). If you opt to display form fields in columns, make sure that all fields fit into the widget container.', 'inbound' ),
					'id'       => 'display_as',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Rows', 'inbound' ),
							'value' => 'rows'
						),
						array(
							'name'  => esc_html__( 'Columns', 'inbound' ),
							'value' => 'columns'
						)
					),
					'std'      => 'rows',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Field Alignment', 'inbound' ),
					'desc'     => esc_html__( 'Select how to align the form fields within the widget container.', 'inbound' ),
					'id'       => 'field_alignment',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Left', 'inbound' ),
							'value' => 'left'
						),
						array(
							'name'  => esc_html__( 'Center', 'inbound' ),
							'value' => 'center'
						),
						array(
							'name'  => esc_html__( 'Right', 'inbound' ),
							'value' => 'right'
						)
					),
					'std'      => 'left',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Rounded Corners', 'inbound' ),
					'desc'     => esc_html__( 'Define border radius for form fields in px.', 'inbound' ),
					'id'       => 'border_radius',
					'type'     => 'number',
					'class'    => 'widefat',
					'units'    => array('px'),
					'std'      => 0,
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Hint', 'inbound' ),
					'desc'     => esc_html__( 'Enter a hint to be displayed below the opt-in form.', 'inbound' ),
					'id'       => 'hint',
					'type'     => 'textarea',
					'rows'     => '3',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => 'esc_textarea'
				),
			); // fields array

			$this->create_widget( $args );
		}


		// Output function
		function widget( $args, $instance ) {

			$form = inbound_array_option( 'form', $instance, false );

			$id = 'widget-form' . '-' . $form;


			$wid    = inbound_get_widget_uid( 'cf7' );

			$radius  = inbound_array_option( 'border_radius', $instance, 0);
			if ( $radius && is_array ( $radius ) && ! empty ( $radius ) ) {
				$radius = $radius['number'] . 'px';
			} else {
				$radius = 0;
			}



			if ( $radius > 0 ) {
				$style = '.' . $wid . ' input, .' . $wid . ' textarea  { border-radius:' . $radius . '; }';
				inbound_add_custom_style( 'cf7', $style );
			}


			$out = $args['before_widget'];

			if ( inbound_array_option( 'title', $instance, false ) && ! inbound_is_pagebuilder() ) {
				$out .= $args['before_title'];
				$out .= esc_html( $instance['title'] );
				$out .= $args['after_title'];
			}

			if ( $form && $form != 0 ) {
				$display_as = inbound_array_option( 'display_as', $instance, "rows" );
				$alignment  = inbound_array_option( 'field_alignment', $instance, "left" );

				$out .= '<div class="form-cf7 ' . esc_attr( $wid ) . ' cf7-' . esc_attr( $display_as ) . ' cf7-' . esc_attr( $alignment ) . '">';
				$out .= do_shortcode( '[contact-form-7 id="' . $form . '"]' );

				$hint = inbound_array_option( 'hint', $instance, '' );
				if ( ! empty( $hint ) ) {
					$out .= '<p class="hint">' . inbound_esc_html( $hint ) . '</p>';
				}

				$out .= '</div>';
			}

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_contact_widget' ) ) {
		function register_inbound_contact_widget() {
			register_widget( 'Inbound_Contact_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_contact_widget', 1 );
	}
}