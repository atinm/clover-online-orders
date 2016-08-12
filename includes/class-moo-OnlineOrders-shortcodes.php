<?php

/**
 * Fired during plugin activation
 *
 * @link       http://merchantech.us
 * @since      1.0.0
 *
 * @package    Moo_OnlineOrders
 * @subpackage Moo_OnlineOrders/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
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
            <div class="row  moo_items" id="Moo_FileterContainer">
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
            echo '<div class="row moo_items" align="center"><button class="btn btn-primary" onclick="javascript:window.history.back();">Back to Main menu</button></div>';
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
                        echo "<h1>You don't have any Item, please import your Items from Clover</h1>";
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
        $model = new moo_OnlineOrders_Model();

        wp_enqueue_script( 'custom-script-accordion');
        wp_enqueue_script( 'jquery-accordion',array( 'jquery' ));
        wp_enqueue_script( 'simple-modal',array( 'jquery' ));
        wp_enqueue_script( 'magnific-modal', array( 'jquery' ) );

        wp_enqueue_style ( 'custom-style-accordion' );
        wp_enqueue_style ( 'simple-modal' );
        wp_enqueue_style ( 'magnific-popup' );

        ob_start();
                ?>
        <a href="#ViewShoppingCart">
            <div class="col-xs-12 col-sm-12 hidden-lg hidden-md MooGoToCart">
                VIEW SHOPPING CART
            </div>
           </a>
                <div class="row MooStyleAccorfion">
                <div class="col-md-7" style="margin-bottom: 20px;">
                <?php
                    $categories = $model->getCategories();
                    $all_items  = $model->getItems();
                    if(count($categories)==0 && count($all_items)==0 )
                        echo "<h1>You don't have any Item, please import your Items from Clover</h1>";
                    else
                        if(count($categories)==0)
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

	                    if($category->uuid == 'NoCategory')
                        {
                            $category_name = $category->name;
                        }
                        else
                        {
                            if(strlen ($category->items)< 1 || $category->show_by_default == 0) continue;
                            if(strlen ($category->name) > 30)$category_name = substr($category->name, 0, 30)."...";
                            else  $category_name = $category->name;
                        }

                ?>

                        <div class="moo_category">
                            <div class="moo_accordion" id="MooCat_<?php echo $category->uuid?>">
                                <div class="moo_category_title">
                                    <div class="moo_title"><?php echo ucfirst(strtolower($category_name))?></div>
                                    <span></span>
                                </div>
                            </div>
                            <div class="moo_accordion_content">
                                <ul>
                                    <?php
                                        if($category->uuid == 'NoCategory')
                                            $items = $model->getItems();
                                        else
                                            $items = explode(',',$category->items);


                                    foreach($items as $uuid_item_or_item)
                                    {
                                        if($uuid_item_or_item == "") continue;

                                        if($category->uuid == 'NoCategory')
                                            $item = $uuid_item_or_item;

                                        else
                                            $item = $model->getItem($uuid_item_or_item);

                                        if($item)
                                        {
                                            if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE') continue;
                                            echo '<li>';
                                            if(($model->itemHasModifiers($item->uuid)->total) != "0")
                                                echo '<a class="popup-text" href="#Modifiers_for_'.$item->uuid.'" onclick="moo_openFirstModifierG(\'MooModifierGroup_default_'.$item->uuid.'\')">';
                                            else
                                                echo '<a href="#" onclick="moo_addToCart(event,\''.$item->uuid.'\',\''.esc_sql($item->name).'\',\''.$item->price.'\')">';
                                            echo '  <div class="moo_detail">'.$item->name.'</div>';
                                            echo '  <div class="moo_price">$'.(number_format(($item->price/100),2,'.','')).'</div>';
                                            echo '</a>';
                                            echo '</li>';

                                            ?>
                                            <div class="row white-popup mfp-hide" id="Modifiers_for_<?php echo $item->uuid?>">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <form id="moo_form_modifiers" method="post">
                                                        <?php
                                                        $modifiersgroup = $model->getModifiersGroup($item->uuid);
                                                        $nb_mg=0;
                                                        foreach ($modifiersgroup as $mg) {
                                                            //var_dump($mg);
                                                            $modifiers = $model->getModifiers($mg->uuid);
                                                            if( count($modifiers) == 0) continue;
                                                            $nb_mg++;
                                                         ?>
                                                            <div class="moo_category">
                                                                <div class="moo_accordion accordion-open" id="<?php echo ($nb_mg == 1)?'MooModifierGroup_default_'.$item->uuid:'MooModifierGroup_'.$mg->uuid?>">
                                                                    <div class="moo_category_title">
                                                                        <div class="moo_title"><?php echo ($mg->alternate_name=="")?$mg->name:$mg->alternate_name; echo ($mg->min_required>=1)?' ( Required )':''; ?></div>
                                                                        <span></span>
                                                                    </div>
                                                                 </div>
                                                                <div class="moo_accordion_content moo_modifier-box2" style="display: none;">
                                                                    <ul>
                                                                        <?php  foreach ( $modifiers as $m) {
                                                                            ?>
                                                                            <li>
                                                                                <a href="#" onclick="moo_check(event,'<?php echo $m->uuid ?>')">
                                                                                    <div class="detail" >
                                                                                        <span class="moo_checkbox" >
                                                                                            <input type="checkbox" onclick="event.stopPropagation();" name="<?php echo 'moo_modifiers[\''.$item->uuid.'\',\''.$mg->uuid.'\',\''.$m->uuid.'\']' ?>" id="moo_checkbox_<?php echo $m->uuid ?>" />
                                                                                        </span>
                                                                                        <p class="moo_label"><?php echo $m->name ?></p>
                                                                                    </div>
                                                                                    <div class="moo_price">
                                                                                        $<?php echo number_format(($m->price/100), 2) ?>
                                                                                    </div>
                                                                                </a>
                                                                            </li>
                                                                        <?php
                                                                        }
                                                                        if($mg->min_required != null || $mg->max_allowd != null ){
                                                                            echo '<li class="Moo_modifiergroupMessage">';
                                                                            if($mg->min_required==1 && $mg->max_allowd==1)
                                                                                echo' Must choose 1 ';
                                                                            else
                                                                            {
                                                                                if($mg->min_required != null && $mg->min_required != 0  ) echo 'Must choose at least '.$mg->min_required;
                                                                                if($mg->max_allowd != null && $mg->max_allowd != 0 ) echo "<br/> Must choose  at max ".$mg->max_allowd;
                                                                            }

                                                                            echo '</li>';
                                                                        }
                                                                        ?>

                                                                    </ul>
                                                                </div>
                                                             </div>
                                                        <?php } ?>
                                                        <div style='text-align: center;margin-top: 10px;'>
                                                            <?php echo '<div class="btn btn-danger" onclick="moo_addItemWithModifiersToCart(event,\''.$item->uuid.'\',\''.esc_sql($item->name).'\',\''.$item->price.'\')"  >ADD TO YOUR CART</div>'; ?>
                                                            <div class="btn btn-info" onclick="javascript:jQuery.magnificPopup.close()">Close</div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
            <?php
                    }
                        $checkout_page_id = get_option('moo_checkout_page');
                        $checkout_page_url =  get_page_link($checkout_page_id);
                        if($checkout_page_url === false || get_post_status( $checkout_page_id ) != "publish")
                        {
                            $post_checkout = array(
                                'comment_status' => 'closed',
                                'ping_status' =>  'closed' ,
                                'post_author' => 1,
                                'post_name' => 'Checkout',
                                'post_status' => 'publish' ,
                                'post_title' => 'Checkout',
                                'post_type' => 'page',
                                'post_content' => '[moo_checkout]',
                            );
                            $checkout_page_id = wp_insert_post( $post_checkout, false );
                            //save the id in the database
                            update_option( 'moo_checkout_page', $checkout_page_id );
                            $checkout_page_url =  get_page_link($checkout_page_id);
                        }

                ?>

            </div>
                            <div class="col-md-5" id="ViewShoppingCart">
                                <div class="moo_cart">
                                  <div class="CartContent">
                                  </div>
                                    <div style="text-align: center">
                                        <a href="<?php echo esc_url($checkout_page_url);?>" class="btn btn-danger BtnCheckout">CHECKOUT</a>
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
    public static function getItemsHtml($category,$filterBy,$orderBy,$search)
    {
        ob_start();
        $model = new moo_OnlineOrders_Model();
        if($search != null && $search !="" )
        {
            $search = esc_sql($search);
            $search = sanitize_text_field($search);
            $items_tab = $model->getItemsBySearch($search);
        }
        else
        {
            if($category == 'NoCategory' || $category == "")
            {
                $items_tab = $model->getItems();
            }
            else
            {
                $cat = $model->getCategory($category);
                $items = explode(',',$cat->items);
                $items_tab = array();
                foreach($items as $uuid_item)
                {
                    if($uuid_item == "") continue;
                    array_push($items_tab,$model->getItem($uuid_item));
                }
            }

            usort($items_tab, function($a, $b) use ($filterBy,$orderBy)
            {
                if($orderBy=='asc'){
                    if(!is_numeric($a->$filterBy)) return strcmp($a->$filterBy,$b->$filterBy);
                    else return $a->$filterBy>$b->$filterBy;
                }

                else{
                    if(!is_numeric($a->$filterBy)) return strcmp($b->$filterBy,$a->$filterBy);
                    else return $a->$filterBy<$b->$filterBy;
                }

            });
        }
        $colors = self::GetColors();
        if(count($items_tab)<=0)  echo '<div class="col-md-12">"'.$search.'" : No items available.</div>';
        else
            foreach((array)$items_tab as $item)
            {
                // $item = $model->getItem($uuid_item);
                // Verify if the item is visible or not

                if($item->visible == 0 || $item->hidden == 1 || $item->price_type == 'VARIABLE') continue;

                $nb_modifiers = $model->itemHasModifiers($item->uuid)->total;

                //Cut the name if the lenght > 20 char
                if(strlen ($item->name)> 20) $item_name = substr($item->name, 0, 20)."...";
                else  $item_name = $item->name;

                $item_name = ucfirst(strtolower($item_name));

                $color = current($colors);

                if($nb_modifiers!="0")
                {
                    echo '<div class="col-md-4 col-sm-6 col-xs-12 moo_item_flip">';
                    echo "<div class='moo_item_flip_container'>";
                    echo '<div class="moo_item_flip_title" onclick="moo_addToCart(this,\''.trim($item->uuid).'\',\''.$item->name.'\')">'.$item_name.'</div>';
                    if($item->price_type == "PER_UNIT")
                        echo "<div class='moo_item_flip_content' style='background-color: ".$color."'>$".($item->price/100)." /".$item->unit_name;
                    else
                        echo "<div class='moo_item_flip_content' style='background-color: ".$color."'>$".($item->price/100)."";
                    echo "<span class='right-span'><a href='".(esc_url(add_query_arg('item', $item->uuid,(get_page_link(get_option('moo_store_page'))))))."'> Customize </a></span><span class='center-span'></span></div>";
                    echo '</div></div>';
                }
                else
                {
                    echo '<div class="col-md-4 col-sm-6 col-xs-12 moo_item_flip" onclick="moo_addToCart(this,\''.trim($item->uuid).'\',\''.$item->name.'\')">';
                    echo "<div class='moo_item_flip_container'>";
                    echo "<div class='moo_item_flip_title'>".$item_name."</div>";
                    if($item->price_type == "PER_UNIT")
                        echo "<div class='moo_item_flip_content' style='background-color: ".$color."'>$".($item->price/100)." /".$item->unit_name."";
                    else
                        echo "<div class='moo_item_flip_content' style='background-color: ".$color."'>$".($item->price/100)."";
                    echo "<span class='center-span'></span></div>";
                    echo '</div></div>';
                }
                if(!next($colors)) reset($colors);
            }

        return ob_get_clean();
    }
    public static function checkoutPage($atts, $content)
    {
        wp_enqueue_style( 'custom-style-cart3');

        wp_enqueue_script( 'moo-google-map' );
        wp_enqueue_script( 'display-merchant-map',array('moo-google-map') );
        wp_enqueue_script( 'custom-script-checkout',array('display-merchant-map') );


        wp_enqueue_script( 'forge' );

        ob_start();
        $model = new moo_OnlineOrders_Model();
        $api   = new moo_OnlineOrders_CallAPI();
        $merchantProprietes = json_decode($api->getMerchantProprietes());
        $MooOptions = (array)get_option('moo_settings');
        $custom_css = $MooOptions["custom_css"];
        $custom_js  = $MooOptions["custom_js"];

        $orderTypes = $model->getVisibleOrderTypes();
        

        $total =   Moo_OnlineOrders_Public::moo_cart_getTotal(true);

        $cart_page_id    = get_option('moo_cart_page');
        if($cart_page_id === false)
        {
            $post_cart = array(
                'comment_status' => 'closed',
                'ping_status' =>  'publish' ,
                'post_name' => 'Cart',
                'post_status' => 'publish' ,
                'post_title' => 'Cart',
                'post_type' => 'page',
                'post_content' => '[moo_cart]'
            );

            $cart_page_id =  wp_insert_post( $post_cart );
            update_option( 'moo_cart_page', $cart_page_id );
        }
        $cart_page_url    =  get_page_link($cart_page_id);



        //Include custom css
        if($custom_css != null)
           echo '<style type="text/css">'.$custom_css.'</style>';

        if($total === false){

           echo '<div class="moo_emptycart"><p>Your cart is empty</p><span><a href="'.get_page_link(get_option('moo_store_page')).'">Browse the store</a></span></div>';
           return;
        };
        if($total['total'] == 0){
            echo '<div class="moo_emptycart"><p>Your cart is empty</p><span><a href="'.get_page_link(get_option('moo_store_page')).'">Browse the store</a></span></div>';
            return;
        };

        $key = $api->getPayKey();
        $key = json_decode($key);

        if($key==NULL)
            echo '<div class="alert alert-danger" role="alert" id="moo_checkout_msg"><strong>Error : </strong>This store cannot accept orders, if you are the owner please verify your API Key</div>';

        $merchant_address =  $api->getMerchantAddress();

        wp_localize_script("custom-script-checkout", "moo_OrderTypes",$orderTypes);
        wp_localize_script("custom-script-checkout", "moo_Total",$total);
        wp_localize_script("custom-script-checkout", "moo_Key",(array)$key);
        wp_localize_script("custom-script-checkout", "moo_thanks_page",$MooOptions['thanks_page']);

        wp_localize_script("display-merchant-map", "moo_merchantLat",$MooOptions['lat']);
        wp_localize_script("display-merchant-map", "moo_merchantLng",$MooOptions['lng']);
        wp_localize_script("display-merchant-map", "moo_merchantAddress",$merchant_address);
        wp_localize_script("display-merchant-map", "moo_delivery_zones",$MooOptions['zones_json']);
        wp_localize_script("display-merchant-map", "moo_delivery_other_zone_fee",$MooOptions['other_zones_delivery']);
        wp_localize_script("display-merchant-map", "moo_delivery_free_amount",$MooOptions['free_delivery']);
        wp_localize_script("display-merchant-map", "moo_delivery_fixed_amount",$MooOptions['fixed_delivery']);
        ?>

        <div id="moo_OnlineStoreContainer">
        <div id="moo_merchantmap">
        </div>
        <form id="moo_form_address" method="post" action="#" novalidate="novalidate">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <p style="font-size: 16px !important; margin:0;">Customer Information</p>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <?php if(count($orderTypes)>0) {?>
                                    <div class="form-group">
                                        <label for="OrderType">Order Type:</label>
                                        <select class="form-control" name="OrderType" id="OrderType"
                                                onchange="moo_OrderTypeChanged(this)">
                                            <?php
                                            foreach ($orderTypes as $ot) {
                                                echo "<option value='".$ot->ot_uuid."'>".$ot->label.(($ot->taxable)?"":" (Not taxable)")."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                <?php }?>
                            </div>
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="name">Full Name:</label><input class="form-control" name="name" id="name"></div>
                                <div class="form-group"><label for="phone">Phone number:</label>
                                    <input class="form-control" name="phone" id="phone"></div>
                                <div class="form-group"><label for="address">Address:</label>
                                    <input class="form-control" name="address" id="address" onchange="moo_address_changed()"></div>
                                <div class="form-group">
                                    <label for="city">City:</label>
                                    <input class="form-control" name="city" id="city" onchange="moo_address_changed()">
                                </div>

                            </div>
                             <div class="col-md-6">


                                 <div class="form-group"><label for="email">Email address:</label>
                                     <input class="form-control" name="email" id="email"></div>
                                        <div class="form-group">
                                            <label for="state">State:</label>
                                            <input class="form-control" name="state" id="state" onchange="moo_address_changed()">
                                        </div>
                                        <div class="form-group">
                                            <label for="zipcode">Zip code:</label>
                                            <input class="form-control" name="zipcode" id="zipcode">
                                        </div>
                                        <div class="form-group">
                                            <label for="country">Country:</label>
                                            <input class="form-control" name="country" id="country" onchange="moo_address_changed()">
                                        </div>
                                </div>
                            <div class="col-md-12">
                                <div class="form-group"><label for="instructions">Special instructions</label>
                                    <textarea rows="9" class="form-control" name="instructions" id="instructions"></textarea>
                                    <small>*additional charges may apply and not all changes are possible</small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-12" id="moo-delivery-details">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <p style="font-size: 16px !important; margin:0;">Delivery Details</p>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-8">Your address : <div id="moo_dz_address"></div></div>
                                <div class="col-md-2 col-md-offset-1"><a href="#" class="btn btn-primary" onclick="moo_address_SetOnMap(event)" title="checks to see if your delivery address is accepted">Set on map ?</a> </div>
                            </div>
                            <div class="row">
                                <div id="moo_dz_map"></div>
                                <input type="hidden"  id="moo_customer_lat"  name="moo_customer_lat"/>
                                <input type="hidden"  id="moo_customer_lng"  name="moo_customer_lng" />
                                <input type="hidden"  id="moo_delivery_amount" name="moo_delivery_amount" value="ERROR" />
                            </div>
                        </div>
                    </div>
                </div>
                <!--  Here you can add your personal fields   -->

                <!--  End of  personal fields   -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><p style="font-size: 16px !important; margin:0;">Payment</p></div>
                        <div class="panel-body">
                            <div class="form-group"><label for="nameOnCard">Name on the card:</label><input
                                    class="form-control" name="nameOnCard" id="nameOnCard"></div>
                            <div class="form-group">
                                <label for="Moo_cardNumber">Card number:</label>
                                 <div class="input-group">
                                    <input class="form-control" name="cardNumber" id="Moo_cardNumber">
                                    <div class="input-group-addon">
                                        <img style="min-width:116px;height: 20px;border: 0px;margin: 0;" class="moo_credit_cards hidden-xs">
                                    </div>
                                 </div>


                                <label for="Moo_cardNumber" class="error" style="display: none;"></label></div>
                            <label for="expiredDate" >Expired date:</label>
                            <div class="form-group row">

                                <div class="col-md-6 col-xs-7 col-sm-7"><select name="expiredDateMonth" id="expiredDate" class="form-control">
                                        <option value="1">January (01)</option>
                                        <option value="2">February (02)</option>
                                        <option value="3">March (03)</option>
                                        <option value="4">April(04)</option>
                                        <option value="5">May (05)</option>
                                        <option value="6">June (06)</option>
                                        <option value="7">July (07)</option>
                                        <option value="8">August (08)</option>
                                        <option value="9">September (09)</option>
                                        <option value="10">October (10)</option>
                                        <option value="11">November (11)</option>
                                        <option value="12">December (12)</option>
                                    </select>
                                </div>
                                <div class="col-md-6  col-xs-5 col-sm-5">
                                    <select name="expiredDateYear" class="form-control">
                                        <?php
                                        $current_year = date("Y");
                                        if($current_year < 2016 )$current_year = 2016;
                                        for($i=$current_year;$i<$current_year+20;$i++)
                                            echo '<option value="'.$i.'">'.$i.'</option>';
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 13px;">
                                <div class="col-md-4"><label for="moo_cardcvv">CVV:</label></div>
                                <div class="col-md-8"><input class="form-control" name="cvv" id="moo_cardcvv"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- TIPS Start -->
                <?php if($merchantProprietes->tipsEnabled && $MooOptions['tips']=='enabled'){ ?>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><p style="font-size: 16px !important; margin:0;">TIP</p></div>
                        <div class="panel-body">
                                <div class="row"  style="margin-top: 13px;">
                                    <div class="col-md-6">
                                        <select class="form-control" name="moo_tips_select" id="moo_tips_select" onchange="moo_tips_select_changed()">
                                            <option value="cash">Add a tip to this order</option>
                                            <option value="10">10%</option>
                                            <option value="15">15%</option>
                                            <option value="20">20%</option>
                                            <option value="25">25%</option>
                                            <option value="other">Custom $</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" name="tip" id="moo_tips" value="0" onchange="moo_tips_amount_changed()">
                                    </div>
                                </div>
                        </div>
                    </div>
                 </div>   <!-- TIPS End -->
                <?php }?>
               <div class="row">
                        <div class="col-md-12">
                            <p style="font-size: 20px">Order Information</p>
                            <div class="moo-shopping-cart">
                                <div class="moo-column-labels">
                                    <?php if($MooOptions['default_style']=='style3'){ ?><label class="moo-product-image">Image</label><?php  }?>
                                    <label class="moo-product-details" <?php if($MooOptions['default_style']!='style3'){echo 'style="width:57%"';}?> >Product</label>
                                    <label class="moo-product-price">Price</label>
                                    <label class="moo-product-quantity">Quantity</label>
                                    <label class="moo-product-line-price" style="width: 21%">Total</label>
                                </div>
                                <?php foreach ($_SESSION['items'] as $key=>$line) {
                                    $modifiers_price=0;
                                    if($MooOptions['default_style']=='style3'){
                                        $item_images = $model->getItemImages($line['item']->uuid);
                                        $no_image_url =  plugin_dir_url(dirname(__FILE__))."public/img/no-image.jpg";
                                        $default_image = (count($item_images)==0)?$no_image_url:$item_images[0]->url;
                                    }

                                    ?>
                                    <div class="moo-product">
                                     <?php if($MooOptions['default_style']=='style3'){ ?>
                                        <div class="moo-product-image">
                                            <img src="<?php echo $default_image ?>">
                                        </div>
                                     <?php  }?>
                                        <div class="moo-product-details" <?php if($MooOptions['default_style']!='style3'){echo 'style="width:57%"';}?>  >
                                            <div class="moo-product-title"><?php echo $line['item']->name?></div>
                                            <p class="moo-product-description">
                                                <?php foreach($line['modifiers'] as $modifier){
                                                    if($modifier['price']>0)
                                                        echo '- '.$modifier['name'].'- $'.number_format(($modifier['price']/100),2)."<br/>";
                                                    else
                                                        echo '- '.$modifier['name']."<br/>";
                                                    $modifiers_price += $modifier['price'];
                                                }?>
                                            </p>
                                        </div>
                                        <div class="moo-product-price"><?php $line_price = $line['item']->price+$modifiers_price; echo number_format(($line_price/100),2)?></div>
                                        <div class="moo-product-quantity">
                                            <?php echo $line['quantity']?>
                                        </div>
                                        <div class="moo-product-line-price" style="width: 21%"><?php echo number_format(($line_price*$line['quantity']/100),2)?></div>
                                    </div>
                                <?php } ?>

                                <div class="moo-totals">
                                    <div class="moo-totals-item">
                                        <label>Subtotal</label>
                                        <div class="moo-totals-value" id="moo-cart-subtotal"><?php echo $total['sub_total']?></div>
                                    </div>
                                    <div class="moo-totals-item">
                                        <label>Tax</label>
                                        <div class="moo-totals-value" id="moo-cart-tax"><?php echo $total['total_of_taxes']?></div>
                                    </div>
                                    <div class="moo-totals-item">
                                        <label>Delivery fee</label>
                                        <div class="moo-totals-value" id="moo-cart-delivery-fee">0.00</div>
                                    </div>
                                    <?php if($merchantProprietes->tipsEnabled && $MooOptions['tips']=='enabled'){?>
                                    <div class="moo-totals-item">
                                        <label>Tip</label>
                                        <div class="moo-totals-value" id="moo-cart-tip">0.00</div>
                                    </div>
                                    <?php } ?>

                                    <!--                <div class="moo-totals-item">-->
                                    <!--                    <label>Shipping</label>-->
                                    <!--                    <div class="moo-totals-value" id="moo-cart-shipping">15.00</div>-->
                                    <!--                </div>-->
                                    <div class="moo-totals-item moo-totals-item-total">
                                        <label>Grand Total</label>
                                        <div class="moo-totals-value" id="moo-cart-total"><?php echo $total['total']?></div>
                                    </div>
                                </div>
                                <a href="<?php echo $cart_page_url?>" style="margin-bottom: 20px;color:blue">
                                    Shopping Cart
                                 </a>
                            </div>
                        </div>

                    </div>
            </div>
            <!-- Button finalize Order -->
            <div class="row" style="margin: 20px">
                <div class="col-md-12">
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
                    <button id="moo_btn_submit_order" type="submit" class="btn btn-primary"
                            style="display: block; width: 100%;">FINALIZE ORDER
                    </button>
                </div>
            </div>
        </form>
        </div>
    <?php
        if($custom_js != null)
            echo '<script type="text/javascript">'.$custom_js.'</script>';
        return ob_get_clean();
    }
    public  static function getItemsModifiers($item_uuid)
    {
            $model = new moo_OnlineOrders_Model();
            $item_uuid = esc_sql($item_uuid);
            $modifiersgroup = $model->getModifiersGroup($item_uuid);
	        $item = $model->getItem($item_uuid);
            ?>
            <div id="moo_modifiers">
                <!-- Nav tabs -->
<!--                <ul class="nav nav-tabs" role="tablist" style="margin: 0px;border-bottom-left-radius: 4px;border-bottom-right-radius: 4px;">-->
                    <?php
//                    $flag=true;
//                    foreach ($modifiersgroup as $mg) {
//	                    if(count($model->getModifiers($mg->uuid))==0) continue;
//                        if($flag)
//                            echo '<li role="presentation" class="active"><a href="#tab_'.$mg->uuid.'" aria-controls="home" role="tab" data-toggle="tab">'.$mg->name.'</a></li>';
//                        else
//                            echo '<li role="presentation"><a href="#tab_'.$mg->uuid.'" aria-controls="home" role="tab" data-toggle="tab">'.$mg->name.'</a></li>';
//                        $flag = false;
//                    }
                    ?>
<!--                </ul>-->
                <div class="panel panel-default" style="border-top: 0px">
                    <div class="panel-body">
                        <!-- Tab panes -->
                        <form id="moo_form_modifiers" method="post">
                            <div class="tab-content">
                                <?php
                                $flag=true;
                                foreach ($modifiersgroup as $mg) {
	                               if( count($model->getModifiers($mg->uuid))==0 ) continue;
/*
	                                if($flag)
                                        echo '<div role="tabpanel" class="tab-pane active" id="tab_'.$mg->uuid.'">';
                                    else
                                        echo '<div role="tabpanel" class="tab-pane" id="tab_'.$mg->uuid.'">';
                                    $flag = false;
*/
                                    ?>
                                   <h1 style="padding: 10px;"><?php echo $mg->name; ?></h1>
                                    <div  class="table-responsive" >
                                        <table class="table table-striped">
