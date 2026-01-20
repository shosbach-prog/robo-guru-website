<?php
/**
 * Plugin Name: BuddyBoss Sharing
 * Plugin URI: https://buddyboss.com
 * Description: Adds share buttons across your site to let members easily share content to social media platforms.
 * Version: 1.1.1
 * Author: BuddyBoss
 * Author URI: https://buddyboss.com
 * Text Domain: buddyboss-sharing
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Requires Plugins: buddyboss-platform
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package BuddyBoss_Sharing
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('SHARE_EDITION', 'all');

// Define plugin constants.
define( 'BUDDYBOSS_SHARING_VERSION', '1.1.1' );
define( 'BUDDYBOSS_SHARING_PLUGIN_FILE', __FILE__ );
define( 'BUDDYBOSS_SHARING_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BUDDYBOSS_SHARING_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BUDDYBOSS_SHARING_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Require Composer autoloader.
if ( file_exists( BUDDYBOSS_SHARING_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once BUDDYBOSS_SHARING_PLUGIN_DIR . 'vendor/autoload.php';
}

/**
 * Check if sharing features should be locked based on DRM/license status.
 *
 * This is the recommended way to check if features should be disabled.
 * It respects the grace period (21 days) before actually locking features.
 *
 * @since 1.1.0
 *
 * @return bool True if features should be locked (disabled), false otherwise.
 */
function bb_sharing_should_lock_features() {
	// Check if License Manager is available.
	if ( ! class_exists( '\BuddyBoss\Sharing\Core\License_Manager' ) ) {
		return false; // If License Manager not available, don't lock features.
	}

	$license_manager = \BuddyBoss\Sharing\Core\License_Manager::instance();
	return $license_manager->should_lock_features();
}

/**
 * Check if the license is valid.
 *
 * @since 1.1.0
 *
 * @return bool True if license is valid, false otherwise.
 */
function bb_sharing_is_license_valid() {
	// Check if License Manager is available.
	if ( ! class_exists( '\BuddyBoss\Sharing\Core\License_Manager' ) ) {
		return false;
	}

	$license_manager = \BuddyBoss\Sharing\Core\License_Manager::instance();
	return $license_manager->is_license_valid();
}

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
final class BuddyBoss_Sharing {

	/**
	 * The single instance of the class.
	 *
	 * @var BuddyBoss_Sharing
	 */
	protected static $instance = null;

	/**
	 * Main BuddyBoss_Sharing Instance.
	 *
	 * Ensures only one instance of BuddyBoss_Sharing is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return BuddyBoss_Sharing - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'check_dependencies' ), 5 );
		add_action( 'plugins_loaded', array( $this, 'init' ), 10 );
		add_action( 'init', array( $this, 'load_textdomain' ) );

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Check dependencies before initializing.
	 *
	 * @since 1.0.0
	 */
	public function check_dependencies() {
		$missing = \BuddyBoss\Sharing\Core\Dependency_Checker::check_dependencies();

		if ( ! empty( $missing ) ) {
			// Show admin notice.
			add_action( 'admin_notices', array( '\BuddyBoss\Sharing\Core\Dependency_Checker', 'show_dependency_notice' ) );

			// Prevent plugin initialization.
			remove_action( 'plugins_loaded', array( $this, 'init' ), 10 );
			return;
		}
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Initialize License Manager first (required for all functionality).
		\BuddyBoss\Sharing\Core\License_Manager::instance();

		// Register addon with DRM system (only if Platform's DRM is available).
		$this->register_with_drm();

		// Initialize admin (always load to show settings, even when locked).
		if ( is_admin() ) {
			\BuddyBoss\Sharing\Admin\Admin::instance();
			\BuddyBoss\Sharing\Admin\Activity_Settings::instance();
		}

		// Check if features should be locked due to DRM.
		if ( bb_sharing_should_lock_features() ) {
			// Features are locked - don't load functional modules.
			return;
		}

		// Initialize BB_OpenGraph class.
		if ( class_exists( 'BB_OpenGraph' ) ) {
			\BB_OpenGraph::get_instance();
		} else {
			// Fallback to our Site SEO module.
			\BuddyBoss\Sharing\Modules\Site_SEO::instance();
		}

		// Initialize Enhanced Activity Sharing module.
		\BuddyBoss\Sharing\Modules\Activity_Sharing_Enhanced::instance();

		// Initialize Activity Open Graph Tags.
		\BuddyBoss\Sharing\Modules\Activity_OpenGraph::instance();

		// Initialize Group Open Graph Tags.
		\BuddyBoss\Sharing\Modules\Group_OpenGraph::instance();

		// Initialize Member Open Graph Tags.
		\BuddyBoss\Sharing\Modules\Member_OpenGraph::instance();

		// Initialize Login Page Open Graph Tags.
		\BuddyBoss\Sharing\Modules\Login_OpenGraph::instance();

		// Initialize Plugin Compatibility (prevent conflicts with other SEO plugins).
		\BuddyBoss\Sharing\Modules\Plugin_Compatibility::instance();

		// Initialize REST API.
		\BuddyBoss\Sharing\API\Link_Preview::instance();

		// Initialize frontend.
		\BuddyBoss\Sharing\Frontend\Frontend::instance();

		do_action( 'buddyboss_sharing_init' );
	}

