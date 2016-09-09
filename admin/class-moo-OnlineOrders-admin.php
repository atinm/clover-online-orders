<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class moo_OnlineOrders_Admin {

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
    private $model;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

        require_once plugin_dir_path( dirname(__FILE__))."admin/model/class-moo-OnlineOrders-model-admin.php";

       
		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action( 'admin_init',  array($this, 'register_mysettings' ));
        add_action( 'admin_bar_menu', array($this, 'toolbar_link_to_settings'), 999 );
        add_action("admin_enqueue_scripts", function(){wp_enqueue_media();});
        $this->model = new moo_OnlineOrders_Admin_Model();

	}

  public function page_products()
    {
        require_once plugin_dir_path( dirname(__FILE__))."admin/includes/class-moo-products-list.php";
        $products = new Products_List_Moo();
        $products->prepare_items();
        if(isset($_GET['action']) && $_GET['action'] == 'update_item')
        {
            if(isset($_GET['item_uuid']) && $_GET['item_uuid'] != '')
            {
                $item_uuid = $_GET['item_uuid'];
                
              ?>
                <div class="wrap" xmlns="http://www.w3.org/1999/html">
                    <h2>Edit an Item</h2>
                    <div id="moo_editItem">
                        <div class="moo_editItem_left">
                            <h3>Item Name</h3> <p id="moo_item_name"></p>
                            <h3>Item Price</h3><p id="moo_item_price"></p>
                            <h3>Item Description</h3>
                            <div id="titlediv">
                                <textarea name="" rows="4" id="moo_item_description"></textarea>
                            </div>
                            <h3>ADD TO CART BUTTON</h3>
                            <p>
                                <code>
                                    [moo_buy_button id='<?php echo $item_uuid?>']
                                </code>
                            </p>
                            <a href="#" class="button button-primary" onclick="moo_save_item_images('<?php echo $item_uuid?>')">Save item</a>
                            <a href="#" class="button button-secondary" onclick="history.back()">Go back</a>
                        </div>
                        <div class="moo_editItem_right">
                            <h3>Images (Square Images for better scaling) </h3>
                            <span class="moo_pull_right" id="moo_uploadImgBtn" onclick="open_media_uploader_image()">Upload Image</span>
                            <div class="moo_itemsimages" id="moo_itemimagesection">
                            </div>
                        </div>

                    </div>
                </div>
                <script type="application/javascript">
                    moo_get_item_with_images('<?php echo $item_uuid?>');
                </script>

                <?php
            }
        }
        else
        {
    ?>
        <div class="wrap">
            <h2>List of products</h2>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <!-- Search Form -->
                            <form method="post">
                                <input type="hidden" name="page" value="moo_products" />
                                <?php $products->search_box('search', 'search_id'); ?>
                            </form>
                            <form method="post">

                                <?php $products->display(); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>

    <?php
        }
    }

    public function page_orders()
    {
        require_once plugin_dir_path( dirname(__FILE__))."admin/includes/class-moo-orders-list.php";

        $orders = new Orders_List_Moo();
        $orders->prepare_items();

    ?>
        <div class="wrap">
            <h2>List of orders</h2>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">

                            <form method="post">
                                <?php $orders->display(); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>

    <?php
    }

    public function page_products_screen_options()
    {
        $option = 'per_page';
        $args   = array(
            'label'   => 'Items',
            'default' => 20,
            'option'  => 'moo_items_per_page'
        );
        add_screen_option( $option, $args );
    }

    public function panel_settings()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/moo-OnlineOrders-CallAPI.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/moo-OnlineOrders-Model.php';
        
        $api   = new  moo_OnlineOrders_CallAPI();
        $model = new  moo_OnlineOrders_Model();

        $errorToken = false;

        //default options
        $MooOptions = (array)get_option('moo_settings');

        if(!isset($MooOptions['api_key']))
            $MooOptions['api_key']='';
        if(!isset($MooOptions['lat']))
            $MooOptions['lat']='';
        if(!isset($MooOptions['lng']))
            $MooOptions['lng']='';
        if(!isset($MooOptions['hours']))
            $MooOptions['hours']='';
        if(!isset($MooOptions['merchant_email']))
            $MooOptions['merchant_email']='';
        if(!isset($MooOptions['thanks_page']))
            $MooOptions['thanks_page']='';
        if(!isset($MooOptions['custom_css']))
            $MooOptions['custom_css']='';
        if(!isset($MooOptions['custom_js']))
            $MooOptions['custom_js']='';
        if(!isset($MooOptions['default_style']))
            $MooOptions['default_style']='';
        if(!isset($MooOptions['tips']))
            $MooOptions['tips']='';
        if(!isset($MooOptions['payment_cash']))
            $MooOptions['payment_cash']='';
        if(!isset($MooOptions['scp']))
            $MooOptions['scp']='';
        if(!isset($MooOptions['merchant_phone']))
            $MooOptions['merchant_phone']='';
        if(!isset($MooOptions['order_later']))
            $MooOptions['order_later']='';
        if(!isset($MooOptions['order_later_days']))
            $MooOptions['order_later_days']='';
        if(!isset($MooOptions['order_later_minutes']))
            $MooOptions['order_later_minutes']='';
        if(!isset($MooOptions['free_delivery']))
            $MooOptions['free_delivery']='';
        if(!isset($MooOptions['fixed_delivery']))
            $MooOptions['fixed_delivery']='';
        if(!isset($MooOptions['other_zones_delivery']))
            $MooOptions['other_zones_delivery']='';
        if(!isset($MooOptions['item_delivery']))
            $MooOptions['item_delivery']='';
        if(!isset($MooOptions['zones_json']))
            $MooOptions['zones_json']='';

        update_option('moo_settings',$MooOptions);

        $token = $MooOptions["api_key"];
        $merchant_proprites = (json_decode($api->getMerchantProprietes())) ;

        if($token != '')
        {
            $this->model->setToken($token);
            $result = $this->model->checkToken();

            if($result == 'Forbidden') $errorToken="( Token invalid )";
            else {
                if(isset(json_decode($result)->status) && json_decode($result)->status =='success') {

                    $merchant_website = get_option( 'moo_store_page');

                    $api->updateWebsiteHooks(esc_url(admin_url('admin-post.php')));
                    $api->updateWebsite(esc_url(get_permalink($merchant_website)));

                    //update_option('moo_store_openingHours',$api->getOpeningHours());

                    $errorToken="( Token valid )";
                }
                else
                    $errorToken="( Token expired )";

            }
        }
        else
            $errorToken="( Required )";
        
        $modifier_groups = $model->getAllModifiersGroup();
        $all_categories  = $model->getCategories();


        /* Start Map Delivery area section */
        $merchant_address =  $api->getMerchantAddress();

        wp_enqueue_script('moo-google-map');
        wp_enqueue_script('moo-map-da',array('jquery','moo-google-map'));
        wp_localize_script("moo-map-da", "moo_merchantAddress",$merchant_address);
        wp_localize_script("moo-map-da", "moo_merchantLat",$MooOptions['lat']);
        wp_localize_script("moo-map-da", "moo_merchantLng",$MooOptions['lng']);
        /* Fin map Delivery area section*/
        ?>
        <div id="MooPanel" class="wrap">
            <div id="MooPanel_sidebar">
                <div id="Moopanel_logo">
                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/woo_100x100.png";?>" alt=""/>
                    <p>Online orders for Clover</p>
                </div>
                <ul>
                    <li class="MooPanel_Selected" id="MooPanel_tab1" onclick="tab_clicked(1)">Key settings</li>
                    <li id="MooPanel_tab2" onclick="tab_clicked(2)">Import Items</li>
                    <li id="MooPanel_tab3" onclick="tab_clicked(3)">Orders Types</li>
                    <li id="MooPanel_tab4" onclick="tab_clicked(4)">Store interface</li>
                    <li id="MooPanel_tab5" onclick="tab_clicked(5)">Categories</li>
                    <li id="MooPanel_tab6" onclick="tab_clicked(6)">Modifiers</li>
                    <li id="MooPanel_tab7" onclick="tab_clicked(7)">Store settings</li>
                    <li id="MooPanel_tab8" onclick="tab_clicked(8)">Delivery areas</li>
                    <li id="MooPanel_tab9" onclick="tab_clicked(9)">Feedback / Help</li>
                    <li id="MooPanel_tab10" onclick="tab_clicked(10)">FAQ</li>
                </ul>
            </div>
            <div id="MooPanel_main">
                <div id="menu_for_mobile">
                    <div style="text-align: center;">
                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/woo_73x73.png";?>" alt=""/>
                    </div>
                    <div class="button_center">
                        <a href="#" id="show_menu" class="button button-secondary">Menu</a>
                    </div>
                    <ul style="font-size:15px; text-align: center; width: 37%; margin: 0 auto; border: 0.5px green;">
                        <li class="MooPanel_Selected" id="MooPanel_tab_1" onclick="tab_clicked(1)">Key settings</li>
                        <li id="MooPanel_tab_2" onclick="tab_clicked(2)">Import Items</li>
                        <li id="MooPanel_tab_3" onclick="tab_clicked(3)">Orders Types</li>
                        <li id="MooPanel_tab_4" onclick="tab_clicked(4)">Store interface</li>
                        <li id="MooPanel_tab_5" onclick="tab_clicked(5)">Categories</li>
                        <li id="MooPanel_tab_6" onclick="tab_clicked(6)">Modifiers</li>
                        <li id="MooPanel_tab_7" onclick="tab_clicked(7)">Store settings</li>
                        <li id="MooPanel_tab_8" onclick="tab_clicked(8)">Delivery areas</li>
                        <li id="MooPanel_tab_9" onclick="tab_clicked(9)">Feedback/Help</li>
                        <li id="MooPanel_tab_10" onclick="tab_clicked(10)">FAQ</li>
                    </ul>
                </div>
                <?php if( $errorToken != "( Token valid )" ) { ?>
                        <form method="post" action="options.php">
                            <?php settings_fields('moo_settings') ?>
                            <input type="text"  name="moo_settings[lat]"     value="<?php echo $MooOptions['lat']?>" hidden />
                            <input type="text"  name="moo_settings[lng]"     value="<?php echo $MooOptions['lng']?>" hidden />
                            <input type="text"  name="moo_settings[hours]"     value="<?php echo $MooOptions['hours']?>" hidden />
                            <input type="text"  name="moo_settings[default_style]"     value="<?php echo $MooOptions['default_style']?>" hidden />
                            <input type="text"  name="moo_settings[merchant_email]"     value="<?php echo $MooOptions['merchant_email']?>" hidden/>
                            <input type="text"  name="moo_settings[thanks_page]"     value="<?php echo $MooOptions['thanks_page']?>" hidden/>
                            <input type="text"  name="moo_settings[custom_css]"     value="<?php echo $MooOptions['custom_css']?>" hidden/>
                            <input type="text"  name="moo_settings[custom_js]"     value="<?php echo $MooOptions['custom_js']?>" hidden/>
                            <input type="text"  name="moo_settings[free_delivery]"     value="<?php echo $MooOptions['free_delivery']?>" hidden/>
                            <input type="text"  name="moo_settings[fixed_delivery]"     value="<?php echo $MooOptions['fixed_delivery']?>" hidden/>
                            <input type="text"  name="moo_settings[other_zones_delivery]"     value="<?php echo $MooOptions['other_zones_delivery']?>" hidden/>
                            <input type="text"  name="moo_settings[item_delivery]"     value="<?php echo $MooOptions['item_delivery']?>" hidden/>
                            <textarea  name="moo_settings[zones_json]" hidden><?php echo $MooOptions['zones_json']?></textarea>
                            <input type="text"  name="moo_settings[tips]"     value="<?php echo $MooOptions['tips']?>" hidden/>
                            <input type="text"  name="moo_settings[payment_cash]"     value="<?php echo $MooOptions['payment_cash']?>" hidden/>
                            <input type="text"  name="moo_settings[scp]"     value="<?php echo $MooOptions['scp']?>" hidden/>
                            <input type="text"  name="moo_settings[merchant_phone]"     value="<?php echo $MooOptions['merchant_phone']?>" hidden/>
                            <input type="text"  name="moo_settings[order_later]"     value="<?php echo $MooOptions['order_later']?>" hidden/>
                            <input type="text"  name="moo_settings[order_later_days]"     value="<?php echo $MooOptions['order_later_days']?>" hidden/>
                            <input type="text"  name="moo_settings[order_later_minutes]"     value="<?php echo $MooOptions['order_later_minutes']?>" hidden/>
                            <div id="MooPanel_tabContent1">
                                <h2>Key settings</h2>
                                <hr>
                                <div class="MooPanelItem">
                                    <h3>API key</h3>
                                    <div class="Moo_option-item">
                                        <div class="label">Your key : </div>
                                        <input type="text" size="60" name="moo_settings[api_key]" value="<?php echo $MooOptions['api_key']?>"/>
                                        <?php echo $errorToken;?>
                                    </div>
                                    <div style="padding: 20px">
                                        You don't have a key ?
                                        <a href="http://api.smartonlineorders.com/oauth" target="_blank">Get your key from here</a>
                                    </div>
                                    <div style="text-align: center; margin-bottom: 20px;">
                                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                                    </div>

                                </div>
                        </div>
                        </form>
                <?php

                } else
                    if( $MooOptions['lng'] == "" || $MooOptions['lat'] == "")
                    {
                        $this->moo_update_address();
                    }
                    else
                        if(isset($_GET['moo_section']) && $_GET['moo_section']=='update_address')
                           $this->moo_update_address();
                        else
                        {
                  ?>
                 <!-- My store -->
                <div id="MooPanel_tabContent1">
                    <h2>My store</h2><hr>
                    <div class="MooRow">
                        <div class="MooRowSection" style="text-align: center">
                            <div>
                                <img width="70px" src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/check.png";?>" alt=""/>
                            </div>
                            <p>Your API KEY is valid</p>
                        </div>
                    </div>
                    <div class="MooRow">
                        <div class="MooRowSection" style="text-align: center">
                            <div>
                                <img  width="70px" src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/address.png";?>" alt=""/>
                            </div>
                            <p>
                                <a href="<?php echo (esc_url(add_query_arg('moo_section', 'update_address',(admin_url('admin.php?page=moo_index'))))); ?>" style="color: #000;text-decoration: none;">Update your address</a>
                            </p>
                        </div>
                    </div>
                </div>
                 <?php } ?>
                <!-- Import Items -->
                <div id="MooPanel_tabContent2">
                    <h2>Import Items</h2><hr>
                    <div class="MooPanelItem">
                        <h3>Import your data</h3>
                        <div class="Moo_option-item" style="text-align: center">
                            <div id="MooPanelSectionImport"></div>
                            <a href="#" onclick="MooPanel_ImportItems(event)" class="button button-secondary"
                               style="margin-bottom: 35px;" >Import inventory</a>
                        </div>
                    </div>
                    <div class="MooPanelItem">
                        <h3>Statistics</h3>
                        <div class="Moo_option-item">
                            <div class="stats">
                                <div class="stat">
                                    <div class="value" id="MooPanelStats_Cats">0</div>
                                    <div class="type" >Categories</div>
                                </div>
                                <div class="stat">
                                    <div class="value" id="MooPanelStats_Products">0</div>
                                    <div class="type">Items</div>
                                </div>
                                <div class="stat">
                                    <div class="value" id="MooPanelStats_Labels">0</div>
                                    <div class="type">Labels</div>
                                </div>
                                <div class="stat">
                                    <div class="value" id="MooPanelStats_Taxes">0</div>
                                    <div class="type">Taxes rates</div>
                                </div>
                            </div>
                        </div>
                    </div>
                 <div class="MooPanelItem">
                        <h3 style="font-size: 12px">Manual Sync "Use manual sync if changes have been made to your inventory and it hasn't synced"</h3>
                        <div id="moo_progressbar_container"></div>
                        <div class="Moo_option-item">
                            <div class="button_center">
                                <a href="#" onclick="MooPanel_UpdateItems(event)" class="button button-secondary"
                                   style="margin-left: 30px;margin-right: 50px" >Update all Items</a>
                                <a href="#" onclick="MooPanel_UpdateCategories(event)" class="button button-secondary"
                                   style="margin-right: 50px;" >Update Categories</a>
                                <a href="#" onclick="MooPanel_UpdateModifiers(event)" class="button button-secondary"
                                   style="margin-right: 50px;" >Update Modifiers</a>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Orders Types -->
                <div id="MooPanel_tabContent3">
                    <h2>
                        Orders Types
                        <hr>
                    </h2>
                    <div class="MooPanelItem" >
                        <h3>Choose the default order types</h3>
							<div id="MooOrderTypesContent" style="margin-bottom: 10px">
							</div>
                    </div>
                    <div class="MooPanelItem">
                        <h3>Add new order type</h3>
                        <div class="Moo_option-item">
                            <div class="iwl_holder">
                                <div class="iwl_label_holder">
                                    <label for="Moo_AddOT_label">Label</label>
                                </div>
                                <div class="iwl_input_holder">
                                    <input type="text" value="" id="Moo_AddOT_label"/>
                                </div>
                            </div>
                            <div>
                                <div class="iwl_holder">
                                    <div class="">Taxable
                                        <input style="margin: 10px; margin-right: 2px; margin-left: 40px;" type="radio" name="taxable" value="oui" id="Moo_AddOT_taxable_oui" checked><label for="Moo_AddOT_taxable_oui"> Yes</label>
                                        <input type="radio" name="taxable" value="non" id="Moo_AddOT_taxable_non" style="margin-left: 10px;" > <label for="Moo_AddOT_taxable_non">No</label>
                                        <div class="button_center">
                                            <div title="This will add the order type to clover account" class="button button-primary"  onclick="moo_addordertype(event)" id="Moo_AddOT_btn">Add</div><div id="Moo_AddOT_loading"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
                <!-- Store interface -->
                <div id="MooPanel_tabContent4">
                    <h2>Store interface</h2><hr>
                    <form method="post" action="options.php">
                        <?php settings_fields('moo_settings') ?>
                        <input type="text"  name="moo_settings[api_key]" value="<?php echo $MooOptions['api_key']?>" hidden/>
                        <input type="text"  name="moo_settings[lat]"     value="<?php echo $MooOptions['lat']?>" hidden />
                        <input type="text"  name="moo_settings[lng]"     value="<?php echo $MooOptions['lng']?>" hidden />
                        <input type="text"  name="moo_settings[hours]"     value="<?php echo $MooOptions['hours']?>" hidden />
                        <input type="text"  name="moo_settings[merchant_email]"     value="<?php echo $MooOptions['merchant_email']?>" hidden/>
                        <input type="text"  name="moo_settings[thanks_page]"     value="<?php echo $MooOptions['thanks_page']?>" hidden/>
                        <input type="text"  name="moo_settings[custom_css]"     value="<?php echo $MooOptions['custom_css']?>" hidden/>
                        <input type="text"  name="moo_settings[custom_js]"     value="<?php echo $MooOptions['custom_js']?>" hidden/>
                        <input type="text"  name="moo_settings[free_delivery]"     value="<?php echo $MooOptions['free_delivery']?>" hidden/>
                        <input type="text"  name="moo_settings[fixed_delivery]"     value="<?php echo $MooOptions['fixed_delivery']?>" hidden/>
                        <input type="text"  name="moo_settings[other_zones_delivery]"     value="<?php echo $MooOptions['other_zones_delivery']?>" hidden/>
                        <input type="text"  name="moo_settings[item_delivery]"     value="<?php echo $MooOptions['item_delivery']?>" hidden/>
                        <textarea  name="moo_settings[zones_json]" hidden><?php echo $MooOptions['zones_json']?></textarea>
                        <input type="text"  name="moo_settings[tips]"     value="<?php echo $MooOptions['tips']?>" hidden/>
                        <input type="text"  name="moo_settings[payment_cash]"     value="<?php echo $MooOptions['payment_cash']?>" hidden/>
                        <input type="text"  name="moo_settings[scp]"     value="<?php echo $MooOptions['scp']?>" hidden/>
                        <input type="text"  name="moo_settings[merchant_phone]"     value="<?php echo $MooOptions['merchant_phone']?>" hidden/>
                        <input type="text"  name="moo_settings[order_later]"     value="<?php echo $MooOptions['order_later']?>" hidden/>
                        <input type="text"  name="moo_settings[order_later_days]"     value="<?php echo $MooOptions['order_later_days']?>" hidden/>
                        <input type="text"  name="moo_settings[order_later_minutes]"     value="<?php echo $MooOptions['order_later_minutes']?>" hidden/>
                    <div class="MooPanelItem">
                        <h3>Default style</h3>
                        <div class="Moo_option-item">
                            <div>
                                <label>
                                    <input style="display: none;" name="moo_settings[default_style]" id="MooDefaultStyle" type="radio" value="style1" <?php echo ($MooOptions["default_style"]=="style1")?"checked":""; ?> >
                                    <img style="margin-right: 80px;" src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/style1.jpg" ?>" align="middle" />
                                </label>
                                <label>
                                    <input style="display: none;" name="moo_settings[default_style]" id="MooDefaultStyle" type="radio" value="style3" <?php echo ($MooOptions["default_style"]=="style3")?"checked":""; ?> >
                                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/style3.jpg" ?>" align="middle" />
                                </label>
                            </div>
	                     </div>
	                    <div style="text-align: center; margin: 20px;">
		                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
	                    </div>
                    </div>
                    </form>
                </div>
                <!-- Categories -->
                <div id="MooPanel_tabContent5">
                    <h2>Categories</h2>
                    <hr>
                    <div id="display_panel5_on_desktop">
                        <div class="MooPanelItem">
                            <h3>Show/Hide category (click save after assigning new names)</h3>
                            <div id="MooCategoriesContent">
                                <?php
                                $show_all_items = get_option("moo-show-allItems");
                                $nb_items = $model->NbProducts();
                                if($nb_items[0]->nb == 0){
                                    echo '<div class="normal_text" >It appears you don\'t have any categories; If you have just imported your inventory, <br/> please refresh this page </div>';
                                }
                                else
                                {
                                    ?>
                                    <div class="Moo_option-item">
                                        <div class="label">All Items (<?php echo $nb_items[0]->nb.' items)'?></div>
                                        <div class="onoffswitch" onchange="MooChangeCategory_Status('NoCategory')" title="Show/Hide this Category">
                                            <input type="checkbox" name="onoffswitch[]" class="onoffswitch-checkbox" id="myonoffswitch_NoCategory" <?php echo ($show_all_items == 'true')?'checked':''?>>
                                            <label class="onoffswitch-label" for="myonoffswitch_NoCategory"><span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <?php
                                    foreach ($all_categories as $category) {
                                        ?>
                                        <div class="Moo_option-item">
                                            <div class="label"><?php echo $category->name.' ( '.(count(explode(',',$category->items))-1).' items)'?></div>
                                            <div class="onoffswitch" onchange="MooChangeCategory_Status('<?php echo $category->uuid?>')" title="Hide/Show this Category">
                                                <input type="checkbox" name="onoffswitch[]" class="onoffswitch-checkbox" id="myonoffswitch_<?php echo $category->uuid?>" <?php echo ($category->show_by_default == 1)?'checked':''?>>
                                                <label class="onoffswitch-label" for="myonoffswitch_<?php echo $category->uuid?>"><span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                            <div style="float: right">
                                                <input type="text" placeholder="The new name" id="Moo_categoryNewName_<?php echo $category->uuid?>">
                                                <div class="button-small button-primary" onclick="Moo_changeCategoryName('<?php echo $category->uuid?>')">Save</div>
                                                <div id="Moo_CategorySaveName_<?php echo $category->uuid?>" style="color: #008000;display: none">Saved</div>
                                            </div>
                                        </div>
                                    <?php }  }?>
                            </div>
                        </div>
                    </div>
                    <div id="display_panel5_on_mobile">
                        <div class="MooPanelItem">
                            <h3>Show or hide a category (press save if giving category a new name)</h3>
                            <?php
                            $show_all_items = get_option("moo-show-allItems");
                            $nb_items = $model->NbProducts();
                            if($nb_items[0]->nb == 0){
                                echo '<div class="normal_text">It\'s appears you don\'t have any categories. If you have just imported your inventory. <br/> Please refresh this page </div>';
                            }
                            else
                            {
                                ?>
                                <div class="Moo_option-item" style="text-align: center; border: 1px solid #1e5429;">
                                    <div><strong>All Items (<?php echo $nb_items[0]->nb.' items)'?></strong></div>
                                    <div style="margin-top: 10px;" onchange="MooChangeCategory_Status('NoCategory')" title="Show/Hide this Category">
                                        <input type="checkbox" name="onoffswitch[]" class="" id="myonoffswitch_NoCategory" <?php echo ($show_all_items == 'true')?'checked':''?>>
                                    </div>
                                </div>
                                <?php
                                foreach ($all_categories as $category) {
                                    ?>
                                    <div class="Moo_option-item" style="text-align: center; border-bottom: 1px solid #1e5429;">
                                        <div><?php echo $category->name.' ( '.(count(explode(',',$category->items))-1).' items)'?></div>
                                        <div style="margin-top: 10px;" class="" onchange="MooChangeCategory_Status_Mobile('<?php echo $category->uuid?>')" title="Hide/Show this Category">
                                            <input type="checkbox" name="onoffswitch[]" class="" id="myonoffswitch_mobile_<?php echo $category->uuid?>" <?php echo ($category->show_by_default == 1)?'checked':''?>>
                                        </div>
                                        <div>
                                            <input style="margin-top: 10px; margin-bottom: 10px;" type="text" placeholder="New name" id="Moo_categoryNewName_mobile_<?php echo $category->uuid?>">
                                            <div class="button button-primary" onclick="Moo_changeCategoryName_Mobile('<?php echo $category->uuid?>')">Save</div>
                                            <div id="Moo_CategorySaveName_mobile_<?php echo $category->uuid?>" style="color: #008000;display: none">Saved</div>
                                        </div>
                                    </div>
                                <?php }  }?>

                        </div>
                    </div>
                </div>
                <!-- Modifiers -->
                <div id="MooPanel_tabContent6">
                    <h2>Modifiers</h2>
                    <hr>
                    <div class="MooPanelItem">
                        <h3>Hide or change modifier group names so they are easy to understand</h3>
                        <?php
                        if(count($modifier_groups)==0) echo "<div class=\"normal_text\">It's appears you don't have any Modifier Group, please import your data by clicking on <b>Import Items from sidebar then import inventory</b></div>";
                        ?>
                        <div id="show_hide_modifer_desktop">
                            <div id="MooCategoriesContent">
                                <?php
                                foreach ($modifier_groups as $mg) {
                                    ?>
                                    <div class="Moo_option-item">
                                        <div class="label"><?php echo $mg->name?></div>
                                        <div class="onoffswitch" onchange="MooChangeModifier_Status('<?php echo $mg->uuid?>')" title="Show/Hide this Modifier Group">
                                            <input type="checkbox" name="onoffswitch[]" class="onoffswitch-checkbox" id="myonoffswitch_<?php echo $mg->uuid?>" <?php echo ($mg->show_by_default)?'checked':''?>>
                                            <label class="onoffswitch-label" for="myonoffswitch_<?php echo $mg->uuid?>"><span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <div style="float: right">
                                            <input type="text" value="<?php echo $mg->alternate_name?>" id="Moo_ModifierGroupNewName_<?php echo $mg->uuid?>">
                                            <div class="button-small button-primary" onclick="Moo_changeModifierGroupName('<?php echo $mg->uuid?>')">Save</div>
                                            <div id="Moo_ModifierGroupSaveName_<?php echo $mg->uuid?>" style="color: #008000;display: none">Saved</div>
                                        </div>
                                    </div>
                                <?php }?>
                            </div>

                    </div>
                        <div id="show_hide_modifer_mobile" style="text-align: center;">
                            <?php
                            foreach ($modifier_groups as $mg) {
                                ?>
                                <div class="Moo_option-item" style="border-bottom: 1px solid #1e5429">
                                    <div><?php echo $mg->name?></div>
                                    <div style="margin-top: 10px;" onchange="MooChangeModifier_Status_Mobile('<?php echo $mg->uuid?>')" title="Show/Hide this Modifier Group">
                                        <input type="checkbox" name="onoffswitch[]" class="" id="myonoffswitch_mobile_<?php echo $mg->uuid?>" <?php echo ($mg->show_by_default)?'checked':''?>>
                                    </div>
                                    <div>
                                        <input style="margin-top: 10px; margin-bottom: 10px;" type="text" value="<?php echo $mg->alternate_name?>" id="Moo_ModifierGroupNewName_mobile_<?php echo $mg->uuid?>">
                                        <div class="button button-primary" onclick="Moo_changeModifierGroupName_Mobile('<?php echo $mg->uuid?>')">Save</div>
                                        <div id="Moo_ModifierGroupSaveName_mobile_<?php echo $mg->uuid?>" style="color: #008000;display: none">Saved</div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>
                <!-- Settings -->
                <div id="MooPanel_tabContent7">
                    <h2>Settings</h2>  <hr>
                    <form method="post" action="options.php">
                        <?php
                        $MooOptions = (array)get_option('moo_settings');
                        settings_fields('moo_settings');
                        ?>
                        <input type="text"  name="moo_settings[api_key]" value="<?php echo $MooOptions['api_key']?>" hidden/>
                        <input type="text"  name="moo_settings[lat]"     value="<?php echo $MooOptions['lat']?>" hidden />
                        <input type="text"  name="moo_settings[lng]"     value="<?php echo $MooOptions['lng']?>" hidden/>
                        <input type="text"  name="moo_settings[free_delivery]"     value="<?php echo $MooOptions['free_delivery']?>" hidden/>
                        <input type="text"  name="moo_settings[fixed_delivery]"     value="<?php echo $MooOptions['fixed_delivery']?>" hidden/>
                        <input type="text"  name="moo_settings[other_zones_delivery]"     value="<?php echo $MooOptions['other_zones_delivery']?>" hidden/>
                        <input type="text"  name="moo_settings[item_delivery]"     value="<?php echo $MooOptions['item_delivery']?>" hidden/>
                        <textarea  name="moo_settings[zones_json]" hidden><?php echo $MooOptions['zones_json']?></textarea>
                        <input type="text"  name="moo_settings[default_style]"     value="<?php echo $MooOptions['default_style']?>" hidden/>
                        <!-- Notifications section -->
                        <div class="MooPanelItem">
                            <h3>Notification when an order is made</h3>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    We use this email to inform you when a new order has been made. And If you want to use more than one Email please separate them with a comma  ','
                                </div>
                             </div>
                            <div class="Moo_option-item">
                                <div class="iwl_holder">
                                    <div class="iwl_label_holder"><label for="youremail">Your Emails</label></div>
                                    <div class="iwl_input_holder"><input id="youremail" name="moo_settings[merchant_email]" id="MooDefaultMerchantEmail" type="text" value="<?php echo $MooOptions['merchant_email']?>" /></div>
                                </div>
                            </div>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    We use this cell phone number to notify you via text message when a new order has been made. Enjoy free sms notification for 60 days.
                                </div>
                             </div>
                            <div class="Moo_option-item">
                                <div class="iwl_holder">
                                    <div class="iwl_label_holder"><label for="yourephone">Your Phone</label></div>
                                    <div class="iwl_input_holder"><input id="yourephone" name="moo_settings[merchant_phone]" id="MooDefaultMerchantPhone" type="text" value="<?php echo $MooOptions['merchant_phone']?>" /></div>
                                </div>
                            </div>
                        </div>
                        <!-- Tips section -->
                        <div class="MooPanelItem">
                        <h3>Tips</h3>
                            <?php if($merchant_proprites->tipsEnabled) { ?>
                                <div class="Moo_option-item">
                                    <div style="float:left; width: 100%;">
                                        <label style="display:block; margin-bottom:8px;">
                                            <input name="moo_settings[tips]" id="MooTips" type="radio" value="enabled" <?php echo ($MooOptions["tips"]=="enabled")?"checked":""; ?> >
                                           Enabled
                                        </label>
                                        <label style="display:block; margin-bottom:8px;">
                                            <input name="moo_settings[tips]" id="MooTips" type="radio" value="disabled" <?php echo ($MooOptions["tips"]!="enabled")?"checked":""; ?> >
                                            Disabled
                                        </label>
                                    </div>
                                </div>
                            <?php } else {?>
                                <div class="Moo_option-item">
                                    <div style="padding-left: 75px;margin-top: -12px;font-size: 15px;">
                                        <input name="moo_settings[tips]" id="MooTips" value="disabled" hidden />
                                        Tips are disabled, please enable it from your Clover settings
                                    </div>
                                </div>
                            <?php } ?>
                    </div>
                        <!-- Additional payment options section -->
                        <div class="MooPanelItem">
                            <h3>Additional payment options</h3>
                            <!-- Desktop version -->
                            <div class="Moo_option-item">
                                <div style="margin-bottom: 14px;" class="label">Pay in Store</div>
                                <div class="onoffswitch"  title="Accept cash payment">
                                    <input type="checkbox" name="moo_settings[payment_cash]" class="onoffswitch-checkbox" id="myonoffswitch_payment_cash" <?php echo ($MooOptions['payment_cash'] == 'on')?'checked':''?>>
                                    <label class="onoffswitch-label" for="myonoffswitch_payment_cash"><span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <span id="moo_info_msg-1" class="moo-info-msg"
                                      data-ot="Allow customer to order online and then pay at store"
                                      data-ot-target="#moo_info_msg-1">
                                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                </span>
                            </div>
                            <div class="Moo_option-item">
                                <div style="margin-bottom: 14px;" class="label">Secure checkout page</div>
                                <div class="onoffswitch"  title="Accept cash payment">
                                    <input type="checkbox" name="moo_settings[scp]" class="onoffswitch-checkbox" id="myonoffswitch_scp" <?php echo (isset($MooOptions['scp']) && $MooOptions['scp'] == 'on')?'checked':''?>>
                                    <label class="onoffswitch-label" for="myonoffswitch_scp"><span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <span id="moo_info_msg-2" class="moo-info-msg"
                                      data-ot="If you don't have SSL installed on your website, you can use our checkout page"
                                      data-ot-target="#moo_info_msg-2">
                                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                </span>
                            </div>
                    </div>
                        <!-- Business Hours -->
                        <div class="MooPanelItem">
                        <h3>Please choose the Hours your store is available</h3>
                        <div class="Moo_option-item">
                            <div style="float:left; width: 100%;">
                                <label style="display:block; margin-bottom:8px;">
                                    <input name="moo_settings[hours]" id="MooDefaultHours" type="radio" value="all" <?php echo ($MooOptions["hours"]=="all")?"checked":""; ?> >
                                    All Hours
                                </label>
                                <label style="display:block; margin-bottom:8px;">
                                    <input name="moo_settings[hours]" id="MooDefaultHours" type="radio" value="business" <?php echo ($MooOptions["hours"]!="all")?"checked":""; ?> >
                                    Business Hours
                                    <span id="moo_info_msg-3" class="moo-info-msg"
                                          data-ot="Please manage your business hours on clover"
                                          data-ot-target="#moo_info_msg-3">
                                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                        <!-- scheduled orders -->
                        <div class="MooPanelItem">
                            <h3>Allow scheduled orders</h3>
                                <div class="Moo_option-item">
                                    <div style="margin-bottom: 14px;" class="label">Show Order Date</div>
                                    <div class="onoffswitch"  title="Show/hide order date">
                                        <input type="checkbox" name="moo_settings[order_later]" class="onoffswitch-checkbox" id="myonoffswitch_order_later" <?php echo (isset($MooOptions['order_later']) && $MooOptions['order_later'] == 'on')?'checked':''?>>
                                        <label class="onoffswitch-label" for="myonoffswitch_order_later"><span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="Moo_option-item" >
                                    <div class="normal_text">
                                        Minimum time in minutes customers can choose when ordering in advance. Default is 20 minutes
                                    </div>
                                </div>
                                <div class="Moo_option-item">
                                    <div class="iwl_holder">
                                        <div class="iwl_label_holder"><label for="yourephone">min in advance</label></div>
                                        <div class="iwl_input_holder">
                                            <input name="moo_settings[order_later_minutes]" id="MooOrderLaterMinutes" type="text" value="<?php echo (isset($MooOptions['order_later_minutes']))?$MooOptions['order_later_minutes']:""; ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="Moo_option-item" >
                                    <div class="normal_text">
                                        Maximum days in the future customers can choose when ordering in advance. Default is 4 days
                                    </div>
                                </div>
                                <div class="Moo_option-item">
                                    <div class="iwl_holder">
                                        <div class="iwl_label_holder"><label for="yourephone">days in future</label></div>
                                        <div class="iwl_input_holder">
                                            <input name="moo_settings[order_later_days]" id="MooOrderLaterDays" type="text" value="<?php echo (isset($MooOptions['order_later_days']))?$MooOptions['order_later_days']:"" ?>" />
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- Thank you  page -->
                        <div class="MooPanelItem">
                            <h3>Thank you page</h3>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    To change the page that appears when the customer confirms his order. Please enter its URL here or leave it blank to display the default page.
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div class="iwl_holder"><div class="iwl_label_holder"><label id="MooDefaultMerchantEmail" >Your Page</label></div>
                                    <div class="iwl_input_holder"><input name="moo_settings[thanks_page]" id="MooDefaultMerchantEmail" type="text" value="<?php echo $MooOptions['thanks_page']?>" placeholder="http://" /></div>
                                </div>
                            </div>
                        </div>
                        <!-- Custom CSS -->
                        <div class="MooPanelItem">
                            <h3>Custom CSS</h3>
                            <div class="Moo_option-item">
                                <textarea name="moo_settings[custom_css]" id="" cols="10" rows="10" style="width: 100%"><?php echo (isset($MooOptions['custom_css']))?$MooOptions['custom_css']:"";?></textarea>
                             </div>
                        </div>
                        <!-- custom JS -->
                        <div class="MooPanelItem">
                            <h3>Custom Javascript</h3>
                            <div class="Moo_option-item">
                                <textarea name="moo_settings[custom_js]" id="" cols="10" rows="10" style="width: 100%"><?php echo (isset($MooOptions['custom_js']))?$MooOptions['custom_js']:"";?></textarea>
                             </div>
                        </div>
                        <!-- Save Changes button -->
                        <div style="text-align: center; margin: 20px;">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                        <a href="<?php echo (esc_url((admin_url('admin.php?page=moo_index')))); ?>" class="button">Cancel</a>
                    </div>
                    </form>

                </div>
                <!-- Delivery areas -->
                <div id="MooPanel_tabContent8">
                    <h2>Delivery areas</h2><hr>
                    <form method="post" action="options.php" onsubmit="return moo_save_changes()">
                        <?php
                        settings_fields('moo_settings');
                        ?>
                        <input type="text"  name="moo_settings[api_key]" value="<?php echo $MooOptions['api_key']?>" hidden/>
                        <input type="text"  name="moo_settings[lat]"     value="<?php echo $MooOptions['lat']?>" hidden />
                        <input type="text"  name="moo_settings[lng]"     value="<?php echo $MooOptions['lng']?>" hidden />
                        <input type="text"  name="moo_settings[hours]"     value="<?php echo $MooOptions['hours']?>" hidden />
                        <input type="text"  name="moo_settings[merchant_email]"     value="<?php echo $MooOptions['merchant_email']?>" hidden/>
                        <input type="text"  name="moo_settings[thanks_page]"     value="<?php echo $MooOptions['thanks_page']?>" hidden/>
                        <input type="text"  name="moo_settings[custom_css]"     value="<?php echo $MooOptions['custom_css']?>" hidden/>
                        <input type="text"  name="moo_settings[custom_js]"     value="<?php echo $MooOptions['custom_js']?>" hidden/>
                        <input type="text"  name="moo_settings[default_style]"     value="<?php echo $MooOptions['default_style']?>" hidden/>
                        <input type="text"  name="moo_settings[tips]"     value="<?php echo $MooOptions['tips']?>" hidden/>
                        <input type="text"  name="moo_settings[payment_cash]"     value="<?php echo $MooOptions['payment_cash']?>" hidden/>
                        <input type="text"  name="moo_settings[scp]"     value="<?php echo $MooOptions['scp']?>" hidden/>
                        <input type="text"  name="moo_settings[merchant_phone]"     value="<?php echo $MooOptions['merchant_phone']?>" hidden/>
                        <input type="text"  name="moo_settings[order_later]"     value="<?php echo $MooOptions['order_later']?>" hidden/>
                        <input type="text"  name="moo_settings[order_later_days]"     value="<?php echo $MooOptions['order_later_days']?>" hidden/>
                        <input type="text"  name="moo_settings[order_later_minutes]"     value="<?php echo $MooOptions['order_later_minutes']?>" hidden/>
                    <div class="MooPanelItem">
                        <h3>Set Delivery Areas (Click save changes when you create zones) <span class="moo_adding-zone-btn" onclick="moo_show_form_adding_zone()">Add zone</span></h3>
                        <div class="Moo_option-item" id='moo_adding-zone'>
                            <table class="delivery_area_for_mobile" style="margin: 0 auto; width: 55%; border-spacing: 10px;">
                                <tr class="tr_for_mobile">
                                    <td class="td_for_mobile"><label for="moo_dz_name">Name</label></td>
                                    <td class="td_for_mobile"><input style="float: right; width: 100%;" type="text" id="moo_dz_name"><br/></td>
                                </tr>
                                <tr id="moo_dz_type_line" class="tr_for_mobile">
                                    <td class="td_for_mobile"><label for="moo_dz_type">Type</label></td>
                                    <td class="td_for_mobile">
                                        <input type="radio" id="moo_dz_typeC" name='moo_dz_type' checked>
                                        <label for="moo_dz_typeC">Circle</label>
                                        <input type="radio" id="moo_dz_typeS" name='moo_dz_type' >
                                        <label for="moo_dz_typeS">Shape</label>
                                    </td  class="td_for_mobile">
                                </tr>
                                <tr class="tr_for_mobile">
                                    <td class="td_for_mobile"><label for="moo_dz_min">Minimum order</label></td>
                                    <td class="td_for_mobile"><input placeholder="0.00 $" style="float: right; width: 100%;" type="text" id="moo_dz_min"><br/></td>
                                </tr>
                                <tr  class="tr_for_mobile">
                                    <td class="td_for_mobile"><label   for="moo_dz_fee">Delivery fee</label></td>
                                    <td class="td_for_mobile"><input placeholder="0.00 $" style="float: right; width: 100%;" type="text" id="moo_dz_fee"><br/></td>
                                </tr>
                                <tr id="moo_dz_color_line" class="tr_for_mobile">
                                    <td class="td_for_mobile"><label for="moo_dz_color">Color</label></td>
                                    <td class="td_for_mobile"><input type="text" id="moo_dz_color" class="moo-color-field" value="#2788d8"></td>
                                </tr>
                                <tr id="moo_dz_action_for_adding" class="tr_for_mobile">
                                    <td  class="td_for_mobile" style="text-align: center;" colspan="2">
                                        <div style="margin-bottom: 10px;">
                                            <button type="button" class="button" onclick="moo_draw_zone()">Draw zone</button>
                                        </div><div style="margin-bottom: 10px;">
                                            <button type="button" class="button button-primary" onclick="moo_validate_selected_zone()">Validate selected zone</button>

                                        </div>
                                        <div>

                                            <button type="button" class="button" onclick="moo_deleteSelectedShape();">Delete selected zone</button>
                                            <button type="button" class="button" onclick="moo_cancel_adding_form()">Cancel</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="moo_dz_action_for_updating" class="tr_for_mobile">
                                    <td style="text-align: center;" colspan="2">
                                        <div class="iwl_holder">
                                            <div class="iwl_input_holder">
                                                <input type="text" value="" id="moo_dz_id_for_update" hidden>
                                            </div>
                                        </div>
                                        <div class="button_center">
                                            <button type="button" class="button button-primary" onclick="moo_update_selected_zone()">Update zone</button>
                                            <button type="button" class="button" onclick="moo_cancel_adding_form()">Cancel</button>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="Moo_option-item" id="moo_areas_container">
                        </div>
                    </div>
                    <div class="MooPanelItem">
                        <div class="Moo_option-item">
                            <div class="moo_map_da" id="moo_map_da"></div>
                            <div id ="moo_Circleradius"></div>
                        </div>
                    </div>
                    <div class="MooPanelItem">
                        <h3>Other options</h3>
                        <div class="Moo_option-item" >
                            <div class="normal_text">
                                <strong>Free Delivery</strong> : if customer spends over this dollar amount, then delivery fee is free, Keep empty if you don't want to offer free delivery
                            </div>
                            <div class="iwl_holder">
                                <div class="iwl_label_holder"><label for="minamount">Min Amount</label></div>
                                <div class="iwl_input_holder">
                                    <input placeholder="$" name="moo_settings[free_delivery]" type="text" value="<?php echo (isset($MooOptions['free_delivery']))?$MooOptions['free_delivery']:""; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="Moo_option-item" >
                            <div class="normal_text">
                                <strong>Fixed Delivery amount</strong> :  This fee will be applied towards any delivered order (order types with shipping address must be enabled) Keep empty if you don"t want to charge a fixed delivery fee
                            </div>
                            <div class="iwl_holder">
                                <div class="iwl_label_holder"><label for="fixeddeliveryamount">Fixed Delivery amount</label></div>
                                <div class="iwl_input_holder">
                                    <input  id="fixeddeliveryamount" placeholder="$" name="moo_settings[fixed_delivery]" type="text" value="<?php echo (isset($MooOptions['fixed_delivery']))?$MooOptions['fixed_delivery']:"";?>" />
                                </div>
                            </div>
                        </div>
                        <div class="Moo_option-item" >
                            <div class="normal_text">
                                <strong>Other Zones Delivery fees</strong> :  This delivery fee will be applied for customers that aren't in the delivery zones as drawn above. Keep empty to prevent customers from ordering outside of delivery zones
                            </div>
                            <div class="iwl_holder">
                                <div class="iwl_label_holder"><label for="otherzonesdeliveryfees">Other Zones Delivery fees</label></div>
                                <div class="iwl_input_holder">
                                    <input placeholder="$" id="otherzonesdeliveryfees" name="moo_settings[other_zones_delivery]" type="text" value="<?php echo (isset($MooOptions['other_zones_delivery']))?$MooOptions['other_zones_delivery']:"";?>"  /></div>
                            </div>
                        </div>
                        <div class="Moo_option-item" >
                            <div class="normal_text">
                                <strong>Delivery taxes & Delivery amount on receipt</strong> :  To set taxes for delivery or to show the delivery amount on the receipt, you should create an item on your clover account with variable price, and affect appropriate taxes to it
                            </div>
                            <div class="iwl_holder">
                                <div class="iwl_label_holder"><label for="otherzonesdeliveryfees">The item's uuid</label></div>
                                <div class="iwl_input_holder">
                                    <input placeholder="$" id="otherzonesdeliveryfees" name="moo_settings[item_delivery]" type="text" value="<?php echo (isset($MooOptions['item_delivery']))?$MooOptions['item_delivery']:"";?>" />
                                </div>
                            </div>
                        </div>

                    </div>
                    <div style="text-align: center; margin: 20px;">
                        <textarea id="moo_zones_json" name="moo_settings[zones_json]" hidden><?php echo (isset($MooOptions['zones_json']))?$MooOptions['zones_json']:"";?></textarea>
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                        <a href="<?php echo (esc_url((admin_url('admin.php?page=moo_index')))); ?>" class="button">Cancel</a>
                    </div>
                    </form>
                </div>
                <!-- Feedback -->
                <div id="MooPanel_tabContent9">
                    <h2>Feedback / Help </h2><hr>
                    <div class="MooPanelItem">
                        <h3>Need Help or Feedback</h3>
                        <div class="normal_text">
                            Do you need help or would like to give us feedback.<br/>Please e-mail or call us: 925-234-5554 (8am-8pm pacific time)
                        </div>
                        <div class="Moo_option-item">
                            <div class="iwl_holder">
                                <div class="iwl_label_holder">
                                    <label for="MoofeedBackEmail">Your Email</label>
                                </div>
                                <div class="iwl_input_holder">
                                    <input type="text" name="MoofeedBackEmail" id="MoofeedBackEmail"
                                           placeholder="Your email" style="width: 100%;" value="<?php $emails = explode(",",$MooOptions['merchant_email']);echo $emails[0];?>" />
                                </div>
                                <div  style="margin-bottom: 3px;">
                                    <label for="Moofeedback">Your Message *</label>
                                </div>
                                <div class="iwl_label_holder">
                                    <textarea placeholder="Your Feedback or Help..." name="MooFeedBack" id="Moofeedback" cols="10" rows="10"></textarea>
                                </div>
                            </div>
                            <div class="button_center">
                                <a class="button button-primary" href="#" onclick="MooSendFeedBack(event)">Send</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FAQ -->
                <div id="MooPanel_tabContent10">
                    <h2>FAQ</h2><hr>
                    <div class="MooPanelItem">
                        <h3>FAQ</h3>
                        <div class="Moo_option-item">
                            <div>
                                <div class="faq_question">
                                    Q1 - Once an order is placed, does it print to the clover POS?
                                </div>
                                <div class="faq_response">
                                    A1 - By default, it prints to the clover POS. If it doesn't print open the Online ordering app for Wordpress on the Clover POS then adjust the settings to select a default printer
                                </div>
                                <div class="faq_question">
                                    Q2 - We have several locations, how do I put the menu for each location on the same website?
                                </div>
                                <div class="faq_response">
                                    A2 - You will need to create a subdomain on your website. For example, for a website named www.texasfoods.com
                                    You would create as many subdomains needed for each location. Then install Wordpress into each subdomain. For example:<br/>
                                    location1.texasfoods.com<br/>
                                    location2.texasfoods.com<br/>
                                    Then place a link on the website for each locations menu
                                </div>
                                <div class="faq_question">
                                    Q3 -  I'm having trouble installing the plugin or have other questions, can you help?
                                </div>
                                <div class="faq_response">
                                    A3 - Yes, we can, please email or call us:<br/>
                                    support@merchantech.us<br/>
                                    925-234-5554
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    public function add_admin_menu()
    {
        $icon_url =  plugin_dir_url(dirname(__FILE__))."public/img/ic_launcher2.png";
        add_menu_page('Settings page', 'Clover Orders', 'manage_options', 'moo_index', array($this, 'panel_settings'),$icon_url);
        add_submenu_page('moo_index', 'Settings', 'Settings', 'manage_options', 'moo_index', array($this, 'panel_settings'));
        add_submenu_page('moo_index', 'Items/Images', 'Items / Images', 'manage_options', 'moo_items', array($this, 'page_products'));
        add_submenu_page('moo_index', 'Orders', 'Orders', 'manage_options', 'moo_orders', array($this, 'page_orders'));
    }

    function toolbar_link_to_settings( $wp_admin_bar ) {
        $args = array(
            'id'    => 'Clover_Orders',
            'title' => 'Clover Orders',
            'parent'  => false
        );
        $args2 = array(
            'id'    => 'Clover_Orders_settings',
            'title' => 'Settings',
            'href'  => admin_url().'admin.php?page=moo_index',
            'parent'  => 'Clover_Orders',
        );
        $args3 = array(
            'id'    => 'Clover_Orders_orders',
            'title' => 'Orders',
            'href'  => admin_url().'admin.php?page=moo_orders',
            'parent'  => 'Clover_Orders',
        );
        $args4 = array(
            'id'    => 'Clover_Orders_items',
            'title' => 'Items / Images',
            'href'  => admin_url().'admin.php?page=moo_items',
            'parent'  => 'Clover_Orders',
        );
        $wp_admin_bar->add_node( $args  );
        $wp_admin_bar->add_node( $args2 );
        $wp_admin_bar->add_node( $args3 );
        $wp_admin_bar->add_node( $args4 );
    }
	/**
	 * Register the options.
	 *
	 * @since    1.0.0
	 */
    public function register_mysettings()
    {
        register_setting('moo_settings', 'moo_settings');
    }
    /**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'moo-OnlineOrders-admin-css', plugin_dir_url( __FILE__ ).'css/moo-OnlineOrders-admin.css', array(), $this->version, 'all');
		wp_enqueue_style( 'moo-OnlineOrders-admin-small-devices-css', plugin_dir_url( __FILE__ ).'css/moo-OnlineOrders-admin-small-devices.css', array(), $this->version, 'only screen and (max-device-width: 1200px)');

        wp_enqueue_style('moo-tooltip-css',   plugin_dir_url( __FILE__ )."css/tooltip.css", array(), $this->version, 'all');
        wp_register_style( 'magnific-popup', plugin_dir_url(dirname(__FILE__))."public/css/magnific-popup.css" );
        wp_enqueue_style( 'magnific-popup');
        wp_enqueue_style( 'wp-color-picker' );
    }

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        $params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php', isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' ),
        );

        wp_enqueue_script('jquery');
        wp_enqueue_script( 'wp-color-picker' );

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/moo-OnlineOrders-admin.js', array( 'jquery' ), $this->version, false );

        wp_register_script('moo-publicAdmin-js', plugins_url( 'js/moo-OnlineOrders-admin.js', __FILE__ ),array(), $this->version);
        wp_register_script('moo-tooltip-js', plugins_url( 'js/tooltip.min.js', __FILE__ ),array(), $this->version);
        wp_register_script('progressbar-js', plugins_url( 'js/progressbar.min.js', __FILE__ ));

        wp_register_script('moo-google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBv1TkdxvWkbFaDz2r0Yx7xvlNKe-2uyRc&libraries=drawing');
        wp_register_script('moo-map-js', plugins_url( 'js/moo_map.js', __FILE__ ),array(), $this->version);
        wp_register_script('moo-map-da', plugins_url( 'js/moo_map_da.js', __FILE__ ),array(), $this->version);
        
        wp_register_script('magnific-modal', plugin_dir_url(dirname(__FILE__))."public/js/magnific.min.js");
        wp_enqueue_script('magnific-modal',array('jquery'));

        wp_enqueue_script('progressbar-js',array('jquery'));
        wp_enqueue_script("moo-tooltip-js",array('jquery'));

        wp_enqueue_script('moo-publicAdmin-js',array('jquery','wp-color-picker'));


        wp_localize_script("moo-publicAdmin-js", "moo_params",$params);

	}

    public function moo_update_address()
    {
        $MooOptions = (array)get_option('moo_settings');

        $api   = new  moo_OnlineOrders_CallAPI();
        $merchant_address = $api->getMerchantAddress();
        wp_enqueue_script('moo-google-map');
        wp_enqueue_script('moo-map-js',array('jquery','moo-google-map'));
        wp_localize_script("moo-map-js", "moo_merchantAddress",$merchant_address);
        wp_localize_script("moo-map-js", "moo_merchantLat",$MooOptions['lat']);
        wp_localize_script("moo-map-js", "moo_merchantLng",$MooOptions['lng']);

       ?>
        <form method="post" action="options.php">
            <?php settings_fields('moo_settings') ?>
            <input type="text"  name="moo_settings[api_key]" value="<?php echo $MooOptions['api_key']?>" hidden/>
            <input type="text"  name="moo_settings[hours]"     value="<?php echo $MooOptions['hours']?>" hidden/>
            <input type="text"  name="moo_settings[default_style]"     value="<?php echo $MooOptions['default_style']?>" hidden/>
            <input type="text"  name="moo_settings[merchant_email]"     value="<?php echo $MooOptions['merchant_email']?>" hidden/>
            <input type="text"  name="moo_settings[thanks_page]"     value="<?php echo $MooOptions['thanks_page']?>" hidden/>
            <input type="text"  name="moo_settings[custom_css]"     value="<?php echo $MooOptions['custom_css']?>" hidden/>
            <input type="text"  name="moo_settings[custom_js]"     value="<?php echo $MooOptions['custom_js']?>" hidden/>
            <input type="text"  name="moo_settings[free_delivery]"     value="<?php echo $MooOptions['free_delivery']?>" hidden/>
            <input type="text"  name="moo_settings[fixed_delivery]"     value="<?php echo $MooOptions['fixed_delivery']?>" hidden/>
            <input type="text"  name="moo_settings[other_zones_delivery]"     value="<?php echo $MooOptions['other_zones_delivery']?>" hidden/>
            <input type="text"  name="moo_settings[item_delivery]"     value="<?php echo $MooOptions['item_delivery']?>" hidden/>
            <textarea name="moo_settings[zones_json]" hidden><?php echo $MooOptions['zones_json']?></textarea>
            <input type="text"  name="moo_settings[tips]"     value="<?php echo $MooOptions['tips']?>" hidden/>
            <input type="text"  name="moo_settings[payment_cash]"     value="<?php echo $MooOptions['payment_cash']?>" hidden/>
            <input type="text"  name="moo_settings[scp]"     value="<?php echo $MooOptions['scp']?>" hidden/>
            <input type="text"  name="moo_settings[merchant_phone]"     value="<?php echo $MooOptions['merchant_phone']?>" hidden/>
            <input type="text"  name="moo_settings[order_later]"     value="<?php echo $MooOptions['order_later']?>" hidden/>
            <input type="text"  name="moo_settings[order_later_days]"     value="<?php echo $MooOptions['order_later_days']?>" hidden/>
            <input type="text"  name="moo_settings[order_later_minutes]"     value="<?php echo $MooOptions['order_later_minutes']?>" hidden/>
            <input type="hidden" name="_wp_http_referer" value="<?php echo (esc_url((admin_url('admin.php?page=moo_index')))); ?>" />
            <div id="MooPanel_tabContent1">
            <h2>Set-up your address</h2><hr>
            <div class="MooPanelItem">
                <h3>Please validate or change your address</h3>
                <div class="Moo_option-item" style="padding-top: 0px;margin-top: -15px;">
                    <div class="normal_text">Your current address is : </div>
                    <p><?php echo $merchant_address?></p>
                    <div class="moo_map" id="moo_map"></div>
                </div>
                <div class="Moo_option-item">
                    <input id="Moo_Lat" type="text" size="15" name="moo_settings[lat]" value="<?php echo $MooOptions['lat']?>" hidden/>
                    <input id="Moo_Lng" type="text" size="15" name="moo_settings[lng]" value="<?php echo $MooOptions['lng']?>" hidden/>
                    <div style="text-align: center;">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                        <a href="<?php echo (esc_url((admin_url('admin.php?page=moo_index')))); ?>" class="button">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

        </form>
        <?php
    }
}
