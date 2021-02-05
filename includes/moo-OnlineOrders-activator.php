<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Moo_OnlineOrders
 * @subpackage Moo_OnlineOrders/includes
 * @author     Mohammed EL BANYAOUI <elbanyaoui@hotmail.com>
 */
class Moo_OnlineOrders_Activator {

	/**
	 * @since    1.0.0
	 */
	public static function activateOnNetwork() {
        // Install DB
        global $wpdb;
      //  $wpdb->show_errors();
        if (function_exists('is_multisite') && is_multisite()) {
            $old_blog = $wpdb->blogid;
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                self::activate();
            }
            switch_to_blog($old_blog);
        } else {
            self::activate();
        }

	}
	public static function activate() {
        // Install DB
        global $wpdb;
        $wpdb->hide_errors();
        //$wpdb->show_errors();

/*      -- -----------------------------------------------------
        -- Table `item_group`
        -- -----------------------------------------------------
*/
        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item_group` (
                          `_id` INT NOT NULL AUTO_INCREMENT,
                          `uuid` VARCHAR(100) NOT NULL,
                          `name` VARCHAR(100) NULL,
                          PRIMARY KEY (`_id`),
                          UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC)) DEFAULT CHARACTER SET $wpdb->charset;");


/*
        -- -----------------------------------------------------
        -- Table `item--
        --------------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item` (
                      `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(45) NOT NULL ,
                      `name` VARCHAR(255) NULL ,
                      `alternate_name` VARCHAR(255) NULL ,
                      `description` TEXT NULL ,
                      `price` MEDIUMTEXT NULL ,
                      `code` VARCHAR(100) NULL ,
                      `price_type` VARCHAR(10) NULL ,
                      `unit_name` VARCHAR(100) NULL ,
                      `default_taxe_rate` INT NULL ,
                      `sku` VARCHAR(100) NULL ,
                      `hidden` INT NULL ,
                      `is_revenue` INT NULL ,
                      `cost` MEDIUMTEXT NULL ,
                      `modified_time` MEDIUMTEXT NULL,
                      `item_group_uuid` VARCHAR(100) NULL,
                      `visible` INT(1) DEFAULT '1',
                      `outofstock` INT(1) NOT NULL DEFAULT '0',
                      `sort_order` INT NULL ,
                      PRIMARY KEY (`_id`),
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC),
                      INDEX `{$wpdb->prefix}fk_item_item_group_idx` (`item_group_uuid` ASC),
                      CONSTRAINT `{$wpdb->prefix}fk_item_item_group`
                        FOREIGN KEY (`item_group_uuid`)
                        REFERENCES `{$wpdb->prefix}moo_item_group` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION) DEFAULT CHARACTER SET $wpdb->charset;");
