<?php
/**
 * Frontend functionality.
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Frontend;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend class.
 *
 * @since 1.0.0
 */
class Frontend {

	/**
	 * The single instance of the class.
	 *
	 * @var Frontend
	 */
	protected static $instance = null;

	/**
	 * Main Frontend Instance.
	 *
	 * @since 1.0.0
	 * @return Frontend
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
		add_action( 'wp_footer', array( $this, 'render_share_modal' ) );
		add_action( 'bp_before_activity_loop', array( $this, 'check_single_activity_access' ) );
		
		// Allow social media crawlers to access activity pages for OG tags.
		// Priority 1 to ensure this runs before BuddyPress blocks access.
		add_filter( 'bp_private_network_pre_check', array( $this, 'allow_social_crawlers_for_activity' ), 1, 1 );
	}

	/**
	 * Enqueue frontend scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_frontend_scripts() {
		// Check license validity first.
		if ( ! \BuddyBoss\Sharing\Core\License_Manager::instance()->can_use_sharing() ) {
			return;
		}

		// Load on BuddyBoss activity pages, group activity feed pages, and messages pages (for shared activity cards).
		$is_activity_page = function_exists( 'bp_is_activity_component' ) && bp_is_activity_component();
		$is_group_activity_page = function_exists( 'bp_is_group_activity' ) && bp_is_group_activity();
		$is_messages_page = function_exists( 'bp_is_messages_component' ) && bp_is_messages_component();

		if ( ! $is_activity_page && ! $is_group_activity_page && ! $is_messages_page ) {
			return;
		}

		$enable_sharing = bp_get_option( 'buddyboss_enable_activity_sharing', 1 );

		if ( ! $enable_sharing ) {
			return;
		}

		// Use the unminified style in debug mode.
		$css_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'frontend.css' : 'frontend.min.css';

		// Enqueue regular frontend styles.
		$css_file = is_rtl() ? 'frontend-rtl.min.css' : $css_file;
		wp_enqueue_style(
			'buddyboss-sharing-frontend',
			BUDDYBOSS_SHARING_PLUGIN_URL . 'assets/css/' . $css_file,
			array(),
			BUDDYBOSS_SHARING_VERSION
		);

		// Load ReadyLaunch-specific styles if ReadyLaunch is enabled.
		if ( function_exists( 'bb_is_readylaunch_enabled' ) && bb_is_readylaunch_enabled() ) {
			$min       = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			$rtl_css   = is_rtl() ? '-rtl' : '';
			$rl_css_file = 'bb-rl-frontend' . $rtl_css . $min . '.css';
			wp_enqueue_style(
				'buddyboss-sharing-rl-frontend',
				BUDDYBOSS_SHARING_PLUGIN_URL . 'assets/css/' . $rl_css_file,
				array( 'buddyboss-sharing-frontend' ),
				BUDDYBOSS_SHARING_VERSION
			);
		}

		// Use ReadyLaunch script if ReadyLaunch is enabled.
		if ( function_exists( 'bb_is_readylaunch_enabled' ) && bb_is_readylaunch_enabled() ) {
			$js_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'assets/js/src/bb-rl-activity-sharing.js' : 'assets/js/bb-rl-activity-sharing.min.js';
		} else {
			$js_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'assets/js/src/activity-sharing.js' : 'assets/js/activity-sharing.min.js';
		}

		// Enqueue scripts.
		wp_enqueue_script(
			'buddyboss-sharing-activity-sharing',
			BUDDYBOSS_SHARING_PLUGIN_URL . $js_file,
			array( 'jquery', 'wp-i18n' ),
			BUDDYBOSS_SHARING_VERSION,
			true
		);

		// Set script translations.
		wp_set_script_translations(
			'buddyboss-sharing-activity-sharing',
			'buddyboss-sharing',
			BUDDYBOSS_SHARING_PLUGIN_DIR . 'languages'
		);

		// Get individual settings.
		$enable_custom_msg = bp_get_option( 'buddyboss_activity_sharing_custom_message', 1 );
		$share_to_groups   = bp_get_option( 'buddyboss_activity_sharing_to_groups', 1 );
		$share_to_friends  = bp_get_option( 'buddyboss_activity_sharing_to_friends', 1 );
		$share_to_message  = bp_get_option( 'buddyboss_activity_sharing_to_message', 1 );
		$share_as_link     = bp_get_option( 'buddyboss_activity_sharing_as_link', 1 );
		$link_platforms    = bp_get_option( 'buddyboss_activity_sharing_link_platforms', array( 'messenger', 'whatsapp', 'facebook', 'twitter', 'linkedin' ) );

		// Localize script.
		wp_localize_script(
			'buddyboss-sharing-activity-sharing',
			'buddybossSharingFrontend',
			array(
				'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
				'nonce'             => wp_create_nonce( 'buddyboss_seo_share' ),
				'enableCustomMsg'   => (bool) $enable_custom_msg,
				'shareToGroups'     => (bool) $share_to_groups,
				'shareToFriends'    => (bool) $share_to_friends,
				'shareToMessage'    => (bool) $share_to_message,
				'shareAsLink'       => (bool) $share_as_link,
				'linkPlatforms'     => $link_platforms,
				'i18n'              => array(
					'share'                => esc_html__( 'Share', 'buddyboss-sharing' ),
					'shareActivity'        => esc_html__( 'Share Activity', 'buddyboss-sharing' ),
					'sharedSuccess'        => esc_html__( 'Activity shared successfully!', 'buddyboss-sharing' ),
					'error'                => esc_html__( 'An error occurred. Please try again.', 'buddyboss-sharing' ),
					'loading'              => esc_html__( 'Loading...', 'buddyboss-sharing' ),
					'failedLoadModal'      => esc_html__( 'Failed to load message modal.', 'buddyboss-sharing' ),
					'failedLoadActivity'   => esc_html__( 'Failed to load activity content.', 'buddyboss-sharing' ),
					'selectTarget'         => esc_html__( 'Please select a target.', 'buddyboss-sharing' ),
					'selectRecipient'      => esc_html__( 'Please select at least one recipient.', 'buddyboss-sharing' ),
					'failedSendMessage'    => esc_html__( 'Failed to send message.', 'buddyboss-sharing' ),
				),
			)
		);
	}

	/**
	 * Render share modal.
	 *
	 * @since 1.0.0
	 */
	public function render_share_modal() {
		// Check license validity first.
		if ( ! \BuddyBoss\Sharing\Core\License_Manager::instance()->can_use_sharing() ) {
			return;
		}

		// Only load on BuddyBoss activity pages and group activity pages.
		$is_activity_page = function_exists( 'bp_is_activity_component' ) && bp_is_activity_component();
		$is_group_activity_page = function_exists( 'bp_is_group_activity' ) && bp_is_group_activity();

		if ( ! $is_activity_page && ! $is_group_activity_page ) {
			return;
		}

		$enable_sharing = bp_get_option( 'buddyboss_enable_activity_sharing', 1 );

		if ( ! $enable_sharing ) {
			return;
		}

		$modal_template = BUDDYBOSS_SHARING_PLUGIN_DIR . 'templates/activity-sharing/share-modal.php';
		if ( file_exists( $modal_template ) ) {
			include $modal_template;
		}

	}

