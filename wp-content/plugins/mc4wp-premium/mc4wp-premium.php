<?php

/*
Plugin Name: MC4WP: Mailchimp for WordPress Premium
Plugin URI: https://www.mc4wp.com/
Description: Add-on for the MC4WP: Mailchimp for WordPress plugin. Adds Premium functionality when activated.
Version: 4.10.23
Author: ibericode
Author URI: https://ibericode.com/
License: GPLv2
Text Domain: mailchimp-for-wp

MC4WP: Mailchimp for WordPress Premium
Copyright (C) 2012 - 2026 ibericode BV

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


if (PHP_VERSION_ID < 70300 || ! defined('ABSPATH')) {
    return;
}

// Define some useful constants
define('MC4WP_PREMIUM_VERSION', '4.10.23');
define('MC4WP_PREMIUM_PLUGIN_FILE', __FILE__);

// include autoloader
require __DIR__ . '/autoload.php';

$plugins = [
    'activity-dashboard-widget',
    'ajax-forms',
    'autocomplete',
    'custom-color-theme',
    'email-notifications',
    'styles-builder',
    'multiple-forms',
    'logging',
    'ecommerce3',
    'licensing',
    'append-form-to-post',
    'user-sync',
    'post-campaign',
];

add_action('plugins_loaded', function () use ($plugins) {
    // make sure core plugin is installed and at version 4.8
    if (!defined('MC4WP_VERSION') || version_compare(MC4WP_VERSION, '4.8', '<')) {
        // if not, show a notice
        $required_plugins = [
            'mailchimp-for-wp' => [
                'url' => 'https://wordpress.org/plugins/mailchimp-for-wp/',
                'name' => 'Mailchimp for WordPress core',
                'version' => '4.8'
            ]
        ];
        $notice = new MC4WP_Required_Plugins_Notice('Mailchimp for WordPress - Premium', $required_plugins);
        $notice->add_hooks();
        return;
    }

    /**
     * Filters which add-on plugins should be loaded
     *
     * Takes an array of plugin slugs, defaults to all plugins.
     *
     * @param array $plugins
     */
    $plugins = apply_filters('mc4wp_premium_enabled_plugins', $plugins);

    // include each plugin
    foreach ($plugins as $plugin) {
        include __DIR__ . "/{$plugin}/{$plugin}.php";
    }
}, 30);

register_activation_hook(__FILE__, function () use ($plugins) {
    foreach ($plugins as $plugin) {
        if (file_exists(__DIR__ . "/{$plugin}/activate.php")) {
            require __DIR__ . "/{$plugin}/activate.php";
        }
    }
});

register_deactivation_hook(__FILE__, function () use ($plugins) {
    foreach ($plugins as $plugin) {
        if (file_exists(__DIR__ . "/{$plugin}/deactivate.php")) {
            require __DIR__ . "/{$plugin}/deactivate.php";
        }
    }
});
