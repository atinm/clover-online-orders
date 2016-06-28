<?php
require_once 'class-wp-list-table-moo.php';
class Products_List_Moo extends WP_List_Table_MOO {

    /** Class constructor */
    public function __construct() {

        parent::__construct( array(
            'singular' => __( 'Item'), //singular name of the listed records
            'plural'   => __( 'Items'), //plural name of the listed records
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
        if(isset($_POST) && !empty($_POST['s']))
        {
            $sql = "SELECT * FROM {$wpdb->prefix}moo_item where name like '%".esc_sql($_POST['s'])."%'";
        }
        else
        {
            $sql = "SELECT * FROM {$wpdb->prefix}moo_item";
        }


        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        return $result;
    }
    /**
     * Hide an item.
     *
     * @param int $uuid of the item
     */
    public static function hide_item( $id ) {
        global $wpdb;
        $wpdb->update(
            "{$wpdb->prefix}moo_item",
            array(
                'visible' => '0'
            ),
            array( 'uuid' => $id )
        );
    }
    /**
     * Show an item.
     *
     * @param int $uuid of the item
     */
    public static function show_item( $id ) {
        global $wpdb;
        $wpdb->update(
            "{$wpdb->prefix}moo_item",
            array(
            'visible' => '1'
            ),
            array( 'uuid' => $id )
        );
    }
    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No items available.');
    }
    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;
        $per_page = 20;
        $page_number = 1;

        if(isset($_POST) && !empty($_POST['s']))
        {
            $sql = "SELECT count(*) FROM {$wpdb->prefix}moo_item where name like '%".esc_sql($_POST['s'])."%'";
        }
        else
        {
            $sql = "SELECT count(*) FROM {$wpdb->prefix}moo_item";
        }


        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
        return $wpdb->get_var( $sql );
    }
    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name( $item ) {

        // create a nonce
        $hide_nonce = wp_create_nonce( 'moo_hide_item' );
        $show_nonce = wp_create_nonce( 'moo_show_item' );
        $title = '<strong>' . $item['name'] . '</strong>';

        if($item['visible'])
            $actions = array(
                'hide' => sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s&paged=%s">Hide from the Website</a> 
                                   |<a href="?page=%s&action=%s&item_uuid=%s">Edit Item</a> ',
                                    'moo_items', 'hide',esc_attr($item['uuid']), $hide_nonce,$this->get_pagenum(),
                                    'moo_items', 'update_item',esc_attr($item['uuid'])  ),
            );
        else
            $actions = array(
                'show' => sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s&paged=%s">Show in the Website</a>
                                   |<a href="?page=%s&action=%s&item_uuid=%s">Edit Item</a>',
                                    esc_attr( $_REQUEST['page'] ), 'show',esc_attr($item['uuid']), $show_nonce,$this->get_pagenum(),
                                    esc_attr( $_REQUEST['page'] ), 'update_item',esc_attr($item['uuid']))
            );

        return $title . $this->row_actions( $actions );
    }
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
            case 'name':
            case 'sku':
            case 'code':
            case 'price_type':
            case 'unit_name':
                return $item[ $column_name ];
            case 'price':
                return '$'.round(($item['price']/100),2);
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
            '<input type="checkbox" name="bulk-hideOrShow[]" value="%s" />', $item['uuid']
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
            'name'    => __( 'Name'),
            'price' => __( 'Price'),
            'price_type' => __( 'Price Type'),
            'unit_name' => __( 'Unit'),
            'sku' => __( 'SKU'),
            'code' => __( 'Code')

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
            'name' => array( 'name', true ),
            'price' => array( 'price', false )
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
            'bulk-show' => 'Show Items',
            'bulk-hide' => 'Hide Items',
        );

        return $actions;
    }
    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

       // $this->_column_headers = $this->get_column_info();
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
        if ( 'hide' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'moo_hide_item' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::hide_item($_GET['item']);
                wp_redirect(admin_url('admin.php?page=moo_items&paged='.$_REQUEST['paged']));
                //wp_redirect(add_query_arg('paged',$_REQUEST['paged']));
                exit;
            }

        }
        else
            if('show' === $this->current_action()){
                // In our file that handles the request, verify the nonce.
                $nonce = esc_attr( $_REQUEST['_wpnonce'] );

                if ( ! wp_verify_nonce( $nonce, 'moo_show_item' ) ) {
                    die( 'Go get a life script kiddies' );
                }
                else {
                    self::show_item($_GET['item']);
                    wp_redirect(admin_url('admin.php?page=moo_items&paged='.$_REQUEST['paged']));
                    exit;
                }
            }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-hide' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-hide' )
        ) {

            $hide_ids = esc_sql( $_POST['bulk-hideOrShow'] );
            // loop over the array of record IDs and delete them
            foreach ( $hide_ids as $id ) {
               self::hide_item( esc_sql($id) );
            }
            wp_redirect(add_query_arg('paged',$_REQUEST['paged']));
           exit;
        }
        else
        {
            if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-show' )
                || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-show' )
            ) {

                $show_ids = esc_sql( $_POST['bulk-hideOrShow'] );
                // loop over the array of record IDs and delete them
                foreach ( $show_ids as $id ) {
                    self::show_item( esc_sql($id) );
                }

                wp_redirect(add_query_arg('paged',$_REQUEST['paged']) );
                exit;
            }
        }
    }
    public function single_row( $item ) {
        if(! $item['visible'])
            echo '<tr class="item-hidden">';
        else
            echo '<tr>';
        $this->single_row_columns( $item );
        echo '</tr>';
    }
}
