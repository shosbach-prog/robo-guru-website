<?php
/**
 * Activity Open Graph Tags
 *
 * Handles OG tags for single activity pages.
 * Logic differs based on whether the site is public or private network.
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
 * Activity_OpenGraph class.
 *
 * @since 1.0.0
 */
class Activity_OpenGraph {

	/**
	 * The single instance of the class.
	 *
	 * @var Activity_OpenGraph
	 */
	protected static $instance = null;

	/**
	 * Current activity data.
	 *
	 * @var array|null
	 */
	private $activity = null;

	/**
	 * Activity author data.
	 *
	 * @var \WP_User|null
	 */
	private $author = null;

	/**
	 * Activity ID.
	 *
	 * @var int
	 */
	private $activity_id = 0;

	/**
	 * Whether this is a private network.
	 *
	 * @var bool
	 */
	private $is_private_network = false;

	/**
	 * Main Activity_OpenGraph Instance.
	 *
	 * @since 1.0.0
	 * @return Activity_OpenGraph
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
		// Check license validity before initializing.
		if ( ! \BuddyBoss\Sharing\Core\License_Manager::instance()->can_render_og_tags() ) {
			return;
		}

		// Hook after BuddyPress parses the query
		add_action( 'bp_parse_query', array( $this, 'init_activity_data' ), 20 );

		// Hook into wp_head to output OG tags
		add_action( 'wp_head', array( $this, 'output_og_tags' ), 5 );
	}

	/**
	 * Initialize activity data on single activity pages.
	 *
	 * @since 1.0.0
	 */
	public function init_activity_data() {
		// Detect if this is a single activity page
		$is_single_activity = $this->detect_single_activity_page();

		if ( ! $is_single_activity ) {
			return;
		}

		// Check if this is a private network
		$this->is_private_network = function_exists( 'bp_enable_private_network' ) && ! bp_enable_private_network();

		// Get activity ID from bp_current_action (on single activity pages, this returns the activity ID)
		$this->activity_id = function_exists( 'bp_current_action' ) ? bp_current_action() : 0;

		if ( ! $this->activity_id || ! is_numeric( $this->activity_id ) ) {
			return;
		}

		// Check if activity has privacy restrictions (not public)
		// If activity is not public, treat it like private network (use admin defaults)
		if ( ! $this->is_activity_public() ) {
			$this->is_private_network = true;
		}

		// For public networks with public activities, load activity data
		if ( ! $this->is_private_network ) {
			$this->load_activity_data();
		}
	}

	/**
	 * Detect if current page is a single activity page.
	 * Uses multiple detection methods for reliability.
	 *
	 * @since 1.0.0
	 * @return bool True if single activity page, false otherwise.
	 */
	private function detect_single_activity_page() {
		// Method 1: Check standard BuddyPress function
		if ( function_exists( 'bp_is_single_activity' ) && bp_is_single_activity() ) {
			return true;
		}

		// Method 2: Check if we're on a member profile with activity component and numeric action
		if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
			if ( function_exists( 'bp_is_current_component' ) && bp_is_current_component( 'activity' ) ) {
				$current_action = function_exists( 'bp_current_action' ) ? bp_current_action() : '';
				if ( is_numeric( $current_action ) && $current_action > 0 ) {
					return true;
				}
			}
		}