/*
        -- -----------------------------------------------------
        -- Table `Order--
        --------------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_order` (
                      `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(45) NOT NULL ,
                      `taxAmount` VARCHAR(100) NULL ,
                      `amount` VARCHAR(100) NULL ,
                      `deliveryfee` VARCHAR(100) NULL ,
                      `shippingfee` VARCHAR(100) NULL ,
                      `tipAmount` VARCHAR(100) NULL ,
                      `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
                      `paid` INT(1)  DEFAULT '0' ,
                      `refpayment` VARCHAR(50) NULL ,
                      `ordertype` VARCHAR(250) NULL ,
                      `p_name` VARCHAR(255) NULL ,
                      `p_address` VARCHAR(100) NULL ,
                      `p_city` VARCHAR(100) NULL ,
                      `p_state` VARCHAR(100) NULL ,
                      `p_zipcode` VARCHAR(100) NULL ,
                      `p_country` VARCHAR(100) NULL ,
                      `p_phone` VARCHAR(100) NULL ,
                      `p_email` VARCHAR(100) NULL ,
                      `p_lat` VARCHAR(255) NULL ,
                      `p_lng` VARCHAR(255) NULL ,
                      `instructions` VARCHAR(250) NULL ,
                      PRIMARY KEY (`_id`),
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC) ) DEFAULT CHARACTER SET $wpdb->charset;");

/*
-- -----------------------------------------------------
-- Table `category`
-- -----------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_category` (
                       `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(100) NOT NULL ,
                      `name` VARCHAR(255) NULL ,
                      `sort_order` INT NULL ,
                      `show_by_default` INT(1) NOT NULL DEFAULT '1' ,
                      `items` TEXT NULL ,
                      `image_url` VARCHAR(255) NULL,
                      `alternate_name` VARCHAR(255) NULL,
                      `description` TEXT NULL,
                      `time_availability` VARCHAR(10) NULL,
                      `custom_hours` VARCHAR(100) NULL,
                      PRIMARY KEY (`_id`)  ,
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC)  ) DEFAULT CHARACTER SET $wpdb->charset;");

/*
-- -----------------------------------------------------
-- Table `attribute`
-- -----------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_attribute` (
                      `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(100) NOT NULL ,
                      `name` VARCHAR(100) NULL ,
                      `item_group_uuid` VARCHAR(100) NOT NULL ,
                      PRIMARY KEY (`_id`)  ,
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC) ,
                      INDEX `{$wpdb->prefix}fk_attribute_item_group1_idx` (`item_group_uuid` ASC),
                      CONSTRAINT `{$wpdb->prefix}fk_attribute_item_group1`
                        FOREIGN KEY (`item_group_uuid`)
                        REFERENCES `{$wpdb->prefix}moo_item_group` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION) DEFAULT CHARACTER SET $wpdb->charset;");


/*
-- -----------------------------------------------------
-- Table `option`
-- -----------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_option` (
                      `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(100) NOT NULL ,
                      `name` VARCHAR(100) NULL ,
                      `attribute_uuid` VARCHAR(100) NOT NULL ,
                      PRIMARY KEY (`_id`)  ,
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC) ,
                      INDEX `{$wpdb->prefix}fk_option_attribute1_idx` (`attribute_uuid` ASC) ,
                      CONSTRAINT `{$wpdb->prefix}fk_option_attribute1`
                        FOREIGN KEY (`attribute_uuid`)
                        REFERENCES `{$wpdb->prefix}moo_attribute` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION) DEFAULT CHARACTER SET $wpdb->charset;");

/*
-- -----------------------------------------------------
-- Table `modifier_group`
-- -----------------------------------------------------
*/
        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_modifier_group` (
                       `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(100) NOT NULL ,
                      `name` VARCHAR(100) NULL,
                      `alternate_name` VARCHAR(100) NULL ,
                      `show_by_default` INT NULL ,
                      `min_required` INT NULL ,
                      `max_allowd` INT NULL ,
                      `sort_order` INT NULL ,
                      PRIMARY KEY (`_id`),
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC)) DEFAULT CHARACTER SET $wpdb->charset;");


/*
-- -----------------------------------------------------
-- Table `modifier`
-- -----------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_modifier` (
                       `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(100) NOT NULL ,
                      `name` VARCHAR(100) NULL,
                      `price` VARCHAR(45) NULL,
                      `alternate_name` MEDIUMTEXT NULL,
                      `sort_order` INT NULL ,
                      `show_by_default` INT NOT NULL DEFAULT '1',
                      `group_id` VARCHAR(100) NOT NULL ,
                      PRIMARY KEY (`_id`)  ,
                      INDEX `{$wpdb->prefix}fk_modifier_modifier_group1_idx` (`group_id` ASC) ,
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC),
                      CONSTRAINT `{$wpdb->prefix}fk_modifier_modifier_group1`
                        FOREIGN KEY (`group_id`)
                        REFERENCES `{$wpdb->prefix}moo_modifier_group` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION) DEFAULT CHARACTER SET $wpdb->charset;");


/*
-- -----------------------------------------------------
-- Table `tag`
-- -----------------------------------------------------
*/


        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_tag` (
                      `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(100) NOT NULL ,
                      `name` VARCHAR(100) NULL ,
                      PRIMARY KEY (`_id`) ,
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC)) DEFAULT CHARACTER SET $wpdb->charset;");


/*
-- -----------------------------------------------------
-- Table `tax_rate`
-- -----------------------------------------------------
*/


        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_tax_rate` (
                      `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(100) NOT NULL ,
                      `name` VARCHAR(100) NULL ,
                      `rate` MEDIUMTEXT NULL ,
                      `is_default` INT NULL ,
                      PRIMARY KEY (`_id`) ,
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC)) DEFAULT CHARACTER SET $wpdb->charset;");

