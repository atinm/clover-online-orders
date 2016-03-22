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

    //Function to call the API for make Orders and payments
    function getPayKey()
    {
        return $this->callApi("paykey",$this->Token);
    }

    //create the order
    function createOrder($total,$orderType)
    {
        if($orderType=='default')
        {
            $res =  $this->callApi_Post("create_order",$this->Token,'total='.$total.'&OrderType=default');
        }
        else
            $res =  $this->callApi_Post("create_order",$this->Token,'total='.$total.'&OrderType='.$orderType);
        return $res;
    }
    //create the order
    function addlineToOrder($oid,$item_uuid,$qte)
    {
        return $this->callApi_Post("create_line_in_order",$this->Token,'oid='.$oid.'&item='.$item_uuid.'&qte='.$qte);
    }
    //create the order
    function addModifierToLine($oid,$lineId,$modifer_uuid)
    {
        return $this->callApi_Post("add_modifier_to_line",$this->Token,'oid='.$oid.'&lineid='.$lineId.'&modifier='.$modifer_uuid);
    }

    //Pay the order
    function payOrder($oid,$taxAmount,$amount,$zip,$expMonth,$cvv,$last4,$expYear,$first6,$cardEncrypted)
    {
        return $this->callApi_Post("pay_order",$this->Token,'orderId='.$oid.'&taxAmount='.$taxAmount.'&amount='.$amount.'&zip='.$zip.'&expMonth='.$expMonth.
            '&cvv='.$cvv.'&last4='.$last4.'&first6='.$first6.'&expYear='.$expYear.'&cardEncrypted='.$cardEncrypted);
    }
    //Send Notification to the merchant when a new order is registered
    function NotifyMerchant($oid)
    {
        return $this->callApi_Post("notify",$this->Token,'orderId='.$oid);
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
    //Create default Orders Types
    function CreateOrdersTypes()
    {
        return $this->callApi("create_default_ot",$this->Token);
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
                                                'status' => ($ot->label=='Delivery' || $ot->label == 'Pickup')?1:0
                ));

            if($res == 1) $count++;
        }
        return $count;
    }
	public function save_One_orderType($ot)
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
	  //  echo 'GET : '.$url;
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
	 //   echo $url." ---- ";
     //   var_dump($reply);
        if($info['http_code']==200)return $reply;
        return false;
    }
}