		// Method 3: Check URL pattern directly (for crawlers that might bypass BP query)
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
		if ( ! empty( $request_uri ) ) {
			// Pattern: /members/{username}/activity/{numeric-id}/
			if ( preg_match( '#/members/[^/]+/activity/(\d+)/?#', $request_uri, $matches ) ) {
				return true;
			}
			// Pattern: /activity/p/{numeric-id}/
			if ( preg_match( '#/activity/p/(\d+)/?#', $request_uri ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Load activity data from BuddyPress.
	 *
	 * @since 1.0.0
	 */
	private function load_activity_data() {
		if ( ! function_exists( 'bp_activity_get_specific' ) ) {
			return;
		}

		$activities = bp_activity_get_specific(
			array(
				'activity_ids'     => array( $this->activity_id ),
				'display_comments' => true,
			)
		);

		if ( empty( $activities['activities'] ) ) {
			return;
		}

		// Get the first (and only) activity
		$activity = current( $activities['activities'] );

		// Convert to array and store
		$this->activity = (array) $activity;

		// Apply content filters for proper rendering (pass activity object as second argument)
		$this->activity['content_rendered'] = ! empty( $this->activity['content'] )
			? apply_filters_ref_array( 'bp_get_activity_content', array( $this->activity['content'], &$activity ) )
			: '';

		// Load author data
		if ( ! empty( $this->activity['user_id'] ) ) {
			$this->author = get_user_by( 'id', $this->activity['user_id'] );
		}
	}

	/**
	 * Output Open Graph tags.
	 *
	 * @since 1.0.0
	 */
	public function output_og_tags() {
		// Detect if this is a single activity page
		$is_single_activity = $this->detect_single_activity_page();

		if ( ! $is_single_activity ) {
			return;
		}

		if ( ! $this->activity_id ) {
			return;
		}

		// Get OG data based on network type
		if ( $this->is_private_network ) {
			$og_data = $this->get_private_network_og_data();
		} else {
			$og_data = $this->get_public_network_og_data();
		}

		// Output OG tags
		$this->render_og_tags( $og_data );
	}

	/**
	 * Get OG data for private network.
	 * Uses BuddyBoss SEO plugin settings.
	 *
	 * @since 1.0.0
	 * @return array OG data
	 */
	private function get_private_network_og_data() {
		$og_title       = bp_get_option( 'buddyboss_og_title', get_bloginfo( 'name' ) );
		$og_description = bp_get_option( 'buddyboss_og_description', get_bloginfo( 'description' ) );
		$og_image       = bp_get_option( 'buddyboss_og_image', '' );

		// Ensure OG image URL is absolute if set.
		if ( ! empty( $og_image ) ) {
			$og_image = $this->ensure_absolute_url( $og_image );
		} else {
			// If OG image is not set, use site icon instead of default avatar.
			// This avoids showing the generic BuddyBoss default avatar.
			$og_image = get_site_icon_url( 512 );

			// If no site icon, then try custom avatar (not default).
			if ( empty( $og_image ) ) {
				$avatar_url = $this->get_activity_author_avatar();
				// Only use avatar if it's not a default BuddyBoss avatar.
				if ( ! $this->is_default_buddyboss_avatar( $avatar_url ) ) {
					$og_image = $avatar_url;
				}
			}
		}

		// Build member activity URL format: /members/{username}/activity/{id}/
		$og_url = '';
		if ( $this->activity_id ) {
			// Get minimal activity data just for user_id
			$activity = new \BP_Activity_Activity( $this->activity_id );

			if ( ! empty( $activity->user_id ) && function_exists( 'bp_core_get_user_domain' ) ) {
				$user_domain = bp_core_get_user_domain( $activity->user_id );
				if ( $user_domain ) {
					$og_url = trailingslashit( $user_domain ) . 'activity/' . $this->activity_id . '/';
				}
			}

			// Fallback to default permalink
			if ( empty( $og_url ) && function_exists( 'bp_activity_get_permalink' ) ) {
				$og_url = bp_activity_get_permalink( $this->activity_id );
			}
		}

		// Final fallback
		if ( empty( $og_url ) ) {
			$og_url = home_url();
		}

		return array(
			'title'       => $og_title,
			'description' => $og_description,
			'image'       => $og_image,
			'url'         => $og_url,
			'type'        => 'website',
		);
	}

	/**
	 * Get OG data for public network.
	 * Uses activity data with customizable templates.
	 *
	 * @since 1.0.0
	 * @return array OG data
	 */
	private function get_public_network_og_data() {
		if ( empty( $this->activity ) ) {
			return $this->get_fallback_og_data();
		}

		// Check for custom templates
		$title_template = bp_get_option( 'buddyboss_activity_og_title_template', '' );
		$desc_template  = bp_get_option( 'buddyboss_activity_og_description_template', '' );

		// Build title - use template if available, otherwise use hardcoded logic
		if ( ! empty( $title_template ) ) {
			$title = $this->replace_template_tags( $title_template, 'title' );
		} else {
			$title = $this->build_activity_title();
		}

		// Build description - use template if available, otherwise use hardcoded logic
		if ( ! empty( $desc_template ) ) {
			$description = $this->replace_template_tags( $desc_template, 'description' );
		} else {
			$description = $this->build_activity_description();
		}

		// Fallback to defaults if templates result in empty strings
		if ( empty( $title ) ) {
			$title = $this->build_activity_title();
		}

		if ( empty( $description ) ) {
			$description = $this->build_activity_description();
		}

		// Get activity URL - use member activity format: /members/{username}/activity/{id}/
		$url = '';
		if ( ! empty( $this->activity['user_id'] ) && function_exists( 'bp_core_get_user_domain' ) ) {
			$user_domain = bp_core_get_user_domain( $this->activity['user_id'] );
			if ( $user_domain ) {
				$url = trailingslashit( $user_domain ) . 'activity/' . $this->activity_id . '/';
			}
		}

		// Fallback to default permalink
		if ( empty( $url ) && function_exists( 'bp_activity_get_permalink' ) ) {
			$url = bp_activity_get_permalink( $this->activity_id, $this->activity );
		}

		// Final fallback
		if ( empty( $url ) ) {
			$url = home_url();
		}

		// Get activity image
		$image = $this->get_activity_image();

		return array(
			'title'       => $title,
			'description' => $description,
			'image'       => $image,
			'url'         => $url,
			'type'        => 'article',
			'author'      => $this->author ? $this->author->display_name : '',
			'published'   => ! empty( $this->activity['date_recorded'] ) ? $this->activity['date_recorded'] : '',
		);
	}

	/**
	 * Build activity title.
	 *
	 * @since 1.0.0
	 * @return string Title
	 */
	private function build_activity_title() {
		// Get activity action (e.g., "John posted an update")
		$action = ! empty( $this->activity['action'] ) ? wp_strip_all_tags( $this->activity['action'] ) : '';

		// If no action, use author name
		if ( empty( $action ) && $this->author ) {
			$action = sprintf(
				/* translators: %s: user's display name */
				__( 'Activity by %s', 'buddyboss-sharing' ),
				$this->author->display_name
			);
		}

		// Append site title
		$site_title = get_bloginfo( 'name' );
		$title      = $action . ' | ' . $site_title;

		return $title;
	}

	/**
	 * Build activity description.
	 *
	 * @since 1.0.0
	 * @return string Description
	 */
	private function build_activity_description() {
		// Use rendered content
		$content = ! empty( $this->activity['content_rendered'] )
			? $this->activity['content_rendered']
			: ( ! empty( $this->activity['content'] ) ? $this->activity['content'] : '' );

		// Strip all tags and trim
		$description = wp_strip_all_tags( $content );

		// Limit to 160 characters for OG description
		if ( strlen( $description ) > 160 ) {
			$description = substr( $description, 0, 157 ) . '...';
		}

		// Fallback to site description
		if ( empty( $description ) ) {
			$description = get_bloginfo( 'description' );
		}

		return $description;
	}

	/**
	 * Get activity image.
	 * Priority: Media Images > Video Thumbnails > Document Images/PDF Previews > Content Images > Author Avatar > Site Icon
	 *
	 * @since 1.0.0
	 * @return string Image URL
	 */
	private function get_activity_image() {
		$image = '';

		// 1. First, try to get attached media (images)
		if ( class_exists( 'BP_Media' ) && ! empty( $this->activity['id'] ) ) {
			// Get activity metas.
			$activity_metas = bb_activity_get_metadata( $this->activity['id'] );
			$media_ids      = '';
			if ( ! empty( $activity_metas['bp_media_ids'][0] ) ) {
				$media_ids = $activity_metas['bp_media_ids'][0];
			} elseif ( ! empty( $activity_metas['bp_media_id'][0] ) ) {
				$media_ids = $activity_metas['bp_media_id'][0];
			}

			if ( ! empty( $media_ids ) ) {
				$media_items = \BP_Media::get(
					array(
						'in'       => $media_ids,
						'per_page' => 1,
						'sort'     => 'ASC', // Get the first attached image
					)
				);

				if ( ! empty( $media_items['medias'] ) ) {
					$first_media = current( $media_items['medias'] );
					// Only use if it's an image (not a video)
					if ( ! empty( $first_media->attachment_data ) && 'photo' === $first_media->type ) {
						$image = ! empty( $first_media->attachment_data->full )
							? $first_media->attachment_data->full
							: $first_media->attachment_data->thumb;

						if ( ! empty( $image ) ) {
							return $image;
						}
					}
				}
			}
		}

		// 2. Try to get attached videos and use thumbnail
		if ( empty( $image ) && class_exists( 'BP_Video' ) && ! empty( $this->activity['id'] ) ) {
			if ( ! empty( $activity_metas['bp_video_ids'][0] ) ) {
				$video_ids = $activity_metas['bp_video_ids'][0];
			} elseif ( ! empty( $activity_metas['bp_video_id'][0] ) ) {
				$video_ids = $activity_metas['bp_video_id'][0];
			}

			if ( ! empty( $video_ids ) ) {
				$video_items = \BP_Video::get(
					array(
						'in'       => $video_ids,
						'per_page' => 1,
						'sort'     => 'ASC', // Get the first attached video
					)
				);

				if ( ! empty( $video_items['videos'] ) ) {
					$first_video = current( $video_items['videos'] );
					// Use video thumbnail
					if ( ! empty( $first_video->attachment_data ) ) {
						$image = ! empty( $first_video->attachment_data->thumb )
							? $first_video->attachment_data->thumb
							: ( ! empty( $first_video->attachment_data->video_activity_thumb )
								? $first_video->attachment_data->video_activity_thumb
								: '' );

						if ( ! empty( $image ) ) {
							return $image;
						}
					}
				}
			}
		}

		// 3. Try to get attached documents (images or PDF previews)
		if ( empty( $image ) && class_exists( 'BP_Document' ) && ! empty( $this->activity['id'] ) ) {
			if ( ! empty( $activity_metas['bp_document_ids'][0] ) ) {
				$document_ids = $activity_metas['bp_document_ids'][0];
			} elseif ( ! empty( $activity_metas['bp_document_id'][0] ) ) {
				$document_ids = $activity_metas['bp_document_id'][0];
			}

			if ( ! empty( $document_ids ) ) {
				$document_items = \BP_Document::get(
					array(
						'in'       => $document_ids,
						'per_page' => 1,
						'sort'     => 'ASC', // Get the first attached document
					)
				);

				if ( ! empty( $document_items['documents'] ) ) {
					$first_document = current( $document_items['documents'] );

					// Use document attachment_data (similar to media and videos)
					if ( ! empty( $first_document->attachment_data ) ) {
						$mime_type = get_post_mime_type( $first_document->attachment_id );

						// For PDFs, use the PDF preview thumbnail
						if ( 'application/pdf' === $mime_type ) {
							$image = ! empty( $first_document->attachment_data->activity_thumb_pdf )
								? $first_document->attachment_data->activity_thumb_pdf
								: ( ! empty( $first_document->attachment_data->thumb )
									? $first_document->attachment_data->thumb
									: '' );
						} else {
							// For images or other documents, use the standard thumbnail
							$image = ! empty( $first_document->attachment_data->thumb )
								? $first_document->attachment_data->thumb
								: ( ! empty( $first_document->attachment_data->full )
									? $first_document->attachment_data->full
									: '' );
						}

						if ( ! empty( $image ) ) {
							return $image;
						}
					}
				}
			}
		}

		// 4. Try to find an image in the activity content
		if ( empty( $image ) && ! empty( $this->activity['content'] ) ) {
			preg_match( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $this->activity['content'], $matches );
			if ( ! empty( $matches[1] ) ) {
				$image = $matches[1];
			}
		}

		// 5. Use admin OG image setting if configured (applies to both public and private networks)
		if ( empty( $image ) ) {
			$admin_og_image = bp_get_option( 'buddyboss_og_image', '' );
			if ( ! empty( $admin_og_image ) ) {
				$image = $this->ensure_absolute_url( $admin_og_image );
			}
		}

		// 6. If no image found, use author avatar
		if ( empty( $image ) && $this->author ) {
			$image = get_avatar_url( $this->author->ID, array( 'size' => 512 ) );
		}

		// 7. Fallback to site icon or logo
		if ( empty( $image ) ) {
			$image = get_site_icon_url( 512 );
		}

		// Sanitize the image URL (convert file paths to URLs if needed)
		$image = $this->sanitize_image_url( $image );

		return $image;
	}

	/**
	 * Sanitize image URL to ensure it's a proper URL, not a file path.
	 *
	 * @since 1.0.0
	 * @param string $image Image URL or path.
	 * @return string Sanitized image URL.
	 */
	private function sanitize_image_url( $image ) {
		if ( empty( $image ) ) {
			return '';
		}

		// If it's already a valid URL, return it
		if ( filter_var( $image, FILTER_VALIDATE_URL ) ) {
			return $image;
		}

		// If it starts with an absolute path, convert to URL
		if ( strpos( $image, '/' ) === 0 ) {
			$upload_dir = wp_upload_dir();

			// Check if it's within the uploads directory
			if ( strpos( $image, $upload_dir['basedir'] ) === 0 ) {
				// Replace the base directory path with the base URL
				$image = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $image );
			}
		}

		return $image;
	}

	/**
	 * Check if URL is a valid image.
	 *
	 * @since 1.0.0
	 * @param string $url URL to check.
	 * @return bool True if URL is an image, false otherwise.
	 */
	private function is_valid_image_url( $url ) {
		if ( empty( $url ) ) {
			return false;
		}

		// Check if URL has image extension
		$image_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg' );
		$url_path         = wp_parse_url( $url, PHP_URL_PATH );
		$extension        = strtolower( pathinfo( $url_path, PATHINFO_EXTENSION ) );

		if ( in_array( $extension, $image_extensions, true ) ) {
			return true;
		}

		// Check if it's a gravatar URL (these are always images)
		if ( strpos( $url, 'gravatar.com' ) !== false || strpos( $url, 'avatar' ) !== false ) {
			return true;
		}

		return false;
	}

	/**
	 * Ensure URL is absolute.
	 *
	 * @since 1.0.0
	 * @param string $url URL to check.
	 * @return string Absolute URL.
	 */
	private function ensure_absolute_url( $url ) {
		if ( empty( $url ) ) {
			return '';
		}

		// If already absolute, return as is.
		if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return $url;
		}

		// If relative URL, make it absolute.
		if ( strpos( $url, '/' ) === 0 ) {
			return home_url( $url );
		}

		// If it's a file path, convert to URL.
		$upload_dir = wp_upload_dir();
		if ( ! empty( $upload_dir['basedir'] ) && strpos( $url, $upload_dir['basedir'] ) === 0 ) {
			return str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $url );
		}

		// Fallback: prepend home URL.
		return home_url( $url );
	}

