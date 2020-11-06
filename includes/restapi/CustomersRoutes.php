<?php
/**
 * Created by Mohammed EL BANYAOUI.
 * Sync route to handle all requests to sync the inventory with Clover
 * User: Smart MerchantApps
 * Date: 3/5/2019
 * Time: 12:23 PM
 */
require_once "BaseRoute.php";

class CustomersRoutes extends BaseRoute {
    /**
     * The model of this plugin (For all interaction with the DATABASE ).
     * @access   private
     * @var      Moo_OnlineOrders_Model    Object of functions that call the Database pr the API.
     */
    private $model;

    /**
     * The model of this plugin (For all interaction with the DATABASE ).
     * @access   private
     * @var Moo_OnlineOrders_CallAPI
     */
    private $api;

    /**
     * @var array
     */
    private $pluginSettings;

    /**
     * SyncRoutes constructor.
     *
     */
    public function __construct($model, $api){
        $this->model          = $model;
        $this->api            = $api;
        $this->pluginSettings = (array) get_option("moo_settings");
    }


    // Register our routes.
    public function register_routes(){

    }

}