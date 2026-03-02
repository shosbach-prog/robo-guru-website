<?php

if (is_admin() || wp_doing_cron() || (defined('WP_CLI') && WP_CLI)) {
    $api_url = 'https://my.mc4wp.com/api/v2';
    $plugin_slug = 'mc4wp-premium';
    $plugin_file = MC4WP_PREMIUM_PLUGIN_FILE;
    $plugin_version = MC4WP_PREMIUM_VERSION;

    require __DIR__ . '/class-admin.php';
    $admin = new MC4WP\Licensing\Admin($plugin_slug, $plugin_file, $plugin_version, $api_url);
    $admin->add_hooks();
}

function mc4wp_has_active_license()
{
    $home_url = get_home_url();
    $hostname = parse_url($home_url, PHP_URL_HOST);
    if (!$hostname) {
        $hostname = $home_url;
    }

    // pretend license is active on anything that looks like a dev- or staging-environment
    if (WP_DEBUG || preg_match('/\.?(local|dev|staging)\.?/', $hostname)) {
        return true;
    }

    $license = array_merge((array) get_site_option('mc4w-premium_license', []), (array) get_option('mc4wp-premium_license', []));
    return $license && isset($license['activated']) && $license['activated'];
}

register_deactivation_hook(MC4WP_PREMIUM_PLUGIN_FILE, function () {
    wp_clear_scheduled_hook('mc4wp_license_check');
});