	/**
	 * Get activity author's avatar for fallback OG image.
	 *
	 * @since 1.0.0
	 * @return string Avatar URL or empty string.
	 */
	private function get_activity_author_avatar() {
		if ( ! $this->activity_id ) {
			return '';
		}

		// Get minimal activity data just for user_id.
		$activity = new \BP_Activity_Activity( $this->activity_id );

		// Check if activity exists and has a valid user_id.
		if ( empty( $activity->id ) || empty( $activity->user_id ) ) {
			return '';
		}

		// Get author avatar.
		$avatar_url = get_avatar_url( $activity->user_id, array( 'size' => 512 ) );

		return $avatar_url ? $avatar_url : '';
	}

	/**
	 * Check if avatar URL is a default BuddyBoss avatar.
	 *
	 * @since 1.0.0
	 * @param string $avatar_url Avatar URL to check.
	 * @return bool True if default BuddyBoss avatar, false otherwise.
	 */
	private function is_default_buddyboss_avatar( $avatar_url ) {
		if ( empty( $avatar_url ) ) {
			return false;
		}

		// Check if URL contains default BuddyBoss avatar paths.
		$default_avatar_patterns = array(
			'bb-profile-avatar-buddyboss.jpg',
			'bb-profile-avatar-buddyboss.png',
			'mystery-man.png',
			'/bp-core/images/',
		);

		foreach ( $default_avatar_patterns as $pattern ) {
			if ( strpos( $avatar_url, $pattern ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if activity is public (no privacy restrictions).
	 * For social media crawlers, we only show actual activity data if it's truly public.
	 *
	 * @since 1.0.0
	 * @return bool True if activity is public, false otherwise.
	 */
	private function is_activity_public() {
		if ( ! $this->activity_id ) {
			return false;
		}

		// Load activity to check privacy
		if ( ! class_exists( 'BP_Activity_Activity' ) ) {
			return false;
		}

		$activity = new \BP_Activity_Activity( $this->activity_id );

		if ( empty( $activity->id ) ) {
			return false;
		}

		// Check activity privacy field
		// BuddyBoss uses 'privacy' field to determine activity visibility
		$privacy = isset( $activity->privacy ) ? $activity->privacy : 'public';

		// Only allow 'public' privacy to show actual activity data
		// Any other privacy level (friends, loggedin, onlyme, etc.) should use admin defaults
		if ( 'public' !== $privacy ) {
			return false;
		}

		// Check if activity is in a private/hidden group
		if ( ! empty( $activity->component ) && 'groups' === $activity->component ) {
			if ( ! empty( $activity->item_id ) && function_exists( 'groups_get_group' ) ) {
				$group = groups_get_group( $activity->item_id );

				// If group is private or hidden, don't expose activity data
				if ( ! empty( $group->status ) && in_array( $group->status, array( 'private', 'hidden' ), true ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Get image dimensions for OG meta tags.
	 *
	 * @since 1.0.0
	 * @param string $image_url Image URL.
	 * @return array Array with 'width' and 'height' keys.
	 */
	private function get_image_dimensions( $image_url ) {
		$dimensions = array(
			'width'  => '',
			'height' => '',
		);

		if ( empty( $image_url ) ) {
			return $dimensions;
		}

		// Try to get dimensions from attachment ID if URL contains attachment ID.
		$attachment_id = attachment_url_to_postid( $image_url );
		if ( $attachment_id ) {
			$image_meta = wp_get_attachment_image_src( $attachment_id, 'full' );
			if ( ! empty( $image_meta ) && isset( $image_meta[1], $image_meta[2] ) ) {
				$dimensions['width']  = $image_meta[1];
				$dimensions['height'] = $image_meta[2];
				return $dimensions;
			}
		}

		// Try to get dimensions from file if it's a local file.
		$upload_dir = wp_upload_dir();
		if ( ! empty( $upload_dir['baseurl'] ) && strpos( $image_url, $upload_dir['baseurl'] ) === 0 ) {
			$file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $image_url );
			if ( file_exists( $file_path ) && function_exists( 'getimagesize' ) ) {
				$image_size = @getimagesize( $file_path );
				if ( ! empty( $image_size ) && isset( $image_size[0], $image_size[1] ) ) {
					$dimensions['width']  = $image_size[0];
					$dimensions['height'] = $image_size[1];
					return $dimensions;
				}
			}
		}

		return $dimensions;
	}

	/**
	 * Get fallback OG data when activity is not found.
	 *
	 * @since 1.0.0
	 * @return array OG data
	 */
	private function get_fallback_og_data() {
		return array(
			'title'       => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'image'       => get_site_icon_url( 512 ),
			'url'         => home_url(),
			'type'        => 'website',
		);
	}

	/**
	 * Replace template tags with activity data.
	 *
	 * Available Tags:
	 * - {activity_title}      : Activity post title (falls back to activity_action if empty)
	 * - {activity_action}     : Activity action text (e.g., "John posted an update")
	 * - {activity_content}    : Activity content (limited based on context)
	 * - {author_name}         : Activity author's display name
	 * - {author_first_name}   : Activity author's first name
	 * - {author_last_name}    : Activity author's last name
	 * - {site_title}          : Site name
	 * - {separator}           : Pipe separator (|)
	 *
	 * @since 1.0.0
	 * @param string $template Template string with tags.
	 * @param string $context  Context: 'title' or 'description'.
	 * @return string Template with tags replaced.
	 */
	private function replace_template_tags( $template, $context = 'title' ) {
		if ( empty( $template ) ) {
			return '';
		}

		// Get activity title using BuddyBoss function
		$activity_title = ! empty( $this->activity['post_title'] )
			? wp_strip_all_tags( $this->activity['post_title'] )
			: '';
		// Note: bb_activity_get_post_title requires BP_Activity_Activity object
		// For now, we'll leave this empty and rely on activity_action fallback

		// Get activity action text
		$activity_action = ! empty( $this->activity['action'] )
			? wp_strip_all_tags( $this->activity['action'] )
			: '';

		// If no action, use author name as fallback
		if ( empty( $activity_action ) && $this->author ) {
			$activity_action = sprintf(
				/* translators: %s: user's display name */
				__( 'Activity by %s', 'buddyboss-sharing' ),
				$this->author->display_name
			);
		}

		// If activity_title is empty, use activity_action as fallback
		if ( empty( $activity_title ) ) {
			$activity_title = $activity_action;
		}

		// Get activity content
		$activity_content = ! empty( $this->activity['content_rendered'] )
			? $this->activity['content_rendered']
			: ( ! empty( $this->activity['content'] ) ? $this->activity['content'] : '' );

		// Strip HTML tags from content
		$activity_content = wp_strip_all_tags( $activity_content );

		// Limit content length based on context
		if ( 'title' === $context && strlen( $activity_content ) > 60 ) {
			$activity_content = substr( $activity_content, 0, 57 ) . '...';
		} elseif ( 'description' === $context && strlen( $activity_content ) > 300 ) {
			$activity_content = substr( $activity_content, 0, 297 ) . '...';
		}

		// Get author data
		$author_name       = $this->author ? $this->author->display_name : '';
		$author_first_name = $this->author ? $this->author->first_name : '';
		$author_last_name  = $this->author ? $this->author->last_name : '';

		// Get site title
		$site_title = get_bloginfo( 'name' );

		// Build replacement array
		$replacements = array(
			'{activity_title}'     => $activity_title,
			'{activity_action}'    => $activity_action,
			'{activity_content}'   => $activity_content,
			'{author_name}'        => $author_name,
			'{author_first_name}'  => $author_first_name,
			'{author_last_name}'   => $author_last_name,
			'{site_title}'         => $site_title,
			'{separator}'          => '|',
		);

		// Replace all tags
		$output = str_replace( array_keys( $replacements ), array_values( $replacements ), $template );

		// Clean up any double spaces or empty separators
		$output = preg_replace( '/\s+/', ' ', $output );
		$output = preg_replace( '/\|\s*\|/', '|', $output );
		$output = preg_replace( '/^\s*\|\s*|\s*\|\s*$/', '', $output );
		$output = trim( $output );

		return $output;
	}

	/**
	 * Render OG tags in HTML.
	 *
	 * @since 1.0.0
	 * @param array $og_data OG data.
	 */
	private function render_og_tags( $og_data ) {
		if ( empty( $og_data ) ) {
			return;
		}

		echo "\n<!-- BuddyBoss SEO - Activity Open Graph Tags -->\n";

		// OG Title
		if ( ! empty( $og_data['title'] ) ) {
			printf(
				'<meta property="og:title" content="%s" />' . "\n",
				esc_attr( $og_data['title'] )
			);
		}

		// OG Description
		if ( ! empty( $og_data['description'] ) ) {
			printf(
				'<meta property="og:description" content="%s" />' . "\n",
				esc_attr( $og_data['description'] )
			);
		}

		// OG Image
		if ( ! empty( $og_data['image'] ) ) {
			printf(
				'<meta property="og:image" content="%s" />' . "\n",
				esc_url( $og_data['image'] )
			);

			// Add image dimensions for better Facebook validation (if available).
			$image_dimensions = $this->get_image_dimensions( $og_data['image'] );
			if ( ! empty( $image_dimensions['width'] ) ) {
				printf(
					'<meta property="og:image:width" content="%s" />' . "\n",
					esc_attr( $image_dimensions['width'] )
				);
			}
			if ( ! empty( $image_dimensions['height'] ) ) {
				printf(
					'<meta property="og:image:height" content="%s" />' . "\n",
					esc_attr( $image_dimensions['height'] )
				);
			}
		}

		// OG URL
		if ( ! empty( $og_data['url'] ) ) {
			printf(
				'<meta property="og:url" content="%s" />' . "\n",
				esc_url( $og_data['url'] )
			);
		}

		// OG Type
		if ( ! empty( $og_data['type'] ) ) {
			printf(
				'<meta property="og:type" content="%s" />' . "\n",
				esc_attr( $og_data['type'] )
			);
		}

		// OG Site Name
		printf(
			'<meta property="og:site_name" content="%s" />' . "\n",
			esc_attr( get_bloginfo( 'name' ) )
		);

		// Additional article metadata for public networks
		if ( ! $this->is_private_network && 'article' === $og_data['type'] ) {
			// Article author
			if ( ! empty( $og_data['author'] ) ) {
				printf(
					'<meta property="article:author" content="%s" />' . "\n",
					esc_attr( $og_data['author'] )
				);
			}

			// Article published time
			if ( ! empty( $og_data['published'] ) ) {
				printf(
					'<meta property="article:published_time" content="%s" />' . "\n",
					esc_attr( gmdate( 'c', strtotime( $og_data['published'] ) ) )
				);
			}
		}

		// Twitter Card meta tags for better Twitter/X sharing
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";

		if ( ! empty( $og_data['title'] ) ) {
			printf(
				'<meta name="twitter:title" content="%s" />' . "\n",
				esc_attr( $og_data['title'] )
			);
		}

		if ( ! empty( $og_data['description'] ) ) {
			printf(
				'<meta name="twitter:description" content="%s" />' . "\n",
				esc_attr( $og_data['description'] )
			);
		}

		if ( ! empty( $og_data['image'] ) ) {
			printf(
				'<meta name="twitter:image" content="%s" />' . "\n",
				esc_url( $og_data['image'] )
			);
		}

		echo "<!-- / BuddyBoss SEO - Activity Open Graph Tags -->\n\n";
	}
}
