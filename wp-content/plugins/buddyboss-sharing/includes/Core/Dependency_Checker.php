<?php
/**
 * Dependency checker for BuddyBoss Platform.
 *
 * Checks if required dependencies are active and prevents plugin activation if not.
 *
 * @package BuddyBoss_Sharing
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dependency Checker class.
 *
 * @since 1.0.0
 */
class Dependency_Checker {

	/**
	 * Minimum required version of BuddyBoss Platform.
	 *
	 * @since 1.0.0
	 */
	const MINIMUM_PLATFORM_VERSION = '2.15.0';

	/**
	 * Check if BuddyBoss Platform is active.
	 *
	 * @since 1.0.0
	 * @return bool True if Platform is active.
	 */
	public static function is_buddyboss_platform_active() {
		return function_exists( 'buddypress' ) && function_exists( 'bp_is_active' );
	}

	/**
	 * Get BuddyBoss Platform version.
	 *
	 * @since 1.0.0
	 * @return string|false Platform version or false if not available.
	 */
	public static function get_platform_version() {
		if ( defined( 'BP_PLATFORM_VERSION' ) ) {
			return BP_PLATFORM_VERSION;
		}

		// Fallback: Try to get version from plugin file during activation.
		// This is needed because constants might not be loaded during activation hook.
		$platform_file = WP_PLUGIN_DIR . '/buddyboss-platform/bp-loader.php';

		if ( file_exists( $platform_file ) ) {
			// Get plugin data without activating it.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$plugin_data = get_plugin_data( $platform_file, false, false );

			if ( ! empty( $plugin_data['Version'] ) ) {
				return $plugin_data['Version'];
			}
		}

		return false;
	}

	/**
	 * Check if BuddyBoss Platform version meets minimum requirement.
	 *
	 * @since 1.0.0
	 * @return bool True if version is sufficient.
	 */
	public static function is_platform_version_sufficient() {
		$current_version = self::get_platform_version();

		if ( ! $current_version ) {
			return false;
		}

		return version_compare( $current_version, self::MINIMUM_PLATFORM_VERSION, '>=' );
	}

	/**
	 * Check if BuddyBoss Platform Pro is active.
	 *
	 * @since 1.0.0
	 * @return bool True if Platform Pro is active.
	 */
	public static function is_buddyboss_platform_pro_active() {
		return function_exists( 'bb_platform_pro' ) || class_exists( 'BB_Platform_Pro' );
	}

	/**
	 * Check all required dependencies.
	 *
	 * @since 1.0.0
	 * @return array Array of missing dependencies.
	 */
	public static function check_dependencies() {
		$missing = array();

		// Check for BuddyBoss Platform (REQUIRED).
		if ( ! self::is_buddyboss_platform_active() ) {
			$missing[] = array(
				'name'        => 'BuddyBoss Platform',
				'url'         => 'https://www.buddyboss.com/platform/',
				'required'    => true,
				'description' => 'BuddyBoss Sharing requires BuddyBoss Platform to function.',
			);
		} elseif ( ! self::is_platform_version_sufficient() ) {
			// Platform is active but version is insufficient.
			$current_version = self::get_platform_version();
			$missing[]       = array(
				'name'             => 'BuddyBoss Platform',
				'url'              => 'https://www.buddyboss.com/platform/',
				'required'         => true,
				'description'      => 'BuddyBoss Sharing requires BuddyBoss Platform version ' . self::MINIMUM_PLATFORM_VERSION . ' or higher. You are currently running version ' . ( $current_version ? $current_version : 'Unknown' ) . '.',
				'current_version'  => $current_version,
				'required_version' => self::MINIMUM_PLATFORM_VERSION,
			);
		}

		return $missing;
	}

	/**
	 * Display admin notice for missing dependencies.
	 *
	 * @since 1.0.0
	 */
	public static function show_dependency_notice() {
		$missing = self::check_dependencies();

		if ( empty( $missing ) ) {
			return;
		}

		foreach ( $missing as $dependency ) {
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php esc_html_e( 'BuddyBoss Sharing:', 'buddyboss-sharing' ); ?></strong>
					<?php echo esc_html( $dependency['description'] ); ?>
				</p>
				<p>
					<a href="<?php echo esc_url( $dependency['url'] ); ?>" class="button button-primary" target="_blank">
						<?php
						/* translators: %s: plugin name */
						echo esc_html( sprintf( __( 'Get %s', 'buddyboss-sharing' ), $dependency['name'] ) );
						?>
					</a>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Deactivate the plugin if dependencies are not met.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate_on_missing_dependencies() {
		// This method is deprecated - we now handle dependencies at runtime instead of during activation.
		// Keeping for backwards compatibility.
	}

	/**
	 * Show activation error notice.
	 *
	 * @since 1.0.0
	 */
	public static function show_activation_error() {
		$missing = self::check_dependencies();
		?>
		<div class="notice notice-error">
			<p>
				<strong><?php esc_html_e( 'BuddyBoss Sharing could not be activated.', 'buddyboss-sharing' ); ?></strong>
			</p>
			<?php if ( ! empty( $missing ) ) : ?>
				<?php foreach ( $missing as $dependency ) : ?>
					<p>
						<?php echo esc_html( $dependency['description'] ); ?>
					</p>
					<?php if ( isset( $dependency['required_version'] ) ) : ?>
						<p>
							<?php
							printf(
								/* translators: %s: URL to update platform */
								esc_html__( 'Please update BuddyBoss Platform to version %s or higher.', 'buddyboss-sharing' ),
								esc_html( $dependency['required_version'] )
							);
							?>
						</p>
					<?php endif; ?>
					<p>
						<a href="<?php echo esc_url( $dependency['url'] ); ?>" class="button button-primary" target="_blank">
							<?php
							if ( isset( $dependency['required_version'] ) ) {
								esc_html_e( 'Learn More About BuddyBoss Platform', 'buddyboss-sharing' );
							} else {
								printf(
									/* translators: %s: dependency name */
									esc_html__( 'Get %s', 'buddyboss-sharing' ),
									esc_html( $dependency['name'] )
								);
							}
							?>
						</a>
					</p>
				<?php endforeach; ?>
			<?php else : ?>
				<p>
					<?php esc_html_e( 'BuddyBoss Platform must be installed and activated before you can use BuddyBoss Sharing.', 'buddyboss-sharing' ); ?>
				</p>
				<p>
					<a href="https://www.buddyboss.com/platform/" class="button button-primary" target="_blank">
						<?php esc_html_e( 'Get BuddyBoss Platform', 'buddyboss-sharing' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}
}
