<?php
/**
 * Enhanced Activity Sharing Module.
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
 * Activity_Sharing_Enhanced class.
 *
 * @since 1.0.0
 */
class Activity_Sharing_Enhanced {

	/**
	 * The single instance of the class.
	 *
	 * @var Activity_Sharing_Enhanced
	 */
	protected static $instance = null;

	/**
	 * Current message ID for AJAX rendering context.
	 *
	 * @var int
	 */
	private $current_message_id = 0;

	/**
	 * HTML generator instance.
	 *
	 * @var \BuddyBoss\Sharing\Modules\Generators\Activity_HTML_Generator
	 */
	private $html_generator = null;

	/**
	 * Main Activity_Sharing_Enhanced Instance.
	 *
	 * @since 1.0.0
	 * @return Activity_Sharing_Enhanced
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
	 * Get HTML generator instance.
	 *
	 * Returns the appropriate generator based on ReadyLaunch status.
	 *
	 * @since 1.0.0
	 * @return \BuddyBoss\Sharing\Modules\Generators\Activity_HTML_Generator
	 */
	private function get_html_generator() {
		if ( null === $this->html_generator ) {
			// Load base class first.
			$base_file = BUDDYBOSS_SHARING_PLUGIN_DIR . 'includes/Modules/generators/Activity_HTML_Generator_Base.php';
			if ( file_exists( $base_file ) ) {
				require_once $base_file;
			}

			// Check if ReadyLaunch is enabled.
			if ( function_exists( 'bb_is_readylaunch_enabled' ) && bb_is_readylaunch_enabled() ) {
				// Load ReadyLaunch generator.
				$generator_file = BUDDYBOSS_SHARING_PLUGIN_DIR . 'includes/Modules/generators/readylaunch/Activity_HTML_Generator.php';
				if ( file_exists( $generator_file ) ) {
					require_once $generator_file;
					$this->html_generator = new \BuddyBoss\Sharing\Modules\Generators\Activity_HTML_Generator_RL();
				} else {
					// Fallback to regular generator if ReadyLaunch version doesn't exist.
					$generator_file = BUDDYBOSS_SHARING_PLUGIN_DIR . 'includes/Modules/generators/Activity_HTML_Generator.php';
					require_once $generator_file;
					$this->html_generator = new \BuddyBoss\Sharing\Modules\Generators\Activity_HTML_Generator();
				}
			} else {
				// Load regular generator.
				$generator_file = BUDDYBOSS_SHARING_PLUGIN_DIR . 'includes/Modules/generators/Activity_HTML_Generator.php';
				require_once $generator_file;
				$this->html_generator = new \BuddyBoss\Sharing\Modules\Generators\Activity_HTML_Generator();
			}
		}

		return $this->html_generator;
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

		$enable_sharing = get_option( 'buddyboss_enable_activity_sharing', 1 );

		if ( $enable_sharing ) {
			// Register custom activity action
			add_action( 'bp_register_activity_actions', array( $this, 'register_activity_actions' ) );

			add_filter( 'bp_nouveau_get_activity_entry_buttons', array( $this, 'add_share_button' ), 10, 2 );
			add_action( 'wp_ajax_buddyboss_share_activity', array( $this, 'ajax_share_activity' ) );
			add_action( 'wp_ajax_buddyboss_get_share_modal_content', array( $this, 'ajax_get_share_modal_content' ) );
			add_action( 'wp_ajax_buddyboss_get_activity_content', array( $this, 'ajax_get_activity_content' ) );
			add_action( 'wp_ajax_buddyboss_share_to_feed', array( $this, 'ajax_share_to_feed' ) );
			add_action( 'wp_ajax_buddyboss_share_to_message', array( $this, 'ajax_share_to_message' ) );
			add_action( 'wp_ajax_buddyboss_search_members_for_message', array( $this, 'ajax_search_members_for_message' ) );
			add_action( 'wp_ajax_buddyboss_get_recent_message_threads', array( $this, 'ajax_get_recent_message_threads' ) );
			add_action( 'wp_ajax_buddyboss_get_groups_for_sharing', array( $this, 'ajax_get_groups_for_sharing' ) );
			add_action( 'wp_ajax_buddyboss_share_to_group', array( $this, 'ajax_share_to_group' ) );
            add_action( 'wp_ajax_buddyboss_get_friends_for_sharing', array( $this, 'ajax_get_friends_for_sharing' ) );
            add_action( 'wp_ajax_buddyboss_share_to_friend_profile', array( $this, 'ajax_share_to_friend_profile' ) );
            add_action( 'wp_ajax_buddyboss_get_activity_permalink', array( $this, 'ajax_get_activity_permalink' ) );
            add_action( 'wp_ajax_buddyboss_load_shared_activity_modal', array( $this, 'ajax_load_shared_activity_modal' ) );
            add_action( 'wp_ajax_nopriv_buddyboss_load_shared_activity_modal', array( $this, 'ajax_load_shared_activity_modal' ) );
            add_action( 'wp_ajax_buddyboss_get_activity_post_form', array( $this, 'ajax_get_activity_post_form' ) );
			add_filter( 'bp_activity_allowed_tags', array( $this, 'allow_shared_activity_html' ) );
			add_action( 'wp_footer', array( $this, 'render_share_templates' ) );

			// Dynamic rendering: Intercept activity content display for shared activities
			add_filter( 'bp_get_activity_content_body', array( $this, 'render_shared_activity_content' ), 10, 2 );

			add_action( 'messages_message_sent', array( $this, 'bb_share_messages_save_activity_data' ) );

			// Disable WordPress embeds in message content (hook into bp_init to ensure BuddyPress is loaded)
			add_action( 'bp_init', array( $this, 'disable_message_embeds' ), 999 );

			// Dynamic rendering: Intercept message content display for shared activities
			// Priority 5: Early hook to capture message context for AJAX responses
			add_filter( 'bp_get_the_thread_message_content', array( $this, 'set_message_context_for_ajax' ), 5 );
			// Priority 20: Main rendering hook
			add_filter( 'bp_get_the_thread_message_content', array( $this, 'render_shared_message_content' ), 20 );
			// Priority 30: Generate link previews from URLs in message content
			add_filter( 'bp_get_the_thread_message_content', array( $this, 'add_link_preview_to_message_content' ), 30 );

			// Add same filters for thread listings (REST API and thread summaries)
			add_filter( 'bp_get_message_thread_content', array( $this, 'render_shared_message_content' ), 20, 2 );
			add_filter( 'bp_get_message_thread_content', array( $this, 'add_link_preview_to_message_content' ), 30, 2 );

			// Dynamic rendering: Inject shared activity card after message content (for AJAX responses)
			add_action( 'bp_after_message_content', array( $this, 'inject_shared_activity_after_message' ) );

			add_filter( 'bp_messages_message_validated_content', array( $this, 'bb_sharing_message_validated_content' ), 20, 3 );

			// Format notifications for activity shares
			add_filter( 'bp_activity_activity_share_notification', array( $this, 'format_activity_share_notification' ), 10, 7 );

			// Display share count in activity state container
			add_action( 'bp_activity_state_after_comments', array( $this, 'display_activity_state_shares' ), 10, 1 );

            add_action( 'bp_rest_api_init', array( $this, 'rest_api_init' ), 9 );
		}
	}

	/**
	 * Disable WordPress embeds in message content.
	 *
	 * Removes the autoembed filter from message content to prevent
	 * automatic embedding of URLs (YouTube, Twitter, etc.) and use
	 * our custom link preview functionality instead.
	 *
	 * @since 1.0.0
	 */
	public function disable_message_embeds() {
		global $wp_embed;

		// Remove WordPress autoembed from message content
		if ( isset( $wp_embed ) ) {
			remove_filter( 'bp_get_the_thread_message_content', array( $wp_embed, 'autoembed' ), 8 );
			remove_filter( 'bp_get_the_thread_message_content', array( $wp_embed, 'run_shortcode' ), 7 );
		}

		// Remove from BuddyPress embed (priority 8 for autoembed, 7 for run_shortcode)
		if ( function_exists( 'buddypress' ) && isset( buddypress()->embed ) ) {
			remove_filter( 'bp_get_the_thread_message_content', array( buddypress()->embed, 'autoembed' ), 8 );
			remove_filter( 'bp_get_the_thread_message_content', array( buddypress()->embed, 'run_shortcode' ), 7 );
		}
	}

	/**
	 * Register custom activity actions.
	 *
	 * @since 1.0.0
	 */
	public function register_activity_actions() {
		// Check if activity component is active
		if ( ! bp_is_active( 'activity' ) || ! function_exists( 'bp_activity_set_action' ) ) {
			return;
		}

		$bp = buddypress();

		// Register for activity component (personal feed)
		bp_activity_set_action(
			'activity',
			'activity_share',
			esc_html__( 'Shared a post', 'buddyboss-sharing' ),
			array( $this, 'format_activity_action_share' ),
			esc_html__( 'Shares', 'buddyboss-sharing' ),
			array( 'activity', 'member' )
		);

		// Register for groups component (group feed) - only if groups component is active
		if ( bp_is_active( 'groups' ) && isset( $bp->groups->id ) ) {
			bp_activity_set_action(
				$bp->groups->id,
				'activity_share',
				esc_html__( 'Shared a post in a group', 'buddyboss-sharing' ),
				array( $this, 'format_activity_action_group_share' ),
				esc_html__( 'Group Shares', 'buddyboss-sharing' ),
				array( 'activity', 'group', 'member', 'member_groups' )
			);
		}

		// Make activity_share type editable so privacy controls appear
		add_filter( 'bp_activity_user_can_edit', array( $this, 'allow_activity_share_edit' ), 10, 2 );
	}

	/**
	 * Format activity action for shared posts.
	 *
	 * @since 1.0.0
	 * @param string $action   Static activity action.
	 * @param object $activity Activity object.
	 * @return string
	 */
	public function format_activity_action_share( $action, $activity ) {
		$user_link = bp_core_get_userlink( $activity->user_id );

		// Check if this is actually a shared activity (has shared_activity_id metadata)
		$shared_activity_id = bp_activity_get_meta( $activity->id, 'shared_activity_id', true );

		// Check if this is a friend profile share (has shared_activity_id AND item_id set to friend's user ID)
		if ( ! empty( $shared_activity_id ) && ! empty( $activity->item_id ) && $activity->component === 'activity' ) {
			// Shared with a friend
			$friend_link = bp_core_get_userlink( $activity->item_id );
			$action = sprintf(
				/* translators: %1$s: user link, %2$s: friend link */
				esc_html__( '%1$s shared a post with %2$s', 'buddyboss-sharing' ),
				$user_link,
				$friend_link
			);
		} else {
			// Regular feed share
			$action = sprintf(
				/* translators: %s: user link */
				esc_html__( '%s shared a post', 'buddyboss-sharing' ),
				$user_link
			);
		}

		return apply_filters( 'buddyboss_seo_activity_share_action', $action, $activity );
	}

