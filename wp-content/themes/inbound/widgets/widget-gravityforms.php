<?php
/**
 * Plugin Name: Inbound Gravity Forms Form
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a form created with the Gravity Forms plug-in
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'GFCommon' ) ) // this widget is only displayed if Contact Form 7 is installed and active.
{
	return;
}

if ( ! class_exists( 'Inbound_Gravity_Forms_Widget' ) ) {
	class Inbound_Gravity_Forms_Widget extends SR_Widget {

		function __construct() {

			// configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Gravity Forms', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a Gravity Forms form.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-gravity-forms' )
			);

			// get forms
			$forms = array();
			$gforms = RGFormsModel::get_forms( null, 'title' );
			if ( !empty ($gforms ) ) {
				foreach ($gforms as $form) {
					$forms[] = array (
							'value' => $form->id,
							'name' => $form->title
					);
				}
			}

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
						'type'     => 'select',
						'fields'   => $forms,
						'std'       => '',
						'validate'  => 'alpha_dash',
						'filter'    => ''
				),
				array(
						'name'     => esc_html__( 'Display Title', 'inbound' ),
						'desc' => esc_html__( 'Display the Gravity Forms title.', 'inbound' ),
						'id'       => 'display_title',
						'type'     => 'checkbox',
						'class'    => 'widefat',
						'std'      => 0,
						'validate' => 'alpha_dash',
						'filter'   => ''
				),
				array(
						'name'     => esc_html__( 'Display Description', 'inbound' ),
						'desc' => esc_html__( 'Display the Gravity Forms description.', 'inbound' ),
						'id'       => 'display_description',
						'type'     => 'checkbox',
						'class'    => 'widefat',
						'std'      => 0,
						'validate' => 'alpha_dash',
						'filter'   => ''
				),
				array(
						'name'     => esc_html__( 'Use AJAX', 'inbound' ),
						'desc' => esc_html__( 'Submit forms via AJAX, without leaving the page.', 'inbound' ),
						'id'       => 'ajax',
						'type'     => 'checkbox',
						'class'    => 'widefat',
						'std'      => 0,
						'validate' => 'alpha_dash',
						'filter'   => ''
				),
			); // fields array

			$this->create_widget( $args );
		}


		// Output function
		function widget( $args, $instance ) {

			$form = inbound_array_option( 'form', $instance, false );

			$out = $args['before_widget'];

			if ( inbound_array_option( 'title', $instance, false ) && ! inbound_is_pagebuilder() ) {
				$out .= $args['before_title'];
				$out .= esc_html( $instance['title'] );
				$out .= $args['after_title'];
			}

			if ( $form && $form != 0 ) {

				if ( inbound_array_option( 'display_title', $instance, false ) )
					$display_title = ' title="true"';
				else
					$display_title = ' title="false"';

				if ( inbound_array_option( 'display_description', $instance, false ) )
					$display_description = ' description="true"';
				else
					$display_description = ' description="false"';

				if ( inbound_array_option( 'ajax', $instance, false ) )
					$ajax = ' ajax="true"';
				else
					$ajax = ' ajax="false"';

				$out .= do_shortcode( '[gravityform id="' . $form . '"' . $display_title . $display_description . $ajax . ']' );
			}

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_gravity_forms_widget' ) ) {
		function register_inbound_gravity_forms_widget() {
			register_widget( 'Inbound_Gravity_Forms_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_gravity_forms_widget', 1 );
	}
}