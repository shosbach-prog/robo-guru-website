<?php

/**
* Plugin Name: AI Chatbot - Jotform
* Plugin URI: http://wordpress.org/plugins/jotform-ai-chatbot/
* Description: AI chatbot that automates support, answers FAQs, drives WooCommerce sales, generates leads, and boosts engagement — easy setup, no coding!
* Author: Jotform
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Version: 3.6.3
* Author URI: https://www.jotform.com/
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit(0);
}

// Define plugin constants for main file, directory path, and URL
define('JAIC_PLUGIN_VERSION', '3.6.3');
define('JAIC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JAIC_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Enqueue admin scripts and styles for the plugin
 */
function jotform_ai_chatbot_admin_enqueue($hook) {
    $allowed_pages = [
        'toplevel_page_jotform_ai_chatbot',
        'jotform-ai-chatbot_page_jotform_ai_chatbot_conversations',
        'jotform-ai-chatbot_page_jotform_ai_chatbot_settings'
    ];

    if (!in_array($hook, $allowed_pages)) {
        return;
    }

    $isDevEnv = isset($_SERVER["SERVER_NAME"]) && $_SERVER["SERVER_NAME"] === "localhost";
    $buildDir = $isDevEnv ? "dist" : "lib";

    // Required WP script
    wp_enqueue_script('wp-date');

    // Main plugin assets
    wp_enqueue_script(
        "plugin-script",
        JAIC_PLUGIN_URL . "{$buildDir}/app/app.js",
        [],
        JAIC_PLUGIN_VERSION,
        true
    );

    // Main plugin css
    $css_path = plugin_dir_path(__FILE__) . "{$buildDir}/app/app.css";
    if (file_exists($css_path)) {
        $custom_css = file_get_contents($css_path);
        wp_register_style('jotform-ai-chatbot-style', false, [], JAIC_PLUGIN_VERSION);
        wp_enqueue_style('jotform-ai-chatbot-style');
        wp_add_inline_style('jotform-ai-chatbot-style', $custom_css);
    }

    // Preloader script
    wp_enqueue_script(
        "plugin-preloader-script",
        JAIC_PLUGIN_URL . "lib/admin.js",
        [],
        JAIC_PLUGIN_VERSION,
        true
    );
}
add_action("admin_enqueue_scripts", "jotform_ai_chatbot_admin_enqueue");

/**
 * Callback Function for Developers Section
 *
 * Renders the plugin interface within the WordPress admin settings page.
 * Initializes JavaScript environment variables required for the plugin.
 */
function jotform_ai_chatbot_developers_callback($args) {
    global $jaic_core;

    // Set Page WP Nounce Fields
    wp_nonce_field("jotform-ai-chatbot", "_nonce");
    ?>
    <input type="hidden" id="platform_api_url" name="platform_api_url" value="<?php echo esc_html($jaic_core->getPlatformAPIURL()); ?>" />
    <div id="jfpChatbot-app">
        <div class="jfLoader-wrapper">
            <div class="jfLoader"></div>
            <strong>Jotform AI Chatbot wizard is loading...</strong>
        </div>
    </div>
    <?php
}

/**
 * Add plugin to WP menu
 *
 * Creates a new menu entry for the plugin in the WordPress admin dashboard.
 */
