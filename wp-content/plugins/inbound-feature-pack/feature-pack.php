<?php
/*
 * Plugin Name: Inbound Core Feature Pack
 * Plugin URI: http://www.shapingrain.com/
 * Description: Provides profiles, banners, modal windows and advanced shortcodes for the Inbound theme.
 * Version: 1.0.10
 * Author: ShapingRain.com Labs
 * Author URI: http://www.shapingrain.com/
 */

/*
 * Global constants
 */
define('INBOUND_FEATURE_PACK', true);


/*
 * Third Party Libraries
 */
if ( ! class_exists( 'SR_Custom_Post_Type') ) {
	require_once ( plugin_dir_path (  __FILE__  ) . '/libs/class.custom-post-types.php' );
}


/*
 * Include individual components
 */
require_once ( plugin_dir_path (  __FILE__  ) . '/user.php' );
require_once ( plugin_dir_path (  __FILE__  ) . '/profiles.php' );
require_once ( plugin_dir_path (  __FILE__  ) . '/banners.php' );
require_once ( plugin_dir_path (  __FILE__  ) . '/modals.php' );
require_once ( plugin_dir_path (  __FILE__  ) . '/advanced-shortcodes.php' );
require_once ( plugin_dir_path (  __FILE__  ) . '/tools.php' );
require_once ( plugin_dir_path (  __FILE__  ) . '/misc.php' );


/*
 * Load plugin textdomain.
 */
function inbound_features_load_textdomain() {
	load_plugin_textdomain( 'inbound', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'inbound_features_load_textdomain' );


/*
 * Compatibility check
 */
function inbound_features_check_theme() {
	if ( ! function_exists('inbound_option') ) {

		add_action( 'admin_init', 'inbound_features_plugin_deactivate' );
		add_action( 'admin_notices', 'inbound_features_plugin_admin_notice' );

		function inbound_features_plugin_deactivate() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		function inbound_features_plugin_admin_notice() {
			echo '<div class="updated"><p>' . __('<strong>Inbound Feature Pack</strong> requires the Inbound theme and has been <strong>deactivated</strong>.', 'inbound') .'</p></div>';
			if ( isset( $_GET['activate'] ) )
				unset( $_GET['activate'] );
		}
	}
}
add_action( 'init', 'inbound_features_check_theme' );


/*
 * Initialization routine
 */
function inbound_features_activation() {
	if ( function_exists('inbound_option_global') && function_exists('inbound_setup_initial_setup') ) {
		if ( ! inbound_option_global('setup_init_done') ) {
			// theme has never before been activated, so we need to run initial setup routine
			inbound_setup_initial_setup();
		}
	}
}
register_activation_hook( __FILE__, 'inbound_features_activation' );
