<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Subscriber_Admin_Display extends WP_List_Table
{

    function __construct()
    {
        parent::__construct(array(
            'singular' => 'subscriber',
            'plural' => 'subscribers',
            'ajax' => false
        ));
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'name':
            case 'email':
            case 'status':
                return $item[$column_name];
            case 'action':
                return ('<a href="%s">Sagar</a>');
            default:
                return print_r($item, true);
        }
    }


    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns()
    {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'sp'),
            'email' => __('Email', 'sp'),
            'status' => __('Status', 'sp'),
            'action' => __('Action', 'sp'),
        ];

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', false),
            'email' => array('email', false),
            'status' => array('status', false),
        );
        return $sortable_columns;
    }


    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = [
            'bulk-delete' => 'Delete',
            'bulk-export' => 'Export CSV',
        ];

        return $actions;
    }


    public function process_bulk_action()
    {
        if ('delete' === $this->current_action()) {
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'sp_delete_subscriber')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_subscriber(absint($_GET['subscriber']));
            }

            add_action('admin_notices', $this->flash_message('1 subscriber permanently deleted.', 'notice notice-error'));

        }

        // If the delete bulk action is triggered
        if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete') || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')) {
            $delete_ids = esc_sql($_POST['bulk-delete']);
            foreach ($delete_ids as $id) {
                self::delete_subscriber($id);
            }
            add_action('admin_notices', $this->flash_message(count($delete_ids) . ' subscribers permanently deleted.', 'notice notice-error'));
        }


        if ((isset($_POST['action']) && $_POST['action'] == 'bulk-export')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-export-table-to-csv.php';
            $exportCSV = new ExportTableToCsv("{$wpdb->prefix}subscribers", ';', 'report');

        }
    }





    /**
     * Delete a record.
     *
     * @param int $id
     */
    public static function delete_subscriber($id)
    {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}subscribers",
            ['id' => $id],
            ['%d']
        );
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count()
    {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}subscribers";
        return $wpdb->get_var($sql);
    }


    public function no_items()
    {
        _e('No subscriber available.', 'sp');
    }


    function column_name($item)
    {
        // create a nonce
        $delete_nonce = wp_create_nonce('sp_delete_subscriber');
        $title = '<strong>' . $item['name'] . '</strong>';
        $actions = [
            //'delete' => sprintf('<a href="?page=%s&action=%s&subscriber=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce),
            // 'ban' => sprintf('<a href="?page=%s&action=%s&subscriber=%s&_wpnonce=%s">Ban</a>', esc_attr($_REQUEST['page']), 'ban', absint($item['id']), $delete_nonce)
        ];
        return $title . $this->row_actions($actions);
    }


    function column_status($item)
    {
        $row = "";
        if ($item['status'] == "1") {
            $row = "<strong>Confirmed</strong>";
        } else {
            $row = "<strong>Unconfirmed</strong>";
        }
        return $row;
    }


    function column_action($item)
    {

        $delete_nonce = wp_create_nonce('sp_delete_subscriber');
        $actions = sprintf('<a href="?page=%s&action=%s&subscriber=%s&_wpnonce=%s">Edit</a>', esc_attr($_REQUEST['page']), 'edit', absint($item['id']), $delete_nonce);
        $actions .= '| ' . sprintf('<a href="?page=%s&action=%s&subscriber=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce);
        if ($item['status'] == "0") {
            $actions .= '| ' . sprintf('<a href="?page=%s&action=%s&subscriber=%s&_wpnonce=%s"><strong>Send Confirmation</strong></a>', esc_attr($_REQUEST['page']), 'ban', absint($item['id']), $delete_nonce);
        }

        return $actions;


    }

    /**
     * Retrieve subscriber’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */

    public static function get_subscribers($per_page = 12, $page_number = 1)
    {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}subscribers";

        if (!empty($_REQUEST['show'])) {
            $satus = "";
            if ($_REQUEST['show'] == 'confirmed') {
                $status = 1;
            } else {
                $status = 0;
            }
            $sql .= ' WHERE status =' . $status;
        }

        $search = (isset($_REQUEST['s'])) ? $_REQUEST['s'] : false;
        if ($search) {
            $sql .= " WHERE name LIKE '%$search%'";
            $sql .= " OR  email LIKE '%$search%'";
        }

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            $sql .= ' ORDER BY id DESC';
        }


        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;


        $result = $wpdb->get_results($sql, 'ARRAY_A');
        return $result;
    }


    function get_views()
    {
        $views = array();
        $current = (!empty($_REQUEST['show']) ? $_REQUEST['show'] : 'all');

        //All link
        $class = ($current == 'all' ? ' class="current"' : '');
        $all_url = remove_query_arg('show');
        $views['all'] = "<a href='{$all_url }' {$class} >All</a>";

        //Confirmed link
        $confirmed_url = add_query_arg('show', 'confirmed');
        $class = ($current == 'confirmed' ? ' class="current"' : '');
        $views['confirmed'] = "<a href='{$confirmed_url}' {$class} >Confirmed</a>";

        //Unconfirmed link
        $unconfirmed_url = add_query_arg('show', 'unconfirmed');
        $class = ($current == 'unconfirmed' ? ' class="current"' : '');
        $views['unconfirmed'] = "<a href='{$unconfirmed_url}' {$class} >Unconfirmed</a>";
        return $views;
    }


    function prepare_items()
    {
        global $wpdb;
        $per_page = 10;
        $this->_column_headers = $this->get_column_info();
        $this->process_bulk_action();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $per_page = $this->get_items_per_page('subscriber_per_page', $per_page);
        $current_page = $this->get_pagenum();
        $subscriber = (isset($_REQUEST['show']) ? $_REQUEST['show'] : 'all');
        $total_items = self::record_count();
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page
        ]);
        $this->items = self::get_subscribers($per_page, $current_page);
    }


    public function flash_message($message = "", $class = "sample-text-domain")
    {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e($message, $class); ?></p>
        </div>
        <?php
    }

}

?>