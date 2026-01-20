<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit(0);
}

// Check if the uninstall process is triggered by WordPress.
// This ensures the code runs only when the plugin is deleted via the WordPress admin interface.
if (!defined("WP_UNINSTALL_PLUGIN")) {
    exit(0); // Exit if accessed directly to prevent unauthorized access.
}

// Delete the plugin's stored options from the WordPress database.
// In this case, it removes the plugin option to clean up the database.
// Require necessary files
require_once __DIR__ . "/classes/JAIC_Core.php";

// Initialize the JAIC_Core object for managing base requests.
$jaic_core = new JAIC\Classes\JAIC_Core([
    "checkUserRegion" => true
]);

// Call the delete method from the JAIC_Core class to handle cleanup operations.
// This may include deleting plugin-related data from the WordPress database or other services.
$jaic_core->delete(true);
