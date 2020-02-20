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
 * Plugin Name:       Smart Online Order for Clover
 * Plugin URI:        http://www.zaytechapps.com
 * Description:       Start taking orders from your Wordpress website and have them sent to your Clover Station
 * Version:           1.3.9
 * Author:            Zaytech
 * Author URI:        http://www.zaytechapps.com
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
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/moo-OnlineOrders-activator.php
 */
function activate_moo_OnlineOrders($network_wide) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-activator.php';
    if (function_exists('is_multisite') && is_multisite() && $network_wide ) {
        Moo_OnlineOrders_Activator::activateOnNetwork();
        return;
    }
    Moo_OnlineOrders_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/moo-OnlineOrders-deactivator.php
 */
function deactivate_moo_OnlineOrders() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-deactivator.php';
    Moo_OnlineOrders_Deactivator::deactivate();
}

function moo_OnlineOrders_shortcodes_allitems($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-shortcodes.php';
    return Moo_OnlineOrders_Shortcodes::TheStore($atts, $content);
}

function moo_OnlineOrders_shortcodes_checkoutPage($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes/checkoutPage.php';
    $checkoutPage = new CheckoutPage();
    return $checkoutPage->render($atts, $content);
}

function moo_OnlineOrders_shortcodes_buybutton($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-shortcodes.php';
    return Moo_OnlineOrders_Shortcodes::moo_BuyButton($atts, $content);
}

function moo_OnlineOrders_shortcodes_thecart($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-shortcodes.php';
    return Moo_OnlineOrders_Shortcodes::theCart($atts, $content);
}
function moo_OnlineOrders_shortcodes_searchBar($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-shortcodes.php';
    return Moo_OnlineOrders_Shortcodes::moo_search_bar($atts, $content);
}
function moo_OnlineOrders_shortcodes_customerAccount($atts, $content) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-shortcodes.php';
    return Moo_OnlineOrders_Shortcodes::moo_customer_account($atts, $content);
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
    require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-widgets.php';
    register_widget( 'Moo_OnlineOrders_Widgets_Opening_hours' );
}
function moo_OnlineOrders_widget_best_selling()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-widgets.php';
    register_widget( 'Moo_OnlineOrders_Widgets_best_selling' );
}
function Moo_OnlineOrders_Widgets_categories()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-widgets.php';
    register_widget( 'Moo_OnlineOrders_Widgets_categories' );
}

function moo_OnlineOrders_RestAPI() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/moo-OnlineOrders-Restapi.php';
    $rest_api = new Moo_OnlineOrders_Restapi();
    $rest_api->register_routes();
}

/* Activate and deactivate hooks*/
register_activation_hook( __FILE__, 'activate_moo_OnlineOrders' );
register_deactivation_hook( __FILE__, 'deactivate_moo_OnlineOrders' );

/* adding  shortcodes*/
add_shortcode('moo_all_items', 'moo_OnlineOrders_shortcodes_allitems');
add_shortcode('moo_cart', 'moo_OnlineOrders_shortcodes_thecart');
add_shortcode('moo_checkout', 'moo_OnlineOrders_shortcodes_checkoutPage');
add_shortcode('moo_my_account', 'moo_OnlineOrders_shortcodes_customerAccount');

add_shortcode('moo_buy_button', 'moo_OnlineOrders_shortcodes_buybutton');
add_shortcode('moo_category_msg', 'moo_OnlineOrders_shortcodes_categorymsg');
add_shortcode('moo_search', 'moo_OnlineOrders_shortcodes_searchBar');


/* adding  widgets*/
add_action( 'widgets_init', 'moo_OnlineOrders_widget_opening_hours' );
add_action( 'widgets_init', 'moo_OnlineOrders_widget_best_selling' );
add_action( 'widgets_init', 'Moo_OnlineOrders_Widgets_categories' );

/* Rest Api */
add_action( 'rest_api_init', 'moo_OnlineOrders_RestAPI' );

// add links to plugin

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'moo_add_action_links' );