/*
-- -----------------------------------------------------
-- Table `item_tax_rate`
-- -----------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item_tax_rate` (
                      `_id` INT NOT NULL AUTO_INCREMENT,
                      `tax_rate_uuid` VARCHAR(100) NOT NULL ,
                      `item_uuid` VARCHAR(100) NOT NULL ,
                      PRIMARY KEY (`_id`) ,
                      INDEX `{$wpdb->prefix}fk_tax_rate_has_item_item1_idx` (`item_uuid` ASC),
                      INDEX `{$wpdb->prefix}fk_tax_rate_has_item_tax_rate1_idx` (`tax_rate_uuid` ASC),
                      CONSTRAINT `{$wpdb->prefix}fk_tax_rate_has_item_tax_rate1`
                        FOREIGN KEY (`tax_rate_uuid`)
                        REFERENCES `{$wpdb->prefix}moo_tax_rate` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
                      CONSTRAINT `{$wpdb->prefix}fk_tax_rate_has_item_item1`
                        FOREIGN KEY (`item_uuid`)
                        REFERENCES `{$wpdb->prefix}moo_item` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
                        UNIQUE( `tax_rate_uuid`, `item_uuid`)) DEFAULT CHARACTER SET $wpdb->charset;");


/*
-- -----------------------------------------------------
-- Table `item_tag`
-- -----------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item_tag` (
                      `_id` INT NOT NULL AUTO_INCREMENT,
                      `tag_uuid` VARCHAR(100) NOT NULL ,
                      `item_uuid` VARCHAR(100) NOT NULL,
                      INDEX `{$wpdb->prefix}fk_tag_has_item_item1_idx` (`item_uuid` ASC),
                      INDEX `{$wpdb->prefix}fk_tag_has_item_tag1_idx` (`tag_uuid` ASC),
                      PRIMARY KEY (`_id`) ,
                      CONSTRAINT `{$wpdb->prefix}fk_tag_has_item_tag1`
                        FOREIGN KEY (`tag_uuid`)
                        REFERENCES `{$wpdb->prefix}moo_tag` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
                      CONSTRAINT `{$wpdb->prefix}fk_tag_has_item_item1`
                        FOREIGN KEY (`item_uuid`)
                        REFERENCES `{$wpdb->prefix}moo_item` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
                        UNIQUE( `tag_uuid`, `item_uuid`)) DEFAULT CHARACTER SET $wpdb->charset;");

        /*
        -- -----------------------------------------------------
        -- Table `item_option`
        -- -----------------------------------------------------
        */

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item_option` (
                      `_id` INT NOT NULL AUTO_INCREMENT,
                      `item_uuid` VARCHAR(100) NOT NULL,
                      `option_uuid` VARCHAR(100) NOT NULL,
                      INDEX `{$wpdb->prefix}fk_item_has_option_option1_idx` (`option_uuid` ASC) ,
                      INDEX `{$wpdb->prefix}fk_item_has_option_item1_idx` (`item_uuid` ASC),
                      PRIMARY KEY (`_id`),
                      CONSTRAINT `{$wpdb->prefix}fk_item_has_option_item1`
                        FOREIGN KEY (`item_uuid`)
                        REFERENCES `{$wpdb->prefix}moo_item` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
                      CONSTRAINT `{$wpdb->prefix}fk_item_has_option_option1`
                        FOREIGN KEY (`option_uuid`)
                        REFERENCES `{$wpdb->prefix}moo_option` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
                        UNIQUE( `option_uuid`, `item_uuid`)) DEFAULT CHARACTER SET $wpdb->charset;");

/*
-- -----------------------------------------------------
-- Table `item_modifier_group`
-- -----------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item_modifier_group` (
                          `_id` INT NOT NULL AUTO_INCREMENT,
                          `item_id` VARCHAR(100) NOT NULL,
                          `group_id` VARCHAR(100) NOT NULL,
                          PRIMARY KEY (`_id`, `item_id`, `group_id`),
                          INDEX `fk_item_has_modifier_group_modifier_group1_idx` (`group_id` ASC) ,
                          INDEX `fk_item_has_modifier_group_item1_idx` (`item_id` ASC)  ,
                          CONSTRAINT `{$wpdb->prefix}fk_item_has_modifier_group_item1`
                            FOREIGN KEY (`item_id`)
                            REFERENCES `{$wpdb->prefix}moo_item` (`uuid`)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                          CONSTRAINT `{$wpdb->prefix}fk_item_has_modifier_group_modifier_group1`
                            FOREIGN KEY (`group_id`)
                            REFERENCES `{$wpdb->prefix}moo_modifier_group` (`uuid`)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                            UNIQUE(`item_id`,`group_id`)) DEFAULT CHARACTER SET $wpdb->charset;");