	/**
	 * Check if user has access to view single activity.
	 * If not, display "no access" message.
	 *
	 * @since 1.0.0
	 */
	public function check_single_activity_access() {
		// Only run on single activity pages.
		if ( ! function_exists( 'bp_is_single_activity' ) || ! bp_is_single_activity() ) {
			return;
		}

		// Get activity ID from bp_current_action (on single activity pages, this returns the activity ID).
		$activity_id = function_exists( 'bp_current_action' ) ? bp_current_action() : 0;

		if ( ! $activity_id || ! is_numeric( $activity_id ) ) {
			return;
		}

		// Load activity.
		if ( ! class_exists( 'BP_Activity_Activity' ) ) {
			return;
		}

		$activity = new \BP_Activity_Activity( $activity_id );

		if ( empty( $activity->id ) ) {
			return;
		}

		// Check if bb_validate_activity_privacy function exists.
		if ( ! function_exists( 'bb_validate_activity_privacy' ) ) {
			return;
		}

		// Validate activity privacy.
		$current_user_id = get_current_user_id();
		$privacy_check   = bb_validate_activity_privacy(
			array(
				'activity_id'     => $activity_id,
				'user_id'         => $current_user_id,
				'validate_action' => 'view_activity',
				'activity_type'   => 'activity',
			)
		);

		// If privacy check returns WP_Error, user doesn't have access.
		if ( is_wp_error( $privacy_check ) ) {
			// Remove the activity loop to prevent displaying the activity.
			remove_action( 'bp_before_activity_loop', 'bp_nouveau_activity_hook' );
			remove_action( 'bp_after_activity_loop', 'bp_nouveau_after_activity_loop' );

			// Display no-access template.
			$no_access_template = BUDDYBOSS_SHARING_PLUGIN_DIR . 'templates/activity-sharing/no-access.php';
			if ( file_exists( $no_access_template ) ) {
				add_action(
					'bp_before_activity_loop',
					function() use ( $no_access_template ) {
						include $no_access_template;
						// Prevent activity from displaying.
						echo '<style>.activity-list { display: none !important; }</style>';
					},
					999
				);
			}
		}
	}

