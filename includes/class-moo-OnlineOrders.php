<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://merchantech.us
 * @since      1.0.0
 *
 * @package    merchantech_OnlineOrders
 * @subpackage merchantech_OnlineOrders/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    merchantech_OnlineOrders
 * @subpackage merchantech_OnlineOrders/includes
 * @author     Mohammed EL BANYAOUI <elbanyaoui@hotmail.com>
 */
class moo_OnlineOrders {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      merchantech_OnlineOrders_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		$this->version = '1.0.1';

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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-moo-OnlineOrders-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-moo-OnlineOrders-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-moo-OnlineOrders-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-moo-OnlineOrders-public.php';

		$this->loader = new Moo_OnlineOrders_Loader();

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
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Moo_OnlineOrders_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Moo_OnlineOrders_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        // Set session
        $this->loader->add_action( 'init', $plugin_public, 'myStartSession',1);


        //allow redirection, even if my plugin starts to send output to the browser
        $this->loader->add_action( 'init', $plugin_public, 'do_output_buffer');

        // Add Cart Button
        $this->loader->add_action( 'wp_footer', $plugin_public, 'addCartButton');

        //Add to cart by AJAX
        $this->loader->add_action( 'wp_ajax_moo_add_to_cart', $plugin_public, 'moo_add_to_cart');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_add_to_cart', $plugin_public, 'moo_add_to_cart');

        //inc quantity btn
        $this->loader->add_action( 'wp_ajax_moo_cart_incQuantity', $plugin_public, 'moo_cart_incQuantity');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_cart_incQuantity', $plugin_public, 'moo_cart_incQuantity');
        //dec quantity btn
        $this->loader->add_action( 'wp_ajax_moo_cart_decQuantity', $plugin_public, 'moo_cart_decQuantity');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_cart_decQuantity', $plugin_public, 'moo_cart_decQuantity');

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

        //Importing DATA, Valid only in the administration page.

        // Import Categories
        $this->loader->add_action( 'wp_ajax_moo_import_categories', $plugin_public, 'moo_ImportCategories');
        // Import Labels
        $this->loader->add_action( 'wp_ajax_moo_import_labels', $plugin_public, 'moo_ImportLabels');
        // Import Taxes
        $this->loader->add_action( 'wp_ajax_moo_import_taxes', $plugin_public, 'moo_ImportTaxes');
        // Import Iemes
        $this->loader->add_action( 'wp_ajax_moo_import_items', $plugin_public, 'moo_ImportItems');
        // Import OrderTypes
        $this->loader->add_action( 'wp_ajax_moo_import_ordertypes', $plugin_public, 'moo_ImportOrderTypes');

        //Get Statistics
        $this->loader->add_action( 'wp_ajax_moo_get_stats', $plugin_public, 'moo_GetStats');
        //Change the status of an OrderType
        $this->loader->add_action( 'wp_ajax_moo_update_ot_status', $plugin_public, 'moo_UpdateOrdertypesStatus');
        //Get list of saved OrderTypes
        $this->loader->add_action( 'wp_ajax_moo_getAllOrderTypes', $plugin_public, 'moo_getAllOrderTypes');

		//Add new Order type
		$this->loader->add_action( 'wp_ajax_moo_add_ot', $plugin_public, 'moo_AddOrderType');

		//Delete a Order type
		$this->loader->add_action( 'wp_ajax_moo_delete_ot', $plugin_public, 'moo_DeleteOrderType');

        // Send the feedback
        $this->loader->add_action( 'wp_ajax_moo_send_feedback', $plugin_public, 'moo_SendFeedBack');

        // Filtering Items
        $this->loader->add_action( 'wp_ajax_moo_getitemsfiltered', $plugin_public, 'moo_GetItemsFiltered');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_getitemsfiltered', $plugin_public, 'moo_GetItemsFiltered');

         // Get Item's Modifiers
        $this->loader->add_action( 'wp_ajax_moo_getitemmodifiers', $plugin_public, 'moo_ModifiersForAnItem');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_getitemmodifiers', $plugin_public, 'moo_ModifiersForAnItem');

        // Update the quantity
        $this->loader->add_action( 'wp_ajax_moo_update_qte', $plugin_public, 'moo_UpdateQuantity');
        $this->loader->add_action( 'wp_ajax_nopriv_moo_update_qte', $plugin_public, 'moo_UpdateQuantity');
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
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
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

}
