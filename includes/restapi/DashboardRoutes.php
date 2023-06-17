<?php
/**
 * Created by Mohammed EL BANYAOUI.
 * Sync route to handle all requests to sync the inventory with Clover
 * User: Smart MerchantApps
 * Date: 3/5/2019
 * Time: 12:23 PM
 */
require_once "BaseRoute.php";

class DashboardRoutes extends BaseRoute {
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
     * @var bool
     */
    private $useAlternateNames;

    /**
     * SyncRoutes constructor.
     *
     */
    public function __construct($model, $api){

        $this->model          = $model;
        $this->api            = $api;
        $this->pluginSettings = (array) get_option("moo_settings");
        if(isset($this->pluginSettings["useAlternateNames"])){
            $this->useAlternateNames = ($this->pluginSettings["useAlternateNames"] !== "disabled");
        } else {
            $this->useAlternateNames = true;
        }
    }


    // Register our routes.
    public function register_routes(){
        // Update category name and description
        register_rest_route($this->namespace, '/dash/category/(?P<cat_id>[a-zA-Z0-9-]+)', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'POST',
                'callback' => array($this, 'dashUpdateCategory'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // Update time for category
        register_rest_route($this->namespace, '/dash/category/(?P<cat_id>[a-zA-Z0-9-]+)/time', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'POST',
                'callback' => array($this, 'dashUpdateCategoryTime'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        //get category
        register_rest_route($this->namespace, '/dash/category/(?P<cat_id>[a-zA-Z0-9-]+)', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashGetCategory'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));

        // get all categories
        register_rest_route($this->namespace, '/dash/categories', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashGetCategories'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));

        // get categories hours
        register_rest_route($this->namespace, '/dash/categories_hours', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashGetCategoriesHours'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // get all ordertypes hours
        register_rest_route($this->namespace, '/dash/ordertypes_hours', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashGetOrderTypesHours'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // update api key
        register_rest_route($this->namespace, '/dash/update_api_key', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'POST',
                'callback' => array($this, 'dashUpdateApiKey'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // export settings
        register_rest_route($this->namespace, '/dash/export/settings', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashExportSettings'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // export items descriptions
        register_rest_route($this->namespace, '/dash/export/descriptions', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashExportDescriptions'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // export items descriptions
        register_rest_route($this->namespace, '/dash/export/images', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashExportImages'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        //import images
        register_rest_route($this->namespace, '/dash/import/images', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'POST',
                'callback' => array($this, 'dashImportImages'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));

        // import descriptions
        register_rest_route($this->namespace, '/dash/import/descriptions', array(
            array(
                'methods' => 'POST',
                'callback' => array($this, 'dashImportItemsDescriptions'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // import settings
        register_rest_route($this->namespace, '/dash/import/settings', array(
            array(
                'methods' => 'POST',
                'callback' => array($this, 'dashImportSettings'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // Check the api key and send the website for sync
        register_rest_route($this->namespace, '/dash/check_apikey', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashCheckApiKey'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // Save the api key
        register_rest_route($this->namespace, '/dash/save_apikey', array(
            array(
                'methods' => 'POST',
                'callback' => array($this, 'dashSaveApiKey'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // Get the opening hours (business Hours)
        register_rest_route($this->namespace, '/dash/opening_hours', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashGetOpeningHours'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // Get the autosync status
        register_rest_route($this->namespace, '/dash/autosync', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashGetAutoSyncStatus'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // Change the auto sync status
        register_rest_route($this->namespace, '/dash/autosync', array(
            array(
                'methods' => 'POST',
                'callback' => array($this, 'dashUpdateAutoSyncStatus'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // Get the detail of the auto sync status
        register_rest_route($this->namespace, '/dash/autosync_details', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashGetAutoSyncDetails'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // Get the names of items based on their UUID
        register_rest_route($this->namespace, '/dash/autosync_items_names', array(
            array(
                'methods' => 'POST',
                'callback' => array($this, 'dashGetAutoSyncItemsNames'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));



    }

    /**
     * @param $request
     * @return array|WP_Error
     */
    public function dashGetCategory( $request ){

        $response = array();
        if ( !isset($request["cat_id"]) || empty( $request["cat_id"] ) ) {
            return new WP_Error( 'category_id_required', 'Category id not found', array( 'status' => 404 ) );
        }
        $category = $this->model->getCategory($request["cat_id"]);

        if($category === null )
            return new WP_Error( 'category_not_found', 'Category not found', array( 'status' => 404 ) );

        $response["uuid"]           = $category->uuid;
        $response["name"]           = stripslashes($category->name);
        $response["alternate_name"] = stripslashes($category->alternate_name);
        $response["image_url"]      = $category->image_url;
        $response["description"]    = stripslashes($category->description);
        $response["sort_order"]     = intval($category->sort_order);
        $response["custom_hours"]   = $category->custom_hours;
        $response["time_availability"]     = $category->time_availability;

        $response["items"]= array();

        if($category->items != "") {
            $items_uuids = explode(",",$category->items);

            foreach ($items_uuids as $items_uuid) {
                if($items_uuid == "") continue;
                $item = $this->model->getItem($items_uuid);
                if(!$item)
                    continue;
                $final_item = array();

                $final_item["uuid"]         =   $item->uuid;
                $final_item["name"]         =   stripslashes($item->name);
                $final_item["alternate_name"]      =   stripslashes($item->alternate_name);
                $final_item["description"]         =   stripslashes($item->description);
                $final_item["price"]        =   $item->price;
                $final_item["price_type"]   =   $item->price_type;
                $final_item["unit_name"]    =   $item->unit_name;
                $final_item["sort_order"]   =   intval($item->sort_order);
                $final_item["visible"]   =   intval($item->visible);

                if($this->useAlternateNames && isset($item->alternate_name) && trim($item->alternate_name)!== ""){
                    $final_item["name"] = stripslashes($item->alternate_name);
                } else {
                    $final_item["name"] = stripslashes($item->name);
                }

                array_push($response['items'],$final_item);
            }
            usort($response["items"], array($this,'sortBySortOrder'));
        }
        // Return response data.
        return $response;
    }

    /**
     * @param $request
     * @return array|WP_Error
     */
    function dashUpdateCategory( $request ) {

        if ( !isset($request["cat_id"]) || empty( $request["cat_id"] ) ) {
            return new WP_Error( 'category_id_required', 'Category id not found', array( 'status' => 404 ) );
        }
        $request_body   = $request->get_body_params();

        $category_name        = sanitize_text_field($request_body['cat_name']);
        $category_description = sanitize_text_field($request_body['cat_description']);
        $result = $this->model->updateCategoryNameAndDescription($request["cat_id"], $category_name, $category_description);

        if($result) {
            return array(
                "status"=>"success"
            );
        } else {
            return array(
                "status"=>"failed"
            );
        }
    }
    /**
     * @param $request
     * @return array|WP_Error
     */
    function dashUpdateCategoryTime( $request ) {
        $request_body   = $request->get_body_params();

        if ( !isset($request["cat_id"]) || empty( $request["cat_id"] ) ) {
            return new WP_Error( 'category_id_required', 'Category id not found', array( 'status' => 404 ) );
        }

        if ( !isset($request_body['status']) || empty( $request_body['status'] ) ) {
            return new WP_Error( 'category_time_status_required', 'Category Time Status not found', array( 'status' => 400 ) );
        }

        $category_status        = sanitize_text_field($request_body['status']);

        if ( $category_status !== "all" && $category_status !== "custom"   ) {
            return new WP_Error( 'category_time_status_required', 'Category Time Must be all or custom', array( 'status' => 400 ) );
        }
        if(isset($request_body['hour'])){
            $category_hour  = sanitize_text_field($request_body['hour']);
        } else {
            $category_hour  = null;
        }

        if(!empty($category_status)) {
            $result = $this->model->updateCategoryTime($request["cat_id"], $category_status, $category_hour);

            if($result) {
                return array(
                    "status"=>"success"
                );
            } else {
                return array(
                    "status"=>"failed"
                );
            }

        }
        return array(
            "status"=>"success"
        );
    }

    function dashGetCategories( $request ){

        $categories = $this->model->getCategories();
        $response = array();
        if(@count($categories) > 0 ){
             foreach ($categories as $cat) {
                 $c = array(
                     "uuid"=>$cat->uuid,
                     "name"=>stripslashes($cat->name),
                     "alternate_name" => "",
                     "description"   => stripslashes($cat->description),
                     "image_url"=>$cat->image_url,
                     "sort_order"=>$cat->sort_order,
                     "show_by_default"=>$cat->show_by_default,
                 );

                 if($this->useAlternateNames && isset($cat->alternate_name) && $cat->alternate_name!==""){
                     $c["name"] = stripslashes($cat->alternate_name);
                 } else {
                     $c["name"] = stripslashes($cat->name);
                 }

                 array_push($response,$c);
             }
             return array(
                 "status"=>"success",
                 "data"=>$response
             );
        } else {
             return array(
                 "status"=>"failed"
             );
        }
    }
    function dashGetCategoriesHours( $request ){

        $hours = $this->api->getMerchantCustomHours("categories");
        $hours = json_decode($hours);
        if($hours !== null  ){
             return array(
                 "status"=>"success",
                 "data"=>$hours
             );
        } else {
             return array(
                 "status"=>"failed"
             );
        }
    }
    function dashGetOrderTypesHours( $request ){

        $hours = $this->api->getMerchantCustomHours("ordertypes");
        $hours = json_decode($hours);
        if($hours !== null ){
             return array(
                 "status"=>"success",
                 "data"=>$hours
             );
        } else {
             return array(
                 "status"=>"failed"
             );
        }
    }
    function dashUpdateApiKey( $request ){

        if ( !isset($request["api_key"]) || empty( $request["api_key"] ) ) {
            return new WP_Error( 'api_key_required', 'New Api Key not found', array( 'status' => 400 ) );
        }
        $api_key = sanitize_text_field($request["api_key"]);
        $checkResult = json_decode($this->api->checkAnyToken($api_key),true);
        //check token
        if(isset($checkResult["status"]) && $checkResult["status"] == "success"){
            //clean inventory
            global $wpdb;
            $settings = (array) get_option("moo_settings");
            if($settings["api_key"] === $api_key) {
                return array(
                    "status"=>false,
                    "message"=>"The API KEY isn't changed"
                );
            }

            //-- Table `item_option`--
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_item_option` ;");
            //-- Table `item_tax_rate` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_item_tax_rate` ;");
            // -- Table `modifier_group` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_item_order` ;");
            //*-- Table `item_tag` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_item_tag` ;");
            //-- Table `item_modifier_group` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_item_modifier_group` ;");
            //-- Table `order_types --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_images` ;");
            //-- Table `item` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_item` ;");
            //-- Table `orders` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_order` ;");
            //-- Table `option`--
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_option` ;");
            //-- Table `tag` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_tag` ;");
            //-- Table `tax_rate` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_tax_rate` ;");
            //-- Table `modifier` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_modifier` ;");
            //-- Table `category` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_category` ;");
            //-- Table `attribute` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_attribute` ;");
            //-- Table `item_group` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_item_group` ;");
            //-- Table `modifier_group` --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_modifier_group` ;");
            //-- Table `order_types --
            $wpdb->query("DELETE FROM `{$wpdb->prefix}moo_order_types` ;");
            //change it
            $settings = (array) get_option("moo_settings");
            $settings["api_key"] = $api_key;
            $settings["jwt-token"] = "";
            update_option("moo_settings",$settings);
            //return response
            return array(
                "status"=>true,
                "message"=>"The API KEY changed successfully"
            );
        } else {
            return array(
                "status"=>false,
                "message"=>"This API KEY isn't correct"
            );
        }
    }
    function dashCheckApiKey( $request ){
        $settings = (array) get_option("moo_settings");
        if (isset($settings["api_key"])){
            $body = array(
                "api_key"=>$settings["api_key"],
                "home_url"=>get_option("home"),
                "restapi_url"=>get_rest_url(),
                "version"=>$this->version
            );
            $response = $this->api->checkApiKey($body);
            if($response && is_array($response)){
                if($response["httpCode"] === 400 ||  $response["httpCode"] === 500 ){
                    return array(
                        "status"=>"failed",
                        "message"=>"An error has occurred, please refresh the page"
                    );
                }
                if($response["httpCode"] === 404 ){
                    return array(
                        "status"=>"failed",
                        "message"=>"The API KEY isn't valid"
                    );
                }
                if($response["httpCode"] === 401 ){
                    return array(
                        "status"=>"failed",
                        "message"=>"The api key is valid but your website isn't connected to Clover. Please re-install the app Smart Online Order on your Clover account"
                    );
                }
                if($response["httpCode"] === 200 ){
                    $result = json_decode($response["responseContent"], true);

                    //check blackout status
                    $blackoutStatusResponse = $this->api->getBlackoutStatus();
                    if(isset($blackoutStatusResponse["status"]) && $blackoutStatusResponse["status"] === "close"){
                        $result["BlackoutStatus"] = "close";
                        $result["BlackoutMessage"] = '<div class="">The store is currently closed. You can change this from your Clover Device. If you recently made changes on your Clover Device, <a href="admin.php?page=moo_index&syncBlackout=true">click here</a> to sync those changes.</div>';
                    } else {
                        $result["BlackoutStatus"] = "open";
                    }
                    return $result;
                }
            }
            return array(
                "status"=>"failed",
                "message"=>"We couldn't check the api key right now, please try again"
            );
        } else {
            return array(
                "status"=>"failed",
                "message"=>"The API KEY isn't valid"
            );
        }
    }
    function dashGetOpeningHours( $request ){
        return $this->api->getOpeningHours();
    }
    function dashGetAutoSyncStatus( $request ){
        $url = get_option("home");
        $res = $this->api->getAutoSyncStatus($url);
        if($res){
            return array(
                "status"=>($res["enabled"])?"enabled":"disabled"
            );
        }
        return array(
            "status"=>"disabled"
        );
    }
    function dashUpdateAutoSyncStatus( $request ){
        $request_body   = $request->get_body_params();
        $url = get_option("home");
        if (isset($request_body["status"])){
            $status = $request_body["status"] === "enabled";
            $res = $this->api->updateAutoSyncStatus($url,$status);
            if($res){
                return array(
                    "status"=>"success"
                );
            }
        }
        return array(
            "status"=>"failed"
        );
    }
    /**
     * @param $request
     * @return array|WP_Error
     */
    function dashSaveApiKey( $request ) {
        $request_body   = $request->get_body_params();
        $settings = (array) get_option("moo_settings");
        if ( !isset($request_body["api_key"]) || empty( $request_body["api_key"] ) ) {
            return new WP_Error( 'api_key_required', 'API KEY is not found', array( 'status' => 404 ) );
        }

        if (isset($request_body["api_key"])){
            $body = array(
                "api_key"=>$request_body["api_key"],
                "home_url"=>get_option("home"),
                "restapi_url"=>get_rest_url(),
                "version"=>$this->version
            );
            $response = $this->api->checkApiKey($body);
            if($response && is_array($response)){
                if($response["httpCode"] === 400 ||  $response["httpCode"] === 500 ){
                    return array(
                        "status"=>"failed",
                        "message"=>"An error has occurred, please refresh the page"
                    );
                }
                if($response["httpCode"] === 404 ){
                    return array(
                        "status"=>"failed",
                        "message"=>"The API KEY isn't valid"
                    );
                }
                if($response["httpCode"] === 401 ){
                    $settings["api_key"] = $request_body["api_key"];
                    update_option("moo_settings",$settings);
                    return array(
                        "status"=>"failed",
                        "message"=>"The api key is valid but your website isn't connected to Clover. Please re-install the app Smart Online Order on your Clover account"
                    );
                }
                if($response["httpCode"] === 200 ){
                    $settings["api_key"] = $request_body["api_key"];
                    update_option("moo_settings",$settings);
                    $result = json_decode($response["responseContent"], true);
                    return $result;
                }
            }
            return array(
                "status"=>"failed",
                "message"=>"We couldn't check the api key right now, please try again"
            );
        } else {
            return array(
                "status"=>"failed",
                "message"=>"The API KEY isn't valid"
            );
        }
    }
    function dashExportSettings( $request ){
        $settings = (array) get_option("moo_settings");
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename=settings.json');
        header('Pragma: no-cache');
        echo json_encode($settings);
        exit();
    }
    function dashExportDescriptions( $request ){
        global $wpdb;
        //-- Table `item_option`--
        $data = $wpdb->get_results("SELECT uuid,name,description FROM `{$wpdb->prefix}moo_item` where description is not null;");
        if($data){
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename=items_descriptions.json');
            header('Pragma: no-cache');
            echo json_encode($data);
            exit();
        } else {
            return array(
                "status"=>false,
                "message"=>"An error has occurred please try again"
            );
        }
    }
    function dashExportImages( $request ){
        global $wpdb;
        $data = array(
            "items"=>array(),
            "categories"=>array()
        );
        //get items images

        $data["items"] = $wpdb->get_results("SELECT items.uuid,items.name,images.url,images.is_default,images.is_enabled FROM `{$wpdb->prefix}moo_item` items,`{$wpdb->prefix}moo_images` images where images.item_uuid = items.uuid");

        // get categories images

        $data["categories"] = $wpdb->get_results("SELECT uuid,name,image_url,description FROM `{$wpdb->prefix}moo_category` where image_url is not null");

        //export
        if($data){
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename=images.json');
            header('Pragma: no-cache');
            echo json_encode($data);
            exit();
        } else {
            return array(
                "status"=>false,
                "message"=>"An error has occurred please try again"
            );
        }
    }
    function dashImportSettings( $request ){

        $permittedExtension = 'json';
        $permittedTypes = ['application/json', 'text/plain'];

        try  {
            $files = $request->get_file_params();

            if ( !isset( $files['file'] ) || empty( $files['file'] ) ) {
                return new WP_Error( 'data_required', 'New Data not found', array( 'status' => 400 ) );
            }

            $file = $files['file'];

            // confirm no file errors
            if (! $file['error'] === UPLOAD_ERR_OK ) {
                return new WP_Error( 'Upload error: ' . $file['error'], array( 'status' => 400 ) );
            }
            // confirm extension meets requirements
            $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
            if ( $ext !== $permittedExtension ) {
                return new WP_Error( 'Invalid extension. ', array( 'status' => 400 ));
            }
            // check type
            /*
            $mimeType = mime_content_type($file['tmp_name']);
            if ( !in_array( $file['type'], $permittedTypes )
                || !in_array( $mimeType, $permittedTypes ) ) {
                return new WP_Error( 'Invalid mime type' , array( 'status' => 400 ));
            }
            */
            $handle = fopen( $file['tmp_name'], 'r' );
            $filecontent =  fread($handle,filesize($file['tmp_name']));

            $data = json_decode($filecontent,true);
            update_option("moo_settings",$data);
            return array(
                "status"=>true,
                "message"=>"imported"
            );
        } catch (Exception $e){
            return array(
                "status"=>false,
                "message"=>$e->getMessage()
            );
        }
    }
    function dashImportItemsDescriptions( $request ){
        global $wpdb;
        $body = $request->get_file_params();

        $permittedExtension = 'json';
        $permittedTypes = ['application/json', 'text/plain'];

        $files = $request->get_file_params();
        $headers = $request->get_headers();

        if ( !isset( $files['file'] ) || empty( $files['file'] ) ) {
            return new WP_Error( 'data_required', 'New Data not found', array( 'status' => 400 ) );
        }


        $file = $files['file'];

        // confirm no file errors
        if (! $file['error'] === UPLOAD_ERR_OK ) {
            return new WP_Error( 'Upload error: ' . $file['error'], array( 'status' => 400 ) );
        }
        // confirm extension meets requirements
        $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
        if ( $ext !== $permittedExtension ) {
            return new WP_Error( 'Invalid extension. ', array( 'status' => 400 ));
        }
        // check type
        /*
        $mimeType = mime_content_type($file['tmp_name']);
        if ( !in_array( $file['type'], $permittedTypes )
            || !in_array( $mimeType, $permittedTypes ) ) {
            return new WP_Error( 'Invalid mime type' , array( 'status' => 400 ));
        }
        */
        $handle = fopen( $file['tmp_name'], 'r' );
        $filecontent =  fread($handle,filesize($file['tmp_name']));

        $data = json_decode($filecontent,true);
        $counter = 0;
        foreach ($data as $item){
            if(isset($item["description"])){
                $desc = esc_sql($item["description"]);
                $name = esc_sql($item["name"]);
                $nameTab = explode("\u",$name);
                $sql = "UPDATE `{$wpdb->prefix}moo_item` 
                        SET description = '{$desc}' 
                        WHERE uuid = '{$item["uuid"]}';";
                $res = $wpdb->query($sql);
                if($res === 0){
                    $sql = "UPDATE `{$wpdb->prefix}moo_item` 
                        SET description = '{$desc}' 
                        WHERE name like '%{$nameTab[0]}%' ;";
                    $res2 = $wpdb->query($sql);
                    if($res2){
                        $counter++;
                    }
                } else {
                    $counter++;
                }
            }
        }
        return array(
            "status"=>true,
            "message"=>"imported"
        );
    }
    function dashImportImages( $request ){
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        global $wpdb;
        if ( isset( $request["cloneImages"] ) ) {
            $cloneImages = $request["cloneImages"];
        } else {
            $cloneImages = true;
        }

        $permittedExtension = 'json';
        $permittedTypes = ['application/json', 'text/plain'];

        $files = $request->get_file_params();
        $headers = $request->get_headers();

        if ( !isset( $files['file'] ) || empty( $files['file'] ) ) {
            return new WP_Error( 'data_required', 'New Data not found', array( 'status' => 400 ) );
        }


        $file = $files['file'];
        // confirm no file errors
        if (! $file['error'] === UPLOAD_ERR_OK ) {
            return new WP_Error( 'Upload error: ' . $file['error'], array( 'status' => 400 ) );
        }
        // confirm extension meets requirements
        $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
        if ( $ext !== $permittedExtension ) {
            return new WP_Error( 'Invalid extension. ', array( 'status' => 400 ));
        }
        // check type
        /*
        $mimeType = mime_content_type($file['tmp_name']);
        if ( !in_array( $file['type'], $permittedTypes )
            || !in_array( $mimeType, $permittedTypes ) ) {
            return new WP_Error( 'Invalid mime type' , array( 'status' => 400 ));
        }
        */
        $handle = fopen( $file['tmp_name'], 'r' );
        $filecontent =  fread($handle,filesize($file['tmp_name']));

        $data = json_decode($filecontent,true);
        $upload_dir = wp_upload_dir();

        if(isset($data["items"]) && is_array($data["items"])){
            foreach ($data["items"] as $item) {
                if(isset($item["url"])){
                    if($cloneImages){
                        try{
                            $image_data = file_get_contents( $item["url"] );
                        } catch (Exception  $e){
                            continue;
                        }
                        $filename = basename( $item["url"] );
                        $filetype = wp_check_filetype( $filename, null );

                        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
                            $file = $upload_dir['path'] . '/' . $filename;
                        }
                        else {
                            $file = $upload_dir['basedir'] . '/' . $filename;
                        }
                        file_put_contents( $file, $image_data );

                        $link_image = $upload_dir['url'] ."/". $filename;
                    } else {
                        $link_image = $item["url"];
                    }

                    //get item uuid based  on name and uuid

                    $name = esc_sql($item["name"]);
                    $sql = "SELECT * FROM `{$wpdb->prefix}moo_item` 
                        WHERE name like '{$name}'
                        OR uuid = '{$item["uuid"]}';";
                    $oneItem = $wpdb->get_row($sql);
                    if ($oneItem){
                        if($cloneImages){
                            //add uploaded image as an attachement
                            $attachment = array(
                                'guid'           => $file,
                                'post_mime_type' => $filetype['type'],
                                'post_title'     => sanitize_file_name( $filename ),
                                'post_content'   => '',
                                'post_status'    => 'inherit'
                            );
                            $attach_id = wp_insert_attachment( $attachment, $file );
                            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                            wp_update_attachment_metadata( $attach_id, $attach_data );
                        }

                        //remove old images
                        $wpdb->delete("{$wpdb->prefix}moo_images",array(
                            "item_uuid"=>$oneItem->uuid
                        ));
                        //add new image
                        $wpdb->insert("{$wpdb->prefix}moo_images",array(
                            "item_uuid"=>$oneItem->uuid,
                            "url"=>$link_image,
                            "is_default"=>($item["is_default"])?$item["is_default"]:1,
                            "is_enabled"=>($item["is_enabled"])?$item["is_enabled"]:1
                        ));
                    }
                }
            }
        }
        if(isset($data["categories"]) && is_array($data["categories"])){
            foreach ($data["categories"] as $category) {
                if(isset($category["image_url"]) && (isset($category["uuid"]) || isset($category["name"]))){
                    if($cloneImages){
                        try{
                            $image_data = file_get_contents( $category["image_url"] );
                        } catch (Exception  $e){
                            continue;
                        }
                        $filename = basename( $category["image_url"] );
                        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
                            $file = $upload_dir['path'] . '/' . $filename;
                        }
                        else {
                            $file = $upload_dir['basedir'] . '/' . $filename;
                        }
                        file_put_contents( $file, $image_data );

                        $link_image = $upload_dir['url'] ."/". $filename;

                        //add uploaded image as an attachement
                        $attachment = array(
                            'guid'           => $file,
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => sanitize_file_name( $filename ),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );
                        $attach_id = wp_insert_attachment( $attachment, $file );
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                        wp_update_attachment_metadata( $attach_id, $attach_data );

                    } else {
                        $link_image =  $category["image_url"];
                    }


                    if(isset($category["name"])){
                        $name     = esc_sql($category["name"]);
                    } else {
                        $name = null;
                    }
                    if(isset($category["description"])){
                        $cat_desc     = esc_sql($category["description"]);
                    } else {
                        $cat_desc = '';
                    }
                    $sql = "UPDATE `{$wpdb->prefix}moo_category` 
                        SET image_url = '{$link_image}',
                            description = '{$cat_desc}'
                        WHERE uuid = '{$category["uuid"]}';";
                   $res =  $wpdb->query($sql);
                   if($res  === 0 && $name){
                       $sql = "UPDATE `{$wpdb->prefix}moo_category` 
                        SET image_url = '{$link_image}',
                          description = '{$cat_desc}'
                        WHERE name like '{$name}';";
                        $wpdb->query($sql);
                   }
                }
            }
        }
        return array(
            "status"=>true,
            "message"=>"imported"
        );
    }
    function dashGetAutoSyncDetails( $request ){
        $url = get_option("home");
        if (isset($request["page"])){
            $page = intval($request["page"]);
        } else {
            $page = 1;
        }
        $res = $this->api->getAutoSyncDetails($url,$page);
        if($res){
            return $res;
        }

        return array(
            "status"=>"failed"
        );
    }
    function dashGetAutoSyncItemsNames( $request ){
        $request_body   = $request->get_body_params();
        if (isset($request_body["items"]) && is_array($request_body["items"])){
            $itemsString = "(";
            foreach($request_body["items"] as $item) {
                $itemsString .= "'".$item."',";
            }
            $itemsString = substr($itemsString, 0, strlen($itemsString)-1);
            $itemsString .= ")";
            if (strlen($itemsString)>1) {
                $items = $this->model->getItemsNamesByUuids($itemsString);
                $finalResult = array();
                foreach ($items as  $i){
                    $finalResult[$i->uuid] = $i->name;
                }
                return array(
                    "status"=>"success",
                    "data"=>$finalResult
                );
            }
        }

        return array(
            "status"=>"failed"
        );
    }
}