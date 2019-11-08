<?php

class Moo_OnlineOrders_CallAPI
{

    public $Token;
    public $url_api;
    public $hours_url_api;
    private $debugMode = false;
    private $isSandbox = false;
    private $session;

    function __construct() {
        $this->getApiKey();
        if ($this->isSandbox) {
            $this->url_api = "http://api-sandbox.smartonlineorders.com/";
            //$this->url_api = "http://localhost/api/";
        } else {
            $this->url_api = "http://api.smartonlineorders.com/";
        }
        $this->hours_url_api = "https://smh.smartonlineorder.com/v1/api/";
        $this->session = MOO_SESSION::instance();

    }
    function getApiKey() {
        $MooSettings = (array)get_option("moo_settings");
        if (isset($MooSettings['api_key'])) {
            $this->Token = $MooSettings['api_key'];
        } else {
            $this->Token = '';
        }

    }

    /*
     * This functions import data from Clover POS and call the save functions
     * for example : getCategories get JSON object of categories from Clover POS and call the function save_categories
     * to save the this categories in Wordpress DB
     */
    public function getCategories()
    {
        $res = $this->callApi("categories", $this->Token);
        if ($res) {
            $saved = $this->save_categories($res);
            return "$saved Categories imported";
        } else {
            return "Please verify your Key in page settings";
        }

    }

    public function getItemGroups()
    {
        $res = $this->callApi("item_groups", $this->Token);
        if ($res) {
            $saved = $this->save_item_groups($res);
            return "$saved item_groups saved in your DB";
        } else {
            return "Please verify your Key in page settings";
        }
    }

    public function getModifierGroups()
    {
        $res = $this->callApi("modifier_groups", $this->Token);
        if ($res) {
            $saved = $this->save_modifier_groups($res);
            return "$saved Modifier groups imported";
        } else {
            return "Please verify your Key in page settings";
        }
    }

    public function getOneModifierGroups($uuid)
    {
        global $wpdb;
        $res = $this->callApi("modifier_groups/" . $uuid, $this->Token);
        $modifier_groups = json_decode($res);
        if(isset($modifier_groups->id)) {
            $wpdb->insert("{$wpdb->prefix}moo_modifier_group", array(
                'uuid' => $modifier_groups->id,
                'name' => $modifier_groups->name,
                'alternate_name' => $modifier_groups->alternateName,
                'show_by_default' => $modifier_groups->showByDefault,
                'min_required' => $modifier_groups->minRequired,
                'max_allowd' => $modifier_groups->maxAllowed,
            ));

            $this->save_modifiers($modifier_groups->modifiers);
            return true;
        }
        return false;

    }

    public function getItems()
    {
        $res = $this->callApi("items_expanded", $this->Token);
        if ($res) {
            $saved = $this->save_items($res);
            return "$saved products imported";
        } else {
            return "Please verify your Key in page settings";
        }
    }

    public function getModifiers()
    {
        $res = json_decode($this->callApi("modifiers", $this->Token));
        if ($res) {
            $saved = $this->save_modifiers($res);
            return "$saved modifier saved in your DB";
        } else {
            return "Please verify your Key in page settings";
        }
    }

    public function getAttributes()
    {
        $res = $this->callApi("attributes", $this->Token);
        if ($res) {
            $saved = $this->save_attributes($res);
            return "$saved attribute saved in your DB";
        } else {
            return "Please verify your Key in page settings";
        }
    }

    public function getOptions()
    {
        $res = $this->callApi("options", $this->Token);
        if ($res) {
            $saved = $this->save_options($res);
            return "$saved Options imported";
        } else {
            return "Please verify your Key in page settings";
        }
    }

    public function getTags()
    {
        $res = $this->callApi("tags", $this->Token);
        if ($res) {
            $saved = $this->save_tags($res);
            return "$saved Labels imported";
        } else {
            return "Please verify your Key in page settings";
        }
    }

    public function getTaxRates()
    {
        $res = $this->callApi("tax_rates", $this->Token);
        if ($res) {
            $saved = $this->save_tax_rates($res);
            return "$saved Taxes rates imported";
        } else {
            return "Please verify your Key in page settings";
        }
    }

    public function getOrderTypes()
    {
        $res = $this->callApi("order_types", $this->Token);
        if ($res) {
            $saved = $this->save_order_types($res);
            return "$saved Order type saved in your DB";
        } else {
            return "Please verify your Key in page settings";
        }
    }

    /*
     * Advanced Importing functions
     */
    public function getOneCategory($cat_id)
    {
        return json_decode($this->callApi("categories/" . $cat_id, $this->Token));
    }

    //Functions to call the API for make Orders and payments
    public function getPayKey()
    {
        return $this->callApi("paykey", $this->Token);
    }

    //get themes
    public function getThemes()
    {
        return json_decode($this->callApi("themes", $this->Token));
    }

    //Functions to call get the merchant logos
    public function getMerchantLogos()
    {
        return $this->callApi("logo", $this->Token);
    }

    public function getMerchantAddress()
    {
        return $this->callApi("address", $this->Token);
    }

    public function getOpeningHours()
    {
        $result = array();
        $days_names = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        $res = json_decode($this->callApi("opening_hours", $this->Token));
        $string = "";
        if (isset($res->elements) && count($res->elements) > 0) {
            $days = $res->elements;
            $days = $days[0];
            foreach ($days_names as $days_name) {
                $string = "";
                $Theday = $days->{$days_name};
                if (@count($Theday->elements) > 0)
                    foreach ($Theday->elements as $time) {
                        $startTime = ($time->start != 0) ? substr_replace(((strlen($time->start) == 4) ? $time->start : ((strlen($time->start) == 2) ? '00' . $time->start : '0' . $time->start)), ':', 2, 0) : '00:00';
                        $endTime = ($time->end != 2400) ? substr_replace(((strlen($time->end) == 4) ? $time->end : ((strlen($time->end) == 2) ? '00' . $time->end : '0' . $time->end)), ':', 2, 0) : '24:00';
                        $string .= date('h:ia', strtotime($startTime)) . ' to ' . date('h:ia', strtotime($endTime)) . ' AND ';
                        $result[ucfirst($days_name)] = substr($string, 0, -5);
                    }
                else
                    $result[ucfirst($days_name)] = 'Closed';

            }
            return $result;
        } else
            return "Please setup you business hours on Clover";

    }

    public function getOpeningStatus($nb_days, $nb_minites)
    {
        return $this->callApi("is_open/" . intval($nb_days) . "/" . intval($nb_minites), $this->Token);
    }

    public function getMerchantProprietes()
    {
        if (!$this->session->isEmpty("merchantProp")) {
            return$this->session->get("merchantProp");
        } else {
            $res = $this->callApi("properties", $this->Token);
            $this->session->set($res,"merchantProp");
            return $res;
        }
    }

    public function getTrackingStockStatus()
    {
        $MooOptions = (array)get_option("moo_settings");
        if (isset($MooOptions["track_stock"]) && $MooOptions["track_stock"] == "enabled") {
            return true;
        } else {
            return false;
        }
    }

    public function getItemStocks()
    {
        $res = $this->callApi("item_stocks", $this->Token);
        $res = json_decode($res);
        if (isset($res->elements))
            return $res->elements;
        return array();
    }

    //Function to update existing data
    public function updateItemGroup($uuid)
    {
        $res = $this->callApi("attributes/" . $uuid, $this->Token);
        if ($res) {
            $this->save_attributes($res);
            $attributes = json_decode($res)->elements;
            foreach ($attributes as $attribute) {
                $res = $this->callApi("attributes/" . $attribute->id . "/options", $this->Token);
                $this->save_options($res);
            }
        } else {
            return "Please verify your Key in page settings";
        }
    }

