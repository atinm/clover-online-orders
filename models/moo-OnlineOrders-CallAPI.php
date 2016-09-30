<?php

class moo_OnlineOrders_CallAPI {

    public $Token;
    public $url_api;


    function __construct()
    {
        $MooSettings = (array) get_option("moo_settings");
        $this->Token = $MooSettings['api_key'];

		//Put the API URL here and don't forget the last slash
        $this->url_api = "http://api.smartonlineorders.com/";


    }
    /*
     * This functions import data from Clover POS and call the save functions
     * for example : getCategories get JSON object of categories from Clover POS and call the function save_categories
     * to save the this categories in Wordpress DB
     */
	function getCategories()
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
    function getItemGroups()
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
    function getModifierGroups()
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
    function getOneModifierGroups($uuid)
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
    function getItems()
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
    function getItemsWithoutSaving($page)
    {
       $res = $this->callApi("items_expanded/".$page."/20",$this->Token);
       return $res;
    }
    function getCategoriesWithoutSaving()
    {
       return  $this->callApi("categories",$this->Token);
    }
    function getModifiersGroupsWithoutSaving()
    {
       return $this->callApi("modifier_groups",$this->Token);
    }
    function getModifiersWithoutSaving()
    {
       return $this->callApi("modifiers",$this->Token);

    }
    function getModifiers()
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
    function getAttributes()
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
    function getOptions()
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
    function updateItemGroup($uuid)
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
    function getTags()
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
    function getTaxRates()
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
    function getOrderTypes()
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
    function getPayKey()
    {
        return $this->callApi("paykey",$this->Token);
    }
    //create the order
    function createOrder($total,$orderType,$paymentmethod)
    {
        if($orderType=='default')
        {
            $res =  $this->callApi_Post("create_order",$this->Token,'total='.$total.'&OrderType=default&paymentmethod='.$paymentmethod);
        }
        else
            $res =  $this->callApi_Post("create_order",$this->Token,'total='.$total.'&OrderType='.$orderType.'&paymentmethod='.$paymentmethod);

        return $res;
    }
    //create the order
    function addlineToOrder($oid,$item_uuid,$qte,$special_ins)
    {
        return $this->callApi_Post("create_line_in_order",$this->Token,'oid='.$oid.'&item='.$item_uuid.'&qte='.$qte.'&special_ins='.$special_ins);
    }
    function addlineWithPriceToOrder($oid,$item_uuid,$qte,$special_ins,$price)
    {
        return $this->callApi_Post("create_line_in_order",$this->Token,'oid='.$oid.'&item='.$item_uuid.'&qte='.$qte.'&special_ins='.$special_ins.'&itemprice='.$price);
    }
    //create the order
    function addModifierToLine($oid,$lineId,$modifer_uuid)
    {
        return $this->callApi_Post("add_modifier_to_line",$this->Token,'oid='.$oid.'&lineid='.$lineId.'&modifier='.$modifer_uuid);
    }
    //Pay the order
    function payOrder($oid,$taxAmount,$amount,$zip,$expMonth,$cvv,$last4,$expYear,$first6,$cardEncrypted,$tipAmount)
    {
        return $this->callApi_Post("pay_order",$this->Token,'orderId='.$oid.'&taxAmount='.$taxAmount.'&amount='.$amount.'&zip='.$zip.'&expMonth='.$expMonth.
            '&cvv='.$cvv.'&last4='.$last4.'&first6='.$first6.'&expYear='.$expYear.'&cardEncrypted='.$cardEncrypted.'&tipAmount='.$tipAmount);
    }
    //Send Notification to the merchant when a new order is registered
    function NotifyMerchant($oid,$instructions,$customer,$pickup_time,$paymentMethode)
    {
        return $this->callApi_Post("notify",$this->Token,'orderId='.$oid.'&instructions='.$instructions.'&pickup_time='.$pickup_time.'&paymentmethod='.$paymentMethode.'&customer='.json_encode($customer));
    }
    // OrderTypes
    function GetOneOrdersTypes($uuid)
    {
        return $this->callApi("order_types/".$uuid,$this->Token);
    }
    function GetOrdersTypes()
    {
        return $this->callApi("order_types",$this->Token);
    }
	function addOrderType($label,$taxable)
	{
		return $this->callApi_Post("order_types",$this->Token,'label='.$label.'&taxable='.$taxable);
	}
    //Updtae the website for the merchant
    function updateWebsite($url)
    {
        return $this->callApi_Post("addsite",$this->Token,'website='.$url);
    }
    function updateWebsiteHooks($url)
    {
        return $this->callApi_Post("addsite_webhooks",$this->Token,'website='.$url);
    }
    function getMerchantAddress()
    {
        return $this->callApi("address",$this->Token);
    }
    function getOpeningHours()
    {
        return $this->callApi("opening_hours",$this->Token);
    }
    function getOpeningStatus($nb_days,$nb_minites,$mindays)
    {
        if($mindays>0)
            return $this->callApi("is_open/".$nb_days."/".$nb_minites."/".$mindays,$this->Token);
        else
            return $this->callApi("is_open/".$nb_days."/".$nb_minites,$this->Token);

    }
    function getMerchantProprietes()
    {
        return $this->callApi("properties",$this->Token);
    }
    //Create default Orders Types
    function CreateOrdersTypes()
    {
        return $this->callApi("create_default_ot",$this->Token);
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
    function updateOrderNote($orderId,$note)
    {
        return $this->callApi_Post("update_order/".$orderId,$this->Token,'note='.$note);
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
        update_option('moo_store_openingHours',$this->getOpeningHours());
        global $wpdb;
        $result=array();
       // $wpdb->show_errors();
        $wpdb->query('START TRANSACTION');

        $res = $this->callApi("order_types",$this->Token);
        $res = json_decode($res)->elements;

        if(count($res)==0) return false;

        // Get the actual status from the database for example if a merchant is already enable an order type
        // So when importing the order types they will be enabled
        foreach ( $wpdb->get_results("SELECT ot_uuid,status,show_sa FROM {$wpdb->prefix}moo_order_types") as $st) {
            $status[$st->ot_uuid]['status']  = $st->status;
            $status[$st->ot_uuid]['show_sa'] = $st->show_sa;
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
                    'status' => (isset($status[$ot->id]['status']) && $status[$ot->id]['status']==1 )?1:0
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
                'sort_order' => $category->sortOrder,
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
            $res = $wpdb->insert("{$wpdb->prefix}moo_modifier_group",array(
                'uuid' => $modifier->id,
                'name' => $modifier->name,
                'alternate_name' => $modifier->alternateName,
                'price' => $modifier->price,
                'group_id' => $modifier->modifierGroup->id
            ));
        if($res>0) return true;
        return false;
    }

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
	    //echo 'GET : '.$url;
       // var_dump($reply);
        if($info['http_code']==200)return $reply;
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
	   // echo $url." ---- ";
       // var_dump($reply);
        if($info['http_code'] == 200)
            return $reply;
        return false;
    }
}