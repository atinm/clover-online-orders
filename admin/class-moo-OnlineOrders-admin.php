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

        $errorToken=false;

        //default options
        $MooOptions = (array)get_option('moo_settings');

        $token = $MooOptions["api_key"];

        $merchant_proprites = (json_decode($api->getMerchantProprietes())) ;
        $tips_is_enabled = $merchant_proprites->tipsEnabled;



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

      // var_dump(file_get_contents('http://test.smartonlineorder.com'));
        /* Start Map Delivery area section */
        $MooOptions = (array)get_option('moo_settings');
        $merchant_address =  $api->getMerchantAddress();

        wp_enqueue_script('moo-google-map');
        wp_enqueue_script('moo-map-da',array('jquery','moo-google-map'));
        wp_localize_script("moo-map-da", "moo_merchantAddress",$merchant_address);
        wp_localize_script("moo-map-da", "moo_merchantLat",$MooOptions['lat']);
        wp_localize_script("moo-map-da", "moo_merchantLng",$MooOptions['lng']);
        /* Fin map Delivery area section*/
        ?>
        <div id="MooPanel">
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
                            <textarea  name="moo_settings[zones_json]" hidden><?php echo $MooOptions['zones_json']?></textarea>
                            <input type="text"  name="moo_settings[tips]"     value="<?php echo $MooOptions['tips']?>" hidden/>
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
                    <h2>My store</h2>
                    <div class="MooRow">
                        <div class="MooRowSection" style="margin-left: 120px">
                                    <img  style="float: left; " width="70px" src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/check.png";?>" alt=""/>
                                    <p style="font-size: 20px;float: left;line-height: 37px;margin-left: 23px;">Your API KEY is valid</p>
                        </div>
                    </div>
                    <div class="MooRow">
                        <div class="MooRowSection" style="margin-left: 120px">
                                    <img  style="float: left; " width="70px" src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/address.png";?>" alt=""/>
                                    <p style="font-size: 18px;float: left;line-height: 47px;margin-left: 23px;">
                                        <a href="<?php echo (esc_url(add_query_arg('moo_section', 'update_address',(admin_url('admin.php?page=moo_index'))))); ?>" style="color: #000;text-decoration: none;">Update your address</a>
                                    </p>
                        </div>
                    </div>
                </div>
                 <?php } ?>
                <!-- Import Items -->
                <div id="MooPanel_tabContent2">
                    <h2>Import Items</h2>
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
                </div>
                <!-- Orders Types -->
                <div id="MooPanel_tabContent3">
                    <h2>Orders Types</h2>
                    <div class="MooPanelItem" >
                        <h3>Choose the default order types</h3>
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
                                     <div title="This will add the order type to clover account" class="button button-primary"  onclick="moo_addordertype(event)" id="Moo_AddOT_btn">Add</div><div id="Moo_AddOT_loading"></div>
                                 </div>
                        </div>

                    </div>
                 </div>
                <!-- Store interface -->
                <div id="MooPanel_tabContent4">
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
                        <textarea  name="moo_settings[zones_json]" hidden><?php echo $MooOptions['zones_json']?></textarea>
                        <input type="text"  name="moo_settings[tips]"     value="<?php echo $MooOptions['tips']?>" hidden/>
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
                                <label style="display:block; margin-bottom:8px;">
                                    <input name="moo_settings[default_style]" id="MooDefaultStyle" type="radio" value="style3" <?php echo ($MooOptions["default_style"]=="style3")?"checked":""; ?> >
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
                    <div class="MooPanelItem">
                        <h3>Show or hide a category ( press save if giving category a new name )</h3>
                        <?php
                        $show_all_items = get_option("moo-show-allItems");
                        $nb_items = $model->NbProducts();
                        if($nb_items[0]->nb == 0){
                            echo '<p style="font-size: 15px;text-align: center">You don\'t have any category or you\'re just imported your inventory. <br/> Please refresh the page </p>';
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
                                    <div class="button button-primary" onclick="Moo_changeCategoryName('<?php echo $category->uuid?>')">Save</div>
                                    <div id="Moo_CategorySaveName_<?php echo $category->uuid?>" style="color: #008000;display: none">Saved</div>
                                </div>
                            </div>
                        <?php }  }?>

                    </div>
                </div>
                <!-- Modifiers -->
                <div id="MooPanel_tabContent6">
                    <h2>Modifiers</h2>
                    <div class="MooPanelItem">
                        <h3>Hide or change modifier group names so they are easy to understand</h3>
                        <?php
                        if(count($modifier_groups)==0) echo "<div style=\"text-align: center;margin-bottom: 10px;\">You don't have any Modifier Group,<br> please import your data by clicking on <b>Import Items</b></div>";

                        foreach ($modifier_groups as $mg) {
                            //var_dump($mg);
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
                                    <div class="button button-primary" onclick="Moo_changeModifierGroupName('<?php echo $mg->uuid?>')">Save</div>
                                    <div id="Moo_ModifierGroupSaveName_<?php echo $mg->uuid?>" style="color: #008000;display: none">Saved</div>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div>
                <!-- Settings -->
                <div id="MooPanel_tabContent7">
                    <h2>Settings</h2>
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
                                <textarea  name="moo_settings[zones_json]" hidden><?php echo $MooOptions['zones_json']?></textarea>
                                <input type="text"  name="moo_settings[default_style]"     value="<?php echo $MooOptions['default_style']?>" hidden/>
                                <div class="MooPanelItem" style="margin-bottom: 0;border-bottom: 0">
                                    <h3>Notification when an order is made</h3>
                                    <div class="Moo_option-item" >
                                        <div style="padding-right: 5px;margin-top: -10px;font-size: 12px;">
                                            We use this email to inform you when a new order has been made. And If you want to use more than one Email please separate them with a comma  ','
                                        </div>
                                     </div>
                                    <div class="Moo_option-item">
                                        <div class="label">Your Emails</div>
                                        <input style="width: 60%" name="moo_settings[merchant_email]" id="MooDefaultMerchantEmail" type="text" value="<?php echo $MooOptions['merchant_email']?>" />
                                     </div>
                                </div>
                                <div class="MooPanelItem" style="margin-bottom: 0;border-bottom: 0">
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
                                <div class="MooPanelItem" style="margin-bottom: 0;border-bottom: 0">
                                    <h3>Thank you page</h3>
                                    <div class="Moo_option-item" >
                                        <div style="padding-right: 5px;margin-top: -10px;font-size: 12px;">
                                            To change the page that appears when the customer confirms his order. Please enter its URL here or leave it blank to display the default page.
                                        </div>
                                    </div>
                                    <div class="Moo_option-item">
                                        <div class="label">Your Page</div>
                                        <input style="width: 60%" name="moo_settings[thanks_page]" id="MooDefaultMerchantEmail" type="text" value="<?php echo $MooOptions['thanks_page']?>" placeholder="http://" />
                                     </div>
                                </div>
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
                                                Business Hours <span class="tooltip"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                                                          viewBox="0 0 23.625 23.625" style="    width: 20px;margin-left: 10px;enable-background:new 0 0 23.625 23.625;" xml:space="preserve"><g><path style="fill:#030104;" d="M11.812,0C5.289,0,0,5.289,0,11.812s5.289,11.813,11.812,11.813s11.813-5.29,11.813-11.813
		S18.335,0,11.812,0z M14.271,18.307c-0.608,0.24-1.092,0.422-1.455,0.548c-0.362,0.126-0.783,0.189-1.262,0.189
		c-0.736,0-1.309-0.18-1.717-0.539s-0.611-0.814-0.611-1.367c0-0.215,0.015-0.435,0.045-0.659c0.031-0.224,0.08-0.476,0.147-0.759
		l0.761-2.688c0.067-0.258,0.125-0.503,0.171-0.731c0.046-0.23,0.068-0.441,0.068-0.633c0-0.342-0.071-0.582-0.212-0.717
		c-0.143-0.135-0.412-0.201-0.813-0.201c-0.196,0-0.398,0.029-0.605,0.09c-0.205,0.063-0.383,0.12-0.529,0.176l0.201-0.828
		c0.498-0.203,0.975-0.377,1.43-0.521c0.455-0.146,0.885-0.218,1.29-0.218c0.731,0,1.295,0.178,1.692,0.53
		c0.395,0.353,0.594,0.812,0.594,1.376c0,0.117-0.014,0.323-0.041,0.617c-0.027,0.295-0.078,0.564-0.152,0.811l-0.757,2.68
		c-0.062,0.215-0.117,0.461-0.167,0.736c-0.049,0.275-0.073,0.485-0.073,0.626c0,0.356,0.079,0.599,0.239,0.728
		c0.158,0.129,0.435,0.194,0.827,0.194c0.185,0,0.392-0.033,0.626-0.097c0.232-0.064,0.4-0.121,0.506-0.17L14.271,18.307z
		 M14.137,7.429c-0.353,0.328-0.778,0.492-1.275,0.492c-0.496,0-0.924-0.164-1.28-0.492c-0.354-0.328-0.533-0.727-0.533-1.193
		c0-0.465,0.18-0.865,0.533-1.196c0.356-0.332,0.784-0.497,1.28-0.497c0.497,0,0.923,0.165,1.275,0.497
		c0.353,0.331,0.53,0.731,0.53,1.196C14.667,6.703,14.49,7.101,14.137,7.429z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                                                 <span class="tooltiptext">Please manage your business hours on clover</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="MooPanelItem" style="margin-bottom: 0;border-bottom: 0">
                                    <h3>Custom CSS</h3>
                                    <div class="Moo_option-item">
                                        <textarea name="moo_settings[custom_css]" id="" cols="10" rows="10" style="width: 100%"><?php echo $MooOptions['custom_css']?></textarea>
                                     </div>
                                </div>
                                <div class="MooPanelItem" style="margin-bottom: 0;border-bottom: 0">
                                    <h3>Custom Javascript</h3>
                                    <div class="Moo_option-item">
                                        <textarea name="moo_settings[custom_js]" id="" cols="10" rows="10" style="width: 100%"><?php echo $MooOptions['custom_js']?></textarea>
                                     </div>
                                </div>
                                <div style="text-align: center; margin: 20px;">
                                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                                    <a href="<?php echo (esc_url((admin_url('admin.php?page=moo_index')))); ?>" class="button">Cancel</a>
                                </div>
                            </form>

                </div>
                <!-- Delivery areas -->
                <div id="MooPanel_tabContent8">
                    <h2>Delivery areas</h2>
                    <form method="post" action="options.php" onsubmit="return moo_save_changes()">
                        <?php
                        $MooOptions = (array)get_option('moo_settings');
                        settings_fields('moo_settings') ?>
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
                    <div class="MooPanelItem">
                        <h3>Set Delivery Areas <span class="moo_adding-zone-btn" onclick="moo_show_form_adding_zone()">Add zone</span></h3>
                        <div class="Moo_option-item" id='moo_adding-zone'>
                            <table style="margin: 0 auto;">
                                    <tr>
                                        <td><label for="moo_dz_name">Name</label></td>
                                        <td><input type="text" id="moo_dz_name"><br/></td>
                                    </tr>
                                    <tr id="moo_dz_type_line">
                                        <td><label for="moo_dz_type">Type</label></td>
                                        <td>
                                            <input type="radio" id="moo_dz_typeC" name='moo_dz_type' checked>
                                            <label for="moo_dz_typeC">Circle</label>
                                            <input type="radio" id="moo_dz_typeS" name='moo_dz_type' >
                                            <label for="moo_dz_typeS">Shape</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="moo_dz_min">Minimum order</label></td>
                                        <td>$<input type="text" id="moo_dz_min" value="0.00"><br/></td>
                                    </tr>
                                    <tr>
                                        <td><label for="moo_dz_fee">Delivery fee</label></td>
                                        <td>$<input type="text" id="moo_dz_fee" value="0.00"><br/></td>
                                    </tr>
                                    <tr id="moo_dz_color_line">
                                        <td><label for="moo_dz_color">Color</label></td>
                                        <td><input type="text" id="moo_dz_color" class="moo-color-field" value="#2788d8"></td>
                                    </tr>
                                    <tr id="moo_dz_action_for_adding">
                                        <td colspan="2">
                                            <button type="button" class="button" onclick="moo_draw_zone()">Draw the zone</button>
                                            <button type="button" class="button button-primary" onclick="moo_validate_selected_zone()">Validate the selected zone</button>
                                            <button type="button" class="button" onclick="moo_deleteSelectedShape();">Delete the selected zone</button>
                                            <button type="button" class="button" onclick="moo_cancel_adding_form()">Cancel</button>
                                        </td>
                                    </tr>
                                <tr id="moo_dz_action_for_updating">
                                        <td colspan="2">
                                            <input type="text" value="" id="moo_dz_id_for_update" hidden>
                                            <button type="button" class="button button-primary" onclick="moo_update_selected_zone()">Update the zone</button>
                                            <button type="button" class="button" onclick="moo_cancel_adding_form()">Cancel</button>
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
                            <div style="padding-right: 5px;margin-top: -10px;font-size: 12px;">
                                Free Delivery :  if customer spends over this dollar amount, then delivery fee is free, Keep empty if you don't want to offer free delivery
                            </div>
                        </div>
                        <div class="Moo_option-item">
                            <div class="label">Min Amount</div>
                            $<input style="width: 59%" name="moo_settings[free_delivery]" type="text" value="<?php echo $MooOptions['free_delivery']?>" />
                        </div>
                        <div class="Moo_option-item" >
                            <div style="padding-right: 5px;font-size: 12px;">
                                Fixed Delivery amount :  will applied for any delivered order (Orders types with shipping address is enabled ), Keep empty if you don't want to offer fixed delivery amount
                            </div>
                        </div>
                        <div class="Moo_option-item">
                            <div class="label">Fixed Delivery amount</div>
                            $<input style="width: 59%" name="moo_settings[fixed_delivery]" type="text" value="<?php echo $MooOptions['fixed_delivery']?>" />
                        </div>
                        <div class="Moo_option-item" >
                            <div style="padding-right: 5px;font-size: 12px;">
                                Other Zones Delivery fees :  will applied for the customers aren't in the zones drawn above, Keep empty to not offer delivery for others zones
                            </div>
                        </div>
                        <div class="Moo_option-item">
                            <div class="label">Other Zones Delivery fees</div>
                            $<input style="width: 59%" name="moo_settings[other_zones_delivery]" type="text" value="<?php echo $MooOptions['other_zones_delivery']?>" />
                        </div>
                    </div>
                    <div style="text-align: center; margin: 20px;">
                        <textarea id="moo_zones_json" name="moo_settings[zones_json]" hidden><?php echo $MooOptions['zones_json']?></textarea>

                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                        <a href="<?php echo (esc_url((admin_url('admin.php?page=moo_index')))); ?>" class="button">Cancel</a>
                    </div>
                    </form>
                </div>
                <!-- Feedback -->
                <div id="MooPanel_tabContent9">
                    <h2>Feedback / Help</h2>
                    <div class="MooPanelItem">
                        <h3>Do you need help or would like to give us feedback.<br/>Please e-mail or call us: 925-234-5554 (8am-8pm pacific time)</h3>
                        <div class="Moo_option-item">
                            <label for="MoofeedBackEmail"">Your Email</label>
                            <input type="text" name=MoofeedBackEmail" id=MoofeedBackEmail" placeholder="Your email" value="<?php echo $MooOptions['merchant_email']?>" style="width: 100%">
                            <label for="MoofeedBack"">Your Message *</label>
                            <textarea name="MooFeedBack" id="Moofeedback" cols="10" rows="10" style="width: 100%"></textarea>
                            <div style="text-align: right;">
                                <a class="button button-primary" href="#" onclick="MooSendFeedBack(event)">Send</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FAQ -->
                <div id="MooPanel_tabContent10">
                    <h2>FAQ</h2>
                    <div class="MooPanelItem">
                        <h3>FAQ</h3>
                        <div class="Moo_option-item">
                            <div>
                                Q1 - Once an order is placed, does it print to the clover POS? <br/>
                                A1 - By default, it prints to the clover POS. If it doesn't print open the Online ordering app for Wordpress on the Clover POS then adjust the settings to select a default printer  <br/><br/>
                                Q2 - We have several locations, how do I put the menu for each location on the same website? <br/>
                                A2 - You will need to create a subdomain on your website. For example, for a website named www.texasfoods.com
                                You would create as many subdomains needed for each location. Then install Wordpress into each subdomain. For example:<br/>
                                location1.texasfoods.com<br/>
                                location2.texasfoods.com<br/>
                                Then place a link on the website for each locations menu <br/><br/>
                                Q3 -  I'm having trouble installing the plugin or have other questions, can you help? <br/>
                                A3 - Yes, we can, please email or call us:
                                support@merchantech.us
                                925-234-5554 <br/><br/>
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

       // add_submenu_page('index.php', __('New Like Label'), __('New Link Label'), 'manage_options', 'new-link-display', 'new_link_display');


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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/moo-OnlineOrders-admin.css', array(), $this->version, 'all' );

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

        wp_register_script('moo-publicAdmin-js', plugins_url( 'js/moo-OnlineOrders-admin.js', __FILE__ ));

        wp_register_script('moo-google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBv1TkdxvWkbFaDz2r0Yx7xvlNKe-2uyRc&libraries=drawing');
        wp_register_script('moo-map-js', plugins_url( 'js/moo_map.js', __FILE__ ));
        wp_register_script('moo-map-da', plugins_url( 'js/moo_map_da.js', __FILE__ ));
        
        wp_register_script('magnific-modal', plugin_dir_url(dirname(__FILE__))."public/js/magnific.min.js");
        wp_enqueue_script('magnific-modal',array('jquery'));
        
        wp_enqueue_script('moo-publicAdmin-js',array('jquery','wp-color-picker'));
        
        wp_localize_script("moo-publicAdmin-js", "moo_params",$params);
	}

    public function moo_update_address()
    {
        $MooOptions = (array)get_option('moo_settings');
        $api   = new  moo_OnlineOrders_CallAPI();

        $merchant_address =  $api->getMerchantAddress();

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
        <textarea name="moo_settings[zones_json]" hidden><?php echo $MooOptions['zones_json']?></textarea>
        <input type="text"  name="moo_settings[tips]"     value="<?php echo $MooOptions['tips']?>" hidden/>

        <input type="hidden" name="_wp_http_referer" value="<?php echo (esc_url((admin_url('admin.php?page=moo_index')))); ?>" />

            <div id="MooPanel_tabContent1">
            <h2>Set-up your address</h2>
            <div class="MooPanelItem">
                <h3>Please validate or change your address</h3>
                <div class="Moo_option-item">
                    <p>Your current address is : </p>
                    <p><?php echo $merchant_address?></p>
                    <div class="moo_map" id="moo_map"></div>
                </div>
                <div class="Moo_option-item">
                    <label for="Moo_Lat">Lat : </label>
                    <input id="Moo_Lat" type="text" size="15" name="moo_settings[lat]" value="<?php echo $MooOptions['lat']?>" />
                    <label for="Moo_Lng">Lng : </label>
                    <input id="Moo_Lng" type="text" size="15" name="moo_settings[lng]" value="<?php echo $MooOptions['lng']?>" />
                    <div style="text-align: center; margin: 20px;">
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
