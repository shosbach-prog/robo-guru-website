<?php
/**
 * Group Open Graph Tags
 *
 * Handles OG tags for single group pages.
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
 * Group_OpenGraph class.
 *
 * @since 1.0.0
 */
class Group_OpenGraph {

	/**
	 * The single instance of the class.
	 *
	 * @var Group_OpenGraph
	 */
	protected static $instance = null;

	/**
	 * Current group data.
	 *
	 * @var object|null
	 */
	private $group = null;

	/**
	 * Group creator data.
	 *
	 * @var \WP_User|null
	 */
	private $creator = null;

	/**
	 * Group slug.
	 *
	 * @var string
	 */
	private $group_slug = '';

	/**
	 * Whether this is a private network.
	 *
	 * @var bool
	 */
	private $is_private_network = false;

	/**
	 * Main Group_OpenGraph Instance.
	 *
	 * @since 1.0.0
	 * @return Group_OpenGraph
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
		add_action( 'bp_parse_query', array( $this, 'init_group_data' ), 20 );

		// Hook into wp_head to output OG tags
		add_action( 'wp_head', array( $this, 'output_og_tags' ), 5 );
	}

	/**
	 * Initialize group data on single group pages.
	 *
	 * @since 1.0.0
	 */
	public function init_group_data() {
		// Only run on single group pages
		if ( ! function_exists( 'bp_is_group' ) || ! bp_is_group() ) {
			return;
		}

		// Check if this is a private network
		$this->is_private_network = function_exists( 'bp_enable_private_network' ) && ! bp_enable_private_network();

		// Get current group using BuddyBoss function
		if ( function_exists( 'groups_get_current_group' ) ) {
			$current_group = groups_get_current_group();

			if ( ! $current_group || empty( $current_group->slug ) ) {
				return;
			}

			// Store group slug for later use
			$this->group_slug = $current_group->slug;

			// For public networks, store group data directly
			if ( ! $this->is_private_network ) {
				$this->group = $current_group;

				// Load creator data
				if ( ! empty( $this->group->creator_id ) ) {
					$this->creator = get_user_by( 'id', $this->group->creator_id );
				}
			}
		}
	}

	/**
	 * Check if current group is private or hidden.
	 *
	 * @since 1.0.0
	 * @return bool True if group is private or hidden, false otherwise.
	 */
	private function is_group_private_or_hidden() {
		if ( ! function_exists( 'groups_get_current_group' ) ) {
			return false;
		}

		$current_group = groups_get_current_group();

		if ( ! $current_group || empty( $current_group->status ) ) {
			return false;
		}

		// Check if group status is 'private' or 'hidden'
		return in_array( $current_group->status, array( 'private', 'hidden' ), true );
	}

	/**
	 * Load group data from BuddyPress.
	 *
	 * @since 1.0.0
	 * @deprecated No longer needed as group is loaded in init_group_data()
	 */
	private function load_group_data() {
		// This method is deprecated but kept for backwards compatibility
		// Group is now loaded directly in init_group_data()
	}

