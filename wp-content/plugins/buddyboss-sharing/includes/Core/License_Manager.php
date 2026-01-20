<?php
/**
 * License Manager for BuddyBoss Sharing.
 *
 * Handles license validation with multiple verification layers to prevent cracking.
 *
 * @package BuddyBoss\Sharing\Core
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * License_Manager class.
 *
 * Multi-layer license validation system with anti-tampering measures.
 *
 * @since 1.0.0
 */
class License_Manager {

	/**
	 * The single instance of the class.
	 *
	 * @var License_Manager
	 */
	protected static $instance = null;

	/**
	 * Cached validation result.
	 *
	 * @var bool|null
	 */
	private $validation_cache = null;

	/**
	 * Validation hash for integrity check.
	 *
	 * @var string|null
	 */
	private $validation_hash = null;

	/**
	 * Main License_Manager Instance.
	 *
	 * @since 1.0.0
	 * @return License_Manager
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
		// Initialize validation on construct.
		$this->initialize_validation();
	}

	/**
	 * Initialize validation system.
	 *
	 * @since 1.0.0
	 */
	private function initialize_validation() {
		// Set up integrity hash.
		$this->validation_hash = $this->generate_integrity_hash();
	}

	/**
	 * Generate integrity hash for anti-tampering.
	 *
	 * @since 1.0.0
	 * @return string Integrity hash.
	 */
	private function generate_integrity_hash() {
		$components = array(
			defined( 'BUDDYBOSS_SHARING_VERSION' ) ? BUDDYBOSS_SHARING_VERSION : '',
			defined( 'BUDDYBOSS_SHARING_PLUGIN_FILE' ) ? BUDDYBOSS_SHARING_PLUGIN_FILE : '',
			get_option( 'siteurl' ),
			wp_salt( 'auth' ),
		);

		return hash_hmac( 'sha256', implode( '|', $components ), wp_salt( 'nonce' ) );
	}

	/**
	 * Verify integrity hash.
	 *
	 * @since 1.0.0
	 * @return bool True if integrity check passes.
	 */
	private function verify_integrity() {
		if ( empty( $this->validation_hash ) ) {
			return false;
		}

		$current_hash = $this->generate_integrity_hash();
		return hash_equals( $this->validation_hash, $current_hash );
	}

	/**
	 * Validate license using BuddyBoss Mothership classes.
	 *
	 * @since 1.0.0
	 * @return bool True if license is valid.
	 */
	private function validate_via_mothership() {
		// Check if Mothership connector exists.
		if ( ! class_exists( '\BuddyBoss\Core\Admin\Mothership\BB_Plugin_Connector' ) ) {
			return false;
		}

		try {
			// Get license activation status from database.
			$connector = new \BuddyBoss\Core\Admin\Mothership\BB_Plugin_Connector();
			$license_status = $connector->getLicenseActivationStatus();

			// Check if license is activated.
			if ( empty( $license_status ) ) {
				return false;
			}

			// Check if BB_Addons_Manager exists.
			if ( ! class_exists( '\BuddyBoss\Core\Admin\Mothership\BB_Addons_Manager' ) ) {
				return false;
			}

			// Check if either Platform Pro OR Sharing product is enabled.
			// User needs a license for at least one of these products.
			$has_platform_pro = \BuddyBoss\Core\Admin\Mothership\BB_Addons_Manager::checkProductBySlug( 'buddyboss-platform-pro' );
			$has_sharing = \BuddyBoss\Core\Admin\Mothership\BB_Addons_Manager::checkProductBySlug( 'buddyboss-sharing' );

			// Return true if either product is licensed.
			if ( $has_platform_pro || $has_sharing ) {
				return true;
			}
		} catch ( \Exception $e ) {
			// Silent fail for security.
			return false;
		}

		return false;
	}

	/**
	 * Check if running on staging server.
	 * Uses Platform Pro function if available, otherwise uses fallback logic.
	 *
	 * @since 1.0.0
	 * @return bool True if staging server.
	 */
	private function is_staging_server() {
		// Use Platform Pro function if available.
		if ( function_exists( 'bb_pro_check_staging_server' ) ) {
			return bb_pro_check_staging_server();
		}

		// Fallback: Use our own staging detection logic.
		return $this->check_staging_environment();
	}

