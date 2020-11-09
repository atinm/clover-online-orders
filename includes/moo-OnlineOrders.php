<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 * @package    zaytech_OnlineOrders
 * @subpackage zaytech_OnlineOrders/includes
 * @author     Mohammed EL BANYAOUI <elbanyaoui@hotmail.com>
 */
class moo_OnlineOrders {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      moo_OnlineOrders_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Api instance to call our API
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $api;
	/**
	 * Model  instance to mange the database
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	 protected $model;

	 /**
	 * Settings
	 *
	 * @since    1.3.2
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	 protected $settings;
    /**
     * The session instance that's responsible for maintaining session data
     *
     * @since    1.3.2
     * @access   protected
     * @var      MOO_SESSION    $session   Maintains the session.
     */
    protected $session;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'moo_OnlineOrders';
		$this->version = '1.4.4';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - moo_OnlineOrders_Loader. Orchestrates the hooks of the plugin.
	 * - moo_OnlineOrders_i18n. Defines internationalization functionality.
	 * - moo_OnlineOrders_Admin. Defines all hooks for the admin area.
	 * - moo_OnlineOrders_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/moo-OnlineOrders-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/moo-OnlineOrders-i18n.php';

		/**
		 * The class responsible for defining session functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/moo-OnlineOrders-session.php';

        /**
         * The class responsible for defining all actions that need to call our servers
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/moo-OnlineOrders-CallAPI.php';

        /**
         * The class responsible for defining all actions that occur in the databse
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/moo-OnlineOrders-Model.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/moo-OnlineOrders-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/moo-OnlineOrders-public.php';

        $this->loader = new Moo_OnlineOrders_Loader();
        $this->api  = new Moo_OnlineOrders_CallAPI();
        $this->model = new Moo_OnlineOrders_Model();
        $this->session = MOO_SESSION::instance();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the moo_OnlineOrders_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Moo_OnlineOrders_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}
	/**
	 * Define the settings
	 *
	 * Uses the moo_OnlineOrders_settings class in order to get the settings from teh database
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_plugin_settings() {


	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new moo_OnlineOrders_Admin( $this->get_plugin_name(), $this->get_version(), $this->getApi(),$this->getModel() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_mysettings' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'toolbar_link_to_settings',999 );
		$this->loader->add_action( 'wpmu_new_blog', $plugin_admin, 'activate_plugin_in_network',10,6 );
		$this->loader->add_action( 'delete_blog', $plugin_admin, 'delete_plugin_in_network',10,1 );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'displayUpdateNotice' );


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Moo_OnlineOrders_Public( $this->get_plugin_name(), $this->get_version(),$this->getApi(),$this->getModel() );
        // Set session
        $this->loader->add_action( 'init', $this->session, 'myStartSession',1);

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        //allow redirection, even if my plugin starts to send output to the browser
        $this->loader->add_action( 'init', $plugin_public, 'do_output_buffer');

        //init cron jobs
        $this->loader->add_action( 'init', $plugin_public, 'moo_register_daily_jwtTokenUpdate');
        $this->loader->add_action( 'init', $plugin_public, 'moo_register_daily_inventoryImport');

        // Import inventory when hook fired
        $this->loader->add_action( 'smart_online_order_import_inventory', $plugin_public, 'moo_ImportInventory');
        // Update  jwt token
        $this->loader->add_action( 'smart_online_order_update_jwttoken', $plugin_public, 'moo_updateJwtToken');

        // Add Cart Button
        $this->loader->add_action( 'wp_footer', $plugin_public, 'addCartButton');

        //Add to cart by AJAX
        $this->loader->add_action( 'wp_ajax_moo_add_to_cart', $plugin_public, 'moo_add_to_cart');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_add_to_cart', $plugin_public, 'moo_add_to_cart');

        //Delete Item form Cart
        $this->loader->add_action( 'wp_ajax_moo_deleteItemFromcart', $plugin_public, 'moo_deleteItemFromcart');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_deleteItemFromcart', $plugin_public, 'moo_deleteItemFromcart');

        //Empty Cart
        $this->loader->add_action( 'wp_ajax_moo_emptycart', $plugin_public, 'moo_emptycart');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_emptycart', $plugin_public, 'moo_emptycart');

        //Get the total of the cart
        $this->loader->add_action( 'wp_ajax_moo_cart_getTotal', $plugin_public, 'moo_cart_getTotal');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_cart_getTotal', $plugin_public, 'moo_cart_getTotal');

        //Get the total of one line in the cart
        $this->loader->add_action( 'wp_ajax_moo_cart_getItemTotal', $plugin_public, 'moo_cart_getItemTotal');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_cart_getItemTotal', $plugin_public, 'moo_cart_getItemTotal');

        //get the cart ajax function
        $this->loader->add_action( 'wp_ajax_moo_get_cart', $plugin_public, 'moo_get_cart');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_get_cart', $plugin_public, 'moo_get_cart');

        //MODIFIERS : get limit for an modifier
        $this->loader->add_action( 'wp_ajax_moo_modifiergroup_getlimits', $plugin_public, 'moo_modifiergroup_getlimits');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_modifiergroup_getlimits', $plugin_public, 'moo_modifiergroup_getlimits');

        $this->loader->add_action( 'wp_ajax_moo_check_item_modifiers', $plugin_public, 'moo_checkItemModifiers');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_check_item_modifiers', $plugin_public, 'moo_checkItemModifiers');

        //MODIFIERS : add a modifier
        $this->loader->add_action( 'wp_ajax_moo_modifier_add', $plugin_public, 'moo_modifier_add');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_modifier_add', $plugin_public, 'moo_modifier_add');

        //MODIFIERS : delete modifier from the Cart
        $this->loader->add_action( 'wp_ajax_moo_cart_DeleteItemModifier', $plugin_public, 'moo_cart_DeleteItemModifier');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_cart_DeleteItemModifier', $plugin_public, 'moo_cart_DeleteItemModifier');

        //Checkout
        $this->loader->add_action( 'wp_ajax_moo_checkout', $plugin_public, 'moo_checkout');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_checkout', $plugin_public, 'moo_checkout');

        //Checkout : Get orders Types
        $this->loader->add_action( 'wp_ajax_moo_getodertybes', $plugin_public, 'moo_GetOrderTypes');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_getodertybes', $plugin_public, 'moo_GetOrderTypes');

		//Checkout : Sending sms and verify the code

        $this->loader->add_action( 'wp_ajax_moo_send_sms', $plugin_public, 'moo_SendVerifSMS');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_send_sms', $plugin_public, 'moo_SendVerifSMS');
		$this->loader->add_action( 'wp_ajax_moo_check_verification_code', $plugin_public, 'moo_CheckVerificationCode');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_check_verification_code', $plugin_public, 'moo_CheckVerificationCode');


        //Importing DATA, Valid only in the administration page.

        // Import Categories
        $this->loader->add_action( 'wp_ajax_moo_import_categories', $plugin_public, 'moo_ImportCategories');
        // Import Labels
        $this->loader->add_action( 'wp_ajax_moo_import_labels', $plugin_public, 'moo_ImportLabels');
        // Import Taxes
        $this->loader->add_action( 'wp_ajax_moo_import_taxes', $plugin_public, 'moo_ImportTaxes');
        // Import Iemes
        $this->loader->add_action( 'wp_ajax_moo_import_items', $plugin_public, 'moo_ImportItems');
        $this->loader->add_action( 'wp_ajax_moo_import_items_v2', $plugin_public, 'moo_ImportItemsV2');
        // Import OrderTypes
        $this->loader->add_action( 'wp_ajax_moo_import_ordertypes', $plugin_public, 'moo_ImportOrderTypes');


		/* Sync manually */
		$this->loader->add_action( 'wp_ajax_moo_update_items', $plugin_public, 'moo_UpdateItems');
		$this->loader->add_action( 'wp_ajax_moo_update_categories', $plugin_public, 'moo_UpdateCategories');
		$this->loader->add_action( 'wp_ajax_moo_update_modifiers_groups', $plugin_public, 'moo_UpdateModifiersG');
		$this->loader->add_action( 'wp_ajax_moo_update_modifiers', $plugin_public, 'moo_UpdateModifiers');
		$this->loader->add_action( 'wp_ajax_moo_update_order_types', $plugin_public, 'moo_UpdateOrderTypes');
		$this->loader->add_action( 'wp_ajax_moo_update_taxes', $plugin_public, 'moo_UpdateTaxes');