    public function getItemsWithoutSaving($page) {
        $res = $this->callApi("items_expanded/" . $page . "/50", $this->Token);
        return $res;
    }

    public function getCategoriesWithoutSaving()
    {
        return $this->callApi("categories", $this->Token);
    }

    public function getModifiersGroupsWithoutSaving()
    {
        return $this->callApi("modifier_groups", $this->Token);
    }

    public function getModifiersWithoutSaving()
    {
        return $this->callApi("modifiers", $this->Token);

    }

    public function getModifiersWithoutSavingPage2(){
        return $this->callApi("modifiers_page2", $this->Token);
    }
    public function getModifiersWithoutSavingPage3(){
        return $this->callApi("modifiers_page3", $this->Token);
    }

    public function updateOrderNote($orderId, $note)
    {
        return $this->callApi_Post("update_local_order/" . $orderId, $this->Token, 'note=' . urlencode($note));
    }


    //manage orders
    public function createOrder($options)
    {
        $string = $this->stringify($options);
        return $this->callApi_Post("create_order", $this->Token, $string);
    }

    public function assignCustomer($customer)
    {
        $res = $this->callApi_Post("assign_customer", $this->Token, 'customer=' . urlencode(json_encode($customer)));
        return $res;
    }

    public function addlineToOrder($oid, $item_uuid, $qte, $special_ins)
    {
        return $this->callApi_Post("create_line_in_order", $this->Token, 'oid=' . $oid . '&item=' . $item_uuid . '&qte=' . $qte . '&special_ins=' . urlencode($special_ins));
    }

    public function addLinesToOrder($oid, $lines){
        return $this->callApi_Post("v2/create_lines", $this->Token, 'oid=' . $oid . '&lines=' . json_encode($lines));
    }

    public function addlineWithPriceToOrder($oid, $item_uuid, $qte, $name, $price)
    {
        return $this->callApi_Post("create_line_in_order", $this->Token, 'oid=' . $oid . '&item=' . $item_uuid . '&qte=' . $qte . '&special_ins=&itemName=' . $name . '&itemprice=' . $price);
    }

    public function addModifierToLine($oid, $lineId, $modifer_uuid)
    {
        return $this->callApi_Post("add_modifier_to_line", $this->Token, 'oid=' . $oid . '&lineid=' . $lineId . '&modifier=' . $modifer_uuid);
    }

    //Pay the order
    public function payOrder($oid, $taxAmount, $amount, $zip, $expMonth, $cvv, $last4, $expYear, $first6, $cardEncrypted, $tipAmount)
    {
        return $this->callApi_Post("pay_order", $this->Token, 'orderId=' . $oid . '&taxAmount=' . $taxAmount . '&amount=' . $amount . '&zip=' . $zip . '&expMonth=' . $expMonth .
            '&cvv=' . $cvv . '&last4=' . $last4 . '&first6=' . $first6 . '&expYear=' . $expYear . '&cardEncrypted=' . $cardEncrypted . '&tipAmount=' . $tipAmount);
    }

    //Pay the order using Spreedly token
    public function moo_PayOrderUsingSpreedly($token, $oid, $taxAmount, $amount, $tipAmount, $saveCard, $customerToken)
    {
        return $this->callApi_Post("pay_order_spreedly", $this->Token, 'orderId=' . $oid . '&taxAmount=' . $taxAmount . '&amount=' . $amount . '&token=' . $token . '&tipAmount=' . $tipAmount . '&saveCard=' . $saveCard . '&customerToken=' . $customerToken);
    }

    //Send Notification to the merchant when a new order is registered
    public function NotifyMerchant($oid, $instructions, $pickup_time, $paymentMethode)
    {
        return $this->callApi_Post("notifyv2", $this->Token, 'orderId=' . $oid . '&instructions=' . urlencode($instructions) . '&pickup_time=' . $pickup_time . '&paymentmethod=' . $paymentMethode);
    }

    // OrderTypes
    public function GetOneOrdersTypes($uuid)
    {
        return $this->callApi("order_types/" . $uuid, $this->Token);
    }

    public function GetOrdersTypes()
    {
        return $this->callApi("order_types", $this->Token);
    }

    public function addOrderType($label, $taxable)
    {
        return $this->callApi_Post("order_types", $this->Token, 'label=' . $label . '&taxable=' . $taxable);
    }

    public function updateOrderType($uuid, $label, $taxable)
    {
        return $this->callApi_Post("order_types/" . $uuid, $this->Token, 'label=' . $label . '&taxable=' . $taxable);
    }

    //Updtae the website for the merchant
    public function updateWebsite($url)
    {
        return $this->callApi_Post("addsite", $this->Token, 'website=' . $url);
    }

    public function updateWebsiteHooks($url)
    {
        return $this->callApi_Post("addsite_webhooks", $this->Token, 'website=' . $url);
    }

    //Create default Orders Types
    public function CreateOrdersTypes()
    {
        return $this->callApi("create_default_ot", $this->Token);
    }

    public function sendSms($code, $phone)
    {
        $phone = str_replace('+', '%2B', $phone);
        $message = 'Your verification code is : ' . $code;
        return $this->callApi_Post("sendsms", $this->Token, 'to=' . $phone . '&body=' . $message);
    }

    public function sendSmsTo($message, $phone)
    {
        $phone = str_replace('+', '%2B', $phone);
        return $this->callApi_Post("sendsms", $this->Token, 'to=' . $phone . '&body=' . $message);
    }

    public function moo_CustomerVerifPhone($token, $phone)
    {
        return $this->callApi_Post("customers/verifphone", $this->Token, 'phone=' . $phone . '&token=' . $token);
    }

    public function moo_CustomerLogin($email, $password)
    {
        return $this->callApi_Post('customers/login', $this->Token, 'email=' . $email . '&password=' . $password);
    }

    public function moo_CustomerFbLogin($options)
    {
        $urlOptions = $this->stringify($options);
        return $this->callApi_Post('customers/fblogin', $this->Token, $urlOptions);
    }

    public function moo_CustomerSignup($options)
    {
        $urlOptions = $this->stringify($options);
        return $this->callApi_Post('customers/signup', $this->Token, $urlOptions);
    }

    public function moo_ResetPassword($email)
    {
        return $this->callApi_Post('customers/resetpassword', $this->Token, 'email=' . $email);
    }

    public function moo_GetAddresses($token)
    {
        return $this->callApi_Post('customers/getaddress', $this->Token, 'token=' . $token);
    }

    public function moo_GetCustomer($token)
    {
        return $this->callApi_Post('customers/get', $this->Token, 'token=' . $token);
    }

    public function moo_GetOrders($token, $page)
    {
        return $this->callApi_Post('customers/getorders/' . $page, $this->Token, 'token=' . $token);
    }

    public function moo_AddAddress($options)
    {
        $urlOptions = $this->stringify($options);
        return $this->callApi_Post('customers/setaddress', $this->Token, $urlOptions);
    }

    public function moo_updateCustomer($name, $email, $phone, $token)
    {
        return $this->callApi_Post('customers/update', $this->Token, 'token=' . $token . '&name=' . $name . '&phone=' . $phone . '&email=' . $email);
    }

    public function updateCustomerPassword($current_pass, $new_pass, $token)
    {
        return $this->callApi_Post('customers/change_password', $this->Token, 'token=' . $token . '&current_password=' . $current_pass . '&new_password=' . $new_pass);
    }

    public function moo_DeleteAddresses($address_id, $token)
    {
        return $this->callApi_Post('customers/deleteaddress', $this->Token, 'token=' . $token . '&address_id=' . $address_id);
    }

    public function moo_DeleteCreditCard($card_token, $Customertoken)
    {
        return $this->callApi_Post('remove_card_spreedly', $this->Token, 'Customertoken=' . $Customertoken . '&token=' . $card_token);

    }

