<?php

/**
 * This class defines all code necessary to for shortcodes
 *
 * @since      1.0.0
 * @package    Moo_OnlineOrders
 * @subpackage Moo_OnlineOrders/includes
 * @author     Mohammed EL BANYAOUI <elbanyaoui@hotmail.com>
 */
class Moo_OnlineOrders_Shortcodes {

    /**
     * This ShortCode display the store using the second style
     *
     * @since    1.0.0
     */
    public static function AllItems($atts, $content)
    {
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-Model.php";
        $model = new moo_OnlineOrders_Model();
        wp_enqueue_script( 'custom-script-items' );
        wp_enqueue_style ( 'custom-style-items' );

       ob_start();
        if(isset($_GET['category'])){
            $category = esc_sql($_GET['category']);
            ?>
<div class="row  moo_items" id="Moo_FileterContainer" xmlns="http://www.w3.org/1999/html">
                <div class="col-md-3 col-sm-3 col-xs-5 ">
                    <!-- <label for="ListCats">Categories :</label> -->

                    <select id="ListCats" class="form-control" onchange="Moo_CategoryChanged(this)">
                        <?php
                        foreach ( $model->getCategories() as $cat ){
                            if(strlen($cat->items)<1 || $cat->show_by_default == 0 ) continue;
                            if($cat->uuid == $category)
                                echo '<option value="'.$cat->uuid.'" selected>'.$cat->name.'</option>';
                            else
                                echo '<option value="'.$cat->uuid.'">'.$cat->name.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-7 ">
                    <!-- <label for="MooSearch">Search :</label> -->
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for..." id="MooSearchFor" onkeypress="Moo_ClickOnGo(event)">
                              <span class="input-group-btn">
                                <button id = "MooSearchButton" class="btn btn-default" type="button" onclick="Moo_Search(event)">Go!</button>
                              </span>
                    </div><!-- /input-group -->

                </div>
                <div class="col-md-3 hidden-xs col-sm-3">
                    <!-- <label for="ListCats">Sort by :</label> -->
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Sort by<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" onclick="Moo_SortBy(event,'name','asc')">Name - A to Z</a></li>
                                <li><a href="#" onclick="Moo_SortBy(event,'name','desc')">Name - Z to A</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="#" onclick="Moo_SortBy(event,'price','asc')">Price - Low to high</a></li>
                                <li><a href="#" onclick="Moo_SortBy(event,'price','desc')">Price - High to low</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>

            </div>
            <?php
            echo '<div class="row moo_items" id="Moo_ItemContainer">';

            echo self::getItemsHtml($category,'name','asc',null);


            echo '</div>';
            echo '<div class="row moo_items" align="center"><button class="moo-btn moo-btn-primary" onclick="javascript:window.history.back();">Back to Main menu</button></div>';
        }
        else
            if(isset($_GET['item'])){
                $item_uuid = esc_sql($_GET['item']);
                $modifiersgroup = $model->getModifiersGroup($item_uuid);
                ?>
                <div id="moo_modifiers">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist" style="margin: 0px;border-bottom-left-radius: 4px;border-bottom-right-radius: 4px;">
                        <?php
                        $flag=true;
                        foreach ($modifiersgroup as $mg) {
                            if($flag)
                                echo '<li role="presentation" class="active"><a href="#tab_'.$mg->uuid.'" aria-controls="home" role="tab" data-toggle="tab">'.$mg->name.'</a></li>';
                            else
                                echo '<li role="presentation"><a href="#tab_'.$mg->uuid.'" aria-controls="home" role="tab" data-toggle="tab">'.$mg->name.'</a></li>';
                            $flag = false;
                        }
                        ?>
                    </ul>
                    <div class="panel panel-default" style="border-top: 0px">
                        <div class="panel-body">
                            <!-- Tab panes -->
                            <form id="moo_form_modifiers" method="post">
                                <div class="tab-content">
                                    <?php
                                    $flag=true;
                                    foreach ($modifiersgroup as $mg) {
                                        if($flag)
                                            echo '<div role="tabpanel" class="tab-pane active" id="tab_'.$mg->uuid.'">';
                                        else
                                            echo '<div role="tabpanel" class="tab-pane" id="tab_'.$mg->uuid.'">';
                                        $flag = false;
                                        ?>

                                        <div  class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th style="width: 50px;text-align: center;">Select</th>
                                                    <th>Name</th>
                                                    <th>Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($model->getModifiers($mg->uuid) as $modifier) {
                                                    echo '<tr>';
                                                    echo '<td style="width: 50px;text-align: center;"><input type="checkbox" name="moo_modifiers[\''.$item_uuid.'\',\''.$mg->uuid.'\',\''.$modifier->uuid.'\']" style="width: 25px;height: 25px;"/></td>';
                                                    echo '<td>'.$modifier->name.'</td>';
                                                    echo '<td>$'.($modifier->price/100).'</td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                </tbody>
                                            </table>

                                        </div>

                                        <?php
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="btn btn-primary btn-lg" onclick="moo_addModifiers()">ADD TO YOUR CART</div>
                </div>

            <?php
            }
            else
            {
                ?>
                <div class="row moo_categories">
                    <?php
                    $colors = self::GetColors();
                    $categories = $model->getCategories();
                    $items = $model->getItems();
                    if(count($categories)==0 && count($items)==0 )
                        echo "<h1>You don't have any Items, please import your inventory from Clover</h1>";
                    else
                    {
                        if(get_option("moo-show-allItems") == 'true')
                        {
                            array_push($categories,(object)array("name"=>'All Items',"uuid"=>'NoCategory'));
                        }
                   if(count($categories)>0)
                        foreach ( $categories as $category ){
                            if($category->uuid=='NoCategory')
                            {
                                $category_name='All Items';
                            }
                            else
                            {
                                if(strlen($category->items)<1 || $category->show_by_default == 0 ) continue;
                                if(strlen ($category->name)> 14)$category_name = substr($category->name, 0, 14)."...";
                                else  $category_name = $category->name;
                            }
                          

                            echo '<div class="col-md-4 col-sm-6 col-xs-12 moo_category_flip" >';
                            echo "<a href='".(esc_url( add_query_arg( 'category', $category->uuid) ))."'><div class='moo_category_flip_container'>";
                            echo "<div class='moo_category_flip_title'>".ucfirst(strtolower($category_name))."</div>";
                            echo "<div class='moo_category_flip_content' style='background-color: ".current($colors)."'></div>";
                            echo '</div></a>';
                            echo '</div>';
                            if(!next($colors)) reset($colors);
                        }
                    else
                    {
                        ?>
                        <div class="row  moo_items" id="Moo_FileterContainer">
                            <div class="col-md-9 col-sm-9 col-xs-7 ">
                                <!-- <label for="MooSearch">Search :</label> -->
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search for..." id="MooSearchFor" onkeypress="Moo_ClickOnGo(event)">
                              <span class="input-group-btn">
                                <button id = "MooSearchButton" class="btn btn-default" type="button" onclick="Moo_Search(event)">Go!</button>
                              </span>
                                </div><!-- /input-group -->

                            </div>
                            <div class="col-md-3 col-xs-5 col-sm-3">
                                <!-- <label for="ListCats">Sort by :</label> -->
                                <ul class="nav navbar-nav">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Sort by<span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onclick="Moo_SortBy(event,'name','asc')">Name - A to Z</a></li>
                                            <li><a href="#" onclick="Moo_SortBy(event,'name','desc')">Name - Z to A</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="#" onclick="Moo_SortBy(event,'price','asc')">Price - Low to high</a></li>
                                            <li><a href="#" onclick="Moo_SortBy(event,'price','desc')">Price - High to low</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>

                        </div>
                        <?php

                        echo '<div class="row moo_items" id="Moo_ItemContainer">';
                        echo self::getItemsHtml('NoCategory','name','asc',null);
                        echo '</div>';
                    }


            }
                    ?>
                </div>
            <?php

            }
        return ob_get_clean();

    }
    /**
     * This ShortCode display the store using the first style
     * @since    1.0.0
     */
    public static function AllItemsAcordion($atts, $content)
    {
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-Model.php";
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-CallAPI.php";

        $model = new moo_OnlineOrders_Model();
        $api   = new moo_OnlineOrders_CallAPI();
        //wp_enqueue_style ( 'bootstrap-css' );
        wp_enqueue_style ( 'font-awesome' );
        wp_enqueue_style ( 'custom-style-accordion' );
        wp_enqueue_style ( 'simple-modal' );
        wp_enqueue_script( 'custom-script-accordion');
        wp_enqueue_script( 'jquery-accordion',array( 'jquery' ));
        wp_enqueue_script( 'simple-modal',array( 'jquery' ));

        $MooOptions = (array)get_option('moo_settings');

        ob_start();
    ?>
        <a href="#ViewShoppingCart">
            <div class="moo-col-xs-12 moo-col-sm-12 moo-hidden-lg moo-hidden-md MooGoToCart">
                VIEW SHOPPING CART
            </div>
         </a>
         <div class="moo-row MooStyleAccorfion">
            <div class="moo-col-md-7" style="margin-bottom: 20px;">
                <?php
                    $categories = $model->getCategories();
                    $all_items  = $model->getItems();
                    $track_stock = $api->getTrackingStockStatus();

                    if($track_stock == true)
                    {
                        $itemStocks = $api->getItemStocks();
                    }
                    else
                    {
                        $itemStocks = false;
                    }


                    if(count($categories)==0 && count($all_items) == 0 )
                        echo "<h1>You don't have any Items, please import your inventory from Clover</h1>";
                    else
                        if(count($categories) == 0)
                        {
                            $categories = array((object)array(
                                "name"=>'All Items',
                                "uuid"=>'NoCategory'
                            ));
                        }
                    /*
                     *  this line to add the category all items your menu
                     */

                    if(get_option("moo-show-allItems") == 'true')
                    {
                        array_push($categories,(object)array("name"=>'All Items',"uuid"=>'NoCategory'));
                    }
                    foreach ($categories as $category ){
                        if(isset($atts['category']) && $atts['category']!="")
                        {
                            if($category->uuid != $atts['category'] ) continue;
                        }
                        else
                            if(isset($_GET['category']) && $_GET['category']!="")
                            {
                                if($category->uuid != $_GET['category'] ) continue;
                            }

                        if($category->uuid == 'NoCategory')
                        {
                            $category_name = $category->name;
                        }
                        else
                        {
                            $category_name = $category->name;
                            if(isset($category->alternate_name) && $category->alternate_name != "")
                                $category_name = $category->alternate_name;

                            if(strlen ($category->items)< 1 || $category->show_by_default == 0) continue;
                        }

                ?>

                        <div class="moo_category">
                            <div class="moo_accordion" id="MooCat_<?php if(isset($atts['category']) && $atts['category']!="")  echo 'NoCategory'; else echo $category->uuid;?>">
                                <div class="moo_category_title">
                                    <div class="moo_title"><?php echo $category_name?></div>
                                    <span></span>
                                </div>
                            </div>
                            <div class="moo_accordion_content">
                                <ul>
                                    <?php
                                        if($category->uuid == 'NoCategory')
                                            $items = $all_items;
                                        else
                                            $items = explode(',',$category->items);

                                    $tab_items = array();
                                    foreach($items as $uuid_item_or_item)
                                    {
                                        if($uuid_item_or_item == "") continue;

                                        if($category->uuid == 'NoCategory')
                                            $item = $uuid_item_or_item;
                                        else
                                            $item = $model->getItem($uuid_item_or_item);

                                        $tab_items[$item->uuid] = $item;
                                    }

                                    usort($tab_items, array('Moo_OnlineOrders_Shortcodes','moo_sort_items'));

                                    foreach($tab_items as $item)
                                    {
                                        if($item)
                                        {
                                            if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE') continue;

                                            if($track_stock)
                                                $itemStock = self::getItemStock($itemStocks,$item->uuid);
                                            else
                                                $itemStock = false;


                                            if($item->outofstock== 1 || ($track_stock==true && $itemStock!=false && isset($itemStock->stockCount)  && $itemStock->stockCount<1))
                                            {
                                                echo '<li>';
                                                echo '<a href="#" onclick="event.preventDefault()">';
                                                echo '  <div class="moo_detail">'.$item->name.' (Out of stock) </div>';
                                                echo '  <div class="moo_price">'.(($item->price>0)?'$'.(number_format(($item->price/100),2,'.','')):'');
                                                if($item->price_type == "PER_UNIT")
                                                {
                                                    echo " /".$item->unit_name;
                                                }
                                                echo '</div>';
                                                echo '</a>';
                                                if(isset($item->description) && $item->description!="")
                                                    echo "<p style='width: 85%;'>".stripslashes ($item->description)."</p>";
                                                echo '</li>';
                                            }
                                            else
                                            {
                                                echo '<li>';
                                                if(($model->itemHasModifiers($item->uuid)->total) != "0")
                                                    echo '<a class="popup-text" href="#Modifiers_for_'.$item->uuid.'" >';
                                                else
                                                    echo '<a href="#" onclick="moo_addToCart(event,\''.$item->uuid.'\',\''.esc_sql($item->name).'\',\''.$item->price.'\')">';

                                                echo '  <div class="moo_detail">'.$item->name;
                                                echo '</div>';
                                                echo '  <div class="moo_price">'.(($item->price>0)?'$'.(number_format(($item->price/100),2,'.','')):'');

                                                if($item->price_type == "PER_UNIT")
                                                {
                                                    echo " /".$item->unit_name;
                                                }
                                                echo '</div>';

                                                echo '</a>';
                                                if(isset($item->description) && $item->description!="")
                                                    echo "<p style='width: 85%;'>".stripslashes ($item->description)."</p>";
                                                echo '</li>';
                                                if(($model->itemHasModifiers($item->uuid)->total) != "0")
                                                {
                                                    echo '<div class="row white-popup mfp-hide" id="Modifiers_for_'.$item->uuid.'">';
                                                    echo ' <div class="col-md-12 col-sm-12 col-xs-12">';
                                                    echo ' <form id="moo_form_modifiers" method="post">';
                                                    $modifiersgroup = $model->getModifiersGroup($item->uuid);
                                                    $nb_mg=0;
                                                    foreach ($modifiersgroup as $mg) {
                                                        $modifiers = $model->getModifiers($mg->uuid);
                                                        if( count($modifiers) == 0) continue;
                                                        $nb_mg++;
                                                        if($mg->min_required==1 && $mg->max_allowd==1)
                                                        {
                                                            ?>
                                                            <div class="moo_category_title">
                                                                <div class="moo_title"><?php echo ($mg->alternate_name=="")?$mg->name:$mg->alternate_name;?></div>
                                                            </div>
                                                            <div style="padding-right: 50px;padding-left: 50px">
                                                                <select name="<?php echo 'moo_modifiers[\''.$item->uuid.'\',\''.$mg->uuid.'\']' ?>" class="moo-form-control">
                                                                    <?php  foreach ( $modifiers as $m) {
                                                                        if($m->price>0)
                                                                            echo '<option value="'.$m->uuid.'">'. (($m->alternate_name=="")?$m->name:$m->alternate_name).' ($'.number_format(($m->price/100), 2).')</option>';
                                                                        else
                                                                            echo '<option value="'.$m->uuid.'">'. (($m->alternate_name=="")?$m->name:$m->alternate_name).'</option>';

                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <div class="moo_category">
                                                                <div class="moo_accordion accordion-open" id="<?php echo ($nb_mg == 1)?'MooModifierGroup_default_'.$item->uuid:'MooModifierGroup_'.$mg->uuid?>">
                                                                    <div class="moo_category_title">
                                                                        <div class="moo_title"><?php echo ($mg->alternate_name=="")?$mg->name:$mg->alternate_name; echo ($mg->min_required>=1)?' (Required)':''; ?></div>
                                                                        <span></span>
                                                                    </div>
                                                                </div>
                                                                <div class="moo_accordion_content moo_modifier-box2" style="display: none;">
                                                                    <ul  class="MooModifierGroup_<?php echo $mg->uuid ?>">
                                                                        <?php  foreach ( $modifiers as $m) {
                                                                           // echo '<li onclick="moo_check(event,\''.$m->uuid.'\',\''.$item->uuid.'\',\''.$mg->uuid.'\',\''.$mg->max_allowd .'\')">';
                                                                            echo '<li>';
                                                                            ?>
                                                                            <a href="#" onclick="moo_check(event,'<?php echo $m->uuid ?>','<?php echo $item->uuid ?>','<?php echo $mg->uuid ?>','<?php echo $mg->max_allowd ?>',false)">
                                                                                <div class="detail" >
                                                                                    <span class="moo_checkbox" >
                                                                                      <input type="checkbox"  onclick="moo_check(event,'<?php echo $m->uuid ?>','<?php echo $item->uuid ?>','<?php echo $mg->uuid ?>','<?php echo $mg->max_allowd ?>',true)" name="<?php echo 'moo_modifiers[\''.$item->uuid.'\',\''.$mg->uuid.'\',\''.$m->uuid.'\']' ?>" id="moo_checkbox_<?php echo $m->uuid ?>" />
                                                                                    </span>
                                                                                    <p class="moo_label"><?php echo ($m->alternate_name=="")?$m->name:$m->alternate_name;?></p>
                                                                                </div>
                                                                                <div class="moo_price">
                                                                                    <?php echo ($m->price>0)?'$'.number_format(($m->price/100), 2):'' ?>
                                                                                </div>
                                                                            </a>
                                                                            <?php
                                                                            echo '</li>';
                                                                        }
                                                                        if($mg->min_required != null || $mg->max_allowd != null ){
                                                                            echo '<li class="Moo_modifiergroupMessage">';
                                                                            if(($mg->min_required == $mg->max_allowd)&& $mg->max_allowd>0)
                                                                                echo' Select '.$mg->max_allowd;
                                                                            else
                                                                            {
                                                                                if($mg->min_required != null && $mg->min_required != 0 ) echo "Must choose at least <br/> ".$mg->min_required;
                                                                                if($mg->max_allowd != null && $mg->max_allowd != 0 ) echo "Select up to  ".$mg->max_allowd;
                                                                            }

                                                                            echo '</li>';
                                                                        }
                                                                        ?>

                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <div style='text-align: center;margin-top: 10px;'>
                                                        <?php echo '<div class="moo-btn moo-btn-danger" onclick="moo_addItemWithModifiersToCart(event,\''.$item->uuid.'\',\''.preg_replace('/[^A-Za-z0-9 \-]/', '', $item->name).'\',\''.$item->price.'\')"  >ADD TO YOUR CART</div>'; ?>
                                                        <div class="moo-btn moo-btn-info" onclick="javascript:jQuery.magnificPopup.close()">Close</div>
                                                    </div>
                                                    <?php
                                                    echo '</form>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                            }
                                        }
                                    }
                                                ?>
                                </ul>
                            </div>
                        </div>
            <?php
                    }

                        $checkout_page_id = $MooOptions['checkout_page'];
                        $checkout_page_url =  get_page_link($checkout_page_id);
                ?>

            </div>
            <div class="moo-col-md-5" id="ViewShoppingCart">
                                <div class="moo_cart">
                                  <div class="CartContent">
                                  </div>
                                    <div style="text-align: center">
                                        <a href="<?php echo esc_url($checkout_page_url);?>" class="moo-btn moo-btn-danger BtnCheckout">CHECKOUT</a>
                                    </div>
                                </div>

                            </div>
         </div>
    <?php
        return ob_get_clean();
    }
    /*
     * It's a private function for internal use in the function
     *  public static function AllItems($atts, $content)
     * This function return a list of colors that we use in Style 2
     */
    private static function GetColors()
    {
        return array(
            0=>"#1abc9c",1=>"#33B5E5",2=>"#676fb4",3=>"#1e5429",4=>"#c5a22d",5=>"#000088",6=>"#b75555",7=>"#666666",8=>"#0099CC",
            9=>"#34428c",10=>"#0f726f",11=>"#c75827",12=>"#e67e22"
        );
    }

    /*
     * This function for getting items from the database based on filters
     * Used in AJAX responses of the style 2
     * @param $category : The category of itemes
     * @param $filterBy : The predicate of filters PRICE or NAME
     * @param $orderBy  : The order
     * @param $search   : a string if we want search an item
     * @return List of ITEMS (HTML)
     * @since 1.0.0
     */
    public static function getItemsHtml($category,$filterBy,$orderBy,$search) {
        //This function deleted in version 1.2.5 because it was used with the ols interface
    }
    /*
     * The checkoutPage shortcode implemnation
     */
    public static function checkoutPage($atts, $content)
    {
        $MooOptions = (array)get_option('moo_settings');

        //wp_enqueue_style( 'bootstrap-css' );
        wp_enqueue_style( 'font-awesome' );
        wp_enqueue_style( 'custom-style-cart3');
        wp_enqueue_script( 'moo-google-map' );

        if($MooOptions["save_cards"] == "enabled")
            wp_enqueue_script( 'moo-spreedly' );

        wp_enqueue_script( 'display-merchant-map','moo-google-map' );
        wp_enqueue_script( 'custom-script-checkout','display-merchant-map' );
        wp_enqueue_script( 'forge' );


        ob_start();
        $model = new moo_OnlineOrders_Model();
        $api   = new moo_OnlineOrders_CallAPI();


        $orderTypes = $model->getVisibleOrderTypes();

        if($MooOptions['scp'] == "on")
        {
            $key = array();
        }
        else
        {
            $key = $api->getPayKey();
            $key = json_decode($key);
            if($key == NULL)
            {
                echo '<div id="moo_checkout_msg">This store cannot accept orders, if you are the owner please verify your API Key</div>';
                return ob_get_clean();
            }
        }

        $custom_css = $MooOptions["custom_css"];
        $custom_js  = $MooOptions["custom_js"];

        $total  =   Moo_OnlineOrders_Public::moo_cart_getTotal(true);

        $merchant_proprites = (json_decode($api->getMerchantProprietes())) ;

        //Coupons
        if(isset($_SESSION['coupon']) && !empty($_SESSION['coupon']))
        {
            $coupon = $_SESSION['coupon'];
            if($coupon['minAmount']>$total['sub_total'])
                $coupon = null;
        }
        else
            $coupon = null;


        //Include spreedly



        //Include custom css
        if($custom_css != null)
           echo '<style type="text/css">'.$custom_css.'</style>';

        if($total === false || !isset($total['nb_items']) || $total['nb_items'] < 1){

           echo '<div class="moo_emptycart"><p>Your cart is empty</p><span><a class="moo-btn moo-btn-default" href="'.get_page_link($MooOptions['store_page']).'" style="margin-top: 30px;">Back to Main Menu</a></span></div>';
           return ob_get_clean();
        };

        if($MooOptions["order_later"] == "on")
        {
            $inserted_nb_days = intval($MooOptions["order_later_days"]);
            $inserted_nb_mins = intval($MooOptions["order_later_minutes"]);

            $inserted_nb_days_d = intval($MooOptions["order_later_days_delivery"]);
            $inserted_nb_mins_d = intval($MooOptions["order_later_minutes_delivery"]);

            if($MooOptions["order_later_days"] === "")
            {
                $nb_days = 4;
            }
            else
            {
                if($inserted_nb_days === 0)
                    $nb_days = 1;
                else
                    $nb_days = ($inserted_nb_days>0)?$inserted_nb_days:4;
            }

            if($inserted_nb_mins != "")
            {
                $nb_minutes = ($inserted_nb_mins>0)?$inserted_nb_mins:20;
            }
            else
                $nb_minutes = 20;

            if($MooOptions["order_later_days_delivery"] === "")
            {
                $nb_days_d = 4;
            }
            else
            {
                if($inserted_nb_days_d == 0)
                    $nb_days_d = 1;
                else
                    $nb_days_d = ($inserted_nb_days_d>0)?$inserted_nb_days_d:4;
            }

            if($inserted_nb_mins_d != "")
            {
                $nb_minutes_d = ($inserted_nb_mins_d>0)?$inserted_nb_mins_d:60;
            }
            else
                $nb_minutes_d = 60;

        }
        else
        {
            $nb_days = 0;
            $nb_minutes = 0;
            $nb_days_d = 0;
            $nb_minutes_d = 0;
        }


        $oppening_status = json_decode($api->getOpeningStatus($nb_days,$nb_minutes));

        if($nb_days != $nb_days_d || $nb_minutes != $nb_minutes_d)
            $oppening_status_d = json_decode($api->getOpeningStatus($nb_days_d,$nb_minutes_d));
        else
            $oppening_status_d = $oppening_status;

        $oppening_msg = "";

        if($MooOptions['hours'] != 'all' && $oppening_status->status == 'close')
        {
            if($oppening_status->store_time == '')
                    $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg">Online Ordering Currently Closed'.(($MooOptions['accept_orders_w_closed'] == 'on' )?"<br/><p style='color: green'>Order in Advance Available</p>":"").'</div>';
            else
                    $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg"><strong>Today\'s Online Ordering hours</strong> <br/> '.$oppening_status->store_time.'<br/>Online Ordering Currently Closed'.(($MooOptions['accept_orders_w_closed'] == 'on' )?"<br/><p style='color: green'>Order in Advance Available</p>":"").'</div>';
        }
        
        //Adding asap to pickup time
        if(isset($oppening_status->pickup_time))
        {
            if(isset($MooOptions['order_later_asap_for_p']) && $MooOptions['order_later_asap_for_p'] == 'on')
            {
                if(isset($oppening_status->pickup_time->Today))
                    array_unshift($oppening_status->pickup_time->Today,'ASAP');
            }
            if(isset($oppening_status->pickup_time->Today))
                array_unshift($oppening_status->pickup_time->Today,'Select a time');

        }
        if(isset($oppening_status_d->pickup_time))
        {
            if(isset($MooOptions['order_later_asap_for_d']) && $MooOptions['order_later_asap_for_d'] == 'on')
            {
                if(isset($oppening_status_d->pickup_time->Today))
                    array_unshift($oppening_status_d->pickup_time->Today,'ASAP');
            }
            if(isset($oppening_status_d->pickup_time->Today))
                array_unshift($oppening_status_d->pickup_time->Today,'Select a time');

        }

        if($MooOptions['hours'] != 'all' && $MooOptions['accept_orders_w_closed'] != 'on' && $oppening_msg != "")
        {
            echo '<div id="moo_OnlineStoreContainer">'.$oppening_msg.'</div>';
            return ob_get_clean();
        }

        $merchant_address =  $api->getMerchantAddress();
        $store_page_id     = $MooOptions['store_page'];
        $cart_page_id     = $MooOptions['cart_page'];
        $checkout_page_id     = $MooOptions['checkout_page'];
        $store_page_url    =  get_page_link($store_page_id);
        $cart_page_url    =  get_page_link($cart_page_id);
        $checkout_page_url    =  get_page_link($checkout_page_id);


        wp_localize_script("custom-script-checkout", "moo_OrderTypes",$orderTypes);
        wp_localize_script("custom-script-checkout", "moo_Total",$total);
        wp_localize_script("custom-script-checkout", "moo_Key",(array)$key);
        wp_localize_script("custom-script-checkout", "moo_thanks_page",$MooOptions['thanks_page']);
        wp_localize_script("custom-script-checkout", "moo_cash_upon_delivery",$MooOptions['payment_cash_delivery']);
        wp_localize_script("custom-script-checkout", "moo_cash_in_store",$MooOptions['payment_cash']);
        wp_localize_script("custom-script-checkout", "moo_pickup_time",$oppening_status->pickup_time);
        wp_localize_script("custom-script-checkout", "moo_pickup_time_for_delivery",$oppening_status_d->pickup_time);
        wp_localize_script("display-merchant-map", "moo_merchantLat",$MooOptions['lat']);
        wp_localize_script("display-merchant-map", "moo_merchantLng",$MooOptions['lng']);
        wp_localize_script("display-merchant-map", "moo_merchantAddress",$merchant_address);
        wp_localize_script("display-merchant-map", "moo_delivery_zones",$MooOptions['zones_json']);
        wp_localize_script("display-merchant-map", "moo_delivery_other_zone_fee",$MooOptions['other_zones_delivery']);
        wp_localize_script("display-merchant-map", "moo_delivery_free_amount",$MooOptions['free_delivery']);
        wp_localize_script("display-merchant-map", "moo_delivery_fixed_amount",$MooOptions['fixed_delivery']);
        wp_localize_script("display-merchant-map", "moo_fb_app_id",$MooOptions['fb_appid']);
        wp_localize_script("display-merchant-map", "moo_scp",$MooOptions['scp']);
        wp_localize_script("display-merchant-map", "moo_checkout_login",$MooOptions['checkout_login']);
        wp_localize_script("display-merchant-map", "moo_save_cards",$MooOptions['save_cards']);
        wp_localize_script("display-merchant-map", "moo_save_cards_fees",$MooOptions['save_cards_fees']);

        if((isset($_GET['logout']) && $_GET['logout']==true))
        {
            unset($_SESSION['moo_customer_token']);
            wp_redirect ( $checkout_page_url );
        }
        if($MooOptions['checkout_login']=="disabled")
        {
            unset($_SESSION['moo_customer_token']);
        }
        ?>

        <div id="moo_OnlineStoreContainer">
        <?php echo $oppening_msg?>
        <div id="moo_merchantmap">
        </div>
        <div class="moo-row" id="moo-checkout">
                <!--            login               -->
                <div id="moo-login-form" <?php if((isset($_SESSION['moo_customer_token']) && $_SESSION['moo_customer_token']!="") || $MooOptions['checkout_login']=="disabled") echo 'style="display:none;"'?> class="moo-col-md-12 ">
                    <div class="moo-row login-top-section">
                        <div class="login-header">
                            Why create a  <a href="http://www.smartonlineorder.com" target="_blank" alt="Online ordering for Clover POS">Smart Online Order</a> account?
                        </div>
                        <div class="moo-col-md-6">
                            <ul>
                                <li>Save your address</li>
                                <li>Faster Checkout!</li>
                            </ul>
                        </div>
                        <div class="moo-col-md-6">
                            <ul>
                                <li>View your past orders (coming soon)</li>
                                <li>Get exclusive deals and coupons</li>
                            </ul>
                        </div>
                    </div>
                    <div class="moo-col-md-6">
                        <div class="moo-row login-social-section" >
                            <?php if(isset($MooOptions['fb_appid']) && $MooOptions['fb_appid']!=""){ ?>
                                <p>
                                    <strong>Sign in</strong> with your social account
                                    <br />
                                    <small>No posts on your behalf, promise!</small>
                                </p>
                                <div class="moo-row">
                                    <div class="moo-col-xs-8 moo-col-sm-6 moo-col-md-6 moo-col-md-offset-3 moo-col-sm-offset-3 moo-col-xs-offset-2">
                                        <a href="#" class="moo-btn moo-btn-lg moo-btn-primary moo-btn-block" onclick="moo_loginViaFacebook(event)" style="margin-top: 12px;">Facebook</a>
                                    </div>
                                    <div class="moo-col-xs-12 moo-col-sm-6 moo-col-md-6 moo-col-md-offset-3">
                                        <div class="login-or">
                                            <hr class="hr-or">
                                            <span class="span-or">or</span>
                                        </div>
                                        <a  class="moo-btn moo-btn-danger" onclick="moo_loginAsguest(event)">
                                            Login As Guest
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
                                    <div class="moo-col-xs-8 moo-col-sm-6 moo-col-md-6 moo-col-md-offset-3 moo-col-sm-offset-3 moo-col-xs-offset-2">
                                        <a href="#" class="moo-btn moo-btn-lg moo-btn-primary moo-btn-block" onclick="moo_loginAsguest(event)" style="margin-top: 12px;"> Login As Guest</a>
                                    </div>
                                    <div class="moo-col-xs-12 moo-col-sm-6 moo-col-md-8 moo-col-md-offset-2">
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
                    <div class="moo-col-md-6">
                        <div>
                            <div class="form-group">
                                <label for="inputEmail">Email</label>
                                <input type="text" id="inputEmail" class="moo-form-control">
                            </div>
                            <div class="moo-form-group">
                                <a class="pull-right" href="#" onclick="moo_show_forgotpasswordform(event)">Forgot password?</a>
                                <label for="inputPassword">Password</label>
                                <input type="password"  id="inputPassword" class="moo-form-control">
                            </div>
                            <a id="mooButonLogin" class="moo-btn" onclick="moo_login(event)">
                                Log In
                            </a>
                            <p style="padding: 10px;"> Don't have an account<a  href="#" onclick="moo_show_sigupform(event)"> Sign-up</a> </p>
                        </div>
                    </div>
                </div>
                <!--            Register            -->
                <div id="moo-signing-form" class="moo-col-md-12">
                    <div class="moo-col-md-8 moo-col-md-offset-2">
                            <div class="moo-form-group">
                                <label for="inputMooFullName">Full Name</label>
                                <input type="text" class="moo-form-control" id="inputMooFullName">
                            </div>

                            <div class="moo-form-group">
                                <label for="inputMooEmail">Email</label>
                                <input type="text" class="moo-form-control" id="inputMooEmail">
                            </div>
                            <div class="moo-form-group">
                                <label for="inputMooPhone">Phone</label>
                                <input type="text" class="moo-form-control" id="inputMooPhone" >
                            </div>
                            <div class="moo-form-group">
                                <label for="inputMooPassword">Password</label>
                                <input type="password" class="moo-form-control" id="inputMooPassword">
                            </div>
                            <p>
                                By clicking the button below you agree to our <a href="http://www.merchantechapps.com/pages/merchantech-eula" target="_blank">Terms Of Service</a>
                            </p>
                            <a class="moo-btn moo-btn-primary" onclick="moo_signin(event)">
                               Submit
                            </a>
                            <p style="padding: 10px;"> Have an account already?<a  href="#" onclick="moo_show_loginform()"> Click here</a> </p>
                    </div>

                </div>
                <!--            Reset Password      -->
                <div   id="moo-forgotpassword-form" class="moo-col-md-12">
                    <div class="moo-col-md-8 moo-col-md-offset-2">
                            <div class="moo-form-group">
                                <label for="inputEmail4Reset">Email</label>
                                <input type="text" class="moo-form-control" id="inputEmail4Reset">
                            </div>
                            <a class="moo-btn moo-btn-primary" onclick="moo_resetpassword(event)">
                                Reset
                            </a>
                            <a class="moo-btn moo-btn-default" onclick="moo_show_loginform()">
                                Cancel
                            </a>
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
                    <a href="?logout=true">Logout</a>
                </div>
                <!--            Add new address      -->
                <div id="moo-addaddress-form" class="moo-col-md-12">
                    <h1>Add new Address to your account</h1>
                    <div class="moo-col-md-8 moo-col-md-offset-2">
                        <div class="moo-form-group">
                            <label for="inputMooAddress">Address</label>
                            <input type="text" class="moo-form-control" id="inputMooAddress">
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
                            <a href="#" class="moo-btn moo-btn-warning" onclick="moo_ConfirmAddressOnMap(event)">Next</a>
                        </p>
                        <div id="MooMapAddingAddress">
                            <p style="margin-top: 150px;">Loading the MAP...</p>
                        </div>
                        <input type="hidden" class="moo-form-control" id="inputMooLat">
                        <input type="hidden" class="moo-form-control" id="inputMooLng">
                        <div class="form-group">
                             <a id="mooButonAddAddress" onclick="moo_addAddress(event)">
                            Confirm and add address
                        </a>
                        </div>

                        <p style="padding: 10px;">If you want to skip this step and add your address later <a  href="#" onclick="moo_pickup_the_order(event)" style="color:blue"> Click here</a> </p>
                    </div>
                </div>
                <!--            Checkout form        -->
                <div id="moo-checkout-form" class="moo-col-md-12" <?php if($MooOptions['checkout_login']=="disabled") echo 'style="display:block;"'?>>
                    <!--            Checkout form - Informaton section       -->
                    <div class="moo-col-md-7 moo-checkout-form-leftside">
                        <div id="moo-checkout-form-customer">
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
                                            <label for="name" class="moo-checkoutText-fullName">Full Name:</label>
                                            <input class="moo-form-control" name="name" id="MooContactName">
                                        </div>
                                    </div>
                                    <div class="moo-row">
                                        <div class="moo-form-group">
                                            <label for="MooContactEmail" class="moo-checkoutText-email">Email</label>
                                            <input class="moo-form-control" id="MooContactEmail">
                                        </div>
                                    </div>
                                    <div class="moo-row">
                                        <div class="moo-form-group">
                                            <label for="MooContactPhone" class="moo-checkoutText-phoneNumber">Phone number:</label>
                                            <input class="moo-form-control" name="phone" id="MooContactPhone" onchange="moo_phone_changed()">
                                        </div>
                                    </div>
                                    <?php wp_nonce_field('moo-checkout-form');?>
                                </div>
                            </div>
                        </div>
                        <div class="moo_chekout_border_bottom"></div>
                        <?php if(count($orderTypes)>0){?>
                        <div id="moo-checkout-form-ordertypes">
                            <div class="moo-checkout-bloc-title moo-checkoutText-orderingMethod">
                                ORDERING METHOD
                            </div>
                            <div class="moo-checkout-bloc-content">
                                <?php
                                foreach ($orderTypes as $ot) {
                                    echo '<div class="moo-checkout-form-ordertypes-option">';
                                    echo '<input class="moo-checkout-form-ordertypes-input" type="radio" name="ordertype" value="'.$ot->ot_uuid.'" id="moo-checkout-form-ordertypes-'.$ot->ot_uuid.'">';
                                    echo '<label for="moo-checkout-form-ordertypes-'.$ot->ot_uuid.'" style="display: inline;margin-left:15px">'.$ot->label.'</label></div>';
                                }
                                ?>
                            </div>
                            <div class="moo-checkout-bloc-message" id="moo-checkout-form-ordertypes-message">
                            </div>
                        </div>
                        <div class="moo_chekout_border_bottom"></div>
                        <?php  } ?>
                        <?php
                        if(isset($MooOptions['order_later']) && $MooOptions['order_later'] == 'on' && count($oppening_status->pickup_time)>0){ ?>
                        <div id="moo-checkout-form-orderdate">
                            <div class="moo-checkout-bloc-title moo-checkoutText-ChooseATime">
                                CHOOSE A TIME
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
                                        <div class="moo-col-md-12">
                                            Today's Online Ordering Hours: <?php echo $oppening_status->store_time  ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="moo_chekout_border_bottom"></div>
        <?php } ?>
                        <div id="moo-checkout-form-payments">
                            <div class="moo-checkout-bloc-title moo-checkoutText-payment" >
                                PAYMENT  <?php if($MooOptions['payment_cash'] == 'on' || $MooOptions['payment_cash_delivery'] == 'on'){ echo 'METHOD';}?>
                            </div>
                            <div class="moo-checkout-bloc-content">
                                <div class="moo-checkout-form-payments-option">
                                    <input class="moo-checkout-form-payments-input" type="radio" name="payments" value="creditcard" id="moo-checkout-form-payments-creditcard">
                                    <label for="moo-checkout-form-payments-creditcard" style="display: inline;margin-left:15px">Pay now with Credit Card</label>
                                </div>
                                <?php if($MooOptions['payment_cash'] == 'on' || $MooOptions['payment_cash_delivery'] == 'on'){ ?>
                                <div class="moo-checkout-form-payments-option">
                                   <input class="moo-checkout-form-payments-input" type="radio" name="payments" value="cash" id="moo-checkout-form-payments-cash">
                                   <label for="moo-checkout-form-payments-cash" style="display: inline;margin-left:15px" id="moo-checkout-form-payincash-label">Pay at Location</label>
                               </div>
                                <?php }
                                ?>
                                <?php if((!isset($MooOptions['scp']) || $MooOptions['scp'] != 'on')){ ?>
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
                                                            <option value="06">June (06)</option>
                                                            <option value="07">July (07)</option>
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
                                                            if($current_year < 2016 )$current_year = 2016;
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
<!--                                            <div class="moo-row">-->
<!--                                                <div class="col-md-12">-->
<!--                                                    <div class="moo-form-group">-->
<!--                                                        <label for="moo_zipcode" class="moo-control-label moo-checkoutText-zipCode">ZIP Code</label>-->
<!--                                                        <input class="moo-form-control" name="zipcode" id="moo_zipcode" placeholder="zip code">-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
                                    </div>
                                <?php } ?>
                                <?php if($MooOptions['payment_cash'] == 'on' || $MooOptions['payment_cash_delivery'] == 'on'){ ?>
                                    <div id="moo_cashPanel">
                                        <div class="moo-row"  id="moo_verifPhone_verified">
                                            <img src="<?php echo  plugin_dir_url(dirname(__FILE__))."public/img/check.png"?>" width="60px">
                                            <p>Your phone number has been verified <br />Please have your payment ready when picking up from the store <br/>Please finalize your order below</p>
                                        </div>
                                        <div class="moo-row" id="moo_verifPhone_sending">
                                            <div class="moo-form-group moo-form-inline">
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
                                                <a href="#" onclick="moo_verifyCodeTryAgain(event)"> click here try again</a>
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

                        <?php if($MooOptions['tips'] == 'enabled' && $merchant_proprites->tipsEnabled){?>
                        <div id="moo-checkout-form-tips">
                            <div class="moo-checkout-bloc-title moo-checkoutText-tip">
                                tip
                            </div>
                            <div class="moo-checkout-bloc-content">
                                <div class="moo-row"  style="margin-top: 13px;">
                                    <div class="moo-col-md-6">
                                        <div class="moo-form-group">
                                            <select class="moo-form-control" name="moo_tips_select" id="moo_tips_select" onchange="moo_tips_select_changed()">
                                                <option value="cash">Add a tip to this order</option>
                                                <option value="10">10%</option>
                                                <option value="15">15%</option>
                                                <option value="20">20%</option>
                                                <option value="25">25%</option>
                                                <option value="other">Custom $</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="moo-col-md-6">
                                        <div class="moo-form-group">
                                            <input class="moo-form-control" name="tip" id="moo_tips" value="0" onchange="moo_tips_amount_changed()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="moo_chekout_border_bottom"></div>
                        <?php

                        }
                        if($MooOptions['use_special_instructions']=="enabled")
                        {
                            ?>
                        <div id="moo-checkout-form-instruction">
                            <div class="moo-checkout-bloc-title moo-checkoutText-instructions">
                                Special instructions
                            </div>
                            <div class="moo-checkout-bloc-content">
                                <textarea cols="100%" rows="5" id="Mooinstructions"></textarea>
                                *additional charges may apply and not all changes are possible
                            </div>
                        </div>
                        <?php
                        }
                        //Check if coupons are enabled
                        if($MooOptions['use_coupons']=="enabled")
                        {
                            ?>
                            <div class="moo_chekout_border_bottom"></div>
                            <div id="moo-checkout-form-coupon">
                                <div class="moo-checkout-bloc-title moo-checkoutText-couponCode">
                                    Coupon code
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
                    </div>
                    <!--            Checkout form - Cart scetion       -->
                    <div class="moo-col-md-5">
                        <div class="moo-shopping-cart MooCartInCheckout">
                            <div class="moo-column-labels-checkout">
                                <label class="moo-product-quantity moo-product-quantity-checkou moo-checkoutText-qtyt" style="width: 20%">Qty</label>
                                <label class="moo-product-details moo-product-details-checkout moo-checkoutText-product" style="width: 60%">Product</label>
                                <label class="moo-product-price moo-product-price-checkout moo-checkoutText-price" style="width: 20%">Price</label>
                            </div>
                            <?php foreach ($_SESSION['items'] as $key=>$line) {
                                $modifiers_price = 0;
                           ?>
                                <div class="moo-product">
                                    <div class="moo-product-quantity" style="width: 20%">
                                        <strong><?php echo $line['quantity']?></strong>
                                    </div>
                                    <div class="moo-product-details moo-product-details-checkout" style="width: 60%">
                                        <div class="moo-product-title"><strong><?php echo $line['item']->name?></strong></div>
                                        <p class="moo-product-description">
                                            <?php
                                            foreach($line['modifiers'] as $modifier){

                                                if(isset($modifier['qty']) && intval($modifier['qty'])>0)
                                                {
                                                    echo '<small>'.$modifier['qty'].'x ';
                                                    $modifiers_price += $modifier['price']*$modifier['qty'];
                                                }
                                                else
                                                {
                                                    echo '<small>1x ';
                                                    $modifiers_price += $modifier['price'];
                                                }

                                                if($modifier['price']>0)
                                                    echo ''.$modifier['name'].'- $'.number_format(($modifier['price']/100),2)."</small><br/>";
                                                else
                                                    echo ''.$modifier['name']."</small><br/>";

                                            }
                                            if($line['special_ins'] != "")
                                                echo '<span style="color:red">SI: '.$line['special_ins']."</span>";
                                            ?>
                                        </p>
                                    </div>
                                    <?php $line_price = $line['item']->price+$modifiers_price;?>
                                        <div class="moo-product-line-price"><strong>$<?php echo number_format(($line_price*$line['quantity']/100),2)?></strong></div>
                                </div>
                            <?php } ?>

                            <div class="moo-totals" style="padding-right: 10px;">
                                <div class="moo-totals-item">
                                    <label class="moo-checkoutText-subtotal">Subtotal</label>
                                    <div class="moo-totals-value" id="moo-cart-subtotal">$<?php echo number_format($total['sub_total'],2);?></div>
                                </div>
                                <div class="moo-totals-item">
                                    <label class="moo-checkoutText-tax" >Tax</label>
                                    <div class="moo-totals-value" id="moo-cart-tax">
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
                                    <label class="moo-checkoutText-deliveryFees"><?php echo $MooOptions["delivery_fees_name"];?></label>
                                    <div class="moo-totals-value" id="moo-cart-delivery-fee">
                                        <?php
                                        if(is_double($MooOptions['fixed_delivery']))
                                        {
                                            echo '$'.number_format(($MooOptions['fixed_delivery']),2);
                                            $grand_total = $total['total']+$MooOptions['fixed_delivery']+$total['serviceCharges'];
                                        }
                                        else
                                        {
                                            echo '$0.00';
                                            $grand_total = $total['total']+$total['serviceCharges'];
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php if($MooOptions['use_coupons']=="enabled"){//check if coupons are enabled ?>
                                    <div class="moo-totals-item" id="MooCouponInTotalsSection" style="<?php if($coupon == null) echo 'display:none;';?>">
                                        <label id="mooCouponName"><?php echo $coupon['name'];?></label>
                                        <div class="moo-totals-value" id="mooCouponValue">
                                            <?php
                                            if($coupon['type']=='amount')
                                                echo '- $'.number_format($coupon['value'],2);
                                            else
                                            {
                                                $t = $coupon['value']*$total['sub_total']/100;
                                                echo "- $".number_format($t,2);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="moo-totals-item" id="MooServiceChargesInTotalsSection"  style="<?php if(! $total['serviceCharges']>0) echo 'display:none;';?>">
                                    <label id="MooServiceChargesName"><?php echo $MooOptions['service_fees_name']; ?></label>
                                    <div class="moo-totals-value" id="moo-cart-service-fee">
                                        <?php
                                        echo '$'.number_format($total['serviceCharges'],2);;
                                        ?>
                                    </div>
                                </div>
                                <?php if($MooOptions['tips']=='enabled'){?>
                                    <div class="moo-totals-item" id="MooTipsInTotalsSection">
                                        <label class="moo-checkoutText-tipAmount">Tip</label>
                                        <div class="moo-totals-value" id="moo-cart-tip">$0.00</div>
                                    </div>
                                <?php } ?>
                                <div class="moo-totals-item moo-totals-item-total" style="font-weight: 700;">
                                    <label class="moo-checkoutText-grandTotal">Grand Total</label>
                                    <div class="moo-totals-value" id="moo-cart-total">$<?php echo number_format($grand_total,2) ?></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--            Checkout form - Link section        -->
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
                        <a href="#"  id="moo_btn_submit_order" onclick="moo_finalize_order(event)" class="moo-btn moo-btn-primary moo-finalize-order-btn moo-checkoutText-finalizeOrder">
                            FINALIZE ORDER
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if($custom_js != null)
            echo '<script type="text/javascript">'.$custom_js.'</script>';
        if(isset($_SESSION['moo_customer_token']) && $_SESSION['moo_customer_token']!="")
            echo '<script type="text/javascript"> jQuery( document ).ready(function($) { moo_show_chooseaddressform() });</script>';

        return ob_get_clean();
    }

    /*
     * The store interface 2
     */
    public static function ItemsWithImages($atts,$content) {
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-Model.php";
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-CallAPI.php";

        $model = new moo_OnlineOrders_Model();
        $api   = new moo_OnlineOrders_CallAPI();

        wp_enqueue_style( 'bootstrap-css' );
        wp_enqueue_style( 'font-awesome' );
        wp_enqueue_script( 'custom-script-items' );
        wp_enqueue_script( 'jquery-accordion','jquery' );

        wp_enqueue_script( 'magnific-modal',  'jquery'  );
        wp_enqueue_style ( 'magnific-popup' );

        wp_enqueue_style ( 'custom-style-accordion' );
        wp_enqueue_style ( 'custom-style-items' );

        $MooOptions = (array)get_option( 'moo_settings' );
        $cart_page_id  = $MooOptions['cart_page'];
        $store_page_id = $MooOptions['store_page'];

        $cart_page_url  =  get_page_link($cart_page_id);
        $store_page_url =  get_page_link($store_page_id);

        ob_start();
        if(isset($_GET['category']) || isset($atts['category'])){
            $nb_items = 0;
            $category = (isset($_GET['category']))?esc_sql($_GET['category']):esc_sql($atts['category']);

            echo '<div class="row moo_items" id="Moo_ItemContainer">';

            if($category == 'NoCategory' || $category == "") $items_tab = $model->getItems();
            else {
                $cat = $model->getCategory($category);
                $items = explode(',',$cat->items);
                $items_tab = array();
                foreach($items as $uuid_item) {
                    if($uuid_item == "") continue;
                    $ItemLoaded = $model->getItem($uuid_item);
                    if($ItemLoaded != null)
                        array_push($items_tab,$ItemLoaded);
                }
            }

            if(count($items_tab)<=0)  echo '<div class="col-md-12">"No items available.</div>';
            else
            {
                $track_stock = $api->getTrackingStockStatus();

                if($track_stock)
                    $itemStocks = $api->getItemStocks();

                $items_tab = (array)$items_tab;
                //ReOrder the items
                usort($items_tab, array('Moo_OnlineOrders_Shortcodes','moo_sort_items'));


                if(isset($cat))
                {
                    if (!isset($cat->alternate_name) || $cat->alternate_name == null || $cat->alternate_name =='') {
                        echo '<div class="moo_category_page_title" id="moo_category_page_content">'.$cat->name.'</div>';
                    }
                    else {
                        echo '<div class="moo_category_page_title" id="moo_category_page_content">'.$cat->alternate_name.'</div>';
                    }
                }
                foreach($items_tab as $item)
                {
                    if($track_stock)
                        $itemStock = self::getItemStock($itemStocks,$item->uuid);
                    else
                        $itemStock = false;

                    // Verify if the item is visible or not
                    if(!is_object($item) || $item->visible == 0 || $item->hidden == 1 || $item->price_type == 'VARIABLE') continue;
                    $item_images = $model->getEnabledItemImages($item->uuid);

                   // $default_images = $model->getDefaultItemImage($item->uuid);
                    $no_image_url =  plugin_dir_url(dirname(__FILE__))."public/img/no-image.jpg";

                    $nb_modifiers = $model->itemHasModifiers($item->uuid)->total;

                    $item_name = $item->name;
                    //$item_name = ucfirst(strtolower($item->name));


                    $img_array = array();
                    foreach ($item_images as $key => $item_img) {
                        array_push($img_array, $item_img->url);
                    }

                    echo '<div class="col-md-4 col-sm-6 col-xs-12 moo_item_flip">';
                    echo '<a class="open-popup-link" href="#moo_popup_item_'.$item->uuid.'" >';
                    echo "<div class='moo_item_flip_container'>";
                    echo "<div class='moo_item_flip_image'>";

                    if (count($img_array)>1) {
                        echo "<div class='demo' data-images='".json_encode($img_array)."'>";
                        echo "<img style='height: 245px; width: 100%;' class='img-responsive img-thumbnail' src='".$img_array[0]."'>";
                        echo "</div>";
                    } else {
                        if(count($img_array)==1)
                            echo "<img class='img-responsive img-thumbnail' style='height: 245px; width: 100%;' src='".$img_array[0]."' />";
                        else
                            echo "<img class='img-responsive img-thumbnail' style='height: 245px; width: 100%;' src='".$no_image_url."' />";
                    }

                    echo "</div>";

                    echo "<div class='moo_item_flip_title'>".$item_name."</div>";

                    if($item->price>0)
                        if($item->price_type == "PER_UNIT") echo "<div class='moo_item_flip_content'>$".(number_format(($item->price/100),2,'.',''))." /".$item->unit_name."";
                        else echo "<div class='moo_item_flip_content'>$".(number_format(($item->price/100),2,'.',''));
                    else
                        echo "<div class='moo_item_flip_content'>";

                    echo "<span class='center-span'></span></div>";
                    echo '</div></a></div>';
                    echo '<div class="row white-popup mfp-hide popup_slider" id="moo_popup_item_'.$item->uuid.'">';
                        if($nb_modifiers != "0") { // If we have modifiers
                   ?>
                            <div class="row nomarginrow">
                                <?php if(count($item_images)>1) { ?>
                                    <div class="col-md-12 carrousel_images_item_top carousel slide" id="carrousel_images_item" data-ride="carousel">
                                        <ol class="carousel-indicators">
                                            <?php foreach ($item_images as $key => $image) {
                                                if ($key == 0) {
                                                    echo '<li data-target="#carrousel_images_item" data-slide-to="0" class="active"></li>';
                                                    continue;
                                                }
                                                echo '<li data-target="#carrousel_images_item" data-slide-to="'.$key.'"></li>';


                                            } ?>
                                        </ol>
                                        <!-- Wrapper for slides -->
                                        <div class="carousel-inner sliders_wrapper" role="listbox">
                                            <?php foreach ($item_images as $key => $image) {
                                                if ($key == 0) {
                                                    echo "<div class='item active'><img class='img-responsive img_carousel'  src='".$image->url."' style='max-width: 300px;margin: 0 auto;' ></div>";
                                                    continue;
                                                }
                                                echo "<div class='item'><img class='img-responsive img_carousel' src='".$image->url."' style='max-width: 300px;margin: 0 auto;'></div>";
                                            }
                                            ?>
                                        </div>
                                        <!-- Left and right controls -->
                                        <a class="left carousel-control" href="#carrousel_images_item" role="button" data-slide="prev">
                                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="right carousel-control" href="#carrousel_images_item" role="button" data-slide="next">
                                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                    </div>
                                <?php
                                }
                                else
                                {
                                    if(count($item_images)==1) {
                                        echo ' <div class="col-md-12">';
                                        echo "<div class='item active'><img class='img-responsive img_carousel' style='max-width: 300px;margin: 0 auto;' src='".$item_images[0]->url."'></div>";
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                            <div class="row nomarginrow">
                                <div class="col-md-7" id="moo_popup_rightSide">
                                    <form id="moo_form_modifiers" method="post">
                                        <?php
                                        $modifiersgroup = $model->getModifiersGroup($item->uuid);
                                        $nb_mg=0;
                                        foreach ($modifiersgroup as $mg) {
                                            //var_dump($mg);
                                            $modifiers = $model->getModifiers($mg->uuid);
                                            if( count($modifiers) == 0) continue;
                                            $nb_mg++;
                                            if($mg->min_required==1 && $mg->max_allowd==1)
                                            {
                                            ?>
                                                <div class="moo_category_title">
                                                    <div class="moo_title"><?php echo ($mg->alternate_name=="")?$mg->name:$mg->alternate_name;?></div>
                                                </div>
                                                <div style="padding-right: 50px;padding-left: 50px">
                                                    <select name="<?php echo 'moo_modifiers[\''.$item->uuid.'\',\''.$mg->uuid.'\']' ?>" class="moo-form-control">
                                                        <?php  foreach ( $modifiers as $m) {
                                                            if($m->price>0)
                                                                echo '<option value="'.$m->uuid.'">'. (($m->alternate_name=="")?$m->name:$m->alternate_name).' ($'.number_format(($m->price/100), 2).')</option>';
                                                            else
                                                                echo '<option value="'.$m->uuid.'">'. (($m->alternate_name=="")?$m->name:$m->alternate_name).'</option>';

                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                             <?php
                                            }
                                            else
                                            {
                                            ?>
                                                <div class="moo_category">
                                                    <div class="moo_accordion accordion-open" id="<?php echo ($nb_mg == 1)?'MooModifierGroup_default_'.$item->uuid:'MooModifierGroup_'.$mg->uuid?>">
                                                        <div class="moo_category_title">
                                                            <div class="moo_title"><?php echo ($mg->alternate_name=="")?$mg->name:$mg->alternate_name; echo ($mg->min_required>=1)?' (Required)':''; ?></div>
                                                            <span></span>
                                                        </div>
                                                    </div>
                                                    <div class="moo_accordion_content moo_modifier-box2" style="display: none;">
                                                        <ul>
                                                            <?php  foreach ( $modifiers as $m) {
                                                                echo '<li>';
                                                                ?>
                                                                <a href="#" onclick="moo_check(event,'<?php echo $m->uuid ?>')">
                                                                    <div class="detail" >
                                                                       <span class="moo_checkbox" >
                                                                          <input type="checkbox" onclick="event.stopPropagation();" name="<?php echo 'moo_modifiers[\''.$item->uuid.'\',\''.$mg->uuid.'\',\''.$m->uuid.'\']' ?>" id="moo_checkbox_<?php echo $m->uuid ?>" />
                                                                       </span>
                                                                        <p class="moo_label"><?php echo ($m->alternate_name=="")?$m->name:$m->alternate_name;?></p>
                                                                    </div>
                                                                    <div class="moo_price">
                                                                        <?php echo ($m->price>0)?'$'.number_format(($m->price/100), 2):'' ?>
                                                                    </div>
                                                                </a>
                                                                <?php
                                                                echo '</li>';
                                                            }
                                                            if($mg->min_required != null || $mg->max_allowd != null ){
                                                                echo '<li class="Moo_modifiergroupMessage">';
                                                                if(($mg->min_required == $mg->max_allowd)&& $mg->max_allowd>0)
                                                                    echo' Select '.$mg->max_allowd;
                                                                else
                                                                {
                                                                    if($mg->min_required != null && $mg->min_required != 0 ) echo "Must choose at least <br/> ".$mg->min_required;
                                                                    if($mg->max_allowd != null && $mg->max_allowd != 0 ) echo "Select up to  ".$mg->max_allowd;
                                                                }
                                                                echo '</li>';
                                                            }
                                                            ?>

                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                          <?php } ?>
                                    </form>
                                </div>
                                <div class="col-md-5 moo_popup_leftSide" id="moo_popup_leftSide">
                                    <div class="moo_popup_title">
                                        <?php echo $item->name ?>
                                    </div>
                                    <div class="moo_popup_description">
                                        <?php echo stripslashes ($item->description) ?>
                                    </div>
                                    <div class="moo_popup_price">
                                        <?php if($item->price>0) echo '$'.(number_format(($item->price/100),2,'.','')) ?>

                                    </div>
                                    <div class="moo_popup_quantity">
                                        Quantity :
                                        <select class="moo-form-control" value="1" id='moo_popup_quantity'>
                                            <?php
                                            if($track_stock==true && $itemStock!=false && isset($itemStock->stockCount) && $itemStock->stockCount>0)
                                                for($i=1; $i<=$itemStock->stockCount && $i<=10; $i++)
                                                    echo "<option>$i</option>";
                                            else
                                                for($i=1; $i<=10; $i++)
                                                    echo "<option>$i</option>";

                                            ?>
                                        </select>
                                    </div>
                                    <div class="moo_popup_special_instruction">
                                        Special Instructions :
                                        <textarea  class="moo-form-control" name="" id="moo_popup_si" cols="30" rows="2"></textarea>
                                    </div>
                                    <div class="moo_popup_btns_action">
                                        <?php
                                        if($item->outofstock == 1 || ($track_stock==true && $itemStock!=false && isset($itemStock->stockCount)  && $itemStock->stockCount<1)) {
                                            echo '<div style="text-align: center">OUT OF STOCK</div>';
                                        } else { ?>
                                            <a href="#" class="btn btn-primary" onclick="moo_addItemWithModifiersToCart(event,'<?php echo trim($item->uuid) ?>','<?php echo preg_replace('/[^A-Za-z0-9 \-]/', '', $item->name); ?>','<?php echo trim($item->price) ?>')" >ADD TO CART</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } else { // If we don't have modifiers ?>
                            <?php if (count($item_images)>1) { ?>
                                <div class=" col-md-6 carousel slide " id="carrousel_images_item" data-ride="carousel">
                                        <ol class="carousel-indicators">
                                            <?php foreach ($item_images as $key => $image) {
                                                if ($key == 0) {
                                                    echo '<li data-target="#carrousel_images_item" data-slide-to="0" class="active"></li>';
                                                    continue;
                                                }
                                                echo '<li data-target="#carrousel_images_item" data-slide-to="'.$key.'"></li>';
                                            } ?>
                                        </ol>
                                        <!-- Wrapper for slides -->
                                        <div class="carousel-inner sliders_wrapper" role="listbox">
                                            <?php foreach ($item_images as $key => $image) {
                                                if ($key == 0) {
                                                    echo "<div class='item active'><img class='img-responsive img_carousel' src='".$image->url."' style='max-width: 300px;margin: 0 auto;'></div>";
                                                    continue;
                                                }
                                                echo "<div class='item'><img class='img-responsive img_carousel' src='".$image->url."' style='max-width: 300px;margin: 0 auto;'></div>";
                                            }
                                             ?>   
                                        </div> 
                                        <!-- Left and right controls -->
                                        <a class="left carousel-control" href="#carrousel_images_item" role="button" data-slide="prev">
                                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="right carousel-control" href="#carrousel_images_item" role="button" data-slide="next">
                                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                </div>
                            <?php }
                            else {
                                    if(count($item_images)==1) {
                                        echo '<div class="col-md-6" style="padding-left: 0px;">';
                                        echo "<img class='img-responsive img_carousel'  src='".$item_images[0]->url."'>";
                                        echo '</div>';
                                    }
                                    else
                                    {
                                        echo '<div class="col-md-6" style="padding-left: 0px;">';
                                        echo "<img class='img-responsive hidden-xs' src='".$no_image_url."'>";
                                        echo '</div>';
                                    }

                            } ?>
                            <div class="col-md-6 moo_popup_leftSide" id="moo_popup_leftSide">
                                <div class="moo_popup_title">
                                    <?php echo $item->name ?>
                                </div>
                                <div class="moo_popup_description">
                                    <?php echo stripslashes ($item->description) ?>
                                </div>
                                <div class="moo_popup_price">
                                    <?php if($item->price>0) echo '$'.(number_format(($item->price/100),2,'.','')) ?>
                                </div>
                                <div class="moo_popup_quantity">
                                    Quantity :
                                    <select class="moo-form-control" value="1" id='moo_popup_quantity'>
                                        <?php
                                        if($track_stock==true && $itemStock!=false && isset($itemStock->stockCount) && $itemStock->stockCount>0)
                                            for($i=1; $i<=$itemStock->stockCount && $i<=10; $i++)
                                                echo "<option>$i</option>";
                                        else
                                            for($i=1; $i<=10; $i++)
                                                echo "<option>$i</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="moo_popup_special_instruction">
                                    Special Instructions :
                                    <textarea  class="moo-form-control" name="" id="moo_popup_si" cols="30" rows="2"></textarea>
                                </div>
                                <div class="moo_popup_btns_action">
                                    <?php

                                    if($item->outofstock == 1 || ($track_stock==true && $itemStock!=false && isset($itemStock->stockCount)  && $itemStock->stockCount<1)) {
                                        echo '<div style="text-align: center">OUT OF STOCK</div>';
                                    } else { ?>
                                        <a href="#" class="btn btn-primary" onclick="moo_addItemWithModifiersToCart(event,'<?php echo trim($item->uuid) ?>','<?php echo preg_replace('/[^A-Za-z0-9 \-]/', '', $item->name); ?>','<?php echo trim($item->price) ?>')" >ADD TO CART</a>
                                    <?php } ?>
                                </div>
                            </div>
                            
                                 
                        <?php } ?>
                    </div>

                <?php }
            }
            echo '</div>';
            echo '<div class="row moo_items" align="center"><a class="moo-btn moo-btn-default" href="'.$store_page_url.'" >Back to Main Menu</a><a style="margin-left:10px" class="btn btn-primary" href="'.$cart_page_url.'">View cart</a></div>';
        }
        else
            {
                $MooOptions = (array)get_option('moo_settings');
                ?>

                <div class="row moo_categories">
                    <?php
                    //$colors = self::GetColors();
                    $categories = $model->getCategories();
                    $items = $model->getItems();
                    if(count($categories) == 0 && count($items)== 0 )
                        echo "<h2 style='text-align: center'>You don't have any Items, please import your inventory from Clover</h2>";
                    else
                    {
                        if(get_option("moo-show-allItems") == 'true')
                        {
                            array_unshift($categories,(object)array("name"=>'All Items',"uuid"=>'NoCategory',"image_url"=>plugin_dir_url(dirname(__FILE__))."public/img/all-items-img.jpg"));
                        }

                        if(count($categories)>0)

                            if(isset($MooOptions['show_categories_images']) && $MooOptions['show_categories_images'] == 'true')
                            {
                                foreach ($categories as $category ){
                                    if($category->uuid == 'NoCategory')
                                    {
                                        $category_name = 'All Items';
                                    }
                                    else
                                    {
                                        if(strlen($category->items) < 1 || $category->show_by_default == 0 ) continue;
                                        $category_name = $category->name;
                                    }
                                    echo '<div class="col-md-4 col-sm-6 col-xs-12 moo_category_flip" >';
                                    echo "<a href='".(esc_url( add_query_arg( 'category', $category->uuid) ))."'><div class='moo_category_flip_container' style='border: none;'>";

                                    if (!isset($category->alternate_name) || $category->alternate_name == "") {
                                        echo "<div class='moo_category_flip_title moo_image'>".ucfirst(strtolower($category_name))."</div>";
                                    }
                                    else
                                    {
                                        echo "<div class='moo_category_flip_title moo_image'>".ucfirst(strtolower($category->alternate_name))."</div>";
                                    }
                                    if (!isset($category->image_url )) {
                                        echo "<div class='moo_item_flip_image'>";
                                        echo "<img src='".plugin_dir_url(dirname(__FILE__))."public/img/no-image.jpg' style='height: 245px;width: 100%;'></div>";                                }
                                    else
                                    {
                                        echo "<div class='moo_item_flip_image'>";
                                        echo "<img src='".$category->image_url."' style='height: 245px;width: 100%;'>";
                                        echo "</div>";
                                    }

                                    echo '</div></a>';
                                    echo '</div>';
                                }
                            }
                            else
                            {
                                foreach ($categories as $category ){
                                    if($category->uuid == 'NoCategory')
                                    {
                                        $category_name = 'All Items';
                                    }
                                    else
                                    {
                                        if(strlen($category->items) < 1 || $category->show_by_default == 0 ) continue;
                                        $category_name = $category->name;
                                    }


                                    echo '<div class="col-md-4 col-sm-6 col-xs-12 moo_category_flip" >';
                                    echo "<a href='".(esc_url( add_query_arg( 'category', $category->uuid) ))."'><div class='moo_category_flip_container'>";

                                    if (!isset($category->alternate_name) || $category->alternate_name == "") {
                                        echo "<div class='moo_category_flip_title'>".ucfirst(strtolower($category_name))."</div>";
                                    }
                                    else
                                    {
                                        echo "<div class='moo_category_flip_title'>".ucfirst(strtolower($category->alternate_name))."</div>";
                                    }


                                    echo '</div></a>';
                                    echo '</div>';
                                }
                            }

                        else
                        {
                          //Redirect to the page No category
                            $location = (esc_url(add_query_arg('category', 'NoCategory',(get_page_link($MooOptions['store_page'])))));
                            wp_redirect ( $location );
                        }
                    }
                    ?>
                </div>
        <?php } ?>
        <div id="moo_cart">
            <a href="<?php echo get_page_link($MooOptions['cart_page']);?>">
                <div id="moo_cart_icon">
                    <span>VIEW SHOPPING CART</span>
                </div>
            </a>
        </div>
        <?php return ob_get_clean();
    }
    /*
     * The function that choose teh default store interface
     */
    public static function TheStore($atts, $content)
    {
        $api   = new moo_OnlineOrders_CallAPI();
        $MooOptions = (array)get_option('moo_settings');
        $oppening_status = json_decode($api->getOpeningStatus(4,30));
        $oppening_msg = "";

        if($MooOptions['hours'] != 'all' && $oppening_status->status == 'close')
        {
            if($oppening_status->store_time == '')
                $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg">Online Ordering Currently Closed'.(($MooOptions['hide_menu'] != 'on' && $MooOptions['accept_orders_w_closed'] == 'on' )?"<br/><p style='color: green'>Order in Advance Available</p>":"").'</div>';
            else
                $oppening_msg = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg"><strong>Today\'s Online Ordering hours</strong> <br/> '.$oppening_status->store_time.'<br/>Online Ordering Currently Closed'.(($MooOptions['hide_menu'] != 'on'&& $MooOptions['accept_orders_w_closed'] == 'on' )?"<br/><p style='color: green'>Order in Advance Available</p>":"").'</div>';

        }
        if($MooOptions['hours'] != 'all' && $MooOptions['hide_menu'] == 'on' && $oppening_status->status == 'close')
        {
            return '<div id="moo_OnlineStoreContainer" >'.$oppening_msg.'</div>';
        }

        $html_code  = '';
        $style = $MooOptions["default_style"];
        $custom_css = $MooOptions["custom_css"];
        $custom_js  = $MooOptions["custom_js"];
        $website_width = intval($MooOptions[$style."_width"]);

        //Include custom css
        if($custom_css != null)
            $html_code .= '<style type="text/css">'.$custom_css.'@media only screen and (min-width: 768px) {#moo_OnlineStoreContainer,.moo-shopping-cart-container {min-width: '.$website_width.'px;}}</style>';
        else
            $html_code .= '<style type="text/css">@media only screen and (min-width: 768px) {#moo_OnlineStoreContainer,.moo-shopping-cart-container {min-width: '.$website_width.'px;}}</style>';

        $html_code .=  $oppening_msg;
        $html_code .=  '<div id="moo_OnlineStoreContainer">';

        if(isset($atts["js_loading"])  && $atts["js_loading"] === "false"){
            if(isset($atts["interface"]) && $atts["interface"] === "si4") {
                $html_code .= self::moo_store_style4($atts, $content);
            } else {
                $html_code .= "Currently only the interface for can be loaded without js, please add the param interface='si4' to this shortcode" ;
            }
        } else {
            if( $style == "style1" ) {
                $html_code .= self::AllItemsAcordion($atts, $content);
            } else {
                if( $style == "style2" ) {
                    $html_code .= self::moo_store_style3($atts, $content);
                } else {
                    if( $style == "style3" ) {
                        $html_code .= self::ItemsWithImages($atts, $content);
                    } else {
                        if($style == "style1") {
                            $html_code .= self::AllItems($atts, $content);
                        } else {
                            $html_code .= self::moo_store_use_theme($atts, $content);
                        }
                    }

                }
            }
        }

        $html_code .=  '</div><div class="row Moo_Copyright">'.$MooOptions["copyrights"].'</div>';

        //Include custom js
        if($custom_js != null)
            $html_code .= '<script type="text/javascript">'.$custom_js.'</script>';

        return $html_code;
    }
    /*
     * The cart page
     */
    public static function theCart($atts, $content)
    {
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-Model.php";
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-CallAPI.php";
        $model = new moo_OnlineOrders_Model();
        $api = new moo_OnlineOrders_CallAPI();

       // wp_enqueue_style( 'bootstrap-css' );
        wp_enqueue_style( 'font-awesome' );
        wp_enqueue_style( 'custom-style-cart3');

        $MooOptions =(array)get_option( 'moo_settings' );

        $checkout_page_id  = $MooOptions['checkout_page'];
        $store_page_id     = $MooOptions['store_page'];


        $store_page_url    =  get_page_link($store_page_id);
        $checkout_page_url =  get_page_link($checkout_page_id);

        ob_start();

        $MooOptions = (array)get_option('moo_settings');
        $custom_css = $MooOptions["custom_css"];
        $custom_js  = $MooOptions["custom_js"];
        //Include custom css
        if($custom_css != null)
           echo '<style type="text/css">'.$custom_css.'</style>';

        $total =   Moo_OnlineOrders_Public::moo_cart_getTotal(true);

        if($total === false){
            return '<div class="moo_emptycart"><p>Your cart is empty</p><span><a class="moo-btn moo-btn-default" href="'.$store_page_url.'" style="margin-top: 30px;">Back to Main Menu</a></span></div>';
        };

        $track_stock = $api->getTrackingStockStatus();
        if($track_stock==true)
        {
            $itemStocks = $api->getItemStocks();
        }
        //Coupons
        if(isset($_SESSION['coupon']) && !empty($_SESSION['coupon']))
        {
            $coupon = $_SESSION['coupon'];
            if($coupon['minAmount']>$total['sub_total'])
                $coupon = null;
        }
        else
            $coupon = null;

    ?>
        <div class="moo-shopping-cart-container">
        <div class="moo-shopping-cart">
            <div class="moo-column-labels">
                <?php if($MooOptions['default_style']=='style3'){?>
                    <label class="moo-product-image">Image</label>
                <?php }?>
                <label class="moo-product-details"  <?php if($MooOptions['default_style']!='style3'){echo 'style="width:57%"';}?>>Product</label>
                <label class="moo-product-price">Price</label>
                <label class="moo-product-quantity">Quantity</label>
                <label class="moo-product-removal">Remove</label>
                <label class="moo-product-line-price">Total</label>
            </div>
            <?php foreach ($_SESSION['items'] as $key=>$line) {
                $modifiers_price=0;
                $item_images = $model->getItemImages($line['item']->uuid);
                $no_image_url =  plugin_dir_url(dirname(__FILE__))."public/img/no-image.png";
                $default_image = (count($item_images)==0)?$no_image_url:$item_images[0]->url;

                if($track_stock)
                    $itemStock = self::getItemStock($itemStocks,$line['item']->uuid);
                else
                    $itemStock = false;
                ?>
            <div class="moo-product">
                <?php if($MooOptions['default_style']=='style3'){?>
                <div class="moo-product-image">
                    <img src="<?php echo $default_image ?>">
                </div>
                <?php }?>
                <div class="moo-product-details"  <?php if($MooOptions['default_style']!='style3'){echo 'style="width:57%"';}?>>
                    <div class="moo-product-title"><?php echo $line['item']->name?></div>
                    <p class="moo-product-description">
                        <?php
                        foreach($line['modifiers'] as $modifier)
                        {

                            if(isset($modifier['qty']) && intval($modifier['qty'])>0)
                            {
                                echo ''.$modifier['qty'].'x ';
                                $modifiers_price += $modifier['price']*$modifier['qty'];
                            }
                            else
                            {
                                echo '1x ';
                                $modifiers_price += $modifier['price'];
                            }

                            if($modifier['price']>0)
                                echo ''.$modifier['name'].'- $'.number_format(($modifier['price']/100),2)."<br/>";
                            else
                                echo ''.$modifier['name']."</small><br/>";

                        }
                        if($line['special_ins'] != "")
                            echo '<span style="color:red">SI: '.$line['special_ins']."</span>";
                        ?>

                    </p>
                </div>
                <div class="moo-product-price"><?php $line_price = $line['item']->price+$modifiers_price; echo number_format(($line_price/100),2)?></div>

                <div class="moo-product-quantity">
                    <input type="number" value="<?php echo $line['quantity']?>" min="1" max="<?php if($itemStock) echo $itemStock->stockCount; else echo '';?>" onchange="moo_updateQuantity(this,'<?php echo $key?>')">
                </div>
                <div class="moo-product-removal">
                    <a class="moo-remove-product" onclick="moo_removeItem(this,'<?php echo $key?>')">
                        Remove
                    </a>
                </div>
                <div class="moo-product-line-price"><?php echo '$'.number_format(($line_price*$line['quantity']/100),2)?></div>
            </div>
        <?php } ?>

            <div class="moo-totals">
                <a href="#" style="color: #337ab7;" onclick="moo_emptyCart(event)">Empty the cart</a>
                <div class="moo-totals-item">
                    <label>Subtotal</label>
                    <div class="moo-totals-value" id="moo-cart-subtotal">$<?php echo  number_format($total['sub_total'],2); ?></div>
                </div>
                <div class="moo-totals-item">
                    <label>Tax</label>
                    <div class="moo-totals-value" id="moo-cart-tax">

                        <?php
                        if($coupon == null)
                            echo '$'. number_format($total['total_of_taxes_without_discounts'],2);
                        else
                            echo '$'. number_format($total['total_of_taxes'],2);
                        ?>
                    </div>
                </div>
                <?php if($MooOptions['use_coupons']=="enabled"){//check if coupons are enabled ?>
                    <div class="moo-totals-item" id="MooCouponInTotalsSection" style="<?php if($coupon == null) echo 'display:none;';?>">
                        <label id="mooCouponName"><?php echo $coupon['name'];?></label>
                        <div class="moo-totals-value" id="mooCouponValue">
                            <?php
                            if($coupon['type']=='amount')
                                echo '- $'.number_format($coupon['value'],2);
                            else
                            {
                                $t = $coupon['value']*$total['sub_total']/100;
                                echo '- $'.number_format($t,2);
                            }
                            ?>
                        </div>
                    </div>
                <?php }
                ?>
                <?php if($total['serviceCharges']>0){//check if coupons are enabled ?>
                    <div class="moo-totals-item" id="MooServiceChargesInTotalsSection">
                        <label id="MooServiceChargesName"><?php echo $MooOptions['service_fees_name'];?></label>
                        <div class="moo-totals-value" id="MooServiceChargesValue">
                            <?php
                                echo '$'.number_format($total['serviceCharges'],2);;
                            ?>
                        </div>
                    </div>
                <?php } ?>
<!--                <div class="moo-totals-item">-->
<!--                    <label>Shipping</label>-->
<!--                    <div class="moo-totals-value" id="moo-cart-shipping">15.00</div>-->
<!--                </div>-->
                <div class="moo-totals-item moo-totals-item-total">
                    <label>Grand Total</label>
                    <div class="moo-totals-value" id="moo-cart-total">$<?php echo  number_format($total['total'],2); ?></div>
                </div>
            </div>
            <a href="<?php echo $checkout_page_url?>" ><button class="moo-checkout">Checkout</button></a>
            <a href="<?php echo $store_page_url?>" ><button class="moo-continue-shopping">Continue shopping</button></a>


        </div>
        </div>
        <?php
        if($custom_js != null)
            echo '<script type="text/javascript">'.$custom_js.'</script>';
        return ob_get_clean();
    }

    /*
     * This function is the callback of the Shortcode adding buy button,
     * @since 1.0.6
     */
    public static function moo_BuyButton($atts, $content)
    {
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-Model.php";
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-CallAPI.php";
        $model = new moo_OnlineOrders_Model();
        $api = new moo_OnlineOrders_CallAPI();
        $cssClass= "";

        if(isset($atts['name']) && $atts['name']!="")
            $title = $atts['name'];
        else
            $title = 'This item';
        if(isset($atts['css-class']) && $atts['css-class']!="")
            $cssClass = $atts['css-class'];
        else
            $cssClass = '';

        if(isset($atts['id']) && $atts['id']!="")
        {
            $item_uuid = sanitize_text_field($atts['id']);
            $item = $model->getItem($item_uuid);
            if($item)
            {

                if($model->itemHasModifiers($item_uuid)->total != '0')
                {
                    if($cssClass=="")
                        $html =  "<a style='background-color: #4CAF50;border: none;color: white;padding: 10px 24px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;' href='#' onclick='moo_openQty_Window(event,\"".$item->uuid."\",moo_btn_addToCartFIWM)'>ADD TO CART</a>";
                    else
                        $html =  "<a class='".$cssClass."' href='#' onclick='moo_openQty_Window(event,\"".$item->uuid."\",moo_btn_addToCartFIWM)'>ADD TO CART</a>";
                }
                else
                {
                    if($cssClass=="")
                        $html =  "<a style='background-color: #4CAF50;border: none;color: white;padding: 10px 24px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;' href='#' onclick='moo_openQty_Window(event,\"".$item->uuid."\",moo_btn_addToCart)'>ADD TO CART</a>";
                    else
                        $html =  "<a class='".$cssClass."' href='#' onclick='moo_openQty_Window(event,\"".$item->uuid."\",moo_btn_addToCart)'>ADD TO CART</a>";

                }
                return $html;
            }
            else
            {
                return 'Item Not Found';
            }
        }
        else
        {
            return 'Missing Item ID';
        }
    }


    public static function moo_sort_items($a,$b)
    {
        return $a->sort_order>$b->sort_order;
    }
    public static function moo_store_style3($atts, $content)
    {

        wp_enqueue_style( 'font-awesome' );

        //wp_enqueue_style( 'bootstrap-css' );
        wp_enqueue_style ( 'mooStyle-style3' );
        wp_enqueue_script( 'mooScript-style3' );


        $MooOptions = (array)get_option( 'moo_settings' );

        $cart_page_id  = $MooOptions['cart_page'];
        $checkout_page_id = $MooOptions['checkout_page'];
        $store_page_id = $MooOptions['store_page'];

        $cart_page_url  =  get_page_link($cart_page_id);
        $checkout_page_url =  get_page_link($checkout_page_id);
        $store_page_url =  get_page_link($store_page_id);


        wp_localize_script("mooScript-style3", "moo_CartPage",$cart_page_url);
        wp_localize_script("mooScript-style3", "moo_CheckoutPage",$checkout_page_url);
        wp_localize_script("mooScript-style3", "moo_StorePage",$store_page_url);
        wp_localize_script("mooScript-style3", "moo_RestUrl",get_rest_url());

        ob_start();
        ?>
        <div class="moo-col-md-7" id="moo-onlineStore-categories"></div>
        <div class="moo-col-md-5" id="moo-onlineStore-cart"></div>
       </div>

        <?php
        return ob_get_clean();
    }
    public static function moo_store_style4($atts, $content)
    {
        require_once plugin_dir_path( __FILE__ ) . 'class-moo-OnlineOrders-Restapi.php';
        $rest = new Moo_OnlineOrders_Restapi();
        $request = new WP_REST_Request();
        $request->set_query_params(array(
            'expand' => "five_items"
        ));
        $categories = $rest->getCategories( $request);
        $themeSettings = $rest->getThemeSettings( array("theme_name"=>"onePage"));

        wp_enqueue_style( 'font-awesome' );
        wp_enqueue_style ( 'mooStyle-style4' );
        wp_enqueue_script( 'mooScript-style4' );


        $MooOptions = (array)get_option( 'moo_settings' );

        $cart_page_id  = $MooOptions['cart_page'];
        $checkout_page_id = $MooOptions['checkout_page'];
        $store_page_id = $MooOptions['store_page'];

        $cart_page_url  =  get_page_link($cart_page_id);
        $checkout_page_url =  get_page_link($checkout_page_id);
        $store_page_url =  get_page_link($store_page_id);


        wp_localize_script("mooScript-style4", "moo_CartPage",$cart_page_url);
        wp_localize_script("mooScript-style4", "moo_CheckoutPage",$checkout_page_url);
        wp_localize_script("mooScript-style4", "moo_StorePage",$store_page_url);
        wp_localize_script("mooScript-style4", "moo_RestUrl",get_rest_url());
        wp_localize_script("mooScript-style4", "moo_themeSettings",$themeSettings["settings"]);

        ob_start();
        $nb_items_in_cart = ($themeSettings["nb_items"]>0)?$themeSettings["nb_items"]:'';
        ?>
        <div class="moo-row">
            <div  class="moo-is-sticky moo-new-icon" onclick="mooShowCart(event)">
                <div class="moo-new-icon__count" id="moo-cartNbItems"><?php echo $nb_items_in_cart; ?></div>
                <div class="moo-new-icon__cart"></div>
            </div>
            <div class="moo-row">
                <?php if(count($categories)==0) {
                    echo "<h3>You don't have any category please import your inventory</h3>";
                } else { ?>
                <div class="moo-col-md-3" id="moo-onlineStore-categories">
                   <nav id="moo-menu-navigation" class="moo-stick-to-content">
                       <div class="moo-choose-category">Choose a Category</div>
                       <ul class="moo-nav moo-nav-menu moo-bg-dark moo-dark">
                       <?php
                       foreach ($categories as $category) {
                           if(count($category["five_items"])>0) {
                               echo '<li><a href="#cat-'.strtolower($category['uuid']).'" onclick="MooCLickOnCategory(event,this)">'.$category['name'].'</a></li>';
                           }
                       }
                       ?>
                       </ul>
                   </nav>
                </div>
                <div class="moo-col-md-9" id="moo-onlineStore-items">
                    <?php
                    $html='';
                    foreach ($categories as $category) {
                        if(count($category["five_items"])>0) {
                            $html    .=   '<div id="cat-'.strtolower($category['uuid']).'" class="moo-menu-category">';
                            $html    .=  '<div class="moo-menu-category-title">';
                            $html    .= '   <div class="moo-bg-image" style="background-image: url(&quot;'.(($category['image_url']!=null)?$category['image_url']:"").'&quot;);"></div>';
                            $html    .= '   <div class="moo-title">'.$category['name'].'</div>';
                            $html    .= '</div>';
                            $html    .= '<div class="moo-menu-category-content" id="moo-items-for-'.strtolower($category['uuid']).'">';
                            foreach ($category["five_items"] as $item) {
                                    $item_price = number_format($item["price"]/100,2);
                                    
                                    if($item["price"] > 0 && $item["price_type"] == "PER_UNIT")
                                       $item_price .= '/' . $item["unit_name"];

                                   $html .= '<div class="moo-menu-item moo-menu-list-item" ><div class="moo-row">';

                                   if($item['image'] != null && $item['image']->url != null && $item['image']->url != "") {
                                        $html .= '<div class="moo-col-lg-2 moo-col-md-2 moo-col-sm-12 moo-col-xs-12 moo-image-zoom">';
                                        $html .= '<a href="'.$item['image']->url.'" data-effect="mfp-zoom-in"><img src="'.$item['image']->url.'" class="moo-img-responsive moo-image-zoom"></a></div>';
                                        $html .= '<div class="moo-col-lg-6 moo-col-md-6 moo-col-sm-12 moo-col-xs-12">';
                                        $html .= '<div class="moo-item-name">'.$item['name'].'</div>';
                                        $html .= '<span class="moo-text-muted moo-text-sm">'.$item['description'].'</span></div>';
                                    } else {
                                        $html .= '    <div class="moo-col-lg-8 moo-col-md-8 moo-col-sm-12 moo-col-xs-12">';
                                        $html .= '         <div class="moo-item-name">'.$item['name'].'</div>';
                                        $html .= '         <span class="moo-text-muted moo-text-sm">'.$item['description'].'</span>';
                                        $html .= '    </div>';
                                    }

                                    if($item['price'] == 0) {
                                        $html .= '    <div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-12 moo-col-xs-12 moo-text-sm-right"><span></span>';
                                    } else {
                                        $html .= '    <div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-12 moo-col-xs-12 moo-text-sm-right">';
                                        $html .='    <span class="moo-price">$'.$item_price.'</span>';
                                    }

                                    if($item["stockCount"] == "out_of_stock") {
                                        $html .= '<button class="moo-btn-sm moo-hvr-sweep-to-top">Out Of Stock</button>';
                                    } else {
                                        //Checking the Qty window show/hide and add add to cart button
                                        if($themeSettings['settings']["onePage_qtyWindow"] != null && $themeSettings['settings']["onePage_qtyWindow"]== "on") {
                                            if($item['has_modifiers']) {
                                                if($themeSettings['settings']["onePage_qtyWindowForModifiers"] != null && $themeSettings['settings']["onePage_qtyWindowForModifiers"] == "on")
                                                    $html .= '<button class="moo-btn-sm moo-hvr-sweep-to-top" onclick="mooOpenQtyWindow(event,\''.$item['uuid'].'\',\''.$item['stockCount'].'\',moo_clickOnOrderBtnFIWM)">Choose Qty & Options</button>';
                                                else
                                                    $html .= '<button class="moo-btn-sm moo-hvr-sweep-to-top" onclick="moo_clickOnOrderBtnFIWM(event,\''.$item['uuid'].'\',1)">Choose Options & Qty</button>';
                                            } else {
                                                $html .= '<button class="moo-btn-sm moo-hvr-sweep-to-top" onclick="mooOpenQtyWindow(event,\''.$item['uuid'].'\',\''.$item['stockCount'].'\',moo_clickOnOrderBtn)">Add to cart</button>';
                                            }

                                        } else {
                                            if($item['has_modifiers'])
                                                $html .= '<button class="moo-btn-sm moo-hvr-sweep-to-top" onclick="moo_clickOnOrderBtnFIWM(event,\''.$item['uuid'].'\',1)">Choose Options & Qty </button>';
                                            else
                                                $html .= '<button class="moo-btn-sm moo-hvr-sweep-to-top" onclick="moo_clickOnOrderBtn(event,\''.$item['uuid'].'\',1)">Add to cart</button>';

                                        }

                                    }

                                    $html .= '</div>';
                                    if($item['has_modifiers'])
                                        $html .= '<div class="moo-col-lg-12 moo-col-md-12 moo-col-sm-12 moo-col-xs-12" id="moo-modifiersContainer-for-'.$item['uuid'].'"></div>';
                                    $html .= '</div></div>';

                           }

                           if(count($category["five_items"]) == 5) {
                                $html .= '<div class="moo-menu-item moo-menu-list-item"><div class="moo-row moo-align-items-center"><a href="#" class="moo-bt-more moo-show-more" onclick="mooClickOnLoadMoreItems(event,\''.$category['uuid'].'\',\''.$category['name'].'\')"> Show More </a><i class="fa fa-chevron-down" aria-hidden="true" style=" display: block; color:red "></i></div></div>';
                            }
                            $html    .= "</div></div>";
                        }
                    }
                    echo $html;
                    ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    public static function moo_search_bar($atts, $content)
    {

        wp_enqueue_style( 'font-awesome' );

        wp_enqueue_style ( 'moo-search-bar' );
        wp_enqueue_script( 'moo-search-bar' );

        ob_start();
        ?>
        <div class="" id="moo-search-bar-container">
            <div class="moo-search-bar moo-row">
                <input class="moo-col-md-10 moo-search-field" type="text" placeholder="Search" />
                <button class="moo-col-md-2 osh-btn action" onclick="mooClickonSearchButton()">Search</button>
            </div>
            <div class="moo-search-result moo-row"></div>
        </div>
        <?php
        return ob_get_clean();
    }
    public static function moo_store_use_theme($atts, $content)
    {
      //  $MooOptions = (array)get_option('moo_settings');

        $MooOptions = (array)get_option( 'moo_settings' );
        $files = scandir(plugin_dir_path(dirname(__FILE__))."public/themes/".$MooOptions["default_style"]);
        foreach ($files as $file) {
            $f = explode(".",$file);
            if(count($f) == 2)
            {
                $file_name = $f[0];
                $file_extension = $f[1];
                if(strtoupper($file_extension) =="CSS")
                {
                   // wp_register_style( 'moo-'.$file_name.'-css' ,plugins_url( 'themes/'.$this->style.'/'.$file_name.'.'.$file_extension, __FILE__ ),array(), $this->version);
                    wp_enqueue_style(  'moo-'.$file_name.'-css' );
                }
                else{
                    if(strtoupper($file_extension) =="JS")
                    {
                        // wp_register_style( 'moo-'.$file_name.'-css' ,plugins_url( 'themes/'.$this->style.'/'.$file_name.'.'.$file_extension, __FILE__ ),array(), $this->version);
                        wp_enqueue_script(  'moo-'.$file_name.'-js' );
                    }
                }
            }
        }

        ob_start();
        return ob_get_clean();
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

}