function jotform_ai_chatbot_plugin_options_page() {
    add_menu_page(
        "Jotform AI Chatbot",
        "Jotform AI Chatbot",
        "manage_options",
        "jotform_ai_chatbot",
        "jotform_ai_chatbot_render_plugin",
        "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9IiNhN2FhYWQiIHZpZXdCb3g9IjAgMCAyNCAyNCI+CiAgPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIgogICAgZD0iTTMuNjY3IDEyLjMxMWEyLjUxNSAyLjUxNSAwIDAgMSAwLTMuNTczTDkuNyAyLjc0YTIuNTU1IDIuNTU1IDAgMCAxIDMuNTk3IDAgMi41MTUgMi41MTUgMCAwIDEgMCAzLjU3NEw3LjI2MyAxMi4zMWEyLjU1NSAyLjU1NSAwIDAgMS0zLjU5NyAwWm05LjQ3IDUuMzc1YTIuNTE1IDIuNTE1IDAgMCAwIDAgMy41NzQgMi41NTUgMi41NTUgMCAwIDAgMy41OTggMGwzLjU4NC0zLjU2MmEyLjUxNSAyLjUxNSAwIDAgMCAwLTMuNTczIDIuNTU1IDIuNTU1IDAgMCAwLTMuNTk3IDBsLTMuNTg1IDMuNTYxWk03LjQ2NyAyMmMuNTM2IDAgLjgwMy0uNjI3LjQyNS0uOTkzTDMuOTM1IDE3LjE3Yy0uMzc4LS4zNjYtMS4wMjUtLjEwOC0xLjAyNS40MTJ2My4yNTNjMCAuNjQyLjUzOSAxLjE2NCAxLjIgMS4xNjRoMy4zNTdabTEuMTMxLTguOTg4YTIuNTE1IDIuNTE1IDAgMCAwIDAgMy41NzQgMi41NTUgMi41NTUgMCAwIDAgMy41OTcgMGw4LjE1Mi04LjA5OGEyLjUxNSAyLjUxNSAwIDAgMCAwLTMuNTc0IDIuNTU1IDIuNTU1IDAgMCAwLTMuNTk3IDBsLTguMTUyIDguMDk4WiIKICAgIGNsaXAtcnVsZT0iZXZlbm9kZCIgLz4KPC9zdmc+"
    );

    $options = get_option('jotform_ai_chatbot_options');
    $options = !empty($options) ? json_decode($options, true) : [];

    // Determine link text based on the presence of ai chatbot
    $link_text = (!empty($options["agentId"])) ? 'My AI Chatbot' : 'Create AI Chatbot';

    add_submenu_page(
        'jotform_ai_chatbot',
        $link_text,
        $link_text,
        'manage_options',
        'jotform_ai_chatbot',
        'jotform_ai_chatbot_render_plugin'
    );

    add_submenu_page(
        'jotform_ai_chatbot',
        'Conversations',
        'Conversations',
        'manage_options',
        'jotform_ai_chatbot_conversations',
        'jotform_ai_chatbot_conversations_callback'
    );

    add_submenu_page(
        'jotform_ai_chatbot',
        'Settings',
        'Settings',
        'manage_options',
        'jotform_ai_chatbot_settings',
        'jotform_ai_chatbot_settings_callback'
    );
}
add_action("admin_menu", "jotform_ai_chatbot_plugin_options_page");


/**
 * Callback function for rendering the AI Chatbot settings page in the WordPress admin area.
 *
 * - Outputs the page wrapper and heading using the current admin page title.
 * - Delegates additional settings/content rendering to `jotform_ai_chatbot_developers_callback()`.
 *
 * @param array $args Arguments passed to the settings page (if any).
 */
function jotform_ai_chatbot_settings_callback($args) {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    </div>
    <?php
    jotform_ai_chatbot_developers_callback($args);
}

/**
 * Callback function for rendering the AI Chatbot conversations page in the WordPress admin area.
 *
 * - Displays the page wrapper and heading using the current admin page title.
 * - Hands off the rest of the page content rendering to `jotform_ai_chatbot_developers_callback()`.
 *
 * @param array $args Arguments passed to the conversations page (if any).
 */
function jotform_ai_chatbot_conversations_callback($args) {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    </div>
    <?php
    jotform_ai_chatbot_developers_callback($args);
}

/**
 * Add plugin to Admin Bar
 *
 * Adds a new menu item to the WordPress admin bar for quick access to the plugin settings.
 */
