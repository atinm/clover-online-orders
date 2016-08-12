<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://merchantech.us
 * @since      1.0.0
 *
 * @package    Moo_OnlineOrders
 * @subpackage Moo_OnlineOrders/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Moo_OnlineOrders
 * @subpackage Moo_OnlineOrders/public
 * @author     Mohammed EL BANYAOUI
 */
class Moo_OnlineOrders_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
	 * The model of this plugin (For all interaction with the DATABASE ).
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Object    $model    Object of functions that call the Database pr the API.
	 */
	private $model;
    private $api;
    private $style;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-Model.php";
        require_once plugin_dir_path( dirname(__FILE__))."models/moo-OnlineOrders-CallAPI.php";
        $MooOptions = (array)get_option('moo_settings');

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->model       = new moo_OnlineOrders_Model();
		$this->api         = new moo_OnlineOrders_CallAPI();
		$this->style       = $MooOptions["default_style"];

	}
    /**
     * Start the session
     *
     * @since    1.0.0
     */
    public function myStartSession() {
        if(!session_id()) {
            session_start();
        }
    }
    /**
     * do_output_buffer
     *
     * @since    1.0.0
     */
    public function do_output_buffer() {
        ob_start();
    }
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {


        wp_register_style( 'bootstrap-css',plugins_url( '/css/bootstrap.min.css', __FILE__ ));
        wp_enqueue_style( 'bootstrap-css' );

        wp_register_style( 'font-awesome',plugins_url( '/css/font-awesome.css', __FILE__ ));
        wp_enqueue_style( 'font-awesome' );

        wp_register_style( 'toastr-css',plugins_url( '/css/toastr.css', __FILE__ ));
        wp_enqueue_style( 'toastr-css' );

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/moo-OnlineOrders-public.css', array(), $this->version, 'all' );

        wp_register_style( 'custom-style-cart3', plugins_url( '/css/custom_style_cart3.css', __FILE__ ),'bootstrap-min' );


        if($this->style == "style1"){
            wp_register_style( 'custom-style-accordion', plugins_url( '/css/custom_style_accordion.css', __FILE__ ),'bootstrap-min' );
            wp_register_style( 'simple-modal', plugins_url( '/css/simplemodal.css', __FILE__ ),'bootstrap-min' );
            wp_register_style( 'magnific-popup', plugins_url( '/css/magnific-popup.css', __FILE__ ));

        }
        else
            if($this->style == "style2")
            {
                wp_register_style( 'custom-style-items', plugins_url( '/css/items.css', __FILE__ ),'bootstrap-min' );
            }
            else
            {
                wp_register_style( 'custom-style-accordion', plugins_url( '/css/custom_style_accordion.css', __FILE__ ),'bootstrap-min' );
                wp_register_style( 'custom-style-items', plugins_url( '/css/items-style3.css', __FILE__ ),'bootstrap-min' );
                wp_register_style( 'magnific-popup', plugins_url( '/css/magnific-popup.css', __FILE__ ));
            }






	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

            wp_enqueue_script( 'jquery' );
            $MooOptions = (array)get_option('moo_settings');

            $params = array(
                'ajaxurl' => admin_url( 'admin-ajax.php', isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' ),
                'plugin_img' =>  plugins_url( '/img', __FILE__ )
            );

            // Register the script like this for a plugin:
            wp_register_script('bootstrap-js', plugins_url( '/js/bootstrap.min.js', __FILE__ ));
            wp_enqueue_script('bootstrap-js',array('jquery'));

            wp_register_script('toastr-js', plugins_url( '/js/toastr.min.js', __FILE__ ));
            wp_enqueue_script('toastr-js',array('jquery'));

            wp_register_script('custom-script-checkout', plugins_url( '/js/moo_checkout.js', __FILE__ ));
            wp_register_script('display-merchant-map', plugins_url( '/js/moo_map.js', __FILE__ ));
            wp_register_script('moo-google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBv1TkdxvWkbFaDz2r0Yx7xvlNKe-2uyRc');
            wp_register_script('forge', plugins_url( '/js/forge.min.js', __FILE__ ));

            wp_register_script('moo_public_js',  plugins_url( 'js/moo-OnlineOrders-public.js', __FILE__ ));
		    wp_enqueue_script('moo_public_js', array( 'jquery' ));

            wp_register_script('script-cart-v3', plugins_url( '/js/cart_v3.js', __FILE__ ));
            wp_enqueue_script('script-cart-v3', array( 'jquery' ));

            if($this->style == "style1"){
                wp_register_script('custom-script-accordion', plugins_url( '/js/custom_script_store_accordion.js', __FILE__ ));
                wp_register_script('simple-modal', plugins_url( '/js/simple-modal.js', __FILE__ ));
                wp_register_script('magnific-modal', plugins_url( '/js/magnific.min.js', __FILE__ ));
                wp_register_script('jquery-accordion', plugins_url( '/js/jquery.accordion.js', __FILE__ ));

                wp_register_script('script-cart-v2', plugins_url( '/js/cart_v2.js', __FILE__ ));
                wp_enqueue_script('script-cart-v2', array( 'jquery' ));
            }
            else
                if($this->style == "style2")
                {
                    wp_register_script('custom-script-items', plugins_url( '/js/items.js', __FILE__ ));
                    wp_register_script('script-cart-v1', plugins_url( '/js/cart_v1.js', __FILE__ ));
                    wp_enqueue_script('script-cart-v1', array( 'jquery' ));
                }
                else
                {
                    wp_register_script('custom-script-accordion', plugins_url( '/js/custom_script_store_accordion.js', __FILE__ ));
                    wp_register_script('jquery-accordion', plugins_url( '/js/jquery.accordion.js', __FILE__ ));
                    wp_register_script('magnific-modal', plugins_url( '/js/magnific.min.js', __FILE__ ));
                    wp_register_script('custom-script-items', plugins_url( '/js/custom-script-style3.js', __FILE__ ));
                }

            wp_register_script('moo_validate_forms',  plugins_url( 'js/jquery.validate.min.js', __FILE__ ));
		    wp_enqueue_script('moo_validate_forms', array( 'jquery' ));

            wp_register_script('moo_validate_payment',  plugins_url( 'js/jquery.payment.min.js', __FILE__ ));
		    wp_enqueue_script('moo_validate_payment', array( 'jquery' ));

            wp_localize_script("moo_public_js", "moo_params",$params);
	}
    /**
	 * Add the cart Button to the Website
	 *
	 * @since    1.0.0
	 */
	public function addCartButton() {
     if($this->style == "style2"){
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
        <div id="moo_cart">
            <div id="moo_cart_icon" data-toggle="modal" data-target=".moo-cart-modal-lg" onclick="moo_updateCart()">
                <span>VIEW SHOPPING CART</span>
            </div>
        </div>
        <!-- Large modal -->
        <div class="modal fade moo-cart-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="z-index:1000000000">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></div>
                        <h4 class="modal-title">Your Cart</h4>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <div class="btn btn-default" style="float: left;" onclick="moo_emptyCart()"><i class="fa fa-times"></i> Empty Cart</div>
                        <div class="btn btn-primary hidden-xs" style="float: left;" onclick="moo_updateCart()"><i class="fa fa-refresh"></i> Update Cart</div>
                        <div class="btn btn-default" data-dismiss="modal">Close</div>
                        <a class="btn btn-primary" href="<?php echo esc_url($checkout_page_url)?>">Checkout</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
	}
  }

// AJAX Responses

    /**
     * Add to Cart
     * @since    1.0.0
     */
    public function moo_add_to_cart($item_key) {

        //TODO : Security
        if(isset($_POST['item']) & !empty($_POST['item'])) $item_uuid = $_POST['item'];
        else
        {
            $item_uuid = explode("__",$item_key);
            $item_uuid = $item_uuid[0];
        }

        $item = $this->model->getItem($item_uuid);
        if($item){
            if(isset($_POST['item']) & !empty($_POST['item']) )
                if(isset($_SESSION['items']) && array_key_exists($item_uuid,$_SESSION['items']) ){
                   $_SESSION['items'][$item_uuid]['quantity']++;
                }
                else
                    $_SESSION['items'][$item_uuid] = array(
                                                            'item'=>$item,
                                                            'quantity'=>1,
                                                            'special_ins'=>'',
                                                            'tax_rate'=>$this->model->getItemTax_rate( $item_uuid),
                                                            'modifiers'=>array()
                                                        );

            else
            {
                if(isset($_SESSION['items']) && array_key_exists($item_key,$_SESSION['items']) ){
                     $_SESSION['items'][$item_key]['quantity']++;
                }
                else
                    $_SESSION['items'][$item_key] = array(
                                                            'item'=>$item,
                                                            'quantity'=>1,
                                                            'special_ins'=>'',
                                                            'tax_rate'=>$this->model->getItemTax_rate($item_uuid),
                                                            'modifiers'=>array()
                                                          );
                }
            $response = array(
                'status'	=> 'success'
            );

        }
        else
        {
            $response = array(
                'status'	=> 'error'
            );

        }
        if(isset($_POST['item']) & !empty($_POST['item']))
            wp_send_json($response);
        else
            return $response;
    }

    /**
     * Get the Cart
     * @since    1.0.0
     */
    public function moo_get_cart() {
        if(isset($_SESSION['items']) && !empty($_SESSION['items'])){
            $response = array(
                'status'	=> 'success',
                'data'   => $_SESSION['items']
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Your cart is empty'
            );
            wp_send_json($response);
        }

    }
    /**
     * Update the quantity
     * @since    1.0.0
     */
    public function moo_UpdateQuantity() {

        $item_uuid = sanitize_text_field($_POST['item']);
        $item_qte= absint($_POST['qte']);

        if(isset($_SESSION['items'][$item_uuid]) && !empty($_SESSION['items'][$item_uuid]) && $item_qte>0){
            $_SESSION['items'][$item_uuid]['quantity'] = $item_qte ;
            if( $_SESSION['items'][$item_uuid]['quantity']<1 )  $_SESSION['items'][$item_uuid]['quantity'] = 1;
            $response = array(
                'status'	=> 'success',
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Item not found'
            );
            wp_send_json($response);
        }

    }
    /**
     * Update the Special Instruction for one item
     * @since    1.0.6
     */
    public function moo_UpdateSpecial_ins() {

        $item_uuid   = sanitize_text_field($_POST['item']);
        $special_ins = sanitize_text_field($_POST['special_ins']);

        if(isset($_SESSION['items'][$item_uuid]) && !empty($_SESSION['items'][$item_uuid])){
            $_SESSION['items'][$item_uuid]['special_ins'] = $special_ins ;
            $response = array(
                'status'	=> 'success',
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Item not found'
            );
            wp_send_json($response);
        }

    }
    /**
     * Get More options for an item in the cart
     * @since    1.0.6
     */
    public function moo_GetitemInCartOptions() {

        $item_uuid   = sanitize_text_field($_POST['item']);

        if(isset($_SESSION['items'][$item_uuid]) && !empty($_SESSION['items'][$item_uuid])){
            $special_ins = $_SESSION['items'][$item_uuid]['special_ins'];
            $qte = $_SESSION['items'][$item_uuid]['quantity'];
            $response = array(
                'status'	=> 'success',
                'special_ins'	=> $special_ins,
                'quantity'	=> $qte
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Item not found'
            );
            wp_send_json($response);
        }

    }
    /**
     * Inc the quantity
     * @since    1.0.0
     */
    public function moo_cart_incQuantity() {
        $item_uuid = sanitize_text_field($_POST['item']);
        if(isset($_SESSION['items'][$item_uuid]) && !empty($_SESSION['items'][$item_uuid])){
            $_SESSION['items'][$item_uuid]['quantity']++;
            $response = array(
                'status'	=> 'success',
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Item not found'
            );
            wp_send_json($response);
        }

    }
    /**
     * Dec the quantity
     * @since    1.0.0
     */
    public function moo_cart_decQuantity() {
        $item_uuid = sanitize_text_field($_POST['item']);
        if(isset($_SESSION['items'][$item_uuid]) && !empty($_SESSION['items'][$item_uuid])){

            $_SESSION['items'][$item_uuid]['quantity']--;

            if( $_SESSION['items'][$item_uuid]['quantity']<1)  $_SESSION['items'][$item_uuid]['quantity'] = 1;
            $response = array(
                'status'	=> 'success',
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Item not found'
            );
            wp_send_json($response);
        }

    }
    /**
     * Delete Item from the cart
     * @since    1.0.0
     */
    public function moo_deleteItemFromcart()
    {
        $item_uuid = sanitize_text_field($_POST['item']);
        if(isset($_SESSION['items'][$item_uuid]) && !empty($_SESSION['items'][$item_uuid])){
            unset($_SESSION['items'][$item_uuid]);
            $response = array(
                'status'	=> 'success',
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Not exist'
            );
            wp_send_json($response);
        }

    }
    /**
     * Delete Item from the cart
     * @since    1.0.0
     */
    public function moo_emptycart()
    {
            unset($_SESSION['items']);
            $response = array(
                'status'	=> 'success'
            );
            wp_send_json($response);

    }

    /**
     * Delete Modifier from the cart
     * @since    1.0.0
     */
    public function moo_cart_DeleteItemModifier()
    {
        $item_uuid     = sanitize_text_field($_POST['item']);
        $modifier_uuid = sanitize_text_field($_POST['modifier']);
        if(isset($_SESSION['items'][$item_uuid]['modifiers'][$modifier_uuid]) && !empty($_SESSION['items'][$item_uuid]['modifiers'][$modifier_uuid])){
            unset($_SESSION['items'][$item_uuid]['modifiers'][$modifier_uuid]);

            //Generate the new Key
            $pos = strrpos($item_uuid, "__");
            if($pos){
                $item_key = explode('__',$item_uuid);
                $item_key = $item_key[0].'_';
                foreach ($_SESSION['items'][$item_uuid]['modifiers'] as $modifier) $item_key .= '_'.$modifier['uuid'];
            }

            $nbModifiers = count($_SESSION['items'][$item_uuid]['modifiers']);
            $last = ($nbModifiers>0)?false:true;
            $response = array(
                'status'	=> 'success',
                'last'	=> $last
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Not exist'
            );
            wp_send_json($response);
        }

    }

    /**
     * Get the total
     * @since    1.0.0
     */
    public static function moo_cart_getTotal($internal)
    {
        if(isset($_SESSION['items']) && !empty($_SESSION['items'])){
            $sub_total = 0;
            $total_of_taxes = 0;
            $taxe_rates_groupping = array();
            $allTaxesRates = array();
            foreach ($_SESSION['items'] as $item) {
                //Grouping taxe rates
                foreach ($item['tax_rate'] as $tr) {
                    if(isset($taxe_rates_groupping[$tr->uuid])) array_push($taxe_rates_groupping[$tr->uuid],$item);
                    else{
                        $taxe_rates_groupping[$tr->uuid] = array();
                        array_push($taxe_rates_groupping[$tr->uuid],$item);
                        $allTaxesRates[$tr->uuid]=$tr->rate;
                    }
                }
                $price = $item['item']->price *  $item['quantity'];
                $price = $price/100;
                $sub_total += $price;
                if(count($item['modifiers'])>0){
                    foreach ($item['modifiers'] as $m) {
                        $m_price = $item['quantity'] * $m['price'];
                        $sub_total += $m_price/100;
                    }
                }
            }

            //calculate taxes
            foreach ($taxe_rates_groupping as $tax_rate_uuid=>$items) {
                $taxes=0;
                $tax_rate = $allTaxesRates[$tax_rate_uuid];
                if($tax_rate == 0) continue;
                foreach ($items as $item) {
                        $lineSubtotal = $item['item']->price * $item['quantity'];
                        if(count($item['modifiers'])>0){
                            foreach ($item['modifiers'] as $m) {
                                $m_price = $item['quantity'] * $m['price'];
                                $lineSubtotal += $m_price;
                            }
                        }
                        $line_taxes = $tax_rate/100000 * $lineSubtotal/10000;

                        $taxes += $line_taxes;
                }
               // var_dump($taxes);
                $total_of_taxes += round($taxes,2,PHP_ROUND_HALF_UP);

            }

            $FinalSubTotal = round($sub_total,2,PHP_ROUND_HALF_UP);
            $FinalTaxTotal = round($total_of_taxes,2,PHP_ROUND_HALF_UP);

            $response = array(
                'status'	        => 'success',
                'sub_total'      	=> number_format($FinalSubTotal,2),
                'total_of_taxes'	=> number_format($FinalTaxTotal,2),
                'total'	            => number_format(($FinalSubTotal+$FinalTaxTotal),2)
            );
            if(!$internal)
              wp_send_json($response);
            else
                return $response;


        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Not exist'
            );

            if(!$internal)
                wp_send_json($response);
            else
                return false;
        }

    }
	/**
     * Get the total of an Item
     * @since    1.0.0
     */
    public function moo_cart_getItemTotal()
    {
        $item_uuid = sanitize_text_field($_POST['item']);

        if(isset($_SESSION['items'][$item_uuid]) && !empty($_SESSION['items'][$item_uuid])){
            $sub_total = 0;
            $total_of_taxes = 0;
            $item = $_SESSION['items'][$item_uuid];

            $price = $item['item']->price * $item['quantity'];
            $sub_total += $price;
            $total_of_taxes += $item['tax_rate'] * $price / 100;

            if(count($item['modifiers'])>0){
                foreach ($item['modifiers'] as $m) {
                    $m_price = $item['quantity'] * $m['price'];
                    $sub_total += $m_price;
                    $total_of_taxes += $item['tax_rate'] * $m_price / 100;
                }
            }
            $sub_total = $sub_total/100; // Conversion to dollar
            $total_of_taxes = $total_of_taxes/100; // Conversion to dollar

            $response = array(
                'status'	=> 'success',
                'sub_total'	=> round($sub_total, 2),
                'total_of_taxes'	=> round($total_of_taxes, 2),
                'total'	=> round(($total_of_taxes+$sub_total), 2)
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Not exist'
            );
            wp_send_json($response);
        }

    }

    /**
     * Modifiers Group : get limits
     * @since    1.0.0
     */
    public function moo_modifiergroup_getlimits()
    {

        $mg_uuid = sanitize_text_field($_POST['modifierGroup']);

        $res = $this->model->getModifiersGroupLimits($mg_uuid);
        if($res){
            $response = array(
                'status'	=> 'success',
                'uuid'	=> $mg_uuid,
                'max'	=> $res->max_allowd,
                'min'	=> $res->min_required,
                'name'	=> $res->name
            );
            wp_send_json($response);
        }


    }
    /**
     * Modifiers Group : check if an item require modifiergroups to be selected
     * @since    1.1.6
     */
    public function moo_checkItemModifiers()
    {
        $mg_required = '';
        $item_uuid = sanitize_text_field($_POST['item']);
        $res = $this->model->getItemModifiersGroupsRequired($item_uuid);
        foreach ($res as $i)
        {
           $mg_required .= $i->uuid.';';
        }
        $response = array(
            'status'	=> 'success',
            'uuids'	=> $mg_required
        );
        wp_send_json($response);
    }

    /**
     * Modifiers : add a modifier to the cart
     * @since    1.0.0
     */
    public function moo_modifier_add()
    {

        $modifiers = $_POST['modifiers'];
        $flag = false;
        if(count($modifiers)>0){
            $iem_uuid =  $modifiers[0]['item'].'_';
            foreach ($modifiers as $modifier) $iem_uuid .= '_'.$modifier['modifier'];

            $this->moo_add_to_cart($iem_uuid);

            foreach ($modifiers as $modifier) {
                $modifier_uuid = $modifier['modifier'];
                $modifierInfos = $this->model->getModifier($modifier_uuid);

                $_SESSION['items'][$iem_uuid]['modifiers'][$modifier_uuid] = (array)$modifierInfos;
            }
        }


        //$res = $model->getModifiersGroupLimits($mg_uuid);

        if(true){
            $response = array(
                'status'	=> 'success',
            );
            wp_send_json($response);
        }

    }

    /*
     * Checkout
     */
    public function moo_checkout()
    {
        //var_dump($_POST);

        if(isset($_POST)){
            if(isset($_SESSION) && !empty($_SESSION['items']))
            {
                $deliveryFee    = 0;
                $tipAmount      = 0;
                $shippingFee    = 0;

                $customer_lat = sanitize_text_field($_POST['moo_customer_lat']);
                $customer_lng = sanitize_text_field($_POST['moo_customer_lat']);
                
                if(isset($_POST['form']['tip']) && $_POST['form']['tip'] > 0 )
                    $tipAmount    = $_POST['form']['tip'];

                if(isset($_POST['form']['moo_delivery_amount']) && $_POST['form']['moo_delivery_amount'] > 0 )
                    $deliveryFee    = $_POST['form']['moo_delivery_amount'];

                if(!empty($_POST['form']['OrderType'])){
                    $OrderTpe_UUID = sanitize_text_field($_POST['form']['OrderType']);
                    $orderType = $this->api->GetOneOrdersTypes($OrderTpe_UUID);
                    $orderCreated = $this->moo_CreateOrder($OrderTpe_UUID,json_decode($orderType)->taxable,$deliveryFee);
                }
                else  $orderCreated = $this->moo_CreateOrder('default',true,$deliveryFee);

                if($orderCreated != false)
                {
                    $this->model->addOrder($orderCreated['OrderId'],$orderCreated['taxamount'],$orderCreated['amount'],$_POST['form']['name'],$_POST['form']['address'], $_POST['form']['city'],$_POST['form']['zipcode'],$_POST['form']['phone'],$_POST['form']['email'],$_POST['form']['instructions'],$_POST['form']['state'],$_POST['form']['country'],$deliveryFee,$tipAmount,$shippingFee,$customer_lat,$customer_lng,json_decode($orderType)->label);
                    $this->model->addLinesOrder($orderCreated['OrderId'],$_SESSION['items']);

                    if( !empty($_POST['form']['cardNumber']) && !empty($_POST['form']['cvv']) && !empty($_POST['form']['expiredDateMonth'])
                        && !empty($_POST['form']['expiredDateYear']) && !empty($_POST['form']['zipcode']))
                    {
                        if($orderCreated['taxable'])
                            $paid = $this->moo_PayOrder($_POST['form']['cardEncrypted'],$_POST['form']['cardNumber'],$_POST['form']['cvv'],$_POST['form']['expiredDateMonth'],$_POST['form']['expiredDateYear'],
                            $orderCreated['OrderId'],$orderCreated['amount'],$orderCreated['taxamount'],$_POST['form']['zipcode'],$tipAmount);
                        else
                            $paid = $this->moo_PayOrder($_POST['form']['cardEncrypted'],$_POST['form']['cardNumber'],$_POST['form']['cvv'],$_POST['form']['expiredDateMonth'],$_POST['form']['expiredDateYear'],
                            $orderCreated['OrderId'],$orderCreated['sub_total'],'0',$_POST['form']['zipcode'],$tipAmount);
                        $response = array(
                            'status'	=> json_decode($paid)->result,
                            'order'	=> $orderCreated['OrderId']
                        );
                        if($response['status'] == 'APPROVED'){

                            $MooOptions = (array)get_option('moo_settings');

                            $customer = array(
                                "name"    =>(isset($_POST['form']['name']))?$_POST['form']['name']:"",
                                "address" =>(isset($_POST['form']['address']))?$_POST['form']['address']:"",
                                "city"    =>(isset($_POST['form']['city']))?$_POST['form']['city']:"",
                                "state"    =>(isset($_POST['form']['state']))?$_POST['form']['state']:"",
                                "country"    =>(isset($_POST['form']['country']))?$_POST['form']['country']:"",
                                "zipcode" =>(isset($_POST['form']['zipcode']))?$_POST['form']['zipcode']:"",
                                "phone"   =>(isset($_POST['form']['phone']))?$_POST['form']['phone']:"",
                                "email"   =>(isset($_POST['form']['email']))?$_POST['form']['email']:"",
                                "lat"   =>$customer_lat,
                                "lng"   =>$customer_lng,
                            );

                            $this->model->updateOrder($orderCreated['OrderId'],json_decode($paid)->paymentId);
                            $this->api->NotifyMerchant($orderCreated['OrderId'],$_POST['form']['instructions'],$customer);

                            $otherInformations = "";

                            /* if you have additional info please set-up it in this section */



                            /* End section additional Infos */

                            $this->sendEmail($_POST['form']['email'],$_POST['form']['name'],$orderCreated['OrderId']);
                            $this->sendEmail2merchant($MooOptions['merchant_email'],$orderCreated['OrderId'],$otherInformations);
                            unset($_SESSION['items']);
                            wp_send_json($response);
                        }
                        else {
                            if(json_decode($paid)->failureMessage == null)
                                $response = array(
                                    'status'	=> 'Error',
                                    'message'	=> $paid
                                );
                            else
                              $response = array(
                                                        'status'	=> json_decode($paid)->result,
                                                        'message'	=> json_decode($paid)->failureMessage
                                                    );
                         wp_send_json($response);
                        }

                    }
                    else{
                        $response = array(
                            'status'	=> 'Error',
                            'message'	=> 'Required fields are empty'
                        );
                        wp_send_json($response);
                    }
                }
                else
                {
                    $response = array(
                        'status'	=> 'Error',
                        'message'	=> 'Internal Error, please contact us, if you\'re the site owner verify your API Key'
                    );
                    wp_send_json($response);
                }

            }
            else
            {
                $response = array(
                    'status'	=> 'Error',
                    'message'	=> 'Your session is expired please update the cart'
                );
                wp_send_json($response);
            }

        }
        else
        {
            $response = array(
                'status'	=> 'Error',
                'message'	=> 'Unauthorized'
            );
            wp_send_json($response);
        }
    }

    private function moo_CreateOrder($ordertype,$taxable,$deliveryfee)
    {
        $total = self::moo_cart_getTotal(true);
        $amount    = floatval(str_replace(',', '', $total['total']));
        $sub_total = floatval(str_replace(',', '', $total['sub_total']));
        $taxAmount = floatval(str_replace(',', '', $total['total_of_taxes']));

        $deliveryfee = floatval($deliveryfee);

        $amount    += $deliveryfee;
        $sub_total += $deliveryfee;

        if($total['status']=='success'){
            if($ordertype=='default')
                    $order = ($taxable==true)?$this->api->createOrder($amount,'default'):$this->api->createOrder($sub_total,'default');
            else
                    $order = ($taxable==true)? $this->api->createOrder($amount,$ordertype):$this->api->createOrder($sub_total,$ordertype);
            $order = json_decode($order);
            if(isset($order->href)){
                // Add Items to order
                foreach($_SESSION['items'] as $item)
                {
                    // If the item is empty skip to the next iteration of the loop
                    if(!isset($item['item'])) continue;

                    // Create line item
                    if(count($item['modifiers']) > 0) {
                        for($i=0;$i<$item['quantity'];$i++){
                            $res = $this->api->addlineToOrder($order->id,$item['item']->uuid,'1',$item['special_ins']);
                            $lineId = json_decode($res)->id;
                            foreach ($item['modifiers'] as $modifier) $this->api->addModifierToLine($order->id,$lineId,$modifier['uuid']);
                        }
                    }
                    else
                    {
                        $this->api->addlineToOrder($order->id,$item['item']->uuid,$item['quantity'],$item['special_ins']);
                    }
                }
                return array("OrderId"=>$order->id,"amount"=>$amount,"taxamount"=>$taxAmount,"taxable"=>$taxable,"sub_total"=>$sub_total);
            }
            else return false;
        }
        else return false;


    }
    private function moo_PayOrder($cardEncrypted,$card_number,$cvv,$expMonth,$expYear,$orderId,$amount,$taxAmount,$zip,$tipAmount)
    {


        $amount = str_replace(',', '', $amount);
        $taxAmount = str_replace(',', '', $taxAmount);
        $tipAmount = str_replace(',', '', $tipAmount);

        $card_number = str_replace(' ','',trim($card_number));
        $cvv       = intval($cvv);
        $expMonth  = intval($expMonth);
        $expYear   = intval($expYear);
        $orderId   = sanitize_text_field($orderId);
        $amount    = floatval($amount);
        $taxAmount = floatval($taxAmount);
        $zip = intval($zip);

        $last4  = substr($card_number,-4);
        $first6 = substr($card_number,0,6);

        $res = $this->api->payOrder($orderId,$taxAmount,$amount,$zip,$expMonth,$cvv,$last4,$expYear,$first6,$cardEncrypted,$tipAmount);
        return $res;

    }

    public function moo_GetOrderTypes()
    {
        $OrdersTypes = $this->api->GetOrdersTypes();
       if(count($OrdersTypes)>0)
       {
           $response = array(
               'status'	=> 'success',
               'data'	=> json_decode($OrdersTypes)->elements
           );
           wp_send_json($response);
       }
        else
        {
            $response = array(
                'status'	=> 'Error',
            );
            wp_send_json($response);
        }


    }
	public function moo_getAllOrderTypes()
    {
        $OrdersTypes = $this->model->getOrderTypes();
       if(count($OrdersTypes)>0)
       {
           $response = array(
               'status'	=> 'success',
               'data'	=> json_encode($OrdersTypes)
           );
           wp_send_json($response);
       }
        else
        {
            $response = array(
                'status'	=> 'success',
                'data'	=> "{}"
            );
            wp_send_json($response);
        }


    }
