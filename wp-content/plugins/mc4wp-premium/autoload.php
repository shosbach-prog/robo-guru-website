<?php

spl_autoload_register(function ($class) {
    static $classmap = [
        MC4WP\User_Sync\Admin::class => '/user-sync/src/class-admin.php',
        MC4WP\User_Sync\Ajax_Listener::class => '/user-sync/src/class-ajax-listener.php',
        MC4WP\User_Sync\CLI_Command::class => '/user-sync/src/class-cli-command.php',
        MC4WP\User_Sync\Observer::class => '/user-sync/src/class-observer.php',
        MC4WP\User_Sync\Schedule::class => '/user-sync/src/class-schedule.php',
        MC4WP\User_Sync\Tools::class => '/user-sync/src/class-tools.php',
        MC4WP\User_Sync\User_Handler::class => '/user-sync/src/class-user-handler.php',
        MC4WP\User_Sync\Users::class => '/user-sync/src/class-users.php',
        MC4WP\User_Sync\Webhook_Listener::class => '/user-sync/src/class-webhook-listener.php',
        MC4WP\User_Sync\Worker::class => '/user-sync/src/class-worker.php',

        'MC4WP\\Licensing\\Admin' => '/licensing/class-admin.php',
        'MC4WP\\Licensing\\ApiException' => '/licensing/class-api-exception.php',
        'MC4WP\\Licensing\\Client' => '/licensing/class-api-client.php',
        'MC4WP\\PostCampaign\\Gutenberg_Editor' => '/post-campaign/includes/class-gutenberg-editor.php',
        'MC4WP\\PostCampaign\\Post_Mailchimp_Campaign' => '/post-campaign/includes/class-post-mailchimp-campaign.php',
        'MC4WP_AJAX_Forms' => '/ajax-forms/includes/class-ajax-forms.php',
        'MC4WP_AJAX_Forms_Admin' => '/ajax-forms/includes/class-admin.php',
        'MC4WP_Custom_Color_Theme' => '/custom-color-theme/includes/class-theme.php',
        'MC4WP_Custom_Color_Theme_Admin' => '/custom-color-theme/includes/class-admin.php',
        'MC4WP_Dashboard_Log_Widget' => '/logging/includes/class-dashboard-log-widget.php',
        'MC4WP_Email_Notification' => '/email-notifications/includes/class-email-notification.php',
        'MC4WP_Form_Notification_Factory' => '/email-notifications/includes/class-factory.php',
        'MC4WP_Form_Notifications_Admin' => '/email-notifications/includes/class-admin.php',
        'MC4WP_Form_Widget_Enhancements' => '/multiple-forms/includes/class-widget-enhancements.php',
        'MC4WP_Forms_Table' => '/multiple-forms/includes/class-forms-table.php',
        'MC4WP_Graph' => '/logging/includes/class-graph.php',
        'MC4WP_Log_Exporter' => '/logging/includes/class-log-exporter.php',
        'MC4WP_Log_Item' => '/logging/includes/class-log-item.php',
        'MC4WP_Log_Table' => '/logging/includes/class-log-table.php',
        'MC4WP_Logger' => '/logging/includes/class-logger.php',
        'MC4WP_Logging_Admin' => '/logging/includes/class-admin.php',
        'MC4WP_Logging_Installer' => '/logging/includes/class-installer.php',
        'MC4WP_Multiple_Forms_Admin' => '/multiple-forms/includes/class-admin.php',
        'MC4WP_RGB_Color' => '/custom-color-theme/includes/class-rgb-color.php',
        'MC4WP_Required_Plugins_Notice' => '/includes/class-required-plugins-notice.php',
        'MC4WP_Styles_Builder' => '/styles-builder/includes/class-styles-builder.php',
        'MC4WP_Styles_Builder_Admin' => '/styles-builder/includes/class-admin.php',
        'MC4WP_Styles_Builder_Public' => '/styles-builder/includes/class-public.php',
    ];

    if (isset($classmap[$class])) {
        require __DIR__ . $classmap[$class];
    }
});