    public function moo_setDefaultAddresses()
    {

    }

    public function moo_updateAddresses()
    {

    }

    public function moo_checkCoupon($couponCode)
    {
        return $this->callApi('coupons/' . $couponCode, $this->Token);
    }

    public function moo_checkCoupon_for_couponsApp($couponCode)
    {
        return $this->callApi('coupons_from_apps/' . $couponCode, $this->Token);
    }

    public function getCoupons($per_page, $page_number)
    {
        return $this->callApi('coupons/' . $page_number . "/" . $per_page, $this->Token);
    }

    public function getCoupon($code)
    {
        return $this->callApi('coupons/get/' . $code, $this->Token);
    }

    public function getNbCoupons()
    {
        return $this->callApi('coupons/count', $this->Token);
    }

    public function deleteCoupon($code)
    {
        $code = urlencode($code);
        return $this->callApi_Post('/coupons/' . $code . '/remove', $this->Token,"");
    }

    public function enableCoupon($code, $status)
    {
        return $this->callApi_Post('/coupons/' . $code . '/enable', $this->Token, 'status=' . $status);
    }

    public function addCoupon($coupon)
    {
        $params = "";
        foreach ($coupon as $key => $value) {
            $params .= $key . "=" . urlencode($value) . "&";
        }
        return $this->callApi_Post('/coupons/add', $this->Token, $params);
    }

    public function updateCoupon($code, $coupon)
    {
        $params = "";
        foreach ($coupon as $key => $value) {
            $params .= $key . "=" . urlencode($value) . "&";
        }
        return $this->callApi_Post('/coupons/' . $code . '/update', $this->Token, $params);
    }

    /*
     * Sync functions
     * @since 1.0.6
     */
    function getItem($uuid){
        if ($uuid == "")
            return null;
        $res = $this->callApi("items/" . $uuid, $this->Token);
        if ($res) {
            $saved = $this->save_one_item($res);
            return $saved;
        } else {
            return false;
        }
    }

    function getItemWithoutSaving($uuid)
    {
        if ($uuid == "")
            return false;
        return json_decode($this->callApi("items/" . $uuid, $this->Token));
    }

    function getCategoryWithoutSaving($uuid)
    {
        if ($uuid == "")
            return false;
        return json_decode($this->callApi("categories/" . $uuid, $this->Token));
    }

    function getModifierGroupsWithoutSaving($uuid)
    {
        if ($uuid == "")
            return false;
        return $this->callApi("modifier_groups/" . $uuid, $this->Token);
    }

    function getModifierWithoutSaving($mg_uuid, $uuid)
    {
        if ($uuid == "" || $mg_uuid == "")
            return false;
        return $this->callApi("modifier/" . $mg_uuid . '/' . $uuid, $this->Token);
    }

    function getTaxRateWithoutSaving($uuid)
    {
        if ($uuid == "")
            return false;
        return $this->callApi("tax_rates/" . $uuid, $this->Token);
    }
    public function getOrderTypesWithoutSaving()
    {
        return $this->callApi("order_types", $this->Token);
    }

    function getTaxesRatesWithoutSaving()
    {
        return $this->callApi("tax_rates", $this->Token);
    }


    public function delete_item($uuid) {
        if ($uuid == "") return;
        global $wpdb;
        $wpdb->hide_errors();
        $wpdb->query('START TRANSACTION');

        $wpdb->delete("{$wpdb->prefix}moo_item_tax_rate", array('item_uuid' => $uuid));
        $wpdb->delete("{$wpdb->prefix}moo_item_modifier_group", array('item_id' => $uuid));
        $wpdb->delete("{$wpdb->prefix}moo_item_tag", array('item_uuid' => $uuid));
        $wpdb->delete("{$wpdb->prefix}moo_images", array('item_uuid' => $uuid));

        //TODO : delete all attribute and options if it is the only item in the group_item

        $res = $wpdb->delete("{$wpdb->prefix}moo_item", array('uuid' => $uuid));
        if ($res) {
            $wpdb->query('COMMIT'); // if the item Inserted in the DB
        } else {
            $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
        }
        return $res;

    }

    /*
     *
     * This Function is for Updating the taxes rate in The  Wordpress Database
     * Is invoked when receiving a webhooks request POST
     * We start by deleting all actual taxes rates in The db, then we get all the taxe rates
     * from clover and save it in the database
     *
     */
    public function update_taxes_rates()
    {
        global $wpdb;
        // $wpdb->show_errors();
        $wpdb->query('START TRANSACTION');

        //Remove from local Database the removed tax_rates in clover
        $local_tr = array(); // tax_rates in our DB
        $Clover_tr = array(); // tax_rates in Clover DB
        $tempo = $wpdb->get_results("SELECT uuid FROM {$wpdb->prefix}moo_tax_rate");
        foreach ($tempo as $l) {
            array_push($local_tr, $l->uuid);
        }

        $res = $this->callApi("tax_rates", $this->Token);
        $taxes_rates = json_decode($res)->elements;
        foreach ($taxes_rates as $l) {
            array_push($Clover_tr, $l->id);
        }

        // delete from the Local database the taxe rate thar are already deleted from Clover
        foreach (array_diff($local_tr, $Clover_tr) as $uuid) {
            $wpdb->query("DELETE FROM {$wpdb->prefix}moo_item_tax_rate where tax_rate_uuid='{$uuid}'");
            $wpdb->query("DELETE FROM {$wpdb->prefix}moo_tax_rate where uuid = '{$uuid}'");
        }

        $res = $this->callApi("tax_rates", $this->Token);
        $taxes_rates = json_decode($res)->elements;
        $count = 0;
        foreach ($taxes_rates as $tax_rate) {
            if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tax_rate where uuid='{$tax_rate->id}'") > 0)
                $updated = $wpdb->update("{$wpdb->prefix}moo_tax_rate", array(
                    'name' => $tax_rate->name,
                    'rate' => $tax_rate->rate,
                    'is_default' => $tax_rate->isDefault,
                ), array('uuid' => $tax_rate->id));
            else
                $updated = $wpdb->insert("{$wpdb->prefix}moo_tax_rate", array(
                    'uuid' => $tax_rate->id,
                    'name' => $tax_rate->name,
                    'rate' => $tax_rate->rate,
                    'is_default' => $tax_rate->isDefault,
                ));
            if ($updated !== false) {
                $count++;
            }
        }
        if (@count($taxes_rates) == $count) {
            $wpdb->query('COMMIT'); // if the item Inserted in the DB
        } else {
            $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
        }

    }

    public function update_order_types()
    {
        global $wpdb;
        $result = array();
        // $wpdb->show_errors();
        $wpdb->query('START TRANSACTION');
        $res = $this->callApi("order_types", $this->Token);
        $res = json_decode($res)->elements;
        if (count($res) == 0) return false;

        // Get the actual status from the database for example if a merchant is already enable an order type
        // So when importing the order types they will be enabled
        foreach ($wpdb->get_results("SELECT ot_uuid,status,show_sa,`type` FROM {$wpdb->prefix}moo_order_types") as $st) {
            $status[$st->ot_uuid]['status'] = $st->status;
            $status[$st->ot_uuid]['show_sa'] = $st->show_sa;
            $status[$st->ot_uuid]['type'] = $st->type;
            $status[$st->ot_uuid]['minAmount'] = $st->minAmount;
        };

        //Delete all ordertypes from wordpress database
        $wpdb->get_results("DELETE FROM {$wpdb->prefix}moo_order_types");

        //Adding the order types imported from CLOVER POS
        foreach ($res as $key => $ot) {
            $result[$key] = $wpdb->insert("{$wpdb->prefix}moo_order_types", array(
                'ot_uuid' => $ot->id,
                'label' => $ot->label,
                'taxable' => $ot->taxable,
                'show_sa' => (isset($status[$ot->id]['show_sa']) && $status[$ot->id]['show_sa'] == 1) ? 1 : 0,
                'status' => (isset($status[$ot->id]['status']) && $status[$ot->id]['status'] == 1) ? 1 : 0,
                'type' => (isset($status[$ot->id]['type'])) ? $status[$ot->id]['type'] : null,
                'minAmount' => (isset($status[$ot->id]['minAmount'])) ? $status[$ot->id]['minAmount'] : null,
            ));

        }
        // If the all ordertypes imported are saved the COMMIT the changes else rollback
        if (count($res) == array_sum($result)) {
            $wpdb->query('COMMIT'); // if the item Inserted in the DB
            return true;
        } else {
            $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
            return false;
        }

    }

