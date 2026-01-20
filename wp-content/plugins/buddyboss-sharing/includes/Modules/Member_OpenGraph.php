<?php
/**
 * Member Open Graph Tags
 *
 * Handles OG tags for single member profile pages.
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
 * Member_OpenGraph class.
 *
 * @since 1.0.0
 */
class Member_OpenGraph {

	/**
	 * The single instance of the class.
	 *
	 * @var Member_OpenGraph
	 */
	protected static $instance = null;

	/**
	 * Current member data.
	 *
	 * @var \WP_User|null
	 */
	private $member = null;

	/**
	 * Member slug.
	 *
	 * @var string
	 */
	private $member_slug = '';

	/**
	 * Whether this is a private network.
	 *
	 * @var bool
	 */
	private $is_private_network = false;

	/**
	 * Main Member_OpenGraph Instance.
	 *
	 * @since 1.0.0
	 * @return Member_OpenGraph
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
		add_action( 'bp_parse_query', array( $this, 'init_member_data' ), 20 );

		// Hook into wp_head to output OG tags
		add_action( 'wp_head', array( $this, 'output_og_tags' ), 5 );
	}

	/**
	 * Initialize member data on single member pages.
	 *
	 * @since 1.0.0
	 */
	public function init_member_data() {
		// Only run on single member pages (not activity pages)
		if ( ! function_exists( 'bp_is_user' ) || ! bp_is_user() ) {
			return;
		}

		// Make sure we're not on a single activity page
		if ( function_exists( 'bp_is_single_activity' ) && bp_is_single_activity() ) {
			return;
		}

		// Check if this is a private network
		$this->is_private_network = function_exists( 'bp_enable_private_network' ) && ! bp_enable_private_network();

		// Get displayed user ID using BuddyBoss function
		$user_id = function_exists( 'bp_displayed_user_id' ) ? bp_displayed_user_id() : 0;

		if ( ! $user_id ) {
			return;
		}

		// Get user object
		$user = get_user_by( 'id', $user_id );

		if ( ! $user ) {
			return;
		}

		// Store member slug for later use
		$this->member_slug = $user->user_nicename;

		// For public networks, load member data
		if ( ! $this->is_private_network ) {
			$this->member = $user;
		}
	}

	/**
	 * Load member data.
	 *
	 * @since 1.0.0
	 * @deprecated No longer needed as member is loaded in init_member_data()
	 */
	private function load_member_data() {
		// This method is deprecated but kept for backwards compatibility
		// Member is now loaded directly in init_member_data()
	}

