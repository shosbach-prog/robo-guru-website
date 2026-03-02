<?php

namespace MC4WP\User_Sync;

function get_settings()
{
    $settings = (array) get_option('mc4wp_user_sync', []);
    $defaults = [
        'list' => '',
        'role' => '',
        'enabled' => 1,
        'field_map' => [],
        'skip_empty_user_fields' => 0,
        'webhook_enabled' => 0,
        'webhook_secret' => '',
    ];

    $settings = array_merge($defaults, $settings);
    $settings['enabled'] = (int) $settings['enabled'];
    $settings['webhook_enabled'] = (int) $settings['webhook_enabled'];

    /**
     * Filters Mailchimp Sync settings
     *
     * @param array $settings
     */
    return (array) apply_filters('mc4wp_user_sync_settings', $settings);
}