    public function save_one_item($res)
    {
        global $wpdb;
        $wpdb->query('START TRANSACTION');
        // $wpdb->show_errors();
        $item = json_decode($res);
        //print_r($item);
        if (isset($item->message) && $item->message == 'Not Found') {
            echo $item->message;
            return;
        }
        /*
         * I verify if the Item is already in Wordpress DB
         */
        if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_item where uuid='{$item->id}'") > 0) {
            if (isset($item->itemGroup)) {
                $this->updateItemGroup($item->itemGroup->id);
            }

            $wpdb->delete("{$wpdb->prefix}moo_item_tax_rate", array('item_uuid' => $item->id));
            $wpdb->delete("{$wpdb->prefix}moo_item_modifier_group", array('item_id' => $item->id));
            $wpdb->delete("{$wpdb->prefix}moo_item_tag", array('item_uuid' => $item->id));

            // update the Item
            $res1 = $wpdb->update("{$wpdb->prefix}moo_item", array(
                'name' => $item->name,
                'alternate_name' => $item->alternateName,
                'price' => $item->price,
                'code' => $item->code,
                'price_type' => $item->priceType,
                'unit_name' => $item->unitName,
                'default_taxe_rate' => $item->defaultTaxRates,
                'sku' => $item->sku,
                'hidden' => $item->hidden,
                'is_revenue' => $item->isRevenue,
                'cost' => $item->cost,
                'modified_time' => $item->modifiedTime,
            ), array('uuid' => $item->id));
        } else {
            if (!isset($item->itemGroup))
                $res1 = $wpdb->insert("{$wpdb->prefix}moo_item", array(
                    'uuid' => $item->id,
                    'name' => substr($item->name, 0, 100),
                    'alternate_name' => substr($item->alternateName, 0, 100),
                    'price' => $item->price,
                    'code' => $item->code,
                    'price_type' => $item->priceType,
                    'unit_name' => $item->unitName,
                    'default_taxe_rate' => $item->defaultTaxRates,
                    'sku' => $item->sku,
                    'hidden' => $item->hidden,
                    'is_revenue' => $item->isRevenue,
                    'cost' => $item->cost,
                    'modified_time' => $item->modifiedTime,
                ));
            else
                $res1 = $wpdb->insert("{$wpdb->prefix}moo_item", array(
                    'uuid' => $item->id,
                    'name' => substr($item->name, 0, 100),
                    'alternate_name' => substr($item->alternateName, 0, 100),
                    'price' => $item->price,
                    'code' => $item->code,
                    'price_type' => $item->priceType,
                    'unit_name' => $item->unitName,
                    'default_taxe_rate' => $item->defaultTaxRates,
                    'sku' => $item->sku,
                    'hidden' => $item->hidden,
                    'is_revenue' => $item->isRevenue,
                    'cost' => $item->cost,
                    'modified_time' => $item->modifiedTime,
                    'item_group_uuid' => $item->itemGroup->id
                ));
        }

