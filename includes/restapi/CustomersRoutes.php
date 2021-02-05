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
        register_rest_route( $this->namespace, '/customers/addresses', array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'getAddresses' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/customers/addresses', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'addAddress' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/customers/addresses', array(
            array(
                'methods'   => 'DELETE',
                'callback'  => array( $this, 'deleteAddress' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/customers/login', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'customerLogin' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/customers/signup', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'customerSignup' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/customers/fblogin', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'customerFbLogin' ),
                'permission_callback' => '__return_true'
            )
        ) );
        register_rest_route( $this->namespace, '/customers/resetpassword', array(
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'customerResetPassword' ),
                'permission_callback' => '__return_true'
            )
        ) );
    }

    /**
     * @param $request
     * @body json
     * @return array
     */
    public function getAddresses( $request ) {
        $fromSession=false;
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

        if($token) {
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
                return
                    array(
                        "status"=>"failed",
                        "message"=>"not logged user"
                    );
            }

        } else {
            return array(
                "status"=>"failed",
                "message"=>"Customer not updated"
            );
        }

    }
    /**
     * @param $request
     * @body json
     * @return array
     */
    public function addAddress( $request ) {
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

        if($token) {
            $request_body = $request->get_json_params();
            $addressOptions = array(
                "token"     => $token,
                "address"   =>  sanitize_text_field($request_body['address']),
                "line2"     =>  sanitize_text_field($request_body['line2']),
                "city"      =>  sanitize_text_field($request_body['city']),
                "state"     =>  sanitize_text_field($request_body['state']),
                "zipcode"   =>  sanitize_text_field($request_body['zipcode']),
                "country"   =>  sanitize_text_field($request_body['country']),
                "lng"       =>  sanitize_text_field( $request_body['lng']),
                "lat"       =>  sanitize_text_field($request_body['lat'])

            );
            $res = $this->api->moo_AddAddress($addressOptions);
            $result= json_decode($res);
            if($result->status == 'success') {
                return array("status"=>"success");
            } else {
                return array("status"=>$result->status);
            }

        } else {
            return array(
                "status"=>"failed",
                "message"=>"Customer not updated"
            );
        }

    }
    public function deleteAddress( $request ) {
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

        if($token) {
            $request_body = $request->get_json_params();
            $address_id = $request_body['address_id'];
            $res = $this->api->moo_DeleteAddresses($address_id,$token);
            $result= json_decode($res);

            if($result->status == 'success') {
                return array("status"=>"success");
            } else {
                return array("status"=>$result->status);
            }

        } else {
            return array(
                "status"=>"failed",
                "message"=>"Customer not updated"
            );
        }

    }
    public function customerLogin( $request ) {
        $request_body = $request->get_json_params();
        $email    = sanitize_text_field($request_body["email"]);
        $password = sanitize_text_field($request_body["password"]);
        $res = $this->api->moo_CustomerLogin($email,sha1($password));
        $result= json_decode($res);
        if($result->status == 'success') {
            $this->session->set($result->token,"moo_customer_token");
            $this->session->set($result->customer_email,"moo_customer_email");
        } else {
            $this->session->set(false,"moo_customer_token");
            $this->session->set(null,"moo_customer_email");
        }
        return $result;
    }
    public function customerFbLogin( $request ) {
        $request_body = $request->get_json_params();
        $customerOptions = array(
            "gender"     => sanitize_text_field($request_body["gender"]),
            "name" => sanitize_text_field($request_body["name"]),
            "email"     => sanitize_text_field($request_body["email"]),
            "id"     => sanitize_text_field($request_body["fbid"])
        );
        $res = $this->api->moo_CustomerFbLogin($customerOptions);
        $result= json_decode($res);
        if($result->status == 'success')
        {
            $this->session->set($result->token,"moo_customer_token");
            $this->session->set($result->customer_email,"moo_customer_email");
        } else {
            $this->session->set(false,"moo_customer_token");
            $this->session->set(null,"moo_customer_email");
        }

        return $result;
    }
    public function customerSignup( $request ) {
        $request_body = $request->get_json_params();
        $password  = sanitize_text_field($request_body["password"]);
        $password  = sha1($password);
        $customerOptions = array(
            "title"     => sanitize_text_field($request_body["title"]),
            "full_name" => sanitize_text_field($request_body["full_name"]),
            "email"     => sanitize_text_field($request_body["email"]),
            "phone"     => sanitize_text_field($request_body["phone"]),
            "password"  => $password,
        );
        $res = $this->api->moo_CustomerSignup($customerOptions);
        $result= json_decode($res);
        if($result->status == 'success') {
            $this->session->set($result->token,"moo_customer_token");
            $this->session->set($result->customer_email,"moo_customer_email");
        }
        return $result;
    }
    public function customerResetPassword( $request ) {
        $request_body = $request->get_json_params();
        $email     = sanitize_text_field($request_body["email"]);
        $res = $this->api->moo_ResetPassword($email);
        $result= json_decode($res);
        return $result;
    }

}