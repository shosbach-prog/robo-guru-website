<?php

defined('ABSPATH') or exit;

// make sure WooCommerce is installed & activated.
if (!class_exists('WooCommerce')) {
    return;
}

define('MC4WP_ECOMMERCE_VERSION', '1.0');
require __DIR__ . '/includes/class-ecommerce.php';
require __DIR__ . '/includes/class-helper.php';
require_once __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/class-worker.php';
require __DIR__ . '/includes/class-lock.php';

// load settings
$settings = mc4wp_ecommerce_get_settings();

// register ecommerce & tracker in service container (for lazy loading)
$mc4wp = mc4wp();
$mc4wp['ecommerce.options'] = $settings;
$mc4wp['ecommerce.tracker'] = function () use ($settings) {
    require __DIR__ . '/includes/class-tracker.php';
    return new MC4WP_Ecommerce_Tracker(__FILE__, $settings);
};
$mc4wp['ecommerce.transformer'] = function () use ($mc4wp, $settings) {
    require __DIR__ . '/includes/interface-transformer.php';

    if (!defined('WOOCOMMERCE_VERSION') || version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) {
        _deprecated_function('MC4WP_Ecommerce_Object_Transformer_WC2', '4.9.7');
        require __DIR__ . '/includes/class-transformer-wc2.php';
        return new MC4WP_Ecommerce_Object_Transformer_WC2($settings, $mc4wp['ecommerce.tracker']);
    }

    require __DIR__ . '/includes/class-transformer-wc3.php';
    return new MC4WP_Ecommerce_Object_Transformer_WC3($settings, $mc4wp['ecommerce.tracker']);
};
$mc4wp['ecommerce'] = function () use ($mc4wp, $settings) {
    return new MC4WP_Ecommerce($settings['store_id'], $mc4wp['ecommerce.transformer']);
};

$mc4wp['ecommerce.queue'] = function () {
    return new MC4WP_Queue('mc4wp_ecommerce_queue');
};

$mc4wp['ecommerce.worker'] = function () use ($mc4wp, $settings) {
    return new MC4WP_Ecommerce_Worker($settings, $mc4wp['ecommerce'], $mc4wp['ecommerce.queue']);
};

// enable queue & worker if e-commerce is enabled in settings
if ($settings['enable_product_tracking']) {
    add_filter('cron_schedules', '_mc4wp_ecommerce_cron_schedules');

    /** @var MC4WP_Ecommerce_Tracker */
    $mc4wp['ecommerce.tracker']->hook();

    // setup worker (processes items from queue)
    $mc4wp['ecommerce.worker']->hook();

    // setup product observer  (adds jobs to queue)
    require __DIR__ . '/includes/class-object-observer.php';
    require __DIR__ . '/includes/class-product-observer.php';
    $product_observer = new MC4WP_Ecommerce_Product_Observer($mc4wp['ecommerce'], $mc4wp['ecommerce.queue']);
    $product_observer->hook();

    if ($settings['enable_order_tracking']) {
        require __DIR__ . '/includes/class-order-observer.php';
        $order_observer = new MC4WP_Ecommerce_Order_Observer($mc4wp['ecommerce'], $mc4wp['ecommerce.queue']);
        $order_observer->hook();
    }

    // setup cart observer (adds jobs to queue)
    if ($settings['enable_cart_tracking']) {
        require __DIR__ . '/includes/class-cart-observer.php';
        $cart_observer = new MC4WP_Ecommerce_Cart_Observer(__FILE__, $mc4wp['ecommerce'], $mc4wp['ecommerce.queue'], $mc4wp['ecommerce.transformer']);
        $cart_observer->hook();
    }
}

// setup admin stuffs?
if (is_admin()) {
    if (wp_doing_ajax()) {
        require __DIR__ . '/includes/class-ajax.php';
        $ajax = new MC4WP_Ecommerce_Admin_Ajax();
        $ajax->hook();
    } else {
        require __DIR__ . '/includes/class-admin.php';

        $admin = new MC4WP_Ecommerce_Admin(__FILE__, $mc4wp['ecommerce.queue'], $settings);
        $admin->add_hooks();
    }
}

// register command when running cli
if (defined('WP_CLI') && WP_CLI) {
    require __DIR__ . '/includes/class-command.php';
    /* @phpstan-ignore class.notFound */
    WP_CLI::add_command('mc4wp-ecommerce', 'MC4WP_Ecommerce_Command');
}

// declare compatibility with WooCommerce HPOS
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', MC4WP_PREMIUM_PLUGIN_FILE, true);
    }
});

// load WPML compatibility functions
if (defined('ICL_SITEPRESS_VERSION')) {
    require __DIR__ . '/integrations/wpml.php';
}
