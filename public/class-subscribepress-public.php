<?php

/**
 * The public-facing functionality of the plugin.
 * @since      1.0.0
 * @package    subscribepress
 * @subpackage subscribepress/public
 */
class SubscribePress_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $subscribepress The ID of this plugin.
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
     * @param      string $subscribepress The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($subscribepress, $version)
    {

        $this->subscribepress = $subscribepress;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->subscribepress, plugin_dir_url(__FILE__) . 'css/subscribepress-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->subscribepress, plugin_dir_url(__FILE__) . 'js/subscribepress-public.js', array('jquery'), $this->version, false);


    }

    public function subscribe_form()
    {
        include_once(plugin_dir_path(__FILE__) . 'templates/subscribe-form.php');
    }


    /**
     * Registers all shortcodes at once
     */
    public function register_shortcodes()
    {
        add_shortcode('subscribe-form', [$this, 'subscribe_form']);
    }


    public function post_subscribe()
    {
        if (isset($_POST['sp-email'])) {
            $email = sanitize_email($_POST['sp-email']);
            $name = sanitize_text_field($_POST['sp-name']);
            $response = [];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = [
                    'status' => 'error',
                    'message' => 'Invalid email format',
                ];
                wp_send_json($response);
                exit();
            }

            if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'])) {
                $response = [
                    'status' => 'error',
                    'message' => 'Sorry, your nonce did not verify.',
                ];
                wp_send_json($response);
                exit();
            } else {
                $data = [
                    'email' => $email,
                    'name' => $name,
                    'remember_token' => md5(uniqid(rand(), true)),
                ];
                $id = $this->insert_subscriber($data);
                $this->send_confirmation($id, $data);
            }

            $response = [
                'status' => 'success',
                'message' => 'You have successfully subscribed to the newsletter.',
            ];
            wp_send_json($response);

            exit();
        }
    }


    public function insert_subscriber($data = [])
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "subscribers";
        $wpdb->insert($table_name, $data);
        return $wpdb->insert_id;
    }

    public function send_confirmation($id = "", $data)
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/email-confirmation.php';
        $headers = 'From: admin <noreply@admin>';
        $to = $data['email'];
        $subject = 'Confirmation';
        $message = file_get_contents(plugin_dir_path(dirname(__FILE__)) . 'public/templates/email/confirmation.php');;
        EmailConfirmation::send($to, $subject, $message, $headers);
    }


    public function post_confirmation(){

    }

}

?>