function moo_add_action_links( $links ) {
    $plugin_links_1 = array(
        '<a href="admin.php?page=moo_index">Settings</a>',
        '<a href="http://docs.smartonlineorder.com/">Docs</a>',
        '<a href="https://smartonlineorder.com/contact-us/">Support</a>',
    );

    if (function_exists('is_multisite') && is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
            $plugin_links_2 = array(
                '<a href="admin.php?page=moo_deactivateAndClean">Clean inventory</a>',
            );
    } else {
        $plugin_links_2 = array(
            '<a href="admin.php?page=moo_deactivateAndClean">Deactivate & clean inventory</a>',
        );
    }

    return array_merge( $plugin_links_1, $links );
}
function moo_deactivateAndClean() {
    if( isset( $_GET['page'] ) &&  $_GET['page'] === 'moo_deactivateAndClean')
    {
        require_once plugin_dir_path( __FILE__)."/includes/moo-OnlineOrders-deactivator.php";

        if(function_exists("is_plugin_active_for_network") && !is_plugin_active_for_network( plugin_basename( __FILE__ ) )){
            Moo_OnlineOrders_Deactivator::deactivateAndClean();
            deactivate_plugins( plugin_basename( __FILE__ ), true );
        } else {
            Moo_OnlineOrders_Deactivator::onlyClean();
        }

        $url = admin_url( 'plugins.php?deactivate=true' );
        header( "Location: $url" );
        die();
    }
}
add_action( 'admin_init', 'moo_deactivateAndClean');
                 
