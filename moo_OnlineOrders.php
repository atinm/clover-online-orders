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
 * Plugin Name:       Merchantech Online Orders for Clover
 * Plugin URI:        http://www.merchantechapps.com
 * Description:       Start taking orders from your Wordpress website and have them sent to your Clover Station
 * Version:           1.2.5
 * Author:            Merchantech
 * Author URI:        http://www.merchantechapps.com
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
    return Moo_OnlineOrders_Shortcodes::TheStore($atts, $content);
}

function moo_OnlineOrders_shortcodes_checkoutPage($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-shortcodes.php';
    return Moo_OnlineOrders_Shortcodes::checkoutPage($atts, $content);
}

function moo_OnlineOrders_shortcodes_buybutton($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-shortcodes.php';
    return Moo_OnlineOrders_Shortcodes::moo_BuyButton($atts, $content);
}

function moo_OnlineOrders_shortcodes_thecart($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-shortcodes.php';
    return Moo_OnlineOrders_Shortcodes::theCart($atts, $content);
}

add_shortcode('moo_all_items', 'moo_OnlineOrders_shortcodes_allitems');
add_shortcode('moo_checkout', 'moo_OnlineOrders_shortcodes_checkoutPage');
add_shortcode('moo_buy_button', 'moo_OnlineOrders_shortcodes_buybutton');
add_shortcode('moo_cart', 'moo_OnlineOrders_shortcodes_thecart');


/*
add_filter( 'wp_mail_content_type', function( $content_type ) {
    return 'text/html';
});
*/
if(get_option('moo_onlineOrders_version')!='125')
    add_action('plugins_loaded', 'moo_onlineOrders_check_version');

/*
 * This function for updating the database structure when the version changed and updated it automatically
 * First of all we save the current version like an option
 * then we compare the current version with the version saved in database
 * for example in the version  1.1.3
 * we added the support of product's image so if the current version is 1.1.2 or previous version we will create the table images.
 *
 * @since v 1.1.2
 */
function moo_onlineOrders_check_version()
{
    global $wpdb;
    $version = get_option('moo_onlineOrders_version');
    switch ($version)
    {
        case false :
            //Adding show/hide a category
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_category` ADD `show_by_default` INT(1) NOT NULL DEFAULT '1' AFTER `sort_order`;");
        case '112':
            //Adding description field
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_item` ADD `description` VARCHAR(255) NULL  AFTER `alternate_name`;");
            //Adding images table
            $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_images` (
                          `_id` INT NOT NULL AUTO_INCREMENT,
                          `url` VARCHAR(255) NOT NULL,
                          `is_enabled` INT NOT NULL,
                          `is_default` INT NOT NULL,
                          `item_uuid` VARCHAR(100) NOT NULL,
                          PRIMARY KEY (`_id`),
                          CONSTRAINT `fk_item_has_images`
                                FOREIGN KEY (`item_uuid`)
                                REFERENCES `{$wpdb->prefix}moo_item` (`uuid`)
                                ON DELETE NO ACTION
                                ON UPDATE NO ACTION)
                        ENGINE = InnoDB;");
        case '113':
        case '114':
        case '115':
            //Adding new fields in order table
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order` ADD `p_state` VARCHAR(100) NULL");
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order` ADD `p_country` VARCHAR(100) NULL");
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order` ADD `p_lat` VARCHAR(255) NULL");
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order` ADD `p_lng` VARCHAR(255) NULL");
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order` ADD `shippingfee` VARCHAR(100) NULL");
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order` ADD `tipAmount` VARCHAR(100) NULL");
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order` ADD `deliveryfee` VARCHAR(100) NULL");
            //Adding out of stock fields
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_item` ADD `outofstock` INT(1) NOT NULL DEFAULT '0'");

        case '116':
        case '117':
        case '118':
        case '119':
        case '120':
            //Adding new fields in category table
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_category` ADD `image_url` VARCHAR(255) NULL");
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_category` ADD `alternate_name` VARCHAR(100) NULL");

        case '121':
        	$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_modifier` ADD `sort_order` INT NULL");
        	$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_modifier` ADD `show_by_default` INT NOT NULL DEFAULT '1'");
        	$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_modifier_group` ADD `sort_order` INT NULL");
	    case '122':
	        $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `type` INT(1) NULL");
        case '123':
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_item` ADD `sort_order` INT NULL");
        case '124':
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `sort_order` INT NULL");
            $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item_order` (
                          `_id` INT NOT NULL AUTO_INCREMENT,
                          `item_uuid` VARCHAR(100) NOT NULL,
                          `order_uuid` VARCHAR(100) NOT NULL,
                          `quantity` VARCHAR(100) NOT NULL,
                          `modifiers` TEXT NOT NULL,
                          `special_ins` VARCHAR(255) NOT NULL,
                          PRIMARY KEY (`_id`, `item_uuid`, `order_uuid`)
                            )
                        ENGINE = InnoDB;");

            $store_page     = get_option('moo_store_page');
            $chekcout_page  = get_option('moo_checkout_page');
            $cart_page      = get_option('moo_cart_page');
            $defaultOptions = get_option( 'moo_settings' );

            if( !isset($defaultOptions["store_page"]) || $defaultOptions["store_page"] == "" ) $defaultOptions["store_page"] = $store_page;
            if( !isset($defaultOptions["checkout_page"]) || $defaultOptions["checkout_page"] == "") $defaultOptions["checkout_page"] = $chekcout_page;
            if( !isset($defaultOptions["cart_page"]) || $defaultOptions["cart_page"] == "") $defaultOptions["cart_page"] = $cart_page;
            if( !isset($defaultOptions["checkout_login"]) || $defaultOptions["checkout_login"] == "") $defaultOptions["checkout_login"] = "disabled";

            update_option('moo_settings', $defaultOptions );
            update_option('moo_onlineOrders_version','125');
            break;
        case '125':
            break;
    }
}

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