	/**
	 * Output Open Graph tags.
	 *
	 * @since 1.0.0
	 */
	public function output_og_tags() {
		// Only run on single group pages
		if ( ! function_exists( 'bp_is_group' ) || ! bp_is_group() ) {
			return;
		}

		if ( ! $this->group_slug ) {
			return;
		}

		// Check if group is private or hidden
		$use_generic_og = $this->is_private_network || $this->is_group_private_or_hidden();

		// Get OG data based on network type and group privacy
		if ( $use_generic_og ) {
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

		// Get group URL from current group
		$og_url = home_url();
		if ( function_exists( 'groups_get_current_group' ) && function_exists( 'bp_get_group_permalink' ) ) {
			$current_group = groups_get_current_group();
			if ( $current_group ) {
				$og_url = bp_get_group_permalink( $current_group );
			}
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
	 * Uses group data with customizable templates.
	 *
	 * @since 1.0.0
	 * @return array OG data
	 */
	private function get_public_network_og_data() {
		if ( empty( $this->group ) ) {
			return $this->get_fallback_og_data();
		}

		// Check for custom templates
		$title_template = bp_get_option( 'buddyboss_group_og_title_template', '' );
		$desc_template  = bp_get_option( 'buddyboss_group_og_description_template', '' );

		// Build title - use template if available, otherwise use hardcoded logic
		if ( ! empty( $title_template ) ) {
			$title = $this->replace_template_tags( $title_template, 'title' );
		} else {
			$title = $this->build_group_title();
		}

		// Build description - use template if available, otherwise use hardcoded logic
		if ( ! empty( $desc_template ) ) {
			$description = $this->replace_template_tags( $desc_template, 'description' );
		} else {
			$description = $this->build_group_description();
		}

		// Fallback to defaults if templates result in empty strings
		if ( empty( $title ) ) {
			$title = $this->build_group_title();
		}

		if ( empty( $description ) ) {
			$description = $this->build_group_description();
		}

		// Get group URL
		$url = function_exists( 'bp_get_group_permalink' )
			? bp_get_group_permalink( $this->group )
			: home_url();

		// Get group image
		$image = $this->get_group_image();

		return array(
			'title'       => $title,
			'description' => $description,
			'image'       => $image,
			'url'         => $url,
			'type'        => 'article',
			'author'      => $this->creator ? $this->creator->display_name : '',
			'published'   => ! empty( $this->group->date_created ) ? $this->group->date_created : '',
		);
	}

	/**
	 * Build group title.
	 *
	 * @since 1.0.0
	 * @return string Title
	 */
	private function build_group_title() {
		$group_name = ! empty( $this->group->name ) ? $this->group->name : '';
		$site_title = get_bloginfo( 'name' );

		return $group_name . ' | ' . $site_title;
	}

	/**
	 * Build group description.
	 *
	 * @since 1.0.0
	 * @return string Description
	 */
	private function build_group_description() {
		$description = ! empty( $this->group->description ) ? wp_strip_all_tags( $this->group->description ) : '';

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
	 * Get group image.
	 *
	 * @since 1.0.0
	 * @return string Image URL
	 */
	private function get_group_image() {
		$image = '';

		// Try to get group avatar
		if ( function_exists( 'bp_core_fetch_avatar' ) && $this->group ) {
			$avatar = bp_core_fetch_avatar(
				array(
					'item_id' => $this->group->id,
					'object'  => 'group',
					'type'    => 'full',
					'html'    => false,
				)
			);

			if ( $avatar ) {
				$image = $avatar;
			}
		}

		// Fallback to site icon
		if ( empty( $image ) ) {
			$image = get_site_icon_url( 512 );
		}

		return $image;
	}

	/**
	 * Get fallback OG data when group is not found.
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
	 * Replace template tags with group data.
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

		// Get group name
		$group_name = ! empty( $this->group->name ) ? $this->group->name : '';

		// Get group description
		$group_description = ! empty( $this->group->description )
			? wp_strip_all_tags( $this->group->description )
			: '';

		// Limit content length based on context
		if ( 'title' === $context && strlen( $group_description ) > 60 ) {
			$group_description = substr( $group_description, 0, 57 ) . '...';
		} elseif ( 'description' === $context && strlen( $group_description ) > 300 ) {
			$group_description = substr( $group_description, 0, 297 ) . '...';
		}

		// Get creator data
		$author_name       = $this->creator ? $this->creator->display_name : '';
		$author_first_name = $this->creator ? $this->creator->first_name : '';
		$author_last_name  = $this->creator ? $this->creator->last_name : '';

		// Get site title
		$site_title = get_bloginfo( 'name' );

		// Build replacement array
		$replacements = array(
			'{group_name}'         => $group_name,
			'{group_description}'  => $group_description,
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

		echo "\n<!-- BuddyBoss SEO - Group Open Graph Tags -->\n";

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

		echo "<!-- / BuddyBoss SEO - Group Open Graph Tags -->\n\n";
	}
}
