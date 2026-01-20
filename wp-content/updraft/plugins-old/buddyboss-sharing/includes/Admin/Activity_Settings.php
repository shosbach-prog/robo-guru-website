<?php
/**
 * Activity Settings Integration.
 *
 * Integrates with BuddyBoss Platform Settings > Activity page.
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activity_Settings class.
 *
 * @since 1.0.0
 */
class Activity_Settings {

	/**
	 * The single instance of the class.
	 *
	 * @var Activity_Settings
	 */
	protected static $instance = null;

	/**
	 * Main Activity_Settings Instance.
	 *
	 * @since 1.0.0
	 * @return Activity_Settings
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
		// Add settings section to BuddyBoss Activity settings.
		add_action( 'bp_admin_setting_activity_register_fields', array( $this, 'register_activity_sharing_settings' ), 10, 1 );
	}

	/**
	 * Register activity sharing settings.
	 *
	 * @since 1.0.0
	 * @param object $settings_tab The settings tab object.
	 */
	public function register_activity_sharing_settings( $settings_tab ) {
		// Get pro class and notice for all fields.
		$pro_class  = $this->get_pro_fields_class();
		$pro_notice = $this->get_pro_label_notice();

		// Add Activity Sharing section.
		$settings_tab->add_section(
			'buddyboss_activity_sharing',
			esc_html__( 'Activity Sharing', 'buddyboss-sharing' ),
			array( $this, 'activity_sharing_section_callback' ),
			array( $this, 'activity_sharing_tutorial_callback' )
		);

		// Enable sharing.
		$settings_tab->add_field(
			'buddyboss_enable_activity_sharing',
			esc_html__( 'Enable sharing', 'buddyboss-sharing' ) . $pro_notice,
			array( $this, 'enable_sharing_callback' ),
			'intval',
			array(
				'class'  => $pro_class,
				'notice' => $pro_notice,
			),
			'buddyboss_activity_sharing'
		);

		// Custom message.
		$settings_tab->add_field(
			'buddyboss_activity_sharing_custom_message',
			esc_html__( 'Custom message', 'buddyboss-sharing' ) . $pro_notice,
			array( $this, 'custom_message_callback' ),
			'intval',
			array(
				'class'  => $pro_class,
				'notice' => $pro_notice,
			),
			'buddyboss_activity_sharing'
		);

		// Share to groups.
		if ( bp_is_active( 'groups' ) ) {
			$settings_tab->add_field(
				'buddyboss_activity_sharing_to_groups',
				esc_html__( 'Share to groups', 'buddyboss-sharing' ) . $pro_notice,
				array( $this, 'share_to_groups_callback' ),
				'intval',
				array(
					'class'  => $pro_class,
					'notice' => $pro_notice,
				),
				'buddyboss_activity_sharing'
			);
		}

		// Share to friends profile.
		if ( bp_is_active( 'friends' ) ) {
			$settings_tab->add_field(
				'buddyboss_activity_sharing_to_friends',
				esc_html__( "Share to friend's profile", 'buddyboss-sharing' ) . $pro_notice,
				array( $this, 'share_to_friends_callback' ),
				'intval',
				array(
					'class'  => $pro_class,
					'notice' => $pro_notice,
				),
				'buddyboss_activity_sharing'
			);
		}

		// Share to message.
		if ( bp_is_active( 'messages' ) ) {
			$settings_tab->add_field(
				'buddyboss_activity_sharing_to_message',
				esc_html__( 'Share to message', 'buddyboss-sharing' ) . $pro_notice,
				array( $this, 'share_to_message_callback' ),
				'intval',
				array(
					'class'  => $pro_class,
					'notice' => $pro_notice,
				),
				'buddyboss_activity_sharing'
			);
		}

		// Share as a link.
		$settings_tab->add_field(
			'buddyboss_activity_sharing_as_link',
			esc_html__( 'Share as a link', 'buddyboss-sharing' ) . $pro_notice,
			array( $this, 'share_as_link_callback' ),
			'intval',
			array(
				'class'  => $pro_class,
				'notice' => $pro_notice,
			),
			'buddyboss_activity_sharing'
		);

		// Share link platforms.
		$settings_tab->add_field(
			'buddyboss_activity_sharing_link_platforms',
			esc_html__( 'Select where to share as a link', 'buddyboss-sharing' ) . $pro_notice,
			array( $this, 'share_link_platforms_callback' ),
			array( $this, 'sanitize_platforms' ),
			array(
				'class'  => $pro_class,
				'notice' => $pro_notice,
			),
			'buddyboss_activity_sharing'
		);
	}

	/**
	 * Activity sharing section callback.
	 *
	 * @since 1.0.0
	 */
	public function activity_sharing_section_callback() {
		?>
		<p class="description">
			<?php esc_html_e( 'Configure activity sharing options for your members.', 'buddyboss-sharing' ); ?>
		</p>
		<?php
	}

