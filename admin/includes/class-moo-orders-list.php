<?php
require_once 'class-wp-list-table-moo.php';
class Orders_List_Moo extends WP_List_Table_MOO {

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
        $sql = "SELECT * FROM {$wpdb->prefix}moo_order";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
        else
        {
            $sql .= ' ORDER BY _id desc';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        return $result;
    }
    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No orders available.');
    }

    /** Delete Order */
    public function delete_order( $uuid ) {
        global $wpdb;
        $wpdb->delete( "{$wpdb->prefix}moo_item_order", array( 'order_uuid' => $uuid ) );
        $wpdb->delete( "{$wpdb->prefix}moo_order", array( 'uuid' => $uuid ) );

    }
    /** Delete Order */
    public function show_order( $uuid ) {
       //For future use if we want display the Order here
    }
    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}moo_order";

        return $wpdb->get_var( $sql );
    }
    /*
     * Get the first Item for an Order
     */
    function  getFirstItemFromOrder($order_uuid){
        global $wpdb;
        $sql = "SELECT name FROM `{$wpdb->prefix}moo_item` WHERE uuid = (SELECT item_uuid FROM {$wpdb->prefix}moo_item_order WHERE order_uuid = '".$order_uuid."' limit 1)";
        $itemName =  $wpdb->get_var( $sql );
        return $itemName;
    }
    function column_p_name( $order ) {
        // create a nonce
        $delete_nonce = wp_create_nonce( 'moo_delete_order' );
        $title = '<strong>' . $order['p_name'] . '</strong>';
        $actions = array(
            'Delete' => sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s">Delete Order</a>', ((isset($_REQUEST['page']))?$_REQUEST['page']:''), 'delete',$order['uuid'], $delete_nonce ),
            'Show' => sprintf( '<a href="https://www.clover.com/r/%s" target="_blank">Show Receipt</a>',$order['uuid']),
            'Show_detail' => sprintf( '<a href="?page=%s&action=%s&order_uuid=%s">Show Detail</a>', ((isset($_REQUEST['page']))?$_REQUEST['page']:''), 'show_order_detail',$order['uuid'] ),
        );
        return
            sprintf( '<a target="_blank" href="https://www.clover.com/r/%s">%s</a>',$order['uuid'],$title) . $this->row_actions( $actions );
    }
    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string

    function column_p_name( $item ) {
        // create a nonce

        $delete_nonce = wp_create_nonce( 'moo_delete_order' );
        $title = '<strong>' . $item['p_name'] . '</strong>';
            $actions = array(
                'Delete' => sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s">Delete Order</a>', esc_attr( $_REQUEST['page'] ), 'delete',$item['uuid'], $delete_nonce ),
                'Show' => sprintf( '<a href="https://www.clover.com/r/%s" target="_blank">Show Receipt</a>',$item['uuid']),
            );

        return
            sprintf( '<a target="_blank" href="https://www.clover.com/r/%s">%s</a>',$item['uuid'],$title) . $this->row_actions( $actions );
    }*/
    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'p_name':
            case 'p_email':
            case 'ordertype':
            case 'p_phone':
            case 'instructions':
                return stripslashes($item[$column_name]);
            case 'p_address':
                return stripslashes($item['p_address'].' '.$item['p_city'].' '.$item['p_state'].' '.$item['p_country']);
            case 'amount':
                return 'Total : $'.number_format((floatval($item['amount'])+floatval($item['tipAmount'])),2).'<br/>'.'TAX : $'.number_format($item['taxAmount'],2).'<br/>'.'TIP : $'.number_format($item['tipAmount'],2);
            case 'deliveryfee':
                return '$'.number_format($item['deliveryfee'],2);
            case 'paid':
                return ($item['paid'])?(($item['refpayment']=='CASH')?'CASH':(($item['refpayment']=='SCP')?'SCP':'PAID')):'FAILED';
            case 'items':
                return $this->getFirstItemFromOrder($item['uuid']);
            case 'state':
                return $item['p_state'];
            case 'orderID':
                return $item['uuid'];
            case 'date':
                return $item['date'].' UTC';
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
            '<input type="checkbox" name="bulk-hide[]" value="%s" />', $item['uuid']
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
            'p_name'    => __( 'Customer Name'),
            'amount' => __( 'Totals'),
            'items'    => __( 'The first Item'),
            'ordertype' => __( 'OrderType'),
            'date' => __( 'Date'),
            'instructions' => __( 'Special instructions')
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
            '_id' => array( '_id', false ),
            'date' => array( 'date', false ),
            'amount' => array( 'amount', false ),
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
            'bulk-delete' => 'Delete Orders'
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

        $per_page     = $this->get_items_per_page( 'moo_items_per_page', 20 );
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

            if ( ! wp_verify_nonce( $nonce, 'moo_delete_order' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_order($_GET['item']);

                wp_redirect( esc_url(add_query_arg()) ); exit;
            }

        }
        else
            if ( 'show' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'moo_show_order' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::show_order($_GET['item']);

               // wp_redirect( esc_url(add_query_arg()) ); exit;
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                $safe_id = intval($id);
                if($safe_id)
                    self::delete_order( $id );

            }
            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
    }
}
