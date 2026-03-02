<?php

namespace MC4WP\Premium\AFTP;

require __DIR__ . '/includes/class-plugin.php';
$plugin = new Plugin();

if (is_admin() && ! wp_doing_ajax()) {
    require __DIR__ . '/includes/class-admin.php';
    $admin = new Admin();
}
