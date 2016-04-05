<?php
/**
 * Plugin Name: SubscribePress
 * Plugin URI: http://www.servingmind.com/
 * Description: Email subscribers plugin has options to send newsletters to subscribers.
 * Version: 1.0
 * Author: Tuklal Poudel
 * Author URI: http://www.servingmind.com/
 * Donate link: http://www.servingmind.com/
 * Requires at least: 3.4
 * Tested up to: 4.4.2
 * Text Domain: subscribepress
 * Domain Path: /languages/
 * License: GPLv3
 * Copyright (c) 2015, 2016 Servingmind Technology
 */
if (!defined('WPINC')) {
    die;
}

function activate_subscribepress(){
    require_once plugin_dir_path(__FILE__) . 'includes/class-subscribepress-activator.php';
    SubscribePress_Activator::activate();
}


function deactivate_subscribepress(){
    require_once plugin_dir_path(__FILE__) . 'includes/class-subscribepress-deactivator.php';
    SubscribePress_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_subscribepress');
register_deactivation_hook(__FILE__, 'deactivate_subscribepress');
require plugin_dir_path(__FILE__) . 'includes/class-subscribepress.php';

function run_subscribepress()
{
    $plugin = new SubscribePress();
    $plugin->run();
}
run_subscribepress();
?>