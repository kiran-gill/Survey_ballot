<?php
/**
 * Plugin Name: Inbound Event
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays an event block.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Event_Widget' ) ) {
	class Inbound_Event_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Event Block', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a block to advertise an upcoming event, including title, description, a link, date and time.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-event-block' )
			);

			// Tab groups
			$args['groups'] = array(
				'general' => esc_html__( 'General', 'inbound' ),
				'time'  => esc_html__( 'Time', 'inbound' ),
			);

			// Configure the widget fields
			// fields array
			$args['fields'] = array(

				// Title field
				array(
					'name'     => esc_html__( 'Title', 'inbound' ),
					'desc'     => esc_html__( 'Enter the event title here.', 'inbound' ),
					'id'       => 'title',
					'type'     => 'text',
					'group'    => 'general',
					'rows'     => '5',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Description', 'inbound' ),
					'desc'     => esc_html__( 'Enter a short description here.', 'inbound' ),
					'id'       => 'description',
					'type'     => 'textarea',
					'group'    => 'general',
					'rows'     => '5',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'  => esc_html__( 'Image', 'inbound' ),
					'desc'  => esc_html__( 'Upload an image to be displayed .', 'inbound' ),
					'class' => 'img',
					'id'    => 'image',
					'type'  => 'image',
					'group'    => 'general',
					'std'   => '',
					//'validate' => '',
					//'filter' => ''
				),
				array(
					'name'     => esc_html__( 'Speaker', 'inbound' ),
					'desc'     => esc_html__( 'Enter the name of a speaker here.', 'inbound' ),
					'id'       => 'speaker',
					'type'     => 'text',
					'group'    => 'general',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'  => esc_html__( 'Speaker Picture', 'inbound' ),
					'desc'  => esc_html__( 'Upload a speaker photo.', 'inbound' ),
					'class' => 'img',
					'id'    => 'speaker_picture',
					'type'  => 'image',
					'group'    => 'general',
					'std'   => '',
				),
				array(
					'name'     => esc_html__( 'Link URL', 'inbound' ),
					'desc'     => esc_html__( 'Define a link to point this button to.', 'inbound' ),
					'id'       => 'link',
					'type'     => 'text',
					'group'    => 'general',
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Start Date', 'inbound' ),
					'desc'     => esc_html__( 'Date, in any format.', 'inbound' ),
					'id'       => 'start_date',
					'type'     => 'text',
					'group'    => 'time',
					'class'    => 'short',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Start Time', 'inbound' ),
					'desc'     => esc_html__( 'Time, in any format.', 'inbound' ),
					'id'       => 'start_time',
					'type'     => 'text',
					'group'    => 'time',
					'class'    => 'short',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'End Date', 'inbound' ),
					'desc'     => esc_html__( 'Date, in any format.', 'inbound' ),
					'id'       => 'end_date',
					'type'     => 'text',
					'group'    => 'time',
					'class'    => 'short',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'End Time', 'inbound' ),
					'desc'     => esc_html__( 'Time, in any format.', 'inbound' ),
					'id'       => 'end_time',
					'type'     => 'text',
					'group'    => 'time',
					'class'    => 'short',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),

			); // fields array

			$this->create_widget( $args );
		}


		// Output function
		function widget( $args, $instance ) {

			$out = $args['before_widget'];

			$image         = inbound_array_option( 'image', $instance, false );
			$title         = inbound_array_option( 'title', $instance, false );
			$description   = inbound_array_option( 'description', $instance, false );

			$start_date    = inbound_array_option( 'start_date', $instance, false );
			$start_time    = inbound_array_option( 'start_time', $instance, false );

			$end_date      = inbound_array_option( 'end_date', $instance, false );
			$end_time      = inbound_array_option( 'end_time', $instance, false );

			$speaker       = inbound_array_option( 'speaker', $instance, false );
			$speaker_image = inbound_array_option( 'speaker_picture', $instance, false );

			$link          = inbound_array_option( 'link', $instance, false );
			$link_text     = inbound_array_option( 'link_text', $instance, false );

			if ( !empty ( $link ) ) {
				$out .= '<a href="' . esc_url( $link ) . '">';
			}


			$out .= '<div class="inbound-event">';


			if ( $image ) {
				$image = intval( $image );
				if ( $image != 0 ) {
					$image_array = wp_get_attachment_image_src( $image, 'event-image' );
					if ( $image_array ) {
						$out .= '<div class="event-image"><img src="' . esc_url( $image_array[0] ) . '" class="event-image" alt="' . esc_attr( inbound_array_option( 'title', $instance, '' ) ) . '"></div>';
					}
				}
			}

			$out .= '<div class="event-content">';

			if ( $speaker_image ) {
				$image = intval( $speaker_image );
				if ( $image != 0 ) {
					$image_array = wp_get_attachment_image_src( $image, 'event-speaker-image' );
					if ( $image_array ) {
						$out .= '<img src="' . esc_url( $image_array[0] ) . '" class="event-speaker-image" alt="' . esc_attr( $speaker ) . '">';
					}
				}
			}

			if ( !empty ( $speaker ) ) {
				$out .= '<p class="event-speaker">' . esc_html( $speaker ) . '</p>';
			}

			if ( !empty ( $title ) ) {
				$out .= '<h3>' . esc_html( $title ) . '</h3>';
			}

			if ( !empty ( $description ) ) {
				$out .= '<p class="event-description">' . esc_html( $description ) . '</p>';
			}

			if ( !empty ( $start_date ) ) {

				$out .= '<p class="event-date">';
				$out .= esc_html( $start_date );
				if ( !empty ( $start_time ) ) {
					$out .= ' ' . esc_html( $start_time );
				}

				if (  !empty ( $start_date ) && !empty ( $start_time ) ) {
					$out .= ' &mdash; ';
				}

				if ( !empty ( $end_date ) ) {
					if ( $start_date != $end_date ) {
						$out .= ' ' . esc_html( $end_date );
					}
				}

				if ( !empty ( $end_time ) ) {
					$out .= ' ' . esc_html( $end_time );
				}

				$out .= '</p>';
			}


			$out .= '</div>';

			$out .= '</div>';

			if ( !empty ( $link ) ) {
				$out .= '</a>';
			}


			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_event_widget' ) ) {
		function register_inbound_event_widget() {
			register_widget( 'Inbound_Event_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_event_widget', 1 );
	}
}