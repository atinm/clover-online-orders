<?php
/**
 * This Class will handle our first version of the rest api
 * Created by Mohammed EL BANYAOUI.
 */

// Require classes
require_once plugin_dir_path( dirname( __FILE__ ) ) . "includes/restapi/SyncRoutes.php";
require_once plugin_dir_path( dirname( __FILE__ ) ) . "includes/restapi/DashboardRoutes.php";
require_once plugin_dir_path( dirname( __FILE__ ) ) . "includes/restapi/CustomersRoutes.php";

//require model and api only if they no exist
if ( ! class_exists( 'moo_OnlineOrders_Model' ) ) {
    /**
     * The class responsible for defining all actions that occur in the databse
     * side of the site.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/moo-OnlineOrders-Model.php';
}
if ( ! class_exists( 'moo_OnlineOrders_CallAPI' ) ) {
    /**
     * The class responsible for defining all actions that need to call our servers
     * side of the site.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/moo-OnlineOrders-CallAPI.php';
}

class Moo_OnlineOrders_Restapi
{

    /*
     * isProduction : it's a flag to hide all php notices in production mode
     */
    private $isProduction = true;

    /**
     * The namespace and the version of the api
     * @var string
     */
    private $namespace = 'moo-clover/v1';

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
     * The SESSION
     * @since    1.3.2
     * @access   private
     * @var MOO_SESSION
     */
    private $session;

    /**
     * The class that will handle all sync routes
     * @since    1.3.3
     * @access   private
     * @var SyncRoutes
     */
    private $syncRoutes;
    /**
     * The class that will handle all sync routes
     * @since    1.3.3
     * @access   private
     * @var DashboardRoutes
     */
    private $dashRoutes;
    /**
     * The class that will handle all customers management routes
     * @since    1.4.3
     * @access   private
     * @var CustomersRoutes
     */
    private $customersRoutes;

    /**
     * use or not alternateNames
     * @var bool
     */
    private $useAlternateNames;

    // Here initialize our namespace and resource name.
    public function __construct() {

        $this->model    =     new moo_OnlineOrders_Model();
        $this->api      =     new moo_OnlineOrders_CallAPI();
        $this->session  =     MOO_SESSION::instance();
        $this->syncRoutes = new SyncRoutes($this->model, $this->api);
        $this->dashRoutes = new DashboardRoutes($this->model, $this->api);
        $this->customersRoutes = new CustomersRoutes($this->model, $this->api);

        if($this->isProduction)
            error_reporting(0);

        if(isset($this->api->settings["useAlternateNames"])){
            $this->useAlternateNames = ($this->api->settings["useAlternateNames"] !== "disabled");
        } else {
            $this->useAlternateNames = true;
        }

    }
    // Register our routes.
    public function register_routes() {

        //register v2 routes

        $this->syncRoutes->register_routes();
        $this->dashRoutes->register_routes();
        $this->customersRoutes->register_routes();

        //get categories route
        register_rest_route( $this->namespace, '/categories', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getCategories' )
            )
        ) );

        //get items per category route
        register_rest_route( $this->namespace, '/categories/(?P<cat_id>[a-zA-Z0-9-]+)/items', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getItemsPerCategory' )
            )
        ) );
        //get the most Purchased items
        register_rest_route( $this->namespace, '/items/most_purchase', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getMostPurchasedItems' )
            )
        ) );

        //get item detail
        register_rest_route( $this->namespace, '/items/(?P<item_id>[a-zA-Z0-9-]+)', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getItemsDetail' )
            )
        ) );


        /* Tha cart routes */
        //get the cart
        register_rest_route( $this->namespace, '/cart', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getCart' )
            )
        ) );
        //add item to cart
        register_rest_route( $this->namespace, '/cart', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'addItemToCart' )
            )
        ) );
        //update item

        //remove item
        register_rest_route( $this->namespace, '/cart/remove', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'removeFromCart' )
            )
        ) );
        //update special instruction
        register_rest_route( $this->namespace, '/cart/update', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'updateSpecialInstructionforItem' )
            )
        ) );
        //update quantity
        register_rest_route( $this->namespace, '/cart/qty_update', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'updateQtyforItem' )
            )
        ) );

        /* The Clean Inventory functions */
        // Clean Items
        // the url forms is : /clean/items/:per_page/:page
        register_rest_route( $this->namespace, '/clean/items/(?P<per_page>[0-9]+)/(?P<page>[0-9]+)', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'cleanItems' )
            )
        ) );
        register_rest_route( $this->namespace, '/clean/categories/(?P<per_page>[0-9]+)/(?P<page>[0-9]+)', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'cleanCategories' )
            )
        ) );
        register_rest_route( $this->namespace, '/clean/modifier_groups/(?P<per_page>[0-9]+)/(?P<page>[0-9]+)', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'cleanModifierGroups' )
            )
        ) );
        register_rest_route( $this->namespace, '/clean/modifiers/(?P<per_page>[0-9]+)/(?P<page>[0-9]+)', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'cleanModifiers' )
            )
        ) );
        register_rest_route( $this->namespace, '/clean/tax_rates/(?P<per_page>[0-9]+)/(?P<page>[0-9]+)', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'cleanTaxRates' )
            )
        ) );
        register_rest_route( $this->namespace, '/clean/order_types/(?P<per_page>[0-9]+)/(?P<page>[0-9]+)', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'cleanOrderTypes' )
            )
        ) );

        /*
        * Tools functions
        */
        //Update images url to https

        register_rest_route( $this->namespace, '/tools/https_for_images', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'httpsForImages' )
            )
        ) );

        register_rest_route( $this->namespace, '/tools/http_for_images', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'httpForImages' )
            )
        ) );

        // Get Fresh version for blackout times
        register_rest_route( $this->namespace, '/tools/update_blackouts', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'updateBlackouts' )
            )
        ) );

        /*
         * Inventory importing & syncynig functions
         */
        register_rest_route( $this->namespace, '/inventory/categories/(?P<cat_id>[a-zA-Z0-9-]+)', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'importCategory' )
            )
        ) );


        // theme settings
        // get the store interface settings
        register_rest_route( $this->namespace, '/theme_settings/(?P<theme_name>[a-zA-Z0-9-]+)', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getThemeSettings' )
            )
        ) );
        register_rest_route( $this->namespace, '/theme_settings/(?P<theme_name>[a-zA-Z0-9-]+)', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'saveThemeSettings' ),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ) );
        // modifiers settings
        // get the store interface settings
        register_rest_route( $this->namespace, '/mg_settings', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getModifierGroupsSettings' )
            )
        ) );

        //Search

        //get item detail
        register_rest_route( $this->namespace, '/search/(?P<word>(.)+)', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'search' )
            )
        ) );
        /*
         * Dashboard functions
         */
        // Get Installed themes
        register_rest_route( $this->namespace, '/dashboard/installed_themes', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getInstalledThemes' )
            )
        ) );
        register_rest_route( $this->namespace, '/dashboard/installed_themes', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'activeTheme' ),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ) );
        register_rest_route( $this->namespace, '/dashboard/all_themes', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getAllThemes' )
            )
        ) );

        //Customer account functions

        register_rest_route( $this->namespace, '/customers', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getCustomer' )
            )
        ) );
        register_rest_route( $this->namespace, '/customers', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'updateCustomer' )
            )
        ) );
        register_rest_route( $this->namespace, '/customers/password', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'updateCustomerPassword' )
            )
        ) );
        register_rest_route( $this->namespace, '/customers/orders', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getOrdersForCustomer' )
            )
        ) );
        register_rest_route( $this->namespace, '/customers/favorites', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getCustomerFavorites' )
            )
        ) );
        register_rest_route( $this->namespace, '/customers/addresses', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getCustomerAddressess' )
            )
        ) );
        register_rest_route( $this->namespace, '/customers/orders/(?P<uuid>(.)+)/reorder', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'reOrder' )
            )
        ) );
        // Change your api key
        register_rest_route( $this->namespace, '/dashboard/change_api_key', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'changeApiKey' ),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ) );

    }
    public function permissionCheck( $request ) {
        return current_user_can( 'manage_options' );
    }
    public function getCategories( $request )
    {
        $params = $request->get_params();
        $response = array();
        $cats = $this->model->getCategories();
        $settings = (array) get_option("moo_settings");

        // Get categories times
        $counter = $this->model->getCategoriesWithCustomHours();
        if(isset($counter->nb) && $counter->nb > 0 ) {
            $HoursResponse = $this->api->getMerchantCustomHoursStatus("categories");
            if( $HoursResponse ){
                $merchantCustomHoursStatus = json_decode($HoursResponse,true);
                $merchantCustomHours = array_keys($merchantCustomHoursStatus);
            } else {
                $merchantCustomHoursStatus = array();
                $merchantCustomHours = array();
            }
        } else {
                $merchantCustomHoursStatus = array();
                $merchantCustomHours = array();
        }

        if($cats) {
            foreach ($cats as $cat) {

                if($cat->show_by_default == "1") {
                    $c = array(
                        "uuid"=>$cat->uuid,
                        "name"=> "",
                        "alternate_name" => stripslashes($cat->alternate_name),
                        "description"   => (isset($cat->description))?stripslashes($cat->description):"",
                        "custoum_hours"   => (isset($cat->custoum_hours))?stripslashes($cat->custoum_hours):"",
                        "image_url"=>$cat->image_url
                    );
                    if($this->useAlternateNames && isset($cat->alternate_name) && $cat->alternate_name!==""){
                        $c["name"]=stripslashes($cat->alternate_name);
                    } else {
                        $c["name"]=stripslashes($cat->name);
                    }

                    if(count($merchantCustomHours) && isset($cat->time_availability) && $cat->time_availability === "custom") {
                        $catAvailable = true;
                        if(isset($cat->custom_hours) && !empty($cat->custom_hours)) {
                            if(in_array($cat->custom_hours, $merchantCustomHours)){
                                $catAvailable = $merchantCustomHoursStatus[$cat->custom_hours] === "open";

                                if( ! $catAvailable){
                                    if(isset($settings["hide_category_ifnotavailable"]) && $settings["hide_category_ifnotavailable"]==="on") {
                                        continue;
                                    }
                                }
                            }
                        }
                    } else {
                        $catAvailable = true;
                    }
                   // var_dump($catAvailable );

                    $c["available"] = $catAvailable;
                    if(isset($params["expand"])) {
                        $c['items'] = array();
                        $limit = null;
                        $count = 0;
                        if($params["expand"] == 'five_items') {
                            $limit = 5;
                        }
                        if($cat->items != "") {
                            $items_uuids = explode(",",$cat->items);
                            $itemsString  = "(";
                            foreach ($items_uuids as $items_uuid) {
                                if($items_uuid!==""){
                                    $itemsString .= "'".$items_uuid."',";
                                }
                            }
                            $itemsString = substr($itemsString, 0, strlen($itemsString)-1);
                            $itemsString .= ")";
                            if (strlen($itemsString)>1) {
                                if(isset($settings["track_stock_hide_items"]) && $settings["track_stock_hide_items"] === "on"){
                                    $items = $this->model->getVisibleItemsByCategory($itemsString,null);
                                } else {
                                    $items = $this->model->getVisibleItemsByCategory($itemsString,$limit);
                                }
                            } else {
                                $items = array();
                            }

                            $track_stock = $this->api->getTrackingStockStatus();
                            if($track_stock == true)
                                $itemStocks = $this->api->getItemStocks();
                            else
                                $itemStocks = false;
                            foreach ($items as $item) {
                                //if($items_uuid == "") continue;
                                //$item = $this->model->getItem($items_uuid);
                                if(!$item)
                                    continue;

                                $final_item = array();

                                //Check the stock
                                if($track_stock)
                                    $itemStock = self::getItemStock($itemStocks,$item->uuid);
                                else
                                    $itemStock = false;


                                if($item->outofstock == 1 || ($track_stock == true && $itemStock != false && isset($itemStock->stockCount)  && $itemStock->stockCount < 1))
                                {
                                    if(isset($settings["track_stock_hide_items"]) && $settings["track_stock_hide_items"] === "on"){
                                        continue;
                                    }
                                    $final_item['stockCount'] = "out_of_stock";
                                } else {
                                    if(isset($itemStock->stockCount))
                                        $final_item['stockCount'] = $itemStock->stockCount;
                                    else
                                        $final_item['stockCount'] = ($track_stock)?"tracking_stock":"not_tracking_stock";
                                }
                                $final_item["uuid"]=$item->uuid;
                                $final_item["name"]           =   stripslashes($item->name);
                                $final_item["alternate_name"] =   stripslashes($item->alternate_name);
                                $final_item["description"]    =   $item->description;
                                $final_item["price"]          =   $item->price;
                                $final_item["price_type"]     =   $item->price_type;
                                $final_item["unit_name"]      =   $item->unit_name;
                                $final_item["sort_order"]     =   intval($item->sort_order);
                                $final_item["has_modifiers"]  =   ($this->model->itemHasModifiers($item->uuid)->total>0)?true:false;
                                $final_item["image"]= $this->model->getDefaultItemImage($item->uuid);

                                if($this->useAlternateNames  && isset($item->alternate_name) && $item->alternate_name!==""){
                                    $final_item["name"]=stripslashes($item->alternate_name);
                                } else {
                                    $final_item["name"]=stripslashes($item->name);
                                }
                                array_push($c['items'],$final_item);

                                if(isset($settings["track_stock_hide_items"]) && $settings["track_stock_hide_items"] === "on"){
                                    $count++;
                                    if($count === $limit){
                                        break;
                                    }
                                }
                            }
                           // usort($c['items'],array('Moo_OnlineOrders_Restapi','moo_sort_items'));
                        }
                    }
                    array_push($response,$c);
                }
            }
        }
        // Return all of our post response data.
        return $response;
    }
    public function getItemsPerCategory( $request )
    {
        $settings = (array) get_option("moo_settings");
        $response = array();
        //var_dump($request["cat_id"]);
        if ( !isset($request["cat_id"]) || empty( $request["cat_id"] ) ) {
            return new WP_Error( 'category_id_required', 'Category id not found', array( 'status' => 404 ) );
        }
        $category = $this->model->getCategory($request["cat_id"]);
        if($category === null || $category->show_by_default != "1")
            return new WP_Error( 'category_not_found', 'Category not found', array( 'status' => 404 ) );

        $response["uuid"]           = $category->uuid;
        $response["name"]           = stripslashes($category->name);
        $response["alternate_name"] = stripslashes($category->alternate_name);
        $response["description"]    = stripslashes($category->description);
        $response["image_url"]      = $category->image_url;
        $response["items"]= array();

        if($this->useAlternateNames && isset($category->alternate_name) && $category->alternate_name!==""){
            $response["name"]=stripslashes($category->alternate_name);
        } else {
            $response["name"]=stripslashes($category->name);
        }

        // Get categories times
        if(isset($category->time_availability) && $category->time_availability === "custom") {
            $catAvailable = true;
            $HoursResponse = $this->api->getMerchantCustomHoursStatus("categories");
            if( $HoursResponse ){
                $merchantCustomHoursStatus = json_decode($HoursResponse,true);
                $merchantCustomHours = array_keys($merchantCustomHoursStatus);
                if(isset($category->custom_hours) && !empty($category->custom_hours)) {
                    if(in_array($category->custom_hours, $merchantCustomHours)){
                        $catAvailable = $merchantCustomHoursStatus[$category->custom_hours] === "open";

                        if( ! $catAvailable){
                            if(isset($settings["hide_category_ifnotavailable"]) && $settings["hide_category_ifnotavailable"]==="on") {
                                return new WP_Error( 'category_not_available', 'Category not available', array( 'status' => 404 ) );
                            }
                        }
                    }
                }
            } else {
                $catAvailable = true;
            }
        } else {
            $catAvailable = true;
        }

        $response["available"] = $catAvailable;

        if($category->items !="") {

            //Get items
            $limit = null;
            $items_uuids = explode(",",$category->items);
            $itemsString  = "(";
            foreach ($items_uuids as $items_uuid) {
                if($items_uuid!==""){
                    $itemsString .= "'".$items_uuid."',";
                }
            }
            $itemsString = substr($itemsString, 0, strlen($itemsString)-1);
            $itemsString .= ")";
            if (strlen($itemsString)>1) {
                $items = $this->model->getVisibleItemsByCategory($itemsString,$limit);
            } else {
                $items = array();
            }

            //Check Stock
            $track_stock = $this->api->getTrackingStockStatus();
            if($track_stock == true)
                $itemStocks = $this->api->getItemStocks();
            else
                $itemStocks = false;
            //return items
            foreach ($items as $item) {
                if(!$item)
                    continue;

                $final_item = array();

                //Check the stock
                if($track_stock)
                    $itemStock = self::getItemStock($itemStocks,$item->uuid);
                else
                    $itemStock = false;


                if($item->outofstock == 1 || ($track_stock == true && $itemStock != false && isset($itemStock->stockCount)  && $itemStock->stockCount < 1))
                {
                    if(isset($settings["track_stock_hide_items"]) && $settings["track_stock_hide_items"] === "on"){
                        continue;
                    }
                    $final_item['stockCount'] = "out_of_stock";
                }
                else
                {
                    if(isset($itemStock->stockCount))
                        $final_item['stockCount'] = $itemStock->stockCount;
                    else
                        $final_item['stockCount'] = ($track_stock)?"tracking_stock":"not_tracking_stock";
                }
                $final_item["uuid"]             =   $item->uuid;
                $final_item["name"]             =   stripslashes($item->name);
                $final_item["alternate_name"]   =   stripslashes($item->alternate_name);
                $final_item["description"]      =   stripslashes($item->description);
                $final_item["price"]            =   $item->price;
                $final_item["price_type"]       =   $item->price_type;
                $final_item["unit_name"]        =   $item->unit_name;
                $final_item["sort_order"]       =   intval($item->sort_order);
                $final_item["has_modifiers"]    =   ($this->model->itemHasModifiers($item->uuid)->total>0)?true:false;
                $final_item["image"]            =   $this->model->getDefaultItemImage($item->uuid);

                if($this->useAlternateNames && isset($item->alternate_name) && $item->alternate_name!==""){
                    $final_item["name"]=stripslashes($item->alternate_name);
                } else {
                    $final_item["name"]=stripslashes($item->name);
                }


                array_push($response['items'],$final_item);
            }
           // usort($response["items"], array('Moo_OnlineOrders_Restapi','moo_sort_items'));

        }
        // Return all of our post response data.
        return $response;
    }
    public function search( $request ) {
        $response = array();
        $settings = (array) get_option("moo_settings");
        if ( !isset($request["word"]) || empty( $request["word"] ) ) {
            return new WP_Error( 'keyword_required', 'Keyword not found', array( 'status' => 404 ) );
        }
        $response["keyworld"] = urldecode( $request["word"] );

        $response["items"]= array();
        $track_stock = $this->api->getTrackingStockStatus();
        if($track_stock == true)
            $itemStocks = $this->api->getItemStocks();
        else
            $itemStocks = false;

        $items = $this->model->getItemsBySearch($response["keyworld"]);

        foreach ($items as $item) {
            $final_item = array();
            if(!$item) continue;
            //Check if the item if it's disabled
            if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE') continue;

            //Check the stock
            if($track_stock)
                $itemStock = self::getItemStock($itemStocks,$item->uuid);
            else
                $itemStock = false;


            if($item->outofstock == 1 || ($track_stock == true && $itemStock != false && isset($itemStock->stockCount)  && $itemStock->stockCount < 1))
            {
                if(isset($settings["track_stock_hide_items"]) && $settings["track_stock_hide_items"] === "on"){
                    continue;
                }
                $final_item['stockCount'] = "out_of_stock";
            }
            else
            {
                if(isset($itemStock->stockCount))
                    $final_item['stockCount'] = $itemStock->stockCount;
                else
                    $final_item['stockCount'] = ($track_stock)?"tracking_stock":"not_tracking_stock";
            }
            $final_item["uuid"]=$item->uuid;
            $final_item["name"]             =   stripslashes($item->name);
            $final_item["alternate_name"]   =   stripslashes($item->alternate_name);
            $final_item["description"]      =   stripslashes($item->description);
            $final_item["price"]        =   $item->price;
            $final_item["price_type"]   =   $item->price_type;
            $final_item["unit_name"]    =   $item->unit_name;
            $final_item["sort_order"]   =   intval($item->sort_order);
            $final_item["has_modifiers"]=   ($this->model->itemHasModifiers($item->uuid)->total>0)?true:false;
            $final_item["image"]= $this->model->getDefaultItemImage($item->uuid);

            if($this->useAlternateNames && isset($item->alternate_name) && $item->alternate_name!==""){
                $final_item["name"]=stripslashes($item->alternate_name);
            } else {
                $final_item["name"]=stripslashes($item->name);
            }

            array_push($response['items'],$final_item);
        }


        usort($response["items"], array('Moo_OnlineOrders_Restapi','moo_sort_items'));

        // Return all of our post response data.
        return $response;
    }
    public function getItemsDetail( $request ) {
        $response = array();
        //var_dump($request["cat_id"]);
        if ( !isset($request["item_id"]) || empty( $request["item_id"] ) ) {
            return new WP_Error( 'item_id_required', 'item id not found', array( 'status' => 404 ) );
        }
        $item = $this->model->getItem($request["item_id"]);


        if($item === null || $item->hidden == "1" || $item->visible != "1" || $item->price_type == "VARIABLE")
            return new WP_Error( 'item_not_found', 'Item not found', array( 'status' => 404 ) );


        //Check the stock
        $track_stock = $this->api->getTrackingStockStatus();
        if($track_stock == true)
            $itemStocks = $this->api->getItemStocks();
        else
            $itemStocks = false;
        if($track_stock)
            $itemStock = self::getItemStock($itemStocks,$item->uuid);
        else
            $itemStock = false;

        if($item->outofstock == 1 || ($track_stock == true && $itemStock != false && isset($itemStock->stockCount)  && $itemStock->stockCount < 1))
        {
            $response['stockCount'] = "out_of_stock";
        }
        else
        {
            if(isset($itemStock->stockCount))
                $response['stockCount'] = $itemStock->stockCount;
            else
                $response['stockCount'] = ($track_stock)?"tracking_stock":"not_tracking_stock";
        }


        $response["uuid"] = $item->uuid;
        $response["name"]             =   stripslashes($item->name);
        $response["alternate_name"]   =   stripslashes($item->alternate_name);
        $response["description"]      =   stripslashes($item->description);
        $response["price"]        =   $item->price;
        $response["price_type"]   =   $item->price_type;
        $response["unit_name"]    =   $item->unit_name;
        $response["modifier_groups"] = array();
        $response["images"] = array();

        if($this->useAlternateNames && isset($item->alternate_name) && $item->alternate_name!==""){
            $response["name"]=stripslashes($item->alternate_name);
        } else {
            $response["name"]=stripslashes($item->name);
        }

        $mg = $this->model->getModifiersGroup($item->uuid);
        if($mg) {
            foreach ($mg as $modifierG) {
                $m = array();

                if($this->useAlternateNames  && isset($modifierG->alternate_name) && $modifierG->alternate_name!==""){
                    $m["name"]=stripslashes($modifierG->alternate_name);
                } else {
                    $m["name"]=stripslashes($modifierG->name);
                }

                $m["uuid"] = $modifierG->uuid;
                $m["min_required"] = $modifierG->min_required;
                $m["max_allowd"]   = $modifierG->max_allowd;
                $m["sort_order"]   = $modifierG->sort_order;
                $m["modifiers"] = array();

                $modifiers = $this->model->getModifiers($modifierG->uuid);
                if(count($modifiers)>0)
                {
                    foreach ($modifiers as $modifier) {
                        $res = array();

                        if($this->useAlternateNames && isset($modifier->alternate_name) && $modifier->alternate_name!==""){
                            $res["name"]=stripslashes($modifier->alternate_name);
                        } else {
                            $res["name"]=stripslashes($modifier->name);
                        }

                        $res["uuid"] = $modifier->uuid;
                        $res["price"] = $modifier->price;
                        $res["sort_order"] = $modifier->sort_order;
                        array_push($m["modifiers"],$res);
                    }
                    array_push($response["modifier_groups"],$m);
                }

            }
        }
        $images = $this->model->getItemImages($item->uuid);
        if(count($images)>0){
            foreach ($images as $image) {
                if($image->is_enabled=="1")
                {
                    $res = array();
                    $res["image_url"]  = $image->url;
                    $res["is_default"] = $image->is_default;
                    array_push($response["images"],$res);
                }
            }
        }
        //get taxes
        $response['tax_rates']= $this->model->getItemTax_rate( $item->uuid );
        // Return all of our post response data.
        return $response;
    }
    public function getMostPurchasedItems( $request )
    {
        $response = array();
        $response['items'] = array();
        $track_stock = $this->api->getTrackingStockStatus();
        if($track_stock == true)
            $itemStocks = $this->api->getItemStocks();
        else
            $itemStocks = false;
        $items = $this->model->getBestSellingProducts(12);
        foreach ($items as $item) {
            if(!$item)
                continue;

            $final_item = array();

            //Check if the item if it's disabled
            if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE') continue;

            //Check the stock
            if($track_stock)
                $itemStock = self::getItemStock($itemStocks,$item->uuid);
            else
                $itemStock = false;


            if($item->outofstock == 1 || ($track_stock == true && $itemStock != false && isset($itemStock->stockCount)  && $itemStock->stockCount < 1))
            {
                continue;
            } else {

                if(isset($itemStock->stockCount))
                    $final_item['stockCount'] = $itemStock->stockCount;
                else
                    $final_item['stockCount'] = ($track_stock)?"tracking_stock":"not_tracking_stock";
            }
            if($this->useAlternateNames && isset($item->alternate_name) && $item->alternate_name!==""){
                $final_item["name"]=stripslashes($item->alternate_name);
            } else {
                $final_item["name"]=stripslashes($item->name);
            }
            $final_item["uuid"]=$item->uuid;
            $final_item["alternate_name"]   =   stripslashes($item->alternate_name);
            $final_item["description"]      =   stripslashes($item->description);
            $final_item["price"]        =   $item->price;
            $final_item["price_type"]   =   $item->price_type;
            $final_item["unit_name"]    =   $item->unit_name;
            $final_item["sort_order"]   =   intval($item->sort_order);
            $final_item["has_modifiers"]=   ($this->model->itemHasModifiers($item->uuid)->total>0)?true:false;
            $final_item["image"]= $this->model->getDefaultItemImage($item->uuid);

            array_push($response['items'],$final_item);
        }

        // Return all of our post response data.
        return $response;
    }
    public function getCart( $request )
    {
        $response = array();

        if(!$this->session->isEmpty("items")){
            $response['items'] = array();
            foreach ($this->session->get("items") as $line_id=>$line_content)
            {
                $line = array(
                    "item"=>array(
                        "name"=>$line_content["item"]->name,
                        "price"=>$line_content["item"]->price,
                        "price_type"=>$line_content["item"]->price_type
                    ),
                    "qty"=>$line_content["quantity"],
                    "special_ins"=>$line_content["special_ins"],
                    "modifiers"=>array()
                );

                if($this->useAlternateNames && isset($line_content["item"]->alternate_name) && $line_content["item"]->alternate_name!==""){
                    $line["item"]["name"]=stripslashes($line_content["item"]->alternate_name);
                } else {
                    $line["item"]["name"]=stripslashes($line_content["item"]->name);
                }

                if(count($line_content["modifiers"])>0)
                    foreach($line_content["modifiers"] as $modifier)
                    {
                        $final_modifier = array(
                                "uuid"=>$modifier["uuid"],
                                "price"=>$modifier["price"],
                                "qty"=>(isset($modifier["qty"]))?intval($modifier["qty"]):1
                            );
                        if($this->useAlternateNames && isset($modifier["alternate_name"]) && $modifier["alternate_name"]!==""){
                            $final_modifier["name"] = stripslashes($modifier["alternate_name"]);
                        } else {
                            $final_modifier["name"]=stripslashes($modifier["name"]);
                        }
                        array_push($line["modifiers"],$final_modifier);
                    }
                $response['items'][$line_id] = $line;
            }
        } else {
            $response['items'] = array();
        }
        $response['total'] = Moo_OnlineOrders_Public::moo_cart_getTotal(true);
        // Return all of our post response data.
        return $response;
    }
    public function addItemToCart( $request )
    {
        $request_body = $request->get_body_params();
        $item_uuid      = sanitize_text_field($request_body['item_uuid']);
        $item_qty       = (intval($request_body['item_qty'])>1)?intval($request_body['item_qty']):1;
        $item_modifiers = (isset($request_body['item_modifiers']) && count($request_body['item_modifiers'])>0)?$request_body['item_modifiers']:array();
        $special_ins = "";
        $cart_line_id = $item_uuid;
        $nb_items_in_cart = 0;

        if(count($item_modifiers)>0)
        {
            //the cart line id will be changed
            foreach ($item_modifiers as $modifier)
                $cart_line_id .= '_'.$modifier['uuid'];
        }

        $qte = $item_qty;


        $item = $this->model->getItem($item_uuid);

        if($item){
            //Check the stock before inserting the item to the cart
            if($this->api->getTrackingStockStatus())
            {
                $itemStocks = $this->api->getItemStocks();
                $itemStock  = $this->getItemStock($itemStocks,$item->uuid);
                if( $this->session->exist("items") && $this->session->exist("itemsQte",$item_uuid) )
                {
                    if($itemStock != false && isset($itemStock->stockCount) && (($this->session->get("itemsQte",$item_uuid)+$qte)>$itemStock->stockCount))
                    {

                        $response = array(
                            'status'	=> 'error',
                            'message'   => "Unfortunately, we are low on stock please change the quantity amount.".((($itemStock->stockCount-$this->session->get("itemsQte",$item_uuid))>0)?" You can add only ".($itemStock->stockCount-$this->session->get("itemsQte",$item_uuid))." units":""),
                            'quantity'   => $itemStock->stockCount
                        );
                        return $response;
                    } else {
                        $newValue = $this->session->get("itemsQte",$item_uuid) + $qte;
                        $this->session->set($newValue,"itemsQte",$item_uuid);
                    }

                } else {
                    if($itemStock != false && isset($itemStock->stockCount) && $qte>$itemStock->stockCount) {
                        if($itemStock->stockCount>0){
                            $response = array(
                                'status'	=> 'error',
                                'message'   => "Unfortunately, we are low on stock please change the quantity amount we have only ".$itemStock->stockCount." left",
                                'quantity'   => $itemStock->stockCount
                            );
                        } else {
                            $response = array(
                                'status'	=> 'error',
                                'message'   => "Unfortunately, we are low on stock please check back again later ",
                                'quantity'   => $itemStock->stockCount
                            );
                        }

                        return $response;
                    } else {
                        $this->session->set($qte,"itemsQte",$item_uuid);
                    }
                }
            }

            if($this->session->exist("items") && array_key_exists($cart_line_id,$this->session->get("items")) ) {
                $cartLine = $this->session->get("items",$cart_line_id);
                $cartLine['quantity']+=$qte;

            } else {
                $cartLine = array(
                    'item'=>$item,
                    'quantity'=>$qte,
                    'special_ins'=>$special_ins,
                    'tax_rate'=>$this->model->getItemTax_rate( $item_uuid ),
                    'modifiers'=>array()
                );
            }

            //Adding modifiers
            foreach ($item_modifiers as $modifier) {
                $modifier_uuid = $modifier['uuid'];
                $modifierInfos = (array)$this->model->getModifier($modifier_uuid);
                $q = intval($modifier['qty']);
                $modifierInfos["qty"] = ($q<1)?1:$q;
                $cartLine['modifiers'][$modifier_uuid] = $modifierInfos;
            }

            $this->session->set($cartLine,"items",$cart_line_id);
            $response = array(
                'status'	=> 'success',
                'line_id'	=> $cart_line_id,
                'name'      => $item->name,
                'nb_items'  =>$this->moo_get_nbItems_in_cart()
            );
            if($this->useAlternateNames && isset($item->alternate_name) && $item->alternate_name  !==""){
                $response["name"] = stripslashes($item->alternate_name);
            } else {
                $response["name"] = stripslashes($item->name);
            }
        } else {

            $response = array(
                'status'	=> 'error',
                'message'   => 'Item not found in database, please refresh the page'
            );

        }
        return $response;
    }
    public function removeFromCart( $request )
    {
        $request_body   = $request->get_body_params();

        $line_id     = sanitize_text_field($request_body['line_id']);

        if($line_id != "") {
            if( ! $this->session->isEmpty("items",$line_id) ){
                $cartLine= $this->session->get("items",$line_id);
                $item_uuid = $cartLine['item']->uuid;
                if( $this->session->exist("itemsQte",$item_uuid))
                {
                    $newValue = $this->session->get("itemsQte",$item_uuid) - $cartLine['quantity'];
                    $this->session->set($newValue,"itemsQte",$item_uuid);
                    if($this->session->get("itemsQte",$item_uuid) <= 0)
                        $this->session->delete("itemsQte",$item_uuid);
                }
                $this->session->delete("items",$line_id);
            }
            $response = array(
                'status'	=> 'success',
                'nb_items'  =>$this->moo_get_nbItems_in_cart()
            );
        } else {
            $response = array(
                'status'	=> 'error',
                'message'   => "Item not exist in your cart, maybe your cart expired or your removed it from an opend tab"
            );
        }
        $this->moo_refresh_itemQte_cart();
        return $response;
    }
    public function updateSpecialInstructionforItem( $request )
    {
        $request_body   = $request->get_body_params();

        $line_id        = sanitize_text_field($request_body['line_id']);
        $special_ins    = sanitize_text_field($request_body['special_ins']);

        if($line_id != "")
        {
            if(!$this->session->isEmpty("items",$line_id)){
                $cartLine = $this->session->get("items",$line_id);
                $cartLine["special_ins"] = $special_ins;
                $this->session->set($cartLine,"items",$line_id);
            }
            $response = array(
                'status'	=> 'success'
            );
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Item not found in cart, please refresh the page'
            );
        }

        return $response;
    }
    public function updateQtyforItem( $request )
    {
        $request_body   = $request->get_body_params();
        $line_id        = sanitize_text_field($request_body['line_id']);
        $qty            = inval($request_body['qty']);
        $old_qty        = inval($request_body['old_qty']);

        if($line_id != "")
        {
            $cartLine = $this->session->get("items",$line_id);
            $item_uuid = $cartLine['item']->uuid;

            $track_stock = $this->api->getTrackingStockStatus();
            if($track_stock == true) {
                $itemStocks = $this->api->getItemStocks();
                $itemStock  = $this->getItemStock($itemStocks,$item_uuid);
            } else {
                $itemStock = false;
            }

            if($track_stock && ($itemStock != false && isset($itemStock->stockCount) && $itemStock->stockCount<$qty))
            {
                $response = array(
                    'status'	=> 'error',
                    'message'   => "Unfortunately, we are low on stock please change the quantity amount",
                    'quantity'   => $itemStock->stockCount
                );
            } else {
                if(isset($cartLine) && !empty($cartLine)){
                    $cartLine["quantity"] = $qty;
                    //update also teh qty that track stock
                    if($this->session->exist('itemsQte',$item_uuid)) {
                        //remove the old qty
                        $newQty = $qty - $old_qty;
                        $this->session->set($newQty,'itemsQte',$item_uuid);
                        // check the new Qty
                        if($this->session->get('itemsQte',$item_uuid)<=0)
                            $this->session->delete('itemsQte',$item_uuid);
                    } else {
                        $this->session->set($qty,'itemsQte',$item_uuid);
                    }
                    $response = array(
                        'status'	=> 'success'
                    );
                }
                else
                {
                    $response = array(
                        'status'	=> 'error',
                        'message'   => "Unfortunately, your session has expired please refresh the page"
                    );
                }

            }
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Item not found in cart, please refresh the page'
            );
        }

        return $response;
    }

    public static function getItemStock($items,$item_uuid)
    {
        foreach ($items as $i)
        {
            if($i->item->id == $item_uuid)
                return $i;
        }
        return false;
    }
    public static function moo_sort_items($a,$b)
    {
        return $a["sort_order"]>$b["sort_order"];
    }
    public static function moo_sort_installed_themes($a,$b)
    {
        return $a->name<$b->name;
    }
    /* Clean's functions */
    public function cleanItems( $request )
    {
        $response = array();
        //var_dump($request["cat_id"]);
        if ( !isset($request["per_page"]) || !isset( $request["page"] ) ) {
            return new WP_Error( 'pagination_params_required', 'Pagination params are required, the page number and number of items per page', array( 'status' => 404 ) );
        }
        $items = $this->model->getItemsByPage(intval($request["per_page"]),intval($request["page"]));
        $response["nb_items"] = count($items);
        $count = 0;
        $removed = 0;
        $hidden = 0;
        foreach ($items as $item) {
            if($item->uuid != ""){
                $res = $this->api->getItemWithoutSaving($item->uuid);
                if(isset($res->id) && $res->id == $item->uuid)
                {
                    $count++;
                    continue;
                } else {
                    $r = $this->api->delete_item($item->uuid);
                    if($r) {
                        $removed++;
                    } else {
                        $hidden++;
                        $this->model->hideItem($item->uuid);
                    }
                }
            }
        }
        $response["checked"] = $count;
        $response["removed"] = $removed;
        $response["hidden"]  = $hidden;
        $response["last_page"] = ($response["nb_items"] < intval($request["per_page"]))?true:false;

        return $response;
    }
    public function cleanCategories( $request )
    {
        $response = array();
        if ( !isset($request["per_page"]) || !isset( $request["page"] ) ) {
            return new WP_Error( 'pagination_params_required', 'Pagination params are required, the page number and number of items per page', array( 'status' => 404 ) );
        }
        $cats = $this->model->getCategoriesByPage(intval($request["per_page"]),intval($request["page"]));
        $response["nb_categories"] = count($cats);
        $count = 0;
        $removed = 0;
        $hidden = 0;
        foreach ($cats as $cat) {
            if($cat->uuid != ""){
                $res = $this->api->getCategoryWithoutSaving($cat->uuid);
                if(isset($res->id) && $res->id == $cat->uuid)
                {
                    $count++;
                    continue;
                }
                else
                {
                    $r = $this->model->deleteCategory($cat->uuid);
                    if($r)
                    {
                        $removed++;
                    }
                    else
                    {
                        $hidden++;
                        $this->model->hideCategory($cat->uuid);
                    }

                }
            }
        }
        $response["checked"] = $count;
        $response["removed"] = $removed;
        $response["hidden"]  = $hidden;
        $response["last_page"] = ($response["nb_categories"] < intval($request["per_page"]))?true:false;

        return $response;
    }
    public function cleanModifierGroups( $request )
    {
        $response = array();
        if ( !isset($request["per_page"]) || !isset( $request["page"] ) ) {
            return new WP_Error( 'pagination_params_required', 'Pagination params are required, the page number and number of items per page', array( 'status' => 404 ) );
        }
        $mGroups = $this->model->getModifierGroupsByPage(intval($request["per_page"]),intval($request["page"]));
        $response["nb_modifier_groups"] = count($mGroups);
        $count = 0;
        $removed = 0;
        $hidden = 0;
        foreach ($mGroups as $m) {
            if($m->uuid != ""){
                $res = json_decode($this->api->getModifierGroupsWithoutSaving($m->uuid));
                 if(isset($res->id) && $res->id == $m->uuid)
                {
                    $count++;
                    continue;
                }
                else
                {
                    $r = $this->model->deleteModifierGroup($m->uuid);
                    if($r)
                    {
                        $removed++;
                    }
                    else
                    {
                        $hidden++;
                        $this->model->UpdateModifierGroupStatus($m->uuid,'false');
                    }

                }
            }
        }
        $response["checked"] = $count;
        $response["removed"] = $removed;
        $response["hidden"]  = $hidden;
        $response["last_page"] = ($response["nb_modifier_groups"] < intval($request["per_page"]))?true:false;

        return $response;
    }
    public function cleanModifiers( $request )
    {
        $response = array();
        if ( !isset($request["per_page"]) || !isset( $request["page"] ) ) {
            return new WP_Error( 'pagination_params_required', 'Pagination params are required, the page number and number of items per page', array( 'status' => 404 ) );
        }
        $modifiers = $this->model->getModifiersByPage(intval($request["per_page"]),intval($request["page"]));
        $response["nb_modifiers"] = count($modifiers);
        $count = 0;
        $removed = 0;
        $hidden = 0;
        foreach ($modifiers as $m) {
            if($m->uuid != ""){
                $res = json_decode($this->api->getModifierWithoutSaving($m->group_id,$m->uuid));
                 if(isset($res->id) && $res->id == $m->uuid)
                {
                    $count++;
                    continue;
                }
                else
                {
                    $r = $this->model->deleteModifier($m->uuid);
                    if($r)
                    {
                        $removed++;
                    }
                    else
                    {
                        $hidden++;
                        $this->model->UpdateModifierStatus($m->uuid,'false');
                    }

                }
            }
        }
        $response["checked"] = $count;
        $response["removed"] = $removed;
        $response["hidden"]  = $hidden;
        $response["last_page"] = ($response["nb_modifiers"] < intval($request["per_page"]))?true:false;

        return $response;
    }
    public function cleanTaxRates( $request )
    {
        $response = array();
        if ( !isset($request["per_page"]) || !isset( $request["page"] ) ) {
            return new WP_Error( 'pagination_params_required', 'Pagination params are required, the page number and number of items per page', array( 'status' => 404 ) );
        }
        $tax_rates = $this->model->getTaxRatesByPage(intval($request["per_page"]),intval($request["page"]));
        $response["nb_tax_rates"] = count($tax_rates);
        $count = 0;
        $removed = 0;
        $hidden = 0;
        foreach ($tax_rates as $t) {
            if($t->uuid != ""){
                $res = json_decode($this->api->getTaxRateWithoutSaving($t->uuid));
                 if(isset($res->id) && $res->id == $t->uuid)
                {
                    $count++;
                    continue;
                }
                else
                {
                    $r = $this->model->deleteTaxRate($t->uuid);
                    if($r)
                    {
                        $removed++;
                    }
                }
            }
        }
        $response["checked"] = $count;
        $response["removed"] = $removed;
        $response["hidden"]  = $hidden;
        $response["last_page"] = ($response["nb_tax_rates"] < intval($request["per_page"]))?true:false;

        return $response;
    }
    public function cleanOrderTypes( $request )
    {
        $response = array();
        if ( !isset($request["per_page"]) || !isset( $request["page"] ) ) {
            return new WP_Error( 'pagination_params_required', 'Pagination params are required, the page number and number of items per page', array( 'status' => 404 ) );
        }
        $order_types = $this->model->getOrderTypesByPage(intval($request["per_page"]),intval($request["page"]));
        $response["nb_order_types"] = count($order_types);
        $count = 0;
        $removed = 0;
        $hidden = 0;
        foreach ($order_types as $o) {
            if($o->ot_uuid != ""){
                $res = json_decode($this->api->GetOneOrdersTypes($o->ot_uuid));
                if(isset($res->id) && $res->id == $o->ot_uuid)
                {
                    $count++;
                    continue;
                }
                else
                {
                    $r = $this->model->moo_DeleteOrderType($o->ot_uuid);
                    if($r)
                    {
                        $removed++;
                    }
                    else
                    {
                        $hidden++;
                        $this->model->updateOrderTypes($o->ot_uuid,'false');
                    }
                }

            }
        }
        $response["checked"] = $count;
        $response["removed"] = $removed;
        $response["hidden"]  = $hidden;
        $response["last_page"] = ($response["nb_order_types"] < intval($request["per_page"]))?true:false;

        return $response;
    }
    /* Get the theme settings */
    public function getThemeSettings( $request )
    {
        $response = array();
        if ( !isset($request["theme_name"]) ) {
            return new WP_Error( 'theme_name_required', 'Please provide ethe theme name', array( 'status' => 404 ) );
        }
        $name = $request["theme_name"];
        $res = array();
        $settings = (array) get_option("moo_settings");

        if($name === "default") {
            $name = $settings["default_style"];
        }
        foreach ($settings as $key=>$val) {
            $k = (string)$key;
            if(strpos($k,$name."_") === 0 && $val != "")
            {
                $res[$key]= $val;
            }
        }
        $response["theme_name"] = $name;
        $response["nb_items"]   = $this->moo_get_nbItems_in_cart();
        $response["settings"]   = $res;
        return $response;
    }
    /* Save the theme settings */
    public function saveThemeSettings( $request )
    {
        $response = array();
        if ( !isset($request["theme_name"]) ) {
            return new WP_Error( 'theme_name_required', 'Please provide ethe theme name', array( 'status' => 404 ) );
        }
        $name = $request["theme_name"];
        $body = json_decode($request->get_body(),true);
        $settings = (array) get_option("moo_settings");
        if(is_array($body) && count($body)>0){
            foreach ($body as $key=>$val) {
                $settings[$name.'_'.$val["name"]] = $val["value"];
            }
        }
        update_option("moo_settings",$settings);
        $response["status"] = 'success';
        return $response;
    }
    /* Get the  Modifier Groups Settings */
    public function getModifierGroupsSettings( $request ){
        $response = array();

        $res = array();
        $settings = (array) get_option("moo_settings");

        if(isset($settings["mg_settings_displayInline"]) && $settings["mg_settings_displayInline"] == "enabled") {
            $res["inlineDisplay"] = true;
        } else {
            $res["inlineDisplay"] = false;
        }

        if(isset($settings["mg_settings_qty_for_all"]) && $settings["mg_settings_qty_for_all"] == "disabled") {
            $res["qtyForAll"] = false;
        } else {
            $res["qtyForAll"] = true;
        }

        if(isset($settings["mg_settings_qty_for_zeroPrice"]) && $settings["mg_settings_qty_for_zeroPrice"] == "disabled") {
            $res["qtyForZeroPrice"] = false;
        } else {
            $res["qtyForZeroPrice"] = true;
        }

        if(isset($settings["mg_settings_minimized"]) && $settings["mg_settings_minimized"] == "enabled") {
            $res["minimized"] = true;
        } else {
            $res["minimized"] = false;
        }

        $response["settings"]   =  $res;
        return $response;
    }
    /*
     * Dashboard's functions
     */
    public function getInstalledThemes($request) {
        $result = array();
        $settings = (array) get_option("moo_settings");
        $currentTheme = $settings["default_style"];
        $path = plugin_dir_path(dirname(__FILE__))."public/themes";
        $directories = scandir($path);
        foreach ($directories as $dir) {
            if(file_exists($path."/".$dir."/manifest.json")){
                $theme_settings = json_decode(file_get_contents($path."/".$dir."/manifest.json"));
                if(!isset($theme_settings->name) || $theme_settings->name === ''){
                    continue;
                }
                $theme_settings->is_active = ($theme_settings->identifier == $currentTheme)?true:false;
                array_push($result,$theme_settings);
            }
        }
        usort($result, array('Moo_OnlineOrders_Restapi','moo_sort_installed_themes'));
        return array(
            "status"=>"ok",
            "data"=>$result
        );
    }
    public function getAllThemes($request) {
        $result = $this->api->getThemes();

        if(count($result)>0) {
            return array(
                "status"=>"nok",
                "data"=>$result);
        }

        return array("status"=>"nok");
    }
    public function activeTheme($request) {
        $request_body   = json_decode($request->get_body(),true);
        $result = array();
        $settings = (array) get_option("moo_settings");
        $theme    = sanitize_text_field($request_body['theme']);
        if($theme != "")
        {
            //change theme
            $settings["default_style"] = $theme;
            //load theme settings from manifest
            $path = plugin_dir_path(dirname(__FILE__))."public/themes";
            if(file_exists($path."/".$theme."/manifest.json")){
                $theme_manifest = json_decode(file_get_contents($path."/".$theme."/manifest.json"),true);
                if(isset($theme_manifest['settings'] )) {
                    foreach ($theme_manifest['settings'] as $item_settings) {
                        if(!isset($settings[$theme.'_'.$item_settings["id"]])) {
                            $settings[$theme.'_'.$item_settings["id"]] = $item_settings["default"];
                        }
                    }
                }
            }
            //save changes
            update_option("moo_settings",$settings);
            $response = array(
                'status'	=> 'success'
            );
        } else {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Selected theme not found'
            );
        }

       return $response;
    }
    public function changeApiKey($request) {
        $request_body   = json_decode($request->get_body(),true);
        $result = array();
        $settings = (array) get_option("moo_settings");
        $new_api_key    = sanitize_text_field($request_body['api_key']);
        //check the api key
        $result = true;
        if($result) {
            //remove the current data

            //import new data

            //change api key

            //return result

            //change theme
            $settings["api_key"]   = $new_api_key;
            $settings["jwt-token"] = "";

            //save changes
            update_option("moo_settings",$settings);
            $response = array(
                'status'	=> 'success'
            );
        } else {
            $response = array(
                'status'	=> 'error',
                'message'   => 'the new api key is not a valid key'
            );
        }

       return $response;
    }
    /*
     * Tools
     */
    public function httpsForImages( $request ){
        $this->model->addHttpsToImages();
        return array(
            "status"=>'success',
        );
    }
    public function httpForImages( $request ){
        $this->model->addHttpToImages();
        return array(
            "status"=>'success',
        );
    }
    public function updateBlackouts( $request ){
        $this->api->getBlackoutStatus(true);
        return array(
            "status"=>'success',
        );
    }

    /*
     * Importing & syncing functions
     */
    public function importCategory( $request ){
        global $wpdb;
        $response = array();
        $nbItemsPerCategory = 0;
        if ( !isset($request["cat_id"]) || empty( $request["cat_id"] ) ) {
            return new WP_Error( 'category_id_required', 'Category id not found', array( 'status' => 404 ) );
        }
        $category_uuid = sanitize_text_field($request["cat_id"]);
        //Get the category details
        $category = $this->api->getOneCategory($category_uuid);
        $string_items_ids ="";
        //Save the items
        if(isset($category->items) && isset($category->items->elements)) {
            $cloverNbItems = @count($category->items->elements);
            foreach ($category->items->elements as $item) {
                if(isset($item->id)) {
                    $string_items_ids .= $item->id.",";
                    if($this->model->getItem($item->id) == null){
                        $this->api->getItem($item->id);
                    } else {
                        $nbItemsPerCategory++;
                    }
                }
            }
        } else {
            $cloverNbItems = 0;
        }
        $wpdb->update("{$wpdb->prefix}moo_category",array(
            'items' =>  $string_items_ids
        ),array('uuid'=>$category->id));

        return array(
            "status"=>'success',
            "clover_nb_items"=>$cloverNbItems,
            "nb_items"=>$nbItemsPerCategory
        );
    }

    /*
     * Function for customers profils
     */
    public function getCustomer($request){
        $fromSession = false;
        // check if token sent in body
        if(isset($request["moo_customer_token"]) && !empty($request["moo_customer_token"])){
            $token = $request["moo_customer_token"];
        } else {
            if($this->session->isEmpty("moo_customer_token")) {
                return array("status"=>"failed","message"=>"not logged user");
            } else {
                $token = $this->session->get("moo_customer_token");
                $fromSession = true;
            }
        }
        $res = $this->api->moo_GetCustomer($token);
        $result= json_decode($res);
        if($result->status == 'success') {
            return array(
                "status"=>"success",
                "customer"=>$result->customer[0] ,
            );
        } else {
            if($fromSession){
                $this->session->set(false,"moo_customer_token");
                $this->session->set(null,"moo_customer");
            }
            return array(
                "status"=>$result->status
            );
        }

    }
    public function getOrdersForCustomer($request){
        $fromSession = false;
        // check if token sent in body
        if(isset($request["moo_customer_token"]) && !empty($request["moo_customer_token"])){
            $token = $request["moo_customer_token"];
        } else {
            if($this->session->isEmpty("moo_customer_token")) {
                return array("status"=>"failed","message"=>"not logged user");
            } else {
                $token = $this->session->get("moo_customer_token");
                $fromSession = true;
            }
        }

        if(isset($request['page'])) {
            $page = intval($request['page']);
        } else {
            $page = 1;
        }

        $res = $this->api->moo_GetOrders($token,$page);
        $result= json_decode($res);
        if($result->status == 'success') {
            $orders = array();
            $compteurOrders = 0;
            foreach ($result->orders as $order) {
                $orderId = sanitize_text_field($order->order->uuid);
                $order_items = $this->model->getItemsOrder($orderId);
                $orderDetail = $this->model->getOneOrder($orderId);
                if($orderDetail === null)
                    continue;
                $oneOrder = $this->api->getOrderDetails2($orderDetail,$order);
                if(isset($oneOrder['payments']) && count($oneOrder['payments'])>0 )
                {
                    $orderPayments = $oneOrder['payments'];
                    foreach ($orderPayments as $p) {
                        if ($p->result == "APPROVED")
                        {
                            $oneOrder['status'] = "Paid";
                        }
                    }
                    if($oneOrder['status'] == "")
                    {
                        $oneOrder['status'] = "Not Paid";
                    }
                } else {
                    if($oneOrder['paymentMethode'] == 'cash')
                    {
                        $oneOrder['status'] ='Will Pay Cash';
                    } else {
                        $oneOrder['status'] = 'Not paid';
                    }

                }
                foreach ($order_items as $order_item) {
                    if($order_item->modifiers != ""){
                        $order_item->list_modifiers = array();
                        $string = substr($order_item->modifiers, 0, strlen($order_item->modifiers)-1);
                        $data_modifier = explode( ',', $string);
                        foreach ($data_modifier as $modifier){
                            $getModifier = $this->model->getModifier($modifier);
                            if($getModifier !== null)
                                array_push($order_item->list_modifiers,$getModifier);
                        }
                    }
                }
                //var_dump($order_items);
                $oneOrder['items'] = $order_items;
                array_push($orders,$oneOrder);
                $compteurOrders++;
            }
            return array(
                "status"=>"success",
                "orders"=>$orders,
                "total_orders"=>($compteurOrders<10)?$compteurOrders:$result->total_orders->nb,
                "current_page"=>$page,
            );
        } else {
            if($fromSession){
                $this->session->set(false,"moo_customer_token");
                $this->session->set(null,"moo_customer");
            }
            return array(
                "status"=>$result->status
            );
        }

    }
    public function getCustomerFavorites($request){
        // check if token sent in body
        if(isset($request["moo_customer_token"]) && !empty($request["moo_customer_token"])){
            $token = $request["moo_customer_token"];
            $customer = json_decode($this->api->moo_GetCustomer($token));
            if(isset($customer->status) && $customer->status === "success"){
                if(isset($customer->customer[0]->email)){
                    $customer_email = $customer->customer[0]->email;
                }
            }
        } else {
            if($this->session->isEmpty("moo_customer_email")) {
                return array("status"=>"failed","message"=>"not logged user");
            } else {
                $customer_email = $this->session->get("moo_customer_email");
            }
        }
        if($customer_email) {
            $items = $this->model->moo_GetBestItems4Customer($customer_email);
        } else {
            return array("status"=>"failed","message"=>"not logged user");
        }

        $settings = (array) get_option("moo_settings");
        $response = array();
        $response["items"]= array();
        $track_stock = $this->api->getTrackingStockStatus();
        if($track_stock == true) {
            $itemStocks = $this->api->getItemStocks();
        } else {
            $itemStocks = false;
        }

        foreach ($items as $item) {
            $final_item = array();
            if(!$item) continue;
            //Check if the item if it's disabled
            if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE') continue;

            //Check the stock
            if($track_stock)
                $itemStock = self::getItemStock($itemStocks,$item->uuid);
            else
                $itemStock = false;


            if($item->outofstock == 1 || ($track_stock == true && $itemStock != false && isset($itemStock->stockCount)  && $itemStock->stockCount < 1))
            {
                if(isset($settings["track_stock_hide_items"]) && $settings["track_stock_hide_items"] === "on"){
                    continue;
                }
                $final_item['stockCount'] = "out_of_stock";
            }
            else
            {
                if(isset($itemStock->stockCount))
                    $final_item['stockCount'] = $itemStock->stockCount;
                else
                    $final_item['stockCount'] = ($track_stock)?"tracking_stock":"not_tracking_stock";
            }
            $final_item["uuid"]=$item->uuid;
            $final_item["name"]=$item->name;
            $final_item["description"]  =   stripslashes ($item->description);
            $final_item["price"]        =   $item->price;
            $final_item["price_type"]   =   $item->price_type;
            $final_item["unit_name"]    =   $item->unit_name;
            $final_item["unit_name"]    =   $item->unit_name;
            $final_item["sort_order"]   =   intval($item->sort_order);
            $final_item["has_modifiers"]=   ($this->model->itemHasModifiers($item->uuid)->total>0)?true:false;
            $final_item["image"]= $this->model->getDefaultItemImage($item->uuid);

            if($this->useAlternateNames && isset($item->alternate_name) && $item->alternate_name!==""){
                $final_item["name"]=stripslashes($item->alternate_name);
            } else {
                $final_item["name"]=stripslashes($item->name);
            }

            array_push($response['items'],$final_item);
        }


        usort($response["items"], array('Moo_OnlineOrders_Restapi','moo_sort_items'));
        // Return all of our post response data.
        return $response;

    }
    public function getCustomerAddressess($request){
        $fromSession = false;
        // check if token sent in body
        if(isset($request["moo_customer_token"]) && !empty($request["moo_customer_token"])){
            $token = $request["moo_customer_token"];
        } else {
            if($this->session->isEmpty("moo_customer_token")) {
                return array("status"=>"failed","message"=>"not logged user");
            } else {
                $token = $this->session->get("moo_customer_token");
                $fromSession = true;
            }
        }
        $res = $this->api->moo_GetAddresses($token);
        $result= json_decode($res);
        if(isset($result->status) && $result->status == 'success') {
            $res = array("status"=>"success","addresses"=>$result->addresses);
            $this->session->set($result->customer,"moo_customer");
            return $res;
        } else {
            if($fromSession){
                $this->session->set(null,"moo_customer");
                $this->session->set(false,"moo_customer_token");
                $this->session->set(null,"moo_customer_email");
            }

        }

        return
            array(
                "status"=>"failed",
                "message"=>"not logged user"
            );

    }
    public function reOrder($request){
        $MooOptions = (array)get_option('moo_settings');
        $cart_page_id     = $MooOptions['cart_page'];
        $cart_page_url    =  get_page_link($cart_page_id);
        if(isset($request['uuid'])) {
            $order_uuid = sanitize_text_field($request['uuid']);
        } else {
            return new WP_Error( 'rest_not_found', 'Sorry, order nor found.', array( 'status' => 404 ) );
        }
        if(isset($request["cart"]) && $request["cart"] == 'empty') {
            //empty the cart
            $this->session->delete("items");
            $this->session->delete("itemsQte");
        }

        $order_items = $this->model->getItemsOrder($order_uuid);
        foreach ($order_items as $item) {
            $item_uuid = $item->item_uuid;
            $cart_line_id = $item->item_uuid;
            $qte = $item->quantity;
            $list_modifiers = array();
            if($item->modifiers != ""){
                $string = substr($item->modifiers, 0, strlen($item->modifiers)-1);
                $data_modifier = explode( ',', $string);
                foreach ($data_modifier as $modifier_uuid){
                    $cart_line_id .= '_'.$modifier_uuid;
                    $getModifier = $this->model->getModifier($modifier_uuid);
                    array_push($list_modifiers,$getModifier);
                }
            }
            if($item){
                //check the item
                if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE' || $item->outofstock == 1) {
                    $response = array(
                        'status'	=> 'error',
                        'message'   => "Unfortunately, the item "
                    );

                    if($this->useAlternateNames && isset($item->alternate_name) && $item->alternate_name!==""){
                        $response["message"] .=  stripslashes($item->alternate_name)." is not available";
                    } else {
                        $response["message"] .= stripslashes($item->name)." is not available";
                    }

                    if(isset($request["cart"]) && $request["cart"] == 'empty') {
                        //empty the cart
                        $this->session->delete("items");
                        $this->session->delete("itemsQte");
                    }
                    //return error
                    return $response;
                }
                //Check the stock before inserting the item to the cart
                if($this->api->getTrackingStockStatus()) {
                    $itemStocks = $this->api->getItemStocks();
                    $itemStock  = $this->getItemStock($itemStocks,$item->item_uuid);
                    if($this->session->exist("items") && $this->session->exist("itemsQte",$item_uuid)) {
                        if($itemStock != false && isset($itemStock->stockCount) && (($this->session->get("itemsQte",$item_uuid)+$qte)>$itemStock->stockCount))
                        {

                            $response = array(
                                'status'	=> 'error',
                                'message'   => "Unfortunately, we are low on stock, you can't order the item ",
                                'quantity'   => $itemStock->stockCount
                            );

                            if($this->useAlternateNames && isset($item->alternate_name) && $item->alternate_name!==""){
                                $response["message"] .=  stripslashes($item->alternate_name);
                            } else {
                                $response["message"] .= stripslashes($item->name);
                            }

                            if(isset($request["cart"]) && $request["cart"] == 'empty') {
                                //empty the cart
                                $this->session->delete("items");
                                $this->session->delete("itemsQte");
                            }
                            //return error
                            return $response;
                        }
                        else
                        {
                            $newValue = $this->session->get("itemsQte",$item_uuid) + $qte;
                            $this->session->set($newValue, "itemsQte",$item_uuid);
                        }

                    } else {
                        if($itemStock != false && isset($itemStock->stockCount) && $qte>$itemStock->stockCount) {
                            $response = array(
                                'status'	=> 'error',
                                'message'   => "Unfortunately, we are low on stock, you can't order the item ",
                                'quantity'   => $itemStock->stockCount
                            );
                            if($this->useAlternateNames && isset($item->alternate_name) && $item->alternate_name!==""){
                                $response["message"] .=  stripslashes($item->alternate_name);
                            } else {
                                $response["message"] .= stripslashes($item->name);
                            }
                            if(isset($request["cart"]) && $request["cart"] == 'empty') {
                                //empty the cart
                                $this->session->delete("items");
                                $this->session->delete("itemsQte");
                            }
                            //return error
                            return $response;
                        } else {
                            $this->session->set($qte, "itemsQte",$item_uuid);
                        }

                    }
                }

                if( $this->session->exist( "items") && array_key_exists($cart_line_id,$this->session->get( "items")) ) {
                    $cartLine = $this->session->get( "items",$cart_line_id);
                    $cartLine['quantity']+=$qte;
                } else {
                    $cartLine = array(
                        'item'=>$item,
                        'quantity'=>$qte,
                        'special_ins'=>$item->special_ins,
                        'tax_rate'=>$this->model->getItemTax_rate( $item_uuid ),
                        'modifiers'=>array()
                    );
                }
                //Adding modifiers
                foreach ($list_modifiers as $modifier) {
                    $modifier_uuid = $modifier->uuid;
                    $modifierInfos = (array)$modifier;
                    $q = 1;
                    $modifierInfos["qty"] = ($q<1)?1:$q;
                    $cartLine['modifiers'][$modifier_uuid] = $modifierInfos;
                }
                $this->session->set($cartLine,"items",$cart_line_id);
            } else {
                $response = array(
                    'status'	=> 'error',
                    'message'   => 'An item not found in our system, maybe we stop selling it or it is out of stock'
                );

                return $response;

            }
        }
        $response = array(
            'status'	=> 'success',
            'cart_url'	=> $cart_page_url,
            'message'   => 'items added to cart'
        );

        return $response;
    }
    public function updateCustomer($request){
        // check if token sent in body
        if(isset($request["moo_customer_token"]) && !empty($request["moo_customer_token"])){
            $token = $request["moo_customer_token"];
        } else {
            if($this->session->isEmpty("moo_customer_token")) {
                return array("status"=>"failed","message"=>"not logged user");
            } else {
                $token = $this->session->get("moo_customer_token");
            }
        }
        $name  = sanitize_text_field($request['name']);
        $email = sanitize_text_field($request['email']);
        $phone = sanitize_text_field($request['phone']);
        $res = $this->api->moo_updateCustomer($name,$email,$phone,$token);
        $result= json_decode($res);
        if($result->status == 'success') {
            $res = array("status"=>"success");
            return $res;
        } else {
           if($res === false){
               return array("status"=>"failed","message"=>"Customer not updated");
           }
        }

        return array("status"=>"failed","message"=>"not logged user");

    }
    public function updateCustomerPassword($request){
        // check if token sent in body
        if(isset($request["moo_customer_token"]) && !empty($request["moo_customer_token"])){
            $token = $request["moo_customer_token"];
        } else {
            if($this->session->isEmpty("moo_customer_token")) {
                return array("status"=>"failed","message"=>"not logged user");
            } else {
                $token = $this->session->get("moo_customer_token");
            }
        }
        $current_password = sanitize_text_field($request['current_password']);
        $new_password     = sanitize_text_field($request['new_password']);

        if(strlen($new_password)<6) {
            $res =array("status"=>"failed","message"=>"The new password must contain 6 chars at min");
            return $res;
        }
        $res = $this->api->updateCustomerPassword(sha1($current_password),sha1($new_password),$token);
        $result= json_decode($res);

        if($result->status == 'success') {
            $res = array("status"=>"success");
            return $res;
        } else {
            $res =array("status"=>"failed","message"=>"The current password is incorrect");
            return $res;
        }
    }

    /* Static functions for internal use */
    public function moo_refresh_itemQte_cart()
    {
        $this->session->delete("itemsQte");
        foreach ($this->session->get("items") as $cartLine) {
            $item_uuid = $cartLine["item"]->uuid;
            if(! $this->session->exist("itemsQte",$item_uuid)) {
                $this->session->set( $cartLine["quantity"],"itemsQte",$item_uuid);
            }
            else {
                $newValue =  $this->session->get( "itemsQte",$item_uuid) + $cartLine["quantity"];
                $this->session->set( $newValue,"itemsQte",$item_uuid);
            }

        }
    }
    public function moo_get_nbItems_in_cart()
    {
        $res = 0;
        if($this->session->exist("items"))
            foreach ($this->session->get("items") as $item) {
               $res += $item["quantity"];
            }
        return $res ;
    }
}