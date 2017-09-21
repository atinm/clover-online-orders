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
 * Version:           1.2.7
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
function moo_OnlineOrders_shortcodes_categorymsg($atts, $content) {
    if(isset($atts["cat_id"]) && $atts["message"])
    {
        if(isset($_GET["category"]) && $_GET["category"] == $atts["cat_id"])
        {
            if(isset($atts["css-class"]) && $atts["css-class"]!="")
                return "<div class='".$atts["css-class"]."'>".$atts["message"]."</div>";
            else
                return $atts["message"];
        }
    }
    else
        return "Please enter the category id (cat_id) and the message";
}



/*
* Widgets Contents
*/
function moo_OnlineOrders_widget_opening_hours() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-widgets.php';
    register_widget( 'Moo_OnlineOrders_Widgets_Opening_hours' );
}
function moo_OnlineOrders_widget_best_selling()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-widgets.php';
    register_widget( 'Moo_OnlineOrders_Widgets_best_selling' );
}
function Moo_OnlineOrders_Widgets_categories()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-widgets.php';
    register_widget( 'Moo_OnlineOrders_Widgets_categories' );
}

function moo_OnlineOrders_RestAPI() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-moo-OnlineOrders-Restapi.php';
    $rest_api = new Moo_OnlineOrders_Restapi();
    $rest_api->register_routes();
}

/* adding  shortcodes*/
add_shortcode('moo_all_items', 'moo_OnlineOrders_shortcodes_allitems');
add_shortcode('moo_checkout', 'moo_OnlineOrders_shortcodes_checkoutPage');
add_shortcode('moo_buy_button', 'moo_OnlineOrders_shortcodes_buybutton');
add_shortcode('moo_cart', 'moo_OnlineOrders_shortcodes_thecart');
add_shortcode('moo_category_msg', 'moo_OnlineOrders_shortcodes_categorymsg');

/* adding  widgets*/
add_action( 'widgets_init', 'moo_OnlineOrders_widget_opening_hours' );
add_action( 'widgets_init', 'moo_OnlineOrders_widget_best_selling' );
add_action( 'widgets_init', 'Moo_OnlineOrders_Widgets_categories' );

/* Rest Api adding*/
add_action( 'rest_api_init', 'moo_OnlineOrders_RestAPI' );


/*
add_filter( 'wp_mail_content_type', function( $content_type ) {
    return 'text/html';
});
*/
if(get_option('moo_onlineOrders_version')!='126')
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
    $defaultOptions = get_option( 'moo_settings' );
    switch ($version)
    {
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
            $checkout_page  = get_option('moo_checkout_page');
            $cart_page      = get_option('moo_cart_page');
            if( !isset($defaultOptions["store_page"]) || $defaultOptions["store_page"] == "" ) $defaultOptions["store_page"] = $store_page;
            if( !isset($defaultOptions["checkout_page"]) || $defaultOptions["checkout_page"] == "") $defaultOptions["checkout_page"] = $checkout_page;
            if( !isset($defaultOptions["cart_page"]) || $defaultOptions["cart_page"] == "") $defaultOptions["cart_page"] = $cart_page;
            if( !isset($defaultOptions["checkout_login"]) || $defaultOptions["checkout_login"] == "") $defaultOptions["checkout_login"] = "disabled";
            if( !isset($defaultOptions["use_coupons"]) || $defaultOptions["use_coupons"] == "") $defaultOptions["use_coupons"] = "disabled";
        case '125':
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_item` CHANGE `description` `description` TEXT ");
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `minAmount` VARCHAR(100) NULL DEFAULT '0' ");
            if( !isset($defaultOptions["use_coupons"]) || $defaultOptions["use_coupons"] == "") $defaultOptions["use_coupons"] = "disabled";
        case '126':
            if( !isset($defaultOptions["use_special_instructions"]) || $defaultOptions["use_special_instructions"] == "") $defaultOptions["use_special_instructions"] = "enabled";
            if( !isset($defaultOptions["save_cards"]) || $defaultOptions["save_cards"] == "") $defaultOptions["save_cards"] = "disabled";
            if( !isset($defaultOptions["save_cards_fees"]) || $defaultOptions["save_cards_fees"] == "") $defaultOptions["save_cards_fees"] = "disabled";
            if( !isset($defaultOptions["service_fees_name"]) || $defaultOptions["service_fees_name"] == "") $defaultOptions["service_fees_name"] = "Service Charge";
            if( !isset($defaultOptions["service_fees_type"]) || $defaultOptions["service_fees_type"] == "") $defaultOptions["service_fees_type"] = "amount";
            if( !isset($defaultOptions["delivery_fees_name"]) || $defaultOptions["delivery_fees_name"] == "") $defaultOptions["delivery_fees_name"] = "Delivery Charge";
            if( !isset($defaultOptions["order_later_minutes_delivery"]) || $defaultOptions["order_later_minutes_delivery"] == "") $defaultOptions["order_later_minutes_delivery"] = "60";
            if( !isset($defaultOptions["order_later_days_delivery"]) || $defaultOptions["order_later_days_delivery"] == "") $defaultOptions["order_later_days_delivery"] = "4";
            if( !isset($defaultOptions["copyrights"]) || $defaultOptions["copyrights"] == "") $defaultOptions["copyrights"] = 'Powered by <a href="https://wordpress.org/plugins/clover-online-orders/" target="_blank" title="Online Orders for Clover POS v 1.2.7">Smart Online Order</a>';

            update_option('moo_settings', $defaultOptions );
            update_option('moo_onlineOrders_version','127');
        case '127':
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