	/**
	 * Activity sharing tutorial callback.
	 *
	 * @since 1.0.0
	 */
	public function activity_sharing_tutorial_callback() {
		?>
		<p>
			<a class="button" href="https://www.buddyboss.com/docs/how-to-enable-and-use-activity-post-sharing-in-buddyboss/" target="_blank">
				<?php esc_html_e( 'View Tutorial', 'buddyboss-sharing' ); ?>
			</a>
		</p>
		<?php
	}

	/**
	 * Enable sharing callback.
	 *
	 * @since 1.0.0
	 */
	public function enable_sharing_callback() {
		$value      = bp_get_option( 'buddyboss_enable_activity_sharing', 1 );
		$pro_notice = $this->get_pro_label_notice();
		$is_locked  = ! empty( $pro_notice );
		?>
		<input type="checkbox" name="<?php echo $is_locked ? '' : 'buddyboss_enable_activity_sharing'; ?>" id="buddyboss_enable_activity_sharing" value="1" <?php echo $is_locked ? '' : checked( $value, 1, false ); ?> />
		<label for="buddyboss_enable_activity_sharing">
			<?php esc_html_e( 'Allow members to share the activity posts', 'buddyboss-sharing' ); ?>
		</label>
		<?php
	}

	/**
	 * Custom message callback.
	 *
	 * @since 1.0.0
	 */
	public function custom_message_callback() {
		$value      = bp_get_option( 'buddyboss_activity_sharing_custom_message', 1 );
		$pro_notice = $this->get_pro_label_notice();
		$is_locked  = ! empty( $pro_notice );
		?>
		<input type="checkbox" name="<?php echo $is_locked ? '' : 'buddyboss_activity_sharing_custom_message'; ?>" id="buddyboss_activity_sharing_custom_message" value="1" <?php echo $is_locked ? '' : checked( $value, 1, false ); ?> />
		<label for="buddyboss_activity_sharing_custom_message">
			<?php esc_html_e( 'Allow members to add a custom message while sharing', 'buddyboss-sharing' ); ?>
		</label>
		<?php
	}

	/**
	 * Share to groups callback.
	 *
	 * @since 1.0.0
	 */
	public function share_to_groups_callback() {
		$value      = bp_get_option( 'buddyboss_activity_sharing_to_groups', 1 );
		$pro_notice = $this->get_pro_label_notice();
		$is_locked  = ! empty( $pro_notice );
		?>
		<input type="checkbox" name="<?php echo $is_locked ? '' : 'buddyboss_activity_sharing_to_groups'; ?>" id="buddyboss_activity_sharing_to_groups" value="1" <?php echo $is_locked ? '' : checked( $value, 1, false ); ?> />
		<label for="buddyboss_activity_sharing_to_groups">
			<?php esc_html_e( 'Allow members to share public posts in their groups', 'buddyboss-sharing' ); ?>
		</label>
		<?php
	}

	/**
	 * Share to friends callback.
	 *
	 * @since 1.0.0
	 */
	public function share_to_friends_callback() {
		$value      = bp_get_option( 'buddyboss_activity_sharing_to_friends', 1 );
		$pro_notice = $this->get_pro_label_notice();
		$is_locked  = ! empty( $pro_notice );
		?>
		<input type="checkbox" name="<?php echo $is_locked ? '' : 'buddyboss_activity_sharing_to_friends'; ?>" id="buddyboss_activity_sharing_to_friends" value="1" <?php echo $is_locked ? '' : checked( $value, 1, false ); ?> />
		<label for="buddyboss_activity_sharing_to_friends">
			<?php esc_html_e( "Allow members to share public posts to their friend's profiles", 'buddyboss-sharing' ); ?>
		</label>
		<?php
	}

	/**
	 * Share to message callback.
	 *
	 * @since 1.0.0
	 */
	public function share_to_message_callback() {
		$value      = bp_get_option( 'buddyboss_activity_sharing_to_message', 1 );
		$pro_notice = $this->get_pro_label_notice();
		$is_locked  = ! empty( $pro_notice );
		?>
		<input type="checkbox" name="<?php echo $is_locked ? '' : 'buddyboss_activity_sharing_to_message'; ?>" id="buddyboss_activity_sharing_to_message" value="1" <?php echo $is_locked ? '' : checked( $value, 1, false ); ?> />
		<label for="buddyboss_activity_sharing_to_message">
			<?php esc_html_e( 'Allow members to send via direct message', 'buddyboss-sharing' ); ?>
		</label>
		<?php
	}

	/**
	 * Share as link callback.
	 *
	 * @since 1.0.0
	 */
	public function share_as_link_callback() {
		$value      = bp_get_option( 'buddyboss_activity_sharing_as_link', 1 );
		$pro_notice = $this->get_pro_label_notice();
		$is_locked  = ! empty( $pro_notice );
		?>
		<input type="checkbox" name="<?php echo $is_locked ? '' : 'buddyboss_activity_sharing_as_link'; ?>" id="buddyboss_activity_sharing_as_link" value="1" <?php echo $is_locked ? '' : checked( $value, 1, false ); ?> />
		<label for="buddyboss_activity_sharing_as_link">
			<?php esc_html_e( 'Allow member to share public post as a link', 'buddyboss-sharing' ); ?>
		</label>
		<?php
	}