function jotform_ai_chatbot_admin_bar_menu($wp_admin_bar) {
    if (current_user_can("manage_options")) {
        $icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: bottom; margin-right: 4px;" fill="currentColor" viewBox="0 0 24 24" width="16" height="32"><path fill-rule="evenodd" d="M3.667 12.311a2.515 2.515 0 0 1 0-3.573L9.7 2.74a2.555 2.555 0 0 1 3.597 0 2.515 2.515 0 0 1 0 3.574L7.263 12.31a2.555 2.555 0 0 1-3.597 0Zm9.47 5.375a2.515 2.515 0 0 0 0 3.574 2.555 2.555 0 0 0 3.598 0l3.584-3.562a2.515 2.515 0 0 0 0-3.573 2.555 2.555 0 0 0-3.597 0l-3.585 3.561ZM7.467 22c.536 0 .803-.627.425-.993L3.935 17.17c-.378-.366-1.025-.108-1.025.412v3.253c0 .642.539 1.164 1.2 1.164h3.357Zm1.131-8.988a2.515 2.515 0 0 0 0 3.574 2.555 2.555 0 0 0 3.597 0l8.152-8.098a2.515 2.515 0 0 0 0-3.574 2.555 2.555 0 0 0-3.597 0l-8.152 8.098Z" clip-rule="evenodd"></path></svg>';
        $parent_id = "jotform_ai_chatbot";

        // Main menu item
        $wp_admin_bar->add_node([
            "id"     => $parent_id,
            "title"  => $icon_svg . " " . esc_html__("Jotform AI Chatbot", "jotform-ai-chatbot"),
            "href"   => admin_url("admin.php?page=jotform_ai_chatbot")
        ]);

        // Submenu: Conversations
        $wp_admin_bar->add_node([
            "id"     => "jotform_ai_chatbot_conversations",
            "parent" => $parent_id,
            "title"  => esc_html__("Conversations", "jotform-ai-chatbot"),
            "href"   => admin_url("admin.php?page=jotform_ai_chatbot_conversations"),
        ]);

        // Submenu: Settings
        $wp_admin_bar->add_node([
            "id"     => "jotform_ai_chatbot_settings",
            "parent" => $parent_id,
            "title"  => esc_html__("Settings", "jotform-ai-chatbot"),
            "href"   => admin_url("admin.php?page=jotform_ai_chatbot_settings"),
        ]);
    }
}
add_action("admin_bar_menu", "jotform_ai_chatbot_admin_bar_menu", 100);

/**
 * Hide notices on the plugin page to avoid confusion
 */
function jaic_hide_notices() {
    $screen = get_current_screen();
    if ($screen && ($screen->id === 'toplevel_page_jotform_ai_chatbot' || $screen->id === 'jotform-ai-chatbot_page_jotform_ai_chatbot_conversations' || $screen->id === 'jotform-ai-chatbot_page_jotform_ai_chatbot_settings')) {
        echo '<style>
            .notice-success,
            .notice-error,
            .notice-warning,
            .notice-info,
            .notice.notice-success,
            .notice.notice-error,
            .notice.notice-warning,
            .notice.notice-info {
                display: none !important;
            }
        </style>';
    }
}

add_action('admin_head', 'jaic_hide_notices');

/**
 * Hide submenus conditionally
 */
function jaic_hide_submenus() {
    $options = get_option("jotform_ai_chatbot_options");
    $options = !empty($options) ? json_decode($options, true) : [];
    if (empty($options["agentId"])) {
        ?>
        <script>
            jQuery(document).ready(function($) {
                const sidebarMenu = $('#toplevel_page_jotform_ai_chatbot > ul');
                const conversationsMenuItem = sidebarMenu?.find('a[href="admin.php?page=jotform_ai_chatbot_conversations"]');
                const conversationsAdminBarMenuItem = $('#wp-admin-bar-jotform_ai_chatbot_conversations');

                // Hide sidebar submenu 
                $(conversationsMenuItem).hide();
                // Hide admin bar submenu
                $(conversationsAdminBarMenuItem).hide();
            });
        </script>
        <?php
    }

    if (empty($options["apiKey"])) {
        ?>
        <script>
            jQuery(document).ready(function($) {
                const sidebarMenu = $('#toplevel_page_jotform_ai_chatbot > ul');
                const settingsTabMenu = sidebarMenu?.find('a[href="admin.php?page=jotform_ai_chatbot_settings"]');
                const settingsAdminBarMenuItem = $('#wp-admin-bar-jotform_ai_chatbot_settings');

                // Hide sidebar submenu 
                $(settingsTabMenu).hide();
                // Hide admin bar submenu
                $(settingsAdminBarMenuItem).hide();
            });
        </script>
        <?php
    }
}

add_action('admin_footer', 'jaic_hide_submenus');

/**
 * Enqueue scripts for the deactivate modal
 *
 * @param string $hook The current admin page hook.
 */