        //save the taxes rates
        foreach ($item->taxRates->elements as $tax_rate) {
            if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tax_rate where uuid='{$tax_rate->id}'") == 0) {
                $table = array('elements' => array($tax_rate));
                $this->save_tax_rates(json_encode($table));
            }
            if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_item_tax_rate where item_uuid = '{$item->id}' and tax_rate_uuid='{$tax_rate->id}'") == 0) {
                $wpdb->insert("{$wpdb->prefix}moo_item_tax_rate", array(
                    'tax_rate_uuid' => $tax_rate->id,
                    'item_uuid' => $item->id
                ));
            }

        }

        //save modifierGroups
        foreach ($item->modifierGroups->elements as $modifier_group) {

            if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_modifier_group where  uuid='{$modifier_group->id}'") == 0) {
                $this->getOneModifierGroups($modifier_group->id);
            }

            if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_item_modifier_group where item_id = '{$item->id}' and group_id='{$modifier_group->id}'") == 0) {
                $wpdb->insert("{$wpdb->prefix}moo_item_modifier_group", array(
                    'group_id' => $modifier_group->id,
                    'item_id' => $item->id
                ));
            }

        }

        //save Tags
        foreach ($item->tags->elements as $tag) {
            if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tag where uuid='{$tag->id}'") == 0) {
                $table = array('elements' => array($tag));
                $this->save_tags(json_encode($table));
            }
            if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_item_tag where item_uuid = '{$item->id}' and tag_uuid='{$tag->id}'") == 0) {
                $wpdb->insert("{$wpdb->prefix}moo_item_tag", array(
                    'tag_uuid' => $tag->id,
                    'item_uuid' => $item->id
                ));
            }

        }
        //save New categories
        foreach ($item->categories->elements as $category) {
            //I verify if the category is already saved in Wordpress database
            if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_category where uuid='{$category->id}'") == 0) {
                $this->update_category($category);
            }
        }
        if ($res1) {
            $wpdb->query('COMMIT'); // if the item Inserted in the DB
        } else {
            $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
        }
    }

    /**
     * This function will take an object item in param then update it in local database
     * with checking of tax rate categories and modifiers
     * @param $item Object
     * @return bool
     */
    public function update_item($item) {
        global $wpdb;
        $wpdb->query('START TRANSACTION');
         //$wpdb->show_errors();

        /*
         * I verify if the Item is already in Wordpress DB and if it's up to date
         */
        if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_item where uuid='{$item->id}'") > 0) {
            if (isset($item->itemGroup)) {
                $this->updateItemGroup($item->itemGroup->id);
            }
            $wpdb->delete("{$wpdb->prefix}moo_item_tax_rate", array('item_uuid' => $item->id));
            $wpdb->delete("{$wpdb->prefix}moo_item_modifier_group", array('item_id' => $item->id));
            $wpdb->delete("{$wpdb->prefix}moo_item_tag", array('item_uuid' => $item->id));
            // update the Item
            $res1 = $wpdb->update("{$wpdb->prefix}moo_item", array(
                'name' => $item->name,
                'alternate_name' => $item->alternateName,
                'price' => $item->price,
                'code' => $item->code,
                'price_type' => $item->priceType,
                'unit_name' => $item->unitName,
                'default_taxe_rate' => $item->defaultTaxRates,
                'sku' => $item->sku,
                'hidden' => $item->hidden,
                'is_revenue' => $item->isRevenue,
                'cost' => $item->cost,
                'modified_time' => $item->modifiedTime,
            ), array('uuid' => $item->id));
            //var_dump($res1);
            if ($res1 >= 0)
                $res1 = true;
        } else {
            $item_To_Add = array(
                'uuid' => $item->id,
                'name' => $item->name,
                'alternate_name' => $item->alternateName,
                'price' => $item->price,
                'code' => $item->code,
                'price_type' => $item->priceType,
                'unit_name' => $item->unitName,
                'default_taxe_rate' => $item->defaultTaxRates,
                'sku' => $item->sku,
                'hidden' => $item->hidden,
                'is_revenue' => $item->isRevenue,
                'cost' => $item->cost,
                'modified_time' => $item->modifiedTime,
            );
            if (isset($item->itemGroup))
                $item_To_Add['item_group_uuid'] = $item->itemGroup->id;

            $res1 = $wpdb->insert("{$wpdb->prefix}moo_item", $item_To_Add);
        }
        //save the taxes rates
        if(isset($item->taxRates) && isset($item->taxRates->elements) && count($item->taxRates->elements)>0) {
            foreach ($item->taxRates->elements as $tax_rate) {

                if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tax_rate where uuid='{$tax_rate->id}'") == 0) {
                    $table = array('elements' => array($tax_rate));
                    $this->save_tax_rates(json_encode($table));
                }

                $wpdb->insert("{$wpdb->prefix}moo_item_tax_rate", array(
                    'tax_rate_uuid' => $tax_rate->id,
                    'item_uuid' => $item->id
                ));
            }
        }


        //save modifierGroups
        if(isset($item->modifierGroups) && isset($item->modifierGroups->elements) && count($item->modifierGroups->elements)>0) {
            foreach ($item->modifierGroups->elements as $modifier_group) {
                if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_modifier_group where uuid='{$modifier_group->id}'") == 0) {
                    $this->getOneModifierGroups($modifier_group->id);
                }
                $wpdb->insert("{$wpdb->prefix}moo_item_modifier_group", array(
                    'group_id' => $modifier_group->id,
                    'item_id' => $item->id
                ));
            }
        }



        //save Tags
        if(isset($item->tags) && isset($item->tags->elements) && count($item->tags->elements)>0) {
            foreach ($item->tags->elements as $tag) {
                if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tag where uuid='{$tag->id}'") == 0) {
                    $table = array('elements' => array($tag));
                    $this->save_tags(json_encode($table));
                }
                $wpdb->insert("{$wpdb->prefix}moo_item_tag", array(
                    'tag_uuid' => $tag->id,
                    'item_uuid' => $item->id
                ));
            }
        }

        //save New categories
        if(isset($item->categories) && isset($item->categories->elements) && count($item->categories->elements)>0) {
            foreach ($item->categories->elements as $category) {
                //I verify if the category is already saved in Wordpress database
                if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_category where uuid='{$category->id}'") == 0) {
                    $this->update_category($category);
                }
            }
        }

        if ($res1) {
            $wpdb->query('COMMIT'); // if the item Inserted in the DB
            return true;
        } else {
            $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
            return false;
        }
    }

    public function update_category($category)
    {
        global $wpdb;
        $items_ids = "";

        foreach ($category->items->elements as $item)
            $items_ids .= $item->id . ",";

        if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_category where uuid='{$category->id}'") > 0)
            $res = $wpdb->update("{$wpdb->prefix}moo_category", array(
                'name' => $category->name,
                'items' => $items_ids
            ), array('uuid' => $category->id));
        else
            $res = $wpdb->insert("{$wpdb->prefix}moo_category", array(
                'uuid' => $category->id,
                'name' => $category->name,
                'sort_order' => $category->sortOrder,
                'show_by_default' => 1,
                'items' => $items_ids
            ));

        if ($res > 0)
            return true;
        return false;
    }

    public function update_modifierGroups($modifier_groups)
    {
        global $wpdb;
        $nb = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_modifier_group where uuid='{$modifier_groups->id}'");
        if($nb>0) {
            if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_modifier_group where uuid='{$modifier_groups->id}'") > 0)
                $res = $wpdb->update("{$wpdb->prefix}moo_modifier_group", array(
                    'name' => $modifier_groups->name,
                    'min_required' => $modifier_groups->minRequired,
                    'max_allowd' => $modifier_groups->maxAllowed

                ), array('uuid' => $modifier_groups->id));
            else
                $res = $wpdb->insert("{$wpdb->prefix}moo_modifier_group", array(
                    'uuid' => $modifier_groups->id,
                    'name' => $modifier_groups->name,
                    'alternate_name' => $modifier_groups->alternateName,
                    'show_by_default' => $modifier_groups->showByDefault,
                    'min_required' => $modifier_groups->minRequired,
                    'max_allowd' => $modifier_groups->maxAllowed

                ));

            if ($res > 0) return true;
        }
        return false;
    }

    public function update_modifier($modifier) {
        global $wpdb;
        $wpdb->hide_errors();

        if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_modifier where uuid='{$modifier->id}'") > 0) {
            $res = $wpdb->update("{$wpdb->prefix}moo_modifier", array(
                'name' => $modifier->name,
                'price' => $modifier->price,
                'group_id' => $modifier->modifierGroup->id,

            ), array('uuid' => $modifier->id));
        } else {
            $res = $wpdb->insert("{$wpdb->prefix}moo_modifier", array(
                'uuid' => $modifier->id,
                'name' => $modifier->name,
                'alternate_name' => $modifier->alternateName,
                'price' => $modifier->price,
                'group_id' => $modifier->modifierGroup->id
            ));
        }
        if ($res > 0)
            return true;
        return false;
    }

    public function update_taxRate($tax) {
        global $wpdb;
        if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tax_rate where uuid='{$tax->id}'") > 0)
            $res = $wpdb->update("{$wpdb->prefix}moo_tax_rate", array(
                'name' => $tax->name,
                'rate' => $tax->rate,
                'is_default' => $tax->isDefault
            ), array('uuid' => $tax->id));
        else
            $res = $wpdb->insert("{$wpdb->prefix}moo_tax_rate", array(
                'uuid' => $tax->id,
                'name' => $tax->name,
                'rate' => $tax->rate,
                'is_default' => $tax->isDefault
            ));
        if ($res > 0)
            return true;
        return false;
    }

    public function update_orderType($orderType) {
        global $wpdb;
        if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_order_types where ot_uuid='{$orderType->id}'") > 0)
            $res = $wpdb->update("{$wpdb->prefix}moo_order_types", array(
                'label' => $orderType->label,
                'taxable' => $orderType->taxable,
            ), array('ot_uuid' => $orderType->id));
        else
            $res = $wpdb->insert("{$wpdb->prefix}moo_order_types", array(
                'ot_uuid' => $orderType->id,
                'label' => $orderType->label,
                'taxable' => $orderType->taxable,
                'status' => 0,
                'show_sa' => 0,
                'sort_order' => 0,
                'type' => 0,
                'minAmount' => '0',
            ));
        if ($res > 0)
            return true;
        return false;
    }

    /*
     * Function to send Order details via email,
     * @from : v 1.2.8
     * @param : the order id
     * @param : the merchant email
     * @param : the customer email
     */
    public function sendOrderEmails($order_id, $merchant_emails, $customer_email) {
        return $this->callApi_Post("send_order_emails", $this->Token, "order_id=" . $order_id . "&merchant_emails=" . urlencode($merchant_emails) . "&customer_email=" . urlencode($customer_email));
    }

    public function checkToken() {
        $url = "checktoken";
        return $this->callApi($url, $this->Token);
    }

    public function checkIpBlackListed() {
        return $this->checkIP($this->Token);
    }

    public function getOrderDetails($order) {
        $result = array();
        $url = 'orders/' . $order->uuid;
        $orderFromServer = json_decode($this->callApi($url, $this->Token));
        if (isset($orderFromServer)) {
            $result['uuid_order'] = $orderFromServer->order->uuid;
            $result['amount_order'] = $orderFromServer->order->amount / 100;
            $result['order_type'] = $orderFromServer->order->order_type;
            $result['special_instruction'] = $orderFromServer->order->special_instruction;
            $result['coupon'] = $orderFromServer->coupon;

            if ($orderFromServer->order->date != "") {
                $result['date_order'] = date('d/m/Y H:i:s', $orderFromServer->order->date / 1000);
            }
            if ($orderFromServer->order->taxRemoved == "1") {
                $result['taxRemoved'] = true;
            } else
                $result['taxRemoved'] = false;

            if (isset($orderFromServer->order->paymentMethode) && $orderFromServer->order->paymentMethode != "") {
                $result['paymentMethode'] = $orderFromServer->order->paymentMethode;
            } else {
                $result['paymentMethode'] = "No";
            }
            if (isset($orderFromServer->order->taxAmount) && $orderFromServer->order->taxAmount != "") {
                $result['taxAmount'] = $orderFromServer->order->taxAmount / 100;
            } else {
                $result['taxAmount'] = $order->taxAmount;
            }

            if (isset($orderFromServer->order->deliveryAmount) && $orderFromServer->order->deliveryAmount != "") {
                $result['deliveryAmount'] = $orderFromServer->order->deliveryAmount / 100;
            } else {
                $result['deliveryAmount'] = $order->deliveryfee;
            }
            if (isset($orderFromServer->order->serviceFee) && $orderFromServer->order->serviceFee != "") {
                $result['serviceFee'] = $orderFromServer->order->serviceFee / 100;
            } else {
                $result['serviceFee'] = 0;
            }

            if (isset($orderFromServer->order->deliveryName) && $orderFromServer->order->deliveryName != "" && $orderFromServer->order->deliveryName != "null" && $orderFromServer->order->deliveryName != null) {
                $result['deliveryName'] = $orderFromServer->order->deliveryName;
            } else {
                $result['deliveryName'] = "Delivery Charges";
            }
            if (isset($orderFromServer->order->serviceFeeName) && $orderFromServer->order->serviceFeeName != "" && $orderFromServer->order->serviceFeeName != "null" && $orderFromServer->order->serviceFeeName != null) {
                $result['serviceFeeName'] = $orderFromServer->order->serviceFeeName;
            } else {
                $result['serviceFeeName'] = "Service Charges";
            }

            if (isset($orderFromServer->order->tipAmount) && $orderFromServer->order->tipAmount != "") {
                $result['tipAmount'] = $orderFromServer->order->tipAmount / 100;
                $result['amount_order'] += $result['tipAmount'];
            } else {
                $result['tipAmount'] = $order->tipAmount;
                $result['amount_order'] += $result['tipAmount'];
            }
            if (isset($orderFromServer->customer->name) && $orderFromServer->customer->name != "") {
                $result['name_customer'] = $orderFromServer->customer->name;
            } else {
                $result['name_customer'] = $order->p_name;
            }
            if (isset($orderFromServer->customer->email) && $orderFromServer->customer->email != "") {
                $result['email_customer'] = $orderFromServer->customer->email;
            } else {
                $result['email_customer'] = $order->p_email;
            }
            if (isset($orderFromServer->customer->phone) && $orderFromServer->customer->phone != "") {
                $result['phone_customer'] = $orderFromServer->customer->phone;
            } else {
                $result['phone_customer'] = $order->p_phone;
            }
            if (isset($orderFromServer->customer->address) && $orderFromServer->customer->address == "") {
                $result['address_customer'] = $orderFromServer->customer->address;
            } else {
                $result['address_customer'] = $order->p_address;
            }
            if (isset($orderFromServer->customer->city) && $orderFromServer->customer->city != "") {
                $result['city_customer'] = $orderFromServer->customer->city;
            } else {
                $result['city_customer'] = $order->p_city;
            }
            if ($orderFromServer->customer->state && $orderFromServer->customer->state != "") {
                $result['state_customer'] = $orderFromServer->customer->state;
            } else {
                $result['state_customer'] = $order->p_state;
            }
            if (isset($orderFromServer->customer->zipcode) && $orderFromServer->customer->zipcode != "") {
                $result['zipcode'] = $orderFromServer->customer->zipcode;
            } else {
                $result['zipcode'] = $order->p_zipcode;
            }
            if (isset($orderFromServer->customer->lat) && $orderFromServer->customer->lat != "") {
                $result['lat'] = $orderFromServer->customer->lat;
            } else {
                $result['lat'] = $order->p_lat;
            }
            if (isset($orderFromServer->customer->lng) && $orderFromServer->customer->lng != "") {
                $result['lng'] = $orderFromServer->customer->lng;
            } else {
                $result['lng'] = $order->p_lng;
            }
            $result['payments'] = $orderFromServer->payments;
        } else {
            $result['uuid_order'] = $order->uuid;
            $result['amount_order'] = $order->amount;
            $result['order_type'] = $order->ordertype;
            $result['special_instruction'] = $order->instructions;
            $result['date_order'] = $order->date;
            $result['paymentMethode'] = "";
            $result['taxAmount'] = $order->taxAmount;
            $result['deliveryAmount'] = $order->deliveryfee;
            $result['tipAmount'] = $order->tipAmount;
            $result['name_customer'] = $order->p_name;
            $result['email_customer'] = $order->p_email;
            $result['phone_customer'] = $order->p_phone;
            $result['address_customer'] = $order->p_address;
            $result['city_customer'] = $order->p_city;
            $result['state_customer'] = $order->p_state;
            $result['zipcode'] = $order->p_zipcode;
            $result['lat'] = $order->p_lat;
            $result['lng'] = $order->p_lng;
            $result['payments'] = array();
            $result['coupon'] = array();
            $result['taxRemoved'] = false;
        }
        //return json_decode($orderFromServer);
        //var_dump($result);
        return $result;
    }

    public function getOrderDetails2($order, $orderFromServer)
    {
        $result = array();
        if (isset($orderFromServer)) {
            $result['uuid_order'] = $orderFromServer->order->uuid;
            $result['amount_order'] = $orderFromServer->order->amount / 100;
            $result['order_type'] = $orderFromServer->order->order_type;
            $result['special_instruction'] = $orderFromServer->order->special_instruction;
            $result['coupon'] = $orderFromServer->coupon;

            if ($orderFromServer->order->date != "") {
                $result['date_order'] = date('m/d/Y', $orderFromServer->order->date / 1000);
            }
            if ($orderFromServer->order->taxRemoved == "1") {
                $result['taxRemoved'] = true;
            } else {
                $result['taxRemoved'] = false;
            }

            if (isset($orderFromServer->order->paymentMethode) && $orderFromServer->order->paymentMethode != "") {
                $result['paymentMethode'] = $orderFromServer->order->paymentMethode;
            } else {
                $result['paymentMethode'] = "No";
            }
            if (isset($orderFromServer->order->taxAmount) && $orderFromServer->order->taxAmount != "") {
                $result['taxAmount'] = $orderFromServer->order->taxAmount / 100;
            } else {
                $result['taxAmount'] = $order->taxAmount;
            }

            if (isset($orderFromServer->order->deliveryAmount) && $orderFromServer->order->deliveryAmount != "") {
                $result['deliveryAmount'] = $orderFromServer->order->deliveryAmount / 100;
            } else {
                $result['deliveryAmount'] = $order->deliveryfee;
            }
            if (isset($orderFromServer->order->serviceFee) && $orderFromServer->order->serviceFee != "") {
                $result['serviceFee'] = $orderFromServer->order->serviceFee / 100;
            } else {
                $result['serviceFee'] = 0;
            }

            if (isset($orderFromServer->order->deliveryName) && $orderFromServer->order->deliveryName != "" && $orderFromServer->order->deliveryName != "null" && $orderFromServer->order->deliveryName != null) {
                $result['deliveryName'] = $orderFromServer->order->deliveryName;
            } else {
                $result['deliveryName'] = "Delivery Charges";
            }
            if (isset($orderFromServer->order->serviceFeeName) && $orderFromServer->order->serviceFeeName != "" && $orderFromServer->order->serviceFeeName != "null" && $orderFromServer->order->serviceFeeName != null) {
                $result['serviceFeeName'] = $orderFromServer->order->serviceFeeName;
            } else {
                $result['serviceFeeName'] = "Service Charges";
            }

            if (isset($orderFromServer->order->tipAmount) && $orderFromServer->order->tipAmount != "") {
                $result['tipAmount'] = $orderFromServer->order->tipAmount / 100;
                $result['amount_order'] += $result['tipAmount'];
            } else {
                $result['tipAmount'] = $order->tipAmount;
                $result['amount_order'] += $result['tipAmount'];
            }
            if (isset($orderFromServer->customer->name) && $orderFromServer->customer->name != "") {
                $result['name_customer'] = $orderFromServer->customer->name;
            } else {
                $result['name_customer'] = $order->p_name;
            }
            if (isset($orderFromServer->customer->email) && $orderFromServer->customer->email != "") {
                $result['email_customer'] = $orderFromServer->customer->email;
            } else {
                $result['email_customer'] = $order->p_email;
            }
            if (isset($orderFromServer->customer->phone) && $orderFromServer->customer->phone != "") {
                $result['phone_customer'] = $orderFromServer->customer->phone;
            } else {
                $result['phone_customer'] = $order->p_phone;
            }
            if (isset($orderFromServer->customer->address) && $orderFromServer->customer->address == "") {
                $result['address_customer'] = $orderFromServer->customer->address;
            } else {
                $result['address_customer'] = $order->p_address;
            }
            if (isset($orderFromServer->customer->city) && $orderFromServer->customer->city != "") {
                $result['city_customer'] = $orderFromServer->customer->city;
            } else {
                $result['city_customer'] = $order->p_city;
            }
            if ($orderFromServer->customer->state && $orderFromServer->customer->state != "") {
                $result['state_customer'] = $orderFromServer->customer->state;
            } else {
                $result['state_customer'] = $order->p_state;
            }
            if (isset($orderFromServer->customer->zipcode) && $orderFromServer->customer->zipcode != "") {
                $result['zipcode'] = $orderFromServer->customer->zipcode;
            } else {
                $result['zipcode'] = $order->p_zipcode;
            }
            if (isset($orderFromServer->customer->lat) && $orderFromServer->customer->lat != "") {
                $result['lat'] = $orderFromServer->customer->lat;
            } else {
                $result['lat'] = $order->p_lat;
            }
            if (isset($orderFromServer->customer->lng) && $orderFromServer->customer->lng != "") {
                $result['lng'] = $orderFromServer->customer->lng;
            } else {
                $result['lng'] = $order->p_lng;
            }
            $result['payments'] = $orderFromServer->payments;
        } else {
            $result['uuid_order'] = $order->uuid;
            $result['amount_order'] = $order->amount;
            $result['order_type'] = $order->ordertype;
            $result['special_instruction'] = $order->instructions;
            $result['date_order'] = $order->date;
            $result['paymentMethode'] = "";
            $result['taxAmount'] = $order->taxAmount;
            $result['deliveryAmount'] = $order->deliveryfee;
            $result['tipAmount'] = $order->tipAmount;
            $result['name_customer'] = $order->p_name;
            $result['email_customer'] = $order->p_email;
            $result['phone_customer'] = $order->p_phone;
            $result['address_customer'] = $order->p_address;
            $result['city_customer'] = $order->p_city;
            $result['state_customer'] = $order->p_state;
            $result['zipcode'] = $order->p_zipcode;
            $result['lat'] = $order->p_lat;
            $result['lng'] = $order->p_lng;
            $result['payments'] = array();
            $result['coupon'] = array();
            $result['taxRemoved'] = false;
        }
        return $result;
    }

    //Funciton to save DATA in db

    private function save_tax_rates($obj)
    {
        global $wpdb;
        // $wpdb->show_errors();
        $wpdb->hide_errors();
        $count = 0;
        foreach (json_decode($obj)->elements as $tax_rate) {
            $wpdb->insert("{$wpdb->prefix}moo_tax_rate", array(
                'uuid' => $tax_rate->id,
                'name' => $tax_rate->name,
                'rate' => $tax_rate->rate,
                'is_default' => $tax_rate->isDefault,
            ));

            if ($wpdb->insert_id != 0) $count++;
        }

        return $count;
    }

    private function save_tags($obj)
    {
        global $wpdb;
        // $wpdb->show_errors();
        $wpdb->hide_errors();
        $count = 0;
        foreach (json_decode($obj)->elements as $tag) {
            $wpdb->insert("{$wpdb->prefix}moo_tag", array(
                'uuid' => $tag->id,
                'name' => $tag->name
            ));

            if ($wpdb->insert_id != 0) $count++;
        }

        return $count;
    }

    private function save_options($obj)
    {
        global $wpdb;
        //$wpdb->show_errors();
        $wpdb->hide_errors();
        $count = 0;
        foreach (json_decode($obj)->elements as $option) {
            //var_dump($option);
            $wpdb->insert("{$wpdb->prefix}moo_option", array(
                'uuid' => $option->id,
                'name' => $option->name,
                'attribute_uuid' => $option->attribute->id
            ));

            if ($wpdb->insert_id != 0) $count++;
        }

        return $count;
    }

    private function save_attributes($obj)
    {
        global $wpdb;
        //$wpdb->show_errors();
        $wpdb->hide_errors();
        $count = 0;
        foreach (json_decode($obj)->elements as $attribute) {
            $wpdb->insert("{$wpdb->prefix}moo_attribute", array(
                'uuid' => $attribute->id,
                'name' => $attribute->name,
                'item_group_uuid' => $attribute->itemGroup->id
            ));

            if ($wpdb->insert_id != 0) $count++;
        }

        return $count;
    }

    private function save_modifiers($obj)
    {
        global $wpdb;
        // $wpdb->show_errors();
        $wpdb->hide_errors();
        $count = 0;
       // var_dump($obj);
        foreach ($obj->elements as $modifier) {
            $wpdb->insert("{$wpdb->prefix}moo_modifier", array(
                'uuid' => $modifier->id,
                'name' => $modifier->name,
                'alternate_name' => (isset($modifier->alternateName))?$modifier->alternateName:"",
                'price' => $modifier->price,
                'group_id' => $modifier->modifierGroup->id,
            ));

            if ($wpdb->insert_id != 0) $count++;
        }

        return $count;


    }

    public function save_items($obj)
    {
        global $wpdb;
        $wpdb->hide_errors();
        $count = 0;
        foreach (json_decode($obj)->elements as $item) {
            if(!$item)
                continue;
            //Save the item
            if ($item->itemGroup === NULL)
                $wpdb->insert("{$wpdb->prefix}moo_item", array(
                    'uuid' => $item->id,
                    'name' => $item->name,
                    'alternate_name' => $item->alternateName,
                    'price' => $item->price,
                    'code' => $item->code,
                    'price_type' => $item->priceType,
                    'unit_name' => $item->unitName,
                    'default_taxe_rate' => $item->defaultTaxRates,
                    'sku' => $item->sku,
                    'hidden' => $item->hidden,
                    'is_revenue' => $item->isRevenue,
                    'cost' => $item->cost,
                    'modified_time' => $item->modifiedTime,
                ));
            else
                $wpdb->insert("{$wpdb->prefix}moo_item", array(
                    'uuid' => $item->id,
                    'name' => $item->name,
                    'alternate_name' => $item->alternateName,
                    'price' => $item->price,
                    'code' => $item->code,
                    'price_type' => $item->priceType,
                    'unit_name' => $item->unitName,
                    'default_taxe_rate' => $item->defaultTaxRates,
                    'sku' => $item->sku,
                    'hidden' => $item->hidden,
                    'is_revenue' => $item->isRevenue,
                    'cost' => $item->cost,
                    'modified_time' => $item->modifiedTime,
                    'item_group_uuid' => $item->itemGroup->id
                ));

            if ($wpdb->insert_id != 0) $count++;

            //save the taxes rates
            foreach ($item->taxRates->elements as $tax_rate) {
                $wpdb->insert("{$wpdb->prefix}moo_item_tax_rate", array(
                    'tax_rate_uuid' => $tax_rate->id,
                    'item_uuid' => $item->id
                ));
            }

            //save modifierGroups
            foreach ($item->modifierGroups->elements as $modifier_group) {
                $wpdb->insert("{$wpdb->prefix}moo_item_modifier_group", array(
                    'group_id' => $modifier_group->id,
                    'item_id' => $item->id
                ));
            }


            //save Tags
            foreach ($item->tags->elements as $tag) {
                $wpdb->insert("{$wpdb->prefix}moo_item_tag", array(
                    'tag_uuid' => $tag->id,
                    'item_uuid' => $item->id
                ));
            }
        }
        return $count;

    }

    private function save_modifier_groups($obj)
    {
        global $wpdb;
        $count = 0;
        foreach (json_decode($obj)->elements as $modifier_groups) {
            $wpdb->insert("{$wpdb->prefix}moo_modifier_group", array(
                'uuid' => $modifier_groups->id,
                'name' => $modifier_groups->name,
                'alternate_name' => $modifier_groups->alternateName,
                'show_by_default' => $modifier_groups->showByDefault,
                'min_required' => $modifier_groups->minRequired,
                'max_allowd' => $modifier_groups->maxAllowed,

            ));
            if ($wpdb->insert_id != 0) $count++;
        }
        return $count;

    }

    private function save_item_groups($obj)
    {
        global $wpdb;
        $count = 0;
        foreach (json_decode($obj)->elements as $item_group) {
            $wpdb->insert("{$wpdb->prefix}moo_item_group", array(
                'uuid' => $item_group->id,
                'name' => $item_group->name

            ));
            if ($wpdb->insert_id != 0) $count++;
        }
        return $count;
    }

    private function save_categories($obj)
    {
        global $wpdb;
        $count = 0;

        // var_dump(json_decode($obj));
        foreach (json_decode($obj)->elements as $cat) {
            $items_ids = "";
            foreach ($cat->items->elements as $item)
                $items_ids .= $item->id . ",";
            $wpdb->insert("{$wpdb->prefix}moo_category", array(
                'uuid' => $cat->id,
                'name' => $cat->name,
                'sort_order' => $cat->sortOrder,
                'show_by_default' => 1,
                'items' => $items_ids
            ));
            if ($wpdb->insert_id != 0) $count++;
        }
        return $count;
    }

    private function save_order_types($obj)
    {
        global $wpdb;
        $count = 0;
        foreach (json_decode($obj)->elements as $ot) {
            $res = $wpdb->insert("{$wpdb->prefix}moo_order_types", array(
                'ot_uuid' => $ot->id,
                'label' => $ot->label,
                'taxable' => $ot->taxable,
                'minAmount' => 0,
                'show_sa' => ($ot->label == 'Online Order Delivery') ? 1 : 0,
                'status' => ($ot->label == 'Online Order Delivery' || $ot->label == 'Online Order Pick Up') ? 1 : 0
            ));

            if ($res == 1)
                $count++;
        }
        return $count;
    }

    public function save_One_orderType($uuid, $label, $taxable, $minAmount, $show_sa)
    {
        global $wpdb;
        $res = $wpdb->insert("{$wpdb->prefix}moo_order_types", array(
            'ot_uuid' => $uuid,
            'label' => esc_sql($label),
            'taxable' => (($taxable == "true") ? "1" : "0"),
            'status' => 1,
            'show_sa' => (($show_sa == "true") ? "1" : "0"),
            'minAmount' => floatval($minAmount),
        ));
        return $res;
    }

    //Hours endpoints

    //get hour
    public function getMerchantCustomHours($type){
        $url = $this->hours_url_api."hours?type=".$type;
        $response = $this->apiGet($url);
        return $response;
    }
    public function getMerchantCustomHoursStatus($type){
        $url = $this->hours_url_api."hours/check?type=".$type;
        $response = $this->apiGet($url);
        return $response;
    }


    public function goToReports()
    {
        $dashboard_url = admin_url('/admin.php?page=moo_index');
        $newURL = "http://dashboard.smartonlineorder.com/#/login/" . $this->Token . "?redirectTo=" . $dashboard_url;
        header('Location: ' . $newURL);
        die();
    }
    public function stringify($options){
        $string = '';
        foreach ($options as $key=>$value) {
            $string .= $key."=".urlencode($value)."&";
        }
        return $string;
    }

    private function checkIP($accesstoken)
    {
        $headr = array();
        $headr[] = 'Accept: application/json';
        $headr[] = 'X-Authorization: ' . $accesstoken;
        $url = $this->url_api . 'checktoken';
        //cURL starts
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_HTTPGET, true);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
        $reply = curl_exec($crl);
        //error handling for cURL
        if ($reply === false) {
            print_r('Curl error: ' . curl_error($crl));
            return $reply;
        }
        curl_close($crl);
        return $reply;
    }

    private function callApi($url, $accesstoken)
    {

        $headr = array();
        $headr[] = 'Accept: application/json';
        $headr[] = 'X-Authorization: ' . $accesstoken;
        $url = $this->url_api . $url;
        //cURL starts
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_HTTPGET, true);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
        $reply = curl_exec($crl);

        //error handling for cURL
        if ($reply === false) {
            print_r('Curl error: ' . curl_error($crl));
            return false;
        }
        $info = curl_getinfo($crl);
        curl_close($crl);
        if ($this->debugMode) {
            echo "GET :: " . $url . " <<";
            var_dump($reply);
            echo ">> ";
        }
        if ($info['http_code'] == 200) return $reply;
        return false;
    }

    private function callApi_Post($url, $accesstoken, $fields_string)
    {
        $headr = array();
        $headr[] = 'Accept: application/json';
        $headr[] = 'X-Authorization: ' . $accesstoken;
        $url = $this->url_api . $url;

        //cURL starts
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
        $reply = curl_exec($crl);
        //error handling for cURL
        if ($reply === false) {
            print_r('Curl error: ' . curl_error($crl));
            return false;
        }

        $info = curl_getinfo($crl);
        curl_close($crl);
        if ($this->debugMode) {
            echo "\n POST " . " " . $info['http_code'] . " " . $url . " <<";
            echo $reply;
            echo ">> ";
            echo ">> ";
            echo $fields_string;
            echo "<< ";
        }
        if ($info['http_code'] == 200)
            return $reply;
        return false;
    }


    /**
     * To send post requests to Smart Online Order api
     * @param $url
     * @param $data
     * @return bool|mixed
     */
    private function apiPost($url, $data) {
        $args = array(
            "headers" => array(
                "Accept"=>"application/json",
                "X-Authorization"=>$this->Token,
            ),
            "body" => $data
        );
        $response = wp_remote_post($url,$args);

        if(is_wp_error( $response )){
            if($this->debugMode){
                echo $response->get_error_message();
            }
            return false;
        }

        if ( is_array( $response ) ) {
            if($response["response"]["code"] === 200)
                return $response['body'];
        }
        return false;
    }
    /**
     * To send get request to our Zaytech API
     * @param $url
     * @return bool|array
     */
    private function apiGet($url) {
        $args = array(
            "headers"=> array(
                "Accept"=>"application/json",
                "X-Authorization"=>$this->Token,
            )
        );
        $response = wp_remote_get($url,$args);
        if(is_wp_error( $response )){
            if($this->debugMode){
                echo $response->get_error_message();
            }
            return false;
        }

        if (is_array( $response ) ) {
            if($response["response"]["code"] === 200)
                return $response['body'];
        }
        return false;
    }
}