	/**
	 * Fallback staging environment detection.
	 * Replicates bb_pro_check_staging_server() logic when Platform Pro is not available.
	 *
	 * @since 1.0.0
	 * @return bool True if staging environment detected.
	 */
	private function check_staging_environment() {
		$raw_domain = site_url();



		// Reserved hosting provider domains that indicate staging/development.
		$reserved_hosting_provider_domains = array(
			'accessdomain',
			'cloudwaysapps',
			'flywheelsites',
			'kinsta',
			'mybluehost',
			'myftpupload',
			'netsolhost',
			'pantheonsite',
			'sg-host',
			'wpengine',
			'wpenginepowered',
			'rapydapps.cloud',
		);

		// Reserved words that indicate testing/staging environments.
		$reserved_words = array(
			'dev',
			'develop',
			'development',
			'test',
			'testing',
			'stg',
			'stage',
			'staging',
			'demo',
			'sandbox',
			'preview',
		);

		// Reserved TLDs for local development.
		$reserved_tlds = array(
			'local',
			'localhost',
			'test',
			'example',
			'invalid',
			'dev',
			'staging',
		);

		// Known local development tool domains.
		$reserved_local_domains = array(
			'lndo.site',
			'ddev.site',
			'docksal',
			'localwp.com',
			'local.test',
			'docker.internal',
			'ngrok.io',
			'localtunnel.me',
		);

		// Parse the URL to get the host.
		$parsed_url = wp_parse_url( $raw_domain );
		$domain     = isset( $parsed_url['host'] ) ? $parsed_url['host'] : $raw_domain;

		// Remove www prefix if present.
		$domain = preg_replace( '/^www\./i', '', $domain );

		// Check if domain is localhost or an IP address.
		if ( 'localhost' === $domain || filter_var( $domain, FILTER_VALIDATE_IP ) ) {
			return true;
		}

		// Check for port numbers (often indicates local development).
		if ( isset( $parsed_url['port'] ) && ! in_array( $parsed_url['port'], array( 80, 443 ), true ) ) {
			return true;
		}

		// Extract domain parts.
		$domain_parts = explode( '.', $domain );
		$tld          = end( $domain_parts );

		// Check for reserved TLDs.
		if ( in_array( $tld, $reserved_tlds, true ) ) {
			return true;
		}

		// Check for reserved testing words in subdomains.
		$subdomain_pattern = '/(\.|-)(' . implode( '|', $reserved_words ) . ')(\.|-)|(^(' . implode( '|', $reserved_words ) . ')\.)/i';
		if ( preg_match( $subdomain_pattern, $domain ) ) {
			return true;
		}

		// Check for known hosting provider staging domains.
		$hosting_pattern = '/\.(' . implode( '|', $reserved_hosting_provider_domains ) . ')\./i';
		if ( preg_match( $hosting_pattern, '.' . $domain . '.' ) ) {
			return true;
		}

		// Check for known development tool domains.
		$dev_tools_pattern = '/(' . implode( '|', array_map( 'preg_quote', $reserved_local_domains ) ) . ')$/i';
		if ( preg_match( $dev_tools_pattern, $domain ) ) {
			return true;
		}

		// Check WordPress-specific staging indicators.
		if ( defined( 'WP_ENVIRONMENT_TYPE' ) && 'production' !== WP_ENVIRONMENT_TYPE ) {
			return true;
		}

		// Check for common WordPress staging constants.
		if ( defined( 'WP_STAGING' ) && WP_STAGING ) {
			return true;
		}

		return false;
	}

