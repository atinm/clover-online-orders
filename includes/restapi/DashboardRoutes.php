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
     * The link of the custom hours API.
     * @access   private
     * @var Moo_OnlineOrders_CallAPI
     */
    private $HoursApilink;

    /**
     * SyncRoutes constructor.
     *
     */
    public function __construct($model, $api){
        $this->model    =     $model;
        $this->api      =     $api;
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

        // get all categories
        register_rest_route($this->namespace, '/dash/categories_hours', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashGetCategoriesHours'),
                'permission_callback' => array( $this, 'permissionCheck' )
            )
        ));
        // get all categories
        register_rest_route($this->namespace, '/dash/ordertypes_hours', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'dashGetOrderTypesHours'),
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

        if(!empty($category_name) || !empty($category_description)) {
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
        return array(
            "status"=>"success"
        );
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
                     "alternate_name" => stripslashes($cat->alternate_name),
                     "description"   => stripslashes($cat->description),
                     "image_url"=>$cat->image_url,
                     "sort_order"=>$cat->sort_order,
                     "show_by_default"=>$cat->show_by_default,
                 );
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
}