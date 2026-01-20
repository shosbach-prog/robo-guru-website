<?php
/**
 * BuddyBoss OpenGraph Support Class
 *
 * Provides OpenGraph meta tags for BuddyBoss components to enable rich previews when shared externally.
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * BuddyBoss OpenGraph Class
 *
 * Handles OpenGraph meta tag generation for various BuddyBoss components.
 *
 * @since 1.0.0
 */
class BB_OpenGraph {

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 * @var BB_OpenGraph
	 */
	private static $instance = null;

	/**
	 * Current component data.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $component_data = array();

	/**
	 * Get the singleton instance.
	 *
	 * @since 1.0.0
	 * @return BB_OpenGraph
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
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

		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'wp_head', array( $this, 'generate_opengraph_tags' ), 5 );
	}

	/**
	 * Generate OpenGraph tags based on current page.
	 *
	 * @since 1.0.0
	 */
	public function generate_opengraph_tags() {
		// Get component data.
		$this->component_data = $this->get_component_data();

		// If no component data, don't generate tags.
		if ( empty( $this->component_data ) ) {
			return;
		}

		// Generate and output tags.
		$og_tags = $this->build_opengraph_tags();
		$this->output_opengraph_tags( $og_tags );
	}

	/**
	 * Get component data based on current page.
	 *
	 * @since 1.0.0
	 * @return array|false Component data or false if not applicable.
	 */
	private function get_component_data() {
		// Activity component.
		if ( function_exists( 'bp_is_single_activity' ) && bp_is_single_activity() ) {
			return $this->get_activity_data();
		}

		// Groups component.
		if ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
			return $this->get_group_data();
		}

