<?php

/**
 * This Class will handle our first version of the rest api
 * Created by Mohammed EL BANYAOUI.
 * User: Smart MerchantApps
 * Date: 7/27/2017
 * Time: 3:47 PM
 */
class Moo_OnlineOrders_Restapi
{
    // Here initialize our namespace and resource name.
    public function __construct() {
        $this->namespace     = 'moo-clover/v1';
        $this->model  = new moo_OnlineOrders_Model();
        $this->api    = new moo_OnlineOrders_CallAPI();

    }
    // Register our routes.
    public function register_routes() {

        //get categories route
        register_rest_route( $this->namespace, '/categories', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getCategories' )
            )
        ) );

        //get items per category route
        register_rest_route( $this->namespace, '/categories/(?P<cat_id>[a-zA-Z0-9-]+)/items', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getItemsPerCategory' )
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
        if($cats)
        {
            foreach ($cats as $cat) {

                if($cat->show_by_default=="1")
                {
                    $c = array(
                        "uuid"=>$cat->uuid,
                        "name"=>($cat->alternate_name=="")?$cat->name:$cat->alternate_name,
                        "image_url"=>$cat->image_url
                    );
                    if(isset($params["expand"]))
                    {
                        if($params["expand"] == 'five_items') {
                            $c['items'] = array();
                            if($cat->items !="") {
                                $items_uuids = explode(",",$cat->items);
                                $track_stock = $this->api->getTrackingStockStatus();
                                if($track_stock == true)
                                    $itemStocks = $this->api->getItemStocks();
                                else
                                    $itemStocks = false;

                                $count = 0;
                                foreach ($items_uuids as $items_uuid) {
                                    if($items_uuid == "") continue;

                                    $item = $this->model->getItem($items_uuid);
                                    $final_item = array();

                                    //Check if the item if it's disabled
                                    if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE') continue;

                                    //Check if we sent five items
                                    if($count++ == 5) break;

                                    //Check the stock
                                    if($track_stock)
                                        $itemStock = self::getItemStock($itemStocks,$item->uuid);
                                    else
                                        $itemStock = false;


                                    if($item->outofstock == 1 || ($track_stock == true && $itemStock != false && isset($itemStock->stockCount)  && $itemStock->stockCount < 1))
                                    {
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

                                    array_push($c['items'],$final_item);
                                }
                                usort($c['items'],array('Moo_OnlineOrders_Restapi','moo_sort_items'));
                            }
                        } else {
                            if($params["expand"] == 'all_items') {
                                $c['items'] = array();
                                if($cat->items !="") {
                                    $items_uuids = explode(",",$cat->items);
                                    $track_stock = $this->api->getTrackingStockStatus();
                                    if($track_stock == true)
                                        $itemStocks = $this->api->getItemStocks();
                                    else
                                        $itemStocks = false;

                                    foreach ($items_uuids as $items_uuid) {
                                        if($items_uuid == "") continue;

                                        $item = $this->model->getItem($items_uuid);
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

                                        array_push($c['items'],$final_item);
                                    }
                                    usort($c['items'],array('Moo_OnlineOrders_Restapi','moo_sort_items'));
                                }
                            }
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
        $response = array();
        //var_dump($request["cat_id"]);
        if ( !isset($request["cat_id"]) || empty( $request["cat_id"] ) ) {
            return new WP_Error( 'category_id_required', 'Category id not found', array( 'status' => 404 ) );
        }
        $category = $this->model->getCategory($request["cat_id"]);
        if($category === null || $category->show_by_default != "1")
            return new WP_Error( 'category_not_found', 'Category not found', array( 'status' => 404 ) );
        $response["uuid"] = $category->uuid;
        $response["name"] = $category->name;
        $response["image_url"] = $category->image_url;

        $response["items"]= array();

        if($category->items !="") {
            $items_uuids = explode(",",$category->items);
            $track_stock = $this->api->getTrackingStockStatus();
            if($track_stock == true)
                $itemStocks = $this->api->getItemStocks();
            else
                $itemStocks = false;

            foreach ($items_uuids as $items_uuid) {
                if($items_uuid == "") continue;
                $item = $this->model->getItem($items_uuid);
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

                array_push($response['items'],$final_item);
            }
        }
        usort($response["items"], array('Moo_OnlineOrders_Restapi','moo_sort_items'));
        // Return all of our post response data.
        return $response;
    }
    public function search( $request )
    {
        $response = array();
        //var_dump($request["cat_id"]);
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
            //Check if the item if it's disabled
            if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE') continue;

            //Check the stock
            if($track_stock)
                $itemStock = self::getItemStock($itemStocks,$item->uuid);
            else
                $itemStock = false;


            if($item->outofstock == 1 || ($track_stock == true && $itemStock != false && isset($itemStock->stockCount)  && $itemStock->stockCount < 1))
            {
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

            array_push($response['items'],$final_item);
        }


        usort($response["items"], array('Moo_OnlineOrders_Restapi','moo_sort_items'));

        // Return all of our post response data.
        return $response;
    }
    public function getItemsDetail( $request )
    {
        $response = array();
        //var_dump($request["cat_id"]);
        if ( !isset($request["item_id"]) || empty( $request["item_id"] ) ) {
            return new WP_Error( 'item_id_required', 'item id not found', array( 'status' => 404 ) );
        }
        $item = $this->model->getItem($request["item_id"]);

      //  var_dump($item);

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
        $response["name"] = $item->name;
        $response["uuid"] = $item->uuid;
        $response["description"]  =   stripslashes ($item->description);
        $response["price"]        =   $item->price;
        $response["price_type"]   =   $item->price_type;
        $response["unit_name"]    =   $item->unit_name;
        $response["unit_name"]    =   $item->unit_name;
        $response["modifier_groups"] = array();
        $response["images"] = array();

        $mg = $this->model->getModifiersGroup($item->uuid);
        if($mg)
        {
            foreach ($mg as $modifierG) {
                $m = array();
                $m["uuid"] = $modifierG->uuid;
                $m["name"] = ($modifierG->alternate_name=="")?$modifierG->name:$modifierG->alternate_name;
                $m["min_required"] = $modifierG->min_required;
                $m["max_allowd"]   = $modifierG->max_allowd;
                $m["sort_order"]   = $modifierG->sort_order;
                $m["modifiers"] = array();

                $modifiers = $this->model->getModifiers($modifierG->uuid);
                if(count($modifiers)>0)
                {
                    foreach ($modifiers as $modifier) {
                        $res = array();
                        $res["uuid"] = $modifier->uuid;
                        $res["name"] = ($modifier->alternate_name == "")?$modifier->name:$modifier->alternate_name;
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
        // Return all of our post response data.
        return $response;
    }
    public function getCart( $request )
    {
        $response = array();

        if(isset($_SESSION['items']) && !empty($_SESSION['items'])){
            $response['items'] = array();
            foreach ($_SESSION['items'] as $line_id=>$line_content)
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
                if(count($line_content["modifiers"])>0)
                    foreach($line_content["modifiers"] as $modifier)
                    {
                        $final_modifier = array(
                                "uuid"=>$modifier["uuid"],
                                "name"=>($modifier["alternate_name"]!="" && $modifier["alternate_name"]!= null)?$modifier["alternate_name"]:$modifier["name"],
                                "price"=>$modifier["price"],
                                "qty"=>(isset($modifier["qty"]))?intval($modifier["qty"]):1
                            );
                        array_push($line["modifiers"],$final_modifier);

                    }
                $response['items'][$line_id] = $line;
            }
        }
        else
        {
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
                if(isset($_SESSION['items']) && isset($_SESSION['itemsQte'][$item_uuid]) )
                {
                    if($itemStock != false && isset($itemStock->stockCount) && (($_SESSION['itemsQte'][$item_uuid]+$qte)>$itemStock->stockCount))
                    {

                        $response = array(
                            'status'	=> 'error',
                            'message'   => "Unfortunately, we are low on stock please change the quantity amount.".((($itemStock->stockCount-$_SESSION['itemsQte'][$item_uuid])>0)?" You can add only ".($itemStock->stockCount-$_SESSION['itemsQte'][$item_uuid])." units":""),
                            'quantity'   => $itemStock->stockCount
                        );
                        return $response;
                    }
                    else
                    {
                        $_SESSION['itemsQte'][$item_uuid] += $qte;
                    }

                }
                else
                {
                    if($itemStock != false && isset($itemStock->stockCount) && $qte>$itemStock->stockCount)
                    {
                        $response = array(
                            'status'	=> 'error',
                            'message'   => "Unfortunately, we are low on stock please change the quantity amount we have only ".$itemStock->stockCount." left",
                            'quantity'   => $itemStock->stockCount
                        );
                        return $response;
                    }
                    else
                    {
                        $_SESSION['itemsQte'][$item_uuid] = $qte;
                    }

                }
            }

            if(isset($_SESSION['items']) && array_key_exists($cart_line_id,$_SESSION['items']) )
            {
                $_SESSION['items'][$cart_line_id]['quantity']+=$qte;
                $response = array(
                    'status'	=> 'success',
                    'name'      => $item->name,
                    'nb_items'  =>$this->moo_get_nbItems_in_cart()
                );
            }
            else
            {
                $_SESSION['items'][$cart_line_id] = array(
                    'item'=>$item,
                    'quantity'=>$qte,
                    'special_ins'=>$special_ins,
                    'tax_rate'=>$this->model->getItemTax_rate( $item_uuid ),
                    'modifiers'=>array()
                );
                $response = array(
                    'status'	=> 'success',
                    'name'      => $item->name,
                    'nb_items'  =>$this->moo_get_nbItems_in_cart()
                );
            }
            //Adding modifiers
            foreach ($item_modifiers as $modifier) {
                $modifier_uuid = $modifier['uuid'];
                $modifierInfos = (array)$this->model->getModifier($modifier_uuid);
                $q = intval($modifier['qty']);
                $modifierInfos["qty"] = ($q<1)?1:$q;
                // $_SESSION['items'][$cart_line_id]['modifiers'][$modifier_uuid] = array("modifier"=>(array)$modifierInfos,"qty"=>$modifier['qty']);
                $_SESSION['items'][$cart_line_id]['modifiers'][$modifier_uuid] = $modifierInfos;
            }
        }
        else
        {
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

        if($line_id != "")
        {
            if(isset($_SESSION['items'][$line_id]) && !empty($_SESSION['items'][$line_id])){
                $item_uuid = $_SESSION['items'][$line_id]['item']->uuid;
                if(isset($_SESSION['itemsQte'][$item_uuid]))
                {
                    $_SESSION['itemsQte'][$item_uuid] -= $_SESSION['items'][$line_id]['quantity'];
                    if($_SESSION['itemsQte'][$item_uuid]<=0)
                        unset($_SESSION['itemsQte'][$item_uuid]);
                }
                unset($_SESSION['items'][$line_id]);
            }
            $response = array(
                'status'	=> 'success',
                'nb_items'  =>$this->moo_get_nbItems_in_cart()
            );
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Item not found in database, please refresh the page'
            );
        }
        self::moo_refresh_itemQte_cart();
        return $response;
    }
    public function updateSpecialInstructionforItem( $request )
    {
        $request_body   = $request->get_body_params();
        $line_id     = sanitize_text_field($request_body['line_id']);
        $special_ins    = sanitize_text_field($request_body['special_ins']);

        if($line_id != "")
        {
            if(isset($_SESSION['items'][$line_id]) && !empty($_SESSION['items'][$line_id])){
                $_SESSION['items'][$line_id]["special_ins"] = $special_ins;
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
            $item_uuid = $_SESSION['items'][$line_id]['item']->uuid;

            $track_stock = $this->api->getTrackingStockStatus();
            if($track_stock == true)
            {
                $itemStocks = $this->api->getItemStocks();
                $itemStock  = $this->getItemStock($itemStocks,$item_uuid);
            }
            else
            {
                $itemStock = false;
            }

            if($track_stock && ($itemStock != false && isset($itemStock->stockCount) && $itemStock->stockCount<$qty))
            {
                $response = array(
                    'status'	=> 'error',
                    'message'   => "Unfortunately, we are low on stock please change the quantity amount",
                    'quantity'   => $itemStock->stockCount
                );
            }
            else
            {
                if(isset($_SESSION['items'][$line_id]) && !empty($_SESSION['items'][$line_id])){
                    $_SESSION['items'][$line_id]["quantity"] = $qty;

                    if(isset($_SESSION['itemsQte'][$item_uuid]))
                    {
                        $_SESSION['itemsQte'][$item_uuid] -= $old_qty;
                        $_SESSION['itemsQte'][$item_uuid] += $qty;
                        if($_SESSION['itemsQte'][$item_uuid]<=0)
                            unset($_SESSION['itemsQte'][$item_uuid]);
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
                $res = json_decode($this->api->getItemWithoutSaving($item->uuid));
                if(isset($res->id) && $res->id == $item->uuid)
                {
                    $count++;
                    continue;
                }
                else
                {
                    $r = $this->api->delete_item($item->uuid);
                    if($r)
                    {
                        $removed++;
                    }
                    else
                    {
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
                $res = json_decode($this->api->getCategoryWithoutSaving($cat->uuid));
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
    public function getModifierGroupsSettings( $request )
    {
        $response = array();

        $res = array();
        $settings = (array) get_option("moo_settings");
        if(isset($settings["mg_settings_displayInline"]) && $settings["mg_settings_displayInline"] == "enabled")
        {
            $res["inlineDisplay"] = true;
        }
        else
        {
            $res["inlineDisplay"] = false;
        }
        if(isset($settings["mg_settings_qty_for_all"]) && $settings["mg_settings_qty_for_all"] == "disabled")
        {
            $res["qtyForAll"] = false;
        }
        else
        {
            $res["qtyForAll"] = true;
        }
        if(isset($settings["mg_settings_qty_for_zeroPrice"]) && $settings["mg_settings_qty_for_zeroPrice"] == "disabled")
        {
            $res["qtyForZeroPrice"] = false;
        }
        else
        {
            $res["qtyForZeroPrice"] = true;
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
                foreach ($theme_manifest['settings'] as $item_settings) {
                    if(!isset($settings[$theme.'_'.$item_settings["id"]])) {
                        $settings[$theme.'_'.$item_settings["id"]] = $item_settings["default"];
                    }
                }
            }
            //save changes
            update_option("moo_settings",$settings);
            $response = array(
                'status'	=> 'success'
            );
        }
        else
        {
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
            $settings["api_key"] = $new_api_key;
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
        if(isset($_SESSION['moo_customer_token']) && $_SESSION['moo_customer_token']!="") {
            $token = $_SESSION['moo_customer_token'];

            $res = $this->api->moo_GetCustomer($token);
            $result= json_decode($res);
            if($result->status == 'success')
            {
                return array(
                    "status"=>"success",
                    "customer"=>$result->customer[0] ,
                );
            } else {
                $_SESSION['moo_customer_token'] = false;
                $_SESSION['moo_customer'] = null;
                return array(
                    "status"=>$result->status
                );
            }

        } else {
            return array("status"=>"failed","message"=>"not logged user");
        }

    }
    public function getOrdersForCustomer($request){
        if(isset($_SESSION['moo_customer_token']) && $_SESSION['moo_customer_token']!="") {
            $token = $_SESSION['moo_customer_token'];
            if(isset($request['page'])) {
                $page = intval($request['page']);
            } else {
                $page = 1;
            }

            $res = $this->api->moo_GetOrders($token,$page);
            $result= json_decode($res);
            if($result->status == 'success')
            {
               // $_SESSION['moo_customer'] = $result->customer;
                $orders = array();
                foreach ($result->orders as $order) {
                    $orderId = sanitize_text_field($order->order->uuid);
                    $order_items = $this->model->getItemsOrder($orderId);
                    $orderDetail = $this->model->getOneOrder($orderId);
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
                                array_push($order_item->list_modifiers,$getModifier);
                            }
                        }
                    }
                    //var_dump($order_items);
                    $oneOrder['items'] = $order_items;
                    array_push($orders,$oneOrder);
                }
                return array(
                    "status"=>"success",
                    "orders"=>$orders,
                    "total_orders"=>$result->total_orders->nb,
                    "current_page"=>$page,
                );
            } else {
                $_SESSION['moo_customer_token'] = false;
                $_SESSION['moo_customer'] = null;
                return array(
                    "status"=>$result->status
                );
            }

        } else {
            return array("status"=>"failed","message"=>"not logged user");
        }

    }
    public function getCustomerFavorites($request){

        if(isset($_SESSION['moo_customer_email']) && $_SESSION['moo_customer_email']!="") {
            $customer_email = $_SESSION['moo_customer_email'];
            $items = $this->model->moo_GetBestItems4Customer($customer_email);
        } else {
            return array("status"=>"failed","message"=>"not logged user");
        }

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
            //Check if the item if it's disabled
            if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE') continue;

            //Check the stock
            if($track_stock)
                $itemStock = self::getItemStock($itemStocks,$item->uuid);
            else
                $itemStock = false;


            if($item->outofstock == 1 || ($track_stock == true && $itemStock != false && isset($itemStock->stockCount)  && $itemStock->stockCount < 1))
            {
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

            array_push($response['items'],$final_item);
        }


        usort($response["items"], array('Moo_OnlineOrders_Restapi','moo_sort_items'));
        // Return all of our post response data.
        return $response;

    }
    public function getCustomerAddressess($request){

        if(isset($_SESSION['moo_customer_token']) && $_SESSION['moo_customer_token']!="") {
            $token = $_SESSION['moo_customer_token'];
            $res = $this->api->moo_GetAddresses($token);
            $result= json_decode($res);
            if($result->status == 'success') {
                $res = array("status"=>"success","addresses"=>$result->addresses);
                $_SESSION['moo_customer'] = $result->customer;
                return $res;
            } else {
                $_SESSION['moo_customer_token'] = false;
                $_SESSION['moo_customer'] = null;
                $_SESSION['moo_customer_email'] = null;
            }
        }
        return array("status"=>"failed","message"=>"not logged user");

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
            unset($_SESSION['items']);
            unset($_SESSION['itemsQte']);
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
                //Check the stock before inserting the item to the cart
                if($this->api->getTrackingStockStatus()) {
                    $itemStocks = $this->api->getItemStocks();
                    $itemStock  = $this->getItemStock($itemStocks,$item->item_uuid);
                    if(isset($_SESSION['items']) && isset($_SESSION['itemsQte'][$item_uuid]) ) {
                        if($itemStock != false && isset($itemStock->stockCount) && (($_SESSION['itemsQte'][$item_uuid]+$qte)>$itemStock->stockCount))
                        {

                            $response = array(
                                'status'	=> 'error',
                                'message'   => "Unfortunately, we are low on stock, you can't order the item ".$item->name,
                                'quantity'   => $itemStock->stockCount
                            );
                            //return error
                            return $response;
                        }
                        else
                        {
                            $_SESSION['itemsQte'][$item_uuid] += $qte;
                        }

                    } else {
                        if($itemStock != false && isset($itemStock->stockCount) && $qte>$itemStock->stockCount) {
                            $response = array(
                                'status'	=> 'error',
                                'message'   => "Unfortunately, we are low on stock, you can't order the item ".$item->name,
                                'quantity'   => $itemStock->stockCount
                            );
                            //return error
                            return $response;
                        } else {
                            $_SESSION['itemsQte'][$item_uuid] = $qte;
                        }

                    }
                }

                if(isset($_SESSION['items']) && array_key_exists($cart_line_id,$_SESSION['items']) ) {
                    $_SESSION['items'][$cart_line_id]['quantity']+=$qte;
                } else {
                    $_SESSION['items'][$cart_line_id] = array(
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
                    $_SESSION['items'][$cart_line_id]['modifiers'][$modifier_uuid] = $modifierInfos;
                }
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

        if(isset($_SESSION['moo_customer_token']) && $_SESSION['moo_customer_token']!="") {
            $token = $_SESSION['moo_customer_token'];
            $name = sanitize_text_field($request['name']);
            $email = sanitize_text_field($request['email']);
            $phone = sanitize_text_field($request['phone']);
            $res = $this->api->moo_updateCustomer($name,$email,$phone,$token);
            $result= json_decode($res);
            if($result->status == 'success') {
                $res = array("status"=>"success");
                return $res;
            }
        }
        return array("status"=>"failed","message"=>"not logged user");

    }
    public function updateCustomerPassword($request){

        if(isset($_SESSION['moo_customer_token']) && $_SESSION['moo_customer_token']!="") {
            $token = $_SESSION['moo_customer_token'];
            $current_password = sanitize_text_field($request['current_password']);
            $new_pâssword = sanitize_text_field($request['new_password']);

            if(strlen($new_pâssword)<6) {
                $res =array("status"=>"failed","message"=>"The new password must contain 6 chars at min");
                return $res;
            }
            $res = $this->api->updateCustomerPassword(sha1($current_password),sha1($new_pâssword),$token);
            $result= json_decode($res);
            if($result->status == 'success') {
                $res = array("status"=>"success");
                return $res;
            } else {
                $res =array("status"=>"failed","message"=>"The current password in incorrect");
                return $res;
            }
        }
        return array("status"=>"failed","message"=>"not logged user");

    }

    /* Static functions for internal use */
    public static function moo_refresh_itemQte_cart()
    {
        unset($_SESSION["itemsQte"]);
        foreach ($_SESSION['items'] as $item) {
            $item_uuid = $item["item"]->uuid;
            if(!isset($_SESSION["itemsQte"][$item_uuid]))
                $_SESSION["itemsQte"][$item_uuid] = $item["quantity"];
            else
                $_SESSION["itemsQte"][$item_uuid] += $item["quantity"];

        }
    }
    public static function moo_get_nbItems_in_cart()
    {
        $res = 0;
        if(isset($_SESSION['items']))
            foreach ($_SESSION['items'] as $item) {
               $res += $item["quantity"];
            }
        return $res ;
    }
    public static function moo_CompareTwoObject()
    {
        unset($_SESSION["itemsQte"]);
        foreach ($_SESSION['items'] as $item) {
            $item_uuid = $item["item"]->uuid;
            if(!isset($_SESSION["itemsQte"][$item_uuid]))
                $_SESSION["itemsQte"][$item_uuid] = $item["quantity"];
            else
                $_SESSION["itemsQte"][$item_uuid] += $item["quantity"];

        }
    }
}