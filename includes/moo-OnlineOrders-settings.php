<?php

/**
 * Define the plugin settings
 *
 * Loads and defines the settings this plugin
 * so that it is ready for translation.
 *
 * @link       http://zaytechapps.com
 * @since      1.0.0
 *
 * @package    Moo_OnlineOrders
 * @subpackage Moo_OnlineOrders/includes
 */


class Moo_OnlineOrders_settings {

	/**
	 * The name of teh option in wordpress databse.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $domain    The domain identifier for this plugin.
	 */
	private $name = 'moo_settings';
	private $settings;

    /**
     * Moo_OnlineOrders_settings constructor.
     */
    public function __construct() {
        $this->load_settings();
    }


    /**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_settings() {

        $this->settings = (array) get_option('moo_settings');

	}

    /**
     * @return array | null
     */
    public function getSettings() {
        return $this->settings;
    }



}
