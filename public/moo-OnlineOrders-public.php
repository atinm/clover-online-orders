<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://zaytechapps.com
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
	 * @access   private
	 * @var      Moo_OnlineOrders_Model    Object of functions that call the Database pr the API.
	 */
	private $model;

    /**
     * The model of this plugin (For all interaction with the DATABASE ).
     * @access   private
     * @var Moo_OnlineOrders_CallAPI
     */
    private $api;

    /**
     * @var mixed
     */
    private $style;

    /*
     * The store settings
     */
    private $settings;

    /**
     * The SESSION
     * @since    1.3.2
     * @access   private
     * @var MOO_SESSION
     */
    private $session;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $apiInstance, $modelInstance ) {
        $MooOptions = (array)get_option('moo_settings');

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->model       = $modelInstance;
		$this->api         = $apiInstance;
		$this->style       = $MooOptions["default_style"];
		$this->settings    = $MooOptions;
		$this->session     = MOO_SESSION::instance();
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

        //wp_register_style( 'moo-grid-css',"//api.smartonlineorders.com/assets/css/grid12.min.css",array(), $this->version);

        wp_register_style( 'moo-grid-css',plugins_url( '/css/grid12.min.css', __FILE__ ),array(), $this->version);
        wp_enqueue_style( 'moo-grid-css' );


        wp_register_style( 'moo-font-awesome',plugins_url( '/css/font-awesome.css', __FILE__ ),array(), $this->version);
        wp_enqueue_style ( 'moo-font-awesome' );

        wp_register_style( 'moo-magnific-popup', plugins_url( '/css/magnific-popup.min.css', __FILE__ ),array(), $this->version);
        wp_enqueue_style( 'moo-magnific-popup' );

        wp_register_style( 'moo-search-bar',plugins_url( '/css/moo-searchbar.css', __FILE__ ),array(), $this->version);
        wp_register_style( 'moo-myaccount',plugins_url( '/css/moo-myaccount.css', __FILE__ ),array(), $this->version);


        wp_register_style( 'moo-icheck-css',plugins_url( '/css/icheck-skins/square/blue.min.css', __FILE__ ),array(), $this->version);
        wp_enqueue_style( 'moo-icheck-css' );

        wp_register_style( 'moo-sweetalert-css-2',plugins_url( '/css/sweetalert2.min.css', __FILE__ ),array(), $this->version);
        wp_enqueue_style(  'moo-sweetalert-css-2' );



        wp_register_style( 'moo-modifiersPanel',plugins_url( '/css/moo-modifiersPanel.min.css', __FILE__ ),array('moo-grid-css','moo-magnific-popup'), $this->version);
        //wp_register_style( 'moo-modifiersPanel',"//api.smartonlineorders.com/assets/css/moo_ModifiersPanel.min.css",array('moo-grid-css','moo-magnific-popup'), $this->version);
        wp_enqueue_style( 'moo-modifiersPanel' );

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/moo-OnlineOrders-public.css', array(), $this->version, 'all' );
        wp_register_style( 'custom-style-cart3', plugins_url( '/css/custom_style_cart3.css', __FILE__ ),array(), $this->version );


        if($this->style == "style1"){
            wp_register_style( 'custom-style-accordion', plugins_url( '/css/custom_style_accordion.min.css', __FILE__ ),array(),$this->version );
            wp_register_style( 'moo-simple-modal', plugins_url( '/css/simplemodal.css', __FILE__ ),array(), $this->version );
        } else {
            if($this->style == "style2") {
                wp_register_style( 'mooStyle-style3', plugins_url( '/css/mooStyle-style3.css', __FILE__ ),array('moo-grid-css','moo-modifiersPanel'), $this->version );
            } else {
                if($this->style == "style3")
                {
                    wp_register_style( 'custom-style-accordion', plugins_url( '/css/custom_style_accordion.min.css', __FILE__ ),array(), $this->version );
                    wp_register_style( 'custom-style-items', plugins_url( '/css/items-style3.css', __FILE__ ),array(), $this->version );
                } else {
                    $files = scandir(plugin_dir_path(dirname(__FILE__))."public/themes/".$this->style);
                    foreach ($files as $file) {
                        $f = explode(".",$file);
                        if(@count($f) == 2) {
                            $file_name = $f[0];
                            $file_extension = $f[1];
                            if(strtoupper($file_extension) =="CSS")
                            {
                                wp_register_style( 'moo-'.$file_name.'-style' ,plugins_url( 'themes/'.$this->style.'/'.$file_name.'.'.$file_extension, __FILE__ ),array(), $this->version);
                            }
                        }
                    }
                }
            }
        }
        //Register styles for forced loading without javascript
        /*
         * Will be removed
         */
        wp_register_style( 'mooStyle-style4', plugins_url( '/themes/onePage/onePage.css', __FILE__ ),array('moo-grid-css','moo-modifiersPanel'), $this->version );

    }

	/**
	 * Register the scripts for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

            wp_enqueue_script( 'jquery' );

            $MooOptions = (array)get_option('moo_settings');

            $params = array(
                'ajaxurl' => admin_url( 'admin-ajax.php', isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' ),
                'plugin_img' =>  plugins_url( '/img', __FILE__ ),
                'custom_sa_title' =>  (isset($MooOptions["custom_sa_title"]) && trim($MooOptions["custom_sa_title"]) !== "")?trim($MooOptions["custom_sa_title"]):"",
                'custom_sa_content' =>  (isset($MooOptions["custom_sa_content"]) && trim($MooOptions["custom_sa_content"]) !== "")?trim($MooOptions["custom_sa_content"]):"",
                'custom_sa_onCheckoutPage' =>  (isset($MooOptions["custom_sa_onCheckoutPage"]))?trim($MooOptions["custom_sa_onCheckoutPage"]):"off"
            );

            // Register the script like this for a plugin:
            wp_register_script('moo-bootstrap-js', plugins_url( '/js/bootstrap.min.js', __FILE__ ),array(), $this->version);
            wp_enqueue_script('moo-bootstrap-js',array('jquery'));

            wp_register_script('moo-image-rotation-js', plugins_url( '/js/jquery.images-rotation.min.js', __FILE__ ),array(), $this->version);
            wp_enqueue_script('moo-image-rotation-js',array('jquery'));


            wp_register_script('moo-icheck-js', plugins_url( '/js/icheck.min.js', __FILE__ ),array(), $this->version);
            wp_enqueue_script('moo-icheck-js',array('jquery'));


            //Promise for IE
            wp_register_script('moo-bluebird', '//cdn.jsdelivr.net/bluebird/latest/bluebird.min.js',array(), $this->version);
            wp_enqueue_script('moo-bluebird');

            wp_register_script('moo-sweetalert-js-2', plugins_url( '/js/sweetalert2.min.js', __FILE__ ),array('moo-bluebird'), $this->version);
            wp_enqueue_script('moo-sweetalert-js-2',array('jquery','moo-bluebird'));

            wp_register_script('custom-script-checkout', plugins_url( '/js/moo_checkout.js', __FILE__ ),array(), $this->version);
            wp_register_script('custom-script-my-account', plugins_url( '/js/moo_myaccount.js', __FILE__ ),array(), $this->version);
            wp_register_script('display-merchant-map', plugins_url( '/js/moo_map.js', __FILE__ ),array(), $this->version);
            wp_register_script('moo-google-map', 'https://maps.googleapis.com/maps/api/js?libraries=geometry&key=AIzaSyBv1TkdxvWkbFaDz2r0Yx7xvlNKe-2uyRc');
            wp_register_script('moo-forge', plugins_url( '/js/forge.min.js', __FILE__ ));

            wp_register_script('moo-spreedly', 'https://core.spreedly.com/iframe/express-2.min.js');
            wp_register_script('moo-search-bar', plugins_url( 'js/moo-searchbar.js', __FILE__ ),array(), $this->version);


		    wp_register_script('moo_public_js',  plugins_url( 'js/moo-OnlineOrders-public.js', __FILE__ ),array(), $this->version);
		    wp_enqueue_script('moo_public_js', array( 'jquery' ));

            wp_register_script('moo-script-cart-v3', plugins_url( '/js/cart_v3.js', __FILE__ ),array(), $this->version);
            wp_enqueue_script('moo-script-cart-v3', array( 'jquery' ));

            wp_register_script('moo-magnific-modal', plugins_url( '/js/magnific.min.js', __FILE__ ),array(), $this->version);
            wp_enqueue_script( 'moo-magnific-modal', array( 'jquery' ) );

            wp_register_script('moo-modifiersPanel-js', plugins_url( '/js/moo-modifiersPanel.min.js', __FILE__ ),array(), $this->version);
            //wp_register_script('moo-modifiersPanel-js', "//api.smartonlineorders.com/assets/js/moo_ModifiersPanel.min.js",array(), $this->version);
            wp_enqueue_script('moo-modifiersPanel-js',array('jquery','moo-magnific-modal'));

            if($this->style == "style1"){
                wp_register_script('custom-script-accordion', plugins_url( '/js/custom_script_store_accordion.js', __FILE__ ),array(), $this->version);
                wp_register_script('moo-simple-modal', plugins_url( '/js/simple-modal.js', __FILE__ ));
                wp_register_script('jquery-accordion', plugins_url( '/js/jquery.accordion.js', __FILE__ ));
                wp_register_script('script-cart-v2', plugins_url( '/js/cart_v2.js', __FILE__ ),array(), $this->version);
                wp_enqueue_script('script-cart-v2', array( 'jquery' ));
            } else {
                if($this->style == "style2") {
                    wp_register_script('mooScript-style3', plugins_url( '/js/mooScript-style3.js', __FILE__ ),array( 'jquery','moo-modifiersPanel-js' ),$this->version);
                } else {
                    if($this->style == "style3") {
                        wp_register_script('jquery-accordion', plugins_url( '/js/jquery.accordion.js', __FILE__ ),array(), $this->version);
                        wp_register_script('custom-script-items', plugins_url( '/js/custom-script-style3.js', __FILE__ ),array(), $this->version);
                    } else {
                        $files = scandir(plugin_dir_path(dirname(__FILE__))."public/themes/".$this->style);
                        foreach ($files as $file) {
                            $f = explode(".",$file);
                            if(@count($f) == 2) {
                                $file_name = $f[0];
                                $file_extension = $f[1];
                                if(strtoupper($file_extension) == "JS")
                                {
                                    wp_register_script( 'moo-'.$file_name.'-js' ,plugins_url( 'themes/'.$this->style.'/'.$file_name.'.'.$file_extension, __FILE__ ),array(), $this->version);
                                }
                            }
                        }
                    }
                }
            }
            //Scripts for styles loaded without js
            wp_register_script('mooScript-style4', plugins_url( '/js/mooScript-style4.js', __FILE__ ),array( 'jquery','moo-modifiersPanel-js' ),$this->version);

            wp_register_script('moo_validate_forms',  plugins_url( 'js/jquery.validate.min.js', __FILE__ ));
		    wp_enqueue_script('moo_validate_forms', array( 'jquery' ));

            wp_register_script('moo_validate_payment',  plugins_url( 'js/jquery.payment.min.js', __FILE__ ));
		    wp_enqueue_script('moo_validate_payment', array( 'jquery' ));

            wp_localize_script("moo_public_js", "moo_params",$params);


            $cart_page_id  = $MooOptions['cart_page'];
            $checkout_page_id = $MooOptions['checkout_page'];
            $store_page_id = $MooOptions['store_page'];

            $cart_page_url  =  get_page_link($cart_page_id);
            $checkout_page_url =  get_page_link($checkout_page_id);
            $store_page_url =  get_page_link($store_page_id);

            if ($cart_page_url != '') {
                wp_localize_script("moo_public_js", "moo_CartPage",$cart_page_url);
            }
            if ($checkout_page_url != '') {
                wp_localize_script("moo_public_js", "moo_CheckoutPage",$checkout_page_url);
            }
            if ($store_page_url != '') {
                wp_localize_script("moo_public_js", "moo_StorePage",$store_page_url);
            }

            wp_localize_script("moo_public_js", "moo_RestUrl",get_rest_url());
	}


	public function addCartButton() {
        //Removed
	  }

    // AJAX Responses

    /**
     * Add to Cart
     * @since    1.0.0
     */
    public function moo_add_to_cart($item_key) {

        if(isset($_POST['item']) & !empty($_POST['item'])) {
            $item_uuid = sanitize_text_field($_POST['item']);
        } else {
            if(isset($item_key)) {
                $item_uuid = explode("__",$item_key);
                $item_uuid = $item_uuid[0];
            } else {
                $response = array(
                    'status'	=> 'error',
                    'message'   => 'Item not found in database, please refresh the page'
                );
            }
        }

        if(isset($_POST['quantity']) & !empty($_POST['quantity'])) {
            $qte= sanitize_text_field($_POST['quantity']);
        } else {
            $qte = 1;
        }

        if(isset($_POST['special_ins']) & !empty($_POST['special_ins'])) {
            $special_ins = sanitize_text_field($_POST['special_ins']);
        } else {
            $special_ins = "";
        }

        $item = $this->model->getItem($item_uuid);

        if($item){
            $track_stock = $this->api->getTrackingStockStatus();
            if($track_stock == true) {
                $itemStocks = $this->api->getItemStocks();
                $itemStock = $this->getItemStock($itemStocks,$item->uuid);
            } else {
                $itemStock = false;
            }

            if(isset($_POST['item']) & !empty($_POST['item']) ) {
                if($this->session->exist("items") && array_key_exists($item_uuid,$this->session->get("items")) ){
                    $cartLine = $this->session->get("items",$item_uuid);
                    if($track_stock && ($itemStock != false && isset($itemStock->stockCount) && $this->session->get("itemsQte",$item_uuid)>=$itemStock->stockCount)) {

                        $response = array(
                            'status'	=> 'error',
                            'message'   => "Unfortunately, we are low on stock please chnage the quantity amount",
                            'quantity'   => $itemStock->stockCount
                        );
                    } else {
                        $this->session->set(($cartLine["quantity"]+1),"items",$item_uuid);
                        $this->session->set(($this->session->get("itemsQte",$item_uuid)+1),"itemsQte",$item_uuid);
                        $response = array(
                            'status'	=> 'success'
                        );
                    }

                } else {
                    if($track_stock && ($itemStock != false && isset($itemStock->stockCount) && $qte>$itemStock->stockCount))
                    {
                        $response = array(
                            'status'	=> 'error',
                            'message'   => "Unfortunately, we are low on stock please chnage the quantity amount",
                            'quantity'   => $itemStock->stockCount
                        );
                    } else {
                        $cartLine = array(
                            'item'=>$item,
                            'quantity'=>$qte,
                            'special_ins'=>$special_ins,
                            'tax_rate'=>$this->model->getItemTax_rate( $item_uuid),
                            'modifiers'=>array()
                        );
                        if(! $this->session->isEmpty("itemsQte",$item_uuid)) {
                            $newQty =  $this->session->get("itemsQte",$item_uuid) + $qte;
                        } else {
                            $newQty =   $qte;
                        }
                        $this->session->set($newQty,"itemsQte",$item_uuid);
                        $this->session->set($cartLine,"items",$item_uuid);
                        $response = array(
                            'status'	=> 'success'
                        );
                    }

                }
            } else {
                if( $this->session->exist("items") && array_key_exists($item_key,$this->session->get("items")) )
                {
                        $cartLine = $this->session->get("items",$item_key);
                        if($track_stock && ($itemStock != false && isset($itemStock->stockCount) && $this->session->get("itemsQte",$item_uuid)>=$itemStock->stockCount))
                        {

                            $response = array(
                                'status'	=> 'error',
                                'message'   => "Unfortunately, we are low on stock please change the quantity amount",
                                'quantity'   => $itemStock->stockCount
                            );
                        }
                        else
                        {
                            $this->session->set(($cartLine["quantity"]+1),"items",$item_key);
                            $this->session->set(($this->session->get("itemsQte",$item_uuid)+1),"itemsQte",$item_uuid);
                            $response = array(
                                'status'	=> 'success'
                            );
                        }
                } else {
                    if($track_stock && ($itemStock != false && isset($itemStock->stockCount) && $itemStock->stockCount<$qte))
                    {
                        $response = array(
                            'status'	=> 'error',
                            'message'   => "Unfortunately, we are low on stock please change the quantity amount",
                            'quantity'   => $itemStock->stockCount
                        );
                    } else {
                        $cartLine = array(
                            'item'=>$item,
                            'quantity'=>$qte,
                            'special_ins'=>$special_ins,
                            'tax_rate'=>$this->model->getItemTax_rate($item_uuid),
                            'modifiers'=>array()
                        );

                        $this->session->set($qte,"itemsQte",$item_uuid);
                        $this->session->set($cartLine,"items",$item_key);

                        if(! $this->session->isEmpty("itemsQte",$item_uuid)) {
                            $newQty =  $this->session->get("itemsQte",$item_uuid) + $qte;
                        } else {
                            $newQty =   $qte;
                        }
                        $this->session->set($newQty,"itemsQte",$item_uuid);
                        $response = array(
                            'status'	=> 'success'
                        );
                    }
                }
            }
        } else {
            $response = array(
                'status'	=> 'error',
                'message'   => 'Item not found in database, please refresh the page'
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
        if( !$this->session->isEmpty("items")){
            $response = array(
                'status'	=> 'success',
                'data'   => $this->session->get("items")
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
          $cart_line_id = sanitize_text_field($_POST['item']);
          $uuids = explode('__',$cart_line_id);
          $item_uuid = $uuids[0];
          $item_qte= absint($_POST['qte']);
        if(!$this->session->isEmpty("items",$cart_line_id) && $item_qte>0){

            $track_stock = $this->api->getTrackingStockStatus();
            if($track_stock==true)
            {
                $itemStocks = $this->api->getItemStocks();
                $itemStock = $this->getItemStock($itemStocks,$item_uuid);
            }
            else
            {
                $itemStock = false;
            }
            if($track_stock && ($itemStock != false && isset($itemStock->stockCount) && $this->session->get("itemsQte",$item_uuid) >= $itemStock->stockCount))
            {

                $response = array(
                    'status'	=> 'error',
                    'message'   => "Unfortunately, we are low on stock. Please try changing the quantity or choose another item",
                    'quantity'   => $itemStock->stockCount
                );
            }
            else
            {
                $cartLine = $this->session->get("items",$cart_line_id);

                if(!$this->session->isEmpty("itemsQte",$item_uuid))
                {
                    $newQte = $item_qte - $cartLine["quantity"];
                    $this->session->set($newQte, "itemsQte", $item_uuid);
                } else {
                    $this->session->set($item_qte, "itemsQte", $item_uuid);
                }

                $cartLine['quantity'] = $item_qte ;

                if( $cartLine['quantity'] < 1 )  {
                    $cartLine['quantity'] = 1;
                }
                $this->session->set($cartLine, "items", $cart_line_id);
                $response = array(
                    'status'	=> 'success',
                );
            }

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

        $cart_line_id   = sanitize_text_field($_POST['item']);
        $special_ins = sanitize_text_field($_POST['special_ins']);

        if(!$this->session->isEmpty("items",$cart_line_id)){
            $cartLine = $this->session->get("items",$cart_line_id);
            $cartLine['special_ins'] = $special_ins ;
            $this->session->set($cartLine,"items",$cart_line_id);
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

        $cart_line_id  = sanitize_text_field($_POST['item']);

        if(!$this->session->isEmpty("items",$cart_line_id)){
            $cartLine = $this->session->get("items",$cart_line_id);
            $special_ins = $cartLine['special_ins'];
            $qte = $cartLine['quantity'];
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
     * Delete Item from the cart
     * @since    1.0.0
     */
    public function moo_deleteItemFromcart() {
        $cart_line_id = sanitize_text_field($_POST['item']);
        if(!$this->session->isEmpty("items",$cart_line_id)){
            $cartLine = $this->session->get("items",$cart_line_id);
            $itemUuid = $cartLine['item']->uuid;
            if($this->session->exist("itemsQte",$itemUuid))
            {
                $newQty = $this->session->get("itemsQte",$itemUuid) - $cartLine['quantity'];
                if($newQty<=0)
                    $this->session->delete("itemsQte",$itemUuid);
                else
                    $this->session->set($newQty,"itemsQte",$itemUuid);
            }
            $this->session->delete("items",$cart_line_id);
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
            $this->session->delete("items");
            $this->session->delete("itemsQte");
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
        $cart_line_id    = sanitize_text_field($_POST['item']);
        $modifier_uuid = sanitize_text_field($_POST['modifier']);
        $cartLine = $this->session->get("items",$cart_line_id);
        if(isset($cartLine['modifiers'][$modifier_uuid]) && !empty($cartLine['modifiers'][$modifier_uuid])){
            unset($cartLine['modifiers'][$modifier_uuid]);
            //Generate the new Key
            $pos = strrpos($cart_line_id, "__");
            if($pos){
                $new_cart_line_id = explode('__',$cart_line_id);
                $new_cart_line_id = $new_cart_line_id[0].'_';
                foreach ($cartLine['modifiers'] as $modifier)
                    $new_cart_line_id .= '_'.$modifier['uuid'];
                $this->session->set($cartLine,"items", $new_cart_line_id);
            }

            $nbModifiers = count($cartLine['modifiers']);
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
        $session = MOO_SESSION::instance();
        $MooOptions = (array)get_option('moo_settings');

        if(! $session->isEmpty("items")){

            $nb_items  = 0;
            $sub_total = 0;
            $total_of_taxes = 0;
            $total_of_taxes_without_discounts = 0;
            $taxe_rates_groupping = array();
            $allTaxesRates = array();
            $service_charges = 0;

            //get the taxes rates and calculate number of items
            foreach ($session->get("items") as $item) {
                if(!$item)
                    continue;
                $nb_items += 1 * $item['quantity'];
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
                        if(isset($m['qty']))
                           $m_price = $item['quantity'] * $m['price'] * intval($m['qty']);
                        else
                            $m_price = $item['quantity'] * $m['price'];

                        $sub_total += $m_price/100;
                    }
                }
            }
            //Coupons
            if( !$session->isEmpty("coupon")) {
                $coupon = $session->get("coupon");
            } else {
                $coupon = null;
            }


            //calculate taxes
            foreach ($taxe_rates_groupping as $tax_rate_uuid=>$items) {
                $taxes = 0;
                $taxesWithoutDiscounts = 0;

                $tax_rate = $allTaxesRates[$tax_rate_uuid];
                if($tax_rate == 0) continue;

                foreach ($items as $item) {
                        $lineSubtotal = $item['item']->price * $item['quantity'];
                        if(@count($item['modifiers'])>0){
                            foreach ($item['modifiers'] as $m) {
                                if(isset($m['qty']))
                                    $m_price = $item['quantity'] * $m['price'] * intval($m['qty']);
                                else
                                    $m_price = $item['quantity'] * $m['price'];

                                $lineSubtotal += $m_price;
                            }
                        }
                        $taxesWithoutDiscounts += ($tax_rate/100000 * $lineSubtotal/10000);

                        //Apply Discount
                        if(isset($coupon)) {
                            if( strtoupper($coupon['type'])=="PERCENTAGE" ) {
                                $lineSubtotal = $lineSubtotal - ($coupon['value']*$lineSubtotal/100);
                            } else {
                                $lineSubtotal = $lineSubtotal - ($coupon['value']*$lineSubtotal/$sub_total);
                            }

                            $line_taxes = $tax_rate/100000 * $lineSubtotal/10000;
                        } else {
                            $line_taxes = ($tax_rate/100000 * $lineSubtotal/10000);
                        }

                        $taxes += $line_taxes;

                }

                $total_of_taxes += round($taxes,2,PHP_ROUND_HALF_UP);
                $total_of_taxes_without_discounts += round($taxesWithoutDiscounts,2,PHP_ROUND_HALF_UP);

            }
            if($total_of_taxes<0)
                $total_of_taxes=0;

            if($total_of_taxes_without_discounts<0)
                $total_of_taxes_without_discounts=0;

            $FinalSubTotal = round($sub_total,2,PHP_ROUND_HALF_UP);
            $FinalTaxTotal = round($total_of_taxes,2,PHP_ROUND_HALF_UP);
            $FinalTaxTotalWithoutDiscounts = round($total_of_taxes_without_discounts,2,PHP_ROUND_HALF_UP);
            $DiscountedSubTotal = $FinalSubTotal;

            //Apply coupoun
            if(isset($coupon)) {
                if($coupon["minAmount"]>0) {
                    if($coupon["minAmount"]<=$FinalSubTotal) {
                        if( strtoupper($coupon['type'])=="PERCENTAGE" ) {
                            $couponValue =  $coupon['value']*$FinalSubTotal/100;
                        } else {
                            $couponValue = $coupon['value'];
                        }
                        if(isset($coupon['maxValue']) && $coupon['maxValue']>0 && $couponValue > ($coupon['maxValue'])) {
                            $couponValue = $coupon['maxValue'];
                            $coupon['type'] = 'AMOUNT';
                            $coupon['use_maxValue'] = true;
                            $coupon['value'] = $couponValue;
                            $session->set($coupon,"coupon");

                        }
                        $DiscountedSubTotal -= $couponValue;
                        $FinalTotal = $DiscountedSubTotal + $FinalTaxTotal;
                    } else {
                        $coupon = null;
                        $FinalTotal    = $DiscountedSubTotal + $FinalTaxTotalWithoutDiscounts;
                    }
                } else {
                    if(strtoupper($coupon['type']) == "PERCENTAGE" ) {
                        $couponValue =  $coupon['value']*$FinalSubTotal/100;
                    } else {
                        $couponValue = $coupon['value'];
                    }
                    if(isset($coupon['maxValue']) && $coupon['maxValue'] > 0 && $couponValue > ($coupon['maxValue'])) {
                        $couponValue = $coupon['maxValue'];
                        $coupon['type'] = 'AMOUNT';
                        $coupon['use_maxValue'] = true;
                        $coupon['value'] = $couponValue;
                        $session->set($coupon,"coupon");
                    }

                    $DiscountedSubTotal -= $couponValue;
                    $FinalTotal = $DiscountedSubTotal + $FinalTaxTotal;
                }

            } else {
                $FinalTotal    = $FinalSubTotal + $FinalTaxTotalWithoutDiscounts;
            }

           // $FinalTotal += $service_charges;

            //Check if total is 0;

            if($FinalTotal<0)
                $FinalTotal = 0;

            $FinalTotalWithoutDiscounts = $FinalSubTotal + $FinalTaxTotalWithoutDiscounts;

            // Correct number format (remove the , in numbers)
            $FinalSubTotal = str_replace(',', '', number_format($FinalSubTotal,2));
            $FinalTaxTotal = str_replace(',', '', number_format($FinalTaxTotal,2));
            $FinalTaxTotalWithoutDiscounts = str_replace(',', '', number_format($FinalTaxTotalWithoutDiscounts,2));
            $DiscountedSubTotal = str_replace(',', '', number_format($DiscountedSubTotal,2));
            $FinalTotal = str_replace(',', '', number_format($FinalTotal,2));
            $FinalTotalWithoutDiscounts = str_replace(',', '', number_format($FinalTotalWithoutDiscounts,2));
            if(isset($MooOptions['service_fees']) && $MooOptions['service_fees']>0)
            {
                if(isset($MooOptions['service_fees_type']) && $MooOptions['service_fees_type'] == "percent")
                {
                    $service_charges = floatval($MooOptions['service_fees'])*$FinalSubTotal/100;
                }
                else
                    $service_charges = floatval($MooOptions['service_fees']);
            }

            $response = array(
                'status'	                            => 'success',
                'sub_total'      	                    => $FinalSubTotal,
                'total_of_taxes'	                    => $FinalTaxTotal,
                'total_of_taxes_without_discounts'	    => $FinalTaxTotalWithoutDiscounts,
                'discounted_subtotal'	                => $DiscountedSubTotal,
                'total'	                                => $FinalTotal,
                'total_without_discounts'	            => $FinalTotalWithoutDiscounts,
                'nb_items'	                            => $nb_items,
                'coupon'	                            => $coupon,
                'serviceCharges'                        => $service_charges
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
     * get Opening Hours for the store
     * @since    1.2.6
     */
    public function moo_getOpeningHours()
    {

        $nb_days   = sanitize_text_field($_POST['nb_days']);
        $nb_minutes  = sanitize_text_field($_POST['nb_minutes']);

        $res = json_decode($this->api->getOpeningStatus($nb_days,$nb_minutes));
        if($res){
            $response = array(
                'status'	=> 'success',
                'pickup_time'	=> $res->pickup_time,
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
     * @deprecated
     */
    public function moo_modifier_add()
    {

        //do not use sanitize_text_field with this, beuacuse it's an array will sanitize it in database requests
        $modifiers = $_POST['modifiers'];
        $flag = false;
        if(@count($modifiers)>0){
            $cart_line_id =  $modifiers[0]['item'].'_';
            foreach ($modifiers as $modifier) $cart_line_id .= '_'.$modifier['modifier'];
            $this->moo_add_to_cart($cart_line_id);
            $cartLine = $this->session->get("items",$cart_line_id);
            foreach ($modifiers as $modifier) {
                $modifier_uuid = $modifier['modifier'];
                $modifierInfos = $this->model->getModifier($modifier_uuid);
                $cartLine['modifiers'][$modifier_uuid] = (array)$modifierInfos;
            }
            $this->session->set($cartLine, "items", $cart_line_id);
            $flag = true;
        }


        //$res = $model->getModifiersGroupLimits($mg_uuid);

        if($flag)
            $response = array(
                'status'	=> 'success',
                'uuid' => $cart_line_id
            );
        else
            $response = array(
                'status'	=> 'error',
            );
        wp_send_json($response);
    }

    /*
     * Checkout
     */
    public function moo_checkout()
    {
        if(isset($_POST) && isset($_POST['form']['_wpnonce'])){
            if( !$this->session->isEmpty("items") )
            {
                $_POST["form"] =  apply_filters( 'moo_filter_checkout_form', $_POST["form"]);
                if( $_POST["form"] === false ) {
                    $response =  array(
                        'status'	=> 'Error',
                        'message'=> "Sorry, please check if you have any additional addons, disable it or contact the developer"
                    );
                    wp_send_json($response);

                }

                $MooOptions = (array)get_option('moo_settings');
                $total = self::moo_cart_getTotal(true);

                $deliveryFee    = 0;
                $tipAmount      = 0;
                $serviceFee     = 0;
                $serviceFeeName = "Service Charge";
                $deliveryfeeName= "Delivery Charge";
                $pickup_time    = '';


                if(isset($total['serviceCharges']) && $total['serviceCharges']>0) {
                    $serviceFee = floatval($total['serviceCharges']);
                }

                // Check teh payment method
                if(isset($_POST['form']['payments'])) {
                    $paymentmethod = sanitize_text_field($_POST['form']['payments']);
                } else {
                    $response = array(
                        'status'	=> 'Error',
                        'message'	=> "The payment method is required"
                    );
                    wp_send_json($response);
                }


                /* Get the names on receipt of Service Charge and delivery charge */
                if(isset($MooOptions['service_fees_name']) && $MooOptions['service_fees_name']!="")
                    $serviceFeeName = $MooOptions['service_fees_name'];

                if(isset($MooOptions['delivery_fees_name']) && $MooOptions['delivery_fees_name']!="")
                    $deliveryfeeName = $MooOptions['delivery_fees_name'];

                //Check the stock
                $track_stock = $this->api->getTrackingStockStatus();
                if( $track_stock == true ) {
                    $itemStocks = $this->api->getItemStocks();
                    foreach ($this->session->get("items") as $cartLine) {
                        $itemStock = $this->getItemStock($itemStocks,$cartLine['item']->uuid);
                        if($itemStock == false)  continue;
                        if($this->session->exist("itemsQte",$cartLine['item']->uuid) && $this->session->get("itemsQte",$cartLine['item']->uuid) > $itemStock->stockCount)
                        {
                            $response = array(
                                'status'	=> 'Error',
                                'message'	=> 'The item '.$cartLine['item']->name.' is low on stock. Please go back and change the quantity in your cart '.(($itemStock->stockCount>0)?"as we have only ".$itemStock->stockCount." left":"")
                            );
                            wp_send_json($response);
                        }
                        else
                        {
                            if($cartLine['quantity']>$itemStock->stockCount)
                            {
                                $response = array(
                                    'status'	=> 'Error',
                                    'message'	=> 'The item '.$cartLine['item']->name.' is low on stock. Please go back and change the quantity in your cart '.(($itemStock->stockCount>0)?"as we have only ".$itemStock->stockCount." left":"")
                                );
                                wp_send_json($response);
                            }
                        }
                    }
                }


                /*
                 * Check Scheduled Orders time
                 */
                //check day
                if(isset($_POST['form']['pickup_day'])) {
                    $pickup_time = sanitize_text_field($_POST['form']['pickup_day']);
                }
                // check hour
                if(isset($_POST['form']['pickup_hour'])) {
                    $pickup_time .= ' at '.$_POST['form']['pickup_hour'];
                }
                // concat day and hour
                if($pickup_time != '') {
                    $pickup_time = ' Scheduled for '.$pickup_time;
                }

                // Check the customer address
                if(isset($_POST['form']['address']) && isset($_POST['form']['address']['lat']) ) {
                    $customer_lat = sanitize_text_field($_POST['form']['address']['lat']);
                } else {
                    $customer_lat=null;
                }

                if(isset($_POST['form']['address']) && isset($_POST['form']['address']['lng']) ) {
                    $customer_lng = sanitize_text_field($_POST['form']['address']['lng']);
                } else {
                    $customer_lng = null;
                }

                // check tips
                if(isset($_POST['form']['tips']) && $_POST['form']['tips'] > 0 )
                    $tipAmount    = $_POST['form']['tips'];
                // check delivery fees
                if(isset($_POST['form']['deliveryAmount']) && $_POST['form']['deliveryAmount'] > 0 )
                    $deliveryFee  = $_POST['form']['deliveryAmount'];
                // check service charges
                if(isset($_POST['form']['serviceCharges']) && $_POST['form']['serviceCharges'] > 0 )
                    $serviceFee  += $_POST['form']['serviceCharges'];


                $deliveryFeeTmp = $deliveryFee;
                if($deliveryFee>0)
                {
                    $delivery_fees_cartLine = array(
                        'item'=>(object)array(
                            "uuid"=>"delivery_fees",
                            "name"=>$MooOptions["delivery_fees_name"],
                            "price"=>($deliveryFee*100)),
                        'quantity'=>1,
                        'special_ins'=>'',
                        'tax_rate'=>array(),
                        'modifiers'=>array()
                    );
                    $this->session->set($delivery_fees_cartLine,"items","delivery_fees");
                }

                $serviceFeeTmp = $serviceFee;
                if($serviceFee>0)
                {
                    $service_fees_cartLine = array(
                        'item'=>(object)array(
                            "uuid"=>"service_fees",
                            "name"=>$serviceFeeName,
                            "price"=>($serviceFee*100)),
                        'quantity'=>1,
                        'special_ins'=>'',
                        'tax_rate'=>array(),
                        'modifiers'=>array()
                    );
                    $this->session->set($service_fees_cartLine,"items","service_fees");
                }
                $customer = array(
                    "name"    => (isset($_POST['form']['name']))?$_POST['form']['name']:"",
                    "address" => (isset($_POST['form']['address']) && isset($_POST['form']['address']['address']))?$_POST['form']['address']['address']:"",
                    "line2"   => (isset($_POST['form']['address']['line2']))?$_POST['form']['address']['line2']:"",
                    "city"    => (isset($_POST['form']['address']['city']))?$_POST['form']['address']['city']:"",
                    "state"   => (isset($_POST['form']['address']['state']))?$_POST['form']['address']['state']:"",
                    "country" => (isset($_POST['form']['address']['country']))?$_POST['form']['address']['country']:"",
                    "zipcode" => (isset($_POST['form']['address']['zipcode']))?$_POST['form']['address']['zipcode']:"",
                    "phone"   => (isset($_POST['form']['phone']))?$_POST['form']['phone']:"",
                    "email"   => (isset($_POST['form']['email']))?$_POST['form']['email']:"",
                    "customer_token"   =>($this->session->isEmpty("moo_customer_token"))?"":$this->session->get("moo_customer_token"),
                    "lat"   =>$customer_lat,
                    "lng"   =>$customer_lng,
                );
                $note = 'Smart Online Order | ' . $customer["name"];

                if($_POST['form']['instructions'] !== "" || $pickup_time !== '')
                    $note .=' | '.$_POST['form']['instructions'].' '.$pickup_time;

                //Create the Order
                if(!empty($_POST['form']['ordertype'])) {
                    $orderType_uuid = sanitize_text_field($_POST['form']['ordertype']);
                    $orderType = $this->api->GetOneOrdersTypes($orderType_uuid);
                    $orderTypeFromClover = json_decode($orderType);

                    if(isset($orderTypeFromClover->code) && $orderTypeFromClover->code == 998)
                        return array( 'status'	=> 'Error','message'=> "Sorry, but we are having a brief maintenance. Check back in a few minutes");

                    if(isset($orderTypeFromClover->message) && $orderTypeFromClover->message == "401 Unauthorized")
                        return array( 'status'	=> 'Error','message'=> "Internal Error, please contact us, if you're the site owner verify your API Key");

                    $orderTypeFromLocal  = (array)$this->model->getOneOrderTypes($orderType_uuid);
                    $isDelivery = ( isset($orderTypeFromLocal['show_sa']) && $orderTypeFromLocal['show_sa'] == "1" )?"Delivery":"Pickup";

                    $note .= ' | '.$orderTypeFromClover->label;

                    if($isDelivery === 'Delivery')
                        $note .= ' | '.$customer["address"].' '.$customer["note"].' '.$customer['city'].', '.$customer["state"].' '.$customer['zipcode'];
                    if(isset($orderTypeFromClover->taxable)) {
                        $orderTaxable = $orderTypeFromClover->taxable;
                    } else {
                        $orderTaxable = true;
                    }
                    $orderCreated = $this->moo_CreateOrder($orderType_uuid,$orderTaxable,$deliveryFee,$deliveryfeeName,$serviceFee,$serviceFeeName,$paymentmethod,$tipAmount,$isDelivery,$_POST['form']['instructions'],$pickup_time,$customer,$note);
                } else {
                    $orderCreated = $this->moo_CreateOrder('default',true,$deliveryFee,$deliveryfeeName,$serviceFee,$serviceFeeName,$paymentmethod,$tipAmount,"Pickup",$_POST['form']['instructions'],$pickup_time,$customer,$note);
                    $orderTypeFromLocal = array('label'=>'default','show_sa'=>'0');
                }

                if($orderCreated) {
                    $tab = array(
                        "oid"=>$orderCreated['OrderId'],
                        "name"=>$customer['name'],
                        "email"=>$customer['email'],
                        "phone"=>$customer['phone'],
                        "address"=>array(
                            "address1"=>$customer["address"],
                            "address2"=>$customer["line2"],
                            "city"=>$customer["city"],
                            "state"=>$customer["state"],
                            "zip"=>$customer["zipcode"],
                            "country"=>$customer["country"]
                        )
                    );
                    $cus = $this->api->assignCustomer($tab);

                    // Add the delivery charges to Clover order
                    if($deliveryFeeTmp>0)
                        $this->api->addlineWithPriceToOrder($orderCreated['OrderId'],"",1,$deliveryfeeName,$deliveryFeeTmp);
                    //Add teh service charges to the CLover order
                    if($serviceFeeTmp>0)
                        $this->api->addlineWithPriceToOrder($orderCreated['OrderId'],"",1,$serviceFeeName,$serviceFeeTmp);

                    $customer["taxAmount"] = ($orderCreated['taxamount']*100);
                    $customer["tipAmount"] = $tipAmount*100;
                    $customer["ServiceFee"] = $serviceFeeTmp*100;
                    $customer["orderAmount"] = $orderCreated['amount'];
                    $customer["deliveryAmount"] = $deliveryFeeTmp*100 ;

                    $this->model->addOrder($orderCreated['OrderId'],$orderCreated['taxamount'],$orderCreated['amount'],$customer['name'],$customer['address'], $customer['city'],$customer['zipcode'],$customer['phone'],$customer['email'],$_POST['form']['instructions'],$customer['state'],$customer['country'],$deliveryFeeTmp,$tipAmount,$serviceFee,$customer_lat,$customer_lng,$orderTypeFromLocal['label'],($orderCreated['order']->createdTime/1000));
                    $this->model->addLinesOrder($orderCreated['OrderId'],$this->session->get("items"));


                    /*
                    if you have additional info please set-up it in this section
                        $otherInformations = "";
                     End section additional Infos
                    */

                    // Add a customer to the order

                    if($paymentmethod == 'cash') {
                            $this->SendSmsToMerchant($orderCreated['OrderId'],'will be paid in cash',$pickup_time,$orderTypeFromLocal['label']);
                           // $this->SendSmsToCustomer($orderCreated['OrderId'],$customer['phone']);
                            $this->model->updateOrder($orderCreated['OrderId'],'CASH');

                            $this->api->NotifyMerchant($orderCreated['OrderId'],$_POST['form']['instructions'],$pickup_time,$paymentmethod);
                            $this->sendEmailsAboutOrder($orderCreated['OrderId'],$MooOptions['merchant_email'],$_POST['form']['email']);

                            /* to debug uncomment this line, to not empty tha cart and you can send the order again */
                           // wp_send_json(array("status"=>"failed"));

                            $this->session->delete("items");
                            $this->session->delete("itemsQte");
                            $this->session->delete("coupon");

                            do_action("moo_action_order_created", $orderCreated['OrderId'], "cash" );

                            $response = array(
                                'status'	=> 'APPROVED',
                                'order'	=> $orderCreated['OrderId']
                            );
                            wp_send_json($response);
                    } else {
                        if( isset($_POST['form']['token']) && !empty($_POST['form']['token']))
                        {
                            if(isset($_POST['form']['saveCard']))
                                $saveCard = $_POST['form']['saveCard'];
                            else
                                $saveCard = false;

                            $customerToken = $customer["customer_token"];

                            if($orderCreated['taxable'])
                                $paid = $this->moo_PayOrderUsingSpreedly($_POST['form']['token'],$orderCreated['OrderId'],$orderCreated['amount'],$orderCreated['taxamount'],$tipAmount,$saveCard,$customerToken);
                            else
                                $paid = $this->moo_PayOrderUsingSpreedly($_POST['form']['token'],$orderCreated['OrderId'],$orderCreated['sub_total'],'0',$tipAmount,$saveCard,$customerToken);


                            $paymentResult = json_decode($paid);
                            $response = array(
                                'status'	=> $paymentResult->result,
                                'order'	=> $orderCreated['OrderId']
                            );

                            if($paymentResult->result == 'APPROVED')
                            {
                                $this->api->NotifyMerchant($orderCreated['OrderId'],$_POST['form']['instructions'],$pickup_time,$paymentmethod);

                                /* to debug uncomment this line, to not empty tha cart and you can send the order again */
                                //return false;

                                $this->SendSmsToMerchant($orderCreated['OrderId'],'is paid with CC',$pickup_time,$orderTypeFromLocal['label']);
                                $this->sendEmailsAboutOrder($orderCreated['OrderId'],$MooOptions['merchant_email'],$_POST['form']['email']);
                                //$this->SendSmsToCustomer($orderCreated['OrderId'],$customer['phone']);
                                $this->model->updateOrder($orderCreated['OrderId'],$paymentResult->paymentId);



                                $this->session->delete("items");
                                $this->session->delete("itemsQte");
                                $this->session->delete("coupon");

                                do_action("moo_action_order_created", $orderCreated['OrderId'], "saved_credit_card" );

                                wp_send_json($response);
                            } else {
                                if($paymentResult->failureMessage == null)
                                    $response = array(
                                        'status'	=> 'Error',
                                        'message'	=> "Payment card was declined. Check card info or try another card.",
                                        'CloverMessage'	=> $paid,
                                    );
                                else
                                    $response = array(
                                        'status'	=> $paymentResult->result,
                                        'message'	=> 'Payment card was declined. Check card info or try another card.',
                                        'CloverMessage'	=> $paymentResult->failureMessage
                                    );
                                wp_send_json($response);
                            }

                        } else {
                            if( !empty($_POST['form']['lastFour']) && !empty($_POST['form']['firstSix']) && !empty($_POST['form']['expiredDateMonth']) && !empty($_POST['form']['expiredDateYear']) )
                            {
                                if(isset($_POST['form']['cardcvv'])&& !empty($_POST['form']['cardcvv'])) {
                                    $cvv = sanitize_text_field($_POST['form']['cardcvv']);
                                } else {
                                    $cvv = '';
                                    $response = array(
                                        'status'	=> 'Error',
                                        'message'	=> 'Card CVV is required'
                                    );
                                    wp_send_json($response);
                                }

                                if($_POST['form']['zipcode'] && !empty($_POST['form']['zipcode'])) {
                                    $zip = sanitize_text_field($_POST['form']['zipcode']);
                                } else {
                                    $zip = '';
                                    $response = array(
                                        'status'	=> 'Error',
                                        'message'	=> 'Zip code is required'
                                    );
                                    wp_send_json($response);
                                }
                                $last4  = $_POST['form']['lastFour'];
                                $first6 = $_POST['form']['firstSix'];

                                if($orderCreated['taxable'])
                                    $paid = $this->moo_PayOrder($_POST['form']['cardEncrypted'],$first6,$last4,$cvv,$_POST['form']['expiredDateMonth'],$_POST['form']['expiredDateYear'],
                                        $orderCreated['OrderId'],$orderCreated['amount'],$orderCreated['taxamount'],$zip,$tipAmount);
                                else
                                    $paid = $this->moo_PayOrder($_POST['form']['cardEncrypted'],$first6,$last4,$cvv,$_POST['form']['expiredDateMonth'],$_POST['form']['expiredDateYear'],
                                        $orderCreated['OrderId'],$orderCreated['sub_total'],'0',$zip,$tipAmount);

                                $response = array(
                                    'status'	=> json_decode($paid)->result,
                                    'order'	=> $orderCreated['OrderId']
                                );
                                if($response['status'] == 'APPROVED')
                                {
                                    $this->api->NotifyMerchant($orderCreated['OrderId'],$_POST['form']['instructions'],$pickup_time,$paymentmethod);

                                    $this->SendSmsToMerchant($orderCreated['OrderId'],'is paid with CC',$pickup_time,$orderTypeFromLocal['label']);

                                    $this->sendEmailsAboutOrder($orderCreated['OrderId'],$MooOptions['merchant_email'],$_POST['form']['email']);

                                    //$this->SendSmsToCustomer($orderCreated['OrderId'],$customer['phone']);
                                    $this->model->updateOrder($orderCreated['OrderId'],json_decode($paid)->paymentId);

                                    $this->session->delete("items");
                                    $this->session->delete("itemsQte");
                                    $this->session->delete("coupon");

                                    do_action("moo_action_order_created", $orderCreated['OrderId'], "credit_card" );

                                    wp_send_json($response);
                                }
                                else
                                {
                                    if(json_decode($paid)->failureMessage == null)
                                    {
                                        if(json_decode($paid)->message == null)
                                        {
                                            $response = array(
                                                'status'	=> 'Error',
                                                'message'	=> "Payment card was declined. Check card info or try another card.",
                                                'CloverMessage'	=> $paid,
                                            );
                                        }
                                        else
                                            $response = array(
                                                'status'	=> 'Error',
                                                'message'	=> json_decode($paid)->message,
                                                'CloverMessage'	=> $paid,
                                            );
                                    }
                                    else
                                        $response = array(
                                            'status'	=> json_decode($paid)->result,
                                            'message'	=> 'Payment card was declined. Check card info or try another card.',
                                            'CloverMessage'	=> json_decode($paid)->failureMessage
                                        );
                                    wp_send_json($response);
                                }

                            }
                            else
                            {
                                if(isset($MooOptions['scp']) && $MooOptions['scp'] =='on') {
                                    /* Update order note */
                                    $merchant_website = esc_url(get_permalink($MooOptions["store_page"]));
                                    $note = array(
                                        "customer"=>array(
                                            "name"=>$customer["name"],
                                            "phone"=>$customer["phone"],
                                            "email"=>$customer["email"],
                                            "address"=>$customer["address"],
                                            "line2"=>$customer["line2"],
                                            "zipcode"=>$customer["zipcode"],
                                            "city"=>$customer["city"],
                                            "state"=>$customer["state"],
                                        ),
                                        'orderType'=>$orderTypeFromLocal['label'],
                                        'merchantPhone'=>$MooOptions['merchant_phone'],
                                        'merchantEmails'=>$MooOptions['merchant_email'],
                                        'pickuptime'=>$pickup_time,
                                        'instructions'=>$_POST['form']['instructions'],
                                        'site_url'=>$merchant_website,
                                        'redirect_url'=>$MooOptions['thanks_page']
                                    );
                                    $result = json_decode($this->api->updateOrderNote($orderCreated['OrderId'],json_encode($note)));

                                    /* Save order in local db */
                                    $this->model->updateOrder($orderCreated['OrderId'],'SCP');

                                    /* to debug uncomment this line, to not empty tha cart and you can send the order again */
                                   // wp_send_json(array("status"=>"failed"));

                                    /* Empty the session */
                                    $this->session->delete("items");
                                    $this->session->delete("itemsQte");
                                    $this->session->delete("coupon");

                                    do_action("moo_action_order_created", $orderCreated['OrderId'], "scp" );

                                    /* redirect the customer to SCP */
                                    if(isset($result->merchant) && isset($result->orderid))
                                    {
                                        $url = 'https://checkout.smartonlineorder.com/pay/'.strtolower($result->merchant).'/'.strtolower($result->orderid);
                                        $response = array(
                                            'status'	=> 'REDIRECT',
                                            'url'	=> $url
                                        );
                                        wp_send_json($response);
                                    }
                                    else
                                    {
                                        $response = array(
                                            'status'	=> 'Error',
                                            'message'	=> 'credit card information are required'
                                        );
                                        wp_send_json($response);
                                    }
                                }
                                else {
                                    $response = array(
                                        'status'	=> 'Error',
                                        'message'	=> 'credit card information are required'
                                    );
                                    wp_send_json($response);
                                }

                            }
                        }
                    }

                } else {
                  //  var_dump($orderCreated);
                    $response = array(
                        'status'	=> 'Error',
                        'message'	=> 'Internal Error, please contact us, if you\'re the site owner verify your API Key and the Order types'
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
                'message'	=> 'Unauthorized or session is expired please refresh the page'
            );
            wp_send_json($response);
        }
    }
    private function moo_CreateOrder($ordertype,$taxable,$deliveryfee,$deliveryfeeName,$serviceFee,$serviceFeeName,$paymentmethod,$tipAmount,$isDelivery,$instructions,$pickupTime,$customer,$note)
    {
        $total = self::moo_cart_getTotal(true);
        $amount    = floatval(str_replace(',', '', $total['total']));
        $sub_total = floatval(str_replace(',', '', $total['sub_total']));
        $taxAmount = floatval(str_replace(',', '', $total['total_of_taxes']));


        $couponCode = "";
        $use_couponsApp = false;
        $use_maxValue = false;

        $coupon = $total['coupon'];

        if($coupon != null)
        {
          if(!$taxable)
          {
              if($coupon["type"]=='amount')
                  $sub_total -= $coupon['value'];
              else
                  $sub_total -= $coupon['value']*$sub_total/100;
          }

          $couponCode = $coupon["code"];

          if(isset($coupon['use_couponsApp'])) {
              $use_couponsApp = $coupon['use_couponsApp'];
          }

          if(isset($coupon['use_maxValue'])) {
              $use_maxValue = $coupon['use_maxValue'];
          }
        }

        if($total['status'] == 'success'){

            $orderOptions = array (
                "total"=>$amount,
                "OrderType"=>$ordertype,
                "paymentmethod"=>$paymentmethod,
                "taxAmount"=>$taxAmount,
                "deliveryfee"=>$deliveryfee,
                "deliveryName"=>$deliveryfeeName,
                "servicefee"=>$serviceFee,
                "servicefeeName"=>$serviceFeeName,
                "tipAmount"=>$tipAmount,
                "isDelivery"=>$isDelivery,
                "coupon"=>$couponCode,
                "use_couponsApp"=>$use_couponsApp,
                "use_maxValue_for_coupon"=>$use_maxValue,
                "instructions"=>$instructions,
                "pickupTime"=>$pickupTime,
                "note"=>$note,
                "customer"=>json_encode($customer)
            );
            if(!$taxable)
                $orderOptions["total"] = $sub_total;

            $order = $this->api->createOrder($orderOptions);
            $order = json_decode($order);

            if(isset($order->id)){
                // Add Items to order
                // deprecated : will changed in version to support bulk add
                foreach($this->session->get("items") as $cartLine)
                {
                    // If the item is empty skip to the next iteration of the loop
                    if(!isset($cartLine['item']) || $cartLine['item']->uuid == "delivery_fees" || $cartLine['item']->uuid == "service_fees") continue;

                    // Create line item
                    if(isset($cartLine['modifiers'])  && is_array($cartLine['modifiers']) && count($cartLine['modifiers']) > 0) {
                        for($i=0;$i<$cartLine['quantity'];$i++){
                            $res = $this->api->addlineToOrder($order->id,$cartLine['item']->uuid,'1',$cartLine['special_ins']);
                            $lineId = json_decode($res)->id;
                            foreach ($cartLine['modifiers'] as $modifier) {
                                if(isset($modifier["qty"]) && intval($modifier["qty"])>1) {
                                    for($k=0;$k<$modifier["qty"];$k++)
                                        $this->api->addModifierToLine($order->id,$lineId,$modifier['uuid']);
                                } else {
                                    $this->api->addModifierToLine($order->id,$lineId,$modifier['uuid']);
                                }
                            }
                        }
                    } else {
                        $this->api->addlineToOrder($order->id,$cartLine['item']->uuid,$cartLine['quantity'],$cartLine['special_ins']);
                    }
                }

                // add all lines in one request
                // for testing purpose
                try{
                    $this->api->addLinesToOrder($order->id,$this->session->get("items") );
                } catch (Exception $e) {
                    //echo $e->getMessage();
                }


                //Return teh order details after adding all lines
                return
                    array("OrderId"=>$order->id,"amount"=>$amount,"taxamount"=>$taxAmount,"taxable"=>$taxable,"sub_total"=>$sub_total,'order'=>$order);

            } else {
                if(isset($order->message) && $order->message !=''){
                    $response = array(
                        'status'	=> 'Error',
                        'message'	=> 'Internal Error, we cannot create the order : '.$order->message.' <br> Please call the store and let them know so it can be resolved'
                    );
                    wp_send_json($response);
                }
                return false;
            }
        }
        else
            return false;


    }
    private function moo_PayOrder($cardEncrypted,$first6,$last4,$cvv,$expMonth,$expYear,$orderId,$amount,$taxAmount,$zip,$tipAmount){


        $amount     = str_replace(',', '', $amount);
        $taxAmount  = str_replace(',', '', $taxAmount);
        $tipAmount  = str_replace(',', '', $tipAmount);

        //$card_number = str_replace(' ','',trim($card_number));
        $cvv       = sanitize_text_field($cvv);
        $expMonth  = intval($expMonth);
        $expYear   = intval($expYear);
        $orderId   = sanitize_text_field($orderId);
        $amount    = floatval($amount);
        $taxAmount = floatval($taxAmount);

        //$last4  = substr($card_number,-4);
        //$first6 = substr($card_number,0,6);

        $res = $this->api->payOrder($orderId,$taxAmount,$amount,$zip,$expMonth,$cvv,$last4,$expYear,$first6,$cardEncrypted,$tipAmount);
        return $res;

    }
    private function moo_PayOrderUsingSpreedly($token,$orderId,$amount,$taxAmount,$tipAmount,$saveCard,$customerToken)
    {


        $amount = str_replace(',', '', $amount);
        $taxAmount = str_replace(',', '', $taxAmount);
        $tipAmount = str_replace(',', '', $tipAmount);

        $orderId   = sanitize_text_field($orderId);
        $amount    = floatval($amount);
        $taxAmount = floatval($taxAmount);

        $res = $this->api->moo_PayOrderUsingSpreedly($token,$orderId,$taxAmount,$amount,$tipAmount,$saveCard,$customerToken);
        return $res;

    }

    public function moo_GetOrderTypes()
    {
       $OrdersTypes = $this->api->GetOrdersTypes();
       if(@count($OrdersTypes)>0)
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
    public function moo_SendVerifSMS()
    {
        $phone_number = sanitize_text_field($_POST['phone']);
        if(! $this->session->isEmpty("moo_verification_code") && $phone_number == $this->session->get("moo_phone_number") ) {
            $verification_code = $this->session->get("moo_verification_code");
        } else {
            $verification_code = rand(100000,999999);
            $this->session->set($verification_code,"moo_verification_code");
        }
        $this->session->set($phone_number,"moo_phone_number");
        $this->session->set(false,"moo_phone_verified");

        $res = $this->api->sendSms($verification_code,$phone_number);
        $response = array(
            'status'	=> 'success',
           ///  'code'	=> $verification_code,
            'result'    => $res
        );
        wp_send_json($response);
    }

    public function moo_CheckVerificationCode()
    {
        $verification_code = sanitize_text_field($_POST['code']);
        if($verification_code != null && $verification_code != "" && $verification_code ==  $this->session->get("moo_verification_code") )
        {
            $response = array(
                'status'	=> 'success'
            );
            $this->session->set(true,"moo_phone_verified");

            if(! $this->session->isEmpty("moo_customer_token"))
                $this->api->moo_CustomerVerifPhone($this->session->get("moo_customer_token"), $this->session->get("moo_phone_number"));
            $this->session->delete("moo_verification_code");
        }
        else
            $response = array(
                'status'	=> 'error'
            );

        wp_send_json($response);
    }

	public function moo_getAllOrderTypes()
    {
        $OrdersTypes = $this->model->getOrderTypes();
       if(@count($OrdersTypes)>0)
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
	    $minAmount =  sanitize_text_field($_POST['minAmount']);
	    $show_sa =  sanitize_text_field($_POST['show_sa']);
        $OrderType = $this->api->addOrderType($label,$taxable);
        if($OrderType) {
           $OrderT_obj = json_decode($OrderType);
           $this->api->save_One_orderType($OrderT_obj->id,$label,$taxable,$minAmount,$show_sa);
           $response = array(
               'status'	=> 'success',
               'data'	=> $OrderType
           );
           wp_send_json($response);
        } else {
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
       $response = array(
           'status'	=> 'Success',
           'data'=> $res
       );
       wp_send_json($response);
   }

    public function moo_ImportLabels() {
        $res = $this->api->getModifierGroups();
        $this->api->getModifiers();
        $this->api->getAttributes();
        $this->api->getOptions();
       $response = array(
           'status'	=> 'Success',
           'data'=> $res
       );
       wp_send_json($response);
   }
   public function moo_ImportTaxes(){
       $this->api->getOrderTypes();
       $res= $this->api->getTaxRates();
       $response = array(
           'status'	=> 'Success',
           'data'=> $res
       );
       wp_send_json($response);
   }
    public function moo_ImportItems()
   {
       $res = $this->api->getItems();
       $response = array(
           'status'	=> 'Success',
           'data'=> $res
       );
       wp_send_json($response);
   }
   public function moo_ImportItemsV2() {
        $page = sanitize_text_field($_POST['page']);
        if($page<0 || !is_numeric($page))
            $page = 0;
        $res = $this->api->getItemsWithoutSaving($page);
        $result = json_decode($res);
        $saved = $this->api->save_items($res);
        $produbtsNb = $this->model->NbProducts();
        $response = array(
            'status'	 => 'Success',
            'received'	 => @count($result->elements),
            'saved'	 => $saved,
            'currentNb'=> (isset($produbtsNb[0]->nb) && $produbtsNb[0]->nb>0)?$produbtsNb[0]->nb:0
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
       $labels   = $this->model->NbModifierGroups();
       $taxes    = $this->model->NbTaxes();
       $products = $this->model->NbProducts();

       $response = array(
           'status'	 => 'Success',
           'cats'    => (isset($cats[0]->nb) && $cats[0]->nb>0)?$cats[0]->nb:0,
           'labels'  => (isset($labels[0]->nb) && $labels[0]->nb>0)?$labels[0]->nb:0,
           'taxes'   => (isset($taxes[0]->nb) && $taxes[0]->nb>0)?$taxes[0]->nb:0,
           'products'=> (isset($products[0]->nb) && $products[0]->nb>0)?$products[0]->nb:0
       );
       wp_send_json($response);
   }
    public function moo_UpdateOrdertype()
   {
       $uuid = $_POST["uuid"];
       $name = $_POST["name"];
       $enable = $_POST["enable"];
       $taxable = $_POST["taxable"];
       $type = $_POST["type"];
       $minAmount = $_POST["minAmount"];
       $res = $this->model->updateOrderType($uuid,$name,$enable,$taxable,$type,$minAmount);
       //update in Clover
       $cloverResponse  = $this->api->updateOrderType($uuid,$name,$taxable);
       $result = json_decode($cloverResponse,true);
       if(isset($result["message"]) && $result["message"] === 'Not Found') {
           $updated = false;
       } else {
           $updated = true;
       }
       $response = array(
           'status'	 => 'Success',
           'data'    => $res,
           'updated' => $updated
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
           //var_dump($_POST['data']);
           $default_options = (array)get_option('moo_settings');
	       $message   =  sanitize_text_field($_POST['data']['message']);
	       $email     =  sanitize_text_field($_POST['data']['email']);
	       $name      =  sanitize_text_field($_POST['data']['name']);
	       $bname     =  sanitize_text_field($_POST['data']['bname']);
	       $phone     =  sanitize_text_field($_POST['data']['phone']);
	       $website   =  sanitize_text_field($_POST['data']['website']);

           $message .='-----------<br/>';
           $message .='Email  '.$email.'<br/>';
           $message .='Full name : '.$name.'<br/>';
           $message .='Business Name : '.$bname.'<br/>';
           $message .='Website : '.$website.'<br/>';
           $message .='Phone : '.$phone.'<br/>';
           $message .='Plugin Version : '.$this->version.'<br/>';
           $message .='Default Style  : '.$this->style.'<br/>';
           $message .='API Key  : '.$default_options['api_key'].'<br/>';
           $message .='Email in settings  : '.$default_options['merchant_email'];

	       $res = wp_mail(array("support@zaytechapps.com","m.elbanyaoui@gmail.com"), 'Feedback from Wordpress plugin user', $message);
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

    /* Manage Modifiers */
    public function moo_ChangeModifierGroupName()
    {
        $mg_uuid  = sanitize_text_field($_POST['mg_uuid']);
        $name     = sanitize_text_field($_POST['mg_name']);
        $res      = $this->model->ChangeModifierGroupName($mg_uuid,$name);

        $response = array(
            'status'	 => 'Success',
            'data'=>$res
        );
        wp_send_json($response);

    }
    function moo_ChangeModifierName()
    {
        $m_uuid   = sanitize_text_field($_POST['m_uuid']);
        $name     = sanitize_text_field($_POST['m_name']);
        $res      = $this->model->ChangeModifierName($m_uuid,$name);

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

    function moo_UpdateModifierStatus(){
        $mg_uuid  = sanitize_text_field($_POST['mg_uuid']);
        $status   = sanitize_text_field($_POST['mg_status']);
        $res      = $this->model->UpdateModifierStatus($mg_uuid,$status);
        wp_send_json($res);
    }
    function moo_ChangeCategoryName()
    {
        $cat_uuid  = sanitize_text_field($_POST['cat_uuid']);
        $name      = sanitize_text_field($_POST['cat_name']);
        $res       = $this->model->ChangeCategoryName($cat_uuid,$name);

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
    public function moo_getItemWithImages()
    {
        $item_uuid = sanitize_text_field($_POST['item_uuid']);
        $res       = $this->model->getItemWithImage($item_uuid);
        $response = array(
            'status'	 => 'Success',
            'data'=>$res
        );
        wp_send_json($response);
    }
    public function moo_saveItemWithImages()
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
    public function moo_saveItemDescription()
    {
        $item_uuid   = sanitize_text_field($_POST['item_uuid']);
        $description = sanitize_text_field($_POST['description']);

        $res = $this->model->saveItemDescription($item_uuid,$description);
        $response = array(
            'status'	 => 'Success',
            'data'=>$res
        );
        wp_send_json($response);
    }

    public function moo_UpdateCategoryStatus()
    {
        $cat_uuid  = sanitize_text_field($_POST['cat_uuid']);
        $status    = sanitize_text_field($_POST['cat_status']);
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
    public function moo_StoreIsOpen()
    {
        $MooOptions = (array)get_option('moo_settings');

        if($MooOptions['hours'] == 'business')
        {
            $res = $this->api->getOpeningStatus(4,30);
            $stat = json_decode($res)->status;
            $response = array(
                'status'     => 'Success',
                'data'=>$stat,
                'infos'=>$res
            );
            wp_send_json($response);
        }
        else
        {
            $response = array(
                'status'     => 'Success',
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
    public function moo_SyncHandle()
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

    public function moo_UpdateItems()
    {
        $page = sanitize_text_field($_POST['page']);
        if($page<0 || !is_numeric($page))
            $page=0;
        $compteur = 0;
        $res = json_decode($this->api->getItemsWithoutSaving($page));
       // var_dump($res);
        if(isset($res->message) && !isset($res->elements) ) return;

        foreach ($res->elements as $item)
        {
            if($this->api->update_item($item)) $compteur++;
        }
        $response = array(
            'status'	 => 'Success',
            'received'	 => @count($res->elements),
            'updated'=>$compteur
        );
        wp_send_json($response);
    }

    public function moo_UpdateCategories()
    {
        $compteur = 0;
        $res = json_decode($this->api->getCategoriesWithoutSaving());

        if(isset($res->message) && !isset($res->elements) ) return;

        foreach ($res->elements as $category)
        {
            if($this->api->update_category($category)) $compteur++;
        }
        $response = array(
            'status'	 => 'Success',
            'received'	 => @count($res->elements),
            'updated'=>$compteur
        );
        wp_send_json($response);
    }
    public function moo_UpdateModifiersG()
    {
        $compteur = 0;
        $res  = json_decode($this->api->getModifiersGroupsWithoutSaving());

        foreach ($res->elements as $modifierG)
        {
            if($this->api->update_modifierGroups($modifierG)) $compteur++;
        }

        $response = array(
            'status'	 => 'Success',
            'ModiferG_received'	 => @count($res->elements),
            'ModifierG_updated'=>$compteur
        );
        wp_send_json($response);
    }
    public function moo_UpdateModifiers()
    {
        $compteur = 0;
        $res = json_decode($this->api->getModifiersWithoutSaving());
        $total  = @count($res->elements);
        if( $total == 1000) {
            $res2 = json_decode($this->api->getModifiersWithoutSavingPage2());
            $total += @count($res2->elements);
            foreach ($res2->elements as $modifier) {
                if($this->api->update_modifier($modifier))
                    $compteur++;
            }
        }

        foreach ($res->elements as $modifier) {
            if($this->api->update_modifier($modifier))
                $compteur++;
        }

        $response = array(
            'status'	 => 'Success',
            'Modifer_received'	 => $total,
            'Modifier_updated'=>$compteur
        );
        wp_send_json($response);
    }
    public function moo_UpdateTaxes()
    {
        $compteur = 0;
        $res = json_decode($this->api->getTaxesRatesWithoutSaving());

        foreach ($res->elements as $tax)
        {
            if($this->api->update_taxRate($tax)) $compteur++;
        }

        $response = array(
            'status'	 => 'Success',
            'tax_received'	 => @count($res->elements),
            'tax_updated'=>$compteur
        );
        wp_send_json($response);
    }
    public function moo_UpdateOrderTypes()
    {
        $compteur = 0;
        $res = json_decode($this->api->getOrderTypesWithoutSaving());
        foreach ($res->elements as $orderType) {
            if($this->api->update_orderType($orderType))
                $compteur++;
        }

        $response = array(
            'status'	 => 'Success',
            'orderTypes_received'	 => @count($res->elements),
            'orderTypes_updated'=>$compteur
        );
        wp_send_json($response);
    }
    private function sendEmailsAboutOrder($order_id,$merchant_emails,$customer_email)
    {
        @$this->api->sendOrderEmails($order_id,$merchant_emails,$customer_email);
    }
    private function SendSmsToMerchant($orderID,$PaymentMethod,$pickuptime,$ordertype)
    {
            $MooOptions = (array)get_option('moo_settings');
            if(isset($MooOptions['merchant_phone']) && $MooOptions['merchant_phone'] != '' )
            {
                $message = 'You have received a new order ('.$ordertype.') and this order '.$PaymentMethod.' '.$pickuptime.' It can be seen at this link http://www.clover.com/r/'.$orderID;
                $phones = $MooOptions['merchant_phone'];
                $phones = explode('__',$phones);
                foreach ($phones as $phone) {
                    $this->api->sendSmsTo($message,$phone);
                }

            }
    }
    private function SendSmsToCustomer($orderID,$phone)
    {
            if($phone != '' )
            {
                $message = 'Thank you for your order, You can see your receipt at this link http://www.clover.com/r/'.$orderID;
                $this->api->sendSmsTo($message,$phone);
            }
    }

    // visibility category
    public function visibility_category()
    {
        $id = $_POST["id_cat"];
        $status = $_POST["visiblite"];
        $ret = $this->model->UpdateCategoryStatus($id,$status);
        wp_send_json($ret);
    }
    public function save_image_category(){
        $uuid = $_POST["category_uuid"];
        $url = $_POST["image"];
        $ret = $this->model->saveImageCategory($uuid,$url);
        wp_send_json($ret);
    }

    public function new_order_categories(){
        $newdata = $_POST["newtable"];
        $ret = $this->model->saveNewCategoriesorder($newdata);
        wp_send_json($ret);
    }

    public function delete_img_category(){
        $uuid = $_POST["uuid"];
        $ret = $this->model->moo_DeleteImgCategorie($uuid);
        wp_send_json($ret);
    }

    public function change_name_category(){
        $uuid = $_POST["id_cat"];
        $newName = $_POST["newName"];
        $ret = $this->model->moo_UpdateNameCategorie($uuid,$newName);
        wp_send_json($ret);
    }
    public function moo_UpdateCategoryImagesStatus(){
        $status = $_POST["status"];
        $DefaultOption = (array)get_option('moo_settings');
        $DefaultOption['show_categories_images'] = $status;
        update_option("moo_settings",$DefaultOption);
        wp_send_json($status);
    }
    public function moo_NewOrderGroupModifier(){
        $newdata = $_POST["newtable"];
        $ret = $this->model->saveNewOrderGroupModifier($newdata);
        wp_send_json($ret);
    }

    public function moo_NewOrderModifier(){
        $group = sanitize_text_field($_POST["group_id"]);
        $newdata = $_POST["newtable"];
        $ret = $this->model->saveNewOrderModifier($group,$newdata);
        wp_send_json($ret);
    }
    public function moo_reorder_items(){
        $OrderedItems = $_POST["newtable"];
        $res = $this->model->reOrderItems($OrderedItems);
        wp_send_json($res);
    }
    public function moo_CustomerLogin()
    {
        $email    = sanitize_text_field($_POST["email"]);
        $password = sanitize_text_field($_POST["password"]);
        $res = $this->api->moo_CustomerLogin($email,sha1($password));
        $result= json_decode($res);
        if($result->status == 'success') {
            $this->session->set($result->token,"moo_customer_token");
            $this->session->set($result->customer_email,"moo_customer_email");
        } else {
            $this->session->set(false,"moo_customer_token");
            $this->session->set(null,"moo_customer_email");
        }
        wp_send_json((array)$result);
    }
    public function moo_CustomerFbLogin()
    {
        $customerOptions = array(
            "gender"     => sanitize_text_field($_POST["gender"]),
            "name" => sanitize_text_field($_POST["name"]),
            "email"     => sanitize_text_field($_POST["email"]),
            "id"     => sanitize_text_field($_POST["fbid"])
        );
        $res = $this->api->moo_CustomerFbLogin($customerOptions);
        $result= json_decode($res);
        if($result->status == 'success')
        {
            $this->session->set($result->token,"moo_customer_token");
            $this->session->set($result->customer_email,"moo_customer_email");
        } else {
            $this->session->set(false,"moo_customer_token");
            $this->session->set(null,"moo_customer_email");
        }

        wp_send_json((array)$result);
    }
    public function moo_CustomerSignup()
    {
        $password  = sanitize_text_field($_POST["password"]);
        $password  = sha1($password);
        $customerOptions = array(
            "title"     => sanitize_text_field($_POST["title"]),
            "full_name" => sanitize_text_field($_POST["full_name"]),
            "email"     => sanitize_text_field($_POST["email"]),
            "phone"     => sanitize_text_field($_POST["phone"]),
            "password"  => $password,
        );
        $res = $this->api->moo_CustomerSignup($customerOptions);
        $result= json_decode($res);
        if($result->status == 'success')
        {
            $this->session->set($result->token,"moo_customer_token");
            $this->session->set($result->customer_email,"moo_customer_email");
        }
        wp_send_json((array)$result);

    }

    public function moo_ResetPassword()
    {
        $email     = sanitize_text_field($_POST["email"]);
        $res = $this->api->moo_ResetPassword($email);
        wp_send_json(json_decode($res));
    }
    public function moo_setDefaultAddresses()
    {

    }
    public function moo_updateAddresses()
    {

    }

    public function moo_GetAddresses()
    {
        if(!$this->session->isEmpty("moo_customer_token"))
        {
            $token = $this->session->get("moo_customer_token");
            $res = $this->api->moo_GetAddresses($token);
            $result= json_decode($res);
            if($result->status == 'success' && count($result->customer)>0)
            {
                $res = array(
                    "status"=>"success",
                    "addresses"=>$result->addresses,
                    "customer"=>$result->customer,
                    "cards"=>$result->cards
                );
                $this->session->set($result->customer,"moo_customer");
            }
            else
            {
                $this->session->set(false,"moo_customer_token");
                $this->session->set(null,"moo_customer_email");
                $this->session->set(null,"moo_customer");
                $res = array("status"=>"failure","message"=>'You must logged first');
            }

        } else {
            $res = array("status"=>"failure","message"=>'You must logged first');
        }

        wp_send_json($res);
    }
    public function moo_AddAddress()
    {
        if(!$this->session->isEmpty("moo_customer_token"))
        {
            $addressOptions = array(
                "token"     => $this->session->get("moo_customer_token"),
                "address"   =>  sanitize_text_field($_POST['address']),
                "line2"     =>  sanitize_text_field($_POST['line2']),
                "city"      =>  sanitize_text_field($_POST['city']),
                "state"     =>  sanitize_text_field($_POST['state']),
                "zipcode"   =>  sanitize_text_field($_POST['zipcode']),
                "country"   =>  sanitize_text_field($_POST['country']),
                "lng"       =>  sanitize_text_field( $_POST['lng']),
                "lat"       =>  sanitize_text_field($_POST['lat'])

            );
            $res = $this->api->moo_AddAddress($addressOptions);
            $result= json_decode($res);

            if($result->status == 'success')
            {
                $res = array("status"=>"success","addresses"=>$result->addresses);
            }
            else
            {
                $res = array("status"=>$result->status);
            }

        }
        else
            $res = array("status"=>"failure","message"=>'You must logged first');
        wp_send_json($res);
    }
    public function moo_DeleteAddresses()
    {
        if(!$this->session->isEmpty("moo_customer_token"))
        {
            $token = $this->session->get("moo_customer_token");
            $address_id = $_POST['address_id'];
            $res = $this->api->moo_DeleteAddresses($address_id,$token);
            $result= json_decode($res);

            if($result->status == 'success')
            {
                $res = array("status"=>"success");
            }
            else
            {
                $res = array("status"=>$result->status);
            }

        }
        else
            $res = array("status"=>"failure","message"=>'You must logged first');
        wp_send_json($res);
    }
    public function moo_DeleteCreditCard()
    {
        if(!$this->session->isEmpty("moo_customer_token"))
        {
            $token = $this->session->get("moo_customer_token");
            $card_token = sanitize_text_field($_POST['token']);
            $res = $this->api->moo_DeleteCreditCard($card_token,$token);
            $result= json_decode($res);
        } else {
            $result = array("status"=>"failure","message"=>'You must logged first');
        }
        wp_send_json($result);
    }
    public function moo_CouponApply()
    {
        if(isset($this->settings["use_couponsApp"])) {
            $use_couponsApp = ($this->settings["use_couponsApp"]=='on');
        } else {
            $use_couponsApp = false;
        }
        if(isset($_POST['moo_coupon_code']) && $_POST['moo_coupon_code'] != "") {
            $res = array();
            $couponCode = sanitize_text_field($_POST['moo_coupon_code']);
            $coupon = $this->api->moo_checkCoupon($couponCode);
            $coupon = json_decode($coupon,true);
            if($coupon['status'] == "success") {

                $this->session->set($coupon,"coupon");
                $newTotal = self::moo_cart_getTotal(true);
                $res = $this->session->get("coupon");
                $res['total'] = $newTotal;

               if($res['total']['sub_total']<$coupon['minAmount']) {
                   $res = array("status"=>"failure","message"=>'This coupon requires a minimum purchase amount of $'.$res['minAmount']);
               }
            } else {
                $this->session->delete("coupon");
                if($use_couponsApp) {
                    $coupon = $this->api->moo_checkCoupon_for_couponsApp($couponCode);
                    $coupon = json_decode($coupon,true);
                    if(isset($coupon['status']) && $coupon['status'] == "success") {

                        /*
                         * put the coupon in session variable to calculate the new total
                         * then get the coupon again from the session because the total function may
                         * modify the coupon if there is a maxValue property
                         */

                        $this->session->set($coupon,"coupon");
                        $newTotal = self::moo_cart_getTotal(true);
                        $res = $this->session->get("coupon");;
                        $res['total'] = $newTotal;

                        if($res['total']['sub_total']<$coupon['minAmount']) {
                            $res = array("status"=>"failure","message"=>'This coupon requires a minimum purchase amount of $'.$res['minAmount']);
                        }
                    } else {
                        $res = $coupon;
                        if($coupon == null) {
                            $res = array("status"=>"failure","message"=>'Please enter a valid coupon code');
                        }
                    }
                } else {
                    $res = $coupon;
                    if($coupon == null) {
                        $res = array("status"=>"failure","message"=>'Please enter a valid coupon code');
                    }
                }
            }
        } else {
            $res = array("status"=>"failure","message"=>'Please enter your coupon code');
        }
        wp_send_json($res);
    }

    public function moo_CouponRemove() {
        $this->session->delete("coupon");
        $res = array("status"=>"success");
        $res['total'] = self::moo_cart_getTotal(true);
        wp_send_json($res);
    }

    public function moo_ReorderOrderTypes(){
        $table = $_POST["newtable"];
        $res = $this->model->saveNewOrderOfOrderTypes($table);
        wp_send_json($res);
    }

    /**
     * @param $items
     * @param $item_uuid
     * @return bool
     */
    private function getItemStock($items,$item_uuid) {
        foreach ($items as $i) {
            if($i->item->id == $item_uuid)
                return $i;
        }
        return false;
    }

}