		// Members component.
		if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
			return $this->get_member_data();
		}

		// Forums component.
		if ( function_exists( 'bbp_is_single_topic' ) && bbp_is_single_topic() ) {
			return $this->get_forum_topic_data();
		}

		// Media component.
		if ( function_exists( 'bp_is_media_component' ) && bp_is_media_component() && function_exists( 'bp_is_single_item' ) && bp_is_single_item() ) {
			return $this->get_media_data();
		}

		// Document component.
		if ( function_exists( 'bp_is_document_component' ) && bp_is_document_component() && function_exists( 'bp_is_single_item' ) && bp_is_single_item() ) {
			return $this->get_document_data();
		}

		// Video component.
		if ( function_exists( 'bp_is_video_component' ) && bp_is_video_component() && function_exists( 'bp_is_single_item' ) && bp_is_single_item() ) {
			return $this->get_video_data();
		}

		return false;
	}

	/**
	 * Get activity data.
	 *
	 * @since 1.0.0
	 * @return array|false Activity data or false if not accessible.
	 */
	private function get_activity_data() {
		if ( ! function_exists( 'bp_current_action' ) || ! function_exists( 'bp_activity_get_specific' ) ) {
			return false;
		}

		$activity_id = bp_current_action();

		if ( empty( $activity_id ) || ! is_numeric( $activity_id ) ) {
			return false;
		}

		$activity = bp_activity_get_specific(
			array(
				'activity_ids' => $activity_id,
				'show_hidden'  => true,
			)
		);

		if ( empty( $activity['activities'][0] ) ) {
			return false;
		}

		$activity_obj = $activity['activities'][0];

		// Check if current user can view this activity.
		if ( function_exists( 'bp_activity_user_can_read' ) && ! bp_activity_user_can_read( $activity_obj ) ) {
			return false;
		}

		$user = get_userdata( $activity_obj->user_id );

		if ( ! $user ) {
			return false;
		}

		// Get activity title from meta or generate one.
		$title = bp_activity_get_meta( $activity_obj->id, 'activity_title', true );
		if ( empty( $title ) ) {
			$title = sprintf(
				/* translators: %s: user's display name */
				__( 'Activity by %s', 'buddyboss-sharing' ),
				$user->display_name
			);
		}

		return array(
			'type'        => 'activity',
			'id'          => $activity_obj->id,
			'title'       => $title,
			'description' => ! empty( $activity_obj->content ) ? wp_strip_all_tags( $activity_obj->content ) : '',
			'url'         => function_exists( 'bp_activity_get_permalink' ) ? bp_activity_get_permalink( $activity_obj->id, $activity_obj ) : '',
			'author'      => $user->display_name,
			'author_id'   => $user->ID,
			'date'        => $activity_obj->date_recorded,
			'images'      => $this->get_activity_images( $activity_obj->id ),
		);
	}

	/**
	 * Get group data.
	 *
	 * @since 1.0.0
	 * @return array|false Group data or false if not accessible.
	 */
	private function get_group_data() {
		if ( ! function_exists( 'groups_get_current_group' ) ) {
			return false;
		}

		$group = groups_get_current_group();

		if ( ! $group ) {
			return false;
		}

		// Check if current user can view this group.
		if ( function_exists( 'groups_is_user_member' ) && function_exists( 'bp_loggedin_user_id' ) &&
			! groups_is_user_member( bp_loggedin_user_id(), $group->id ) && 'private' === $group->status ) {
			return false;
		}

		$avatar = '';
		if ( function_exists( 'bp_core_fetch_avatar' ) ) {
			$avatar = bp_core_fetch_avatar(
				array(
					'item_id' => $group->id,
					'object'  => 'group',
					'type'    => 'full',
					'html'    => false,
				)
			);
		}

		return array(
			'type'        => 'group',
			'id'          => $group->id,
			'title'       => $group->name,
			'description' => $group->description,
			'url'         => function_exists( 'bp_get_group_permalink' ) ? bp_get_group_permalink( $group ) : '',
			'author'      => function_exists( 'bp_core_get_user_displayname' ) ? bp_core_get_user_displayname( $group->creator_id ) : '',
			'author_id'   => $group->creator_id,
			'date'        => $group->date_created,
			'images'      => $avatar ? array( $avatar ) : array(),
		);
	}

	/**
	 * Get member data.
	 *
	 * @since 1.0.0
	 * @return array|false Member data or false if not accessible.
	 */
	private function get_member_data() {
		if ( ! function_exists( 'bp_displayed_user_id' ) ) {
			return false;
		}

		$user_id = bp_displayed_user_id();

		if ( ! $user_id ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return false;
		}

		return array(
			'type'        => 'member',
			'id'          => $user_id,
			'title'       => sprintf(
				/* translators: %s: user's display name */
				__( '%s\'s Profile', 'buddyboss-sharing' ),
				$user->display_name
			),
			'description' => get_user_meta( $user_id, 'description', true ),
			'url'         => function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $user_id ) : '',
			'author'      => $user->display_name,
			'author_id'   => $user_id,
			'date'        => $user->user_registered,
			'images'      => array( get_avatar_url( $user_id, array( 'size' => 300 ) ) ),
		);
	}

	/**
	 * Get forum topic data.
	 *
	 * @since 1.0.0
	 * @return array|false Topic data or false if not accessible.
	 */
	private function get_forum_topic_data() {
		if ( ! function_exists( 'bbp_get_topic_id' ) ) {
			return false;
		}

		$topic_id = bbp_get_topic_id();

		if ( ! $topic_id ) {
			return false;
		}

		$topic = get_post( $topic_id );

		if ( ! $topic || 'publish' !== $topic->post_status ) {
			return false;
		}

		$author = get_userdata( $topic->post_author );

		if ( ! $author ) {
			return false;
		}

		return array(
			'type'        => 'forum_topic',
			'id'          => $topic_id,
			'title'       => $topic->post_title,
			'description' => wp_strip_all_tags( $topic->post_content ),
			'url'         => function_exists( 'bbp_get_topic_permalink' ) ? bbp_get_topic_permalink( $topic_id ) : '',
			'author'      => $author->display_name,
			'author_id'   => $topic->post_author,
			'date'        => $topic->post_date,
			'images'      => array( get_avatar_url( $topic->post_author, array( 'size' => 300 ) ) ),
		);
	}

	/**
	 * Get media data.
	 *
	 * @since 1.0.0
	 * @return array|false Media data or false if not accessible.
	 */
	private function get_media_data() {
		// Placeholder for media component implementation.
		return false;
	}

	/**
	 * Get document data.
	 *
	 * @since 1.0.0
	 * @return array|false Document data or false if not accessible.
	 */
	private function get_document_data() {
		// Placeholder for document component implementation.
		return false;
	}

	/**
	 * Get video data.
	 *
	 * @since 1.0.0
	 * @return array|false Video data or false if not accessible.
	 */
	private function get_video_data() {
		// Placeholder for video component implementation.
		return false;
	}

	/**
	 * Get activity images.
	 *
	 * @since 1.0.0
	 * @param int $activity_id Activity ID.
	 * @return array Array of image URLs.
	 */
	private function get_activity_images( $activity_id ) {
		$images = array();

		if ( ! function_exists( 'bp_activity_get_meta' ) ) {
			return $images;
		}

		// Check for media attachments.
		$media_ids = bp_activity_get_meta( $activity_id, 'bp_media_ids', true );

		if ( ! empty( $media_ids ) ) {
			$media_ids = maybe_unserialize( $media_ids );

			if ( is_array( $media_ids ) ) {
				foreach ( $media_ids as $media_id ) {
					$image_url = wp_get_attachment_image_url( $media_id, 'large' );

					if ( $image_url ) {
						$images[] = $image_url;
					}
				}
			}
		}

		// Check for document attachments.
		$document_ids = bp_activity_get_meta( $activity_id, 'bp_document_ids', true );

		if ( ! empty( $document_ids ) ) {
			$document_ids = maybe_unserialize( $document_ids );

			if ( is_array( $document_ids ) ) {
				foreach ( $document_ids as $doc_id ) {
					$doc_url = wp_get_attachment_url( $doc_id );

					if ( $doc_url ) {
						$images[] = $doc_url;
					}
				}
			}
		}

		return $images;
	}

	/**
	 * Build OpenGraph tags from component data.
	 *
	 * @since 1.0.0
	 * @return array OpenGraph tags.
	 */
	private function build_opengraph_tags() {
		$data    = $this->component_data;
		$og_tags = array();

		// Basic tags.
		$og_tags['og:type']      = 'article';
		$og_tags['og:url']       = $data['url'];
		$og_tags['og:site_name'] = get_bloginfo( 'name' );

		// Title.
		$og_tags['og:title'] = $data['title'];

		// Description.
		if ( ! empty( $data['description'] ) ) {
			$description                = wp_trim_words( $data['description'], 25, '...' );
			$og_tags['og:description'] = $description;
		}

		// Image.
		if ( ! empty( $data['images'] ) ) {
			$image_url                  = $data['images'][0];
			$og_tags['og:image']        = $image_url;
			$og_tags['og:image:width']  = '1200';
			$og_tags['og:image:height'] = '630';
			$og_tags['og:image:type']   = 'image/jpeg';
		}

		// Author.
		$og_tags['og:author'] = $data['author'];

		// Published time.
		$og_tags['article:published_time'] = gmdate( 'c', strtotime( $data['date'] ) );

		// Modified time.
		$og_tags['article:modified_time'] = gmdate( 'c', strtotime( $data['date'] ) );

		// Twitter Card tags.
		$og_tags['twitter:card']  = 'summary_large_image';
		$og_tags['twitter:title'] = $data['title'];

		if ( ! empty( $og_tags['og:description'] ) ) {
			$og_tags['twitter:description'] = $og_tags['og:description'];
		}

		if ( ! empty( $og_tags['og:image'] ) ) {
			$og_tags['twitter:image'] = $og_tags['og:image'];
		}

		/**
		 * Filter OpenGraph tags before output.
		 *
		 * @since 1.0.0
		 * @param array $og_tags        OpenGraph tags.
		 * @param array $component_data Component data.
		 */
		return apply_filters( 'bb_opengraph_tags', $og_tags, $data );
	}

	/**
	 * Output OpenGraph tags.
	 *
	 * @since 1.0.0
	 * @param array $og_tags OpenGraph tags.
	 */
	private function output_opengraph_tags( $og_tags ) {
		echo "<!-- BuddyBoss OpenGraph Tags -->\n";

		foreach ( $og_tags as $property => $content ) {
			if ( ! empty( $content ) ) {
				printf(
					'<meta property="%s" content="%s" />' . "\n",
					esc_attr( $property ),
					esc_attr( $content )
				);
			}
		}

		echo "<!-- End BuddyBoss OpenGraph Tags -->\n";
	}

	/**
	 * Get component data for external use.
	 *
	 * @since 1.0.0
	 * @return array Component data.
	 */
	public function get_current_component_data() {
		return $this->component_data;
	}
}