	/**
	 * Additional validation layer - Check license key format.
	 *
	 * @since 1.0.0
	 * @return bool True if license key format is valid.
	 */
	private function validate_license_key_format() {
		if ( ! class_exists( '\BuddyBoss\Core\Admin\Mothership\BB_Plugin_Connector' ) ) {
			return false;
		}

		try {
			$connector = new \BuddyBoss\Core\Admin\Mothership\BB_Plugin_Connector();
			$license_key = $connector->getLicenseKey();

			// Check if license key exists and has valid format.
			if ( empty( $license_key ) || strlen( $license_key ) < 32 ) {
				return false;
			}

			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Additional validation layer - Verify plugin files integrity.
	 *
	 * @since 1.0.0
	 * @return bool True if plugin files are intact.
	 */
	private function verify_plugin_files() {
		// Check critical files exist.
		$critical_files = array(
			BUDDYBOSS_SHARING_PLUGIN_DIR . 'buddyboss-sharing.php',
			BUDDYBOSS_SHARING_PLUGIN_DIR . 'includes/Core/License_Manager.php',
		);

		foreach ( $critical_files as $file ) {
			if ( ! file_exists( $file ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Additional validation layer - Check database options.
	 *
	 * @since 1.0.0
	 * @return bool True if database options are valid.
	 */
	private function validate_database_options() {
		// Verify buddyboss sharing option exists.
		$enable_sharing = bp_get_option( 'buddyboss_enable_activity_sharing', 1 );

		if ( is_null( $enable_sharing ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Multi-layer license validation.
	 *
	 * This method performs multiple validation checks to prevent cracking:
	 * 1. Integrity hash verification
	 * 2. Platform Pro existence check
	 * 3. Mothership license validation
	 * 4. License key format validation
	 * 5. Plugin files integrity check
	 * 6. Database options validation
	 *
	 * @since 1.0.0
	 * @param bool $bypass_cache Whether to bypass cache.
	 * @return bool True if license is valid.
	 */
	public function is_license_valid( $bypass_cache = false ) {
		// Return cached result if available and not bypassing.
		if ( ! $bypass_cache && ! is_null( $this->validation_cache ) ) {
			return $this->validation_cache;
		}

		// Layer 0: Integrity check.
		if ( ! $this->verify_integrity() ) {
			$this->validation_cache = false;
			return false;
		}

		// Layer 1: Staging server check (bypass for development).
		if ( $this->is_staging_server() ) {
			$this->validation_cache = true;
			return true;
		}

		// Layer 2: Mothership license validation (PRIMARY CHECK).
		// This validates via the Mothership API and checks for valid Platform Pro license.
		if ( ! $this->validate_via_mothership() ) {
			$this->validation_cache = false;
			return false;
		}

		// Layer 3: License key format validation.
		if ( ! $this->validate_license_key_format() ) {
			$this->validation_cache = false;
			return false;
		}

		// Layer 4: Plugin files integrity.
		if ( ! $this->verify_plugin_files() ) {
			$this->validation_cache = false;
			return false;
		}

		// Layer 5: Database options validation.
		if ( ! $this->validate_database_options() ) {
			$this->validation_cache = false;
			return false;
		}

		// All checks passed.
		$this->validation_cache = true;
		return true;
	}

	/**
	 * Check if sharing features should be enabled.
	 *
	 * During grace period (days 1-20), features remain enabled even without valid license.
	 * Only locks at day 21+.
	 *
	 * @since 1.0.0
	 * @return bool True if sharing should be enabled.
	 */
	public function can_use_sharing() {
		// Check if features are locked due to DRM.
		if ( $this->should_lock_features() ) {
			return false;
		}

		// Check if sharing is enabled in settings.
		$enable_sharing = bp_get_option( 'buddyboss_enable_activity_sharing', 1 );

		return (bool) $enable_sharing;
	}

	/**
	 * Check if OG tags should be rendered.
	 *
	 * During grace period (days 1-20), OG tags remain enabled even without valid license.
	 * Only locks at day 21+.
	 *
	 * @since 1.0.0
	 * @return bool True if OG tags should be rendered.
	 */
	public function can_render_og_tags() {
		return ! $this->should_lock_features();
	}

	/**
	 * Check if features should be locked due to DRM.
	 *
	 * @since 1.1.0
	 *
	 * @return bool True if features should be locked.
	 */
	public function should_lock_features() {
		// Check if DRM Registry is available.
		if ( class_exists( '\BuddyBoss\Core\Admin\DRM\BB_DRM_Registry' ) ) {
			// Use DRM system to check if features should be locked.
			return \BuddyBoss\Core\Admin\DRM\BB_DRM_Registry::should_lock_addon_features( 'buddyboss-sharing' );
		}

		// Fallback to license manager if DRM not available.
		return ! $this->is_license_valid();
	}

	/**
	 * Get license status message for admin.
	 *
	 * @since 1.0.0
	 * @return string License status message.
	 */
	public function get_license_status_message() {
		if ( $this->is_staging_server() ) {
			return __( 'Running on staging/development server - License check bypassed.', 'buddyboss-sharing' );
		}

		if ( ! $this->is_license_valid() ) {
			return __( 'Invalid or expired license. Please activate a valid BuddyBoss Pro license.', 'buddyboss-sharing' );
		}

		return __( 'License is valid and active.', 'buddyboss-sharing' );
	}

	/**
	 * Clear validation cache.
	 *
	 * @since 1.0.0
	 */
	public function clear_cache() {
		$this->validation_cache = null;
		$this->validation_hash = $this->generate_integrity_hash();
	}

	/**
	 * Get upgrade URL.
	 *
	 * @since 1.0.0
	 * @return string Upgrade URL.
	 */
	public function get_upgrade_url() {
		return 'https://www.buddyboss.com/bbwebupgrade';
	}

	/**
	 * Prevent cloning.
	 *
	 * @since 1.0.0
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton.' );
	}
}