if(get_option('moo_onlineOrders_version') != '139') {
    add_action('plugins_loaded', 'moo_onlineOrders_check_version');
}


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
    $wpdb->hide_errors();
    $version = get_option('moo_onlineOrders_version');
    $defaultOptions = get_option( 'moo_settings' );
    if(! isset($version) || empty($version)){
        $version="120";
    }
   // $version="120";
    switch ($version)
    {
        case '120':
            //Adding new fields in category table
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_category` ADD `image_url` VARCHAR(255) NULL");
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_category` ADD `alternate_name` VARCHAR(100) NULL");

        case '121':
        	@$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_modifier` ADD `sort_order` INT NULL");
        	@$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_modifier` ADD `show_by_default` INT NOT NULL DEFAULT '1'");
        	@$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_modifier_group` ADD `sort_order` INT NULL");
	    case '122':
	        @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `type` INT(1) NULL");
        case '123':
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_item` ADD `sort_order` INT NULL");
        case '124':
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `sort_order` INT NULL");
            @$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item_order` (
                          `_id` INT NOT NULL AUTO_INCREMENT,
                          `item_uuid` VARCHAR(100) NOT NULL,
                          `order_uuid` VARCHAR(100) NOT NULL,
                          `quantity` VARCHAR(100) NOT NULL,
                          `modifiers` TEXT NOT NULL,
                          `special_ins` VARCHAR(255) NOT NULL,
                          PRIMARY KEY (`_id`, `item_uuid`, `order_uuid`)
                            );");

            //Change where pages are saved
            $store_page     = get_option('moo_store_page');
            $checkout_page  = get_option('moo_checkout_page');
            $cart_page      = get_option('moo_cart_page');
            if( !isset($defaultOptions["store_page"]) || $defaultOptions["store_page"] == "" ) $defaultOptions["store_page"] = $store_page;
            if( !isset($defaultOptions["checkout_page"]) || $defaultOptions["checkout_page"] == "") $defaultOptions["checkout_page"] = $checkout_page;
            if( !isset($defaultOptions["cart_page"]) || $defaultOptions["cart_page"] == "") $defaultOptions["cart_page"] = $cart_page;
            if( !isset($defaultOptions["checkout_login"]) || $defaultOptions["checkout_login"] == "") $defaultOptions["checkout_login"] = "enabled";
        case '125':
            //add description to items
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_item` CHANGE `description` `description` TEXT ");
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `minAmount` VARCHAR(100) NULL DEFAULT '0' ");
            //add default options for coupons feature
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
            if( !isset($defaultOptions["copyrights"]) || $defaultOptions["copyrights"] == "") $defaultOptions["copyrights"] = 'Powered by <a href="https://wordpress.org/plugins/clover-online-orders/" target="_blank" title="Online Orders for Clover POS v 1.2.8">Smart Online Order</a>';
        case '127':
            $default_options = array(
                array("name"=>"onePage_fontFamily","value"=>"Oswald,sans-serif"),
                array("name"=>"onePage_categoriesTopMargin","value"=>"0"),
                array("name"=>"onePage_width","value"=>"1024"),
                array("name"=>"onePage_categoriesFontColor","value"=>"#ffffff"),
                array("name"=>"onePage_categoriesBackgroundColor","value"=>"#282b2e"),
                array("name"=>"onePage_qtyWindow","value"=>"on"),
                array("name"=>"onePage_qtyWindowForModifiers","value"=>"on"),
                array("name"=>"onePage_backToTop","value"=>"off"),
                array("name"=>"order_later_asap_for_p","value"=>"off"),
                array("name"=>"order_later_asap_for_d","value"=>"off"),
                array("name"=>"mg_settings_displayInline","value"=>"disabled"),
                array("name"=>"mg_settings_qty_for_all","value"=>"enabled"),
                array("name"=>"mg_settings_qty_for_zeroPrice","value"=>"enabled"),
            );

            foreach ($default_options as $default_option) {
                if(!isset($defaultOptions[$default_option["name"]]))
                    $defaultOptions[$default_option["name"]]=$default_option["value"];
            }
        case '128':
        case '130':
        case '131':
            if(!isset($defaultOptions['onePage_show_more_button'])) {
                $defaultOptions['onePage_show_more_button']='on';
            }
            $defaultOptions['payment_creditcard'] = 'on';
            $defaultOptions['use_sms_verification'] = 'enabled';
        case '132':
            if(!isset($defaultOptions['my_account_page'])) {
                $defaultOptions['my_account_page']='';
            }
            if(!isset($defaultOptions['text_under_special_instructions'])) {
                $defaultOptions['text_under_special_instructions']='*additional charges may apply and not all changes are possible';
            }
            if(!isset($defaultOptions['use_couponsApp'])) {
                $defaultOptions['use_couponsApp']= "off";
            }
            if(!isset($defaultOptions['custom_sa_content'])) {
                $defaultOptions['custom_sa_content']= "";
            }
            if(!isset($defaultOptions['custom_sa_title'])) {
                $defaultOptions['custom_sa_title']= "";
            }
            if(!isset($defaultOptions['custom_sa_onCheckoutPage'])) {
                $defaultOptions['custom_sa_onCheckoutPage']= "off";
            }
            if(!isset($defaultOptions['closing_msg'])) {
                $defaultOptions['closing_msg']= "";
            }
        case '133':
            if(!isset($defaultOptions['accept_orders'])) {
                $defaultOptions['accept_orders']= "enabled";
            }
            if(!isset($defaultOptions['onePage_askforspecialinstruction'])) {
                $defaultOptions['onePage_askforspecialinstruction'] = 'off';
            }
            if(!isset($defaultOptions['onePage_messageforspecialinstruction'])) {
                $defaultOptions['onePage_messageforspecialinstruction'] = 'Type your instructions here, additional charges may apply and not all changes are possible';
            }
            if(!isset($defaultOptions['jTheme_askforspecialinstruction'])) {
                $defaultOptions['jTheme_askforspecialinstruction'] = 'off';
            }
            if(!isset($defaultOptions['jTheme_messageforspecialinstruction'])) {
                $defaultOptions['jTheme_messageforspecialinstruction'] = 'Type your instructions here, additional charges may apply and not all changes are possible';
            }
            if(!isset($defaultOptions['style2_askforspecialinstruction'])) {
                $defaultOptions['style2_askforspecialinstruction'] = 'off';
            }
            if(!isset($defaultOptions['style2_messageforspecialinstruction'])) {
                $defaultOptions['style2_messageforspecialinstruction'] = 'Type your instructions here, additional charges may apply and not all changes are possible';
            }
            update_option("moo_settings",$defaultOptions);
        case '136':
        case '137':
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_category` ADD `description` TEXT NULL");
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_category` ADD `custom_hours` VARCHAR(100) NULL");
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_category` ADD `time_availability` VARCHAR(10) NULL");
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_item` CHANGE `description` `description` TEXT ");
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `minAmount` VARCHAR(100) NULL DEFAULT '0' ");
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `custom_hours` VARCHAR(100) NULL");
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `time_availability` VARCHAR(10) DEFAULT 1");
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `use_coupons` INT(1) NULL DEFAULT 1");
            @$wpdb->query("ALTER TABLE `{$wpdb->prefix}moo_order_types` ADD `custom_message` VARCHAR(255) NULL DEFAULT 'Not available yet'");
        case '138':
            update_option('moo_onlineOrders_version','139');
        case '138':
            break;
    }
}


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
