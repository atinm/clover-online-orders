<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Wordpress_Integration
 *
 * @wordpress-plugin
 * Plugin Name:       Merchantech Online Orders
 * Plugin URI:        http://www.merchantech.us
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Merchantech
 * Author URI:        http://www.merchantech.us
 * License:           Clover app
 * License URI:       http://www.clover.com
 * Text Domain:       moo_OnlineOrders
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-moo-OnlineOrders-activator.php
 */
function activate_moo_OnlineOrders() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-activator.php';
    Moo_OnlineOrders_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-moo-OnlineOrders-deactivator.php
 */
function deactivate_moo_OnlineOrders() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-deactivator.php';
    Moo_OnlineOrders_Deactivator::deactivate();
}


register_activation_hook( __FILE__, 'activate_moo_OnlineOrders' );
register_deactivation_hook( __FILE__, 'deactivate_moo_OnlineOrders' );

function moo_OnlineOrders_shortcodes_allitems($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-shortcodes.php';
    Moo_OnlineOrders_Shortcodes::TheStore($atts, $content);
}
function moo_OnlineOrders_shortcodes_checkoutPage($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-shortcodes.php';
    Moo_OnlineOrders_Shortcodes::checkoutPage($atts, $content);
}
add_shortcode('moo_all_items', 'moo_OnlineOrders_shortcodes_allitems');
add_shortcode('moo_checkout', 'moo_OnlineOrders_shortcodes_checkoutPage');
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_moo_OnlineOrders() {

	$plugin = new moo_OnlineOrders();
	$plugin->run();

}
run_moo_OnlineOrders();
