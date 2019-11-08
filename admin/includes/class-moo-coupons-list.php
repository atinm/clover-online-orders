<?php
require_once 'class-wp-list-table-moo.php';
class Coupons_List_Moo extends WP_List_Table_MOO {
    /** Class constructor */
    public function __construct() {
              parent::__construct( array(
            'singular' => __( 'Order'), //singular name of the listed records
            'plural'   => __( 'Orders'), //plural name of the listed records
            'ajax'     => false //should this table support ajax?

        ) );
        //var_dump('creating an Object');
        /** Process bulk action */
        $this->process_bulk_action();

    }
    /**
     * Retrieve item’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_items( $per_page = 20, $page_number = 1 ) {
        global $wpdb;
        require_once plugin_dir_path( dirname(__FILE__) )."../models/moo-OnlineOrders-CallAPI.php";
        $api = new moo_OnlineOrders_CallAPI();
        $res = $api->getCoupons($per_page,$page_number-1);
        $res = json_decode($res,true);
        return $res['elements'];
    }
    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No Coupon available.');
    }

    /** Delete Order */
    public function delete_coupon($code) {
        require_once plugin_dir_path( dirname(__FILE__) )."../models/moo-OnlineOrders-CallAPI.php";
        $api = new moo_OnlineOrders_CallAPI();
        $api->deleteCoupon($code);
        return true;
    }
    public function enable_coupon($code,$status) {
        require_once plugin_dir_path( dirname(__FILE__) )."../models/moo-OnlineOrders-CallAPI.php";
        $api = new moo_OnlineOrders_CallAPI();
        $res = $api->enableCoupon($code,$status);
        $res = json_decode($res);
        if($res->status=="success")
            return true;
        return false;
    }
    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        require_once plugin_dir_path( dirname(__FILE__) )."../models/moo-OnlineOrders-CallAPI.php";
        $api = new moo_OnlineOrders_CallAPI();
        $res = json_decode($api->getNbCoupons());
        return $res->elements;
    }
    /*
     * Get the first Item for an Order
     */
    function column_name( $coupon ) {
        // create a nonce
        $delete_nonce = wp_create_nonce( 'moo_delete_coupon' );
        $title = '<strong>' . $coupon['name'] . '</strong>';

        if($coupon['isEnabled']=="1")
            $actions['Disable'] = sprintf( '<a href="?page=%s&paged=%s&action=%s&coupon=%s">Disable</a>', ((isset($_REQUEST['page']))?$_REQUEST['page']:''),((isset($_REQUEST['paged']))?$_REQUEST['paged']:''), 'disable',$coupon['code']);
        else
            $actions['Enable']  = sprintf( '<a href="?page=%s&paged=%s&action=%s&coupon=%s">Enable</a>',((isset($_REQUEST['page']))?$_REQUEST['page']:''),((isset($_REQUEST['paged']))?$_REQUEST['paged']:''), 'enable',$coupon['code']);

        $actions['Edit']   = sprintf( '<a href="?page=%s&paged=%s&action=%s&coupon=%s">Edit</a>', ((isset($_REQUEST['page']))?$_REQUEST['page']:''), ((isset($_REQUEST['paged']))?$_REQUEST['paged']:''), 'edit_coupon',$coupon['code'] );
        $actions['Delete'] = sprintf( '<a onclick="mooDeleteCoupon(event)" href="?page=%s&paged=%s&action=%s&coupon=%s&_wpnonce=%s">Delete</a>',((isset($_REQUEST['page']))?$_REQUEST['page']:''),((isset($_REQUEST['paged']))?$_REQUEST['paged']:''), 'delete',$coupon['code'], $delete_nonce );

        return
            sprintf( '%s',$title) . $this->row_actions( $actions );
    }

    public function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'name':
            case 'code':
            case 'type':
            case 'minAmount':
            case 'startdate':
            case 'expirationdate':
                return stripslashes($item[$column_name]);
            case 'uses':
                return stripslashes($item['uses'].' / '.$item['maxuses']);
            case 'value':
                return ($item['type']=="amount")?"$".$item['value']:$item['value']."%";
            case 'isEnabled':
                return ($item['isEnabled']=="1")?"Yes":"No";
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }
    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-coupon[]" value="%s" />', $item['code']
        );
    }
    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'      => '<input type="checkbox" />',
            'name'    => __( 'Coupon Name'),
            'code' => __( 'Code'),
            'value'    => __( 'Value'),
            'type'    => __( 'Type'),
            'minAmount'    => __( 'Min Amount'),
            'uses' => __( 'Number of uses'),
            'isEnabled' => __( 'Is enabled ?'),
            'expirationdate'    => __( 'Expiry date')
        );

        return $columns;
    }
    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            '_id' => array( '_id', false )
        );

        return $sortable_columns;
    }
    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => 'Delete Coupons',
            'bulk-enable' => 'Enable Coupons',
            'bulk-disable' => 'Disable Coupons'
        );

        return $actions;
    }
    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        //$this->_column_headers = $this->get_column_info();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        /** Process bulk action */
        //$this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'moo_items_per_page', 10 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );


        $this->items = self::get_items( $per_page, $current_page );
    }
    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );
            if ( ! wp_verify_nonce( $nonce, 'moo_delete_coupon' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                $res = self::delete_coupon($_GET['coupon']);
                wp_redirect( add_query_arg(array("deleted"=>$res),remove_query_arg( array('coupon', 'action'))));
                exit;
            }

        }
        else
            if ( 'enable' === $this->current_action() ) {
                     $res = self::enable_coupon($_GET['coupon'],"1");
                    wp_redirect( add_query_arg(array("enabled"=>$res),remove_query_arg( array('coupon', 'action'))));
                    exit;
            }
             else
                if ( 'disable' === $this->current_action() ) {
                    $res = self::enable_coupon($_GET['coupon'],"0");
                    //var_dump(add_query_arg(array("disabled"=>$res,"coupon"=>"")));
                    wp_redirect( add_query_arg(array("disabled"=>$res),remove_query_arg( array('coupon', 'action'))));
                    exit;
                }


        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $codes = esc_sql( $_POST['bulk-coupon'] );
            // loop over the array of record IDs and delete them
            foreach ( $codes as $code ) {
                $res = self::delete_coupon( $code );
                wp_redirect( add_query_arg(array("deleted"=>$res),remove_query_arg( array('coupon', 'action'))));
                exit;

            }
           // wp_redirect( esc_url( add_query_arg() ) );
           // exit;
        }
        else
            if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-enable' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-enable' )
            ) {
                $codes = esc_sql( $_POST['bulk-coupon'] );
                // loop over the array of record IDs and delete them
                foreach ( $codes as $code ) {
                    $res = self::enable_coupon( $code, "1" );
                }
                wp_redirect( add_query_arg(array("enabled"=>$res),remove_query_arg( array('coupon', 'action'))));
                exit;
            }
            else
                if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-disable' )
                || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-disable' )
                ) {

                    $codes = esc_sql( $_POST['bulk-coupon'] );
                    // loop over the array of record IDs and delete them
                    foreach ( $codes as $code ) {
                        $res = self::enable_coupon( $code, "0" );
                    }
                    wp_redirect( add_query_arg(array("disabled"=>$res),remove_query_arg( array('coupon', 'action'))));
                    exit;
                }
    }
}
