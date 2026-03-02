<?php

class MC4WP_Autocomplete_Admin
{
    /**
     * Add hooks
     */
    public function add_hooks()
    {
        add_action('mc4wp_admin_form_after_behaviour_settings_rows', [$this, 'show_setting'], 10, 2);
    }

    /**
     * @param            $opts
     * @param MC4WP_Form $form
     */
    public function show_setting($opts, $form)
    {
        include __DIR__ . '/views/autocomplete-setting.php';
    }
}
