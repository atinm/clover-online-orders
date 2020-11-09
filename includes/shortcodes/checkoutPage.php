<?php


class checkoutPage
{
    /**
     * Display or not the header in checkoutPage
     *  Change this to false if you want hide our header that contain information about teh benifets of using an account
     * @var bool
     */
    private $displayPageHeader = true;

    /**
     * the plugin settings
     * @var array()
     */
    private $pluginSettings;

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
     * use or not alternateNames
     * @var bool
     */
    private $useAlternateNames;

    /**
     * checkoutPage constructor.
     */
    public function __construct() {
        $MooOptions = (array)get_option('moo_settings');
        $this->pluginSettings = $MooOptions;
        $this->model = new moo_OnlineOrders_Model();
        $this->api   = new moo_OnlineOrders_CallAPI();

        if(isset($this->pluginSettings["useAlternateNames"])){
            $this->useAlternateNames = ($this->pluginSettings["useAlternateNames"] !== "disabled");
        } else {
            $this->useAlternateNames = true;
        }

    }

    /**
     * @param $atts
     * @param $content
     * @return string
     */
    public function render($atts, $content)
    {
        $this->enqueueStyles();
        $this->enqueueScripts();

        ob_start();
        $session = MOO_SESSION::instance();
        //check store availibilty

        if(isset($this->pluginSettings['accept_orders']) && $this->pluginSettings['accept_orders'] === "disabled"){
            if(isset($this->pluginSettings["closing_msg"]) && $this->pluginSettings["closing_msg"] !== '') {
                $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg">'.$this->pluginSettings["closing_msg"].'</div>';
            } else  {
                $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg">We are currently closed and will open again soon</div>';

            }
            return '<div id="moo_OnlineStoreContainer" >'.$oppening_msg.'</div>';
        }

        //Get blackout status
        $blackoutStatusResponse = $this->api->getBlackoutStatus();
        if(isset($blackoutStatusResponse->status) && $blackoutStatusResponse->status === "close"){

            if(isset($blackoutStatusResponse->custom_message) && !empty($blackoutStatusResponse->custom_message)){
                $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg">'.$blackoutStatusResponse->custom_message.'</div>';
            } else {
                $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg">We are currently closed and will open again soon</div>';

            }
            return '<div id="moo_OnlineStoreContainer" >'.$oppening_msg.'</div>';
        }

       // $nbOfOrdersPerHour = $this->model->nbOfOrdersPerHour();
       // var_dump($nbOfOrdersPerHour);
        $orderTypes = $this->model->getVisibleOrderTypes();
        // Get ordertypes times
        $counter = $this->model->getOrderTypesWithCustomHours();
        if(isset($counter->nb) && $counter->nb > 0 ) {
            $HoursResponse = $this->api->getMerchantCustomHoursStatus("ordertypes");
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

        $nbOfOrderTypes = count($orderTypes);
        $nbOfUnvailableOrderTypes = null;
        if(@count($merchantCustomHours) > 0 && $nbOfOrderTypes > 0){
            $nbOfUnvailableOrderTypes = 0;
            for($i=0;$i<$nbOfOrderTypes;$i++) {
                $orderType  = $orderTypes[$i];
                $orderTypes[$i]->available = true;
                if(isset($orderType->custom_hours) && !empty($orderType->custom_hours)) {
                    if(in_array($orderType->custom_hours, $merchantCustomHours)){
                        $isNotAvailable = $merchantCustomHoursStatus[$orderType->custom_hours] === "close";
                        if ($isNotAvailable){
                            //unset($orderTypes[$i]);
                            $orderTypes[$i]->available = false;
                            $nbOfUnvailableOrderTypes++;
                        }
                    }
                }
            }
        }
        /*
        if($nbOfOrderTypes === $nbOfUnvailableOrderTypes ){
            echo '<div id="moo_checkout_msg">This store cannot accept orders right now, please come back later</div>';
            return ob_get_clean();
        }
        */
        if($this->pluginSettings['scp'] == "on") {
            $cloverKey = array();
        } else {
            $cloverKey = $this->api->getPayKey();
            $cloverKey = json_decode($cloverKey);
            if($cloverKey == NULL) {
                echo '<div id="moo_checkout_msg">This store cannot accept orders, if you are the owner please verify your API Key</div>';
                return ob_get_clean();
            }
        }
        if(isset($this->pluginSettings["clover_payment_form"]) && $this->pluginSettings["clover_payment_form"] == "on") {

            $cloverPakmsKey = $this->api->getPakmsKey();
            $cloverPakmsKey = json_decode($cloverPakmsKey);
            if($cloverPakmsKey && isset($cloverPakmsKey->status) && $cloverPakmsKey->status == "success") {
                $cloverPakmsKey = $cloverPakmsKey->key;
                //localize clover code
                $cloverCodeExist = true;
            } else {
                // var_dump(print_r($cloverPakmsKey,TRUE));
                // string(86) "stdClass Object ( [status] => failed [message] => Cannot get the public key ) "
                $cloverCodeExist = false;
                $cloverPakmsKey = null;
            }
        } else {
            $cloverPakmsKey = null;
        }

        $custom_css = $this->pluginSettings["custom_css"];
        $custom_js  = $this->pluginSettings["custom_js"];

        $total  =   Moo_OnlineOrders_Public::moo_cart_getTotal(true);

        $merchant_proprites = (json_decode($this->api->getMerchantProprietes())) ;

        //Coupons
        if(!$session->isEmpty("coupon")) {
            $coupon = $session->get("coupon");
            if($coupon['minAmount']>$total['sub_total'])
                $coupon = null;
        } else {
            $coupon = null;
        }

        //Include custom css
        if($custom_css != null)
            wp_add_inline_style( "custom-style-cart3", $custom_css );


        if($total === false || !isset($total['nb_items']) || $total['nb_items'] < 1){
            return $this->cartIsEmpty();
        };

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


        $oppening_status = json_decode($this->api->getOpeningStatus($nb_days,$nb_minutes));
        if($nb_days != $nb_days_d || $nb_minutes != $nb_minutes_d)
            $oppening_status_d = json_decode($this->api->getOpeningStatus($nb_days_d,$nb_minutes_d));
        else
            $oppening_status_d = $oppening_status;

        $oppening_msg = "";

        if($this->pluginSettings['hours'] != 'all' && $oppening_status->status == 'close') {
            if(isset($this->pluginSettings["closing_msg"]) && $this->pluginSettings["closing_msg"] !== '') {
                $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg">'.$this->pluginSettings["closing_msg"].'</div>';
            } else  {
                if($oppening_status->store_time == '')
                    $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg">Online Ordering Currently Closed'.(($this->pluginSettings['accept_orders_w_closed'] == 'on' )?"<br/><p style='color: #006b00'>Order in Advance Available</p>":"").'</div>';
                else
                    $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg"><strong>Today\'s Online Ordering hours</strong> <br/> '.$oppening_status->store_time.'<br/>'.(($this->pluginSettings['accept_orders_w_closed'] == 'on' )?"<p style='color: #006b00'>Order in Advance Available</p>":"").'</div>';
            }
        }

        //Adding asap to pickup time
        if(isset($oppening_status->pickup_time)) {
            if(isset($this->pluginSettings['order_later_asap_for_p']) && $this->pluginSettings['order_later_asap_for_p'] == 'on')
            {
                if(isset($oppening_status->pickup_time->Today))
                    array_unshift($oppening_status->pickup_time->Today,'ASAP');
            }
            if(isset($oppening_status->pickup_time->Today))
                array_unshift($oppening_status->pickup_time->Today,'Select a time');

        }

        if(isset($oppening_status_d->pickup_time))
        {
            if(isset($this->pluginSettings['order_later_asap_for_d']) && $this->pluginSettings['order_later_asap_for_d'] == 'on')
            {
                if(isset($oppening_status_d->pickup_time->Today))
                    array_unshift($oppening_status_d->pickup_time->Today,'ASAP');
            }
            if(isset($oppening_status_d->pickup_time->Today))
                array_unshift($oppening_status_d->pickup_time->Today,'Select a time');

        }

        if($this->pluginSettings['hours'] != 'all' && $this->pluginSettings['accept_orders_w_closed'] != 'on' && $oppening_msg != "")
        {
            echo '<div id="moo_OnlineStoreContainer">'.$oppening_msg.'</div>';
            return ob_get_clean();
        }

        $merchant_address =  $this->api->getMerchantAddress();
        $store_page_id     = $this->pluginSettings['store_page'];
        $cart_page_id     = $this->pluginSettings['cart_page'];
        $checkout_page_id     = $this->pluginSettings['checkout_page'];

        $store_page_url    =  get_page_link($store_page_id);
        $cart_page_url    =  get_page_link($cart_page_id);
        $checkout_page_url    =  get_page_link($checkout_page_id);

        // Not localize empty params
        // localize params
        $localizeParams = array(
            "thanks_page","payment_cash_delivery","payment_cash","payment_creditcard","lat","lng","zones_json",
            "other_zones_delivery","free_delivery","fixed_delivery","fb_appid","scp","checkout_login",
            "save_cards","save_cards_fees",'use_sms_verification','clover_payment_form'
        );
        foreach($this->pluginSettings as $key=>$value) {
            if (in_array($key,$localizeParams)) {
                if ($value == "") {
                    $this->pluginSettings[$key] = null;
                }
            }
        }
        if(!isset($this->pluginSettings['save_cards_fees'])){
            $this->pluginSettings['save_cards_fees'] = null;
        }
        if(!isset($this->pluginSettings['clover_payment_form'])){
            $this->pluginSettings['clover_payment_form'] = null;
        }
        if(isset($this->pluginSettings['thanks_page_wp']) && !empty($this->pluginSettings['thanks_page_wp'])){
            $this->pluginSettings['thanks_page'] = get_page_link($this->pluginSettings['thanks_page_wp']);
        }

        wp_localize_script("custom-script-checkout", "moo_OrderTypes",$orderTypes);
        wp_localize_script("custom-script-checkout", "moo_Total",$total);
        wp_localize_script("custom-script-checkout", "moo_Key",(array)$cloverKey);
        wp_localize_script("custom-script-checkout", "moo_thanks_page",$this->pluginSettings['thanks_page']);
        wp_localize_script("custom-script-checkout", "moo_cash_upon_delivery",$this->pluginSettings['payment_cash_delivery']);
        wp_localize_script("custom-script-checkout", "moo_cash_in_store",$this->pluginSettings['payment_cash']);
        wp_localize_script("custom-script-checkout", "moo_pay_online",$this->pluginSettings['payment_creditcard']);
        wp_localize_script("custom-script-checkout", "moo_pickup_time",$oppening_status->pickup_time);
        wp_localize_script("custom-script-checkout", "moo_pickup_time_for_delivery",$oppening_status_d->pickup_time);
        wp_localize_script("display-merchant-map", "moo_merchantLat",$this->pluginSettings['lat']);
        wp_localize_script("display-merchant-map", "moo_merchantLng",$this->pluginSettings['lng']);
        wp_localize_script("display-merchant-map", "moo_merchantAddress",$merchant_address);
        wp_localize_script("display-merchant-map", "moo_delivery_zones",$this->pluginSettings['zones_json']);
        wp_localize_script("display-merchant-map", "moo_delivery_other_zone_fee",$this->pluginSettings['other_zones_delivery']);
        wp_localize_script("display-merchant-map", "moo_delivery_free_amount",$this->pluginSettings['free_delivery']);
        wp_localize_script("display-merchant-map", "moo_delivery_fixed_amount",$this->pluginSettings['fixed_delivery']);
        wp_localize_script("display-merchant-map", "moo_fb_app_id",$this->pluginSettings['fb_appid']);
        wp_localize_script("display-merchant-map", "moo_scp",$this->pluginSettings['scp']);
        wp_localize_script("display-merchant-map", "moo_use_sms_verification",$this->pluginSettings['use_sms_verification']);
        wp_localize_script("display-merchant-map", "moo_checkout_login",$this->pluginSettings['checkout_login']);
        wp_localize_script("display-merchant-map", "moo_save_cards",$this->pluginSettings['save_cards']);
        wp_localize_script("display-merchant-map", "moo_save_cards_fees",$this->pluginSettings['save_cards_fees']);
        wp_localize_script("display-merchant-map", "moo_clover_payment_form",$this->pluginSettings['clover_payment_form']);
        wp_localize_script("display-merchant-map", "moo_clover_key",$cloverPakmsKey);

        if((isset($_GET['logout']) && $_GET['logout']==true))
        {
            $session->delete("moo_customer_token");
            wp_redirect ( $checkout_page_url );
        }
        if($this->pluginSettings['checkout_login']=="disabled") {
            $session->delete("moo_customer_token");
        }
        ?>

        <div id="moo_OnlineStoreContainer">
            <?php echo $oppening_msg; ?>
            <div id="moo_merchantmap">
            </div>
            <div class="moo-row" id="moo-checkout">
                <!--            login               -->
                <div id="moo-login-form" <?php if((!$session->isEmpty("moo_customer_token")) || $this->pluginSettings['checkout_login']=="disabled") echo 'style="display:none;"'?> class="moo-col-md-12 ">
                    <?php if($this->displayPageHeader){ ?>
                        <div class="moo-row login-top-section" tabindex="-1">
                            <div class="login-header" >
                                Why create a  <a href="https://www.smartonlineorder.com" target="_blank">Smart Online Order</a> account?
                            </div>
                            <div class="moo-col-md-6">
                                <ul>
                                    <li>Save your address</li>
                                    <li>Faster Checkout!</li>
                                </ul>
                            </div>
                            <div class="moo-col-md-6">
                                <ul>
                                    <li>View your past orders</li>
                                    <li>Get exclusive deals and coupons</li>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="moo-col-md-6" tabindex="0">
                        <div class="moo-row login-social-section">
                            <?php if(isset($this->pluginSettings['fb_appid']) && $this->pluginSettings['fb_appid']!=""){ ?>
                                <p>
                                    <strong>Sign in</strong> with your social account
                                    <br />
                                    <small>No posts on your behalf, promise!</small>
                                </p>
                                <div class="moo-row">
                                    <div class="moo-col-xs-12 moo-col-sm-6 moo-col-md-7 moo-col-md-offset-3 moo-col-sm-offset-3" >
                                        <a href="#" class="moo-btn moo-btn-lg moo-btn-primary moo-btn-block" onclick="moo_loginViaFacebook(event)" style="margin-top: 12px;" tabindex="0" aria-label="Sign in with your Facebook account">Facebook</a>
                                    </div>
                                    <div class="moo-col-xs-12 moo-col-sm-12 moo-col-md-7 moo-col-md-offset-3" tabindex="0">
                                        <div class="login-or">
                                            <hr class="hr-or">
                                            <span class="span-or">or</span>
                                        </div>
                                        <a role="button" class="moo-btn moo-btn-danger" onclick="moo_loginAsguest(event)" tabindex="0">
                                            Continue As Guest
                                        </a>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <p>
                                    Don't want an account?
                                    <br />
                                    <small>You can checkout without registering</small>
                                </p>
                                <div class="moo-row">
                                    <div class="moo-col-xs-12 moo-col-sm-6 moo-col-md-7 moo-col-md-offset-3 moo-col-sm-offset-3">
                                        <a  role="button" tabindex="0" href="#" class="moo-btn moo-btn-lg moo-btn-primary moo-btn-block" onclick="moo_loginAsguest(event)" style="margin-top: 12px;"> Continue As Guest</a>
                                    </div>
                                    <div class="moo-col-xs-12 moo-col-sm-12 moo-col-md-9 moo-col-md-offset-2">
                                        <div class="login-or">
                                            <hr class="hr-or">
                                            <span class="span-or">or</span>
                                        </div>
                                        <a  class="moo-btn moo-btn-danger" onclick="moo_show_sigupform(event)">
                                            Create An Account
                                        </a>
                                    </div>
                                </div>
                            <?php  } ?>
                        </div>
                        <div class="login-separator moo-hidden-xs moo-hidden-sm">
                            <div class="separator">
                                <span>or</span>
                            </div>
                        </div>
                    </div>
                    <div class="moo-col-md-6" tabindex="0" >
                        <form action="post" onsubmit="moo_login(event)" aria-label="Sign in with your account">
                            <div class="form-group">
                                <label for="inputEmail">Email</label>
                                <input type="text" id="inputEmail" class="moo-form-control" autocomplete="email" aria-label="your email">
                            </div>
                            <div class="moo-form-group">
                                <label for="inputPassword">Password</label>
                                <input type="password"  id="inputPassword" class="moo-form-control" autocomplete="current-password" aria-label="your password">
                                <a class="pull-right" href="#" onclick="moo_show_forgotpasswordform(event)" aria-label="Click here if you forgotten your password">Forgot password?</a>
                            </div>
                            <button id="mooButonLogin" class="moo-btn" onclick="moo_login(event)" aria-label="log in">
                                Log In
                            </button>
                            <p style="padding: 10px;"> Don't have an account<a  href="#" onclick="moo_show_sigupform(event)" aria-label="Don't have an account Sign-up"> Sign-up</a> </p>
                        </form>
                    </div>
                </div>
                <!--            Register            -->
                <div id="moo-signing-form" class="moo-col-md-12">
                    <div class="moo-col-md-8 moo-col-md-offset-2">
                        <form action="post" onsubmit="moo_signin(event)">
                            <div class="moo-form-group">
                                <label for="inputMooFullName">Full Name</label>
                                <input type="text" class="moo-form-control" id="inputMooFullName" autocomplete="fullName">
                            </div>

                            <div class="moo-form-group">
                                <label for="inputMooEmail">Email</label>
                                <input type="text" class="moo-form-control" id="inputMooEmail" autocomplete="email">
                            </div>
                            <div class="moo-form-group">
                                <label for="inputMooPhone">Phone</label>
                                <input type="text" class="moo-form-control" id="inputMooPhone" autocomplete="phone">
                            </div>
                            <div class="moo-form-group">
                                <label for="inputMooPassword">Password</label>
                                <input type="password" class="moo-form-control" id="inputMooPassword" autocomplete="current-password">
                            </div>
                            <p>
                                By clicking the button below you agree to our <a href="https://www.zaytechapps.com/zaytech-eula/" target="_blank">Terms Of Service</a>
                            </p>
                            <button class="moo-btn moo-btn-primary" onclick="moo_signin(event)">
                                Submit
                            </button>
                            <p style="padding: 10px;"> Have an account already?<a  href="#" onclick="moo_show_loginform()"> Click here</a> </p>
                        </form>
                    </div>

                </div>
                <!--            Reset Password      -->
                <div   id="moo-forgotpassword-form" class="moo-col-md-12">
                    <div class="moo-col-md-8 moo-col-md-offset-2">
                        <form action="post" onsubmit="moo_resetpassword(event)">
                            <div class="moo-form-group">
                                <label for="inputEmail4Reset">Email</label>
                                <input type="text" class="moo-form-control" id="inputEmail4Reset">
                            </div>
                            <button class="moo-btn moo-btn-primary" onclick="moo_resetpassword(event)">
                                Reset
                            </button>
                            <button class="moo-btn moo-btn-default" onclick="moo_cancel_resetpassword(event)">
                                Cancel
                            </button>
                        </form>
                    </div>
                </div>
                <!--            Choose address      -->
                <div id="moo-chooseaddress-form" class="moo-col-md-12">
                    <div id="moo-chooseaddress-formContent" class="moo-row">
                    </div>
                    <div class="MooAddressBtnActions">
                        <a class="MooSimplButon" href="#" onclick="moo_show_form_adding_address()">Add Another Address</a>
                        <a class="MooSimplButon" href="#" onclick="moo_pickup_the_order(event)">Click here if this Order is for Pick Up</a>
                    </div>
                    <a class="moologoutlabel" href="?logout=true">Logout</a>
                </div>
                <!--            Add new address      -->
                <div id="moo-addaddress-form" class="moo-col-md-12">
                    <form method="post" onsubmit="moo_addAddress(event)">
                        <h1 tabindex="0" aria-level="1">Add new Address to your account</h1>
                        <div class="moo-col-md-8 moo-col-md-offset-2">
                            <div class="mooFormAddingAddress">
                                <div class="moo-form-group">
                                    <label for="inp utMooAddress">Address</label>
                                    <input type="text" class="moo-form-control" id="inputMooAddress">
                                </div>
                                <div class="moo-form-group">
                                    <label for="inputMooAddress">Suite / Apt #</label>
                                    <input type="text" class="moo-form-control" id="inputMooAddress2">
                                </div>
                                <div class="moo-form-group">
                                    <label for="inputMooCity">City</label>
                                    <input type="text" class="moo-form-control" id="inputMooCity">
                                </div>
                                <div class="moo-form-group">
                                    <label for="inputMooState">State</label>
                                    <input type="text" class="moo-form-control" id="inputMooState">
                                </div>
                                <div class="moo-form-group">
                                    <label for="inputMooZipcode">Zip code</label>
                                    <input type="text" class="moo-form-control" id="inputMooZipcode">
                                </div>
                                <p class="moo-centred">
                                    <button href="#" class="moo-btn moo-btn-warning" onclick="moo_ConfirmAddressOnMap(event)">Next</button>
                                </p>
                            </div>
                            <div class="mooFormConfirmingAddress">
                                <div id="MooMapAddingAddress" tabindex="-1">
                                    <p style="margin-top: 150px;">Loading the MAP...</p>
                                </div>
                                <input type="hidden" class="moo-form-control" id="inputMooLat">
                                <input type="hidden" class="moo-form-control" id="inputMooLng">
                                <div class="form-group">
                                    <button id="mooButonAddAddress" onclick="moo_addAddress(event)" aria-label="Confirm and add address">
                                        Confirm and add address
                                    </button>
                                    <button id="mooButonChangeAddress" onclick="moo_changeAddress(event)" aria-label="Change address">
                                        Change address
                                    </button>
                                </div>
                            </div>
                            <p style="padding: 10px;">If you want to skip this step and add your address later <a role="button" href="#" onclick="moo_pickup_the_order(event)" style="color:blue"> Click here</a> </p>
                        </div>
                    </form>
                </div>
                <!--            Checkout form        -->
                <div id="moo-checkout-form" class="moo-col-md-12" <?php if($this->pluginSettings['checkout_login']=="disabled") echo 'style="display:block;"'?>>
                    <form action="#" method="post" onsubmit="moo_finalize_order(event)">
                        <!--            Checkout form - Informaton section       -->
                        <div class="moo-col-md-7 moo-checkout-form-leftside" tabindex="0" aria-label="the checkout form">
                            <div id="moo-checkout-form-customer" tabindex="0" aria-label="your information">
                                <div class="moo-checkout-bloc-title moo-checkoutText-contact">
                                    contact
                                    <span class="moo-checkout-edit-icon" onclick="moo_checkout_edit_contact()">
                                        <img src="//api.smartonlineorders.com/assets/images/if_edit_103539.png" alt="edit">
                                    </span>
                                </div>
                                <div class="moo-checkout-bloc-content">
                                    <div id="moo-checkout-contact-content">
                                    </div>
                                    <div id="moo-checkout-contact-form">
                                        <div class="moo-row">
                                            <div class="moo-form-group">
                                                <label for="MooContactName" class="moo-checkoutText-fullName">Full Name:*</label>
                                                <input class="moo-form-control" name="name" id="MooContactName">
                                            </div>
                                        </div>
                                        <div class="moo-row">
                                            <div class="moo-form-group">
                                                <label for="MooContactEmail" class="moo-checkoutText-email">Email:*</label>
                                                <input class="moo-form-control" id="MooContactEmail">
                                            </div>
                                        </div>
                                        <div class="moo-row">
                                            <div class="moo-form-group">
                                                <label for="MooContactPhone" class="moo-checkoutText-phoneNumber">Phone number:*</label>
                                                <input class="moo-form-control" name="phone" id="MooContactPhone" onchange="moo_phone_changed()">
                                            </div>
                                        </div>
                                        <?php wp_nonce_field('moo-checkout-form');?>
                                    </div>
                                </div>
                            </div>
                            <div class="moo_chekout_border_bottom"></div>
                            <?php if(count($orderTypes)>0){?>
                                <div id="moo-checkout-form-ordertypes" tabindex="0" aria-label="the ordering method">
                                    <div class="moo-checkout-bloc-title moo-checkoutText-orderingMethod">
                                        ORDERING METHOD*
                                    </div>
                                    <div class="moo-checkout-bloc-content">
                                        <?php
                                        $countOrderTypes = count($orderTypes);
                                        foreach ($orderTypes as $ot) {
                                            if(isset($ot->available) && $ot->available === false){
                                                echo '<div class="moo-checkout-form-ordertypes-option">';
                                                echo '<input class="moo-checkout-form-ordertypes-input" type="radio" name="ordertype" value="'.$ot->ot_uuid.'" id="moo-checkout-form-ordertypes-'.$ot->ot_uuid.'" disabled>';
                                                echo '<label for="moo-checkout-form-ordertypes-'.$ot->ot_uuid.'" style="display: inline;margin-left:15px">'.stripslashes($ot->label).'</label></div>';

                                            } else {
                                                echo '<div class="moo-checkout-form-ordertypes-option">';
                                                echo '<input class="moo-checkout-form-ordertypes-input" type="radio" name="ordertype" value="'.$ot->ot_uuid.'" id="moo-checkout-form-ordertypes-'.$ot->ot_uuid.'">';
                                                echo '<label for="moo-checkout-form-ordertypes-'.$ot->ot_uuid.'" style="display: inline;margin-left:15px">'.stripslashes($ot->label).'</label></div>';
                                            }
                                          }
                                        ?>
                                    </div>
                                    <div class="moo-checkout-bloc-message" id="moo-checkout-form-ordertypes-message">
                                    </div>
                                </div>
                                <div class="moo_chekout_border_bottom"></div>
                            <?php  } ?>
                            <?php
                            if(isset($this->pluginSettings['order_later']) && $this->pluginSettings['order_later'] == 'on' && @count($oppening_status->pickup_time)>0){ ?>
                                <div id="moo-checkout-form-orderdate" tabindex="0" aria-label="Choose a time if you want schedule the order">
                                    <div class="moo-checkout-bloc-title moo-checkoutText-ChooseATime">
                                        CHOOSE A PICKUP TIME
                                    </div>
                                    <div class="moo-checkout-bloc-content">
                                        <div class="moo-row">
                                            <div class="moo-col-md-6">
                                                <div class="moo-form-group">
                                                    <select class="moo-form-control" name="moo_pickup_day" id="moo_pickup_day" onchange="moo_pickup_day_changed(this)">
                                                        <?php
                                                        foreach ($oppening_status->pickup_time as $key=>$val) {
                                                            echo '<option value="'.$key.'">'.$key.'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="moo-col-md-6">
                                                <div class="moo-form-group">
                                            
                                                    <select class="moo-form-control" name="moo_pickup_hour" id="moo_pickup_hour" >
                                                        <?php
                                                        foreach ($oppening_status->pickup_time as $key=>$val) {
                                                            foreach ($val as $h)
                                                                if($h == 'Select a time')
                                                                    echo '<option value="'.$h.'">Select time</option>';
                                                                else 
                                                                    echo '<option value="'.$h.'">'.$h.'</option>';
                                                            break;
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($oppening_status->store_time != '') { ?>
                                            <div class="moo-row">
                                            <!-- //UPGRADE IT START -->
                                            <?php if(isset($this->pluginSettings["order_later_minutes"])): ?>
                                                <?php $leadTime = $this->pluginSettings["order_later_minutes"];
                                                if(is_numeric($leadTime)){
                                                        $num = (int)$leadTime+15;
                                                        $time_range = $leadTime.'-'.$num;
                                                    }
                                                ?>
                                                <div class="moo-col-md-12">
                                                    Please allow at least <span id="lead-time"><?php echo $time_range;  ?></span> minutes from order time till pickup.
                                                </div>
                                            <?php endif; ?>
                                                <!-- // UPGRADE IT END  -->
                                                <div class="moo-col-md-12">
                                                    Today's Online Ordering Hours: <?php echo $oppening_status->store_time  ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="moo_chekout_border_bottom"></div>
                            <?php } ?>
                            <div id="moo-checkout-form-payments" tabindex="0" aria-label="the payments method">
                                <div class="moo-checkout-bloc-title moo-checkoutText-payment" >
                                    PAYMENT  <?php if($this->pluginSettings['payment_cash'] == 'on' || $this->pluginSettings['payment_cash_delivery'] == 'on'){ echo 'METHOD';}?>*
                                </div>
                                <div class="moo-checkout-bloc-content">

                                    <?php
                                    if (isset($cloverCodeExist) && $cloverCodeExist && isset($this->pluginSettings['clover_payment_form']) && $this->pluginSettings['clover_payment_form'] == 'on'){ ?>
                                        <div class="moo-checkout-form-payments-option">
                                            <input class="moo-checkout-form-payments-input" type="radio" name="payments" value="clover" id="moo-checkout-form-payments-clover">
                                            <label for="moo-checkout-form-payments-clover" style="display: inline;margin-left:15px">Pay with Credit Card (Secured By Clover)</label>
                                        </div>
                                    <?php // UPGRADE IT START (clover elseif creditcard)
                                    } elseif (isset($this->pluginSettings['payment_creditcard']) && $this->pluginSettings['payment_creditcard'] == 'on'){ 
                                        //UPGRADE IT END ?>
                                        <div class="moo-checkout-form-payments-option">
                                            <input class="moo-checkout-form-payments-input" type="radio" name="payments" value="creditcard" id="moo-checkout-form-payments-creditcard">
                                            <label for="moo-checkout-form-payments-creditcard" style="display: inline;margin-left:15px">Pay with Credit Card</label>
                                        </div>
                                    <?php } ?>
                                    <?php if($this->pluginSettings['payment_cash'] == 'on' || $this->pluginSettings['payment_cash_delivery'] == 'on'){ ?>
                                        <div class="moo-checkout-form-payments-option">
                                            <input class="moo-checkout-form-payments-input" type="radio" name="payments" value="cash" id="moo-checkout-form-payments-cash">
                                            <label for="moo-checkout-form-payments-cash" style="display: inline;margin-left:15px" id="moo-checkout-form-payincash-label">Pay with Gift Card</label>
                                        </div>
                                    <?php } ?>
                                    <?php if(isset($this->pluginSettings['payment_creditcard']) && $this->pluginSettings['payment_creditcard'] == 'on' && $this->pluginSettings['scp'] !=="on"){ ?>
                                        <div id="moo_creditCardPanel">
                                            <div class="moo-row">
                                                <div class="moo-col-md-12">
                                                    <div class="moo-form-group">
                                                        <label for="Moo_cardNumber" class="control-label moo-checkoutText-cardNumber">Card number</label>
                                                        <input class="moo-form-control" name="cardNumber" id="Moo_cardNumber" placeholder="Debit/Credit Card Number" pattern="[0-9]{13,16}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="moo-row">
                                                <div class="moo-col-md-6">
                                                    <div class="moo-form-group">
                                                        <select name="expiredDateMonth" id="MooexpiredDateMonth" class="moo-form-control">
                                                            <option value="01">Jan (01)</option>
                                                            <option value="02">Feb (02)</option>
                                                            <option value="03">Mar (03)</option>
                                                            <option value="04">Apr (04)</option>
                                                            <option value="05">May (05)</option>
                                                            <option value="06">June(06)</option>
                                                            <option value="07">July(07)</option>
                                                            <option value="08">Aug (08)</option>
                                                            <option value="09">Sep (09)</option>
                                                            <option value="10">Oct (10)</option>
                                                            <option value="11">Nov (11)</option>
                                                            <option value="12">Dec (12)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="moo-col-md-6">
                                                    <div class="moo-form-group">

                                                        <select name="expiredDateYear"id="MooexpiredDateYear"  class="moo-form-control">
                                                            <?php
                                                            $current_year = date("Y");
                                                            if($current_year < 2018 )$current_year = 2020;
                                                            for($i=$current_year;$i<$current_year+20;$i++)
                                                                echo '<option value="'.$i.'">'.$i.'</option>';
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="moo-row">
                                                <div class="moo-col-md-12">
                                                    <div class="moo-form-group">
                                                        <label for="moo_cardcvv" class="moo-control-label moo-checkoutText-cardCvv">Card CVV</label>
                                                        <input class="moo-form-control" name="cvv" id="moo_cardcvv" placeholder="Security Code">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="moo-row">
                                                <div class="moo-col-md-12">
                                                    <div class="moo-form-group">
                                                        <label for="moo_zipcode" class="moo-control-label moo-checkoutText-zipCode">Zip Code</label>
                                                        <input class="moo-form-control" name="zipcode" id="moo_zipcode" placeholder="Zip code">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                    if(isset($cloverCodeExist) && $cloverCodeExist  && isset($this->pluginSettings['clover_payment_form']) && $this->pluginSettings['clover_payment_form'] == 'on'){
                                        $this->cloverCardSection();
                                    }
                                    if($this->pluginSettings['payment_cash'] == 'on' || $this->pluginSettings['payment_cash_delivery'] == 'on'){ ?>
                                        <div id="moo_cashPanel">
                                            <div class="moo-row"  id="moo_verifPhone_verified">
                                                <img src="<?php echo  plugin_dir_url(dirname(__FILE__))."../public/img/check.png"?>" width="60px">
                                                <p>Your phone number has been verified <br/>Please finalize your order below</p>
                                            </div>
                                            <div class="moo-row" id="moo_verifPhone_sending">
                                                <div class="moo-form-group moo-form-inline">
                                                    <p>Gift Card orders may be placed online, <br>but you must bring your gift card to cashier upon arrival.</p>
                                                    <label for="Moo_PhoneToVerify moo-checkoutText-yourPhone">Your phone</label>
                                                    <input class="moo-form-control" id="Moo_PhoneToVerify" style="margin-bottom: 10px" onchange="moo_phone_to_verif_changed()"/>
                                                    <a class="moo-btn moo-btn-primary" href="#" style="margin-bottom: 10px" onclick="moo_verifyPhone(event)">Verify via SMS</a>
                                                    <label for="Moo_PhoneToVerify" class="error" style="display: none;"></label>
                                                </div>
                                                <p>
                                                    We will send a verification code via SMS to number above
                                                </p>
                                            </div>
                                            <div class="moo-row" id="moo_verifPhone_verificatonCode">
                                                <p style='font-size:18px;color:green'>
                                                    Please enter the verification that was sent to your phone, if you didn't receive a code,
                                                    <a href="#" onclick="moo_verifyCodeTryAgain(event)"> click here to try again</a>
                                                </p>
                                                <div class="moo-form-group moo-form-inline">
                                                    <input class="moo-form-control" id="Moo_VerificationCode" style="margin-bottom: 10px"  />
                                                    <a class="moo-btn moo-btn-primary" href="#" style="margin-bottom: 10px" onclick="moo_verifyCode(event)">Submit</a>
                                                    <label for="Moo_VerificationCode" class="error" style="display: none;"></label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="moo_chekout_border_bottom"></div>
                            <!-- Save payment method -->
                            <div id="moo-checkout-form-savecard">
                                <div class="moo-checkout-bloc-content">
                                    <div class="moo-checkout-form-savecard-option">
                                        <input class="moo-checkout-form-savecard-input" type="checkbox" name="moo_save_card" id="moo-checkout-form-savecard" checked>
                                        <label for="moo-checkout-form-savecard" style="display: inline;margin-left:15px">Use this card for future purchase</label>
                                    </div>
                                </div>
                            </div>
                            <div class="moo_chekout_border_bottom"></div>

                            <?php
                                if($this->pluginSettings['tips'] == 'enabled' && isset($merchant_proprites->tipsEnabled) && $merchant_proprites->tipsEnabled){
                                        $this->tipsSection();
                                        $this->borderBottom();
                                }
                                if($this->pluginSettings['use_special_instructions']=="enabled")
                                {
                                    ?>
                                    <div id="moo-checkout-form-instruction">
                                        <div class="moo-checkout-bloc-title moo-checkoutText-instructions">
                                            <label for="Mooinstructions">Special instructions</label>
                                        </div>
                                        <div class="moo-checkout-bloc-content">
                                            <textarea class="moo-form-control" cols="100%" rows="5" id="Mooinstructions"></textarea>
                                            <?php
                                            if(isset($this->pluginSettings['text_under_special_instructions']) && $this->pluginSettings['text_under_special_instructions']!=='') {
                                                echo $this->pluginSettings['text_under_special_instructions'];
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            //Check if coupons are enabled
                            if($this->pluginSettings['use_coupons']=="enabled")
                            {
                                ?>
                                <div class="moo_chekout_border_bottom"></div>
                                <div id="moo-checkout-form-coupon">
                                    <div class="moo-checkout-bloc-title moo-checkoutText-couponCode">
                                        <label for="moo_coupon">Coupon code</label>
                                    </div>
                                    <div class="moo-checkout-bloc-content" id="moo_enter_coupon" style="<?php if($coupon != null) echo 'display:none';?>">
                                        <div class="moo-col-md-8">
                                            <div class="moo-form-group">
                                                <input type="text" class="moo-form-control" id="moo_coupon" style="background-color: #ffffff">
                                            </div>
                                        </div>
                                        <div class="moo-col-md-4">
                                            <div class="moo-form-group">
                                                <a href="#" class="moo-btn moo-btn-primary" onclick="mooCouponApply(event)">Apply</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="moo-checkout-bloc-content" id="moo_remove_coupon" style="<?php if($coupon == null) echo 'display:none'; ?>">
                                        <div class="moo-col-md-8">
                                            <div class="moo-form-group">
                                                <p style="font-size: 20px" id="moo_remove_coupon_code"><?php if($coupon != null) echo $coupon['code'];?></p>
                                            </div>
                                        </div>
                                        <div class="moo-col-md-4">
                                            <div class="moo-form-group">
                                                <a href="#" class="moo-btn moo-btn-primary" onclick="mooCouponRemove(event)">Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php  }?>
                            <button type="submit"  id="moo_btn_submit_order" onclick="moo_finalize_order(event)" class="moo-btn moo-btn-primary moo-finalize-order-btn moo-checkoutText-finalizeOrder">
                                PLACE ORDER
                            </button>
                        </div>
                        <!--            Checkout form - Cart scetion       -->
                        <div class="moo-col-md-5 moo-checkout-cart">
                            <div class="moo-shopping-cart MooCartInCheckout" tabindex="0" aria-label="the cart">
                                <div class="moo-column-labels-checkout">
                                    <label class="moo-product-quantity moo-product-quantity-checkou moo-checkoutText-qtyt" style="width: 20%">Qty</label>
                                    <label class="moo-product-details moo-product-details-checkout moo-checkoutText-product" style="width: 60%">Product</label>
                                    <label class="moo-product-price moo-product-price-checkout moo-checkoutText-price" style="width: 20%">Price</label>
                                </div>
                                <?php foreach ($session->get("items") as $key=>$line) {
                                    $modifiers_price = 0;
                                    $item_name = "";
                                    if($this->useAlternateNames && isset($line['item']->alternate_name) && $line['item']->alternate_name!==""){
                                        $item_name=stripslashes($line['item']->alternate_name);
                                    } else {
                                        $item_name=stripslashes($line['item']->name);
                                    }
                                    ?>
                                    <div class="moo-product" tabindex="0" aria-label="<?php echo $line['quantity']." of ".$line['item']->name."" ?>">
                                        <div class="moo-product-quantity" style="width: 20%">
                                            <strong><?php echo $line['quantity']?></strong>
                                        </div>
                                        <div class="moo-product-details moo-product-details-checkout" style="width: 60%">
                                            <div class="moo-product-title"><strong><?php echo $item_name; ?></strong></div>
                                            <p class="moo-product-description">
                                                <?php
                                                foreach($line['modifiers'] as $modifier){
                                                    $modifier_name = "";
                                                    if($this->useAlternateNames && isset($modifier["alternate_name"]) && $modifier["alternate_name"]!==""){
                                                        $modifier_name =stripslashes($modifier["alternate_name"]);
                                                    } else {
                                                        $modifier_name =stripslashes($modifier["name"]);
                                                    }
                                                    if(isset($modifier['qty']) && intval($modifier['qty'])>0) {
                                                        echo '<small tabindex="0">'.$modifier['qty'].'x ';
                                                        $modifiers_price += $modifier['price']*$modifier['qty'];
                                                    } else {
                                                        echo '<small tabindex="0">1x ';
                                                        $modifiers_price += $modifier['price'];
                                                    }

                                                    if($modifier['price']>0)
                                                        echo ''.$modifier_name.'- $'.number_format(($modifier['price']/100),2)."</small><br/>";
                                                    else
                                                        echo ''.$modifier_name."</small><br/>";

                                                }
                                                if($line['special_ins'] != "")
                                                    echo '<span tabindex="0" aria-label="your special instructions">SI:<span><span tabindex="0"> '.$line['special_ins']."<span>";
                                                ?>
                                            </p>
                                        </div>
                                        <?php $line_price = $line['item']->price+$modifiers_price;?>
                                        <div class="moo-product-line-price" tabindex="0"><strong>$<?php echo number_format(($line_price*$line['quantity']/100),2)?></strong></div>
                                    </div>
                                <?php } ?>

                                <div class="moo-totals" style="padding-right: 10px;">
                                    <div class="moo-totals-item">
                                        <label class="moo-checkoutText-subtotal"  tabindex="0">Subtotal</label>
                                        <div class="moo-totals-value" id="moo-cart-subtotal"  tabindex="0">$<?php echo number_format($total['sub_total'],2);?></div>
                                    </div>
                                    <div class="moo-totals-item">
                                        <label class="moo-checkoutText-tax"  tabindex="0" >Tax</label>
                                        <div class="moo-totals-value" id="moo-cart-tax"  tabindex="0">
                                            <?php
                                            // var_dump($total);
                                            if($coupon == null)
                                                echo  '$'.number_format($total['total_of_taxes_without_discounts'],2);
                                            else
                                                echo  '$'.number_format($total['total_of_taxes'],2);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="moo-totals-item" id="MooDeliveryfeesInTotalsSection">
                                        <label class="moo-checkoutText-deliveryFees"  tabindex="0"><?php echo $this->pluginSettings["delivery_fees_name"];?></label>
                                        <div class="moo-totals-value" id="moo-cart-delivery-fee"  tabindex="0">
                                            <?php
                                            if(is_double($this->pluginSettings['fixed_delivery'])) {
                                                echo '$'.number_format(($this->pluginSettings['fixed_delivery']),2);
                                                $grand_total = $total['total']+$this->pluginSettings['fixed_delivery']+$total['serviceCharges'];
                                            } else {
                                                echo '$0.00';
                                                $grand_total = $total['total']+$total['serviceCharges'];
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php if($this->pluginSettings['use_coupons']=="enabled"){//check if coupons are enabled ?>
                                        <div class="moo-totals-item" id="MooCouponInTotalsSection" style="<?php if($coupon == null) echo 'display:none;';?>">
                                            <label id="mooCouponName" tabindex="0"><?php echo $coupon['name'];?></label>
                                            <div class="moo-totals-value" id="mooCouponValue" tabindex="0">
                                                <?php
                                                if($coupon['type']=='amount') {
                                                    echo '- $'.number_format($coupon['value'],2);
                                                } else {
                                                    $t = $coupon['value']*$total['sub_total']/100;
                                                    echo "- $".number_format($t,2);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="moo-totals-item" id="MooServiceChargesInTotalsSection"  style="<?php if(! $total['serviceCharges']>0) echo 'display:none;';?>">
                                        <label id="MooServiceChargesName" tabindex="0"><?php echo $this->pluginSettings['service_fees_name']; ?></label>
                                        <div class="moo-totals-value" id="moo-cart-service-fee"  tabindex="0">
                                            <?php
                                            echo '$'.number_format($total['serviceCharges'],2);;
                                            ?>
                                        </div>
                                    </div>
                                    <?php if($this->pluginSettings['tips']=='enabled'){?>
                                        <div class="moo-totals-item" id="MooTipsInTotalsSection">
                                            <label class="moo-checkoutText-tipAmount" tabindex="0" >Tip</label>
                                            <div class="moo-totals-value" id="moo-cart-tip" tabindex="0">$0.00</div>
                                        </div>
                                    <?php } ?>
                                    <div class="moo-totals-item moo-totals-item-total" style="font-weight: 700;" >
                                        <label class="moo-checkoutText-grandTotal" tabindex="0" >Grand Total</label>
                                        <div class="moo-totals-value" id="moo-cart-total" tabindex="0" >$<?php echo number_format($grand_total,2) ?></div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit"  id="moo_btn_submit_order_cart" onclick="moo_finalize_order(event)" class="moo-btn moo-btn-primary moo-finalize-order-btn moo-checkoutText-finalizeOrder">
                                PLACE ORDER
                            </button>
                        </div>

                        <!--   Checkout form - Link section     -->
                        <div style="text-align: center;text-decoration: none;">
                            <a href="<?php echo $cart_page_url?>" class="moo-checkoutText-updateCart">Update cart</a> | <a href="<?php echo $store_page_url?>" class="moo-checkoutText-continueShopping">Continue shopping</a>
                        </div>
                        <!--            Checkout form - Buttons section       -->
                        <div id="moo-checkout-form-btnActions">
                            <div id="moo_checkout_loading" style="display: none; width: 100%;text-align: center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="44px" height="44px" viewBox="0 0 100 100"
                                     preserveAspectRatio="xMidYMid" class="uil-default">
                                    <rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(0 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0s"
                                                 repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(30 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s"
                                                 begin="0.08333333333333333s" repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(60 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s"
                                                 begin="0.16666666666666666s" repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(90 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.25s"
                                                 repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(120 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s"
                                                 begin="0.3333333333333333s" repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(150 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s"
                                                 begin="0.4166666666666667s" repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(180 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.5s"
                                                 repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(210 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s"
                                                 begin="0.5833333333333334s" repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(240 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s"
                                                 begin="0.6666666666666666s" repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(270 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.75s"
                                                 repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(300 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s"
                                                 begin="0.8333333333333334s" repeatCount="indefinite"></animate>
                                    </rect>
                                    <rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff"
                                          transform="rotate(330 50 50) translate(0 -30)">
                                        <animate attributeName="opacity" from="1" to="0" dur="1s"
                                                 begin="0.9166666666666666s" repeatCount="indefinite"></animate>
                                    </rect>
                                </svg>
                            </div>
                            <!-- <button type="submit"  id="moo_btn_submit_order" onclick="moo_finalize_order(event)" class="moo-btn moo-btn-primary moo-finalize-order-btn moo-checkoutText-finalizeOrder">
                                PLACE ORDER
                            </button> -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        if($custom_js != null)
            echo '<script type="text/javascript">'.$custom_js.'</script>';
        if(!$session->isEmpty("moo_customer_token"))
            echo '<script type="text/javascript"> jQuery( document ).ready(function($) { moo_show_chooseaddressform() });</script>';

        return ob_get_clean();
    }

    private function enqueueStyles(){

        wp_enqueue_style( 'moo-font-awesome' );
        wp_enqueue_style( 'custom-style-cart3');

    }
    private function enqueueScripts(){

        wp_enqueue_script( 'moo-google-map' );

        if(isset($this->pluginSettings["clover_payment_form"]) && $this->pluginSettings["clover_payment_form"] == "on")
            wp_enqueue_script( 'moo-clover' );

        wp_enqueue_script( 'moo-google-map');
        wp_enqueue_script( 'display-merchant-map');
        wp_enqueue_script( 'custom-script-checkout');
        wp_enqueue_script( 'moo-forge' );
    }
    private function cartIsEmpty() {
        $message =  '<div class="moo_emptycart"><p>Your cart is empty</p><span><a class="moo-btn moo-btn-default" href="'.get_page_link($this->pluginSettings['store_page']).'" style="margin-top: 30px;">Back to Main Menu</a></span></div>';
        return $message;
    }
    private function tipsSection(){
        $html = <<<HTML
        <div id="moo-checkout-form-tips">
            <div class="moo-checkout-bloc-title moo-checkoutText-tip">
                <label for="moo_tips">tip</label>
            </div>
            <div class="moo-checkout-bloc-content">
                <div class="moo-row"  style="margin-top: 13px;">
                    <div class="moo-col-md-6 new-tip-selector">
                        <div class="moo-form-group">
                            <div>
                                <input type="radio" id="cash-tip" name="moo_tips_select" value="cash"  onchange="moo_tips_select_changed(this)" checked>
                                <label for="cash-tip">Cash</label>
                            </div>
                            <div>
                                <input type="radio" id="10-tip" name="moo_tips_select" onchange="moo_tips_select_changed(this)" value="10">
                                <label for="10-tip">10%</label>
                            </div>
                            <div>
                                <input type="radio" id="15-tip" name="moo_tips_select" onchange="moo_tips_select_changed(this)" value="15">
                                <label for="15-tip">15%</label>
                            </div>
                            <div>
                                <input type="radio" id="20-tip" name="moo_tips_select" onchange="moo_tips_select_changed(this)" value="20">
                                <label for="20-tip">20%</label>
                            </div>
                            <div>
                                <input type="radio" id="25-tip" name="moo_tips_select" onchange="moo_tips_select_changed(this)" value="25">
                                <label for="25-tip">25%</label>
                            </div>
                            <div>
                                <input type="radio" id="other-tip" name="moo_tips_select" onchange="moo_tips_select_changed(this)" value="other">
                                <label for="other-tip">Custom</label>
                            </div>
                        </div>
                    </div>
                    <div class="moo-col-md-6">
                        <div class="moo-form-group">
                            <input class="moo-form-control" name="tip" id="moo_tips" value="0.00" onchange="moo_tips_amount_changed()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
HTML;
        $html = apply_filters( 'moo_filter_checkout_tips', $html);
         echo  $html;
    }
    private function cloverCardSection(){
        $html = <<<HTML
        <div id="moo-cloverCreditCardPanel">
            <input type="hidden" name="cloverToken" id="moo-CloverToken">
            <div class="moo-row">
                <div class="moo-col-md-12">
                    <div class="moo-form-group">
                        <div class="moo-form-control" id="moo_CloverCardNumber"></div>
                        <div class="card-number-error">
                            <div class="clover-error"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="moo-row">
                <div class="moo-col-md-6">
                    <div class="moo-form-group">
                        <div class="moo-form-control" id="moo_CloverCardDate"></div>  
                         <div class="date-error">
                            <div class="clover-error"></div>
                        </div>                                                  
                    </div>
                </div>
                <div class="moo-col-md-6">
                    <div class="moo-form-group">
                        <div class="moo-form-control" id="moo_CloverCardCvv"></div>
                         <div class="cvv-error">
                            <div class="clover-error"></div>
                        </div>                                                  
                    </div>
                </div>
            </div>
            <div class="moo-row">
                <div class="moo-col-md-12">
                    <div class="moo-form-group">
                        <div class="moo-form-control" id="moo_CloverCardZip"></div>
                         <div class="zip-error">
                            <div class="clover-error"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clover-errors"></div>
        </div>
HTML;
        $html = apply_filters( 'moo_filter_checkout_cloverCard', $html);
         echo  $html;
    }
    private function borderBottom() {
        echo '<div class="moo_chekout_border_bottom"></div>';
    }
}
           
        
        