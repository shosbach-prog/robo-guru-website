<?php

class MC4WP_Custom_Color_Theme_Admin
{
    public function add_hooks()
    {
        add_filter('mc4wp_admin_form_css_options', [ $this, 'add_theme_option' ]);
        add_action('mc4wp_admin_form_after_appearance_settings_rows', [ $this, 'add_custom_color_option' ]);
        add_action('admin_enqueue_scripts', [ $this, 'enqueue_scripts' ]);
    }

    public function enqueue_scripts()
    {
        if (strpos($_GET['page'] ?? '', 'mailchimp-for-wp') !== 0) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    /**
     * Adds an option to the CSS dropdown.
     *
     * @param array $opts
     * @return array
     */
    public function add_theme_option($opts)
    {
        $opts['form-theme-custom-color'] = __('Custom Colored Theme', 'mailchimp-for-wp');
        return $opts;
    }

    /**
     * Adds the color option row
     *
     * @param $opts
     */
    public function add_custom_color_option($opts)
    {
        add_action('admin_print_footer_scripts', [ $this, 'admin_footer' ], 99);

        include __DIR__ . '/../views/setting.php';
    }

    /**
     * Instantiate the WP Color Picker on our fields
     */
    public function admin_footer()
    {

        ?>
        <script type="text/javascript">
          window.jQuery(document).ready(function() {
            window.jQuery('.color-field').wpColorPicker();
          });
        </script>
        <?php
    }
}