function jaic_deactivate_modal_scripts($hook) {
    if ($hook !== 'plugins.php') {
        return;
    }

    wp_enqueue_style('jaic-deactivate-modal', JAIC_PLUGIN_URL . 'lib/css/jaic-deactivate-modal.css', [], JAIC_PLUGIN_VERSION);
    wp_enqueue_script('jaic-deactivate-modal', JAIC_PLUGIN_URL . 'lib/jaic-deactivate-modal.js', [], JAIC_PLUGIN_VERSION, true);

    // Localize script to pass plugin slug
    wp_localize_script('jaic-deactivate-modal', 'jaicPluginData', [
        'pluginSlug' => dirname(plugin_basename(__FILE__))
    ]);
}
add_action('admin_enqueue_scripts', 'jaic_deactivate_modal_scripts');

/**
 * Display the deactivate modal
 *
 * @return void
 */
function jaic_deactivate_modal() {
    $formURL = "https://submit.jotform.com/submit/252104898587975";
    $plugin_file = JAIC_PLUGIN_DIR . '/jotform-ai-chatbot.php';
    $plugin_data = get_file_data($plugin_file, [
        'Version' => 'Version'
    ]);
    $current_version = $plugin_data['Version'] ?? '-';
    $current_user = wp_get_current_user();
    $current_user_email = esc_attr($current_user->user_email ?? '');
    ?>
    <div class="jaic_modal" style="display:none;">
    <div class="jaic_modal_inner">
        <div class="jaic_modal_content">
            <iframe name="jaic_hidden_iframe" style="display:none;" id="jaic_hidden_iframe"></iframe>
            <h2 class="jaic_title">😞 We’re sorry to see you go</h2>
            <p class="jaic_subtext">Help us understand why you’re deactivating. Your feedback makes us better.</p>
            <form id="jaic_deactivate_form" action="<?php echo esc_url($formURL); ?>" method="post" target="jaic_hidden_iframe">
                <input type="hidden" name="q3_domain" value="<?php echo esc_attr(wp_parse_url(home_url(), PHP_URL_HOST)); ?>">
                <input type="hidden" name="q7_version" value="<?php echo esc_attr($current_version); ?>">
                <label for="jaic_email" class="jaic_input_label">
                    Email
                    <span>(optional)</span>
                </label>
                <p class="jaic_input_subtext">
                    Please provide your email address so we may contact you if needed.
                </p>
                <div id='jaic_email_wrapper'>
                    <input type='text' id='jaic_email' class='jaic-text-input' name='q10_email' placeholder='Email' value="<?php echo esc_attr($current_user_email); ?>" />
                </div>
                <h3 class="jaic_subtitle">Why are you leaving?</h3>
                <?php
                $reasons = [
                    "only_testing" => "I was only testing the plugin",
                    "no_longer_needed" => "I no longer need the plugin",
                    "temporarily_deactivated" => "I temporarily deactivated it",
                    "not_working" => "I couldn’t get it to work",
                    "published_not_visible" => "I published it, but it didn’t appear on my site",
                    "sign_up_issue" => "I couldn’t sign up / log in",
                    "woocommerce_issue" => "I have an issue with WooCommerce integration",
                    "performance" => "It affected my site’s performance",
                    "missing_features" => "It doesn’t have the features I need",
                    "better_alternative" => "I found a better alternative",
                    "other" => "Other"
                ];
                foreach ($reasons as $value => $label) {
                    $escaped_label = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
                    echo "<div class='jaic_option' data-id='" . esc_attr($value) . "'>
                            <div class='checkmark'>
                                <input type='radio' name='q4_feedback' value='" . esc_attr($escaped_label) . "' id='jaic_" . esc_attr($value) . "'>
                                <div class='checkmark-inner'></div>
                            </div>
                            <label for='jaic_" . esc_attr($value) . "'>" . esc_html($escaped_label) . "</label>
                        </div>";
                }
                ?>

                <div id="jaic_detail_text_wrapper" style="display:none;">
                    <input type="text" name="q5_detail" id="jaic_detail_text" class="jaic-text-input" placeholder="" aria-required="false">
                </div>

                <div class="jaic_buttons">
                    <button type="button" class="jaic secondary">Continue to use</button>
                    <button type="submit" class="jaic primary disabled">
                        <span class="jaic_text">Submit & Deactivate</span>
                        <div class="jaic_loader"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
    <?php
}
add_action('admin_footer-plugins.php', 'jaic_deactivate_modal');

