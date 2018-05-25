<?php
/**
 * Plugin Name: Inbound Blog Posts
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays the latest blog posts
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Blog_Posts_Widget' ) ) {
	class Inbound_Blog_Posts_Widget extends SR_Widget {

		function __construct() {

			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Blog Posts', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays the latest blog posts.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-blog-posts' )
			);

			// Configure the widget fields

			// fields array
			$args['fields'] = array(
				array(
					'name'     => esc_html__( 'Title', 'inbound' ),
					'desc'     => esc_html__( 'Enter the widget title.', 'inbound' ),
					'id'       => 'title',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => esc_html__( 'Latest News', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'      => esc_html__('Categories', 'inbound'),
					'type'      => 'categories',
					'id'        => 'categories',
					'taxonomy'  => 'category',
					'post_type' => 'posts',
					'multiple'  => true,
					'validate'  => null,
					'filter'    => ''
				),
				array(
					'name'     => esc_html__( 'Amount of posts to display', 'inbound' ),
					'desc'     => esc_html__( 'Select how many blog posts you would like this widget to display.', 'inbound' ),
					'id'       => 'amount',
					'type'     => 'number',
					'units'    => array('posts'),
					'std'      => 15,
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				),
				array(
					'name'           => esc_html__( 'Layout', 'inbound' ),
					'desc'           => esc_html__( 'Select a layout to be used for this block.', 'inbound' ),
					'id'             => 'layout',
					'group-selector' => true,
					'type'           => 'select',
					'fields'         => array(
						array(
							'name'  => esc_html__( 'List (Large)', 'inbound' ),
							'value' => 'list'
						),
						array(
							'name'  => esc_html__( 'List (Medium)', 'inbound' ),
							'value' => 'medium'
						),
						array(
							'name'  => esc_html__( 'Grid', 'inbound' ),
							'value' => 'grid'
						),
						array(
							'name'  => esc_html__( 'Masonry', 'inbound' ),
							'value' => 'masonry'
						),
						array(
							'name'  => esc_html__( 'Timeline', 'inbound' ),
							'value' => 'timeline'
						),
						array(
							'name'  => esc_html__( 'Minimal', 'inbound' ),
							'value' => 'minimal'
						),
					),
					'std'            => 'list',
					'validate'       => 'alpha_dash',
					'filter'         => ''
				),
				array(
					'name'        => esc_html__( 'Group by', 'inbound' ),
					'desc'        => esc_html__( 'Select how you would like to group posts in the timeline view.', 'inbound' ),
					'id'          => 'group_by',
					'is-group'    => 'layout',
					'group-value' => array( 'timeline' ),
					'type'        => 'select',
					'fields'      => array(
						array(
							'name'  => esc_html__( 'Day', 'inbound' ),
							'value' => 'day'
						),
						array(
							'name'  => esc_html__( 'Month', 'inbound' ),
							'value' => 'month'
						),
						array(
							'name'  => esc_html__( 'Year', 'inbound' ),
							'value' => 'year'
						),
					),
					'std'         => 'day',
					'validate'    => 'alpha_dash',
					'filter'      => ''
				),
				array(
					'name'        => esc_html__( 'Columns', 'inbound' ),
					'desc'        => esc_html__( 'Select how many columns you would like to have for views that support columns.', 'inbound' ),
					'id'          => 'columns',
					'is-group'    => 'layout',
					'group-value' => array( 'grid', 'masonry' ),
					'type'        => 'select',
					'fields'      => array(
						array(
							'name'  => '2',
							'value' => '2'
						),
						array(
							'name'  => '3',
							'value' => '3'
						),
						array(
							'name'  => '4',
							'value' => '4'
						),
						array(
							'name'  => '5',
							'value' => '5'
						),
					),
					'std'         => '3',
					'validate'    => 'numeric',
					'filter'      => ''
				),
				array(
					'name'     => esc_html__( 'Excerpt length', 'inbound' ),
					'desc'     => esc_html__( 'Define how many words you would like to display in the excerpt.', 'inbound' ),
					'id'       => 'excerpt_length',
					'type'     => 'number',
					'units'    => array('words'),
					'std'      => 25,
					'validate' => 'numeric_with_unit',
					'filter'   => ''
				)


			); // fields array

			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {
			global $inbound_widget_group_by,
			       $inbound_widget_cols_per_row,
			       $inbound_widget_excerpt_length;

			$id    = inbound_get_widget_uid( 'posts' );
			$cache = wp_cache_get( $id, 'widget' );

			$inbound_widget_group_by       = inbound_array_option( 'group_by', $instance, 'day' );
			$inbound_widget_cols_per_row   = inbound_array_option( 'columns', $instance, '3' );

			$inbound_widget_excerpt_length = inbound_array_option( 'excerpt_length', $instance, '25' );
			if ( !empty ($inbound_widget_excerpt_length['number']) ) {
				$inbound_widget_excerpt_length = $inbound_widget_excerpt_length['number'];
			} else {
				$inbound_widget_excerpt_length = 25;
			}

			$amount = inbound_array_option( 'amount', $instance, '10' );
			if ( !empty ($amount['number']) ) {
				$amount = $amount['number'];
			} else {
				$amount = 10;
			}

			$add_classes = "";
			$out         = $args['before_widget'];

			$out .= '<section>';

			$categories_tmp = inbound_array_option( 'categories', $instance );
			$categories     = array();
			if ( $categories_tmp ) {
				foreach ( $categories_tmp as $slug => $cat ) {
					$categories[] = $slug;
				}
			}

			$r = new WP_Query( apply_filters( 'widget_posts_args',
					array(
						'post_type'      => 'post',
						'posts_per_page' => $amount,
						'no_found_rows'  => true,
						'category_name'  => implode( ",", $categories ),
						'post_status'    => 'publish'
					)
				)
			);

			if ( $r->have_posts() ) {
				ob_start();
				$template = locate_template( 'templates/blog/widget/blog-' . inbound_array_option( 'layout', $instance, 'list' ) . '.php', false, false );
				inbound_include_file($template, array ( 'posts' => $r ) );
				/*
				if ( $template ) {
					get_template_part('templates/blog/widget/blog', inbound_array_option( 'layout', $instance, 'list' ) );
				} else {
					$out .= esc_html__( 'Template not available.', 'inbound' );
				}
				*/
				$out .= ob_get_contents();
				ob_end_clean();
			}

			wp_reset_postdata();

			$out .= '</section>';

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_blog_posts_widget' ) ) {
		function register_inbound_blog_posts_widget() {
			register_widget( 'Inbound_Blog_Posts_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_blog_posts_widget', 1 );
	}
}