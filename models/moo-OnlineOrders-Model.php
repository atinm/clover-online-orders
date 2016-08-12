<?php

class moo_OnlineOrders_Model {

    public $db;


    function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }
    function getCategories()
    {
        return $this->db->get_results("SELECT * FROM {$this->db->prefix}moo_category ORDER BY 4");
    }
    function getItems()
    {
        return $this->db->get_results("SELECT * FROM {$this->db->prefix}moo_item");
    }
    function getCategory($uuid)
    {
        $uuid = esc_sql($uuid);
        return $this->db->get_row("SELECT *
                                    FROM {$this->db->prefix}moo_category c
                                    WHERE c.uuid = '{$uuid}'
                                    ");
    }
    function getItem($uuid)
    {
        $uuid = esc_sql($uuid);
        return $this->db->get_row("SELECT *
                                    FROM {$this->db->prefix}moo_item i
                                    WHERE i.uuid = '{$uuid}'
                                    ");
    }
    function getItemsBySearch($motCle)
    {
        $motCle = esc_sql($motCle);
        return $this->db->get_results("SELECT *
                                    FROM {$this->db->prefix}moo_item i
                                    WHERE i.name like '%{$motCle}%'
                                    ");
    }

    function getItemTax_rate($uuid)
    {
        $item = $this->getItem($uuid);

        if($item->default_taxe_rate){
            $taxes = $this->db->get_results("SELECT uuid,rate
                                    FROM {$this->db->prefix}moo_tax_rate t
                                    WHERE t.is_default = 1
                                    ");
            return $taxes;
        }
        else
        {
            $taxes = $this->db->get_results("SELECT uuid,rate FROM {$this->db->prefix}moo_item_tax_rate itr,{$this->db->prefix}moo_tax_rate tr
                                          WHERE itr.tax_rate_uuid=tr.uuid
                                          AND itr.item_uuid='{$uuid}'

                                    ");
            return $taxes;
        }
    }

    function getModifiers($uuid_group)
    {
        $uuid_group = esc_sql($uuid_group);
        return $this->db->get_results("SELECT *
                                    FROM `{$this->db->prefix}moo_modifier` m
                                    WHERE m.group_id = '{$uuid_group}'
                                    ");
    }
    function getModifiersGroup($item)
    {
        return $this->db->get_results("SELECT mg.*
                                    FROM `{$this->db->prefix}moo_item_modifier_group` img,  `{$this->db->prefix}moo_modifier_group` mg
                                    WHERE mg.uuid=img.group_id AND mg.show_by_default='1'
                                    AND img.item_id = '{$item}'
                                    ");
    }
    function getAllModifiersGroup()
    {
        return $this->db->get_results("SELECT *
                                    FROM `{$this->db->prefix}moo_modifier_group`");
    }
    function itemHasModifiers($item)
    {
        return $this->db->get_row("SELECT count(*) as total
                                    FROM `{$this->db->prefix}moo_item_modifier_group` img, `{$this->db->prefix}moo_modifier_group` mg, `{$this->db->prefix}moo_modifier` m
                                    WHERE img.group_id = mg.uuid AND img.item_id = '{$item}' AND mg.uuid=m.group_id AND mg.show_by_default='1'
                                    ");
    }
    function getModifiersGroupLimits($uuid)
    {
        return $this->db->get_row("SELECT min_required, max_allowd, name
                                    FROM `{$this->db->prefix}moo_modifier_group` mg
                                    WHERE mg.uuid = '{$uuid}'
                                    ");
    }
    function getItemModifiersGroupsRequired($uuid)
    {
        return $this->db->get_results("SELECT mg.uuid
                                    FROM `{$this->db->prefix}moo_modifier_group` mg,`{$this->db->prefix}moo_item` item,`{$this->db->prefix}moo_item_modifier_group` item_mg  
                                    WHERE item_mg.item_id =  item.uuid
                                    AND item_mg.group_id =  mg.uuid
                                    AND item.uuid = '{$uuid}'
                                    AND mg.min_required >= 1
                                    ");
    }
    function getModifier($uuid)
{
    return $this->db->get_row("SELECT *
                                    FROM `{$this->db->prefix}moo_modifier` m
                                    WHERE m.uuid = '{$uuid}'
                                    ");
}
    function getOrderTypes()
{
    return $this->db->get_results("SELECT * FROM {$this->db->prefix}moo_order_types");
}
    function getOrder($orderId)
{
    return $this->db->get_results("SELECT * FROM {$this->db->prefix}moo_order where uuid='".$orderId."'");
}
    function getVisibleOrderTypes()
{
    return $this->db->get_results("SELECT * FROM {$this->db->prefix}moo_order_types where status=1");
}

    function updateOrderTypes($uuid,$status)
    {
        $uuid = esc_sql($uuid);
        $st = ($status == "true")? 1:0;
        return $this->db->update("{$this->db->prefix}moo_order_types",
                                array(
                                    'status' => $st
                                ),
                                array( 'ot_uuid' => $uuid )
        );
    }
    function updateOrderTypesSA($uuid,$bool)
    {
        $uuid = esc_sql($uuid);
        $st = ($bool == "true")? 1:0;
        return $this->db->update("{$this->db->prefix}moo_order_types",
                                array(
                                    'show_sa' => $st
                                ),
                                array( 'ot_uuid' => $uuid )
        );
    }

    function ChangeModifierGroupName($mg_uuid,$name)
    {
        $uuid = esc_sql($mg_uuid);
        $name = esc_sql($name);
        return $this->db->update("{$this->db->prefix}moo_modifier_group",
                                array(
                                    'alternate_name' => $name
                                ),
                                array( 'uuid' => $uuid )
        );
        
    } 
    function UpdateModifierGroupStatus($mg_uuid,$status)
    {
        $uuid = esc_sql($mg_uuid);
        $st = ($status == "true")? 1:0;

        return $this->db->update("{$this->db->prefix}moo_modifier_group",
                                array(
                                    'show_by_default' => $st
                                ),
                                array( 'uuid' => $uuid )
        );
        
    }
    function ChangeCategoryName($cat_uuid,$name)
    {
        $uuid = esc_sql($cat_uuid);
        $name = esc_sql($name);
        return $this->db->update("{$this->db->prefix}moo_category",
            array(
                'name' => $name
            ),
            array( 'uuid' => $uuid )
        );

    }
    function UpdateCategoryStatus($cat_uuid,$status)
    {
        $uuid = esc_sql($cat_uuid);
        $st = ($status == "true")? 1:0;

        return $this->db->update("{$this->db->prefix}moo_category",
                                array(
                                    'show_by_default' => $st
                                ),
                                array( 'uuid' => $uuid )
        );

    }
	function moo_DeleteOrderType($uuid)
{
    $uuid = esc_sql($uuid);
    return $this->db->delete("{$this->db->prefix}moo_order_types",
                            array( 'ot_uuid' => $uuid )
    );
}

    function addOrder($uuid,$tax,$total,$name,$address, $city,$zipcode,$phone,$email,$instructions,$state,$country,$deliveryFee,$tipAmount,$shippingFee,$customer_lat,$customer_lng,$ordertype)
    {
        $uuid         = esc_sql($uuid);
        $tax          = esc_sql($tax);
        $total        = esc_sql($total);
        $name         = esc_sql($name);
        $address      = esc_sql($address);
        $city         = esc_sql($city);
        $zipcode      = esc_sql($zipcode);
        $phone        = esc_sql($phone);
        $email        = esc_sql($email);
        $instructions = esc_sql($instructions);
        $ordertype    = esc_sql($ordertype);

        $state       = esc_sql($state);
        $country     = esc_sql($country);

        $deliveryFee     = esc_sql($deliveryFee);
        $tipAmount       = esc_sql($tipAmount);
        $shippingFee     = esc_sql($shippingFee);
        $customer_lat    = esc_sql($customer_lat);
        $customer_lng    = esc_sql($customer_lng);
        $this->db->show_errors();
        $this->db->insert(
            "{$this->db->prefix}moo_order",
            array(
                'uuid' => $uuid,
                'taxAmount' => $tax,
                'amount' => $total,
                'paid' => 0,
                'refpayment' => null,
                'ordertype' => $ordertype,
                'p_name' => $name,
                'p_address' => $address,
                'p_city' => $city,
                'p_state' => $state,
                'p_country' => $country,
                'p_zipcode' => $zipcode,
                'p_phone' => $phone,
                'p_email' => $email,
                'p_lat' => $customer_lat,
                'p_lng' => $customer_lng,
                'shippingfee' => $shippingFee,
                'deliveryfee' => $deliveryFee,
                'tipAmount' => $tipAmount,
                'instructions' => $instructions,
            ));
        return $this->db->insert_id;
    }
    function addLinesOrder($order,$items)
    {
        $order    = esc_sql($order);
        foreach ($items as $uuid=>$item) {
            $string = "";
            if(count($item['modifiers'])) foreach ($item['modifiers'] as $key=>$mod) $string .=$key.",";

            $item_id        = esc_sql($item['item']->uuid);
            $quantity       = esc_sql($item['quantity']);
            $special_ins    = esc_sql($item['special_ins']);
            $string         = esc_sql($string);

            $this->db->insert(
                "{$this->db->prefix}moo_item_order",
                array(
                    'item_uuid' => $item_id,
                    'order_uuid' => $order,
                    'quantity' => $quantity,
                    'modifiers' => $string,
                    'special_ins' => $special_ins,
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                ) );
        }
       return true;
    }
    function updateOrder($uuid,$ref)
    {
        $uuid      = esc_sql($uuid);
        $ref       = esc_sql($ref);
        return $this->db->update(
                        "{$this->db->prefix}moo_order",
                        array(
                            'paid' => 1,	// string
                            'refpayment' => $ref	// integer (number)
                        ),
                        array( 'uuid' => $uuid )
                    );
    }
    function NbCats()
    {
        return $this->db->get_results("SELECT count(*) as nb FROM {$this->db->prefix}moo_category");
    }

    function NbLabels()
    {
        return $this->db->get_results("SELECT count(*) as nb FROM {$this->db->prefix}moo_tag");
    }

    function NbTaxes()
    {
        return $this->db->get_results("SELECT count(*) as nb FROM {$this->db->prefix}moo_tax_rate");
    }

    function NbProducts()
    {
        return $this->db->get_results("SELECT count(*) as nb FROM {$this->db->prefix}moo_item");
    }

    /*
     * Manage Item's image
     */
    function getItemWithImage($uuid)
    {
        $uuid = esc_sql($uuid);
        return $this->db->get_results("SELECT *
                                    FROM {$this->db->prefix}moo_item items
                                    LEFT JOIN {$this->db->prefix}moo_images images
                                    ON items.uuid=images.item_uuid
                                    WHERE items.uuid = '{$uuid}'
                                    ");
    }
    function getItemImages($uuid)
    {
        $uuid = esc_sql($uuid);
        return $this->db->get_results("SELECT *
                                    FROM {$this->db->prefix}moo_images images
                                    WHERE images.item_uuid = '{$uuid}'
                                    ");
    }
    function saveItemWithImage($uuid,$description,$images)
    {
      //  $this->db->show_erros();
        $compteur = 0;
        // Update the description of the item
        if($description != "")
        {
            $this->db->update("{$this->db->prefix}moo_item",
                array(
                    'description' => $description
                ),
                array( 'uuid' => $uuid ));
        }
        $this->db->query('START TRANSACTION');

        $this->db->query("DELETE FROM {$this->db->prefix}moo_images  WHERE item_uuid = '{$uuid}'");

        foreach ($images as $image) {
            $this->db->insert(
                "{$this->db->prefix}moo_images",
                array(
                    'is_default' => 0,
                    'is_enabled' => 1,
                    'item_uuid' => $uuid,
                    'url' => $image
                ));
            if($this->db->insert_id) $compteur++;
        }
       if($compteur == count($images))
       {
           $this->db->query('COMMIT');
           return true;
       }
       else {
           $this->db->query('ROLLBACK');
           return false;
       }
    }


}