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

        if($this->style == "style1"){
            wp_register_style( 'custom-style-accordion', plugins_url( '/css/custom_style_accordion.css', __FILE__ ),'bootstrap-min' );
            wp_register_style( 'simple-modal', plugins_url( '/css/simplemodal.css', __FILE__ ),'bootstrap-min' );
            wp_register_style( 'magnific-popup', plugins_url( '/css/magnific-popup.css', __FILE__ ));
        }
        else
        {
            wp_register_style( 'custom-style-items', plugins_url( '/css/items.css', __FILE__ ),'bootstrap-min' );
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
                'plugin_img' =>  plugins_url( '/img', __FILE__ ),
                'store_opening_hours' =>  get_option('moo_store_openingHours'),
                'store_opening' =>  $MooOptions['hours']
            );
            // Register the script like this for a plugin:

            wp_register_script('bootstrap-js', plugins_url( '/js/bootstrap.min.js', __FILE__ ));
            wp_enqueue_script('bootstrap-js',array('jquery'));

            wp_register_script('toastr-js', plugins_url( '/js/toastr.min.js', __FILE__ ));
            wp_enqueue_script('toastr-js',array('jquery'));

            wp_register_script('custom-script-checkout', plugins_url( '/js/moo_checkout.js', __FILE__ ));
            wp_register_script('display-merchant-map', plugins_url( '/js/moo_map.js', __FILE__ ));
            wp_register_script('moo-google-map', 'https://maps.googleapis.com/maps/api/js');
            wp_register_script('forge', plugins_url( '/js/forge.min.js', __FILE__ ));

            wp_register_script('moo_public_js',  plugins_url( 'js/moo-OnlineOrders-public.js', __FILE__ ));
		    wp_enqueue_script('moo_public_js', array( 'jquery' ), $this->version, false);

            if($this->style == "style1"){
                wp_register_script('custom-script-accordion', plugins_url( '/js/custom_script_store_accordion.js', __FILE__ ));
                wp_register_script('simple-modal', plugins_url( '/js/simple-modal.js', __FILE__ ));
                wp_register_script('magnific-modal', plugins_url( '/js/magnific.min.js', __FILE__ ));
                wp_register_script('jquery-accordion', plugins_url( '/js/jquery.accordion.js', __FILE__ ));

                wp_register_script('script-cart-v2', plugins_url( '/js/cart_v2.js', __FILE__ ));
                wp_enqueue_script('script-cart-v2', array( 'jquery' ));
            }
            else
            {
                wp_register_script('custom-script-items', plugins_url( '/js/items.js', __FILE__ ));

                wp_register_script('script-cart-v1', plugins_url( '/js/cart_v1.js', __FILE__ ));
                wp_enqueue_script('script-cart-v1', array( 'jquery' ));

            }

            wp_register_script('moo_validate_forms',  plugins_url( 'js/jquery.validate.js', __FILE__ ));
		    wp_enqueue_script('moo_validate_forms', array( 'jquery' ), $this->version, false);

            wp_register_script('moo_validate_payment',  plugins_url( 'js/jquery.payment.min.js', __FILE__ ));
		    wp_enqueue_script('moo_validate_payment', array( 'jquery' ), $this->version, false);

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
                    if($_SESSION['items'][$item_uuid]['quantity']<10) $_SESSION['items'][$item_uuid]['quantity']++;
                    else  $_SESSION['items'][$item_uuid]['quantity'] = 10;
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
                    if($_SESSION['items'][$item_key]['quantity']<10) $_SESSION['items'][$item_key]['quantity']++;
                    else  $_SESSION['items'][$item_key]['quantity'] = 10;
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
            if( $_SESSION['items'][$item_uuid]['quantity']>10)  $_SESSION['items'][$item_uuid]['quantity'] = 10;
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
            if( $_SESSION['items'][$item_uuid]['quantity']>10)  $_SESSION['items'][$item_uuid]['quantity'] = 10;
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
            $taxe_rates_groupping = [];
            $allTaxesRates = [];
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
          //  echo json_encode($taxe_rates_groupping);
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
    public static function moo_cart_getTotal_IQ()
    {
        if(isset($_SESSION['items']) && !empty($_SESSION['items'])){
            $sub_total = 0;
            $total_of_taxes = 0;
            $taxe_rates_groupping = [];
            foreach ($_SESSION['items'] as $item) {
                //Grouping taxe rates
                foreach ($item['tax_rate'] as $tr) {
                    if(isset($taxe_rates_groupping[$tr->rate])) array_push($taxe_rates_groupping[$tr->rate],$item);
                    else{
                        $taxe_rates_groupping[$tr->rate] = array();
                        array_push($taxe_rates_groupping[$tr->rate],$item);
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
            foreach ($taxe_rates_groupping as $tax_rate=>$items) {
                $taxes=0;
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
                    $taxes += self::round_up($line_taxes,2);
                }
                $total_of_taxes += $taxes;
            }

            $FinalSubTotal = round($sub_total,2,PHP_ROUND_HALF_UP);
            $FinalTaxTotal = self::round_up($total_of_taxes,2);

            $response = array(
                'status'	        => 'success',
                'sub_total'      	=>  number_format($FinalSubTotal, 2),
                'total_of_taxes'	=>  number_format($FinalTaxTotal, 2),
                'total'	            =>  number_format(($FinalSubTotal+$FinalTaxTotal), 2)
            );
            return $response;

        }
        return false;

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
                'min'	=> $res->min_required
            );
            wp_send_json($response);
        }


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
        if(isset($_POST)){
            if(isset($_SESSION) && !empty($_SESSION['items']))
            {
                if(!empty($_POST['form']['OrderType'])){
                    $OrderTpe_UUID = sanitize_text_field($_POST['form']['OrderType']);
                    $orderType = $this->api->GetOneOrdersTypes($OrderTpe_UUID);
                    $orderCreated = $this->moo_CreateOrder($OrderTpe_UUID,json_decode($orderType)->taxable);
                }

                else  $orderCreated = $this->moo_CreateOrder('default',true);
                if($orderCreated != false)
                {
                    $this->model->addOrder($orderCreated['OrderId'],$orderCreated['taxamount'],$orderCreated['amount'],$_POST['form']['name'],$_POST['form']['address'], $_POST['form']['city'],$_POST['form']['zipcode'],$_POST['form']['phone'],$_POST['form']['email'],$_POST['form']['instructions'],json_decode($orderType)->label);
                    $this->model->addLinesOrder($orderCreated['OrderId'],$_SESSION['items']);

                    if( !empty($_POST['form']['cardNumber']) && !empty($_POST['form']['cvv']) && !empty($_POST['form']['expiredDateMonth'])
                        && !empty($_POST['form']['expiredDateYear']) && !empty($_POST['form']['zipcode']))
                    {
                        if($orderCreated['taxable'])
                            $paid = $this->moo_PayOrder($_POST['form']['cardEncrypted'],$_POST['form']['cardNumber'],$_POST['form']['cvv'],$_POST['form']['expiredDateMonth'],$_POST['form']['expiredDateYear'],
                            $orderCreated['OrderId'],$orderCreated['amount'],$orderCreated['taxamount'],$_POST['form']['zipcode']);
                        else
                            $paid = $this->moo_PayOrder($_POST['form']['cardEncrypted'],$_POST['form']['cardNumber'],$_POST['form']['cvv'],$_POST['form']['expiredDateMonth'],$_POST['form']['expiredDateYear'],
                            $orderCreated['OrderId'],$orderCreated['sub_total'],'0',$_POST['form']['zipcode']);
                        $response = array(
                            'status'	=> json_decode($paid)->result,
                            'order'	=> $orderCreated['OrderId']
                        );
                        if($response['status'] == 'APPROVED'){

                            $this->model->updateOrder($orderCreated['OrderId'],json_decode($paid)->paymentId);
                            $this->api->NotifyMerchant($orderCreated['OrderId'],$_POST['form']['instructions']);
                            $this->sendEmail($_POST['form']['email'],$_POST['form']['name'],$orderCreated['OrderId']);
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

    private function moo_CreateOrder($ordertype,$taxable)
    {
        $total = self::moo_cart_getTotal(true);
        if($total['status']=='success'){
            if($ordertype=='default')
                    $order = ($taxable==true)?$this->api->createOrder($total['total'],'default'):$this->api->createOrder($total['sub_total'],'default');
            else
                    $order = ($taxable==true)? $this->api->createOrder($total['total'],$ordertype):$this->api->createOrder($total['sub_total'],$ordertype);

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
                return array("OrderId"=>$order->id,"amount"=>$total['total'],"taxamount"=>$total['total_of_taxes'],"taxable"=>$taxable,"sub_total"=>$total['sub_total']);
            }
            else return false;
        }
        else return false;

        //

    }
    private function moo_PayOrder($cardEncrypted,$card_number,$cvv,$expMonth,$expYear,$orderId,$amount,$taxAmount,$zip)
    {
       // var_dump($cardEncrypted);
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

        $res = $this->api->payOrder($orderId,$taxAmount,$amount,$zip,$expMonth,$cvv,$last4,$expYear,$first6,$cardEncrypted);
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
	       $message   =  sanitize_text_field($_POST['message']);
           //TODO : Send an email to us, the message is in $_POST['message']
	       $res = wp_mail("m.elbanyaoui@gmail.com", 'Feedback from Wordpress plugin user', $message);
           $response = array(
               'status'	 => 'Success',
	           'data'=>$res,
	           'message'=>$message
           );
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
    function moo_StoreIsOpen()
    {
        $MooOptions = (array)get_option('moo_settings');
        $style = $MooOptions["default_style"];

        if($MooOptions['hours'] == 'business')
        {
            $res = $this->api->getOpeningStatus();
            $response = array(
                'status'	 => 'Success',
                'data'=>'close',
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

}
