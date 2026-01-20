<?php
/**
 * Login Page Open Graph Tags
 *
 * Handles OG tags for WordPress login page.
 * Always uses admin settings (generic site-level data) for private networks.
 *
 * @package BuddyBoss_Sharing
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Modules;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login_OpenGraph class.
 *
 * @since 1.0.0
 */
class Login_OpenGraph {

	/**
	 * The single instance of the class.
	 *
	 * @var Login_OpenGraph
	 */
	protected static $instance = null;

	/**
	 * Whether this is a private network.
	 *
	 * @var bool
	 */
	private $is_private_network = false;

	/**
	 * Main Login_OpenGraph Instance.
	 *
	 * @since 1.0.0
	 * @return Login_OpenGraph
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

		// Hook into login_head to output OG tags
		add_action( 'login_head', array( $this, 'output_og_tags' ), 5 );
	}

	/**
	 * Output Open Graph tags on login page.
	 *
	 * @since 1.0.0
	 */
	public function output_og_tags() {
		// Check if this is a private network
		$this->is_private_network = function_exists( 'bp_enable_private_network' ) && ! bp_enable_private_network();

		// Only output OG tags if private network is enabled
		// (Public sites don't redirect to login, so no need for OG tags)
		if ( ! $this->is_private_network ) {
			return;
		}

		// Get OG data from admin settings
		$og_data = $this->get_login_og_data();

		// Output OG tags
		$this->render_og_tags( $og_data );
	}

	/**
	 * Get OG data for login page.
	 * Always uses BuddyBoss SEO plugin settings (admin settings).
	 *
	 * @since 1.0.0
	 * @return array OG data
	 */
	private function get_login_og_data() {
		$og_title       = bp_get_option( 'buddyboss_og_title', get_bloginfo( 'name' ) );
		$og_description = bp_get_option( 'buddyboss_og_description', get_bloginfo( 'description' ) );
		$og_image       = bp_get_option( 'buddyboss_og_image', '' );
		$og_url         = wp_login_url();

		return array(
			'title'       => $og_title,
			'description' => $og_description,
			'image'       => $og_image,
			'url'         => $og_url,
			'type'        => 'website',
		);
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

		echo "\n<!-- BuddyBoss SEO - Login Page Open Graph Tags -->\n";

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

		echo "<!-- / BuddyBoss SEO - Login Page Open Graph Tags -->\n\n";
	}
}
