<?php

defined('ABSPATH') or exit;

add_action('after_setup_theme', function () {
    /*
    * Do not run if logging is disabled.
    * This allows people to disable all logging by adding the following to their wp-config.php or functions.php file.
    *
    *   define('MC4WP_LOGGING', false);
    *
    */
    if (defined('MC4WP_LOGGING') && ! MC4WP_LOGGING) {
        return;
    }

    require __DIR__ . '/includes/functions.php';

    if (is_admin() && ! wp_doing_ajax()) {
        require __DIR__ . '/includes/class-admin.php';
        $logging_admin = new MC4WP_Logging_Admin();
        $logging_admin->add_hooks();
    }

    require __DIR__ . '/includes/class-logger.php';
    $logger = new MC4WP_Logger();
    $logger->add_hooks();
}, 10, 0);
