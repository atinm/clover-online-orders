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


        $this->model = new moo_OnlineOrders_Admin_Model();

	}

  public function page_products()
    {
        require_once "includes/class-moo-products-list.php";
        $products = new Products_List_Moo();
        $products->prepare_items();

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

    public function page_orders()
    {
        require_once "includes/class-moo-orders-list.php";
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
        $args   = [
            'label'   => 'Items',
            'default' => 20,
            'option'  => 'moo_items_per_page'
        ];
        add_screen_option( $option, $args );
    }

    public function panel_settings()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/moo-OnlineOrders-CallAPI.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/moo-OnlineOrders-Model.php';

        $api   = new  moo_OnlineOrders_CallAPI();
        $model = new  moo_OnlineOrders_Model();

       // $res = $api->save_one_item('{ "id": "6PCJR6XH3GGMC", "hidden": false, "itemGroup": { "id": "241GNJR3G6NPJ" }, "name": "Nhmy Ftjhvg edited", "alternateName": "", "code": "", "sku": "", "price": 4650, "priceType": "FIXED", "defaultTaxRates": true, "unitName": "", "isRevenue": true, "taxRates": { "elements": [ { "id": "NHP2RVD56HPSM", "name": "No Sales Tax", "rate": 0, "isDefault": true }, { "id": "4WMS1B307M9KE", "name": "Ca Tax", "rate": 850000, "isDefault": true }, { "id": "5SCHBDA2W6FNR", "name": "Tracy", "rate": 850000, "isDefault": true }, { "id": "WXMKJRKGQS3W0", "name": "Cal Sales Tax", "rate": 500000, "isDefault": true } ] }, "modifierGroups": { "elements": [] }, "categories": { "elements": [] }, "tags": { "elements": [] }, "modifiedTime": 1460044643000 }');

       // $api->getItem('6PCJR6XH3GGMC');
      //  $api->delete_item('6PCJR6XH3GGMC');
      //  $api->update_taxes_rates();
        $errorToken=false;

        //default options
        $MooOptions = (array)get_option('moo_settings');

        $FirstUse = get_option('moo_first_use');

        $token = $MooOptions["api_key"];


        if($token != '')
        {
            $this->model->setToken($token);
            $result = $this->model->checkToken();

            if($result == 'Forbidden') $errorToken="( Token invalid )";
            else {
                if(isset(json_decode($result)->status) && json_decode($result)->status =='success') {
                    $newvalue = get_option( 'moo_store_page');
                    $api->updateWebsite(esc_url( admin_url('admin-post.php') ));
                    $errorToken="( Token valid )";
                }
                else
                    $errorToken="( Token expired )";

            }
        }
        else
            $errorToken="( Required )";
        $modifier_groups = $model->getAllModifiersGroup();
        ?>
        <div id="MooPanel">
            <div id="MooPanel_sidebar">
                <div id="Moopanel_logo">
                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/woo_100x100.png";?>" alt=""/>
                    <p>Online orders for Clover</p>
                </div>
                <ul>
                    <li class="MooPanel_Selected" id="MooPanel_tab1" onclick="tab_clicked(1)">API Key settings</li>
                    <li id="MooPanel_tab2" onclick="tab_clicked(2)">Import Items</li>
                    <li id="MooPanel_tab3" onclick="tab_clicked(3)">Orders Types</li>
                    <li id="MooPanel_tab4" onclick="tab_clicked(4)">Store interface</li>
                    <li id="MooPanel_tab5" onclick="tab_clicked(5)">Modifiers</li>
                    <li id="MooPanel_tab6" onclick="tab_clicked(6)">Feedback</li>
                </ul>
            </div>
            <div id="MooPanel_main">

                <form method="post" action="options.php">
                    <?php settings_fields('moo_settings') ?>
                <div id="MooPanel_tabContent1">
                        <h2>Key settings</h2>
                        <div class="MooPanelItem">
                            <h3>API key</h3>
                            <div class="Moo_option-item">
                                <div class="label">Your key : </div>
                                <input type="text" size="60" name="moo_settings[api_key]" value="<?php echo $MooOptions['api_key']?>"/>
                                <?php echo $errorToken;?>
                            </div>
                            <div style="padding: 20px">
                                You don't have a key ?
                                <a href="http://api.smartonlineorders.com/oauth" target="_blank">Get your key</a>
                            </div>
                            <div style="text-align: center; margin-bottom: 20px;">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                            </div>

                        </div>
                </div>
                <div id="MooPanel_tabContent2">
                    <h2>Import Items</h2>
                    <div class="MooPanelItem">
                        <h3>Import your data</h3>
                        <div class="Moo_option-item" style="text-align: center">
                            <div id="MooPanelSectionImport"></div>
                            <a href="#" onclick="MooPanel_ImportItems(event)" class="button button-secondary"
                               style="margin-bottom: 35px;" >Import your data</a>
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

                </div>
                <div id="MooPanel_tabContent3">
                    <h2>Orders Types</h2>
                    <div class="MooPanelItem" >
                        <h3>Choose the defaults order types</h3>
							<div id="MooOrderTypesContent" style="margin-bottom: 10px">
							</div>
                    </div>
                    <div class="MooPanelItem">
                        <h3>Add new order type</h3>
                        <div class="Moo_option-item">
		                        <div class="label">Label :
		                        </div>
		                        <input type="text" size="60" value="" id="Moo_AddOT_label"/>
								 <div class="label">taxable :<br />
									 <input type="radio" name="taxable" value="oui" id="Moo_AddOT_taxable_oui" style="margin: 10px" checked> Yes<br>
									 <input type="radio" name="taxable" value="non" id="Moo_AddOT_taxable_non" style="margin-left: 10px;margin-right: 10px" > No<br><br>
									 <div class="button button-primary"  onclick="moo_addordertype(event)" id="Moo_AddOT_btn">Add</div><div id="Moo_AddOT_loading"></div>
								 </div>
                        </div>
                    </div>
                 </div>
                <div id="MooPanel_tabContent4">
                    <h2>Store interface</h2>
                    <div class="MooPanelItem">
                        <h3>Default style</h3>
                        <div class="Moo_option-item">
                            <div style="float:left; width: 295px;">
                                <label style="display:block; margin-bottom:8px;">
                                    <input name="moo_settings[default_style]" id="MooDefaultStyle" type="radio" value="style1" <?php echo ($MooOptions["default_style"]=="style1")?"checked":""; ?> >
                                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/style1.jpg" ?>" align="middle" />
                                </label>
                                <label style="display:block; margin-bottom:8px;">
                                    <input name="moo_settings[default_style]" id="MooDefaultStyle" type="radio" value="style2" <?php echo ($MooOptions["default_style"]=="style2")?"checked":""; ?> >
                                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/style2.jpg" ?>" align="middle" />
                                </label>

                            </div>

	                     </div>
	                    <div style="text-align: center; margin-bottom: 20px;">
		                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
	                    </div>

                    </div>
                </div>
            </form>
                <div id="MooPanel_tabContent5">
                    <h2>Modifiers</h2>
                    <div class="MooPanelItem">
                        <h3>Update your modifier names so they are easy to understand.</h3>
                        <?php
                        if(count($modifier_groups)==0) echo "<div style=\"text-align: center;margin-bottom: 10px;\">You don't have any Modifier Group,<br> please import your data by clicking on <b>Import Items</b></div>";

                        foreach ($modifier_groups as $mg) {
                            //var_dump($mg);
                            ?>

                            <div class="Moo_option-item">
                                <div class="label"><?php echo $mg->name?></div>
                                <div class="onoffswitch" onchange="MooChangeModifier_Status('<?php echo $mg->uuid?>')">
                                    <input type="checkbox" name="onoffswitch[]" class="onoffswitch-checkbox" id="myonoffswitch_<?php echo $mg->uuid?>" <?php echo ($mg->show_by_default)?'checked':''?>>
                                    <label class="onoffswitch-label" for="myonoffswitch_<?php echo $mg->uuid?>"><span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <div style="float: right">
                                    <input type="text" value="<?php echo $mg->alternate_name?>" id="Moo_ModifierGroupNewName_<?php echo $mg->uuid?>">
                                    <div class="button button-primary" onclick="Moo_changeModifierGroupName('<?php echo $mg->uuid?>')">Save</div>
                                    <div id="Moo_ModifierGroupSaveName_<?php echo $mg->uuid?>" style="color: #008000;display: none">Saved</div>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div>
                <div id="MooPanel_tabContent6">
                    <h2>Feedback</h2>
                    <div class="MooPanelItem">
                        <h3>Send us your feedback</h3>
                        <div class="Moo_option-item">
                            <textarea name="MooFeedBack" id="Moofeedback" cols="10" rows="10" style="width: 100%"></textarea>
                            <div style="text-align: right;">
                                <a class="button button-primary" href="#" onclick="MooSendFeedBack(event)">Send</a>
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
        add_submenu_page('moo_index', 'Items', 'Items', 'manage_options', 'moo_items', array($this, 'page_products'));
        add_submenu_page('moo_index', 'Orders', 'Orders', 'manage_options', 'moo_orders', array($this, 'page_orders'));

       // add_action("load-$moo_products_page", array($this, 'page_products_screen_options'));


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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/moo-OnlineOrders-admin.css', array(), $this->version, 'all' );
		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jquery.steps.css', array(), $this->version, 'all' );

       // wp_register_style( 'jquery.steps',plugins_url( 'css/jquery.steps.css', __FILE__ ));
       // wp_enqueue_style( 'jquery.steps' );

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

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/moo-OnlineOrders-admin.js', array( 'jquery' ), $this->version, false );

        wp_register_script('moo-publicAdmin-js', plugins_url( 'js/moo-OnlineOrders-admin.js', __FILE__ ));
        wp_enqueue_script('moo-publicAdmin-js',array('jquery'));

      //  wp_register_script('jquery-steps', plugins_url( 'js/jquery.steps.js', __FILE__ ));
      //  wp_enqueue_script('jquery-steps',array('jquery'));

      //  wp_register_script('custom-script-admin-import', plugins_url( 'js/moo_admin_import.js', __FILE__ ));

        wp_localize_script("moo-publicAdmin-js", "moo_params",$params);
	}

}
