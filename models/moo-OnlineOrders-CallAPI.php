<?php

class moo_OnlineOrders_CallAPI {

    public $Token;
    public $url_api;
    public $debug;


    function __construct()
    {
        $MooSettings = (array) get_option("moo_settings");
        $this->Token = $MooSettings['api_key'];
        //Put the API URL here and don't forget the last slash
        $this->url_api = "http://api.smartonlineorders.com/";
       // $this->url_api = "http://localhost/api/";

        $this->debug = false;

    }
    /*
     * This functions import data from Clover POS and call the save functions
     * for example : getCategories get JSON object of categories from Clover POS and call the function save_categories
     * to save the this categories in Wordpress DB
     */
    public function getCategories()
    {
        $res = $this->callApi("categories",$this->Token);
        if($res){
            $saved = $this->save_categories($res);
            return "$saved categories imported";
        }
       else{
           return "Please verify your Key in page settings";
       }

    }
    public function getItemGroups()
    {
        $res = $this->callApi("item_groups",$this->Token);
        if($res){
            $saved = $this->save_item_groups($res);
            return "$saved item_groups saved in your DB";
        }
       else{
           return "Please verify your Key in page settings";
       }
    }
    public function getModifierGroups()
    {
        $res = $this->callApi("modifier_groups",$this->Token);
        if($res){
            $saved = $this->save_modifier_groups($res);
            return "$saved modifier_groups saved in your DB";
        }
       else{
           return "Please verify your Key in page settings";
       }
    }
    public function getOneModifierGroups($uuid)
    {
        global $wpdb;
        $res = $this->callApi("modifier_groups/".$uuid,$this->Token);
        $modifier_groups = json_decode($res);

        $wpdb->insert("{$wpdb->prefix}moo_modifier_group",array(
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
    public function getItems()
    {
        $res = $this->callApi("items_expanded",$this->Token);
        if($res){
            $saved = $this->save_items($res);
            return "$saved products imported";
        }
       else{
           return "Please verify your Key in page settings";
       }
    }
    public function getModifiers()
    {
        $res = $this->callApi("modifiers",$this->Token);
        if($res){
            $saved = $this->save_modifiers($res);
            return "$saved modifier saved in your DB";
        }
       else{
           return "Please verify your Key in page settings";
       }
    }
    public function getAttributes()
    {
        $res = $this->callApi("attributes",$this->Token);
        if($res){
            $saved = $this->save_attributes($res);
            return "$saved attribute saved in your DB";
        }
       else{
           return "Please verify your Key in page settings";
       }
    }
    public function getOptions()
    {
        $res = $this->callApi("options",$this->Token);
        if($res){
            $saved = $this->save_options($res);
            return "$saved option saved in your DB";
        }
       else{
           return "Please verify your Key in page settings";
       }
    }
    public function getTags()
    {
        $res = $this->callApi("tags",$this->Token);
        if($res){
            $saved = $this->save_tags($res);
            return "$saved Labels imported";
        }
       else{
           return "Please verify your Key in page settings";
       }
    }
    public function getTaxRates()
    {
        $res = $this->callApi("tax_rates",$this->Token);
        if($res){
            $saved = $this->save_tax_rates($res);
            return "$saved Taxes rates imported";
        }
       else{
           return "Please verify your Key in page settings";
       }
    }
    public function getOrderTypes()
    {
        $res = $this->callApi("order_types",$this->Token);
        if($res){
            $saved = $this->save_order_types($res);
            return "$saved Order type saved in your DB";
        }
       else{
           return "Please verify your Key in page settings";
       }
    }
    //Functions to call the API for make Orders and payments
    public function getPayKey()
    {
        return $this->callApi("paykey",$this->Token);
    }
    //Functions to call get the merchant logos
    public function getMerchantLogos()
    {
        return $this->callApi("logo",$this->Token);
    }
    public function getMerchantAddress()
    {
        return $this->callApi("address",$this->Token);
    }
    public function getOpeningHours()
    {
        $result = array();
        $days_names = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
        $res =  json_decode($this->callApi("opening_hours",$this->Token));
        $string = "";
        if(isset($res->elements))
        {
            $days = $res->elements;
            $days = $days[0];
            foreach ($days_names as $days_name)
            {
                $string = "";
                $Theday = $days->{$days_name};
                if(count($Theday->elements)>0)
                    foreach ($Theday->elements as $time)
                    {
                        $startTime   =  ($time->start!=0)?substr_replace(((strlen($time->start) == 4)?$time->start:((strlen($time->start) == 2)?'00'.$time->start:'0'.$time->start)), ':', 2, 0):'00:00';
                        $endTime     =  ($time->end!=2400)?substr_replace(((strlen($time->end) == 4)?$time->end:((strlen($time->end) == 2)?'00'.$time->end:'0'.$time->end)), ':', 2, 0):'24:00';
                        $string  .= date('h:ia', strtotime($startTime)).' to '.date('h:ia', strtotime($endTime)). ' AND ';
                        $result[ucfirst($days_name)] = substr($string,0,-5);
                    }
                else
                    $result[ucfirst($days_name)] = 'Closed';

            }
            return $result;
        }
        else
            return "Please setup you business hours on Clover";

    }
    public function getOpeningStatus($nb_days,$nb_minites)
    {
        return $this->callApi("is_open/".intval($nb_days)."/".intval($nb_minites),$this->Token);
    }
    public function getMerchantProprietes()
    {
        if(isset($_SESSION['merchantProp']) && $_SESSION['merchantProp'] != "")
        {
            return $_SESSION['merchantProp'];
        }
        else
        {
            $res = $this->callApi("properties",$this->Token);
            $_SESSION['merchantProp'] = $res;
            return $res;
        }
    }
    public function getTrackingStockStatus()
    {
        $MooOptions = (array)get_option("moo_settings");
        if(isset($MooOptions["track_stock"]) && $MooOptions["track_stock"]=="enabled")
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function getItemStocks()
    {
        $res = $this->callApi("item_stocks",$this->Token);
        $res = json_decode($res);
        if(isset($res->elements))
            return $res->elements;
        return array();
    }

    //Function to update existing data
    public function updateItemGroup($uuid)
    {
        $res = $this->callApi("attributes/".$uuid,$this->Token);
        if($res){
            $this->save_attributes($res);
            $attributes = json_decode($res)->elements;
            foreach ($attributes as $attribute)
            {
                $res = $this->callApi("attributes/".$attribute->id."/options",$this->Token);
                $this->save_options($res);
            }
        }
        else{
            return "Please verify your Key in page settings";
        }
    }
    public function getItemsWithoutSaving($page)
    {
        $res = $this->callApi("items_expanded/".$page."/20",$this->Token);
        return $res;
    }
    public function getCategoriesWithoutSaving()
    {
        return  $this->callApi("categories",$this->Token);
    }
    public function getModifiersGroupsWithoutSaving()
    {
        return $this->callApi("modifier_groups",$this->Token);
    }
    public function getModifiersWithoutSaving()
    {
        return $this->callApi("modifiers",$this->Token);

    }
    public function updateOrderNote($orderId,$note)
    {
        return $this->callApi_Post("update_order/".$orderId,$this->Token,'note='.$note);
    }


    //manage orders
    public function createOrder($total,$orderType,$paymentmethod,$taxAmount,$deliveryfee,$tipAmount,$isDelivery)
    {
        if($orderType=='default')
        {
            $res =  $this->callApi_Post("create_order",$this->Token,'total='.$total.'&OrderType=default&paymentmethod='.$paymentmethod.'&taxAmount='.$taxAmount.'&deliveryfee='.$deliveryfee.'&tipAmount='.$tipAmount.'&isDelivery='.$isDelivery);
        }
        else
            $res =  $this->callApi_Post("create_order",$this->Token,'total='.$total.'&OrderType='.$orderType.'&paymentmethod='.$paymentmethod.'&taxAmount='.$taxAmount.'&deliveryfee='.$deliveryfee.'&tipAmount='.$tipAmount.'&isDelivery='.$isDelivery);

        return $res;
    }
    public function addlineToOrder($oid,$item_uuid,$qte,$special_ins)
    {
        return $this->callApi_Post("create_line_in_order",$this->Token,'oid='.$oid.'&item='.$item_uuid.'&qte='.$qte.'&special_ins='.$special_ins);
    }

    public function addlineWithPriceToOrder($oid,$item_uuid,$qte,$special_ins,$price)
    {
        return $this->callApi_Post("create_line_in_order",$this->Token,'oid='.$oid.'&item='.$item_uuid.'&qte='.$qte.'&special_ins='.$special_ins.'&itemprice='.$price);
    }
    public function addModifierToLine($oid,$lineId,$modifer_uuid)
    {
        return $this->callApi_Post("add_modifier_to_line",$this->Token,'oid='.$oid.'&lineid='.$lineId.'&modifier='.$modifer_uuid);
    }

    //Pay the order
    public function payOrder($oid,$taxAmount,$amount,$zip,$expMonth,$cvv,$last4,$expYear,$first6,$cardEncrypted,$tipAmount)
    {
        return $this->callApi_Post("pay_order",$this->Token,'orderId='.$oid.'&taxAmount='.$taxAmount.'&amount='.$amount.'&zip='.$zip.'&expMonth='.$expMonth.
            '&cvv='.$cvv.'&last4='.$last4.'&first6='.$first6.'&expYear='.$expYear.'&cardEncrypted='.$cardEncrypted.'&tipAmount='.$tipAmount);
    }
    //Send Notification to the merchant when a new order is registered
    public function NotifyMerchant($oid,$instructions,$customer,$pickup_time,$paymentMethode)
    {
        return $this->callApi_Post("notify",$this->Token,'orderId='.$oid.'&instructions='.$instructions.'&pickup_time='.$pickup_time.'&paymentmethod='.$paymentMethode.'&customer='.json_encode($customer));
    }
    // OrderTypes
    public function GetOneOrdersTypes($uuid)
    {
        return $this->callApi("order_types/".$uuid,$this->Token);
    }
    public function GetOrdersTypes()
    {
        return $this->callApi("order_types",$this->Token);
    }
    public function addOrderType($label,$taxable)
	{
		return $this->callApi_Post("order_types",$this->Token,'label='.$label.'&taxable='.$taxable);
	}

	//Updtae the website for the merchant
    public function updateWebsite($url)
    {
        return $this->callApi_Post("addsite",$this->Token,'website='.$url);
    }
    public function updateWebsiteHooks($url)
    {
        return $this->callApi_Post("addsite_webhooks",$this->Token,'website='.$url);
    }
    //Create default Orders Types
    public function CreateOrdersTypes()
        {
            return $this->callApi("create_default_ot",$this->Token);
        }
    public function sendSms($code,$phone)
    {
        $phone = str_replace('+','%2B',$phone);
        $message = 'Your verification code is : '.$code;
        return $this->callApi_Post("sendsms",$this->Token,'to='.$phone.'&body='.$message);
    }
    public function sendSmsTo($message,$phone)
    {
        $phone = str_replace('+','%2B',$phone);
        return $this->callApi_Post("sendsms",$this->Token,'to='.$phone.'&body='.$message);
    }

    public function moo_CustomerVerifPhone($token,$phone)
    {
        return $this->callApi_Post("customers/verifphone",$this->Token,'phone='.$phone.'&token='.$token);
    }

    public function moo_CustomerLogin($email,$password)
    {
        return $this->callApi_Post('customers/login',$this->Token,'email='.$email.'&password='.$password);
    }
    public function moo_CustomerFbLogin($email,$fbid,$name,$gender)
    {
        return $this->callApi_Post('customers/fblogin',$this->Token,'email='.$email.'&id='.$fbid.'&name='.$name.'&gender='.$gender);
    }
    public function moo_CustomerSignup($title,$full_name,$email,$phone,$password)
    {
        return $this->callApi_Post('customers/signup',$this->Token,'email='.$email.'&password='.$password.'&title='.$title.'&full_name='.$full_name.'&phone='.$phone);
    }
    public function moo_ResetPassword($email)
    {
        return $this->callApi_Post('customers/resetpassword',$this->Token,'email='.$email);
    }
    public function moo_GetAddresses($token)
    {
        return $this->callApi_Post('customers/getaddress',$this->Token,'token='.$token);
    }
    public function moo_AddAddress($address,$city,$state,$country,$zipcode,$lat,$lng,$token)
    {
        return $this->callApi_Post('customers/setaddress',$this->Token,'token='.$token.'&address='.$address.'&city='.$city.'&state='.$state.'&country='.$country.'&zipcode='.$zipcode.'&lat='.$lat.'&lng='.$lng);
    }
    public function moo_DeleteAddresses($address_id,$token)
    {
        return $this->callApi_Post('customers/deleteaddress',$this->Token,'token='.$token.'&address_id='.$address_id);

    }
    public function moo_setDefaultAddresses()
    {

    }
    public function moo_updateAddresses()
    {

    }
    /*
     * Sync functions
     * @since 1.0.6
     */
    function getItem($uuid)
    {
        if($uuid=="") return;
        $res = $this->callApi("items/".$uuid,$this->Token);
        if($res){
            $saved = $this->save_one_item($res);
            return $saved;
        }
        else{
            return false;
        }
    }
    public function delete_item($uuid)
    {
        if($uuid=="") return;
        global $wpdb;
        //$wpdb->show_errors();
        $wpdb->query('START TRANSACTION');

        $wpdb->delete("{$wpdb->prefix}moo_item_tax_rate",array('item_uuid'=>$uuid));
        $wpdb->delete("{$wpdb->prefix}moo_item_modifier_group",array('item_id'=>$uuid));
        $wpdb->delete("{$wpdb->prefix}moo_item_tag",array('item_uuid'=>$uuid));

        //TODO : delete all attribute and options if it is the lonly item in the group_item
        $res = $wpdb->delete("{$wpdb->prefix}moo_item",array('uuid'=>$uuid));
        if($res)
        {
            $wpdb->query('COMMIT'); // if the item Inserted in the DB
        }
        else {
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
        $local_tr  = array(); // tax_rates in our DB
        $Clover_tr = array(); // tax_rates in Clover DB
        $tempo = $wpdb->get_results("SELECT uuid FROM {$wpdb->prefix}moo_tax_rate");
        foreach ($tempo as $l) {
            array_push($local_tr,$l->uuid);
        }

        $res = $this->callApi("tax_rates",$this->Token);
        $taxes_rates = json_decode($res)->elements;
        foreach ($taxes_rates as $l) {
            array_push($Clover_tr,$l->id);
        }

        // delete from the Local database the taxe rate thar are already deleted from Clover
        foreach (array_diff($local_tr,$Clover_tr) as $uuid) {
            $wpdb->query("DELETE FROM {$wpdb->prefix}moo_item_tax_rate where tax_rate_uuid='{$uuid}'");
            $wpdb->query("DELETE FROM {$wpdb->prefix}moo_tax_rate where uuid = '{$uuid}'");
        }

        $res = $this->callApi("tax_rates",$this->Token);
        $taxes_rates = json_decode($res)->elements;
        $count=0;
        foreach ($taxes_rates as $tax_rate)
        {
            if($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tax_rate where uuid='{$tax_rate->id}'") > 0)
                $updated = $wpdb->update("{$wpdb->prefix}moo_tax_rate",array(
                    'name' => $tax_rate->name,
                    'rate' => $tax_rate->rate,
                    'is_default' => $tax_rate->isDefault,
                ),array('uuid'=> $tax_rate->id));
            else
                $updated = $wpdb->insert("{$wpdb->prefix}moo_tax_rate",array(
                    'uuid'=> $tax_rate->id,
                    'name' => $tax_rate->name,
                    'rate' => $tax_rate->rate,
                    'is_default' => $tax_rate->isDefault,
                ));
            if( $updated !== false ){
                $count++;
            }
        }
        if(count($taxes_rates)==$count)
        {
            $wpdb->query('COMMIT'); // if the item Inserted in the DB
        }
        else {
            $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
        }

    }
    public function update_order_types()
    {
        global $wpdb;
        $result=array();
       // $wpdb->show_errors();
        $wpdb->query('START TRANSACTION');
        $res = $this->callApi("order_types",$this->Token);
        $res = json_decode($res)->elements;
        if(count($res)==0) return false;

        // Get the actual status from the database for example if a merchant is already enable an order type
        // So when importing the order types they will be enabled
        foreach ( $wpdb->get_results("SELECT ot_uuid,status,show_sa,type FROM {$wpdb->prefix}moo_order_types") as $st) {
            $status[$st->ot_uuid]['status']  = $st->status;
            $status[$st->ot_uuid]['show_sa'] = $st->show_sa;
            $status[$st->ot_uuid]['type'] = $st->type;
        };

        //Delete all ordertypes from wordpress database
        $wpdb->get_results("DELETE FROM {$wpdb->prefix}moo_order_types");

        //Adding the order types imported from CLOVER POS
        foreach ($res as $key=>$ot) {
            $result[$key] = $wpdb->insert("{$wpdb->prefix}moo_order_types",array(
                    'ot_uuid' => $ot->id,
                    'label' => $ot->label,
                    'taxable' => $ot->taxable,
                    'show_sa' => (isset($status[$ot->id]['show_sa']) && $status[$ot->id]['show_sa']==1 )?1:0,
                    'status' => (isset($status[$ot->id]['status']) && $status[$ot->id]['status']==1 )?1:0,
                    'type' => (isset($status[$ot->id]['type']))?$status[$ot->id]['type']:null
            ));

        }
        // If the all ordertypes imported are saved the COMMIT the changes else rollback
        if(count($res)==array_sum($result))
        {
            $wpdb->query('COMMIT'); // if the item Inserted in the DB
            return true;
        }
        else {
            $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
            return false;
        }

    }
    public function save_one_item($res)
    {
        global $wpdb;
        $wpdb->query('START TRANSACTION');
        $wpdb->show_errors();
        $item = json_decode($res);
        //print_r($item);
        if(isset($item->message) && !isset($item->id) ) return;

        /*
         * I verify if the Item is already in Wordpress DB
         */
        if($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_item where uuid='{$item->id}'") > 0)
        {
            if($item->itemGroup != NULL)
            {
                  $this->updateItemGroup($item->itemGroup->id);
            }

            $wpdb->delete("{$wpdb->prefix}moo_item_tax_rate",array('item_uuid'=>$item->id));
            $wpdb->delete("{$wpdb->prefix}moo_item_modifier_group",array('item_id'=>$item->id));
            $wpdb->delete("{$wpdb->prefix}moo_item_tag",array('item_uuid'=>$item->id));

            // update the Item
            $res1 = $wpdb->update("{$wpdb->prefix}moo_item",array(
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
            ),array('uuid'=>$item->id));
        }
        else
        {
            if($item->itemGroup===NULL)
                $res1 = $wpdb->insert("{$wpdb->prefix}moo_item",array(
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
                $res1 = $wpdb->insert("{$wpdb->prefix}moo_item",array(
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
        }


        //save the taxes rates
        foreach($item->taxRates->elements as $tax_rate)
        {
            if( $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tax_rate where uuid='{$tax_rate->id}'") == 0 )
            {
                $table = array('elements'=>array($tax_rate));
                $this->save_tax_rates(json_encode($table));
            }

            $wpdb->insert("{$wpdb->prefix}moo_item_tax_rate",array(
                'tax_rate_uuid' => $tax_rate->id,
                'item_uuid' => $item->id
            ));
        }

        //save modifierGroups
        foreach($item->modifierGroups->elements as $modifier_group)
        {

            if( $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_modifier_group where  uuid='{$modifier_group->id}'") == 0 )
            {
                $this->getOneModifierGroups($modifier_group->id);
            }
            $wpdb->insert("{$wpdb->prefix}moo_item_modifier_group",array(
                'group_id' => $modifier_group->id,
                'item_id' => $item->id
            ));
        }


        //save Tags
        foreach($item->tags->elements as $tag)
        {
            if( $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tag where uuid='{$tag->id}'") == 0 )
            {
                $table = array('elements'=>array($tag));
                $this->save_tags(json_encode($table));
            }
            $wpdb->insert("{$wpdb->prefix}moo_item_tag",array(
                'tag_uuid' => $tag->id,
                'item_uuid' => $item->id
            ));
        }
        //save New categories
        foreach($item->categories->elements as $category)
        {
            //I verify if the category is already saved in Wordpress database
            if( $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_category where uuid='{$category->id}'") == 0 )
            {
                    $this->getCategories();
            }
        }
        if($res1)
        {
            $wpdb->query('COMMIT'); // if the item Inserted in the DB
        }
        else {
            $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
        }
    }
    public function update_item($item)
    {

        global $wpdb;
        $wpdb->query('START TRANSACTION');

        // $wpdb->show_errors();
        /*
         * I verify if the Item is already in Wordpress DB and if it's up to date
         */
        if($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_item where uuid='{$item->id}'") > 0)
        {
            if($item->itemGroup != NULL)
            {
                $this->updateItemGroup($item->itemGroup->id);
            }
            $wpdb->delete("{$wpdb->prefix}moo_item_tax_rate",array('item_uuid'=>$item->id));
            $wpdb->delete("{$wpdb->prefix}moo_item_modifier_group",array('item_id'=>$item->id));
            $wpdb->delete("{$wpdb->prefix}moo_item_tag",array('item_uuid'=>$item->id));
           // update the Item
            $res1=$wpdb->update("{$wpdb->prefix}moo_item",array(
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
            ),array('uuid'=>$item->id));
            //var_dump($res1);
            if($res1>=0) $res1 = true;
        }
        else
        {
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
            if($item->itemGroup != NULL)
                $item_To_Add['item_group_uuid'] = $item->itemGroup->id;

            $res1 = $wpdb->insert("{$wpdb->prefix}moo_item",$item_To_Add);
        }

        //save the taxes rates
        foreach($item->taxRates->elements as $tax_rate)
        {
            if( $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tax_rate where uuid='{$tax_rate->id}'") == 0 )
            {
                $table = array('elements'=>array($tax_rate));
                $this->save_tax_rates(json_encode($table));
            }

            $wpdb->insert("{$wpdb->prefix}moo_item_tax_rate",array(
                'tax_rate_uuid' => $tax_rate->id,
                'item_uuid' => $item->id
            ));
        }

        //save modifierGroups
        foreach($item->modifierGroups->elements as $modifier_group)
        {
            if( $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_modifier_group where  uuid='{$modifier_group->id}'") == 0 )
            {
                $this->getOneModifierGroups($modifier_group->id);
            }
            $wpdb->insert("{$wpdb->prefix}moo_item_modifier_group",array(
                'group_id' => $modifier_group->id,
                'item_id' => $item->id
            ));
        }


        //save Tags
        foreach($item->tags->elements as $tag)
        {
            if( $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_tag where uuid='{$tag->id}'") == 0 )
            {
                $table = array('elements'=>array($tag));
                $this->save_tags(json_encode($table));
            }
            $wpdb->insert("{$wpdb->prefix}moo_item_tag",array(
                'tag_uuid' => $tag->id,
                'item_uuid' => $item->id
            ));
        }
        if($res1)
        {
            $wpdb->query('COMMIT'); // if the item Inserted in the DB
            return true;
        }
        else {
            $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
            return false;
        }
    }
    public function update_category($category)
    {
        global $wpdb;
        $items_ids="";

        foreach($category->items->elements as $item)
            $items_ids.=$item->id.",";

        if($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_category where uuid='{$category->id}'") > 0)
            $res = $wpdb->update("{$wpdb->prefix}moo_category",array(
                'name' => $category->name,
                'items' =>  $items_ids
            ),array('uuid'=>$category->id));
        else
            $res = $wpdb->insert("{$wpdb->prefix}moo_category",array(
                'uuid'=>$category->id,
                'name' => $category->name,
                'sort_order' => $category->sortOrder,
                'show_by_default' => 1,
                'items' =>  $items_ids
            ));

        if($res>0) return true;
        return false;
    }
    public function update_modifierGroups($modifier_groups)
    {
        global $wpdb;
        if($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_modifier_group where uuid='{$modifier_groups->id}'") > 0)
            $res = $wpdb->update("{$wpdb->prefix}moo_modifier_group",array(
                'name' => $modifier_groups->name,
                'min_required' => $modifier_groups->minRequired,
                'max_allowd' => $modifier_groups->maxAllowed

            ),array('uuid' => $modifier_groups->id));
        else
            $res = $wpdb->insert("{$wpdb->prefix}moo_modifier_group",array(
                'uuid' => $modifier_groups->id,
                'name' => $modifier_groups->name,
                'alternate_name' => $modifier_groups->alternateName,
                'show_by_default' => $modifier_groups->showByDefault,
                'min_required' => $modifier_groups->minRequired,
                'max_allowd' => $modifier_groups->maxAllowed

            ));

        if($res>0) return true;
        return false;
    }
    public function update_modifier($modifier)
    {
        global $wpdb;
        if($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}moo_modifier where uuid='{$modifier->id}'") > 0)
            $res = $wpdb->update("{$wpdb->prefix}moo_modifier",array(
                'name' => $modifier->name,
                'alternate_name' => $modifier->alternateName,
                'price' => $modifier->price,
                'group_id' => $modifier->modifierGroup->id,

            ),array('uuid' => $modifier->id));
        else
            $res = $wpdb->insert("{$wpdb->prefix}moo_modifier",array(
                'uuid' => $modifier->id,
                'name' => $modifier->name,
                'alternate_name' => $modifier->alternateName,
                'price' => $modifier->price,
                'group_id' => $modifier->modifierGroup->id
            ));
        if($res>0) return true;
        return false;
    }
    public function send_an_email($order_id,$emails,$customer,$instructions,$pickup_time,$tipAmount,$taxAmount,$deliveryAmount)
    {
        return $this->callApi_Post("sendemails",$this->Token,"order_id=".$order_id."&merchant_emails=".$emails."&customer=".$customer."&instructions=".$instructions."&pickup_time=".$pickup_time."&tipAmount=".$tipAmount."&taxAmount=".$taxAmount."&deliveryAmount=".$deliveryAmount);
    }
    public function checkToken()
    {
        $url = "checktoken";
        return $this->callApi($url,$this->Token);
    }
    public function checkIpBlackListed()
    {
        return $this->checkIP($this->Token);
    }
    public function getOrderDetails($order)
    {
        $result = array();
        $url='orders/'.$order->uuid;
        $orderFromServer = json_decode($this->callApi($url,$this->Token));

        if(isset($orderFromServer))
        {
            $result['uuid_order']=$orderFromServer->order->uuid;
            $result['amount_order']=$orderFromServer->order->amount/100;
            $result['order_type']=$orderFromServer->order->order_type;
            $result['special_instruction']=$orderFromServer->order->special_instruction;

            if($orderFromServer->order->date != "")
            {
                $result['date_order']= date('d/m/Y H:i:s', $orderFromServer->order->date/1000);
            }
            if (isset($orderFromServer->order->paymentMethode) && $orderFromServer->order->paymentMethode != ""){
                $result['paymentMethode']=$orderFromServer->order->paymentMethode;
            }
            else{
                $result['paymentMethode']="No";
            }
            if (isset($orderFromServer->order->taxAmount) && $orderFromServer->order->taxAmount != ""){
                $result['taxAmount']=$orderFromServer->order->taxAmount/100;
            }
            else{
                $result['taxAmount']=$order->taxAmount;
            }
            if (isset($orderFromServer->order->deliveryAmount) && $orderFromServer->order->deliveryAmount != ""){
                $result['deliveryAmount']=$orderFromServer->order->deliveryAmount/100;
            }
            else {
                $result['deliveryAmount']=$order->deliveryfee;
            }
            if (isset($orderFromServer->order->tipAmount) && $orderFromServer->order->tipAmount != ""){
                $result['tipAmount']=$orderFromServer->order->tipAmount/100;
            }
            else{
                $result['tipAmount']=$order->tipAmount;
            }
            if (isset($orderFromServer->customer->name) && $orderFromServer->customer->name != ""){
                $result['name_customer']=$orderFromServer->customer->name;
            }
            else{
                $result['name_customer']=$order->p_name;
            }
            if (isset($orderFromServer->customer->email) && $orderFromServer->customer->email != ""){
                $result['email_customer'] = $orderFromServer->customer->email;
            }
            else{
                $result['email_customer']=$order->p_email;
            }
            if (isset($orderFromServer->customer->phone) && $orderFromServer->customer->phone != ""){
                $result['phone_customer']=$orderFromServer->customer->phone;
            }
            else {
                $result['phone_customer']=$order->p_phone;
            }
            if (isset($orderFromServer->customer->address)&& $orderFromServer->customer->address == ""){
                $result['address_customer']= $orderFromServer->customer->address;
            }
            else{
                $result['address_customer']=$order->p_address;
            }
            if (isset($orderFromServer->customer->city) && $orderFromServer->customer->city != ""){
                $result['city_customer']=$orderFromServer->customer->city;
            }
            else{
                $result['city_customer']=$order->p_city;
            }
            if ($orderFromServer->customer->state && $orderFromServer->customer->state != ""){
                $result['state_customer']=$orderFromServer->customer->state;
            }
            else {
                $result['state_customer']=$order->p_state;
            }
            if (isset($orderFromServer->customer->zipcode) && $orderFromServer->customer->zipcode != ""){
                $result['zipcode']=$orderFromServer->customer->zipcode;
            }
            else {
                $result['zipcode']=$order->p_zipcode;
            }
            if (isset($orderFromServer->customer->lat) && $orderFromServer->customer->lat != ""){
                $result['lat']=$orderFromServer->customer->lat;
            }
            else {
                $result['lat']=$order->p_lat;
            }
            if (isset($orderFromServer->customer->lng) && $orderFromServer->customer->lng != ""){
                $result['lng']=$orderFromServer->customer->lng;
            }
            else {
                $result['lng']=$order->p_lng;
            }
            $result['payments']=$orderFromServer->payments;
        }
        else
        {
            $result['uuid_order']=$order->uuid;
            $result['amount_order']=$order->amount;
            $result['order_type']=$order->ordertype;
            $result['special_instruction']=$order->instructions;
            $result['date_order']=$order->date;
            $result['paymentMethode']=$orderFromServer->order['paymentMethode'];
            $result['taxAmount']=$order->taxAmount;
            $result['deliveryAmount']=$order->deliveryfee;
            $result['tipAmount']=$order->tipAmount;
            $result['name_customer']=$order->p_name;
            $result['email_customer']=$order->p_email;
            $result['phone_customer']=$order->p_phone;
            $result['address_customer']=$order->p_address;
            $result['city_customer']=$order->p_city;
            $result['state_customer']=$order->p_state;
            $result['zipcode']=$order->p_zipcode;
            $result['lat']=$order->p_lat;
            $result['lng']=$order->p_lng;
            $result['payments']=$orderFromServer->payments;
        }
        //return json_decode($orderFromServer);
        //var_dump($result);
        return $result;
    }

    //Funciton to save DATA in db

    private function save_tax_rates($obj)
    {
        global $wpdb;
       // $wpdb->show_errors();
        $count=0;
        foreach (json_decode($obj)->elements as $tax_rate)
        {
            $wpdb->insert("{$wpdb->prefix}moo_tax_rate",array(
                                                'uuid' => $tax_rate->id,
                                                'name' => $tax_rate->name,
                                                'rate' => $tax_rate->rate,
                                                'is_default' => $tax_rate->isDefault,
                ));

            if($wpdb->insert_id!=0) $count++;
        }

        return $count;
    }
    private function save_tags($obj)
    {
        global $wpdb;
       // $wpdb->show_errors();
        $count=0;
        foreach (json_decode($obj)->elements as $tag)
        {
            $wpdb->insert("{$wpdb->prefix}moo_tag",array(
                                                'uuid' => $tag->id,
                                                'name' => $tag->name
                ));

            if($wpdb->insert_id!=0) $count++;
        }

        return $count;
    }
    private function save_options($obj)
    {
        global $wpdb;
        //$wpdb->show_errors();
        $count=0;
        foreach (json_decode($obj)->elements as $option)
        {
            //var_dump($option);
            $wpdb->insert("{$wpdb->prefix}moo_option",array(
                                                'uuid' => $option->id,
                                                'name' => $option->name,
                                                'attribute_uuid' => $option->attribute->id
                ));

            if($wpdb->insert_id!=0) $count++;
        }

        return $count;
    }
    private function save_attributes($obj)
    {
        global $wpdb;
        //$wpdb->show_errors();
        $count=0;
        foreach (json_decode($obj)->elements as $attribute)
        {
            $wpdb->insert("{$wpdb->prefix}moo_attribute",array(
                                                'uuid' => $attribute->id,
                                                'name' => $attribute->name,
                                                'item_group_uuid' => $attribute->itemGroup->id
                ));

            if($wpdb->insert_id!=0) $count++;
        }

        return $count;
    }
    private function save_modifiers($obj)
    {
        global $wpdb;
       // $wpdb->show_errors();
        $count=0;
        foreach (json_decode($obj)->elements as $modifier)
        {
            $wpdb->insert("{$wpdb->prefix}moo_modifier",array(
                                                'uuid' => $modifier->id,
                                                'name' => $modifier->name,
                                                'alternate_name' => $modifier->alternateName,
                                                'price' => $modifier->price,
                                                'group_id' => $modifier->modifierGroup->id,

                ));

            if($wpdb->insert_id!=0) $count++;
        }

        return $count;


    }
    private function save_items($obj)
    {
        global $wpdb;
        $count=0;
        foreach (json_decode($obj)->elements as $item)
        {
            //Save the item
            if($item->itemGroup===NULL)
                    $wpdb->insert("{$wpdb->prefix}moo_item",array(
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
                    $wpdb->insert("{$wpdb->prefix}moo_item",array(
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

            if($wpdb->insert_id!=0) $count++;

            //save the taxes rates
            foreach($item->taxRates->elements as $tax_rate)
            {
                $wpdb->insert("{$wpdb->prefix}moo_item_tax_rate",array(
                    'tax_rate_uuid' => $tax_rate->id,
                    'item_uuid' => $item->id
                ));
            }

            //save modifierGroups
            foreach($item->modifierGroups->elements as $modifier_group)
            {
                $wpdb->insert("{$wpdb->prefix}moo_item_modifier_group",array(
                    'group_id' => $modifier_group->id,
                    'item_id' => $item->id
                ));
            }


            //save Tags
            foreach($item->tags->elements as $tag)
            {
                $wpdb->insert("{$wpdb->prefix}moo_item_tag",array(
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
        $count=0;
        foreach (json_decode($obj)->elements as $modifier_groups)
        {
            $wpdb->insert("{$wpdb->prefix}moo_modifier_group",array(
                                                'uuid' => $modifier_groups->id,
                                                'name' => $modifier_groups->name,
                                                'alternate_name' => $modifier_groups->alternateName,
                                                'show_by_default' => $modifier_groups->showByDefault,
                                                'min_required' => $modifier_groups->minRequired,
                                                'max_allowd' => $modifier_groups->maxAllowed,

                ));
            if($wpdb->insert_id!=0) $count++;
        }
        return $count;

    }
    private function save_item_groups($obj)
    {
        global $wpdb;
        $count=0;
        foreach (json_decode($obj)->elements as $item_group)
        {
            $wpdb->insert("{$wpdb->prefix}moo_item_group",array(
                                                'uuid' => $item_group->id,
                                                'name' => $item_group->name

                ));
            if($wpdb->insert_id!=0) $count++;
        }
        return $count;
    }
    private function save_categories($obj)
    {
        global $wpdb;
        $count=0;

       // var_dump(json_decode($obj));
        foreach (json_decode($obj)->elements as $cat)
        {
            $items_ids="";
            foreach($cat->items->elements as $item)
                $items_ids.=$item->id.",";
            $wpdb->insert("{$wpdb->prefix}moo_category",array(
                                                'uuid' => $cat->id,
                                                'name' => $cat->name,
                                                'sort_order' => $cat->sortOrder,
                                                'show_by_default' => 1,
                                                'items' =>  $items_ids
                ));
            if($wpdb->insert_id!=0) $count++;
        }
        return $count;
    }
    private function save_order_types($obj)
    {
        global $wpdb;
        $count=0;
        foreach (json_decode($obj)->elements as $ot)
        {
            $res = $wpdb->insert("{$wpdb->prefix}moo_order_types",array(
                                                'ot_uuid' => $ot->id,
                                                'label' => $ot->label,
                                                'taxable' => $ot->taxable,
                                                'show_sa' => ($ot->label=='Delivery')?1:0,
                                                'status' => ($ot->label=='Delivery' || $ot->label == 'Pickup')?1:0
                ));

            if($res == 1) $count++;
        }
        return $count;
    }
	public function  save_One_orderType($ot)
    {
        global $wpdb;
        $res = $wpdb->insert("{$wpdb->prefix}moo_order_types",array(
                                            'ot_uuid' => $ot->id,
                                            'label' => $ot->label,
                                            'taxable' => $ot->taxable,
                                            'status' => 0
            ));
        return $res;
    }

    private function checkIP($accesstoken)
    {

        $headr = array();
        $headr[] = 'Accept: application/json';
        $headr[] = 'X-Authorization: '.$accesstoken;
        $url=  $this->url_api.'checktoken';
        //cURL starts
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_HTTPGET,true);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER,false);
        $reply = curl_exec($crl);
        //error handling for cURL
        if ($reply === false) {
            print_r('Curl error: ' . curl_error($crl));
            return $reply;
        }
        curl_close($crl);
        return $reply;
    }

    private function callApi($url,$accesstoken)
    {

        $headr = array();
        $headr[] = 'Accept: application/json';
        $headr[] = 'X-Authorization: '.$accesstoken;
        $url=  $this->url_api.$url;
        //cURL starts
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_HTTPGET,true);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER,false);
        $reply = curl_exec($crl);

        //error handling for cURL
        if ($reply === false) {
            print_r('Curl error: ' . curl_error($crl));
            return false;
        }
        $info = curl_getinfo($crl);
        curl_close($crl);
        if($this->debug)
        {
            echo $url." GET ---- ";
            var_dump($reply);
        }
        if( $info['http_code']==200 )return $reply;
        return false;
    }
    private function callApi_Post($url,$accesstoken,$fields_string)
    {
        $headr = array();
        $headr[] = 'Accept: application/json';
        $headr[] = 'X-Authorization: '.$accesstoken;
        $url=  $this->url_api.$url;

        //cURL starts
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_POST,true);
        curl_setopt($crl,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER,false);
        $reply = curl_exec($crl);
        //error handling for cURL
        if ($reply === false) {
            print_r('Curl error: ' . curl_error($crl));
            return false;
        }

        $info = curl_getinfo($crl);
        curl_close($crl);
        if($this->debug)
        {
            echo $url." POST ---- ";
            var_dump($reply);
        }
        if($info['http_code'] == 200)
            return $reply;
        return false;
    }
}