public function moo_AddOrderType()
    {
	    $label   =  sanitize_text_field($_POST['label']);
	    $taxable =  sanitize_text_field($_POST['taxable']);
        $OrderType = $this->api->addOrderType($label,$taxable);
       if($OrderType)
       {
	       $this->api->save_One_orderType(json_decode($OrderType));
           $response = array(
               'status'	=> 'success',
               'data'	=> json_encode($OrderType)
           );
           wp_send_json($response);
       }
        else
        {
            $response = array(
                'status'	=> 'error'
            );
            wp_send_json($response);
        }


    }
	public function moo_DeleteOrderType()
    {
	    $uuid   =  sanitize_text_field($_POST['uuid']);

        $OrderType = $this->model->moo_DeleteOrderType($uuid);
       if($OrderType)
       {
	       $this->api->save_One_orderType(json_decode($OrderType));
           $response = array(
               'status'	=> 'success',
               'data'	=> json_encode($OrderType)
           );
           wp_send_json($response);
       }
        else
        {
            $response = array(
                'status'	=> 'error'
            );
            wp_send_json($response);
        }


    }

    // Function for Importing DATA, Response to The AJAX requests


   public function moo_ImportCategories()
   {
       $res = $this->api->getCategories();
       $this->api->getItemGroups();
       $this->api->getModifierGroups();
       $this->api->getModifiers();

       $response = array(
           'status'	=> 'Success',
           'data'=> $res
       );
       wp_send_json($response);
   }

    public function moo_ImportLabels()
   {
       $this->api->getAttributes();
       $this->api->getOptions();
       $res= $this->api->getTags();
       $response = array(
           'status'	=> 'Success',
           'data'=> $res
       );
       wp_send_json($response);
   }
    public function moo_ImportTaxes()
   {
       $res= $this->api->getTaxRates();
       $response = array(
           'status'	=> 'Success',
           'data'=> $res
       );
       wp_send_json($response);
   }
    public function moo_ImportItems()
   {
       $this->api->getOrderTypes();
       $res= $this->api->getItems();
       $response = array(
           'status'	=> 'Success',
           'data'=> $res
       );
       wp_send_json($response);
   }
    public function moo_ImportOrderTypes()
   {
       $res = $this->api->getOrderTypes();
       $response = array(
           'status'	=> 'Success',
           'data'=> $res
       );
       wp_send_json($response);
   }
    public function moo_GetStats()
   {
       $cats     = $this->model->NbCats();
       $labels   = $this->model->NbLabels();
       $taxes    = $this->model->NbTaxes();
       $products = $this->model->NbProducts();

       $response = array(
           'status'	 => 'Success',
           'cats'    => $cats[0]->nb?:0,
           'labels'  => $labels[0]->nb?:0,
           'taxes'   => $taxes[0]->nb?:0,
           'products'=> $products[0]->nb?:0
       );
       wp_send_json($response);
   }
    public function moo_UpdateOrdertypesStatus()
   {
       $ot_uuid = $_POST['ot_uuid'];
       $status= $_POST['ot_status'];
       $res = $this->model->updateOrderTypes($ot_uuid,$status);
       $response = array(
           'status'	 => 'Success',
           'data'    => $res
       );
       wp_send_json($response);
   }
    public function moo_UpdateOrdertypesShowSa()
   {
       $ot_uuid  = $_POST['ot_uuid'];
       $show_sa  = $_POST['show_sa'];
       $res = $this->model->updateOrderTypesSA($ot_uuid,$show_sa);
       $response = array(
           'status'	 => 'Success',
           'data'    => $res
       );
       wp_send_json($response);
   }
     public function moo_SendFeedBack()
       {
           $default_options = (array)get_option('moo_settings');
	       $message   =  sanitize_text_field($_POST['message']);
	       $email     =  sanitize_text_field($_POST['email']);

           $message .='-----------<br/>';
           $message .='EMAIl : '.$email.'<br/>';
           $message .='Plugin Version : '.$this->version.'<br/>';
           $message .='Default Style  : '.$this->style.'<br/>';
           $message .='API Key  : '.$default_options['api_key'];
           $message .='Email in settings  : '.$default_options['merchant_email'];

	       $res = wp_mail("support@merchantech.us,m.elbanyaoui@gmail.com", 'Feedback from Wordpress plugin user', $message);
           $response = array(
               'status'	 => 'Success',
	           'data'=>$res,
	           'message'=>$message
           );
           var_dump($message);
           wp_send_json($response);
       }

    // Filtering Items
    // Get Items Filtered
    public function moo_GetItemsFiltered()
   {
       require_once plugin_dir_path( dirname(__FILE__))."includes/class-moo-OnlineOrders-shortcodes.php";

       $cat     = sanitize_text_field($_POST['Category']);
       $filerBy = sanitize_text_field($_POST['FilterBy']);
       $order   = sanitize_text_field($_POST['Order']);
       $search  = sanitize_text_field($_POST['search']);

       $html = Moo_OnlineOrders_Shortcodes::getItemsHtml($cat,$filerBy,$order,$search);
       echo $html;
       die();
   }

   // Get Modifiers for an Item
    public function moo_ModifiersForAnItem()
   {
       require_once plugin_dir_path( dirname(__FILE__))."includes/class-moo-OnlineOrders-shortcodes.php";

       $item_uuid  = sanitize_text_field($_POST['item_uuid']);

       $html = Moo_OnlineOrders_Shortcodes::getItemsModifiers($item_uuid);
       echo $html;
       die();
   }
    /* Manage Modifiers */

    function moo_ChangeModifierGroupName()
    {
        $mg_uuid  = sanitize_text_field($_POST['mg_uuid']);
        $name     = sanitize_text_field($_POST['mg_name']);
        $res = $this->model->ChangeModifierGroupName($mg_uuid,$name);

        $response = array(
            'status'	 => 'Success',
            'data'=>$res
        );
        wp_send_json($response);

    }
    function moo_UpdateModifierGroupStatus()
    {
        $mg_uuid  = sanitize_text_field($_POST['mg_uuid']);
        $status   = sanitize_text_field($_POST['mg_status']);
        $res = $this->model->UpdateModifierGroupStatus($mg_uuid,$status);
        $response = array(
            'status'	 => 'Success',
            'data'=>$res
        );
        wp_send_json($response);
    }
    function moo_ChangeCategoryName()
    {
        $cat_uuid  = sanitize_text_field($_POST['cat_uuid']);
        $name      = sanitize_text_field($_POST['cat_name']);
        $res = $this->model->ChangeCategoryName($cat_uuid,$name);

        $response = array(
            'status'	 => 'Success',
            'data'=>$res
        );
        wp_send_json($response);

    }
    /*
     * Function to manage item's images
     * since v1.1.3
     */
    function moo_getItemWithImages()
    {
        $item_uuid = sanitize_text_field($_POST['item_uuid']);
        $res = $this->model->getItemWithImage($item_uuid);
        $response = array(
            'status'	 => 'Success',
            'data'=>$res
        );
        wp_send_json($response);
    }
    function moo_saveItemWithImages()
    {
        $item_uuid = sanitize_text_field($_POST['item_uuid']);
        $description = sanitize_text_field($_POST['description']);
        $images = $_POST['images'];

        $res = $this->model->saveItemWithImage($item_uuid,$description,$images);
        $response = array(
            'status'	 => 'Success',
            'data'=>$res
        );
        wp_send_json($response);
    }

    function moo_UpdateCategoryStatus()
    {
        $cat_uuid  = sanitize_text_field($_POST['cat_uuid']);
        $status   = sanitize_text_field($_POST['cat_status']);
        if($cat_uuid == 'NoCategory')
        {
            if($status == "true") update_option('moo-show-allItems','true');
            else update_option('moo-show-allItems','false');
            $response = array(
                'status'	 => 'Success',
                'data'=>'OK'
            );
        }
        else
        {
            $res = $this->model->UpdateCategoryStatus($cat_uuid,$status);
            $response = array(
                'status'	 => 'Success',
                'data'=>$res
            );
        }

        wp_send_json($response);
    }
    function moo_StoreIsOpen()
    {
        $MooOptions = (array)get_option('moo_settings');

        if($MooOptions['hours'] == 'business')
        {
            $res = $this->api->getOpeningStatus();
            $stat = json_decode($res)->status;
            $response = array(
                'status'	 => 'Success',
                'data'=>$stat,
                'infos'=>$res
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	 => 'Success',
                'data'=>'open'
            );
            wp_send_json($response);
        }

    }
    function moo_TipsIsEnabled()
    {
        $res = json_decode($this->api->getMerchantProprietes());
        var_dump($res);

      /*  if($MooOptions['hours'] == 'business')
        {
            $res = $this->api->getOpeningStatus();
            $stat = json_decode($res)->status;
            $response = array(
                'status'	 => 'Success',
                'data'=>$stat,
                'infos'=>$res
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'	 => 'Success',
                'data'=>'open'
            );
            wp_send_json($response);
        }
      */

    }
    /*
     *
     * Sync with Clover POS handle
     *
     */
    function moo_SyncHandle()
    {
      if(isset($_POST['event']))
      {
          switch ($_POST['event']){
              case 'UPDATE_ITEM':
                  $item_uuid = (isset($_POST['item']) && !empty($_POST['item']))?$_POST['item']:'';
                  $this->api->getItem($item_uuid);
                  echo 'OK';
                  break;
              case 'CREATE_ITEM':
                  $item_uuid = (isset($_POST['item']) && !empty($_POST['item']))?$_POST['item']:'';
                  $this->api->getItem($item_uuid);
                  echo 'OK';
                  break;
              case 'DELETE_ITEM':
                  $item_uuid = (isset($_POST['item']) && !empty($_POST['item']))?$_POST['item']:'';
                  $res = $this->api->delete_item($item_uuid);
                  echo ($res)?'OK':'NOK';
                  break;
              case 'UPDATE_TAX_RATES':
                  $this->api->update_taxes_rates();
                  echo 'OK';
                  break;
              case 'UPDATE_ORDER_TYPES':
                  $res = $this->api->update_order_types();
                  echo ($res)?'OK':'NOK';
                  break;
              default :
                  echo 'EVENT NOT FOUND';
                  break;
          }
      }
      else
        echo 'NOK';
    }

	private function round_up ( $value, $precision ) {
		$pow = pow ( 10, $precision );
		return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow;
	}
    private function sendEmail($email,$name,$orderID)
    {
        $message    =  'Dear '.$name;
        $message   .=  '<br/>Thank you for placing your order with us ';
        $message   .=  '<br/><b><a href="https://www.clover.com/r/'.$orderID.'" target="_blanck">Order details</a></b>';
        wp_mail($email, 'Thank you for your order', $message);
    }
    private function sendEmail2merchant($email,$orderID,$otherInformations)
    {
        if($email != null && $email != '')
        {
            $order = $this->model->getOrder($orderID);
            $order = $order[0];
            $emails = explode(',',$email);
            $message    =  'Hello';
            $message   .=  '<br/>You have received a new order on your website ';
            $message   .=  '<br/><br/><b>Customer Information</b>';
            $message   .=  '<br/>Name : '.$order->p_name;
            $message   .=  '<br/>Address : '.$order->p_address;
            $message   .=  '<br/>City : '.$order->p_city;
            $message   .=  '<br/>ZipCode : '.$order->p_zipcode;
            $message   .=  '<br/>Email : '.$order->p_email;
            $message   .=  '<br/>Phone : '.$order->p_phone;
            if($order->instructions != '')
                $message   .=  '<br/><b>Special Instructions</b>';
            $message   .=  '<br/>'.$order->instructions;
            $message   .=  '<br/><br/><b><a href="https://www.clover.com/r/'.$orderID.'" target="_blanck">Order receipt</a></b>';

            $message    .= $otherInformations;

            foreach ($emails as $email) {
                wp_mail(trim($email), 'New order received', $message);
            }

        }

    }

}
