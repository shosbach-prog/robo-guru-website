<?php

defined('ABSPATH') or exit;

// main functionality
require __DIR__ . '/includes/class-autocomplete.php';
$autocomplete = new MC4WP_Autocomplete(__FILE__);
$autocomplete->add_hooks();


if (is_admin() && ! wp_doing_ajax()) {
    require __DIR__ . '/includes/class-admin.php';
    $admin = new MC4WP_Autocomplete_Admin();
    $admin->add_hooks();
}