        //Get Statistics
        $this->loader->add_action( 'wp_ajax_moo_get_stats', $plugin_public, 'moo_GetStats');

        //Change the status and show/hide shipping address of an OrderType
        // $this->loader->add_action( 'wp_ajax_moo_update_ot_status', $plugin_public, 'moo_UpdateOrdertypesStatus');
        $this->loader->add_action( 'wp_ajax_moo_update_ot_showSa', $plugin_public, 'moo_UpdateOrdertypesShowSa');

        //Get list of saved OrderTypes
        $this->loader->add_action( 'wp_ajax_moo_getAllOrderTypes', $plugin_public, 'moo_getAllOrderTypes');

        //Get list of all Categories
        $this->loader->add_action( 'wp_ajax_moo_getAllCategories', $plugin_public, 'moo_getAllCategories');
        $this->loader->add_action( 'wp_ajax_moo_getOneCategory', $plugin_public, 'moo_getOneCategory');

		//Add new Order type
		$this->loader->add_action( 'wp_ajax_moo_add_ot', $plugin_public, 'moo_AddOrderType');

		//Delete a Order type
		$this->loader->add_action( 'wp_ajax_moo_delete_ot', $plugin_public, 'moo_DeleteOrderType');

		// Reorder Order Types
		$this->loader->add_action( 'wp_ajax_moo_reorder_ordertypes', $plugin_public, 'moo_ReorderOrderTypes');

