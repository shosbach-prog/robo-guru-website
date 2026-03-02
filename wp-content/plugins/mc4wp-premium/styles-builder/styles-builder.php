<?php

defined('ABSPATH') or exit;

if (is_admin() && ! wp_doing_ajax()) {
    require __DIR__ . '/includes/class-admin.php';
    $admin = new MC4WP_Styles_Builder_Admin(__FILE__);
    $admin->add_hooks();
}

require __DIR__ . '/includes/class-public.php';
$public = new MC4WP_Styles_Builder_Public();
$public->add_hooks();
