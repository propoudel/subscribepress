<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    SubscribePress
 * @subpackage subscribepress/includes
 * @author     Tuklal Poudel <developer.sagarpoudel@gmail.com>
 */
class SubscribePress_Activator
{

    public static function activate()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "subscribers";
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
          id mediumint(10) NOT NULL AUTO_INCREMENT,
          name varchar(55) DEFAULT '' NOT NULL,
          email varchar(55) DEFAULT '' NOT NULL,
          remember_token varchar(100) NULL,
          status mediumint(2) DEFAULT '0' NOT NULL,
          created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          UNIQUE KEY id (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);


    }

}