		// Update Order Type
		$this->loader->add_action( 'wp_ajax_moo_update_ordertype', $plugin_public, 'moo_UpdateOrdertype');

        //Show or hide images of categories
		$this->loader->add_action( 'wp_ajax_moo_update_category_images_status', $plugin_public, 'moo_UpdateCategoryImagesStatus');

		/* Manage modifiers */
        //Change modifier Group name
		$this->loader->add_action( 'wp_ajax_moo_change_modifiergroup_name', $plugin_public, 'moo_ChangeModifierGroupName');

        //Change modifier name
        $this->loader->add_action( 'wp_ajax_moo_change_modifier_name', $plugin_public, 'moo_ChangeModifierName');

        //update modifier group status
		$this->loader->add_action( 'wp_ajax_moo_update_modifiergroup_status', $plugin_public, 'moo_UpdateModifierGroupStatus');

        //update modifier status
        $this->loader->add_action( 'wp_ajax_moo_update_modifier_status', $plugin_public, 'moo_UpdateModifierStatus');

        //Change modifier name
		$this->loader->add_action( 'wp_ajax_moo_change_category_name', $plugin_public, 'moo_ChangeCategoryName');
		//update category status
		$this->loader->add_action( 'wp_ajax_moo_update_category_status', $plugin_public, 'moo_UpdateCategoryStatus');

        // Send the feedback
        $this->loader->add_action( 'wp_ajax_moo_send_feedback', $plugin_public, 'moo_SendFeedBack');

        // Filtering Items
        $this->loader->add_action( 'wp_ajax_moo_getitemsfiltered', $plugin_public, 'moo_GetItemsFiltered');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_getitemsfiltered', $plugin_public, 'moo_GetItemsFiltered');

        // Update the quantity
        $this->loader->add_action( 'wp_ajax_moo_update_qte', $plugin_public, 'moo_UpdateQuantity');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_update_qte', $plugin_public, 'moo_UpdateQuantity');

		// Update the Special_ins for one Item
        $this->loader->add_action( 'wp_ajax_moo_update_special_ins', $plugin_public, 'moo_UpdateSpecial_ins');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_update_special_ins', $plugin_public, 'moo_UpdateSpecial_ins');

		//get the current quantity and the currant Special instruction of an item i the cart
		$this->loader->add_action( 'wp_ajax_moo_get_item_options', $plugin_public, 'moo_GetitemInCartOptions');
		$this->loader->add_action( 'wp_ajax_nopriv_moo_get_item_options', $plugin_public, 'moo_GetitemInCartOptions');

		//verify if the store is open according to bussiness hours configured in Clover
		$this->loader->add_action( 'wp_ajax_moo_store_isopen', $plugin_public, 'moo_StoreIsOpen');
		$this->loader->add_action( 'wp_ajax_nopriv_moo_store_isopen', $plugin_public, 'moo_StoreIsOpen');
        /*
         * category visibility
         */
        $this->loader->add_action( 'wp_ajax_moo_update_visiblite_category', $plugin_public, 'visibility_category');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_update_visiblite_category', $plugin_public, 'visibility_category');
        /*
         * category save image
         */
        $this->loader->add_action( 'wp_ajax_moo_save_category_image', $plugin_public, 'save_image_category');
       // $this->loader->add_action( 'wp_ajax_nopriv_moo_save_category_image', $plugin_public, 'save_image_category');
        /*
         * category new order
         */
        $this->loader->add_action( 'wp_ajax_moo_new_order_categories', $plugin_public, 'new_order_categories');
      //  $this->loader->add_action( 'wp_ajax_nopriv_moo_new_order_categories', $plugin_public, 'new_order_categories');
        /*
         * delete image category
         */
        $this->loader->add_action( 'wp_ajax_moo_delete_img_category', $plugin_public, 'delete_img_category');
       // $this->loader->add_action( 'wp_ajax_nopriv_moo_delete_img_category', $plugin_public, 'delete_img_category');
        /*
         * change name category
         */
        $this->loader->add_action( 'wp_ajax_moo_change_name_category', $plugin_public, 'change_name_category');
       // $this->loader->add_action( 'wp_ajax_nopriv_moo_change_name_category', $plugin_public, 'change_name_category');

