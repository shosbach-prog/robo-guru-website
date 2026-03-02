<?php

// make sure WooCommerce is installed & activated.
if (!class_exists('WooCommerce')) {
    return;
}

require_once __DIR__ . '/includes/functions.php';

$settings = mc4wp_ecommerce_get_settings();

if ($settings['enable_product_tracking']) {
    add_filter('cron_schedules', '_mc4wp_ecommerce_cron_schedules');
    _mc4wp_ecommerce_schedule_events();
}
