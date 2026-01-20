<?php
/**
 * Link Preview REST API.
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\API;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Link_Preview class.
 *
 * @since 1.0.0
 */
class Link_Preview {

	/**
	 * The single instance of the class.
	 *
	 * @var Link_Preview
	 */
	protected static $instance = null;

	/**
	 * Main Link_Preview Instance.
	 *
	 * @since 1.0.0
	 * @return Link_Preview
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
		// add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		// Activity link preview.
		register_rest_route(
			'buddyboss/v1',
			'/activity/link-preview',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_activity_link_preview' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(
					'url' => array(
						'required'          => true,
						'validate_callback' => function( $param ) {
							return filter_var( $param, FILTER_VALIDATE_URL );
						},
					),
				),
			)
		);

		// Forum link preview.
		register_rest_route(
			'buddyboss/v1',
			'/forums/link-preview',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_forum_link_preview' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(
					'url' => array(
						'required'          => true,
						'validate_callback' => function( $param ) {
							return filter_var( $param, FILTER_VALIDATE_URL );
						},
					),
				),
			)
		);

		// Messages link preview.
		register_rest_route(
			'buddyboss/v1',
			'/messages/link-preview',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_message_link_preview' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(
					'url' => array(
						'required'          => true,
						'validate_callback' => function( $param ) {
							return filter_var( $param, FILTER_VALIDATE_URL );
						},
					),
				),
			)
		);
	}

	/**
	 * Check permissions.
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function get_items_permissions_check( $request ) {
		return is_user_logged_in();
	}

	/**
	 * Get activity link preview.
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_activity_link_preview( $request ) {
		$url = $request->get_param( 'url' );

		$response = $this->parse_url( $url );

		return rest_ensure_response( $response );
	}

	/**
	 * Get forum link preview.
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_forum_link_preview( $request ) {
		$url = $request->get_param( 'url' );

		$response = $this->parse_url( $url );

		return rest_ensure_response( $response );
	}

	/**
	 * Get message link preview.
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_message_link_preview( $request ) {
		$url = $request->get_param( 'url' );

		$response = $this->parse_url( $url );

		return rest_ensure_response( $response );
	}

	/**
	 * Parse URL and generate preview.
	 *
	 * @since 1.0.0
	 * @param string $url URL to parse.
	 * @return array
	 */
	private function parse_url( $url ) {
		// Check if it's an internal activity URL.
		// Try member activity URL format: /members/{username}/activity/{id}/
		if ( preg_match( '#/members/[^/]+/activity/(\d+)/?#', $url, $matches ) ) {
			$activity_id = intval( $matches[1] );
			if ( $activity_id > 0 ) {
				return $this->get_internal_activity_preview( $activity_id );
			}
		}

		// Try legacy activity URL format: /activity-feed/p/{id}/
		if ( preg_match( '#/activity-feed/p/(\d+)/?#', $url, $matches ) ) {
			$activity_id = intval( $matches[1] );
			if ( $activity_id > 0 ) {
				return $this->get_internal_activity_preview( $activity_id );
			}
		}

		// Try bp_core_parse_activity_url if available (for other formats)
		if ( function_exists( 'bp_core_parse_activity_url' ) ) {
			$parsed = bp_core_parse_activity_url( $url );

			if ( ! empty( $parsed['activity_id'] ) ) {
				return $this->get_internal_activity_preview( $parsed['activity_id'] );
			}
		}

		// If not internal or couldn't parse, return external preview.
		return $this->get_external_preview( $url );
	}

	/**
	 * Get internal activity preview.
	 *
	 * @since 1.0.0
	 * @param int $activity_id Activity ID.
	 * @return array
	 */
	private function get_internal_activity_preview( $activity_id ) {
		if ( ! function_exists( 'bp_activity_get_specific' ) ) {
			return $this->get_error_response();
		}

		$activity = bp_activity_get_specific(
			array(
				'activity_ids' => $activity_id,
				'show_hidden'  => true,
			)
		);

		if ( empty( $activity['activities'][0] ) ) {
			return $this->get_error_response();
		}

		$activity_obj = $activity['activities'][0];

		// Check if user can read this activity.
		if ( function_exists( 'bp_activity_user_can_read' ) && ! bp_activity_user_can_read( $activity_obj ) ) {
			return $this->get_error_response( esc_html__( 'You do not have permission to view this activity.', 'buddyboss-sharing' ) );
		}

		// Generate preview HTML.
		$preview_html = $this->generate_activity_preview_html( $activity_obj );

		return array(
			'activity_preview' => $preview_html,
			'activity_id'      => $activity_id,
			'is_internal'      => true,
		);
	}