<!--                                            <thead>-->
<!--                                            <tr >-->
<!--                                                <th style="width: 50px;text-align: center;">Select</th>-->
<!--                                                <th>Name</th>-->
<!--                                                <th>Price</th>-->
<!--                                            </tr>-->
<!--                                            </thead>-->
                                            <tbody>
                                            <?php
                                            foreach ($model->getModifiers($mg->uuid) as $modifier) {
                                                if(strlen($modifier->name)>17 ) $name = substr($modifier->name,0,17).'...';
                                                else $name = $modifier->name;
                                                echo '<tr style="cursor:pointer;">';
                                                echo '<td style="width: 50px;text-align: center;"><input type="checkbox" name="moo_modifiers[\''.$item_uuid.'\',\''.$mg->uuid.'\',\''.$modifier->uuid.'\']" style="width: 25px;height: 25px;"/></td>';
                                                echo '<td onclick="clickLineInModifiersTab(this)">'.$name.'</td>';
                                                echo '<td onclick="clickLineInModifiersTab(this)">$'.($modifier->price/100).'</td>';
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
	                    <div style="text-align: center;padding: 10px">
		                   <?php echo '<div class="btn btn-primary" onclick="moo_addItemWithModifiersToCart(event,\''.$item->uuid.'\',\''.esc_sql($item->name).'\',\''.$item->price.'\')">ADD TO YOUR CART</div>'; ?>
	                    </div>
                    </div>
                </div>
            </div>
        <?php
    }

    /*
     *
     */
    public static function ItemsWithImages($atts,$content)
    {
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-Model.php";
        $model = new moo_OnlineOrders_Model();

        wp_enqueue_script( 'custom-script-accordion');
        wp_enqueue_script( 'jquery-accordion',array( 'jquery' ));

        wp_enqueue_script( 'magnific-modal', array( 'jquery' ) );
        wp_enqueue_style ( 'magnific-popup' );

        wp_enqueue_style ( 'custom-style-accordion' );
        wp_enqueue_script( 'custom-script-items' );
        wp_enqueue_style ( 'custom-style-items' );


        $cart_page_id  = get_option('moo_cart_page');
        $store_page_id = get_option('moo_store_page');

        if($cart_page_id === false)
        {
            $post_cart = array(
                'comment_status' => 'closed',
                'ping_status' =>  'publish' ,
                'post_name' => 'Cart',
                'post_status' => 'publish' ,
                'post_title' => 'Cart',
                'post_type' => 'page',
                'post_content' => '[moo_cart]'
            );

            $cart_page_id =  wp_insert_post( $post_cart );
            update_option( 'moo_cart_page', $cart_page_id );
        }
        $cart_page_url  =  get_page_link($cart_page_id);
        $store_page_url =  get_page_link($store_page_id);

        ob_start();
        if(isset($_GET['category'])){
            $category = esc_sql($_GET['category']);
            ?>
            <?php
            echo '<div class="row moo_items" id="Moo_ItemContainer">';
                if($category == 'NoCategory' || $category == "")
                {
                    $items_tab = $model->getItems();
                }
                else
                {
                    $cat = $model->getCategory($category);
                    $items = explode(',',$cat->items);
                    $items_tab = array();
                    foreach($items as $uuid_item)
                    {
                        if($uuid_item == "") continue;
                        array_push($items_tab,$model->getItem($uuid_item));
                    }
                }


            if(count($items_tab)<=0)  echo '<div class="col-md-12">"No items available.</div>';
            else
                foreach((array)$items_tab as $item)
                {
                    // $item = $model->getItem($uuid_item);
                    // Verify if the item is visible or not

                    if($item->visible == 0 || $item->hidden == 1 || $item->price_type == 'VARIABLE') continue;

                    $item_images = $model->getItemImages($item->uuid);
                    $no_image_url =  plugin_dir_url(dirname(__FILE__))."public/img/no-image.jpg";
                    $default_image = (count($item_images)==0)?$no_image_url:$item_images[0]->url;

                    $nb_modifiers = $model->itemHasModifiers($item->uuid)->total;
                    $item_name = $item->name;
                    $item_name = ucfirst(strtolower($item_name));
                    echo '<div class="col-md-4 col-sm-6 col-xs-12 moo_item_flip">';
                    echo '<a class="open-popup-link" href="#moo_popup_item_'.$item->uuid.'" onclick="moo_openFirstModifierG(\'MooModifierGroup_default_'.$item->uuid.'\')">';
                    echo "<div class='moo_item_flip_container'>";
                    echo "<div class='moo_item_flip_image'>";
                    echo "<img src='".$default_image."' style='height: 242px;'>";
                    echo "</div>";
                    echo "<div class='moo_item_flip_title'>".$item_name."</div>";
                    if($item->price_type == "PER_UNIT")
                        echo "<div class='moo_item_flip_content'>$".(number_format(($item->price/100),2,'.',''))." /".$item->unit_name."";
                    else
                        echo "<div class='moo_item_flip_content'>$".(number_format(($item->price/100),2,'.',''))."";
                    echo "<span class='center-span'></span></div>";
                    echo '</div></a></div>';

                    ?>
                    <div class="row white-popup mfp-hide" id="moo_popup_item_<?php echo $item->uuid?>">
                        <?php
                        if($nb_modifiers != "0")
                        {
                          ?>
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
                                        ?>
                                        <div class="moo_category">
                                            <div class="moo_accordion accordion-open" id="<?php echo ($nb_mg == 1)?'MooModifierGroup_default_'.$item->uuid:'MooModifierGroup_'.$mg->uuid?>">
                                                <div class="moo_category_title">
                                                    <div class="moo_title"><?php echo ($mg->alternate_name=="")?$mg->name:$mg->alternate_name; echo ($mg->min_required>=1)?' ( Required )':''; ?></div>
                                                    <span></span>
                                                </div>
                                            </div>
                                            <div class="moo_accordion_content moo_modifier-box2" style="display: none;">
                                                <ul>
                                                    <?php  foreach ( $modifiers as $m) {
                                                        ?>
                                                        <li>
                                                            <a href="#" onclick="moo_check(event,'<?php echo $m->uuid ?>')">
                                                                <div class="detail" >
                                                                                        <span class="moo_checkbox" >
                                                                                            <input type="checkbox" onclick="event.stopPropagation();" name="<?php echo 'moo_modifiers[\''.$item->uuid.'\',\''.$mg->uuid.'\',\''.$m->uuid.'\']' ?>" id="moo_checkbox_<?php echo $m->uuid ?>" />
                                                                                        </span>
                                                                    <p class="moo_label"><?php echo $m->name ?></p>
                                                                </div>
                                                                <div class="moo_price">
                                                                    $<?php echo number_format(($m->price/100), 2) ?>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <?php
                                                    }
                                                    if($mg->min_required != null || $mg->max_allowd != null ){
                                                        echo '<li class="Moo_modifiergroupMessage">';
                                                        if($mg->min_required==1 && $mg->max_allowd==1)
                                                            echo' Must choose 1 ';
                                                        else
                                                        {
                                                            if($mg->min_required != null && $mg->min_required != 0 ) echo 'Must choose at least '.$mg->min_required;
                                                            if($mg->max_allowd != null && $mg->max_allowd != 0 ) echo "<br/> Must choose  at max ".$mg->max_allowd;
                                                        }

                                                        echo '</li>';
                                                    }
                                                    ?>

                                                </ul>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </form>
                            </div>
                            <div class="col-md-5" id="moo_popup_leftSide">
                                <div class="moo_popup_title">
                                    <?php echo ucfirst(strtolower($item->name)) ?>
                                </div>
                                <div class="moo_popup_description">
                                    <?php echo $item->description ?>
                                </div>
                                <div class="moo_popup_price">
                                    $<?php echo (number_format(($item->price/100),2,'.','')) ?>
                                </div>
                                <div class="moo_popup_quantity">
                                    Quantity :
                                    <input type="number" class="form-control" value="1" id='moo_popup_quantity'>
                                </div>
                                <div class="moo_popup_special_instruction">
                                    Special Instructions :
                                    <textarea  class="form-control" name="" id="moo_popup_si" cols="30" rows="2"></textarea>
                                </div>
                                <div class="moo_popup_btns_action">
                                    <a href="#" class="btn btn-primary" onclick="moo_addItemWithModifiersToCart(event,'<?php echo trim($item->uuid) ?>','<?php echo trim($item->name) ?>','<?php echo trim($item->price) ?>')" >ADD TO CART</a>
                                    <a href="<?php echo $cart_page_url; ?>" class="btn btn-default">VIEW CART</a>
                                </div>
                            </div>
                            <?php
                        }
                        else
                        {

                        ?>
                        <div class="col-md-6" id="moo_popup_rightSide">
                            <div class="moo_popup_image">
                                <img src='<?php echo $default_image ?>'>
                            </div>
                        </div>
                        <div class="col-md-6" id="moo_popup_leftSide">
                            <div class="moo_popup_title">
                                <?php echo ucfirst(strtolower($item->name)) ?>
                            </div>
                            <div class="moo_popup_description">
                                <?php echo $item->description ?>
                            </div>
                            <div class="moo_popup_price">
                               $<?php echo (number_format(($item->price/100),2,'.','')) ?>
                            </div>
                            <div class="moo_popup_quantity">
                                Quantity :
                                <input type="number" class="form-control" value="1" id='moo_popup_quantity'>
                            </div>
                            <div class="moo_popup_special_instruction">
                                Special Instructions :
                                <textarea  class="form-control" name="" id="moo_popup_si" cols="30" rows="2">

                                </textarea>
                            </div>
                            <div class="moo_popup_btns_action">
                                <a href="#" class="btn btn-primary" onclick="moo_cartv3_addtocart('<?php echo trim($item->uuid) ?>','<?php echo str_replace("'",'',$item->name) ?>')">ADD TO CART</a>
                                <a href="<?php echo $cart_page_url; ?>" class="btn btn-default">VIEW CART</a>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php
                }



            echo '</div>';
            echo '<div class="row moo_items" align="center"><a style="margin-right:10px" class="btn btn-primary" href="'.$cart_page_url.'">View cart</a><a class="btn btn-default" href="'.$store_page_url.'">Back to Main Menu</a></div>';
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
                        echo "<h2 style='text-align: center'>You don't have any Item, please import your Items from Clover</h2>";
                    else
                    {
                        if(get_option("moo-show-allItems") == 'true')
                        {
                            array_push($categories,(object)array("name"=>'All Items',"uuid"=>'NoCategory'));
                        }

                        if(count($categories)>0)
                            foreach ( $categories as $category ){
                                if($category->uuid == 'NoCategory')
                                {
                                    $category_name = 'All Items';
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
                          //Redirect to the page No category
                            $location = (esc_url(add_query_arg('category', 'NoCategory',(get_page_link(get_option('moo_store_page'))))));
                            //echo $location;
                            wp_redirect ( $location );
                        }


                    }
                    ?>
                </div>
                <?php

            }
        ?>
        <div id="moo_cart">
            <a href="<?php echo get_page_link(get_option('moo_cart_page'));
            ?>">
                <div id="moo_cart_icon">
                    <span>VIEW SHOPPING CART</span>
                </div>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
    public static function TheStore($atts, $content)
    {
        $MooOptions = (array)get_option('moo_settings');
        $html_code  = '';

        $custom_css = $MooOptions["custom_css"];
        $custom_js  = $MooOptions["custom_js"];
        //Include custom css
        if($custom_css != null)
            $html_code .= '<style type="text/css">'.$custom_css.'</style>';

        $html_code .=  '<div id="moo_OnlineStoreContainer">';
        $style = $MooOptions["default_style"];
        if($style == "style1")
            $html_code .= self::AllItemsAcordion($atts, $content);
        else
            if($style == "style2")
                $html_code .= self::AllItems($atts, $content);
            else
                $html_code .= self::ItemsWithImages($atts, $content);

        $html_code .=  '<div class="row Moo_Copyright">Powered by <a href="http://merchantech.us" target="_blank">Merchantech apps</a></div>';

        //Include custom js
        if($custom_js != null)
            $html_code .= '<script type="text/javascript">'.$custom_js.'</script>';

        return $html_code;
    }
    public static function theCart($atts, $content)
    {
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-Model.php";
        $model = new moo_OnlineOrders_Model();

        wp_enqueue_style( 'custom-style-cart3');

        $store_page_id    = get_option('moo_store_page');
        $checkout_page_id = get_option('moo_checkout_page');

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
            echo '<div class="moo_emptycart"><p>Your cart is empty</p><span><a href="'.get_page_link(get_option('moo_store_page')).'">Browse the store</a></span></div>';
            return;
        };
    ?>
        <div class="moo-shopping-cart">
            <div class="moo-column-labels">
                <?php if($MooOptions['default_style']=='style3'){?>
                    <label class="moo-product-image">Image</label>
                <?php }?>
                <label class="moo-product-details"  <?php if($MooOptions['default_style']!='style3'){echo 'style="width:57%"';}?>>Product</label>
                <label class="moo-product-price">Price</label>
                <label class="moo-product-quantity">Qty</label>
                <label class="moo-product-removal">Remove</label>
                <label class="moo-product-line-price">Total</label>
            </div>
            <?php foreach ($_SESSION['items'] as $key=>$line) {
                $modifiers_price=0;
                $item_images = $model->getItemImages($line['item']->uuid);
                $no_image_url =  plugin_dir_url(dirname(__FILE__))."public/img/no-image.jpg";
                $default_image = (count($item_images)==0)?$no_image_url:$item_images[0]->url;
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
                        <?php foreach($line['modifiers'] as $modifier){
                            if($modifier['price']>0)
                                echo '- '.$modifier['name'].'- $'.number_format(($modifier['price']/100),2)."<br/>";
                            else
                                echo '- '.$modifier['name']."<br/>";
                            $modifiers_price += $modifier['price'];
                        }?>
                    </p>
                </div>
                <div class="moo-product-price"><?php $line_price = $line['item']->price+$modifiers_price; echo number_format(($line_price/100),2)?></div>
                <div class="moo-product-quantity">
                    <input type="number" value="<?php echo $line['quantity']?>" min="1" onchange="moo_updateQuantity(this,'<?php echo $key?>')">
                </div>
                <div class="moo-product-removal">
                    <a class="moo-remove-product" onclick="moo_removeItem(this,'<?php echo $key?>')">
                        Remove
                    </a>
                </div>
                <div class="moo-product-line-price"><?php echo number_format(($line_price*$line['quantity']/100),2)?></div>
            </div>
        <?php } ?>

            <div class="moo-totals">
                <div class="moo-totals-item">
                    <label>Subtotal</label>
                    <div class="moo-totals-value" id="moo-cart-subtotal"><?php echo $total['sub_total'] ?></div>
                </div>
                <div class="moo-totals-item">
                    <label>Tax</label>
                    <div class="moo-totals-value" id="moo-cart-tax"><?php echo $total['total_of_taxes'] ?></div>
                </div>
<!--                <div class="moo-totals-item">-->
<!--                    <label>Shipping</label>-->
<!--                    <div class="moo-totals-value" id="moo-cart-shipping">15.00</div>-->
<!--                </div>-->
                <div class="moo-totals-item moo-totals-item-total">
                    <label>Grand Total</label>
                    <div class="moo-totals-value" id="moo-cart-total"><?php echo $total['total'] ?></div>
                </div>
            </div>
            <a href="<?php echo $checkout_page_url?>" ><button class="moo-checkout">Checkout</button></a>
            <a href="<?php echo $store_page_url?>" ><button class="moo-continue-shopping">Continue shopping</button></a>

        </div>
        <?php
        if($custom_js != null)
            echo '<script type="text/javascript">'.$custom_js.'</script>';
        return ob_get_clean();
    }
    /*
     * This function is the callback of the Shortcode adding buy button,
     *
     * @since 1.0.6
     */
    public static function moo_BuyButton($atts, $content)
    {
        return 'Dans la prochaine version';
    }

}
