<?php
class SubscribePress_Admin
{
    /**
     * @since    1.0.0
     * @access   private
     * @var      string $subscribepress .
     */
    private $subscribepress;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $subscribepress The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($subscribepress, $version)
    {
        $this->subscribepress = $subscribepress;
        $this->version = $version;
    }


    public function enqueue_styles()
    {
        wp_enqueue_style($this->subscribepress, plugin_dir_url(__FILE__) . 'css/subscribepress-admin.css', array(), $this->version, 'all');

    }


    public function enqueue_scripts()
    {

        wp_enqueue_script($this->subscribepress, plugin_dir_url(__FILE__) . 'js/subscribepress-admin.js', array('jquery'), $this->version, false);

    }

    public function add_menu()
    {
        add_menu_page(
            'Subscribers',
            'Subscribers',
            'manage_options',
            $this->subscribepress . '-listing',
            array($this, 'subscriber_email_listing'),
            plugin_dir_url(__FILE__) . 'images/email-icon.png',
            '23.56'
        );
        add_submenu_page(
            $this->subscribepress . '-listing',
            apply_filters($this->subscribepress . '-setting', esc_html__('Campaign', 'create-campaign')),
            apply_filters($this->subscribepress . '-setting', esc_html__('Campaign', 'create-campaign')),
            'manage_options',
            $this->subscribepress . '-create-campaign',
            array($this, 'subscribepress_setting')
        );
        add_submenu_page(
            $this->subscribepress . '-listing',
            apply_filters($this->subscribepress . '-setting', esc_html__('Setting', 'setting')),
            apply_filters($this->subscribepress . '-setting', esc_html__('Setting', 'setting')),
            'manage_options',
            $this->subscribepress . '-setting',
            array($this, 'subscribepress_setting')
        );
    }


    public function subscriber_email_listing()
    {
        global $wpdb;
        if (isset($_GET['action']) == 'add') {
            include_once(plugin_dir_path(__FILE__) . 'templates/subscriber-add.php');
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['sp-email'])) {
                $this->post_add_subscriber();
            }

        } elseif (isset($_GET['action']) == 'edit') {
            $id = $_GET['subscriber'];
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['sp-email'])) {
                $this->post_update_subscriber();
            }
            $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}subscribers WHERE id = $id", OBJECT);
            include_once(plugin_dir_path(__FILE__) . 'templates/subscriber-edit.php');

        } else {
            $subscriber = new Subscriber_Admin_Display();
            $subscriber->prepare_items();
            include_once(plugin_dir_path(__FILE__) . 'templates/subscriber-list.php');
        }
    }

    public function post_add_subscriber()
    {
        $email = sanitize_email($_POST['sp-email']);
        $name = sanitize_text_field($_POST['sp-name']);
        $status = sanitize_text_field($_POST['sp-status']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash_message('Invalid email format');
            return true;
        }

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'])) {
            $this->flash_message('Sorry, your nonce did not verify.');
            return true;
        } else {
            $this->insert_subscriber([
                'email' => $email,
                'name' => $name,
                'status' => $status
            ]);
        }
        $this->flash_message('Item successfully added.');
        return true;
    }

    public function post_update_subscriber()
    {
        $email = sanitize_email($_POST['sp-email']);
        $name = sanitize_text_field($_POST['sp-name']);
        $status = sanitize_text_field($_POST['sp-status']);
        $id = $_GET['subscriber'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash_message('Invalid email format');
            return true;
        }

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'])) {
            $this->flash_message('Sorry, your nonce did not verify.');
            return true;
        } else {
            $this->update_subscriber([
                'email' => $email,
                'name' => $name,
                'status' => $status
            ], [
                'id' => $id
            ]);
        }
        $this->flash_message('Item successfully updated.');

    }


    public function subscribepress_setting()
    {
        include_once(plugin_dir_path(__FILE__) . 'templates/subscribepress-setting.php');
    }

    public function update_subscriber($data = [], $id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "subscribers";
        $wpdb->update($table_name, $data, $id);
        return true;
    }

    public function insert_subscriber($data = [])
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "subscribers";
        $wpdb->insert($table_name, $data);
        return true;
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