	/**
	 * Generate activity preview HTML.
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @return string
	 */
	private function generate_activity_preview_html( $activity ) {
		$user = get_userdata( $activity->user_id );

		if ( ! $user ) {
			return '';
		}

		ob_start();
		?>
		<div class="shared-activity-preview" data-activity-id="<?php echo esc_attr( $activity->id ); ?>">
			<div class="shared-activity-header">
				<div class="shared-activity-avatar">
					<?php echo get_avatar( $activity->user_id, 40 ); ?>
				</div>
				<div class="shared-activity-meta">
					<div class="shared-activity-author">
						<?php echo esc_html( $user->display_name ); ?>
						<?php if ( ! empty( $activity->item_id ) && 'groups' === $activity->component ) : ?>
							<?php
							$group = groups_get_group( $activity->item_id );
							if ( $group ) :
								?>
								<span class="shared-activity-group">
									<?php
									printf(
										/* translators: %s: group name */
										esc_html__( 'in %s', 'buddyboss-sharing' ),
										esc_html( $group->name )
									);
									?>
								</span>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<div class="shared-activity-time">
						<?php
						printf(
							/* translators: %s: time ago */
							esc_html__( '%s ago', 'buddyboss-sharing' ),
							human_time_diff( strtotime( $activity->date_recorded ), current_time( 'timestamp' ) )
						);
						?>
					</div>
				</div>
			</div>
			<div class="shared-activity-content">
				<?php echo wp_kses_post( $activity->content ); ?>
			</div>
			<?php
			// Display media if available.
			$media_ids = bp_activity_get_meta( $activity->id, 'bp_media_ids', true );
			if ( ! empty( $media_ids ) ) :
				$media_ids = maybe_unserialize( $media_ids );
				if ( is_array( $media_ids ) && ! empty( $media_ids ) ) :
					?>
					<div class="shared-activity-media">
						<?php
						foreach ( array_slice( $media_ids, 0, 4 ) as $media_id ) :
							$image_url = wp_get_attachment_image_url( $media_id, 'medium' );
							if ( $image_url ) :
								?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="" />
								<?php
							endif;
						endforeach;
						?>
					</div>
					<?php
				endif;
			endif;
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get external preview.
	 *
	 * @since 1.0.0
	 * @param string $url URL.
	 * @return array
	 */
	private function get_external_preview( $url ) {
		// For external URLs, fetch meta tags.
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 10,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $this->get_error_response( $response->get_error_message() );
		}

		$body = wp_remote_retrieve_body( $response );

		// Parse OpenGraph tags.
		$og_data = $this->parse_og_tags( $body );

		return array(
			'activity_preview' => '',
			'activity_id'      => 0,
			'is_internal'      => false,
			'og_data'          => $og_data,
		);
	}

	/**
	 * Parse OpenGraph tags from HTML.
	 *
	 * @since 1.0.0
	 * @param string $html HTML content.
	 * @return array
	 */
	private function parse_og_tags( $html ) {
		$og_data = array(
			'title'       => '',
			'description' => '',
			'image'       => '',
			'url'         => '',
		);

		// Simple regex parsing (in production, use DOMDocument for better parsing).
		preg_match_all( '/<meta[^>]+property=["\']og:([^"\']+)["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $html, $matches );

		if ( ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $index => $property ) {
				if ( isset( $og_data[ $property ] ) ) {
					$og_data[ $property ] = $matches[2][ $index ];
				}
			}
		}

		return $og_data;
	}

	/**
	 * Get error response.
	 *
	 * @since 1.0.0
	 * @param string $message Error message.
	 * @return array
	 */
	private function get_error_response( $message = '' ) {
		if ( empty( $message ) ) {
			$message = esc_html__( 'Unable to generate preview.', 'buddyboss-sharing' );
		}

		return array(
			'activity_preview' => '',
			'activity_id'      => 0,
			'is_internal'      => false,
			'error'            => $message,
		);
	}
}
