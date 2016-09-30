<?php

/**
 * Fired during plugin activation
 *
 * @link       http://merchantechapps.com
 * @since      1.0.0
 *
 * @package    Moo_OnlineOrders
 * @subpackage Moo_OnlineOrders/includes
 */

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
	public static function activate() {
        // Add option to db
        update_option("moo_first_use", "true");
        // Install DB
        global $wpdb;
       $wpdb->hide_errors();

/*      -- -----------------------------------------------------
        -- Table `item_group`
        -- -----------------------------------------------------
*/
        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item_group` (
                          `_id` INT NOT NULL AUTO_INCREMENT,
                          `uuid` VARCHAR(100) NOT NULL,
                          `name` VARCHAR(100) NULL,
                          PRIMARY KEY (`_id`),
                          UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC))
                        ENGINE = InnoDB;");


/*
        -- -----------------------------------------------------
        -- Table `item--
        --------------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item` (
                      `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(45) NOT NULL ,
                      `name` VARCHAR(100) NULL ,
                      `alternate_name` VARCHAR(100) NULL ,
                      `description` VARCHAR(255) NULL ,
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
                      PRIMARY KEY (`_id`),
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC),
                      INDEX `{$wpdb->prefix}fk_item_item_group_idx` (`item_group_uuid` ASC),
                      CONSTRAINT `{$wpdb->prefix}fk_item_item_group`
                        FOREIGN KEY (`item_group_uuid`)
                        REFERENCES `{$wpdb->prefix}moo_item_group` (`uuid`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION)
                    ENGINE = InnoDB;");
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
                      `p_name` VARCHAR(100) NULL ,
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
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC)
                      )
                    ENGINE = InnoDB;");

/*
-- -----------------------------------------------------
-- Table `category`
-- -----------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_category` (
                       `_id` INT NOT NULL AUTO_INCREMENT,
                      `uuid` VARCHAR(100) NOT NULL ,
                      `name` VARCHAR(45) NULL ,
                      `sort_order` INT NULL ,
                      `show_by_default` INT(1) NOT NULL DEFAULT '1' ,
                      `items` TEXT NULL ,
                      `image_url` VARCHAR(255) NULL,
                      `alternate_name` VARCHAR(100) NULL,
                      PRIMARY KEY (`_id`)  ,
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC)  )
                    ENGINE = InnoDB;");

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
                        ON UPDATE NO ACTION)
                    ENGINE = InnoDB;");


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
                        ON UPDATE NO ACTION)
                    ENGINE = InnoDB;");

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
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC))
                    ENGINE = InnoDB;");


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
                        ON UPDATE NO ACTION)
                    ENGINE = InnoDB;");


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
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC))
                    ENGINE = InnoDB;");


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
                      UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC))
                    ENGINE = InnoDB;");

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
                        UNIQUE( `tax_rate_uuid`, `item_uuid`))
                    ENGINE = InnoDB;");


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
                        UNIQUE( `tag_uuid`, `item_uuid`))
                    ENGINE = InnoDB;");

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
                        UNIQUE( `option_uuid`, `item_uuid`))
                    ENGINE = InnoDB;");

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
                            UNIQUE(`item_id`,`group_id`))
                        ENGINE = InnoDB;");

/*
-- -----------------------------------------------------
-- Table `item_order`
-- -----------------------------------------------------
*/

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}moo_item_order` (
                          `_id` INT NOT NULL AUTO_INCREMENT,
                          `item_uuid` VARCHAR(100) NOT NULL,
                          `order_uuid` VARCHAR(100) NOT NULL,
                          `quantity` VARCHAR(100) NOT NULL,
                          `modifiers` VARCHAR(255) NOT NULL,
                          `special_ins` VARCHAR(255) NOT NULL,
                          PRIMARY KEY (`_id`, `item_uuid`, `order_uuid`),
                          INDEX `{$wpdb->prefix}fk_order_has_items_idx1` (`order_uuid` ASC) ,
                          INDEX `{$wpdb->prefix}fk_order_has_items_idx2` (`item_uuid` ASC)  ,
                          CONSTRAINT `{$wpdb->prefix}fk_order_has_items_item`
                            FOREIGN KEY (`item_uuid`)
                            REFERENCES `{$wpdb->prefix}moo_item` (`uuid`)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                          CONSTRAINT `{$wpdb->prefix}fk_order_has_items_order`
                            FOREIGN KEY (`order_uuid`)
                            REFERENCES `{$wpdb->prefix}moo_order` (`uuid`)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                            UNIQUE(`item_uuid`,`order_uuid`,`modifiers`))
                        ENGINE = InnoDB;");

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
                          PRIMARY KEY (`ot_uuid`))
                        ENGINE = InnoDB;");

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
                                ON UPDATE NO ACTION)
                        ENGINE = InnoDB;");

// Add the page :

        //post status and options
        $post_store = array(
            'comment_status' => 'closed',
            'ping_status' =>  'closed' ,
            'post_author' => 1,
            'post_name' => 'Store',
            'post_status' => 'publish' ,
            'post_title' => 'Order Online',
            'post_type' => 'page',
            'post_content' => '[moo_all_items]'
        );
        $post_checkout = array(
            'comment_status' => 'closed',
            'ping_status' =>  'closed' ,
            'post_author' => 1,
            'post_name' => 'Checkout',
            'post_status' => 'publish' ,
            'post_title' => 'Checkout',
            'post_type' => 'page',
            'post_content' => '[moo_checkout]'
        );
         $post_cart = array(
            'comment_status' => 'closed',
            'ping_status' =>  'closed' ,
            'post_author' => 1,
            'post_name' => 'Cart',
            'post_status' => 'publish' ,
            'post_title' => 'Cart',
            'post_type' => 'page',
            'post_content' => '[moo_cart]'
        );
        // Save the version of the plugin in the Database
         update_option('moo_onlineOrders_version', '121');
        //insert page and save the id
        $store_page_id    =  wp_insert_post( $post_store, false );
        $checkout_page_id =  wp_insert_post( $post_checkout, false );
        $cart_page_id     =  wp_insert_post( $post_cart, false );

        //save the id in the database
        update_option( 'moo_store_page', $store_page_id );
        update_option( 'moo_checkout_page', $checkout_page_id );
        update_option( 'moo_cart_page', $cart_page_id );

        $defaultOptions = get_option( 'moo_settings' );

        if( !isset($defaultOptions["default_style"]) || $defaultOptions["default_style"] == "" || $defaultOptions["default_style"] == "style2" ) $defaultOptions["default_style"] = "style1";
        if( !isset($defaultOptions["hours"]) || $defaultOptions["hours"] == "") $defaultOptions["hours"] = "business";
        if( !isset($defaultOptions["payment_cash"]) || $defaultOptions["payment_cash"] == "") $defaultOptions["payment_cash"] = "on";
        if( !isset($defaultOptions["payment_cash_delivery"]) || $defaultOptions["payment_cash_delivery"] == "") $defaultOptions["payment_cash_delivery"] = "on";
        if( !isset($defaultOptions["scp"]) || $defaultOptions["scp"] == "") $defaultOptions["scp"] = "on";

        update_option('moo_settings', $defaultOptions );
            
	}

}
