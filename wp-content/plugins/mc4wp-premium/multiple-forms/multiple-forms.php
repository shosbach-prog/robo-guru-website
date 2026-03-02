<?php

defined('ABSPATH') or exit;

require __DIR__ . '/includes/class-widget-enhancements.php';
$widget_enhancements = new MC4WP_Form_Widget_Enhancements();
$widget_enhancements->add_hooks();

if (is_admin() && ! wp_doing_ajax()) {
    require __DIR__ . '/includes/class-admin.php';
    $admin = new MC4WP_Multiple_Forms_Admin();
    $admin->add_hooks();
}