/**
 * Initialize Plugin Settings
 *
 * Registers plugin settings and settings sections.
 */
function jotform_ai_chatbot_initialize_plugin($action) {
    // Construct the API endpoint URL to initialize settings on Jotform side
    $url = "https://api.jotform.com/ai-chatbot/installment";
    $domain = rawurlencode(wp_parse_url(home_url(), PHP_URL_HOST));

    // Payload
    $payload = [
        "platform" => "wordpress",
        "domain"   => $domain,
        "action"   => $action . "_V2"
    ];

    // Request params
    $args = [
        "method"    => "POST",
        "body"      => wp_json_encode($payload),
        "headers"   => [
            "Content-Type" => "application/json"
        ]
    ];

    // Add the API Key if already generated
    $options = get_option("jotform_ai_chatbot_options");
    $options = !empty($options) ? json_decode($options, true) : [];
    if (isset($options["apiKey"]) && !empty($options["apiKey"])) {
        $args["headers"]["APIKEY"] = $options["apiKey"];
    }

    // Make the request
    wp_remote_request($url, $args);
}

/**
 * Hook into plugin activation to initialize the Jotform AI Chatbot plugin.
 *
 * This function checks if the currently activated plugin is this plugin itself.
 * If so, it triggers the plugin initialization logic with the 'activated' status.
 *
 * @param string $plugin The path to the plugin being activated.
 */
function jaic_jotform_ai_plugin_activation($plugin) {
    if ($plugin === plugin_basename(__FILE__)) {
        jotform_ai_chatbot_initialize_plugin('activated');
    }
}
add_action('activated_plugin', 'jaic_jotform_ai_plugin_activation');

/**
 * Hook into plugin deactivation to handle cleanup or state changes for the Jotform AI Chatbot plugin.
 *
 * This function checks if the currently deactivated plugin is this plugin itself.
 * If so, it triggers the plugin deinitialization logic with the 'deactivated' status.
 *
 * @param string $plugin The path to the plugin being deactivated.
 */
function jaic_jotform_ai_plugin_deactivation($plugin) {
    if ($plugin === plugin_basename(__FILE__)) {
        jotform_ai_chatbot_initialize_plugin('deactivated');
        wp_clear_scheduled_hook('jotform_ai_chatbot_cron_hook');
    }
}
add_action('deactivated_plugin', 'jaic_jotform_ai_plugin_deactivation');

/**
 * Hook into plugin uninstallation to perform final cleanup for the Jotform AI Chatbot plugin.
 *
 * This function checks if the plugin being uninstalled is this plugin.
 * If so, it triggers the plugin cleanup logic with the 'uninstalled' status.
 *
 * @param string $plugin The path to the plugin being uninstalled.
 */
function jaic_jotform_ai_plugin_uninstallation($plugin) {
    if ($plugin === plugin_basename(__FILE__)) {
        jotform_ai_chatbot_initialize_plugin('uninstalled');
        wp_clear_scheduled_hook('jotform_ai_chatbot_cron_hook');
    }
}
register_uninstall_hook(__FILE__, 'jaic_jotform_ai_plugin_uninstallation');

/**
 * Hook into plugin update to handle update-specific logic for the Jotform AI Chatbot plugin.
 *
 * This function listens for plugin update actions and checks if this plugin is among those being updated.
 * If so, it triggers the plugin initialization logic with the 'updated' status.
 *
 * @param WP_Upgrader $upgrader_object The upgrader class handling the update process.
 * @param array $options Array of update options, including 'action', 'type', and 'plugins'.
 */
function jaic_jotform_ai_plugin_updating($upgrader_object, $options) {
    if ($options['action'] === 'update' && $options['type'] === 'plugin') {
        $plugin_basename = plugin_basename(__FILE__);
        foreach ($options['plugins'] as $plugin) {
            if ($plugin === $plugin_basename) {
                jotform_ai_chatbot_initialize_plugin('updated');
                break;
            }
        }
    }
}
add_action('upgrader_process_complete', 'jaic_jotform_ai_plugin_updating', 10, 2);