        // New order Modifiers Group
        $this->loader->add_action( 'wp_ajax_moo_new_order_group_modifier', $plugin_public, 'moo_NewOrderGroupModifier');
        // $this->loader->add_action( 'wp_ajax_moo_new_order_group_modifier', $plugin_public, 'moo_NewOrderGroupModifier');

        // New order Modifiers Group
        $this->loader->add_action( 'wp_ajax_moo_new_order_modifier', $plugin_public, 'moo_NewOrderModifier');
      //  $this->loader->add_action( 'wp_ajax_moo_new_order_modifier', $plugin_public, 'moo_NewOrderModifier');

        /*
         * Reorder items
         */
        $this->loader->add_action( 'wp_ajax_moo_reorder_items', $plugin_public, 'moo_reorder_items');

        /*
         * Item's images
         */

		$this->loader->add_action( 'wp_ajax_moo_get_items_with_images', $plugin_public, 'moo_getItemWithImages');
		$this->loader->add_action( 'wp_ajax_moo_save_items_with_images', $plugin_public, 'moo_saveItemWithImages');
		$this->loader->add_action( 'wp_ajax_moo_save_items_description', $plugin_public, 'moo_saveItemDescription');
		$this->loader->add_action( 'wp_ajax_moo_set_default_image', $plugin_public, 'moo_setDefaultImage');
		$this->loader->add_action( 'wp_ajax_moo_enable_items_with_images', $plugin_public, 'moo_enableItemsWithImages');

        /*
         * Custmer login & sign-up
         */

        $this->loader->add_action( 'wp_ajax_moo_customer_login', $plugin_public, 'moo_CustomerLogin');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_customer_login', $plugin_public, 'moo_CustomerLogin');

        $this->loader->add_action( 'wp_ajax_moo_customer_fblogin', $plugin_public, 'moo_CustomerFbLogin');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_customer_fblogin', $plugin_public, 'moo_CustomerFbLogin');

        $this->loader->add_action( 'wp_ajax_moo_customer_signup', $plugin_public, 'moo_CustomerSignup');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_customer_signup', $plugin_public, 'moo_CustomerSignup');

        $this->loader->add_action( 'wp_ajax_moo_customer_resetpassword', $plugin_public, 'moo_ResetPassword');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_customer_resetpassword', $plugin_public, 'moo_ResetPassword');

        $this->loader->add_action( 'wp_ajax_moo_customer_getAddresses', $plugin_public, 'moo_GetAddresses');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_customer_getAddresses', $plugin_public, 'moo_GetAddresses');

        $this->loader->add_action( 'wp_ajax_moo_customer_addAddress', $plugin_public, 'moo_AddAddress');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_customer_addAddress', $plugin_public, 'moo_AddAddress');

        $this->loader->add_action( 'wp_ajax_moo_customer_deleteAddresses', $plugin_public, 'moo_DeleteAddresses');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_customer_deleteAddresses', $plugin_public, 'moo_DeleteAddresses');

        $this->loader->add_action( 'wp_ajax_moo_customer_setDefaultAddresses', $plugin_public, 'moo_setDefaultAddresses');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_customer_setDefaultAddresses', $plugin_public, 'moo_setDefaultAddresses');

        $this->loader->add_action( 'wp_ajax_moo_customer_updateAddresses', $plugin_public, 'moo_updateAddresses');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_customer_updateAddresses', $plugin_public, 'moo_updateAddresses');

        $this->loader->add_action( 'wp_ajax_moo_customer_deleteCreditCard', $plugin_public, 'moo_DeleteCreditCard');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_customer_deleteCreditCard', $plugin_public, 'moo_DeleteCreditCard');
		/*
        * Coupons apply on checkout page
        */

        $this->loader->add_action( 'wp_ajax_moo_coupon_apply', $plugin_public, 'moo_CouponApply');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_coupon_apply', $plugin_public, 'moo_CouponApply');

        $this->loader->add_action( 'wp_ajax_moo_coupon_remove', $plugin_public, 'moo_CouponRemove');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_coupon_remove', $plugin_public, 'moo_CouponRemove');

        /*
         *  Get Opening hours
         */
        $this->loader->add_action( 'wp_ajax_moo_opening_hours', $plugin_public, 'moo_getOpeningHours');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_opening_hours', $plugin_public, 'moo_getOpeningHours');

		/*
		 * Suncy handle
		 */
		$this->loader->add_action( 'admin_post_moo_sync', $plugin_public, 'moo_SyncHandle');
		$this->loader->add_action( 'admin_post_nopriv_moo_sync', $plugin_public, 'moo_SyncHandle');

		/**
         * Plugin upgrade wp_upe_upgrade_completed
         */
        $this->loader->add_action( 'wp_upe_upgrade_completed', $plugin_public, 'moo_pluginUpdated',1,2);

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Moo_OnlineOrders_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    /**
     * @return string
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }


}