	/**
	 * Format activity action for group shared posts.
	 *
	 * @since 1.0.0
	 * @param string $action   Static activity action.
	 * @param object $activity Activity object.
	 * @return string
	 */
	public function format_activity_action_group_share( $action, $activity ) {
		$user_link = bp_core_get_userlink( $activity->user_id );

		// Check if groups component is active
		if ( ! bp_is_active( 'groups' ) || ! function_exists( 'groups_get_group' ) || ! function_exists( 'bp_get_group_permalink' ) ) {
			return sprintf(
				/* translators: %s: user link */
				esc_html__( '%s shared a post', 'buddyboss-sharing' ),
				$user_link
			);
		}

		// Get group object
		$group = groups_get_group( $activity->item_id );
		if ( empty( $group->id ) ) {
			return sprintf(
				/* translators: %s: user link */
				esc_html__( '%s shared a post', 'buddyboss-sharing' ),
				$user_link
			);
		}

		$group_link = '<a href="' . esc_url( bp_get_group_permalink( $group ) ) . '" data-bb-hp-group="' . esc_attr( $group->id ) . '">' . esc_html( $group->name ) . '</a>';

		$action = sprintf(
			/* translators: %1$s: user link, %2$s: group link */
			esc_html__( '%1$s shared a post in the group %2$s', 'buddyboss-sharing' ),
			$user_link,
			$group_link
		);

		return apply_filters( 'buddyboss_seo_activity_group_share_action', $action, $activity );
	}

	/**
	 * Format notification for activity share.
	 *
	 * @since 1.0.0
	 *
	 * @param string $notification      Null value.
	 * @param int    $item_id           The activity ID.
	 * @param int    $secondary_item_id The user ID who shared.
	 * @param int    $total_items       Total number of notifications.
	 * @param string $format            'string' or 'array'.
	 * @param int    $id                Notification ID.
	 * @param string $screen            Notification screen type.
	 * @return string|array Formatted notification.
	 */
	public function format_activity_share_notification( $notification, $item_id, $secondary_item_id, $total_items, $format = 'string', $id = 0, $screen = 'web' ) {
		$user_fullname = bp_core_get_user_displayname( $secondary_item_id );
		$link          = bp_activity_get_permalink( $item_id );
		$text          = '';

		if ( (int) $total_items > 1 ) {
			$text = sprintf(
				/* translators: %d: number of new shares */
				__( 'You have %d new shares', 'buddyboss-sharing' ),
				(int) $total_items
			);
			$link = bp_get_notifications_permalink();
		} else {
			$text = sprintf(
				/* translators: %s: user's display name */
				__( '%s shared a post with you', 'buddyboss-sharing' ),
				$user_fullname
			);
		}

		if ( 'string' === $format ) {
			return '<a href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>';
		} else {
			return array(
				'text' => $text,
				'link' => $link,
			);
		}
	}

	/**
	 * Allow activity_share type to be editable.
	 * This enables privacy controls for shared activities.
	 *
	 * @since 1.0.0
	 * @param bool   $can_edit Whether the user can edit.
	 * @param object $activity Activity object.
	 * @return bool Whether the user can edit.
	 */
	public function allow_activity_share_edit( $can_edit, $activity ) {
		// If already allowed to edit, return early
		if ( $can_edit ) {
			return $can_edit;
		}

		// Check if this is an activity_share type
		if ( empty( $activity->type ) || 'activity_share' !== $activity->type ) {
			return $can_edit;
		}

		// Allow edit if user is logged in and owns the activity
		if ( is_user_logged_in() && isset( $activity->user_id ) && bp_loggedin_user_id() === $activity->user_id ) {
			return true;
		}

		return $can_edit;
	}

	/**
	 * Add share button to activity items.
	 *
	 * @since 1.0.0
	 * @param array $buttons The list of buttons.
	 * @param int   $activity_id The current activity ID.
	 * @return array Modified buttons array.
	 */
	public function add_share_button( $buttons, $activity_id ) {
		// Check license validity first.
		if ( ! \BuddyBoss\Sharing\Core\License_Manager::instance()->can_use_sharing() ) {
			return $buttons;
		}

		if ( ! is_user_logged_in() ) {
			return $buttons;
		}

		// Get activity type
		$activity_type = bp_get_activity_type();

		// Hide share button for friendship activities
		$excluded_types =
                array(
                        'friendship_accepted',
                        'friendship_created',
                        'zoom_meeting_create',
                        'zoom_meeting_notify',
                        'zoom_webinar_create',
                        'zoom_webinar_notify',
                        'created_group',
                        'joined_group',
                        'group_details_updated',
                        'bbp_topic_create',
                        'bbp_reply_create',
                        'new_member',
                        'new_avatar',
                        'updated_profile',
                );
		if ( in_array( $activity_type, $excluded_types, true ) ) {
			return $buttons;
		}

		// Only show for appropriate privacy levels.
		$privacy = bp_get_activity_privacy();
		if ( in_array( $privacy, array( 'onlyme' ), true ) ) {
			return $buttons;
		}

		// Get the original activity ID (in case of a shared activity)
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		// Get share count.
		$share_count = (int) bp_activity_get_meta( $original_activity_id, 'share_count', true );

		// Use ReadyLaunch icon if ReadyLaunch is enabled, otherwise use regular icon.
		$icon_class = ( function_exists( 'bb_is_readylaunch_enabled' ) && bb_is_readylaunch_enabled() ) 
			? 'bb-icons-rl-share-fat' 
			: 'bb-icon-f bb-icon-share';

		// Build button text with icon and label (matching Like/Comment pattern).
		$button_text = sprintf(
			'<i class="%1$s"></i><span class="bp-screen-reader-text">%2$s</span> <span class="share-btn-label">%3$s</span>',
			esc_attr( $icon_class ),
			esc_html__( 'Share', 'buddyboss-sharing' ),
			esc_html__( 'Share', 'buddyboss-sharing' )
		);

		// Add the share button to the buttons array.
		$buttons['activity_share'] = array(
			'id'                => 'activity_share',
			'position'          => 6,
			'component'         => 'activity',
			'must_be_logged_in' => true,
			'link_text'         => $button_text,
			'button_attr'       => array(
				'href'                    => '#',
				'class'                   => 'button share bp-secondary-action',
				'data-activity-id'        => $activity_id,
				'data-original-activity-id' => $original_activity_id,
				'role'                    => 'button',
			),
		);

		return $buttons;
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

		$activity_id     = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;
		$share_type      = isset( $_POST['share_type'] ) ? sanitize_text_field( wp_unslash( $_POST['share_type'] ) ) : '';
		$share_target    = isset( $_POST['share_target'] ) ? sanitize_text_field( wp_unslash( $_POST['share_target'] ) ) : '';
		$custom_message  = isset( $_POST['custom_message'] ) ? $_POST['custom_message'] : '';

		if ( ! $activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid activity.', 'buddyboss-sharing' ) ) );
		}

		// Get the original activity ID (in case of a shared activity)
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		// Process sharing based on type.
		$result = $this->process_share( $activity_id, $share_type, $share_target, $custom_message );

		if ( $result ) {
			// Update share count.
			$share_count = (int) bp_activity_get_meta( $original_activity_id, 'share_count', true );
			bp_activity_update_meta( $original_activity_id, 'share_count', $share_count + 1 );

			wp_send_json_success(
				array(
					'message'     => esc_html__( 'Activity shared successfully!', 'buddyboss-sharing' ),
					'share_count' => $share_count + 1,
				)
			);
		} else {
			wp_send_json_error( array( 'message' => esc_html__( 'Failed to share activity.', 'buddyboss-sharing' ) ) );
		}
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
		// Get the original activity ID (in case of a shared activity)
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		$original_activity = new \BP_Activity_Activity( $original_activity_id );

		if ( ! $original_activity->id ) {
			return false;
		}

		// Store only custom message as content, NOT the HTML preview.
		// The preview will be rendered dynamically using the shared_activity_id meta.
		$content = ! empty( $custom_message ) ? $custom_message : '';

		$args = array(
			'user_id' => get_current_user_id(),
			'content' => $content,
			'type'    => 'activity_share',
		);

		// Set component and item_id based on share type.
		switch ( $share_type ) {
			case 'feed':
				$args['component'] = 'activity';
				$new_activity_id   = bp_activity_add( $args );

				if ( $new_activity_id ) {
					// Store shared activity ID.
					bp_activity_update_meta( $new_activity_id, 'shared_activity_id', $original_activity_id );
				}

				return $new_activity_id;

			case 'group':
				if ( get_option( 'buddyboss_activity_sharing_to_groups', 1 ) ) {
					$args['component'] = 'groups';
					$args['item_id']   = intval( $share_target );

					$new_activity_id = bp_activity_add( $args );

					if ( $new_activity_id ) {
						// Store shared activity ID.
						bp_activity_update_meta( $new_activity_id, 'shared_activity_id', $original_activity_id );
					}

					return $new_activity_id;
				}
				break;

			case 'profile':
				if ( get_option( 'buddyboss_activity_sharing_to_friends', 1 ) ) {
					$args['component']         = 'activity';
					$args['secondary_item_id'] = intval( $share_target );

					$new_activity_id = bp_activity_add( $args );

					if ( $new_activity_id ) {
						// Store shared activity ID.
						bp_activity_update_meta( $new_activity_id, 'shared_activity_id', $original_activity_id );
					}

					return $new_activity_id;
				}
				break;

			case 'message':
				if ( get_option( 'buddyboss_activity_sharing_to_message', 1 ) && function_exists( 'messages_new_message' ) ) {
				// Store only the custom message text as content
				// The activity card will be rendered dynamically via the bp_get_the_thread_message_content filter
				$message_content = ! empty( $content ) ? $content : "";

					$message_id = messages_new_message(
						array(
							'sender_id'  => get_current_user_id(),
							'recipients' => array( intval( $share_target ) ),
							'subject'    => esc_html__( 'Shared Activity', 'buddyboss-sharing' ),
							'content'    => $message_content,
						)
					);


				if ( $message_id ) {
						// Store shared activity ID in message meta.
						bp_messages_update_meta( $message_id, 'shared_activity_id', $original_activity_id );
					}

					return $message_id;
				}
				break;
		}

		return false;
	}

	/**
	 * Allow shared activity HTML in activity content.
	 *
	 * @since 1.0.0
	 * @param array $tags Allowed tags.
	 * @return array
	 */
	public function allow_shared_activity_html( $tags ) {
		$tags['div']['class']            = array();
		$tags['div']['data-activity-id'] = array();
		$tags['span']['class']           = array();
		$tags['video']['controls']       = array();
		$tags['video']['class']          = array();
		$tags['source']['src']           = array();
		$tags['source']['type']          = array();
		$tags['i']['class']              = array();

		// Ensure span tag exists and has class attribute
		if ( ! isset( $tags['span'] ) ) {
			$tags['span'] = array();
		}
		$tags['span']['class'] = array();

		return $tags;
	}