	/**
	 * Share link platforms callback.
	 *
	 * @since 1.0.0
	 */
	public function share_link_platforms_callback() {
		$platforms = array(
			'messenger' => esc_html__( 'Messenger', 'buddyboss-sharing' ),
			'whatsapp'  => esc_html__( 'Whatsapp', 'buddyboss-sharing' ),
			'facebook'  => esc_html__( 'Facebook', 'buddyboss-sharing' ),
			'twitter'   => esc_html__( 'X', 'buddyboss-sharing' ),
			'linkedin'  => esc_html__( 'LinkedIn', 'buddyboss-sharing' ),
		);

		$selected   = bp_get_option( 'buddyboss_activity_sharing_link_platforms', array_keys( $platforms ) );
		$pro_notice = $this->get_pro_label_notice();
		$is_locked  = ! empty( $pro_notice );

		foreach ( $platforms as $key => $label ) {
			$checked = in_array( $key, $selected, true ) ? 'checked' : '';
			?>
			<label style="display: block; margin-bottom: 8px;">
				<input type="checkbox" name="<?php echo $is_locked ? '' : 'buddyboss_activity_sharing_link_platforms[]'; ?>" value="<?php echo esc_attr( $key ); ?>" <?php echo $is_locked ? '' : esc_attr( $checked ); ?> />
				<?php echo esc_html( $label ); ?>
			</label>
			<?php
		}
	}

	/**
	 * Sanitize platforms.
	 *
	 * @since 1.0.0
	 * @param array $value Platforms value.
	 * @return array
	 */
	public function sanitize_platforms( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$allowed = array( 'messenger', 'whatsapp', 'facebook', 'twitter', 'linkedin' );
		return array_intersect( $value, $allowed );
	}

	/**
	 * Get pro label notice for sharing features.
	 *
	 * Shows upgrade/install message when license is invalid or locked.
	 *
	 * @since 1.0.0
	 * @return string HTML notice or empty string.
	 */
	private function get_pro_label_notice() {
		static $notice = null;

		if ( null !== $notice ) {
			return $notice;
		}

		// Check if features are locked due to DRM.
		$is_locked = $this->is_features_locked();

		if ( ! $is_locked ) {
			$notice = '';
			return $notice;
		}

		// Features are locked - show appropriate notice.
		$license_valid = \BuddyBoss\Sharing\Core\License_Manager::instance()->is_license_valid();

		if ( $license_valid ) {
			// License is valid but features locked (shouldn't happen, but just in case).
			$notice = sprintf(
				'<br/><span class="bb-head-notice"> %1$s <a href="%2$s">%3$s</a></span>',
				esc_html__( 'Features locked.', 'buddyboss-sharing' ),
				bp_get_admin_url( 'admin.php?page=buddyboss-license' ),
				esc_html__( 'Check license activation', 'buddyboss-sharing' )
			);
		} else {
			// License is invalid - show upgrade notice.
			$notice = sprintf(
				'<br/><span class="bb-head-notice"> %1$s <a target="_blank" href="https://www.buddyboss.com/bbwebupgrade">%2$s</a> %3$s</span>',
				esc_html__( 'Upgrade', 'buddyboss-sharing' ),
				esc_html__( 'Pro', 'buddyboss-sharing' ),
				esc_html__( 'to unlock', 'buddyboss-sharing' )
			);
		}

		return $notice;
	}

	/**
	 * Get pro fields CSS class.
	 *
	 * Returns 'bb-pro-inactive' when features are locked, 'bb-pro-active' when unlocked.
	 *
	 * @since 1.0.0
	 * @return string CSS class.
	 */
	private function get_pro_fields_class() {
		static $class = null;

		if ( null !== $class ) {
			return $class;
		}

		// Check if features are locked.
		if ( $this->is_features_locked() ) {
			$class = 'bb-pro-inactive';
		} else {
			$class = 'bb-pro-active';
		}

		return $class;
	}

	/**
	 * Check if features are locked due to DRM or an invalid license.
	 *
	 * @since 1.1.0
     *
	 * @return bool True if features are locked.
	 */
	private function is_features_locked() {
		// Check if DRM Registry is available.
		if ( class_exists( '\BuddyBoss\Core\Admin\DRM\BB_DRM_Registry' ) ) {
			// Use DRM system to check if features should be locked.
			return \BuddyBoss\Core\Admin\DRM\BB_DRM_Registry::should_lock_addon_features( 'buddyboss-sharing' );
		}

		// Fallback to license manager if DRM not available.
		return ! \BuddyBoss\Sharing\Core\License_Manager::instance()->is_license_valid();
	}
}
