<?php
/**
 * Activity Sharing Module.
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Modules;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activity Sharing class.
 *
 * @since 1.0.0
 */
class Activity_Sharing {

	/**
	 * The single instance of the class.
	 *
	 * @var Activity_Sharing
	 */
	protected static $instance = null;

	/**
	 * Main Activity_Sharing Instance.
	 *
	 * @since 1.0.0
	 * @return Activity_Sharing
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
		// Only initialize if BuddyPress/BuddyBoss is active.
		if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) ) {
			return;
		}

		$enable_sharing = bp_get_option( 'buddyboss_enable_activity_sharing', 1 );

		if ( $enable_sharing ) {
			add_action( 'bp_activity_entry_meta', array( $this, 'add_share_button' ) );
			add_action( 'wp_ajax_buddyboss_share_activity', array( $this, 'ajax_share_activity' ) );
			add_action( 'wp_ajax_buddyboss_get_share_modal_content', array( $this, 'ajax_get_share_modal_content' ) );
		}
	}

	/**
	 * Add share button to activity items.
	 *
	 * @since 1.0.0
	 */
	public function add_share_button() {
		$activity_id = bp_get_activity_id();

		// Only show for public activities.
		if ( 'public' !== bp_get_activity_privacy() ) {
			return;
		}

		?>
		<button
			class="buddyboss-share-btn"
			data-activity-id="<?php echo esc_attr( $activity_id ); ?>"
			aria-label="<?php esc_attr_e( 'Share', 'buddyboss-sharing' ); ?>"
		>
			<span class="bp-screen-reader-text"><?php esc_html_e( 'Share', 'buddyboss-sharing' ); ?></span>
			<i class="bb-icon-l bb-icon-share"></i>
			<?php esc_html_e( 'Share', 'buddyboss-sharing' ); ?>
		</button>
		<?php
	}

	/**
	 * AJAX handler for sharing activity.
	 *
	 * @since 1.0.0
	 */
	public function ajax_share_activity() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		$activity_id   = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;
		$share_type    = isset( $_POST['share_type'] ) ? sanitize_text_field( wp_unslash( $_POST['share_type'] ) ) : '';
		$share_target  = isset( $_POST['share_target'] ) ? sanitize_text_field( wp_unslash( $_POST['share_target'] ) ) : '';
		$custom_message = isset( $_POST['custom_message'] ) ? wp_kses_post( wp_unslash( $_POST['custom_message'] ) ) : '';

		if ( ! $activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid activity.', 'buddyboss-sharing' ) ) );
		}

		// Process sharing based on type.
		$result = $this->process_share( $activity_id, $share_type, $share_target, $custom_message );

		if ( $result ) {
			// Update share count.
			$share_count = (int) bp_activity_get_meta( $activity_id, 'share_count', true );
			bp_activity_update_meta( $activity_id, 'share_count', $share_count + 1 );

			wp_send_json_success( array(
				'message'     => esc_html__( 'Activity shared successfully!', 'buddyboss-sharing' ),
				'share_count' => $share_count + 1,
			) );
		} else {
			wp_send_json_error( array( 'message' => esc_html__( 'Failed to share activity.', 'buddyboss-sharing' ) ) );
		}
	}

	/**
	 * AJAX handler for getting share modal content.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_share_modal_content() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		$activity_id = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;
		$share_type  = isset( $_POST['share_type'] ) ? sanitize_text_field( wp_unslash( $_POST['share_type'] ) ) : '';

		if ( ! $activity_id ) {
			wp_send_json_error();
		}

		ob_start();
		include BUDDYBOSS_SHARING_PLUGIN_DIR . 'templates/activity-sharing/share-modal-content.php';
		$content = ob_get_clean();

		wp_send_json_success( array( 'content' => $content ) );
	}

	/**
	 * Process activity sharing.
	 *
	 * @since 1.0.0
	 * @param int    $activity_id Activity ID.
	 * @param string $share_type Share type.
	 * @param string $share_target Share target.
	 * @param string $custom_message Custom message.
	 * @return bool|int
	 */
	private function process_share( $activity_id, $share_type, $share_target, $custom_message ) {
		$original_activity = new \BP_Activity_Activity( $activity_id );

		if ( ! $original_activity->id ) {
			return false;
		}

		$content = $custom_message ? $custom_message . "\n\n" : '';
		$content .= sprintf(
			'<div class="shared-activity" data-activity-id="%d">%s</div>',
			$activity_id,
			$original_activity->content
		);

		$args = array(
			'user_id'   => get_current_user_id(),
			'content'   => $content,
			'type'      => 'activity_share',
			'item_id'   => $activity_id,
		);

		// Set component and item_id based on share type.
		switch ( $share_type ) {
			case 'feed':
				$args['component'] = 'activity';
				break;

			case 'group':
				if ( bp_get_option( 'buddyboss_activity_sharing_to_groups', 1 ) ) {
					$args['component'] = 'groups';
					$args['item_id']   = intval( $share_target );
				}
				break;

			case 'profile':
				if ( bp_get_option( 'buddyboss_activity_sharing_to_friends', 1 ) ) {
					$args['component']         = 'activity';
					$args['secondary_item_id'] = intval( $share_target );
				}
				break;

			case 'message':
				if ( bp_get_option( 'buddyboss_activity_sharing_to_message', 1 ) && function_exists( 'messages_new_message' ) ) {
					return messages_new_message( array(
						'sender_id'  => get_current_user_id(),
						'recipients' => array( intval( $share_target ) ),
						'subject'    => esc_html__( 'Shared Activity', 'buddyboss-sharing' ),
						'content'    => $content,
					) );
				}
				break;
		}

		return bp_activity_add( $args );
	}

	/**
	 * Get share platforms.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_share_platforms() {
		$platforms = array(
			'messenger' => array(
				'label' => esc_html__( 'Messenger', 'buddyboss-sharing' ),
				'icon'  => 'bb-icon-facebook-messenger',
			),
			'whatsapp' => array(
				'label' => esc_html__( 'WhatsApp', 'buddyboss-sharing' ),
				'icon'  => 'bb-icon-whatsapp',
			),
			'facebook' => array(
				'label' => esc_html__( 'Facebook', 'buddyboss-sharing' ),
				'icon'  => 'bb-icon-facebook',
			),
			'twitter' => array(
				'label' => esc_html__( 'X', 'buddyboss-sharing' ),
				'icon'  => 'bb-icon-twitter',
			),
			'linkedin' => array(
				'label' => esc_html__( 'LinkedIn', 'buddyboss-sharing' ),
				'icon'  => 'bb-icon-linkedin',
			),
		);

		$enabled_platforms = bp_get_option( 'buddyboss_activity_sharing_link_platforms', array( 'messenger', 'whatsapp', 'facebook', 'twitter', 'linkedin' ) );

		return array_intersect_key( $platforms, array_flip( $enabled_platforms ) );
	}
}
