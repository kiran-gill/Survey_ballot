<?php
/**
 * Plugin Name: Inbound Opt-In Form
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays an opt-in form.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Optin_Widget' ) ) {
	class Inbound_Optin_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Opt-In Form', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays an opt-in form.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-opt-in-form' )
			);


			// Get available forms from options
			$forms      = array();
			$forms_temp = inbound_option( 'forms' );
			if ( is_array( $forms_temp ) && count( $forms_temp ) > 0 ) {
				foreach ( $forms_temp as $form ) {
					if ( $form['form_name'] == '' ) {
						$name = $form['form_uid'];
					} else {
						$name = $form['form_name'];
					}
					$forms[] = array(
						'name'  => $name,
						'value' => $form['form_uid']
					);
				}
			}

			// Configure the widget fields

			// fields array
			$args['fields'] = array(
				array(
					'type' => 'paragraph',
					'id'   => 'info_optin',
					'desc' => esc_html__( 'Forms are retrieved from Theme Options &#8594; Forms.', 'inbound' ),
				),
				array(
					'name'     => esc_html__( 'Title', 'inbound' ),
					'desc'     => esc_html__( 'Enter the headline title.', 'inbound' ),
					'id'       => 'title',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => esc_html__( 'Sign up now for updates', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Form', 'inbound' ),
					'desc'     => esc_html__( 'Select an opt-in form to use for this widget.', 'inbound' ),
					'id'       => 'form',
					'type'     => 'select',
					'fields'   => $forms,
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
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
					'units'    => array('px'),
					'class'    => 'widefat',
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
			$out = $args['before_widget'];

			$display_as = inbound_array_option( 'display_as', $instance, "rows" );
			$alignment  = inbound_array_option( 'field_alignment', $instance, "left" );

			$wid    = inbound_get_widget_uid( 'optin' );

			$radius  = inbound_array_option( 'border_radius', $instance, 0);
			if ( $radius && is_array ( $radius ) && ! empty ( $radius ) ) {
				$radius = $radius['number'] . 'px';
			} else {
				$radius = 0;
			}


			if ( $radius > 0 ) {
				$style = '.' . $wid . ' input, .' . $wid . ' textarea  { border-radius:' . $radius . '; }';
				inbound_add_custom_style( 'optin', $style );
			}


			$out .= '<div class="form-optin ' . $wid . ' optin-' . $display_as . ' optin-' . $alignment . '">';

			if ( inbound_array_option( 'title', $instance, false ) ) {
				$out .= $args['before_title'];
				$out .= esc_html( $instance['title'] );
				$out .= $args['after_title'];
			}

			$forms_temp = inbound_option( 'forms' );
			$code       = '';
			if ( is_array( $forms_temp ) && count( $forms_temp ) > 0 ) { // see if we can find a form that matches the selection
				foreach ( $forms_temp as $form ) {
					if ( $form['form_uid'] == inbound_array_option( 'form', $instance ) ) { // form found
						$code = $form['form_code'];
						break;
					}
				}

				if ( ! empty( $code ) ) { // if there is a form code to be rendered, execute shortcodes within form code
					$out .= inbound_esc_html( do_shortcode( stripslashes( $code ) ) );
				}
			}

			$hint = inbound_array_option( 'hint', $instance, '' );
			if ( ! empty( $hint ) ) {
				$out .= '<p class="hint">' . esc_html( $hint ) . '</p>';
			}


			$out .= '</div>';

			$out .= $args['after_widget'];
			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_optin_widget' ) ) {
		function register_inbound_optin_widget() {
			register_widget( 'Inbound_Optin_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_optin_widget', 1 );
	}
}