/*
-- -----------------------------------------------------
-- Table `item_order`
-- -----------------------------------------------------
* remove the UNIQUE(`item_uuid`,`order_uuid`,`modifiers`) and the FOREIGN KEY on the version 124
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item_order` (
                          `_id` INT NOT NULL AUTO_INCREMENT,
                          `item_uuid` VARCHAR(100) NOT NULL,
                          `order_uuid` VARCHAR(100) NOT NULL,
                          `quantity` VARCHAR(100) NOT NULL,
                          `modifiers` TEXT NOT NULL,
                          `special_ins` VARCHAR(255) NOT NULL,
                          PRIMARY KEY (`_id`, `item_uuid`, `order_uuid`)
                            ) DEFAULT CHARACTER SET $wpdb->charset;");

        /*
        -- -----------------------------------------------------
        -- Table `Order TYPES`
        -- -----------------------------------------------------
        */
        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_order_types` (
                          `ot_uuid` VARCHAR(100) NOT NULL,
                          `label` VARCHAR(100) NOT NULL,
                          `taxable` INT(1),
                          `status` INT(1),
                          `show_sa` INT(1),
                          `minAmount` VARCHAR(100) NULL DEFAULT '0',
                          `maxAmount` VARCHAR(100) NULL DEFAULT '',
                          `custom_hours` VARCHAR(100) NULL,
                          `time_availability` VARCHAR(100)  DEFAULT 1,
                          `use_coupons` INT(1) NULL DEFAULT 1,
                          `custom_message` VARCHAR(255) NULL DEFAULT 'Not available yet',
                          `sort_order` INT NULL,
                          `type` INT(1) NULL,
                          `allow_sc_order` INT(1) NULL DEFAULT 1 ,
                          PRIMARY KEY (`ot_uuid`)) DEFAULT CHARACTER SET $wpdb->charset;");

         /*
        -- -----------------------------------------------------
        -- Table `Images`
        -- -----------------------------------------------------
        */
        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_images` (
                          `_id` INT NOT NULL AUTO_INCREMENT,
                          `url` VARCHAR(255) NOT NULL,
                          `is_enabled` INT NOT NULL,
                          `is_default` INT NOT NULL,
                          `item_uuid` VARCHAR(100) NOT NULL,
                          PRIMARY KEY (`_id`),
                          CONSTRAINT `{$wpdb->prefix}fk_item_has_images`
                                FOREIGN KEY (`item_uuid`)
                                REFERENCES `{$wpdb->prefix}moo_item` (`uuid`)
                                ON DELETE NO ACTION
                                ON UPDATE NO ACTION) DEFAULT CHARACTER SET $wpdb->charset;");

        // Add the page :
        $pages = array(
            'store'=>array(
                'comment_status' => 'closed',
                'ping_status'    =>  'closed' ,
                'post_author'    => 1,
                'post_name'      => 'Store',
                'post_status'    => 'publish' ,
                'post_title'     => 'Order Online',
                'post_type'      => 'page',
                'post_content'   => '[moo_all_items]'
            ),
            'checkout'=>array(
                'comment_status' => 'closed',
                'ping_status'    =>  'closed' ,
                'post_author'    => 1,
                'post_name'      => 'Checkout',
                'post_status'    => 'publish' ,
                'post_title'     => 'Checkout',
                'post_type'      => 'page',
                'post_content'   => '[moo_checkout]'
            ),
            'cart'=>array(
                'comment_status' => 'closed',
                'ping_status'    =>  'closed' ,
                'post_author'    => 1,
                'post_name'      => 'Cart',
                'post_status'    => 'publish' ,
                'post_title'     => 'Cart',
                'post_type'      => 'page',
                'post_content'   => '[moo_cart]'
            ),
            'my_account_page'=>array(
                'comment_status' => 'closed',
                'ping_status'    =>  'closed' ,
                'post_author'    => 1,
                'post_name'      => 'My Orders',
                'post_status'    => 'publish' ,
                'post_title'     => 'My Orders',
                'post_type'      => 'page',
                'post_content'   => '[moo_my_account]'
            )
        );
        $defaultOptions = (array)get_option( 'moo_settings' );

        if(!isset($defaultOptions['store_page']) || $defaultOptions['store_page'] == "" )
        {
            $store_page_id =  wp_insert_post( $pages['store'], false );
            $defaultOptions['store_page'] = $store_page_id ;
        }
        if(!isset($defaultOptions['checkout_page']) || $defaultOptions['checkout_page'] == "")
        {
            $checkout_page_id =  wp_insert_post( $pages['checkout'], false );
            $defaultOptions['checkout_page'] =  $checkout_page_id;
        }
        if(!isset($defaultOptions['cart_page']) || $defaultOptions['cart_page'] == "")
        {
            $cart_page_id  =  wp_insert_post( $pages['cart'], false );
            $defaultOptions['cart_page'] = $cart_page_id ;
        }
        if(!isset($defaultOptions['my_account_page']) || $defaultOptions['my_account_page'] == "")
        {
            $login_page_id  =  wp_insert_post( $pages['my_account_page'], false );
            $defaultOptions['my_account_page'] = $login_page_id ;
        }

        // Save the version of the plugin in the Database
         update_option('moo_onlineOrders_version', '145');

        $defaultOptions = self::applyDefaultOptions($defaultOptions);

        update_option('moo_settings', $defaultOptions );
	}
	public static function applyDefaultOptions($MooOptions) {
        $default_options = array(
            array("name"=>"api_key","value"=>""),
            array("name"=>"lat","value"=>""),
            array("name"=>"lng","value"=>""),
            array("name"=>"hours","value"=>""),
            array("name"=>"closing_msg","value"=>""),
            array("name"=>"merchant_email","value"=>""),
            array("name"=>"thanks_page","value"=>""),
            array("name"=>"my_account_page","value"=>""),
            array("name"=>"fb_appid","value"=>""),
            array("name"=>"use_coupons","value"=>"disabled"),
            array("name"=>"use_sms_verification","value"=>"enabled"),
            array("name"=>"custom_css","value"=>""),
            array("name"=>"custom_js","value"=>""),
            array("name"=>"custom_sa_content","value"=>""),
            array("name"=>"custom_sa_title","value"=>""),
            array("name"=>"custom_sa_onCheckoutPage","value"=>"off"),
            array("name"=>"copyrights","value"=>'Powered by <a href="https://wordpress.org/plugins/clover-online-orders/" target="_blank" title="Online Orders for Clover POS v 1.4.0">Smart Online Order</a>'),
            array("name"=>"default_style","value"=>"onePage"),
            array("name"=>"track_stock","value"=>""),
            array("name"=>"track_stock_hide_items","value"=>"off"),
            array("name"=>"checkout_login","value"=>"enabled"),
            array("name"=>"tips","value"=>""),
            array("name"=>"payment_creditcard","value"=>"on"),
            array("name"=>"clover_payment_form","value"=>"off"),
            array("name"=>"payment_cash","value"=>"on"),
            array("name"=>"payment_cash_delivery","value"=>"on"),
            array("name"=>"scp","value"=>"off"),
            array("name"=>"merchant_phone","value"=>""),
            array("name"=>"order_later","value"=>"on"),
            array("name"=>"order_later_mandatory","value"=>"off"),
            array("name"=>"order_later_days","value"=>"4"),
            array("name"=>"order_later_minutes","value"=>"20"),
            array("name"=>"order_later_days_delivery","value"=>"4"),
            array("name"=>"order_later_minutes_delivery","value"=>"60"),
            array("name"=>"order_later_asap_for_p","value"=>"off"),
            array("name"=>"order_later_asap_for_d","value"=>"off"),
            array("name"=>"free_delivery","value"=>""),
            array("name"=>"fixed_delivery","value"=>""),
            array("name"=>"other_zones_delivery","value"=>""),
            array("name"=>"delivery_fees_name","value"=>"Delivery Charge"),
            array("name"=>"delivery_errorMsg","value"=>"Sorry, zone not supported. We do not deliver to this address at this time"),
            array("name"=>"zones_json","value"=>""),
            array("name"=>"hide_menu","value"=>""),
            array("name"=>"hide_menu_w_closed","value"=>"off"),
            array("name"=>"accept_orders_w_closed","value"=>"on"),
            array("name"=>"show_categories_images","value"=>false),
            array("name"=>"save_cards","value"=>"disabled"),
            array("name"=>"save_cards_fees","value"=>"disabled"),
            array("name"=>"service_fees","value"=>""),
            array("name"=>"service_fees_name","value"=>"Service Charge"),
            array("name"=>"service_fees_type","value"=>"amount"),
            array("name"=>"use_special_instructions","value"=>"enabled"),
            array("name"=>"onePage_fontFamily","value"=>"Oswald,sans-serif"),
            array("name"=>"onePage_categoriesTopMargin","value"=>"0"),
            array("name"=>"onePage_width","value"=>"1024"),
            array("name"=>"onePage_categoriesFontColor","value"=>"#ffffff"),
            array("name"=>"onePage_categoriesBackgroundColor","value"=>"#282b2e"),
            array("name"=>"onePage_qtyWindow","value"=>"on"),
            array("name"=>"onePage_qtyWindowForModifiers","value"=>"on"),
            array("name"=>"onePage_backToTop","value"=>"off"),
            array("name"=>"jTheme_width","value"=>"1024"),
            array("name"=>"jTheme_qtyWindow","value"=>"on"),
            array("name"=>"jTheme_qtyWindowForModifiers","value"=>"on"),
            array("name"=>"style1_width","value"=>"1024"),
            array("name"=>"style2_width","value"=>"1024"),
            array("name"=>"style3_width","value"=>"1024"),
            array("name"=>"mg_settings_displayInline","value"=>"disabled"),
            array("name"=>"mg_settings_qty_for_all","value"=>"enabled"),
            array("name"=>"mg_settings_qty_for_zeroPrice","value"=>"disabled"),
            array("name"=>"text_under_special_instructions","value"=>"*additional charges may apply and not all changes are possible"),
            array("name"=>"special_instructions_required","value"=>"no"),
            array("name"=>"use_couponsApp","value"=>"off"),
            array("name"=>"special_instructions_required","value"=>"off"),
            array("name"=>"accept_orders","value"=>"enabled"),
            array("name"=>"onePage_askforspecialinstruction","value"=>"off"),
            array("name"=>"onePage_messageforspecialinstruction","value"=>"Type your instructions here, additional charges may apply and not all changes are possible"),
            array("name"=>"jTheme_askforspecialinstruction","value"=>"off"),
            array("name"=>"jTheme_messageforspecialinstruction","value"=>"Type your instructions here, additional charges may apply and not all changes are possible"),
            array("name"=>"style2_askforspecialinstruction","value"=>"off"),
            array("name"=>"style2_messageforspecialinstruction","value"=>"Type your instructions here, additional charges may apply and not all changes are possible"),
            array("name"=>"useAlternateNames","value"=>"enabled"),
            array("name"=>"hide_category_ifnotavailable","value"=>"off"),
            array("name"=>"show_order_number","value"=>"off"),
            array("name"=>"mg_settings_minimized","value"=>"off"),
            array("name"=>"tips_selection","value"=>"10,15,20,25"),
            array("name"=>"tips_default","value"=>""),
            array("name"=>"rollout_order_number","value"=>"on"),
            array("name"=>"rollout_order_number_max","value"=>"999"),
            array("name"=>"thanks_page_wp","value"=>""),
            array("name"=>"cdn_for_images","value"=>"off"),
            array("name"=>"cdn_url","value"=>""),
        );

        foreach ($default_options as $default_option) {
            if(!isset($MooOptions[$default_option["name"]]))
                $MooOptions[$default_option["name"]]=$default_option["value"];
        }

        return $MooOptions;
    }

}
