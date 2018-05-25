<?php
/**
 * Plugin Name: Inbound Payment Icons
 * Plugin URI: http://www.shapingrain.com
 * Description: Displays payment icons.
 * Version: 1.0
 * License: GPLv2
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! class_exists( 'Inbound_Payment_Icons_Widget' ) ) {
	class Inbound_Payment_Icons_Widget extends SR_Widget {

		public $icons;

		function __construct() {

			$this->icons = array(
				'2checkout'     => esc_html__( '2Checkout', 'inbound' ),
				'amazon'        => esc_html__( 'Amazon', 'inbound' ),
				'amex'          => esc_html__( 'American Express', 'inbound' ),
				'bitcoin'       => esc_html__( 'Bitcoin', 'inbound' ),
				'cirrus'        => esc_html__( 'Cirrus', 'inbound' ),
				'credit-card'   => esc_html__( 'Credit Card (Generic)', 'inbound' ),
				'discover'      => esc_html__( 'Discover', 'inbound' ),
				'ebay'          => esc_html__( 'eBay', 'inbound' ),
				'google-wallet' => esc_html__( 'Google Wallet', 'inbound' ),
				'maestro'       => esc_html__( 'Maestro', 'inbound' ),
				'mastercard'    => esc_html__( 'MasterCard', 'inbound' ),
				'paypal'        => esc_html__( 'PayPal', 'inbound' ),
				'skrill'        => esc_html__( 'Skrill', 'inbound' ),
				'solo'          => esc_html__( 'Solo', 'inbound' ),
				'square-up'     => esc_html__( 'Square', 'inbound' ),
				'visa'          => esc_html__( 'Visa', 'inbound' ),
				'wu'            => esc_html__( 'Western Union', 'inbound' ),
			);


			// Configure widget array
			$args = array(
				// Widget Backend label
				'label'       => esc_html__( 'INB Payment Icons', 'inbound' ),
				// Widget Backend Description
				'description' => esc_html__( 'Displays a set of payment icons.', 'inbound' ),
				'options' => array ( 'classname' => 'inbound-payment-icons' )
			);

			// Tab groups
			$args['groups'] = array(
				'general'  => esc_html__( 'General', 'inbound' ),
				'services' => esc_html__( 'Services', 'inbound' ),
			);


			// Configure the widget fields
			// fields array
			$args['fields'] = array(
				array(
					'name'     => esc_html__( 'Title', 'inbound' ),
					'desc'     => esc_html__( 'Enter the widget title.', 'inbound' ),
					'id'       => 'title',
					'group'    => 'general',
					'type'     => 'text',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => esc_html__( 'Accepted Payment Methods', 'inbound' ),
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Style', 'inbound' ),
					'desc'     => esc_html__( 'Select which style to use for the payment icons.', 'inbound' ),
					'id'       => 'style',
					'group'    => 'general',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Full Color', 'inbound' ),
							'value' => 'color'
						),
						array(
							'name'  => esc_html__( 'Greyscale', 'inbound' ),
							'value' => 'grey'
						),
					),
					'std'      => 'color',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Icon Size', 'inbound' ),
					'desc'     => esc_html__( 'Select which size to use for these payment icons.', 'inbound' ),
					'id'       => 'size',
					'group'    => 'general',
					'type'     => 'select',
					'fields'   => array(
						array(
							'name'  => esc_html__( 'Small', 'inbound' ),
							'value' => 'small'
						),
						array(
							'name'  => esc_html__( 'Medium', 'inbound' ),
							'value' => 'medium'
						),
						array(
							'name'  => esc_html__( 'Large', 'inbound' ),
							'value' => 'large'
						),
					),
					'std'      => 'medium',
					'validate' => 'alpha_dash',
					'filter'   => ''
				),
				array(
					'name'     => esc_html__( 'Hint or Notice', 'inbound' ),
					'desc'     => esc_html__( 'Optional hint to be displayed below the icons.', 'inbound' ),
					'id'       => 'hint',
					'group'    => 'general',
					'type'     => 'textarea',
					'rows'     => '3',
					// class, rows, cols
					'class'    => 'widefat',
					'std'      => '',
					'validate' => 'alpha_dash',
					'filter'   => 'esc_textarea'
				),
				array(
					'name'     => esc_html__( 'Payment Icons', 'inbound' ),
					'desc'     => esc_html__( 'Select payment icons to display.', 'inbound' ),
					'id'       => 'icons',
					'group'    => 'services',
					'type'     => 'checkbox_list',
					'sortable' => false,
					'fields'   => $this->icons,
					'std'      => array(),
					'validate' => null,
					'filter'   => ''
				),

			); // fields array

			$this->create_widget( $args );
		}

		// Output function
		function widget( $args, $instance ) {

			$add_classes = "";
			$out         = $args['before_widget'];

			$title = inbound_array_option( 'title', $instance, false );
			if ( $title && ! inbound_is_pagebuilder( $args ) ) {
				$out .= $args['before_title'];
				$out .= esc_html( $title );
				$out .= $args['after_title'];
			}


			$icons = $title = inbound_array_option( 'icons', $instance, false );
			$id    = inbound_get_widget_uid( 'payment-icons' );

			$style = inbound_array_option( 'style', $instance, 'color' );
			$size  = inbound_array_option( 'size', $instance, 'medium' );

			$suffix = '';
			if ( $style == 'grey' ) {
				$suffix = '_bw';
			}

			if ( is_array( $icons ) ) {
				$out .= '<ul id="' . $id . '" class="payment-icons-list payment-icons-size-' . $size . '">';

				foreach ( $icons as $icon ) {
					if ( ! empty( $this->icons[ $icon ] ) ) {
						$out .= '<li class="payment-' . $icon . '">';
						$out .= '<img src="' . esc_url( get_template_directory_uri() . '/images/payment/' . $icon . $suffix . '.png' ) . '" alt="' . esc_attr( $this->icons[ $icon ] ) . '" title="' . esc_attr( $this->icons[ $icon ] ) . '">';
						$out .= '</li>' . "\n";
					}
				}

				$out .= '</ul>';
			}

			$hint = inbound_array_option( 'hint', $instance, '' );
			if ( trim( $hint ) != "" ) {
				$out .= '<p class="hint">' . inbound_esc_html( $hint, false, true ) . '</p>';
			}

			$out .= $args['after_widget'];

			echo $out;
		}

	} // class

	// Register widget
	if ( ! function_exists( 'register_inbound_payment_icons_widget' ) ) {
		function register_inbound_payment_icons_widget() {
			register_widget( 'Inbound_Payment_Icons_Widget' );
		}

		add_action( 'widgets_init', 'register_inbound_payment_icons_widget', 1 );
	}
}