/**
 * Initialize plugin settings for the Jotform AI Chatbot plugin.
 *
 * - Adds a permission check on `wp_loaded` to restrict access to administrators.
 * - Registers the plugin settings with a custom sanitization callback.
 * - Adds a settings section to the plugin's settings page in the WordPress admin.
 */
function jotform_ai_chatbot_plugin_settings_init() {
    add_action("wp_loaded", function () {
        if (!current_user_can("manage_options")) {
            wp_die(esc_html(__("You do not have sufficient permissions to access this page.", "jotform-ai-chatbot")));
        }
    });

    register_setting(
        'jotform_ai_chatbot',
        'jotform_ai_chatbot_options',
        [
            'type'              => 'array',
            'sanitize_callback' => 'jotform_ai_chatbot_sanitize_options',
        ]
    );
    add_settings_section(
        "",
        esc_html(__("Jotform AI Chatbot", "jotform-ai-chatbot")),
        "jotform_ai_chatbot_developers_callback",
        "jotform_ai_chatbot",
        [
            "before_section" => "<div class=\"jfpChatbot-plugin-section\">",
            "after_section" => "</div>"
        ]
    );
}
add_action("admin_init", "jotform_ai_chatbot_plugin_settings_init");

/**
 * Sanitize Plugin Options
 *
 * Sanitizes the plugin options to ensure that only valid values are stored in the database.
 *
 * @param array|string $input The input value to sanitize.
 * @return array|string The sanitized input value.
 */
function jotform_ai_chatbot_sanitize_options($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitize_text_field($value);
        }
    } else {
        $input = sanitize_text_field($input);
    }
    return $input;
}

/**
 * Render Plugin
 *
 * Displays the plugin's settings page in the admin dashboard.
 */
function jotform_ai_chatbot_render_plugin() {
    global $jaic_core;
    $jaic_core->createKnowledgeBase();
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div><?php do_settings_sections("jotform_ai_chatbot"); ?></div>
    </div>
    <?php
}

/**
 * Displays a visual indicator for plugin preview mode.
 *
 * This function checks if the plugin is enabled via URL query parameters or session variables.
 * If enabled, it adds custom styles and a notification bar at the top of the page.
 */
function jotform_ai_chatbot_show_preview_indicator() {
    global $jaic_core;

    if ($jaic_core->isPreviewMode()) {
        wp_enqueue_style(
            "preview-mode-style",
            JAIC_PLUGIN_URL . "lib/css/preview.css",
            [],
            JAIC_PLUGIN_VERSION
        );

        echo "<div class=\"plugin_preview_indicator_container\">";
        echo esc_html(__("You are in Jotform AI Chatbot Preview Mode.", "jotform-ai-chatbot"));
        echo "</div>";
    }
}
add_action("wp_head", "jotform_ai_chatbot_show_preview_indicator");

/**
 * Defines the `jotform_ai_chatbot_show_plugin` function to display the plugin.
 * Uses the global `$jaic_core` object to call the `renderChatbot()` method and outputs the plugin's HTML content.
 * Hooks the `jotform_ai_chatbot_show_plugin` function into the `wp_footer` action to ensure the plugin is added to the footer of the webpage.
 * The `wp_footer` action is triggered just before the closing </body> tag in a theme's template, making it a suitable place for rendering the plugin.
 */
function jotform_ai_chatbot_show_plugin() {
    try {
        global $jaic_core;
        $jaic_core->renderChatbot();
    } catch (\Exception $e) {
    }
}
add_action("wp_footer", "jotform_ai_chatbot_show_plugin");

// Hook the function to register plugin
function jotform_ai_chatbot_register_plugin() {
    try {
        // Include required files for handling core functionality
        require_once JAIC_PLUGIN_DIR . "/classes/JAIC_Core.php";

        // Initialize the JAIC_Core object for managing base functionalities.
        global $jaic_core;
        $jaic_core = new JAIC\Classes\JAIC_Core([
            "checkUserRegion" => true
        ]);
    } catch (\Exception $e) {
    }
}
add_action("plugins_loaded", "jotform_ai_chatbot_register_plugin");

