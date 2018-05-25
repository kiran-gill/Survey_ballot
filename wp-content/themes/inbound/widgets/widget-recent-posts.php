<?php
/**
 * Plugin Name: Inbound Recent Posts
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays a progress bar.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Recent_Posts_Widget' ) ) {
	class Inbound_Recent_Posts_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Recent Posts', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays the most recent posts.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-recent-posts' )

			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(
				array(
					'name'     => esc_html__( 'Title', 'inbound' ),
					'desc'     => esc_html__( 'Enter the widget title.', 'inbound' ),
					'id'       => 'title',
					'type'     => 'text',
					'class'    => 'widefat',
					'std'      => esc_html__( 'Recent Posts', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Amount of posts to show', 'inbound' ),
					'desc'     => esc_html__( 'Enter an amount of posts you would like to display.', 'inbound' ),
					'id'       => 'amount',
					'type'     => 'text',
					'class'    => '',
					'std'      => '5',
					'validate' => 'numeric',
					'filter'   => ''
				),

			); // fields array

			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {
			$id = inbound_get_widget_uid( 'recent-posts' );

			$out = $args['before_widget'];

			if ( ! inbound_is_pagebuilder( $args ) ) {
				$out .= $args['before_title'] . inbound_array_option( "title", $instance ) . $args['after_title'];
			}

			if ( ! $amount = absint( inbound_array_option( 'amount', $instance ) ) ) {
				$amount = 5;
			}

			$qargs = array(
				'showposts' => $amount
			);

			$inbound_recent_posts_q = null;
			$inbound_recent_posts_q = new WP_Query( $qargs );

			$out .= "<ul>\n";

			while ( $inbound_recent_posts_q->have_posts() ) {
				$inbound_recent_posts_q->the_post();

				$format = get_post_format();
				if ( false === $format ) {
					$format = 'default';
				}

				$out .= '<li class="format-' . $format . '"><a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '" rel="bookmark">';

				if ( has_post_thumbnail() ) {
					$out .= get_the_post_thumbnail( get_the_ID(), 'inbound-post-thumbnail-small' );
				}

				$out .= '<p>' . esc_html( get_the_title() ) . '<span class="widget_date">' . get_the_time( get_option( 'date_format' ) ) . '</span></p>';

				$out .= '</a></li>';

			}

			wp_reset_query();

			$out .= "</ul>\n";

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_recent_posts_widget' ) ) {
		function register_inbound_recent_posts_widget() {
			register_widget( 'Inbound_Recent_Posts_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_recent_posts_widget', 1 );
	}
}