	/**
	 * AJAX handler for getting share modal content.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_share_modal_content() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		$activity_id = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;
		$share_type  = isset( $_POST['share_type'] ) ? sanitize_text_field( wp_unslash( $_POST['share_type'] ) ) : '';

		if ( ! $activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid activity.', 'buddyboss-sharing' ) ) );
		}

		// Get the original activity ID (in case of a shared activity)
		// When sharing to message, we want to share the original activity, not the shared version
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		// Load template.
		ob_start();
		$this->render_share_modal_content( $original_activity_id, $share_type );
		$content = ob_get_clean();

		wp_send_json_success( array( 'content' => $content ) );
	}

	/**
	 * Render share modal content.
	 *
	 * @since 1.0.0
	 * @param int    $activity_id Activity ID.
	 * @param string $share_type Share type.
	 */
	private function render_share_modal_content( $activity_id, $share_type ) {
		// Use separate template for message sharing
		if ( 'message' === $share_type ) {
			$template_file = BUDDYBOSS_SHARING_PLUGIN_DIR . 'templates/activity-sharing/share-message-modal.php';
		} else {
			$template_file = BUDDYBOSS_SHARING_PLUGIN_DIR . 'templates/activity-sharing/share-modal-content.php';
		}

		if ( file_exists( $template_file ) ) {
			// Make variables available to template.
			$activity       = new \BP_Activity_Activity( $activity_id );
			$current_user   = wp_get_current_user();
			$enable_custom_msg = bp_get_option( 'buddyboss_activity_sharing_custom_message', 1 );

			include $template_file;
		}
	}

	/**
	 * AJAX handler to get activity content for modal preview.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_activity_content() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		// Check if activity component is active
		if ( ! bp_is_active( 'activity' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Activity component is not available.', 'buddyboss-sharing' ) ) );
		}

		$activity_id = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;

		if ( ! $activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid activity.', 'buddyboss-sharing' ) ) );
		}

		// Get the original activity ID (in case of a shared activity)
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		$activity = new \BP_Activity_Activity( $original_activity_id );

		if ( ! $activity->id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Activity not found.', 'buddyboss-sharing' ) ) );
		}

		// Check if user can read this activity using BuddyBoss privacy validation
		$current_user_id = get_current_user_id();
		$can_read        = true;

		// Use bb_validate_activity_privacy if available (more comprehensive)
		if ( function_exists( 'bb_validate_activity_privacy' ) ) {
			$privacy_check = bb_validate_activity_privacy(
				array(
					'activity_id'     => $activity->id,
					'user_id'         => $current_user_id,
					'validate_action' => 'view_activity',
					'activity_type'   => 'activity',
				)
			);

			// If privacy check returns WP_Error, user doesn't have access
			if ( is_wp_error( $privacy_check ) ) {
				$can_read = false;
			}
		} elseif ( function_exists( 'bp_activity_user_can_read' ) ) {
			// Fallback to bp_activity_user_can_read
			$can_read = bp_activity_user_can_read( $activity, $current_user_id );
		}

		// If user cannot read the activity, use the no-access message renderer
		if ( ! $can_read ) {
			$html = $this->render_no_access_message( '' );
			wp_send_json_success( array( 'content' => $html, 'original_activity_id' => $original_activity_id, 'no_access' => true ) );
			return;
		}

		// Generate activity preview HTML.
		$html = $this->get_html_generator()->generate_shared_activity_html( $activity );

		wp_send_json_success( array( 'content' => $html, 'original_activity_id' => $original_activity_id ) );
	}

	/**
	 * AJAX handler to share activity to feed.
	 *
	 * @since 1.0.0
	 */
	public function ajax_share_to_feed() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		// Check if activity component is active
		if ( ! bp_is_active( 'activity' ) || ! function_exists( 'bp_activity_add' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Activity component is not available.', 'buddyboss-sharing' ) ) );
		}

		$activity_id         = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;
		$custom_message      = isset( $_POST['custom_message'] ) ? $_POST['custom_message'] : '';
		$privacy             = isset( $_POST['privacy'] ) ? sanitize_text_field( wp_unslash( $_POST['privacy'] ) ) : 'public';
		$topic_id            = isset( $_POST['topic_id'] ) ? intval( $_POST['topic_id'] ) : 0;
		$has_topic_selector  = isset( $_POST['has_topic_selector'] ) ? (bool) $_POST['has_topic_selector'] : false;

		if ( ! $activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid activity.', 'buddyboss-sharing' ) ) );
		}

		// Server-side validation: Check if any media/attachments were added (which should be disabled)
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Already checked via check_ajax_referer above
		$has_media     = ! empty( $_POST['media'] ) || ! empty( $_POST['bp_media_ids'] );
		$has_video     = ! empty( $_POST['video'] ) || ! empty( $_POST['bp_video_ids'] );
		$has_document  = ! empty( $_POST['document'] ) || ! empty( $_POST['bp_document_ids'] );
		$has_gif       = ! empty( $_POST['gif_data'] );
		$has_poll      = ! empty( $_POST['poll_question'] );
		$has_scheduled = ! empty( $_POST['scheduled_date'] );
		// phpcs:enable

		// If any upload features were used, reject the request
		if ( $has_media || $has_video || $has_document || $has_gif || $has_poll || $has_scheduled ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Sharing activities cannot include media uploads, polls, or scheduled posts.', 'buddyboss-sharing' ) ) );
		}

		// Get the original activity ID (in case of a shared activity)
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		$original_activity = new \BP_Activity_Activity( $original_activity_id );

		if ( ! $original_activity->id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Activity not found.', 'buddyboss-sharing' ) ) );
		}

		// Store only custom message as content, NOT the HTML preview.
		// The preview will be rendered dynamically using the shared_activity_id meta.
		$content = ! empty( $custom_message ) ? $custom_message : '';

		// Apply the same content filter that BuddyBoss Platform uses for activity updates.
		// This ensures emoji and other content processing is handled correctly.
		$content = apply_filters( 'bp_activity_new_update_content', $content );

		// Create new activity.
		$args = array(
			'user_id'       => get_current_user_id(),
			'content'       => $content,
			'type'          => 'activity_share',
			'component'     => 'activity',
			'privacy'       => $privacy,
			'hide_sitewide' => false,
		);

		// Add topic_id if provided (same as BuddyBoss Platform activity post form)
		if ( $topic_id > 0 ) {
			$args['topic_id'] = $topic_id;
		}

		// Log args for debugging

		$new_activity_id = bp_activity_add( $args );

		// Log result for debugging

		if ( $new_activity_id ) {
			// Store shared activity ID.
			bp_activity_update_meta( $new_activity_id, 'shared_activity_id', $original_activity_id );

			// Save topic relationship if topic_id is provided (same as BuddyBoss Platform)
			if ( $topic_id > 0 && function_exists( 'bb_activity_topics_manager_instance' ) ) {
				bb_activity_topics_manager_instance()->bb_add_activity_topic_relationship(
					array(
						'topic_id'    => $topic_id,
						'activity_id' => $new_activity_id,
						'component'   => 'activity',
						'item_id'     => 0,
					)
				);
			}

			// Update share count.
			$share_count = (int) bp_activity_get_meta( $original_activity_id, 'share_count', true );
			bp_activity_update_meta( $original_activity_id, 'share_count', $share_count + 1 );

			// Generate activity HTML (same way BuddyBoss does it)
			ob_start();
			if ( bp_has_activities(
				array(
					'include'     => $new_activity_id,
					'show_hidden' => ( 'public' !== $privacy ),
				)
			) ) {
				while ( bp_activities() ) {
					bp_the_activity();
					bp_get_template_part( 'activity/entry' );
				}
			}
			$activity_html = ob_get_contents();
			ob_end_clean();

			wp_send_json_success(
				array(
					'id'               => $new_activity_id,
					'message'          => esc_html__( 'Activity shared successfully!', 'buddyboss-sharing' ),
					'activity'         => $activity_html,
					'share_count'      => $share_count + 1,
					'is_directory'     => bp_is_activity_directory(),
					'is_user_activity' => bp_is_user_activity(),
				)
			);
		} else {
			wp_send_json_error( array( 'message' => esc_html__( 'Failed to share activity.', 'buddyboss-sharing' ) ) );
		}
	}

	/**
	 * Render share templates in footer.
	 *
	 * @since 1.0.0
	 */
	public function render_share_templates() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Load on all pages where BuddyBoss is active (activity can appear in many places).
		$template_file = BUDDYBOSS_SHARING_PLUGIN_DIR . 'templates/activity-sharing/share-modal.php';

		if ( file_exists( $template_file ) ) {
			include $template_file;
		}

		// Load share to activity feed modal.
		$activity_modal_file = BUDDYBOSS_SHARING_PLUGIN_DIR . 'templates/activity-sharing/share-activity-modal.php';

		if ( file_exists( $activity_modal_file ) ) {
			include $activity_modal_file;
		}

