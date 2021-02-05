<?php
/**
 * Created by Mohammed EL BANYAOUI.
 * Sync route to handle all requests to sync the inventory with Clover
 * User: Smart MerchantApps
 * Date: 3/5/2019
 * Time: 12:23 PM
 */
require_once "BaseRoute.php";

class  CheckoutRoutes extends BaseRoute {
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
     * The SESSION
     * @since    1.3.2
     * @access   private
     * @var MOO_SESSION
     */
    private $session;

    /**
     * CustomerRoutes constructor.
     *
     */
    public function __construct($model, $api){

        $this->model          = $model;
        $this->api            = $api;
        $this->pluginSettings = (array) get_option("moo_settings");
        $this->session  =     MOO_SESSION::instance();
    }


    // Register our routes.
    public function register_routes(){
        register_rest_route( $this->namespace, '/checkout', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getCheckoutOptions' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/checkout', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'checkout' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/checkout/delivery_areas', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'deliveryAreas' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/checkout/order_types', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'orderTypes' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/checkout/opening_status', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'openingStatus' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/checkout/verify_number', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'sendSmsVerification' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/checkout/check_verif_code', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'checkVerificationCode' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/checkout/check_coupon', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'checkCouponCode' ),
                'permission_callback' => '__return_true'
            )
        ) );

    }

    /**
     * @param $request
     * @body json
     * @return array
     */
    public function getCheckoutOptions( $request ) {
        $response = array();
       // return $response;
        $response["login2checkout"] = (isset($this->pluginSettings['checkout_login']) && $this->pluginSettings['checkout_login'] == "enabled")?true:false;
        $response["use_sms_verification"] = (isset($this->pluginSettings['use_sms_verification']) && $this->pluginSettings['use_sms_verification'] == "enabled")?true:false;
        $response["use_coupons"] = (isset($this->pluginSettings['use_coupons']) && $this->pluginSettings['use_coupons'] == "enabled")?true:false;
        $response["schedule_orders"] = (isset($this->pluginSettings['order_later']) && $this->pluginSettings['order_later'] == "on")?true:false;
        $response["fb_appid"] = $this->pluginSettings['fb_appid'];
        $response["order_types"] = $this->orderTypes($request);
        $response["opening_status"] = $this->openingStatus($request);
        $response["special_instructions"] = array(
            "accept_special_instructions"=>(isset($this->pluginSettings['use_special_instructions']) && $this->pluginSettings['use_special_instructions'] == "enabled")?true:false,
            "text"=>$this->pluginSettings['text_under_special_instructions'],
            "is_required"=>($this->pluginSettings['special_instructions_required'] === "yes"),
        );
        $response["tips"] = array(
           "accept_tips" => (isset($this->pluginSettings['tips']) && $this->pluginSettings['tips'] == "enabled")?true:false,
            "values"=>explode(",",$this->pluginSettings['tips_selection']),
            "default_value"=>$this->pluginSettings['tips_default']
        );



        $response["payment_methods"]["clover_form"] = $this->pluginSettings["clover_payment_form"];
        $response["payment_methods"]["standard_form"] = $this->pluginSettings["payment_creditcard"];
        $response["payment_methods"]["cash_pickup"] = $this->pluginSettings["payment_cash"];
        $response["payment_methods"]["cash_delivery"] = $this->pluginSettings["payment_cash_delivery"];
        if(isset($this->pluginSettings["service_fees"]) && $this->pluginSettings["service_fees"] !==""){
            $response["services_fees"] = array(
                "name"=>$this->pluginSettings["service_fees_name"],
                "amount"=>$this->pluginSettings["service_fees"],
                "type"=>$this->pluginSettings["service_fees_type"],
            );
        } else {
            $response["services_fees"] = null;
        }


        //check if the store markes as closed from the settings
        if(isset($this->pluginSettings['accept_orders']) && $this->pluginSettings['accept_orders'] === "disabled"){
            $response["store_is_open"] = false;
            if(isset($this->pluginSettings["closing_msg"]) && $this->pluginSettings["closing_msg"] !== '') {
                $response["closing_msg"] = $this->pluginSettings["closing_msg"];
            } else  {
                $response["closing_msg"] = "We are currently closed and will open again soon";
            }
            if(isset($this->pluginSettings["hide_menu_w_closed"]) && $this->pluginSettings["hide_menu_w_closed"] === "on") {
                $response["hide_menu"] = true;
            } else {
                $response["hide_menu"] = false;
            }
        } else {
            $response["store_is_open"] = true;
        }

        //Get blackout status
        $blackoutStatusResponse = $this->api->getBlackoutStatus();
        if(isset($blackoutStatusResponse["status"]) && $blackoutStatusResponse["status"] === "close"){
            $response["store_is_open"] = false;
            if(isset($blackoutStatusResponse["custom_message"]) && !empty($blackoutStatusResponse["custom_message"])){
                $response["closing_msg"] = $blackoutStatusResponse["custom_message"];
            } else {
                $response["closing_msg"] = "We are currently closed and will open again soon";

            }
        }

        //delivery areas
        $response["delivery_areas"]["merchant_lat"] = $this->pluginSettings['lat'];
        $response["delivery_areas"]["merchant_lng"] = $this->pluginSettings['lng'];
        $response["delivery_areas"]["areas"]        = json_decode($this->pluginSettings['zones_json']);
        $response["delivery_areas"]["other_zones"]  = $this->pluginSettings['other_zones_delivery'];
        $response["delivery_areas"]["free_after"]   = $this->pluginSettings['free_delivery'];
        $response["delivery_areas"]["fixed_fees"]   = $this->pluginSettings['fixed_delivery'];
        $response["delivery_areas"]["errorMsg"]     = $this->pluginSettings['delivery_errorMsg'];

        // payment keys
        if($this->pluginSettings["payment_creditcard"] == "on") {
            $cloverKey = $this->api->getPayKey();
            $cloverKey = json_decode($cloverKey);
            if($cloverKey == NULL) {
                return array(
                    "message"=>'this store cannot accept orders, if you are the owner please verify your API Key',
                    "error"=>true
                );
            }
            $response["cloverStandardPaymentKey"]=$cloverKey;
        }
        if(isset($this->pluginSettings["clover_payment_form"]) && $this->pluginSettings["clover_payment_form"] == "on") {

            $cloverPakmsKey = $this->api->getPakmsKey();
            $cloverPakmsKey = json_decode($cloverPakmsKey);
            if($cloverPakmsKey && isset($cloverPakmsKey->status) && $cloverPakmsKey->status == "success") {
                $cloverPakmsKey = $cloverPakmsKey->key;
            } else {
                $cloverPakmsKey = null;
            }
            $response["cloverPakmsPaymentKey"] = $cloverPakmsKey;
        }
        return $response;
    }
    public function orderTypes( $request ) {
        $response = array();
        $visibleOrderTypes = $this->model->getVisibleOrderTypes();
        $HoursResponse = $this->api->getMerchantCustomHoursStatus("ordertypes");
        if( $HoursResponse ){
            $merchantCustomHoursStatus = $HoursResponse;
            $merchantCustomHours = array_keys($merchantCustomHoursStatus);
        } else {
            $merchantCustomHoursStatus = array();
            $merchantCustomHours = array();
        }

        foreach ($visibleOrderTypes as $orderType){
            $tempo = array();
            $tempo["uuid"]=$orderType->ot_uuid;
            $tempo["name"]=$orderType->label;
            $tempo["unavailable_message"]=$orderType->custom_message;
            $tempo["taxable"]=($orderType->taxable == "1")?true:false;
            $tempo["is_delivery"]=($orderType->show_sa == "1")?true:false;
            $tempo["use_coupons"]=($orderType->use_coupons == "1")?true:false;
            $tempo["allow_sc_order"]=($orderType->allow_sc_order == "1")?true:false;
            $tempo["minAmount"]=floatval($orderType->minAmount );
            $tempo["maxAmount"]=floatval($orderType->maxAmount );
            $tempo["available"] = true;
            if(isset($orderType->custom_hours) && !empty($orderType->custom_hours)) {
                if(in_array($orderType->custom_hours, $merchantCustomHours)){
                    $isNotAvailable = $merchantCustomHoursStatus[$orderType->custom_hours] === "close";
                    if ($isNotAvailable){
                        $tempo["available"] = false;
                    }
                }
            }
            array_push($response, $tempo);
        }
        return $response;
    }
    public function deliveryAreas( $request ) {
        $response = array();
        $response["merchant_lat"] = $this->pluginSettings['lat'];
        $response["merchant_lng"] = $this->pluginSettings['lng'];
        $response["areas"] = json_decode($this->pluginSettings['zones_json']);
        $response["other_zones"] = $this->pluginSettings['other_zones_delivery'];
        $response["free_after"] = $this->pluginSettings['free_delivery'];
        $response["fixed_fees"] = $this->pluginSettings['fixed_delivery'];
        return $response;
    }
    public function openingStatus( $request ) {
        $response = array();
        if($this->pluginSettings["order_later"] == "on") {
            $inserted_nb_days = $this->pluginSettings["order_later_days"];
            $inserted_nb_mins = $this->pluginSettings["order_later_minutes"];

            $inserted_nb_days_d = $this->pluginSettings["order_later_days_delivery"];
            $inserted_nb_mins_d = $this->pluginSettings["order_later_minutes_delivery"];

            if($inserted_nb_days === "") {
                $nb_days = 4;
            } else {
                $nb_days = intval($inserted_nb_days);
            }

            if($inserted_nb_mins === "") {
                $nb_minutes = 20;
            } else {
                $nb_minutes = intval($inserted_nb_mins);
            }

            if( $inserted_nb_days_d === "") {
                $nb_days_d = 4;
            } else {
                $nb_days_d = intval($inserted_nb_days_d);
            }

            if($inserted_nb_mins_d === "") {
                $nb_minutes_d = 60;
            } else {
                $nb_minutes_d = intval($inserted_nb_mins_d);
            }

        } else {
            $nb_days = 0;
            $nb_minutes = 0;
            $nb_days_d = 0;
            $nb_minutes_d = 0;
        }


        $oppening_status = json_decode($this->api->getOpeningStatus($nb_days,$nb_minutes),true);
        if($nb_days != $nb_days_d || $nb_minutes != $nb_minutes_d) {
            $oppening_status_d = json_decode($this->api->getOpeningStatus($nb_days_d,$nb_minutes_d),true);
            if(isset($oppening_status_d["pickup_time"])){
                $oppening_status["delivery_time"]=$oppening_status_d["pickup_time"];
            } else {
                $oppening_status["delivery_time"] = null;
            }
        } else {
            $oppening_status["delivery_time"]=$oppening_status["pickup_time"];
        }
        //remove times if schedule_orders disabled
        if($this->pluginSettings["order_later"] != "on") {
            $oppening_status["pickup_time"] = null;
            $oppening_status["delivery_time"] = null;
        } else {
            //Adding asap to pickup time
            if(isset($oppening_status["pickup_time"])) {
                if(isset($this->pluginSettings['order_later_asap_for_p']) && $this->pluginSettings['order_later_asap_for_p'] == 'on')
                {
                    if(isset($oppening_status["pickup_time"]["Today"])) {
                        array_unshift($oppening_status["pickup_time"]["Today"],'ASAP');
                    }
                }
                if(isset($oppening_status["pickup_time"]["Today"])) {
                    array_unshift($oppening_status["pickup_time"]["Today"],'Select a time');
                }

            }
            //Adding asap to delivery time
            if(isset($oppening_status["delivery_time"])) {
                if(isset($this->pluginSettings['order_later_asap_for_d']) && $this->pluginSettings['order_later_asap_for_d'] == 'on')
                {
                    if(isset($oppening_status["delivery_time"]["Today"])) {
                        array_unshift($oppening_status["delivery_time"]["Today"],'ASAP');
                    }
                }
                if(isset($oppening_status["delivery_time"]["Today"])) {
                    array_unshift($oppening_status["delivery_time"]["Today"],'Select a time');
                }

            }
        }


        $oppening_msg = "";

        if($this->pluginSettings['hours'] != 'all' && $oppening_status["status"] == 'close') {
            if(isset($this->pluginSettings["closing_msg"]) && $this->pluginSettings["closing_msg"] !== '') {
                $oppening_msg = $this->pluginSettings["closing_msg"];
            } else  {
                if($oppening_status["store_time"] == '')
                    $oppening_msg = 'Online Ordering Currently Closed'.(($this->pluginSettings['accept_orders_w_closed'] == 'on' )?" Order in Advance Available ":"");
                else
                    $oppening_msg = 'Today\'s Online Ordering Hours '.$oppening_status["store_time"] .' Online Ordering Currently Closed'.(($this->pluginSettings['accept_orders_w_closed'] == 'on' )?" Order in Advance Available ":"");
            }
        }
        if($this->pluginSettings['hours'] != 'all'){
            $oppening_status["accept_orders_when_closed"] = ($this->pluginSettings['accept_orders_w_closed'] == 'on')?true:false;
        } else {
            $oppening_status["accept_orders_when_closed"] = true;
        }
        $oppening_status["message"] = $oppening_msg;
        $oppening_status["schedule_orders"] = (isset($this->pluginSettings['order_later']) && $this->pluginSettings['order_later'] == "on")?true:false;

        return $oppening_status;
    }
    /**
     * @param $request
     * @body json
     * @return array
     */
    public function checkout( $request ) {
        $body = json_decode($request->get_body(),true);

        //Check blackout status
        //Get blackout status
        $blackoutStatusResponse = $this->api->getBlackoutStatus();
        if(isset($blackoutStatusResponse["status"]) && $blackoutStatusResponse["status"] === "close") {

            if(isset($blackoutStatusResponse["custom_message"]) && !empty($blackoutStatusResponse["custom_message"])){
                $errorMsg = $blackoutStatusResponse["custom_message"];
            } else {
                $errorMsg = 'We are currently closed and will open again soon';

            }
            return array(
                'status'	=> 'failed',
                'message'	=> $errorMsg
            );
        }
        //check some required fields

        if (!isset($body["payment_method"])) {
            return array(
                'status'	=> 'failed',
                'message'	=> "Payment method is required"
            );
        } else {
            if($body["payment_method"]  === "creditcard"){
                if(!isset($body["card"])){
                    return array(
                        'status'	=> 'failed',
                        'message'	=> "Payment card is required"
                    );
                } else {
                    if(!isset($body["amount"])){
                        return array(
                            'status'	=> 'failed',
                            'message'	=> "Order amount is required"
                        );
                    }
                    if(!isset($body["tax_amount"])){
                        return array(
                            'status'	=> 'failed',
                            'message'	=> "Tax amount is required"
                        );
                    }
                    if(!isset($body["card"]["expMonth"])){
                        return array(
                            'status'	=> 'failed',
                            'message'	=> "expMonth is required"
                        );
                    }
                    if(!isset($body["card"]["expYear"])){
                        return array(
                            'status'	=> 'failed',
                            'message'	=> "expYear is required"
                        );
                    }
                    if(!isset($body["card"]["cvv"])){
                        return array(
                            'status'	=> 'failed',
                            'message'	=> "cvv is required"
                        );
                    }
                    if(!isset($body["card"]["last4"])){
                        return array(
                            'status'	=> 'failed',
                            'message'	=> "last4 is required"
                        );
                    }
                    if(!isset($body["card"]["first6"])){
                        return array(
                            'status'	=> 'failed',
                            'message'	=> "first6 is required"
                        );
                    }
                    if(!isset($body["card"]["cardEncrypted"])){
                        return array(
                            'status'	=> 'failed',
                            'message'	=> "cardEncrypted is required"
                        );
                    }
                    if(!isset($body["card"]["zip"])){
                        return array(
                            'status'	=> 'failed',
                            'message'	=> "zip is required"
                        );
                    }

                }
            }
        }
        if (!isset($body["customer"])) {
            return array(
                'status'	=> 'failed',
                'message'	=> "Customer is required"
            );
        }
        //service  Fee and delivery fees
        if(isset($this->pluginSettings['service_fees_name']) && !empty($this->pluginSettings['service_fees_name'])){
            $body["service_fee_name"] = $this->pluginSettings['service_fees_name'];
        } else {
            $body["service_fee_name"] = "Service Charge";
        }
        if(isset($this->pluginSettings['delivery_fees_name']) && !empty($this->pluginSettings['delivery_fees_name'])){
            $body["delivery_name"] = $this->pluginSettings['delivery_fees_name'];
        } else {
            $body["delivery_name"] = "Delivery Charge";
        }

        //Check the stock
        $track_stock = $this->api->getTrackingStockStatus();
        if( $track_stock == true ) {
            $itemStocks = $this->api->getItemStocks();
            $itemsQte = array();
            if(count($itemStocks)>0 && isset($body["cart"]) && isset($body["cart"]["items"])){
                //count items
                foreach ($body["cart"]["items"] as $line) {
                    if(isset($line["item"]) && isset($line["item"]["id"])){
                        if(isset($itemsQte[$line["item"]["id"]])){
                            $itemsQte[$line["item"]["id"]]++;
                        } else {
                            $itemsQte[$line["item"]["id"]] = 1;
                        }
                    }
                }

                //check stock
                foreach ($body["cart"]["items"] as $cartLine) {
                    if(isset($cartLine['item']["id"])){
                        $itemStock = $this->getItemStock($itemStocks,$cartLine['item']["id"]);

                        if($itemStock == false)
                            continue;
                        if(isset($itemsQte[$cartLine['item']["id"]])&& $itemsQte[$cartLine['item']["id"]] > $itemStock->stockCount)
                        {
                            $response = array(
                                'status'	=> 'failed',
                                'message'	=> 'The item '.$cartLine['item']["id"].' is low on stock. Please go back and change the quantity in your cart '.(($itemStock->stockCount>0)?"as we have only ".$itemStock->stockCount." left":"")
                            );
                            return $response;
                        } else {
                            if($itemStock->stockCount < 1) {
                                $response = array(
                                    'status'	=> 'failed',
                                    'message'	=> 'The item '.$cartLine['item']["id"].' is out off stock'
                                );
                                return $response;
                            }
                        }
                    }
                }
            }
        }

        //check Scheculde time
        if(isset($body['pickup_day'])) {
            $pickup_time = sanitize_text_field($body['pickup_day']);
        }
        // check hour
        if(isset($body['pickup_hour'])) {
            $pickup_time .= ' at '.$body['pickup_hour'];
        }
        // concat day and hour
        if(isset($pickup_time)) {
            $body["scheduled_time"] = ' Scheduled for '.$pickup_time;
        }

        //show Order number
        if(isset($this->pluginSettings["show_order_number"]) && $this->pluginSettings["show_order_number"] === "on") {
            $nextNumber = intval(get_option("moo_next_order_number"));
            if($nextNumber){
                if(isset($this->pluginSettings["rollout_order_number"]) && $this->pluginSettings["rollout_order_number"] === "on"){
                    if(isset($this->pluginSettings["rollout_order_number_max"]) && $nextNumber > $this->pluginSettings["rollout_order_number_max"] ){
                        $nextNumber = 1;
                    }
                }
            } else {
                $nextNumber = 1;
            }
            $showOrderNumber   = "SOO-".str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $body["show_order_number"] = true;
        } else {
            $showOrderNumber = false;
            $body["show_order_number"] = false;
        }

        //start  preparing the note
        $note = 'SOO' ;

        //check the customer
        if(isset($body["customer"])){
            $customer  = $body["customer"];
            if(isset($customer["name"]) && !empty($customer["name"])){
                $note .= ' | ' .  $customer["name"];
            }
        } else {
            $customer = array();
        }
        //add special instruction to the note
        if(!empty($body['special_instructions'])){
            $note .=' | '.$body['special_instructions'];
        }
        if(isset($body['scheduled_time'])){
            $note .=' | '.$body['scheduled_time'];
        }
        //check the ordertype
        if(isset($body["order_type"]) && !empty($body["order_type"])){
            $orderTypeUuid = sanitize_text_field($body['order_type']);

            $orderType = $this->api->GetOneOrdersTypes($orderTypeUuid);
            $orderTypeFromClover = json_decode(json_encode($orderType));

            if(isset($orderTypeFromClover->code) && $orderTypeFromClover->code == 998)
                return array( 'status'	=> 'Error','message'=> "Sorry, but we are having a brief maintenance. Check back in a few minutes");

            if(isset($orderTypeFromClover->message) && $orderTypeFromClover->message == "401 Unauthorized")
                return array( 'status'	=> 'Error','message'=> "Internal Error, please contact us, if you're the owner verify your API Key or the re-install the app on Clover app Market");

            $orderTypeFromLocal  = (array)$this->model->getOneOrderTypes($orderTypeUuid);
            $isDelivery = ( isset($orderTypeFromLocal['show_sa']) && $orderTypeFromLocal['show_sa'] == "1" )?"Delivery":"Pickup";

            $note .= ' | '.$orderTypeFromClover->label;
            if($isDelivery === 'Delivery' && isset($customer["full_address"]))
                $note .= ' | '.$customer["full_address"];

            if(isset($orderTypeFromLocal['taxable']) && !$orderTypeFromLocal['taxable']){
                $body["tax_removed"] = true;
            }

        }
        //add order title
        if( isset($showOrderNumber) && $showOrderNumber !== false ) {
            $body["title"] = $showOrderNumber;
            if ($body["payment_method"] === "cash"){
                if(isset($isDelivery) && $isDelivery === 'Delivery'){
                    $body["title"] .= " (Will pay upon delivery)";
                } else {
                    $body["title"] .= " (Will pay at location)";
                }
            }
        } else {
            if ($body["payment_method"] === "cash"){
                if(isset($isDelivery) && $isDelivery === 'Delivery'){
                    $body["title"] = "Will pay upon delivery";
                } else {
                    $body["title"] = "Will pay at location";
                }
            }
        }
        //add order note
        $body["note"] = $note;
        // add some merchant info
        $body["merchant"] = array();

        if(isset($this->pluginSettings["merchant_phone"])){
            $body["merchant"]["phone"] = $this->pluginSettings["merchant_phone"];
        }
        if(isset($this->pluginSettings["merchant_email"])){
            $body["merchant"]["emails"] = $this->pluginSettings["merchant_email"];
        }
        //return  $body;
        //send request to the Api
        try{
            $result = $this->api->createOrderV2($body);
            if($result && isset($result["id"])){
                //increment order number
                if(isset($showOrderNumber) && $showOrderNumber!== false ) {
                    update_option("moo_next_order_number",++$nextNumber);
                }
                try {
                    // Save the order locally
                   // $orderId = $this->model->addOrderLocally($body);
                   // $this->model->addLinesOrder($orderId, $body["cart"]["items"]);

                    return $result;

                } catch (Exception $exception){
                    return $result;
                }
            } else {
                return $result;
            }
        } catch (Exception  $e){
            return array(
                "status"=>"error",
                "message"=>"An error has occured please try again",
                "response"=>$result
            );
        }
    }
    /**
     * @param $request
     * @body json
     * @return array
     */
    public function sendSmsVerification( $request ) {
        $body = json_decode($request->get_body(),true);
        $phone_number = sanitize_text_field($body['phone']);
        if( ! isset($phone_number) || empty($phone_number)){
            return array(
                'status'	=> 'error',
                'message'   => 'Please send the phone number'
            );
        }
        if(! $this->session->isEmpty("moo_verification_code") && $phone_number == $this->session->get("moo_phone_number") ) {
            $verification_code = $this->session->get("moo_verification_code");
        } else {
            $verification_code = rand(100000,999999);
            $this->session->set($verification_code,"moo_verification_code");
        }
        $this->session->set($phone_number,"moo_phone_number");
        $this->session->set(false,"moo_phone_verified");

        $res = $this->api->sendVerificationSms($verification_code,$phone_number);
        $response = array(
            'status'	=> $res["status"],
            'code'	=> $verification_code,
            'result'    => $res
        );
        return $response;
    }
    public function checkVerificationCode( $request ) {
        $body = json_decode($request->get_body(),true);
        $verification_code = sanitize_text_field($body['code']);
        if( ! isset($verification_code) || empty($verification_code)){
            return array(
                'status'	=> 'error',
                'message'   => 'Please send the code'
            );
        }

        if($verification_code != null && $verification_code != "" && $verification_code ==  $this->session->get("moo_verification_code") )
        {
            $response = array(
                'status'	=> 'success'
            );
            $this->session->set(true,"moo_phone_verified");

            if(! $this->session->isEmpty("moo_customer_token"))
                $this->api->moo_CustomerVerifPhone($this->session->get("moo_customer_token"), $this->session->get("moo_phone_number"));
            $this->session->delete("moo_verification_code");
        } else {
            $response = array(
                'status'	=> 'error'
            );
        }

        return $response;

    }
    public function checkCouponCode( $request ) {

        $body = json_decode($request->get_body(),true);
        $coupon_code = sanitize_text_field($body['code']);

        if( ! isset($coupon_code) || empty($coupon_code)){
            return array(
                'status'	=> 'error',
                'message'   => 'Please send the coupon code'
            );
        }

        if($coupon_code != null && $coupon_code != "" ) {
            if(isset($this->pluginSettings["use_couponsApp"])) {
                $use_couponsApp = ($this->pluginSettings["use_couponsApp"]=='on');
            } else {
                $use_couponsApp = false;
            }
            // TODO : add integration with coupons app

            $use_couponsApp = false;

            $coupon = $this->api->moo_checkCoupon($coupon_code);
            $coupon = json_decode($coupon,true);
            if($coupon['status'] == "success") {
                $response = array(
                    'status'	=> 'success',
                    "coupon" =>$coupon
                );
            }  else {
                if($use_couponsApp) {
                    $coupon = $this->api->moo_checkCoupon_for_couponsApp($coupon_code);
                    $coupon = json_decode($coupon,true);
                    if(isset($coupon['status']) && $coupon['status'] == "success") {
                        $coupon["use_couponsApp"]  = true;
                        $response = array(
                            'status'	=> 'success',
                            "coupon" =>$coupon
                        );
                    } else {
                        $response = array(
                            'status'	=> 'failed',
                            "message" =>"Coupon not found"
                        );
                    }
                } else {
                    $response = array(
                        'status'	=> 'failed',
                        "message" =>"Coupon not found"
                    );
                }
            }
        } else {
            $response = array(
                'status'	=> 'failed',
                "message" =>"Please enter the coupon code"
            );
        }

        return $response;

    }
    /**
     * Parse items stocks and get the stock of an item passed via param
     * @param $items
     * @param $item_uuid
     * @return bool
     */
    private function getItemStock($items,$item_uuid) {
        foreach ($items as $i) {
            if($i->item->id == $item_uuid)
                return $i;
        }
        return false;
    }
}