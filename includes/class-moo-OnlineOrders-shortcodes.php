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


        global $wpdb;
        //$wpdb->show_errors();

        wp_enqueue_script( 'custom-script-items' );
        wp_enqueue_style ( 'custom-style-items' );

        /*  if(isset($_POST)) echo "yes"; */

        if(isset($_GET['category'])){
            $category = esc_sql($_GET['category']);
            ?>
            <div class="row  moo_items" id="Moo_FileterContainer">
                <div class="col-md-3 col-sm-3 col-xs-5 ">
                    <!-- <label for="ListCats">Categories :</label> -->

                    <select id="ListCats" class="form-control" onchange="Moo_CategoryChanged(this)">
                        <?php
                        foreach ( $model->getCategories() as $cat ){
                            if(strlen($cat->items)<1) continue;
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

            self::getItemsHtml($category,'name','asc',null);


            echo '</div>';
            echo '<div class="row moo_items" align="center"><button class="btn btn-primary" onclick="javascript:window.history.back();">Back to categories</button></div>';
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
                    foreach ( $model->getCategories() as $category ){
                        if(strlen ($category->items)<1 ) continue;
                        if(strlen ($category->name)> 14)$category_name = substr($category->name, 0, 14)."...";
                        else  $category_name = $category->name;

                        echo '<div class="col-md-4 col-sm-6 col-xs-12 moo_category_flip" >';
                        echo "<a href='".(esc_url( add_query_arg( 'category', $category->uuid) ))."'><div class='moo_category_flip_container'>";
                        echo "<div class='moo_category_flip_title'>".ucfirst(strtolower($category_name))."</div>";
                        echo "<div class='moo_category_flip_content' style='background-color: ".current($colors)."'></div>";
                        echo '</div></a>';
                        echo '</div>';
                        if(!next($colors)) reset($colors);
                    }

                    ?>
                </div>
            <?php

            }

    }
	/**
	 * This ShortCode display the store using the first style
	 * @since    1.0.0
	 */
	public static function AllItemsAcordion($atts, $content)
    {
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-Model.php";
        $model = new moo_OnlineOrders_Model();


        global $wpdb;
        //$wpdb->show_errors();

        wp_enqueue_script( 'custom-script-accordion');
        wp_enqueue_script( 'jquery-accordion',array( 'jquery' ));
        wp_enqueue_script( 'simple-modal',array( 'jquery' ));
        wp_enqueue_script( 'magnific-modal', array( 'jquery' ) );

        wp_enqueue_style ( 'custom-style-accordion' );
        wp_enqueue_style ( 'simple-modal' );
        wp_enqueue_style ( 'magnific-popup' );
                ?>
                <div class="col-xs-12 hidden-md hidden-lg hidden-sm MooGoToCart">
                    <a href="#ViewShoppingCart">VIEW SHOPPING CART</a>
                </div>
                <div class="row MooStyleAccorfion">
                <div class="col-md-7" style="margin-bottom: 20px;">
                <?php
                    foreach ( $model->getCategories() as $category ){

	                    // I verify if there is some itmes in the category
	                    // and the length of the name then I cut if it more than 30 characters
                        if(strlen ($category->items)< 1 ) continue;
                        if(strlen ($category->name) > 30)$category_name = substr($category->name, 0, 30)."...";
                        else  $category_name = $category->name;
                ?>

                        <div class="moo_category">
                            <div class="moo_accordion" id="MooCat_<?php echo $category->uuid?>">
                                <div class="moo_category_title">
                                    <div class="title"><?php echo ucfirst(strtolower($category_name))?></div>
                                    <span></span>
                                </div>
                            </div>
                            <div class="moo_accordion_content">
                                <ul>
                                    <?php
                                    $items = explode(',',$category->items);
                                    foreach($items as $uuid_item)
                                    {
                                        if($uuid_item =="") continue;
                                        $item = $model->getItem($uuid_item);
                                        if($item)
                                        {
                                            if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE' || $item->price == 0) continue;
                                            echo '<li>';
                                            if(($model->itemHasModifiers($item->uuid)->total) != "0")
                                                echo '<a class="popup-text" href="#Moo_ItemWithModifier" onclick="ItemHasModifiers(this,event,\''.$item->uuid.'\',\''.esc_sql($item->name).'\',\''.$item->price.'\')">';
                                            else
                                                echo '<a href="#" onclick="moo_addToCart(event,\''.$item->uuid.'\',\''.esc_sql($item->name).'\',\''.$item->price.'\')">';
                                            echo '  <div class="detail">'.$item->name.'</div>';
                                            echo '  <div class="price">$'.(number_format(($item->price/100),2,'.','')).'</div>';
                                            echo '</a>';
                                            echo '</li>';
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
                                'post_date' => date('Y-m-d H:i:s'),
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
	    <div id="Moo_ItemWithModifier" class="white-popup mfp-hide">
			<p id="Moo_ItemWithModifierContainer">
				Loading ...
			</p>
	    </div>
                <?php
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
        /*
         * return array(
            0=>"#1abc9c",1=>"#e67e22",2=>"#3498db",3=>"#9b59b6",4=>"#34495e",5=>"#16a085",6=>"#27ae60",7=>"#2980b9",8=>"#8e44ad",
            9=>"#354b60",10=>"#c0392b",11=>"#2ecc71",12=>"#e74c3c",13=>"#f39c12",14=>"#7f8c8d",15=>"#d35400"
                    );
        */
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
        $model = new moo_OnlineOrders_Model();

        if($search!=null)
        {
            $search = esc_sql($search);
            $search = sanitize_text_field($search);

            $items_tab = $model->getItemsBySearch($search);
        }
        else
        {
            $cat = $model->getCategory($category);
            $items = explode(',',$cat->items);
            $items_tab=array();
            foreach($items as $uuid_item)
            {
                if($uuid_item =="") continue;
                array_push($items_tab,$model->getItem($uuid_item));
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

                if($item->visible == 0 || $item->hidden == 1 || $item->price_type=='VARIABLE' || $item->price == 0) continue;

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
    }
    public static function checkoutPage($atts, $content)
    {
        wp_enqueue_script( 'custom-script-checkout' );

        $model = new moo_OnlineOrders_Model();
        $orderTypes = $model->getVisibleOrderTypes();
        $total =   Moo_OnlineOrders_Public::moo_cart_getTotal_IQ();
        $firstTotal = $total['total'];

       if($total === false){
           echo 'Your Cart is empty';
           return;
       };

        wp_localize_script("custom-script-checkout", "moo_OrderTypes",$orderTypes);
        wp_localize_script("custom-script-checkout", "moo_Total",$total);
        ?>
        <form id="moo_form_address" method="post" action="#" novalidate="novalidate">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <p style="font-size: 16px !important; margin:0;">Personal infos</p>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
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
                                <div class="form-group">
                                    <label for="name">Name:</label><input class="form-control" name="name" id="name"></div>
                                <div class="form-group"><label for="phone">Phone number:</label>
                                    <input class="form-control" name="phone" id="phone"></div>
                                <div class="form-group"><label for="email">Email address:</label>
                                    <input class="form-control" name="email" id="email"></div>
                                <div class="form-group"><label for="address">Address:</label>
                                    <input class="form-control" name="address" id="address"></div>

                            </div>
                            <div class="col-md-6">

                                        <div class="form-group"><label for="city">City:</label>
                                            <input class="form-control" name="city" id="city"></div>
                                        <div class="form-group"><label for="zipcode">Zip code:</label>
                                            <input class="form-control" name="zipcode" id="zipcode"></div>
                                        <div class="form-group"><label for="instructions">Special instructions</label>
                                            <textarea rows="9" class="form-control" name="instructions" id="instructions"></textarea></div>
                                </div>
                        </div>
                    </div>
                </div>
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
                                        <img style="min-width:116px;height: 20px;" class="moo_credit_cards hidden-xs">
                                    </div>
                                 </div>


                                <label for="Moo_cardNumber" class="error" style="display: none;"></label></div>
                            <label for="expiredDate" >Expired date:</label>
                            <div class="form-group row">

                                <div class="col-md-6 col-xs-7 col-sm-7"><select name="expiredDateMonth" id="expiredDate" class="form-control">
                                        <option value="1">January</option>
                                        <option value="2">February</option>
                                        <option value="3">March</option>
                                        <option value="4">April</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">August</option>
                                        <option value="9">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                                <div class="col-md-6  col-xs-5 col-sm-5">
                                    <select name="expiredDateYear" class="form-control">
                                        <option value="2016">2016</option>
                                        <option value="2017">2017</option>
                                        <option value="2018">2018</option>
                                        <option value="2019">2019</option>
                                        <option value="2020">2020</option>
                                        <option value="2021">2021</option>
                                        <option value="2022">2022</option>
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2024">2025</option>
                                        <option value="2024">2026</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 13px;">
                                <div class="col-md-4"><label for="moo_cardcvv">CVV:</label></div>
                                <div class="col-md-8"><input class="form-control" name="cvv" id="moo_cardcvv"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 col-sm-8 col-xs-8"><h1
                                style="text-align: right; font-size: 20px !important;">Total :</h1></div>
                        <div class="col-md-4 col-sm-4 col-xs-4">
                            <h1 style="font-size: 20px !important;" id="moo_Total_inCheckout">$<?php echo $firstTotal?></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
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
                    <button id="moo_btn_submit_order" type="submit" class="btn btn-lg btn-primary"
                            style="display: block; width: 100%;">FINALIZE ORDER
                    </button>
                </div>
            </div>
        </form>
    <?php
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
                <ul class="nav nav-tabs" role="tablist" style="margin: 0px;border-bottom-left-radius: 4px;border-bottom-right-radius: 4px;">
                    <?php
                    $flag=true;
                    foreach ($modifiersgroup as $mg) {
	                    if(count($model->getModifiers($mg->uuid))==0) continue;
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
	                               if(count($model->getModifiers($mg->uuid))==0) continue;

	                                if($flag)
                                        echo '<div role="tabpanel" class="tab-pane active" id="tab_'.$mg->uuid.'">';
                                    else
                                        echo '<div role="tabpanel" class="tab-pane" id="tab_'.$mg->uuid.'">';
                                    $flag = false;
                                    ?>

                                    <div  class="table-responsive" style="height: 235px;">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr >
                                                <th style="width: 50px;text-align: center;">Select</th>
                                                <th>Name</th>
                                                <th>Price</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($model->getModifiers($mg->uuid) as $modifier) {
                                                echo '<tr style="cursor:pointer;">';
                                                echo '<td style="width: 50px;text-align: center;"><input type="checkbox" name="moo_modifiers[\''.$item_uuid.'\',\''.$mg->uuid.'\',\''.$modifier->uuid.'\']" style="width: 25px;height: 25px;"/></td>';
                                                echo '<td onclick="clickLineInModifiersTab(this)">'.$modifier->name.'</td>';
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
		                   <?php echo '<div class="btn btn-primary" onclick=" moo_addItemWithModifiersToCart(event,\''.$item->uuid.'\',\''.esc_sql($item->name).'\',\''.$item->price.'\')">ADD TO YOUR CART</div>'; ?>
	                    </div>
                    </div>
                </div>
            </div>
        <?php
    }
    public static function TheStore($atts, $content)
    {
        $MooOptions = (array)get_option('moo_settings');
        $style = $MooOptions["default_style"];

        if($style == "style1")
            self::AllItemsAcordion($atts, $content);
        else
            self::AllItems($atts, $content);
            ?>
            <div class="row Moo_Copyright">
                Powered by <a href="http://merchantech.us" target="_blank">Merchantech</a>
            </div>
            <?php
    }


}
