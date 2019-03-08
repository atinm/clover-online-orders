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
        $category_id="";
        if(isset($_GET['category']) && !empty($_GET['category']))
        {
            $category_id = esc_sql($_GET['category']);
            $category = $wpdb->get_row("SELECT items from {$wpdb->prefix}moo_category WHERE uuid='{$category_id}'",'ARRAY_A');
            $category_items = explode(',',$category['items']);
        }
        if(isset($_POST) && !empty($_POST['s'])) {
            $sql = "SELECT * FROM {$wpdb->prefix}moo_item where name like '%".esc_sql($_POST['s'])."%'";
        } else {
            if($category_id!="") {
                if(count($category_items)>0)
                {
                    $items_txt = implode("','", $category_items);
                    $items_txt = "'".$items_txt;
                    $items_txt =  substr($items_txt, 0, -2);;
                    $sql = "SELECT * FROM {$wpdb->prefix}moo_item where uuid in ({$items_txt})";
                }
                else
                {
                    $sql = "SELECT * FROM {$wpdb->prefix}moo_item where 1=-1')";
                }

            }
            else
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
    /**
     * Go out of stock.
     *
     * @param int $uuid of the item
     */
    public static function out_of_stock($id,$status) {
        global $wpdb;
        $res = ($status)?'1':'0';
        $wpdb->update(
            "{$wpdb->prefix}moo_item",
            array(
            'outofstock' => $res
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
        $category_id="";
        if(isset($_GET['category']) && !empty($_GET['category']))
        {
            $category_id = esc_sql($_GET['category']);
            $category = $wpdb->get_row("SELECT items from {$wpdb->prefix}moo_category WHERE uuid='{$category_id}'",'ARRAY_A');
            $category_items = explode(',',$category['items']);
        }

        if(isset($_POST) && !empty($_POST['s']))
        {
            $sql = "SELECT count(*) FROM {$wpdb->prefix}moo_item where name like '%".esc_sql($_POST['s'])."%'";
        }
        else
        {
            if($category_id!="")
            {
                if(count($category_items)>0)
                {
                    $items_txt = implode("','", $category_items);
                    $items_txt = "'".$items_txt;
                    $items_txt =  substr($items_txt, 0, -2);;
                    $sql = "SELECT count(*) FROM {$wpdb->prefix}moo_item where uuid in ({$items_txt})";
                }
                else
                {
                    $sql = "SELECT count(*) FROM {$wpdb->prefix}moo_item where 1=-1')";
                }

            }
            else
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
        $hide_nonce       = wp_create_nonce( 'moo_hide_item' );
        $show_nonce       = wp_create_nonce( 'moo_show_item' );

        $enable_ot_nonce       = wp_create_nonce( 'moo_enable_ot' );
        $disable_ot_nonce       = wp_create_nonce( 'moo_disable_ot' );

        $title = '<strong>' . $item['name'] . '</strong>';
        if($item['visible'])
            $actions = array(
                'hide' => sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s&paged=%s">Hide from the Website</a>',
                                    'moo_items', 'hide',esc_attr($item['uuid']), $hide_nonce,$this->get_pagenum())
            );
        else
            $actions = array(
                'show' => sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s&paged=%s">Show in the Website</a>',
                    'moo_items', 'show',esc_attr($item['uuid']), $show_nonce,$this->get_pagenum())
            );

        if($item['outofstock'])
            $actions['disable_ot']  = sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s&paged=%s">Disable out of stock</a>',
                    'moo_items', 'disable_ot',esc_attr($item['uuid']), $disable_ot_nonce,$this->get_pagenum());
        else
            $actions['enable_ot']  = sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s&paged=%s">Enable out of stock</a>',
                'moo_items', 'enable_ot',esc_attr($item['uuid']), $enable_ot_nonce,$this->get_pagenum());

        $actions['edit'] = sprintf( '<a href="?page=%s&action=%s&item_uuid=%s">Add / Edit Images</a>',
            'moo_items', 'update_item',esc_attr($item['uuid']));
        $actions['edit_description'] = sprintf( '<a class="moo-edit-description-button" href="#edit-description-popup-%s">Add / Edit description</a><div id="edit-description-popup-%s" class="white-popup mfp-hide"><textarea id="edit-description-content-%s" style="width: 100&#37;"  rows="5">%s</textarea><button class="button" onclick="moo_editItemDescription(event,\'%s\')">Save</button></div>',
            esc_attr($item['uuid']),esc_attr($item['uuid']),esc_attr($item['uuid']),stripslashes($item['description']),esc_attr($item['uuid']));

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
            'bulk-enable-ot' => 'Enable Out of stock',
            'bulk-disable-ot' => 'Disable Out of stock'
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
                wp_redirect(admin_url('admin.php?page=moo_items&paged='.((isset($_REQUEST['paged']))?$_REQUEST['paged']:'')));
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
                    wp_redirect(admin_url('admin.php?page=moo_items&paged='.((isset($_REQUEST['paged']))?$_REQUEST['paged']:'')));
                    exit;
                }
            }
            else
                if('enable_ot' === $this->current_action())
                {
                    $nonce = esc_attr( $_REQUEST['_wpnonce'] );

                    if ( ! wp_verify_nonce( $nonce, 'moo_enable_ot' ) ) {
                        die( 'Go get a life script kiddies' );
                    }
                    else {
                        self::out_of_stock($_GET['item'],true);
                        wp_redirect(admin_url('admin.php?page=moo_items&paged='.((isset($_REQUEST['paged']))?$_REQUEST['paged']:'')));
                        exit;
                    }
                }
                else
                {
                    if('disable_ot' === $this->current_action())
                    {
                        $nonce = esc_attr( $_REQUEST['_wpnonce'] );
                        if ( ! wp_verify_nonce( $nonce, 'moo_disable_ot' ) ) {
                            die( 'Go get a life script kiddies' );
                        }
                        else {
                            self::out_of_stock($_GET['item'],false);
                            wp_redirect(admin_url('admin.php?page=moo_items&paged='.((isset($_REQUEST['paged']))?$_REQUEST['paged']:'')));
                            exit;
                        }
                    }
                }
        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-hide' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-hide' )
        )
        {

            $hide_ids = esc_sql( $_POST['bulk-hideOrShow'] );
            // loop over the array of record IDs and delete them
            foreach ( $hide_ids as $id ) {
               self::hide_item( esc_sql($id) );
            }
            wp_redirect(add_query_arg('paged',((isset($_REQUEST['paged']))?$_REQUEST['paged']:'')));
           exit;
         }
        else
        {
            if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-show' )
                || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-show' )
            )
            {

                $show_ids = esc_sql( $_POST['bulk-hideOrShow'] );
                // loop over the array of record IDs and delete them
                foreach ( $show_ids as $id ) {
                    self::show_item( esc_sql($id) );
                }

                wp_redirect(add_query_arg('paged',((isset($_REQUEST['paged']))?$_REQUEST['paged']:'')) );
                exit;
            }
            else
            {
                if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-enable-ot' )
                    || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-enable-ot' )
                )
                {

                    $enable_ids = esc_sql( $_POST['bulk-hideOrShow'] );
                    // loop over the array of record IDs and delete them
                    foreach ( $enable_ids as $id ) {
                        self::out_of_stock( esc_sql($id),true);
                    }

                    wp_redirect(add_query_arg('paged',((isset($_REQUEST['paged']))?$_REQUEST['paged']:'')) );
                    exit;
                }
                else
                {
                    if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-disable-ot' )
                        || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-disable-ot' )
                    )
                    {

                        $disable_ids = esc_sql( $_POST['bulk-hideOrShow'] );
                        // loop over the array of record IDs and delete them
                        foreach ( $disable_ids as $id ) {
                            self::out_of_stock( esc_sql($id),false );
                        }

                        wp_redirect(add_query_arg('paged',((isset($_REQUEST['paged']))?$_REQUEST['paged']:'')) );
                        exit;
                    }
                }
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
    function extra_tablenav( $which ) {
        global $wpdb;
        $move_on_url = '&category=';
        if ( $which == "top" ){
            ?>
            <div class="alignleft actions bulkactions">
                <?php
                $cats = $wpdb->get_results("select * from {$wpdb->prefix}moo_category order by sort_order asc", ARRAY_A);
                if( $cats ){
                    ?>
                    <select id="moo_cat_filter" class="ewc-filter-cat">
                        <option value="">All categories</option>
                        <?php
                        foreach( $cats as $cat ){
                            $selected = '';
                            if( $_GET['category'] == $cat['uuid'] ){
                                $selected = ' selected = "selected"';
                            }
                            ?>
                                <option value="<?php echo $move_on_url . $cat['uuid']; ?>" <?php echo $selected; ?>><?php echo $cat['name']; ?></option>
                                <?php
                        }
                        ?>
                    </select>
                    <input type="button" name="filter_action" onclick="moo_filtrer_by_category(event)" class="button" value="Filter">
                    <?php
                }
                ?>
            </div>
            <?php
        }
        if ( $which == "bottom" ){
            //The code that goes after the table is there

        }
    }
}
