<?php
/**
 * Created by Mohammed EL BANYAOUI.
 * Sync route to handle all requests to sync the inventory with Clover
 * User: Smart MerchantApps
 * Date: 3/5/2019
 * Time: 12:23 PM
 */
require_once "BaseRoute.php";

class SyncRoutes extends BaseRoute
{
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
     * SyncRoutes constructor.
     *
     */
    public function __construct($model, $api){
        $this->model    =     $model;
        $this->api      =     $api;
    }


    // Register our routes.
    public function register_routes(){
        // Update category route
        register_rest_route($this->namespace, '/sync/update_category/(?P<cat_id>[a-zA-Z0-9-]+)', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'syncUpdateCategory')
            )
        ));
        // Update item route
        register_rest_route($this->namespace, '/sync/update_item/(?P<item_id>[a-zA-Z0-9-]+)', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods' => 'GET',
                'callback' => array($this, 'syncUpdateItem')
            )
        ));
    }
    function syncUpdateCategory($request) {
        if ( !isset($request["cat_id"]) || empty( $request["cat_id"] ) ) {
            return new WP_Error( 'category_id_required', 'Category id not found', array( 'status' => 404 ) );
        } else {
            $category_id = sanitize_text_field($request["cat_id"]);
            $category = $this->api->getCategoryWithoutSaving($category_id);
            if(isset($category->message)){
                    // catgeory not found in Clover
                    if($category->message === 'Not Found') {
                        $this->model->hideCategory($category_id);
                        return 'updated, thanks';
                    } else {
                        return 'Not updated';
                    }
            } else {
                $this->model->update_category($category);
                return 'Updated, thanks';
            }
        }
    }
    function syncUpdateItem($request) {
        if ( !isset($request["item_id"]) || empty( $request["item_id"] ) ) {
            return new WP_Error( 'item_id_required', 'Item id not found', array( 'status' => 404 ) );
        } else {
            $item_id = sanitize_text_field($request["item_id"]);
            $cloverItem = $this->api->getItemWithoutSaving($item_id);
            $currentItem = $this->model->getItem($item_id);
            if(isset($cloverItem->message) && $cloverItem->message == 'Not Found') {
                if(isset($currentItem)){
                   //Item exist and removed from Clover,hide from local database
                    $this->model->hideItem($item_id);
                    return 'updated, thank you';
                } else {
                    return 'Updated, thanks';
                }
            } else {
                if(isset($cloverItem->id)){
                    if(isset($currentItem) && isset($currentItem->modified_time) && $currentItem->modified_time === $cloverItem->modifiedTime){
                        return 'Updated, thanks';
                    } else {
                        $this->api->update_item($cloverItem);
                        return 'updated, thanks';
                    }

                } else {
                    return "Item not found";
                }
            }
        }
    }

}