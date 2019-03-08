<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
} else {
	global $wpdb;

    if (function_exists('is_multisite') && is_multisite()) {
        $old_blog = $wpdb->blogid;
        $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        foreach ($blogids as $blog_id) {
            switch_to_blog($blog_id);
            $defaultOptions = get_option( 'moo_settings' );
            $store_page_id  = $defaultOptions['store_page'];
            $cart_page_id   = $defaultOptions['cart_page'];
            $checkout_page_id = $defaultOptions['checkout_page'];

            if($store_page_id) wp_delete_post($store_page_id,true);
            if($checkout_page_id) wp_delete_post($checkout_page_id,true);
            if($cart_page_id) wp_delete_post($cart_page_id,true);

            /*-- Table `item_option`--*/
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_option` ;");

            /*-- Table `item_tax_rate` --*/
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_tax_rate` ;");

            /* -- Table `modifier_group` -- */
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_order` ;");

            /*-- Table `item_tag` --*/
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_tag` ;");

            /*-- Table `item_modifier_group` --*/
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_modifier_group` ;");

            /* -- Table `order_types -- */
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_images` ;");

            /* -- Table `item` -- */
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item` ;");

            /* -- Table `orders` -- */
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_order` ;");

            /*-- Table `option`--*/
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_option` ;");

            /* -- Table `tag` --*/
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_tag` ;");

            /* -- Table `tax_rate` --*/
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_tax_rate` ;");

            /* -- Table `modifier` --*/
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_modifier` ;");

            /*-- Table `category` -- */
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_category` ;");

            /* -- Table `attribute` --*/
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_attribute` ;");

            /* -- Table `item_group` --*/
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_group` ;");

            /* -- Table `modifier_group` -- */
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_modifier_group` ;");

            /* -- Table `order_types -- */
            $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_order_types` ;");

            update_option( 'moo_settings','');
        }
        switch_to_blog($old_blog);
    } else {
        $defaultOptions = get_option( 'moo_settings' );
        $store_page_id  = $defaultOptions['store_page'];
        $cart_page_id   = $defaultOptions['cart_page'];
        $checkout_page_id = $defaultOptions['checkout_page'];
        $my_account_page_id = $defaultOptions['my_account_page'];

        if($store_page_id) wp_delete_post($store_page_id,true);
        if($checkout_page_id) wp_delete_post($checkout_page_id,true);
        if($cart_page_id) wp_delete_post($cart_page_id,true);
        if($my_account_page_id) wp_delete_post($my_account_page_id,true);

        /*-- Table `item_option`--*/
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_option` ;");

        /*-- Table `item_tax_rate` --*/
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_tax_rate` ;");

        /* -- Table `modifier_group` -- */
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_order` ;");

        /*-- Table `item_tag` --*/
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_tag` ;");

        /*-- Table `item_modifier_group` --*/
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_modifier_group` ;");

        /* -- Table `order_types -- */
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_images` ;");

        /* -- Table `item` -- */
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item` ;");

        /* -- Table `orders` -- */
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_order` ;");

        /*-- Table `option`--*/
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_option` ;");

        /* -- Table `tag` --*/
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_tag` ;");

        /* -- Table `tax_rate` --*/
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_tax_rate` ;");

        /* -- Table `modifier` --*/
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_modifier` ;");

        /*-- Table `category` -- */
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_category` ;");

        /* -- Table `attribute` --*/
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_attribute` ;");

        /* -- Table `item_group` --*/
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_item_group` ;");

        /* -- Table `modifier_group` -- */
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_modifier_group` ;");

        /* -- Table `order_types -- */
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}moo_order_types` ;");

        update_option( 'moo_settings','');
    }
}