		// Load group selection modal.
		// Note: This modal will be populated dynamically when opened, so we render an empty container.
		echo '<div id="buddyboss-share-group-modal-container bb-share-modal-container" style="display: none;"></div>';
	}

	/**
	 * Check if an activity is a shared activity.
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @return bool
	 */
	private function is_shared_activity( $activity ) {
		if ( ! $activity || ! isset( $activity->id ) || ! isset( $activity->type ) ) {
			return false;
		}

		return $activity->type === 'activity_share';
	}

	/**
	 * Get the original activity ID for sharing.
	 * If the activity is itself a shared activity, return the original activity ID.
	 * Otherwise, return the activity ID itself.
	 *
	 * @since 1.0.0
	 * @param int $activity_id Activity ID to check.
	 * @return int Original activity ID.
	 */
	private function get_original_activity_id( $activity_id ) {
		if ( ! $activity_id ) {
			return $activity_id;
		}

		$activity = new \BP_Activity_Activity( $activity_id );

		if ( ! $activity->id ) {
			return $activity_id;
		}

		// Check if this is a shared activity
		if ( $this->is_shared_activity( $activity ) ) {
			$shared_activity_id = bp_activity_get_meta( $activity->id, 'shared_activity_id', true );

			if ( $shared_activity_id ) {
				// Recursively check in case of nested shares (though unlikely)
				return $this->get_original_activity_id( $shared_activity_id );
			}
		}

		return $activity_id;
	}

	/**
	 * Display share count in activity state container.
	 *
	 * Hooks into bp_activity_state_after_comments to display share count.
	 *
	 * @since 1.0.0
	 * @param int $activity_id The activity ID.
	 */
	public function display_activity_state_shares( $activity_id ) {
		if ( ! $activity_id ) {
			return;
		}

		// Only show for appropriate privacy levels.
		$privacy = bp_get_activity_privacy();
		if ( in_array( $privacy, array( 'onlyme' ), true ) ) {
			return;
		}

		// Get the original activity ID (in case of a shared activity)
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		// Get share count.
		$share_count = (int) bp_activity_get_meta( $original_activity_id, 'share_count', true );

		// Only show if there's at least one share
		if ( $share_count <= 0 ) {
			return;
		}

		// Build class array similar to comments
		$activity_state_share_class = array( 'activity-state-shares' );
		if ( $share_count > 0 ) {
			$activity_state_share_class[] = 'has-shares';
		}

		/**
		 * Filter the activity state share classes.
		 *
		 * @since 1.0.0
		 *
		 * @param array $activity_state_share_class The activity state share class array.
		 * @param int   $activity_id                 The activity ID.
		 */
		$activity_state_class = apply_filters( 'bp_nouveau_get_activity_share_buttons_activity_state', $activity_state_share_class, $activity_id );
		?>
		<a href="#" class="<?php echo esc_attr( trim( implode( ' ', $activity_state_class ) ) ); ?>" data-activity-id="<?php echo esc_attr( $activity_id ); ?>">
			<span class="shares-count" data-shares-count="<?php echo esc_attr( $share_count ); ?>">
				<?php
				if ( $share_count === 1 ) {
					/* translators: %1$s: number wrapped in span, %2$s: Share label */
					printf( _x( '%1$s %2$s', 'placeholder: activity share count', 'buddyboss-sharing' ), '<span class="shares-count-number">' . esc_html( $share_count ) . '</span>', esc_html_x( 'Share', 'singular share label', 'buddyboss-sharing' ) );
				} else {
					/* translators: %1$s: number wrapped in span, %2$s: Shares label */
					printf( _x( '%1$s %2$s', 'placeholder: activity shares count', 'buddyboss-sharing' ), '<span class="shares-count-number">' . esc_html( $share_count ) . '</span>', esc_html_x( 'Shares', 'plural shares label', 'buddyboss-sharing' ) );
				}
				?>
			</span>
		</a>
		<?php
	}

	/**
	 * Dynamically render shared activity content.
	 *
	 * This filter intercepts the activity content display and dynamically
	 * renders the shared activity preview from the stored activity ID.
	 *
	 * @since 1.0.0
	 * @param string $content Activity content.
	 * @param object $activity Activity object.
	 * @return string
	 */
	public function render_shared_activity_content( $content, $activity ) {

		// Only process if this is a shared activity
		if ( ! $this->is_shared_activity( $activity ) ) {
			return $content;
		}

		// Get the shared activity ID from meta
		$shared_activity_id = bp_activity_get_meta( $activity->id, 'shared_activity_id', true );

		if ( ! $shared_activity_id ) {
			// No shared activity ID found, return original content
			return $content;
		}

        $is_rest_request = bb_is_rest();
        // For REST API requests, return the activity URL instead of HTML preview.
        if ( $is_rest_request && bp_is_active( 'activity' ) ) {
            // Get the activity URL.
            $activity_url = bp_activity_get_permalink( $shared_activity_id );

            if ( ! empty( $content ) ) {
                $content = $content . '<br/>';
            }

            // Return format: "<p>Shared a Post <a href="URL">URL</a></p>"
            return $content . '<p>' . esc_html__( 'Shared a Post: ', 'buddyboss-sharing' ) . ' <a href="' . esc_url( $activity_url ) . '">' . esc_url( $activity_url ) . '</a></p>';

        }

		// Try to fetch the original activity
		$original_activity = new \BP_Activity_Activity( $shared_activity_id );

		if ( ! $original_activity->id ) {
			// Original activity not found or deleted
			return $this->render_deleted_activity_message( $content );
		}

		// Validate activity privacy - check if current user has permission to view
		if ( function_exists( 'bb_validate_activity_privacy' ) ) {
			$current_user_id = get_current_user_id();
            $privacy_check   = bb_validate_activity_privacy(
                    array(
                            'activity_id'     => $shared_activity_id,
                            'user_id'         => $current_user_id,
                            'validate_action' => 'view_activity',
                            'activity_type'   => 'activity',
                    )
            );

			// If user cannot read the activity, show no-access message
            if ( is_wp_error( $privacy_check ) ) {
				return $this->render_no_access_message( $content );
			}
		}

		// Temporarily remove bp_activity_link_preview filter to prevent double rendering
		// since we're already handling link preview in the HTML generator.
		$link_preview_removed = false;
		if ( has_filter( 'bp_get_activity_content_body', 'bp_activity_link_preview' ) ) {
			remove_filter( 'bp_get_activity_content_body', 'bp_activity_link_preview', 20 );
			$link_preview_removed = true;
		}

		// Generate the shared activity HTML preview
		$shared_html = $this->get_html_generator()->generate_shared_activity_html( $original_activity );

		// Re-add the filter if it was removed
		if ( $link_preview_removed ) {
			add_filter( 'bp_get_activity_content_body', 'bp_activity_link_preview', 20, 2 );
		}

		// Combine custom message (if any) with shared activity preview
		$output = '';
		if ( ! empty( $content ) ) {
			$output = $content . "\n\n";
		}
		$output .= $shared_html;

		return $output;
	}

	/**
	 * Render a message when the original shared activity is deleted or unavailable.
	 *
	 * @since 1.0.0
	 * @param string $custom_message The custom message added by the user.
	 * @return string
	 */
	private function render_deleted_activity_message( $custom_message ) {
		$output = '';

		if ( ! empty( $custom_message ) ) {
			$output = $custom_message . "\n\n";
		}

		$output .= '<div class="shared-activity-wrapper shared-activity-deleted">';
		$output .= '<div class="shared-activity-preview">';
		$output .= '<div class="shared-activity-unavailable">';
		$icon_class = ( function_exists( 'bb_is_readylaunch_enabled' ) && bb_is_readylaunch_enabled() ) 
			? 'bb-icons-rl-eye-slash' 
			: 'bb-icon-l bb-icon-eye-slash';
		$output .= '<i class="' . esc_attr( $icon_class ) . '"></i>';
		$output .= '<div class="shared-activity-unavailable-content">';
		$output .= '<h5>' . esc_html__( 'This content isn\'t available right now', 'buddyboss-sharing' ) . '</h5>';
		$output .= '<p>' . esc_html__( 'When this happens, it\'s usually because the owner changed who can see it or it\'s been deleted.', 'buddyboss-sharing' ) . '</p>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Render "no access" message when user doesn't have permission to view the shared activity.
	 *
	 * @since 1.0.0
	 * @param string $custom_message The custom message added by the user.
	 * @return string
	 */
	private function render_no_access_message( $custom_message ) {
		$output = '';

		if ( ! empty( $custom_message ) ) {
			$output = $custom_message . "\n\n";
		}

		// Check if this is a REST API request
		$is_rest_request = bb_is_rest();

		if ( $is_rest_request ) {
			// For REST API, return simple format
			$output = __( 'Shared a Post', 'buddyboss-sharing' ) . ' ' . __( '[Activity not accessible]', 'buddyboss-sharing' );
			return $output;
		}

		// For regular requests, show HTML no-access message
		$output .= '<div class="shared-activity-wrapper shared-activity-no-access">';

		// Use the no-access template
		$no_access_template = BUDDYBOSS_SHARING_PLUGIN_DIR . 'templates/activity-sharing/no-access.php';
		if ( file_exists( $no_access_template ) ) {
			ob_start();
			include $no_access_template;
			$output .= ob_get_clean();
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Dynamically render shared message content.
	 *
	 * This filter intercepts the message content display and dynamically
	 * renders the shared activity card from the stored activity ID.
	 *
	 * @since 1.0.0
	 * @param string $content Message content.
	 * @param int    $message_id Optional message ID (for AJAX contexts).
	 * @return string
	 */
	public function render_shared_message_content( $content, $message_id = 0 ) {
		// Check if required components are active
		if ( ! bp_is_active( 'messages' ) || ! bp_is_active( 'activity' ) || ! function_exists( 'bp_messages_get_meta' ) || ! function_exists( 'bp_activity_get_permalink' ) ) {
			return $content;
		}

		global $thread_template;


		// Try to get message_id from multiple sources
		// 1. From parameter (passed in AJAX contexts via our custom filter)
		// 2. From global thread template (standard template rendering)
		// 3. From our class property (set before hook capture)
		if ( empty( $message_id ) ) {
			if ( ! empty( $thread_template->message ) && ! empty( $thread_template->message->id ) ) {
				$message_id = $thread_template->message->id;
			} elseif ( ! empty( $this->current_message_id ) ) {
				$message_id = $this->current_message_id;
			}
		}

		// If we still don't have a message_id, we can't proceed
		if ( empty( $message_id ) ) {
			return $content;
		}

		// Check if message has shared_activity_id meta
		$shared_activity_id = bp_messages_get_meta( $message_id, 'shared_activity_id', true );

		if ( ! $shared_activity_id  || bb_is_rest() ) {
			// Not a shared activity message, return original content
			return $content;
		}

		// Try to fetch the original activity
		$original_activity = new \BP_Activity_Activity( $shared_activity_id );

		if ( ! $original_activity->id ) {
			// Original activity not found or deleted

			// Check if this is a REST API request
			$is_rest_request = bb_is_rest();

			if ( $is_rest_request ) {
				// For REST API, return simple format
				$output = __( 'Shared a Post', 'buddyboss-sharing' ) . ' ' . __( '[Activity unavailable]', 'buddyboss-sharing' );
				return $output;
			}

			// For regular requests, show HTML unavailable message
			$output = '';
			if ( ! empty( $content ) ) {
				$output = $content . "\n\n";
			}
			$output .= '<div class="shared-activity-wrapper shared-activity-deleted">';
			$output .= '<div class="shared-activity-preview">';
			$output .= '<div class="shared-activity-unavailable">';
			$icon_class = ( function_exists( 'bb_is_readylaunch_enabled' ) && bb_is_readylaunch_enabled() )
				? 'bb-icons-rl-eye-slash'
				: 'bb-icon-l bb-icon-eye-slash';
			$output .= '<i class="' . esc_attr( $icon_class ) . '"></i>';
			$output .= '<div class="shared-activity-unavailable-content">';
			$output .= '<h5>' . esc_html__( 'This content isn\'t available right now', 'buddyboss-sharing' ) . '</h5>';
			$output .= '<p>' . esc_html__( 'When this happens, it\'s usually because the owner changed who can see it or it\'s been deleted.', 'buddyboss-sharing' ) . '</p>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			return $output;
		}

		// Validate activity privacy - check if current user has permission to view
		$current_user_id = get_current_user_id();
		$can_read        = true;

		// Use bb_validate_activity_privacy if available (more comprehensive)
		if ( function_exists( 'bb_validate_activity_privacy' ) ) {
			$privacy_check = bb_validate_activity_privacy(
				array(
					'activity_id'     => $original_activity->id,
					'user_id'         => $current_user_id,
					'validate_action' => 'view_activity',
					'activity_type'   => 'activity',
				)
			);

			// If privacy check returns WP_Error, user doesn't have access
			if ( is_wp_error( $privacy_check ) ) {
				$can_read = false;
			}
		} elseif ( function_exists( 'bp_activity_user_can_read' ) ) {
			// Fallback to bp_activity_user_can_read
			$can_read = bp_activity_user_can_read( $original_activity, $current_user_id );
		}

		// If user cannot read the activity, show no-access message
		if ( ! $can_read ) {
			return $this->render_no_access_message( $content );
		}

		// Check if this is a REST API request
		$is_rest_request = bb_is_rest();

		// For REST API requests, return the activity URL instead of HTML preview
		if ( $is_rest_request ) {
			// Get the activity URL
			$activity_url = bp_activity_get_permalink( $original_activity->id );

			// Return simple format: "Shared a Post <URL>"
			$output = __( 'Shared a Post', 'buddyboss-sharing' ) . ' ' . esc_url( $activity_url );

			return $output;
		}

		// Generate compact card HTML for message (for non-REST requests)
		$shared_html = $this->get_html_generator()->generate_shared_activity_html_for_message( $original_activity );

		// Combine custom message (if any) with compact card
		$output = '';
		if ( ! empty( $content ) ) {
			$output = $content . "\n\n";
		}
		$output .= $shared_html;

		return $output;
	}

	/**
	 * Add link preview to message content.
	 *
	 * This filter runs after shared activity rendering to add link previews
	 * for any URLs in regular message content (non-shared activities).
	 *
	 * @since 1.0.0
	 * @param string $content Message content.
	 * @param int    $message_id Message ID.
	 * @return string Content with link preview appended.
	 */
	public function add_link_preview_to_message_content( $content, $message_id = 0 ) {
		// Only add link previews if this is NOT a shared activity message
		// (shared activities are already rendered by render_shared_message_content)
		if ( ! empty( $message_id ) ) {
			$shared_activity_id = bp_messages_get_meta( $message_id, 'shared_activity_id', true );
			if ( $shared_activity_id ) {
				// This is a shared activity, don't add link preview
				return $content;
			}
		}

		// Check if content already has a shared-activity-wrapper (from previous filter)
		if ( strpos( $content, 'shared-activity-wrapper' ) !== false || bb_is_rest() ) {
			// Already has a shared activity card, don't add link preview
			return $content;
		}

		// Generate link preview from content
		$link_preview = $this->generate_link_preview_from_content( $content );

		// If we got a preview, append it to the content
		if ( ! empty( $link_preview ) ) {
			$content .= "\n\n" . $link_preview;
		}

		return $content;
	}

	/**
	 * Set message context for AJAX rendering.
	 *
	 * This runs early in the bp_get_the_thread_message_content filter chain to extract
	 * the message ID from the calling function's scope using debug_backtrace().
	 *
	 * Since BuddyBoss's bb_get_message_response_object() has access to $message->id but
	 * only passes $content to the filter, we extract the message ID from the call stack.
	 *
	 * @since 1.0.0
	 * @param string $content Message content.
	 * @return string Unchanged content.
	 */
	public function set_message_context_for_ajax( $content ) {
		// Reset for each filter call
		$this->current_message_id = 0;

		// Use debug_backtrace to get the $message object from bb_get_message_response_object
		$backtrace = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 25 );


		// Log all function names in the backtrace
		$function_names = array();
		foreach ( $backtrace as $trace ) {
			if ( isset( $trace['function'] ) ) {
				$function_names[] = $trace['function'];
			}
		}

		foreach ( $backtrace as $trace ) {
			// Look for the bb_get_message_response_object function
			if ( isset( $trace['function'] ) && $trace['function'] === 'bb_get_message_response_object' ) {
				// Check if we have access to the function's local variables
				if ( isset( $trace['args'][0] ) && is_object( $trace['args'][0] ) && isset( $trace['args'][0]->id ) ) {
					$this->current_message_id = intval( $trace['args'][0]->id );
					break;
				}
			}
		}

		if ( $this->current_message_id === 0 ) {
		}

		return $content;
	}

	/**
	 * Inject shared activity card after message content (for AJAX responses).
	 *
	 * This action hook is called by BuddyBoss to capture content after the message,
	 * which is then included in the AJAX response as 'afterContent'.
	 *
	 * We need to use a filter on bb_get_message_response_object to inject our content
	 * because the bp_after_message_content hook doesn't have access to the message object.
	 *
	 * @since 1.0.0
	 */
	public function inject_shared_activity_after_message() {
		// This method is deprecated - we now use the filter on bb_messages_get_reply_object instead
		// Keeping it here for reference but it won't be called
	}

	/**
	 * AJAX handler to share activity via message.
	 *
	 * @since 1.0.0
	 */
	public function ajax_share_to_message() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		$activity_id     = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;
		$thread_ids      = isset( $_POST['thread_ids'] ) ? array_map( 'intval', (array) $_POST['thread_ids'] ) : array();
		$member_ids      = isset( $_POST['member_ids'] ) ? array_map( 'intval', (array) $_POST['member_ids'] ) : array();
		$custom_message  = isset( $_POST['custom_message'] ) ? $_POST['custom_message'] : '';

		if ( ! $activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid activity.', 'buddyboss-sharing' ) ) );
		}

		if ( empty( $thread_ids ) && empty( $member_ids ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Please select at least one recipient or thread.', 'buddyboss-sharing' ) ) );
		}

		// Check if messages component is active
		if ( ! bp_is_active( 'messages' ) || ! function_exists( 'messages_new_message' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Messaging is not available.', 'buddyboss-sharing' ) ) );
		}

		// Get the original activity ID (in case of a shared activity)
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		// Get the original activity
		$original_activity = new \BP_Activity_Activity( $original_activity_id );

		if ( ! $original_activity->id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Activity not found.', 'buddyboss-sharing' ) ) );
		}

		// Check if current user can access this activity before sharing
		$current_user_id = get_current_user_id();
		$can_read        = true;

		// Use bb_validate_activity_privacy if available (more comprehensive)
		if ( function_exists( 'bb_validate_activity_privacy' ) ) {
			$privacy_check = bb_validate_activity_privacy(
				array(
					'activity_id'     => $original_activity->id,
					'user_id'         => $current_user_id,
					'validate_action' => 'view_activity',
					'activity_type'   => 'activity',
				)
			);

			// If privacy check returns WP_Error, user doesn't have access
			if ( is_wp_error( $privacy_check ) ) {
				$can_read = false;
			}
		} elseif ( function_exists( 'bp_activity_user_can_read' ) ) {
			// Fallback to bp_activity_user_can_read
			$can_read = bp_activity_user_can_read( $original_activity, $current_user_id );
		}

		// If user cannot read the activity, don't allow sharing
		if ( ! $can_read ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to share this activity.', 'buddyboss-sharing' ) ) );
		}

		// Store only the custom message text as content
		// The activity card will be rendered dynamically via the bp_get_the_thread_message_content filter
		$message_content = ! empty( $custom_message ) ? $custom_message : '';

		$sent_count = 0;

		// Send to existing threads (add message to thread)
		if ( ! empty( $thread_ids ) ) {
			foreach ( $thread_ids as $thread_id ) {
				// Use BuddyBoss function to send message to existing thread
				$message_id = messages_new_message(
					array(
						'sender_id'  => get_current_user_id(),
						'thread_id'  => $thread_id,
						'subject'    => esc_html__( 'Shared Activity', 'buddyboss-sharing' ),
						'content'    => $message_content,
					)
				);

				// Debug logging

			if ( $message_id ) {
					// Store shared activity ID in message meta
					bp_messages_update_meta( $message_id, 'shared_activity_id', $original_activity_id );
					$sent_count++;
				}
			}
		}

		// Send to individual members (create new threads)
		if ( ! empty( $member_ids ) ) {
			foreach ( $member_ids as $member_id ) {
				$message_id = messages_new_message(
					array(
						'sender_id'  => get_current_user_id(),
						'recipients' => array( $member_id ),
						'subject'    => esc_html__( 'Shared Activity', 'buddyboss-sharing' ),
						'content'    => $message_content,
					)
				);

				// Debug logging

			if ( $message_id ) {
					// Store shared activity ID in message meta
					bp_messages_update_meta( $message_id, 'shared_activity_id', $original_activity_id );
					$sent_count++;
				}
			}
		}

		if ( $sent_count > 0 ) {
			// Update share count
			$share_count = (int) bp_activity_get_meta( $original_activity_id, 'share_count', true );
			bp_activity_update_meta( $original_activity_id, 'share_count', $share_count + 1 );

			// Build success message
			$total_targets = count( $thread_ids ) + count( $member_ids );
			$message = sprintf(
				/* translators: %d: number of conversations */
				_n(
					'Message sent to %d conversation successfully!',
					'Messages sent to %d conversations successfully!',
					$total_targets,
					'buddyboss-sharing'
				),
				$total_targets
			);

			wp_send_json_success(
				array(
					'message'     => $message,
					'share_count' => $share_count + 1,
				)
			);
		} else {
			wp_send_json_error( array( 'message' => esc_html__( 'Failed to send messages.', 'buddyboss-sharing' ) ) );
		}
	}

	/**
	 * AJAX handler to search members for message sharing.
	 * Uses the same approach as BuddyBoss native messaging search.
	 *
	 * @since 1.0.0
	 */
	public function ajax_search_members_for_message() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		$search_term = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';

		if ( empty( $search_term ) ) {
			wp_send_json_success( array( 'members' => array() ) );
		}

		// Use BuddyBoss suggestions system (same as native messaging)
		// This automatically excludes current user and respects friendship restrictions
		add_filter( 'bp_members_suggestions_query_args', array( $this, 'search_recipients_exclude_current' ) );

		// If friends component is active and force friendship to message is enabled, add filter
		if (
			function_exists( 'bp_is_active' ) &&
			bp_is_active( 'friends' ) &&
			function_exists( 'bp_force_friendship_to_message' ) &&
			bp_force_friendship_to_message() &&
			function_exists( 'bb_messages_allowed_messaging_without_connection' ) &&
			empty( bb_messages_allowed_messaging_without_connection( bp_loggedin_user_id() ) )
		) {
			add_filter( 'bp_user_query_uid_clauses', 'bb_messages_update_recipient_user_query_uid_clauses', 9999, 2 );
		}

		$results = bp_core_get_suggestions(
			array(
				'term'            => $search_term,
				'type'            => 'members',
				'only_friends'    => false,
				'count_total'     => 'count_query',
				'limit'           => 20,
				'populate_extras' => true,
			)
		);

		// Remove friendship filter if it was added
		if (
			function_exists( 'bp_is_active' ) &&
			bp_is_active( 'friends' ) &&
			function_exists( 'bp_force_friendship_to_message' ) &&
			bp_force_friendship_to_message() &&
			function_exists( 'bb_messages_allowed_messaging_without_connection' ) &&
			empty( bb_messages_allowed_messaging_without_connection( bp_loggedin_user_id() ) )
		) {
			remove_filter( 'bp_user_query_uid_clauses', 'bb_messages_update_recipient_user_query_uid_clauses', 9999 );
		}

		remove_filter( 'bp_members_suggestions_query_args', array( $this, 'search_recipients_exclude_current' ) );

		$members_results = $results['members'] ?? array();
		$formatted_results = array();

		if ( ! empty( $members_results ) ) {
			foreach ( $members_results as $member ) {
				$formatted_results[] = array(
					'id'     => (int) $member->user_id,
					'name'   => esc_html( $member->name ),
					'avatar' => esc_url( $member->image ),
				);
			}
		}

		wp_send_json_success( array( 'members' => $formatted_results ) );
	}

	/**
	 * Exclude logged in member from recipients list.
	 *
	 * @since 1.0.0
	 * @param array $user_query User query args.
	 * @return array
	 */
	public function search_recipients_exclude_current( $user_query ) {
		if ( isset( $user_query['exclude'] ) && ! $user_query['exclude'] ) {
			$user_query['exclude'] = array();
		} elseif ( ! empty( $user_query['exclude'] ) ) {
			$user_query['exclude'] = wp_parse_id_list( $user_query['exclude'] );
		}

		$user_query['exclude'][] = get_current_user_id();

		// Avoid duplicate user IDs.
		$user_query['exclude'] = array_unique( $user_query['exclude'] );

		return $user_query;
	}

	/**
	 * AJAX handler to get recent message threads for message sharing.
	 * Uses the same approach as BuddyBoss Platform's bp_nouveau_ajax_get_user_message_threads.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_recent_message_threads() {
		global $messages_template;

		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		if ( ! bp_is_active( 'messages' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Messages component is not active.', 'buddyboss-sharing' ) ) );
		}

		// Get page parameter for pagination support
		$page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$page = max( 1, $page ); // Ensure page is at least 1

		$bp           = buddypress();
		$reset_action = $bp->current_action;

		// Override bp_current_action() to use inbox.
		$bp->current_action = 'inbox';

		// Build query string for message threads.
		$querystring = http_build_query(
			array(
				'user_id'  => get_current_user_id(),
				'box'      => 'inbox',
				'type'     => 'all',
				'page'     => $page,
				'per_page' => 100,
			)
		);

		// Simulate the loop to get threads.
		if ( ! bp_has_message_threads( $querystring ) ) {
			// Remove the bp_current_action() override.
			$bp->current_action = $reset_action;

			wp_send_json_success(
				array(
					'threads' => array(),
					'total'   => 0,
				)
			);
			return;
		}

		$threads = array();
		$thread_count = 0;
		$max_threads = 100; // Limit to match per_page

		while ( bp_message_threads() && $thread_count < $max_threads ) :
			bp_message_thread();

			$thread_id        = bp_get_message_thread_id();
			$check_recipients = (array) $messages_template->thread->recipients;

			// Check if this is a group thread.
			$is_group_thread = 0;
			$group_id        = 0;
			$group_name      = '';
			$group_avatar    = '';
			$first_message   = \BP_Messages_Thread::get_first_message( $thread_id );

			if ( isset( $first_message->id ) ) {
				$group_id = (int) bp_messages_get_meta( $first_message->id, 'group_id', true );

				if ( $group_id > 0 ) {
					$group_message_thread_id = bp_messages_get_meta( $first_message->id, 'group_message_thread_id', true );
					$message_users           = bp_messages_get_meta( $first_message->id, 'group_message_users', true );
					$message_type            = bp_messages_get_meta( $first_message->id, 'group_message_type', true );
					$message_from            = bp_messages_get_meta( $first_message->id, 'message_from', true );

					if ( 'group' === $message_from && $thread_id === (int) $group_message_thread_id && 'all' === $message_users && 'open' === $message_type ) {
						$is_group_thread = 1;

						if ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
							$group = groups_get_group( $group_id );
							if ( $group ) {
								$group_name = bp_get_group_name( $group );

								if ( ! bp_disable_group_avatar_uploads() ) {
									$group_avatar = bp_core_fetch_avatar(
										array(
											'item_id'    => $group_id,
											'object'     => 'group',
											'type'       => 'full',
											'avatar_dir' => 'group-avatars',
											'html'       => false,
										)
									);
								} else {
									$group_avatar = function_exists( 'bb_get_buddyboss_group_avatar' ) ? bb_get_buddyboss_group_avatar() : '';
								}
							}
						}

						if ( empty( $group_name ) ) {
							$group_name = esc_html__( 'Deleted Group', 'buddyboss-sharing' );
						}
					}
				}
			}

			if ( is_array( $check_recipients ) ) {
				$thread_recipients = array();
				$recipient_count   = 0;

				foreach ( $check_recipients as $recipient ) {
					// Skip current user and deleted recipients.
					if ( empty( $recipient->is_deleted ) && (int) $recipient->user_id !== get_current_user_id() ) {
						$thread_recipients[ $recipient->user_id ] = array(
							'id'     => (int) $recipient->user_id,
							'name'   => esc_html( bp_core_get_user_displayname( $recipient->user_id ) ),
							'avatar' => esc_url( bp_core_fetch_avatar(
								array(
									'item_id' => $recipient->user_id,
									'object'  => 'user',
									'type'    => 'thumb',
									'width'   => 50,
									'height'  => 50,
									'html'    => false,
								)
							) ),
						);
						$recipient_count++;
					}
				}

				// Only include threads with recipients.
				if ( ! empty( $thread_recipients ) ) {
					// Get avatars for the thread (BuddyBoss native function).
					$avatars = array();
					if ( function_exists( 'bp_messages_get_avatars' ) ) {
						$avatars = bp_messages_get_avatars( $thread_id, get_current_user_id() );
					}

					// Calculate "toOthers" - shows "+X" when there are more than 4 recipients.
					$to_others = '';
					if ( $recipient_count > 4 ) {
						$to_others = '+' . ( $recipient_count - 4 );
					}

					$threads[] = array(
						'thread_id'       => (int) $thread_id,
						'recipients'      => $thread_recipients,
						'recipientsCount' => (int) $recipient_count,
						'avatars'         => $avatars,
						'toOthers'        => esc_html( $to_others ),
						'is_group_thread' => (int) $is_group_thread,
						'group_id'        => (int) $group_id,
						'group_name'      => esc_html( $group_name ),
						'group_avatar'    => esc_url( $group_avatar ),
					);
					$thread_count++; // Increment counter when thread is added
				}
			}

		endwhile;

		// Remove the bp_current_action() override.
		$bp->current_action = $reset_action;

		// Calculate pagination info
		$total_threads = isset( $messages_template->total_thread_count ) ? $messages_template->total_thread_count : 0;
		$per_page      = 100;
		$total_pages   = ceil( $total_threads / $per_page );
		$has_more      = $page < $total_pages;

		wp_send_json_success(
			array(
				'threads'     => $threads,
				'total'       => $total_threads,
				'page'        => $page,
				'total_pages' => $total_pages,
				'has_more'    => $has_more,
			)
		);
	}

	/**
	 * AJAX handler to get groups for sharing.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_groups_for_sharing() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		if ( ! bp_is_active( 'groups' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Groups component is not active.', 'buddyboss-sharing' ) ) );
		}

		// Get parameters
		$search_term = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
		$page        = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$per_page    = 20;

		// Build query args
		$args = array(
			'user_id'  => get_current_user_id(),
			'per_page' => $per_page,
			'page'     => $page,
			'orderby'  => 'last_activity',
			'order'    => 'DESC',
			'show_hidden' => false, // Don't show hidden groups
		);

		// Add search if provided
		if ( ! empty( $search_term ) ) {
			$args['search_terms'] = $search_term;
		}

		// Get groups
		$groups_query = groups_get_groups( $args );
		$groups       = array();

		if ( ! empty( $groups_query['groups'] ) ) {
			foreach ( $groups_query['groups'] as $group ) {
				// Get group avatar
				$group_avatar = '';
				if ( ! bp_disable_group_avatar_uploads() ) {
					$group_avatar = bp_core_fetch_avatar(
						array(
							'item_id'    => $group->id,
							'object'     => 'group',
							'type'       => 'full',
							'avatar_dir' => 'group-avatars',
							'html'       => false,
						)
					);
				} else {
					$group_avatar = function_exists( 'bb_get_buddyboss_group_avatar' ) ? bb_get_buddyboss_group_avatar() : '';
				}

				// Get group status (privacy level)
				$group_status = bp_get_group_status( $group );
				$group_status_label = '';

				switch ( $group_status ) {
					case 'public':
						$group_status_label = esc_html__( 'Public', 'buddyboss-sharing' );
						break;
					case 'private':
						$group_status_label = esc_html__( 'Private', 'buddyboss-sharing' );
						break;
					case 'hidden':
						$group_status_label = esc_html__( 'Hidden', 'buddyboss-sharing' );
						break;
				}

				// Get group type(s)
				$group_type = '';
				$group_type_label = '';
				if ( function_exists( 'bp_groups_get_group_type' ) ) {
					$group_types = bp_groups_get_group_type( $group->id, false );
					if ( ! empty( $group_types ) ) {
						// Get the first group type
						$group_type = is_array( $group_types ) ? $group_types[0] : $group_types;

						// Get the label for the group type
						if ( function_exists( 'bp_groups_get_group_type_object' ) ) {
							$type_object = bp_groups_get_group_type_object( $group_type );
							if ( $type_object && isset( $type_object->labels['singular_name'] ) ) {
								$group_type_label = $type_object->labels['singular_name'];
							}
						}
					}
				}

				$groups[] = array(
					'id'     => (int) $group->id,
					'name'   => esc_html( bp_get_group_name( $group ) ),
					'avatar' => esc_url( $group_avatar ),
					'status' => esc_attr( $group_status ),
					'status_label' => esc_html( $group_status_label ),
					'type'   => esc_attr( $group_type ),
					'type_label' => esc_html( $group_type_label ),
				);
			}
		}

		wp_send_json_success(
			array(
				'groups'      => $groups,
				'total'       => $groups_query['total'],
				'page'        => $page,
				'per_page'    => $per_page,
				'total_pages' => ceil( $groups_query['total'] / $per_page ),
				'has_more'    => $page < ceil( $groups_query['total'] / $per_page ),
			)
		);
	}

	/**
	 * AJAX handler to share activity to group.
	 *
	 * @since 1.0.0
	 */
	public function ajax_share_to_group() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		if ( ! bp_is_active( 'groups' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Groups component is not active.', 'buddyboss-sharing' ) ) );
		}

		$activity_id    = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;
		$group_id       = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : 0;
		$custom_message = isset( $_POST['custom_message'] ) ? $_POST['custom_message'] : '';

		if ( ! $activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid activity.', 'buddyboss-sharing' ) ) );
		}

		if ( ! $group_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid group.', 'buddyboss-sharing' ) ) );
		}

		// Verify user is a member of the group
		if ( ! groups_is_user_member( get_current_user_id(), $group_id ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You are not a member of this group.', 'buddyboss-sharing' ) ) );
		}

		// Get the original activity ID (in case of a shared activity)
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		$original_activity = new \BP_Activity_Activity( $original_activity_id );

		if ( ! $original_activity->id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Activity not found.', 'buddyboss-sharing' ) ) );
		}

		// Store only custom message as content, NOT the HTML preview.
		// The preview will be rendered dynamically using the shared_activity_id meta.
		$content = ! empty( $custom_message ) ? $custom_message : '';

		// Apply the same content filter that BuddyBoss Platform uses for activity updates.
		// This ensures emoji and other content processing is handled correctly.
		$content = apply_filters( 'bp_activity_new_update_content', $content );

		// Create new group activity.
		$args = array(
			'user_id'   => get_current_user_id(),
			'content'   => $content,
			'type'      => 'activity_share',
			'component' => 'groups',
			'item_id'   => $group_id,
		);

		$new_activity_id = bp_activity_add( $args );

		if ( $new_activity_id ) {
			// Store shared activity ID.
			bp_activity_update_meta( $new_activity_id, 'shared_activity_id', $original_activity_id );

			// Update share count.
			$share_count = (int) bp_activity_get_meta( $original_activity_id, 'share_count', true );
			bp_activity_update_meta( $original_activity_id, 'share_count', $share_count + 1 );

			wp_send_json_success(
				array(
					'message'     => esc_html__( 'Shared to group successfully!', 'buddyboss-sharing' ),
					'share_count' => $share_count + 1,
				)
			);
		} else {
			wp_send_json_error( array( 'message' => esc_html__( 'Failed to share to group.', 'buddyboss-sharing' ) ) );
		}
	}

	/**
	 * Save group message meta.
	 *
	 * @since 1.0.0
	 *
	 * @param object $message Message object.
	 */
	public function bb_share_messages_save_activity_data( &$message ) {
		// Check if messages component is active
		if ( ! bp_is_active( 'messages' ) || ! function_exists( 'bp_messages_update_meta' ) ) {
			return;
		}

		$activity_id   = ( isset( $_POST ) && isset( $_POST['activity_id'] ) && '' !== $_POST['activity_id'] ) ? trim( $_POST['activity_id'] ) : ''; // Activity ID.
		$thread_action = ( isset( $_POST ) && isset( $_POST['action'] ) && '' !== $_POST['action'] ) ? trim( $_POST['action'] ) : ''; // Thread action.

		bp_messages_update_meta( $message->id, 'shared_activity_id', $activity_id );
		bp_messages_update_meta( $message->id, 'thread_action', $thread_action );

	}

	/**
	 * Validate message if sharing is not empty.
	 *
	 * @since 1.0.0
	 *
	 * @param bool         $validated_content True if message is valid, false otherwise.
	 * @param string       $content           Message content.
	 * @param array|object $post              Request object.
	 *
	 * @return bool
	 *
	 */
	function bb_sharing_message_validated_content( $validated_content, $content, $post ) {
		// Check if activity component is active
		if ( ! bp_is_active( 'activity' ) ) {
			return $validated_content;
		}

		$action      = ! empty( $post['action'] ) ? sanitize_text_field( wp_unslash( $post['action'] ) ) : '';
		$activity_id = ! empty( $post['activity_id'] ) ? (int) sanitize_text_field( wp_unslash( $post['activity_id'] ) ) : 0;

		// If sharing activity is set, ensure activity ID is valid.
		if ( 'buddyboss_share_to_message' === $action && $activity_id > 0 ) {
			$original_activity = new \BP_Activity_Activity( $activity_id );

			if ( $original_activity->id ) {
				return true;
			}
		}

		return $validated_content;
	}

	/**
	 * AJAX handler to get friends for sharing
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_friends_for_sharing() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		if ( ! bp_is_active( 'friends' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Friends component is not active.', 'buddyboss-sharing' ) ) );
		}

	// Get parameters
	$search_term = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
	$page        = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
	$per_page    = 20;

	// Build query args using BP_User_Query
	$args = array(
		'type'            => 'alphabetical',
		'per_page'        => $per_page,
		'page'            => $page,
		'populate_extras' => false,
		'user_id'         => get_current_user_id(),
	);

	// Add search if provided
	if ( ! empty( $search_term ) ) {
		$args['search_terms'] = $search_term;
	}

	// Get friend IDs first
	$friend_ids = friends_get_friend_user_ids( get_current_user_id() );

	if ( empty( $friend_ids ) ) {
		wp_send_json_success(
			array(
				'friends' => array(),
				'page'    => $page,
				'total'   => 0,
			)
		);
	}

	// Add friend IDs to query
	$args['include'] = $friend_ids;

	// Query friends
	$friends_query = new \BP_User_Query( $args );
	$friends       = array();

	if ( ! empty( $friends_query->results ) ) {
		foreach ( $friends_query->results as $user ) {
			// Get user avatar
			$friend_avatar = bp_core_fetch_avatar(
				array(
					'item_id' => $user->ID,
					'type'    => 'full',
					'html'    => false,
				)
			);

			$friends[] = array(
				'id'     => (int) $user->ID,
				'name'   => esc_html( $user->display_name ),
				'avatar' => esc_url( $friend_avatar ),
			);
		}
	}

	wp_send_json_success(
		array(
			'friends' => $friends,
			'page'    => $page,
			'total'   => $friends_query->total_users,
		)
	);
	}

	/**
	 * AJAX handler to share activity to friend's profile
	 *
	 * @since 1.0.0
	 */
	public function ajax_share_to_friend_profile() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		// Get parameters
		$activity_id     = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;
		$friend_id       = isset( $_POST['friend_id'] ) ? intval( $_POST['friend_id'] ) : 0;
		$custom_message  = isset( $_POST['custom_message'] ) ? $_POST['custom_message'] : '';

		// Validate
		if ( ! $activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid activity ID.', 'buddyboss-sharing' ) ) );
		}

		if ( ! $friend_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid friend ID.', 'buddyboss-sharing' ) ) );
		}

		// Check if users are actually friends
		if ( bp_is_active( 'friends' ) ) {
			if ( ! friends_check_friendship( get_current_user_id(), $friend_id ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'You can only share to friends.', 'buddyboss-sharing' ) ) );
			}
		}

		// Get the original activity ID (in case of a shared activity)
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		// Get original activity
		$original_activity = new \BP_Activity_Activity( $original_activity_id );

		if ( empty( $original_activity->id ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Original activity not found.', 'buddyboss-sharing' ) ) );
		}

		// Get friend's user object to add @mention
		$friend_user = get_userdata( $friend_id );
		if ( ! $friend_user ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Friend user not found.', 'buddyboss-sharing' ) ) );
		}

		// Store only custom message as content, NOT the HTML preview.
		// The preview will be rendered dynamically using the shared_activity_id meta.
		// Prepend @mention of the friend so they get notified (same as posting on their profile)
		$mention = '@' . $friend_user->user_nicename . ' ';
		$content = $mention;
		if ( ! empty( $custom_message ) ) {
			$content .= $custom_message;
		}

		// Apply the same content filter that BuddyBoss Platform uses for activity updates.
		// This ensures emoji and other content processing is handled correctly.
		$content = apply_filters( 'bp_activity_new_update_content', $content );

		// Create the activity on friend's profile
		$new_activity_id = bp_activity_add(
			array(
				'user_id'   => get_current_user_id(),
				'action'    => sprintf(
					/* translators: %1$s: user link, %2$s: friend link */
					esc_html__( '%1$s shared a post with %2$s', 'buddyboss-sharing' ),
					bp_core_get_userlink( get_current_user_id() ),
					bp_core_get_userlink( $friend_id )
				),
				'content'   => $content,
				'type'      => 'activity_share',
				'component' => 'activity',
				'item_id'   => $friend_id, // Friend's user ID as item_id
			)
		);

		if ( ! $new_activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Failed to share activity.', 'buddyboss-sharing' ) ) );
		}

		// Store shared activity ID as metadata
		bp_activity_update_meta( $new_activity_id, 'shared_activity_id', $original_activity_id );

		// Update share count
		$share_count = (int) bp_activity_get_meta( $original_activity_id, 'share_count', true );
		bp_activity_update_meta( $original_activity_id, 'share_count', $share_count + 1 );

		// Send notification to friend
		if ( bp_is_active( 'notifications' ) ) {
			bp_notifications_add_notification(
				array(
					'user_id'           => $friend_id,
					'item_id'           => $new_activity_id,
					'secondary_item_id' => get_current_user_id(),
					'component_name'    => 'activity',
					'component_action'  => 'activity_share',
					'date_notified'     => bp_core_current_time(),
					'is_new'            => 1,
				)
			);
		}

		wp_send_json_success(
			array(
				'message'       => esc_html__( 'Activity shared successfully on your friend\'s profile!', 'buddyboss-sharing' ),
				'activity_id'   => $new_activity_id,
				'share_count'   => $share_count + 1,
			)
		);
	}


	/**
	 * AJAX handler to get activity permalink.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_activity_permalink() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'buddyboss_seo_share' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Security check failed.', 'buddyboss-sharing' ) ) );
		}

		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		// Get activity ID
		$activity_id = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;

		if ( ! $activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid activity ID.', 'buddyboss-sharing' ) ) );
		}

		// Get the original activity ID (in case of a shared activity)
		// When sharing a link to a shared activity, we want to link to the original
		$original_activity_id = $this->get_original_activity_id( $activity_id );

		// Get activity object using the original ID
		$activity = new \BP_Activity_Activity( $original_activity_id );

		if ( ! $activity->id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Activity not found.', 'buddyboss-sharing' ) ) );
		}

		// Build member activity URL format: /members/{username}/activity/{id}/
		// This format is preferred over /activity-feed/p/{id}/ for external sharing
		$permalink = '';

		if ( ! empty( $activity->user_id ) && function_exists( 'bp_core_get_user_domain' ) ) {
			// Get user domain (e.g., https://site.com/members/username/)
			$user_domain = bp_core_get_user_domain( $activity->user_id );

			if ( $user_domain ) {
				// Build: /members/{username}/activity/{original_id}/
				// Use original activity ID, not the shared activity ID
				$permalink = trailingslashit( $user_domain ) . 'activity/' . $original_activity_id . '/';
			}
		}

		// Fallback to default permalink if user domain not available
		if ( empty( $permalink ) ) {
			$permalink = bp_activity_get_permalink( $original_activity_id, $activity );
		}

		if ( ! $permalink ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Could not generate activity permalink.', 'buddyboss-sharing' ) ) );
		}

		wp_send_json_success(
			array(
				'permalink' => $permalink,
			)
		);
	}

	/**
	 * AJAX handler to load full activity in modal.
	 *
	 * @since 1.0.0
	 */
	public function ajax_load_shared_activity_modal() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'buddyboss_seo_share' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Security check failed.', 'buddyboss-sharing' ) ) );
		}

		// Get activity ID
		$activity_id = isset( $_POST['activity_id'] ) ? intval( $_POST['activity_id'] ) : 0;

		if ( ! $activity_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid activity ID.', 'buddyboss-sharing' ) ) );
		}

		// Get activity object
		$activity = new \BP_Activity_Activity( $activity_id );

		if ( ! $activity->id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Activity not found.', 'buddyboss-sharing' ) ) );
		}

		// Check if user can read this activity
		// For logged-out users, bp_activity_user_can_read will check if activity is public/accessible
		if ( function_exists( 'bp_activity_user_can_read' ) ) {
			$can_read = bp_activity_user_can_read( $activity );
			if ( ! $can_read ) {
				wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to view this activity.', 'buddyboss-sharing' ) ) );
			}
		}

		// Capture the activity output with UL wrapper
		ob_start();

		// Load only the single activity
		if ( bp_has_activities(
			array(
				'include'          => $activity_id,
				'display_comments' => 'threaded',
				'show_hidden'      => true,
				'spam'             => 'ham_only',
			)
		) ) {
			?>
			<ul class="activity-list item-list bp-list">
				<?php
				while ( bp_activities() ) {
					bp_the_activity();
					bp_get_template_part( 'activity/entry' );
				}
				?>
			</ul>
			<?php
		}

		$activity_html = ob_get_clean();

		// Get activity author name for modal title
		$author_name = bp_core_get_user_displayname( $activity->user_id );

		wp_send_json_success(
			array(
				'activity_html' => $activity_html,
				'author_name'   => $author_name,
				'activity_id'   => $activity_id,
			)
		);
	}

	/**
	 * AJAX handler to load activity post form template.
	 * Used on single activity pages where the form is not loaded by default.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_activity_post_form() {
		check_ajax_referer( 'buddyboss_seo_share', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You must be logged in.', 'buddyboss-sharing' ) ) );
		}

		// Check if user can create activity
		if ( ! function_exists( 'bb_user_can_create_activity' ) || ! bb_user_can_create_activity() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to create activities.', 'buddyboss-sharing' ) ) );
		}

		// Check if ReadyLaunch is enabled
		$is_readylaunch = function_exists( 'bb_is_readylaunch_enabled' ) && bb_is_readylaunch_enabled();

		// Check if media is active
		$media_enabled_class = '';
		if ( ! bp_is_active( 'media' ) ) {
			$media_enabled_class = ' media-off';
		}

		// Start output buffering to capture the form template
		ob_start();

		// Load the activity post form template
		// This mimics what bp_nouveau_activity_member_post_form() does
		if ( function_exists( 'bp_nouveau_before_activity_post_form' ) ) {
			bp_nouveau_before_activity_post_form();
		}

		// Output the form structure based on ReadyLaunch or regular version
		if ( $is_readylaunch ) {
			// ReadyLaunch version
			?>
			<h2 class="bb-rl-screen-reader-text"><?php esc_html_e( 'Post Update', 'buddyboss' ); ?></h2>
			<div id="bb-rl-activity-form-placeholder" class="bb-rl-activity-form-placeholder-<?php echo esc_attr( $media_enabled_class ); ?>"></div>
			<div id="bb-rl-activity-form" class="bb-rl-activity-update-form<?php echo esc_attr( $media_enabled_class ); ?>"></div>
			<?php
		} else {
			// Regular version
			?>
			<h2 class="bp-screen-reader-text"><?php esc_html_e( 'Post Update', 'buddyboss' ); ?></h2>
			<div id="bp-nouveau-activity-form-placeholder" class="bp-nouveau-activity-form-placeholder-<?php echo esc_attr( $media_enabled_class ); ?>"></div>
			<div id="bp-nouveau-activity-form" class="activity-update-form<?php echo esc_attr( $media_enabled_class ); ?>"></div>
			<?php
		}

		if ( function_exists( 'bp_nouveau_after_activity_post_form' ) ) {
			bp_nouveau_after_activity_post_form();
		}

		$form_html = ob_get_clean();

		wp_send_json_success(
			array(
				'form_html' => $form_html,
				'is_readylaunch' => $is_readylaunch,
			)
		);
	}

	/**
	 * Generate link preview from content with OG tags.
	 *
	 * This function extracts the first URL from the content, fetches its Open Graph tags,
	 * and generates an HTML preview card. For YouTube links, it generates an embedded player.
	 *
	 * @since 1.0.0
	 * @param string $content The content to extract URL from.
	 * @return string HTML preview or empty string if no URL found or preview failed.
	 */
	public function generate_link_preview_from_content( $content ) {
		if ( empty( $content ) ) {
			return '';
		}

		// Extract first URL from content
		$url = $this->extract_first_url( $content );

		if ( empty( $url ) ) {
			return '';
		}

		// Check if URL is a YouTube video
		$youtube_id = $this->extract_youtube_video_id( $url );
		if ( $youtube_id ) {
			return $this->generate_youtube_embed( $youtube_id, $url );
		}

		// For non-YouTube URLs, fetch OG tags
		$og_data = $this->fetch_og_tags( $url );

		if ( empty( $og_data ) || empty( $og_data['title'] ) ) {
			return '';
		}

		// Generate HTML preview
		return $this->generate_og_preview_html( $og_data, $url );
	}

	/**
	 * Extract first URL from content.
	 *
	 * @since 1.0.0
	 * @param string $content Content to extract URL from.
	 * @return string|null First URL found or null.
	 */
	private function extract_first_url( $content ) {
		// Strip HTML tags to get plain text
		$text = wp_strip_all_tags( $content );

		// Regex pattern to match URLs
		$pattern = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#i';

		if ( preg_match( $pattern, $text, $matches ) ) {
			return $matches[0];
		}

		return null;
	}

	/**
	 * Fetch Open Graph tags from URL.
	 *
	 * @since 1.0.0
	 * @param string $url URL to fetch OG tags from.
	 * @return array Array of OG tag data or empty array on failure.
	 */
	private function fetch_og_tags( $url ) {
		// Check cache first
		$cache_key = 'bbshare_og_' . md5( $url );
		$cached = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		// Fetch URL content
		$response = wp_remote_get(
			$url,
			array(
				'timeout'    => 10,
				'user-agent' => 'Mozilla/5.0 (compatible; BuddyBoss-Sharing/1.0; +' . home_url() . ')',
			)
		);

		if ( is_wp_error( $response ) ) {
			return array();
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return array();
		}

		// Parse OG tags
		$og_data = $this->parse_og_tags_from_html( $body );

		// Cache for 1 hour
		if ( ! empty( $og_data ) ) {
			set_transient( $cache_key, $og_data, HOUR_IN_SECONDS );
		}

		return $og_data;
	}

	/**
	 * Parse Open Graph tags from HTML content.
	 *
	 * @since 1.0.0
	 * @param string $html HTML content.
	 * @return array Array of OG tag data.
	 */
	private function parse_og_tags_from_html( $html ) {
		$og_data = array(
			'title'       => '',
			'description' => '',
			'image'       => '',
			'url'         => '',
			'site_name'   => '',
			'type'        => '',
		);

		// Parse og:property meta tags
		if ( preg_match_all( '/<meta[^>]+property=["\']og:([^"\']+)["\'][^>]+content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches ) ) {
			foreach ( $matches[1] as $index => $property ) {
				if ( isset( $og_data[ $property ] ) ) {
					$og_data[ $property ] = html_entity_decode( $matches[2][ $index ], ENT_QUOTES, 'UTF-8' );
				}
			}
		}

		// Also try reversed attribute order (content before property)
		if ( preg_match_all( '/<meta[^>]+content=["\']([^"\']*)["\'][^>]+property=["\']og:([^"\']+)["\'][^>]*>/i', $html, $matches ) ) {
			foreach ( $matches[2] as $index => $property ) {
				if ( isset( $og_data[ $property ] ) && empty( $og_data[ $property ] ) ) {
					$og_data[ $property ] = html_entity_decode( $matches[1][ $index ], ENT_QUOTES, 'UTF-8' );
				}
			}
		}

		// Fallback to standard meta tags if OG tags not found
		if ( empty( $og_data['title'] ) ) {
			if ( preg_match( '/<title[^>]*>([^<]+)<\/title>/i', $html, $match ) ) {
				$og_data['title'] = html_entity_decode( $match[1], ENT_QUOTES, 'UTF-8' );
			}
		}

		if ( empty( $og_data['description'] ) ) {
			if ( preg_match( '/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']*)["\'][^>]*>/i', $html, $match ) ) {
				$og_data['description'] = html_entity_decode( $match[1], ENT_QUOTES, 'UTF-8' );
			}
		}

		return $og_data;
	}

	/**
	 * Generate HTML preview from OG data.
	 *
	 * @since 1.0.0
	 * @param array  $og_data Array of OG tag data.
	 * @param string $url     Original URL.
	 * @return string HTML preview.
	 */
	private function generate_og_preview_html( $og_data, $url ) {
		ob_start();
		?>
		<div class="bb-link-preview-container">
			<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="bb-link-preview">
				<?php if ( ! empty( $og_data['image'] ) ) : ?>
					<div class="bb-link-preview-image">
						<img src="<?php echo esc_url( $og_data['image'] ); ?>" alt="<?php echo esc_attr( $og_data['title'] ); ?>" loading="lazy" />
					</div>
				<?php endif; ?>
				<div class="bb-link-preview-content">
					<?php if ( ! empty( $og_data['site_name'] ) ) : ?>
						<div class="bb-link-preview-site">
							<?php echo esc_html( $og_data['site_name'] ); ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $og_data['title'] ) ) : ?>
						<div class="bb-link-preview-title">
							<?php echo esc_html( $og_data['title'] ); ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $og_data['description'] ) ) : ?>
						<div class="bb-link-preview-description">
							<?php echo esc_html( wp_trim_words( $og_data['description'], 30, '...' ) ); ?>
						</div>
					<?php endif; ?>
					<div class="bb-link-preview-url">
						<?php echo esc_html( parse_url( $url, PHP_URL_HOST ) ); ?>
					</div>
				</div>
			</a>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Extract YouTube video ID from URL.
	 *
	 * Supports various YouTube URL formats:
	 * - https://www.youtube.com/watch?v=VIDEO_ID
	 * - https://youtu.be/VIDEO_ID
	 * - https://www.youtube.com/embed/VIDEO_ID
	 * - https://www.youtube.com/v/VIDEO_ID
	 *
	 * @since 1.0.0
	 * @param string $url YouTube URL.
	 * @return string|false Video ID or false if not a YouTube URL.
	 */
	private function extract_youtube_video_id( $url ) {
		// Pattern for various YouTube URL formats
		$patterns = array(
			'#^https?://(?:www\.)?youtube\.com/watch\?.*v=([a-zA-Z0-9_-]{11})#i',  // youtube.com/watch?v=ID
			'#^https?://(?:www\.)?youtu\.be/([a-zA-Z0-9_-]{11})#i',                 // youtu.be/ID
			'#^https?://(?:www\.)?youtube\.com/embed/([a-zA-Z0-9_-]{11})#i',        // youtube.com/embed/ID
			'#^https?://(?:www\.)?youtube\.com/v/([a-zA-Z0-9_-]{11})#i',            // youtube.com/v/ID
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $url, $matches ) ) {
				return $matches[1];
			}
		}

		return false;
	}

	/**
	 * Generate YouTube embed HTML.
	 *
	 * Creates a responsive YouTube iframe embed with standard YouTube player options.
	 *
	 * @since 1.0.0
	 * @param string $video_id YouTube video ID.
	 * @param string $original_url Original YouTube URL.
	 * @return string HTML for YouTube embed.
	 */
	private function generate_youtube_embed( $video_id, $original_url ) {
		ob_start();
		?>
		<div class="bb-youtube-embed-container">
			<div class="bb-youtube-embed-wrapper">
				<iframe
					src="https://www.youtube.com/embed/<?php echo esc_attr( $video_id ); ?>?feature=oembed"
					frameborder="0"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
					referrerpolicy="strict-origin-when-cross-origin"
					allowfullscreen
					title="YouTube video player"
					class="bb-youtube-embed-iframe"
				></iframe>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function rest_api_init() {
		add_filter(
			'bp_get_the_thread_message_content',
			function ( $message, $message_id ) {
				$is_rest_request    = bb_is_rest();
				$shared_activity_id = bp_messages_get_meta( $message_id, 'shared_activity_id', true );

				// For REST API requests, return the activity URL instead of HTML preview.
				if ( $is_rest_request && $shared_activity_id && bp_is_active( 'activity' ) ) {
					// Get the activity URL.
					$activity_url = bp_activity_get_permalink( $shared_activity_id );

					if ( ! empty( $message ) ) {
						$message = $message . '<br/>';
					}

                    // Return format: "<p>Shared a Post <a href="URL">URL</a></p>"
                    return $message . '<p>' . esc_html__( 'Shared a Post', 'buddyboss-sharing' ) . ' <a href="' . esc_url( $activity_url ) . '">' . esc_url( $activity_url ) . '</a></p>';

				}

				return $message;
			},
			9,
			2
		);
	}
}
