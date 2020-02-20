<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smart Online Orders
 * @subpackage moo_OnlineOrders/admin
 * @author     Mohammed EL BANYAOUI <m.elbanyaoui@gmail.com>
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

    /*
     * Use jQuery Ui froml external link
     */
    private $external_ui;


    /**
     * @var Moo_OnlineOrders_CallAPI
     */
    private $api;

    /**
     * @var Moo_OnlineOrders_Model
     */
    private $model;

    /**
     * The SESSION
     *
     * @var MOO_SESSION
     */
    private $session;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version, $apiInstance, $modelInstance ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->external_ui = false;
        $this->api = $apiInstance;
        $this->model = $modelInstance;
        //$this->session = MOO_SESSION::instance();

    }
    public function add_admin_menu()
    {
        $icon_url =  plugin_dir_url(dirname(__FILE__))."public/img/launcher.png";
        add_menu_page('Settings page', 'Clover Orders', 'manage_options', 'moo_index', array($this, 'panel_settings'),$icon_url);
        add_submenu_page('moo_index', 'Settings', 'Settings', 'manage_options', 'moo_index', array($this, 'panel_settings'));
        add_submenu_page('moo_index', 'Themes', 'Store Interfaces', 'manage_options', 'moo_themes', array($this, 'page_themes'));
        add_submenu_page('moo_index', 'Items/Images', 'Items / Images / Description', 'manage_options', 'moo_items', array($this, 'page_products'));
        add_submenu_page('moo_index', 'Orders', 'Orders', 'manage_options', 'moo_orders', array($this, 'page_orders'));
        add_submenu_page('moo_index', 'Coupons', 'Coupons', 'manage_options', 'moo_coupons', array($this, 'page_coupons'));
        add_submenu_page('moo_index', 'Reports', 'Reports', 'manage_options', 'moo_reports', array($this, 'page_reports'));
       // add_submenu_page('moo_index', 'Import your Inventory', '', 'manage_options', 'moo_import', array($this, 'page_import'));
       // add_submenu_page('moo_index', '', '', 'manage_options', 'moo_deactivateAndClean', array($this, 'moo_deactivateAndClean'));
    }
    public function page_products()
    {
        require_once plugin_dir_path( dirname(__FILE__))."admin/includes/class-moo-products-list.php";

        $products = new Products_List_Moo();
        $products->prepare_items();
        $model = $this->model;
        if(isset($_GET['action']) && $_GET['action'] == 'update_item')
        {
            if(isset($_GET['item_uuid']) && $_GET['item_uuid'] != '')
            {
                $item_uuid = $_GET['item_uuid'];
                $item = $model->getItem($item_uuid);
                // var_dump($item);

                ?>
                <div class="wrap" xmlns="http://www.w3.org/1999/html">
                    <h2>Edit an Item</h2>
                    <div id="moo_editItem" style="margin-top: 25px;">
                        <div class="moo_editItem_left">
                            <div class="edit_item_left_holder"><span>Item Name : </span> <strong><p id="moo_item_name"><?php echo $item->name; ?></p></strong></div><hr />
                            <div class="edit_item_left_holder"><span>Item Price : </span> <strong><p id="moo_item_price">$<?php echo $item->price/100; ?></p></div><strong><hr />
                                <div class="edit_item_left_holder"><span>Item Description : </span></div>
                                <div class="edit_item_left_holder">
                                    <textarea style="width:100%;" name="" rows="4" id="moo_item_description"><?php echo stripslashes ($item->description); ?></textarea>
                                </div><hr />
                                <div class="edit_item_left_holder">
                                    <span>Add to cart button : </span>
                                    <p>
                                        <code>
                                            [moo_buy_button id='<?php echo $item_uuid?>']
                                        </code>
                                    </p>
                                </div><hr />
                                <div style="text-align: center;">
                                    <a href="#" class="button button-primary" onclick="moo_save_item_images('<?php echo $item_uuid?>')">Save item</a>
                                    <a href="<?php echo (admin_url('admin.php?page=moo_items'))?>" class="button button-secondary" >Go back</a>
                                </div>
                        </div>
                        <div class="moo_editItem_right">

                            <div class="moo_pull_right" id="moo_uploadImgBtn"> <a class="button" onclick="open_media_uploader_image()">Upload Image</a></div>
                            <div class="moo_itemsimages" id="moo_itemimagesection" style="margin-left: 4%">
                            </div>
                            <div class="square_images" style='margin: 4%;'>
                                <span style="color: red;">*</span>Images (Square Images for better scaling)
                            </div>
                        </div>

                    </div>
                </div>
                <script type="application/javascript">
                    moo_get_item_with_images('<?php echo $item_uuid?>');
                </script>

                <?php
            }
        } else {
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
        if(isset($_GET['action']) && $_GET['action'] == 'show_order_detail')
        {
            if(isset($_GET['order_uuid']) && $_GET['order_uuid'] != '')
            {

                $model = $this->model;
                $api = $this->api;
                $MooOptions = (array)get_option("moo_settings");

                $orderId = sanitize_text_field($_GET['order_uuid']);
                $order_items = $model->getItemsOrder($orderId);
                $orderDetail = $model->getOneOrder($orderId);
                $view = $api->getOrderDetails($orderDetail);

                if(isset($view['payments']) && count($view['payments'])>0 )
                {
                    $orderPayments = $view['payments'];
                    $status="";
                    $status_color="";

                    foreach ($orderPayments as $p) {
                        if ($p->result == "APPROVED")
                        {
                            $status = "Paid";
                            $status_color='green';
                        }
                    }
                    
                    if($status == "")
                    {
                        $status = "Not Paid";
                        $status_color='red';
                    }
                } else {
                    if($view['paymentMethode'] == 'cash') {
                        $status ='Will Pay Cash';
                        $status_color='blue';

                    } else {
                        $status = 'Not paid';
                        $status_color='red';

                    }

                }

                $price_item = 0;

                ?>
                <h1>Detail order</h1>
                <div class="moo_order_detail">
                    <div class="detail order_status">
                        <p>STATUS :</p>
                        <p class="moo_order_status" style="color:<?php echo $status_color; ?>"><?php echo $status; ?></p>
                    </div>
                    <div class="detail order_info">
                        <h2>ORDER INFO</h2>
                        <?php
                        if($view['date_order'] != "")
                            echo  '<p><strong>Order date :  </strong>'.$view['date_order'].' UTC</p>';
                        if($view['order_type'] != "")
                            echo  '<p><strong>Order type : </strong>'.$view['order_type'].'</p>';

                        if($view['paymentMethode'] != "")
                        {
                            if($view['paymentMethode']=="creditcard")
                                echo  '<p><strong>Payment method : </strong>Credit Card</p>';
                            else
                                echo  '<p><strong>Payment method : </strong>'.$view['paymentMethode'].'</p>';
                        }
                        if($view['uuid_order'] != "")
                            echo  '<p><strong>Order id : </strong>'.$view['uuid_order'].'</p>';

                        ?>
                    </div>
                    <div class="detail order_Customer">
                        <h2>CUSTOMER</h2>
                        <?php
                        if($view['name_customer']!="")
                            echo ' <strong>'.$view['name_customer'].'</strong><br />';
                        if($view['address_customer']!="")
                            echo $view['address_customer'].'<br />';
                        if($view['city_customer']!="")
                            echo $view['city_customer'].', ';
                        if($view['state_customer']!="")
                            echo strtoupper($view['state_customer']);
                        if($view['zipcode']!="")
                            echo ' '.$view['zipcode'].'<br />';
                        if($view['email_customer']!="")
                            echo ' <strong>Email : </strong>'.$view['email_customer'].' <br />';
                        if($view['phone_customer']!="")
                            echo ' <strong>Phone : </strong>'.$view['phone_customer'].' <br />';
                        if($view['lat'] != "" && $view['lng'] != "")
                               echo '<a href="#" onclick="moo_showCustomerMap(event,\''.$view['lat'].'\',\''.$view['lng'].'\')">Show address on map</a><br/><div id="mooCustomerMap"></div>';
                        ?>
                    </div>
                </div>
                <div class="list_itemsOrder">
                    <div class="table_items">
                        <h2>ITEMS</h2>
                        <table class="main_table">
                            <tr class="top_table">
                                <th>Name</th><th>Unit price</th><th>Quantity</th><th>Total price</th>
                            </tr>
                            <?php
                            $subtotal_order = 0;
                            foreach ($order_items as $item) {
                                $line_price =  $item->price;
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        $modifiers = $item->modifiers;
                                        if($modifiers != ""){
                                            echo $item->name."($".number_format(($item->price/100),2).")</br>";
                                            $string = substr($modifiers, 0, strlen($modifiers)-1);
                                            $data_modifier = explode( ',', $string);
                                            foreach ($data_modifier as $modifier){
                                                $getModifier = $model->getModifier($modifier);
                                                echo " - ".$getModifier->name."($".number_format(($getModifier->price/100),2).")</br>";
                                                $line_price += $getModifier->price;
                                            }
                                        } else {
                                            echo $item->name;
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align:center"><?php echo "$".number_format(($line_price/100),2); ?></td>
                                    <td style="text-align:center"><?php echo $item->quantity; ?></td>
                                    <td style="text-align:center"><?php echo "$".number_format((($item->quantity*$line_price)/100),2); ?></td>
                                </tr>
                                <?php
                                $subtotal_order += $line_price*$item->quantity;
                            }
                            ?>
                            <tr class="info-total">
                                <td colspan="3" style="text-align: right"><strong>Subtotal</strong></td><td><?php echo "$".number_format($subtotal_order/100,2); ?></td>
                            </tr>
                            <tr class="info-total">
                                <td colspan="3" style="text-align: right"><strong>Taxes</strong></td>
                                <td>
                                    <?php
                                    if($view['taxAmount'] && !$view['taxRemoved'])
                                        echo "$".number_format($view['taxAmount'],2);
                                     else
                                         echo "$0.00";
                                    ?>
                                </td>
                            </tr>
                            <?php
                                if(isset($view['deliveryAmount']) && $view['deliveryAmount']>0){
                                    echo '<tr class="info-total">';
                                    echo '<td colspan="3" style="text-align: right"><strong>'.(($view['deliveryName']=="" || $view['deliveryName']=="null")?$MooOptions["delivery_fees_name"]:$view['deliveryName']).'</strong></td><td>$'.number_format($view['deliveryAmount'],2).'</td>';
                                    echo '</tr>';
                                 }
                                 if(isset($view['serviceFee']) && $view['serviceFee']>0){
                                    echo '<tr class="info-total">';
                                    echo '<td colspan="3" style="text-align: right"><strong>'.(($view['serviceFeeName']== "" || $view['serviceFeeName']=="null")?$MooOptions["service_fees_name"]:$view['serviceFeeName']).'</strong></td><td>$'.number_format($view['serviceFee'],2).'</td>';
                                    echo '</tr>';
                                 }
                                 if(isset($view['tipAmount']) && $view['tipAmount']>0){
                                    echo '<tr class="info-total">';
                                    echo '<td colspan="3" style="text-align: right"><strong>Tips</strong></td><td>$'.number_format($view['tipAmount'],2).'</td>';
                                    echo '</tr>';
                                 }
                                if(isset($view["coupon"]) && $view["coupon"]) {
                                    echo "<tr class=\"info-total\">";
                                    if($view["coupon"]->type == "amount")
                                        echo "<td colspan='3' style='text-align: right'><strong>".$view["coupon"]->name."</strong></td><td>- $".number_format($view["coupon"]->value,2)."</td>";
                                    else
                                        echo "<td colspan='3' style='text-align: right'><strong>".$view["coupon"]->name."</strong></td><td>- $".number_format(($view["coupon"]->value*$subtotal_order/10000),2)."</td>";


                                    echo "</tr>";
                                }
                            ?>
                            <tr class="info-total">
                                <td colspan="3" style="text-align: right"><strong>Total</strong></td><td><?php echo "$".number_format($view['amount_order'],2); ?></td>
                            </tr>
                        </table>
                    </div>
                    <?php if(count($view['payments']) > 0){?>
                        <div class="payment_opt">
                            <h2>PAYMENTS</h2>
                            <table class="main_table paymentTable">
                                <tr class="top_table">
                                    <th>Payment results</th><th>Total</th><th>Subtotal</th><th>Taxes</th><th>Tips</th><th>Credit Card</th><th>payment date</th>
                                </tr>
                                <?php
                                foreach ($view['payments'] as $payment) {
                                    //var_dump($payment);
                                    echo "<tr>";
                                    echo "<td>".$payment->result."</td>";
                                    echo "<td>$".number_format((($payment->paymentAmount+$payment->tipAmount)/100),2)."</td>";

                                    if(!$view['taxRemoved'])
                                    {
                                        echo "<td>$".number_format((($payment->paymentAmount-$payment->taxAmount)/100),2)."</td>";
                                        echo "<td>$".number_format(($payment->taxAmount/100),2)."</td>";
                                    }
                                    else
                                    {
                                        echo "<td>$".number_format((($payment->paymentAmount)/100),2)."</td>";
                                        echo "<td>Not taxable</td>";
                                    }
                                    echo "<td>$".number_format(($payment->tipAmount/100),2)."</td>";
                                    echo "<td> ********".$payment->last4."</td>";
                                    echo "<td>".$payment->createdtime." UTC</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </div>
                        <?php
                    }
                    if($view['special_instruction']!="")
                        echo '<div class="instruction_order"><h2>Special instructions</h2> <p>'.stripslashes ($view['special_instruction']).'</p></div>';
                    ?>
                </div>
                <?php
            }
            else
                echo 'Not Found';
        }
        else
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
    }
    public function page_coupons()
    {
        $d = new DateTime('today');
        if(isset($_GET['action']) && ($_GET['action'] == 'add_coupon' || $_GET['action'] == "edit_coupon") )
         {
            $action = $_GET['action'];
            require_once plugin_dir_path( dirname(__FILE__))."/models/moo-OnlineOrders-CallAPI.php";
            $api = new moo_OnlineOrders_CallAPI();
            $message="";
            $header_message = "Add New coupon";
            if(isset($_POST['submit'])) {
                $theCoupon = array(
                    "CouponName"=>$_POST['CouponName'],
                    "CouponCode"=>$_POST['CouponCode'],
                    "CouponType"=>$_POST['CouponType'],
                    "CouponValue"=>$_POST['CouponValue'],
                    "CouponMinAmount"=>$_POST['CouponMinAmount'],
                    "CouponMaxUses"=>$_POST['CouponMaxUses'],
                    "CouponStartDate"=>$_POST['CouponStartDate'],
                    "CouponExpiryDate"=>$_POST['CouponExpiryDate'],
                );


                if(!isset($_POST['CouponName']) || $_POST['CouponName'] == "")
                    $message ="Please enter the coupon name";
                else
                    if(!isset($_POST['CouponCode']) || $_POST['CouponCode'] == "" || preg_match('/\s/',$_POST['CouponCode']))
                        $message =" Please enter a valid coupon Code";
                    else
                        if(!isset($_POST['CouponType']) || $_POST['CouponType'] == "" || ($_POST['CouponType'] != "amount" && $_POST['CouponType'] != "percentage" ))
                            $message =" Please select the discount type";
                        else
                            if(!isset($_POST['CouponValue']) || $_POST['CouponValue']=="" || $_POST['CouponValue'] <= 0 )
                                $message =" Please enter a valid value (should be a positive number)";
                            else
                                if(!isset($_POST['CouponMinAmount']) || ($_POST['CouponMinAmount'] != "" && $_POST['CouponMinAmount'] < 0) )
                                    $message =" Please enter a valid minAmount value (should be a positive number)";
                                else
                                    if(!isset($_POST['CouponMaxUses']) || $_POST['CouponMaxUses']=="" || $_POST['CouponMaxUses'] < 0 )
                                        $message =" Please enter a valid max use value (should be a positive number)";
                                    else
                                        if(!isset($_POST['CouponExpiryDate']) || $_POST['CouponExpiryDate']== "")
                                            $message =" The Expiration date is required";

                $class='error';
                if($message == "") {
                    if($_POST['submit'] == "Add")
                    {
                        $d = new DateTime('today');
                        $coupon = array(
                            "name"=>$_POST['CouponName'],
                            "code"=>$_POST['CouponCode'],
                            "value"=>$_POST['CouponValue'],
                            "type"=>$_POST['CouponType'],
                            "expirationdate"=>$_POST['CouponExpiryDate'],
                            "minAmount"=>$_POST['CouponMinAmount'],
                            "maxuses"=>$_POST['CouponMaxUses'],
                            "startdate"=>$_POST['CouponStartDate']
                        );
                        $res = json_decode($api->addCoupon($coupon));
                        if($res->status=="success")
                        {
                            $message = 'The coupon was added';
                            $class="success";

                        }
                        else
                            $message = $res->message;
                    } else {
                        if($_POST['submit'] == "Save") {
                            $coupon = array(
                                "name"=>$_POST['CouponName'],
                                "code"=>$_POST['CouponCode'],
                                "value"=>$_POST['CouponValue'],
                                "type"=>$_POST['CouponType'],
                                "expirationdate"=>$_POST['CouponExpiryDate'],
                                "minAmount"=>$_POST['CouponMinAmount'],
                                "maxuses"=>$_POST['CouponMaxUses'],
                                "startdate"=>$_POST['CouponStartDate']
                            );
                            $res = json_decode($api->updateCoupon($_GET["coupon"],$coupon));
                            if($res->status=="success") {
                                if($_GET['coupon']!=$_POST['CouponCode'])
                                    $message = 'The coupon was updated. You are updated the coupon code, any other changes on this page will not affect the coupon please go back to coupons page';
                                else
                                    $message = 'The coupon was updated';

                                $class="success";

                            } else {
                                $message = $res->message;
                            }
                        }
                    }
                }
            } else {
                if($action=="edit_coupon") {
                    $coupon_code = $_GET['coupon'];
                    $coupon = json_decode($api->getCoupon($coupon_code));
                    if(isset($coupon->status))
                        if($coupon->status=="success")
                        {
                            $c = $coupon->coupon;
                            $theCoupon = array(
                                "CouponName"=>$c->name,
                                "CouponCode"=>$c->code,
                                "CouponType"=>$c->type,
                                "CouponValue"=>$c->value,
                                "CouponMinAmount"=>$c->minAmount,
                                "CouponMaxUses"=>$c->maxuses,
                                "CouponExpiryDate"=>$c->expirationdate,
                                "CouponStartDate"=>$c->startdate
                            );
                            $header_message = 'Edit a coupon';
                        }
                        else
                            die($coupon->message);
                    else
                        die($coupon);
                }
            }
                ?>
                <div class="wrap">
                    <h2><?php echo $header_message;?></h2>
                    <?php if($message!="")
                        echo '<div class="notice notice-'.$class.' is-dismissibl" style="min-height: 33px;line-height: 33px;">'.$message.'</div>';
                    ?>
                    <form method="post" action="#">
                        <table class="form-table">
                            <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="couponName">Coupon name</label>
                                </th>
                                <td>
                                    <input name="CouponName" type="text" id="CouponName" class="regular-text" value="<?php echo (isset($theCoupon['CouponName']))?$theCoupon['CouponName']:'';?>" required>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="CouponCode">Coupon Code</label>
                                </th>
                                <td>
                                    <input name="CouponCode" type="text" id="CouponCode" aria-describedby="CouponCode-description" class="regular-text" value="<?php echo (isset($theCoupon['CouponCode']))?$theCoupon['CouponCode']:'';?>" required>
                                    <p class="description" id="CouponCode-description">This  coupon code will be used by customers during checkout to receive a discount (please do not use spaces and special characters)</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="CouponType">Type of discount</label>
                                </th>
                                <td>
                                    <select name="CouponType" id="CouponType">
                                        <option <?php echo (isset($theCoupon['CouponType']) && $theCoupon['CouponType']=='amount')?'selected="selected"':'';?> value="amount">Amount</option>
                                        <option <?php echo (isset($theCoupon['CouponType']) && $theCoupon['CouponType']=='percentage')?'selected="selected"':'';?> value="percentage">Percentage</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="CouponValue">Coupon value</label>
                                </th>
                                <td>
                                    <input name="CouponValue" type="number" min="0" step="0.01" id="CouponValue" value="<?php echo (isset($theCoupon['CouponValue']))?$theCoupon['CouponValue']:'';?>" required>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="CouponMinAmount">Minimum Amount</label>
                                </th>
                                <td>
                                    <input name="CouponMinAmount" type="number" min="0" step="0.01" id="CouponMinAmount" aria-describedby="CouponMinAmount-description" value="<?php echo (isset($_POST['CouponMinAmount']))?$_POST['CouponMinAmount']:'';?>">
                                    <p class="description" id="CouponMinAmount-description">The coupon will be valid only if the subtotal is greater than the min amount</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="CouponStartDate">Starting date</label>
                                </th>
                                <td>
                                    <input name="CouponStartDate" type="text" id="CouponStartDate" value="<?php echo (isset($theCoupon['CouponStartDate']))?$theCoupon['CouponStartDate']:$d->format('m-d-Y');?>" >
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="CouponExpiryDate">Expiration date</label>
                                </th>
                                <td>
                                    <input name="CouponExpiryDate" type="text" id="CouponExpiryDate" value="<?php echo (isset($theCoupon['CouponExpiryDate']))?$theCoupon['CouponExpiryDate']:'';?>" >
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="CouponMaxUses">Number of uses</label>
                                </th>
                                <td>
                                    <input name="CouponMaxUses" type="number" id="CouponMaxUses" aria-describedby="CouponMaxUses-description" value="<?php echo (isset($theCoupon['CouponMaxUses']))?$theCoupon['CouponMaxUses']:'0';?>">
                                    <p class="description" id="CouponMaxUses-description">Enter 0 for unlimited uses. This is for total number of uses for all customers. To allow for more
                                    customizations of coupons, download the Coupon app from the Clover App Market by Zaytech. It will allow you to print coupons on your Clover POS and have them redeemed online
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p>After adding coupon, make sure it is enabled by going to clover orders, settings, checkout settings. You can also  <a href="<?php echo (esc_url((admin_url('admin.php?page=moo_index#checkout')))); ?>">click here</a></p>
                        <p class="submit">
                            <?php
                            if($action == "add_coupon"){ ?>
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Add">
                            <?php } ?>
                            <?php if($action == "edit_coupon"){ ?>
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
                            <?php } ?>
                        </p>
                    </form>
                </div>
                <?php
            }
        else
        {
            require_once plugin_dir_path( dirname(__FILE__))."admin/includes/class-moo-coupons-list.php";
            $orders = new Coupons_List_Moo();
            $orders->prepare_items();

            $message="";
            if(isset($_GET['enabled']) && $_GET['enabled'] ==="1")
                $message = '<div class="update-nag" style="display: block;">The coupon have been enabled</div>';
            else
                if(isset($_GET['disabled']) && $_GET['disabled'] ==="1")
                    $message = '<div class="update-nag" style="display: block;">The coupon have been disabled</div>';
                else
                    if(isset($_GET['deleted']) && $_GET['deleted'] ==="1")
                        $message = '<div class="update-nag" style="display: block;">The coupon was removed</div>';
            ?>
            <div class="wrap">
                <?php if($message!="") echo $message; ?>
                <h1 style="float: left;">List of coupons</h1>
                <a href="<?php echo add_query_arg(array("action"=>"add_coupon"),remove_query_arg( array('coupon', 'paged'))); ?>" class="page-title-action" style="float: left;top: 11px;margin-left: 18px;">Add Coupon</a>
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
    }
    public function page_reports()
    {
        require_once plugin_dir_path( dirname(__FILE__))."/models/moo-OnlineOrders-CallAPI.php";
        $api = new moo_OnlineOrders_CallAPI();
        $api->goToReports();

    }
    public function page_themes()
    {
        require_once plugin_dir_path( dirname(__FILE__))."/models/moo-OnlineOrders-CallAPI.php";
        $api = new moo_OnlineOrders_CallAPI();
        $params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php', isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' ),
            'plugin_url'=>plugin_dir_url(dirname(__FILE__)),
            'plugin_img'=>plugins_url( '/img', __FILE__ ),
            'nonce' => wp_create_nonce( 'wp_rest' )
        );

        wp_enqueue_script('moo-AdminDashboard-js');
        wp_localize_script("moo-AdminDashboard-js", "moo_params",$params);
        ?>
        <div class="wrap">
            <div class="moo_dashboard_logo">
                <div class="moo_dashboard_logo_img">
                    <a href="http://smartonlineorder.com" title="Smart Online Order" target="_blank">
                        <img src="//api.smartonlineorders.com/assets/images/small-logo.png" alt="Smart online order logo">
                    </a>
                </div>
                <div class="moo_dashboard_logo_links">
                    <a target="_blank" href="https://docs.smartonlineorder.com" title="Read smart online order Documentation">
                        Documentation
                    </a>|
                    <a target="_blank" href="https://smartonlineorder.com/contact-us/" title="Need Help ?">
                        Get Support
                    </a>|
                    <span class="moo_dashboard_header_version"><strong><?php echo $this->version;?></strong></span>
                </div>
            </div>
            <h2 class="moo_dashboard_title">
                <i class="moo_dashboard_title_icon fas fa-cubes"></i>
                Store Interfaces
            </h2>
            <div>
                <div class="moo_dashboard_wrapper">
                    <?php if(!isset($_GET["theme_identifier"])) { ?>
                        <!-- Menu -->
                        <div class="moo_dashboard_nav_tabs pull-left">
                            <div class="moo_dashboard_nav_tab pull-left noMargin active" id="mooDashbboardTab1" onclick="moo_dashboard_tab_clicked(1)">
                                <i class="moo_dashboard_tab_icon fas fa-check-circle"></i><br>
                                <span>Available Store Interfaces</span>
                            </div>
<!--                            <div class="moo_dashboard_nav_tab pull-left noMargin" id="mooDashbboardTab2" onclick="moo_dashboard_tab_clicked(2)">-->
<!--                                <i class="moo_dashboard_tab_icon fas fa-th-large"></i><br>-->
<!--                                <span>Browse New Store Interfaces</span>-->
<!--                            </div>-->
                        </div>
                        <!-- Fin Menu -->
                        <!-- Content -->
                        <!-- Tab installed themes -->
                        <div class="moo_dashboard_content_tabs" id="mooDashbboardTabContent1">
                        </div>
                        <!-- Tab installed themes -->
                        <div class="moo_dashboard_content_tabs" id="mooDashbboardTabContent2">
                        </div>
                        <!-- Fin Content -->
                   <?php } else {
                        $theme_id = $_GET["theme_identifier"];
                        //get the manifest file
                        $path = plugin_dir_path(dirname(__FILE__))."public/themes";
                            if(file_exists($path."/".$theme_id."/manifest.json")){
                                $theme_manifest = json_decode(file_get_contents($path."/".$theme_id."/manifest.json"),true);
                                echo '<h1> Customize '.$theme_manifest['name'].'</h1>';
                                if(!isset($theme_manifest['settings']) || $theme_manifest['settings'] === '' || !is_array($theme_manifest['settings'])){
                                    echo '<div class="moo_dashboard_text_error">Store interface not customizable</div>';
                                    echo '<div class="moo_dashboard_buttons_actions"><a href="?page=moo_themes" class="moo_dashboard_button moo_dashboard_medium  pull-left moo_dashboard_button_go_back" style="background: black;">Go back to store interfaces</a></div>';
                                } else {
                                    //get the theme settingsx
                                    $themes_current_settings = array();
                                    $settings = (array) get_option("moo_settings");

                                    foreach ($settings as $key=>$val) {
                                        $k = (string)$key;
                                        if(strpos($k,$theme_id."_") === 0 && $val != "") {
                                            $themes_current_settings[$key]= $val;
                                        }
                                    }
                                    echo '<div class="wpvr_options_content"> <form id="moo_theme_customize">';
                                    foreach ($theme_manifest['settings'] as $item_settings) {
                                        if(isset($item_settings['type'])) {
                                            $key = $theme_id."_".$item_settings["id"];
                                            if(!isset($themes_current_settings[$key]))
                                                $themes_current_settings[$key] = '';
                                            switch ($item_settings['type']) {
                                                case 'input_text':
                                                    ?>
                                                    <div class="moo_dashboard_option moo_dashboard_option_input moo_dashboard_input  on">
                                                        <div class="moo_dashboard_option_button pull-right">
                                                            <input type="text" class="moo_dashboard_input" name="<?php echo $item_settings['id'];?>" id="<?php echo $item_settings['id'];?>"  value="<?php echo $themes_current_settings[$key];?>">
                                                        </div>
                                                        <div class="option_text">
                                                            <span class="moo_dashboard_option_title"><?php echo $item_settings['label'];?></span>
                                                            <br>
                                                            <p class="moo_dashboard_option_desc">
                                                                <?php echo $item_settings['info'];?>
                                                            </p>
                                                        </div>
                                                        <div class="moo_dashboard_clearfix"></div>
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'textaerea':
                                                    ?>
                                                    <div class="moo_dashboard_option moo_dashboard_option_input moo_dashboard_input  on">
                                                        <div class="option_text">
                                                            <span class="moo_dashboard_option_title"><?php echo $item_settings['label'];?></span>
                                                            <br>
                                                            <p class="moo_dashboard_option_desc">
                                                                <?php echo $item_settings['info'];?>
                                                            </p>
                                                            <textarea type="text" class="moo_dashboard_textaerea" name="<?php echo $item_settings['id'];?>" id="<?php echo $item_settings['id'];?>"><?php echo $themes_current_settings[$key];?></textarea>
                                                        </div>
                                                        <div class="moo_dashboard_clearfix"></div>
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'input_number':
                                                    ?>
                                                    <div class="moo_dashboard_option moo_dashboard_option_input moo_dashboard_input  on">
                                                        <div class="moo_dashboard_option_button pull-right">
                                                            <input type="number" class="small moo_dashboard_input" name="<?php echo $item_settings['id'];?>" id="<?php echo $item_settings['id'];?>"  value="<?php echo $themes_current_settings[$key]; ?>">
                                                        </div>
                                                        <div class="option_text">
                                                            <span class="moo_dashboard_option_title"><?php echo $item_settings['label'];?></span>
                                                            <br>
                                                            <p class="moo_dashboard_option_desc">
                                                                <?php echo $item_settings['info'];?>
                                                            </p>
                                                        </div>
                                                        <div class="moo_dashboard_clearfix"></div>
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'onoff':
                                                    if($themes_current_settings[$key] != '') {
                                                        if($themes_current_settings[$key] == 'on') {
                                                            $checked = 'checked';
                                                        } else {
                                                            $checked = '';
                                                        }
                                                    } else {
                                                        if($item_settings['default'] == 'on') {
                                                            $checked = 'checked';
                                                        } else {
                                                            $checked = '';
                                                        }
                                                    }
                                                    ?>
                                                    <div class="moo_dashboard_option moo_dashboard_option_input moo_dashboard_input  on">
                                                        <div class="moo_dashboard_option_button pull-right">
                                                            <div class="moo-onoffswitch" >
                                                                <input type="hidden" name="<?php echo $item_settings['id'];?>" value="off">
                                                                <input type="checkbox" name="<?php echo $item_settings['id'];?>" class="moo-onoffswitch-checkbox" id="myonoffswitch_<?php echo $item_settings['id'];?>" <?php echo $checked; ?> >
                                                                <label class="moo-onoffswitch-label" for="myonoffswitch_<?php echo $item_settings['id'];?>"><span class="moo-onoffswitch-inner"></span>
                                                                    <span class="moo-onoffswitch-switch"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="option_text">
                                                            <span class="moo_dashboard_option_title"><?php echo $item_settings['label'];?></span>
                                                            <br>
                                                            <p class="moo_dashboard_option_desc">
                                                                <?php echo $item_settings['info'];?>
                                                            </p>
                                                        </div>
                                                        <div class="moo_dashboard_clearfix"></div>
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'color':
                                                    ?>
                                                    <div class="moo_dashboard_option moo_dashboard_option_input moo_dashboard_input  on">
                                                        <div class="moo_dashboard_option_button pull-right">
                                                            <input type="color" class="moo_dashboard_input moo_dashboard_inputColor" name="<?php echo $item_settings['id'];?>" id="<?php echo $item_settings['id'];?>"  value="<?php echo ($themes_current_settings[$key]=='')?$item_settings['default']:$themes_current_settings[$key];?>" onchange="mooChangedInputColorValue('<?php echo $item_settings['id'];?>')"/>
                                                        </div>
                                                        <div class="moo_dashboard_option_button pull-right">
                                                            <input type="text" size="7" class="moo_dashboard_input" name="<?php echo $item_settings['id'];?>" id="<?php echo $item_settings['id'];?>_val"  value="<?php echo ($themes_current_settings[$key]=='')?$item_settings['default']:$themes_current_settings[$key];?>" onchange="mooChangedInputColorTextValue('<?php echo $item_settings['id'];?>')"/>
                                                        </div>
                                                        <div class="option_text">
                                                            <span class="moo_dashboard_option_title"><?php echo $item_settings['label'];?></span>
                                                            <br>
                                                            <p class="moo_dashboard_option_desc">
                                                                <?php echo $item_settings['info'];?>
                                                            </p>
                                                        </div>
                                                        <div class="moo_dashboard_clearfix"></div>
                                                    </div>
                                                    <?php
                                                    break;
                                            }
                                        }
                                    }
                                    echo '</div>';
                                    echo '<div class="moo_dashboard_buttons_actions"><button onclick="moo_save_theme_customization(event,\''.$theme_id.'\')" class="moo_dashboard_button moo_dashboard_medium moo_dashboard_save_options pull-right "><i class="moo_dashboard_button_icon fas fa-save"></i>Save options</button><a href="?page=moo_themes" class="moo_dashboard_button moo_dashboard_medium  pull-left moo_dashboard_button_go_back" style="background: black;">Go back to store interfaces</a></div>';
                                    echo '</form>';
                                }

                            } else {
                                echo '<div class="moo_dashboard_text_error"> Store interface not installed correctly</div>';
                                echo '<div class="moo_dashboard_buttons_actions"><a href="?page=moo_themes" class="moo_dashboard_button moo_dashboard_medium  pull-left moo_dashboard_button_go_back" style="background: black;">Go back to store interfaces</a></div>';
                            }
                        ?>
                   <?php }?>

                </div>
            </div>

        </div>
        <?php
    }
    public function page_import()
    {
        require_once plugin_dir_path( dirname(__FILE__))."/models/moo-OnlineOrders-CallAPI.php";
        $api = new moo_OnlineOrders_CallAPI();
        wp_enqueue_script('moo-import-wizard-js');
        ?>
        <div class="wrap">
            <div class="moo_dashboard_logo">
                <div class="moo_dashboard_logo_img">
                    <a href="http://smartonlineorder.com" title="Smart Online Order" target="_blank">
                        <img src="//api.smartonlineorders.com/assets/images/small-logo.png" alt="Smart online order logo">
                    </a>
                </div>
                <div class="moo_dashboard_logo_links">
                    <a target="_blank" href="https://docs.smartonlineorder.com" title="Read smart online order Documentation">
                        Documentation
                    </a>|
                    <a target="_blank" href="https://smartonlineorder.com/contact-us/" title="Need Help ?">
                        Get Support
                    </a>|
                    <span class="moo_dashboard_header_version"><strong><?php echo $this->version;?></strong></span>
                </div>
            </div>
            <h2 class="moo_dashboard_title">
                <i class="moo_dashboard_title_icon fas fa-cubes"></i>
                Import your Inventory
            </h2>
            <div>
                <div class="moo_dashboard_wrapper">
                   <?php
                        $theme_id = 'onePage';
                        //get the manifest file
                        $path = plugin_dir_path(dirname(__FILE__))."public/themes";
                            if(file_exists($path."/".$theme_id."/manifest.json")){
                                $theme_manifest = json_decode(file_get_contents($path."/".$theme_id."/manifest.json"),true);
                                echo '<h1> Customize '.$theme_manifest['name'].'</h1>';
                                if(!isset($theme_manifest['settings']) || $theme_manifest['settings'] === '' || !is_array($theme_manifest['settings'])){
                                    echo '<div class="moo_dashboard_text_error">Store interface not customisable</div>';
                                    echo '<div class="moo_dashboard_buttons_actions"><a href="?page=moo_themes" class="moo_dashboard_button moo_dashboard_medium  pull-left moo_dashboard_button_go_back" style="background: black;">Go back to store interfaces</a></div>';

                                } else {
                                    //get the theme settings
                                    $themes_current_settings = array();
                                    $settings = (array) get_option("moo_settings");

                                    foreach ($settings as $key=>$val) {
                                        $k = (string)$key;
                                        if(strpos($k,$theme_id."_") === 0 && $val != "")
                                        {
                                            $themes_current_settings[$key]= $val;
                                        }
                                    }
                                    echo '<div class="wpvr_options_content"> <form id="moo_theme_customize">';
                                    foreach ($theme_manifest['settings'] as $item_settings) {
                                        if(isset($item_settings['type'])) {
                                            $key = $theme_id."_".$item_settings["id"];
                                            if(!isset($themes_current_settings[$key]))
                                                $themes_current_settings[$key] = '';
                                            switch ($item_settings['type']) {
                                                case 'input_text':
                                                    ?>
                                                    <div class="moo_dashboard_option moo_dashboard_option_input moo_dashboard_input  on">
                                                        <div class="moo_dashboard_option_button pull-right">
                                                            <input type="text" class="moo_dashboard_input" name="<?php echo $item_settings['id'];?>" id="<?php echo $item_settings['id'];?>"  value="<?php echo $themes_current_settings[$key];?>">
                                                        </div>
                                                        <div class="option_text">
                                                            <span class="moo_dashboard_option_title"><?php echo $item_settings['label'];?></span>
                                                            <br>
                                                            <p class="moo_dashboard_option_desc">
                                                                <?php echo $item_settings['info'];?>
                                                            </p>
                                                        </div>
                                                        <div class="moo_dashboard_clearfix"></div>
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'input_number':
                                                    ?>
                                                    <div class="moo_dashboard_option moo_dashboard_option_input moo_dashboard_input  on">
                                                        <div class="moo_dashboard_option_button pull-right">
                                                            <input type="number" class="small moo_dashboard_input" name="<?php echo $item_settings['id'];?>" id="<?php echo $item_settings['id'];?>"  value="<?php echo $themes_current_settings[$key]; ?>">
                                                        </div>
                                                        <div class="option_text">
                                                            <span class="moo_dashboard_option_title"><?php echo $item_settings['label'];?></span>
                                                            <br>
                                                            <p class="moo_dashboard_option_desc">
                                                                <?php echo $item_settings['info'];?>
                                                            </p>
                                                        </div>
                                                        <div class="moo_dashboard_clearfix"></div>
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'onoff':
                                                    if($themes_current_settings[$key] != '') {
                                                        if($themes_current_settings[$key] == 'on') {
                                                            $checked = 'checked';
                                                        } else {
                                                            $checked = '';
                                                        }
                                                    } else {
                                                        if($item_settings['default'] == 'on') {
                                                            $checked = 'checked';
                                                        } else {
                                                            $checked = '';
                                                        }
                                                    }
                                                    ?>
                                                    <div class="moo_dashboard_option moo_dashboard_option_input moo_dashboard_input  on">
                                                        <div class="moo_dashboard_option_button pull-right">
                                                            <div class="moo-onoffswitch" >
                                                                <input type="hidden" name="<?php echo $item_settings['id'];?>" value="off">
                                                                <input type="checkbox" name="<?php echo $item_settings['id'];?>" class="moo-onoffswitch-checkbox" id="myonoffswitch_<?php echo $item_settings['id'];?>" <?php echo $checked; ?> >
                                                                <label class="moo-onoffswitch-label" for="myonoffswitch_<?php echo $item_settings['id'];?>"><span class="moo-onoffswitch-inner"></span>
                                                                    <span class="moo-onoffswitch-switch"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="option_text">
                                                            <span class="moo_dashboard_option_title"><?php echo $item_settings['label'];?></span>
                                                            <br>
                                                            <p class="moo_dashboard_option_desc">
                                                                <?php echo $item_settings['info'];?>
                                                            </p>
                                                        </div>
                                                        <div class="moo_dashboard_clearfix"></div>
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'color':
                                                    ?>
                                                    <div class="moo_dashboard_option moo_dashboard_option_input moo_dashboard_input  on">
                                                        <div class="moo_dashboard_option_button pull-right">
                                                            <input type="color" class="moo_dashboard_input moo_dashboard_inputColor" name="<?php echo $item_settings['id'];?>" id="<?php echo $item_settings['id'];?>"  value="<?php echo ($themes_current_settings[$key]=='')?$item_settings['default']:$themes_current_settings[$key];?>">
                                                        </div>
                                                        <div class="option_text">
                                                            <span class="moo_dashboard_option_title"><?php echo $item_settings['label'];?></span>
                                                            <br>
                                                            <p class="moo_dashboard_option_desc">
                                                                <?php echo $item_settings['info'];?>
                                                            </p>
                                                        </div>
                                                        <div class="moo_dashboard_clearfix"></div>
                                                    </div>
                                                    <?php
                                                    break;
                                            }
                                        }
                                    }
                                    echo '</div>';
                                    echo '<div class="moo_dashboard_buttons_actions"><button onclick="moo_save_theme_customization(event,\''.$theme_id.'\')" class="moo_dashboard_button moo_dashboard_medium moo_dashboard_save_options pull-right "><i class="moo_dashboard_button_icon fas fa-save"></i>Save options</button><a href="?page=moo_themes" class="moo_dashboard_button moo_dashboard_medium  pull-left moo_dashboard_button_go_back" style="background: black;">Go back to store interfaces</a></div>';
                                    echo '</form>';
                                }

                            } else {
                                echo '<div class="moo_dashboard_text_error">Store interface not installed correctly</div>';
                                echo '<div class="moo_dashboard_buttons_actions"><a href="?page=moo_themes" class="moo_dashboard_button moo_dashboard_medium  pull-left moo_dashboard_button_go_back" style="background: black;">Go back to store interfaces</a></div>';
                            }

                        ?>
                </div>
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

    public function panel_settings() {
        $api   = $this->api;
        $model = $this->model;
        wp_enqueue_script('moo-grid');

        $errorToken = false;

        $MooOptions = (array)get_option('moo_settings');
        //Force options
        $MooOptions["save_cards"] = "disabled";

        $all_pages = get_pages();

        $token = $MooOptions["api_key"];
        $merchant_proprites = (json_decode($api->getMerchantProprietes())) ;
        $current_hours = $api->getOpeningHours() ;
        $orderTypesHours = json_decode($api->getMerchantCustomHours("ordertypes"),true);
        if($token !== '') {
            if($MooOptions['store_page'] == "") {
                echo '<div class="update-nag">Hello, please select the store page from settings then click save</div>';
            } else {
                if(get_post_status( $MooOptions['store_page'] ) === false )
                    echo '<div class="update-nag">Hello, please verify if the store page is published</div>';

                if( $MooOptions['cart_page'] == "") {
                    echo '<div class="update-nag">Hello, please select the cart page from settings then click save</div>';
                } else {
                    if(get_post_status( $MooOptions['cart_page'] )=== false )
                        echo '<div class="update-nag">Hello, please verify if the cart page is published</div>';

                    if( $MooOptions['checkout_page'] == "") {
                        echo '<div class="update-nag">Hello, please select the checkout page from settings then click save</div>';
                    } else {
                        if(get_post_status( $MooOptions['checkout_page'] )=== false )
                            echo '<div class="update-nag">Hello, please verify if the checkout page is published</div>';
                    }
                }
            }
        }


        if($token != '') {
            $result = $api->checkToken();
            if($result == 'Forbidden') $errorToken="( Token invalid )";
            else
            {
                if(isset(json_decode($result)->status) && json_decode($result)->status =='success')
                {
                    $api->updateWebsiteHooks(esc_url(admin_url('admin-post.php')));
                    $api->updateWebsite(get_rest_url());
                    $errorToken = "( Token valid )";
                } else {
                    $errorToken="( Token expired )";
                    $checkIp = $api->checkIpBlackListed();
                    if($checkIp != false) {
                        echo '<div class="update-nag">Please re-install the app Smart Online Order on your Clover account, if you have already did that please open it from the Clover App Market to refresh your key</div>';
                    }
                }

            }
        } else {
            $errorToken="( Required )";
        }

        if(isset($_GET["item_uuid"])) {
            $item_uuid = sanitize_text_field($_GET["item_uuid"]);
            $modifier_groups = $model->getAllModifiersGroupByItem($item_uuid);
        } else {
            $modifier_groups = $model->getAllModifiersGroup();
        }

        $all_categories  = $model->getCategories();

        /* Start Map Delivery area section */
        $merchant_address =  $api->getMerchantAddress();
        wp_enqueue_script('moo-google-map');
        wp_enqueue_script('moo-map-da',array('jquery','moo-google-map'));

        // Not localize empty params
        $localizeParams = array(
            "lat","lng"
        );
        foreach($MooOptions as $key=>$value) {
            if (in_array($key,$localizeParams)) {
                if ($value == "") {
                    $MooOptions[$key] = null;
                }
            }
        }
        if($merchant_address == "" || !$merchant_address) {
            $merchant_address = null;
        } else {
            $merchant_address = urlencode($merchant_address);
        }
        if($merchant_address == "" || !$merchant_address) {
            $merchant_address = null;
        }
        if($MooOptions['lat'] == "") {
            $MooOptions['lat'] = null;
        }
        if($MooOptions['lng'] == "") {
            $MooOptions['lng'] = null;
        }
        wp_localize_script("moo-map-da", "moo_merchantAddress",$merchant_address);
        wp_localize_script("moo-map-da", "moo_merchantLat",$MooOptions['lat']);
        wp_localize_script("moo-map-da", "moo_merchantLng",$MooOptions['lng']);
        /* Fin map Delivery area section*/
        ?>
        <div id="loader-wrapper">
            <div id="loader"></div>
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>

        <div id="MooPanel">
            <div id="MooPanel_sidebar">
                <div id="Moopanel_logo" style="margin-bottom: 20px">
                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/woo_100x100.png";?>" alt=""/>
                    <p>Smart Online Order for Clover by Zaytech</p>
                </div>
                <ul>
                    <a href="#apikey"><li class="MooPanel_Selected" id="MooPanel_tab1" onclick="tab_clicked(1)">Key settings</li></a>
                    <a href="#inventory"><li id="MooPanel_tab2" onclick="tab_clicked(2)">Import / Sync inventory</li></a>
                    <a href="#ordertypes"><li id="MooPanel_tab3" onclick="tab_clicked(3)">Orders Types</li></a>
                    <a href="#categories"><li id="MooPanel_tab5" onclick="tab_clicked(5)">Categories & Items</li></a>
                    <a href="#modifiergroups"><li id="MooPanel_tab6" onclick="tab_clicked(6)">Modifier groups & Modifiers</li></a>
                    <a href="#checkout"><li id="MooPanel_tab7" onclick="tab_clicked(7)">Checkout settings</li></a>
                    <a href="#store"><li id="MooPanel_tab8" onclick="tab_clicked(8)">Store settings</li></a>
                    <a href="#custom-hours"><li id="MooPanel_tab12" onclick="tab_clicked(12)">Custom Hours</li></a>
                    <a href="#delivery"><li id="MooPanel_tab9" onclick="tab_clicked(9)">Delivery areas & fees</li></a>
                    <a href="<?php echo admin_url()?>admin.php?page=moo_themes"><li>Store Interfaces <i class="fas fa-external-link-square-alt"></i></li></a>
                    <a href="<?php echo admin_url()?>admin.php?page=moo_items"><li>Items / Images / Description <i class="fas fa-external-link-square-alt"></i></li></a>
                    <a href="<?php echo admin_url()?>admin.php?page=moo_coupons"><li>Coupons <i class="fas fa-external-link-square-alt"></i></li></a>
                    <a href="#help"><li id="MooPanel_tab10" onclick="tab_clicked(10)">Feedback / Help</li></a>
                    <a href="#faq"><li id="MooPanel_tab11" onclick="tab_clicked(11)">FAQ</li></a>
                    <a href="https://www.youtube.com/channel/UCvG2UY0xjcLVTOccDqaGBow" target="_blank"><li>Video Tutorials <i class="fas fa-external-link-square-alt"></i></li></a>
                    <a href="https://docs.smartonlineorder.com" target="_blank"><li>Helpful Articles <i class="fas fa-external-link-square-alt"></i></li></a>
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
                        <a href="#apikey"><li  id="MooPanel_tab1" onclick="tab_clicked(1)">Key settings</li></a>
                        <a href="#inventory"><li id="MooPanel_tab2" onclick="tab_clicked(2)">Import / Sync inventory</li></a>
                        <a href="#ordertypes"><li id="MooPanel_tab3" onclick="tab_clicked(3)">Orders Types</li></a>
                        <a href="#categories"><li id="MooPanel_tab5" onclick="tab_clicked(5)">Categories & Items</li></a>
                        <a href="#modifiergroups"><li id="MooPanel_tab6" onclick="tab_clicked(6)">Modifier groups & Modifiers</li></a>
                        <a href="#checkout"><li id="MooPanel_tab7" onclick="tab_clicked(7)">Checkout settings</li></a>
                        <a href="#store"><li id="MooPanel_tab8" onclick="tab_clicked(8)">Store settings</li></a>
                        <a href="#custom-hours"><li id="MooPanel_tab12" onclick="tab_clicked(12)">Custom Hours</li></a>
                        <a href="#delivery"><li id="MooPanel_tab9" onclick="tab_clicked(9)">Delivery areas & fees</li></a>
                        <a href="<?php echo admin_url()?>admin.php?page=moo_themes"><li>Store Interfaces <i class="fas fa-external-link-square-alt"></i></li></a>
                        <a href="<?php echo admin_url()?>admin.php?page=moo_items"><li>Items / Images / Description <i class="fas fa-external-link-square-alt"></i>/li></a>
                        <a href="<?php echo admin_url()?>admin.php?page=moo_coupons"><li>Coupons <i class="fas fa-external-link-square-alt"></i></li></a>
                        <a href="#help"><li id="MooPanel_tab10" onclick="tab_clicked(10)">Feedback / Help</li></a>
                        <a href="#faq"><li id="MooPanel_tab11" onclick="tab_clicked(11)">FAQ</li></a>
                        <a href="https://www.youtube.com/channel/UCvG2UY0xjcLVTOccDqaGBow" target="_blank"><li>Video Tutorials <i class="fas fa-external-link-square-alt"></i></li></a>
                        <a href="https://docs.smartonlineorder.com" target="_blank"><li>Helpful Articles <i class="fas fa-external-link-square-alt"></i></li></a>

                    </ul>
                </div>
                <?php
                if( $errorToken != "( Token valid )" ) { ?>
                    <form method="post" action="options.php" onsubmit="mooSaveChanges(event,this)">
                        <?php
                        settings_fields('moo_settings');
                        $fields = array('api_key');
                        foreach ($MooOptions as $option_name=>$option_value)
                            if(!in_array($option_name,$fields))
                                if($option_name=="custom_js" || $option_name =="custom_css" || $option_name == "copyrights"|| $option_name == "zones_json" || $option_name == "custom_sa_content" || $option_name == "closing_msg")
                                    echo '<textarea name="moo_settings['.$option_name.']" id="" cols="10" rows="10" style="display:none">'.$option_value.'</textarea>';
                                else
                                    echo '<input type="text"  name="moo_settings['.$option_name.']" value="'.$option_value.'" hidden/>';
                        ?>
                        <div id="MooPanel_tabContent1">
                            <h2>Key settings</h2>
                            <hr>
                            <div class="MooPanelItem">
                                <h3>API key</h3>
                                <div class="Moo_option-item">
                                    <p>
                                        The API Key or Activation license is a secret and unique key used to link your website with your Clover device. You can get the Api Key by going to Clover.com from a computer, then login. Then select more tools, and install Smart Online Order. Please watch <a href="https://www.youtube.com/watch?v=GGGm22D-f0M" target="_blank">this video</a> to learn how to install the app or search “Smart Online Order” on Youtube. You can also visit smartonlineorder.com to learn more.
                                        If you already have installed Smart Online Order enter the Api Key here:
                                    </p>
                                </div>
                                <div class="Moo_option-item">
                                    <div class="label">Your key : </div>
                                    <input type="text" size="60" name="moo_settings[api_key]" value="<?php echo $MooOptions['api_key']?>"/>
                                    <?php echo $errorToken;?>
                                </div>
                                <div style="padding: 20px">
                                    <a href="http://api.smartonlineorders.com/oauth" target="_blank">You can also get your Api Key from this link</a>
                                </div>
                                <div style="text-align: center; margin-bottom: 20px;">
                                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                                </div>

                            </div>
                        </div>
                    </form>
                    <?php

                }
                else
                    if( $MooOptions['lng'] == "" || $MooOptions['lat'] == ""){
                        $this->moo_update_address();
                    } else {

                        if(isset($_GET['moo_section']) && $_GET['moo_section']=='update_address') {
                            $this->moo_update_address();
                        } else {
                            if(isset($_GET['moo_section']) && $_GET['moo_section']=='update_apikey') {
                                $this->moo_update_token();
                            } else {
                            ?>
                            <!-- My store -->
                            <div id="MooPanel_tabContent1">
                                <h2>My store</h2><hr>
                                <div class="MooRow">
                                    <div class="MooRowSection" style="text-align: center">
                                        <div>
                                            <img width="70px" src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/check.png";?>" alt=""/>
                                        </div>
                                        <p><?php _e("Your API KEY is valid","moo_OnlineOrders"); ?></p>
                                    </div>
                                </div>
                                <div class="MooRow">
                                    <div class="MooRowSection" style="text-align: center">
                                        <div>
                                            <img  width="70px" src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/address.png";?>" alt=""/>
                                        </div>
                                        <p>
                                            <a href="<?php echo (esc_url(add_query_arg('moo_section', 'update_address',(admin_url('admin.php?page=moo_index'))))); ?>">View your address</a>
                                        </p>
                                    </div>
                                </div>
                                <div class="MooRow">
                                    <div class="MooRowSection" style="text-align: center">
                                        <h3>Need help on what to do next ?</h3>
                                        <p>
                                            We have video tutorials. Simply go to youtube.com and search for Smart Online Order
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php
                            }
                        }
                    }
                        ?>
                <!-- Import Items -->
                <div id="MooPanel_tabContent2">
                    <h2>Import inventory (Scroll Down for more options)</h2><hr>
                    <div class="MooPanelItem">
                        <h3>Import your data</h3>
                        <p>You may need to refresh your browser after data is imported. Use manual sync below after you have made additional inventory changes</p>
                        <div class="Moo_option-item" style="text-align: center">
                            <div id="MooPanelSectionImport"></div>
                            <div id="MooPanelSectionImportItems"></div>
                            <div id="MooPanelButtonImport">
                                <a href="#" onclick="MooPanel_ImportItems(event)" class="button button-secondary"
                                   style="margin-bottom: 35px;" >Import inventory</a>
                            </div>

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
                                    <div class="type">Modifier Groups</div>
                                </div>
                                <div class="stat">
                                    <div class="value" id="MooPanelStats_Taxes">0</div>
                                    <div class="type">Tax rates</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="MooPanelItem">
                        <h3 style="font-size: 15px">Manual Sync "Use manual sync if changes have been made to your inventory and it hasn't synced"</h3>
                        <p>To perform a complete manual sync, Go in this order, Update Modifiers, Update Categories, Update all Items</p>
                        <div id="moo_progressbar_container"></div>
                        <div class="Moo_option-item">
                            <div class="button_center">
                                <a href="#" onclick="MooPanel_UpdateItems(event)" class="button button-secondary"
                                   style="margin-left: 30px;" >Update all Items</a>
                                <a href="#" onclick="MooPanel_UpdateCategories(event)" class="button button-secondary">Update Categories</a>
                                <a href="#" onclick="MooPanel_UpdateModifiers(event)" class="button button-secondary">Update Modifiers</a>
                                <a href="#" onclick="MooPanel_UpdateOrderTypes(event)" class="button button-secondary">Update Order Types</a>
                                <a href="#" onclick="MooPanel_UpdateTaxes(event)" class="button button-secondary">Update Taxes</a>
                            </div>

                        </div>
                    </div>
                    <div class="MooPanelItem">
                        <h3>Clean Inventory</h3>
                        <p>If you have deleted categories, items, modifier groups, modifiers, taxes, and order types from your Clover and they are still appearing on the website; Then use "Clean Inventory"</p>
                        <div id="moo_progressbar_container"></div>
                        <div class="Moo_option-item">
                            <div class="button_center">
                                <a href="#" onclick="MooPanel_CleanInventory(event)" class="button button-secondary"  style="margin: 0 auto">Clean Inventory</a>
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
                    <div class="orderTypesContainer">
                        <div id="MooOrderTypesContent"></div>
                    </div>
                    <h2>
                        Add new order type <hr>
                    </h2>
                    <div class="MooPanelItem">
                            <div class="Moo_option-item">
                                <div class="iwl_holder">
                                    <div class="iwl_label_holder">
                                        <label for="Moo_AddOT_label">Label or Name</label>
                                    </div>
                                    <div class="iwl_input_holder">
                                        <input type="text" value="" id="Moo_AddOT_label"/>
                                    </div>
                                </div>

                                <div class="iwl_holder">
                                    <div class="iwl_label_holder">
                                        <label for="Moo_AddOT_label">Minimum order amount</label>
                                    </div>
                                    <div class="iwl_input_holder">
                                        <input type="number" step="0.01" id="Moo_AddOT_minAmount"/>
                                    </div>
                                </div>
                            </div>
                            <div>
                             <div>
                                <div class="iwl_holder">
                                    <div class="">Delivery Order
                                        <input style="margin: 10px; margin-right: 2px; margin-left: 40px;" type="radio" name="delivery" value="oui" id="Moo_AddOT_delivery_oui" checked>
                                        <label for="Moo_AddOT_delivery_oui"> Yes</label>
                                        <input type="radio" name="delivery" value="non" id="Moo_AddOT_delivery_non" style="margin-left: 10px;" >
                                        <label for="Moo_AddOT_delivery_non">No</label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="iwl_holder">
                                    <div class="">Taxable
                                        <input style="margin: 10px; margin-right: 2px; margin-left: 40px;" type="radio" name="taxable" value="oui" id="Moo_AddOT_taxable_oui" checked><label for="Moo_AddOT_taxable_oui"> Yes</label>
                                        <input type="radio" name="taxable" value="non" id="Moo_AddOT_taxable_non" style="margin-left: 10px;" > <label for="Moo_AddOT_taxable_non">No</label>
                                    </div>
                                </div>
                            </div>

                            <div class="button_center">
                                <div title="This will add the order type to clover account" class="button button-primary"  onclick="moo_addordertype(event)" id="Moo_AddOT_btn">Add</div><div id="Moo_AddOT_loading"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Store interface (removed) -->
                <div id="MooPanel_tabContent4"></div>
                <!-- Image categorie -->
                <div id="MooPanel_tabContent5">
                    <span class="moo_collaps_all" onclick="Moo_SetupReorderCategoriesSection(event)">[ Reorder Categories ]</span>

                    <h2>Categories</h2><hr>
                    <!--
                    <div class="moo-pull-right" id="moo-btn-reordercategories"><a class="button button-secondary" href="#" onclick="Moo_SetupReorderCategoriesSection(event)">Re-Order Categories</a></div>
                    <div class="moo-pull-right" id="moo-btn-backtocategories"><a class="button button-secondary" href="#" onclick="Moo_SetupCategoriesSection(event)">Back To Categories</a></div>
                    <p>You can rearrange categories by dragging and dropping. To view items, press the "+" sign</p>
                    -->
                    <div class="moo-categories-section">

                    </div>
                    <div class="moo-categories-section moo-categories-edit-section" id="moo-categories-edit-section">

                    </div>
                    <div id="display_panel5_on_desktop">
                            <?php
                            if(count($all_categories) == 0){
                                echo '<div class="normal_text" >It appears you don\'t have any categories, If you have just imported your inventory, <br/> please refresh this page </div>';
                            }
                            ?>
                    </div>
                </div>
                <!-- Modifiers -->
                <div id="MooPanel_tabContent6">
                    <h2>Modifier Groups (scroll down for more options)</h2>
                    <hr>
                    <?php
                    if(count($modifier_groups)==0) {
                        echo "<div class=\"normal_text\">It appears you don't have any Modifier Group, please import your data by clicking on <b>Import / Sync inventory from sidebar then import inventory</b></div>";
                    }  else {
                    ?>
                    <div class="MooPanelItem">
                        <h3>Hide or change modifier group names so they are easy to understand. To view the modifiers press the "+" sign (for all store interfaces)</h3>
                        <p>You can rearrange Modifier groups and Modifiers by dragging and dropping</p>
                        <div class="moo_ModifierGroupsFilter">
                            <label class="modifierFilterLabel"  for="modifierFilter">Search By Name</label>
                            <input class="modifierFilter" type="text" name="" id="modifierFilter" onkeyup="mooFilterModifiers(event)">
                        </div>
                        <ul class="moo_ModifierGroup">
                            <?php
                            $i=0;
                            $j=0;
                            foreach ($modifier_groups as $mg) { ?>
                                <li class="list-group" group-id="<?php echo $mg->uuid?>">
                               <span class="show-detail-group">
                                   <?php
                                   $modifiers = $model->getAllModifiers($mg->uuid);
                                   $Nb_MG = count($modifiers);
                                   if($Nb_MG != 0){
                                       ?>
                                       <a href="#" onclick="show_sub(event,'<?php echo $mg->uuid ?>')">
                                      <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/add.png" ?>" id="plus_<?php echo $mg->uuid ?>" style="width: 20px;">
                                    </a>
                                   <?php } ?>
                                </span>
                                    <div class="label_name" id="label_<?php echo $mg->uuid?>">
                                        <label class="getname"><?php if ($mg->alternate_name == null) {$name = $mg->name;} else {$name = $mg->alternate_name;} echo $name;?></label>
                                        <span class="change-name" style="display: none;">
                                        <input type="text" value="<?php echo $name;?>" class="nameGGroup" id="newName_<?php echo $mg->uuid?>">
                                        <a href="#" onclick="validerChangeNameGG(event,'<?php echo $mg->uuid?>')"> <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/valider.png" ?>" style="width: 18px;"></a>
                                        <a href="#" onclick="annulerChangeNameGG(event,'<?php echo $mg->uuid?>','<?php echo $name?>')"> <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/annuler.png" ?>" style="width: 18px;"></a>
                                    </span>
                                    </div>
                                    <div class="moo-onoffswitch show_group" onchange="MooChangeModifier_Status('<?php echo $mg->uuid?>')" title="Show/Hide this Modifier Group">
                                        <input type="checkbox" name="onoffswitch[]" class="moo-onoffswitch-checkbox" id="myonoffswitch_<?php echo $mg->uuid?>" <?php echo ($mg->show_by_default)?'checked':''?>>
                                        <label class="moo-onoffswitch-label" for="myonoffswitch_<?php echo $mg->uuid?>"><span class="moo-onoffswitch-inner"></span>
                                            <span class="moo-onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                    <div class="saved_new_name">
                                        <a href="#" class="bt-eidt-GGroup" onclick="edit_name_GGroup(event,'<?php echo $mg->uuid ?>')">
                                        <span id="moo_edit_nameGG<?php echo $i; ?>"
                                              data-ot="Edit the modifier group name"
                                              data-ot-target="#moo_edit_nameGG<?php echo $i; ?>">
                                            <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/edit.png" ?>" style="width: 24px;">
                                        </span>
                                        </a>
                                    </div>
                                    <ul id="detail_group_<?php echo $mg->uuid ?>" class="sub-group" GM="<?php echo $mg->uuid?>">
                                        <?php foreach ($modifiers as $value){?>
                                            <li class="list-GModifier_<?php echo $mg->uuid?>" group-id="<?php echo $value->uuid?>">
                                            <span class="moo_modifier_name" id="label_<?php echo $value->uuid?>">
                                                <label class="getname"><?php if ($value->alternate_name == null) {$name = $value->name;} else {$name = $value->alternate_name;} echo $name;?></label>
                                                <span class="change-name-modifier" style="display: none;">
                                                    <input type="text" value="<?php echo $name;?>" class="nameGGroup" id="newName_<?php echo $value->uuid?>">
                                                    <a href="#" onclick="validerChangeNameModifier(event,'<?php echo $value->uuid?>')"> <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/valider.png" ?>" style="width: 18px;"></a>
                                                    <a href="#" onclick="annulerChangeNameModifier(event,'<?php echo $value->uuid?>')"> <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/annuler.png" ?>" style="width: 18px;"></a>
                                                </span>
                                            </span>
                                                <div class="moo-onoffswitch show_group" onchange="MooChangeM_Status('<?php echo $value->uuid?>')" title="Show/Hide this Modifier">
                                                    <input type="checkbox" name="onoffswitch[]" class="moo-onoffswitch-checkbox" id="myonoffswitch_<?php echo $value->uuid?>" <?php echo ($value->show_by_default)?'checked':''?>>
                                                    <label class="moo-onoffswitch-label" for="myonoffswitch_<?php echo $value->uuid?>"><span class="moo-onoffswitch-inner"></span>
                                                        <span class="moo-onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                                <div class="edit_modifer_name">
                                                    <a href="#" class="bt-eidt-GGroup" onclick="edit_name_GModifer(event,'<?php echo $value->uuid ?>')">
                                                <span id="moo_edit_nameGM<?php echo $j; ?>"
                                                      data-ot="Edit the modifier name"
                                                      data-ot-target="#moo_edit_nameGM<?php echo $j; ?>">
                                                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/edit.png" ?>" style="width: 24px;">
                                                </span>
                                                    </a>
                                                </div>
                                                <span class="bar-group">
                                                <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/menu.png" ?>" style="width: 18px;">
                                            </span>
                                            </li>
                                            <?php $j++; } ?>
                                    </ul>
                                </li>
                                <?php $i++; }?>
                        </ul>

                    </div>
                    <div class="MooPanelItem">
                        <form method="post" action="options.php" onsubmit="mooSaveChanges(event,this)">
                            <?php
                            $MooOptions = (array)get_option('moo_settings');
                            settings_fields('moo_settings');
                            $fields = array(
                                'mg_settings_displayInline',
                                'mg_settings_qty_for_all',
                                'mg_settings_qty_for_zeroPrice',
                            );

                            foreach ($MooOptions as $option_name=>$option_value)
                                if(!in_array($option_name,$fields))
                                    if($option_name=="custom_js" || $option_name =="custom_css" || $option_name == "copyrights"|| $option_name == "zones_json"|| $option_name == "custom_sa_content" || $option_name == "closing_msg")
                                        echo '<textarea name="moo_settings['.$option_name.']" id="" cols="10" rows="10" style="display:none">'.$option_value.'</textarea>';
                                    else
                                        echo '<input type="text"  name="moo_settings['.$option_name.']" value="'.$option_value.'" hidden/>';
                            ?>
                            <h3>Modifier settings</h3>
                            <div class="Moo_option-item">
                                <div class="normal_text">
                                    Display Options for modifier selection
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div style="float:left; width: 100%;padding-left: 60px">
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[mg_settings_displayInline]" id="mg_settings_displayInline" type="radio" value="disabled" <?php echo ($MooOptions["mg_settings_displayInline"]=="disabled")?"checked":""; ?>>
                                        Pop-Up window
                                    </label>
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[mg_settings_displayInline]" id="mg_settings_displayInline" type="radio" value="enabled" <?php echo ($MooOptions["mg_settings_displayInline"]=="enabled")?"checked":""; ?> >
                                        Underneath item name
                                    </label>
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div class="normal_text">
                                    Allow customers to choose modifier quantity for all modifiers.
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div style="float:left; width: 100%;padding-left: 60px">
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[mg_settings_qty_for_all]" id="mg_settings_qty_for_all" type="radio" value="disabled" <?php echo ($MooOptions["mg_settings_qty_for_all"]!="enabled")?"checked":""; ?>>
                                        No
                                    </label>
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[mg_settings_qty_for_all]" id="mg_settings_qty_for_all" type="radio" value="enabled" <?php echo ($MooOptions["mg_settings_qty_for_all"]=="enabled")?"checked":""; ?>>
                                        Yes
                                    </label>
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div class="normal_text">
                                    Allow customers to choose modifier quantity when modifier is free or $0.00.
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div style="float:left; width: 100%;padding-left: 60px">
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[mg_settings_qty_for_zeroPrice]" id="mg_settings_qty_for_all" type="radio" value="disabled" <?php echo ($MooOptions["mg_settings_qty_for_zeroPrice"]!="enabled")?"checked":""; ?>>
                                        No
                                    </label>
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[mg_settings_qty_for_zeroPrice]" id="mg_settings_qty_for_all" type="radio" value="enabled" <?php echo ($MooOptions["mg_settings_qty_for_zeroPrice"]=="enabled")?"checked":""; ?>>
                                        Yes
                                    </label>
                                </div>
                            </div>
                            <!-- Save Changes button -->
                            <div style="text-align: center; margin: 20px;">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                            </div>
                        </form>
                    </div>
                    <?php } ?>
                </div>
                <!-- Checkout settings -->
                <div id="MooPanel_tabContent7">
                    <span class="moo_collaps_all" onclick="expandAllSections(this)">[ Collapse All ]</span>
                    <h2>Checkout Settings</h2>

                    <hr>
                    <form method="post" action="options.php" onsubmit="mooSaveChanges(event,this)">
                        <?php
                        $MooOptions = (array)get_option('moo_settings');
                        settings_fields('moo_settings');
                        $fields = array(
                            'payment_creditcard',
                            'payment_cash',
                            'payment_cash_delivery',
                            'scp',
                            'thanks_page',
                            'fb_appid',
                            'use_coupons',
                            'use_couponsApp',
                            'use_sms_verification',
                            'use_special_instructions',
                            'checkout_login',
                            'save_cards',
                            'save_cards_fees',
                            'service_fees',
                            'service_fees_name',
                            'service_fees_type',
                            'tips');

                        foreach ($MooOptions as $option_name=>$option_value)
                            if(!in_array($option_name,$fields)){
                                if($option_name=="custom_js" || $option_name =="custom_css" || $option_name == "copyrights" || $option_name == "zones_json" || $option_name == "custom_sa_content" || $option_name == "closing_msg") {
                                    echo '<textarea name="moo_settings['.$option_name.']" id="" cols="10" rows="10" style="display:none">'.$option_value.'</textarea>';
                                } else {
                                    echo '<input type="text"  name="moo_settings['.$option_name.']" value="'.$option_value.'" hidden/>';
                                }
                            }


                        ?>
                        <!-- Checkout login Section -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Login to checkout</h3>
                            <div class="Moo_option-item">
                                <div class="normal_text">
                                    Allow customers to create an account when making a purchase. This will prompt each customer to sign up for an account during checkout.
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div style="float:left; width: 100%;padding-left: 60px">
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[checkout_login]" id="Moocheckout_login" type="radio" value="disabled" <?php echo ($MooOptions["checkout_login"]!="enabled")?"checked":""; ?> onclick="moo_login2checkoutClicked(false)">
                                        Disabled
                                    </label>
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[checkout_login]" id="Moocheckout_login" type="radio" value="enabled" <?php echo ($MooOptions["checkout_login"]=="enabled")?"checked":""; ?> onclick="moo_login2checkoutClicked(true)">
                                        Enabled
                                    </label>
                                </div>
                            </div>
                            <div class="moo_login2checkout" style="display:<?php echo ($MooOptions["checkout_login"]=="enabled")?"block":"none"; ?>">
                                <div class="Moo_option-item " >
                                    <div class="normal_text">
                                        <h4> Facebook login during checkout</h4>
                                        To add Facebook login during checkout, please create an app then enter the app id here (for example 244779189290302). For more information please visit: https://developers.facebook.com/docs/apps/register
                                    </div>
                                </div>
                                <div class="Moo_option-item">
                                    <div class="iwl_holder"><div class="iwl_label_holder"><label id="MooFbAppID" >Your APP ID</label></div>
                                        <div class="iwl_input_holder"><input name="moo_settings[fb_appid]" id="MooFbAppID" type="text" value="<?php echo $MooOptions['fb_appid']?>" /></div>
                                    </div>
                                </div>
                                <div class="Moo_option-item" style="display: none">
                                    <div class="normal_text">
                                        <h3> Save customers credit cards (Soon)</h3>
                                        Allow customers to save their credit cards, so in next time they will not neet to enter the card information again
                                    </div>
                                </div>
                                <div class="Moo_option-item">
                                    <div style="float:left; width: 100%;;padding-left: 60px; display: none">
                                        <label style="display:block; margin-bottom:8px;">
                                            <input name="moo_settings[save_cards]" id="Moocheckout_saveCreditCrads" type="radio" value="disabled" <?php echo ($MooOptions["save_cards"]!="enabled")?"checked":""; ?> onclick="moo_saveCardsClicked(false)">
                                            Disabled
                                        </label>
                                        <label style="display:block; margin-bottom:8px;">
                                            <input name="moo_settings[save_cards]" id="Moocheckout_saveCreditCrads" type="radio" value="enabled" <?php echo ($MooOptions["save_cards"]=="enabled")?"checked":""; ?> onclick="moo_saveCardsClicked(true)">
                                            Enabled
                                        </label>
                                    </div>
                                </div>
                                <div class="moo_saveCardsClicked" style="display:<?php echo ($MooOptions["save_cards"]=="enabled")?"block":"none"; ?>">
                                    <div class="Moo_option-item" >
                                        <div class="normal_text">
                                            <h3> Saving customers credit cards fees</h3>
                                            To save credit cards on a safe place, we are using a company specialazed on saving credit cards (Spreedly) and PCI commplinace, to covert their charges we will charge $0.5 for each tranascation made using a saved credit crard. But you have choice to pay it by yourself or charge it to you customers :
                                        </div>
                                    </div>
                                    <div class="Moo_option-item">
                                        <div style="float:left; width: 100%;;padding-left: 60px;">
                                            <label style="display:block; margin-bottom:8px;">
                                                <input name="moo_settings[save_cards_fees]" id="Moocheckout_saveCreditCradsFees" type="radio" value="disabled" <?php echo ($MooOptions["save_cards_fees"]!="enabled")?"checked":""; ?> >
                                                I will pay the fees
                                            </label>
                                            <label style="display:block; margin-bottom:8px;">
                                                <input name="moo_settings[save_cards_fees]" id="Moocheckout_saveCreditCradsFees" type="radio" value="enabled" <?php echo ($MooOptions["save_cards_fees"]=="enabled")?"checked":""; ?> >
                                                Charge the customer
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!-- Additional payment options section -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Payment options</h3>
                            <div class="Moo_option-item">
                                <div class="normal_text">
                                    You must enable at least one payment option. You can choose  Pay Online, Pay at location, Pay Upon Delivery, or all three. Hint : Don't forget to press "save changes"
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div style="margin-bottom: 14px;" class="label">Pay Online With Credit Card</div>
                                <div class="moo-onoffswitch"  title="Pay Online">
                                    <input type="hidden" name="moo_settings[payment_creditcard]" value="off">
                                    <input type="checkbox" name="moo_settings[payment_creditcard]" class="moo-onoffswitch-checkbox" id="myonoffswitch_payment_creditcard" <?php echo (isset($MooOptions['payment_creditcard']) && $MooOptions['payment_creditcard'] == 'on')?'checked':''?>>
                                    <label class="moo-onoffswitch-label" for="myonoffswitch_payment_creditcard"><span class="moo-onoffswitch-inner"></span>
                                        <span class="moo-onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <span id="moo_info_msg-0" class="moo-info-msg"
                                      data-ot="Allow customer to pay in advance with credit card"
                                      data-ot-target="#moo_info_msg-0">
                                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                </span>
                            </div>
                            <div class="Moo_option-item">
                                <div style="margin-bottom: 14px;" class="label">Pay at location</div>
                                <div class="moo-onoffswitch"  title="Pay at location">
                                    <input type="hidden" name="moo_settings[payment_cash]" value="off">
                                    <input type="checkbox" name="moo_settings[payment_cash]" class="moo-onoffswitch-checkbox" id="myonoffswitch_payment_cash" <?php echo (isset($MooOptions['payment_cash']) && $MooOptions['payment_cash'] == 'on')?'checked':''?>>
                                    <label class="moo-onoffswitch-label" for="myonoffswitch_payment_cash"><span class="moo-onoffswitch-inner"></span>
                                        <span class="moo-onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <span id="moo_info_msg-1" class="moo-info-msg"
                                      data-ot="Allow customer to order online and then pay at store"
                                      data-ot-target="#moo_info_msg-1">
                                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                </span>
                            </div>
                            <div class="Moo_option-item">
                                <div style="margin-bottom: 14px;" class="label">Pay upon delivery</div>
                                <div class="moo-onoffswitch"  title="Pay upon delivery">
                                    <input type="hidden" name="moo_settings[payment_cash_delivery]" value="off">
                                    <input type="checkbox" name="moo_settings[payment_cash_delivery]" class="moo-onoffswitch-checkbox" id="myonoffswitch_payment_cash_delivery" <?php echo (isset($MooOptions['payment_cash_delivery']) && $MooOptions['payment_cash_delivery'] == 'on')?'checked':''?>>
                                    <label class="moo-onoffswitch-label" for="myonoffswitch_payment_cash_delivery"><span class="moo-onoffswitch-inner"></span>
                                        <span class="moo-onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <span id="moo_info_msg-1" class="moo-info-msg"
                                      data-ot="Allow customer to order online and then pay upon delivery, If you are not offering delivery Orders then this setting won't affect you"
                                      data-ot-target="#moo_info_msg-1">
                                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                </span>
                            </div>
                            <div class="Moo_option-item">
                                <div style="margin-bottom: 14px;" class="label">Use Smart Online Order Checkout page</div>
                                <div class="moo-onoffswitch"  title="Secure checkout page">
                                    <input type="hidden" name="moo_settings[scp]" value="off">
                                    <input type="checkbox" name="moo_settings[scp]" class="moo-onoffswitch-checkbox" id="myonoffswitch_scp" <?php echo (isset($MooOptions['scp']) && $MooOptions['scp'] == 'on')?'checked':''?>>
                                    <label class="moo-onoffswitch-label" for="myonoffswitch_scp"><span class="moo-onoffswitch-inner"></span>
                                        <span class="moo-onoffswitch-switch"></span>
                                    </label>
                                </div>
                                <span id="moo_info_msg-2" class="moo-info-msg"
                                      data-ot="If you don't have SSL installed on your website, you can use our checkout page"
                                      data-ot-target="#moo_info_msg-2">
                                    <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                </span>
                            </div>
                        </div>
                        <!-- Coupon section -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Coupons</h3>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    To add coupon codes, select Clover orders then coupons. You can also download "Perfect Coupons" from the Clover App Market to use as Coupon Codes
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div style="float:left; width: 100%;padding-left: 60px;">
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[use_coupons]" id="Moouse_coupons" type="radio" value="disabled" <?php echo ($MooOptions["use_coupons"]!="enabled")?"checked":""; ?> onclick="moo_couponsStatusClicked(false)">
                                        Disabled
                                    </label>
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[use_coupons]" id="Moouse_coupons" type="radio" value="enabled" <?php echo ($MooOptions["use_coupons"]=="enabled")?"checked":""; ?> onclick="moo_couponsStatusClicked(true)">
                                        Enabled
                                    </label>
                                    <div class="Moo_option-item" id="moo_use_couponsapp" style="display:<?php echo ($MooOptions["use_coupons"]=="enabled")?"block":"none"; ?>">
                                        <div style="margin-bottom: 14px;" class="label">Accept coupons created via Perfect Coupons app by Zaytech</div>
                                        <div class="moo-onoffswitch"  title="Use Coupons app">
                                            <input type="hidden" name="moo_settings[use_couponsApp]" value="off">
                                            <input type="checkbox" name="moo_settings[use_couponsApp]" class="moo-onoffswitch-checkbox" id="myonoffswitch_use_couponsApp" <?php echo (isset($MooOptions['use_couponsApp']) && $MooOptions['use_couponsApp'] == 'on')?'checked':''?>>
                                            <label class="moo-onoffswitch-label" for="myonoffswitch_use_couponsApp"><span class="moo-onoffswitch-inner"></span>
                                                <span class="moo-onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                        <span id="moo_info_msg_coupons-0" class="moo-info-msg"
                                              data-ot="A coupon promotion can dramatically increase awareness of your Online Ordering. Go to the Clover App Market and install “Perfect Coupons” by Zaytech. It will allow you to print Coupons from your Clover POS and then have it redeemed "
                                              data-ot-target="#moo_info_msg_coupons-0">
                                            <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Service fees -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Service Fees</h3>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    You can set a service charge which will be applied to all orders. Service fees wil be added to subtotal
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div class="iwl_holder"><div class="iwl_label_holder"><label id="MooServiceFeesname" >Service charge name, Example: Service Fee, Convenience Charges, Catering FEE</label></div>
                                    <div class="iwl_input_holder"><input name="moo_settings[service_fees_name]" id="MooServiceFeesName" type="text" value="<?php echo $MooOptions['service_fees_name']?>" /></div>
                                </div>
                                <div class="iwl_holder"><div class="iwl_label_holder"><label id="MooServiceFees" >Fees to be charged, Examples: For amount, enter 5.00 then select "Amount". For percent, enter 5, then select "Percent"</label></div>
                                    <div class="iwl_input_holder"><input name="moo_settings[service_fees]" id="MooServiceFees" type="text" value="<?php echo $MooOptions['service_fees']?>" placeholder="0.00" /></div>
                                    Type :
                                    <label style="margin-right:8px;">
                                        <input name="moo_settings[service_fees_type]" id="MooServiceFeesType" type="radio" value="amount" <?php echo ($MooOptions["service_fees_type"]=="amount")?"checked":""; ?> >
                                        Amount
                                    </label>
                                    <label style="margin-right:8px;">
                                        <input name="moo_settings[service_fees_type]" id="MooServiceFeestype" type="radio" value="percent" <?php echo ($MooOptions["service_fees_type"]=="percent")?"checked":""; ?> >
                                        Percent
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- Tips section -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Tips</h3>
                            <?php if($merchant_proprites->tipsEnabled) { ?>
                                <div class="Moo_option-item">
                                    <div style="float:left; width: 100%;padding-left: 60px;">
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
                        <!-- SMS verification section -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Verify with SMS</h3>
                                <div class="Moo_option-item" >
                                    <div class="normal_text">
                                        Require customers to verify their phone number with text message when not paying with credit card in advance
                                    </div>
                                </div>
                                <div class="Moo_option-item">
                                    <div style="float:left; width: 100%;padding-left: 60px;">
                                        <label style="display:block; margin-bottom:8px;">
                                            <input name="moo_settings[use_sms_verification]" id="MooSMSVerification" type="radio" value="enabled" <?php echo ($MooOptions["use_sms_verification"]=="enabled")?"checked":""; ?> >
                                            Enabled
                                        </label>
                                        <label style="display:block; margin-bottom:8px;">
                                            <input name="moo_settings[use_sms_verification]" id="MooSMSVerification" type="radio" value="disabled" <?php echo ($MooOptions["use_sms_verification"]!="enabled")?"checked":""; ?> >
                                            Disabled
                                            <span id="moo_info_msg_MooSMSVerification" class="moo-info-msg"
                                                  data-ot="Not recommended as you may get orders where customers may not show up - By disabling you are increasing the risk of no-shows"
                                                  data-ot-target="#moo_info_msg_MooSMSVerification">
                                                <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                            </span>
                                        </label>
                                    </div>
                                </div>
                        </div>
                        <!-- Special Instruction -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Special instructions </h3>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    Allow customers to leave special instructions on the checkout page
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div style="float:left; width: 100%;padding-left: 60px;">
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[use_special_instructions]" id="MooUse_special_instructions" type="radio" value="disabled" <?php echo ($MooOptions["use_special_instructions"]!="enabled")?"checked":""; ?> onclick ='moo_click_on_textUnderSI(false)'>
                                        Disabled
                                    </label>
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[use_special_instructions]" id="MooUse_special_instructions" type="radio" value="enabled" <?php echo ($MooOptions["use_special_instructions"]=="enabled")?"checked":""; ?> onclick ='moo_click_on_textUnderSI(true)'>
                                        Enabled
                                    </label>
                                </div>
                                <div class="moo_textUnderSI" style="display:<?php echo ($MooOptions["use_special_instructions"]=="enabled")?"block":"none"; ?>">
                                    <div class="Moo_option-item " >
                                        <div class="normal_text">
                                            <h4> Text under Special instructions</h4>
                                            Custom text under Special Instructions
                                        </div>
                                    </div>
                                    <div class="Moo_option-item">
                                        <div class="iwl_holder"><div class="iwl_label_holder">
                                                <label id="MooTextUnderSI" >Your text</label>
                                            </div>
                                            <div class="iwl_input_holder"><input name="moo_settings[text_under_special_instructions]" id="MooTextUnderSI" type="text" value="<?php echo $MooOptions['text_under_special_instructions']?>" /></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- Thank you  page -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Thank you page</h3>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    To change the page that appears when the customer confirms his order. Please enter its URL here or leave it blank to display the default page.
                                    Recommended to leave blank. When entering URL, it must include https://
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div class="iwl_holder"><div class="iwl_label_holder"><label id="MooDefaultMerchantEmail" >Your Page</label></div>
                                    <div class="iwl_input_holder"><input name="moo_settings[thanks_page]" id="MooDefaultMerchantEmail" type="text" value="<?php echo $MooOptions['thanks_page']?>" placeholder="https://" /></div>
                                </div>
                            </div>
                        </div>
                        <!-- Save Changes button -->
                        <div style="text-align: center; margin: 20px;">
                            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                            <a href="<?php echo (esc_url((admin_url('admin.php?page=moo_index')))); ?>" class="button">Cancel</a>
                        </div>
                    </form>

                </div>
                <!-- Store Settings -->
                <div id="MooPanel_tabContent8">
                    <span class="moo_collaps_all" onclick="expandAllSections(this)">[ Collapse All ]</span>
                    <h2>Store Settings</h2>
                    <hr>
                    <form method="post" action="options.php" onsubmit="mooSaveChanges(event,this)">
                        <?php
                        $MooOptions = (array)get_option('moo_settings');
                        settings_fields('moo_settings');
                        $fields = array(
                            'accept_orders',
                            'merchant_email',
                            'merchant_phone',
                            'track_stock',
                            'item_delivery',
                            'hours',
                            'closing_msg',
                            'hide_menu',
                            'accept_orders_w_closed',
                            'order_later',
                            'order_later_minutes',
                            'order_later_days',
                            'order_later_minutes_delivery',
                            'order_later_days_delivery',
                            'order_later_asap_for_p',
                            'order_later_asap_for_d',
                            'custom_css',
                            'custom_js',
                            'custom_sa_title',
                            'custom_sa_content',
                            'custom_sa_onCheckoutPage',
                            'copyrights',
                            'store_page',
                            'checkout_page',
                            'cart_page',
                            'my_account_page');
                        foreach ($MooOptions as $option_name=>$option_value)
                            if(!in_array($option_name,$fields))
                            if($option_name == "zones_json")
                                echo '<textarea name="moo_settings['.$option_name.']" id="" cols="10" rows="10" style="display:none">'.$option_value.'</textarea>';
                            else
                                echo '<input type="text"  name="moo_settings['.$option_name.']" value="'.$option_value.'" hidden/>';

                        ?>
                        <textarea  name="moo_settings[zones_json]" hidden><?php echo $MooOptions['zones_json']?></textarea>
                        <!-- Accept Orders section -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Accept Online Orders</h3>
                            <div class="Moo_option-item">
                                <div class="normal_text">
                                    Use this to close the Order Online Page
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div style="float:left; width: 100%;padding-left: 60px;">
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[accept_orders]" id="MooAccept_orders" type="radio" value="enabled" <?php echo ($MooOptions["accept_orders"]!="disabled")?"checked":""; ?> >
                                        Open
                                    </label>
                                    <label style="display:block; margin-bottom:8px;">
                                        <input name="moo_settings[accept_orders]" id="MooAccept_orders" type="radio" value="disabled" <?php echo ($MooOptions["accept_orders"]=="disabled")?"checked":""; ?> >
                                        Closed
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- Notifications section -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Notification when an order is made</h3>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    We use this email to inform you when a new order has been made. If you want to use more than one Email please separate them with a comma. Example: tim@gmail.com,susan@msn.com,bob@yahoo.com
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div class="iwl_holder">
                                    <div class="iwl_label_holder"><label for="youremail">Your Email(s)</label></div>
                                    <div class="iwl_input_holder"><input id="youremail" name="moo_settings[merchant_email]" id="MooDefaultMerchantEmail" type="text" value="<?php echo $MooOptions['merchant_email']?>" /></div>
                                </div>
                            </div>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    We use this cell phone number to notify you via text message when a new order has been made. Enjoy free sms notifications for 60 days. Enter just one phone number. Do not use parenthesis. Example: 555-234-1212 or 5552341212
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div class="iwl_holder">
                                    <div class="iwl_label_holder">
                                        <label for="yourephone">Your Phone</label></div>
                                    <div class="iwl_input_holder"><input id="yourephone" name="moo_settings[merchant_phone]" id="MooDefaultMerchantPhone" type="text" value="<?php echo $MooOptions['merchant_phone']?>" /></div>
                                </div>
                            </div>
                        </div>
                        <!-- Track stock section -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Track stock</h3>
                            <div class="Moo_option-item">
                                <div class="normal_text">
                                    If an item is sold on the website it will deduct the quantity from the Clover Inventory. Once an item reaches 0 count it will say "Out Of Stock"
                                </div>
                            </div>
                                <div class="Moo_option-item">
                                    <div style="float:left; width: 100%;padding-left: 60px;">
                                        <label style="display:block; margin-bottom:8px;">
                                            <input name="moo_settings[track_stock]" id="Mootrack_stock" type="radio" value="enabled" <?php echo ($MooOptions["track_stock"]=="enabled")?"checked":""; ?> >
                                            Enabled
                                        </label>
                                        <label style="display:block; margin-bottom:8px;">
                                            <input name="moo_settings[track_stock]" id="Mootrack_stock" type="radio" value="disabled" <?php echo ($MooOptions["track_stock"]!="enabled")?"checked":""; ?> >
                                            Disabled
                                        </label>
                                    </div>
                                </div>
                        </div>
                        <!-- Business Hours -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Hours your store is available</h3>
                            <div class="Moo_option-item">
                                <div style="float:left; width: 100%;padding-left: 60px;">
                                    <label style="display:block; margin-bottom:8px;" onclick="moo_bussinessHours_Details(false)">
                                        <input name="moo_settings[hours]" id="MooDefaultHours" type="radio" value="all" <?php echo ($MooOptions["hours"]=="all")?"checked":""; ?> >
                                        All Hours
                                    </label>
                                    <label style="display:block; margin-bottom:8px;" onclick="moo_bussinessHours_Details(true)">
                                        <input name="moo_settings[hours]" id="MooDefaultHours" type="radio" value="business" <?php echo ($MooOptions["hours"]!="all")?"checked":""; ?> >
                                        Clover Business Hours
                                        <span id="moo_info_msg-3" class="moo-info-msg"
                                              data-ot="Please manage your business hours on clover"
                                              data-ot-target="#moo_info_msg-3">
                                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                    </span>
                                    </label>
                                    <div id="moo_bussinessHours_Details" class="<?php echo ($MooOptions["hours"] != "all")?"":"moo_hidden"; ?> ">
                                        <div class="Moo_option-item">
                                            <div style="margin-bottom: 14px;" class="label">Hide the menu when the store is closed</div>
                                            <div class="moo-onoffswitch"  title="Show/hide the menu">
                                                <input type="hidden" name="moo_settings[hide_menu]" value="off">
                                                <input type="checkbox" name="moo_settings[hide_menu]" class="moo-onoffswitch-checkbox" id="myonoffswitch_hide_menu" <?php echo (isset($MooOptions['hide_menu']) && $MooOptions['hide_menu'] == 'on')?'checked':''?>>
                                                <label class="moo-onoffswitch-label" for="myonoffswitch_hide_menu"><span class="moo-onoffswitch-inner"></span>
                                                    <span class="moo-onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Moo_option-item">
                                            <div style="margin-bottom: 14px;" class="label">Allow scheduled orders when the store is closed</div>
                                            <div class="moo-onoffswitch"  title="Show/hide the menu">
                                                <input type="hidden" name="moo_settings[accept_orders_w_closed]" value="off">
                                                <input type="checkbox" name="moo_settings[accept_orders_w_closed]" class="moo-onoffswitch-checkbox" id="myonoffswitch_accept_orders" <?php echo (isset($MooOptions['accept_orders_w_closed']) && $MooOptions['accept_orders_w_closed'] == 'on')?'checked':''?>>
                                                <label class="moo-onoffswitch-label" for="myonoffswitch_accept_orders"><span class="moo-onoffswitch-inner"></span>
                                                    <span class="moo-onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Moo_option-item">
                                                Your Clover Business Hours:
                                                <span id="moo_info_msg-4" class="moo-info-msg"
                                                      data-ot="<?php
                                                      if(count($current_hours)>0)
                                                          foreach ($current_hours as $key=>$value) {
                                                              echo '<strong><dt>'.$key.'</dt></strong>';
                                                              echo '<dd>'.$value.'</dd>';
                                                          };
                                                      ?>"
                                                      data-ot-target="#moo_info_msg-4">
                                                <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/info-icon.png" ?>" alt="">
                                                </span>
                                                <p>
                                                    To change your Clover Business Hours, go to <a href="https://www.clover.com" target="_blank">Clover.com</a> from a computer, then go to Setup, then Business information
                                                </p>
                                                <p>
                                                    <a href="#custom-hours" onclick="tab_clicked(12)">You can also use Custom Hours for different Categories and Order Types</a>
                                                </p>
                                        </div>
                                        <div class="Moo_option-item">
                                            <div style="font-size: 16px;font-weight: 700;">Customize the store closed message</div>
                                            <div class="Moo_option-item">
                                                <div class="normal_text">
                                                    The message that appears on the Order Online Page when the store is closed.
                                                    <p>
                                                        Use &lt;br&gt; after each sentence to keep it centered
                                                    </p>
                                                    <p>
                                                        Leave empty to use the default message
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="Moo_option-item">
                                                <textarea name="moo_settings[closing_msg]" id="" cols="8" rows="10" style="width: 80%"><?php echo (isset($MooOptions['closing_msg']))?$MooOptions['closing_msg']:"";?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- custom Store announcem -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Custom Store Annoncement</h3>
                            <div class="Moo_option-item">
                                <h4>Custom Pop Up Message when customers first views the Order Online Page or Checkout Page</h4>
                                <p>
                                    You can write a special message that will display as a pop-up once customers view the Order Online Page or Checkout Page.
                                </p>
                                <p>
                                    Leave blank for no pop-up message
                                </p>
                                <label for="MooCustom_sa_title">Title</label>
                                <div class="iwl_input_holder">
                                    <input name="moo_settings[custom_sa_title]" id="MooCustom_sa_title" type="text" value="<?php echo (isset($MooOptions['custom_sa_title']))?$MooOptions['custom_sa_title']:""; ?>" />
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                Content
                                <textarea name="moo_settings[custom_sa_content]" id="" cols="10" rows="10" style="width: 100%"><?php echo (isset($MooOptions['custom_sa_content']))?$MooOptions['custom_sa_content']:"";?></textarea>
                            </div>
                            <div class="Moo_option-item">
                                <div style="margin-bottom: 14px;" class="label">Show again on checkout page</div>
                                <div class="moo-onoffswitch"  title="Show/hide the custom store annoncement on checkout page" style="margin-top: 7px;">
                                    <input type="hidden" name="moo_settings[custom_sa_onCheckoutPage]" value="off">
                                    <input type="checkbox" name="moo_settings[custom_sa_onCheckoutPage]" class="moo-onoffswitch-checkbox" id="myonoffswitch_custom_sa_onCheckoutPage" <?php echo (isset($MooOptions['custom_sa_onCheckoutPage']) && $MooOptions['custom_sa_onCheckoutPage'] == 'on')?'checked':''?>>
                                    <label class="moo-onoffswitch-label" for="myonoffswitch_custom_sa_onCheckoutPage"><span class="moo-onoffswitch-inner"></span>
                                        <span class="moo-onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- scheduled orders -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Scheduled Orders</h3>
                            <div class="Moo_option-item">
                                <div style="margin-bottom: 14px;" class="label">Allow customer to schedule their orders </div>
                                <div class="moo-onoffswitch"  title="Show/hide order date">
                                    <input type="hidden" name="moo_settings[order_later]" value="off">
                                    <input onchange="MooChangeOrderLater_Status()" type="checkbox" name="moo_settings[order_later]" class="moo-onoffswitch-checkbox" id="myonoffswitch_order_later" <?php echo (isset($MooOptions['order_later']) && $MooOptions['order_later'] == 'on')?'checked':''?>>
                                    <label class="moo-onoffswitch-label" for="myonoffswitch_order_later"><span class="moo-onoffswitch-inner"></span>
                                        <span class="moo-onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="Moo_option-item ">
                                <div id="moo_orderLater_Details" class="<?php echo ($MooOptions["order_later"] == "on")?"":"moo_hidden"; ?> ">
                                    <div style="font-size: 16px;font-weight: 700;">Pick Up Orders</div>
                                    <div class="Moo_option-item">
                                        <div class="normal_text">
                                            Minimum time in minutes and Maximum days in the future customers can choose when ordering in advance for <b>pickup</b> orders. Default is 20 minutes and 4 days
                                        </div>
                                    </div>
                                    <div class="Moo_option-item">
                                        <div class="iwl_holder">
                                            <div class="iwl_label_holder"><label for="MooOrderLaterMinutesP">minutes in advance</label></div>
                                            <div class="iwl_input_holder">
                                                <input name="moo_settings[order_later_minutes]" id="MooOrderLaterMinutesP" type="text" value="<?php echo (isset($MooOptions['order_later_minutes']))?$MooOptions['order_later_minutes']:""; ?>" />
                                            </div>
                                        </div>
                                        <div class="iwl_holder">
                                            <div class="iwl_label_holder"><label for="MooOrderLaterDaysP">days in future</label></div>
                                            <div class="iwl_input_holder">
                                                <input name="moo_settings[order_later_days]" id="MooOrderLaterDaysP" type="text" value="<?php echo (isset($MooOptions['order_later_days']))?$MooOptions['order_later_days']:"" ?>" />
                                            </div>
                                        </div>
                                        <div class="iwl_holder">
                                            <div style="margin-bottom: 14px;" class="label">Allow customers to choose : ASAP</div>
                                            <div class="moo-onoffswitch"  title="Show/hide asap in pickup time" style="margin-top: 7px;">
                                                <input type="hidden" name="moo_settings[order_later_asap_for_p]" value="off">
                                                <input type="checkbox" name="moo_settings[order_later_asap_for_p]" class="moo-onoffswitch-checkbox" id="myonoffswitch_order_later_asap_for_p" <?php echo (isset($MooOptions['order_later_asap_for_p']) && $MooOptions['order_later_asap_for_p'] == 'on')?'checked':''?>>
                                                <label class="moo-onoffswitch-label" for="myonoffswitch_order_later_asap_for_p"><span class="moo-onoffswitch-inner"></span>
                                                    <span class="moo-onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="font-size: 16px;font-weight: 700;">Delivery Orders</div>
                                    <div class="Moo_option-item" >
                                        <div class="normal_text">
                                            Minimum time in minutes and Maximum days in the future customers can choose when ordering in advance for <b>delivery</b> orders. Default is 60 minutes and 4 days
                                        </div>
                                    </div>
                                    <div class="Moo_option-item">
                                        <div class="iwl_holder">
                                            <div class="iwl_label_holder"><label for="MooOrderLaterMinutesD">minutes in advance</label></div>
                                            <div class="iwl_input_holder">
                                                <input name="moo_settings[order_later_minutes_delivery]" id="MooOrderLaterMinutesD" type="text" value="<?php echo (isset($MooOptions['order_later_minutes_delivery']))?$MooOptions['order_later_minutes_delivery']:""; ?>" />
                                            </div>
                                        </div>
                                        <div class="iwl_holder">
                                            <div class="iwl_label_holder"><label for="MooOrderLaterDaysD">days in future</label></div>
                                            <div class="iwl_input_holder">
                                                <input name="moo_settings[order_later_days_delivery]" id="MooOrderLaterDaysD" type="text" value="<?php echo (isset($MooOptions['order_later_days_delivery']))?$MooOptions['order_later_days_delivery']:"" ?>" />
                                            </div>
                                        </div>
                                        <div class="iwl_holder">
                                            <div style="margin-bottom: 14px;" class="label">Allow customers to choose : ASAP</div>
                                            <div class="moo-onoffswitch"  title="Show/hide asap in delivery time" style="margin-top: 7px;">
                                                <input type="hidden" name="moo_settings[order_later_asap_for_d]" value="off">
                                                <input type="checkbox" name="moo_settings[order_later_asap_for_d]" class="moo-onoffswitch-checkbox" id="myonoffswitch_order_later_asap_for_d" <?php echo (isset($MooOptions['order_later_asap_for_d']) && $MooOptions['order_later_asap_for_d'] == 'on')?'checked':''?>>
                                                <label class="moo-onoffswitch-label" for="myonoffswitch_order_later_asap_for_d"><span class="moo-onoffswitch-inner"></span>
                                                    <span class="moo-onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!-- store  pages -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Store pages</h3>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    Please choose the store's pages.
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <div class="iwl_holder"><div class="iwl_label_holder"><label id="MooDefaultMerchantEmail" >Store Page</label></div>
                                    <div class="iwl_input_holder">
                                        <select name="moo_settings[store_page]" style="width: 100%;">
                                            <?php
                                            echo '<option></option>';
                                            foreach ( $all_pages as $page ) {
                                                $option = '<option value="' .$page->ID. '"';
                                                if($page->ID==$MooOptions['store_page'])
                                                    $option .= 'selected ';
                                                $option .= '>';
                                                $option .= $page->post_title;
                                                $option .= '</option>';
                                                echo $option;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="iwl_holder"><div class="iwl_label_holder"><label id="MooDefaultMerchantEmail" >Checkout Page</label></div>
                                    <div class="iwl_input_holder">
                                        <select name="moo_settings[checkout_page]" style="width: 100%;">
                                            <?php
                                            echo '<option></option>';
                                            foreach ( $all_pages as $page ) {
                                                $option = '<option value="' .$page->ID. '"';
                                                if($page->ID==$MooOptions['checkout_page'])
                                                    $option .= 'selected ';
                                                $option .= '>';
                                                $option .= $page->post_title;
                                                $option .= '</option>';

                                                echo $option;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="iwl_holder"><div class="iwl_label_holder"><label id="MooDefaultMerchantEmail" >Cart Page</label></div>
                                    <div class="iwl_input_holder">
                                        <select name="moo_settings[cart_page]" style="width: 100%;">
                                            <?php
                                            echo '<option></option>';
                                            foreach ( $all_pages as $page ) {
                                                $option = '<option value="' .$page->ID. '"';
                                                if($page->ID==$MooOptions['cart_page'])
                                                    $option .= 'selected ';
                                                $option .= '>';
                                                $option .= $page->post_title;
                                                $option .= '</option>';

                                                echo $option;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="iwl_holder"><div class="iwl_label_holder"><label id="MooDefaultMerchantEmail" >My Account Page</label></div>
                                    <div class="iwl_input_holder">
                                        <select name="moo_settings[my_account_page]" style="width: 100%;">
                                            <?php
                                            echo '<option></option>';
                                            foreach ( $all_pages as $page ) {
                                                $option = '<option value="' .$page->ID. '"';
                                                if($page->ID==$MooOptions['my_account_page'])
                                                    $option .= 'selected ';
                                                $option .= '>';
                                                $option .= $page->post_title;
                                                $option .= '</option>';

                                                echo $option;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Custom CSS -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Custom CSS</h3>
                            <div class="Moo_option-item">
                                <div class="normal_text">
                                    Visit <a href="https://docs.smartonlineorder.com">docs.smartonlineorder.com</a> for some sample code
                                </div>
                            </div>
                            <div class="Moo_option-item">
                                <textarea name="moo_settings[custom_css]" id="" cols="10" rows="10" style="width: 100%"><?php echo (isset($MooOptions['custom_css']))?$MooOptions['custom_css']:"";?></textarea>
                            </div>
                        </div>
                        <!-- custom JS -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Custom Javascript</h3>
                            <div class="Moo_option-item">
                                <textarea name="moo_settings[custom_js]" id="" cols="10" rows="10" style="width: 100%"><?php echo (isset($MooOptions['custom_js']))?$MooOptions['custom_js']:"";?></textarea>
                            </div>
                        </div>
                        <!-- Copyrights -->
                        <div class="MooPanelItem MooPanelItemExpanded">
                            <h3 onclick="expandSection(this)">Copyrights</h3>
                            <div class="Moo_option-item">
                                <textarea name="moo_settings[copyrights]" id="" cols="10" rows="5" style="width: 100%"><?php echo (isset($MooOptions['copyrights']))?$MooOptions['copyrights']:"";?></textarea>
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
                <div id="MooPanel_tabContent9">
                    <h2>Delivery areas</h2><hr>
                    <form method="post" action="options.php" onsubmit="moo_save_changes()">
                        <?php
                        settings_fields('moo_settings');
                        //This form fields
                        $fields = array(
                                'free_delivery',
                                'fixed_delivery',
                                'other_zones_delivery',
                                'delivery_fees_name',
                                'zones_json');
                        foreach ($MooOptions as $option_name=>$option_value)
                            if(!in_array($option_name,$fields))
                            {
                                if($option_name=="custom_js" || $option_name =="custom_css" || $option_name == "copyrights" || $option_name == "custom_sa_content" || $option_name == "closing_msg"
                                )
                                    echo '<textarea name="moo_settings['.$option_name.']" id="" cols="10" rows="10" style="display:none">'.$option_value.'</textarea>';
                                else
                                    echo '<input type="text"  name="moo_settings['.$option_name.']" value="'.$option_value.'" hidden/>';
                            }
                        ?>
                        <div class="MooPanelItem">
                            <h3>Set Delivery Areas (Click save changes when you create zones) <span class="moo_adding-zone-btn" onclick="moo_show_form_adding_zone()">Add zone</span></h3>
                            <div class="Moo_option-item" id='moo_adding-zone'>
                                <table class="delivery_area_for_mobile" style="margin: 0 auto; width: 55%; border-spacing: 10px;">
                                    <tr class="tr_for_mobile">
                                        <td class="td_for_mobile"><label for="moo_dz_name">Name*</label></td>
                                        <td class="td_for_mobile"><input style="float: right; width: 100%;" type="text" id="moo_dz_name"><br/></td>
                                    </tr>
                                    <tr id="moo_dz_type_line" class="tr_for_mobile">
                                        <td class="td_for_mobile"><label for="moo_dz_type">Zone Type*</label></td>
                                        <td class="td_for_mobile">
                                            <input onclick="mooZone_type_Clicked()" type="radio" id="moo_dz_typeC" name='moo_dz_type' checked>
                                            <label for="moo_dz_typeC">Circle</label>
                                            <input onclick="mooZone_type_Clicked()" type="radio" id="moo_dz_typeS" name='moo_dz_type' >
                                            <label for="moo_dz_typeS">Shape</label>
                                        </td  class="td_for_mobile">
                                    </tr>
                                    <tr class="tr_for_mobile">
                                        <td class="td_for_mobile"><label for="moo_dz_min">Delivery Radius</label></td>
                                        <td class="td_for_mobile"><input placeholder="0" style="float: right; width: 100%" type="text" id="moo_dz_radius">Miles<br/></td>
                                    </tr>
                                    <tr class="tr_for_mobile">
                                        <td class="td_for_mobile"><label for="moo_dz_min">Minimum order</label></td>
                                        <td class="td_for_mobile"><input placeholder="$0.00" style="float: right; width: 100%;" type="text" id="moo_dz_min"><br/></td>
                                    </tr>
                                    <tr  class="tr_for_mobile">
                                        <td class="td_for_mobile"><label   for="moo_dz_fee">Delivery fee</label></td>
                                        <td class="td_for_mobile"><input placeholder="0.00" style="float: right; width: 100%;" type="text" id="moo_dz_fee"><br/></td>
                                    </tr>
                                    <tr id="moo_dz_type_line" class="tr_for_mobile">
                                        <td class="td_for_mobile"><label for="moo_dz_fee_type">Type</label></td>
                                        <td class="td_for_mobile">
                                            <input type="radio" id="moo_dz_fee_type_value" name='moo_dz_fee_type' checked>
                                            <label for="moo_dz_fee_type_value">Dollar Value</label>
                                            <input type="radio" id="moo_dz_fee_type_percent" name='moo_dz_fee_type' >
                                            <label for="moo_dz_fee_type_percent">Percent of Subtotal</label>
                                        </td  class="td_for_mobile">
                                    </tr>
                                    <tr id="moo_dz_color_line" class="tr_for_mobile">
                                        <td class="td_for_mobile"><label for="moo_dz_color">Color</label></td>
                                        <td class="td_for_mobile"><input type="text" id="moo_dz_color" class="moo-color-field" value="#2788d8"></td>
                                    </tr>
                                    <tr id="moo_dz_action_for_adding" class="tr_for_mobile">
                                        <td  class="td_for_mobile" style="text-align: center;" colspan="2">
                                            <div style="margin-bottom: 10px;">
                                                <button type="button" class="button" onclick="moo_draw_zone()">Draw zone</button>
                                            </div>
<!--                                            <div style="margin-bottom: 10px;">-->
<!--                                                <button type="button" class="button button-primary" onclick="moo_validate_selected_zone()">Validate selected zone</button>-->
<!--                                            </div>-->
<!--                                            <div>-->
<!--                                                <button type="button" class="button" onclick="moo_deleteSelectedShape();">Delete selected zone</button>-->
<!--                                                <button type="button" class="button" onclick="moo_cancel_adding_form()">Cancel</button>-->
<!--                                            </div>-->
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
                            <div class="MooAddingZoneBtn">
                                <button type="button" class="button button-primary" onclick="moo_validate_selected_zone()">Validate selected zone</button>
                                <button type="button" class= button button-primary" onclick="moo_deleteSelectedShape();">Delete selected zone</button>
                                <button type="button" class= button button-primary" onclick="moo_cancel_adding_form()">Cancel</button>
                            </div>
                        </div>
                        <div class="MooPanelItem">
                            <h3>Other options</h3>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    <strong>Free Delivery</strong> : if customer spends over this dollar amount, then delivery fee is free, Keep empty if you don't want to offer free delivery (you should draw your delivery zones)
                                </div>
                                <div class="iwl_holder">
                                    <div class="iwl_label_holder"><label for="minamount">Min Amount</label></div>
                                    <div class="iwl_input_holder">
                                        <input  name="moo_settings[free_delivery]" type="text" value="<?php echo (isset($MooOptions['free_delivery']))?$MooOptions['free_delivery']:""; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    <strong>Fixed Delivery amount</strong> :  This fee will be applied towards any delivered order (order types with shipping address must be enabled) Keep empty if you don"t want to charge a fixed delivery fee.<b style="color: red">This will override any delivery fees you added when drawing the map. </b> Recommended to leave blank
                                </div>
                                <div class="iwl_holder">
                                    <div class="iwl_label_holder"><label for="fixeddeliveryamount">Fixed Delivery amount</label></div>
                                    <div class="iwl_input_holder">
                                        <input  id="fixeddeliveryamount"  name="moo_settings[fixed_delivery]" type="text" value="<?php echo (isset($MooOptions['fixed_delivery']))?$MooOptions['fixed_delivery']:"";?>" />
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
                                        <input  id="otherzonesdeliveryfees" name="moo_settings[other_zones_delivery]" type="text" value="<?php echo (isset($MooOptions['other_zones_delivery']))?$MooOptions['other_zones_delivery']:"";?>"  /></div>
                                </div>
                            </div>
                            <div class="Moo_option-item" >
                                <div class="normal_text">
                                    <strong>Delivery fee name</strong> :  The name of the delivery charge to appear on the receipt
                                </div>
                                <div class="iwl_holder">
                                    <div class="iwl_label_holder"><label for="merchantLocation">name</label></div>
                                    <div class="iwl_input_holder">
                                        <input  id="delivery_fees_name" name="moo_settings[delivery_fees_name]" type="text" value="<?php echo (isset($MooOptions['delivery_fees_name']))?$MooOptions['delivery_fees_name']:"";?>"  /></div>
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
                <div id="MooPanel_tabContent10">
                    <h2>Feedback / Help </h2><hr>
                    <div class="MooPanelItem">
                        <h3>Need Help or Feedback</h3>
                        <div class="normal_text">
                            Do you need help or would like to give us feedback.<br/>Please e-mail or call us: 925-234-5554 (8am-6pm pacific time).<br/>
                            You can also visit our support site at <a href="https://docs.smartonlineorder.com/" target="_blank">https://docs.smartonlineorder.com</a>
                        </div>
                        <div class="Moo_option-item">
                            <div class="iwl_holder">
                                <div class="iwl_label_holder">
                                    <label for="MoofeedBackEmail">Your Email</label>
                                </div>
                                <div class="iwl_input_holder">
                                    <input type="text" name="MoofeedbackEmail" id="MoofeedbackEmail"
                                           style="width: 100%;" value="<?php $emails = explode(",",$MooOptions['merchant_email']);echo $emails[0];?>" />
                                </div>

                                <div class="iwl_label_holder">
                                    <label for="MoofeedBackFullName">Full Name</label>
                                </div>
                                <div class="iwl_input_holder">
                                    <input type="text" name="MoofeedBackFullName" id="MoofeedBackFullName"
                                           style="width: 100%;" value="" />
                                </div>

                                <div class="iwl_label_holder">
                                    <label for="MoofeedBackBusinessName">Business Name</label>
                                </div>
                                <div class="iwl_input_holder">
                                    <input type="text" name="MoofeedBackBusinessName" id="MoofeedBackBusinessName"
                                           style="width: 100%;" value="" />
                                </div>

                                <div class="iwl_label_holder">
                                    <label for="MoofeedBackWebsiteName">Website Name</label>
                                </div>
                                <div class="iwl_input_holder">
                                    <input type="text" name="MoofeedBackWebsiteName" id="MoofeedBackWebsiteName"
                                           style="width: 100%;" value="" />
                                </div>

                                <div class="iwl_label_holder">
                                    <label for="MoofeedBackPhone">Phone Number</label>
                                </div>
                                <div class="iwl_input_holder">
                                    <input type="text" name="MoofeedBackPhone" id="MoofeedBackPhone"
                                           style="width: 100%;" value="" />
                                </div>

                                <div  style="margin-bottom: 3px;">
                                    <label for="Moofeedback">Your Message *</label>
                                </div>
                                <div class="iwl_label_holder">
                                    <textarea placeholder="Your Feedback or Help..." name="MooFeedBack" id="Moofeedback" cols="10" rows="10"></textarea>
                                </div>
                            </div>
                            <div class="button_center">
                                <a class="button button-primary" href="#" id="MooSendFeedBackBtn" onclick="MooSendFeedBack(event)">Send</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FAQ -->
                <div id="MooPanel_tabContent11">
                    <h2>Frequently asked questions </h2><hr>
                    <div class="MooPanelItem">
                        <div class="Moo_option-item">
                            <div>
                                <div class="faq_question">
                                    Once an order is placed, does it print to the clover POS?
                                </div>
                                <div class="faq_response">
                                    By default, it prints to the clover POS. If it doesn't print open the Online ordering app for Wordpress on the Clover POS then adjust the settings to select a default printer
                                </div>
                                <div class="faq_question">
                                    We have several locations, how do I put the menu for each location on the same website?
                                </div>
                                <div class="faq_response">
                                    You will need to create a subdomain on your website. For example, for a website named www.texasfoods.com
                                    You would create as many subdomains needed for each location. Then install Wordpress into each subdomain. For example:<br/>
                                    location1.texasfoods.com<br/>
                                    location2.texasfoods.com<br/>
                                    Then place a link on the website for each locations menu
                                </div>
                                <div class="faq_question">
                                    I'm having trouble installing the plugin or have other questions, can you help?
                                </div>
                                <div class="faq_response">
                                    Yes, we can, please email or call us:<br/>
                                    support@zaytechapps.com<br/>
                                    925-234-5554
                                </div>
                                <div class="faq_question">
                                    When I get an Online Order, it is not printing the receipt?
                                </div>
                                <div class="faq_response">
                                    Please search YouTube, Smart Online Order Printer Setup, or read the following: In the
                                    Smart Online Order Clover app: Select Printer Settings, Clover Devices, Check the Box, Choose
                                    this device as the default printer” Then General, make sure Auto Print Order and Payment
                                    receipts is “ON” Then check the box for “Print Customer Receipt and Print Order Receipt. Under
                                    Printers, Turn “Off” Use auto print settings, then manually select your Order Receipt Printer and
                                    Payment Receipt Printer. If you have more than one Clover Device, then on all of the other
                                    Clover Devices, uncheck the box, “Choose this device as the default printer” It is recommended
                                    to only check the box “Choose this device as the default printer on only one device. Please
                                    search Youtube, “Smart Online Order Printer Setup”, for an easy explainer video
                                </div>
                                <div class="faq_question">
                                    Can I have my orders print to a specific kitchen printer?
                                </div>
                                <div class="faq_response">
                                    Yes, your orders can print to both a Clover Device and Kitchen Printer. Turn off “Use Auto
                                    Print Settings” then manually choose the Order Receipts Printer
                                </div>
                                <div class="faq_question">
                                    I have more than one Clover Device, can I select more than one device for the Payments to
                                    print?
                                </div>
                                <div class="faq_response">
                                    It is recommended to only select one Clover device as the default printer for the payment or
                                    customer receipt. Walk over to the Clover where you want the orders to print and check the
                                    box, “Choose this device as the default printer” On the remaining Clover devices, uncheck that
                                    same box.
                                </div>
                                <div class="faq_question">
                                    The customer’s name is not showing on the printed receipt?
                                </div>
                                <div class="faq_response">
                                    Go to the Clover Setup App, then select Order Receipt, check the box “Show Customer Info,
                                    On the left-hand side, select Payment Receipt, check the box, show customer Info
                                </div>

                                <div class="faq_question">
                                    The special instructions is not showing up on the printed receipts?
                                </div>
                                <div class="faq_response">
                                    Go to the Clover Setup app, Select Order Receipts, check the box “Order note”
                                </div>
                                <div class="faq_question">
                                    How do I login to my website?
                                </div>
                                <div class="faq_response">
                                    Your Online Ordering page is the Smart Online Order Page or your existing WordPress
                                    Website. Typically, if you add /wp-admin after .com You can login with your username and
                                    password.
                                </div>
                                <div class="faq_question">
                                    I would like to allow customers to schedule their orders?
                                </div>
                                <div class="faq_response">
                                    In order for customers to schedule their orders, you will first need to add your business
                                    hours on Clover.com – Once your business hours are added, then login to the Smart Online
                                    Order plugin, select Clover orders, settings, store settings, enable schedule orders.
                                </div>
                                <div class="faq_question">
                                    I don’t’ want to accept “pay at location” orders, how do I change that?
                                </div>
                                <div class="faq_response">
                                    Login to your website, then go to clover orders, settings, checkout settings
                                </div>
                                <div class="faq_question">
                                    Are there any tutorial videos?
                                </div>
                                <div class="faq_response">
                                    Yes, there are lots of tutorial videos, please search YouTube for “Smart Online Order” and
                                    you will get a wide variety of videos.
                                </div>

                                <div class="faq_question">
                                    How do I add Delivery Areas and fees?
                                </div>
                                <div class="faq_response">
                                    Please search YouTube “Smart Online Order - Delivery Areas and you will be shown how to
                                    add and draw your delivery areas.
                                </div>

                                <div class="faq_question">
                                    I want to change the Online Business hours or wish to be closed for the day?
                                </div>
                                <div class="faq_response">
                                    Go to Clover.com, then setup, then business information and change the business hours.
                                    Your Clover Business hours correspond with your Online hours. For this to work, you must have
                                    enabled “Allow Scheduled Orders” on the website. Search Youtube, if you need a video tutorial.
                                </div>

                                <div class="faq_question">
                                    I have additional locations; can I add those as well?
                                </div>
                                <div class="faq_response">
                                    Yes, you can. If you need help, please email or call us and we can help you. You can search
                                    YouTube “Smart Online Order Multiple locations”
                                </div>

                                <div class="faq_question">
                                    I just received an online order and it is showing as Partially Paid?
                                </div>
                                <div class="faq_response">
                                    This happens when you have changed the price of an item on your Clover inventory and the
                                    price hasn’t been updated on the website. Simply perform a manual sync, so the website price
                                    is the same as the Clover inventory price. Search YouTube “Smart Online Order Manual Sync”
                                </div>

                                <div class="faq_question">
                                    I still have questions?
                                </div>
                                <div class="faq_response">
                                    Please e-mail support@zaytechapps.com or call 925-234- 5554
                                </div>

                            </div>
                        </div>
                    </div>
                </div> 
                <!-- Custom Hours -->
                <div id="MooPanel_tabContent12">
                    <h2>Custom Hours </h2><hr>
                    <iframe id="mooFrameCustomHours" src="https://smh.smartonlineorder.com/home/<?php echo $MooOptions['api_key'];?>" frameborder="0" style="width: 100%;height: 600px;overflow-y: scroll"></iframe>
                </div>
            </div>
        </div>
        <?php
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
            'id'    => 'Clover_Orders_themes',
            'title' => 'Store Interfaces',
            'href'  => admin_url().'admin.php?page=moo_themes',
            'parent'  => 'Clover_Orders',
        );
        $args4 = array(
            'id'    => 'Clover_Orders_orders',
            'title' => 'Orders',
            'href'  => admin_url().'admin.php?page=moo_orders',
            'parent'  => 'Clover_Orders',
        );
        $args5 = array(
            'id'    => 'Clover_Orders_items',
            'title' => 'Items / Images / Description',
            'href'  => admin_url().'admin.php?page=moo_items',
            'parent'  => 'Clover_Orders',
        );
        $args6 = array(
            'id'    => 'Clover_Orders_coupons',
            'title' => 'Coupons',
            'href'  => admin_url().'admin.php?page=moo_coupons',
            'parent'  => 'Clover_Orders',
        );
        $args7 = array(
            'id'    => 'Clover_Orders_reports',
            'title' => 'Reports',
            'href'  => admin_url().'admin.php?page=moo_reports',
            'parent'  => 'Clover_Orders',
        );
        $wp_admin_bar->add_node( $args  );
        $wp_admin_bar->add_node( $args2 );
        $wp_admin_bar->add_node( $args3 );
        $wp_admin_bar->add_node( $args4 );
        $wp_admin_bar->add_node( $args5 );
        $wp_admin_bar->add_node( $args6 );
        $wp_admin_bar->add_node( $args7 );
    }
    /**
     * Register the options.
     *
     * @since    1.0.0
     */
    public function register_mysettings() {
        register_setting('moo_settings', 'moo_settings');
    }
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_register_style( 'moo-grid-css',plugin_dir_url(dirname(__FILE__))."public/css/grid12.min.css", array(), $this->version);
        wp_enqueue_style( 'moo-grid-css' );

        wp_enqueue_style( 'moo-OnlineOrders-admin-css', plugin_dir_url( __FILE__ ).'css/moo-OnlineOrders-admin.css', array(), $this->version, 'all');
        wp_enqueue_style( 'moo-OnlineOrders-admin-small-devices-css', plugin_dir_url( __FILE__ ).'css/moo-OnlineOrders-admin-small-devices.css', array(), $this->version, 'only screen and (max-device-width: 1200px)');

        wp_enqueue_style( 'moo-OnlineOrders-dashboard-css', plugin_dir_url( __FILE__ ).'css/moo-dashboard.css', array(), $this->version,'all');

        wp_enqueue_style('moo-tooltip-css',   plugin_dir_url( __FILE__ )."css/tooltip.css", array(), $this->version, 'all');

        wp_register_style( 'moo-magnific-popup', plugin_dir_url(dirname(__FILE__))."public/css/magnific-popup.min.css" );
        wp_enqueue_style( 'moo-magnific-popup');

        wp_register_style( 'moo-font-awesome-dash', plugin_dir_url(dirname(__FILE__))."public/css/font-awesome.css" );
        wp_enqueue_style( 'moo-font-awesome-dash' );

        wp_register_style( 'moo-introjs-css',plugin_dir_url(__FILE__)."css/introjs.min.css",array(), $this->version);
        wp_enqueue_style( 'moo-introjs-css' );

        wp_register_style('moo-jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
        wp_enqueue_style('moo-jquery-ui');

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'jquery-ui-datepicker' );

        wp_register_style( 'moo-sweetalert-css',plugin_dir_url(dirname(__FILE__))."public/css/sweetalert2.min.css",array(), $this->version);
        wp_enqueue_style( 'moo-sweetalert-css' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        $MooOptions = (array)get_option('moo_settings');
        $params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php', isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' ),
            'plugin_url'=>plugin_dir_url(dirname(__FILE__)),
            'plugin_img'=>plugins_url( '/img', __FILE__ ),
            'nonce' => wp_create_nonce( 'wp_rest' )
        );
        $response = $this->api->getMerchantCustomHours("categories");
        if($response){
            $merchantCustomHours = json_decode($response,true);
        } else {
            $merchantCustomHours = array();
        }
        $response = $this->api->getMerchantCustomHours("ordertypes");
        if($response){
            $merchantCustomHoursForOT = json_decode($response,true);
        } else {
            $merchantCustomHoursForOT = array();
        }
        wp_enqueue_script('jquery');
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-sortable');
        wp_enqueue_media();


        // check if the merchant want use jQuery UI from an external link, some theme remove it
        if($this->external_ui) {
            wp_enqueue_script(
                'uicore',
                'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js',
                array('jquery')
            );
        }



        //wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/moo-OnlineOrders-admin.js', array( 'jquery' ), $this->version, false );
        wp_register_script('moo-google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBv1TkdxvWkbFaDz2r0Yx7xvlNKe-2uyRc&libraries=drawing&geometry');

        wp_register_script('moo-publicAdmin-js', plugins_url( 'js/moo-OnlineOrders-admin.js', __FILE__ ),array('moo-google-map'), $this->version);
        wp_register_script('moo-AdminDashboard-js', plugins_url( 'js/moo-dashboard.js', __FILE__ ),array('jquery','wp-color-picker'), $this->version);
        wp_register_script('moo-import-wizard-js', plugins_url( 'js/moo-importing-wizard.js', __FILE__ ),array('jquery'), $this->version);

        wp_register_script('moo-tooltip-js', plugins_url( 'js/tooltip.min.js', __FILE__ ),array(), $this->version);
        wp_register_script('moo-progressbar-js', plugins_url( 'js/progressbar.min.js', __FILE__ ));

        wp_register_script('moo-map-js', plugins_url( 'js/moo_map.js', __FILE__ ),array(), $this->version);
        wp_register_script('moo-map-da', plugins_url( 'js/moo_map_da.js', __FILE__ ),array(), $this->version);

        wp_register_script('moo-magnific-modal', plugin_dir_url(dirname(__FILE__))."public/js/magnific.min.js");
        wp_enqueue_script('moo-magnific-modal',array('jquery'));

        //Promise for IE
        wp_register_script('moo-bluebird', '//cdn.jsdelivr.net/bluebird/latest/bluebird.min.js',array(), $this->version);
        wp_enqueue_script('moo-bluebird');
        wp_register_script('moo-sweetalert-js', plugin_dir_url(dirname(__FILE__))."public/js/sweetalert2.min.js");
        wp_enqueue_script('moo-sweetalert-js',array('jquery'));

        wp_register_script('moo-introjs-js', plugin_dir_url(__FILE__)."js/introjs.min.js");
        wp_enqueue_script('moo-introjs-js',array('jquery'));

        wp_enqueue_script('moo-progressbar-js',array('jquery'));
        wp_enqueue_script("moo-tooltip-js",array('jquery'));

        wp_enqueue_script('moo-publicAdmin-js',array('jquery','wp-color-picker','jquery-ui-datepicker','jquery-ui-sortable'));

        //wp_enqueue_script('moo-AdminDashboard-js');
       // wp_localize_script("moo-AdminDashboard-js", "moo_params",$params);

        wp_localize_script("moo-publicAdmin-js", "moo_params",$params);
        wp_localize_script("moo-publicAdmin-js", "moo_custom_hours",$merchantCustomHours);
        wp_localize_script("moo-publicAdmin-js", "moo_custom_hours_for_ot",$merchantCustomHoursForOT);
        if($MooOptions["api_key"]) {
            wp_localize_script("moo-publicAdmin-js", "moo_api_key",$MooOptions["api_key"]);
        }
        wp_localize_script("moo-publicAdmin-js", "moo_RestUrl",get_rest_url());

    }

    public function moo_update_address()
    {
        $MooOptions = (array)get_option('moo_settings');

        $api   = new  moo_OnlineOrders_CallAPI();
        $merchant_address = $api->getMerchantAddress();
        wp_enqueue_script('moo-google-map');
        wp_enqueue_script('moo-map-js',array('jquery','moo-google-map'));

        // Not localize empty params
        $localizeParams = array(
            "lat","lng"
        );
        foreach($MooOptions as $key=>$value) {
            if (in_array($key,$localizeParams)) {
                if ($value == "") {
                    $MooOptions[$key] = null;
                }
            }
        }
        if($merchant_address=='')
            $merchant_address = ' ';
        wp_localize_script("moo-map-js", "moo_merchantAddress",urlencode($merchant_address));
        wp_localize_script("moo-map-js", "moo_merchantLat",$MooOptions['lat']);
        wp_localize_script("moo-map-js", "moo_merchantLng",$MooOptions['lng']);

        ?>
        <form method="post" action="options.php"  onsubmit="mooSaveChanges(event,this)">
            <?php
            settings_fields('moo_settings');

            //This form fields
            $fields = array('lat','lng');
            foreach ($MooOptions as $option_name=>$option_value)
                if(!in_array($option_name,$fields))
                    if($option_name=="custom_js" || $option_name =="custom_css" || $option_name == "copyrights"|| $option_name == "zones_json" || $option_name == "custom_sa_content" || $option_name == "closing_msg"
                    )
                        echo '<textarea name="moo_settings['.$option_name.']" id="" cols="10" rows="10" style="display:none">'.$option_value.'</textarea>';
                    else
                        echo '<input type="text"  name="moo_settings['.$option_name.']" value="'.$option_value.'" hidden/>';
            ?>
            <input type="hidden" name="_wp_http_referer" value="<?php echo (esc_url((admin_url('admin.php?page=moo_index')))); ?>" />
            <div id="MooPanel_tabContent1">
                <h2>Setup your address</h2><hr>
                <div class="MooPanelItem">
                    <h3>Please verify your address</h3>
                    <div class="Moo_option-item">
                        <div class="normal_text">If the address is incorrect, please go to Clover.com and make changes. You can also move the red pointer over to the correct location</div>
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
    public function moo_update_token()
    {
        $MooOptions = (array)get_option('moo_settings');

    ?>
            <div id="MooPanel_tabContent1">
                <h2>Change your api key</h2><hr>
                <div class="MooPanelItem">
                    <div class="Moo_option-item" style="padding-top: 0px;margin-top: -15px;">
                        <div style="color: red;font-size: 20px;line-height: 25px;margin: 10px;">
                            This action is irreversible and you will lose all your items,categories,modifiers and modifier groups, items images, items descriptions, categories images & descriptions,
                            This will be helpful if you want to keep only your settings. (refresh the page after changing the api key)
                        </div>
                    </div>
                    <div class="Moo_option-item">
                        <label for="api_key">Your New API KEY</label>
                        <input id="chang_api_key" type="text" value="<?php echo $MooOptions['api_key']?>" style="width: 100%;margin-top: 5px"/>

                    </div>
                    <div class="Moo_option-item">
                        <div style="text-align: center;">
                            <input type="button" class="button button-primary" value="Save Changes" onclick="mooUpdateApiKey()">
                            <a href="<?php echo (esc_url((admin_url('admin.php?page=moo_index')))); ?>" class="button">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
    public function detail_categories($all_categories)
    {
        $i=0;
        foreach ($all_categories as $category) { ?>
            <tr id="row_id_<?php echo $category->uuid ?>" data-cat-id="<?php echo $category->uuid ?>">
                <td style="display:none;"><span id='id_cat'><?php echo $category->uuid ?></span></td>
                <td class="img-cat" id="<?php echo $category->uuid ?>">
                    <?php if ($category->image_url == null) { ?>
                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/no-image.png" ?>" style="width: 50px;" onclick="uploader_image_category(event,'<?php echo $category->uuid ?>','D')">
                    <?php } else { ?>
                        <img src="<?php echo $category->image_url ?>" style="width: 50px;">
                    <?php } ?>
                </td>
                <td class="name-cat" id="name_<?php echo $category->uuid ?>" style="font-weight: bold;"><?php if ($category->alternate_name == null) {echo stripslashes($category->name);} else {echo stripslashes($category->alternate_name);} ?></td>
                <td class="show-cat">
                    <div class="moo-onoffswitch" title="Visibility Category">
                        <input type="checkbox" name="onoffswitch[]" id="myonoffswitch_Visibility_<?php echo $category->uuid ?>" class="moo-onoffswitch-checkbox visib_cat<?php echo $category->uuid ?>" onclick="visibility_cat('<?php echo $category->uuid ?>')" <?php if ($category->show_by_default == 1) {  ?>checked<?php } ?>>
                        <label class="moo-onoffswitch-label" for="myonoffswitch_Visibility_<?php echo $category->uuid ?>"><span class="moo-onoffswitch-inner"></span>
                            <span class="moo-onoffswitch-switch"></span>
                        </label>
                    </div>
                </td>
                <?php if ($category->image_url == null) { ?>
                    <td class="bt-cat">
                        <a href="#" onclick="uploader_image_category(event,'<?php echo $category->uuid ?>','D')">
                                        <span id="moo_epload_img<?php echo $i; ?>"
                                              data-ot="Upload Image"
                                              data-ot-target="#moo_epload_img<?php echo $i; ?>">
                                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/upload.png" ?>">
                                        </span>
                        </a>
                    </td>
                    <td colspan="2" style="text-align: center;">
                        <a href="#" class="edit_name">
                                        <span id="moo_edit_name<?php echo $i; ?>"
                                              data-ot="Edit Name"
                                              data-ot-target="#moo_edit_name<?php echo $i; ?>">
                                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/edit.png" ?>">
                                        </span>
                        </a>
                    </td>
                    <td colspan="2" style="text-align: center;">
                        <a href="#" class="edit_description">
                                        <span id="moo_edit_description<?php echo $i; ?>"
                                              data-ot="Edit Description"
                                              data-ot-target="#moo_edit_description<?php echo $i; ?>">
                                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/edit.png" ?>">
                                        </span>
                        </a>
                    </td>
                <?php } else { ?>
                    <td class="bt-cat">
                        <a href="#" onclick="uploader_image_category(event,'<?php echo $category->uuid ?>','D')">
                                        <span id="moo_change_name<?php echo $i; ?>"
                                              data-ot="Change Image"
                                              data-ot-target="#moo_change_name<?php echo $i; ?>">
                                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/upload.png" ?>">
                                        </span>
                        </a>
                    </td>
                    <td class="bt-cat">
                        <a href="#" class="edit_name">
                                        <span id="moo_edit_name<?php echo $i; ?>"
                                              data-ot="Edit Name"
                                              data-ot-target="#moo_edit_name<?php echo $i; ?>">
                                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/edit.png" ?>">
                                        </span>
                        </a>
                    </td>
                    <td class="bt-cat">
                        <a href="#" class="edit_description">
                                        <span id="moo_edit_description<?php echo $i; ?>"
                                              data-ot="Edit Decsription"
                                              data-ot-target="#moo_edit_description<?php echo $i; ?>">
                                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/edit.png" ?>">
                                        </span>
                        </a>
                    </td>
                    <td colspan="2" class="bt-cat">
                        <a href="#" onclick="delete_img_category(event,'<?php echo $category->uuid ?>','D')">
                                        <span id="moo_delete_img<?php echo $i; ?>"
                                              data-ot="Delete Image"
                                              data-ot-target="#moo_delete_img<?php echo $i; ?>">
                                        <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/delete.png" ?>">
                                        </span>
                        </a>
                    </td>
                <?php } ?>
                <td class="items_cat">
                    <a href="#detailCat<?php echo $category->uuid; ?>" class="moo-open-popupItems">
                         <span id="moo_items_get<?php echo $i; ?>"
                               data-ot="Reorder items"
                               data-ot-target="#moo_items_get<?php echo $i; ?>">
                              <img src="<?php echo plugin_dir_url(dirname(__FILE__))."public/img/plusIT.png" ?>" style="width: 20px;">
                         </span>
                    </a>
                </td>
            </tr>
            <div id="detailCat<?php echo $category->uuid; ?>" class="white-popup mfp-hide listItems" style="overflow-y: scroll;max-height:400px;">
                <h1>Reorder items</h1>
                <div style="font-size: 10px;color: #808080">Category UUID : <?php echo $category->uuid;?></div>
                <p>To add images to an item, from the dashboard select Clover Orders then items/images or click <a href="<?php echo admin_url().'admin.php?page=moo_items';?>">here</a></p>
                <?php
                $this->moo_getItemsByCategory($category->uuid,$category->items);
                ?>
                <p>If you don't see all items, click <a href="#" onclick="mooImportOneCategory('<?php echo $category->uuid; ?>')">here</a> to sync this category with your Clover Inventory</p>

            </div>
    <?php
        $i++;
        }
    }

    public function moo_getItemsByCategory($idcat,$items){
        $model = $this->model;
        $elements = explode(",", $items);
        $tab_items = array();
        if (count($elements) > 0)
        {
            for ($i = 0; $i < count($elements); $i++)
            {
                if($elements[$i] == "") continue;
                $item = $model->getItem($elements[$i]);
                if(!$item) continue;
                $tab_items[] = $item;
            }
            usort($tab_items, array("moo_OnlineOrders_Admin","sortItems"));
            if (!empty($tab_items)){
                echo "<ul class='moo_listItem' id-cat='$idcat'>";
                foreach ($tab_items as $value){
                    echo "<li class='cat$idcat' uuid_item='".$value->uuid."'>";
                    echo $value->name;
                    echo '<img src="'.plugin_dir_url(dirname(__FILE__)).'public/img/menu.png" style="width: 14px;float: right;margin-top: 2px;">';
                    echo "</li>";
                }
                echo "</ul>";
            }
            else{
                echo "<span>No items found</span>";
            }

        }
        else
            echo "<span>No items found</span>";

    }

    public function activate_plugin_in_network($blog_id, $user_id, $domain, $path, $site_id, $meta) {
        if( is_multisite()) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/moo-OnlineOrders-activator.php';
            switch_to_blog($blog_id);
            Moo_OnlineOrders_Activator::activate();
        }
    }
    public function delete_plugin_in_network($blog_id) {
        if( is_multisite()) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/moo-OnlineOrders-deactivator.php';
            switch_to_blog($blog_id);
            Moo_OnlineOrders_Deactivator::deactivateAndClean();
        }
    }
    public function moo_deactivateAndClean() {

    }
    public static function sortItems($a, $b)
    {
        return $a->sort_order>$b->sort_order;
    }
    public function displayUpdateNotice(){
        if( get_transient( 'moo_updated' ) ) {
            echo '<div class="notice notice-success">Thanks for updating</div>';
            // Delete the transient so we don't keep displaying the update message
            delete_transient( 'moo_updated' );
        }
    }
}