	/**
	 * Register addon with Platform's DRM system.
	 *
	 * @since 1.1.0
	 */
	private function register_with_drm() {
		// Check if Platform's DRM Registry is available.
		if ( ! class_exists( '\BuddyBoss\Core\Admin\DRM\BB_DRM_Registry' ) ) {
			return;
		}

		// Register with DRM system.
		\BuddyBoss\Core\Admin\DRM\BB_DRM_Registry::register_addon(
			'buddyboss-sharing',
			'BuddyBoss Sharing',
			array(
				'version' => BUDDYBOSS_SHARING_VERSION,
				'file'    => BUDDYBOSS_SHARING_PLUGIN_FILE,
			)
		);
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'buddyboss-sharing',
			false,
			dirname( BUDDYBOSS_SHARING_PLUGIN_BASENAME ) . '/languages/'
		);
	}

	/**
	 * Plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function activate() {
		// Check version requirements before activating.
		$missing = \BuddyBoss\Sharing\Core\Dependency_Checker::check_dependencies();

		if ( ! empty( $missing ) ) {
			// Deactivate the plugin.
			deactivate_plugins( plugin_basename( __FILE__ ) );

			// Build error message without any translation functions.
			$message  = '<h1 style="margin-bottom: 10px;">Plugin Activation Error</h1>';
			$message .= '<p style="font-size: 16px; margin-bottom: 20px;"><strong>BuddyBoss Sharing could not be activated.</strong></p>';

			foreach ( $missing as $dependency ) {
				$message .= '<p>' . $dependency['description'] . '</p>';

				if ( isset( $dependency['required_version'] ) ) {
					$message .= '<p>Please update BuddyBoss Platform to version <strong>' . $dependency['required_version'] . '</strong> or higher before activating this plugin.</p>';
				}
			}

			$message .= '<p><a href="' . admin_url( 'plugins.php' ) . '" style="text-decoration: none;">&larr; Go back</a></p>';

			// Use wp_die to properly stop activation.
			wp_die(
				$message,
				'Plugin Activation Error',
				array( 'back_link' => true )
			);
		}

		// Set default options.
		$this->set_default_options();

		// Flush rewrite rules.
		flush_rewrite_rules();

		do_action( 'buddyboss_sharing_activate' );
	}

	/**
	 * Plugin deactivation.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		// Flush rewrite rules.
		flush_rewrite_rules();

		do_action( 'buddyboss_sharing_deactivate' );
	}

	/**
	 * Set default options.
	 *
	 * @since 1.0.0
	 */
	private function set_default_options() {
		// Activity Sharing defaults - set individual options to match admin settings.
		if ( false === bp_get_option( 'buddyboss_enable_activity_sharing' ) ) {
			bp_update_option( 'buddyboss_enable_activity_sharing', 1 );
		}
		if ( false === bp_get_option( 'buddyboss_activity_sharing_custom_message' ) ) {
			bp_update_option( 'buddyboss_activity_sharing_custom_message', 1 );
		}
		if ( false === bp_get_option( 'buddyboss_activity_sharing_to_groups' ) ) {
			bp_update_option( 'buddyboss_activity_sharing_to_groups', 1 );
		}
		if ( false === bp_get_option( 'buddyboss_activity_sharing_to_friends' ) ) {
			bp_update_option( 'buddyboss_activity_sharing_to_friends', 1 );
		}
		if ( false === bp_get_option( 'buddyboss_activity_sharing_to_message' ) ) {
			bp_update_option( 'buddyboss_activity_sharing_to_message', 1 );
		}
		if ( false === bp_get_option( 'buddyboss_activity_sharing_as_link' ) ) {
			bp_update_option( 'buddyboss_activity_sharing_as_link', 1 );
		}
		if ( false === bp_get_option( 'buddyboss_activity_sharing_link_platforms' ) ) {
			bp_update_option( 'buddyboss_activity_sharing_link_platforms', array( 'messenger', 'whatsapp', 'facebook', 'twitter', 'linkedin' ) );
		}
	}
}

/**
 * Returns the main instance of BuddyBoss_Sharing.
 *
 * @since 1.0.0
 * @return BuddyBoss_Sharing
 */
function buddyboss_sharing() {
	return BuddyBoss_Sharing::instance();
}

// Initialize the plugin.
buddyboss_sharing();