// Hook the function to add custom links.
function jaic_my_plugin_action_links($links) {
    $learnMoreLink = '<a href="https://link.jotform.com/utP7pEtJfP" target="_blank">Learn More</a>';
    $helpLink  = '<a href="https://link.jotform.com/gKacs8I9pG" target="_blank">Help</a>';
    $giveFeedbackLink = '<a href="https://link.jotform.com/ElmhVHf4uh?domainField=' . rawurlencode(wp_parse_url(home_url(), PHP_URL_HOST)) . '&versionField=' . JAIC_PLUGIN_VERSION . '" target="_blank">Give Feedback</a>';
    array_unshift($links, $helpLink, $learnMoreLink, $giveFeedbackLink);

    // Check if plugin is active
    if (is_plugin_active('jotform-ai-chatbot/jotform-ai-chatbot.php')) {
        // Get plugin options
        $options = get_option('jotform_ai_chatbot_options');
        $options = !empty($options) ? json_decode($options, true) : [];

        // Determine link text based on the presence of ai chatbot
        $link_text = (!empty($options["agentId"])) ? 'My AI Chatbot' : 'Create AI Chatbot';

        // Build the new link with custom color and URL
        $dashboard_link = '<a href="' . admin_url("admin.php?page=jotform_ai_chatbot") . '" style="color: #FF6100; font-weight: bold;">' . esc_html($link_text) . '</a>';

        // Add the new link to the beginning of the links array
        array_unshift($links, $dashboard_link);
    }

    return $links;
}
add_filter('plugin_action_links_jotform-ai-chatbot/jotform-ai-chatbot.php', 'jaic_my_plugin_action_links');

/**
 * Handles the update of a WordPress page by adding it to the pending sync queue.
 *
 * This function is triggered when a page is saved and checks if the post is published.
 * If the post is published, it adds the page to the pending sync queue.
 *
 * @param int     $post_ID The ID of the page being saved.
 * @param WP_Post $post    The WP_Post object for the page.
 * @param bool    $update  Whether this is an existing post being updated.
 */
function jotform_ai_chatbot_handle_post_update($post_ID, $post, $update) {
    // Ignore autosaves or revisions
    if (wp_is_post_autosave($post_ID) || wp_is_post_revision($post_ID)) {
        return;
    }

    // Only for post and page
    if (!in_array($post->post_type, ['post', 'page'], true)) {
        return;
    }

    // Skip if not published
    if ($post->post_status !== 'publish') {
        return;
    }

    require_once JAIC_PLUGIN_DIR . "/classes/JAIC_Core.php";

    global $jaic_core;

    if (!isset($jaic_core) || !($jaic_core instanceof \JAIC\Classes\JAIC_Core)) {
        $jaic_core = new \JAIC\Classes\JAIC_Core([
            'checkUserRegion' => true,
        ]);
    }

    $jaic_core->handlePostUpdate($post_ID, $post, $update);
}
add_action('save_post_page', 'jotform_ai_chatbot_handle_post_update', 10, 3);
add_action('save_post_post', 'jotform_ai_chatbot_handle_post_update', 10, 3);

/**
 * Syncs pages to the knowledge base using a cron job.
 *
 * This function is triggered hourly via a scheduled event.
 * It checks for pending page updates and syncs them to the knowledge base.
 *
 * @global $jaic_core The JAIC_Core object for managing core functionalities.
 */
function jotform_ai_chatbot_cron_sync_pages() {
    require_once JAIC_PLUGIN_DIR . "/classes/JAIC_Core.php";

    global $jaic_core;

    if (!isset($jaic_core) || !($jaic_core instanceof \JAIC\Classes\JAIC_Core)) {
        $jaic_core = new \JAIC\Classes\JAIC_Core([
            'checkUserRegion' => true,
        ]);
    }

    $jaic_core->handleCronSyncPages();
}

/**
 * Schedules the cron job for syncing pages to the knowledge base.
 *
 * This function checks if the cron job is already scheduled.
 * If not, it schedules the cron job to run hourly.
 */
function jotform_ai_chatbot_schedule_cron() {
    if (!wp_next_scheduled('jotform_ai_chatbot_cron_hook')) {
        wp_schedule_event(time(), 'hourly', 'jotform_ai_chatbot_cron_hook');
    }
}
add_action('wp', 'jotform_ai_chatbot_schedule_cron');
add_action('jotform_ai_chatbot_cron_hook', 'jotform_ai_chatbot_cron_sync_pages');