	/**
	 * Output Open Graph tags.
	 *
	 * @since 1.0.0
	 */
	public function output_og_tags() {
		// Only run on single member pages (not activity pages)
		if ( ! function_exists( 'bp_is_user' ) || ! bp_is_user() ) {
			return;
		}

		// Make sure we're not on a single activity page
		if ( function_exists( 'bp_is_single_activity' ) && bp_is_single_activity() ) {
			return;
		}

		if ( ! $this->member_slug ) {
			return;
		}

		// Get OG data based on network type
		if ( $this->is_private_network ) {
			$og_data = $this->get_private_network_og_data();
		} else {
			$og_data = $this->get_public_network_og_data();
		}

		// Get user ID for filter
		$user_id = function_exists( 'bp_displayed_user_id' ) ? bp_displayed_user_id() : 0;

		// Allow developers to modify OG data
		$og_data = apply_filters( 'buddyboss_sharing_member_og_data', $og_data, $user_id );

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

		// Get user domain from displayed user ID
		$user_id = function_exists( 'bp_displayed_user_id' ) ? bp_displayed_user_id() : 0;
		$og_url  = $user_id && function_exists( 'bp_core_get_user_domain' )
			? bp_core_get_user_domain( $user_id )
			: home_url();

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
	 * Uses member data with customizable templates.
	 *
	 * @since 1.0.0
	 * @return array OG data
	 */
	private function get_public_network_og_data() {
		if ( empty( $this->member ) ) {
			return $this->get_fallback_og_data();
		}

		// Check for custom templates
		$title_template = bp_get_option( 'buddyboss_member_og_title_template', '' );
		$desc_template  = bp_get_option( 'buddyboss_member_og_description_template', '' );

		// Build title - use template if available, otherwise use hardcoded logic
		if ( ! empty( $title_template ) ) {
			$title = $this->replace_template_tags( $title_template, 'title' );
		} else {
			$title = $this->build_member_title();
		}

		// Build description - use template if available, otherwise use hardcoded logic
		if ( ! empty( $desc_template ) ) {
			$description = $this->replace_template_tags( $desc_template, 'description' );
		} else {
			$description = $this->build_member_description();
		}

		// Fallback to defaults if templates result in empty strings
		if ( empty( $title ) ) {
			$title = $this->build_member_title();
		}

		if ( empty( $description ) ) {
			$description = $this->build_member_description();
		}

		// Get member URL
		$url = function_exists( 'bp_core_get_user_domain' )
			? bp_core_get_user_domain( $this->member->ID )
			: home_url();

		// Get member image
		$image = $this->get_member_image();

		return array(
			'title'       => $title,
			'description' => $description,
			'image'       => $image,
			'url'         => $url,
			'type'        => 'profile',
		);
	}

	/**
	 * Build member title.
	 *
	 * @since 1.0.0
	 * @return string Title
	 */
	private function build_member_title() {
		$member_name = ! empty( $this->member->display_name ) ? $this->member->display_name : '';
		$site_title  = get_bloginfo( 'name' );

		return $member_name . ' | ' . $site_title;
	}

	/**
	 * Build member description.
	 *
	 * @since 1.0.0
	 * @return string Description
	 */
	private function build_member_description() {
		// Try to get user bio/description
		$description = ! empty( $this->member->description ) ? wp_strip_all_tags( $this->member->description ) : '';

		// Limit to 160 characters for OG description
		if ( strlen( $description ) > 160 ) {
			$description = substr( $description, 0, 157 ) . '...';
		}

		// Fallback to member name
		if ( empty( $description ) ) {
			$description = sprintf(
				/* translators: %s: member display name */
				__( 'View %s\'s profile', 'buddyboss-sharing' ),
				$this->member->display_name
			);
		}

		return $description;
	}

	/**
	 * Get member image.
	 *
	 * @since 1.0.0
	 * @return string Image URL
	 */
	private function get_member_image() {
		$image = '';

		// Try to get member avatar
		if ( $this->member ) {
			$image = get_avatar_url( $this->member->ID, array( 'size' => 512 ) );
		}

		// Fallback to site icon
		if ( empty( $image ) ) {
			$image = get_site_icon_url( 512 );
		}

		return $image;
	}

	/**
	 * Get fallback OG data when member is not found.
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
	 * Replace template tags with member data.
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

		// Get member data
		$author_name       = $this->member ? $this->member->display_name : '';
		$author_first_name = $this->member ? $this->member->first_name : '';
		$author_last_name  = $this->member ? $this->member->last_name : '';
		$author_bio        = $this->member && ! empty( $this->member->description )
			? wp_strip_all_tags( $this->member->description )
			: '';

		// Limit content length based on context
		if ( 'title' === $context && strlen( $author_bio ) > 60 ) {
			$author_bio = substr( $author_bio, 0, 57 ) . '...';
		} elseif ( 'description' === $context && strlen( $author_bio ) > 300 ) {
			$author_bio = substr( $author_bio, 0, 297 ) . '...';
		}

		// Get site title
		$site_title = get_bloginfo( 'name' );

		// Build replacement array
		$replacements = array(
			'{author_name}'        => $author_name,
			'{author_first_name}'  => $author_first_name,
			'{author_last_name}'   => $author_last_name,
			'{author_bio}'         => $author_bio,
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

		echo "\n<!-- BuddyBoss SEO - Member Open Graph Tags -->\n";

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

		echo "<!-- / BuddyBoss SEO - Member Open Graph Tags -->\n\n";
	}
}