	/**
	 * Allow social media crawlers to access activity pages when site is private.
	 * This enables Facebook, Twitter, LinkedIn, etc. to fetch OG tags for sharing.
	 *
	 * @since 1.0.0
	 * @param bool $pre_check Current pre-check value.
	 * @return bool True to bypass private network restriction, false otherwise.
	 */
	public function allow_social_crawlers_for_activity( $pre_check ) {
		// Check if this is a social media crawler first.
		if ( ! $this->is_social_media_crawler() ) {
			return $pre_check;
		}

		// Check if we're on an activity page by checking the URL pattern.
		// Activity pages typically have pattern: /members/{username}/activity/{id}/
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		
		if ( empty( $request_uri ) ) {
			return $pre_check;
		}

		// Check if URL matches activity page pattern.
		// Pattern: /members/*/activity/*/ or /activity/*/
		$is_activity_page = (
			preg_match( '#/members/[^/]+/activity/\d+/?#', $request_uri ) ||
			preg_match( '#/activity/\d+/?#', $request_uri )
		);

		if ( ! $is_activity_page ) {
			return $pre_check;
		}

		// Allow crawler to access the page for OG tags.
		// Log for debugging.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'BuddyBoss Sharing: Allowing social crawler to access activity page: ' . $request_uri );
		}

		return true;
	}

	/**
	 * Check if the current request is from a social media crawler.
	 *
	 * @since 1.0.0
	 * @return bool True if crawler, false otherwise.
	 */
	private function is_social_media_crawler() {
		// Get user agent.
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		if ( empty( $user_agent ) ) {
			return false;
		}

		// List of social media crawler user agents.
		$crawler_agents = array(
			// Facebook crawlers.
			'facebookexternalhit',
			'Facebot',
			'facebook',
			// Twitter crawlers.
			'Twitterbot',
			'twitterbot',
			// LinkedIn crawlers.
			'LinkedInBot',
			'linkedinbot',
			// WhatsApp crawlers.
			'WhatsApp',
			'whatsapp',
			// Pinterest crawlers.
			'Pinterest',
			'pinterest',
			// Slack crawlers.
			'Slackbot',
			'slackbot',
			// Discord crawlers.
			'Discordbot',
			'discordbot',
			// Generic social media crawlers.
			'Googlebot',
			'googlebot',
			'bingbot',
			'Bingbot',
		);

		// Check if user agent matches any crawler.
		foreach ( $crawler_agents as $agent ) {
			if ( false !== stripos( $user_agent, $agent ) ) {
				// Log for debugging (can be removed in production).
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'BuddyBoss Sharing: Allowing social crawler: ' . $user_agent );
				}
				return true;
			}
		}

		return false;
	}
}
